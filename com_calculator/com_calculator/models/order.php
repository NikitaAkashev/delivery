<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');

class CalculatorModelsOrder extends CalculatorModelsDefault
{
	private $_inner_price_viewer_group_ids = array(7,8); // ID групп, которым можно считать разницу в ценах.
	
	private $user_id;
	
	private $min_exact_volume = 0.01; // минимальный объем. Все что меньше, заменяется на "менее Х"
	public $weight_no_size = 0.5; // до этого веса размеры указывать необязательно	
	
	public $nds = 0.18;
	
	public $city_from;
	public $city_to;
	public $weight;
	public $assessed_value;
	public $width;
	public $length;
	public $height;	
	
	public $volume;
	
	public $prices = array();
	
	public $calculated = false;
	public $ordered = false;
	
	// и вдруг, не мудурствуя лукаво, берем и фигачим весь реквест в переменную!!!
	public $form;
	
	function __construct() {
		parent::__construct();
		
		$this->user_id = JFactory::getUser()->get('id');
				
		$this->city_from = JRequest::getInt('city_from', null);
		$this->city_to = JRequest::getInt('city_to', null);				
		$this->weight = JRequest::getFloat('weight', null);    
		$this->assessed_value = JRequest::getFloat('assessed_value', null);    
		$this->width = JRequest::getFloat('width', null);    
		$this->length = JRequest::getFloat('length', null);    
		$this->height = JRequest::getFloat('height', null);
		    
		$this->form = JRequest::get();
	}
	
	// проверяет, что переданы все необходимые данные для расчета
	function IsFilled(){
		return 
			isset($this->city_from) 
			&& isset($this->city_to) 
			&& $this->city_from != 0 
			&& $this->city_to != 0 
			&& isset($this->weight) 
			&& $this->weight != 0 
			&& isset($this->assessed_value) 
			&&(
				$this->weight <= $this->weight_no_size || // либо вес меньше граничного, либо размеры заполнены
				(
					isset($this->width) 
					&& isset($this->length) 
					&& isset($this->height) 
					&& $this->width != 0 
					&& $this->length != 0 
					&& $this->height != 0
				)
			);
	}
	
	// проверяет, можно ли пользователю смотреть внутреннюю стоимость отправки
	function IsInnerPriceViewer(){
		$usergroups = JAccess::getGroupsByUser($this->user_id);
		foreach ($this->_inner_price_viewer_group_ids as $agid)
		{
			if (in_array($agid,$usergroups)) return true;
		}	  
		
		return false;
	}
	
	// Производит расчет
	function Calculate(){
		$this->with_inner = $this->IsInnerPriceViewer();
		if($this->IsFilled())
		{
			if($this->city_from == $this->city_to)
			{				
				$this->CalculationSetEmpty();
				$this->calculated = true;
				return $this->CalculationResult();
			}
			
			$this->volume = $this->width * $this->length * $this->height;
			$db = JFactory::getDBO();
			$query = "
select
	base.*,
	wp.base_price,
	wp.overweight_cost,
	wp.from weight_bottom,
	vp.from avp_bottom,
	vp.base_price avp_base_price,
	vp.overprice_percent,
	greatest(d_t_u.factor, d_t_a.factor, d_a_u.factor, d_a_a.factor) discount_factor,
	t.margin,
	t.name tariff_name,
	t.code tariff_code,
	t.dimension_limit,
	t.weight_limit,
	t.oversize_limit_factor,	
	cf2t.min_time cf_min_time,
	cf2t.max_time cf_max_time,
	ct2t.min_time ct_min_time,
	ct2t.max_time ct_max_time,
	p.min_assessed_price,
	p.name provider_name,
	p.code provider_code,
	p.is_zones_by_exact_city,
	p.prices_with_nds,
	coalesce(ff.factor_inner, 1) cf_factor_inner,
	coalesce(ft.factor_inner, 1) ct_factor_inner,
	coalesce(ff.factor_outer, 1) cf_factor_outer,
	coalesce(ft.factor_outer, 1) ct_factor_outer,
	dt.name delivery_type_name,
	dt.code delivery_type_code,
	cp.price courier_price
from(
	/* зоны по основным городам */
	select 
		cf.city city_from,
		ct.city city_to,
		r.rate,
		r.tariff,
		r.provider,
		null min_days,
		null max_days,
		r.delivery_hours,
		greatest(".$db->quote($this->weight).", ".$db->quote($this->volume)."/p.volume_weight_divider) real_weight
	from #__delivery_city cf
		join #__delivery_city ct on ct.city = ".$db->quote($this->city_to)."
		join #__delivery_direction2zone d2z on d2z.city_from = COALESCE(cf.parent, cf.city) 
							and d2z.city_to = COALESCE(ct.parent, ct.city)
		join #__delivery_zone z on z.zone = d2z.zone
		join #__delivery_rate r on r.zone = z.zone and r.provider = z.provider
		join #__delivery_provider p on p.provider = r.provider
		join #__delivery_city2provider fc2p on fc2p.provider = p.provider and fc2p.city = cf.city /* в __delivery_city2provider заполняются связки только для поставщиков с is_zones_by_exact_city = 0 */ 
		join #__delivery_city2provider tc2p on tc2p.provider = p.provider and tc2p.city = ct.city		
	where
		cf.city = ".$db->quote($this->city_from)."
		and p.is_zones_by_exact_city = 0
		and r.is_enabled = 1
		
	union all	
		
	/* зоны по всем городам */
	select 
		cf.city city_from,
		ct.city city_to,
		r.rate,
		r.tariff,
		r.provider,
		d2z.min_days,
		d2z.max_days,
		r.delivery_hours,
		greatest(".$db->quote($this->weight).", ".$db->quote($this->volume)."/p.volume_weight_divider) real_weight
	from #__delivery_city cf
		join #__delivery_city ct on ct.city = ".$db->quote($this->city_to)."
		join #__delivery_direction2zone d2z on d2z.city_from = cf.city and d2z.city_to = ct.city
		join #__delivery_zone z on z.zone = d2z.zone
		join #__delivery_rate r on r.zone = z.zone and r.provider = z.provider
		join #__delivery_provider p on p.provider = r.provider		
	where
		cf.city = ".$db->quote($this->city_from)."
		and p.is_zones_by_exact_city = 1
		and r.is_enabled = 1
		
	union all

	/* без зон */
	select
		cf.city city_from,
		ct.city city_to,
		r.rate,
		r.tariff,
		r.provider,
		r.min_days,
		r.max_days,
		r.delivery_hours,
		greatest(".$db->quote($this->weight).", ".$db->quote($this->volume)."/p.volume_weight_divider) real_weight
	from #__delivery_city cf
		join #__delivery_city ct on ct.city = ".$db->quote($this->city_to)."
		join #__delivery_rate r on 
							(r.city_from = cf.city and r.city_to = ct.city)
		join #__delivery_provider p on p.provider = r.provider
	where
		cf.city = ".$db->quote($this->city_from)."
		and r.is_enabled = 1
) base
	join #__delivery_provider p on p.provider = base.provider
	join #__delivery_tariff t on t.tariff = base.tariff
	join #__delivery_delivery_type2tariff dt2t on dt2t.tariff = t.tariff
	join #__delivery_delivery_type dt on dt.delivery_type = dt2t.delivery_type
	join #__delivery_courier_price cp on 
					cp.tariff = t.tariff
					and cp.weight_from <= base.real_weight
					and cp.weight_to > base.real_weight
	join #__delivery_weight_price wp on 
							wp.rate = base.rate 
							and (wp.from < base.real_weight or base.real_weight = 0) 
							and wp.to >= base.real_weight
	left join #__delivery_city_factor ff on 
					ff.city = base.city_from
					and ff.tariff = t.tariff
	left join #__delivery_city_factor ft on 
					ft.city = base.city_to
					and ft.tariff = t.tariff
	left join #__delivery_city2delivery_time cf2t on 
					cf2t.provider = base.provider 
					and cf2t.city = base.city_from
	left join #__delivery_city2delivery_time ct2t on 
					ct2t.provider = base.provider 
					and ct2t.city = base.city_to
	left join #__delivery_assessed_value_price vp on 
					vp.from <= ".$db->quote($this->assessed_value)." 
					and vp.to > ".$db->quote($this->assessed_value)."
					and vp.tariff = base.tariff
					
	-- скидка по пользователю и тарифу				
	left join #__delivery_discount d_t_u on
					d_t_u.city_from = base.city_from
					and d_t_u.city_to = base.city_to
					and d_t_u.tariff = t.tariff
					and d_t_u.user = ".$db->quote($this->user_id)."
	-- скидка по пользователю и всем тарифам				
	left join #__delivery_discount d_a_u on
					d_a_u.city_from = base.city_from
					and d_a_u.city_to = base.city_to
					and d_a_u.tariff is null
					and d_t_u.user = ".$db->quote($this->user_id)."
	-- скидка по всем пользователям и тарифу				
	left join #__delivery_discount d_t_a on
					d_t_a.city_from = base.city_from
					and d_t_a.city_to = base.city_to
					and d_t_a.tariff = t.tariff
					and d_t_a.user is null
	-- скидка по всем пользователям и всем тарифам				
	left join #__delivery_discount d_a_a on
					d_t_a.city_from = base.city_from
					and d_t_a.city_to = base.city_to
					and d_t_a.tariff is null
					and d_t_a.user is null;	
";
			$db->setQuery($query);
			$result = $db->loadObjectList();
						
			if(is_null($result))
			{
				$this->CalculationSetEmpty();
				return $this->CalculationResult();
			}
			
			foreach($result as $i => $rate){
				
				$uid = $this->GetUniqueId($rate);
				
				$this->prices[$i] = new stdClass();
				
				$this->prices[$i]->uid = $uid;
				
				// определим, сколько будет курьерская цена
				$courier_price = 0;
				if($rate->delivery_type_code == 'door.door')
				{
					$courier_price = $rate->courier_price;
				}
				
				// проверим коэфициенты превышения
				$oversize = ($this->width > $rate->dimension_limit || 
						$this->length > $rate->dimension_limit || 
						$this->height > $rate->dimension_limit ||
						$rate->real_weight > $rate->weight_limit) && // превышение размеров
						(($rate->tariff_code == 'ural' && $rate->real_weight == $this->weight) // либо урал, расчитанный не по объемному весу
						|| $rate->tariff_code != 'ural') // либо все остальные
						 ? $rate->oversize_limit_factor : 1;
				
				// вычислим цену за вес
				$weight_price = $rate->base_price + $rate->overweight_cost * (ceil($rate->real_weight) - $rate->weight_bottom);
				
				// вычислим оценочную стоимость
				$assessed_value_price = $this->assessed_value == 0 ? 0 : max($rate->avp_base_price + $rate->overprice_percent * (ceil($this->assessed_value) - $rate->avp_bottom), $rate->min_assessed_price);
				
				// вычислим внутреннюю цену (НДС, если цена без него * вес * скидка * коэф. городов + оценочная стоимость)
				$this->prices[$i]->inner_price = round(($rate->prices_with_nds == 0 ? 1 + $this->nds : 1) * $weight_price * (1 - $rate->discount_factor) * ($rate->cf_factor_inner + $rate->ct_factor_inner - 1) + $assessed_value_price, 2);
				
				// цена клиента = внутренняя * наценку * доп. коэфициент городов
				$this->prices[$i]->customer_price = $this->RoundPrice($this->prices[$i]->inner_price * $rate->margin * ($rate->cf_factor_outer + $rate->ct_factor_outer - 1)) + $courier_price;
				
				// прибыль
				$this->prices[$i]->profit = $this->prices[$i]->customer_price - $this->prices[$i]->inner_price;
				
				$this->prices[$i]->inner_nds = round($this->nds * $this->prices[$i]->inner_price / (1 + $this->nds), 2);
				$this->prices[$i]->customer_nds = round($this->nds * $this->prices[$i]->customer_price / (1 + $this->nds), 2);
				$this->prices[$i]->profit_nds = round($this->nds * $this->prices[$i]->profit / (1 + $this->nds), 2);
				
				$this->prices[$i]->inner_price_no_nds = $this->prices[$i]->inner_price - $this->prices[$i]->inner_nds;
								
				if($rate->is_zones_by_exact_city == 0) // вычисляем время для зон, которые определяются через столичные города
				{
					if($this->city_from == 38){// Москва
						$min_delivery_time = $rate->ct_min_time;
						$max_delivery_time = $rate->ct_max_time;	
					} else if($this->city_to == 38){// Москва
						$min_delivery_time = $rate->cf_min_time;
						$max_delivery_time = $rate->cf_max_time;	
					} else if ($rate->cf_min_time == 1){
						$min_delivery_time = $rate->ct_min_time + 1;
						$max_delivery_time = $rate->ct_max_time + 2;					
					} else if ($rate->ct_min_time == 1){
						$min_delivery_time = $rate->cf_min_time + 1;
						$max_delivery_time = $rate->cf_max_time + 2;	
					} else {
						$min_delivery_time = $rate->cf_min_time + $rate->ct_min_time;
						$max_delivery_time = $rate->cf_max_time + $rate->ct_max_time;
					}
				} 
				else // для явных направлений
				{
					$min_delivery_time = $rate->min_days;
					$max_delivery_time = $rate->max_days;
				}
				
				$this->prices[$i]->delivery_time = $min_delivery_time == $max_delivery_time ? $max_delivery_time : $min_delivery_time . ' - ' . $max_delivery_time;
				
				$this->prices[$i]->displayed_volume = $this->volume / 1000000 < $this->min_exact_volume ? 'менее 0,01' : $this->volume / 1000000;
				$this->prices[$i]->real_weight = $rate->real_weight;
				
				$this->prices[$i]->tariff_name = $rate->tariff_name . ' ' . $rate->delivery_hours . ' ' . $rate->delivery_type_name;
				
				$this->prices[$i]->provider_name = $rate->provider_name;
				
				// уберем внутренние цены, если это обычный пользователь
				if(!$this->with_inner)
				{
					unset($this->prices[$i]->inner_price);
					unset($this->prices[$i]->inner_price_no_nds);
					unset($this->prices[$i]->inner_nds);
					unset($this->prices[$i]->profit);
					unset($this->prices[$i]->profit_nds);
					unset($this->prices[$i]->provider_name);
				}
			}
			
			$this->calculated = true;
		} 
		else 
		{
			$this->CalculationSetEmpty();			
		}
		return $this->CalculationResult();
	}

	// проверим, что пришли все данные, которые нам нужны для заказа TODO: Перенести проверку в JTable::check();
	function CheckOrderData()
	{		
		if (empty($this->form['phone']))
			return false;
		
		return true;
	}
	
	// возвращает объект с результатами расчетов
	function CalculationResult()
	{
		$result = new stdClass();
		$result->calculated = $this->calculated;
		$result->with_inner = $this->with_inner;
		$result->prices = $this->prices;
		return $result;
	}
	
	// Устанавливает пустые значения, если расчет не выполнился
	function CalculationSetEmpty()
	{
		$this->calculated = false;
		$this->with_inner = $this->with_inner;
		$this->prices = array();
	}
	
	// округление цены по хитрым правилам
	function RoundPrice($price)
	{
		if ($price < 1000) // округление до десятков
		{
			return ceil($price/10) * 10;
		}
		else // округление кратно 50
		{
			return ceil($price / 50) * 50;
		}
	}
	
	// Формирует уникальный идентификатор строки
	function GetUniqueId($rate)
	{
		return $rate->rate . '_' . $rate->delivery_type_code;// в настоящий момент уникальна строка и тип доставки
	}
	
	// Отправим заказ
	function MakeOrder()
	{
		if($this->CheckOrderData())
		{	
			// сохраним в лог
			$row = $this->LogOrder($this->form);
			
			$view = CalculatorHelpersView::load('email', 'normal', 'html', array('data' => $row));
			
			// Render our view.
			$message = $view->render();
			
			$requesites = $this->GetEmailRequisites();
			// отправим мыло			
			$headers = 'MIME-Version: 1.0' . "\r\n".
						'Content-type: text/html; charset=utf-8' . "\r\n" .
						'From: '. $requesites->to . "\r\n" .
						'Reply-To: '. $requesites->to . "\r\n" .
						'X-Mailer: PHP/' . phpversion();

			mail($requesites->to, $requesites->subject, $message, $headers);
			
			$this->ordered = true;
			$this->order_message = $message;
		}
	}	
		
	// Логирование заказа
	function LogOrder($data){
		$date = date("Y-m-d H:i:s");
		
		$order_unique = explode('_', $data['calc_row_id'], 2); // Уникальные характеристики заказа.
				
		$data['table'] = 'order';
		$data['created'] = $date;
		$data['modified'] = $date;
		$data['rate'] = $order_unique[0];
		$data['delivery_type_code'] = $order_unique[1];
		$data['user'] = $this->user_id;
		
		return $this->store($data);
	}
	
	// Информация о строке - ценнике
	static function GetRateInfo($data)
	{		
		$db = JFactory::getDBO();
		$query = "
select
	concat(
		t.name, 
		coalesce(concat(' ', r.delivery_hours), ''), 
		concat(' (', dt.name, ')')) tariff_name
from #__delivery_rate r
	join #__delivery_tariff t on t.tariff = r.tariff
	join #__delivery_delivery_type dt on dt.code = ".$db->quote($data->delivery_type_code)."
where r.rate  = ".$db->quote($data->rate)."";
		$db->setQuery($query);
		$result = $db->loadResult();
		
		return $result;
	}
	
	// Получение реквизитов для отправки письма
	function GetEmailRequisites()
	{		
		$db = JFactory::getDBO();
		$query = "
select 
	s.value `to`,
	ss.value `subject`
from #__delivery_settings s
	join #__delivery_settings ss on ss.code = 'mail_subject'
where s.code = 'mail_to'
";
		$db->setQuery($query);
		$result = $db->loadObject();
		
		return $result;
	}
}
?>

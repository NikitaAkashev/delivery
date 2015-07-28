<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 *  Model
 */
class CalculatorModelsOrder extends CalculatorModelsDefault
{
	private $_inner_price_viewer_group_ids = array(7,8); // ID групп, которым можно считать разницу в ценах.
	
	private $user_id;
	
	private $min_exact_volume = 0.01; // минимальный объем. Все что меньше, заменяется на "менее Х"
	
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
		return isset($this->city_from) && isset($this->city_to) && isset($this->weight) &&
				isset($this->assessed_value) && isset($this->width) &&
				isset($this->length) && isset($this->height) && 
				$this->city_from != 0 && $this->city_to != 0 && 
				$this->weight != 0 && $this->width != 0 &&
				$this->length != 0 && $this->height != 0;
	}
	
	// проверяет, можно ли пользователю смотреть внутреннюю стоимость отправки
	function IsInnerPriceViewer(){
		$usergroups = JAccess::getGroupsByUser($this->user_id);
		foreach ($this->_inner_price_viewer_group_ids as $agid)
		{
			if (in_array($agid,$usergroups)) return true;
		}	  
		
		return true; // на время тестов
	}
	
	// Производит расчет
	function Calculate(){
		$this->with_inner = $this->IsInnerPriceViewer();
		if($this->IsFilled())
		{
			$this->volume = $this->width * $this->length * $this->height;
			$db = JFactory::getDBO();
			$query = "
select
	base.*,
	vp.from avp_bottom,
	vp.base_price avp_base_price,
	vp.overprice_percent,
	d.factor discount_factor,
	t.margin,
	t.name tariff_name,
	t.code tariff_code,
	t.dimension_limit,
	t.weight_limit,
	t.oversize_limit_factor,
	p.min_assessed_price,
	p.name provider_name,
	p.code provider_code,
	cf2t.min_time cf_min_time,
	cf2t.max_time cf_max_time,
	ct2t.min_time ct_min_time,
	ct2t.max_time ct_max_time,
	coalesce(ff.factor, 1) cf_factor,
	coalesce(ft.factor, 1) ct_factor,
	dt.name delivery_type_name,
	dt.code delivery_type_code,
	cp.price courier_price
from(
	select 
		cf.city city_from,
		ct.city city_to,
		r.rate,
		r.tariff,
		r.provider,
		r.min_days,
		r.max_days,
		r.delivery_hours,
		wp.base_price,
		wp.overweight_cost,
		wp.from weight_bottom,
		greatest(".$db->quote($this->weight).", ".$db->quote($this->volume)."/p.volume_weight_divider) real_weight
	from #__delivery_city cf
		join #__delivery_city ct on ct.city = ".$db->quote($this->city_to)."
		left join #__delivery_direction2zone d2z on d2z.city_from = COALESCE(cf.parent, cf.city) 
							and d2z.city_to = COALESCE(ct.parent, ct.city)
		left join #__delivery_direction2zone d2z_exact on d2z_exact.city_from = cf.city 
							and d2z_exact.city_to = ct.city
		join #__delivery_zone z on z.zone = d2z.zone or d2z_exact.zone = z.zone
		join #__delivery_rate r on 
							(r.zone = z.zone and r.provider = z.provider)
		join #__delivery_provider p on p.provider = r.provider
		join #__delivery_weight_price wp on 
							wp.rate = r.rate 
							and wp.from <= greatest(".$db->quote($this->weight).", ".$db->quote($this->volume)."/p.volume_weight_divider) 
							and wp.to > greatest(".$db->quote($this->weight).", ".$db->quote($this->volume)."/p.volume_weight_divider)
	where
		cf.city = ".$db->quote($this->city_from)."

	union all

	select
		cf.city city_from,
		ct.city city_to,
		r.rate,
		r.tariff,
		r.provider,
		r.min_days,
		r.max_days,
		r.delivery_hours,
		wp.base_price,
		wp.overweight_cost,
		wp.from weight_bottom,
		greatest(".$db->quote($this->weight).", ".$db->quote($this->volume)."/p.volume_weight_divider) real_weight
	from #__delivery_city cf
		join #__delivery_city ct on ct.city = ".$db->quote($this->city_to)."
		join #__delivery_rate r on 
							(r.city_from = cf.city and r.city_to = ct.city)
		join #__delivery_provider p on p.provider = r.provider
		join #__delivery_weight_price wp on 
							wp.rate = r.rate 
							and wp.from <= greatest(".$db->quote($this->weight).", ".$db->quote($this->volume)."/p.volume_weight_divider) 
							and wp.to > greatest(".$db->quote($this->weight).", ".$db->quote($this->volume)."/p.volume_weight_divider)
	where
		cf.city = ".$db->quote($this->city_from)."
) base
	join #__delivery_provider p on p.provider = base.provider
	join #__delivery_tariff t on t.tariff = base.tariff
	join #__delivery_delivery_type2tariff dt2t on dt2t.tariff = t.tariff
	join #__delivery_delivery_type dt on dt.delivery_type = dt2t.delivery_type
	join #__delivery_courier_price cp on 
					cp.tariff = t.tariff
					and cp.weight_from <= base.real_weight
					and cp.weight_to > base.real_weight
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
	left join #__delivery_discount d on
					d.city_from = base.city_from
					and d.city_to = base.city_to
					and (d.user is null or d.user = ".$db->quote($this->user_id).");	
";
			$db->setQuery($query);
			$result = $db->loadObjectList();
						
			if(is_null($result))
			{
				$this->CalculationSetEmpty();
				return $this->CalculationResult();;
			}
			
			foreach($result as $i => $rate){
				
				$uid = $this->GetUniqueId($rate);
				
				$this->prices[$i] = new stdClass();
				
				$this->prices[$i]->uid = $uid;
				
				$courier_price = 0;
				if($rate->delivery_type_code == 'door.door')
				{
					$courier_price = $rate->courier_price;
				}
				
				$oversize = ($this->width > $rate->dimension_limit || 
						$this->length > $rate->dimension_limit || 
						$this->height > $rate->dimension_limit ||
						$rate->real_weight > $rate->weight_limit) && // превышение размеров
						(($rate->tariff_code == 'ural' && $rate->real_weight == $this->weight) // либо урал, расчитанный не по объемному весу
						|| $rate->tariff_code != 'ural') // либо все остальные
						 ? $rate->oversize_limit_factor : 1;
				
				$weight_price = $rate->base_price + $rate->overweight_cost * (ceil($rate->real_weight) - $rate->weight_bottom);
				
				$assessed_value_price = max($rate->avp_base_price + $rate->overprice_percent * (ceil($this->assessed_value) - $rate->avp_bottom), $rate->min_assessed_price);
				
				$this->prices[$i]->inner_price = $weight_price * (1 - $rate->discount_factor) * ($rate->cf_factor + $rate->ct_factor - 1) + $assessed_value_price + $courier_price;
				
				$this->prices[$i]->customer_price = $this->RoundPrice($this->prices[$i]->inner_price * $rate->margin);
				
				$this->prices[$i]->profit = $this->prices[$i]->customer_price - $this->prices[$i]->inner_price;
				
				$this->prices[$i]->inner_nds = ceil($this->nds * $this->prices[$i]->inner_price / (1 + $this->nds) * 100 ) / 100;
				$this->prices[$i]->customer_nds = ceil($this->nds * $this->prices[$i]->customer_price / (1 + $this->nds) * 100 ) / 100;
				$this->prices[$i]->profit_nds = ceil($this->nds * $this->prices[$i]->profit / (1 + $this->nds) * 100) / 100;
								
				if($rate->min_days == null) // вычисляем время для зон
				{
					if($this->city_from == 38){// Москва
						$min_delivery_time = $rate->ct_min_time;
						$max_delivery_time = $rate->ct_max_time;	
					} else if($this->city_to == 38){// Москва
						$min_delivery_time = $rate->cf_min_time;
						$max_delivery_time = $rate->cf_max_time;	
					} else if ($rate->cf_min_time == 1){
						$min_delivery_time = $rate->ct_min_time + 1;
						$max_delivery_time = $rate->ct_max_time + 1;					
					} else if ($rate->ct_min_time == 1){
						$min_delivery_time = $rate->cf_min_time + 1;
						$max_delivery_time = $rate->cf_max_time + 1;	
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
		if (empty($this->form['comments']))
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
			return round($price, -1);
		}
		else // округление кратно 50
		{
			return round($price / 50) * 50;
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
from calc_delivery_settings s
	join calc_delivery_settings ss on ss.code = 'mail_subject'
where s.code = 'mail_to'
";
		$db->setQuery($query);
		$result = $db->loadObject();
		
		return $result;
	}
}
?>

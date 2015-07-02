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
	
	public $nds = 0.18;
	
	public $city_from;
	public $city_to;
	public $weight;
	public $assessed_value;
	public $width;
	public $length;
	public $height;
	
	public $prices;
	
	public $volume;
	
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
		return false;
	}
	
	// Производит расчет
	function Calculate(){
		if($this->IsFilled()){
			$db = JFactory::getDBO();
			$this->volume = $this->width * $this->length * $this->height;
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
	p.dimension_limit,
	p.weight_limit,
	p.oversize_limit_factor,
	p.min_assessed_price,
	p.name provider_name,
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
		left join #__delivery_zone z on z.zone = d2z.zone or d2z_exact.zone = z.zone
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
	join #__delivery_city2delivery_time cf2t on 
					cf2t.provider = base.provider 
					and cf2t.city = base.city_from
	join #__delivery_city2delivery_time ct2t on 
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
				$this->prices = null;
				return;
			}
			
			foreach($result as $i => $rate){
				$discount = (100 - $rate->discount_factor)/100;
				$weight_price = $rate->base_price + $rate->overweight_cost * (ceil($rate->real_weight) - $rate->weight_bottom);
				
				$this->prices[$i]['wp'] = $weight_price;
			}
							
			
			$weight_price = $result->weight_base + $result->weight_over * (ceil($real_weight) - $result->weight_bottom);
			
			$assessed_value_price = $result->assessed_value_base + $result->assessed_value_over * (ceil($this->assessed_value) - $result->assessed_value_bottom);
			
			if($is_public){
				$this->price = $weight_price * $oversize * ($result->factor_from + $result->factor_to - 1)* $discount + $assessed_value_price;
				
				if($this->city_from == 38){// Москва
					$this->min_delivery_time = $result->t_min_time;
					$this->max_delivery_time = $result->t_max_time;	
				} else if($this->city_to == 38){// Москва
					$this->min_delivery_time = $result->f_min_time;
					$this->max_delivery_time = $result->f_max_time;	
				} else if ($result->f_min_time == 1){
					$this->min_delivery_time = $result->t_min_time + 1;
					$this->max_delivery_time = $result->t_max_time + 1;					
				} else if ($result->t_min_time == 1){
					$this->min_delivery_time = $result->f_min_time + 1;
					$this->max_delivery_time = $result->f_max_time + 1;	
				} else {
					$this->min_delivery_time = $result->f_min_time + $result->t_min_time;
					$this->max_delivery_time = $result->f_max_time + $result->t_max_time;
				}
				
				$this->nds_part = ceil($this->nds * $this->price / (1 + $this->nds) * 100 ) / 100;
				$this->volume = $this->width * $this->length * $this->height / 1000000;
			} else
			{
				$this->inner_price = $weight_price * $oversize * ($result->factor_from + $result->factor_to - 1) * $discount + $assessed_value_price;
				$this->nds_part_inner = ceil($this->nds * $this->inner_price / (1 + $this->nds) * 100) / 100;
				$this->profit = $this->price - $this->inner_price;
				$this->profit_nds_part = ceil($this->nds * $this->profit / (1 + $this->nds) * 100) / 100;
			}
		} else {
			$this->price = null;
		}
	}

	// проверим, что пришли все данные, которые нам нужны для заказа TODO: Перенести проверку в JTable::check();
	function CheckOrderData()
	{		
		// установлена дата заказа	
		if (empty($this->form['comments']))
			return false;
		
		return true;
	}
	
	// Отправим заказ
	function MakeOrder()
	{
		if($this->CheckOrderData())
		{	
			// сохраним в лог
			$row = $this->LogOrder($this->form);
			
			$view = CalculatorHelpersView::load('email', 'normal', 'html', array('data' => $row, 'pit' => $this)); // TODO когда будет тариф, pit станет не нужен
			
			// Render our view.
			$message = $view->render();
			
			// отправим мыло			
			$to      = 'regspambox@yandex.ru';
			$subject = 'Заказ с сайта ... дописать';
			$headers = 'MIME-Version: 1.0' . "\r\n".
						'Content-type: text/html; charset=utf-8' . "\r\n" .
						'From: webmaster@example.com' . "\r\n" .
						'Reply-To: webmaster@example.com' . "\r\n" .
						'X-Mailer: PHP/' . phpversion();

			mail($to, $subject, $message, $headers);
			
			$this->ordered = true;
			$this->order_message = $message;
		}
	}	
		
	// Логирование заказа
	function LogOrder($data){
		$date = date("Y-m-d H:i:s");
		
		$data['table'] = 'order';
		$data['created'] = $date;
		$data['modified'] = $date;
		$data['price'] = $this->price;
		$data['user'] = $this->user_id;
		$data['order_status'] = 1; // magic number, TODO переделать на получение по коду
		$data['tariff'] = 0; // TODO После переделки системы тарифов сделать сюда вставку идентификатора тарифа
		
		return $this->store($data);
	}
}
?>

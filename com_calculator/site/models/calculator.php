<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 *  Model
 */
class CalculatorModelCalculator extends JModelItem
{
	private $_volume_weight_divider = 6000;
	private $_dimension_limit = 300;
	private $_weight_limit = 200;
	private $_inner_price_viewer_group_ids = array(7,8); // ID групп, которым можно считать разницу в ценах.
	
	private $user_id;
	
	public $nds = 0.18;
	
	public $is_express;
	public $from_door;
	public $city_from;
	public $city_to;
	public $weight;
	public $assessed_value;
	public $width;
	public $length;
	public $height;
	
	public $price;
	public $inner_price;
	public $min_delivery_time;
	public $max_delivery_time;	
	
	public $step;
	
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
		    
		$this->is_express = JRequest::getFloat('is_express', 1);    
		$this->from_door = JRequest::getFloat('from_door', 0) + JRequest::getFloat('to_door', 0) == 2 ? 1 : 0 ; // тариф дверь-дверь только если выбрано и забрать груз и доставить груз
		
		$this->form = JRequest::get();
	}
	
	function IsFilled(){
		return isset($this->city_from) && isset($this->city_to) && isset($this->weight) &&
				isset($this->assessed_value) && isset($this->width) &&
				isset($this->length) && isset($this->height) && 
				$this->city_from != 0 && $this->city_to != 0 && 
				$this->weight != 0 && $this->width != 0 &&
				$this->length != 0 && $this->height != 0;
	}
	
	function IsInnerPriceViewer(){
		$usergroups = JAccess::getGroupsByUser($this->user_id);
		foreach ($this->_inner_price_viewer_group_ids as $agid)
		{
			if (in_array($agid,$usergroups)) return true;
		}	  
		  return false;
	}
	
	function Calculate($is_public){
		if(!$is_public && $this->price === null){
			$this->inner_price = null;
		}
		
		if($this->IsFilled()){
			
			$volume_weight = $this->width * $this->length * $this->height / $this->_volume_weight_divider;
			$real_weight = $this->weight > $volume_weight ? $this->weight : $volume_weight;
			
			$oversize = $this->width > $this->_dimension_limit || 
						$this->length > $this->_dimension_limit || 
						$this->height > $this->_dimension_limit ||
						$real_weight > $this->_weight_limit ? 1.5 : 1;
			
			$db = JFactory::getDBO();
			$query = "
select 
	case when t.is_public = 1 then ff.value 
		else ff.value_for_inner_calculations
	end as factor_from,
	case when t.is_public = 1 then ft.value
		else ft.value_for_inner_calculations
	end as factor_to,
	wp.from as weight_bottom,
	wp.base_price as weight_base,
	COALESCE(wp.overweight_cost, 0) as weight_over,
	avp.from as assessed_value_bottom,
	avp.base_price as assessed_value_base,
	COALESCE(avp.overprice_percent, 0) as assessed_value_over,
	COALESCE(d.factor, 0) as discount,
	case when t.is_express = 1 then cf.express_min_delivery_time
		else cf.standart_min_delivery_time 
	end as f_min_time,
	case when t.is_express = 1 then cf.express_max_delivery_time
		else cf.standart_max_delivery_time 
	end as f_max_time,
	case when t.is_express = 1 then ct.express_min_delivery_time
		else ct.standart_min_delivery_time 
	end as t_min_time,
	case when t.is_express = 1 then ct.express_max_delivery_time
		else ct.standart_max_delivery_time 
	end as t_max_time	
from `#__calc_city`as cf
	join `#__calc_city` as ct on ct.city=".$db->quote($this->city_to)."	 
	join `#__calc_factor` as ff on ff.factor = cf.factor
	join `#__calc_factor` as ft on ft.factor = ct.factor
	join `#__calc_direction2zone` as d2z 
					on d2z.city_from = COALESCE(cf.parent, cf.city) 
						and d2z.city_to = COALESCE(ct.parent, ct.city)
	join `#__calc_tariff` as t 
					on t.is_express = ".($is_public ? $db->quote($this->is_express) : 0)."
						and t.is_public = ".$db->quote($is_public)."
						and t.from_door = ".$db->quote($this->from_door)."
	join `#__calc_weight_price` as wp 
					on wp.zone = d2z.zone
						and (wp.from < ".$db->quote($real_weight)." or ".$db->quote($real_weight)."=0)
						and wp.to >= ".$db->quote($real_weight)."
						and wp.tariff = t.tariff
	join `#__calc_assessed_value_price` as avp
					on avp.from <= ".$db->quote($this->assessed_value)."
						and avp.to > ".$db->quote($this->assessed_value)."
						and avp.is_public = t.is_public
	left join `#__calc_discount` as d 
					on d.city_from = cf.city
						and d.city_to = ct.city
						and (d.user = ".$db->quote($this->user_id)." or d.user is null)
where
	cf.city=".$db->quote($this->city_from).";";
			$db->setQuery($query);
			$result = $db->loadObject();
						
			if(is_null($result))
			{
				$this->price = null;
				$this->inner_price = null;
				return;
			}
									
			$discount = (100 - $result->discount)/100;
			
			$weight_price = $result->weight_base + $result->weight_over * (ceil($real_weight) - $result->weight_bottom);
			$assessed_value_price = $result->assessed_value_base + $result->assessed_value_over * (ceil($this->assessed_value) - $result->assessed_value_bottom);
			
			if($is_public){
				$this->price = $weight_price * $oversize * ($result->factor_from + $result->factor_to - 1)* $discount + $assessed_value_price;
				
				if ($result->f_min_time == 1){
					$this->min_delivery_time = $result->t_min_time;
					$this->max_delivery_time = $result->t_max_time;					
				} else if ($result->t_min_time == 1){
					$this->min_delivery_time = $result->f_min_time;
					$this->max_delivery_time = $result->f_max_time;	
				} else {
					$this->min_delivery_time = $result->f_min_time + $result->t_min_time;
					$this->max_delivery_time = $result->f_max_time + $result->t_max_time;
				}
				
			} else
			{
				$this->inner_price = $weight_price * $oversize * ($result->factor_from + $result->factor_to - 1) * $discount + $assessed_value_price;
			}
			
			if($this->step == 0){
				$this->step = 1;				
			}
		} else {
			$this->price = null;
		}
	}
	
	function GetCities($city = null){
		$db = JFactory::getDbo();
		
		$query = "
select 
	c.city,
	concat(c.name,
		(case when c.city IN (38,55) then ''
		else concat(' (', coalesce(p.region_name, c.region_name, ''), ')') end)) as name
from #__calc_city c
	left join #__calc_city p on p.city = c.parent
".($city == null ? '' : 'where c.city='.$db->quote($city))."
order by c.name
		";
				 
		$db->setQuery($query);
		 
		$results = $db->loadObjectList();
		
		return $results;
	}
	
	// список терминалов в области, к которой относится переданный город
	function GetTerminalsByCity($city){
		$db = JFactory::getDbo();
		
		$query = "
select 
	t.terminal,
	t.name
from #__calc_terminal t
where
	exists(select 1 from #__calc_city c 
				join #__calc_city ch on 
						ch.parent = ifnull(c.parent, c.city) 
						or ch.city = ifnull(c.parent, c.city)
			where 
				c.city = ".$db->quote($city)." 
				and t.city = ch.city)
order by case when t.city = ".$db->quote($city)." then 1 else 2 end, t.name
		";
				 
		$db->setQuery($query);
		 
		$results = $db->loadObjectList();
		
		return $results;
	}
	
	// проверим, что пришли все данные, которые нам нужны для заказа
	function CheckOrderData()
	{
		if($this->step != 1)
			return false;
		
		// установлена дата заказа	
		if (empty($this->form['produceDate']))
			return false;
		
		// есть телефон отправителя
		if (empty($this->form['sender_phone']))
			return false;
		
		// у частного отправителя нужно указать имя
		if (
			array_key_exists('sender_legal_type', $this->form)
			&& $this->form['sender_legal_type'] == "physical"
			&& empty($this->form["sender_name"])
		)
			return false;
		
		// проверим данные корпоративного отправителя
		if (
			!array_key_exists('sender_legal_type', $this->form)
			&& (empty($this->form["sender_inn"])
				|| empty($this->form["sender_company_name"])
				|| empty($this->form["sender_ZIP_code"])
				|| empty($this->form["sender_juridical_city"])
				|| empty($this->form["sender_juridical_street"])
				|| empty($this->form["sender_house"])
				|| empty($this->form["sender_contact"]))
		)
			return false;
		
		// есть телефон получателя
		if (empty($this->form['receiver_phone']))
			return false;
		
		// у частного получателя нужно указать имя
		if (
			array_key_exists('receiver_legal_type', $this->form)
			&& $this->form['receiver_legal_type'] == "physical"
			&& empty($this->form["receiver_name"])
		)
			return false;
		
		// проверим данные корпоративного получателя
		if (
			!array_key_exists('receiver_legal_type', $this->form)
			&& (empty($this->form["receiver_inn"])
				|| empty($this->form["receiver_company_name"])
				|| empty($this->form["receiver_ZIP_code"])
				|| empty($this->form["receiver_juridical_city"])
				|| empty($this->form["receiver_juridical_street"])
				|| empty($this->form["receiver_house"])
				|| empty($this->form["receiver_contact"]))
		)
			return false;
		
		// если плательщик - третье лицо, то проверим и его данные
		if (
			array_key_exists('payer', $this->form)
			&& $this->form['payer'] == 'third'
			&& (empty($this->form["third_inn"])
				|| empty($this->form["third_company_name"])
				|| empty($this->form["third_ZIP_code"])
				|| empty($this->form["third_juridical_city"])
				|| empty($this->form["third_juridical_street"])
				|| empty($this->form["third_house"])
				|| empty($this->form["third_contact"])
				|| empty($this->form['third_phone']))
		)
			return false;
		
		// Забрать
		if (array_key_exists('from_door', $this->form))
		{
			// Если выбрано "от дома клиента"
			if($this->form['from_door'] == 1)
			{
				if (empty($this->form['from_door_street'])
					|| empty($this->form['from_door_house']))
					return false;
			}
			// Если Сам довезет
			else
			{
				if(empty($this->form['from_terminal_address']))
					return false;
			}
		}
		
		// Доставить
		if (array_key_exists('to_door', $this->form))
		{
			// Если выбрано "от дома клиента"
			if($this->form['to_door'] == 1)
			{
				if (empty($this->form['to_door_street'])
					|| empty($this->form['to_door_house']))
					return false;
			}
			// Если Сам заберет
			else
			{
				if(empty($this->form['to_terminal_address']))
					return false;
			}
		}
		
		
		return true;
	}
	
	// Отправим заказ
	function MakeOrder()
	{
		if($this->CheckOrderData())
		{
			$this->step = 2;
			
			// сформируем тело
			$tariff_name = ($this->from_door ? 'Дверь-Дверь ' : 'Окно-Дверь ').($this->is_express ? 'Экспресс' : 'Стандарт');
			$city_name_from = $this->GetCities($this->city_from)[0]->name;
			$city_name_to = $this->GetCities($this->city_to)[0]->name;
			
			$address_from = $this->form['from_door'] == 1 ? 
				$this->form['from_door_street']
					.', д. '.$this->form['from_door_house']
					.(empty($this->form['from_door_building']) ? '' : ', корп. '.$this->form['from_door_building'])
					.(empty($this->form['from_door_structure']) ? '' : ', стр. '.$this->form['from_door_structure'])
					.(empty($this->form['from_door_flat']) ? '' : ', кв/оф. '.$this->form['from_door_flat'])
				: 'терминал '.$this->form['from_terminal_address'] ;
			
			$address_to = $this->form['to_door'] == 1 ? 
				$this->form['to_door_street']
					.', д. '.$this->form['to_door_house']
					.(empty($this->form['to_door_building']) ? '' : ', корп. '.$this->form['to_door_building'])
					.(empty($this->form['to_door_structure']) ? '' : ', стр. '.$this->form['to_door_structure'])
					.(empty($this->form['to_door_flat']) ? '' : ', кв/оф. '.$this->form['to_door_flat'])
				: 'терминал '.$this->form['to_terminal_address'] ;
				
			$time_from = $this->form['from_door'] == 1 ?
					(empty($this->form['from_door_worktime_start']) ? '' : 'с '.$this->form['from_door_worktime_start']).
					(empty($this->form['from_door_worktime_end']) ? '' : ' до '.$this->form['from_door_worktime_end']).
					(empty($this->form['from_door_breaktime_start']) && empty($this->form['from_door_breaktime_end']) ? '' : ', перерыв' ).
					(empty($this->form['from_door_breaktime_start']) ? '' : ' с '.$this->form['from_door_breaktime_start']).
					(empty($this->form['from_door_breaktime_end']) ? '' : ' до '.$this->form['from_door_breaktime_end'])
				: 'не указано';
				
			$time_to = $this->form['to_door'] == 1 ?
					(empty($this->form['to_door_worktime_start']) ? '' : 'с '.$this->form['to_door_worktime_start']).
					(empty($this->form['to_door_worktime_end']) ? '' : ' до '.$this->form['to_door_worktime_end']).
					(empty($this->form['to_door_breaktime_start']) && empty($this->form['to_door_breaktime_end']) ? '' : ', перерыв' ).
					(empty($this->form['to_door_breaktime_start']) ? '' : ' с '.$this->form['to_door_breaktime_start']).
					(empty($this->form['to_door_breaktime_end']) ? '' : ' до '.$this->form['to_door_breaktime_end'])				
				: 'не указано';
			
			$fixed_time_from = $this->form['from_door'] == 1 && $this->form['from_door_exact_time'] == 1 ? 'Фиксированное время забора' : '';
			$fixed_time_to = $this->form['to_door'] == 1 && $this->form['to_door_exact_time'] == 1 ? 'Фиксированное время доставки' : '';
			
			$send_price = ceil($this->price * ($this->nds + 1)*100)/100;
			$nds_in_price = ceil($this->price * ($this->nds)*100)/100;
			$vol = $this->width * $this->length * $this->height / 1000000;
			$vol  = $vol < 0.01 ? "менее 0,01" : $vol;
			
			$sender = $this->FormatActorData('sender');
			$receiver = $this->FormatActorData('receiver');
			switch($this->form['payer'])
			{
				case 'sender': $payer = 'Плательщиком является отправитель.'; break;
				case 'receiver': $payer = 'Плательщиком является получатель.'; break;
				case 'third': $payer = $this->FormatActorData('third'); break;
			};
			
			$message = <<<MSG
Внимание! Новый заказ.
Тариф: {$tariff_name}

Откуда: {$city_name_from}, {$address_from}
Время забора: {$time_from}. {$fixed_time_from}

Куда: {$city_name_to}, {$address_to}
Время доставки: {$time_to}. {$fixed_time_to}

Вес: {$this->weight}кг	
Оценка: {$this->assessed_value}руб	
Ширина: {$this->width}см	
Длина: {$this->length}см	
Высота: {$this->height}см	

Стоимость доставки: {$send_price}, в том числе НДС {$nds_in_price}
Время доставки: {$this->min_delivery_time} - {$this->max_delivery_time} дн.
Объем груза: {$vol} m3

Дата выполнения заявки: {$this->form['produceDate']}
Комментарий: {$this->form['comments']}

Данные отправителя:
{$sender}

Данные получателя:
{$receiver}

Данные плательщика:
{$payer}

MSG;
			// покажем на экране
			// echo '<pre>'.$message.'</pre>';
			
			// сохраним в лог
			$this->LogOrder($message);
			
			// отправим мыло			
			$to      = 'regspambox@yandex.ru';
			$subject = 'Заказ с сайта ... дописать';
			$headers = 'From: webmaster@example.com' . "\r\n" .
						'Reply-To: webmaster@example.com' . "\r\n" .
						'X-Mailer: PHP/' . phpversion();

			mail($to, $subject, $message, $headers);
		}
	}	
	
	// форматирование данных об действующих лицах
	function FormatActorData($actor)
	{
		if(array_key_exists($actor.'_legal_type', $this->form) && $this->form[$actor.'_legal_type'] == 'physical'){
			$data = 'Тип клиента: Физическое лицо'.
				"\r\nФИО: {$this->form[$actor.'_name']}".
				"\r\nНомер телефона: {$this->form[$actor.'_phone']}";
			
		}else{
			$data = 'Тип клиента: Юридическое лицо'.
				"\r\nНаименование: {$this->form[$actor.'_company_name']}".
				"\r\nИНН: {$this->form[$actor.'_inn']}".
				"\r\nЮр. адрес: Индекс "
					.$this->form[$actor.'_ZIP_code']
					.', г. '.$this->form[$actor.'_juridical_city']
					.",\r\n\tул. ".$this->form[$actor.'_juridical_street']
					.', д. '.$this->form[$actor.'_house']
					.(empty($this->form[$actor.'_building']) ? '' : ', корп. '.$this->form[$actor.'_building'])
					.(empty($this->form[$actor.'_structure']) ? '' : ', стр. '.$this->form[$actor.'_structure'])
					.(empty($this->form[$actor.'_flat']) ? '' : ', кв/оф. '.$this->form[$actor.'_flat']).
				"\r\nКонтактное лицо: {$this->form[$actor.'_contact']}".
				"\r\nНомер телефона: {$this->form[$actor.'_phone']}";
		}
		
		return $data;
	}
	
	// Логирование заказа
	function LogOrder($message){
		$db = JFactory::getDbo();
		
		$query = "
insert into #__calc_order_log(message, user)
values(".$db->quote($message).", ".$db->quote($this->user_id).")
		";
				 
		$db->setQuery($query);
		
		return $db->query();
	}
}
?>

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
	}
	
	function IsFilled(){
		return isset($this->city_from) && isset($this->city_to) && isset($this->weight) &&
				isset($this->assessed_value) && isset($this->width) &&
				isset($this->length) && isset($this->height);
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
			$this->inner_price == null;
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
		} else {
			$this->price = null;
		}
	}
	
	function GetCities(){
		$db = JFactory::getDbo();
		
		$query = "
select 
	c.city,
	concat(c.name, ' (', coalesce(p.region_name, c.region_name, ''), ')') as name
from #__calc_city c
	left join #__calc_city p on p.city = c.parent
order by c.name
		";
				 
		$db->setQuery($query);
			$result = $db->loadObject();
		 
		$results = $db->loadObjectList();
		
		return $results;
	}
}
?>

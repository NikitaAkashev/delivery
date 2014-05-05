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
	
	public $city_from;
	public $city_to;
	public $weight;
	public $assessed_value;
	public $width;
	public $length;
	public $height;
	public $price;
	
	
	function __construct() {
		parent::__construct();
		$this->city_from = JRequest::getInt('city_from', null);
		$this->city_to = JRequest::getInt('city_to', null);				
		$this->weight = JRequest::getFloat('weight', null);    
		$this->assessed_value = JRequest::getFloat('assessed_value', null);    
		$this->width = JRequest::getFloat('width', null);    
		$this->length = JRequest::getFloat('length', null);    
		$this->height = JRequest::getFloat('height', null);    
	}
	
	function IsFilled(){
		return $this->city_from != null && $this->city_to != null && $this->weight != null &&
				$this->assessed_value != null && $this->width != null &&
				$this->length != null && $this->height != null;
	}
	
	function Calculate(){
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
	cf.factor as factor_from,
	ct.factor as factor_to,
	wp.from as weight_bottom,
	wp.base_price as weight_base,
	COALESCE(wp.overweight_cost, 0) as weight_over,
	avp.from as assessed_value_bottom,
	avp.base_price as assessed_value_base,
	COALESCE(avp.overprice_percent, 0) as assessed_value_over,
	COALESCE(d.factor, 1) as discount
from `#__calc_city`as cf
	join `#__calc_city` as ct on ct.city=".$db->quote($this->city_to)." 
	join `#__calc_direction2zone` as d2z 
					on d2z.city_from = COALESCE(cf.parent, cf.city) 
						and d2z.city_to = COALESCE(ct.parent, ct.city)
	join `#__calc_weight_price` as wp 
					on wp.zone = d2z.zone
						and (wp.from < ".$db->quote($real_weight)." or ".$db->quote($real_weight)."=0)
						and wp.to >= ".$db->quote($real_weight)."
	join `#__calc_assessed_value_price` as avp
					on avp.from <= ".$db->quote($this->assessed_value)."
						and avp.to > ".$db->quote($this->assessed_value)."
	left join `#__calc_discount` as d 
					on d.city_from = cf.city
						and d.city_to = ct.city
where
	cf.city=".$db->quote($this->city_from).";";
			$db->setQuery($query);
			$result = $db->loadObject();
									
			$discount = (100 - $result->discount)/100;
			
			$weight_price = $result->weight_base + $result->weight_over * (ceil($real_weight) - $result->weight_bottom);
			$assessed_value_price = $result->assessed_value_base + $result->assessed_value_over * (ceil($this->assessed_value) - $result->assessed_value_bottom);
			
			$this->price = $weight_price * $oversize * $result->factor_from * $result->factor_to * $discount + $assessed_value_price;
		} else {
			$this->price = null;
		}

	}
	
	function GetCitys(){
		//select `city`, `name` from `#__city`
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true);
		 
		$query->select($db->quoteName(array('city', 'name')));
		$query->from($db->quoteName('#__calc_city'));
		$query->order('name ASC');
		 
		$db->setQuery($query);
		 
		$results = $db->loadObjectList();
		
		return $results;
	}
}
?>

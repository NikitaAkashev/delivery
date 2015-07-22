<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 *  Город
 */
class CalculatorModelsCity extends CalculatorModelsDefault
{	
	// Список городов с областями
	static function GetCities($city = null){
		$db = JFactory::getDbo();
		
		$query = "
select 
	c.city,
	concat(c.name,
		(case when c.city IN (38,55) then ''
		else concat(' (', coalesce(p.region_name, c.region_name, ''), ')') end)) as name
from #__calc_city c
	left join #_delivery_city p on p.city = c.parent
".($city == null ? '' : 'where c.city='.$db->quote($city))."
order by c.name
		";
				 
		$db->setQuery($query);
		 
		$results = $db->loadObjectList();
		
		return $results;
	}
	
	// Информация о городе
	static function GetCity($city){
		$results = CalculatorModelsCity::GetCities($city);
		
		return $results[0];
	}
}
?>

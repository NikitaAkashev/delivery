<?php

defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modelitem');

class CalculatorModelsTerminal extends CalculatorModelsDefault
{
	// список терминалов в области, к которой относится переданный город
	static function GetTerminalsByCity($city){
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
	
	// Получить терминал по его идентификатору
	static function GetTerminal($terminal){
		
		$terminal = empty($terminal) ? -1 : $terminal;
		
		$db = JFactory::getDbo();
		
		$query = "
select 
	t.terminal,
	t.name
from #__calc_terminal t
where
	t.terminal = ".$db->quote($terminal)."
		";
				 
		$db->setQuery($query);
		 
		$result = $db->loadObject();
		
		return $result;
	}
}
?>

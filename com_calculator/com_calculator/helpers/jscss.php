<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

// Подгружает необходимый JS для компонента
class CalculatorHelpersJscss
{
	static function load()
	{
		$document = JFactory::getDocument();
		 
		//stylesheets
		$document->addStylesheet(JURI::base().'components/com_calculator/assets/css/com_calculator.css');
		$document->addStylesheet(JURI::base().'media/chosen/chosen.min.css');
		$document->addStylesheet(JURI::base().'media/system/css/calendar-jos.css');
		 
		//javascripts
		$document->addScript(JURI::base().'media/jui/js/jquery.min.js');
		$document->addScript(JURI::base().'media/chosen/chosen.jquery.min.js');
		$document->addScript(JURI::base().'media/system/js/calendar.js');
		$document->addScript(JURI::base().'media/system/js/calendar-setup.js');
		$document->addScript(JURI::base().'components/com_calculator/assets/js/com_calculator.js');
	}
}
?>

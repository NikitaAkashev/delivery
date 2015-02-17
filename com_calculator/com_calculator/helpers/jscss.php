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
		$document->addStylesheet('components/com_calculator/assets/css/com_calculator.css');
		$document->addStylesheet('media/jui/css/chosen.css');
		$document->addStylesheet('media/system/css/calendar-jos.css');
		
		//javascripts
		JHtml::_('jquery.framework');
		$document->addScript('media/jui/js/chosen.jquery.min.js');
		$document->addScript('components/com_calculator/assets/js/com_calculator.js');
	}
}
?>

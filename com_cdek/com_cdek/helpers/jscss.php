<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

// Подгружает необходимый JS для компонента
class CdekHelpersJscss
{
	static function load()
	{
		$document = JFactory::getDocument();
		
		//stylesheets
		$document->addStylesheet('components/com_cdek/assets/css/com_cdek.css');
		$document->addStylesheet('media/jui/css/chosen.css');
		$document->addStylesheet('media/system/css/calendar-jos.css');
		
		//javascripts
		JHtml::_('jquery.framework');
		$document->addScript('media/jui/js/chosen.jquery.min.js');
		$document->addScript('components/com_cdek/assets/js/com_cdek.js');
	}
}
?>

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
		$document->addStylesheet('components/com_cdek/assets/css/jquery-ui.min.css');
		
		//javascripts
		JHtml::_('jquery.framework');
		$document->addScript('components/com_cdek/assets/js/jquery-ui.min.js');
		$document->addScript('components/com_cdek/assets/js/com_cdek.js');
	}
}
?>

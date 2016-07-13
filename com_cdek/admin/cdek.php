<?php
defined('_JEXEC') or die('Restricted access');

if (!JFactory::getUser()->authorise('core.manage', 'com_cdek'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

$document = JFactory::getDocument();

$controller = JControllerLegacy::getInstance('Cdek');
 
$controller->execute(JFactory::getApplication()->input->get('task'));

$controller->redirect();
?>
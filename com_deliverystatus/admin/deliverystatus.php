<?php
defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$document->addStyleDeclaration('.icon-deliverystatus {background-image: url(../media/com_deliverystatus/images/tux-16x16.png);}');

$controller = JControllerLegacy::getInstance('DeliveryStatus');
 
$controller->execute(JFactory::getApplication()->input->get('task'));

$controller->redirect();
?>
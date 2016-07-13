<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
 
//sessions
jimport( 'joomla.session.session' );
 
//load tables
JTable::addIncludePath(JPATH_COMPONENT.'/tables');
 
//load classes
JLoader::registerPrefix('Cdek', JPATH_COMPONENT);
 
//Load plugins
JPluginHelper::importPlugin('cdek');

//Load styles and javascripts
CdekHelpersJscss::load();

//application
$app = JFactory::getApplication();
 
// Require specific controller if requested
if($controller = $app->input->get('controller','order')) {
	require_once (JPATH_COMPONENT.'/controllers/'.$controller.'.php');
}
 
// Create the controller
$classname = 'CdekControllers'.$controller;

$controller = new $classname();

$controller->execute();

?>

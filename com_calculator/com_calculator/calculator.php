<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
 
//sessions
jimport( 'joomla.session.session' );
 
//load tables
JTable::addIncludePath(JPATH_COMPONENT.'/tables');
 
//load classes
JLoader::registerPrefix('Calculator', JPATH_COMPONENT);
 
//Load plugins
JPluginHelper::importPlugin('calculator');

//Load styles and javascripts
CalculatorHelpersJscss::load();

//application
$app = JFactory::getApplication();
 
// Require specific controller if requested
if($controller = $app->input->get('controller','order')) {
	require_once (JPATH_COMPONENT.'/controllers/'.$controller.'.php');
}
 
// Create the controller
$classname = 'CalculatorControllers'.$controller;

$controller = new $classname();

$controller->execute();

?>

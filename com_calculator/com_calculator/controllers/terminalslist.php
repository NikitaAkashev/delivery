<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
class CalculatorControllersTerminalslist extends CalculatorControllersDefault
{
	function execute()
	{		
		$app = JFactory::getApplication();
		
		$viewName = $app->input->get('view', 'terminalslist');
		
		$app->input->set('view', $viewName);
		
		return parent::execute();
	}
}
?>

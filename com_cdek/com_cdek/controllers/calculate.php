<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
class CdekControllersCalculate extends CdekControllersDefault
{
	function execute()
	{
		$app = JFactory::getApplication();
		$viewName = $app->input->get('view', 'calculate');
		$app->input->set('view', $viewName);
		
		return parent::execute();
	}
}
?>

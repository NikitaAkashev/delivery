<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
class CdekControllersOrder extends CdekControllersDefault
{
	function execute()
	{
		$app = JFactory::getApplication();
		$viewName = $app->input->get('view', 'order');
		$app->input->set('layout','normal');
		$app->input->set('view', $viewName);
		
		return parent::execute();
	}
}
?>

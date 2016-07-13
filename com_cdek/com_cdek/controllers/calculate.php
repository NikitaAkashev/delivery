<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
class CdekControllersCalculate extends CdekControllersDefault
{
	function execute()
	{
		$app = $this->getApplication();
		$app->input->set('view', 'calculate');
		
		return parent::execute();
	}
}
?>

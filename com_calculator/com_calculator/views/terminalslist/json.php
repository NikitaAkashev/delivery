<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
class CalculatorViewsTerminalslistJson extends JViewHtml
{
        function render() 
        {
			$app = JFactory::getApplication();
			 
			$city = $app->input->getInt("city", -1);
			
			$model = new CalculatorModelsTerminal();
			
			$data = $model->GetTerminalsByCity($city);
						
			header('Content-Type: application/json');
			echo json_encode($data);
        }
}
?>

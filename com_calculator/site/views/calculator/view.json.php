<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HelloWorld Component
 */
class CalculatorViewCalculator extends JViewLegacy
{
        // Overwriting JView display method
        function display($tpl = null) 
        {
			$model = $this->getModel();
			
			$city = JRequest::getInt("city", -1);
			
			$data = $model->GetTerminalsByCity($city);
						
			header('Content-Type: application/json');
			echo json_encode($data);
        }
}
?>

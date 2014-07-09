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
			$model->Calculate();
			$cities = $model->GetCities();
			$tariffs = $model->GetTariffs();
						
			$this->assignRef('model', $model);
			$this->assignRef('cities', $cities);
			$this->assignRef('tariffs', $tariffs);
			
			parent::display($tpl);
        }
}
?>

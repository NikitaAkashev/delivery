<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HelloWorld Component
 */
class CalculatorViewsOrderHtml extends JViewHtml
{
        // Overwriting JView display method
        function render() 
        {						 
			$model = new CalculatorModelsOrder();
			
			$model->Calculate();
			
			$model->MakeOrder();
			
			$cities = CalculatorModelsCity::GetCities();
			
			$this->model = $model;
			$this->cities = $cities;
			
			return parent::render();
        }
}
?>

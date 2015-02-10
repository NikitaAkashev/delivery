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
			$app = JFactory::getApplication();
			
			$view = $app->input->get('view');
			 
			$model = new CalculatorModelsOrder();
			$model->Calculate(1);
			if($model->IsInnerPriceViewer()){
				$model->Calculate(0);
			}
			
			$model->MakeOrder();
			
			$cities = CalculatorModelsCity::GetCities();
			
			$terminals = array(
				'from' => CalculatorModelsTerminal::GetTerminalsByCity($model->city_from),
				'to' => CalculatorModelsTerminal::GetTerminalsByCity($model->city_to)
			);
						
			$this->model = $model;
			$this->cities = $cities;
			$this->terminals =$terminals;
			
			return parent::render();
        }
}
?>

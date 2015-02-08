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
			$type = $app->input->get('type');
			$view = $app->input->get('view');
			 
			$model = new CalculatorModelsOrder();
			$model->Calculate(1);
			if($model->IsInnerPriceViewer()){
				$model->Calculate(0);
			}
			
			$model->MakeOrder();
			
			$cities = $model->GetCities();
			
			$terminals = array(
				'from' => $model->GetTerminalsByCity($model->city_from),
				'to' => $model->GetTerminalsByCity($model->city_to)
			);
						
			$this->model = $model;
			$this->cities = $cities;
			$this->terminals =$terminals;
			
			return parent::render();
        }
}
?>

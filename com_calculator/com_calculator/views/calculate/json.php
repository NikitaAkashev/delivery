<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
class CalculatorViewsCalculateJson extends JViewHtml
{
        function render() 
        {
			$app = JFactory::getApplication();
			
			$model = new CalculatorModelsOrder();
			$model->Calculate(1);
			if($model->IsInnerPriceViewer()){
				$model->Calculate(0);
			}
		
			$data['calculated'] = !empty($model->prices);
			$data['prices'] = $model->prices;
			/*$data['calculated_inner'] = !empty($model->inner_price);
			
			$data['price'] = $model->price;
			$data['inner_price'] = $model->inner_price;
			
			$data['nds_part'] = $model->nds_part;
			$data['nds_part_inner'] = $model->nds_part_inner;
			
			$data['min_delivery_time'] = $model->min_delivery_time;
			$data['max_delivery_time'] = $model->max_delivery_time;	
			
			$data['volume'] = $model->volume;
			
			$data['profit'] = $model->profit;
			$data['profit_nds_part'] = $model->profit_nds_part;
			*/
			header('Content-Type: application/json');
			echo json_encode($data);
        }
}
?>

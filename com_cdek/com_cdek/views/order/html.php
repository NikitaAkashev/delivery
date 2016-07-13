<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HelloWorld Component
 */
class CdekViewsOrderHtml extends JViewHtml
{
        // Overwriting JView display method
        function render() 
        {						 
			$model = new CdekModelsOrder();
			
			$model->Calculate();
			
			$model->MakeOrder();
						
			$this->model = $model;
			
			return parent::render();
        }
}
?>

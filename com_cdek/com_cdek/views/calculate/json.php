<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
class CdekViewsCalculateJson extends JViewHtml
{
        function render() 
        {
			$app = JFactory::getApplication();
			
			$model = new CdekModelsOrder();
			
			$data = $model->Calculate();
		
			header('Content-Type: application/json');
			echo json_encode($data);
        }
}
?>

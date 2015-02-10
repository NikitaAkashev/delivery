<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

// возвращает вьюху
class CalculatorHelpersView
{
	static function load($viewName, $layoutName='normal', $viewFormat='html', $vars=null)
	{
		// Get the application
		$app = JFactory::getApplication();
		 
		$app->input->set('view', $viewName);
		 
		// Register the layout paths for the view
		$paths = new SplPriorityQueue;
		$paths->insert(JPATH_COMPONENT . '/views/' . $viewName . '/tmpl', 'normal');
		$viewClass = 'CalculatorViews' . ucfirst($viewName) . ucfirst($viewFormat);
		$modelClass = 'CalculatorModels' . ucfirst($viewName);
		 
		if (false === class_exists($modelClass))
		{
			$modelClass = 'CalculatorModelsDefault';
		}
		 
		$view = new $viewClass(new $modelClass, $paths);
		 
		$view->setLayout($layoutName);
		if(isset($vars))
		{
			foreach($vars as $varName => $var)
			{
				$view->$varName = $var;
			}
		}
	 
	return $view;
	}
}
?>

<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

// возвращает вьюху
class CdekHelpersView
{
	static function load($viewName, $layoutName='normal', $viewFormat='html', $vars=null)
	{
		// Get the application
		$app = JFactory::getApplication();
		 
		$app->input->set('view', $viewName);
		 
		// Register the layout paths for the view
		$paths = new SplPriorityQueue;
		$paths->insert(JPATH_COMPONENT . '/views/' . $viewName . '/tmpl', 'normal');
		$viewClass = 'CdekViews' . ucfirst($viewName) . ucfirst($viewFormat);
		$modelClass = 'CdekModels' . ucfirst($viewName);
		 
		if (false === class_exists($modelClass))
		{
			$modelClass = 'CdekModelsDefault';
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

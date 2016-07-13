<?php defined( '_JEXEC' ) or die( 'Restricted access' );
class CdekControllersDefault extends JControllerBase
{
	public function execute()
	{
		// Get the application
		$app = $this->getApplication();
		 
		// Get the document object.
		$document = $app->getDocument();
		$viewName = $app->input->getWord('view', 'order');
		$viewFormat = $document->getType();
		$layoutName = $app->input->getWord('layout', 'normal');
		 
		$app->input->set('view', $viewName);
		 
		// Register the layout paths for the view
		$paths = new SplPriorityQueue;
		$paths->insert(JPATH_COMPONENT . '/views/' . $viewName . '/tmpl', 'order');
		 
		$viewClass = 'CdekViews' . ucfirst($viewName) . ucfirst($viewFormat);
		$modelClass = 'CdekModels' . ucfirst($viewName);
		 
		if (false === class_exists($modelClass))
		{
			$modelClass = 'CdekModelsDefault';
		}
		 
		$view = new $viewClass(new $modelClass, $paths);
		$view->setLayout($layoutName);
		 
		// Render our view.
		echo $view->render();
		 
		return true;
	}
}

?>

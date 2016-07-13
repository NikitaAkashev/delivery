<?php
defined('_JEXEC') or die('Restricted access');

class CdekControllerTariffs extends JControllerAdmin
{
	public function getModel($name = 'Tariff', $prefix = 'CdekModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}
?>
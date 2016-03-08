<?php
defined('_JEXEC') or die('Restricted access');

class DeliveryStatusControllerParcels extends JControllerAdmin
{
	public function getModel($name = 'Parcel', $prefix = 'DeliveryStatusModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}
?>
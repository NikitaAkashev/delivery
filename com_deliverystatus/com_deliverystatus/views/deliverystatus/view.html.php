<?php

defined('_JEXEC') or die('Restricted access');
require_once( JPATH_ADMINISTRATOR.'/components/com_deliverystatus/models/parcel.php' );

class DeliveryStatusViewDeliveryStatus extends JViewLegacy
{
	function display($tpl = null)
	{		
		$this->parcel = $this->get('ParcelInfo');
		
		JLoader::import( 'Parcel', JPATH_ADMINISTRATOR . '/' . 'components' . '/' . 'com_deliverystatus/models' );
		$status_model = new DeliveryStatusModelParcel();
		$this->statuses = $status_model->getStatuses($this->parcel['parcel']);
		
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
 
			return false;
		}
 
		parent::display($tpl);
	}
}
?>
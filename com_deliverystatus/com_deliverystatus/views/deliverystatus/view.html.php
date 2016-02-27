<?php

defined('_JEXEC') or die('Restricted access');
require_once( JPATH_ADMINISTRATOR.'/components/com_deliverystatus/models/parcel.php' );

class DeliveryStatusViewDeliveryStatus extends JViewLegacy
{
	function display($tpl = null)
	{		
		$this->parcel = $this->get('ParcelInfo');
		$this->item_id = $this->get('Itemid');

		$jinput = JFactory::getApplication()->input;
		$this->parcel_number = $jinput->get('parcel_number', 0, 'string');
		
		JLoader::import( 'Parcel', JPATH_ADMINISTRATOR . '/' . 'components' . '/' . 'com_deliverystatus/models' );
		$status_model = new DeliveryStatusModelParcel();
		if (array_key_exists('parcel', $this->parcel)) {
			$this->statuses = $status_model->getStatuses($this->parcel['parcel']);
		}else 
		{
			$this->statuses = array();
		}	
		
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
 
			return false;
		}
 
		parent::display($tpl);
	}
}
?>
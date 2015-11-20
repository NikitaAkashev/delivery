<?php
defined('_JEXEC') or die('Restricted access');

class DeliveryStatusTableDeliveryParcel2ParcelStatus extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__delivery_parcel2parcel_status', 'parcel2parcel_status', $db);
	}
}
?>
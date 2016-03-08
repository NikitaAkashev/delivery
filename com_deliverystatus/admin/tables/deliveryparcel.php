<?php
defined('_JEXEC') or die('Restricted access');

class DeliveryStatusTableDeliveryParcel extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__delivery_parcel', 'parcel', $db);
	}
}
?>
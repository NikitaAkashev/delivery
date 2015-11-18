<?php
defined('_JEXEC') or die('Restricted access');

class DeliveryStatusTableDeliveryStatus extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__delivery_parcel', 'parcel', $db);
	}
}
?>
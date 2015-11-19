<?php
defined('_JEXEC') or die('Restricted access');

class DeliveryStatusTableDeliveryUser extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__delivery_user', 'user', $db);
	}
}
?>
<?php
defined('_JEXEC') or die('Restricted access');

class DeliveryStatusModelParcels extends JModelList
{
	protected function getListQuery()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select('*')
		->from($db->quoteName('#__delivery_parcel'));

		return $query;
	}
}
?>
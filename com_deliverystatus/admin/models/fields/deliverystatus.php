<?php

defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('list');

class JFormFieldDeliveryStatus extends JFormFieldList
{
	protected $type = 'DeliveryStatus';

	protected function getOptions()
	{
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('parcel_number,parcel_number');
		$query->from('#__delivery_parcel');
		$db->setQuery((string) $query);
		$messages = $db->loadObjectList();
		$options  = array();

		if ($messages)
		{
			foreach ($messages as $message)
			{
				$options[] = JHtml::_('select.option', $message->parcel_number, $message->parcel_number);
			}
		}

		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
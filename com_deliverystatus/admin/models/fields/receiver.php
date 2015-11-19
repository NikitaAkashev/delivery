<?php

defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('combo');

class JFormFieldReceiver extends JFormFieldCombo
{
	protected $type = 'Receiver';

	protected function getOptions()
	{
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('distinct receiver');
		$query->from('#__delivery_parcel');
		$query->order('receiver ASC');
		$db->setQuery((string) $query);
		$messages = $db->loadObjectList();
		$options  = array();

		if ($messages)
		{
			foreach ($messages as $message)
			{
				$options[] = JHtml::_('select.option', $message->receiver, $message->receiver);
			}
		}

		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
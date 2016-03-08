<?php

defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('combo');

class JFormFieldPayer extends JFormFieldCombo
{
	protected $type = 'Payer';

	protected function getOptions()
	{
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('distinct payer');
		$query->from('#__delivery_parcel');
		$query->order('sender ASC');
		$db->setQuery((string) $query);
		$messages = $db->loadObjectList();
		$options  = array();

		if ($messages)
		{
			foreach ($messages as $message)
			{
				$options[] = JHtml::_('select.option', $message->payer, $message->payer);
			}
		}

		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
<?php

defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('list');

class JFormFieldParcelStatus extends JFormFieldList
{
	protected $type = 'ParcelStatus';

	protected function getOptions()
	{
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('parcel_status, name');
		$query->from('#__delivery_parcel_status');
		$query->order('name ASC');
		$db->setQuery((string) $query);
		$users = $db->loadObjectList();
		$options  = array();

		$options[] = JHtml::_('select.option', "", "Не добавлять");
		
		if ($users)
		{
			foreach ($users as $user)
			{
				$options[] = JHtml::_('select.option', $user->parcel_status, $user->name);
			}
		}

		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
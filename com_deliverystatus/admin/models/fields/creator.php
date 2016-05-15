<?php

defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('list');

class JFormFieldCreator extends JFormFieldList
{
	protected $type = 'Creator';

	protected function getOptions()
	{
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('u.id user, u.name');
		$query->from('#__users u');
		$query->order('u.name ASC');
		$db->setQuery((string) $query);
		$users = $db->loadObjectList();
		$options  = array();

		if ($users)
		{
			foreach ($users as $user)
			{
				$options[] = JHtml::_('select.option', $user->user, $user->name);
			}
		}

		$options = array_merge(parent::getOptions(), $options);

		$this->value = JFactory::getUser()->id;
		
		return $options;
	}
}
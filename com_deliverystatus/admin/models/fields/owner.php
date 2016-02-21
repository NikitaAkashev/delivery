<?php

defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('list');

class JFormFieldOwner extends JFormFieldList
{
	protected $type = 'Owner';

	protected function getOptions()
	{
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('u.id user, du.contract_name, u.name');
		$query->from('#__delivery_user du');
		$query->join('inner', '#__users u on u.id = du.user');
		$query->order('contract_name ASC');
		$db->setQuery((string) $query);
		$users = $db->loadObjectList();
		$options  = array();

		$options[] = JHtml::_('select.option', "", "Не выбран");
		
		if ($users)
		{
			foreach ($users as $user)
			{
				$options[] = JHtml::_('select.option', $user->user, $user->name.' ('.$user->contract_name.')');
			}
		}

		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
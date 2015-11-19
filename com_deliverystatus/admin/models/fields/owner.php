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
		$query->select('user, contract_name');
		$query->from('#__delivery_user');
		$query->order('contract_name ASC');
		$db->setQuery((string) $query);
		$users = $db->loadObjectList();
		$options  = array();

		$options[] = JHtml::_('select.option', "", "Не выбран");
		
		if ($users)
		{
			foreach ($users as $user)
			{
				$options[] = JHtml::_('select.option', $user->user, $user->contract_name);
			}
		}

		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
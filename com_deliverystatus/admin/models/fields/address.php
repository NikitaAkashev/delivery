<?php

defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('combo');

class JFormFieldAddress extends JFormFieldCombo
{
	protected $type = 'Address';

	protected function getOptions()
	{
		$db    = JFactory::getDBO();
		$query = "
			select distinct c.address from (
				select address_from as address from #__delivery_parcel
				union all
				select address_to as address from #__delivery_parcel
				) c
			order by c.address
				";
		$db->setQuery($query);
		$messages = $db->loadObjectList();
		$options  = array();

		if ($messages)
		{
			foreach ($messages as $message)
			{
				$options[] = JHtml::_('select.option', $message->address, $message->address);
			}
		}

		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
<?php
defined('_JEXEC') or die('Restricted access');
class CdekModelTariffs extends JModelList
{
	public function __construct($config = array())	
	{		
		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState('tariff_name', 'asc');
	}
	
	protected function getListQuery()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("tariff, tariff_id, tariff_name, published")
		->from($db->quoteName('#__cdek_tariff'));
		
		$orderCol	= $this->state->get('list.ordering', 'tariff_name');
		$orderDirn 	= $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
		return $query;
	}
}
?>
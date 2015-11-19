<?php
defined('_JEXEC') or die('Restricted access');

class DeliveryStatusModelParcels extends JModelList
{
	public function __construct($config = array())	
	{		
		if (empty($config['filter_fields']))		
		{			
			$config['filter_fields'] = array('parcel', 'parcel_number',	'is_enabled');		
		} 		
		parent::__construct($config);	
	}
	
	protected function getListQuery()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
		->from($db->quoteName('#__delivery_parcel'));
		
		$search = $this->getState('filter.search');
		if (!empty($search))		
		{			
			$like = $db->quote('%' . $search . '%');			
			$query->where('parcel_number LIKE ' . $like);		
		} 
		$is_enabled = $this->getState('filter.is_enabled');
		if (is_numeric($is_enabled))
		{			
			$query->where('is_enabled = ' . (int) $is_enabled);		
		}		
		elseif ($is_enabled === '')		
		{			
			$query->where('(is_enabled IN (0, 1))');
		} 
		$orderCol	= $this->state->get('list.ordering', 'parcel_number');
		$orderDirn 	= $this->state->get('list.direction', 'asc');
		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
		
		return $query;
	}
}
?>
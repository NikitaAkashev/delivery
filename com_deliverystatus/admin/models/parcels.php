<?php
defined('_JEXEC') or die('Restricted access');
class DeliveryStatusModelParcels extends JModelList
{
	public function __construct($config = array())	
	{		
		if (empty($config['filter_fields']))		
		{			
			$config['filter_fields'] = array(
					'parcel', 'p.parcel', 
					'parcel_number', 'p.parcel_number',
					'sender', 'p.sender', 
					'receiver', 'p.receiver', 
					'status_name', 
					'published', 'p.published',
					'outer_id', 'p.outer_id');		
		} 		
		parent::__construct($config);	
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState('parcel', 'desc');
	}
	
	protected function getListQuery()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("p.*, coalesce(s.name, '".JText::_('COM_DELIVERYSTATUS_HAS_NO_STATUS')."') status_name")
		->from($db->quoteName('#__delivery_parcel', 'p'))
		->leftJoin("(
		select ps.parcel, max(ps.dt) dt
		from #__delivery_parcel p
			join #__delivery_parcel2parcel_status ps on ps.parcel = p.parcel
		group by ps.parcel
	) pdt on p.parcel = pdt.parcel")
		->leftJoin("#__delivery_parcel2parcel_status ps on ps.parcel = p.parcel and ps.dt = pdt.dt")
		->leftJoin("#__delivery_parcel_status s on s.parcel_status = ps.parcel_status");		
			
		$search = $this->getState('filter.search');
		if (!empty($search))		
		{			
			$like = $db->quote('%' . $search . '%');			
			$query->where('parcel_number LIKE ' . $like);		
		} 
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{			
			$query->where('published = ' . (int) $published);		
		}		
		elseif ($published === '')		
		{			
			$query->where('(published IN (0, 1))');
		} 
		$orderCol	= $this->state->get('list.ordering', 'parcel');
		$orderDirn 	= $this->state->get('list.direction', 'desc');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
		return $query;
	}
}
?>
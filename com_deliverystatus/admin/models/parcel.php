<?php
defined('_JEXEC') or die('Restricted access');

class DeliveryStatusModelParcel extends JModelAdmin
{
	public function getTable($type = 'DeliveryParcel', $prefix = 'DeliveryStatusTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm(
				'com_deliverystatus.parcel',
				'parcel',
				array(
						'control' => 'jform',
						'load_data' => $loadData
				)
		);
	
		if (empty($form))
		{
			return false;
		}
	
		return $form;
	}
	
	public function getStatusForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm(
				'com_deliverystatus.status',
				'status',
				array(
						'control' => 'jform',
						'load_data' => $loadData
				)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}
	
	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState(
				'com_deliverystatus.edit.parcel.data',
				array()
		);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	public function getScripts()
	{
		return array(
				'administrator/components/com_deliverystatus/models/forms/parcel.js',
				'administrator/components/com_deliverystatus/assets/js/jquery.datetimepicker.full.min.js'
		);
	}
	
	public function getStyles() 	
	{		
		return array(
			'administrator/components/com_deliverystatus/assets/css/jquery.datetimepicker.css'
		);	
	}
	
	public function getStatuses($parcel)
	{
		$db    = JFactory::getDBO();
		$query = "
			select ps.dt, s.name
			from #__delivery_parcel2parcel_status ps
				join #__delivery_parcel_status s on s.parcel_status = ps.parcel_status
			where ps.parcel = " . (int)$parcel . "
			order by ps.dt
				";
		$db->setQuery($query);
		$statuses = $db->loadObjectList();
		
		return $statuses;
	}
	
	public function save($data)
	{
		if (!parent::save($data)) {
			return false;
		}
		
		if($data['delivery_status'])
		{
			$table = $this->GetTable('DeliveryParcel2ParcelStatus', $prefix = 'DeliveryStatusTable');
			
			die(print_r($data));
		}
		
		return true;
	}
}

?>
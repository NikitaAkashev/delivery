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
		
		$parcel = $data['parcel'];
		if(!$parcel){
			$parcel = $this->getState($this->getName() . '.id');
		}
		
		$jinput = JFactory::getApplication()->input;
		$data_status = $jinput->post->get('jform', array(), 'array');
		$form_status = $this->getStatusForm($data_status, false);
		$data_status_valid = $this->validate($form_status, $data_status);
		$data_status_valid['parcel'] = $parcel;
		
		if($data_status_valid['parcel_status'])
		{
			$table = $this->GetTable('DeliveryParcel2ParcelStatus', $prefix = 'DeliveryStatusTable');
			if (!$table->bind($data_status_valid))
			{
				$this->setError($table->getError());	
				return false;
			}
			if (!$table->check())
			{
				$this->setError($table->getError());			
				return false;
			}
			if (!$table->store())
			{
				$this->setError($table->getError());			
				return false;
			}
		}
		
		return true;
	}
}

?>
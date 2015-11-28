<?php
defined('_JEXEC') or die('Restricted access');

class DeliveryStatusModelDeliveryStatus extends JModelItem
{
	protected $info;
	protected $user_from_request;
	protected $user_current;

	public function getTable($type = 'DeliveryParcel', $prefix = 'DeliveryStatusTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getParcelInfo()
	{		
		$jinput = JFactory::getApplication()->input;
		$parcel_number_string = $jinput->get('parcel_number', 0, 'string');
		
		$info = explode('/', $parcel_number_string);
		$parcel_number = $info[0];
		
		$contract_name = null;
		if(array_key_exists(1, $info))
			$contract_name = $info[1];
		
		$this->user_from_request = $this->getUserByContractName($contract_name);
		$this->user_current = JFactory::getUser()->id;
		
		if (!isset($this->info))
		{ 
			$table = $this->getTable();
			$fields = array('parcel_number' => (INT)$parcel_number);
			$table->load($fields);
			$this->info = $table->getProperties();
			$this->SecureData();
		}
 
		return $this->info;
	}
	
	private function getUserByContractName($contract_name)
	{
		if (!$contract_name)
			return null;
		
		$table = $this->getTable('DeliveryUser');
		$table->load(array('contract_name' => $contract_name));
		
		return $table->user;
	}
	
	private function SecureData()
	{
		if(
			!$this->info['owner'] 
			||
			($this->info['owner'] != $this->user_current && $this->info['owner'] != $this->user_from_request) 
		)
		{
			unset($this->info['sender']);
			unset($this->info['receiver']);
			unset($this->info['payer']);			
		}
	}
}
?>
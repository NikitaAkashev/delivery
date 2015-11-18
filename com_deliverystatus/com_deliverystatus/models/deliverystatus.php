<?php
defined('_JEXEC') or die('Restricted access');

class DeliveryStatusModelDeliveryStatus extends JModelItem
{
	protected $messages;

	public function getTable($type = 'DeliveryStatus', $prefix = 'DeliveryStatusTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getMsg()
	{
		$jinput = JFactory::getApplication()->input;
		$parcel_number = $jinput->get('parcel_number', 0, 'INT');
			
		if (!is_array($this->messages))
		{
			$this->messages = array();
		}
 
		if (!isset($this->messages[$parcel_number]))
		{ 
			$table = $this->getTable();
			$fields = array('parcel_number' => $parcel_number);
			$table->load($fields);
			
			$this->messages[$parcel_number] = $table->sender;
		}
 
		return $this->messages[$parcel_number];
	}
}
?>
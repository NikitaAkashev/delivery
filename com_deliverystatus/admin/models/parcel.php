<?php
defined('_JEXEC') or die('Restricted access');

class DeliveryStatusModelParcel extends JModelAdmin
{
	public function getTable($type = 'DeliveryStatus', $prefix = 'DeliveryStatusTable', $config = array())
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
}

?>
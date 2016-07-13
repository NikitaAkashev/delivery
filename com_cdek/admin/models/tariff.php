<?php
defined('_JEXEC') or die('Restricted access');

class CdekModelTariff extends JModelAdmin
{
	public function getTable($type = 'Tariff', $prefix = 'CdekTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm(
				'com_cdek.tariff',
				'tariff',
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
				'com_cdek.edit.tariff.data',
				array()
		);
		
		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}


	
	public function save($data)
	{
		if (!parent::save($data)) {
			return false;
		}

		return true;
	}
}

?>
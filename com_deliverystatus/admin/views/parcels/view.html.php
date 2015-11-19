<?php
defined('_JEXEC') or die('Restricted access');

class DeliveryStatusViewParcels extends JViewLegacy
{
	function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}
		
		$this->addToolBar();
		
		parent::display($tpl);
	}
	
	protected function addToolBar()	
	{		
		JToolBarHelper::title(JText::_('COM_DELIVERYSTATUS_MANAGER_PARCELS'));		
		JToolBarHelper::addNew('parcel.add');		
		JToolBarHelper::editList('parcel.edit');		
		JToolBarHelper::deleteList('', 'parcels.delete');	
	}
}
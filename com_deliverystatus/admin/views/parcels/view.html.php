<?php
defined('_JEXEC') or die('Restricted access');

class DeliveryStatusViewParcels extends JViewLegacy
{
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$context = "deliverystatus.list.admin.parcel";
		
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		
		$this->state = $this->get('State');
		$this->filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', 'parcel_number', 'cmd');
		$this->filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'cmd');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}
		
		$this->addToolBar();
		
		parent::display($tpl);
		
		$this->setDocument();
	}
	
	protected function addToolBar()	
	{		
		$title = JText::_('COM_DELIVERYSTATUS_MANAGER_PARCELS');
		if ($this->pagination->total)
		{
			$title .= "<span style='font-size: 0.5em; vertical-align: middle;'>(" . $this->pagination->total . ")</span>";
		}
		JToolBarHelper::title($title, 'deliverystatus');
		JToolBarHelper::deleteList('', 'parcels.delete');
		JToolBarHelper::editList('parcel.edit');
		JToolBarHelper::addNew('parcel.add');
	}
	
	protected function setDocument()
	{		
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_DELIVERYSTATUS_ADMINISTRATION'));	
	}
}
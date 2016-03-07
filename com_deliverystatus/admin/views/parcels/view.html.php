<?php
defined('_JEXEC') or die('Restricted access');

class DeliveryStatusViewParcels extends JViewLegacy
{
	protected $items;
	
	protected $pagination;
	
	protected $state;
	
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		
		$context = 'delivery_status.admin.list';		
		$this->state		= $this->get('State');
		// костыль для определения сортировки при возврате из редактирования
		$this->state->set('list.ordering', $app->getUserStateFromRequest($context.'filter_order', 'filter_order', 'parcel', 'cmd'));
		$this->state->set('list.direction', $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'cmd'));
		
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		
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
		JToolBarHelper::addNew('parcel.add');
		JToolBarHelper::editList('parcel.edit');
		JToolBarHelper::deleteList('', 'parcels.delete');
		JToolBarHelper::publishList('parcels.publish');
		JToolBarHelper::unpublishList('parcels.unpublish');
		// Options button.
		if (JFactory::getUser()->authorise('core.admin', 'com_deliverystatus'))     {	
			JToolBarHelper::preferences('com_deliverystatus');
		}
	}
	
	protected function setDocument()
	{		
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_DELIVERYSTATUS_ADMINISTRATION'));	
	}
}
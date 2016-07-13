<?php
defined('_JEXEC') or die('Restricted access');

class CdekViewTariffs extends JViewLegacy
{
	protected $items;
	
	protected $pagination;
	
	protected $state;
	
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		
		$context = 'cdek.admin.list';
		$this->state		= $this->get('State');
		// костыль для определения сортировки при возврате из редактирования
		$this->state->set('list.ordering', $app->getUserStateFromRequest($context.'filter_order', 'filter_order', 'tariff_name', 'cmd'));
		$this->state->set('list.direction', $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'cmd'));
		
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
				
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
		$title = JText::_('COM_CDEK_MANAGER_TARIFFS');
		if ($this->pagination->total)
		{
			$title .= "<span style='font-size: 0.5em; vertical-align: middle;'>(" . $this->pagination->total . ")</span>";
		}
		JToolBarHelper::title($title, 'cdek');
		JToolBarHelper::addNew('tariff.add');
		JToolBarHelper::editList('tariff.edit');
		JToolBarHelper::deleteList('', 'tariffs.delete');
		JToolBarHelper::publishList('tariffs.publish');
		JToolBarHelper::unpublishList('tariffs.unpublish');
		// Options button.
		if (JFactory::getUser()->authorise('core.admin', 'com_cdek'))     {
			JToolBarHelper::preferences('com_cdek');
		}
	}
	
	protected function setDocument()
	{		
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_CDEK_ADMINISTRATION'));
	}
}
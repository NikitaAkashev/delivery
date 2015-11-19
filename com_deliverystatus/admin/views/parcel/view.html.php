<?php
defined('_JEXEC') or die('Restricted access');

class DeliveryStatusViewParcel extends JViewLegacy
{
	protected $form = null;

	public function display($tpl = null)
	{
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		
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
		$input = JFactory::getApplication()->input;

		$input->set('hidemainmenu', true);

		$isNew = ($this->item->parcel == 0);

		if ($isNew)
		{
			$title = JText::_('COM_DELIVERYSTATUS_MANAGER_PARCEL_NEW');
		}
		else
		{
			$title = JText::_('COM_DELIVERYSTATUS_MANAGER_PARCEL_EDIT');
		}

		JToolBarHelper::title($title, 'deliverystatus');
		JToolBarHelper::save('parcel.save');
		JToolBarHelper::cancel('parcel.cancel',	$isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
	}
	
	protected function setDocument() 
	{
		$isNew = ($this->item->parcel < 1);
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? 
					JText::_('COM_DELIVERYSTATUS_MANAGER_PARCEL_NEW') 
						: JText::_('COM_DELIVERYSTATUS_MANAGER_PARCEL_EDIT'));
	}
}
?>
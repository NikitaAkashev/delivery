<?php
defined('_JEXEC') or die('Restricted access');

class DeliveryStatusViewParcel extends JViewLegacy
{
	protected $form = null;

	public function display($tpl = null)
	{
		$this->form = $this->get('Form');
		$this->status_form = $this->get('StatusForm');
		$this->item = $this->get('Item');
		$this->scripts = $this->get('Scripts');
		$this->styles = $this->get('Styles');
		
		$model = $this->getModel();
		$this->statuses = $model->getStatuses($this->item->parcel);
		
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
		JToolBarHelper::apply('parcel.apply');
		JToolBarHelper::cancel('parcel.cancel',	$isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
		JToolBarHelper::save('parcel.save');
		JToolbarHelper::save2new('parcel.save2new');
	}
	
	protected function setDocument() 
	{
		$isNew = ($this->item->parcel < 1);
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? 
					JText::_('COM_DELIVERYSTATUS_MANAGER_PARCEL_NEW') 
						: JText::_('COM_DELIVERYSTATUS_MANAGER_PARCEL_EDIT'));

		foreach ($this->styles as $style)
			$document->addStyleSheet(JURI::root() . $style);
		
		foreach ($this->scripts as $script)
			$document->addScript(JURI::root() . $script);
			
		$document->addScript(JURI::root() . "administrator/components/com_deliverystatus"
				            . "/views/parcel/submitbutton.js");
		JText::script('COM_DELIVERYSTATUS_ERROR_UNACCEPTABLE');
	}
}
?>

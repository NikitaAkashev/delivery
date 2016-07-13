<?php
defined('_JEXEC') or die('Restricted access');

class CdekViewTariff extends JViewLegacy
{
	protected $form = null;

	public function display($tpl = null)
	{
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		
		$model = $this->getModel();
		
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

		$isNew = ($this->item->tariff_id == 0);

		if ($isNew)
		{
			$title = JText::_('COM_CDEK_MANAGER_TARIFF_NEW');
		}
		else
		{
			$title = JText::_('COM_CDEK_MANAGER_TARIFF_EDIT');
		}

		JToolBarHelper::title($title, 'cdek');
		JToolBarHelper::apply('tariff.apply');
		JToolBarHelper::save('tariff.save');
		JToolbarHelper::save2new('tariff.save2new');
		JToolBarHelper::cancel('tariff.cancel',	$isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
	}
	
	protected function setDocument() 
	{
		$isNew = ($this->item->tariff_id < 1);
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? 
					JText::_('COM_CDEK_MANAGER_TARIFF_NEW')
						: JText::_('COM_CDEK_MANAGER_TARIFF_EDIT'));

		JText::script('COM_CDEK_ERROR_UNACCEPTABLE');
	}
}
?>

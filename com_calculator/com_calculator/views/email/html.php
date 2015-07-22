<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HelloWorld Component
 */
class CalculatorViewsEmailHtml extends JViewHtml
{
	function render() 
	{			
		$this->extra = new stdClass();
		$this->extra->city_name_from = CalculatorModelsCity::GetCity($this->data->city_from);
		$this->extra->city_name_to = CalculatorModelsCity::GetCity($this->data->city_to);
		$this->extra->tariff_name = CalculatorModelsOrder::GetRateInfo($this->data);
					
		return parent::render();
	}
}
?>

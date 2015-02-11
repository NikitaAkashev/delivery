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
		$this->extra->terminal_from =CalculatorModelsTerminal::GetTerminal($this->data->from_terminal);
		$this->extra->terminal_to = CalculatorModelsTerminal::GetTerminal($this->data->to_terminal);
					
		return parent::render();
	}
}
?>

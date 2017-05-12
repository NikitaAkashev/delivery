<?php
defined('_JEXEC') or die('Restricted access');

class JFormRuleParcelNumber extends JFormRule
{
	protected $regex = '^[0-9а-я#№\/-]+$';
}
?>

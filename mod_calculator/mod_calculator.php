<?php

// no direct access
defined('_JEXEC') or die;
// Include the syndicate functions only once
require_once( dirname(__FILE__) . '/../../components/com_calculator/models/calculator.php' );
 
$cities = CalculatorModelCalculator::GetCities();
require( JModuleHelper::getLayoutPath('mod_calculator'));
?>

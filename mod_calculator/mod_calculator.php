<?php

// no direct access
defined('_JEXEC') or die;
// Include the syndicate functions only once
require_once( dirname(__FILE__) . '/../../components/com_calculator/models/calculator.php' );
 
$selected_city_from = 18; // екатеринбург

$city_to_variants = array(38, 55, 42, 65, 47, 72, 29, 70); //москва, питер, новосиб, тюмень, пермь, челябинск, краснодар, ханты-мансийск
$selected_city_to = $city_to_variants[array_rand($city_to_variants)];

$parcel_parameters_variant = array(
	array("weight"=>0.5, "width"=>28, "length"=>21, "height"=>5.5),
	array("weight"=>1.8, "width"=>25, "length"=>25, "height"=>15),
	array("weight"=>3.5, "width"=>35, "length"=>30, "height"=>20),
	array("weight"=>11, "width"=>50, "length"=>50, "height"=>25),
	array("weight"=>25, "width"=>100, "length"=>60, "height"=>25),
	array("weight"=>180, "width"=>100, "length"=>100, "height"=>100)		
);

$parcel = $parcel_parameters_variant[array_rand($parcel_parameters_variant)];

$cities = CalculatorModelCalculator::GetCities();
require( JModuleHelper::getLayoutPath('mod_calculator'));
?>

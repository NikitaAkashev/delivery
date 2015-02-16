<?php

// no direct access
defined('_JEXEC') or die;
// Include the syndicate functions only once
require_once( dirname(__FILE__) . '/../../components/com_calculator/models/default.php' );
require_once( dirname(__FILE__) . '/../../components/com_calculator/models/order.php' );
require_once( dirname(__FILE__) . '/../../components/com_calculator/models/city.php' );
 
$document = JFactory::getDocument();

JHtml::_('jquery.framework');
$document->addScript('media/jui/js/chosen.jquery.min.js');
$document->addStylesheet('media/jui/css/chosen.css');
		
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

$cities = CalculatorModelsCity::GetCities();

$model = new CalculatorModelsOrder();

$model->city_from = $selected_city_from;
$model->city_to = $selected_city_to;
$model->weight = $parcel["weight"];
$model->assessed_value = 0;
$model->width = $parcel["width"];
$model->length = $parcel["length"];
$model->height = $parcel["height"];

$model->is_express = 1;
$model->from_door = 0;

$model->Calculate(1);

require( JModuleHelper::getLayoutPath('mod_calculator'));
?>

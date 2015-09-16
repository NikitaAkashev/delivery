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

$city_to_variants = array(38, 55, 29, 51, 30); //Москву, Питер, Краснодар, Ростов-на-Дону, Красноярск
$selected_city_to = $city_to_variants[array_rand($city_to_variants)];

$parcel_parameters_variant = array(
	array("weight"=>0.5, "width"=>0, "length"=>0, "height"=>0)		
);

$parcel = $parcel_parameters_variant[array_rand($parcel_parameters_variant)];

$cities = CalculatorModelsCity::GetCities();

$model = new CalculatorModelsOrder();

$model->city_from = $selected_city_from;
$model->city_to = $selected_city_to;
$model->weight = $parcel["weight"];
$model->assessed_value = 0;

$model->Calculate();
$price = null;

foreach($model->prices as $p){
	if(strpos($p->tariff_name, 'Экспресс-Приоритет') !== false && $p->delivery_type_code == 'office.door'){
		$price = $p;		
		break;
	}
}

if ($price == null){
	$price = $model->prices[0];
}

require( JModuleHelper::getLayoutPath('mod_calculator'));
?>

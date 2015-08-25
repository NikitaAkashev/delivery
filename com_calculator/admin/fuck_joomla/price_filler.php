<?php

if (!array_key_exists("input", $_FILES)){
	
echo <<<TXT
<html>
<head>
	<title>Конвертор CSV to SQL</title>
</head>
<body>
	<form method="POST" action="" enctype="multipart/form-data" >
		<input type="file" name="input" />
		Префикс таблиц в БД<input type="text" name="prefix"/>
		<input type="submit" value="Преобразовать" />	
	</form>	
</body>
</html>



TXT;

}else{
	
	$prefix = $_POST["prefix"];
	
	$query = "";
	
	if (($handle = fopen($_FILES["input"]["tmp_name"], "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$r = array_map("FormatValue", $data);
			$r[7] = $r[7] == 'null' ? $r[7] : "'".$r[7]."'";
			
			$query .= <<<TXT
INSERT INTO {$prefix}_delivery_rate (`city_from`, `city_to`, `zone`, `tariff`, `provider`, `min_days`, `max_days`, `delivery_hours`, `is_enabled`)
values ({$r[0]}, {$r[1]}, {$r[2]}, {$r[3]}, {$r[4]}, {$r[5]}, {$r[6]}, '{$r[7]}', {$r[20]});

INSERT INTO {$prefix}_delivery_weight_price (`rate`,`from`,`to`,`base_price`,`overweight_cost`)
SELECT MAX(`rate`), {$r[8]}, {$r[9]}, {$r[10]}, {$r[11]}
FROM {$prefix}_delivery_rate;

INSERT INTO {$prefix}_delivery_weight_price (`rate`,`from`,`to`,`base_price`,`overweight_cost`)
SELECT MAX(`rate`), {$r[12]}, {$r[13]}, {$r[14]}, {$r[15]}
FROM {$prefix}_delivery_rate;

INSERT INTO {$prefix}_delivery_weight_price (`rate`,`from`,`to`,`base_price`,`overweight_cost`)
SELECT MAX(`rate`), {$r[16]}, {$r[17]}, {$r[18]}, {$r[19]}
FROM {$prefix}_delivery_rate;




TXT;
		}
		fclose($handle);
	}
		
	echo $query;
}

function FormatValue($value){
	
	if($value === null || $value == '')
	{
		return 'null';
	}
	
	return str_replace(",", ".", $value);
}
?>

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
	
	$old_tariff = 1;
	$query = "insert into `".$prefix."_calc_weight_price` (`tariff`, `zone`, `from`, `to`, `base_price`, `overweight_cost`) values ";
	
	if (($handle = fopen($_FILES["input"]["tmp_name"], "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if($old_tariff != $data[0]){
				$old_tariff = $data[0];
				
				$query = mb_substr($query, 0, -1);
				
				$query .= "; \r\ninsert into `".$prefix."_calc_weight_price` (`tariff`, `zone`, `from`, `to`, `base_price`, `overweight_cost`) values ";
			}
			$query .= "(".str_replace(",", ".", $data[0]).", ".str_replace(",", ".", $data[1]).", ".str_replace(",", ".", $data[2]).", ".str_replace(",", ".", $data[3]).", ".str_replace(",", ".", $data[4]).", ".str_replace(",", ".", $data[5])."),";
		}
		fclose($handle);
	}
	
	$query = mb_substr($query, 0, -1);
	$query .= ";";
	
	echo $query;
}

?>

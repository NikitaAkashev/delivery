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
		<input type="text" name="prefix"/>
		<input type="submit" value="Преобразовать" />	
	</form>	
</body>
</html>



TXT;

}else{
	
	$prefix = $_POST["prefix"];
	
	$rows_inserted = 0;
	$query = "insert into `".$prefix."_calc_city` (`name`, `factor`, `parent`) values ";
	
	if (($handle = fopen($_FILES["input"]["tmp_name"], "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if($rows_inserted == 500){
				$rows_inserted = 0;
				
				$query = mb_substr($query, 0, -1);
				$query .= ";";
				
				$query .= "; \r\ninsert into `".$prefix."_calc_city` (`name`, `factor`, `parent`) values ";
			}
			$query .= "('".$data[0]."', ".(array_key_exists($data, 1) ? $data[1] : "1").", ".(array_key_exists($data, 2) ? $data[2] : "null")."),";
			$rows_inserted ++;
		}
		fclose($handle);
	}
	if ($rows_inserted != 500){
		$query = mb_substr($query, 0, -1);
	}
	$query .= ";";
	
	echo $query;
}

?>

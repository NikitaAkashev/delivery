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
		<input type="submit" value="Преобразовать" />	
	</form>	
</body>
</html>



TXT;

}else{
	
	$converted = array();
	$row = 1;
	if (($handle = fopen($_FILES["input"]["tmp_name"], "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$num = count($data);			
			for ($c=0; $c < $num; $c++) {
				$converted[$row][$c+1] = $data[$c];				
			}
			$row++;
		}
		fclose($handle);
	}
	
	$rows_inserted = 0;
	
	$query = "insert into #__calc_direction2zone (city_from, city_to, zone) values ";
	
	for ($i = 1; $i <= count($converted); $i++){
		for ($j = 1; $j <= count($converted[$i]); $j++){
			if($converted[$i][$j] == 0){
				continue;
			}
			if($rows_inserted == 500){
				$rows_inserted = 0;
				
				$query = mb_substr($query, 0, -1);
				
				$query .= "; \r\ninsert into #__calc_direction2zone (city_from, city_to, zone) values ";
			}
			$query .= " (".$i.", ".$j.", ".$converted[$i][$j]."),";
				
			$rows_inserted ++;
		}
	}	
	
	if ($rows_inserted != 500){
		$query = mb_substr($query, 0, -1);
	}
	$query .= ";";
	
	echo $query;
}

?>

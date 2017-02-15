<?php

function GetOrdersRemote()
{
	$login = "";
	$password = "";
	$url = "";

	$date = date("Y-m-d", time() - 60 * 60 * 24 * 5);

	$secure = md5($date.'&'.$password);

	$xml ='<?xml version="1.0" encoding="UTF8"?>
	<StatusReport Date="'.$date.'" Account="'.$login.'" Secure="'.$secure.'" ShowHistory="1" >
		<ChangePeriod DateFirst ="2013-07-16" DateLast ="2017-07-17" / >
	</StatusReport>"';

	// создание нового ресурса cURL
	$ch = curl_init();
	// установка URL и других необходимых параметров
	curl_setopt($ch, CURLOPT_URL, $url.'?account='.$login.'&secure='.$secure.'&datefirst='.$date);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "?xml_request=".urlencode($xml));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
	// загрузка страницы и выдача её браузеру
	$out = curl_exec($ch);
	// завершение сеанса и освобождение ресурсов
	curl_close($ch);
		
	$order = simplexml_load_string ($out);
	
	$orders = array();
	
	foreach($order->Order as $ord )
	{
		$row = array();
		$attrs = $ord->attributes();
		$status_attrs = $ord->Status->attributes();
		
		$row['outer_id'] = (string)$attrs['DispatchNumber'];	
		$row['date'] = (string)$status_attrs['Date'];
		$row['code'] = (string)$status_attrs['Code'];
		
		$orders[] = $row;
	}
	
	return $orders;
}

function GetOrdersLocal()
{
	$table_prefix = 'fu28n_';
	$mysqli = new mysqli('', '', '', '');
	if ($mysqli->connect_error) {
		die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}
	
	$query = <<<TXT
select 
	p.parcel, 
	p.outer_id,
	s.code,
	ps.dt
from #__delivery_parcel p
left join (
		select ps.parcel, max(ps.dt) dt
		from #__delivery_parcel p
			join #__delivery_parcel2parcel_status ps on ps.parcel = p.parcel
		group by ps.parcel
	) pdt on p.parcel = pdt.parcel
left join #__delivery_parcel2parcel_status ps on ps.parcel = p.parcel and ps.dt = pdt.dt
left join #__delivery_parcel_status s on s.parcel_status = ps.parcel_status
where
    p.outer_id <> ''
    and p.outer_id is not null
TXT;
	
	$query = str_replace("#__", $table_prefix, $query);
	
	$result = $mysqli->query($query);
	
	$orders = $result->fetch_all(MYSQLI_ASSOC);
	
	return $orders;
}

print_r(GetOrdersRemote());
print_r(GetOrdersLocal());
?>
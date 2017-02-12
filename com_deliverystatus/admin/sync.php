<?php
$login = "";
$password = "";
$url = "http://int.cdek.ru/status_report_h.php";

$date = date("Y-m-d", time() - 60 * 60 * 24 * 5);

$secure = md5($date.'&'.password);

$xml ='<?xml version="1.0" encoding="UTF8"?>
<StatusReport Date="'.$date.'" Account="'.$login.'" Secure="'.$secure.'" ShowHistory="1" >
	<ChangePeriod DateFirst ="2013-07-16" DateLast ="2017-07-17" / >
</StatusReport>"';

// создание нового ресурса cURL
$ch = curl_init();
echo $date;
echo $url.'?account='.$login.'&secure='.$secure.'&datefirst='.$date.'&showhistory=1';
// установка URL и других необходимых параметров
curl_setopt($ch, CURLOPT_URL, $url.'?account='.$login.'&secure='.$secure.'&datefirst='.$date.'&showhistory=1');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, "?xml_request=".urlencode($xml));
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
// загрузка страницы и выдача её браузеру
    $out = curl_exec($ch);
    echo $out;


// завершение сеанса и освобождение ресурсов
curl_close($ch);
?>
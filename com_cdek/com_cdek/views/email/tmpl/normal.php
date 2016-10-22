<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<html>
	<head>
		<title>Заказ с Сайта</title>
	</head>
	<body>		
		<p>
			Тариф: <?php echo $this->data->outer_tariff_name; ?>
		</p>
		<p>
			Откуда: <?php echo $this->data->outer_city_from_name; ?><br />
			Куда: <?php echo $this->data->outer_city_to_name; ?>
		</p>
		<p>
			Вес: <?php echo $this->data->weight; ?> кг	<br />
			Ширина: <?php echo $this->data->width; ?> cм	<br />
			Длина: <?php echo $this->data->length; ?> см	<br />
			Высота: <?php echo $this->data->height; ?> см	<br />
		</p>
		<p>
			Срок доставки: <?php echo $this->data->delivery_time; ?> дней<br />
		</p>
		<p>
			Стоимость доставки: <?php echo $this->data->price; ?> р<br />
		</p>
		<p>
			ФИО: <?php echo $this->data->customer_name; ?><br />
			Телефон: <?php echo $this->data->phone; ?><br />
			Email: <?php echo $this->data->email; ?><br />
			Комментарий: <p><?php echo $this->data->mem; ?></p>
		</p>
	</body>
</html>

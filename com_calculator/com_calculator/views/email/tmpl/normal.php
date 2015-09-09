<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<html>
	<head>
		<title>Заказ с Сайта</title>
	</head>
	<body>
		<a href="<?php echo JURI::current() . '?weight=' . $this->data->weight . '&assessed_value=' . $this->data->assessed_value . '&width=' . $this->data->width . '&length=' . $this->data->length . '&height=' . $this->data->height . '&city_from=' . $this->data->city_from . '&city_to=' . $this->data->city_to . '&calc_row_id=' . $this->data->calc_row_id ?>"><h3>Ссылка на заказ</h3></a>		
		<p>
			Тариф: <?php echo $this->extra->tariff_name; ?>
		</p>
		<p>
			Откуда: <?php echo $this->extra->city_name_from->name; ?><br />
			Куда: <?php echo $this->extra->city_name_to->name; ?>
		</p>
		<p>
			Вес: <?php echo $this->data->weight; ?> кг	<br />
			Оценка: <?php echo $this->data->assessed_value; ?> руб	<br />
			Ширина: <?php echo $this->data->width; ?> cм	<br />
			Длина: <?php echo $this->data->length; ?> см	<br />
			Высота: <?php echo $this->data->height; ?> см	<br />
		</p>
		<p>
			Стоимость доставки: <?php echo $this->data->price; ?> р<br />
		</p>
		<p>
			ФИО: <?php echo $this->data->customer_name; ?><br />
			Телефон: <?php echo $this->data->phone; ?><br />
			Email: <?php echo $this->data->email; ?><br />
			Комментарий: <p><?php echo $this->data->comments; ?></p>
		</p>
	</body>
</html>

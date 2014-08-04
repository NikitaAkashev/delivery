<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<link rel="stylesheet" href="/kadence_of_pride/media/chosen/chosen.min.css" type="text/css" />
<script src="/kadence_of_pride/media/chosen/chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery(".city_select").chosen();
	});
</script>
<h1>Расчет стоимости отправки</h1>
<form method="POST" name="calculate_form" action="">
<table>
	<tr><td>Тариф</td>
		<td>
			<label><input type="radio" name="is_express" value="1" <?php if($this->model->is_express === null || $this->model->is_express == 1) echo "checked" ?> />Экспресс</label>
			<label><input type="radio" name="is_express" value="0" <?php if($this->model->is_express == 0) echo "checked" ?> />Стандарт</label>
		</td></tr>
	<tr><td>Забрать</td>
	<td>
		<label><input type="radio" name="from_door" value="1" <?php if($this->model->price === null) echo "checked" ?> />Забрать от адреса</label>
		<label><input type="radio" name="from_door" value="0" />Самостоятельно привезти</label>
	</td></tr>
	<tr><td>Доставить</td>
	<td>
		<label><input type="radio" name="to_door" value="1" <?php if($this->model->price === null) echo "checked" ?> />Доставить на адрес</label>
		<label><input type="radio" name="to_door" value="0" />Самостоятельно забрать</label>
	</td></tr>
	<tr><td>Откуда</td>
		<td><select name="city_from" class="city_select">
			<option value="">Нет</option>
			<?php
				foreach($this->cities as $city){
						$selected = $city->city == $this->model->city_from ? " selected=\"selected\" " : "";
						echo "<option ".$selected." value=\"".$city->city."\">".$city->name."</option>";
				}
			 ?>		
		</select></td></tr>
	<tr><td>Куда</td>
		<td><select name="city_to" class="city_select">
			<option value="">Нет</option>
			<?php
				foreach($this->cities as $city){
						$selected = $city->city == $this->model->city_to ? " selected=\"selected\" " : "";
						echo "<option ".$selected." value=\"".$city->city."\">".$city->name."</option>";
				}
			 ?>
		</select></td></tr>
	<tr><td>Вес, кг</td><td><input type="text" name="weight" value="<?php echo $this->model->weight; ?>" /></td></tr>
	<tr><td>Оценка, руб</td><td><input type="text" name="assessed_value" value="<?php echo $this->model->assessed_value; ?>" /></td></tr>
	<tr><td>Высота, см</td><td><input type="text" name="width" value="<?php echo $this->model->width; ?>" /></td></tr>
	<tr><td>Длина, см</td><td><input type="text" name="length" value="<?php echo $this->model->length; ?>" /></td></tr>
	<tr><td>Ширина, см</td><td><input type="text" name="height" value="<?php echo $this->model->height; ?>" /></td></tr>
	<tr><td colspan="2"><input type="submit" name="submit" value="Расчитать" /></td></tr>
</table>
</form>
<?php if($this->model->price != null){ ?>
	<h2>Стоимость отправки: <?php echo $this->model->price; ?> руб.</h2>
	Время доставки: <?php echo $this->model->min_delivery_time;  ?> - <?php echo $this->model->max_delivery_time; ?> дней
<?php } ?>
<?php if($this->model->inner_price != null){ ?>
	<h2>Внутренняя стоимость отправки: <?php echo $this->model->inner_price; ?> руб.</h2>
<?php } ?>

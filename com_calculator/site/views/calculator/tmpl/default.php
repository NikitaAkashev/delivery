<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<h1>Расчет стоимости отправки</h1>
<form method="POST" name="calculate_form" action="">
<table>
	<tr><td>Тариф</td>
		<td><select name="tariff">
			<?php
				foreach($this->tariffs as $tariff){
						$selected = $tariff->tariff == $this->model->tariff ? " selected=\"selected\" " : "";
						echo "<option ".$selected." value=\"".$tariff->tariff."\">".$tariff->name."</option>";
				}
			 ?>		
		</select></td></tr>
	<tr><td>Откуда</td>
		<td><select name="city_from">
			<option value="">Нет</option>
			<?php
				foreach($this->citys as $city){
						$selected = $city->city == $this->model->city_from ? " selected=\"selected\" " : "";
						echo "<option ".$selected." value=\"".$city->city."\">".$city->name."</option>";
				}
			 ?>		
		</select></td></tr>
	<tr><td>Куда</td>
		<td><select name="city_to">
			<option value="">Нет</option>
			<?php
				foreach($this->citys as $city){
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
<?php } ?>

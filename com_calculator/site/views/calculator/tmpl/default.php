<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<link rel="stylesheet" href="/media/chosen/chosen.min.css" type="text/css" />
<script src="/media/chosen/chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery(".city_select").chosen();

		jQuery(".comma-replace").keyup(function(){
			jQuery(this).val(jQuery(this).val().replace(',', '.'));
		});
	});
</script>
<h1>Расчет стоимости отправки</h1>
<form id="calculator" method="POST" name="calculate_form" action="">
<table>
	<tr><td>Тариф</td>
		<td>
			<label><input type="radio" name="is_express" value="1" <?php if($this->model->is_express === null || $this->model->is_express == 1) echo "checked" ?> /><span>Экспресс</span></label>
			<b class="separate">|</b>
			<label><input type="radio" name="is_express" value="0" <?php if($this->model->is_express == 0) echo "checked" ?> /><span>Стандарт</span></label>
		</td></tr>
	<tr><td>Забрать</td>
	<td><!-- по умолчанию доставка от двери -->
		<label><input type="radio" name="from_door" value="1" <?php if(JRequest::getFloat('from_door', 1) == 1) echo "checked" ?> /><span>Забрать от адреса</span></label>
		<b class="separate">|</b>
		<label><input type="radio" name="from_door" value="0" <?php if(JRequest::getFloat('from_door', 1) == 0) echo "checked" ?>/><span>Самостоятельно привезти</span></label>
	</td></tr>
	<tr><td>Доставить</td>
	<td><!-- по умолчанию доставка до двери -->
		<label><input type="radio" name="to_door" value="1" <?php if(JRequest::getFloat('to_door', 1) == 1) echo "checked" ?> /><span>Доставить на адрес</span></label>
		<b class="separate">|</b>
		<label><input type="radio" name="to_door" value="0" <?php if(JRequest::getFloat('to_door', 1) == 0) echo "checked" ?> /><span>Самостоятельно забрать</span></label>
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
	<tr><td>Вес, кг</td><td><input class="comma-replace <?php if($this->model->weight !== null && $this->model->weight == 0) echo 'alert-error'?>" type="text" name="weight" value="<?php echo $this->model->weight; ?>" /></td></tr>
	<tr><td>Оценка, руб</td><td><input class="comma-replace" type="text" name="assessed_value" value="<?php echo $this->model->assessed_value; ?>" /></td></tr>
	<tr><td>Ширина, см</td><td><input class="comma-replace <?php if($this->model->width !== null && $this->model->width == 0) echo 'alert-error'?>" type="text" name="width" value="<?php echo $this->model->width; ?>" /></td></tr>
	<tr><td>Длина, см</td><td><input class="comma-replace <?php if($this->model->length !== null && $this->model->length == 0) echo 'alert-error'?>" type="text" name="length" value="<?php echo $this->model->length; ?>" /></td></tr>
	<tr><td>Высота, см</td><td><input class="comma-replace <?php if($this->model->height !== null && $this->model->height == 0) echo 'alert-error'?>" type="text" name="height" value="<?php echo $this->model->height; ?>" /></td></tr>
	<tr><td colspan="2"><input class="submit" type="submit" name="submit" value="Расчитать" /></td></tr>
</table>
</form>
<?php if($this->model->price != null){ ?>
	<h2>Стоимость отправки: <?php echo ceil($this->model->price * ($this->model->nds + 1)); ?> руб <span style="text-transform:none;">(в том числе НДС <?php echo ceil($this->model->price * ($this->model->nds)); ?> руб.)</span></h2>
	Время доставки: <?php echo $this->model->min_delivery_time;  ?> - <?php echo $this->model->max_delivery_time; ?> дн.
	Объем груза: <?php $vol = $this->model->width * $this->model->length * $this->model->height / 1000000; echo ($vol < 0.01 ? "менее 0,01" : $vol);  ?> м<sup>3</sup>
	
<?php } ?>
<?php if($this->model->inner_price != null){ ?>
	<h2>Внутренняя стоимость отправки: <?php echo ceil($this->model->inner_price * ($this->model->nds + 1)); ?> руб <span style="text-transform:none;">(в том числе НДС <?php echo ceil($this->model->inner_price * ($this->model->nds)); ?> руб.)</span></h2>
	<h2>Прибыль: <?php echo ceil(($this->model->price - $this->model->inner_price) * ($this->model->nds + 1)); ?> руб <span style="text-transform:none;">(в том числе НДС <?php echo ceil(($this->model->price - $this->model->inner_price) * ($this->model->nds)); ?> руб.)</span></h2>
<?php } ?>

<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>

<?php if ($this->model->ordered) {?>
	<?php echo $this->model->order_message; ?>
	<a href="<?php echo JURI::current(); ?>"><h3>Оформить новый заказ</h3></a>
<?php } else { ?>
<h1>Расчет стоимости отправки</h1>
<form id="calculator" method="POST" name="calculate_form" action="">
	<div id="calculator_body">
		<div class="control-group">
			<label class="control-label">Вес, кг<span class="asterisk correct">*</span></label>
			<div class="controls">
				<input class="comma-replace advantage_fields <?php if($this->model->weight !== null && $this->model->weight == 0) echo 'alert-error'?>" type="text" name="weight" value="<?php echo $this->model->weight; ?>" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">Оценка, руб</label>
			<div class="controls">
				<input class="comma-replace advantage_fields" type="text" name="assessed_value" value="<?php echo $this->model->assessed_value; ?>" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">Ширина, см<span class="asterisk correct">*</span></label>
			<div class="controls">
				<input class="comma-replace advantage_fields <?php if($this->model->width !== null && $this->model->width == 0) echo 'alert-error'?>" type="text" name="width" value="<?php echo $this->model->width; ?>" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">Длина, см<span class="asterisk correct">*</span></label>
			<div class="controls">
				<input class="comma-replace advantage_fields <?php if($this->model->length !== null && $this->model->length == 0) echo 'alert-error'?>" type="text" name="length" value="<?php echo $this->model->length; ?>" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">Высота, см<span class="asterisk correct">*</span></label>
			<div class="controls">
				<input class="comma-replace advantage_fields <?php if($this->model->height !== null && $this->model->height == 0) echo 'alert-error'?>" type="text" name="height" value="<?php echo $this->model->height; ?>" />
			</div>
		</div>
		<div class="control-group border-top">
			<label class="control-label">Откуда<span class="asterisk correct">*</span></label>
			<div class="controls">
				<select id="city_from" name="city_from" class="city_select advantage_fields">
					<option value="">Нет</option>
					<?php
						foreach($this->cities as $city){
							$selected = $city->city == $this->model->city_from ? " selected=\"selected\" " : "";
							echo "<option ".$selected." value=\"".$city->city."\">".$city->name."</option>";
						}
					 ?>		
				</select>
			</div>
		</div>
		<div class="control-group border-top">
			<label class="control-label">Куда<span class="asterisk correct">*</span></label>
			<div class="controls">
				<select id="city_to" name="city_to" class="city_select advantage_fields">
					<option value="">Нет</option>
					<?php
						foreach($this->cities as $city){
							$selected = $city->city == $this->model->city_to ? " selected=\"selected\" " : "";
							echo "<option ".$selected." value=\"".$city->city."\">".$city->name."</option>";
						}
					 ?>
				</select>
			</div>
		</div>
		<div class="control-group" id="calculated" style="<?php if($this->model->price == null){ echo "display:none;"; }?>">
			<div>
				<h2>Стоимость отправки: 
					<span id="price"><?php echo $this->model->price; ?></span> руб 
					<span style="text-transform:none;">(в том числе НДС <span id="nds_part"><?php echo $this->model->nds_part; ?></span> руб.)</span>
				</h2>
				Время доставки: 
				<span id="min_delivery_time"><?php echo $this->model->min_delivery_time; ?></span> &mdash; 
				<span id="max_delivery_time"><?php echo $this->model->max_delivery_time; ?></span> дн.
				Объем груза: 
				<span id="volume"><?php echo ($this->model->volume < 0.01 ? "менее 0,01" : $this->model->volume); ?></span> м<sup>3</sup>
			</div>
		</div>
		<div class="control-group" id="calculated_inner" style="<?php if($this->model->inner_price == null){ echo "display:none;"; }?>">
			<div>
				<h2>Внутренняя стоимость отправки: 
					<span id="inner_price"><?php echo $this->model->inner_price; ?></span> руб 
					<span style="text-transform:none;">(в том числе НДС <span id="nds_part_inner"><?php echo $this->model->nds_part_inner; ?></span> руб.)</span>
				</h2>
				<h2>Прибыль: 
					<span id="profit"><?php echo $this->model->profit; ?></span> руб 
					<span style="text-transform:none;">(в том числе НДС <span id="profit_nds_part"><?php echo $this->model->profit_nds_part; ?></span> руб.)</span>
				</h2>
			</div>
		</div>
		<div class="control-group">
			<div>
				<a href="#" id="order_details_link" style="display:none;" onclick="jQuery('#order_form').show(); jQuery('#order_details_link').hide(); return false;" >Оформить заказ</a>
			</div>
		</div>
	</div>

	<div id="order_form" class="border-top" style="<?php if($this->model->price == null){ echo "display:none;"; }?>">
		
		<div id="comments" class="form-block">
			<label for="comments" class="control-label">Комментарии:</label>
			<div class="controls">
				<textarea name="comments"><?php if(array_key_exists('comments', $this->model->form)) echo $this->model->form['comments']; ?></textarea>
			</div>
		</div>

		<div id="counteragents">
			<hr class="divider">
			<input class="submit" type="submit" name="submit" value="Оформить заказ" />
		</div>
	</div>	
</form>
<?php }?>

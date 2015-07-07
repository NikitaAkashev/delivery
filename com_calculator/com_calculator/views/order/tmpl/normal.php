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
		<?php 
			$show = $this->model->calculated;			
			$show_inner = $this->model->with_inner;
			$selected_row = array_key_exists('calc_row_id', $this->model->form) ? $this->model->form['calc_row_id'] : 0;
			$current_i = 0;
		?>		
		<table id="calc_results" style="<?php if(!$show){ echo "display:none;"; }?>">
			<thead>
				<tr id="calc_results_head">
					<th>Выбрать</th>
					<th>Тариф</th>
					<th>Стоимость, руб. (с НДС)</th>
					<th>Срок доставки, раб. дни, не считая дня приема отправления</th>
					<?php if($show_inner) { ?><th>Перевозчик</th><?php } ?>
				</tr>
			</thead>
			<tbody id="calc_results_rows">
			<?php foreach($this->model->prices as $i => $p ) { ?>
				<tr>
					<?php 
						$selected = ($selected_row ? $p->uid == $selected_row : $i == $selected_row );
						if($selected) { $current_i = $i; }
					 ?>
					<td><input type="radio" name="calc_row_id" value="<?php echo $p->uid; ?>" <?php echo $selected ? 'checked="checked"' : ''; ?> /></td>
					<td><?php echo $p->tariff_name ?></td>
					<td><?php echo $p->customer_price ?></td>
					<td><?php echo $p->delivery_time ?></td>
					<?php if($show_inner) { echo '<td>'.$p->provider_name.'</td>';} ?>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		
		<div class="control-group" id="calculated" style="<?php if(!$show){ echo "display:none;"; }?>">
			<div>
				<h2>Стоимость отправки: 
					<span id="customer_price"><?php echo $show ? $this->model->prices[$current_i]->customer_price : ''; ?></span> руб 
					<span style="text-transform:none;">(в том числе НДС <span id="customer_nds"><?php echo $show ? $this->model->prices[$current_i]->customer_nds : ''; ?></span> руб.)</span>
				</h2>
				Время доставки: 
				<span id="delivery_time"><?php echo $show ? $this->model->prices[$current_i]->delivery_time : ''; ?></span> дн.
				Объем груза: 
				<span id="displayed_volume"><?php echo $show ? $this->model->prices[$current_i]->displayed_volume : ''; ?></span> м<sup>3</sup>
				Расчетный вес: 
				<span id="real_weight"><?php echo $show ? $this->model->prices[$current_i]->real_weight : ''; ?></span> кг
			</div>
		</div>
		<?php if($show_inner){ ?>
			<div class="control-group" id="calculated_inner" style="<?php if(!$show){ echo "display:none;"; }?>">
				<div>
					<h2>Внутренняя стоимость отправки: 
						<span id="inner_price"><?php echo $show ? $this->model->prices[$current_i]->inner_price : ''; ?></span> руб 
						<span style="text-transform:none;">(в том числе НДС <span id="nds_part_inner"><?php echo $show ? $this->model->prices[$current_i]->inner_nds : ''; ?></span> руб.)</span>
					</h2>
					<h2>Прибыль: 
						<span id="profit"><?php echo $show ? $this->model->prices[$current_i]->profit : ''; ?></span> руб 
						<span style="text-transform:none;">(в том числе НДС <span id="profit_nds"><?php echo $show ? $this->model->prices[$current_i]->profit_nds: ''; ?></span> руб.)</span>
					</h2>
				</div>
			</div>
		<?php }?>
		<div class="control-group">
			<div>
				<a href="#" id="order_details_link" style="display:none;" onclick="jQuery('#order_form').show(); jQuery('#order_details_link').hide(); return false;" >Оформить заказ</a>
			</div>
		</div>
	</div>

	<div id="order_form" class="border-top" style="<?php if(!$show){ echo "display:none;"; }?>">
		
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

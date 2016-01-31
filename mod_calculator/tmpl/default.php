<?php defined('_JEXEC') or die; ?>

<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery(".mod_city_select").chosen();

		jQuery(".comma-replace").keyup(function(){
			jQuery(this).val(jQuery(this).val().replace(',', '.'));
		});
	});
</script>

<form name="mod_calculator" method="post" action="/calculator/" >
	<div>
		<label>Откуда</label>
		<select name="city_from" class="mod_city_select">
			<option value="">Нет</option>
			<?php
				foreach($cities as $city){
						echo "<option".($city->city == $selected_city_from ? " selected" : "")." value=\"".$city->city."\">".$city->name."</option>";
				}
			 ?>		
		</select>
	</div>
	<div>
		<label>Куда</label> 
		<select name="city_to" class="mod_city_select">
			<option value="">Нет</option>
			<?php
				foreach($cities as $city){
						echo "<option".($city->city == $selected_city_to ? " selected" : "")." value=\"".$city->city."\">".$city->name."</option>";
				}
			 ?>
		</select>
	</div>
		
	<div class="weight-size">
		<label>Вес/Размеры</label>
		<input class="comma-replace weight" size="2" maxlength="3" type="text" name="weight" value="<?php echo $parcel["weight"]; ?>" /><span class="decoding">кг</span><sub class="separator">/</sub> 
		<input class="comma-replace" size="2" maxlength="3" type="text" name="width"  value="<?php echo $parcel["width"]; ?>" /><span class="decoding">см</span><span class="separator">x</span>
		<input class="comma-replace" size="2" maxlength="3" type="text" name="length"  value="<?php echo $parcel["length"]; ?>" /><span class="decoding">см</span><span class="separator">x</span>
		<input class="comma-replace" size="2" maxlength="3" type="text" name="height"  value="<?php echo $parcel["height"]; ?>" /><span class="decoding">см</span>
		<input type="hidden" value="" name="assessed_value" />
		<input type="hidden" value="<?php echo $price->uid; ?>" name="calc_row_id" />
	</div>
	<div id="result">
			<span class="data"><?php echo $price->delivery_time; ?></span> дн. <b><span class="data"><?php echo $price->customer_price; ?></span> руб.</b><br /><span class="tariff-name">(<?php echo $price->tariff_name; ?>)</span></div>
	<button class="roboto">рассчитать</button>	
	
</form>

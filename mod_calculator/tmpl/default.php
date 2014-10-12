<?php 
// No direct access
defined('_JEXEC') or die; ?>

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

<form name="mod_calculator" method="post" action="/calculator/" >
	<div>
		<label>Откуда</label>
		<select name="city_from" class="city_select">
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
		<select name="city_to" class="city_select">
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
		<input class="comma-replace weight" size="2" maxlength="3" type="text" name="weight" value="<?php echo $parcel["weight"]; ?>" /> <span class="decoding">кг</span> <sub class="separator">/</sub> 
		<input class="comma-replace" size="2" maxlength="3" type="text" name="width"  value="<?php echo $parcel["width"]; ?>" /> <span class="decoding">см</span> <span class="separator">x</span>
		<input class="comma-replace" size="2" maxlength="3" type="text" name="length"  value="<?php echo $parcel["length"]; ?>" /> <span class="decoding">см</span> <span class="separator">x</span>
		<input class="comma-replace" size="2" maxlength="3" type="text" name="height"  value="<?php echo $parcel["height"]; ?>" /> <span class="decoding">см</span>
		<input type="hidden" value="" name="assessed_value" />
		<input type="hidden" value="0" name="from_door" />
	</div>

	<div id="result"><span class="data"><?php echo $model->min_delivery_time;  ?>–<?php echo $model->max_delivery_time; ?></span> дн. <b><span class="data"><?php echo ceil($model->price * ($model->nds + 1)); ?></span> руб.</b></div>
	
	<button class="roboto">заказать</button>	
	
</form>

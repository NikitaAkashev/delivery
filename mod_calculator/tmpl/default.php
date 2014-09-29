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

<form name="mod_calculator" method="post" action="/kadence_of_pride/index.php/component/calculator/" >
<table>
<tr><td>Откуда</td>
		<td><select name="city_from" class="city_select">
			<option value="">Нет</option>
			<?php
				foreach($cities as $city){
						echo "<option value=\"".$city->city."\">".$city->name."</option>";
				}
			 ?>		
		</select></td></tr>
	<tr><td>Куда</td>
		<td><select name="city_to" class="city_select">
			<option value="">Нет</option>
			<?php
				foreach($cities as $city){
						echo "<option value=\"".$city->city."\">".$city->name."</option>";
				}
			 ?>
		</select></td></tr>
		
	<tr><td>Размеры, см</td>
		<td>
			<input class="comma-replace" type="text" name="width" placeholder="шир." />x
			<input class="comma-replace" type="text" name="length" placeholder="дл." />x
			<input class="comma-replace" type="text" name="height" placeholder="выс." />
	
		
	<tr><td colspan = 2><input type="submit" value="К расчету!"></td></tr>
</table>
</form>
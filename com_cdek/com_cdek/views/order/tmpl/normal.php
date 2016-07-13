<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>

<?php if ($this->model->ordered) {?>
	<?php echo $this->model->order_message; ?>
	<a href="<?php echo JURI::current(); ?>"><h3>Оформить новый заказ</h3></a>
<?php } else { ?>
<style type="text/css">
	.ui-autocomplete-loading {
	  background: #FFF right center no-repeat;
	}
	#city { width: 25em; }
	#log { height: 200px; width: 600px; overflow: auto; }
	</style>
	<script type="text/javascript">
	

	</script>
<h1>Расчет стоимости отправки</h1>
<form id="calculator" method="POST" name="calculate_form" action="<?php echo JURI::current(); ?>">
	<div id="calculator_body">
		<div class="control-group city_from pull-left">
			<label class="control-label">Откуда<span class="asterisk correct">*</span></label>
			<div class="controls">
				<input id="city_from" name="city_from" class="advantage_fields"/>
				<input type="hidden" id="city_from_id" name="city_from_id" class="advantage_fields"/>
			</div>
		</div>
		<div class="control-group city_to pull-right">
			<label class="control-label">Куда<span class="asterisk correct">*</span></label>
			<div class="controls">
				<input id="city_to" name="city_to" class="advantage_fields"/>
				<input type="hidden" id="city_to_id" name="city_to_id" class="advantage_fields"/>
			</div>
		</div>
		<div class="control-group weight pull-left">
			<label class="control-label">Вес, кг<span class="asterisk correct">*</span></label>
			<div class="controls">
				<input class="comma-replace advantage_fields" type="text" name="weight" id="weight_input" value="" />
			</div>
		</div>
		<div class="control-group dimension pull-left">
			<label class="control-label">Габариты ДxШxВ, см<span class="asterisk correct noneed">*</span></label>
			<div class="controls">
				<input class="comma-replace advantage_fields" type="text" name="width" id="width_input" value="" />
			</div>
			<label class="label-x">X</label>
			<div class="controls">
				<input class="comma-replace advantage_fields" type="text" name="length" id="length_input" value="" />
			</div>
			<label class="label-x">X</label>
			<div class="controls">
				<input class="comma-replace advantage_fields" type="text" name="height" id="height_input" value="" />
			</div>
		</div>
        <div class="explanation">
        	<span class="asterisk correct">*</span> Поля, отмеченные звёздочкой, обязательны для заполнения
		</div>

		<button id="calculate">Расчитать</button>


		<div id="advantage_area" style="display: none;">
			<table class="has_prices" id="calc_results" cellpadding="0" cellspacing="0">
				<thead>
					<tr id="calc_results_head">
						<th>Выбрать</th>
						<th>Тариф</th>
						<th>Стоимость, руб.<br>(с НДС)</th>
						<th>Срок доставки (раб. <br>дни, не считая дня <br>приема отправления)</th>
					</tr>
				</thead>
				<tbody id="calc_results_rows">
				</tbody>
			</table>
		</div>

		<div class="control-group has_prices order_details_link">
			<div>
				<a href="#" class="order_details_link" id="order_details_link" style="display:none;" onclick="jQuery('#order_form').show(); jQuery('.order_details_link').hide(); return false;" >Оформить заказ</a>
			</div>
		</div>
		<div class="no_prices" style="display:none;">
			 К сожалению, по данному направлению в нашем калькуляторе нет необходимых данных для рассчета. Позвоните нам по телефону +7 (343) 266-36-16 и мы обязательно поможем доставить ваше отправление.
		</div>
	</div>

	<div id="order_form" class="has_prices" style="display:none;">

		<div id="customer_name" class="form-block">
			<label for="customer_name" class="control-label">Ваше имя:</label>
			<div class="controls">
				<input type="text" name="customer_name" value=""/>
			</div>
		</div>
		
		<div id="customer_phone" class="form-block">
			<label for="phone" class="control-label">Телефон<span class="asterisk correct">*</span>:</label>
			<div class="controls">
				<input type="text" id="client_phone" class="" name="phone" value=""/>
			</div>
		</div>
		
		<div id="email" class="form-block">
			<label for="email" class="control-label">Email:</label>
			<div class="controls">
				<input type="text" id="client_email" name="email" value=""/>
			</div>
		</div>
		
		<div id="comments" class="form-block">
			<label for="comments" class="control-label">Комментарии:</label>
			<div class="controls">
				<textarea name="comments"></textarea>
			</div>
		</div>

		<div id="counteragents">
			<hr class="divider">
			<input type="hidden" name="make_order" value="sure" />
			<input type="hidden" id="tariff_name" name="tariff_name" value="" />
			<input type="hidden" id="price" name="price" value="" />
			<input class="submit" type="submit" name="submit" id="orderbutton" value="Оформить заказ" />
		</div>
	</div>	
</form>
<?php }?>

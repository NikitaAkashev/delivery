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
<h1>экспресс доставка по России</h1>
<div>
	<p>В нашем калькуляторе представленны наиболее востребованные направления и тарифы. Если вы не нашли нужный вам населенный пункт доставки/забора или необходимые сроки доставки, позвоните нам по телефону <strong>+7 (343) 288-56-62</strong> и наши менеджеры подберут оптимальное решение для Вас!</p>
</div>
<form id="calculator" method="POST" name="calculate_form" action="<?php echo JURI::current(); ?>">
	<div id="calculator_body">
		<div class="control-group city_from pull-left">
			<div class="controls">
				<input type="text" id="city_from" name="city_from" class="advantage_fields" placeholder="Откуда?"/>
				<input type="hidden" id="city_from_id" name="city_from_id" class="advantage_fields"/>
			</div>
		</div>
        <div class="arrow pull-left"></div>
		<div class="control-group city_to">
			<div class="controls">
				<input type="text" id="city_to" name="city_to" class="advantage_fields" placeholder="Куда?"/>
				<input type="hidden" id="city_to_id" name="city_to_id" class="advantage_fields"/>
			</div>
		</div>
		<div class="control-group weight pull-left">
			<div class="controls">
            	<label class="control-label">, кг<span class="asterisk correct">*</span></label>
				<input class="comma-replace advantage_fields" type="text" name="weight" id="weight_input" value="<?php echo $this->model->GetSettings()->weight_no_size; ?>" autocomplete="off" />
			</div>
		</div>
		<div class="control-group dimension pull-left">
			<div class="controls">
            	<label class="control-label">, см<span class="asterisk correct">*</span></label>
				<input class="comma-replace advantage_fields" type="text" name="width" id="width_input" placeholder="Длина" autocomplete="off" />
			</div>
			<label class="label-x">X</label>
			<div class="controls">
            	<label class="control-label">, см<span class="asterisk correct">*</span></label>
				<input class="comma-replace advantage_fields" type="text" name="length" id="length_input" placeholder="Ширина" autocomplete="off" />
			</div>
			<label class="label-x">X</label>
			<div class="controls">
            	<label class="control-label">, см<span class="asterisk correct">*</span></label>
				<input class="comma-replace advantage_fields" type="text" name="height" id="height_input" placeholder="Высота" autocomplete="off" />
			</div>
		</div>


		<button id="calculate"><span>Расчитать заказ</span></button>
        
        <div class="clear"></div>

		<div id="advantage_area" style="display: none;">
			<table class="has_prices" id="calc_results" cellpadding="0" cellspacing="0">
				<thead>
					<tr id="calc_results_head">
						<th colspan="2">Тариф</th>
						<th>Стоимость, руб.</th>
						<th>Срок доставки, раб. дни</th>
					</tr>
				</thead>
				<tbody id="calc_results_rows">
				</tbody>
			</table>
            <p>Срок доставки указан без учета дня приема отправления</p>
		</div>
               

		<div class="control-group has_prices order_details_link">
			<div>
				<a href="#" class="order_details_link" id="order_details_link" style="display:none;" onclick="jQuery('#order_form').show(); jQuery('.order_details_link').hide(); return false;" ><span>Оформить заказ</span></a>
			</div>
		</div>
		<div class="no_prices" style="display:none;">
			 К сожалению, по данному направлению в нашем калькуляторе нет необходимых данных для рассчета. Позвоните нам по телефону +7 (343) 266-36-16 и мы обязательно поможем доставить ваше отправление.
		</div>
	</div>

	<div id="order_form" class="has_prices" style="display:none;">

		<div id="customer_name" class="form-block">
			<div class="controls">
				<input type="text" name="customer_name" placeholder="Ваше имя"/>
			</div>
		</div>
		
		<div id="customer_phone" class="form-block">
			<div class="controls">
				<input type="text" id="client_phone" class="" name="phone" placeholder="Телефон*"/>
			</div>
		</div>
		
		<div id="email" class="form-block">
			<div class="controls">
				<input type="text" id="client_email" name="email" placeholder="Email"/>
			</div>
		</div>
		
		<div id="comments" class="form-block">
			<div class="controls">
				<textarea name="comments" placeholder="Комментарии"></textarea>
			</div>
		</div>

		<div id="counteragents">
			<hr class="divider">
			<input type="hidden" name="make_order" value="sure" />
			<input type="hidden" name="weight_no_size" id="weight_no_size" value="<?php echo $this->model->GetSettings()->weight_no_size; ?>" />
			<input type="hidden" id="tariff_name" name="tariff_name" value="" />
			<input type="hidden" id="price" name="price" value="" />
			<input type="hidden" id="delivery_time" name="delivery_time" value="" />
			<button class="submit" name="submit" id="orderbutton"/><span>Оформить заказ</span></button>
		</div>
	</div>	
</form>
<?php }?>

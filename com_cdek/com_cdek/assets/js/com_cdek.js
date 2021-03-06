jQuery(document).ready(function(){

	autocity("#city_from");
	autocity("#city_to");

	jQuery(".comma-replace").keyup(function(){
		jQuery(this).val(jQuery(this).val().replace(',', '.'));
	});

	jQuery('#client_email').change(ValidateEmail);
	jQuery('#client_email').keyup(ValidateEmail);
	jQuery('#client_phone').change(ValidatePhone);
	jQuery('#client_phone').keyup(ValidatePhone);

	jQuery('#calculate').click(function(){return Recalculate(true)});
	jQuery('#orderbutton').click(ValidateOrder);
	
	jQuery('#weight_input').change(CheckSizeRequired);
	jQuery('#weight_input').keyup(CheckSizeRequired);
	
	CheckSizeRequired();
	
	jQuery('.advantage_fields').change(function(){Recalculate(false)});
	jQuery('.advantage_fields').keyup(function(){Recalculate(false)});
});
/**
 * подтягиваем список городов ajax`ом, данные jsonp в зависмости от введённых символов
 */
function autocity(id)
{
	jQuery(id).autocomplete(
		{
			source: function(request,response)
			{
				jQuery.ajax(
					{
						url: "http://api.cdek.ru/city/getListByTerm/jsonp.php?callback=?",
						dataType: "jsonp",
						data:
						{
							q: function () { return jQuery(id).val() },
							name_startsWith: function () { return jQuery(id).val() }
						},
						success: function(data) {
							response(jQuery.map(data.geonames, function(item) {
								return {
									label: item.name,
									value: item.name,
									id: item.id
								}
							}));
						}
					});
			},
			minLength: 1,
			select: function(event,ui) {
				//console.log("Yep!");
				jQuery(id+"_id").val(ui.item.id);
			}
		});
};

/**
 * @return {boolean}
 */
function ValidateCalc(display_errors)
{
	var has_errors = false;
	jQuery('.advantage_fields').removeClass('alert-error');

	if(jQuery('#city_from_id').val() == '') {
		if (display_errors) 
			jQuery('#city_from').addClass('alert-error');
		has_errors = true;
	};
	if(jQuery('#city_to_id').val() == '') {
		if (display_errors) 
			jQuery('#city_to').addClass('alert-error');
		has_errors = true;
	};
	if(jQuery('#weight_input').val() == '') {
		if (display_errors) 
			jQuery('#weight_input').addClass('alert-error');
		has_errors = true;
	};

	if(jQuery('#weight_input').val() <= jQuery('#weight_no_size').val())
		return !has_errors;

	if(jQuery('#width_input').val() == '') {
		if (display_errors) 
			jQuery('#width_input').addClass('alert-error');
		has_errors = true;
	};
	if(jQuery('#length_input').val() == '') {
		if (display_errors) 
			jQuery('#length_input').addClass('alert-error');
		has_errors = true;
	};
	if(jQuery('#height_input').val() == '') {
		if (display_errors) 
			jQuery('#height_input').addClass('alert-error');
		has_errors = true;
	};

	if(!has_errors && display_errors)
		jQuery('.advantage_fields').removeClass('alert-error');

	return !has_errors;
}

function Recalculate(display_errors){
	if (!ValidateCalc(display_errors)) return false;

	jQuery('#advantage_area').addClass("loading");
	jQuery.post(
		'?option=com_cdek&controller=calculate&format=json',
		jQuery('#calculator').serialize(), 
		function (data) {
			if(data.calculated){
				jQuery('#advantage_area').show();
				
				FillResults(data.prices);
			}else{
				jQuery('#advantage_area').hide();
				jQuery('.order_details_link').hide();
				jQuery('#order_form').hide();
				jQuery('#calc_results').hide();
			}
			jQuery('#advantage_area').removeClass("loading");
		}
	);

	return false;
}

function FillResults(data){
	if(data.length > 0){
		jQuery('#calc_results_rows tr').remove();
		jQuery.each(data, function(i, v){
			jQuery('#calc_results_rows').append(jQuery('<tr>'
				+ '<td><input class="rate_line" data-tariffname="'+v.name+'" data-price="'+v.price+'" data-nds="'+v.nds+'" data-delivery_time="'+v.delivery_time+'" type="radio" name="tariff" value="'+v.tariffId+'" /></td>'
				+ '<td>' + v.name + '</td>'
				+ '<td>' + v.price + (jQuery('#with_nds') ? ' ('+ v.nds + ')' : '' ) + '</td>'
				+ '<td>' + v.delivery_time + '</td>'
			+'</tr>'));
		});

		jQuery('.rate_line').click(SelectTariff);
		
		// выбор первого тарифа
		jQuery('.rate_line')[0].click();

		jQuery('.no_prices').hide();		
		
		if(!jQuery('#order_form').is(':visible')){	
			jQuery('.order_details_link').show();
		}	
	}else{
		jQuery('.has_prices').hide();
		jQuery('.no_prices').show();
	}
}

function SelectTariff()
{
	var el = jQuery(this);

	jQuery('#tariff_name').val(el.data('tariffname'));
	jQuery('#price').val(el.data('price'));
	jQuery('#nds').val(el.data('nds'));
	jQuery('#delivery_time').val(el.data('delivery_time'));
}

function ValidateEmail()
{
	var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
	if(!jQuery('#client_email').val() || re.test(jQuery('#client_email').val())){
		jQuery('#client_email').removeClass('alert-error');
		return true;
	}else{
		jQuery('#client_email').addClass('alert-error');
		return false;
	}
}

/**
 * @return {boolean}
 */
function ValidatePhone()
{
	var re = /^[\d \+\-\(\)]+$/;
	if(re.test(jQuery('#client_phone').val())){
		jQuery('#client_phone').removeClass('alert-error');
		return true;
	}else{
		jQuery('#client_phone').addClass('alert-error');
		return false;
	}
}

function ValidateOrder()
{
	var has_errors = false;

	if(!ValidatePhone())
		has_errors = true;

	if(!ValidateEmail())
		has_errors = true;

	if(!jQuery(".rate_line:checked").val())
		has_errors = true;

	return !has_errors;
}

function CheckSizeRequired()
{
	if(jQuery('#weight_input').val() <= jQuery('#weight_no_size').val())
	{
		jQuery('.dimension .asterisk').hide();
	}
	else
	{
		jQuery('.dimension .asterisk').show();
	}
}
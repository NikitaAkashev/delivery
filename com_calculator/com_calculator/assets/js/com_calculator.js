var calc_model = null;
var current_selected = null;

jQuery(document).ready(function(){
	jQuery(".city_select").chosen();

	jQuery(".comma-replace").keyup(function(){
		jQuery(this).val(jQuery(this).val().replace(',', '.'));
	});
	
	jQuery('.advantage_fields').change(Recalculate);
	jQuery('.advantage_fields').keyup(Recalculate);
	
	jQuery('.rate_line').click(SelectRow);
	
	jQuery('#client_email').change(ValidateEmail);
	jQuery('#client_email').keyup(ValidateEmail);
	jQuery('#client_phone').change(ValidatePhone);
	jQuery('#client_phone').keyup(ValidatePhone);
	
	Recalculate();
	ValidateEmail();
});

function Recalculate(){
	if(jQuery('#weight_input').val() > jQuery('#weight_no_size').val()){
		jQuery('.noneed').show();
	}else{
		jQuery('.noneed').hide();
	}
	jQuery('#advantage_area').addClass("loading");
	jQuery.post(
		'?option=com_calculator&controller=calculate&format=json', 
		jQuery('#calculator').serialize(), 
		function (data) {
			if(data.calculated){
				jQuery('#calculated').show();
				jQuery('#calc_results').show();
				if(data.with_inner){jQuery('#calculated_inner').show();}
				
				calc_model = data.prices;
				current_selected = jQuery('.rate_line:checked').val();
				FillResults(data);
				
				if(!current_selected){
					if(jQuery('.rate_line')[0]){
						jQuery('.rate_line')[0].click();
					}
				}
				else{
					var sel = jQuery(".rate_line").filter(function(){return this.value==current_selected})[0];
					if(sel) {
						sel.click()
					} 
					else
					{
						if(jQuery('.rate_line')[0]){
							jQuery('.rate_line')[0].click();
						}
					};
				}
				
			}else{
				jQuery('#calculated').hide();
				jQuery('.order_details_link').hide();
				jQuery('#order_form').hide();
				jQuery('#calc_results').hide();
				if(data.with_inner){jQuery('#calculated_inner').hide();}
			}
		}
	);
}

function FillResults(data){
	if(calc_model.length > 0){
		jQuery('#calc_results_rows tr').remove();
		jQuery.each(calc_model, function(i, v){
			jQuery('#calc_results_rows').append(jQuery('<tr>'
				+ '<td><input class="rate_line" type="radio" name="calc_row_id" value="'+v.uid+'" /></td>'
				+ '<td>'+v.tariff_name+'</td>'
				+ '<td>'+v.customer_price+'</td>'
				+ '<td>'+v.delivery_time+'</td>'
				+ (data.with_inner ? '<td>'+v.provider_name+'</td>' : '' )
			+'</tr>'));
		});
		jQuery('.rate_line').click(SelectRow);
		jQuery('.no_prices').hide();		
		
		if(!jQuery('#order_form').is(':visible')){	
			jQuery('.order_details_link').show();
		}	
	}else{
		jQuery('.has_prices').hide();
		jQuery('.no_prices').show();
	}
	jQuery('#advantage_area').removeClass("loading");
}


function SelectRow(){
	jQuery(this).attr('checked', 'checked');
	current_selected = jQuery(this).val();
	r = calc_model.filter(function(x){return x.uid == current_selected})[0];
	if(r){
		jQuery('#customer_price').text(r.customer_price);
		jQuery('#customer_price_input').val(r.customer_price);
		jQuery('#customer_nds').text(r.customer_nds);
		jQuery('#delivery_time').text(r.delivery_time);
		jQuery('#displayed_volume').text(r.displayed_volume);
		jQuery('#real_weight').text(r.real_weight);
		jQuery('#inner_price').text(r.inner_price);
		jQuery('#inner_price_no_nds').text(r.inner_price_no_nds);
		jQuery('#inner_nds').text(r.inner_nds);
		jQuery('#profit').text(r.profit);
		jQuery('#profit_nds').text(r.profit_nds);
	}
}

function ValidateEmail()
{
	var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
	if(!jQuery('#client_email').val() || re.test(jQuery('#client_email').val())){
		jQuery('#client_email').removeClass('alert-error');
	}else{
		jQuery('#client_email').addClass('alert-error');
	}
}

function ValidatePhone()
{
	var re = /^[\d \+\-\(\)]+$/;
	if(re.test(jQuery('#client_phone').val())){
		jQuery('#client_phone').removeClass('alert-error');
	}else{
		jQuery('#client_phone').addClass('alert-error');
	}
}

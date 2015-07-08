var calc_model = null;

jQuery(document).ready(function(){
	jQuery(".city_select").chosen();

	jQuery(".comma-replace").keyup(function(){
		jQuery(this).val(jQuery(this).val().replace(',', '.'));
	});
	
	jQuery('.advantage_fields').change(Recalculate);
	jQuery('.advantage_fields').keyup(Recalculate);
	
	jQuery('.rate_line').click(SelectRow);
	
	Recalculate();
});

function Recalculate(){
	jQuery.post(
		'?option=com_calculator&controller=calculate&format=json', 
		jQuery('#calculator').serialize(), 
		function (data) {
			if(data.calculated){
				jQuery('#calculated').show();
				if(!jQuery('#order_form').is(':visible'))
					jQuery('#order_details_link').show();
				jQuery('#calc_results').show();
				if(data.with_inner){jQuery('#calculated_inner').show();}
								
				FillResults(data);
				jQuery('.rate_line').click(SelectRow);
				if(jQuery('.rate_line')[0])
					jQuery('.rate_line')[0].click();
				
			}else{
				jQuery('#calculated').hide();
				jQuery('#order_details_link').hide();
				jQuery('#order_form').hide();
				jQuery('#calc_results').hide();
				if(data.with_inner){jQuery('#calculated_inner').hide();}
			}
		}
	);
}

function FillResults(data){
	calc_model = data.prices;
		
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
}


function SelectRow(){
	var selected = jQuery(this).val();
	r = calc_model.filter(function(x){return x.uid == selected})[0];
	
	jQuery('#customer_price').text(r.customer_price);
	jQuery('#customer_nds').text(r.customer_nds);
	jQuery('#delivery_time').text(r.delivery_time);
	jQuery('#displayed_volume').text(r.displayed_volume);
	jQuery('#real_weight').text(r.real_weight);
	jQuery('#inner_price').text(r.inner_price);
	jQuery('#inner_nds').text(r.inner_nds);
	jQuery('#profit').text(r.profit);
	jQuery('#profit_nds').text(r.profit_nds);
	
	console.log(jQuery('#profit_nds'));
	console.log(r.profit_nds);
	console.log(r);
}

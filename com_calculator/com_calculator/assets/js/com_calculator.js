var calc_model = null;

jQuery(document).ready(function(){
	jQuery(".city_select").chosen();

	jQuery(".comma-replace").keyup(function(){
		jQuery(this).val(jQuery(this).val().replace(',', '.'));
	});
	
	jQuery('.advantage_fields').change(Recalculate);
	jQuery('.advantage_fields').keyup(Recalculate);
	
	jQuery('input[name=calc_row_id]').change(SelectRow(this));
});

function Recalculate(){
	jQuery.post(
		'?option=com_calculator&controller=calculate&format=json', 
		jQuery('#calculator').serialize(), 
		function (data) {
			if(data.calculated){
				jQuery('#calculated').show();
				jQuery('#order_details_link').show();
				jQuery('#calc_results').show();
				if(data.with_inner){jQuery('#calculated_inner').show();}
								
				FillResults(data);
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
			+ '<td><input type="radio" name="calc_row_id" value="'+v.uid+'" /></td>'
			+ '<td>'+v.tariff_name+'</td>'
			+ '<td>'+v.customer_price+'</td>'
			+ '<td>'+v.delivery_time+'</td>'
			+ (data.with_inner ? '<td>'+v.provider_name+'</td>' : '' )
		+'</tr>'));
	});
	
	//jQuery('input[name=calc_row_id]')[0].change();	
}


function SelectRow(elem){
	alert(elem.value);
	console.log(elem.value);
}

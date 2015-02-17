jQuery(document).ready(function(){
	jQuery(".city_select").chosen();

	jQuery(".comma-replace").keyup(function(){
		jQuery(this).val(jQuery(this).val().replace(',', '.'));
	});

	jQuery('.city_select').change(function(){ LoadTerminalList(jQuery(this).val(), jQuery(this).attr('name'));});
	
	jQuery('.advantage_fields').change(Recalculate);
	jQuery('.advantage_fields').keyup(Recalculate);
});

function UseFunctionSender(whichOpt) 
{
 	if (whichOpt == '1') {
		jQuery('#sender').addClass('physical');
	} else {
		jQuery('#sender').removeClass('physical');
	}
}

function UseFunctionReceiver(whichOpt) 
{
 	if (whichOpt == '1') {
		jQuery('#receiver').addClass('physical');
	} else {
		jQuery('#receiver').removeClass('physical');
	}
}


function LoadTerminalList(id, select_id)
{	
	jQuery.post('?controller=terminalslist&format=json', {city: id}, function (data){
			jQuery('#'+select_id+'_terminal option').remove();
			jQuery.each(data, function(index, t){
				jQuery('#'+select_id+'_terminal').append(jQuery('<option value="'+t.terminal+'">'+t.name+'</option>'));
			});
		}
	);
}

function Recalculate(){
	jQuery.post(
		'?controller=calculate&format=json', 
		jQuery('#calculator').serialize(), 
		function (data) {
			if(data.calculated){
				jQuery('#calculated').show();
				jQuery('#order_details_link').show();
			}else{
				jQuery('#calculated').hide();
				jQuery('#order_details_link').hide();
				jQuery('#order_form').hide();
			}				
			if(data.calculated_inner){
				jQuery('#calculated_inner').show();
			}else{
				jQuery('#calculated_inner').hide();
			}
			
			jQuery('#price').text(data.price);
			jQuery('#nds_part').text(data.nds_part);
			jQuery('#min_delivery_time').text(data.min_delivery_time);
			jQuery('#max_delivery_time').text(data.max_delivery_time);
			jQuery('#volume').text(data.volume >= 0.01? data.volume : 'менее 0.01');
			jQuery('#inner_price').text(data.inner_price);
			jQuery('#nds_part_inner').text(data.nds_part_inner);
			jQuery('#profit').text(data.profit);
			jQuery('#profit_nds_part').text(data.profit_nds_part);
		}
	);
}

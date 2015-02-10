jQuery(document).ready(function(){
	jQuery(".city_select").chosen();

	jQuery(".comma-replace").keyup(function(){
		jQuery(this).val(jQuery(this).val().replace(',', '.'));
	});

	jQuery('.city_select').change(function(){ LoadTerminalList(jQuery(this).val(), jQuery(this).attr('name'));});

	Calendar._DN = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"]; Calendar._SDN = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat","Sun"]; Calendar._FD = 0; Calendar._MN = ["January","February","March","April","May","June","July","August","September","October","November","December"]; Calendar._SMN = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"]; Calendar._TT = {"INFO":"About the Calendar","ABOUT":"DHTML Date\/Time Selector\n(c) dynarch.com 2002-2005 \/ Author: Mihai Bazon\nFor latest version visit: http:\/\/www.dynarch.com\/projects\/calendar\/\nDistributed under GNU LGPL.  See http:\/\/gnu.org\/licenses\/lgpl.html for details.\n\nDate selection:\n- Use the \u00ab and \u00bb buttons to select year\n- Use the < and > buttons to select month\n- Hold mouse button on any of the above buttons for faster selection.","ABOUT_TIME":"\n\nTime selection:\n- Click on any of the time parts to increase it\n- or Shift-click to decrease it\n- or click and drag for faster selection.","PREV_YEAR":"Click to move to the previous year. Click and hold for a list of years.","PREV_MONTH":"Click to move to the previous month. Click and hold for a list of the months.","GO_TODAY":"Go to today","NEXT_MONTH":"Click to move to the next month. Click and hold for a list of the months.","SEL_DATE":"Select a date.","DRAG_TO_MOVE":"Drag to move","PART_TODAY":" Today ","DAY_FIRST":"Display %s first","WEEKEND":"0,6","CLOSE":"Close","TODAY":"Today","TIME_PART":"(Shift-)Click or Drag to change the value.","DEF_DATE_FORMAT":"%Y-%m-%d","TT_DATE_FORMAT":"%a, %b %e","WK":"wk","TIME":"Time:"};
	
	Calendar.setup({
		inputField: "produceDate",
		ifFormat: "%Y-%m-%d",
		align: "Bl",
		singleClick: true,
		firstDay: 0
		});
	
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

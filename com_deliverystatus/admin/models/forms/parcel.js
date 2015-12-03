jQuery(function() {
    document.formvalidator.setHandler('parcelnumber',
        function (value) {
            regex=/^[0-9]+$/;
            return regex.test(value);
        });
    document.formvalidator.setHandler('sender',
            function (value) {
                regex=/^.+$/;
                return regex.test(value);
            });
    document.formvalidator.setHandler('receiver',
            function (value) {
                regex=/^.+$/;
                return regex.test(value);
            });
    document.formvalidator.setHandler('address',
            function (value) {
                regex=/^.+$/;
                return regex.test(value);
            });
    
    var dt = new Date();
    var val = dt.getFullYear()+'-'+dt.getMonth()+'-'+dt.getDay();
    jQuery("#jform_dt").datetimepicker(
    	{value: val,
		format:	'Y-m-d H:i',
		formatTime:	'H:i',
		formatDate:	'Y-m-d'}
    );
});
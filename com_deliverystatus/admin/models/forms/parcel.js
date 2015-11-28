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
    var val = dt.getDay()+'.'+dt.getMonth()+'.'+dt.getFullYear();
    jQuery("#jform_dt").datetimepicker(
    	{value: val,
		format:	'd.m.Y H:i',
		formatTime:	'H:i',
		formatDate:	'd.m.Y'}
    );
});
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
});
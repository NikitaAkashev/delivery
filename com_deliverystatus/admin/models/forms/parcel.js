jQuery(function() {
    document.formvalidator.setHandler('parcelnumber',
        function (value) {
            regex=/^[0-9]+$/;
            return regex.test(value);
        });
});
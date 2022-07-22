jQuery('#woof_get_export').click(function () {
    jQuery(this).hide();
    var data = {
        action: "woof_get_export_data"
    };
    jQuery.post(ajaxurl, data, function (answer) {
        //***

        jQuery('#woof_export_settings').text(answer);
    });
});

jQuery('#woof_do_import').click(function () {
    var data_import = jQuery('#woof_import_settings').val();
    if (!data_import) {
        alert(woof_imp_exp_vars.empty);
        return;
    }

    if (confirm(woof_imp_exp_vars.sure)) {
        var data = {
            action: "woof_do_import_data",
            import_value: data_import
        };
        jQuery.post(ajaxurl, data, function (answer) {
            // jQuery('#woof_do_import').hide();
            jQuery('#woof_do_import').after("<p>" + answer + "</p>");
            alert(answer);
            location.reload();
        });

    }

});
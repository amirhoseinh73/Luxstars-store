function woof_sections_html_items() {

    var sections = jQuery('.woof_section_tab');
    var request = woof_current_values.replace(/(\\)/, '');
    request = jQuery.parseJSON(request);

    jQuery.each(sections, function (e, item) {
        var _this = this;
        jQuery.each(request, function (k, val) {

            var selected = jQuery(_this).find(".woof_container_" + k);
            if (jQuery(selected).length) {
                if (!jQuery(_this).prev('label').prev("input:checked").length) {
                    jQuery(_this).prev('label').trigger('click');
                }

            }
        });
    });
}

woof_sections_html_items()

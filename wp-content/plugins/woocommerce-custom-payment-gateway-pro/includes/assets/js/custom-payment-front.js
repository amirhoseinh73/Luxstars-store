jQuery(function() {
	jQuery('body').on('updated_checkout', function(){
		var dateElement = jQuery('.custom_payment_date_input');
		jQuery(dateElement).datepicker({
			changeMonth: true,
			changeYear: true,
			yearRange:"c-100:c+25"
		});
		jQuery(dateElement).datepicker("option", "dateFormat", jQuery( dateElement ).attr('data-dateformat'));
		jQuery(dateElement).datepicker("setDate",jQuery( dateElement ).attr('data-defaultdate'));

	});

});
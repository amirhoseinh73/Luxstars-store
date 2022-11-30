jQuery(function( $ ) {

	$('.orddd-import-yes').click(function( event ){
		event.preventDefault();
		
		$('.orddd_import_checkboxes').fadeIn();
		$('.orddd_import_yes_no').fadeOut();
	});

	$('.orddd-import-no').click(function( event ){
		event.preventDefault();
		
		$.post( ajaxurl, {
			action    : 'orddd_do_not_import_lite_data',
			
		}, function( orddd_do_not_import_lite_data_response ) {
			window.location = 'admin.php?page=order_delivery_date';
		});
	});

	$('.orddd-import-now').click(function( event ){
		event.preventDefault();
		var orddd_delivery_dates_import = $('#orddd_delivery_dates_import').is(":checked");
		var wcap_import_settings = $('#wcap_settings_import').is(":checked"); 
		$.post( ajaxurl, {
			action    : 'orddd_import_lite_data',
			orddd_import_delivery_dates : orddd_delivery_dates_import,
			wcap_import_settings: wcap_import_settings
		}, function( orddd_import_lite_data_response ) {
			window.location.replace("admin.php?page=order_delivery_date&orddd_lite_import=yes");
		});
	});

	$('#orddd_plugin_page_import').click(function( event ){
		event.preventDefault();
		
		window.location = 'admin.php?page=orddd-update&orddd_plugin_link=orddd-update';
	});

	/** Set the value when the Enable delivery date checkbox is checked or unchecked in the Category settings. */
	var selectElement = document.querySelector('#orddd_delivery_date_for_product_category');
	if( null !== selectElement && undefined != selectElement ) {
		selectElement.addEventListener('change', function( event) {
			if( event.target.checked ) {
				document.querySelector('#orddd_delivery_date_for_product_category_value').value = 'on'
			} else {
				document.querySelector('#orddd_delivery_date_for_product_category_value').value = '';
			}
		});
	}
});
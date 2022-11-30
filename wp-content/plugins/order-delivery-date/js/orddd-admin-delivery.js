/**
 * Ajax to add/remove the Delivery fields for the Virtual products in the Admin order page.
 *
 * @namespace orddd_admin_delivery
 * @since 3.2
 */

jQuery( function( $ ) {
	
	var admin_funtions = {

		init: function() {
			jQuery( document ).on( 
                'ajaxComplete',
                admin_funtions.add_remove_order_item
            );

			jQuery( document ).on( 
                'ajaxComplete',
                admin_funtions.add_remove_order_item
            );

			jQuery( document ).on( 
				'items_saved',
				admin_funtions.save_order_item
			);
		},

		/** When an item is added or removed from the Order meta box */
		add_remove_order_item: function( event, xhr, options ) {
			var options_data = options.data;
			if( typeof( options_data ) !== "undefined" ) {
				var options_arr = options_data.split( "&" );
				for( var i = 0; i <= options_arr.length; i++ ) {
					if( typeof( options_arr[ i ] ) !== "undefined" ) {
						var option_value_arr = options_arr[ i ].split( "=" );
						if( option_value_arr[ 0 ] == "action" && option_value_arr[ 1 ] == "woocommerce_remove_order_item" ) {
							var data = {
								order_id: orddd_admin_params.orddd_order_id,
								action: 'orddd_remove_order_item',
							}

							$.ajax({
								url: orddd_admin_params.orddd_admin_url + "admin-ajax.php",
								data: data,
								type: 'POST',
								success: function( response ) {
									if ( '' !== response.settings_based_on ) {
				
										if( 'on' == orddd_params.orddd_enable_shipping_based_delivery ) {
											if( 'product_categories' == response.settings_based_on ) {
								 				orddd_params.orddd_category_settings_to_load = response.orddd_category_settings_to_load;
											} else if ( 'shipping_class' == response.settings_based_on ) {
												orddd_params.orddd_shipping_class_settings_to_load = response.orddd_shipping_class_settings_to_load;
											} else {
												orddd_params.orddd_category_settings_to_load = '';
												orddd_params.orddd_shipping_class_settings_to_load = '';
											}
											jQuery( "#admin_delivery_fields" ).show();
											jQuery( "#is_virtual_product" ).html( "" );
											load_delivery_date();
										}
									} else {
										jQuery( "#admin_delivery_fields" ).hide();
										jQuery( "#is_virtual_product" ).html( "Delivery date settings is not enabled for this product." );
									}
								},
							});
						} else if ( option_value_arr[ 0 ] == "action" && option_value_arr[ 1 ] == "woocommerce_add_order_item" ) {
							admin_funtions.save_order_item();
						}
					}
				}
			}
		},

		/** When the line items are saved through the save button */
		save_order_item: function() {
			var data = {
				order_id: orddd_admin_params.orddd_order_id,
				action: 'orddd_save_order_items',
			}

			$.ajax({
				url: orddd_admin_params.orddd_admin_url + "admin-ajax.php",
				data: data,
				type: 'POST',
				success: function( response ) {
					if ( '' !== response.settings_based_on ) {
						if( 'on' == orddd_params.orddd_enable_shipping_based_delivery ) {
							if( 'product_categories' == response.settings_based_on ) {
								 orddd_params.orddd_category_settings_to_load = response.orddd_category_settings_to_load;
							} else if ( 'shipping_class' == response.settings_based_on ) {
								orddd_params.orddd_shipping_class_settings_to_load = response.orddd_shipping_class_settings_to_load;
							} else {
								orddd_params.orddd_category_settings_to_load = '';
								orddd_params.orddd_shipping_class_settings_to_load = '';
							}
							jQuery( "#admin_delivery_fields" ).show();
							jQuery( "#is_virtual_product" ).html( "" );

							load_delivery_date();
						}
					} else {
						jQuery( "#admin_delivery_fields" ).hide();
						jQuery( "#is_virtual_product" ).html( "Delivery date settings is not enabled for this product." );
					}
				},
			});
		}
	}

	admin_funtions.init();
});
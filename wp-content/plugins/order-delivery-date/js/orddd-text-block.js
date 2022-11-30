jQuery( function( $ ) {
    jQuery( '#orddd_locations' ).select2();

    var shipping_method = orddd_get_selected_shipping_method();

    if( shipping_method.indexOf( 'local_pickup' ) === -1 ) {
        jQuery( "#orddd_locations_field" ).hide();
        jQuery( "#orddd_locations" ).val( "select_location" ).trigger( "change" );    
    } else {
        jQuery( "#orddd_locations_field" ).show();    
        if ( 'on' === orddd_text_params.orddd_auto_populate_first_pickup_location && ( ( '' === localStorage.getItem( "orddd_location_session" ) || undefined === localStorage.getItem( "orddd_location_session" ) ) || 'select_location' === localStorage.getItem( "orddd_location_session" ) ) ) {
            jQuery( "#orddd_locations" ).prop("selectedIndex", 1).trigger( "change" ); 
        } else if ( localStorage.getItem( "orddd_location_session" ) == '' || localStorage.getItem( "orddd_location_session" ) == undefined || 'select_location' === localStorage.getItem( "orddd_location_session" ) ) {
            jQuery( "#orddd_locations" ).prop("selectedIndex", 0 ).trigger( "change" );
        } 
    }

    jQuery( document ).on( 
        'change', 
        'input[name="shipping_method[0]"]',
        select_shipping_method
    );

    jQuery( document ).on( 
        'change', 
        'select[name="shipping_method[0]"]', 
        select_shipping_method
    );

    jQuery( document ).on( 
        'change', 
        'input[name="shipping_method_[0]"]', 
        select_shipping_method
    );
    
});

function orddd_get_selected_shipping_method() {
    var shipping_method = jQuery( "input[name=\"shipping_method[0]\"]:checked" ).val();
    if( typeof shipping_method === "undefined" ) {
        var shipping_method = jQuery( "select[name=\"shipping_method[0]\"] option:selected" ).val();
    }
    if( typeof shipping_method === "undefined" ) {
        var shipping_method = jQuery( "input[name=\"shipping_method[0]\"]" ).val();                    
    }

    if( typeof shipping_method === "undefined" ) {
        var shipping_method = jQuery( "input[name=\"shipping_method_[0]\"]:checked" ).val();
    }
    
    if( typeof shipping_method === "undefined" ) {
        var shipping_method = "";
    }

    // Check if the shipping package for Advance Shipping Packages for WooCommerce plugin are added.
    // If yes check for the shipping methods for the shipping package.
    if( typeof jQuery( "#orddd_shipping_package_to_load" ).val() !== "undefined" && 
        jQuery( "#orddd_shipping_package_to_load" ).val() != '' ) { 
        var shipping_package = jQuery( "#orddd_shipping_package_to_load" ).val();
        var shipping_package_method = jQuery( "input[name=\"shipping_method[" + shipping_package + "]\"]:checked" ).val();
        if( typeof shipping_package_method === "undefined" ) {
            shipping_package_method = jQuery( "select[name=\"shipping_method[" + shipping_package + "]\"] option:selected" ).val();
        }

        if( typeof shipping_package_method === "undefined" ) {
            var shipping_package_method = jQuery( "input[name=\"shipping_method[" + shipping_package + "]\"]" ).val();                    
        }

        if( typeof shipping_package_method != "undefined" ) {
            shipping_method = shipping_package_method + ":" + shipping_package;
        }
    }
    
    return shipping_method;
}

function select_shipping_method() {
    var shipping_method = orddd_get_selected_shipping_method();

    if( shipping_method.indexOf( 'local_pickup' ) === -1 ) {
        jQuery( "#orddd_locations_field" ).hide();
        jQuery( "#orddd_locations" ).val( "select_location" ).trigger( "change" );    
    } else {
        jQuery( "#orddd_locations_field" ).show();    
        if ( 'on' === orddd_text_params.orddd_auto_populate_first_pickup_location && ( ( '' === localStorage.getItem( "orddd_location_session" ) || undefined === localStorage.getItem( "orddd_location_session" ) ) || 'select_location' === localStorage.getItem( "orddd_location_session" ) ) ) {
            jQuery( "#orddd_locations" ).prop("selectedIndex", 1).trigger( "change" ); 
        } else if ( localStorage.getItem( "orddd_location_session" ) == '' || localStorage.getItem( "orddd_location_session" ) == undefined || 'select_location' === localStorage.getItem( "orddd_location_session" ) ) {
            jQuery( "#orddd_locations" ).prop("selectedIndex", 0 ).trigger( "change" );
        } 
    }
}
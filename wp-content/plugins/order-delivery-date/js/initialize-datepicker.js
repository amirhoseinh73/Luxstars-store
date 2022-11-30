/**
 * Allows to initiliaze/load the settings in the calendar on frontend.
 *
 * @namespace orddd_initialize
 * @since 1.0
 */
jQuery( function( $ ) {   
    // orddd_params is required to continue, ensure the object exists
	if ( typeof orddd_params === 'undefined' ) {
		return false;
	}

    if( undefined !== jQuery.blockUI ) {
        jQuery.blockUI.defaults.overlayCSS.cursor = 'default';
    }
    
    var shipping_method = orddd_get_selected_shipping_method();

    var orddd_functions = {

        init: function() {
            //Select Woo class for time alot and pickup locations field. 
            jQuery( '#orddd_time_slot' ).select2();
            jQuery( '#orddd_locations' ).select2();
            
            orddd_functions.maybe_clear_local_storage;

            if( 'on' === jsL10n.is_timeslot_list_view ) {
                jQuery( '#orddd_time_slot' ).hide();
                orddd_load_time_slots_list();
            }

            var shipping_method = orddd_get_selected_shipping_method();

            if( shipping_method.indexOf( 'local_pickup' ) === -1 ) {
                jQuery( "#orddd_locations_field" ).hide();
                jQuery( "#orddd_locations" ).val( "select_location" ).trigger( "change" );    
                jQuery( "#orddd_locations" ).prop("selectedIndex", 0 ).trigger( "change" );   
            }
        
            jQuery( "#orddd_unique_custom_settings" ).val( "" );            
            jQuery( '#' + orddd_params.orddd_field_name ).prop( "disabled", true );

            if( '1' == orddd_params.orddd_is_account_page ) {
                window.onload = orddd_my_account_init();
            } else if( '1' == orddd_params.orddd_is_admin ) {
                window.onload = orddd_init();
            } else {
                window.onload = load_general_settings();
            }

            orddd_functions.style_widget;
            jQuery.extend( jQuery.datepicker, { afterShow: function( event ) {
                    var z_index = 9999;
                    if( jQuery.datepicker._getInst( event.target ).dpDiv.css('z-index') > z_index ) {
                        z_index = jQuery.datepicker._getInst( event.target ).dpDiv.css('z-index');
                    }
                    jQuery.datepicker._getInst( event.target ).dpDiv.css( "z-index", z_index );
                    
                    // If the device is mobile then make the calendar appear below the input field.
                    if( screen.width < 600 ) {
                        jQuery.datepicker._getInst( event.target ).dpDiv.css( { top: jQuery('#' + orddd_params.orddd_field_name ).offset().top + 35, left: jQuery('#' + orddd_params.orddd_field_name ).offset().left} );
                    }

                    if( "1" == orddd_params.orddd_number_of_months && '1' == orddd_params.orddd_is_admin ) {
                        jQuery.datepicker._getInst( event.target ).dpDiv.css( "width", "17em" );
                    } else if ( "1" == orddd_params.orddd_number_of_months ) {
                        jQuery.datepicker._getInst( event.target ).dpDiv.css( "width", "300px" );
                    } else {
                        jQuery.datepicker._getInst( event.target ).dpDiv.css( "width", "41em" );
                    }
                }
            });
        
            // If the device is mobile then the input field will move to the top.
            if( screen.width < 600 ) {
                jQuery('#' + orddd_params.orddd_field_name ).focus(function () {    
                    jQuery('html, body').animate({ scrollTop: jQuery(this).offset().top - 25 }, 10);
                });
            }

            if ( '' != orddd_params.orddd_field_note_text ) {
                jQuery( "#e_deliverydate_field" ).append( "<br><small class='orddd_field_note'>" + orddd_params.orddd_field_note_text + "</small>" );
            }
        
            if( '1' == orddd_params.orddd_is_admin ) {
                jQuery( '#' + orddd_params.orddd_field_name ).width( "150px" );
                jQuery( '#' + orddd_params.orddd_field_name ).attr( "readonly", true );
            }
        
            jQuery( '#edit_delivery_date' ).on( 'click', function() {
                jQuery( '#orddd_edit_div' ).toggle();
            });
            jQuery( '#cancel_delivery_date' ).on( 'click', function() {
                jQuery( '#orddd_edit_div' ).fadeOut(); 
            });

            //For flatsome theme
            if( jQuery('#e_deliverydate').parent().hasClass('fl-wrap') ) {
                jQuery('#e_deliverydate').parent().addClass('fl-is-active');
            }

            if( jQuery( '#orddd_time_slot' ).parent().hasClass('fl-wrap') ) {
                jQuery( '#orddd_time_slot' ).parent().addClass('fl-is-active');
            }

            if( jQuery( '#orddd_locations' ).parent().hasClass('fl-wrap') ) {
                jQuery( '#orddd_locations' ).parent().addClass('fl-is-active');
            }

            jQuery( 'form.checkout' ).on( 
                'input validate change',
                '#orddd_time_slot',
                orddd_functions.validate_time_slot 
            );

            jQuery( document ).on( 
                'change', 
                '#orddd_time_slot',
                orddd_functions.select_time_slot
            );

            jQuery( document ).on( 
                'change', 
                'select[name=\"orddd_locations\"]',
                orddd_functions.select_pickup_location
            );

            jQuery( document ).on( 
                'change', 
                'input[name=\"shipping_method[0]\"]',
                orddd_functions.select_shipping_method
            );

            jQuery( document ).on( 
                'change', 
                'select[name=\"shipping_method[0]\"]', 
                orddd_functions.update_session
            );
        
            jQuery( document ).on( 
                'change', 
                'input[name=\"shipping_method_[0]\"]', 
                orddd_functions.update_session
            );
        
            jQuery( document ).on( 
                "change", 
                '#ship-to-different-address input', 
                orddd_functions.update_session
            );

            jQuery( document ).on( 
                'change', 
                '.address-field input.input-text, .update_totals_on_change input.input-text, .address-field select',
                orddd_functions.maybe_disable_delivery_field
            );

            jQuery( document ).on( 
                'ajaxComplete',
                orddd_functions.update_cart_settings
            );

            jQuery( document ).on( 
                'updated_cart_totals',
                orddd_functions.update_zone_id
            );

            jQuery( document ).on( 
                'updated_checkout',
                orddd_functions.update_zone_id
            );

            jQuery( '#update_date' ).on( 
                'click',
                orddd_functions.update_date_from_account
            );

            // Update the delivery session when a product is added or removed on one page checkout.
            jQuery( 'body' ).on( 
                'after_opc_add_remove_product',
                orddd_functions.update_session
            );
            
            if( '1' == orddd_params.orddd_is_admin ) {
               
                jQuery( "#save_delivery_date" ).click(function() {
                    save_delivery_dates( 'no' );
                });        
        
                jQuery( "#save_delivery_date_and_notify" ).click(function() {
                    save_delivery_dates( 'yes' );
                });        
            }
        },

        /**
         * Validate the time slot field if mandatory.
         */
        validate_time_slot: function( e ) {
            var parent =  jQuery( '#orddd_time_slot' ).closest( '.form-row' );
            validated = true;

            if ( 'validate' === e.type || 'change' === e.type ) {
                if( jQuery('#orddd_time_slot').val() == 'select' && 'checked' == orddd_params.orddd_timeslot_field_mandatory ) {
                    jQuery(parent).removeClass( 'woocommerce-validated' ).addClass( 'woocommerce-invalid woocommerce-invalid-required-field' );
                    validated = false;
                }
            }
            
            if( validated ) {
                jQuery(parent).removeClass( 'woocommerce-invalid woocommerce-invalid-required-field' ).addClass( 'woocommerce-validated' );
            }
        },

        /**
         * Update the local storage on change of time slot
         */
        select_time_slot: function() {
            var shipping_method = orddd_get_selected_shipping_method();
            jQuery( "#hidden_e_deliverydate" ).val( jQuery( '#' + orddd_params.orddd_field_name ).val() );
            jQuery( "#hidden_h_deliverydate" ).val( jQuery( "#h_deliverydate" ).val() );
            jQuery( "#hidden_timeslot" ).val( jQuery(this).find(":selected").val() );
            jQuery( "#hidden_shipping_method" ).val( shipping_method );
            jQuery( "#hidden_shipping_class" ).val( orddd_params.orddd_shipping_class_settings_to_load );
            jQuery( "#hidden_location" ).val( jQuery( "#orddd_locations" ).find(":selected").val() );

            var selected_val = jQuery(this).val();
            jQuery(this).find('option[value="'+ selected_val + '"]').prop( 'selected', true );
            if ( "1" !== orddd_params.orddd_is_admin ) {
                localStorage.setItem( "e_deliverydate_session", jQuery( '#' + orddd_params.orddd_field_name ).val() );
                localStorage.setItem( "h_deliverydate_session", jQuery( "#h_deliverydate" ).val() );
                localStorage.setItem( "orddd_time_slot", selected_val );
            }

            var current_date = orddd_get_current_day();
            var split_current_date = current_date.split( '-' );
            var ordd_next_date = new Date( split_current_date[ 2 ], ( split_current_date[ 1 ] - 1 ), split_current_date[ 0 ],orddd_params.orddd_current_hour, orddd_params.orddd_current_minute );

            ordd_next_date.setHours( ordd_next_date.getHours() + 2 );
            localStorage.setItem( "orddd_storage_next_time", ordd_next_date.getTime() );

            jQuery( "body" ).trigger( "update_checkout" );
            if ( 'on' == orddd_params.orddd_delivery_date_on_cart_page && '1' == orddd_params.orddd_is_cart ) {
                jQuery( "body" ).trigger( "wc_update_cart" );
            }
            jQuery( "body" ).trigger( "change_orddd_time_slot", [ jQuery( this ) ] );
        },

        /**
         * Update settings on change of Pickup Location
         */
        select_pickup_location: function() {
            if ( 'on' == orddd_params.orddd_enable_shipping_based_delivery ) {
                var update_settings = load_delivery_date();
                if( update_settings == 'yes' && orddd_params.orddd_enable_autofill_of_delivery_date == 'on' ) {
                    orddd_autofil_date_time( 'no' );
                }
            }
            var location = jQuery(this).val();
            if( null === location ) {
                location = 'select_location';
            }
            localStorage.setItem( "orddd_location_session", location );
        },

        /**
         * Update settings on change of shipping method
         */
        select_shipping_method: function() {
            if( 'on' == orddd_params.orddd_enable_shipping_based_delivery ) {
                localStorage.removeItem( "orddd_storage_next_time" );
                localStorage.removeItem( "e_deliverydate_session" );
                localStorage.removeItem( "h_deliverydate_session" );
                localStorage.removeItem( "orddd_time_slot" );  
            }
            orddd_update_delivery_session();
        },

        /**
         * Disable date field until the address is entered
         */
        maybe_disable_delivery_field: function() {
            if( "on" == orddd_params.orddd_enable_shipping_based_delivery && 'yes' == orddd_params.orddd_disable_delivery_fields ) {
                jQuery( '#' + orddd_params.orddd_field_name ).datepicker( "option", "disabled", true );    
                jQuery( "#orddd_time_slot" ).attr( "disabled", "disabled" );
            }
        },

        /**
         * Update the delivery calendar when an item is removed from the cart
         */
        update_cart_settings: function( event, xhr, options ) {
            //don't execute for get_refreshed_fragments as it will lead to infinite loop.
            if ( options.url.indexOf( "wc-ajax=get_refreshed_fragments" ) !== -1 ) {
                return;
            }
            // Update the custom settings when an item is removed from cart.
            if ( options.url.indexOf( "cart/?remove_item=" ) !== -1 || options.url.indexOf( "cart/?undo_item=" ) !== -1 ) {
                if( xhr.statusText != "abort" ) {
                    orddd_update_delivery_session( 'cart_delete' );
                }
            }
        },

        /**
         * Update the custom settings when the shipping zone is changed.
         */
        update_zone_id: function( data ) {
            var changed_method = orddd_get_selected_shipping_method( 'yes' );
            if ( 'on' !== orddd_params.orddd_enable_shipping_based_delivery ) {
                return false;
            }
            if( shipping_method != changed_method ) {
                var zone_details = orddd_params.orddd_zones;
    
                for ( var zone in zone_details ) {
                    var zone_array = zone_details[zone].split("-");
    
                    if ( ( zone_array[1] == changed_method && zone_array[0] != orddd_params.zone_id ) || '' === changed_method ) {
                        if( '' === changed_method ) {
                            orddd_params.orddd_zone_id = '';
                            orddd_params.orddd_shipping_id = '';
                        } else {
                            orddd_params.orddd_zone_id = zone_array[0];
                            orddd_params.orddd_shipping_id = zone_array[1];
                        }
                        jQuery( '#orddd_zone_id' ).val( orddd_params.orddd_zone_id );
                        jQuery( '#' + orddd_params.orddd_field_name ).datepicker( "option", "disabled", false );
                        jQuery( "#orddd_time_slot" ).removeAttr( "disabled", "disabled" );
    
                        orddd_update_delivery_session();
    
                        shipping_method = changed_method;
                    }
                }
            }
        },

        /**
         * Update delivery session & custom settings
         */
        update_session: function() {
            orddd_update_delivery_session();
        },

        /**
         * Clear the local storage after every 2 hours
         */
        maybe_clear_local_storage: function() {
            //Clear local storage for the selected delivery date in next 2 hours. 
            var orddd_last_check_date = localStorage.getItem( "orddd_storage_next_time" );
            var current_date = orddd_get_current_day();
            
            if( current_date != '' && typeof( current_date ) != 'undefined' ) {
                var split_current_date = current_date.split( '-' );
                var ordd_next_date = new Date( split_current_date[ 2 ], ( split_current_date[ 1 ] - 1 ), split_current_date[ 0 ], orddd_params.orddd_current_hour, orddd_params.orddd_current_minute );
            } else {
                var ordd_next_date = new Date();
            }

            if ( null != orddd_last_check_date ) {
                if ( ordd_next_date.getTime() > orddd_last_check_date ) {
                    localStorage.removeItem( "orddd_storage_next_time" );
                    localStorage.removeItem( "e_deliverydate_session" );
                    localStorage.removeItem( "h_deliverydate_session" );
                    localStorage.removeItem( "orddd_time_slot" );
                    localStorage.removeItem( "orddd_availability_postcode" );
                }
            }
        }, 

        /** 
         * Update the Delivery date & time on My Account page
         */
        update_date_from_account: function() {
            var ordd_date_and_time_validation = "allow";

            var ordd_is_delivery_date_mandatory = orddd_params.orddd_date_field_mandatory;
            var ordd_is_delivery_time_mandatory = orddd_params.orddd_timeslot_field_mandatory;
            
            var ordd_get_delivery_date = jQuery( '#' + orddd_params.orddd_field_name ).val();
            var ordd_get_delivery_time = jQuery( '#orddd_time_slot' ).val();
    
            var ordd_date_label        = orddd_params.orddd_field_label;
            var ordd_time_label        = orddd_params.orddd_timeslot_field_label;
    
            var ordd_validation_message = "";
            if ( "checked" == ordd_is_delivery_date_mandatory && "checked" == ordd_is_delivery_time_mandatory ) {
                ordd_validation_message =  ordd_date_label + " is a required field." + ordd_time_label + " is a required field.";
                if ( ordd_get_delivery_date.length == 0 ||  "select" == ordd_get_delivery_time ) {
                    ordd_date_and_time_validation = "no";
                }
            }else if ( "checked" == ordd_is_delivery_date_mandatory ) {
                ordd_validation_message = ordd_date_label +" is a required field.";
                if ( ordd_get_delivery_date.length == 0 ) {
                    ordd_date_and_time_validation = "no";
                }
            } else if ( "checked" == ordd_is_delivery_time_mandatory ) {
                ordd_validation_message = ordd_time_label + " is a required field.";
                if ( "select" == ordd_get_delivery_time ) {
                    ordd_date_and_time_validation = "no";
                }
            }
    
            if ( "no" == ordd_date_and_time_validation ) {
                jQuery( "#display_update_message" ).css( "color","red" );
                jQuery( "#display_update_message" ).html( ordd_validation_message );
                jQuery( "#display_update_message" ).fadeIn();
                var delay = 2000; 
                setTimeout(function() {
                    jQuery( "#display_update_message" ).fadeOut();
                }, delay );
            }
     
            if ( "allow" == ordd_date_and_time_validation ) {
                var data = {
                    order_id: orddd_account_params.orddd_my_account_order_id,
                    e_deliverydate: jQuery( '#' + orddd_params.orddd_field_name ).val(),
                    h_deliverydate: jQuery( '#h_deliverydate' ).val(),
                    shipping_method: jQuery( '#shipping_method' ).val(),
                    orddd_category_settings_to_load: orddd_params.orddd_category_settings_to_load,
                    time_setting_enable_for_shipping_method: orddd_params['custom_settings'].time_setting_enable_for_shipping_method,
                    orddd_time_settings_selected: orddd_params.orddd_time_settings_selected,
                    orddd_time_slot: jQuery( '#orddd_time_slot' ).val(),
                    is_my_account: orddd_account_params.orddd_is_account_page,
                };
                jQuery( '#display_update_message' ).html( '<b>Saving...</b>' );

                jQuery.ajax({
                    type:		'POST',
                    url:		orddd_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'orddd_update_delivery_date' ),
                    data:		data,
                    success:	function( response ) {
                        jQuery( '#display_update_message' ).html( jsL10n.success_delivery_date_message );
                        var delay = 500; //10 second
                        setTimeout(function() {
                            location.reload();
                        }, delay);
                    }
                });
            }
        },

        /**
         * Adjust the datepicker size for mobile
         */
        adjust_datepicker_size: function() {
            jQuery.extend( jQuery.datepicker, { afterShow: function( event ) {
                var z_index = 9999;
                if( jQuery.datepicker._getInst( event.target ).dpDiv.css('z-index') > z_index ) {
                    z_index = jQuery.datepicker._getInst( event.target ).dpDiv.css('z-index');
                }
                jQuery.datepicker._getInst( event.target ).dpDiv.css( "z-index", z_index );
                
                // If the device is mobile then make the calendar appear below the input field.
                if( screen.width < 600 ) {
                    jQuery.datepicker._getInst( event.target ).dpDiv.css( { top: jQuery('#' + orddd_params.orddd_field_name ).offset().top + 35, left: jQuery('#' + orddd_params.orddd_field_name ).offset().left} );
                }
                    if( "1" == orddd_params.orddd_number_of_months && '1' == orddd_params.orddd_is_admin ) {
                        jQuery.datepicker._getInst( event.target ).dpDiv.css( "width", "17em" );
                    } else if ( "1" == orddd_params.orddd_number_of_months ) {
                        jQuery.datepicker._getInst( event.target ).dpDiv.css( "width", "300px" );
                    } else {
                        jQuery.datepicker._getInst( event.target ).dpDiv.css( "width", "41em" );
                    }
                }
            });
        
            // If the device is mobile then the input field will move to the top.
            if( screen.width < 600 ) {
                jQuery('#' + orddd_params.orddd_field_name ).focus(function () {    
                    jQuery('html, body').animate({ scrollTop: jQuery(this).offset().top - 25 }, 10);
                });
            }
        },

        /** 
         * Availability widget settings
         */
        style_widget: function() {
            var local_storage_postcode = localStorage.getItem( "orddd_availability_postcode" );
            if( local_storage_postcode != '' && local_storage_postcode != 'undefined' && local_storage_postcode != null ) {
                jQuery( '#billing_postcode' ).val( local_storage_postcode );    
            }
            
            var orddd_available_dates_color = jQuery( "#orddd_available_dates_color" ).val() + '59';
            var orddd_booked_dates_color    = jQuery( "#orddd_booked_dates_color" ).val() + '59';

            jQuery( ".partially-booked" ).children().attr( 'style', 'background: linear-gradient(to bottom right, ' + orddd_booked_dates_color + ' 0%, ' + orddd_booked_dates_color + ' 50%, ' + orddd_available_dates_color + ' 50%, ' + orddd_available_dates_color + ' 100%);' );
            jQuery( ".available-deliveries" ).children().attr( 'style', 'background: ' + orddd_available_dates_color + ' !important;' );
        },

        /**
         * Update date & time from Edit order page
         * @param {string} $notify whether to notify the customer or not.
         */
        update_date_from_admin: function( $notify ) {
            save_delivery_dates( $notify );
        }
    }
   
    orddd_functions.init();
});

/**
 * Initialize & load the settings for dates displayed in dropdown.
 */
jQuery( function( $ ) {
    // orddd_params is required to continue, ensure the object exists
	if ( typeof orddd_params === 'undefined' ) {
		return false;
	}

    var orddd_functions = {

        init: function() {
            $( '#e_deliverydate' ).select2();
            $( '#orddd_time_slot' ).select2();
            $( '#orddd_locations' ).select2();

            if( 'on' === jsL10n.is_timeslot_list_view ) {
                jQuery( '#orddd_time_slot' ).hide(); 
                orddd_load_time_slots_list();
            }

            orddd_functions.maybe_clear_local_storage;

            //Hide pickup location field if the shipping method is not selected. 
            var shipping_method = orddd_get_selected_shipping_method();
            if( shipping_method.indexOf( 'local_pickup' ) === -1 ) {
                jQuery( "#orddd_locations_field" ).hide();
                jQuery( "#orddd_locations" ).val( "select_location" ).trigger( "change" );    
            }

            window.onload = load_functions();
            orddd_functions.style_widget;
            if ( "on" === orddd_params.orddd_enable_autofill_of_delivery_date ) {
                orddd_autofil_date_time();
            } else { 
                orddd_set_date_dropdown_from_session();
            }

            jQuery( '#edit_delivery_date' ).on( 'click', function() {
                jQuery( '#orddd_edit_div' ).toggle();
            });
            jQuery( '#cancel_delivery_date' ).on( 'click', function() {
                jQuery( '#orddd_edit_div' ).fadeOut();
            });

            if ( '' != orddd_params.orddd_field_note_text ) {
                jQuery( "#e_deliverydate_field" ).append( "<br><small class='orddd_field_note'>" + orddd_params.orddd_field_note_text + "</small>" );
            }

            jQuery( 'form.checkout' ).on( 
                'input validate change',
                '#orddd_time_slot',
                orddd_functions.validate_time_slot 
            );

            $( document ).on( 
                'change', 
                '#e_deliverydate',
                orddd_functions.select_delivery_date
            );

            jQuery( '#update_date' ).on( 
                'click',
                orddd_functions.update_date_from_account
            );

            $( document ).on( 
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
                'ajaxComplete',
                orddd_functions.update_cart_settings
            );

            jQuery( document ).on( 
                'updated_cart_totals',
                orddd_functions.update_session
            );

            jQuery( document ).on( 
                'updated_checkout',
                orddd_functions.update_zone_id
            );

            if( '1' == orddd_params.orddd_is_admin ) {
                jQuery( '#' + orddd_params.orddd_field_name ).width( "150px" );
                jQuery( '#' + orddd_params.orddd_field_name ).attr( "readonly", true );

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

        select_delivery_date: function() {
            var e_deliverydate = $( this ).val();
            if( 'select' == e_deliverydate ) {
                e_deliverydate = '';
            }
            $( this ).find('option[value="'+ e_deliverydate + '"]').prop( 'selected', true );
    
            $( "#h_deliverydate" ).val( e_deliverydate );
            localStorage.setItem( "h_deliverydate_session", jQuery( "#h_deliverydate" ).val() );
    
            show_times_for_dropdown();
        },

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
                    is_my_account: orddd_params.orddd_is_account_page,
                    action: 'orddd_update_delivery_date'
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

        select_time_slot: function() {
            var shipping_method = orddd_get_selected_shipping_method();
            jQuery( "#hidden_e_deliverydate" ).val( jQuery( '#e_deliverydate option:selected' ).text() );
            jQuery( "#hidden_h_deliverydate" ).val( jQuery( "#h_deliverydate" ).val() );
            jQuery( "#hidden_timeslot" ).val( jQuery(this).find(":selected").val() );
            jQuery( "#hidden_shipping_method" ).val( shipping_method );
            jQuery( "#hidden_shipping_class" ).val( orddd_params.orddd_shipping_class_settings_to_load );
            jQuery( "#hidden_location" ).val( jQuery( "#orddd_locations" ).find(":selected").val() );
    
            var selected_val = jQuery(this).val();
            jQuery(this).find('option[value="'+ selected_val + '"]').prop( 'selected', true );
            if ( "1" !== orddd_params.orddd_is_admin ) {
                if( 'select' == jQuery( '#e_deliverydate' ).val() ) {
                    localStorage.setItem( "e_deliverydate_session", '' );
                } else {
                    localStorage.setItem( "e_deliverydate_session", jQuery( '#e_deliverydate option:selected' ).text() );
                }
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

        select_pickup_location: function() {
            if ( 'on' == orddd_params.orddd_enable_shipping_based_delivery ) {
                var update_settings = load_delivery_date();
                if( update_settings == 'yes' && orddd_params.orddd_enable_autofill_of_delivery_date == 'on' ) {
                    orddd_autofil_date_time();
                }
            }
            var location = jQuery(this).val();
            if( null === location ) {
                location = 'select_location';
            }
            localStorage.setItem( "orddd_location_session", location );
        },

        select_shipping_method: function() {
            if( 'on' == orddd_params.orddd_enable_shipping_based_delivery ) {
                localStorage.removeItem( "orddd_storage_next_time" );
                localStorage.removeItem( "e_deliverydate_session" );
                localStorage.removeItem( "h_deliverydate_session" );
                localStorage.removeItem( "orddd_time_slot" );  
                orddd_update_delivery_session();
            }
        },

        update_session: function() {
            orddd_update_delivery_session();
        },

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

        update_zone_id: function( data ) {
            var changed_method = orddd_get_selected_shipping_method();
            if( shipping_method != changed_method ) {
                var zone_details = orddd_params.orddd_zones;
    
                for ( var zone in zone_details ) {
                    var zone_array = zone_details[zone].split("-");
    
                    if( zone_array[1] == changed_method && zone_array[0] != orddd_params.zone_id ) {
                        orddd_params.orddd_zone_id = zone_array[0];
                        orddd_params.orddd_shipping_id = zone_array[1];
    
                        jQuery( "#orddd_time_slot" ).removeAttr( "disabled", "disabled" );
    
                        load_delivery_date();
                        
                        // running the session related code only if auto-populate is not set to ON
                        // because orddd_autofil_date_time() already runs the session code too
                        if ( "on" == orddd_params.orddd_enable_autofill_of_delivery_date ) {
                            orddd_autofil_date_time();
                        } else { 
                            orddd_set_date_dropdown_from_session();
                        }
    
                        shipping_method = changed_method;
                    }
                }
            }
        },

        style_widget: function() {
            var local_storage_postcode = localStorage.getItem( "orddd_availability_postcode" );
            if( local_storage_postcode != '' && local_storage_postcode != 'undefined' && local_storage_postcode != null ) {
                jQuery( '#billing_postcode' ).val( local_storage_postcode );    
            }
        }
    }
    orddd_functions.init();

});

function show_times_for_dropdown() {
    //jQuery( "#h_deliverydate" ).val( jQuery('#e_deliverydate').val() );
    var h_deliverydate_session = localStorage.getItem( 'h_deliverydate_session' );
    let h_deliverydate = jQuery( "#h_deliverydate" ).val();

    if( h_deliverydate_session ) {
        h_deliverydate = h_deliverydate_session;
        jQuery( "#h_deliverydate" ).val(h_deliverydate_session);
       // jQuery( "#e_deliverydate" ).val(h_deliverydate_session);
    }

    if( 'select' == jQuery( "#h_deliverydate" ).val() ) {
        h_deliverydate = '';
        jQuery( "#h_deliverydate" ).val('');
    }

    if( '' != h_deliverydate ) {
        var date_dmy = h_deliverydate.split("-");
        var date_YMD = date_dmy[2] + "-" + date_dmy[1] + "-" + date_dmy[0];
    }

    var location = jQuery( "select[name=\"orddd_locations\"]" ).find(":selected").val();
    if( typeof location === "undefined" ) {
        var location = "";
    }

    var shipping_method = orddd_get_selected_shipping_method();
    if( typeof( shipping_method ) != 'undefined' && shipping_method != '' && shipping_method.indexOf( 'usps' ) !== -1 && (shipping_method.split(":").length ) < 3 ) {
        shipping_method = orddd_params.orddd_zone_id + ":" + shipping_method;
    }

    if( typeof( shipping_method ) != 'undefined' && shipping_method != '' && shipping_method.indexOf( 'wf_fedex_woocommerce_shipping' ) === -1 && shipping_method.indexOf( 'fedex' ) !== -1 && ( shipping_method.split( ":" ).length ) < 3 ) {
        shipping_method = orddd_params.orddd_zone_id + ":" + shipping_method;
    }

    // TODO: Below code can be removed as the Pickup Locations addon is no longer available now
    var pickup_location = '';
    if( typeof orddd_lpp_method_func == 'function' ) {
        pickup_location = orddd_lpp_method_func( shipping_method );    
    }
    var setting_id = 0;
    var orddd_unique_custom_settings = orddd_params.orddd_unique_custom_settings;

    if( 'global_settings' !== orddd_unique_custom_settings && '' !== orddd_unique_custom_settings ) {
        var split_str = orddd_unique_custom_settings.split('_');
        setting_id    = split_str[2];
    }
    
    if( "on" == orddd_params['custom_settings'].time_slot_enable_for_shipping_method ) {
        orddd_get_time_slot_response( h_deliverydate, date_YMD, setting_id );
    } else if( "on" == orddd_params.orddd_enable_time_slot && ( typeof( orddd_params['custom_settings'].time_slot_enable_for_shipping_method ) == 'undefined' ) ) {
        orddd_get_time_slot_response( h_deliverydate, date_YMD, '0' );    
    } else {
        jQuery( "#hidden_e_deliverydate" ).val( jQuery( '#e_deliverydate option:selected' ).text() );
        jQuery( "#hidden_h_deliverydate" ).val( h_deliverydate );
        jQuery( "#hidden_timeslot" ).val( jQuery( "#orddd_time_slot" ).val() );
        jQuery( "#hidden_location" ).val( jQuery( "#orddd_locations" ).find(":selected").val() );
        jQuery( "body" ).trigger( "update_checkout" );
        if ( 'on' == orddd_params.orddd_delivery_date_on_cart_page && '1' == orddd_params.orddd_is_cart ) {
            jQuery( "body" ).trigger( "wc_update_cart" );
        }
    }

    //localStorage.setItem( "e_deliverydate_session",jQuery('#e_deliverydate option:selected').text() );
    if( 'select' == jQuery( '#e_deliverydate' ).val() ) {
        localStorage.setItem( "e_deliverydate_session", '' );
    } else {
        localStorage.setItem( "e_deliverydate_session", jQuery( '#e_deliverydate option:selected' ).text() );
    }
    localStorage.setItem( "h_deliverydate_session",  h_deliverydate );
    if( localStorage.getItem( "orddd_time_slot" ) == null ) {
        localStorage.setItem( "orddd_time_slot", jQuery( "#orddd_time_slot" ).find( ":selected" ).val() );
    } 

    var current_date = orddd_get_current_day();
    if( typeof( current_date ) != 'undefined' && current_date != '' ) {
        var split_current_date = current_date.split( '-' );
        var ordd_next_date = new Date( split_current_date[ 2 ], ( split_current_date[ 1 ] - 1 ), split_current_date[ 0 ], orddd_params.orddd_current_hour, orddd_params.orddd_current_minute );
    } else {
        var ordd_next_date = new Date();
    }            

    ordd_next_date.setHours( ordd_next_date.getHours() + 2 );
    localStorage.setItem( "orddd_storage_next_time", ordd_next_date.getTime() );
}

function orddd_get_time_slot_response( all, date, setting_id ) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', orddd_params.orddd_rest_url + 'orddd/v1/delivery_schedule/' + setting_id + '?date=' + date );
    xhr.onload = function () {
        if (xhr.readyState === xhr.DONE) {
            if (xhr.status === 200) {
                var response = JSON.parse( xhr.responseText );
                var time_slot_str = '';
                var option_selected = orddd_params.orddd_auto_populate_first_available_time_slot;
                var time_slot_session = localStorage.getItem( "orddd_time_slot" );
                jQuery( "#orddd_time_slot" ).attr("disabled", "disabled");
                jQuery( "#orddd_time_slot_field" ).attr( "style", "opacity: 0.5" );
                time_slot_str = "select/";
                response.forEach( element => {
                    var selected = '';
                    if( '' !== time_slot_session && element.time_slot == time_slot_session ) {
                        selected = 'selected';
                    }

                    if( '' != selected ) {
                        time_slot_str = time_slot_str + element.time_slot + "_" + element.time_slot_i18n + "_" + element.charges + "_" + selected + "/";
                    } else {
                        time_slot_str = time_slot_str + element.time_slot + "_" + element.time_slot_i18n + "_" + element.charges + "/";
                    }
                    
                });
                jQuery( "#orddd_time_slot_field" ).attr( "style" ,"opacity:1" );
                if( '1' == orddd_params.orddd_is_cart ) {
                    jQuery( "#orddd_time_slot" ).attr( "style", "cursor: pointer !important;max-width:300px" );
                } else {
                    jQuery( "#orddd_time_slot" ).attr( "style", "cursor: pointer !important" );
                }
                
                jQuery( "#orddd_time_slot" ).removeAttr( "disabled" ); 
                orddd_load_time_slots( time_slot_str );

                if( option_selected == "on" || ( localStorage.getItem( "orddd_time_slot" ) != '' ) ) {
                    jQuery( "body" ).trigger( "update_checkout" );
                    if ( 'on' == orddd_params.orddd_delivery_date_on_cart_page && orddd_params.orddd_is_cart == '1') {
                        jQuery( "#hidden_e_deliverydate" ).val( jQuery( '#' + orddd_params.orddd_field_name ).val() );
                        jQuery( "#hidden_h_deliverydate" ).val( all );
                        jQuery( "#hidden_timeslot" ).val( jQuery( "#orddd_time_slot" ).find(":selected").val() );
                        jQuery( "#hidden_shipping_method" ).val( shipping_method );
                        jQuery( "#hidden_shipping_class" ).val( orddd_params.orddd_shipping_class_settings_to_load );
                        jQuery( "#hidden_location" ).val( jQuery( "#orddd_locations" ).find(":selected").val() );
                        jQuery( "body" ).trigger( "wc_update_cart" );
                    }
                }
            }
        }
    };
    xhr.send();
}

function load_dropdown_dates() {
    jQuery( "#h_deliverydate" ).val( jQuery( "e_deliverydate option:selected" ).val() );
     
    if( 'on' === orddd_params.orddd_enable_shipping_based_delivery ) {
        var custom_settings = jQuery('#orddd_unique_custom_settings').val();
        var custom_setting_id = '';
        if( 'global_settings' == custom_settings || '' == custom_settings ) {
            custom_setting_id = 0;
        } else {
            var custom_settings_arr = custom_settings.split('_');
            custom_setting_id = custom_settings_arr[2];
        }
      
        var number_of_dates = orddd_get_number_of_dates();
        var xhr = new XMLHttpRequest();
        xhr.open('GET', orddd_params.orddd_rest_url + 'orddd/v1/delivery_schedule/' + custom_setting_id + '?number_of_dates=' + number_of_dates );

        xhr.onload = function () {
            if (xhr.readyState === xhr.DONE) {
                if (xhr.status === 200) {
                    var response = JSON.parse( xhr.responseText );
                    
                    jQuery( "#e_deliverydate_field" ).attr( "style", "opacity: 1" );
                    if( jQuery( "#orddd_is_cart" ).val() == 1 ) {
                        jQuery( "#e_deliverydate" ).attr( "style", "cursor: pointer !important;max-width:300px" );
                    } else {
                        jQuery( "#e_deliverydate" ).attr( "style", "cursor: pointer !important" );
                    }
                    jQuery( "#e_deliverydate" ).removeAttr( "disabled" );

                    orddd_load_dates( response );
                }
            }
        };

        xhr.send();
    }
    show_times_for_dropdown();
}

function orddd_load_dates( response ) {
    jQuery( "#e_deliverydate" ).empty(); 
    for( key in response ) {
        jQuery( "#e_deliverydate" ).append( jQuery( "<option></option>" ).attr( "value", key ).text( response[key] ) );
    }

    var h_deliverydate_session = localStorage.getItem( 'h_deliverydate_session' );

    if( h_deliverydate_session ) {
        jQuery( "#h_deliverydate" ).val(h_deliverydate_session);
        jQuery( "#e_deliverydate" ).val(h_deliverydate_session);
    }
}

/**
 * Update the date field based on session.
 */
function orddd_set_date_dropdown_from_session() {
    var e_deliverydate_session = localStorage.getItem( 'e_deliverydate_session' ),
        h_deliverydate_session = localStorage.getItem( 'h_deliverydate_session' );
    
    var shipping_method = orddd_get_selected_shipping_method();
    if ( ! e_deliverydate_session ) {
        e_deliverydate_session = jQuery( '#e_deliverydate option[value="' + jQuery( "#h_deliverydate" ).val() + '"]' ).text();

        localStorage.setItem( "e_deliverydate_session", e_deliverydate_session );
    }

    if ( ! h_deliverydate_session ) {
        localStorage.setItem( "h_deliverydate_session", jQuery( "#h_deliverydate" ).val() );
        h_deliverydate_session = jQuery( "#h_deliverydate" ).val();
    }
    if( typeof( e_deliverydate_session ) != 'undefined' && e_deliverydate_session != '' ) {
        if ( h_deliverydate_session ) {
            var default_date_arr = h_deliverydate_session.split( '-' );
            var default_date = new Date( default_date_arr[ 1 ] + '/' + default_date_arr[ 0 ] + '/' + default_date_arr[ 2 ] );
            
            var enabled       = dwd( default_date );

            var delay_date = orddd_get_min_date();

            if( delay_date != "" && typeof( delay_date ) != 'undefined' ) {
                 var split_date = delay_date.split( "-" );
                 var delay_days = new Date ( split_date[ 1 ] + "/" + split_date[ 0 ] + "/" + split_date[ 2 ] );
            } else {
                 var delay_days = new Date();
            }

            var date_to_set = orddd_get_first_available_date( delay_date, delay_days );
            var session_date = '';
            if( undefined !== date_to_set && '' !== date_to_set ) {
                var session_date = date_to_set.getDate() + "-" + ( date_to_set.getMonth()+1 ) + "-" + date_to_set.getFullYear();
            }

            if( delay_days < default_date && enabled[0] == true ) {
                date_to_set = default_date;
            }else {
                h_deliverydate_session = session_date;
                localStorage.setItem( 'h_deliverydate_session', h_deliverydate_session );
            }

            jQuery( "#h_deliverydate" ).val( h_deliverydate_session );
            jQuery( '#e_deliverydate' ).val( h_deliverydate_session );
           
        }
    }
}
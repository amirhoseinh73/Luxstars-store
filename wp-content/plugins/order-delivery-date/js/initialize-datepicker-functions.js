/**
 * Functions to initiliaze/load the settings in the calendar on frontend.
 *
 * @namespace orddd_initialize_functions
 * @since 8.6
 */


/**
 * Handles the functionality of Delivery fields on My Account page.
 *
 * @function orddd_my_account_init
 * @memberof orddd_initialize_functions
 * @since 5.7
 */
function orddd_my_account_init() {
    load_general_settings();

    if( ( '' != orddd_account_params.shipping_method || '' != orddd_account_params.location ) && 'on' == orddd_params.orddd_enable_shipping_based_delivery ) {
        load_delivery_date();
    }
    var default_date_str = orddd_account_params.orddd_my_account_default_date;
    if( default_date_str != '' && typeof( default_date_str ) != 'undefined' ) {
        var default_date_arr = default_date_str.split( '-' );
        var default_date = new Date( default_date_arr[ 1 ] + '/' + default_date_arr[ 0 ] + '/' + default_date_arr[ 2 ] );
        var show = orddd_params.orddd_show_datepicker;
        if( 'datetimepicker' == show ) {
            jQuery( '#' + orddd_params.orddd_field_name ).datetimepicker( 'setDate', default_date );
        } else {
            jQuery( '#' + orddd_params.orddd_field_name ).datepicker( 'setDate', default_date );
        }
        jQuery( '#h_deliverydate' ).val( orddd_account_params.orddd_my_account_default_h_date );
        var default_date_inst = jQuery.datepicker._getInst( jQuery( '#' + orddd_params.orddd_field_name )[0] );
        if( 'on' == orddd_params.orddd_enable_shipping_based_delivery ) {
            show_times_custom( default_date_str, default_date_inst );
        } else {
            show_times( default_date_str, default_date_inst );
        }
    }
}

/**
 * Adds the Delivery information on Admin order page load.
 *
 * @function orddd_init
 * @memberof orddd_initialize_functions
 * @since 3.2
 */
function orddd_init() {
    load_general_settings();

    if( "auto-draft" == jQuery( "#original_post_status").val() ) {
        jQuery( '#' + orddd_params.orddd_field_name ).prop("disabled",true );
    } else {
        jQuery( '#' + orddd_params.orddd_field_name ).prop("disabled",false );
    }
    var default_date_str = orddd_admin_params.orddd_default_date;
    if( default_date_str != "" ) {
        var default_date_inst = jQuery.datepicker._getInst( jQuery( "#e_deliverydate" )[0] );
        show_admin_times( default_date_str, default_date_inst );

        if( default_date_str != '' && typeof( default_date_str ) != 'undefined' ) {
            var default_date_arr = default_date_str.split( "-" );
            var default_date = new Date( default_date_arr[ 1 ] + "/" + default_date_arr[ 0 ] + "/" + default_date_arr[ 2 ] );
        } else {
            var default_date = new Date();
        }

        var show = orddd_params.orddd_show_datepicker;
        if( 'datetimepicker' == show ) {
            // get the delivery time
            var default_datetime = orddd_admin_params.default_date_time;
            if( default_datetime != '' && typeof( default_datetime ) != 'undefined' ) {
                var time = default_datetime.split( ':' );
                // Set the Hours & minutes to be prepopulated in the time slider
                default_date.setHours( time[0] );
    			default_date.setMinutes( time[1] );
            }
            jQuery( '#' + orddd_params.orddd_field_name ).datetimepicker( "setDate", default_date );
        } else {
            jQuery( '#' + orddd_params.orddd_field_name ).datepicker( "setDate", default_date );    
        }
        
        jQuery( "#h_deliverydate" ).val( orddd_admin_params.orddd_default_h_date );
    }
    
    if( '1' !== orddd_admin_params.orddd_delivery_enabled && 'on' != orddd_admin_params.orddd_enable_delivery_date_for_category ) {
		jQuery( "#admin_delivery_fields" ).hide();;
        jQuery( "#is_virtual_product" ).html( "Delivery date settings are not enabled for the products." );                    
    }    

}

/**
 * Load the general settings in the datepicker.
 * @function load_general_settings
 */
 function load_general_settings() {
    jQuery( '#' + orddd_params.orddd_field_name ).prop( "disabled", false );    
    var option_str = get_datepicker_options();
    var show = orddd_params.orddd_show_datepicker;
    if( show == 'datetimepicker' ) {
        jQuery( '#' + orddd_params.orddd_field_name ).datetimepicker( option_str ).focus( function ( event ) {
            jQuery(this).trigger( "blur" );
            jQuery.datepicker.afterShow( event );
        });
    } else {
        jQuery( '#' + orddd_params.orddd_field_name ).datepicker( option_str ).focus( function ( event ) {
            jQuery(this).trigger( "blur" );
            jQuery.datepicker.afterShow( event );
        });
    }

    if( '1' !== orddd_params.orddd_is_admin ) {
        load_functions();
    }
}
 
/**
 * Options for JQuery Datepicker
 *
 * @function get_datepicker_options
 * @memberof orddd_initialize_functions
 * @since 1.0
 */
function get_datepicker_options() {
    var option_str = {}
    
    option_str[ 'beforeShowDay' ] = chd;
    option_str[ 'firstDay' ] = parseInt( orddd_params.orddd_start_of_week );

    if( jQuery( '#' + orddd_params.orddd_field_name ).length == 0 ) {
        if( "on" == orddd_params.orddd_same_day_delivery || "on" == orddd_params.orddd_next_day_delivery ) { 
            var avd_obj                     = maxdt();
        } else {      
            var avd_obj                     = avd();
        }

        option_str[ 'minDate' ]   = avd_obj.minDate;
        option_str[ 'maxDate' ]   = avd_obj.maxDate;
    } else {
        var show = orddd_params.orddd_show_datepicker;
        if( show == "datepicker" ){
            option_str[ "showButtonPanel" ] = true; 
            option_str[ "closeText" ] = jsL10n.clearText;
        }

        var is_inline = orddd_params.orddd_is_inline;

        option_str[ 'onClose' ] = function( dateStr, inst ) {
            if ( dateStr != "" ) {
                var monthValue = inst.selectedMonth+1;
                var dayValue = inst.selectedDay;
                var yearValue = inst.selectedYear;
                var all = dayValue + "-" + monthValue + "-" + yearValue;
             
                var hourValue = jQuery( ".ui_tpicker_time" ).html();
                jQuery( "#orddd_time_settings_selected" ).val( hourValue );
                orddd_params.orddd_time_settings_selected = hourValue;
                var event = arguments.callee.caller.caller.arguments[0];
                // If "Clear" gets clicked, then really clear it
                if( typeof( event ) !== "undefined" ) {
                    if ( jQuery( event.delegateTarget ).hasClass( "ui-datepicker-close" ) ) {
                        jQuery( this ).val( "" ); 
                        jQuery( "#h_deliverydate" ).val( "" );
                        jQuery( "#orddd_time_slot" ).prepend( "<option value=\"select\">" + jsL10n.selectText + "</option>" );
                        jQuery( "#orddd_time_slot" ).children( "option:not(:first)" ).remove();
                        jQuery( "#orddd_time_slot" ).attr( "disabled", "disabled" );
                        if( orddd_params.orddd_is_cart == 1 ) {
                            jQuery( "#orddd_time_slot" ).attr( "style", "cursor: not-allowed !important;max-width:300px" );
                        } else {
                            jQuery( "#orddd_time_slot" ).attr( "style", "cursor: not-allowed !important" );
                        }
                        jQuery( "#orddd_time_slot_field" ).css({ opacity: "0.5" });
                        jQuery( "body" ).trigger( "update_checkout" );
                        if ( 'on' == orddd_params.orddd_delivery_date_on_cart_page && '1' == orddd_params.orddd_is_cart ) {
                            jQuery( "#hidden_e_deliverydate" ).val( jQuery( '#' + orddd_params.orddd_field_name ).val() );
                            jQuery( "#hidden_h_deliverydate" ).val( all );
                            jQuery( "#hidden_timeslot" ).val( jQuery( "#orddd_time_slot" ).val() );
                            jQuery( "#hidden_location" ).val( jQuery( "#orddd_locations" ).find(":selected").val() );
                            jQuery( "body" ).trigger( "wc_update_cart" );
                        }
                    }
                } else if( jQuery( '#ui-datepicker-div' ).html().indexOf( 'ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all ui-state-hover' ) > -1 ) {
                    jQuery( "body" ).trigger( "update_checkout" );
                    if ( 'on' == orddd_params.orddd_delivery_date_on_cart_page && '1' == orddd_params.orddd_is_cart ) {
                        jQuery( "#hidden_e_deliverydate" ).val( jQuery( '#' + orddd_params.orddd_field_name ).val() );
                        jQuery( "#hidden_h_deliverydate" ).val( all );
                        jQuery( "#hidden_timeslot" ).val( jQuery( "#orddd_time_slot" ).val() );
                        jQuery( "#hidden_location" ).val( jQuery( "#orddd_locations" ).find(":selected").val() );
                        jQuery( "body" ).trigger( "wc_update_cart" );
                    }
                }
            }
            jQuery( '#' + orddd_params.orddd_field_name ).blur();
        };

        if ( "1" == orddd_params.orddd_is_admin ) {
            option_str[ 'onSelect' ] = show_admin_times;
        } else {
            if( "on" == orddd_params.orddd_enable_shipping_based_delivery ) {
                option_str[ 'onSelect' ] = show_times_custom;    
            } else {
                option_str[ 'onSelect' ] = show_times;    
            }
        }

        var options = orddd_params.orddd_option_str;
        var before_df_arr = options.split( '&' );
        jQuery.each( before_df_arr, function( key, value ) {
            if( '' != value && 'undefined' != typeof( value ) ) {
                var split_value = value.split( ":" );
                if( split_value.length != '2' ) {
                    var str = split_value[1] + ":" + split_value[2];
                    option_str[ split_value[0] ] = str.trim().replace( /'/g, "" );
                } else if( 'hourMax' == split_value[0] || 'minuteMax' == split_value[0] || 'hourMin' == split_value[0] || 'stepMinute' == split_value[0] ) {
                    option_str[ split_value[0] ] = parseInt( split_value[1].trim() );  
                } else if ( 'minuteMin' == split_value[0] ) {
                    return;
                } else if( 'beforeShow' == split_value[0] ) {
                    if( "on" == orddd_params.orddd_same_day_delivery || "on" == orddd_params.orddd_next_day_delivery ) { 
                        if( '1' == is_inline ) {
                            var avd_obj = maxdt();   
                            option_str[ split_value[0] ] = avd_obj;      
                            option_str['minDate'] 	     = avd_obj.minDate;
                            option_str['maxDate'] 	     = avd_obj.maxDate;
                            option_str['numberOfMonths'] = avd_obj.numberOfMonths;
                        } else {
                            option_str[ split_value[0] ] = maxdt;   
                        }      
                    } else {      
                        if( '1' == is_inline ) {
                            var avd_obj = avd();  
                            option_str[ split_value[0] ] = avd_obj;      
                            option_str['minDate'] 	     = avd_obj.minDate;
                            option_str['maxDate'] 	     = avd_obj.maxDate;
                            option_str['numberOfMonths'] = avd_obj.numberOfMonths;
                        } else {
                            option_str[ split_value[0] ] = avd;   
                        }     
                    }  
                } else {
                     option_str[ split_value[0] ] = split_value[1].trim().replace( /'/g, "" );    
                }    
            }
        });
    }

    return option_str;
}

/**
 * Loads the hidden variables. 
 *
 * @function load_hidden_vars
 * @memberof orddd_initialize_functions
 * @since 1.0
 */
function load_hidden_vars( value, load_common_settings ) {

    //As IE does not support default parameters, check if its undefined and set to blank
    if(load_common_settings == undefined ) {
        load_common_settings = '';
    }
    
    orddd_params['custom_settings'] = {};
    jQuery.each( value, function( pkey, pvalue ) { 
        //jQuery( "<input>" ).attr({id: pkey, name: pkey, type: "hidden", value: pvalue }).appendTo( "#orddd_dynamic_hidden_vars" );
        orddd_params['custom_settings'][pkey] = pvalue;
    });

    if( 'yes' == orddd_params.orddd_categories_settings_common ) {
        if( typeof( orddd_params.orddd_common_delivery_dates_for_product_category ) !== "undefined" && orddd_params.orddd_common_delivery_dates_for_product_category != '' ) {
            var specific_dates = eval( '[' + orddd_params.orddd_common_delivery_dates_for_product_category + ']' );
            var disabled_common_days = eval( '[' + orddd_params.orddd_common_holidays_for_product_category + ']' );
            
            var specific_dates_str = "";
            for( j = 0 ; j <= specific_dates.length; j++ ) {
                if( typeof( specific_dates[j] ) != 'undefined' ) {
                    if( jQuery.inArray( specific_dates[j], disabled_common_days ) !== -1 ) {
                        delete specific_dates[j];
                    } else {
                        specific_dates_str += '"' + specific_dates[j] +  '",';
                    }    
                }
            }
            
            specific_dates_str = specific_dates_str.substring( 0, specific_dates_str.length - 1);

            orddd_params['custom_settings'].orddd_delivery_dates = specific_dates_str;
            if( typeof orddd_params['custom_settings'].orddd_specific_delivery_dates != 'undefined' ) {
                orddd_params['custom_settings'].orddd_specific_delivery_dates = 'on';
            } else {
                orddd_params['custom_settings']['orddd_specific_delivery_dates'] = 'on';

            }
        } else {
            orddd_params['custom_settings'].orddd_delivery_dates = "";
        }

        if( load_common_settings == 'yes' ) {
            // Assign Common delivery days, dates, lockout days and also holidays to the checkout calendar
            // when settings for product categories as well as shipping classes are added and multiple products
            // are added to the cart. 
            var common_delivery_days = [];
            if( typeof( orddd_params.orddd_common_delivery_days_for_product_category ) !== "undefined" && orddd_params.orddd_common_delivery_days_for_product_category != '' ) {
                common_delivery_days = orddd_params.orddd_common_delivery_days_for_product_category;
                common_delivery_days = jQuery.parseJSON( common_delivery_days || '{}' );
            } 
    		
    		if( common_delivery_days.length == 0 ) {
    			if( typeof orddd_params['custom_settings'].orddd_specific_delivery_dates != 'undefined' ) {
                    orddd_params['custom_settings'].orddd_specific_delivery_dates = "on";
                } else {
                    orddd_params['custom_settings']['orddd_specific_delivery_dates'] = 'on';
                }
    		}
    		
            for( i=0; i<7; i++ ) {
                if ( typeof( common_delivery_days[ "orddd_weekday_" + i ] ) !== "undefined" ) {
                    orddd_params['custom_settings']["orddd_weekday_" + i] = 'checked';
                } else {
                    orddd_params['custom_settings']["orddd_weekday_" + i] = '';
                }    
            }

            if( typeof( orddd_params.orddd_common_locked_days ) !== "undefined" && orddd_params.orddd_common_locked_days != '' ) {
                orddd_params['custom_settings'].orddd_lockout_days = orddd_params.orddd_common_locked_days;
            }

            if( typeof( orddd_params.orddd_common_holidays_for_product_category ) !== "undefined" && 
            orddd_params.orddd_common_holidays_for_product_category != '' ) {
                orddd_params['custom_settings'].orddd_delivery_date_holidays = orddd_params.orddd_common_holidays_for_product_category;
            }

            if( common_delivery_days.length == 0 && ( orddd_params['custom_settings'].orddd_delivery_dates == '' || typeof orddd_params['custom_settings'].orddd_delivery_dates == 'undefined' ) ) {
                if ( typeof orddd_params['custom_settings'].orddd_common_days_disabled == 'undefined' ) {
                    orddd_params['custom_settings'].orddd_common_days_disabled = "yes";
                } else {
                    orddd_params['custom_settings']['orddd_common_days_disabled'] = "yes";
                    
                }
            }
        }
    }
}

/**
 * Loads the removed global weekday hidden fields
 *
 *
 * @function load_weekday_vars
 * @memberof load_delivery_date
 *
 * @since 7.8
 */

 function load_weekday_vars( vars ) {
    jQuery.each( vars, function( pkey, pvalue ) { 
        if( typeof jQuery( "#" + pkey ).val() === 'undefined' && typeof pvalue != "undefined" ) {        
            //jQuery( "<input>" ).attr({id: pkey, name: pkey, type: "hidden", value: pvalue }).insertAfter( "#h_deliverydate" );
            orddd_params[pkey] = pvalue;
        }
    });
 }

/**
 * Loads the Custom Date settings on the Delivery Date field.
 *
 * @function load_delivery_date
 * @memberof orddd_initialize_functions
 * @returns {string} update_settings
 * @since 3.0
 */
function load_delivery_date() {
    var string                  = "", enable_delivery_date = "";  
    var i                       = 0;
    var shipping_method         = orddd_get_selected_shipping_method();
    var update_settings         = 'no';
    var unique_custom_setting   = orddd_params.orddd_unique_custom_settings;
    var custom_settings         = orddd_get_applied_custom_settings();
    var custom_settings_to_load = custom_settings.custom_settings_to_load;
    var load_common_settings    = custom_settings.load_common_settings;
    
    if( '1' == orddd_params.orddd_is_cart ) {
        var timeslot_custom_css = "cursor: pointer !important;max-width:300px";
    } else {
        var timeslot_custom_css = "cursor: pointer !important";
    }
     
    var is_text_block = false;
    if( jQuery.isEmptyObject( custom_settings_to_load ) == false ) {
        var hidden_obj = custom_settings_to_load.hidden_vars;
        var hidden_vars = jQuery.parseJSON( hidden_obj || '{}' );
        if( hidden_vars == null ) {
            hidden_vars = [];
        }

        jQuery( "#orddd_dynamic_hidden_vars" ).empty();
        load_hidden_vars( hidden_vars, load_common_settings );

        var current_unique_setting_key = custom_settings_to_load.unique_settings_key;
        if( typeof( current_unique_setting_key  ) !== 'undefined' && unique_custom_setting != current_unique_setting_key ) {
            update_settings = 'yes';
            jQuery( "#orddd_unique_custom_settings" ).val( current_unique_setting_key );
            orddd_params.orddd_unique_custom_settings = current_unique_setting_key;
            if( jQuery( '#' + orddd_params.orddd_field_name ).length == 0 ) {
                jQuery( ".availability_calendar" ).datepicker( "destroy" );
                var specific_dates     = orddd_params.orddd_specific_delivery_dates;
                var recurring_weekdays = orddd_params.orddd_recurring_days;
                if( specific_dates == "on" && ( recurring_weekdays == "" || recurring_weekdays == "on" && 'yes' == orddd_params['custom_settings'].orddd_is_all_weekdays_disabled ) )  {                             
                    for( i = 0; i < 7; i++ ) {
                        jQuery( "#orddd_weekday_" + i ).remove();
                    }
                }
            } else {
                if ( "1" == orddd_params.orddd_is_admin ) {
                    jQuery( "#admin_time_slot_field" ).remove();
                    jQuery( "#admin_delivery_date_field" ).remove();
                } else {
                    jQuery( "#e_deliverydate_field label[ for=\"e_deliverydate\" ] abbr" ).remove();
                    jQuery( "#e_deliverydate_field" ).fadeOut();
                    jQuery( "#orddd_datepicker" ).fadeOut();
                    jQuery( "#orddd_time_slot_field" ).fadeOut();
                }

                jQuery( "#h_deliverydate" ).val( "" );
                jQuery( "#e_deliverydate" ).val( "" );
                jQuery( '#' + orddd_params.orddd_field_name ).datepicker( "destroy" );
                if( typeof jQuery.fn.datetimepicker !== "undefined" && 
                    'datetimepicker' == orddd_params.orddd_show_datepicker ) {
                    jQuery( '#' + orddd_params.orddd_field_name ).datetimepicker( "destroy" );
                }

                if( jQuery( '#orddd_time_slot' ).parent().hasClass( 'fl-wrap' ) ) {
                    jQuery( "#orddd_time_slot_field .fl-wrap" ).empty();
                } else {
                    jQuery( "#orddd_time_slot_field" ).empty();    
                }

                jQuery( ".orddd_text_block" ).hide();
                jQuery( "#orddd_estimated_shipping_date" ).val( "" );
                
                enable_delivery_date = custom_settings_to_load.enable_delivery_date;

                orddd_params['custom_settings'].orddd_enable_shipping_delivery_date = custom_settings_to_load.enable_delivery_date;
                if( enable_delivery_date == "on" ) {
                    if( 'delivery_calendar' == custom_settings_to_load.orddd_delivery_checkout_options ) {
                        orddd_params['custom_settings'].orddd_delivery_checkout_options = 'delivery_calendar';
                        is_text_block = false;
                        if ( "1" == orddd_params.orddd_is_admin ) {
                            jQuery( "#admin_delivery_fields tr:first" ).before( "<tr id=\"admin_delivery_date_field\" ><td><label class =\"orddd_delivery_date_field_label\"> " + orddd_admin_params.orddd_field_name_admin + "</label></td><td><input type=\"text\" id=\"e_deliverydate\" name=\"e_deliverydate\" class=\"e_deliverydate\" style='width:164px' /><input type=\"hidden\" id=\"h_deliverydate\" name=\"h_deliverydate\" /></td></tr>");
                            jQuery( "#admin_delivery_fields tr:first" ).after( "<tr id=\"admin_time_slot_field\"><td>" + orddd_admin_params.orddd_time_field_name_admin + "</td><td><select name=\"orddd_time_slot\" id=\"orddd_time_slot\" class=\"orddd_admin_time_slot\" disabled=\"disabled\" placeholder=\"\"><option value=\"select\">" + jsL10n.selectText + "</option></select></td></tr>");

                        } else {    
                            jQuery( "#e_deliverydate_field" ).fadeIn();
                            jQuery( "#orddd_datepicker" ).fadeIn();
                            jQuery( "#orddd_time_slot_field" ).fadeIn();
                        }
                        
                        if( "1" !=  orddd_params.orddd_is_admin ) {
                            if( '' != custom_settings_to_load.orddd_date_field_label ) {
                                jQuery( "#e_deliverydate_field label[for=\"e_deliverydate\"]" ).html( custom_settings_to_load.orddd_date_field_label );    
                            } else {
                                jQuery( "#e_deliverydate_field label[for=\"e_deliverydate\"]" ).html( orddd_params.orddd_field_label );
                            }
                            
                            var date_field_mandatory = custom_settings_to_load.date_field_mandatory;
                            if( date_field_mandatory == "checked" ) {
                                jQuery( "#e_deliverydate_field label[for=\"e_deliverydate\"]").append( "<abbr class=\"required\" title=\"required\">*</abbr>" );
                                orddd_params['custom_settings'].date_mandatory_for_shipping_method = 'checked';
                                jQuery( "#e_deliverydate_field" ).attr( "class", "form-row form-row-wide validate-required" );
                            } else {
                                jQuery( "#e_deliverydate_field label[for=\"e_deliverydate\"] abbr" ).remove();
                                orddd_params['custom_settings'].date_mandatory_for_shipping_method = '';
                                jQuery( "#e_deliverydate_field" ).attr( "class", "form-row form-row-wide" );
                            }
                        } else {
                            var date_field_mandatory = custom_settings_to_load.date_field_mandatory;
                            if( date_field_mandatory == "checked" ) {
                                jQuery( "<input>" ).attr({id: "date_mandatory_for_shipping_method", name: "date_mandatory_for_shipping_method", type: "hidden", value: "checked"}).appendTo( "#orddd_dynamic_hidden_vars" );
                                orddd_params['custom_settings'].date_mandatory_for_shipping_method = 'checked';
                            } else {
                                jQuery( "<input>" ).attr({id: "date_mandatory_for_shipping_method", name: "date_mandatory_for_shipping_method", type: "hidden", value: ""}).appendTo( "#orddd_dynamic_hidden_vars" );
                                orddd_params['custom_settings'].date_mandatory_for_shipping_method = '';
                            }
                        }
                            
                        if ( custom_settings_to_load.time_settings != "" ) {
                            string = custom_settings_to_load.time_settings;
                            orddd_params['custom_settings'].time_setting_enable_for_shipping_method = 'on';
                            jQuery( '#time_setting_enable_for_shipping_method' ).val( 'on' );
                        } else {
                            string = "off";
                            orddd_params['custom_settings'].time_setting_enable_for_shipping_method = 'off';
                            jQuery( '#time_setting_enable_for_shipping_method' ).val( 'off' );
                        }
                        
                        if( "1" !=  orddd_params.orddd_is_admin ) {
                            if ( custom_settings_to_load.time_slots == "on" ) {
                                var time_slot_field_mandatory = custom_settings_to_load.timeslot_field_mandatory;
                                if( '' != custom_settings_to_load.orddd_time_field_label ) {
                                    var orddd_time_field_label = custom_settings_to_load.orddd_time_field_label;
                                } else {
                                    var orddd_time_field_label = orddd_params.orddd_timeslot_field_label;
                                }

                                if( time_slot_field_mandatory == "checked" ) {
                                    if( jQuery( "#orddd_time_slot_field" ).children().children().hasClass( 'fl-wrap' ) ) {
                                        jQuery( "#orddd_time_slot_field" ).children().append( "<label for=\"orddd_time_slot\" class=\"\">" + orddd_time_field_label + "<abbr class=\"required\" title=\"required\">*</abbr></label><select name=\"orddd_time_slot\" id=\"orddd_time_slot\" class=\"orddd_custom_time_slot_mandatory\" disabled=\"disabled\" style=\"" + timeslot_custom_css + "\" placeholder=\"\"><option value=\"select\">" + jsL10n.selectText + "</option></select>" );
                                    } else {
                                        jQuery( "#orddd_time_slot_field" ).append( "<label for=\"orddd_time_slot\" class=\"\">" + orddd_time_field_label + "<abbr class=\"required\" title=\"required\">*</abbr></label><select name=\"orddd_time_slot\" id=\"orddd_time_slot\" class=\"orddd_custom_time_slot_mandatory\" disabled=\"disabled\" style=\"" + timeslot_custom_css + "\" placeholder=\"\"><option value=\"select\">" + jsL10n.selectText + "</option></select>" );    
                                    }
                                   
                                    orddd_params['custom_settings'].time_slot_mandatory_for_shipping_method = 'checked';

                                    jQuery( "#orddd_time_slot_field" ).attr( "class", "form-row form-row-wide validate-required" );
                                    jQuery( "#orddd_time_slot_field" ).attr( "style", "opacity: 0.5;" );
                                } else {
                                    if( jQuery( "#orddd_time_slot_field" ).children().children().hasClass( 'fl-wrap' ) ) {
                                        jQuery( "#orddd_time_slot_field" ).children().children().append( "<label for=\"orddd_time_slot\" class=\"\">" + orddd_time_field_label + "</label><select name=\"orddd_time_slot\" id=\"orddd_time_slot\" class=\"orddd_custom_time_slot_mandatory\" disabled=\"disabled\" style=\"" + timeslot_custom_css + "\" placeholder=\"\"><option value=\"select\">" + jsL10n.selectText + "</option></select>" );                                        
                                    } else {
                                        jQuery( "#orddd_time_slot_field" ).append( "<label for=\"orddd_time_slot\" class=\"\">" + orddd_time_field_label + "</label><select name=\"orddd_time_slot\" id=\"orddd_time_slot\" class=\"orddd_custom_time_slot_mandatory\" disabled=\"disabled\" style=\"" + timeslot_custom_css + "\" placeholder=\"\"><option value=\"select\">" + jsL10n.selectText + "</option></select>" );
                                    }
                                    orddd_params['custom_settings'].time_slot_mandatory_for_shipping_method = '';

                                    jQuery( "#orddd_time_slot_field" ).attr( "class", "form-row form-row-wide" );
                                    jQuery( "#orddd_time_slot_field" ).attr( "style", "opacity: 0.5;" );
                                }
                                jQuery( "#orddd_time_slot" ).select2();
                                orddd_params['custom_settings'].time_slot_enable_for_shipping_method = 'on';

                            } else {
                                if( jQuery( '#orddd_time_slot' ).parent().hasClass('fl-wrap') ) {
                                    jQuery( "#orddd_time_slot_field .fl-wrap" ).empty();
                                } else {
                                    jQuery( "#orddd_time_slot_field" ).empty();    
                                }
                                orddd_params['custom_settings'].time_slot_enable_for_shipping_method = 'off';
                            }
                        } else {
                            if ( custom_settings_to_load.time_slots == "on" ) {
                                var time_slot_field_mandatory = custom_settings_to_load.timeslot_field_mandatory;
                                if( time_slot_field_mandatory == "checked" ) {
                                    orddd_params['custom_settings'].time_slot_mandatory_for_shipping_method = 'checked';

                                } else {
                                    orddd_params['custom_settings'].time_slot_mandatory_for_shipping_method = '';

                                }
                                orddd_params['custom_settings'].time_slot_enable_for_shipping_method = 'on';

                            } else {
                                jQuery( "#admin_time_slot_field" ).remove();
                            }
                        }
                        
                        var specific_dates = orddd_params.orddd_specific_delivery_dates;
                        var recurring_weekdays = orddd_params.orddd_recurring_days;
                        if( specific_dates == "on" && ( recurring_weekdays == "" || recurring_weekdays == "on" && 'yes' == orddd_params['custom_settings'].orddd_is_all_weekdays_disabled ) )  {                             
                            for( i = 0; i < 7; i++ ) {
                                jQuery( "#orddd_weekday_" + i ).remove();
                            }
                        }
                        orddd_params.orddd_is_shipping_text_block = 'no';
                        jQuery( ".orddd_text_block" ).hide();
                        jQuery( "#orddd_estimated_shipping_date" ).val( "" );
                    } else if( 'text_block' == custom_settings_to_load.orddd_delivery_checkout_options ) {
                        orddd_params['custom_settings'].orddd_delivery_checkout_options = 'text_block';

                        is_text_block = true;

                        jQuery( "#e_deliverydate_field" ).fadeOut();
                        jQuery( "#orddd_datepicker" ).fadeOut();
                        jQuery( "#e_deliverydate" ).val( "" );
                        jQuery( "#h_deliverydate" ).val( "" );
                        jQuery( "#e_deliverydate_field label[for=\"e_deliverydate\"] abbr" ).remove();
                        orddd_params['custom_settings'].date_mandatory_for_shipping_method = '';
                        jQuery( "#torddd_time_slot_field" ).fadeOut();
                       
                        orddd_params['custom_settings'].time_slot_mandatory_for_shipping_method = '';
                        orddd_params['custom_settings'].time_slot_enable_for_shipping_method = 'off';

                        orddd_params.orddd_is_shipping_text_block = 'yes';
                        jQuery( ".orddd_text_block" ).show();
                        var shipping_date = orddd_get_text_block_shipping_date( custom_settings_to_load.orddd_minimum_delivery_time );
                        var orddd_between_range = custom_settings_to_load.orddd_min_between_days + "-" + custom_settings_to_load.orddd_max_between_days;
                        jQuery( "#orddd_min_range" ).html( custom_settings_to_load.orddd_min_between_days );
                        jQuery( "#orddd_max_range" ).html( custom_settings_to_load.orddd_max_between_days );
                        jQuery( "#shipping_date" ).html( shipping_date[ 'shipping_date' ] );
                        jQuery( "#orddd_estimated_shipping_date" ).val( shipping_date[ 'hidden_shipping_date' ] );
                    }
                } else {
                    if( "1" !=  orddd_params.orddd_is_admin ) {
                        jQuery( "#e_deliverydate_field" ).fadeOut();
                        jQuery( "#orddd_datepicker" ).fadeOut();
                        jQuery( "#e_deliverydate" ).val( "" );
                        jQuery( "#h_deliverydate" ).val( "" );
                        jQuery( "#e_deliverydate_field label[for=\"e_deliverydate\"] abbr" ).remove();
                      
                        orddd_params['custom_settings'].date_mandatory_for_shipping_method = '';

                        jQuery( "#orddd_time_slot_field" ).fadeOut();
                        orddd_params['custom_settings'].time_slot_mandatory_for_shipping_method = '';
                        orddd_params['custom_settings'].time_slot_enable_for_shipping_method = 'off';

                        jQuery( ".orddd_text_block" ).hide();
                        jQuery( "#orddd_estimated_shipping_date" ).val( "" );
                    } else {
                        jQuery( "#admin_delivery_fields" ).empty();
                        jQuery( "#is_virtual_product" ).html( "Delivery is not available for the shipping method." )
                    }
                }
            }
        } else {
            enable_delivery_date = custom_settings_to_load.enable_delivery_date;
            orddd_params['custom_settings'].orddd_enable_shipping_delivery_date = custom_settings_to_load.enable_delivery_date;
            
            if( enable_delivery_date == "on" ) {
                if( 'delivery_calendar' == custom_settings_to_load.orddd_delivery_checkout_options ) {
                    orddd_params['custom_settings'].orddd_delivery_checkout_options = 'delivery_calendar';

                    is_text_block = false;

                    if( "1" != orddd_params.orddd_is_admin ) {
                        var date_field_mandatory = custom_settings_to_load.date_field_mandatory;
                        if( date_field_mandatory == "checked" ) {
                            orddd_params['custom_settings'].date_mandatory_for_shipping_method = 'checked';

                        } else {
                            orddd_params['custom_settings'].date_mandatory_for_shipping_method = '';

                        }
                    } else {
                        var date_field_mandatory = custom_settings_to_load.date_field_mandatory;
                        if( date_field_mandatory == "checked" ) {
                            orddd_params['custom_settings'].date_mandatory_for_shipping_method = 'checked';

                        } else {
                            orddd_params['custom_settings'].date_mandatory_for_shipping_method = '';

                        }
                    }
                        
                    if ( custom_settings_to_load.time_settings != "" ) {
                        string = custom_settings_to_load.time_settings;
                        orddd_params['custom_settings'].time_setting_enable_for_shipping_method = 'on';
                        jQuery( '#time_setting_enable_for_shipping_method' ).val( 'on' );
                    } else {
                        string = "off";
                        orddd_params['custom_settings'].time_setting_enable_for_shipping_method = 'off';
                        jQuery( '#time_setting_enable_for_shipping_method' ).val( 'on' );
                    }
                    
                    if( "1" != orddd_params.orddd_is_admin ) {
                        if ( custom_settings_to_load.time_slots == "on" ) {
                            var time_slot_field_mandatory = custom_settings_to_load.timeslot_field_mandatory;
                            if( time_slot_field_mandatory == "checked" ) {
                                orddd_params['custom_settings'].time_slot_mandatory_for_shipping_method = 'checked';

                            } else {
                                orddd_params['custom_settings'].time_slot_mandatory_for_shipping_method = '';
                            }
                            orddd_params['custom_settings'].time_slot_enable_for_shipping_method = 'on';

                        } else {
                            orddd_params['custom_settings'].time_slot_enable_for_shipping_method = 'off';
                        }
                    } else {
                        if ( custom_settings_to_load.time_slots == "on" ) {
                            var time_slot_field_mandatory = custom_settings_to_load.timeslot_field_mandatory;
                            if( time_slot_field_mandatory == "checked" ) {
                                orddd_params['custom_settings'].time_slot_mandatory_for_shipping_method = 'checked';
                            } else {
                                orddd_params['custom_settings'].time_slot_mandatory_for_shipping_method = '';
                            }
                            orddd_params['custom_settings'].time_slot_enable_for_shipping_method = 'on';
                        }
                    }
                } else if( 'text_block' == custom_settings_to_load.orddd_delivery_checkout_options ) {
                    orddd_params['custom_settings'].orddd_delivery_checkout_options = 'text_block';

                    // Empty the delivery date field value and h_deliverydate hidden field value when 
                    // text block option is selected for the custome delivery setting.
                    // This fields has value when the Auto populate delivery date is enabled. 
                    is_text_block = true;

                    jQuery( "#e_deliverydate_field" ).fadeOut();
                    jQuery( "#orddd_datepicker" ).fadeOut();

                    jQuery( "#e_deliverydate" ).val( "" );
                    jQuery( "#h_deliverydate" ).val( "" );

                    jQuery( "#e_deliverydate_field label[for=\"e_deliverydate\"] abbr" ).remove();
                    orddd_params['custom_settings'].date_mandatory_for_shipping_method = '';

                    jQuery( "#torddd_time_slot_field" ).fadeOut();
                    orddd_params['custom_settings'].time_slot_mandatory_for_shipping_method = '';
                    orddd_params['custom_settings'].time_slot_enable_for_shipping_method = 'off';
                    orddd_params.orddd_is_shipping_text_block = 'yes';

                    jQuery( ".orddd_text_block" ).show();
                    var shipping_date = orddd_get_text_block_shipping_date( custom_settings_to_load.orddd_minimum_delivery_time );
                    var orddd_between_range = custom_settings_to_load.orddd_min_between_days + "-" +custom_settings_to_load.orddd_max_between_days;
                    jQuery( "#orddd_min_range" ).html( custom_settings_to_load.orddd_min_between_days );
                    jQuery( "#orddd_max_range" ).html( custom_settings_to_load.orddd_max_between_days );
                    jQuery( "#shipping_date" ).html( shipping_date[ 'shipping_date' ] );
                    jQuery( "#orddd_estimated_shipping_date" ).val( shipping_date[ 'hidden_shipping_date' ] );
                }
            } else {
                if( "1" != orddd_params.orddd_is_admin ) {
                    orddd_params['custom_settings'].date_mandatory_for_shipping_method = '';
                    orddd_params['custom_settings'].time_slot_mandatory_for_shipping_method = '';
                    orddd_params['custom_settings'].time_slot_enable_for_shipping_method = 'off';
                }
            }
        }
    } else {
        if( unique_custom_setting != "global_settings" ) {
            var enabled_weekdays = orddd_params.orddd_load_delivery_date_var;
            var hidden_enabled_weekdays_var = jQuery.parseJSON( enabled_weekdays );
            if( hidden_enabled_weekdays_var == null ) {
                hidden_enabled_weekdays_var = [];
            }
                
            load_weekday_vars( hidden_enabled_weekdays_var );

            update_settings = 'yes';
            jQuery( "#orddd_unique_custom_settings" ).val( "global_settings" );
            orddd_params.orddd_unique_custom_settings = 'global_settings';
            orddd_params['custom_settings'] = {};

            if( 'orddd_datepicker' !== orddd_params.orddd_field_name && jQuery( '#' + orddd_params.orddd_field_name ).length == 0 ) {
                jQuery( "#orddd_dynamic_hidden_vars" ).empty();
                jQuery( ".availability_calendar" ).datepicker( "destroy" );
            } else {
                if ( "1" == orddd_params.orddd_is_admin ) {
                    jQuery( "#admin_time_slot_field" ).remove();
                    jQuery( "#admin_delivery_date_field" ).remove();
                } else {
                    jQuery( "#e_deliverydate_field label[ for=\"e_deliverydate\" ] abbr" ).remove();
                    jQuery( "#e_deliverydate_field" ).fadeOut();
                    jQuery( "#orddd_datepicker" ).fadeOut();
                    jQuery( "#orddd_time_slot_field" ).fadeOut();
                    if( jQuery( '#torddd_time_slot' ).parent().hasClass( 'fl-wrap' ) ) {
                        jQuery( "#orddd_time_slot_field .fl-wrap" ).empty();
                    } else {
                        jQuery( "#orddd_time_slot_field" ).empty();    
                    }
                }

                jQuery( "#h_deliverydate" ).val( "" );
                jQuery( "#e_deliverydate" ).val( "" );
                jQuery( '#' + orddd_params.orddd_field_name ).datepicker( "destroy" );

                // TODO: Need to change this code for Bakery theme
                // as when using Bakery theme, it gives an error on Checkout page
                // and adding this condition below
                //if( typeof( jQuery( "#e_deliverydate" ).datetimepicker() ) !== "undefined" && 
                // fixes the error on Checkout page for Bakery theme, but it creates errors 
                // for Pickup Location & when editing on My Account page
                // For My Account page: if( '1' == jQuery( "#orddd_is_account_page" ).val() ) {
                if ( typeof jQuery.fn.datetimepicker !== "undefined" && 
                     'datetimepicker' == orddd_params.orddd_show_datepicker )
                {
                    jQuery( '#' + orddd_params.orddd_field_name ).datetimepicker( "destroy" );
                }

                jQuery( "#e_deliverydate_field label[ for=\"e_deliverydate\" ] abbr" ).remove();
                if( 'delivery_calendar' == orddd_params.orddd_delivery_checkout_options ) {
                    is_text_block = false;

                    if( "1" != orddd_params.orddd_is_admin ) {
                        jQuery( "#e_deliverydate_field" ).fadeIn();
                        jQuery( "#orddd_datepicker" ).fadeIn();
                        jQuery( "#orddd_time_slot_field" ).fadeIn();
                    } else {
                        if( jQuery( "#admin_delivery_date_field" ).length == 0 ) { 
                            jQuery( "#admin_delivery_fields tr:first" ).before( "<tr id=\"admin_delivery_date_field\" ><td><label class =\"orddd_delivery_date_field_label\">" + orddd_admin_params.orddd_field_name_admin + ": </label></td><td><input type=\"text\" id=\"e_deliverydate\" name=\"e_deliverydate\" class=\"e_deliverydate\" /><input type=\"hidden\" id=\"h_deliverydate\" name=\"h_deliverydate\" /></td></tr>");
                        }
                     // Time slot field is not present and the order uses a time slot, then display the field
                        var fixed_time = 'off';
                        if( jQuery( '#orddd_fixed_time' ).length > 0 ) {
                            fixed_time = jQuery( '#orddd_fixed_time' ).val();
                        }
                        if( jQuery( "#admin_time_slot_field" ).length == 0 && fixed_time != 'on' ) { 
                            jQuery( "#admin_delivery_fields tr:first" ).after( "<tr id=\"admin_time_slot_field\"><td>" + orddd_admin_params.orddd_time_field_name_admin + "</td><td><select name=\"orddd_time_slot\" id=\"orddd_time_slot\" class=\"orddd_admin_time_slot\" disabled=\"disabled\" placeholder=\"\"><option value=\"select\">" + jsL10n.selectText + "</option></select></td></tr>");

                        }
                        if( jQuery( "#save_delivery_date_button" ).length == 0 ) {
                            jQuery( "#admin_delivery_fields tr:second" ).after( "<tr id=\"save_delivery_date_button\"><td><input type=\"button\" value=\"Update\" id=\"save_delivery_date\" class=\"save_button\"></td></tr>" );
                        }
                    }
                    jQuery( "#e_deliverydate" ).val( "" );
                    jQuery( "#orddd_dynamic_hidden_vars" ).empty();

                    jQuery( "#e_deliverydate_field label[for=\"e_deliverydate\"]" ).html( orddd_params.orddd_field_label );
                    var time_slot_enabled = orddd_params.orddd_enable_time_slot;
                    if( "1" != orddd_params.orddd_is_admin ) {
                        if( ( jQuery( "#orddd_time_slot_field" ).is(":empty") || jQuery( "#orddd_time_slot_field" ).children().children().hasClass( 'fl-wrap' ) ) && time_slot_enabled == "on" ) {
                            var time_slot_field_mandatory = orddd_params.orddd_timeslot_field_mandatory;
                            if( time_slot_field_mandatory == "checked" ) {
                                if( jQuery( "#orddd_time_slot_field" ).children().children().hasClass( 'fl-wrap' ) ) {
                                    jQuery( "#orddd_time_slot_field" ).children().children().append( "<label for=\"orddd_time_slot\" class=\"\">" + orddd_params.orddd_timeslot_field_label + "<abbr class=\"required\" title=\"required\">*</abbr></label><select name=\"orddd_time_slot\" id=\"orddd_time_slot\" class=\"orddd_custom_time_slot_mandatory\" disabled=\"disabled\" style=\"" + timeslot_custom_css + "\" placeholder=\"\"><option value=\"select\">" + jsL10n.selectText + "</option></select>" );
                                } else {
                                    jQuery( "#orddd_time_slot_field" ).append( "<label for=\"orddd_time_slot\" class=\"\">" + orddd_params.orddd_timeslot_field_label + "<abbr class=\"required\" title=\"required\">*</abbr></label><select name=\"orddd_time_slot\" id=\"orddd_time_slot\" class=\"orddd_custom_time_slot_mandatory\" disabled=\"disabled\" style=\"" + timeslot_custom_css + "\" placeholder=\"\"><option value=\"select\">" + jsL10n.selectText + "</option></select>" );
                                }
                                
                                orddd_params['custom_settings'].time_slot_mandatory_for_shipping_method = 'checked';
                                jQuery( "#orddd_time_slot_field" ).attr( "class", "form-row form-row-wide validate-required" );
                                jQuery( "#orddd_time_slot_field" ).attr( "style", "opacity: 0.5;" );                               
                            } else {
                                if( jQuery( "#orddd_time_slot_field" ).children().children().hasClass( 'fl-wrap' ) ) {
                                    jQuery( "#orddd_time_slot_field" ).children().children().append( "<label for=\"orddd_time_slot\" class=\"\">" + orddd_params.orddd_timeslot_field_label + "</label><select name=\"orddd_time_slot\" id=\"orddd_time_slot\" class=\"orddd_custom_time_slot_mandatory\" disabled=\"disabled\" style=\"" + timeslot_custom_css + "\" placeholder=\"\"><option value=\"select\">" + jsL10n.selectText + "</option></select>" );
                                } else {
                                    jQuery( "#orddd_time_slot_field" ).append( "<label for=\"orddd_time_slot\" class=\"\">" + orddd_params.orddd_timeslot_field_label + "</label><select name=\"orddd_time_slot\" id=\"orddd_time_slot\" class=\"orddd_custom_time_slot_mandatory\" disabled=\"disabled\" style=\"" + timeslot_custom_css + "\" placeholder=\"\"><option value=\"select\">" + jsL10n.selectText + "</option></select>" );                                    
                                } 

                                orddd_params['custom_settings'].time_slot_mandatory_for_shipping_method = '';

                                jQuery( "#orddd_time_slot_field" ).attr( "class", "form-row form-row-wide" );
                                jQuery( "#orddd_time_slot_field" ).attr( "style", "opacity: 0.5;" );
                            }
                            jQuery( "#orddd_time_slot" ).select2();
                        }
                    } else {
                        if( time_slot_enabled != "on" ) {
                            jQuery( "#admin_time_slot_field" ).remove();
                        }
                    }

                    if( "1" != orddd_params.orddd_is_admin ) {
                        var date_field_mandatory = orddd_params.orddd_date_field_mandatory;
                        if( date_field_mandatory == "checked" ) {
                            jQuery( "#e_deliverydate_field label[ for = \"e_deliverydate\" ]" ).append( "<abbr class=\"required\" title=\"required\">*</abbr>" );
                            jQuery( "#e_deliverydate_field" ).attr( "class", "form-row form-row-wide validate-required" );
                        } else {
                            jQuery( "#e_deliverydate_field" ).attr( "class", "form-row form-row-wide" );
                        }
                    }
                    jQuery( ".orddd_text_block" ).hide();
                    jQuery( "#orddd_estimated_shipping_date" ).val( "" );
                    orddd_params.orddd_is_shipping_text_block = 'no';
                } else if ( 'text_block' == orddd_params.orddd_delivery_checkout_options ) {
                    is_text_block = true;

                    jQuery( "#e_deliverydate_field" ).fadeOut();
                    jQuery( "#orddd_datepicker" ).fadeOut();
                    jQuery( "#e_deliverydate" ).val( "" );
                    jQuery( "#h_deliverydate" ).val( "" );
                    jQuery( "#e_deliverydate_field label[for=\"e_deliverydate\"] abbr" ).remove();
                   
                    orddd_params['custom_settings'].date_mandatory_for_shipping_method = '';

                    jQuery( "#orddd_time_slot_field" ).fadeOut();
                    orddd_params['custom_settings'].time_slot_mandatory_for_shipping_method = '';
                    orddd_params['custom_settings'].time_slot_enable_for_shipping_method = 'off';
                    
                    orddd_params.orddd_is_shipping_text_block = 'yes';
                    jQuery( ".orddd_text_block" ).show();
                    var shipping_date = orddd_get_text_block_shipping_date( orddd_params.orddd_global_minimum_delivery_time );
                    jQuery( "#orddd_min_range" ).html( jQuery( "#orddd_min_between_days" ).val() );
                    jQuery( "#orddd_max_range" ).html( jQuery( "#orddd_max_between_days" ).val() );
                    jQuery( "#shipping_date" ).html( shipping_date[ 'shipping_date' ] );
                    jQuery( "#orddd_estimated_shipping_date" ).val( shipping_date[ 'hidden_shipping_date' ] );
                }
            }
        }
    }

    if( 'yes' == update_settings ) {
        if( jQuery( '#' + orddd_params.orddd_field_name ).length == 0 ) {
            var a = { firstDay: parseInt( orddd_params.orddd_start_of_week ), beforeShowDay: chd };
                        
            if( "on" == orddd_params['custom_settings'].orddd_custom_based_same_day_delivery || "on" == orddd_params['custom_settings'].orddd_custom_based_next_day_delivery ) {
                var b = maxdt();
            } else if( "" == orddd_params['custom_settings'].orddd_custom_based_same_day_delivery && "" == orddd_params['custom_settings'].orddd_custom_based_next_day_delivery ) {
                var b = avd()
            } else if( "on" == orddd_params.orddd_same_day_delivery || "on" == orddd_params.orddd_next_day_delivery ) {
                var b = maxdt();
            } else {
                var b = avd();
            }

            var c = { minDate: b.minDate, maxDate: b.maxDate };

            var option_str = {};
            option_str = jsonConcat( option_str, a );
            option_str = jsonConcat( option_str, c );

            jQuery( ".availability_calendar" ).datepicker( option_str );
            jQuery( '.undefined' ).addClass( "ui-datepicker-unselectable" );
            jQuery( '.ui-state-default' ).replaceWith(function(){
                return jQuery( "<span class='ui-state-default'/>" ).append( jQuery(this).contents());
            });
        } else {

            if( 'yes' === jsL10n.is_dropdown_field && !is_text_block ) {
                load_dropdown_dates();
                return update_settings;
            }
            var date_format = orddd_params.orddd_delivery_date_format;
            var is_inline   = orddd_params.orddd_is_inline;

            var a = { firstDay: parseInt( orddd_params.orddd_start_of_week ), beforeShowDay: chd, dateFormat: date_format,
                onClose:function( dateStr, inst ) {
                if ( dateStr != "" ) {
                    var monthValue = inst.selectedMonth+1;
                    var dayValue = inst.selectedDay;
                    var yearValue = inst.selectedYear;
                    var all = dayValue + "-" + monthValue + "-" + yearValue;

                    var hourValue = jQuery( ".ui_tpicker_time" ).html();
                    jQuery( "#orddd_time_settings_selected" ).val( hourValue );
                    var event = arguments.callee.caller.caller.arguments[0];
                    // If "Clear" gets clicked, then really clear it
                    if( typeof( event ) !== "undefined" ) {
                        if ( jQuery( event.delegateTarget ).hasClass( "ui-datepicker-close" )) {
                            jQuery( this ).val(""); 
                            jQuery( "#h_deliverydate" ).val( "" );
                            jQuery( "#orddd_time_slot" ).prepend( "<option value=\"select\">" + jsL10n.selectText + "</option>" );
                            jQuery( "#orddd_time_slot" ).children( "option:not(:first)" ).remove();
                            jQuery( "#orddd_time_slot" ).attr( "disabled", "disabled" );
                            if( 1 == orddd_params.orddd_is_cart ) {
                                jQuery( "#orddd_time_slot" ).attr( "style", "cursor: not-allowed !important;max-width:300px" );
                            } else {
                                jQuery( "#orddd_time_slot" ).attr( "style", "cursor: not-allowed !important" );
                            }
                            jQuery( "#orddd_time_slot_field" ).css({ opacity: "0.5" });

                            jQuery( "body" ).trigger( "update_checkout" );
                            if ( 'on' == orddd_params.orddd_delivery_date_on_cart_page && '1' == orddd_params.orddd_is_cart ) {
                                jQuery( "#hidden_e_deliverydate" ).val( jQuery( '#' + orddd_params.orddd_field_name ).val() );
                                jQuery( "#hidden_h_deliverydate" ).val( all );
                                jQuery( "#hidden_timeslot" ).val( jQuery( "#orddd_time_slot" ).find(":selected").val() );
                                jQuery( "#hidden_shipping_method" ).val( shipping_method );
                                jQuery( "#hidden_shipping_class" ).val( orddd_params.orddd_shipping_class_settings_to_load );
                                jQuery( "#hidden_location" ).val( jQuery( "#orddd_locations" ).find(":selected").val() );
                                jQuery( "body" ).trigger( "wc_update_cart" );
                            }
                        }
                    } else if( jQuery( '#ui-datepicker-div' ).html().indexOf( 'ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all ui-state-hover' ) > -1 ) {
                        jQuery( "body" ).trigger( "update_checkout" );
                        if ( 'on' == orddd_params.orddd_delivery_date_on_cart_page && '1' == orddd_params.orddd_is_cart ) {
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
                jQuery( "#e_deliverydate" ).blur();
            },
            onSelect: show_times_custom }; 

            if( "on" == orddd_params['custom_settings'].orddd_custom_based_same_day_delivery || "on" == orddd_params['custom_settings'].orddd_custom_based_next_day_delivery ) {
                var avd_obj = maxdt();
                var b = '1' == is_inline ? { beforeShow: maxdt() } : { beforeShow: maxdt };
            } else if( "" == orddd_params['custom_settings'].orddd_custom_based_same_day_delivery && "" == orddd_params['custom_settings'].orddd_custom_based_next_day_delivery ) {
                var avd_obj = avd();
                var b = '1' == is_inline ? { beforeShow: avd() } : { beforeShow: avd } ;
            } else if( "on" == orddd_params.orddd_same_day_delivery || "on" == orddd_params.orddd_next_day_delivery ) {
                var avd_obj = maxdt();
                var b = '1' == is_inline ? { beforeShow: maxdt() } : { beforeShow: maxdt };
            } else {
                var avd_obj = avd();
                var b = is_inline ? { beforeShow: avd() } : { beforeShow: avd } ;
            }
            var time_settings_enabled = orddd_params.orddd_enable_time_slider;
            if ( string != "" && string != "off" ) {
                var clear_button_text = {};
            } else if ( string == "" && time_settings_enabled == "on" ) {
                var clear_button_text = {};
            } else {
                var clear_button_text = {showButtonPanel: true, closeText: jsL10n.clearText };
            }
            var option_str = {};
            option_str = jsonConcat( option_str, a );
            option_str = jsonConcat( option_str, b );
            option_str = jsonConcat( option_str, clear_button_text );

            option_str['minDate'] 	= avd_obj.minDate;
            option_str['maxDate'] 	= avd_obj.maxDate;
            option_str['numberOfMonths'] = avd_obj.numberOfMonths;
           // option_str['altField'] 	='#e_deliverydate';    

            if ( string != "" && string != "off" ) {
                var c = jQuery.parseJSON( string );                    
                var hour_min = parseInt( c.hourMin );
                var hour_max = parseInt( c.hourMax );
                var minute_min = parseInt( c.minuteMin );
                var minute_max = parseInt( c.minuteMax );
                var step_minute = parseInt( c.stepMinute );
                var time_format = ( c.timeFormat );
                option_str = jsonConcat( option_str, { hourMin: hour_min, hourMax: hour_max, stepMinute: step_minute, timeFormat: time_format } );
                jQuery( '#' + orddd_params.orddd_field_name ).datetimepicker( option_str ).focus( function ( event ) {
                    jQuery(this).trigger( "blur" );
                    jQuery.datepicker.afterShow( event );
                });
            } else if ( string == "" && time_settings_enabled == "on" ) {
                var options = orddd_params.orddd_option_str;
                var before_df_arr = options.split( '&' );
                var c = {};
                jQuery.each( before_df_arr, function( key, value ) {
                    if( '' != value && 'undefined' != typeof( value ) ) {
                        var split_value = value.split( ":" );
                        if( split_value.length != '2' ) {
                            var str = split_value[1] + ":" + split_value[2];
                            c[ split_value[0] ] = str.trim().replace( /'/g, "" );
                        } else if( 'hourMax' == split_value[0] || 'hourMin' == split_value[0] || 'stepMinute' == split_value[0] ) {
                            c[ split_value[0] ] = parseInt( split_value[1].trim() );  
                        }  else if ( 'minuteMin' == split_value[0] ) {
                            return;
                        } else if( 'beforeShow' == split_value[0] ) {
                            if( "on" == orddd_params.orddd_same_day_delivery || "on" == orddd_params.orddd_next_day_delivery ) {
                                c[ split_value[0] ] = maxdt();
                            } else {
                                c[ split_value[0] ] = avd();
                            }  
                        } else {
                            c[ split_value[0] ] = split_value[1].trim().replace( /'/g, "" );    
                        }    
                    }
                });
                option_str = jsonConcat( option_str, c );
                jQuery( '#' + orddd_params.orddd_field_name ).datetimepicker( option_str ).focus( function ( event ) {
                    jQuery(this).trigger( "blur" );
                    jQuery.datepicker.afterShow( event );
                });
            } else if ( string == "" && time_settings_enabled != "on" ) {
                jQuery( '#' + orddd_params.orddd_field_name ).datepicker( option_str ).focus( function ( event ) {
                    jQuery(this).trigger( "blur" );
                    jQuery.datepicker.afterShow( event );
                });
            } else {
                jQuery( '#' + orddd_params.orddd_field_name ).datepicker( option_str ).focus( function ( event ) {
                    jQuery(this).trigger( "blur" );
                    jQuery.datepicker.afterShow( event );
                });
            }
        }
        
        var orddd_available_dates_color = jQuery( "#orddd_available_dates_color" ).val() + '59';
        var orddd_booked_dates_color    = jQuery( "#orddd_booked_dates_color" ).val() + '59';

        if( 'on' == orddd_params.orddd_show_partially_booked_dates ) {
            jQuery( ".partially-booked" ).children().attr( 'style', 'background: linear-gradient(to bottom right, ' + orddd_booked_dates_color + ' 0%, ' + orddd_booked_dates_color + ' 50%, ' + orddd_available_dates_color + ' 50%, ' + orddd_available_dates_color + ' 100%);' );    
        }

        jQuery( ".available-deliveries" ).children().attr( 'style', 'background: ' + orddd_available_dates_color + ' !important;' );
        
    }
    return update_settings; 
}

/**
 * Returns the Text block information for the shipping method.
 *
 * @function orddd_get_text_block_shipping_date
 * @memberof orddd_initialize_functions
 * @param {timestamp} delivery_time_seconds - Minimum Delivery time in seconds
 * @returns {array} shipping_info - Shipping information
 * @since 6.7
 */
function orddd_get_text_block_shipping_date( delivery_time_seconds ) {
    var shipping_date = '';
    var date_format = orddd_params.orddd_delivery_date_format;
    var js_date_format = get_js_date_formats( date_format );

    var current_date = orddd_get_current_day();
    if( current_date != '' && typeof( current_date ) != 'undefined' ) {
        var split_current_date = current_date.split( '-' );
        var current_day = new Date( split_current_date[ 2 ], ( split_current_date[ 1 ] - 1 ), split_current_date[ 0 ], orddd_params.orddd_current_hour, orddd_params.orddd_current_minute );
    } else {
        var current_day = new Date();
    }
    
    var current_time = current_day.getTime();
    var current_weekday = current_day.getDay();

    var shipping_info = [];
    if( delivery_time_seconds != 0 && delivery_time_seconds != '' ) {
        var cut_off_timestamp = current_time + parseInt( delivery_time_seconds * 60 * 60 * 1000 );
        var cut_off_date = new Date( cut_off_timestamp );
        var cut_off_weekday = cut_off_date.getDay();

        if( 'on' == orddd_params.orddd_enable_shipping_days ) {
            for( i = current_weekday; current_time <= cut_off_timestamp; i++ ) {
                if( i >= 0 ) {
                    var shipping_day = 'orddd_shipping_day_' + current_weekday;
                    var shipping_day_check = orddd_params[shipping_day];
                    if ( shipping_day_check == '' ) {
                        current_day.setDate( current_day.getDate()+1 );
                        current_weekday = current_day.getDay();
                        current_time = current_day.getTime();
                        cut_off_date.setDate( cut_off_date.getDate()+1 );
                        cut_off_timestamp = cut_off_date.getTime();
                    } else {
                        if( current_time <= cut_off_timestamp ) {
                            current_day.setDate( current_day.getDate()+1 );
                            current_weekday = current_day.getDay();
                            current_time = current_day.getTime();
                        }
                    }
                } else {
                    break;
                }
            }
        }
        // shipping_info[ 'shipping_date' ] = moment( cut_off_date ).format( js_date_format ) ;    
        shipping_info[ 'shipping_date' ] = jQuery.datepicker.formatDate( date_format, cut_off_date ); 
        shipping_info[ 'hidden_shipping_date' ] = moment( cut_off_date ).format( 'D-M-YYYY' ) ;     
    } else {
        // shipping_info[ 'shipping_date' ] = moment( current_day ).format( js_date_format ) ;
        shipping_info[ 'shipping_date' ] = jQuery.datepicker.formatDate( date_format, current_day );
        shipping_info[ 'hidden_shipping_date' ] = moment( current_day ).format( 'D-M-YYYY' ) ;     
    }
    return shipping_info;
}

/**
 * Returns the date format in JS date format.
 *
 * @function get_js_date_formats
 * @memberof orddd_initialize_functions
 * @param {string} date_format - Date format
 * @returns {string} year_str - JS date format
 * @since 6.7
 */
function get_js_date_formats( date_format ) {
    var date_str = '';
    var month_str = '';
    var year_str = '';
    var day_str = '';
    switch( date_format ) {
        case "mm/dd/y":
            date_str = date_format.replace( new RegExp("\\bdd\\b"), 'DD' );
            month_str = date_str.replace( new RegExp("\\bmm\\b"), 'MM' );
            year_str = month_str.replace( new RegExp("\\by\\b"), 'YY' );
            break;
        case "dd/mm/y": 
            date_str = date_format.replace( new RegExp("\\bdd\\b"), 'DD' );
            month_str = date_str.replace( new RegExp("\\bmm\\b"), 'MM' );
            year_str = month_str.replace( new RegExp("\\by\\b"), 'YY' );
            break;
        case "y/mm/dd":
            date_str = date_format.replace( new RegExp("\\bdd\\b"), 'DD' );
            month_str = date_str.replace( new RegExp("\\bmm\\b"), 'MM' );
            year_str = month_str.replace( new RegExp("\\by\\b"), 'YY' );
            break;
        case "mm/dd/y, D":
            day_str = date_format.replace( new RegExp("\\bD\\b"), 'ddd' );
            date_str = day_str.replace( new RegExp("\\bdd\\b"), 'DD' );
            month_str = date_str.replace( new RegExp("\\bmm\\b"), 'MM' );
            year_str = month_str.replace( new RegExp("\\by\\b"), 'YY' );
            break;
        case "dd.mm.y":
            date_str = date_format.replace( new RegExp("\\bdd\\b"), 'DD' );
            month_str = date_str.replace( new RegExp("\\bmm\\b"), 'MM' );
            year_str = month_str.replace( new RegExp("\\by\\b"), 'YY' );
            break;
        case "y.mm.dd":
            date_str = date_format.replace( new RegExp("\\bdd\\b"), 'DD' );
            month_str = date_str.replace( new RegExp("\\bmm\\b"), 'MM' );
            year_str = month_str.replace( new RegExp("\\by\\b"), 'YY' );
            break;
        case "yy-mm-dd":
            date_str = date_format.replace( new RegExp("\\bdd\\b"), 'DD' );
            month_str = date_str.replace( new RegExp("\\bmm\\b"), 'MM' );
            year_str = month_str.replace( new RegExp("\\byy\\b"), 'YYYY' );
            break;
        case "dd-mm-y":
            date_str = date_format.replace( new RegExp("\\bdd\\b"), 'DD' );
            month_str = date_str.replace( new RegExp("\\bmm\\b"), 'MM' );
            year_str = month_str.replace( new RegExp("\\by\\b"), 'YY' );
            break;
        case 'd M, y':
            date_str = date_format.replace( new RegExp("\\bd\\b"), 'D' );
            month_str = date_str.replace( new RegExp("\\bM\\b"), 'MMM' );
            year_str = month_str.replace( new RegExp("\\by\\b"), 'YY' );
            break;
        case 'd M, yy':
            date_str = date_format.replace( new RegExp("\\bd\\b"), 'D' );
            month_str = date_str.replace( new RegExp("\\bM\\b"), 'MMM' );
            year_str = month_str.replace( new RegExp("\\byy\\b"), 'YYYY' );
            break;
        case 'd MM, y':
            date_str = date_format.replace( new RegExp("\\bd\\b"), 'D' );
            month_str = date_str.replace( new RegExp("\\bMM\\b"), 'MMMM' );
            year_str = month_str.replace( new RegExp("\\by\\b"), 'YY' );
            break;
        case 'd MM, yy':
            date_str = date_format.replace( new RegExp("\\bd\\b"), 'D' );
            month_str = date_str.replace( new RegExp("\\bMM\\b"), 'MMMM' );
            year_str = month_str.replace( new RegExp("\\byy\\b"), 'YYYY' );
            break;
        case 'DD, d MM, yy':
            day_str = date_format.replace( new RegExp("\\bDD\\b"), 'dddd' );
            date_str = day_str.replace( new RegExp("\\bd\\b"), 'D' );
            month_str = date_str.replace( new RegExp("\\bMM\\b"), 'MMMM' );
            year_str = month_str.replace( new RegExp("\\byy\\b"), 'YYYY' );
            break;
        case 'D, M d, yy':
            day_str = date_format.replace( new RegExp("\\bD\\b"), 'ddd' );
            date_str = day_str.replace( new RegExp("\\bd\\b"), 'D' );
            month_str = date_str.replace( new RegExp("\\bM\\b"), 'MMM' );
            year_str = month_str.replace( new RegExp("\\byy\\b"), 'YYYY' );
            break;
        case 'DD, M d, yy':
            day_str = date_format.replace( new RegExp("\\bDD\\b"), 'dddd' );
            date_str = day_str.replace( new RegExp("\\bd\\b"), 'D' );
            month_str = date_str.replace( new RegExp("\\bM\\b"), 'MMM' );
            year_str = month_str.replace( new RegExp("\\byy\\b"), 'YYYY' );
            break;
        case 'DD, MM d, yy':
            day_str = date_format.replace( new RegExp("\\bDD\\b"), 'dddd' );
            date_str = day_str.replace( new RegExp("\\bd\\b"), 'D' );
            month_str = date_str.replace( new RegExp("\\bMM\\b"), 'MMMM' );
            year_str = month_str.replace( new RegExp("\\byy\\b"), 'YYYY' );
            break;
        case 'D, MM d, yy':
            day_str = date_format.replace( new RegExp("\\bD\\b"), 'ddd' );
            date_str = day_str.replace( new RegExp("\\bd\\b"), 'D' );
            month_str = date_str.replace( new RegExp("\\bMM\\b"), 'MMMM' );
            year_str = month_str.replace( new RegExp("\\byy\\b"), 'YYYY' );
            break;
    }

    return year_str;
}

/**
 * Concatenation of options for jQuery datepicker
 *
 * @function jsonConcat
 * @memberof orddd_initialize_functions
 * @param {string} o1 - Options of datepicker
 * @param {string} o2 - Options of datepicker
 * @returns {string} o1 - Concatenation of two Options o1 and o2
 * @since 1.0
 */
function jsonConcat( o1, o2 ) {
    for ( var key in o2 ) {
        o1[ key ] = o2[ key ];
    }
    return o1;
}

/**
 * Shows the Custom Time Slots
 *
 * @function show_times_custom
 * @memberof orddd_initialize_functions
 * @param {date} date - Date
 * @param {object} inst 
 * @since 3.0
 */
function show_times_custom( date, inst ) {
    var hourValue = jQuery( ".ui_tpicker_time" ).html();
    jQuery( "#orddd_time_settings_selected" ).val( hourValue );
    jQuery( document ).trigger( 'on_select_additional_action', [ date, inst ] );

    if( 'yes' == orddd_params.orddd_disable_delivery_fields && "1" != orddd_params.orddd_is_admin ) {
        jQuery( '#' + orddd_params.orddd_field_name ).datepicker( "option", "disabled", true );
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
    
    if ( inst.selectedMonth ) {
        var monthValue = inst.selectedMonth+1;
        var dayValue   = inst.selectedDay;
        var yearValue  = inst.selectedYear;
        var all        = dayValue + "-" + monthValue + "-" + yearValue;
    } else {
        var all = jQuery('#h_deliverydate').val();
    }
    var date_YMD   = yearValue  + "-" + monthValue  + "-" + dayValue;
    var setting_id = 0;
    var orddd_unique_custom_settings = orddd_params.orddd_unique_custom_settings;
    var orddd_applied_custom_settings = orddd_get_applied_custom_settings();
    var setting_ids = orddd_applied_custom_settings.setting_ids;
    var common_ids  = [];

    if( setting_ids.length > 1 ) {
        common_ids = setting_ids;
    }

    if( 'global_settings' !== orddd_unique_custom_settings && '' !== orddd_unique_custom_settings ) {
        var split_str = orddd_unique_custom_settings.split('_');
        setting_id    = split_str[2];
    }
    
    jQuery( "#h_deliverydate" ).val( all );
    jQuery( "#e_deliverydate" ).val(  jQuery('#' + orddd_params.orddd_field_name ).val() );

    if( "on" == orddd_params['custom_settings'].time_slot_enable_for_shipping_method ) {
        if( typeof( inst.id ) !== "undefined" ) {  
            orddd_get_time_slot_response( all, date_YMD, setting_id, common_ids );    
        }
    } else if( "on" == orddd_params['custom_settings'].time_setting_enable_for_shipping_method ) {
        all = orddd_set_time_slider_range( 'yes', date, inst );
    } else if( "on" == orddd_params.orddd_enable_time_slot && ( typeof( orddd_params['custom_settings'].time_slot_enable_for_shipping_method ) == 'undefined' ) ) {
        if( typeof( inst.id ) !== "undefined" ) {  
            orddd_get_time_slot_response( all, date_YMD, '0', common_ids );    
        }
    } else if( "on" == orddd_params.orddd_enable_time_slider && ( typeof( orddd_params['custom_settings'].time_setting_enable_for_shipping_method ) == 'undefined' ) ) {
        all = orddd_set_time_slider_range( 'no', date, inst );
    } else {
        jQuery( "body" ).trigger( "update_checkout" );
        if ( 'on' == orddd_params.orddd_delivery_date_on_cart_page && '1' == orddd_params.orddd_is_cart ) {
            jQuery( "#hidden_e_deliverydate" ).val( jQuery( '#' + orddd_params.orddd_field_name ).val() );
            jQuery( "#hidden_h_deliverydate" ).val( all );
            jQuery( "#hidden_timeslot" ).val( jQuery( "#orddd_time_slot" ).find(":selected").val() );
            jQuery( "#hidden_shipping_method" ).val( shipping_method );
            jQuery( "#hidden_shipping_class" ).val( orddd_params.orddd_shipping_class_settings_to_load );
            jQuery( "#hidden_location" ).val( jQuery( "#orddd_locations" ).find(":selected").val() );
            jQuery( "body" ).trigger( "wc_update_cart" );
        }
    }

    // Below code sets the selected date & time slot in localStorage when the field is enabled 
    // for Cart page. It is stored in localStorage upto 2 hours from current time
    localStorage.setItem( "e_deliverydate_session", jQuery( '#' + orddd_params.orddd_field_name ).val() );
    localStorage.setItem( "h_deliverydate_session", all );
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

/**
 * Get the timeslots for a given date through REST API.
 * @param {string} all Selected date.
 * @param {string} date Selected date in YMD format.
 * @param {int} setting_id Custom settings ID.
 */
function orddd_get_time_slot_response( all, date, setting_id, common_ids ) {
    var shipping_method = orddd_get_selected_shipping_method();

    var xhr = new XMLHttpRequest();
    var rest_url = orddd_params.orddd_rest_url + 'orddd/v1/delivery_schedule/' + setting_id + '?date=' + date;

    if( common_ids.length > 1 ) {
        var common_ids_str = common_ids.join( ',' );
        rest_url = orddd_params.orddd_rest_url + 'orddd/v1/delivery_schedule/?date=' + date + '&ids=' + common_ids_str;
    }
    xhr.open( 'GET', rest_url );
 
    xhr.onload = function () {
        if (xhr.readyState === xhr.DONE) {
            if (xhr.status === 200) {
                var response = JSON.parse( xhr.responseText );
                var time_slot_str     = '';
                var option_selected   = orddd_params.orddd_auto_populate_first_available_time_slot;
                var time_slot_session = localStorage.getItem( "orddd_time_slot" );
                jQuery( "#orddd_time_slot" ).attr("disabled", "disabled");
                jQuery( "#orddd_time_slot_field" ).attr( "style", "opacity: 0.5" );
                time_slot_str = "select/";
                response.forEach( ( element, index ) => {
                    var selected = '';
                    if( '' !== time_slot_session && element.time_slot == time_slot_session ) {
                        selected = 'selected';
                    } else if( 0 == index && 'on' === option_selected ) {
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

/**
 * This function disables the date in the calendar for holidays.
 *
 * @function nd
 * @memberof orddd_initialize_functions
 * @param {object} date - date to be checked
 * @returns {bool} Returns true or false based on date available or not
 * @since 1.0
 */
function nd( date ) {
    var holidays     = orddd_get_holidays();
    var disabledDays = JSON.parse( '[' + holidays + ']' );
    var m = date.getMonth(), d = date.getDate(), y = date.getFullYear(), w = date.getDay();
    var currentdt = m + '-' + d + '-' + y;
    
    var dt = new Date();
    var today = dt.getMonth() + '-' + dt.getDate() + '-' + dt.getFullYear();
    for ( i = 0; i < disabledDays.length; i++ ) {
        if( disabledDays[ i ] != '' && typeof( disabledDays[ i ] ) != 'undefined' ) {
            var holidays_array = disabledDays[ i ].split( ":" );
            if( holidays_array[ 1 ] == ( ( m+1 ) + '-' + d + '-' + y ) || holidays_array[ 1 ] == ( ( m+1 ) + '-' + d ) ) {
                if( '' == holidays_array[ 0 ] ) {
                    return [ false, "holidays", "Holiday" ];
                } else {
                    return [ false, "holidays", holidays_array[ 0 ]  ];
                }
            } 
        }
    }
	var weekdays = [];
    weekdays[ 'monday' ] = 1;
    weekdays[ 'tuesday' ] = 2;
    weekdays[ 'wednesday' ] = 3;
    weekdays[ 'thursday' ] = 4;
    weekdays[ 'friday' ] = 5;
    weekdays[ 'saturday' ] = 6;
    weekdays[ 'sunday' ] = 0;
	
    var add_tooltip_for_weekday = orddd_params.add_tooltip_for_weekday;
    var is_tooltip_set = 'no';
    if( '' != add_tooltip_for_weekday && typeof( add_tooltip_for_weekday ) != 'undefined' ) {
        var weekday_tooltip_arr = add_tooltip_for_weekday.split( ";" );
        for( i=0; i < weekday_tooltip_arr.length; i++ ) {
            var tooltip_arr = weekday_tooltip_arr[ i ].split( '=>' );
            var weekday = tooltip_arr[ 0 ];
            var weekday_tooltip = tooltip_arr[ 1 ];
            if( typeof weekdays[ weekday ] != 'undefined' && date.getDay() == weekdays[ weekday ] ) {
                return [ true, '', weekday_tooltip ];        
            } else {
                is_tooltip_set = 'no';
            }    
        }
    } 
    if( 'no' == is_tooltip_set ) {
        return [ true ];    
    }
}

/**
 * This function disables the date in the calendar for disabled weekdays and for which lockout is reached.
 *
 * @function dwd
 * @memberof orddd_initialize_functions
 * @param {object} date - date to be checked
 * @returns {bool} Returns true or false based on date available or not
 * @since 1.0
 */
function dwd( date ) {
    var lockout_calculation = 'yes';
    if ( 'on' == orddd_params.orddd_subscriptions_settings && "undefined" != typeof orddd_params.orddd_if_renewal_subscription && 'yes' == orddd_params.orddd_if_renewal_subscription ) {
        lockout_calculation = 'no'; 
    }
    
    var orddd_unique_settings        = orddd_params.orddd_unique_custom_settings;
    var orddd_disabled_days_str      = orddd_get_disabled_days();;
    var orddd_disabled_weekdays_str  = orddd_get_disabled_weekdays();
    var orddd_lockout_days           = orddd_get_lockout_days();
    var orddd_delivery_date_holidays = orddd_get_holidays();

    var lockoutDays = JSON.parse( '[' + orddd_lockout_days + ']' );
    var holidayDays = JSON.parse( '[' + orddd_delivery_date_holidays + ']' );
    var holidays    = [];
    
    for ( i = 0; i < holidayDays.length; i++ ) {
        if( holidayDays[ i ] != '' && typeof( holidayDays[ i ] ) != 'undefined' ) {
            var holidays_array  = holidayDays[ i ].split( ":" );
            holidays[i]         = holidays_array[ 1 ];
        }
    }

    var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();

    var startDaysDisabled = JSON.parse( "[" + orddd_disabled_days_str + "]" );
    var startWeekDaysDisabled = JSON.parse( "[" + orddd_disabled_weekdays_str + "]" );

    var orddd_weekdays = [];
    if( 'on' !== orddd_params.orddd_enable_shipping_based_delivery ) {
        orddd_weekdays = JSON.parse( "[" + orddd_params.orddd_load_delivery_date_var + "]" );
    }else {
        var hidden_var_obj = orddd_params.orddd_hidden_vars_str;
        var html_vars_obj = jQuery.parseJSON( hidden_var_obj || '{}' );
        if( html_vars_obj == null ) {
            html_vars_obj = {};
        }

        jQuery.each( html_vars_obj, function( key, value ) {
            orddd_weekdays = JSON.parse( "[" + value.hidden_vars + "]" );
        });
    }

    for ( i = 0; i < startDaysDisabled.length; i++ ) {
        if( jQuery.inArray( ( m+1 ) + '-' + d + '-' + y, startDaysDisabled ) != -1 ) {
            var disabled_date = new Date(( m+1 ) + '-' + d + '-' + y);
            var disabled_day = disabled_date.getDay();

            if( typeof( orddd_weekdays[0] ) != 'undefined' && 
                typeof( orddd_weekdays[0][ "orddd_weekday_" + disabled_day ] ) != 'undefined' && 
                orddd_weekdays[0][ "orddd_weekday_" + disabled_day ] != 'checked' && 
                date.getDay() == disabled_day ) {
                return [ false, "disabled_weekdays", "" ];
            } else {
                return [ false, "cut_off_time_over", jsL10n.cutOffTimeText ];
            } 
        }
    }

    for ( i = 0; i < startWeekDaysDisabled.length; i++ ) {
        if( jQuery.inArray( ( m+1 ) + '-' + d + '-' + y, startWeekDaysDisabled ) != -1 ) {
            var disabled_date = new Date(( m+1 ) + '-' + d + '-' + y);
            var disabled_day = disabled_date.getDay();
            var lastItem = startWeekDaysDisabled.pop();
            
            if( ( typeof( orddd_weekdays[0] ) != 'undefined' && 
                  typeof( orddd_weekdays[0][ "orddd_weekday_" + disabled_day ] ) != 'undefined' && 
                  orddd_weekdays[0]["orddd_weekday_" + disabled_day ] !== 'checked' && 
                  date.getDay() == disabled_day ) || 
                jQuery.inArray( ( m+1 ) + '-' + d + '-' + y, holidays ) == -1 ) {
                return [ false, "disabled_weekdays", "" ];
            }

            if( ( m+1 ) + '-' + d + '-' + y == lastItem ) {
                return [ false, "cut_off_time_over", jsL10n.cutOffTimeText ];
            } else {
                return [ false, "disabled_weekdays", "" ];    
            }
        }
    }

    if( lockout_calculation == "yes" ) {
        for ( i = 0; i < lockoutDays.length; i++ ) {
            if( jQuery.inArray( ( m+1 ) + '-' + d + '-' + y, lockoutDays ) != -1 ) {
                return [ false, "booked_dates", jsL10n.bookedText ];
            }
        }
    }
	
	var weekdays = [];
    weekdays[ 'monday' ] = 1;
    weekdays[ 'tuesday' ] = 2;
    weekdays[ 'wednesday' ] = 3;
    weekdays[ 'thursday' ] = 4;
    weekdays[ 'friday' ] = 5;
    weekdays[ 'saturday' ] = 6;
    weekdays[ 'sunday' ] = 0;
	
    var day = 'orddd_weekday_' + date.getDay();
    var weekday_enabled        = orddd_day_check( day );
    var specific_dates_enabled = orddd_specific_dates_enabled();
    var specific_dates         = orddd_get_specific_dates();

    if ( 'checked' == weekday_enabled ) {
        var add_tooltip_for_weekday = orddd_params.add_tooltip_for_weekday;
        var is_tooltip_set = 'no';
        if( '' != add_tooltip_for_weekday ) {
            var weekday_tooltip_arr = add_tooltip_for_weekday.split( ";" );
            for( i=0; i < weekday_tooltip_arr.length; i++ ) {
                var tooltip_arr = weekday_tooltip_arr[ i ].split( '=>' );
                var weekday = tooltip_arr[ 0 ];
                var weekday_tooltip = tooltip_arr[ 1 ];
                if( typeof weekdays[ weekday ] != 'undefined' && date.getDay() == weekdays[ weekday ] ) {
                    return [ true, '', weekday_tooltip ];        
                } else {
                    is_tooltip_set = 'no';
                }    
            }
        } 
        if( 'no' == is_tooltip_set ) {
            return [ true ];    
        }
    } else if ( "on" == specific_dates_enabled ) {
        if ( '' != specific_dates ) {
            var deliveryDates = JSON.parse( '[' + specific_dates + ']');
            var dt = new Date();
            var today = dt.getMonth() + '-' + dt.getDate() + '-' + dt.getFullYear();
            for ( i = 0; i < deliveryDates.length; i++ ) {
                if( jQuery.inArray( ( m+1 ) + '-' + d + '-' + y, deliveryDates ) != -1 ) {
                    return [ true ];
                }
            }
        }
    }
    return [ false ];
}

/**
 * This function returns the availability of the dates in the calendar. 
 *
 * @function pd
 * @memberof orddd_initialize_functions
 * @param {object} date - date to be checked
 * @returns {bool} Returns true or false based on date available or not
 * @since 1.0
 */
function pd( date ) {
    var field_name = orddd_params.orddd_field_name;    
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var current_date = orddd_get_min_date();
    var split_current_date = current_date.split( '-' );
    var current_day_to_check = new Date ( split_current_date[ 1 ] + '/' + split_current_date[ 0 ] + '/' + split_current_date[ 2 ] );

    var current_time = current_day_to_check.getTime();
    var date_time = date.getTime();

    var orddd_enable_availability_display = orddd_params.orddd_enable_availability_display;
    if( date_time >= current_time ) {
        var day = 'orddd_weekday_' + date.getDay();
        var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
        var dfm = ( m+1 ) + '-' + d + '-' + y;
        var partially_booked_str = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].orddd_partially_booked_dates : orddd_params.orddd_partially_booked_dates;
        partially_booked_str     = partially_booked_str.replaceAll( "'", "" );

        if( partially_booked_str != '' && 'on' == orddd_params.orddd_show_partially_booked_dates ) {
            var partially_booked = partially_booked_str.split( "," );
            partially_booked     = partially_booked.filter(Boolean);

            var partial_availability_str = "";

            for ( i = 0; i <= partially_booked.length; i++ ) {
                if( typeof partially_booked[ i ] != 'undefined' ) {
                    var partially_booked_arr = partially_booked[ i ].split( ">" );
                    if( partially_booked_arr[0] == 'available_slots' ) {
                        partial_availability_str = partial_availability_str + partially_booked_arr[1].replace( /--/gi, "\n" );
                    }

                    if( partially_booked_arr[0] == dfm ) {
                        partial_availability_str = partial_availability_str + partially_booked_arr[1].replace( /--/gi, "\n" );

                        if( 'on' != orddd_enable_availability_display ) {
                            partial_availability_str = '';
                        }
                        if( jQuery( '#' + field_name ).length == 0 && jQuery( 'input[id^=' + field_name + ']' ).length == 0 ) {
                            return [ true, "undefined ui-datepicker-unselectable partially-booked", partial_availability_str ];
                        } else {
                            return [ true, "partially-booked", partial_availability_str ];
                        }
                    }
                }
            } 
        }

        var available_deliveries = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].orddd_available_deliveries : orddd_params.orddd_available_deliveries;
        available_deliveries     = available_deliveries.replaceAll( "'", "" );

        var timeslots_enabled = 'off';

        if( 'global_settings' != orddd_unique_settings && 'on' == orddd_params['custom_settings'].time_slot_enable_for_shipping_method ) {
            timeslots_enabled = 'on';
        }else if( ( 'global_settings' == orddd_unique_settings || '' == orddd_unique_settings ) && 'on' == orddd_params.orddd_enable_time_slot ) {
            timeslots_enabled = 'on';
        }
        
        if( available_deliveries.indexOf( ',' ) !== -1 ) {
            available_deliveries_arr = available_deliveries.split( "," );
            available_deliveries_arr = available_deliveries_arr.filter(Boolean);
            var availability_str = "";
            var is_added_as_specifc_date = 'no';

            if( "on" == timeslots_enabled ) {
                for ( i = 0; i <= available_deliveries_arr.length; i++ ) {
                    // If the time slots are added for the specific dates or 
                    // if the current date is set as min date. It will display the time slots for that date.
                    // Here we have added two condition for d-m-Y for and m-d-Y format because the specific dates
                    // are added in m-d-Y format and min date as other. And as we have added min date as specific date,
                    // We have added 2 conditions in if.
                    // If not, then it will go in else and display the time slots for the weekday of the date.
                    // is_added_as_specifc_date variable is set to no by default and if the current date passed in the function i.e 
                    // the date variable is added as specific date or it is min date the is_added_as_specifc_date variable will be set
                    // to yes.
                    if( typeof available_deliveries_arr[ i ] != 'undefined' ) {
                        var availability_arr = available_deliveries_arr[ i ].split( ">" );
                    }
                    if( typeof available_deliveries_arr[ i ] != 'undefined' && 
                        ( available_deliveries_arr[ i ].indexOf( d + '-' + ( m+1 ) + '-' + y ) !== -1 ||
                            available_deliveries_arr[ i ].indexOf( dfm ) !== -1 ) ) {
                        var availability_arr = available_deliveries_arr[ i ].split( ">" );
                        var availability_str = availability_str + availability_arr[ 1 ] + "\n"; 
                        var is_added_as_specifc_date = 'yes';
                    } else if( typeof available_deliveries_arr[ i ] != 'undefined' && (
                        ( ( available_deliveries_arr[ i ].indexOf( day ) !== -1 || available_deliveries_arr[ i ].indexOf( 'all' ) !== -1 ) && 
                        is_added_as_specifc_date == 'no' ) ||
                         available_deliveries_arr[ i ].indexOf( 'available_slots' ) !== -1
                        ) ) {
                       var availability_arr = available_deliveries_arr[ i ].split( ">" );
                       var availability_str = availability_str + availability_arr[ 1 ] + "\n";
                    }
                }
            }else {
                for ( i = 0; i <= available_deliveries_arr.length; i++ ) {
                    if( typeof available_deliveries_arr[ i ] != 'undefined' ) {
                        var availability_arr = available_deliveries_arr[ i ].split( ">" );
                        var availability_str = availability_arr[ 1 ];
                    }
                    if( typeof available_deliveries_arr[ i ] != 'undefined' && availability_arr[ 0 ] !== "" && 
                        ( available_deliveries_arr[ i ].indexOf( ( m+1 ) + '-' + d + '-' + y ) !== -1 ) ) {
                            var availability_str = availability_arr[ 1 ];
                            break;
                    }
                   
                }
            }

            if( 'on' != orddd_enable_availability_display ) {
                availability_str = '';
            }
            if( jQuery( '#' + field_name ).length == 0 && jQuery( 'input[id^=' + field_name + ']' ).length == 0 ) {
                return [ true, 'undefined ui-datepicker-unselectable available-deliveries', availability_str ];    
            } else {
                return [ true, 'available-deliveries', availability_str ];    
            }
        }

        if( 'on' != orddd_enable_availability_display ) {
            available_deliveries = '';
        }
        if( jQuery( '#' + field_name ).length == 0 && jQuery( 'input[id^=' + field_name + ']' ).length == 0 ) {
            return [ true, 'undefined ui-datepicker-unselectable available-deliveries', available_deliveries ];    
        } else {
            return [ true, 'available-deliveries', available_deliveries ];    
        }
    } else {
        return [true];
    }
}

/**
 * The function is called for each day in the datepicker before it is displayed.
 *
 * @function chd
 * @memberof orddd_initialize_functions
 * @param {object} date - date to be checked
 * @returns {array} Returns an array
 * @since 1.0
 */
function chd( date ) {
    var nW = dwd( date );
    if( nW[ 0 ] == true ) {
        var holiday = nd( date );
        if( holiday[ 0 ] == false ) {
            return holiday;
        } else {
            return pd( date );
        } 
    } else {
        return nW;
    }
}

/**
 * This function is called just before the datepicker is displayed.
 *
 * @function avd
 * @memberof orddd_initialize_functions
 * @param {object} date - date to be checked
 * @returns {object} options object to update the datepicker
 * @since 1.0
 */
function avd( date, inst ) {
    // Added to not translate the calendar when the site is translated using Google Translator. 
    if( typeof( inst ) != 'undefined' ) {
        inst.dpDiv.addClass( 'notranslate' );
    }
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var if_subscription       = 'no';
    var current_date          = orddd_get_current_day();
    
    if( current_date != '' && typeof( current_date ) != 'undefined' ) {
        var split_current_date   = current_date.split( '-' );
        var current_day          = new Date ( split_current_date[ 1 ] + '/' + split_current_date[ 0 ] + '/' + split_current_date[ 2 ] );
        var current_day_to_check = new Date ( split_current_date[ 1 ] + '/' + split_current_date[ 0 ] + '/' + split_current_date[ 2 ] );
    } else {
        var current_day          = new Date();
        var current_day_to_check = new Date();
    }

    var holidays_str        = orddd_get_holidays();
    var lockout_days        = orddd_get_lockout_days();
    var specific_dates      = orddd_get_specific_dates();
    var disabledDays        = JSON.parse( '[' + holidays_str + ']' );
    var bookedDays          = JSON.parse( "[" + lockout_days + "]" );
    var holidays            = [];
    var deliveryDates       = JSON.parse( '[' + specific_dates + ']');

    var all_common_days_disabled = orddd_params['custom_settings'].orddd_common_days_disabled;

    for ( i = 0; i < disabledDays.length; i++ ) {
        if( disabledDays[ i ] != '' && typeof( disabledDays[ i ] ) != 'undefined' ) {
            var holidays_array  = disabledDays[ i ].split( ":" );
            holidays[i]         = holidays_array[ 1 ];
        }
    }
    
    var specific_dates_enabled  = orddd_specific_dates_enabled();
    var renewal_date    = new Date();
    var subscription_date = new Date();

    if ( 'on' == orddd_params.orddd_subscriptions_settings ) {
        var noOfDaysToFind = parseInt( orddd_params.orddd_number_of_dates_for_subscription );
        var orddd_start_date_for_subscription = ( '1' == orddd_params.orddd_is_account_page ) ? orddd_account_params.orddd_start_date_for_subscription : orddd_params.orddd_start_date_for_subscription;
        var orddd_number_of_dates_for_subscription = ( '1' == orddd_params.orddd_is_account_page ) ? orddd_account_params.orddd_number_of_dates_for_subscription : orddd_params.orddd_number_of_dates_for_subscription;

        if ( "undefined" != typeof orddd_start_date_for_subscription && "undefined" != typeof orddd_number_of_dates_for_subscription ) {
            var start_date = orddd_start_date_for_subscription;
            if( start_date != '' && typeof( start_date ) != 'undefined' ) {
                var split_date = start_date.split( '-' );
                var delay_days = new Date ( split_date[1] + '/' + split_date[0] + '/' + split_date[2] );
            } else {
                var delay_days = new Date();
            }
            
            
            if ( isNaN( delay_days ) ) {
                delay_days = new Date();
                delay_days.setDate( delay_days.getDate()+1 );
            }
            
            if ( isNaN( noOfDaysToFind ) || 0 === noOfDaysToFind ) {
                noOfDaysToFind = 1000;
            }

            subscription_date.setDate( delay_days.getDate() );
            renewal_date = new Date( ad( subscription_date, noOfDaysToFind ) );

            if_subscription = 'yes';
        }
        
        if( jQuery( "#renewal_delivery_date" ).val() !== undefined ) {
            var renewal = jQuery( "#renewal_delivery_date" ).val();
            var split_date = renewal.split( '-' );
            var delay_days = new Date ( split_date[1] + '/' + split_date[0] + '/' + split_date[2] );

            if ( isNaN( delay_days ) ) {
                delay_days = new Date();
                delay_days.setDate( delay_days.getDate()+1 );

            }
            
            subscription_date.setDate( delay_days.getDate() );
            renewal_date = new Date( ad( subscription_date, noOfDaysToFind ) );

            if_subscription = 'yes';

        }

        if( "1" == orddd_params.orddd_is_account_page && orddd_account_params.orddd_my_account_default_date !== undefined ) {
            
            var renewal = orddd_get_min_date();
            var split_date = renewal.split( '-' );
            var delay_days = new Date ( split_date[1] + '/' + split_date[0] + '/' + split_date[2] );
            if ( isNaN( delay_days ) ) {
                delay_days = new Date();
                delay_days.setDate( delay_days.getDate()+1 );
            }
            
            subscription_date.setDate( delay_days.getDate() );
            renewal_date = new Date( ad( subscription_date, noOfDaysToFind ) );
            if_subscription = 'yes';
        }
    }
    
    if ( if_subscription == 'no' ) {
        
        var delay_date = orddd_get_min_date();

        if ( inst != undefined && inst.id == 'e_pickupdate' ) {
            delay_date = jQuery( "#orddd_pickup_min_date" ).val();
        }
        
        if ( delay_date != '' && typeof( delay_date ) != 'undefined' ) {
            var split_date = delay_date.split( '-' );
            var delay_days = new Date ( split_date[ 1 ] + '/' + split_date[ 0 ] + '/' + split_date[ 2 ] );
        } else {
            var delay_days = current_day;
        }
    
        var noOfDaysToFind = orddd_get_number_of_dates();
        
        if ( isNaN( delay_days ) ) {
            delay_days = new Date();
            delay_days.setDate( delay_days.getDate() + 1 );
        }
        
        if ( isNaN( noOfDaysToFind ) || 0 === noOfDaysToFind ) {
            noOfDaysToFind = 1000;
        }
    }
    
    // re-calculate the Minimum Delivery time (in days): to include weekdays that are disabled for delivery
    if ( 'yes' != orddd_params.orddd_disable_for_delivery_days ) {
        var limit = 0;
        if ( delay_date != "" ) {
            if ( 'on' == orddd_params.orddd_enable_shipping_days ) {

                if( 'yes' != all_common_days_disabled ) {
                    var delay_weekday = delay_days.getDay();
                    for ( j = delay_weekday ; ; j++ ) {
                        limit++;

                        day         = 'orddd_weekday_' + delay_weekday;
                        day_check   = orddd_day_check( day );
                        var dm = delay_days.getMonth(), dd = delay_days.getDate(), dy = delay_days.getFullYear();
        
                        if ( day_check == '' && jQuery.inArray( ( dm+1 ) + "-" + dd + "-" + dy, deliveryDates ) == -1 ) {
                            delay_days.setDate( delay_days.getDate()+1 );
                            delay_weekday = delay_days.getDay();
                        } else {
                            break;
                        }

                        if( limit >= 365 ) {
                            break;
                        }

                    }
                }

                var current_date_to_set = orddd_get_current_date_set();
                if( current_date_to_set != '' && typeof( current_date_to_set ) != 'undefined' ) {
                    var split_current_date_to_check = current_date_to_set.split( '-' );
                    var current_day_to_check        = new Date ( split_current_date_to_check[ 1 ] + '/' + split_current_date_to_check[ 0 ] + '/' + split_current_date_to_check[ 2 ] );
                } else {
                    var current_day_to_check        = new Date ();
                }
            } 
        }
    }

    var min_date_to_set = delay_days.getDate() + "-" + ( delay_days.getMonth()+1 ) + "-" + delay_days.getFullYear();
    //jQuery( "#orddd_min_date_set" ).val( min_date_to_set );
    orddd_params.orddd_min_date_set = min_date_to_set;
    
    var current_day_to_set = current_day_to_check.getDate() + "-" + ( current_day_to_check.getMonth()+1 ) + "-" + current_day_to_check.getFullYear();
    
    if( 'global_settings' !== orddd_unique_settings && '' != orddd_unique_settings ) {
        orddd_params['custom_settings'].orddd_current_date_set = current_day_to_set;
    } else {
        orddd_params.orddd_current_date_set = current_day_to_set;
    }

    var todays_date     = new Date();
    var t_year          = todays_date.getFullYear();
    var t_month         = todays_date.getMonth()+1;
    var t_day           = todays_date.getDate();
    var t_month_days    = new Date( t_year, t_month, 0 ).getDate();

    start               = ( delay_days.getMonth()+1 ) + "/" + delay_days.getDate() + "/" + delay_days.getFullYear();
    var start_month     = delay_days.getMonth()+1;
    var start_year      = delay_days.getFullYear();
    
    var end_date        = new Date( ad( delay_days , noOfDaysToFind ) );  
    
    var orddd_number_of_dates_for_subscription = ( '1' == orddd_params.orddd_is_account_page ) ? orddd_account_params.orddd_number_of_dates_for_subscription : orddd_params.orddd_number_of_dates_for_subscription;
    if ( 'on' == orddd_params.orddd_subscriptions_settings && "undefined" != typeof orddd_number_of_dates_for_subscription ) {
        if( end_date > renewal_date ) {
            end_date = renewal_date;
        }
    }
    
    end = (end_date.getMonth()+1) + "/" + end_date.getDate() + "/" + end_date.getFullYear();
    
    var specific_max_date = start;
    var m = todays_date.getMonth(),
        d = todays_date.getDate(),
        y = todays_date.getFullYear();
    var currentdt = m + '-' + d + '-' + y;
    
    var dt             = new Date();
    var today          = dt.getMonth() + '-' + dt.getDate() + '-' + dt.getFullYear();
    var specific_dates = orddd_get_specific_dates();

     if ( '' != specific_dates ) {
        
        var deliveryDates = JSON.parse( '[' + specific_dates + ']');
        
        for ( ii = 0; ii < deliveryDates.length; ii++ ) {
            if( deliveryDates[ ii ] != '' && typeof( deliveryDates[ ii ] ) != 'undefined' ) {
                var split           = deliveryDates[ ii ].split( '-' );
                var specific_date   = split[ 0 ] + '/' + split[ 1 ] + '/' + split[ 2 ];
            } else {
                var specific_date   = new Date();
            }
            var diff            = gd( specific_max_date , specific_date , 'days' );
            if ( diff >= 0 && ii < noOfDaysToFind ) {
                specific_max_date = specific_date;
            }
        }
    }
    
    var loopCounter = gd( start , end , 'days' );
    var prev        = delay_days;
    var new_l_end, is_holiday;
    var ex_limit = 0;

    for( var i = 1; i <= loopCounter; i++ ) {
        
        ex_limit++;    
        var l_start     = new Date( start );
        var l_end       = new Date( end ); 
        new_l_end       = l_end;

        var new_date    = new Date( ad( l_start, i ) );

        // If recurring days enable or not for this date
        var day         = "";
        day             = 'orddd_weekday_' + new_date.getDay();
        day_check       = orddd_day_check( day );

        // If specific date enable or not for this date
        is_specific     = sp( new_date );

        // If holiday enable or not for this date
        is_holiday      = nd( new_date );

        var increment_date = false;

        if ( is_holiday != "true" ) {
            increment_date = true;
        } else {
            if ( day_check != "checked" ) {                
                if ( is_specific == "false" ) {
                    increment_date = true;
                }
            }
        }

        var orddd_number_of_dates_for_subscription = ( '1' == orddd_params.orddd_is_account_page ) ? orddd_account_params.orddd_number_of_dates_for_subscription : orddd_params.orddd_number_of_dates_for_subscription;

        if ( 'on' == orddd_params.orddd_subscriptions_settings && "undefined" != typeof orddd_number_of_dates_for_subscription ) {
            break;
        }
           

        if ( increment_date ) {
            new_l_end   = l_end = new Date( ad( l_end, 2 ) );
            end         = ( l_end.getMonth()+1) + "/" + l_end.getDate() + "/" + l_end.getFullYear();
            loopCounter = gd( start , end , 'days' );
        }

        if ( ex_limit >= 365 ) { // This check is to break infinite execution of this loop.
            break;
        }
    }
    
    var maxMonth            = new_l_end.getMonth()+1;
    var maxYear             = new_l_end.getFullYear();
    var number_of_months    = parseInt( orddd_params.orddd_number_of_months );

    if ( "1" == orddd_params.orddd_is_admin ) {
        return {
            minDate: '',
            maxDate: '',
            numberOfMonths: number_of_months             
        };
    } else {
        if ( maxMonth > start_month || maxYear > start_year ) {
            return {
                minDate: new Date(start),
                maxDate: l_end,
                numberOfMonths: number_of_months 
            };
        }
        else {
            return {
                minDate: new Date(start),
                maxDate: l_end,
                numberOfMonths: number_of_months                 
            };
        }
    }
}

/**
 * This function is called to find the end date to be set in the calendar.
 *
 * @function sp
 * @memberof orddd_initialize_functions
 * @param {object} dateObj
 * @returns {bool} returns true if passed date is specific date else false
 * @since 8.x
 */

function sp( date ) {
    var m              = date.getMonth(), d = date.getDate(), y = date.getFullYear();
    var specific_dates = orddd_get_specific_dates();
    var deliveryDates  = JSON.parse( '[' + specific_dates + ']' );

    for ( ii = 0; ii < deliveryDates.length; ii++) {
        if ( jQuery.inArray( (m+1) + '-' + d + '-' + y, deliveryDates) != -1 ) {
            return [true];
        }
    }
    return [false];
}

/**
 * This function is called to find the end date to be set in the calendar.
 *
 * @function ad
 * @memberof orddd_initialize_functions
 * @param {object} dateObj
 * @param {number} numDays - number of dates to choose
 * @returns {number} returns the end date to be set in the calendar
 * @since 1.0
 */
function ad( dateObj, numDays ) {
    return dateObj.setDate( dateObj.getDate() + ( numDays - 1 ) );
}

/**
 * This function is called to find the difference between the two dates.
 *
 * @function gd
 * @memberof orddd_initialize_functions
 * @param {string} date1 - start date
 * @param {string} date2 - end date
 * @param {string} interval - days
 * @returns {number} returns the number between two dates.
 * @since 1.0
 */
function gd( date1, date2, interval ) {
    var second = 1000,
    minute = second * 60,
    hour = minute * 60,
    day = hour * 24,
    week = day * 7;
    
    date1 = new Date( date1 ).getTime();
    date2 = ( date2 == 'now' ) ? new Date().getTime() : new Date( date2 ).getTime();
    
    var timediff = date2 - date1;
    if ( isNaN( timediff ) ) return NaN;
        switch ( interval ) {
        case "years":
            return date2.getFullYear() - date1.getFullYear();
        case "months":
            return ( (date2.getFullYear() * 12 + date2.getMonth() ) - ( date1.getFullYear() * 12 + date1.getMonth() ) );
        case "weeks":
            return Math.floor( timediff / week );
        case "days":
            return ( Math.floor( timediff / day ) ) + 1;
        case "hours":
            return Math.floor( timediff / hour );
        case "minutes":
            return Math.floor( timediff / minute );
        case "seconds":
            return Math.floor( timediff / second );
        default:
            return undefined;
    }
}

/**
 * This function is called when Same day or Next day is enabled.
 *
 * @function maxdt
 * @memberof orddd_initialize_functions
 * @param {string} date - Date
 * @returns {array} returns max date, min date and number of months.
 * @since 1.0
 */
function maxdt( date, inst ) {
    // Added to not translate the calendar when the site is translated using Google Translator. 
    if( typeof( inst ) != 'undefined' ) {
        inst.dpDiv.addClass( 'notranslate' );
    }
    var if_subscription = "no";
    if ( 'on' == orddd_params.orddd_subscriptions_settings ) {
        var orddd_number_of_dates_for_subscription = ( '1' == orddd_params.orddd_is_account_page ) ? orddd_account_params.orddd_number_of_dates_for_subscription : orddd_params.orddd_number_of_dates_for_subscription;
        var orddd_start_date_for_subscription = ( '1' == orddd_params.orddd_is_account_page ) ? orddd_account_params.orddd_start_date_for_subscription : orddd_params.orddd_start_date_for_subscription;

        if ( typeof orddd_start_date_for_subscription != "undefined" && typeof orddd_number_of_dates_for_subscription != "undefined" ) {
            var start_date = orddd_start_date_for_subscription;
            if( start_date != '' && typeof( start_date ) != 'undefined' ) {
                var split_date = start_date.split( '-' );
                var min_date = new Date ( split_date[1] + '/' + split_date[0] + '/' + split_date[2] );
            } else {
                var min_date = new Date();
            }
            
            var noOfDaysToFind = parseInt( orddd_number_of_dates_for_subscription );
                        
            if ( isNaN( min_date ) ) {
                min_date = new Date();
            }
            
            if( isNaN( noOfDaysToFind ) || 0 === noOfDaysToFind ) {
                noOfDaysToFind = 1000;
            }
            if_subscription = 'yes';
        }   

        if( jQuery( "#renewal_delivery_date" ).val() !== undefined ) {
            
            var renewal_date = jQuery( "#renewal_delivery_date" ).val();
            var split_date = renewal_date.split( '-' );
            min_date = new Date ( split_date[1] + '/' + split_date[0] + '/' + split_date[2] );

            if ( isNaN( min_date ) ) {
                min_date = new Date();
            }

            if_subscription = 'yes';

        }
    }
    
    if( if_subscription == "no" ) {
        
        var current_date = orddd_get_current_day();
        if( current_date != '' && typeof( current_date ) != 'undefined' ) {
            var split_current_date = current_date.split( '-' );
            var current_day = new Date ( split_current_date[ 1 ] + '/' + split_current_date[ 0 ] + '/' + split_current_date[ 2 ]);
        } else {
            var current_day = new Date();
        }

        var delay_date = orddd_get_min_date();
        if( delay_date != '' && typeof( delay_date ) != 'undefined' ) {
            var split_delay_date = delay_date.split( '-' );
            var min_date = new Date ( split_delay_date[ 1 ] + '/' + split_delay_date[ 0 ] + '/' + split_delay_date[ 2 ]);
        } else {
            var min_date = new Date();
        }

        if( "on" != orddd_params['custom_settings'].orddd_custom_based_same_day_delivery && "on" == orddd_params['custom_settings'].orddd_custom_based_next_day_delivery ) {
            if( min_date.getTime() == current_day.getTime() ) {
                min_date.setDate( min_date.getDate()+1 );    
            }
        } else if( typeof orddd_params['custom_settings'].orddd_custom_based_same_day_delivery == "undefined" && typeof orddd_params['custom_settings'].orddd_custom_based_next_day_delivery == "undefined" ) {
            if( 'on' != orddd_params.orddd_same_day_delivery && 'on' == orddd_params.orddd_next_day_delivery ) {
                if( min_date.getTime() == current_day.getTime() ) {
                    min_date.setDate( min_date.getDate()+1 );
                }
            }
        }
        
        var noOfDaysToFind =  orddd_get_number_of_dates();
        if( isNaN( min_date ) ) {
            min_date = new Date();
        }
        if( isNaN( noOfDaysToFind ) || 0 === noOfDaysToFind ) {
            noOfDaysToFind = 1000;
        }
    }
        
    min_date = same_day_next_day_to_set( min_date );

    if( min_date == "" ) {
        var delay_date = orddd_get_current_day();
        if( delay_date != '' && typeof( delay_date ) != 'undefined' ) {
            var split_current_date = delay_date.split( '-' );
            var min_date = new Date ( split_current_date[ 1 ] + '/' + split_current_date[ 0 ] + '/' + split_current_date[ 2 ]);
        } else {
            var min_date = new Date();
        }

        if( "on" != orddd_params['custom_settings'].orddd_custom_based_same_day_delivery && "on" == orddd_params['custom_settings'].orddd_custom_based_next_day_delivery ) {
            min_date.setDate( min_date.getDate()+1 );
        } else if( typeof orddd_params['custom_settings'].orddd_custom_based_same_day_delivery == "undefined" && typeof orddd_params['custom_settings'].orddd_custom_based_next_day_delivery == "undefined" ) {
            if( 'on' != orddd_params.orddd_same_day_delivery && 'on' == orddd_params.orddd_next_day_delivery ) {
                min_date.setDate( min_date.getDate()+1 );
            }
        }
        if( isNaN( min_date ) ) {
            min_date = new Date();
        }
    }
    
    var date = new Date();
    var t_year = date.getFullYear();
    var t_month = date.getMonth()+1;
    var t_day = date.getDate();
    var t_month_days = new Date( t_year, t_month, 0 ).getDate();
    
    start = ( min_date.getMonth()+1 ) + "/" + min_date.getDate() + "/" + min_date.getFullYear();
    var start_month = min_date.getMonth()+1;
    var start_year = min_date.getFullYear();
    
    var end_date = new Date( ad( min_date , noOfDaysToFind ) );
    end = ( end_date.getMonth()+1 ) + "/" + end_date.getDate() + "/" + end_date.getFullYear();

    var specific_max_date = start;
    var specific_dates    = orddd_get_specific_dates();
     if ( specific_dates != '' ) {
            var deliveryDates = JSON.parse( '[' + specific_dates + ']');
            for ( ii = 0; ii < deliveryDates.length; ii++ ) {
                if( deliveryDates[ ii ] != '' && typeof( deliveryDates[ ii ] ) != 'undefined' ) {
                    var split = deliveryDates[ ii ].split( '-' );
                    var specific_date = split[ 0 ] + '/' + split[ 1 ] + '/' + split[ 2 ];
                } else {
                    var specific_date = '';
                }
                var diff = gd( specific_max_date , specific_date , 'days');
                if ( diff >= 0 && ii < noOfDaysToFind ) {
                    specific_max_date = specific_date;
                }
            }
    }
    var loopCounter = gd( start , end , 'days' );
    var prev = min_date;
    var new_l_end, is_holiday;
    var ex_limit = 0;
    for( var i = 1; i <= loopCounter; i++ ) {
        ex_limit++;
        
        var l_start = new Date( start );
        var l_end = new Date( end );
        new_l_end = l_end;
        var new_date = new Date( ad( l_start, i ) );

        var day = "";
        day = 'orddd_weekday_' + new_date.getDay();
        day_check = orddd_day_check( day );
        is_holiday = nd( new_date );
        is_specific = sp( new_date );

        var increment_date = false;
        if ( is_holiday != "true" ) {
            increment_date = true;
        } else {
            if ( day_check != "checked" && is_specific == "false" ) {                
                increment_date = true;
            }
        }

        if( increment_date ) {
            new_l_end = l_end = new Date( ad( l_end,2 ) );
            end = ( l_end.getMonth()+1 ) + "/" + l_end.getDate() + "/" + l_end.getFullYear();
            loopCounter = gd( start, end , 'days' );
        }

        if ( ex_limit >= 365 ) { // This check is to break infinite execution of this loop.
            break;
        }
    }
    
    var maxMonth = new_l_end.getMonth()+1;
    var maxYear = new_l_end.getFullYear();
    var number_of_months = parseInt( orddd_params.orddd_number_of_months );
    if ( "1" == orddd_params.orddd_is_admin ) {
        return {
            minDate: '',
            maxDate: '',
            numberOfMonths: number_of_months             
        };
    } else {
        if ( maxMonth > start_month || maxYear > start_year ) {
            return {
                minDate: new Date( start ),
                maxDate: l_end,
                numberOfMonths: number_of_months 
            };
        } else {
            return {
                minDate: new Date( start ),
                maxDate: l_end,
                numberOfMonths: number_of_months                 
            };
        }
    }
}

/**
 * Sorts the Specific dates
 *
 * @function sortSpecificDates
 * @memberof orddd_initialize_functions
 * @param {array} value_1 - Date
 * @param {array} value_2 - Date
 * @returns {array} returns the sorted array.
 * @since 4.6
 */
function sortSpecificDates( value_1 , value_2 ) {
    return value_1 - value_2;
}

/**
 * Auto populates the first available delivery date on the Delivery Date field
 *
 * @function orddd_autofil_date_time
 * @memberof orddd_initialize_functions
 * @since 4.6
 */
function orddd_autofil_date_time( load_dates = 'yes' ) {
    var checkout_options = orddd_get_checkout_options();

    if( 'no' == orddd_params.orddd_is_shipping_text_block || ( ( '' == orddd_params.orddd_is_shipping_text_block || 'undefined' == typeof orddd_params.orddd_is_shipping_text_block ) && 'delivery_calendar' == checkout_options ) )  {
        var current_date = orddd_get_current_day();
        if( current_date != '' && typeof( current_date ) != 'undefined' ) {
            var split_current_date = current_date.split( "-" );
            var current_day = new Date ( split_current_date[ 1 ] + "/" + split_current_date[ 0 ] + "/" + split_current_date[ 2 ] );
        } else {
            var current_day = new Date();
        }

        var if_subscription = 'no';
        if ( 'on' == orddd_params.orddd_subscriptions_settings ) {
            var orddd_number_of_dates_for_subscription = ( '1' == orddd_params.orddd_is_account_page ) ? orddd_account_params.orddd_number_of_dates_for_subscription : orddd_params.orddd_number_of_dates_for_subscription;
            var orddd_start_date_for_subscription = ( '1' == orddd_params.orddd_is_account_page ) ? orddd_account_params.orddd_start_date_for_subscription : orddd_params.orddd_start_date_for_subscription;
            
            if ( typeof orddd_start_date_for_subscription != "undefined" && typeof orddd_number_of_dates_for_subscription != "undefined" ) {
                var start_date = orddd_params.orddd_start_date_for_subscription;
                if( start_date != '' && typeof( start_date ) != 'undefined' ) {
                    var split_date = start_date.split( '-' );
                    var delay_days = new Date ( split_date[1] + '/' + split_date[0] + '/' + split_date[2] );
                } else {
                    var delay_days = new Date();
                }
                if_subscription = 'yes';
            }
        }
        
        if( if_subscription == 'no' ) {
            var delay_date = orddd_get_min_date();
            if( delay_date != "" && typeof( delay_date ) != 'undefined' ) {
                var split_date = delay_date.split( "-" );
                var delay_days = new Date ( split_date[ 1 ] + "/" + split_date[ 0 ] + "/" + split_date[ 2 ] );
            } else {
                var delay_days = current_day;
            }
        }

        if ( isNaN( delay_days ) ) {
            delay_days = new Date();
            delay_days.setDate( delay_days.getDate()+1 );
        }
        
        delay_days = orddd_get_first_available_date( delay_date, delay_days );
        if ( 'on' == orddd_params.orddd_subscriptions_settings ) {
            var orddd_number_of_dates_for_subscription = ( '1' == orddd_params.orddd_is_account_page ) ? orddd_account_params.orddd_number_of_dates_for_subscription : orddd_params.orddd_number_of_dates_for_subscription;
            var orddd_start_date_for_subscription = ( '1' == orddd_params.orddd_is_account_page ) ? orddd_account_params.orddd_start_date_for_subscription : orddd_params.orddd_start_date_for_subscription;

            if ( typeof orddd_start_date_for_subscription != "undefined" ) {
                var subscription_date = orddd_start_date_for_subscription;
                if( subscription_date != '' && typeof( subscription_date ) != 'undefined' ) {
                    var split_subscription_date = subscription_date.split( "-" );
                    delay_days = new Date ( split_subscription_date[ 1 ] + "/" + split_subscription_date[ 0 ] + "/" + split_subscription_date[ 2 ] );
                } else {
                    delay_days = new Date();
                }
            }

            if( jQuery( "#renewal_delivery_date" ).val() !== undefined ) {
                var renewal_date = jQuery( "#renewal_delivery_date" ).val();
                var split_date = renewal_date.split( '-' );
                delay_days = new Date ( split_date[1] + '/' + split_date[0] + '/' + split_date[2] );
    
                if ( isNaN( delay_days ) ) {
                    delay_days = new Date();
                }
    
                if_subscription = 'yes';
            }
        }

        var min_date_to_set = '';
        if( "" != delay_days ) {
            min_date_to_set = delay_days.getDate() + "-" + ( delay_days.getMonth()+1 ) + "-" + delay_days.getFullYear();
        }

        if ( '' != orddd_params.orddd_first_autofil_delivery_date ) {
            min_date_to_set = orddd_params.orddd_first_autofil_delivery_date;
            var autofil_date_split = min_date_to_set.split( "-" );
            delay_days = new Date ( autofil_date_split[ 1 ] + "/" + autofil_date_split[ 0 ] + "/" + autofil_date_split[ 2 ] );
        }

        if( 'on' == orddd_params['custom_settings'].orddd_enable_shipping_delivery_date || 'undefined' == typeof orddd_params['custom_settings'].orddd_enable_shipping_delivery_date ) {
            var date_to_set = delay_days;
            jQuery( '#' + orddd_params.orddd_field_name ).datepicker( "setDate", date_to_set );
            jQuery( "#h_deliverydate" ).val( min_date_to_set );
            var inst = jQuery.datepicker._getInst( jQuery( '#' + orddd_params.orddd_field_name )[0] );

            if ( 'yes' === load_dates ) {
                if( 'yes' == jsL10n.is_dropdown_field ) {
                    orddd_set_date_dropdown_from_session();
                } else {
                    orddd_set_date_from_session(); 
                }
            }
        }
    }
}

/**
 * Loads the Delivery information from the Local storage. 
 * This function is called when the Cart or Checkout page is first loaded
 *
 * @function load_functions
 * @memberof orddd_initialize_functions
 * @since 7.0
 */
function load_functions() {
    jQuery( '#' + orddd_params.orddd_field_name ).prop( "disabled", false );

    var orddd_location_session = localStorage.getItem( 'orddd_location_session' );    
    if( typeof( orddd_location_session ) != 'undefined' && orddd_location_session != '' && orddd_location_session != 'null' ) {
        jQuery( "#orddd_locations" ).val( orddd_location_session ).trigger( "change" );    
    }
    
    orddd_update_delivery_session();
}

/**
 * Concatenation of options for jQuery datepicker
 *
 * @function jsonConcat
 * @memberof orddd_initialize_functions
 * @param {string} o1 - Options of datepicker
 * @param {string} o2 - Options of datepicker
 * @returns {string} o1 - Concatenation of two Options o1 and o2
 * @since 1.0
 */
function jsonConcat( o1, o2 ) {
    for ( var key in o2 ) {
        o1[ key ] = o2[ key ];
    }
    return o1;
}

/**
 * Calculates the Minimum date to be set in the calendar where holiday, lockout, 
 * specific dates, Minimum Delivery time etc., is considered.
 *
 * @function minimum_date_to_set
 * @memberof orddd_initialize_functions
 * @param {object} delay_days - Delay Day
 * @returns {object} delay_days - Delay Days
 * @since 1.0
 */
function minimum_date_to_set( delay_days ) {
    var holidays_str = orddd_get_holidays();
    var disabledDays = JSON.parse( "[" + holidays_str + "]" );
    var holidays = [];
    for ( i = 0; i < disabledDays.length; i++ ) {
        if( disabledDays[ i ] != '' && typeof( disabledDays[ i ] ) != 'undefined' ) {
            var holidays_array = disabledDays[ i ].split( ":" );
            holidays[i] = holidays_array[ 1 ];
        }
    }

    var lockout_days = orddd_get_lockout_days();
    var bookedDays   = JSON.parse( "[" + lockout_days + "]" );
    var current_date = orddd_get_current_day();
    if( current_date != '' && typeof( current_date ) != 'undefined' ) {
        var split_current_date = current_date.split( "-" );
        var current_day = new Date ( split_current_date[ 1 ] + "/" + split_current_date[ 0 ] + "/" + split_current_date[ 2 ] );
    } else  {
        var current_day = new Date();
    }
    
    var delay_time = delay_days.getTime();
    var current_time = current_day.getTime();
    var current_weekday = current_day.getDay();
    
    var delivery_day_3 = '';
    var specific_dates_sorted_array = new Array ();
    var specific_dates_enabled = orddd_specific_dates_enabled();
    var deliveryDates = [];

    var is_all_past_dates = 'No';
    var is_all_holidays = 'No';
    var is_all_booked_days = 'No';

    var past_dates = [];
    var highest_delivery_date = [];
    var specific_days_in_holidays = 0;
    var specific_days_in_booked_days = 0;
    var orddd_all_weekdays_disabled = orddd_is_all_weekdays_disabled();
    var all_common_days_disabled = orddd_params['custom_settings'].orddd_common_days_disabled;
    if ( "on" == specific_dates_enabled ) {
        var specific_dates = orddd_get_specific_dates();
        if ( '' != specific_dates ) {
            deliveryDates = JSON.parse( '[' + specific_dates + ']');
            for ( sort = 0; sort < deliveryDates.length; sort++ ) {
                if( deliveryDates[sort] != '' && typeof( deliveryDates[sort] ) != 'undefined' ) {
                    var split_delivery_date_1 = deliveryDates[sort].split( "-" );
                    var delivery_day_1 = new Date ( split_delivery_date_1[ 0 ] + "/" + split_delivery_date_1[ 1 ] + "/" + split_delivery_date_1[ 2 ] );
                    specific_dates_sorted_array[sort] = delivery_day_1.getTime();
                }
            }
            
            highest_delivery_date = specific_dates_sorted_array[ specific_dates_sorted_array.length - 1 ];

            specific_dates_sorted_array.sort( sortSpecificDates );
            for ( i = 0; i < specific_dates_sorted_array.length; i++ ) {
                if ( specific_dates_sorted_array[i] >= current_day.getTime() ){
                    delivery_day_3 = specific_dates_sorted_array[i];
                    break;
                }
            }
            
            for ( j = 0; j < deliveryDates.length; j++ ) {
                if( deliveryDates[j] != '' && typeof( deliveryDates[j] ) != 'undefined' ) {
                    var split_delivery_date = deliveryDates[j].split( "-" );
                    var delivery_date = new Date ( split_delivery_date[ 0 ] + "/" + split_delivery_date[ 1 ] + "/" + split_delivery_date[ 2 ] );
                    if ( delivery_date.getTime() >= current_day.getTime() ){
                        past_dates[j] = deliveryDates[j];
                    }

                    if( jQuery.inArray( deliveryDates[j], holidays ) >= 0 ) {
                        specific_days_in_holidays++;
                    } else if( jQuery.inArray( deliveryDates[j], holidays ) == -1 && jQuery.inArray( deliveryDates[j], past_dates ) == -1 ) {
                        specific_days_in_holidays++;
                    }

                    if( jQuery.inArray( deliveryDates[j], bookedDays ) >= 0 ) {
                        specific_days_in_booked_days++;
                    } else if( jQuery.inArray( deliveryDates[j], bookedDays ) == -1 && jQuery.inArray( deliveryDates[j], past_dates ) == -1 ) {
                        specific_days_in_booked_days++;
                    }
                }
            }       

            if( past_dates.length == 0 ) {
                is_all_past_dates = 'Yes';
            }

            if( specific_days_in_holidays == deliveryDates.length ) {
                is_all_holidays = 'Yes';
            }

            if( specific_days_in_booked_days == deliveryDates.length ) {
                is_all_booked_days = 'Yes';
            }
        } else {
            is_all_past_dates = 'Yes';
        }
    }

    var j;
    if( 'on' == orddd_params.orddd_enable_shipping_days ) {
        var delay_weekday = delay_days.getDay();

        var delay_date_to_check = orddd_get_min_date();
        if( delay_date_to_check != "" && typeof( delay_date_to_check ) != 'undefined' ) {
            var split_date_to_check = delay_date_to_check.split( "-" );
            var delay_days_to_check = new Date ( split_date_to_check[ 1 ] + "/" + split_date_to_check[ 0 ] + "/" + split_date_to_check[ 2 ] );
        }

        var delay_time_to_check = delay_days_to_check.getTime();
        var delay_weekday_to_check = delay_days_to_check.getDay();
        for ( j = delay_weekday_to_check ; delay_time_to_check <= delay_time ; j++ ) {
            if( j >= 0 ) {
                day = "orddd_weekday_" + delay_weekday_to_check;
                day_check = orddd_day_check( day );
                if ( day_check == "" || typeof day_check == "undefined" ) {
                    var increment_delay_day = 'no';
                    if ( "on" == specific_dates_enabled  ) {
                        if( ( 'Yes' ==  is_all_past_dates || 'Yes' == is_all_holidays || 'Yes' == is_all_booked_days ) && ( 'yes' == orddd_all_weekdays_disabled || 'yes' == all_common_days_disabled) ) {
                            delay_days = '';
                            break;
                        } else if( ( 'Yes' ==  is_all_past_dates || 'Yes' == is_all_holidays || 'Yes' == is_all_booked_days ) && 'no' == orddd_all_weekdays_disabled ) {
                            increment_delay_day = 'yes';
                        } else {
                            var m = delay_days.getMonth(), d = delay_days.getDate(), y = delay_days.getFullYear();
                            if( jQuery.inArray( ( m+1 ) + "-" + d + "-" + y, deliveryDates ) == -1 && delay_days.getTime() < highest_delivery_date && 'yes' == orddd_all_weekdays_disabled ) {
                                increment_delay_day = 'yes';
                            } else if ( typeof delivery_day_3 != "undefined" && delivery_day_3 != '' && delay_days != '' ) {
                                if( delivery_day_3 != delay_days.getTime() && delay_days.getTime() < delivery_day_3 ) {
                                    delay_days.setDate( delay_days.getDate()+1 );
                                    delay_time = delay_days.getTime();
                                    delay_weekday = delay_days.getDay();
                                } else {
                                    if ( 'yes' != orddd_params.orddd_disable_for_holidays ) {
                                        if( jQuery.inArray( ( m+1 ) + "-" + d + "-" + y, holidays ) != -1 || 
                                            jQuery.inArray( ( m+1 ) + "-" + d, holidays ) != -1 || 
                                            jQuery.inArray( ( m+1 ) + "-" + d + "-" + y, bookedDays ) != -1 ) {
                                            delay_days.setDate( delay_days.getDate()+1 );
                                            delay_time = delay_days.getTime();
                                            delay_weekday = delay_days.getDay();
                                        } else {
                                            if( 'yes' == orddd_all_weekdays_disabled && delay_days.getTime() > highest_delivery_date ) {
                                                delay_days = '';
                                            }
                                            break;
                                        }
                                    } else {
                                        if( 'yes' == orddd_all_weekdays_disabled && delay_days.getTime() > highest_delivery_date ) {
                                            delay_days = '';
                                        }
                                        break;    
                                    }
                                    
                                }
                            } else {
                                if( 'yes' == orddd_all_weekdays_disabled && delay_days.getTime() > highest_delivery_date ) {
                                    delay_days = '';
                                }
                                break;
                            }
                        }
                    } else {
                        increment_delay_day = 'yes';
                    }
                    if( 'yes' == increment_delay_day ) {
                        delay_days.setDate( delay_days.getDate()+1 );
                        delay_time = delay_days.getTime();
                        delay_days_to_check.setDate( delay_days_to_check.getDate()+1 );
                        delay_time_to_check = delay_days_to_check.getTime();
                        delay_weekday_to_check = delay_days_to_check.getDay();
                    }
                } else {
                    if( delay_days_to_check <= delay_days ) {
                        var m = delay_days_to_check.getMonth(), d = delay_days_to_check.getDate(), y = delay_days_to_check.getFullYear();
                        if ( 'yes' != orddd_params.orddd_disable_for_holidays ) {
                            if( jQuery.inArray( ( m+1 ) + "-" + d + "-" + y, holidays ) != -1 || jQuery.inArray( ( m+1 ) + "-" + d, holidays ) != -1 || jQuery.inArray( ( m+1 ) + "-" + d + "-" + y, bookedDays ) != -1 ) {
                                delay_days.setDate( delay_days.getDate()+1 );
                                delay_time = delay_days.getTime();
                            }
                        }
                        delay_days_to_check.setDate( delay_days_to_check.getDate()+1 );
                        delay_time_to_check = delay_days_to_check.getTime();
                        delay_weekday_to_check = delay_days_to_check.getDay();
                    }
                }
            } else {
                break;
            }
        }
    } 
    
    if( delay_days != '' ) {
        var dm = delay_days.getMonth(), dd = delay_days.getDate(), dy = delay_days.getFullYear();
        if( jQuery.inArray( ( dm+1 ) + "-" + dd + "-" + dy, holidays ) != -1 || jQuery.inArray( ( dm+1 ) + "-" + dd, holidays ) != -1 ) {
            delay_days.setDate( delay_days.getDate()+1 );
            delay_time = delay_days.getTime();
        }

        if( jQuery.inArray( ( dm+1 ) + "-" + dd + "-" + dy, bookedDays ) != -1 ) {
            delay_days.setDate( delay_days.getDate()+1 );
            delay_time = delay_days.getTime();
        } 

        var delay_weekday = delay_days.getDay();
    }

    var common_delivery_days = [];
    if( typeof( orddd_params.orddd_common_delivery_days_for_product_category ) !== "undefined" && '' != orddd_params.orddd_common_delivery_days_for_product_category ) {
        common_delivery_days_str = orddd_params.orddd_common_delivery_days_for_product_category;
        common_delivery_days = jQuery.parseJSON( common_delivery_days_str );
    }

    var specific_dates = [];
    if( "undefined" !== typeof( orddd_params.orddd_common_delivery_dates_for_product_category ) && '' !== orddd_params.orddd_common_delivery_dates_for_product_category ) {
        specific_dates = JSON.parse( '[' + orddd_params.orddd_common_delivery_dates_for_product_category + ']' );
    }

    var disabled_common_days = [];
    if( "undefined" !== typeof( orddd_params.orddd_common_holidays_for_product_category ) && '' != orddd_params.orddd_common_holidays_for_product_category ) {
        disabled_common_days = JSON.parse( '[' + orddd_params.orddd_common_holidays_for_product_category + ']' );
    }

    for( i = 0; ;i++ ) {
        if( delay_days != '' ) {
            var dm = delay_days.getMonth(), dd = delay_days.getDate(), dy = delay_days.getFullYear();
            var delay_weekday = delay_days.getDay();
            day         = 'orddd_weekday_' + delay_weekday;
            day_check   = orddd_day_check( day );

            if( jQuery.inArray( ( dm+1 ) + "-" + dd + "-" + dy, holidays ) != -1 || jQuery.inArray( ( dm+1 ) + "-" + dd, holidays ) != -1 || ( jQuery.inArray( ( dm+1 ) + "-" + dd + "-" + dy, bookedDays ) != -1 ) ) {
                delay_days.setDate( delay_days.getDate()+1 );
                delay_time = delay_days.getTime();
            } else if( 'yes' == orddd_params.orddd_is_days_common && 
                ( ( specific_dates.length > 0 && 
                        jQuery.inArray( ( dm+1 ) + "-" + dd + "-" + dy, specific_dates ) == -1 && 
                        is_all_past_dates != 'Yes' ) || 
                    specific_dates.length == 0 
                ) && 
                ( jQuery.isEmptyObject( common_delivery_days ) == true || 
                    ( jQuery.isEmptyObject( common_delivery_days ) == false && 
                        !common_delivery_days.hasOwnProperty( "orddd_weekday_" + delay_weekday ) 
                    ) 
                ) 
            ) {
                delay_days.setDate( delay_days.getDate()+1 );
            } else if( 'yes' == orddd_params.orddd_categories_settings_common && 'no' == orddd_params.orddd_is_days_common ) {
                delay_days = '';
            } else if( 'yes' == orddd_all_weekdays_disabled && 
                delay_days.getTime() < highest_delivery_date && 
                jQuery.inArray( ( dm+1 ) + "-" + dd + "-" + dy, deliveryDates ) == -1 ) {
                delay_days.setDate( delay_days.getDate()+1 );
            } else if( ( 'Yes' ==  is_all_past_dates || 
                    'Yes' == is_all_holidays || 
                    'Yes' == is_all_booked_days ) && 
                'yes' == orddd_all_weekdays_disabled ) {
                delay_days = '';  
                break;
            } else if( ( day_check == '' && jQuery.inArray( ( dm+1 ) + "-" + dd + "-" + dy, deliveryDates ) == -1 ) ) {
                delay_days.setDate( delay_days.getDate()+1 );
                delay_time = delay_days.getTime();
            } else {
                break;
            }
        } else {
            break;
        }
    }
    
    return delay_days;
}

/**
 * Calculates the date to be set in the calendar after the checking the Same day and Next day cut-off time.
 *
 * @function same_day_next_day_to_set
 * @memberof orddd_initialize_functions
 * @param {object} current_day - Current Day
 * @returns {object} current_day - Date to be set in the calendar
 * @since 1.0
 */
function same_day_next_day_to_set( current_day ) {
    var orddd_disabled_days_str     = orddd_get_disabled_days();
    var orddd_disabled_weekdays_str = orddd_get_disabled_weekdays();
    var holidays_str                = orddd_get_holidays();
    var lockout_days                = orddd_get_lockout_days();
    var startDaysDisabled           = JSON.parse( "[" + orddd_disabled_days_str + "]" );
    var startWeekDaysDisabled       = JSON.parse( "[" + orddd_disabled_weekdays_str + "]" );
    var disabledDays                = JSON.parse( "[" + holidays_str + "]" );
    var bookedDays                  = JSON.parse( "[" + lockout_days + "]" );
    var holidays                    = [];

    for ( i = 0; i < disabledDays.length; i++ ) {
        if( disabledDays[ i ] != '' && typeof( disabledDays[ i ] ) != 'undefined' ) {
            var holidays_array = disabledDays[ i ].split( ":" );
            holidays[i] = holidays_array[ 1 ];
        }
    }
    
    var delivery_day_3 = '';
    var specific_dates_sorted_array = new Array();
    var specific_dates_enabled      = orddd_specific_dates_enabled();
    var orddd_all_weekdays_disabled = orddd_is_all_weekdays_disabled();
    var all_common_days_disabled    = orddd_params['custom_settings'].orddd_common_days_disabled;
    var is_all_past_dates = 'No';
    var is_all_holidays = 'No';
    var is_all_booked_days = 'No';
    var is_sameday_cutoff_reached = orddd_same_day_cutoff_reached();
    var is_nextday_cutoff_reached = orddd_next_day_cutoff_reached();
    if ( specific_dates_enabled == "on" ) {
        var specific_dates = orddd_get_specific_dates();
         if ( '' != specific_dates ) {
            var deliveryDates = JSON.parse( '[' + specific_dates + ']');
            for ( sort = 0; sort < deliveryDates.length; sort++ ) {
                if( deliveryDates[sort] != '' && typeof( deliveryDates[sort] ) != 'undefined' ) {
                    var split_delivery_date_1 = deliveryDates[sort].split( "-" );
                    var delivery_day_1 = new Date ( split_delivery_date_1[ 0 ] + "/" + split_delivery_date_1[ 1 ] + "/" + split_delivery_date_1[ 2 ] );
                    specific_dates_sorted_array[sort] = delivery_day_1.getTime();
                }
            }
            specific_dates_sorted_array.sort( sortSpecificDates );
            for ( i = 0; i < specific_dates_sorted_array.length; i++ ) {
                if ( specific_dates_sorted_array[i] >= current_day.getTime() ){
                    delivery_day_3 = specific_dates_sorted_array[i];
                    break;
                }
            }   
			var highest_delivery_date = specific_dates_sorted_array[ specific_dates_sorted_array.length - 1 ];
            var past_dates = [];
            for ( j = 0; j < deliveryDates.length; j++ ) {
                if( deliveryDates[j] != '' && typeof( deliveryDates[j] ) != 'undefined' ) {
                    var split_delivery_date = deliveryDates[j].split( "-" );
                    var delivery_date = new Date ( split_delivery_date[ 0 ] + "/" + split_delivery_date[ 1 ] + "/" + split_delivery_date[ 2 ] );
                    if ( delivery_date.getTime() >= current_day.getTime() ){
                        past_dates[j] = deliveryDates[j];
                    }
                }
            }           
            if( past_dates.length == 0 ) {
                is_all_past_dates = 'Yes';
            }       
        } else {
            is_all_past_dates = 'Yes';
        }
    }
    
    if( current_day != '' ) {
        var current_weekday = current_day.getDay();
        var k;
        if( 'on' == orddd_params.orddd_next_day_delivery 
            && 'undefined' == typeof orddd_params['custom_settings'].orddd_custom_based_next_day_delivery
            && ( 'yes' == is_sameday_cutoff_reached || 'undefined' == typeof is_sameday_cutoff_reached ) ) {
            for ( k = current_weekday ; k <= 6; ) {
                if( 'yes' == is_nextday_cutoff_reached )  {
                    if( typeof( orddd_params.orddd_after_cutoff_weekday ) != "undefined" && orddd_params.orddd_after_cutoff_weekday != '' ) {
                        var weekday = "orddd_weekday_" + current_day.getDay();
                        var after_weekday = orddd_params.orddd_after_cutoff_weekday;
                        if( weekday != after_weekday ) {
                            current_day.setDate( current_day.getDate()+1 );
                            k = current_day.getDay();
                        } else {
                            break;
                        }
                    } else {
                        break;
                    }   
                } else {
                    if( typeof( orddd_params.orddd_before_cutoff_weekday ) != "undefined" && orddd_params.orddd_before_cutoff_weekday != '' ) {
                        var weekday = "orddd_weekday_" + current_day.getDay();
                        var before_weekday = orddd_params.orddd_before_cutoff_weekday;
                        if( weekday != before_weekday ) {
                            current_day.setDate( current_day.getDate()+1 );
                            k = current_day.getDay();
                        } else {
                            break;
                        }
                    } else {
                        break;
                    }
                }
            }
        }
    }
    
    var current_time = current_day.getTime();
    var current_weekday = current_day.getDay();
    var j;
   
    for ( j = current_weekday ;  j <= 6; ) {
        var m = current_day.getMonth(), d = current_day.getDate(), y = current_day.getFullYear();
        if( jQuery.inArray( ( m+1 ) + '-' + d + '-' + y, startDaysDisabled ) != -1 || 
            jQuery.inArray( ( m+1 ) + '-' + d + '-' + y, startWeekDaysDisabled ) != -1 ) {
            current_day.setDate( current_day.getDate()+1 );
            j = current_day.getDay();
        } else if( jQuery.inArray( ( m+1 ) + '-' + d + '-' + y, bookedDays ) != -1 ) {
            current_day.setDate( current_day.getDate()+1 );
            j = current_day.getDay();			
        } else if( jQuery.inArray( ( m+1 ) + "-" + d + "-" + y, holidays ) != -1 || jQuery.inArray( ( m+1 ) + "-" + d, holidays ) != -1 ) {
            current_day.setDate( current_day.getDate()+1 );
            j = current_day.getDay();
        } else {
            var shipping_day_check = '';
            if( 'on' == orddd_params.orddd_enable_shipping_days ) {
                shipping_day = 'orddd_weekday_' + j;
                shipping_day_check = orddd_params[shipping_day];
                if( typeof shipping_day_check == "undefined" ) {
                    shipping_day_check = '';
                }

                if( shipping_day_check == "" || typeof shipping_day_check == "undefined" ) {
                    if ( "on" == specific_dates_enabled ) {
                        if( is_all_past_dates != 'Yes' || ( 'Yes' ==  is_all_past_dates && 'no' == orddd_all_weekdays_disabled ) ) {
                            if( jQuery.inArray( ( m+1 ) + "-" + d + "-" + y, deliveryDates ) == -1 && current_day.getTime() < highest_delivery_date ) {
                                current_day.setDate( current_day.getDate()+1 );
                                j = current_day.getDay();
                            } else if ( typeof delivery_day_3 != "undefined" ) {
                                if ( delivery_day_3 != current_day.getTime() && current_day.getTime() < delivery_day_3 ) {
                                    current_day.setDate( current_day.getDate()+1 );
                                    j = current_day.getDay();
                                } else {
                                    break;
                                }
                            } else {
                                break;
                            }    
                        } else {
                            current_day.setDate( current_day.getDate()+1 );
                            j = current_day.getDay();    
                            break;
                        }
                    } else {
                        current_day.setDate( current_day.getDate()+1 );
                        j = current_day.getDay();
                    }   
                } else {
                    break;
                }
            } else {
                day = "orddd_weekday_" + j;
                day_check = orddd_day_check( day );
                if ( day_check == "" || typeof day_check == "undefined" ) {
                    var increment_delay_day = 'no';
                    if ( "on" == specific_dates_enabled ) {
                        if( 'Yes' ==  is_all_past_dates && ( 'yes' == orddd_all_weekdays_disabled || 'yes' == all_common_days_disabled ) ) {
                            current_day = '';
                            break;
                        } else if( 'Yes' ==  is_all_past_dates && 'no' == orddd_all_weekdays_disabled ) {
                            increment_delay_day = 'yes';
                        } else {
                            var m = current_day.getMonth(), d = current_day.getDate(), y = current_day.getFullYear();
							highest_delivery_date
                            if( jQuery.inArray( ( m+1 ) + "-" + d + "-" + y, deliveryDates ) == -1 && current_day.getTime() < highest_delivery_date ) {
                                increment_delay_day = 'yes';
                            } else if ( typeof delivery_day_3 != "undefined" && delivery_day_3 != '' ) {
                                if ( delivery_day_3 != current_day.getTime() && current_day.getTime() < delivery_day_3 && current_day.getTime() < highest_delivery_date ) {
                                     increment_delay_day = 'yes';
                                } else {
									if( 'yes' == orddd_all_weekdays_disabled && current_day.getTime() > highest_delivery_date ) {
                                        current_day = '';
                                        break;
									} else if( ( day_check == '' && jQuery.inArray( ( m+1 ) + "-" + d + "-" + y, deliveryDates ) == -1 ) ) {
                                        increment_delay_day = 'yes';
                                    } else {
                                        break;
                                    }
                                }
                            } else {
                                break;
                            }
                        }
                    } else {
                        increment_delay_day = 'yes';
                    }

                    if( 'yes' == increment_delay_day ) {
                        current_day.setDate( current_day.getDate()+1 );
                        j = current_day.getDay();
                    }
                } else {
                    break;
                }
            }
        } 
    }
	
    var common_delivery_days = [];
    if(  "undefined" !== typeof( orddd_params.orddd_common_delivery_days_for_product_category ) && '' != orddd_params.orddd_common_delivery_days_for_product_category ) {
        common_delivery_days_str = orddd_params.orddd_common_delivery_days_for_product_category;
        common_delivery_days     = jQuery.parseJSON( common_delivery_days_str );
    }

    var specific_dates = [];
    if( "undefined" !== typeof( orddd_params.orddd_common_delivery_dates_for_product_category ) && '' != orddd_params.orddd_common_delivery_dates_for_product_category ) {
        specific_dates = JSON.parse( '[' + orddd_params.orddd_common_delivery_dates_for_product_category + ']' );
    }

    var disabled_common_days = [];
    if( "undefined" !== typeof( orddd_params.orddd_common_holidays_for_product_category ) && '' != orddd_params.orddd_common_holidays_for_product_category ) {
        disabled_common_days = JSON.parse( '[' + orddd_params.orddd_common_holidays_for_product_category + ']' );
    }

    for( i = 0; ;i++ ) {
        if( current_day != '' ) {
            var dm = current_day.getMonth(), dd = current_day.getDate(), dy = current_day.getFullYear();
            var delay_weekday = current_day.getDay();
            day         = 'orddd_weekday_' + delay_weekday;
            day_check   = orddd_day_check( day );

            if( jQuery.inArray( ( dm+1 ) + "-" + dd + "-" + dy, holidays ) != -1 || jQuery.inArray( ( dm+1 ) + "-" + dd, holidays ) != -1 || ( jQuery.inArray( ( dm+1 ) + "-" + dd + "-" + dy, bookedDays ) != -1 ) ) {
                current_day.setDate( current_day.getDate()+1 );
                delay_time = current_day.getTime();
            } else if( 'yes' == orddd_params.orddd_is_days_common && 
                ( ( specific_dates.length > 0 && 
                        jQuery.inArray( ( dm+1 ) + "-" + dd + "-" + dy, specific_dates ) == -1 && 
                        is_all_past_dates != 'Yes' ) || 
                    specific_dates.length == 0 
                ) && 
                ( jQuery.isEmptyObject( common_delivery_days ) == true || 
                    ( jQuery.isEmptyObject( common_delivery_days ) == false && 
                        !common_delivery_days.hasOwnProperty( "orddd_weekday_" + delay_weekday ) 
                    ) 
                )
            ) {
                current_day.setDate( current_day.getDate()+1 );
                //jQuery( "#orddd_is_days_common" ).val('no');
                orddd_params.orddd_is_days_common = 'no';
            } else if( 'yes' == orddd_params.orddd_categories_settings_common && 'no' == orddd_params.orddd_is_days_common ) {
                    current_day = '';
                    break;
            } else if( 'yes' == orddd_all_weekdays_disabled && 
            current_day.getTime() < highest_delivery_date && 
                jQuery.inArray( ( dm+1 ) + "-" + dd + "-" + dy, deliveryDates ) == -1 ) {
                    current_day.setDate( current_day.getDate()+1 );
            } else if( ( 'Yes' ==  is_all_past_dates || 
                    'Yes' == is_all_holidays || 
                    'Yes' == is_all_booked_days ) && 
                'yes' == orddd_all_weekdays_disabled ) {
                current_day = '';  
                break;
            } else if( ( day_check == '' && jQuery.inArray( ( dm+1 ) + "-" + dd + "-" + dy, deliveryDates ) == -1 ) ) {
                current_day.setDate( current_day.getDate()+1 );
                delay_time = current_day.getTime();
            } else {
                break;
            }
        } else {
            break;
        }
    }
    return current_day;
}

/**
 * Shows the Global Time Slots
 *
 * @function show_times
 * @memberof orddd_initialize_functions
 * @param {date} date - Date
 * @param {object} inst 
 * @since 1.0
 */
function show_times( date, inst ) {
    var hourValue = jQuery( ".ui_tpicker_time" ).html();
    jQuery( "#orddd_time_settings_selected" ).val( hourValue );
    jQuery( document ).trigger( 'on_select_additional_action', [ date, inst ] );

    var monthValue = inst.selectedMonth+1;
    var dayValue = inst.selectedDay;
    var yearValue = inst.selectedYear;
    var all = dayValue + "-" + monthValue + "-" + yearValue;
    var date_YMD = yearValue + "-" + monthValue + "-" + dayValue;
    jQuery( "#h_deliverydate" ).val( all );
    jQuery( "#e_deliverydate" ).val(  jQuery( '#' + orddd_params.orddd_field_name ).val() );

    var min_date = orddd_params.orddd_min_date_set;

    if( "on" == orddd_params.orddd_enable_time_slot ) {
        if( typeof( inst.id ) !== "undefined" ) {
            orddd_get_time_slot_response( all, date_YMD, '0', [] );
        }
    } else if( "on" == orddd_params.orddd_enable_time_slider ) { 
        all = orddd_set_time_slider_range( 'no', date, inst );
    } else {
        jQuery( "#hidden_e_deliverydate" ).val( jQuery( '#' + orddd_params.orddd_field_name ).val() );
        jQuery( "#hidden_h_deliverydate" ).val( all );
        jQuery( "#hidden_timeslot" ).val( jQuery( "#orddd_time_slot" ).val() );
        jQuery( "#hidden_location" ).val( jQuery( "#orddd_locations" ).find(":selected").val() );
        jQuery( "body" ).trigger( "update_checkout" );
        if ( 'on' == orddd_params.orddd_delivery_date_on_cart_page && '1' == orddd_params.orddd_is_cart ) {
            jQuery( "body" ).trigger( "wc_update_cart" );
        }
    }

    localStorage.setItem( "e_deliverydate_session", jQuery('#' + orddd_params.orddd_field_name ).val() );
    localStorage.setItem( "h_deliverydate_session", all );
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

/**
 * Decodes the html entities for currency symbol.
 *
 * @function decodeHtml
 * @param {string} html - String to decode
 * @returns {string} Decoded string.
 * @since 8.0
 */
function decodeHtml(html) {
    var txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
}

/**
 * Shows the Time Slots in the admin Orders page
 *
 * @function show_admin_times
 * @memberof orddd_initialize_functions
 * @param {date} date - Date
 * @param {object} inst 
 * @since 3.2
 */
function show_admin_times( date, inst ) {

    jQuery( document ).trigger( 'on_select_additional_action', [ date, inst ] );
    
    var shipping_class = "";
    var shipping_method_id = jQuery( "input[name=\"shipping_method_id[]\"]" ).val();
    if( typeof shipping_method_id === "undefined" ) {
        var shipping_method_id = "";
    }
    var shipping_method = jQuery( "select[name=\"shipping_method[" + shipping_method_id + "]\"]" ).find(":selected").val();
    if( typeof shipping_method === "undefined" ) {
        var shipping_method = "";
    }
    
    var hidden_var_obj = orddd_params.orddd_hidden_vars_str;
    if( hidden_var_obj != '' ) {
        var html_vars_obj = jQuery.parseJSON( hidden_var_obj || '{}' );
        if( html_vars_obj == null ) {
            html_vars_obj = {};
        }
    }
    

    var time_enable = "";
    jQuery.each( html_vars_obj, function( key, value ) {
        if( typeof value.shipping_methods !== "undefined" ) {
            var shipping_methods = value.shipping_methods.split(",");
            for( i = 0; i < shipping_methods.length; i++ ) {
                if( shipping_method.indexOf( shipping_methods[ i ] ) !== -1 ) {
                    shipping_method = shipping_methods[ i ];
                }
            }
            var shipping_class = orddd_params.orddd_shipping_class_settings_to_load; 
        } else if ( typeof value.orddd_locations !== "undefined" ) {
            var shipping_methods = value.orddd_locations.split(",");
            for( i = 0; i < shipping_methods.length; i++ ) {
                if( shipping_method.indexOf( shipping_methods[ i ] ) !== -1 ) {
                    shipping_method = shipping_methods[ i ];
                }
            }
        } else if ( typeof value.product_categories !== "undefined" ) {
            var shipping_methods = value.product_categories.split(",");
            shipping_method = orddd_params.orddd_category_settings_to_load;
            shipping_class = "";
        }        

        if( typeof( shipping_method ) != 'undefined' && shipping_method != '' && shipping_method.indexOf( 'wf_fedex_woocommerce_shipping' ) === -1 && shipping_method.indexOf( 'fedex' ) !== -1 && ( shipping_method.split( ":" ).length ) < 3 ) {
            shipping_method = orddd_params.orddd_zone_id + ":" + shipping_method;
        }

        if ( jQuery.inArray( shipping_method, shipping_methods ) !== -1 || jQuery.inArray( shipping_class, shipping_methods ) !== -1 ) {
            if ( value.time_slots == "on" ) {
                time_enable = value.time_slots;    
            } 
        }
    });

    var monthValue = inst.selectedMonth+1;
    var dayValue = inst.selectedDay;
    var yearValue = inst.selectedYear;
    var all = dayValue + "-" + monthValue + "-" + yearValue;
    jQuery( "#h_deliverydate" ).val( all );
    jQuery( "#e_deliverydate" ).val(  jQuery('#' + orddd_params.orddd_field_name ).val() );


    if( "on" == orddd_params.orddd_enable_time_slot || "on" == orddd_params['custom_settings'].time_slot_enable_for_shipping_method ) {
        if( typeof( inst.id ) !== "undefined" ) {  
            var data = {
                current_date: all,
                order_id: orddd_admin_params.orddd_order_id,
                action: "orddd_display_timeslots_admin",
            };

            jQuery( "#orddd_time_slot" ).attr( "disabled", "disabled" );
            jQuery( "#orddd_time_slot_field" ).attr( "style", "opacity: 0.5" );
            if( '' != orddd_params.orddd_admin_url && typeof( orddd_params.orddd_admin_url ) != 'undefined' ) {
                jQuery.post( orddd_params.orddd_admin_url + "admin-ajax.php", data, function( response ) {
                    jQuery( "#orddd_time_slot_field" ).attr( "style" ,"opacity:1" );
                    if( 1 == orddd_params.orddd_is_cart ) {
                        jQuery( "#orddd_time_slot" ).attr( "style", "cursor: pointer !important;max-width:300px" );
                    } else {
                        jQuery( "#orddd_time_slot" ).attr( "style", "cursor: pointer !important" );
                    }
                    jQuery( "#orddd_time_slot" ).removeAttr( "disabled" ); 
                    // If there is weglot plugin is active then it will return the response with non-translatable div with id time_slot_var so we will check for that below and if it has that div then it will use its html() as response text.
                    response_text = jQuery( jQuery.parseHTML( response ) ).filter("#time_slot_var");
                    if ( response_text.length ) {
                        response = response_text.html();
                    }
                    orddd_load_time_slots( response );
                });
            }
        }
    } else if( "on" == orddd_params.time_setting_enable_for_shipping_method ) {
        all = orddd_set_time_slider_range( 'yes', date, inst );
    } else if( "on" == orddd_params.orddd_enable_time_slider && ( typeof( orddd_params['custom_settings'].time_setting_enable_for_shipping_method ) == 'undefined' || 'off' == orddd_params['custom_settings'].time_setting_enable_for_shipping_method ) ) {
        all = orddd_set_time_slider_range( 'no', date, inst );
    }
}

/** 
 * Load the time slots in the time slot dropdown on select of date
 *
 * @function orddd_load_time_slots
 * @param {string} Response returned from the ajax call
 * @since 8.
 */
 function orddd_load_time_slots( response ) {
    var orddd_time_slots = response.split( "/" );
    jQuery( "#orddd_time_slot" ).empty(); 
    var selected_value = '';
    orddd_time_slots = orddd_time_slots.filter(Boolean);
    for( i = 0; i < orddd_time_slots.length; i++ ) {
        var time_slot_to_display = orddd_time_slots[ i ].split( "_" );
        if( 'select' == time_slot_to_display[ 0 ].replace(/\s/g, "") ) {
            jQuery( "#orddd_time_slot" ).append( jQuery( "<option></option>" ).attr( { value:"select", selected:"selected" } ).text( jsL10n.selectText ) );
            selected_value = orddd_time_slots[ i ];
        } else if( 'asap' == time_slot_to_display[ 0 ] ) {
            if( typeof time_slot_to_display[ 3 ] != 'undefined' ) {
                jQuery( "#orddd_time_slot option:selected" ).removeAttr( "selected" );
                jQuery( "#orddd_time_slot" ).append( jQuery( "<option></option>" ).attr( {value:time_slot_to_display[ 0 ], selected:"selected"}).text( jsL10n.asapText ) );
                selected_value = time_slot_to_display[ 0 ];    
            } else {
                jQuery( "#orddd_time_slot" ).append( jQuery( "<option></option>" ).attr( {value:time_slot_to_display[ 0 ]} ).text( jsL10n.asapText ) );
            }
        } else if( 'NA' == time_slot_to_display[ 0 ] ) {
            if( typeof time_slot_to_display[ 3 ] != 'undefined' ) {
                jQuery( "#orddd_time_slot option:selected" ).removeAttr( "selected" );
                jQuery( "#orddd_time_slot" ).append( jQuery( "<option></option>" ).attr( {value:time_slot_to_display[ 0 ], selected:"selected"}).text( jsL10n.NAText ) );
                selected_value = time_slot_to_display[ 0 ];    
            } else {
                jQuery( "#orddd_time_slot" ).append( jQuery( "<option></option>" ).attr( {value:time_slot_to_display[ 0 ]} ).text( jsL10n.NAText ) );
            }
        } else if( typeof time_slot_to_display[ 3 ] != 'undefined' ) {
            jQuery( "#orddd_time_slot option:selected" ).removeAttr( "selected" );
            if( typeof time_slot_to_display[ 2 ] != 'undefined' && time_slot_to_display[ 2 ] != '' ) {
                var time_slot_charges = decodeHtml( time_slot_to_display[ 2 ] );
                jQuery( "#orddd_time_slot" ).append( jQuery( "<option></option>" ).attr( {value:time_slot_to_display[ 0 ], selected:"selected"}).text( time_slot_to_display[ 1 ] + " " + time_slot_charges ) );
            } else {
                jQuery( "#orddd_time_slot" ).append( jQuery( "<option></option>" ).attr( {value:time_slot_to_display[ 0 ], selected:"selected"}).text( time_slot_to_display[ 1 ] ) );
            }
            selected_value = time_slot_to_display[ 0 ];
        } else {
            if( typeof time_slot_to_display[ 2 ] != 'undefined' && time_slot_to_display[ 2 ] != '' ) {
                var time_slot_charges = decodeHtml( time_slot_to_display[ 2 ] );
                jQuery( "#orddd_time_slot" ).append( jQuery( "<option></option>" ).attr( "value", time_slot_to_display[ 0 ] ).text( time_slot_to_display[ 1 ] + " " + time_slot_charges ) );
            } else {
                jQuery( "#orddd_time_slot" ).append( jQuery( "<option></option>" ).attr( "value", time_slot_to_display[ 0 ] ).text( time_slot_to_display[ 1 ] ) );
            }
        }                   
    }

    if( 'on' === jsL10n.is_timeslot_list_view ) {
        orddd_load_time_slots_list();
    }
}

/**
 * Load the time slots in a list view.
 */
function orddd_load_time_slots_list() {
    jQuery( '#orddd_time_slot' ).addClass( 'ordddSelect' );
    if( jQuery( "#orddd_list_view_select" ).length == 0 ) {
        var container = jQuery("<div class='orddd_list_view_container' id='orddd_list_view_select' />");
    } else {
        var container = jQuery("#orddd_list_view_select");
        container.find('label').remove();
    }

    jQuery( '.orddd_list_view_container > select' ).css('display', 'none' );
    jQuery( '#orddd_time_slot_field span.select2' ).css('display', 'none' );
    jQuery( '#orddd_time_slot' ).each( function (selectIndex, selectElement) {
        var select = jQuery( selectElement );

        select.parent().append(container);
        container.append(select);
        jQuery( '.ordddSelect option' ).each(function (optionIndex, optionElement) {
            if( 'select' == jQuery(this).val() || 0 == jQuery(this).val() ) {
                if( jQuery("#orddd-empty-slots").length == 0 && jQuery("#orddd-na-slots").length == 0 ) {
                    jQuery( '<p id="orddd-empty-slots"><small>' + jsL10n.emptyListText + '</small></p>' ).appendTo('#orddd_time_slot_field');    
                }
                return;
            } else if( 'NA' == jQuery(this).val() ) {
                if( jQuery("#orddd-na-slots").length == 0 ) {
                    jQuery("#orddd-empty-slots").remove();
                    jQuery( '<p id="orddd-na-slots"><small>' + jsL10n.NAText + '</small></p>' ).appendTo('#orddd_time_slot_field');    
                }
                return;
            }

            jQuery("#orddd-empty-slots").remove();
            jQuery("#orddd-na-slots").remove();
            var radioGroup = select.attr('id') + "Group";
            var label = jQuery( "<label class='list-view'>" );
            container.append( label );
            
            if( jQuery(this).prop('selected') ) {
                label.addClass('selected');
            }

            jQuery( "<input type='radio' name='" + radioGroup + "' />" )
            .attr( "value", jQuery(this).val() )
            .click( ( function () { select.val( jQuery(this).text() ); } ) ) //radio updates select - see optional below
            .appendTo(label);

            var time = jQuery(this).val();
            if( 'asap' == jQuery(this).val() ) {
                time = jsL10n.asapText;
            }
            jQuery("<span> " + time + " </span>").appendTo(label);

        });

        jQuery( '#orddd_list_view_select' ).on( "click", "label",  function(e) {
            var slot = jQuery( 'input[name="orddd_time_slotGroup"]:checked ').val();
            jQuery( '#orddd_time_slot option[value="'+slot+'"]' ).prop('selected', true).trigger('change');
            jQuery( jQuery("#orddd_list_view_select") ).find( ".selected" ).removeClass( "selected" );
            jQuery(this).addClass( "selected" );
        });
    });
}

/**
 * Gets the selected shipping method
 *
 * @function orddd_get_selected_shipping_method
 * @memberof orddd_initialize_functions
 * @returns {string} shipping_method - Shipping Method
 * @since 7.1
 */
function orddd_get_selected_shipping_method( update_zone = 'no' ) {
    if ( "1" == orddd_params.orddd_is_admin ) {
        var shipping_method_id = jQuery( "input[name=\"shipping_method_id[]\"]" ).val();
        if( typeof shipping_method_id === "undefined" ) {
            var shipping_method_id = "";
        }
        var shipping_method = jQuery( "select[name=\"shipping_method[" + shipping_method_id + "]\"]" ).find(":selected").val();
        if( typeof shipping_method === "undefined" ) {
            var shipping_method = "";
        }
    } else if( "1" == orddd_params.orddd_is_account_page ) {
        var shipping_method = orddd_account_params.shipping_method;
    } else {
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
        
        if( typeof shipping_method === "undefined" && 'no' == update_zone ) {
            var shipping_method = orddd_params.orddd_shipping_id;                    
        }

        if( typeof shipping_method === "undefined" ) {
            var shipping_method = "";
        }
    }

    if( shipping_method.indexOf( 'usps' ) !== -1 && ( shipping_method.split( ":" ).length ) < 3 ) {
        shipping_method = orddd_params.orddd_zone_id + ":" + shipping_method;
    } else if( shipping_method.indexOf( 'wf_fedex_woocommerce_shipping' ) === -1 && shipping_method.indexOf( 'fedex' ) !== -1 && ( shipping_method.split( ":" ).length ) < 3 ) {
        shipping_method = orddd_params.orddd_zone_id + ":" + shipping_method;
    } else if( "1" == orddd_params.orddd_is_admin && orddd_admin_params.orddd_canada_post_id !== undefined ) {
        shipping_method = orddd_admin_params.orddd_canada_post_id;
    } else if( "1" == orddd_params.orddd_is_admin ) { 
        shipping_method = orddd_params.orddd_shipping_id;
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
    
    var recurring_key = orddd_params.recurring_cart_key;

    if( recurring_key != '' && recurring_key !== undefined ) {
        var recurring_shipping_method = jQuery("input[name=\"shipping_method["+recurring_key+"]\"]:checked").val();

        if( recurring_shipping_method !== undefined ) {
            shipping_method = recurring_shipping_method;
        }
    }
    
    return shipping_method;
}

/**
 * Saves the delivery information which are changed in the admin Orders page
 *
 * @function save_delivery_dates
 * @memberof orddd_initialize_functions
 * @param {string} notify - Yes/No
 * @since 3.2
 */
function save_delivery_dates( notify ) {
    var hourValue = jQuery( ".ui_tpicker_time" ).html() 
    var shipping_method_id = jQuery( "input[name=\"shipping_method_id[]\"]" ).val();
    if( typeof shipping_method_id === "undefined" ) {
        var shipping_method_id = "";
    }

    var shipping_method =  [ jQuery( "select[name=\"shipping_method[" + shipping_method_id + "]\"]" ).find(":selected").val() ];
    if( typeof shipping_method === "undefined" ) {
        var shipping_method = [];
    }
    
    var data = {
        order_id: orddd_admin_params.orddd_order_id,
        e_deliverydate: jQuery( '#' + orddd_params.orddd_field_name ).val(),
        h_deliverydate: jQuery( "#h_deliverydate" ).val(),
        orddd_time_slot: jQuery( "#orddd_time_slot option:selected" ).val(),
        orddd_time_settings_selected: hourValue,
        shipping_method: shipping_method,
        orddd_post_type: orddd_admin_params.orddd_post_type,
        orddd_category_settings_to_load: orddd_params.orddd_category_settings_to_load,
        orddd_shipping_class_settings_to_load: orddd_params.orddd_shipping_class_settings_to_load,
        orddd_unique_custom_settings: jQuery( "#orddd_unique_custom_settings" ).val(),
        orddd_notify_customer: notify,
        orddd_charges: jQuery( '#del_charges' ).val(),
        action: "save_delivery_dates"
    };

    if( '' != orddd_params.orddd_admin_url && typeof( orddd_params.orddd_admin_url ) != 'undefined' ) {
        jQuery( "#orddd_update_notice" ).html( 'Updating delivery details...' );

        jQuery.post( orddd_params.orddd_admin_url + 'admin-ajax.php', data, function( response ) {
            var validations = response.split( "," );
            if(  validations[ 0 ] == "yes" && validations[ 1 ] == "yes" && validations[2] == "yes" ) {
                jQuery( "#orddd_update_notice" ).html( "Delivery details have been updated." );
                jQuery( "#orddd_update_notice" ).attr( "color", "green" );
                jQuery( "#orddd_update_notice" ).fadeIn();
                setTimeout( function() {
                    jQuery( "#orddd_update_notice" ).fadeOut();
                },3000 );
            } else if ( validations[ 0 ] == "no" && ( "checked" == orddd_params.orddd_date_field_mandatory || "checked" == orddd_params['custom_settings'].date_mandatory_for_shipping_method ) ) {
                jQuery( "#orddd_update_notice" ).html( orddd_admin_params.orddd_field_name_admin + " is mandatory." );
                jQuery( "#orddd_update_notice" ).attr( "color", "red" );
                jQuery( "#orddd_update_notice" ).fadeIn();
                setTimeout( function() {
                    jQuery( "#orddd_update_notice" ).fadeOut();
                },3000 );
            } else if ( validations[ 1 ] == "no" && ( ( "on" == orddd_params.orddd_enable_time_slot && "checked" == orddd_params.orddd_timeslot_field_mandatory ) || ( "on" == orddd_params['custom_settings'].time_slot_enable_for_shipping_method && "checked" == orddd_params['custom_settings'].time_slot_mandatory_for_shipping_method ) ) ) {
                jQuery( "#orddd_update_notice" ).html( orddd_admin_params.orddd_time_field_name_admin + " is mandatory." );
                jQuery( "#orddd_update_notice" ).attr( "color", "red" );
                jQuery( "#orddd_update_notice" ).fadeIn();
                setTimeout( function() {
                    jQuery( "#orddd_update_notice" ).fadeOut();
                },3000 );
            }

            setTimeout( function() {
                jQuery( "#orddd_update_notice" ).fadeOut();
            },3000 );
        });
    }
}

/**
 * This function updates all the necessary variables whenever a shipping method is changed or when 
 * customer clicks on "Ship to a different address" or when an item is deleted from the Cart page.
 *
 * @function orddd_update_delivery_session
 * @memberof orddd_initialize_functions
 * @since 9.6
 */
function orddd_update_delivery_session( called_from ) {

    //As IE does not support default parameters, check if it is undefined and set it blank
    if( called_from == undefined ) {
        called_from = '';
    }

    var shipping_method          = orddd_get_selected_shipping_method();
    var shipping_method_to_check = shipping_method;
    var custom_settings          = orddd_get_applied_custom_settings();
    var set_date                 = false;

    if( shipping_method.indexOf( 'local_pickup' ) === -1 ) {
        jQuery( "#orddd_locations_field" ).hide();
        jQuery( "#orddd_locations" ).val( "select_location" );    
    } else {
        jQuery( "#orddd_locations_field" ).show();    
        if ( 'on' === orddd_params.orddd_auto_populate_first_pickup_location && ( ( '' === localStorage.getItem( "orddd_location_session" ) || undefined === localStorage.getItem( "orddd_location_session" ) ) || 'select_location' === localStorage.getItem( "orddd_location_session" ) ) ) {
            jQuery( "#orddd_locations" ).prop("selectedIndex", 1).trigger( "change" ); 
        } else if ( localStorage.getItem( "orddd_location_session" ) == '' || localStorage.getItem( "orddd_location_session" ) == undefined || 'select_location' === localStorage.getItem( "orddd_location_session" ) ) {
            jQuery( "#orddd_locations" ).prop("selectedIndex", 0 ).trigger( "change" );
        } 
    }

    if ( 'on' == orddd_params.orddd_enable_shipping_based_delivery ) {

        jQuery("#e_deliverydate_field").block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        if( 'categories' == custom_settings.settings_based_on || 'category_shipping' == custom_settings.settings_based_on ) {
            var data = {
                shipping_method: shipping_method_to_check,
                settings_based_on: custom_settings.settings_based_on,
                called_from: called_from,
            };
     
            jQuery.ajax({
                type:		'POST',
                url:		orddd_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'orddd_update_delivery_session' ),
                data:		data,
                success:	function( response ) {
                    // For weglot plugin
                    response_text = jQuery( jQuery.parseHTML( response ) ).filter("#get_common_delivery_days_str");
                    if ( response_text.length ) {
                        response = response_text.html();
                    }
                    
                    if( response.replace(/\s/g, "") != '' ) {
                        var response_arr = response.split( "%%" );
                        orddd_params.orddd_common_delivery_days_for_product_category = response_arr[ 0 ];
                        orddd_params.orddd_common_delivery_dates_for_product_category = response_arr[ 1 ];
                        orddd_params.orddd_common_holidays_for_product_category = response_arr[ 2 ];
                        orddd_params.orddd_common_locked_days = response_arr[ 3 ];
                        orddd_params.orddd_is_days_common = response_arr[ 4 ];
                        orddd_params.orddd_categories_settings_common = response_arr[ 5 ];
                        orddd_params.orddd_category_settings_to_load = response_arr[ 6 ];
                        orddd_params.orddd_shipping_class_settings_to_load = response_arr[ 7 ];
                        if( typeof response_arr[8] !== 'undefined' ) {
                            var availability = response_arr[8].split( '&' );    
                            orddd_params.orddd_partially_booked_dates = availability[0];
                            orddd_params.orddd_available_deliveries   = availability[1];                
                        }
    
                        //Update the orddd_hidden_vars_str only when the cart item is deleted or added using undo option. 
                        //The 9th element will be returned only for the above action.
                        if( typeof response_arr[9] !== 'undefined' ) {
                            orddd_params.orddd_hidden_vars_str = response_arr[ 9 ];
                        }

                        var update_settings = load_delivery_date();
    
                        if( 'on' === jsL10n.is_timeslot_list_view ) {
                            orddd_load_time_slots_list();
                        }
                
                        if( 'yes' == update_settings && 'on' == orddd_params.orddd_enable_autofill_of_delivery_date ) {
                            orddd_autofil_date_time();
                        } else { 
                            if( 'yes' == jsL10n.is_dropdown_field ) {
                                orddd_set_date_dropdown_from_session();
                            } else {
                                orddd_set_date_from_session(); 
                            }
                        }
                
                        jQuery("#e_deliverydate_field").unblock();
                    }
                }
            });
        } else {
            var update_settings = load_delivery_date();
            
            if( 'yes' == update_settings && 'on' == orddd_params.orddd_enable_autofill_of_delivery_date ) {
                orddd_autofil_date_time();
            } else { 
                if( 'yes' == jsL10n.is_dropdown_field ) {
                    orddd_set_date_dropdown_from_session();
                } else {
                    orddd_set_date_from_session(); 
                }
            }
            jQuery("#e_deliverydate_field").unblock();
        }
    } else {
        if( 'on' == orddd_params.orddd_enable_autofill_of_delivery_date ) {
            orddd_autofil_date_time();
        } else { 
            if( 'yes' == jsL10n.is_dropdown_field ) {
                orddd_set_date_dropdown_from_session();
            } else {
                orddd_set_date_from_session(); 
            }
        }
    }
}

/**
 * Update the date field based on session.
 */
function orddd_set_date_from_session() {
    var e_deliverydate_session = localStorage.getItem( 'e_deliverydate_session' );
    var h_deliverydate_session = localStorage.getItem( 'h_deliverydate_session' );
    
    if ( ! e_deliverydate_session ) {
        localStorage.setItem( "e_deliverydate_session", jQuery( '#' + orddd_params.orddd_field_name ).val() );
        e_deliverydate_session = jQuery( '#' + orddd_params.orddd_field_name ).val();
    }

    if ( ! h_deliverydate_session ) {
        localStorage.setItem( "h_deliverydate_session", jQuery( "#h_deliverydate" ).val() );
        h_deliverydate_session = jQuery( "#h_deliverydate" ).val();
    }
    if( typeof( e_deliverydate_session ) != 'undefined' && e_deliverydate_session != '' ) {
        if ( h_deliverydate_session ) {
            var default_date_arr = h_deliverydate_session.split( '-' );
            var default_date = new Date( default_date_arr[ 1 ] + '/' + default_date_arr[ 0 ] + '/' + default_date_arr[ 2 ] );
            
            var delay_weekday = default_date.getDay();
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

            jQuery( '#' + orddd_params.orddd_field_name ).datepicker( "setDate", date_to_set );
            jQuery( "#h_deliverydate" ).val( h_deliverydate_session );

            var show = orddd_params.orddd_show_datepicker;
            if( 'datetimepicker' == show ) {
                var time = e_deliverydate_session.split( ' ' );
                var time_value = time[time.length-1];
                if( time_value.indexOf( ':' ) !== -1 ) {
                    // Set the Hours & minutes to be prepopulated in the time slider
                    var time_arr = time_value.split( ":" );
                    date_to_set.setHours( time_arr[0], time_arr[1] );
                    orddd_params.orddd_time_settings_selected = time_value;
                    jQuery( "#orddd_time_settings_selected" ).val( time_value );
                }                    
                jQuery( '#' + orddd_params.orddd_field_name ).datetimepicker( "setDate", date_to_set );

            }

            var inst = jQuery.datepicker._getInst( jQuery( '#' + orddd_params.orddd_field_name )[0] );
            // Trigger the onSelect when a date is auto-populated.
            jQuery.datepicker._get(inst, 'onSelect').apply(inst.input[0], [ jQuery( '#' + orddd_params.orddd_field_name ).datepicker('getDate'), inst] );
        }
    }
}

/**
 * Get the first available delivery date 
 * 
 * @param {string } delay_date 
 * @param {Date} delay_days 
 */
function orddd_get_first_available_date( delay_date, delay_days ) {
    var current_date = orddd_get_current_day();
    if( current_date != '' && typeof( current_date ) != 'undefined' ) {
        var split_current_date = current_date.split( "-" );
        var current_day = new Date ( split_current_date[ 1 ] + "/" + split_current_date[ 0 ] + "/" + split_current_date[ 2 ] );
    } else {
        var current_day = new Date();
    }

    if( "undefined" != typeof orddd_params['custom_settings'].orddd_custom_based_next_day_delivery &&  "undefined" !== typeof orddd_params['custom_settings'].orddd_custom_based_same_day_delivery ) {
        if( "on" != orddd_params['custom_settings'].orddd_custom_based_same_day_delivery && 
            "on" != orddd_params['custom_settings'].orddd_custom_based_next_day_delivery ) {
            if( delay_date != "" ) {
                delay_days = minimum_date_to_set( delay_days );
                orddd_set_min_date( delay_days );
            }
        } else if(  "on" != orddd_params['custom_settings'].orddd_custom_based_same_day_delivery && 
                    "on" == orddd_params['custom_settings'].orddd_custom_based_next_day_delivery ) {
            if( delay_days.getTime() == current_day.getTime() ) {
                delay_days.setDate( delay_days.getDate()+1 );    
            } 
            delay_days = same_day_next_day_to_set( delay_days );
            orddd_set_min_date( delay_days );

        } else if( "on" == orddd_params['custom_settings'].orddd_custom_based_same_day_delivery && 
        "on" != orddd_params['custom_settings'].orddd_custom_based_next_day_delivery  ) {
            delay_days = same_day_next_day_to_set( delay_days );
            orddd_set_min_date( delay_days );
        } else if( "on" == orddd_params['custom_settings'].orddd_custom_based_same_day_delivery && 
        "on" == orddd_params['custom_settings'].orddd_custom_based_next_day_delivery ) {
            delay_days = same_day_next_day_to_set( delay_days );
            orddd_set_min_date( delay_days );
        }
    } else if( 'on' != orddd_params.orddd_same_day_delivery && 'on' != orddd_params.orddd_next_day_delivery ) {
        if( delay_date != "" ) {
            delay_days = minimum_date_to_set( delay_days );
            orddd_set_min_date( delay_days );
        }
    } else if( 'on' != orddd_params.orddd_same_day_delivery && 'on' == orddd_params.orddd_next_day_delivery ) {
        if( delay_days.getTime() == current_day.getTime() ) {
            delay_days.setDate( delay_days.getDate()+1 );
        }
        delay_days = same_day_next_day_to_set( delay_days );
        orddd_set_min_date( delay_days );
    } else if( 'on' == orddd_params.orddd_same_day_delivery && 'on' != orddd_params.orddd_next_day_delivery ) {
        delay_days = same_day_next_day_to_set( delay_days );
        orddd_set_min_date( delay_days );
    } else if( 'on' == orddd_params.orddd_same_day_delivery && 'on' == orddd_params.orddd_next_day_delivery ) {
        delay_days = same_day_next_day_to_set( delay_days );
        orddd_set_min_date( delay_days );
    }

    return delay_days;
}

/**
 * Set the min date for same day/next day cutoff
 * @param {Date} delay_days 
 */
function orddd_set_min_date( delay_days ) {
    if( delay_days != '' ) {
        var min_date_to_set = delay_days.getDate() + "-" + ( delay_days.getMonth()+1 ) + "-" + delay_days.getFullYear();
        orddd_params.orddd_min_date_set = min_date_to_set;
    }
}

function orddd_set_time_slider_range( is_custom, date, inst ) {
    var monthValue      = inst.selectedMonth+1;
    var dayValue        = inst.selectedDay;
    var yearValue       = inst.selectedYear;
    var all             = dayValue + "-" + monthValue + "-" + yearValue;
    var current_day     = orddd_get_current_day();
    var min_date_set    = orddd_params.orddd_min_date_set;
    var min_date        = orddd_get_min_date();
    var next_day        = orddd_get_next_day();
    var min_hour        = orddd_get_min_hour();
    var min_minute      = orddd_get_min_minute();
    var max_hour_set    = orddd_get_max_hour_set();
    var max_mins_set    = orddd_get_max_mins_set();
    var min_hour_set    = orddd_get_min_hour_set();
    var min_mins_set    = orddd_get_min_mins_set();
    var time_format     = orddd_params.orddd_delivery_time_format;
    var is_min_hour_set = 'no';
    if( 'no' == is_custom ) {
        if( ( all == current_day || all == min_date_set ) && ( 'on' != orddd_params.orddd_same_day_delivery && 'on' != orddd_params.orddd_next_day_delivery ) ) {
            is_min_hour_set = 'yes';
        } else if( all == current_day && ( 'on' == orddd_params.orddd_same_day_delivery || 'on' == orddd_params.orddd_next_day_delivery ) ) {
            is_min_hour_set = 'yes';
        }else if(  all == next_day && next_day == min_date  && 'on' == orddd_params.orddd_next_day_delivery ) {
            is_min_hour_set = 'yes';
        }
    }else if( 'yes' == is_custom ) {
        if( ( all == current_day || all == min_date_set ) && ( 'on' != orddd_params['custom_settings'].orddd_custom_based_same_day_delivery && 'on' != orddd_params['custom_settings'].orddd_custom_based_next_day_delivery ) ) {
            is_min_hour_set = 'yes';
        }else if( all == current_day && ( 'on' == orddd_params['custom_settings'].orddd_custom_based_same_day_delivery || 'on' != orddd_params['custom_settings'].orddd_custom_based_next_day_delivery ) ) {
            is_min_hour_set = 'yes';
        }else if(  all == next_day && next_day == min_date && 'on' == orddd_params.orddd_next_day_delivery ) {
            is_min_hour_set = 'yes';
        }
    }

    if( typeof( inst.id ) !== "undefined" ) {  
        jQuery( "#h_deliverydate" ).val( all );
        var tp_inst = jQuery.datepicker._get( inst, "timepicker" );
        var orddd_disable_minimum_delivery_time_slider = orddd_params.orddd_disable_minimum_delivery_time_slider;
    
        if( 'yes' != orddd_disable_minimum_delivery_time_slider ) {
            if( 'yes' == is_min_hour_set ) {
                if( typeof( tp_inst ) != 'undefined' ) {
                    var split = tp_inst.formattedTime.split( ":" );

                    if( time_format == "1" ) {
                        if( split[ 1 ].indexOf( 'PM' ) !== -1 && parseInt( split[ 0 ] ) != 12 ) {
                            var hour_time  = parseInt( split[ 0 ] ) + parseInt( 12 );    
                        } else {
                            var hour_time = parseInt( split[ 0 ] );
                        }
                    } else {
                        var hour_time  = parseInt( split[ 0 ] );
                    }                      
                } else {
                    var hour_time = '';
                }

                inst.settings.hourMin = parseInt( min_hour );
                tp_inst._defaults.hourMin = parseInt( min_hour );

                if( hour_time == parseInt( min_hour ) ) {
                    inst.settings.minuteMin = parseInt( min_minute );
                    tp_inst._defaults.minuteMin = parseInt( min_minute );

                    inst.settings.minuteMax = 59;
                    tp_inst._defaults.minuteMax = 59;
                } else {
                    inst.settings.minuteMin = parseInt( min_mins_set );
                    tp_inst._defaults.minuteMin = parseInt( min_mins_set );
                }
                tp_inst._limitMinMaxDateTime(inst, true);
            } else {
                if( typeof( tp_inst ) != 'undefined' ) {
                    var split = tp_inst.formattedTime.split( ":" );

                    if( time_format == "1" ) {
                        if( undefined != split[1] && split[ 1 ].indexOf( 'PM' ) !== -1 && parseInt( split[ 0 ] ) != 12 ) {
                            var hour_time  = parseInt( split[ 0 ] ) + parseInt( 12 );    
                        } else {
                            var hour_time = parseInt( split[ 0 ] );
                        }
                    } else {
                        var hour_time  = parseInt( split[ 0 ] );
                    }                      
                } else {
                    var hour_time = '';
                }
                inst.settings.hourMin = parseInt( min_hour_set );
                tp_inst._defaults.hourMin = parseInt( min_hour_set );
                if( hour_time == parseInt( min_hour_set ) ) {
                    inst.settings.minuteMin = parseInt( min_mins_set );
                    tp_inst._defaults.minuteMin = parseInt( min_mins_set );
                    inst.settings.minuteMax = 59;
                    tp_inst._defaults.minuteMax = 59;
                } else if( hour_time == parseInt( max_hour_set )  ) {
                    inst.settings.minuteMax = parseInt( max_mins_set );
                    tp_inst._defaults.minuteMax = parseInt( max_mins_set );
                } else {
                    inst.settings.minuteMin = 0;
                    tp_inst._defaults.minuteMin = 0;
                    inst.settings.minuteMax = 59;
                    tp_inst._defaults.minuteMax = 59;
                }
                tp_inst.hourMinOriginal = null;
                tp_inst._limitMinMaxDateTime(inst, true);
            }
        } else {
            if( typeof( tp_inst ) != 'undefined' ) {
                var split = tp_inst.formattedTime.split( ":" );
                if( time_format == "1" ) {
                    if( split[ 1 ].indexOf( 'PM' ) !== -1 && parseInt( split[ 0 ] ) != 12 ) {
                        var hour_time  = parseInt( split[ 0 ] ) + parseInt( 12 );    
                    } else {
                        var hour_time = parseInt( split[ 0 ] );
                    }
                } else {
                    var hour_time  = parseInt( split[ 0 ] );
                }  
            } else {
                var hour_time = '';
            }
            inst.settings.hourMin = parseInt( min_hour_set );
            tp_inst._defaults.hourMin = parseInt( min_hour_set );
            inst.settings.minuteMin = 0;
            tp_inst._defaults.minuteMin = 0;
            tp_inst._limitMinMaxDateTime(inst, true);
        }
        jQuery.datepicker._updateDatepicker(inst);  
    } else if( typeof( inst.inst.id ) !== "undefined" )  {

        var monthValue = inst.inst.currentMonth+1;
        var dayValue = inst.inst.currentDay;
        var yearValue = inst.inst.currentYear;
        var all = dayValue + "-" + monthValue + "-" + yearValue;
        jQuery( "#h_deliverydate" ).val( all );

        if( 'no' == is_custom ) {
            if( (  all == current_day || all == min_date_set ) && ( 'on' != orddd_params.orddd_same_day_delivery && 'on' != orddd_params.orddd_next_day_delivery ) ) {
                is_min_hour_set = 'yes';
            } else if( all == current_day && ( 'on' == orddd_params.orddd_same_day_delivery || 'on' == orddd_params.orddd_next_day_delivery ) ) {
                is_min_hour_set = 'yes';
            }else if( all == next_day && next_day == min_date  && 'on' == orddd_params.orddd_next_day_delivery ) {
                is_min_hour_set = 'yes';
            }
        }else if( 'yes' == is_custom ) {
            if( ( all == current_day || all == min_date_set ) && ( 'on' != orddd_params['custom_settings'].orddd_custom_based_same_day_delivery && 'on' != orddd_params['custom_settings'].orddd_custom_based_next_day_delivery ) ) {
                is_min_hour_set = 'yes';
            }else if( all == current_day && ( 'on' == orddd_params['custom_settings'].orddd_custom_based_same_day_delivery || 'on' != orddd_params['custom_settings'].orddd_custom_based_next_day_delivery  ) ) {
                is_min_hour_set = 'yes';
            }else if( all ==next_day && next_day == min_date && 'on' == orddd_params.orddd_next_day_delivery ) {
                is_min_hour_set = 'yes';
            }
        }

        var tp_inst = jQuery.datepicker._get( inst.inst, "timepicker" );
        if( 'yes' == is_min_hour_set ) {
            if( typeof( inst ) != 'undefined' ) {
                var split = inst.formattedTime.split( ":" );

                if( time_format == "1" ) {
                    if( split[ 1 ].indexOf( 'PM' ) !== -1 && parseInt( split[ 0 ] ) != 12 ) {
                        var hour_time  = parseInt( split[ 0 ] ) + parseInt( 12 );    
                    } else {
                        var hour_time = parseInt( split[ 0 ] );
                    }
                } else {
                    var hour_time  = parseInt( split[ 0 ] );
                }
            } else {
                var hour_time = '';
            }
            if( hour_time == parseInt( min_hour ) ) {
                inst._defaults.minuteMin = parseInt( min_minute );
                inst.inst.settings.minuteMin = parseInt( min_minute );
                tp_inst._defaults.minuteMin = parseInt( min_minute );

                inst._defaults.minuteMax = 59;
                inst.inst.settings.minuteMax = 59;
                tp_inst._defaults.minuteMax = 59;
                tp_inst._limitMinMaxDateTime( inst.inst, true );
            } else {
                if( hour_time == parseInt( max_hour_set )  ) {
                    inst._defaults.minuteMin = 0;
                    inst.inst.settings.minuteMin = 0;
                    tp_inst._defaults.minuteMin = 0;

                    inst._defaults.minuteMax = parseInt( max_mins_set );
                    inst.inst.settings.minuteMax =  parseInt( max_mins_set );
                    tp_inst._defaults.minuteMax = parseInt( max_mins_set );

                    tp_inst._limitMinMaxDateTime( inst.inst, true );
                } else {
                    inst._defaults.minuteMin = 0;
                    inst.inst.settings.minuteMin = 0;
                    tp_inst._defaults.minuteMin = 0;

                    inst._defaults.minuteMax = 59;
                    inst.inst.settings.minuteMax = 59;
                    tp_inst._defaults.minuteMax = 59;

                    tp_inst._limitMinMaxDateTime( inst.inst, true );
                }
            }
        } else {
            if( typeof( inst ) != 'undefined' ) {
                var split = inst.formattedTime.split( ":" );

                if( time_format == "1" ) {
                    if( split[ 1 ].indexOf( 'PM' ) !== -1 ) {
                        var hour_time  = parseInt( split[ 0 ] ) + parseInt( 12 );    
                    } else {
                        var hour_time = parseInt( split[ 0 ] );
                    }
                } else {
                    var hour_time  = parseInt( split[ 0 ] );
                }
            } else {
                var hour_time = '';
            }
            inst.inst.settings.hourMin = parseInt( min_hour_set );
            tp_inst._defaults.hourMin = parseInt( min_hour_set );
            if( hour_time == parseInt( min_hour_set ) ) {
                inst.inst.settings.minuteMin = parseInt( min_mins_set );
                tp_inst._defaults.minuteMin = parseInt( min_mins_set );
                inst.inst.settings.minuteMax = 59;
                tp_inst._defaults.minuteMax = 59;
            } else if( hour_time == parseInt( max_hour_set )  ) {
                inst.inst.settings.minuteMax = parseInt( max_mins_set );
                tp_inst._defaults.minuteMax = parseInt( max_mins_set );
            } else {
                inst.inst.settings.minuteMin = 0;
                tp_inst._defaults.minuteMin = 0;
                inst.inst.settings.minuteMax = 59;
                tp_inst._defaults.minuteMax = 59;
            }
            tp_inst._limitMinMaxDateTime(inst.inst, true);
        }
        jQuery.datepicker._updateDatepicker(inst.inst);
    }

    return all;
}

/**
 * Get the custom settings to be applied on the cart or checkout page.
 * @returns Object
 * @since 9.27.0
 */
function orddd_get_applied_custom_settings() {
    var shipping_class   = orddd_params.orddd_shipping_class_settings_to_load;
    var product_category = orddd_params.orddd_category_settings_to_load;

    if( '1' == orddd_params.orddd_is_account_page ) {
        shipping_class   = orddd_account_params.orddd_shipping_class_settings_to_load;
        product_category = orddd_account_params.orddd_category_settings_to_load;
    }

    var shipping_class_arr   = shipping_class.split( "," );
    var product_category_arr = product_category.split( "," );

    var shipping_method          = orddd_get_selected_shipping_method();
    var shipping_method_to_check = shipping_method;

    var orddd_lpp_pickup_location = '';
    if( typeof orddd_lpp_method_func == 'function' ) {
        orddd_lpp_pickup_location = orddd_lpp_method_func( shipping_method );
    }
    
    var location = jQuery( "select[name=\"orddd_locations\"]" ).find(":selected").val();

    if( typeof location === "undefined" ) {
        var location = jQuery( "#orddd_location" ).val();
    }

    if( typeof location === "undefined" ) {
        var location = "";
    }

    var custom_settings_to_load = new Object();
    var category_flag = true;

    var hidden_var_obj = orddd_params.orddd_hidden_vars_str;
    var html_vars_obj = jQuery.parseJSON( hidden_var_obj || '{}' );
    if( html_vars_obj == null ) {
        html_vars_obj = {};
    }

    var load_common_settings = 'no';
    var settings_based_on    = '';
    var method_found         = 0;
    var custom_settings      = {};
    var setting_ids          = [];
    if ( shipping_method != "" || shipping_class != "" || product_category != "" ) {
        // hidden vars
        jQuery.each( html_vars_obj, function( key, value ) {
            method_found = 0;
            if( orddd_lpp_pickup_location != '' ) {
                if( typeof value.orddd_pickup_locations !== "undefined" ) {
                    var locations = value.orddd_pickup_locations.split( "," );
                    if( jQuery.inArray( orddd_lpp_pickup_location, locations ) != -1 ) {                   
                        custom_settings_to_load = value;
                        settings_based_on = 'lpp_pickup_location';
                        method_found = 1;
                        return false;
                    }
                }
            }

            if( method_found == 0 && typeof value.orddd_locations !== "undefined" ) {
                var categories_for_pickup_locations = value.categories_for_pickup_locations.split( "," );
                var locations = value.orddd_locations.split( "," );
                if( jQuery.inArray( location, locations ) != -1 ) {    
                    if( value.categories_for_pickup_locations.length != '' ) {
                        jQuery.each( product_category_arr, function( pkey, pvalue ) {
                            if( jQuery.inArray( pvalue, categories_for_pickup_locations ) != -1 ) {
                                custom_settings_to_load = value;
                                settings_based_on = 'pickup_location_category';
                                method_found = 1;
                                setting_ids.push( custom_settings_to_load.unique_settings_key );
                                return false;
                            }
                        });
                    } else {
                        custom_settings_to_load = value;
                        settings_based_on = 'pickup_location';
                        method_found = 1;
                        return false;
                    }
                }
            } 
            
            if( method_found == 0 && typeof value.shipping_methods !== "undefined" ) {
                var shipping_methods = value.shipping_methods.split( "," );
                if( jQuery.inArray( shipping_method, shipping_methods ) != -1 ) {                   
                    custom_settings_to_load = value;
                    settings_based_on = 'shipping_method';
                    method_found = 1;
                    return false;
                }
            } 
            
            if( method_found == 0 && typeof value.product_categories !== "undefined" ) {
                jQuery.each( product_category_arr, function( pkey, pvalue ) {
                    var shipping_methods_for_categories = value.shipping_methods_for_categories.split( "," );
                    var product_categories = value.product_categories.split( "," );
                    if( jQuery.inArray( pvalue, product_categories ) != -1 && ( jQuery.inArray( shipping_method_to_check, shipping_methods_for_categories ) != -1 ) ) {
                        custom_settings_to_load = value;
                        settings_based_on = 'category_shipping';
                        method_found = 1;
                        load_common_settings = 'yes';
                        setting_ids.push( custom_settings_to_load.unique_settings_key );
                        return false;
                    } else if( jQuery.inArray( pvalue, product_categories ) != -1 && value.shipping_methods_for_categories.length != '' ) {
                        jQuery.each( shipping_class_arr, function( skey, svalue ) {
                            if( jQuery.inArray( svalue, shipping_methods_for_categories ) != -1 ) {
                                custom_settings_to_load = value;
                                settings_based_on = 'category_shipping';
                                method_found = 1;
                                load_common_settings = 'yes';
                                setting_ids.push( custom_settings_to_load.unique_settings_key );
                                return false;
                            }
                        });
                    } else if( jQuery.inArray( pvalue, product_categories ) != -1 && value.shipping_methods_for_categories.length == "" ) {
                        custom_settings_to_load = value;
                        settings_based_on = 'categories';
                        load_common_settings = 'yes';
                        method_found = 1;
                        setting_ids.push( custom_settings_to_load.unique_settings_key );
                        enable_delivery_date = custom_settings_to_load.enable_delivery_date;
                        if( enable_delivery_date == "" ) {
                            category_flag = false;
                            return category_flag;
                        } 
                        return false;
                    }
                });
            } 
            
            if( method_found == 0 && shipping_class != "" && typeof value.shipping_methods !== "undefined" ) {
                jQuery.each( shipping_class_arr, function( skey, svalue ) {
                    var shipping_methods = value.shipping_methods.split( "," );
                    if( jQuery.inArray( svalue, shipping_methods ) != -1 ) {
                        custom_settings_to_load = value;
                        settings_based_on = 'shipping_class';
                        load_common_settings = 'yes';
                        method_found = 1;
                        setting_ids.push( custom_settings_to_load.unique_settings_key );
                        enable_delivery_date = custom_settings_to_load.enable_delivery_date;
                        if( enable_delivery_date == "" ) {
                            return false;
                        }
                        return false;
                    }
                });
            }
            return true;           
        });
    }

    var common_ids = [];
    if( setting_ids.length > 0 ) {
        jQuery.each( setting_ids, function( key, value ) {
            var id_arr = value.split( '_' );
            var id = id_arr[2];
            common_ids.push( id );
        });
    }
    custom_settings.settings_based_on       = settings_based_on;
    custom_settings.custom_settings_to_load = custom_settings_to_load;
    custom_settings.load_common_settings    = load_common_settings;
    custom_settings.setting_ids             = common_ids;
    return custom_settings;
}

/**
 * Get the holidays based on the global or custom settings.
 * @returns String
 * @since 9.27.0
 */
function orddd_get_holidays() {
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var holidays = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].orddd_delivery_date_holidays : orddd_params.orddd_delivery_date_holidays;

    return holidays;
}

/**
 * Get the blocked days based on the global or custom settings.
 * @returns String
 * @since 9.27.0
 */
function orddd_get_lockout_days() {
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var lockout_days = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].orddd_lockout_days : orddd_params.orddd_lockout_days;

    return lockout_days;
}

/**
 * Get the first available date based on the global or custom settings.
 * @returns String
 * @since 9.27.0
 */
function orddd_get_min_date() {
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var min_date = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].orddd_minimumOrderDays : orddd_params.orddd_minimumOrderDays;

    return min_date;
}


/**
 * Get the number of dates based on the global or custom settings.
 * @returns int
 * @since 9.27.0
 */
function orddd_get_number_of_dates() {
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var number_of_dates = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].orddd_number_of_dates : orddd_params.orddd_number_of_dates;

    return parseInt( number_of_dates );
}

/**
 * Get the current date set based on the global or custom settings.
 * @returns String
 * @since 9.27.0
 */
function orddd_get_current_date_set() {
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var current_date_to_set = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].orddd_current_date_set : orddd_params.orddd_current_date_set;

    return current_date_to_set;
}

/**
 * Get the specific dates based on the global or custom settings.
 * @returns String
 * @since 9.27.0
 */
function orddd_get_specific_dates() {
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var specific_dates = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].orddd_delivery_dates : orddd_params.orddd_delivery_dates;

    return specific_dates;
}

/**
 * Get the current date based on the global or custom settings.
 * @returns String
 * @since 9.27.0
 */
function orddd_get_current_day() {
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var current_day = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].orddd_current_day : orddd_params.orddd_current_day;

    return current_day;
}

/**
 * Get the next day's date based on the global or custom settings.
 * @returns String
 * @since 9.27.0
 */
function orddd_get_next_day() {
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var next_day = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].orddd_next_day : orddd_params.orddd_next_day;

    return next_day;
}

/**
 * Check if the specific dates are enabled based on the global or custom settings.
 * @returns String
 * @since 9.27.0
 */
function orddd_specific_dates_enabled() {
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var specific_enabled = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].orddd_specific_delivery_dates : orddd_params.orddd_specific_delivery_dates;

    return specific_enabled;
}

/**
 * Check if all weekdays are disabled based on the global or custom settings.
 * @returns String
 * @since 9.27.0
 */
function orddd_is_all_weekdays_disabled() {
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var all_weekdays_disabled = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].orddd_is_all_weekdays_disabled : orddd_params.orddd_is_all_weekdays_disabled;

    return all_weekdays_disabled;
}

/**
 * Check if the weekday is enabled based on the global or custom settings.
 * @returns String
 * @since 9.27.0
 */
function orddd_day_check( day ) {
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var weekday_enabled = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'][day] : orddd_params[day];

    return weekday_enabled;
}

/**
 * Get the days to be disabled when same day/next day cutoff is enabled.
 * @returns String
 * @since 9.27.0
 */
function orddd_get_disabled_days() {
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var disabled_days = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].orddd_disabled_days_str : orddd_params.orddd_disabled_days_str;

    return disabled_days;
}

/**
 * Get the weekdays to be disabled when same day/next day cutoff is enabled.
 * @returns String
 * @since 9.27.0
 */
function orddd_get_disabled_weekdays() {
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var disabled_weekdays = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].orddd_disabled_weekdays_str : orddd_params.orddd_disabled_weekdays_str;

    return disabled_weekdays;
}

/**
 * Get the min hour for first date based on the global or custom settings.
 * @returns String
 * @since 9.27.0
 */
function orddd_get_min_hour() {
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var min_hour = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].orddd_min_hour : orddd_params.orddd_min_hour;

    return min_hour;
}

/**
 * Get the min minute for first date based on the global or custom settings.
 * @returns String
 * @since 9.27.0
 */
function orddd_get_min_minute() {
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var min_minute = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].orddd_min_minute : orddd_params.orddd_min_minute;

    return min_minute;
}

/**
 * Get the max hour for time slider based on the global or custom settings.
 * @returns String
 * @since 9.27.0
 */
function orddd_get_max_hour_set() {
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var max_hour_set = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].orddd_max_hour_set : orddd_params.orddd_max_hour_set;

    return max_hour_set;
}

/**
 * Get the max minute for time slider based on the global or custom settings.
 * @returns String
 * @since 9.27.0
 */
function orddd_get_max_mins_set() {
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var max_mins_set = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].orddd_max_mins_set : orddd_params.orddd_max_mins_set;

    return max_mins_set;
}

/**
 * Get the min hour for time slider based on the global or custom settings.
 * @returns String
 * @since 9.27.0
 */
function orddd_get_min_hour_set() {
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var min_hour_set = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].orddd_min_hour_set : orddd_params.orddd_min_hour_set;

    return min_hour_set;
}

/**
 * Get the min minute for time slider based on the global or custom settings.
 * @returns String
 * @since 9.27.0
 */
function orddd_get_min_mins_set() {
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var min_mins_set = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].orddd_min_mins_set : orddd_params.orddd_min_mins_set;

    return min_mins_set;
}

/**
 * Check if the same day off is reached.
 * @returns String
 * @since 9.27.0
 */
function orddd_same_day_cutoff_reached() {
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var is_sameday_cutoff_reached = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].is_sameday_cutoff_reached : orddd_params.is_sameday_cutoff_reached;

    return is_sameday_cutoff_reached;
}


/**
 * Check if the next day off is reached.
 * @returns String
 * @since 9.27.0
 */
function orddd_next_day_cutoff_reached() {
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var is_nextday_cutoff_reached = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].is_nextday_cutoff_reached : orddd_params.is_nextday_cutoff_reached;

    return is_nextday_cutoff_reached;
}

/**
 * Check if calendar or text block is enabled.
 * @returns String
 * @since 9.27.0
 */
function orddd_get_checkout_options() {
    var orddd_unique_settings = orddd_params.orddd_unique_custom_settings;
    var checkout_option = 'global_settings' != orddd_unique_settings && '' != orddd_unique_settings ? orddd_params['custom_settings'].orddd_delivery_checkout_options : orddd_params.orddd_delivery_checkout_options;
    return checkout_option;
}

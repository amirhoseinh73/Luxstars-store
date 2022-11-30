<?php
/**
 * Order Delivery Date Pro for WooCommerce
 *
 * Handles the Delivery Date and Time Slot for Multiple Shipping Addresses. 
 * It has functions for admin as well as frontend. 
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Pro-for-WooCommerce/Integration
 * @since       4.7
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Main class which handles different Delivery Date and Time Slot for different
 * shipping addresses from Multiple Shipping Addresses plugin.
 *
 * @class orddd_shipping_multiple_address
 */
class orddd_shipping_multiple_address {
    
    /**
     * product_ids
     * @since 4.7
     */
    private $product_ids = array();

    /**
     * Default constructor
     * 
     * @since 4.7
     */
    public function __construct() {
        if( get_option( 'orddd_shipping_multiple_address_compatibility' ) == 'on' ) {
            add_action( 'wc_ms_address_table_head',                array( &$this, 'orddd_shipping_multiple_heading_address_header' ) );
            add_action( 'wc_ms_multiple_address_table_row',        array( &$this, 'orddd_shipping_multuple_delivery_date_table_row' ), 10, 3 );
            add_action( 'wc_ms_multiple_address_table_row',        array( 'orddd_scripts', 'orddd_front_scripts_js' ) );
            add_action( 'wc_ms_multiple_address_table_row',        array( 'orddd_scripts', 'orddd_front_scripts_css' ) );
            add_action( 'template_redirect',                       array( &$this, 'save_orddd_dates' ), 1 );
            add_action( 'wc_ms_shipping_package_block',            array( &$this, 'orddd_package_block' ), 10, 2 );
            add_action( 'wc_ms_order_package_block_after_address', array( &$this, 'orddd_session_view_order' ), 10, 3 );
            add_action( 'woocommerce_checkout_update_order_meta',  array( &$this, 'orddd_update_order_item_wcms' ) );
            add_filter( 'woocommerce_checkout_fields',             array( &$this, 'orddd_remove_checkout_fields' ), 100, 1 );
            add_action( 'wc_ms_shop_table_head',                   array( &$this, 'orddd_wcms_order_recieved_head' ) );
            add_action( 'wc_ms_shop_table_row',                    array( &$this, 'orddd_wcms_order_recieved_row' ), 10, 2 );
            add_action( 'woocommerce_cart_calculate_fees',         array( &$this, 'orddd_get_delivery_charges' ) );

            if ( ! is_user_logged_in() ) {
				add_action( 'wp_ajax_nopriv_orddd_multiple_address_timeslots', array( &$this, 'orddd_multiple_address_timeslots' ) );
			} else {
				add_action( 'wp_ajax_orddd_multiple_address_timeslots', array( &$this, 'orddd_multiple_address_timeslots' ) );
            }
        }
    }
    
    /**
     * This function adds Delivery Date header in the table added by the Multiple Shipping Addresses plugin.
     *
     * @hook wc_ms_address_table_head
     * @since 4.7
     */
    function orddd_shipping_multiple_heading_address_header() {
        if( get_option( 'orddd_enable_time_slot' ) == "on" ) {
            ?>
            <th class="delivery-date"><?php _e( get_option( 'orddd_delivery_date_field_label' ) . " and " . get_option( 'orddd_delivery_timeslot_field_label' ), 'order-delivery-date' ); ?>
            <?php
            if( get_option( 'orddd_time_slot_mandatory' ) == "checked" || get_option( 'orddd_date_field_mandatory' ) == "checked" ) {
                ?>
                <abbr class="required" title="required" id="orddd_shipping_multiple_mandatory">*</abbr>
                <?php 
            }
            ?></th><?php
        } else {
            ?>
            <th class="delivery-date"><?php _e( get_option( 'orddd_delivery_date_field_label' ), 'order-delivery-date' ); ?><?php
            if( get_option( 'orddd_date_field_mandatory' ) == "checked" ) {
                ?>
                <abbr class="required" title="required" id="orddd_shipping_multiple_mandatory">*</abbr>
                <?php 
            }
            ?></th><?php
        }
    }
    
    /**
     * This function adds Delivery Date and Time Slot fields as a row in the table.
     *
     * @hook wc_ms_multiple_address_table_row
     *
     * @param string $key - Cart Key
     * @param array $value - Cart Details 
     * @param string $address_key - Address Key
     *
     * @globals resource $wpdb
     * @globals array $orddd_date_formats
     * @globals resource $post
     * @globals resource $woocommerce
     * @globals array $orddd_languages
     * @globals array $orddd_weekdays
     * @since 4.7
     */
    function orddd_shipping_multuple_delivery_date_table_row( $key, $value, $address_key ) {
        global $wpdb, $orddd_date_formats, $post, $woocommerce, $orddd_languages, $orddd_weekdays, $orddd_version;
        $gmt = false;
        if( has_filter( 'orddd_gmt_calculations' ) ) {
            $gmt = apply_filters( 'orddd_gmt_calculations', '' );
        }
        $current_time = current_time( 'timestamp', $gmt );
        $orddd_shipping = array();
        $default_e_delivery_date = $default_h_delivery_date = "";
        $delivery_dates = wcms_session_get( 'wcms_item_delivery_dates' );
        $checkout = new WC_Checkout;
        $pid = $value[ 'product_id' ];
        if( in_array( $pid, $this->product_ids ) ) {
            $array_keys = array_keys( $this->product_ids, $pid );
            $unique_arr = explode( "_", end( $array_keys ) );
            $quantity = $pid . "_" . ( $unique_arr[ 1 ] + 1 );  
        } else {
            $quantity = $pid . "_0";
        }
        $this->product_ids[ $quantity ] = $pid;
        if( is_array( $delivery_dates ) && count( $delivery_dates ) > 0 ) {
            $quantity_arr = explode( "_", $quantity );
            if( isset( $delivery_dates[ 'h_deliverydate_' . $key . '_' . $pid . '_' . $address_key ] ) ) {
                $default_h_delivery_date = $delivery_dates[ 'h_deliverydate_' . $key . '_' . $pid . '_' . $address_key ];
                if( get_option( 'orddd_enable_delivery_time' ) == 'on' ) {
                    if ( isset( $delivery_dates[ 'e_deliverydate_' . $key . '_' . $pid . '_' . $address_key ] ) ) {
                        $time_settings_arr = explode( " ", $delivery_dates[ 'e_deliverydate_' . $key . '_' . $pid . '_' . $address_key ] );
                        $time_settings_arr_1 = array_pop( $time_settings_arr );
                        $time_settings = date( "H:i", strtotime( end( $time_settings_arr ) ) );
                        $default_h_delivery_date = $default_h_delivery_date . " " . $time_settings;
                    }
                }
            }
        }
        ?>
        <td>
            <?php
            if ( get_option( 'orddd_enable_delivery_date' ) == 'on' ) {
                wp_enqueue_script( 'initialize-datepicker-functions-orddd' );
                if( true == wp_script_is( 'jquery-ui-orddd-timepicker-addon', 'registered' ) ) {
                    wp_enqueue_script( 'jquery-ui-orddd-timepicker-addon' );
                }
                
                if( true == wp_script_is( 'jquery-ui-orddd-sliderAccess', 'registered' ) ) {
                    wp_enqueue_script( 'jquery-ui-orddd-sliderAccess' );
                }

                wp_enqueue_style( 'jquery-ui-timepicker-addon-orddd' );
                wp_enqueue_style( 'jquery-ui-style-orddd' );
                wp_enqueue_style( 'orddd-datepicker' );

                $show = 'datepicker';
                $display = $min_date = '';
                $disabled_days = array();
                $field_name = 'e_deliverydate_' . $quantity;
                
                $hidden_variables = orddd_common::load_hidden_fields();
                echo $hidden_variables;
                
                if ( get_option( 'orddd_enable_delivery_time' ) == 'on' ) {
                    if ( get_option( 'orddd_enable_delivery_date' ) == 'on' ) {
                        $show = 'datetimepicker';
                    } else {
                        $show = 'timepicker';
                    }
                }
                if( get_option( 'start_of_week' ) != '' ) {
                    $first_day_of_week = get_option( 'start_of_week' );
                }
        
                $options_arr = orddd_common::get_datepicker_options( '','' );
                $options_arr =  explode( '&', $options_arr );
                $options_str = '';

                foreach( $options_arr as $option ) {
                    $options_str .= $option . ",";
                }

                if ( get_option( 'orddd_enable_same_day_delivery' ) == 'on' && get_option( 'orddd_enable_delivery_date' ) == 'on' ){
                    $current_date = date( 'd', $current_time );
                    $current_month = date( 'm', $current_time );
                    $current_year = date( 'Y', $current_time );
                    $cut_off_hour = get_option( 'orddd_disable_same_day_delivery_after_hours' );
                    $cut_off_minute = get_option( 'orddd_disable_same_day_delivery_after_minutes' );
                    $cut_off_timestamp = gmmktime( $cut_off_hour, $cut_off_minute, 0, $current_month, $current_date, $current_year );
                    if ( $cut_off_timestamp > $current_time ) {
                    } else {
                        $disabled_days[] = date( ORDDD_HOLIDAY_DATE_FORMAT, $current_time );
                    }
                }
        
                if ( get_option( 'orddd_enable_next_day_delivery' ) == 'on' && get_option( 'orddd_enable_delivery_date' ) == 'on' ) {
                    $current_date = date( 'd', $current_time );
                    $current_month = date( 'm', $current_time );
                    $current_year = date( 'Y', $current_time );
                    $cut_off_hour = get_option( 'orddd_disable_next_day_delivery_after_hours' );
                    $cut_off_minute = get_option( 'orddd_disable_next_day_delivery_after_minutes' );
                    $cut_off_timestamp = gmmktime( $cut_off_hour, $cut_off_minute, 0, $current_month, $current_date, $current_year );
                    if ( $cut_off_timestamp > $current_time ) {
                    } else {
                        $disabled_days[] = date( ORDDD_HOLIDAY_DATE_FORMAT, $current_time +86400 );
                    }
                }
            
                $disabled_days_var = 'var startDaysDisabled = [];';
                if ( is_array( $disabled_days ) && count( $disabled_days ) > 0 ) {
                    $disabled_days_str = '"' . implode( '","', $disabled_days ) . '"';
                    $disabled_days_var = 'var startDaysDisabled = [' . $disabled_days_str . '];';
                }

                $orddd_shipping['default_h_date']   = $default_h_delivery_date;
                $orddd_shipping['quantity']         = $quantity;
                $orddd_shipping['orddd_field_name'] = 'e_deliverydate';
                $orddd_shipping['pid']              = $pid;
                $orddd_shipping['address_key']      = $address_key;
                $orddd_shipping['key']              = $key;
                
                $clear_button_text = '';
                if( $show == "datepicker" ){
                    $clear_button_text = "showButtonPanel: true, closeText: " . __( "'Clear'", "order-delivery-date" ) . ",";
                }
                
                $display_datepicker = 'var default_h_date = "' . $default_h_delivery_date . '";
                    if( default_h_date != "" ) { 
                        var time_settings = default_h_date.split( " " );
                        var split = time_settings[ 0 ].split( "-" );
                        split[1] = split[1] - 1;
                        if( typeof time_settings[ 1 ] != "undefined" ) {
                            var time_settings_values = time_settings[ 1 ].split( ":" ); 
                            var default_date = new Date( split[2], split[1], split[0], time_settings_values[ 0 ], time_settings_values[ 1 ] );
                        } else {
                            var default_date = new Date( split[2], split[1], split[0] );
                        }
                        jQuery( "#h_deliverydate_' . $quantity . '" ).val( time_settings[ 0 ] );
                        var default_date_inst = { selectedMonth: parseInt( split[ 1 ] ) - 1, selectedDay: parseInt( split[ 0 ] ), selectedYear: parseInt( split[ 2 ] ) };
                        show_times( default_h_date, default_date_inst );
                    }
                   
                jQuery( "#' . $field_name . '" ).val( "" ).' . $show . '( {' . $options_str . 'beforeShowDay: chd, firstDay: parseInt( ' . $first_day_of_week . ' ),' . $clear_button_text . '
                    onClose:function( dateStr, inst ) {
                        if ( dateStr != "" ) {
                            var monthValue = inst.selectedMonth+1;
                            var dayValue = inst.selectedDay;
                            var yearValue = inst.selectedYear;
                            var all = dayValue + "-" + monthValue + "-" + yearValue;
                            jQuery( "#h_deliverydate_' . $quantity . '" ).val( all );
                            var hourValue = jQuery( ".ui_tpicker_time" ).html();
                            jQuery( "#orddd_time_settings_selected_' . $quantity . '" ).val( hourValue );
                            var event = arguments.callee.caller.caller.arguments[0];
                            // If "Clear" gets clicked, then really clear it
                            if( typeof( event ) !== "undefined" ) {
                                if ( jQuery( event.delegateTarget ).hasClass( "ui-datepicker-close" ) ) {
                                    jQuery( this ).val( "" );
                                    jQuery( "#h_deliverydate_' . $quantity . '" ).val( "" );
                                    jQuery( "#time_slot_' . $quantity . '" ).prepend( "<option value=\"select\">Select a time slot</option>" );
                                    jQuery( "#time_slot_' . $quantity . '" ).children( "option:not(:first)" ).remove();
                                    jQuery( "#time_slot_' . $quantity . '" ).attr( "disabled", "disabled" );
                                    jQuery( "#time_slot_' . $quantity . '" ).attr( "style", "cursor: not-allowed !important" );
                                    jQuery( "#time_slot_field_' . $quantity . '" ).css({ opacity: "0.5" });
                                }
                            }
                        }
                        jQuery( "#' . $field_name . '" ).blur();
                    },
                    onSelect: show_times,
                }).focus( function ( event ) {
                    jQuery.datepicker.afterShow( event );
                }).' . $show . '( "setDate", default_date );
                function show_times( date, inst ) {
                    if( jQuery( "#orddd_enable_time_slot" ).val() == "on" ) {
                        var monthValue = inst.selectedMonth+1;
                        var dayValue = inst.selectedDay;
                        var yearValue = inst.selectedYear;
                        var all = dayValue + "-" + monthValue + "-" + yearValue;
                        var date_YMD = yearValue + "-" + monthValue + "-" + dayValue;

                        var data = {
                            current_date: all,
                            date_YMD: date_YMD,
                            post_id: "' . $pid . '",
                            min_date: jQuery( "#orddd_min_date_set" ).val(),
                            action: "orddd_multiple_address_timeslots",
                            address_key: "' . $address_key . '",
                            cart_key: "' . $key . '"
                        };
                        jQuery( "#time_slot_' . $quantity . '" ).attr("disabled", "disabled");
                        jQuery( "#time_slot_field_' . $quantity . '" ).attr( "style", "opacity: 0.5" );
                        jQuery.post( "' . get_admin_url() . '/admin-ajax.php", data, function( response ) {
                            jQuery( "#time_slot_field_' . $quantity . '" ).attr( "style" ,"opacity:1" );
                            jQuery( "#time_slot_' . $quantity . '" ).attr( "style", "cursor: pointer !important" );
                            jQuery( "#time_slot_' . $quantity . '" ).removeAttr( "disabled" );
                            
                            var orddd_time_slots = response.split( "/" );
                            jQuery( "#time_slot_' . $quantity . '" ).empty(); 
                            var selected_value = "";
                            for( i = 0; i < orddd_time_slots.length; i++ ) {
                                var time_slot_to_display = orddd_time_slots[ i ].split( "_" );
                                console.log( time_slot_to_display );
                                if( "Select a time slot" == time_slot_to_display[ 0 ] ) {
                                    jQuery( "#time_slot_' . $quantity . '" ).append( jQuery( "<option></option>" ).attr( { value:"select", selected:"selected" } ).text( orddd_time_slots[ 0 ] ) );
                                    selected_value = orddd_time_slots[ i ];
                                } else if( "asap" == time_slot_to_display[ 0 ] ) {
                                    if( typeof time_slot_to_display[ 2 ] != "undefined" ) {
                                        jQuery( "#time_slot_' . $quantity . ' option:selected" ).removeAttr( "selected" );
                                        jQuery( "#time_slot_' . $quantity . '" ).append( jQuery( "<option></option>" ).attr( {value:time_slot_to_display[ 0 ], selected:"selected"}).text( jsL10n.asapText ) );
                                        selected_value = time_slot_to_display[ 0 ];    
                                    } else {
                                        jQuery( "#time_slot_' . $quantity . '" ).append( jQuery( "<option></option>" ).attr( {value:time_slot_to_display[ 0 ]} ).text( jsL10n.asapText ) );
                                    }
                                } else if( typeof time_slot_to_display[ 2 ] != "undefined" ) {
                                    jQuery( "#time_slot_' . $quantity . ' option:selected" ).removeAttr( "selected" );
                                    if( typeof time_slot_to_display[ 1 ] != "undefined" && time_slot_to_display[ 1 ] != "" ) {
                                        var time_slot_charges = decodeHtml( time_slot_to_display[ 1 ] );
                                        jQuery( "#time_slot_' . $quantity . '" ).append( jQuery( "<option></option>" ).attr( {value:time_slot_to_display[ 0 ], selected:"selected"}).text( time_slot_to_display[ 0 ] + " " + time_slot_charges ) );
                                    } else {
                                        jQuery( "#time_slot_' . $quantity . '" ).append( jQuery( "<option></option>" ).attr( {value:time_slot_to_display[ 0 ], selected:"selected"}).text( time_slot_to_display[ 0 ] ) );
                                    }
                                    selected_value = time_slot_to_display[ 0 ];
                                } else {
                                    if( typeof time_slot_to_display[ 1 ] != "undefined" && time_slot_to_display[ 1 ] != "" ) {
                                        var time_slot_charges = decodeHtml( time_slot_to_display[ 1 ] );
                                        jQuery( "#time_slot_' . $quantity . '" ).append( jQuery( "<option></option>" ).attr( "value", time_slot_to_display[ 0 ] ).text( time_slot_to_display[ 0 ] + " " + time_slot_charges ) );
                                    } else {
                                        jQuery( "#time_slot_' . $quantity . '" ).append( jQuery( "<option></option>" ).attr( "value", time_slot_to_display[ 0 ] ).text( time_slot_to_display[ 0 ] ) );
                                    }
                                }                   
                            }
                        })
                    }
                }';
                $display .= '<script type="text/javascript">
                jQuery( document ).ready(function() {
                    jQuery( "#' . $field_name . '" ).width( "250px" );
                    jQuery( "#' . $field_name . '" ).attr( "readonly", true );
                    var formats = ["d.m.y", "d MM, yy","MM d, yy"];
                    jQuery.extend( jQuery.datepicker, { afterShow: function( event ) {
                        jQuery.datepicker._getInst( event.target ).dpDiv.css( "z-index", 99 );
                    } } );
                    '.$display_datepicker;
                    if ( get_option( 'orddd_delivery_date_field_note' ) != '' ) {
                        $display .= 'jQuery( "#' . $field_name . '" ).parent().append( "<br><small class=\'orddd_field_note\'>'.__( get_option( 'orddd_delivery_date_field_note' ), 'order-delivery-date' ).'</small>" );';
                    }
                $display .= '} );
                
                '.$disabled_days_var.'
                                
                </script>';
                echo $display;
                
                $is_delivery_enabled = orddd_common::orddd_is_delivery_enabled();
                if( $is_delivery_enabled == 'yes' ) {
                    $validate_wpefield = false;
                    if ( get_option( 'orddd_date_field_mandatory' ) == 'checked' ) {
                        $validate_wpefield = true;
                    }
                    
                    do_action( 'orddd_before_checkout_delivery_date', $checkout );
                    
                    woocommerce_form_field( 'e_deliverydate_' . $quantity, array(
                                                'type'              => 'text',
                                                'required'          => $validate_wpefield,
                                                'placeholder'       => __( get_option( 'orddd_delivery_date_field_placeholder' ), 'order-delivery-date' ),
                                                'custom_attributes' => array( 'style'=>'cursor:text !important;')
                    ),
                    $checkout->get_value( 'e_deliverydate_' . $quantity ) );
                   
                    woocommerce_form_field( 'h_deliverydate_' . $quantity, array(
                                                'type' => 'text',
                                                'custom_attributes' => array( 'style'=>'display: none !important;' ) ),
                    $checkout->get_value( 'h_deliverydate_' . $quantity ) );
                    
                    do_action( 'orddd_after_checkout_delivery_date', $checkout );
                }
                
                if ( get_option( 'orddd_enable_delivery_date' ) == 'on' && ( get_option( 'orddd_enable_time_slot' ) == 'on' ) ) {
                    $time_slot_str = get_option( 'orddd_delivery_time_slot_log' );
                    $time_slots = json_decode( $time_slot_str, true );
                    $result = array ( __( "Select a time slot", "order-delivery-date" ) );
                    $validate_wpefield = false;
                    if (  get_option( 'orddd_time_slot_mandatory' ) == 'checked' ) {
                        $validate_wpefield = true;
                    }
                
                    do_action( 'orddd_before_checkout_time_slot', $checkout );
                
                    woocommerce_form_field( 'time_slot_' . $quantity, array(
                        'type'              => 'select',
                        'required'          => $validate_wpefield,
                        'options'           => $result,
                        'custom_attributes' => array( 'disabled'=>'disabled', 'style'=>'cursor:not-allowed !important;' )
                    ),
                    $checkout->get_value( 'time_slot_' . $quantity ) );
                    do_action( 'orddd_after_checkout_time_slot', $checkout );
                    // code to remove the choosen class added from checkout field editor plugin.
                    echo '<script type="text/javascript" language="javascript">
                    jQuery( document ).ready( function() {
                        jQuery( "#time_slot" ).removeClass();
                    });
                    </script>';
                }
            }
            ?>
        </td>
        <?php 
    }

    function orddd_multiple_address_timeslots() {
        //Time slots for multiple address from Shipping Multiple addresses plugin.
        $time_slot_for_multiple = '';
		if ( function_exists( 'wcms_session_isset' ) ) {
		    if ( wcms_session_isset( 'wcms_item_delivery_dates' ) ) {
		        $delivery_dates = wcms_session_get( 'wcms_item_delivery_dates' );
                $pid = '';
                if( isset( $_POST[ 'post_id' ] ) ) {
                    $pid = $_POST[ 'post_id' ];    
                }
		        
		        if( is_array( $delivery_dates ) && count( $delivery_dates ) > 0 ) {
		            if( isset( $_POST[ 'cart_key' ] ) && isset( $_POST[ 'address_key' ] ) && isset( $delivery_dates[ 'time_slot_' . $_POST[ 'cart_key' ] . '_' . $pid . '_' . $_POST[ 'address_key' ] ] ) ) {
		                $time_slot_for_multiple = $delivery_dates[ 'time_slot_' . $_POST[ 'cart_key' ] . '_' . $pid . '_' . $_POST[ 'address_key' ] ];
		            }
		        }
		    }
		}

        $time_slots_to_show_timestamp = orddd_common::orddd_get_timeslot_display( $_POST['date_YMD'], '0' );
        asort( $time_slots_to_show_timestamp );
		if ( is_array( $time_slots_to_show_timestamp ) && count( $time_slots_to_show_timestamp ) > 1 ) {
			unset( $time_slots_to_show_timestamp[ 'NA' ] );
        }

        $i = 1;
        $time_slot_var = '';
        // Changing the seperator for time slots from comma(,) to backslash(/) to avoid conflicts with the 
        // decimal seperator and thousand seperator. 
        $time_slot_var .= "select/";
        foreach ( $time_slots_to_show_timestamp as $key => $value ) {
            
            $sel = '';
            if( $time_slot_for_multiple != '' && $key == $time_slot_for_multiple ) {
                $sel = 'selected';
            } else if ( $i == 1 && get_option( 'orddd_auto_populate_first_available_time_slot' ) == 'on' ) {
                $sel = 'selected';
            }

            $current_date = isset( $_POST[ 'current_date' ] ) ? $_POST[ 'current_date' ] : false;
            
            $timeslot_charges = orddd_common::orddd_get_timeslot_charges( $key, $current_date );

            $timeslot_charges_label = '';
            if ( "" != $timeslot_charges ) {
                $timeslot_charges_label = "(" . $timeslot_charges . ")";
                if( has_filter( 'orddd_timeslot_charges_label' ) ) {
                    $timeslot_charges_label = apply_filters( 'orddd_timeslot_charges_label', $timeslot_charges_label );
                }
            }

            $key_i18n = '';
            if ( 'asap' !== $key && 'NA' !== $key ) {

                if( $time_format_to_show === 'h:i A' ) {
                    $key = orddd_common::orddd_change_time_slot_format( $key, $time_format_to_show );
                }

                $time_arr = explode( ' - ', $key );

                $from_time = date_i18n( $time_format_to_show, strtotime( $current_date . " " . $time_arr[0] ) );
                if ( isset( $time_arr[1] ) ) {
					$to_time  = date_i18n( $time_format_to_show, strtotime( $current_date . ' ' . $time_arr[1] ) );
					$key_i18n = $from_time . ' - ' . $to_time;
				} else {
					$key_i18n = $from_time;
				}
            }

            if( $sel != '' ) {
                $time_slot_var .=  $key . "_" . $key_i18n . "_" . $timeslot_charges_label . "_" . $sel . "/";
            } else {
                $time_slot_var .=  $key . "_" . $key_i18n . "_" . $timeslot_charges_label . "/";
            }

            $i++;
        }

        $time_slot_var = substr( $time_slot_var, 0, strlen( $time_slot_var )-1 );
        echo $time_slot_var;
        die();

    }
    
    /**
     * Save the Delivery information for specific address in the Session.
     *
     * @globals resource $woocommerce
     *
     * @hook template_redirect
     * 
     * @since 4.7
     */
    function save_orddd_dates () {
        if( isset( $_POST[ 'shipping_address_action' ] ) && $_POST[ 'shipping_address_action' ] == 'save' && !isset( $_POST[ 'delete_line' ] ) ) {
            global $woocommerce;
            $delivery_dates = array();
            $fields = $woocommerce->countries->get_address_fields( $woocommerce->countries->get_base_country(), 'shipping_' );
            if ( isset( $_POST[ 'items' ] ) ) {
                $items = $_POST[ 'items' ];
                $cart_items = wcms_get_real_cart_items();
                foreach ( $items as $cart_key => $item ) {
                    $qtys           = $item[ 'qty' ];
                    $item_addresses = $item[ 'address' ];
        
                    $product_id = $cart_items[ $cart_key ][ 'product_id' ];
                    $sig        = $cart_key .'_'. $product_id .'_';
                    $_sig       = '';
                    foreach ( $item_addresses as $idx => $item_address ) {
                        $address_id = $item_address;
                        $i = 1;
                        for ( $x = 0; $x < $qtys[ $idx ]; $x++ ) {
                            while ( isset( $data[ 'shipping_first_name_' . $sig . $i ] ) ) {
                                $i++;
                            }
                            $_sig = $sig . $address_id;
                            if ( $fields ) {
                                foreach ( $fields as $key => $field ) {
                                    if( isset( $_POST[ $key . '_' . $address_id ] ) ) {
                                        $data[ $key . '_' . $_sig ] = $_POST[ $key . '_' . $address_id ];
                                    }
                                }
                            }
                            
                            if( isset( $_POST[ 'e_deliverydate_' . $product_id . '_' . ( $idx ) ] ) && $_POST[ 'e_deliverydate_' . $product_id . '_' . ( $idx ) ] != "" ) {
                               $delivery_dates[ 'e_deliverydate_' . $_sig ] = $_POST[ 'e_deliverydate_' . $product_id . '_' . ( $idx ) ];
                            } else if( get_option( 'orddd_date_field_mandatory' ) == 'checked' ) {
                                $message = '<strong>' . get_option( 'orddd_delivery_date_field_label' ) . '</strong>' . ' ' . __( 'is a required field.', 'order-delivery-date' );
                                wc_add_notice( $message, $notice_type = 'error' );
                                if( get_option( 'orddd_time_slot_mandatory' ) == 'checked' && get_option( 'orddd_enable_time_slot' ) == 'on' && !isset( $_POST[ 'time_slot_' . $product_id . '_' . ( $idx ) ] ) ) {
                                } else {
                                    header( 'Location: ' . $_SERVER[ 'HTTP_REFERER' ] );
                                    die();
                                }
                            }
                            if( isset( $_POST[ 'h_deliverydate_' . $product_id . '_' . ( $idx ) ] ) && $_POST[ 'h_deliverydate_' . $product_id . '_' . ( $idx ) ] != "" ) {
                               $delivery_dates[ 'h_deliverydate_' . $_sig ] = $_POST[ 'h_deliverydate_' . $product_id . '_' . ( $idx ) ];
                            }
                            if( isset( $_POST[ 'time_slot_' . $product_id . '_' . ( $idx ) ] ) && $_POST[ 'time_slot_' . $product_id . '_' . ( $idx ) ] != "" && $_POST[ 'time_slot_' . $product_id . '_' . ( $idx ) ] != 'choose' && $_POST[ 'time_slot_' . $product_id . '_' . ( $idx ) ] != "NA" && $_POST[ 'time_slot_' . $product_id . '_' . ( $idx ) ] != "select" ) {
                                if( $_POST[ 'time_slot_' . $product_id . '_' . ( $idx ) ] == 'asap' ) {
                                    $delivery_dates[ 'time_slot_'. $_sig ] = __( 'As Soon As Possible.', 'order-delivery-date' );
                                } else {
                                    $delivery_dates[ 'time_slot_'. $_sig ] = $_POST[ 'time_slot_' . $product_id . '_' . ( $idx ) ];
                                }
                            } else if( get_option( 'orddd_time_slot_mandatory' ) == 'checked' && get_option( 'orddd_enable_time_slot' ) == 'on' ) {
                                $message = '<strong>' . get_option( 'orddd_delivery_timeslot_field_label' ) . '</strong>' . ' ' .__( 'is a required field.', 'order-delivery-date' );
                                wc_add_notice( $message, $notice_type = 'error' );
                                header( 'Location: ' . $_SERVER[ 'HTTP_REFERER' ] );
                                die();
                            } 
                        }
                    }
                }
                wcms_session_set( 'wcms_item_delivery_dates', $delivery_dates );
            }
        }
    }
    
    /**
     * Show the Delivery information on the shipping packages blocks
     *
     * @param string $x - Cart Key
     * @param array $package - Shipping Package block
     *
     * @globals resource $woocommerce
     *
     * @hook wc_ms_shipping_package_block
     * 
     * @since 4.7
     */
    function orddd_package_block( $x, $package ) {
        global $woocommerce;
        $address_key = orddd_common::orddd_get_address_key( $package );
        $delivery_dates = wcms_session_get( 'wcms_item_delivery_dates' );
        foreach ( $package[ 'contents' ] as $package_key => $values ) {
            $product_id = $values['product_id'];
            $key = "e_deliverydate_" . $package_key . "_" . $product_id . "_" . $address_key;
            $timeslot_key = "time_slot_" . $package_key . "_" . $product_id . "_" . $address_key;
            foreach( $delivery_dates as $d_key => $d_value ) {
                if( $d_key == $key ) {
                    echo get_option( 'orddd_delivery_date_field_label' ) . ': <delivey_date_' . $d_key . '>' . $d_value . '</delivey_date_' . $d_key . '><br />';
                }
                if( $d_key == $timeslot_key ) {
                    if( $timeslot_key == 'asap' ) {
                        echo get_option( 'orddd_delivery_timeslot_field_label' ) . ': <time_slot_' . $d_key . '>' . __( "As Soon As Possible.", 'order-delivery-date' ) . '</time_slot_' . $d_key . '><br />';
                    } else {
                        echo get_option( 'orddd_delivery_timeslot_field_label' ) . ': <time_slot_' . $d_key . '>' . $d_value . '</time_slot_' . $d_key . '><br />';    
                    }
                }
            }
        }
    }
    
    /**
     * Show the Delivery information after the Package details in the Your order table.
     *
     * @param WC_Order $order - Order Object
     * @param string $x - Cart Key
     * @param array $package - Shipping Package block
     *
     * @globals resource $woocommerce
     *
     * @hook wc_ms_order_package_block_after_address
     * 
     * @since 4.7
     */    
    function orddd_session_view_order( $order, $package, $x ) {
        global $woocommerce;
        $user = wp_get_current_user();
        $address_key = orddd_common::orddd_get_address_key( $package );
        foreach ( $package['contents'] as $package_key => $values ) {
            $product_id = $values['product_id'];
            $key = "e_deliverydate_" . $package_key . "_" . $product_id . "_" . $address_key;
            $timeslot_key = "time_slot_" . $package_key . "_" . $product_id . "_" . $address_key;
            if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
                $order_id = $order->get_id();
            } else {
                $order_id = $order->id;
            }
            $delivery_date = get_post_meta( $order_id, '_orddd_shipping_multiple_addresss_' . $key, true );
            $timeslot = get_post_meta( $order_id, '_orddd_shipping_multiple_addresss_' . $timeslot_key, true );
            if( $delivery_date != "" && $delivery_date != null ) {
                echo get_option( 'orddd_delivery_date_field_label' ) . ': ' . $delivery_date . '<br />';
            }
            if( $timeslot != "" && $timeslot != null ) {
                echo get_option( 'orddd_delivery_timeslot_field_label' ) . ': ' . $timeslot . '<br />';
            }
        }
    }
    
    /**
     * Update the Delivery information in the post_meta table.
     *
     * @param int $order_id - Order ID
     *
     * @hook woocommerce_checkout_update_order_meta
     * 
     * @since 4.7
     */
    function orddd_update_order_item_wcms( $order_id ) {
        $timestamps = array();
        $date_format = 'dd-mm-y';
        
        if ( function_exists( 'wcms_session_isset' ) ) {
            if ( wcms_session_isset( 'wcms_item_delivery_dates' ) ) {
                $delivery_dates_sess = wcms_session_get( 'wcms_item_delivery_dates' );
                foreach( $delivery_dates_sess as $key => $val ) {
                    update_post_meta( $order_id, '_orddd_shipping_multiple_addresss_' . $key, $val );
                    if( has_filter( 'orddd_before_delivery_date_update' ) ) {
                        $delivery_date = apply_filters( 'orddd_before_delivery_date_update', $val );
                    }
                    if( preg_match( '/e_deliverydate/', $key ) ) {
                        $address_key = explode( "_", $key );
                        if( isset( $delivery_dates_sess[ "h_deliverydate_" . $address_key[ 2 ] . "_" . $address_key[ 3 ] . "_" . $address_key [ 4 ] ] ) ) {
                            $h_deliverydate = $delivery_dates_sess[ "h_deliverydate_" . $address_key[ 2 ] . "_" . $address_key[ 3 ] . "_" . $address_key [ 4 ] ];
                        }
                        
                        if( isset( $delivery_dates_sess[ "time_slot_" . $address_key[ 2 ] . "_" . $address_key[ 3 ] . "_" . $address_key [ 4 ] ] ) ) {
                            $time_slot = $delivery_dates_sess[ "time_slot_" . $address_key[ 2 ] . "_" . $address_key[ 3 ] . "_" . $address_key [ 4 ] ];
                        }

                        if( get_option( 'orddd_enable_delivery_time' ) == 'on' ) {
                            $time_setting[ 'enable' ] = get_option( 'orddd_enable_delivery_time' );
                            $time_settings_arr = explode( " ", $delivery_dates_sess[ "e_deliverydate_" . $address_key[ 2 ] . "_" . $address_key[ 3 ] . "_" . $address_key [ 4 ] ] );
                            $time_settings_arr_1 = array_pop( $time_settings_arr );
                            $time_settings = date( "H:i", strtotime( end( $time_settings_arr ) ) );
                            $time_setting[ 'time_selected' ] = $time_settings;
                        } else {
                            $time_setting = '';
                        }
                        
                        $timestamp = orddd_common::orddd_get_timestamp( $h_deliverydate, $date_format, $time_setting );
                        orddd_lockout_functions::orddd_maybe_reduce_delivery_lockout( $order_id );

                        if ( isset( $time_slot ) && $time_slot != '' && $time_slot != 'choose' && $time_slot != 'NA' && $time_slot != 'select' ) {
                            orddd_process::orddd_update_time_slot( $time_slot, $h_deliverydate );
                        }
                        update_post_meta( $order_id, '_orddd_shipping_multiple_addresss_timestamp_' . $address_key[ 2 ] . "_" . $address_key[ 3 ] . "_" . $address_key [ 4 ], $timestamp );
                    }
                    do_action( 'orddd_after_delivery_date_update', $val );
                }
            }
        }
        if ( function_exists( 'wcms_session_delete' ) ) {
           wcms_session_delete( 'wcms_item_delivery_dates' );
        }
    }

    /**
     * Removes the Delivery fields on the checkout page.
     *
     * @param array $fields - Checkout fields 
     *
     * @hook woocommerce_checkout_fields
     * @return array $fields -Checkout fields
     * @since 4.7
     */
    function orddd_remove_checkout_fields( $fields ) {
        if ( function_exists( 'wcms_session_get' ) ) {
            $sess_cart_addresses = wcms_session_get( 'cart_item_addresses' );
            if ( $sess_cart_addresses && !empty( $sess_cart_addresses ) ) {
                remove_action( ORDDD_SHOPPING_CART_HOOK, array( 'order_delivery_date', 'orddd_front_scripts_js' ) );
                remove_action( ORDDD_SHOPPING_CART_HOOK, array( 'order_delivery_date', 'orddd_front_scripts_css' ) );
                
                remove_action( ORDDD_SHOPPING_CART_HOOK, array( 'orddd_process','orddd_date_after_checkout_billing_form' ) );
                remove_action( ORDDD_SHOPPING_CART_HOOK, array( 'orddd_process', 'orddd_time_slot_after_checkout_billing_form' ) );

                remove_filter( ORDDD_SHOPPING_CART_HOOK , array( 'orddd_process', 'custom_override_checkout_fields' ) );
                
                remove_action( 'woocommerce_after_checkout_validation', array( 'orddd_process', 'orddd_validate_date' ), 10, 2 );
                remove_action( 'woocommerce_after_checkout_validation', array( 'orddd_process', 'orddd_validate_time_slot' ), 10, 2 );
                remove_action( 'woocommerce_cart_calculate_fees', array( 'orddd_process', 'orddd_add_delivery_date_fee' ) );
            }
        }
        return $fields;
    }
    
    /**
     * Adds Delivery label in the Order Received table.
     *
     * @hook wc_ms_shop_table_head 
     * @since 4.7
     */
    function orddd_wcms_order_recieved_head() {
        ?>
        <th class="delivery-date"><?php _e( get_option( 'orddd_delivery_date_field_label' ), 'woocommerce' ); ?></th>
        <?php
    }
    
    /**
     * Displays the Delivery information in the Order received table.
     *
     * @param array $package - Shipping Package block
     * @param int $order_id - Order ID
     * 
     * @globals resource $post
     *
     * @hook wc_ms_shop_table_row
     * 
     * @since 4.7
     */ 
    function orddd_wcms_order_recieved_row( $package, $order_id ) {
        global $post;
        $user = wp_get_current_user();
        $address_key = orddd_common::orddd_get_address_key( $package );
        foreach ( $package['contents'] as $package_key => $values ) {
            $product_id = $values['product_id'];
            $key = "e_deliverydate_" . $package_key . "_" . $product_id . "_" . $address_key;
            $timeslot_key = "time_slot_" . $package_key . "_" . $product_id . "_" . $address_key;
            $delivery_date = get_post_meta( $order_id, '_orddd_shipping_multiple_addresss_' . $key, true );
            $timeslot = get_post_meta( $order_id, '_orddd_shipping_multiple_addresss_' . $timeslot_key, true );
            echo "<td>";
            if( $delivery_date != "" && $delivery_date != null ) {
                echo get_option( 'orddd_delivery_date_field_label' ) . ': ' . $delivery_date . '<br />';
            }
            if( $timeslot != "" && $timeslot != null ) {
                echo get_option( 'orddd_delivery_timeslot_field_label' ) . ': ' . $timeslot . '<br />';
            }
            echo "</td>";
        }
    }
    
    /**
     * Adds the Delivery charges on the checkout page.
     *    
     * @globals resource $woocommerce
     *
     * @hook woocommerce_cart_calculate_fees
     * 
     * @since 4.7
     */
    public static function orddd_get_delivery_charges() {
        global $woocommerce;
        $gmt = false;
        if( has_filter( 'orddd_gmt_calculations' ) ) {
            $gmt = apply_filters( 'orddd_gmt_calculations', '' );
        }
        $current_time = current_time( 'timestamp', $gmt );
        
        if( function_exists( 'wcms_session_isset' ) ) {
            if ( wcms_session_isset( 'wcms_item_delivery_dates' ) ) {
                $delivery_dates_sess = wcms_session_get( 'wcms_item_delivery_dates' );
            } else {
                $delivery_dates_sess = array();
            }

            $timeslot_log_str = get_option( 'orddd_delivery_time_slot_log' );
            $timeslot_log_arr = array();
            if ( $timeslot_log_str == 'null' || $timeslot_log_str == '' || $timeslot_log_str == '{}' || $timeslot_log_str == '[]' ) {
                $timeslot_log_arr = array();
            } else {
                $timeslot_log_arr = json_decode( $timeslot_log_str );
            }
            
            $total_fees = 0;
            $delivery_fees_labels = $delivery_fees = array();
            foreach( $delivery_dates_sess as $key => $value ) {
                $specific_fees = "No";
                if( preg_match( '/h_deliverydate/', $key ) ) {
                    $delivery_date = $value;
                    if( get_option( 'orddd_enable_specific_delivery_dates' ) == 'on' ) {
                        $delivery_dates = get_option( 'orddd_delivery_dates' );
                        if ( $delivery_dates != '' && $delivery_dates != '{}' && $delivery_dates != '[]' ) {
                            $delivery_dates_arr = json_decode( get_option( 'orddd_delivery_dates' ) );
                        } else {
                            $delivery_dates_arr = array();
                        }
                        if( is_array( $delivery_dates_arr ) && count( $delivery_dates_arr ) > 0 ) {
                            if( !preg_match( '/[A-Za-z]/', $delivery_date ) ) {
                                $date = date( 'n-j-Y', strtotime( $delivery_date ) );
                                foreach ( $delivery_dates_arr as $key => $value ) {
                                    foreach ( $value as $k => $v ) {
                                        $temp_arr[ $k ] = $v;
                                    }
                                    if ( $date == $temp_arr[ 'date' ] ) {
                                        $fees = $temp_arr[ 'fees' ];
                                        $specific_charges_label = $temp_arr[ 'label' ];
                                        if( has_filter( 'orddd_add_delivery_date_fees' ) ) {
                                            $fees = apply_filters( 'orddd_add_delivery_date_fees', $delivery_date, $fees );
                                        }
                                        if ( $fees > 0 && in_array( $specific_charges_label, $delivery_fees_labels ) ) {
                                            $previous_fees = $delivery_fees[ $specific_charges_label ];
                                            $delivery_fees[ $specific_charges_label ] = $previous_fees + $fees;
                                            $specific_fees = "Yes";
                                        } else if( $fees > 0 && in_array( 'Delivery Charges', $delivery_fees_labels ) && $specific_charges_label == '' ) {
                                            $previous_fees = $delivery_fees[ 'Delivery Charges' ];
                                            $delivery_fees[ 'Delivery Charges' ] = $previous_fees + $fees;
                                            $specific_fees = "Yes";
                                        } else if( $fees > 0 ) {
                                            if( isset( $specific_charges_label ) && $specific_charges_label != '' ) {
                                                $delivery_fees[ $specific_charges_label ] = $fees;
                                                $delivery_fees_labels[] = $specific_charges_label;
                                                $specific_fees = "Yes";
                                            } else {
                                                $delivery_fees[ 'Delivery Charges' ] = $fees;
                                                $delivery_fees_labels[] = 'Delivery Charges';
                                                $specific_fees = "Yes";
                                            }
                                            
                                        }
                                    }
                                    $delivery_dates_array[] = $temp_arr[ 'date' ];
                                }
                            }
                            
                            if( $specific_fees == "No" && !in_array( $date, $delivery_dates_array ) ) {
                                if( !preg_match('/[A-Za-z]/', $delivery_date ) ) {
                                    $day = date( 'w', strtotime( $delivery_date ) );
                                } else {
                                    $day = '';
                                }
                                $fee_var = "additional_charges_orddd_weekday_" . $day;
                                $fees = get_option( $fee_var );
                                if( has_filter( 'orddd_add_delivery_date_fees' ) ) {
                                    $fees = apply_filters( 'orddd_add_delivery_date_fees', $delivery_date, $fees );
                                }
                                $delivery_charges_var = "delivery_charges_label_orddd_weekday_" . $day;
                                $delivery_charges_label = get_option( $delivery_charges_var );
                                
                                if( $fees > 0 && in_array( $delivery_charges_label, $delivery_fees_labels ) ) {
                                    $previous_fees = $delivery_fees[ $delivery_charges_label ];
                                    $delivery_fees[ $delivery_charges_label ] = $previous_fees + $fees;
                                } else if( $fees > 0 && in_array( 'Delivery Charges', $delivery_fees_labels ) && $delivery_charges_label == '' ) {
                                    $previous_fees = $delivery_fees[ 'Delivery Charges' ];
                                    $delivery_fees[ 'Delivery Charges' ] = $previous_fees + $fees;
                                } else if ( $fees > 0 ) {
                                    if( isset( $delivery_charges_label ) && $delivery_charges_label != '' ) {
                                        $delivery_fees[ $delivery_charges_label ] = $fees;
                                        $delivery_fees_labels[] = $delivery_charges_label;
                                    } else {
                                        $delivery_fees[ 'Delivery Charges' ] = $fees;
                                        $delivery_fees_labels[] = 'Delivery Charges';
                                    }
                                }
                            }
                        } else {
                            if( !preg_match('/[A-Za-z]/', $delivery_date ) ) {
                                $day = date( 'w', strtotime( $delivery_date ) );
                            } else {
                                $day = '';
                            }
                            $fee_var = "additional_charges_orddd_weekday_".$day;
                            $fees = get_option( $fee_var );
                            if( has_filter( 'orddd_add_delivery_date_fees' ) ) {
                                $fees = apply_filters('orddd_add_delivery_date_fees', $delivery_date, $fees );
                            }
                            $delivery_charges_var = "delivery_charges_label_orddd_weekday_".$day;
                            $delivery_charges_label = get_option( $delivery_charges_var );
                            if( $fees > 0 && in_array( $delivery_charges_label, $delivery_fees_labels ) ) {
                                $previous_fees = $delivery_fees[ $delivery_charges_label ];
                                $delivery_fees[ $delivery_charges_label ] = $previous_fees + $fees;
                            } else if( $fees > 0 && in_array( 'Delivery Charges', $delivery_fees_labels ) && $delivery_charges_label == '' ) {
                                $previous_fees = $delivery_fees[ 'Delivery Charges' ];
                                $delivery_fees[ 'Delivery Charges' ] = $previous_fees + $fees;
                            } else if ( $fees > 0 ) {
                                if( isset( $delivery_charges_label ) && $delivery_charges_label != '' ) {
                                    $delivery_fees[ $delivery_charges_label ] = $fees;
                                    $delivery_fees_labels[] = $delivery_charges_label;
                                } else {
                                    $delivery_fees[ 'Delivery Charges' ] = $fees;
                                    $delivery_fees_labels[] = 'Delivery Charges';
                                }
                            }
                        }
                    } else {
                        if( !preg_match('/[A-Za-z]/', $delivery_date ) ) {
                            $day = date( 'w', strtotime( $delivery_date ) );
                        } else {
                            $day = '';
                        }
                        $fee_var = "additional_charges_orddd_weekday_".$day;
                        $fees = get_option( $fee_var );
                        if( has_filter( 'orddd_add_delivery_date_fees' ) ) {
                            $fees = apply_filters('orddd_add_delivery_date_fees', $delivery_date, $fees );
                        }
                        $delivery_charges_var = "delivery_charges_label_orddd_weekday_" . $day;
                        $delivery_charges_label = get_option( $delivery_charges_var );
                        if( $fees > 0 && in_array( $delivery_charges_label, $delivery_fees_labels ) ) {
                            $previous_fees = $delivery_fees[ $delivery_charges_label ];
                            $delivery_fees[ $delivery_charges_label ] = $previous_fees + $fees;
                        } else if( $fees > 0 && in_array( 'Delivery Charges', $delivery_fees_labels ) && $delivery_charges_label == '' ) {
                            $previous_fees = $delivery_fees[ 'Delivery Charges' ];
                            $delivery_fees[ 'Delivery Charges' ] = $previous_fees + $fees;
                        } else if ( $fees > 0 ) {
                            if( isset( $delivery_charges_label ) && $delivery_charges_label != '' ) {
                                $delivery_fees[ $delivery_charges_label ] = $fees;
                                $delivery_fees_labels[] = $delivery_charges_label;
                            } else {
                                $delivery_fees[ 'Delivery Charges' ] = $fees;
                                $delivery_fees_labels[] = 'Delivery Charges';
                            }
                        }
                    }
                    
                    $current_date = date( 'j-n-Y' , $current_time );
                    // next day date
                    $next_day = date( "j-n-Y", strtotime( "+1 day", strtotime( $current_date ) ) );
                    if ( get_option( 'orddd_enable_same_day_delivery' ) == 'on' ) {
                        if( !preg_match( '/[A-Za-z]/', $delivery_date ) ) {
                            if ( $current_date == $delivery_date ) {
                                $same_day_delivery_charges_label = __( 'Same Day Delivery Charges', 'order-delivery-date' );
                                if ( has_filter( 'orddd_change_same_day_delivery_charges_label' ) ) {
                                    $same_day_delivery_charges_label = apply_filters( 'orddd_change_same_day_delivery_charges_label', $same_day_delivery_charges_label );
                                }

                                $fees = get_option( 'orddd_same_day_additional_charges' );
                                if( has_filter( 'orddd_add_delivery_date_fees' ) ) {
                                    $fees = apply_filters('orddd_add_delivery_date_fees', $delivery_date, $fees );
                                }
                                
                                if( $fees > 0 && in_array( $same_day_delivery_charges_label, $delivery_fees_labels ) ) {
                                    $previous_fees = $delivery_fees[ $same_day_delivery_charges_label ];
                                    $delivery_fees[ $same_day_delivery_charges_label ] = $previous_fees + $fees;
                                } else if( $fees > 0 && in_array( 'Same Day Delivery Charges', $delivery_fees_labels ) && $same_day_delivery_charges_label == '' ) {
                                    $previous_fees = $delivery_fees[ 'Same Day Delivery Charges' ];
                                    $delivery_fees[ 'Same Day Delivery Charges' ] = $previous_fees + $fees;
                                } else if ( $fees > 0 ) {
                                    if( isset( $same_day_delivery_charges_label ) && $same_day_delivery_charges_label != '' ) {
                                        $delivery_fees[ $same_day_delivery_charges_label ] = $fees;
                                        $delivery_fees_labels[] = $same_day_delivery_charges_label;
                                    } else {
                                        $delivery_fees[ 'Same Day Delivery Charges' ] = $fees;
                                        $delivery_fees_labels[] = 'Same Day Delivery Charges';
                                    }
                                }
                            }
                        }
                    }
                    
                    if ( get_option( 'orddd_enable_next_day_delivery' ) == 'on' ) {
                        if( !preg_match( '/[A-Za-z]/', $delivery_date ) ) {
                            if ( $next_day == $delivery_date ) {
                                $next_day_delivery_charges_label = __( 'Next Day Delivery Charges', 'order-delivery-date' );
                                if ( has_filter( 'orddd_change_next_day_delivery_charges_label' ) ) {
                                    $next_day_delivery_charges_label = apply_filters( 'orddd_change_next_day_delivery_charges_label', $next_day_delivery_charges_label );
                                }

                                $fees = get_option( 'orddd_next_day_additional_charges' );
                                if( has_filter( 'orddd_add_delivery_date_fees' ) ) {
                                    $fees = apply_filters('orddd_add_delivery_date_fees', $delivery_date, $fees );
                                }
                                if( $fees > 0 && in_array( $next_day_delivery_charges_label, $delivery_fees_labels ) ) {
                                    $previous_fees = $delivery_fees[ $next_day_delivery_charges_label ];
                                    $delivery_fees[ $next_day_delivery_charges_label ] = $previous_fees + $fees;
                                } else if( $fees > 0 && in_array( 'Next Day Delivery Charges', $delivery_fees_labels ) && $next_day_delivery_charges_label == '' ) {
                                    $previous_fees = $delivery_fees[ 'Next Day Delivery Charges' ];
                                    $delivery_fees[ 'Next Day Delivery Charges' ] = $previous_fees + $fees;
                                } else if ( $fees > 0 ) {
                                    if( isset( $next_day_delivery_charges_label ) && $next_day_delivery_charges_label != '' ) {
                                        $delivery_fees[ $next_day_delivery_charges_label ] = $fees;
                                        $delivery_fees_labels[] = $next_day_delivery_charges_label;    
                                    } else {
                                        $delivery_fees[ 'Next Day Delivery Charges' ] = $fees;
                                        $delivery_fees_labels[] = 'Next Day Delivery Charges';
                                    }
                                }
                            }
                        }
                    }
                }
                if( preg_match( '/time_slot/', $key ) ) {
                    $h_deliverydate_arr = explode( "_", $key );
                    $h_deliverydate = $delivery_dates_sess [ "h_deliverydate_" . $h_deliverydate_arr[2] . "_" . $h_deliverydate_arr[3] . "_" . $h_deliverydate_arr[4] ];
                    $select_time_slot_arr = explode( " - ", $value );
                    $select_from_time = date( "G:i", strtotime( $select_time_slot_arr[0] ) );
                    if( isset( $select_time_slot_arr[ 1 ] ) ) {
                        $select_to_time = date( "G:i", strtotime( $select_time_slot_arr[1] ) );
                        $timeslot_selected = $select_from_time . " - " . $select_to_time;
                    } else {
                        $timeslot_selected = $select_from_time;
                    }
                    foreach( $timeslot_log_arr as $k => $v ) {
                        $ft = $v->fh . ":" . trim( $v->fm );
                        if ( $v->th != 00 ){
                            $tt = $v->th . ":" . trim( $v->tm );
                            $time_slot_key = $ft . " - " . $tt;
                        } else {
                            $time_slot_key = $ft;
                        }
                        if ( gettype( json_decode( $v->dd ) ) == 'array' && count( json_decode( $v->dd ) ) > 0 && get_option( 'orddd_enable_specific_delivery_dates' ) == "on" ) {
                            $delivery_date = date( "n-j-Y", strtotime( $h_deliverydate ) );
                            $dd = json_decode( $v->dd );
                            if ( is_array( $dd ) && count( $dd ) > 0 ) {
                                foreach( $dd as $dkey => $dval ) {
                                    if( $timeslot_selected == $time_slot_key && $dval == $delivery_date ) {
                                        $additional_charges = $v->additional_charges;
                                        if( $additional_charges > 0 && $additional_charges != "" ) {
                                            if( isset( $v->additional_charges_label ) && $v->additional_charges_label != "" ) {
                                                $delivery_fees[ $v->additional_charges_label ] = $additional_charges;
                                                $delivery_fees_labels[] = $v->additional_charges_label;
                                            } else {
                                                $delivery_fees[ "Time slot Charges" ] = $additional_charges;
                                                $delivery_fees_labels[] = "Time slot Charges";
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $weekday = date( "w", strtotime( $h_deliverydate ) );
                            if ( gettype( json_decode( $v->dd ) ) == 'array' && count( json_decode( $v->dd ) ) > 0 ) {
                                $dd = json_decode( $v->dd );
                                foreach( $dd as $dkey => $dval ) {
                                    if( $timeslot_selected == $time_slot_key && ( $dval == "orddd_weekday_" . $weekday || $dval == "all" ) ) {
                                        $additional_charges = $v->additional_charges;
                                        if( $additional_charges > 0 && $additional_charges != "" ) {
                                            if( isset( $v->additional_charges_label ) && $v->additional_charges_label != "" ) {
                                                $delivery_fees[ $v->additional_charges_label ] = $additional_charges;
                                                $delivery_fees_labels[] = $v->additional_charges_label;
                                            } else {
                                                $delivery_fees[ "Time slot Charges" ] = $additional_charges;
                                                $delivery_fees_labels[] = "Time slot Charges";
                                            }
                                        }
                                    }
                                }
                            } else {
                                if( $timeslot_selected == $time_slot_key && ( $v->dd == "orddd_weekday_" . $weekday || $v->dd == "all" ) ) {
                                    $additional_charges = $v->additional_charges;
                                    if( $additional_charges > 0 && $additional_charges != "" ) {
                                        if( isset( $v->additional_charges_label ) && $v->additional_charges_label != "" ) {
                                            $delivery_fees[ $v->additional_charges_label ] = $additional_charges;
                                            $delivery_fees_labels[] = $v->additional_charges_label;
                                        } else {
                                            $delivery_fees[ "Time slot Charges" ] = $additional_charges;
                                            $delivery_fees_labels[] = "Time slot Charges";
                                        }
                                    }
                                }
                            }
                        }
                    }
                } 
            }
            
            foreach( $delivery_fees as $key_fees => $value_fees ) {
                if( get_option( 'orddd_enable_tax_calculation_for_delivery_charges' ) == 'on' ) {
                    $woocommerce->cart->add_fee( __( $key_fees, 'order-delivery-date' ), $value_fees , true );
                } else {
                    $woocommerce->cart->add_fee( __( $key_fees, 'order-delivery-date' ), $value_fees, false );
                }
            } 
        }
    }
}
$orddd_shipping_multiple_address = new orddd_shipping_multiple_address();
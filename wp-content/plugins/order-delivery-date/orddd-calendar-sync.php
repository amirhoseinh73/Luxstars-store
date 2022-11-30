<?php
/**
 * Order Delivery Date Pro for WooCommerce
 *
 * Handles the two way Google Calendar sync on frontend.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Pro-for-WooCommerce/Google-Calendar
 * @since       4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Setup the Import of events from Google Calendar
 * 
 * @since 9.1
 */

function orddd_import_events_cron() {
    $calendar_sync = new orddd_calendar_sync();
    $calendar_sync->orddd_setup_import_events();
}

add_action( 'orddd_import_events', 'orddd_import_events_cron' );

/**
 * Main class for Google Calendar Sync for deliveries.
 *
 * @class orddd_calendar_sync
 */
class orddd_calendar_sync {

    /**
    * Default constructor
    * 
    * @since 4.0
    */
    public function __construct() {
        $this->gcal_api = false;

        $this->plugin_dir = plugin_dir_path( __FILE__ );
        $this->plugin_url = plugins_url( basename( dirname( __FILE__ ) ) );
        
        if ( 'directly' === get_option( 'orddd_calendar_sync_integration_mode' ) || 
             'manually' === get_option( 'orddd_calendar_sync_integration_mode' ) ) {
            add_action( 'init', array( $this, 'orddd_setup_gcal_sync' ), 10 );
            add_action( 'woocommerce_order_status_changed', array( &$this, 'orddd_google_calendar_sync_delivery' ), 11, 3 );
            
            add_action( 'orddd_clean_events_db', array( &$this, 'orddd_delete_events_db' ) );
        }

        if( get_option( 'orddd_add_to_calendar_order_received_page' ) == 'on' ) {
            add_action( 'init', array( $this, 'orddd_setup_gcal_sync' ), 10 );
            add_filter( 'woocommerce_order_details_after_order_table', array( &$this, 'orddd_add_to_calendar_customer' ), 11, 3 );
        }
        if( get_option( 'orddd_add_to_calendar_my_account_page' ) == 'on' ) {
            add_action( 'init', array( $this, 'orddd_setup_gcal_sync' ), 10 );
            add_filter( 'woocommerce_my_account_my_orders_actions', array( &$this, 'orddd_add_to_calendar_customer_my_account' ), 10, 3 );
        }
        
        if( get_option( 'orddd_add_to_calendar_customer_email' ) == 'on' ) {
            add_action( 'init', array( $this, 'orddd_setup_gcal_sync' ), 10 );
            add_filter( 'woocommerce_email_customer_details', array( &$this, 'orddd_add_to_calendar_customer_email' ), 1, 3 );
        }

        if( get_option( 'orddd_admin_add_to_calendar_email_notification' ) == 'on' && get_option( 'orddd_calendar_sync_integration_mode' ) == 'manually' ) {
            add_filter( 'woocommerce_email_customer_details', array( &$this, 'orddd_admin_add_to_calendar_email_notification' ), 1, 3 );
        }

        add_action( 'wp_ajax_orddd_setup_import_events', array( &$this, 'orddd_setup_import_events' ) );
        add_action( 'wp_ajax_save_ics_url_feed', array( &$this, 'save_ics_url_feed' ) );
        add_action( 'wp_ajax_delete_ics_url_feed', array( &$this, 'delete_ics_url_feed' ) );
        add_action( 'wp_ajax_orddd_admin_delivery_calendar_events', array( &$this, 'orddd_admin_delivery_calendar_events' ) );
        add_action( 'wp_ajax_orddd_export_orders_again', array( &$this, 'orddd_export_orders_again' ) );
        
        require_once $this->plugin_dir . 'includes/iCal/SG_iCal.php';
	    
	    // Add Scheduled Actions for Import of Gcal events & cleanup of old gcal events.
	    add_action( 'init', array( &$this, 'orddd_schedule_gcal_event_cleanup_action' ) );
	    add_action( 'init', array( &$this, 'orddd_schedule_import_gcal_event_action' ) );

        // Add custom action for edit order page.
        add_action( 'woocommerce_order_actions', array( 'orddd_calendar_sync', 'orddd_add_custom_order_actions' ), 10, 1 );

        // Sends the order to google calendar.
        add_action( 'woocommerce_order_action_orddd_order_export_to_calendar', array( 'orddd_calendar_sync', 'orddd_send_order_to_google_calendar' ), 10, 1 );
    }

    /**
    * Sync the delivery to admin's Google calendar only when order status is changed to processing or completed. This will make sure the event in Google calendar is not created for any other status like on-hold or pending payment, etc.
    * 
    * @hook woocommerce_order_status_changed
    * @param int $order_id Order ID
    * @param string $old_status Previous status of the order
    * @param string $new_status New status of the order
    * @since 9.4
    */
    public static function orddd_google_calendar_sync_delivery( $order_id, $old_status, $new_status ) {
        $order_statues = apply_filters( 'orddd_export_order_statuses', array( 'processing', 'completed' ) );
        // Add the Event to the Google Calendar only when order status is changed to Processing or Completed or 
        // any status passed from the filter.
        // Also do not create Google calendar event if a Processing order is marked as Completed, or any order status 
        // is marked to the same order statuses passed from the filter.

        if( !( in_array( $old_status, $order_statues ) && in_array( $new_status, $order_statues ) )
            && in_array( $new_status, $order_statues ) ) {
            
            if ( ! class_exists( 'OrdddGcal' ) ) {
                $this->orddd_setup_gcal_sync();
            }
            $gcal = new OrdddGcal();
            if ( $gcal->get_api_mode() == "directly" ) {
                $event_details = orddd_common::orddd_get_event_details( $order_id );
                if ( isset( $event_details[ 'h_deliverydate' ] ) ) {
                    $gcal->insert_event( $event_details, $order_id, false );                    
                }
            }
        }
    }

    /**
     * Adds the GCal class file.
     * This file is used only for Sync Automatically
     *
     * @hook init
     * @since 4.0
     */
    public function orddd_setup_gcal_sync() {

        // GCal Integration
        $this->gcal_api = false;

        // Allow forced disabling in case of emergency
        require_once $this->plugin_dir . '/includes/class.gcal.php';
        $this->gcal_api = new OrdddGcal();
    }
    
    /**
     * This function adds an event/delivery information to the Google Calendar if automated sync is enabled.
     *
     * @param integer $order_id - Order ID for which delivery information need to be synced
     * @hook woocommerce_checkout_update_order_meta
     * @since 4.0
     */
    function orddd_google_calendar_update_order_meta( $order_id ) {
        $gcal = new OrdddGcal();
        if( $gcal->get_api_mode() == "directly" ) {
            $gcal->insert_event( $_POST, $order_id, false );
        }
    }
    
    /**
     * Adds buttons on WooCommerce Order received page
     * using which customers can add deliveries into their calendars.
     *
     * @param WC_Order $order - Order Object
     *
     * @hook woocommerce_order_details_after_order_table
     * @since 4.0
     */    
    function orddd_add_to_calendar_customer( $order ) {
        wp_enqueue_style( 'calendar-sync', plugins_url( '/css/calendar-sync.css', __FILE__ ) , '', '', false );
        if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {
            $order_id = $order->get_id();
        } else {
            $order_id = $order->id;
        }
        $data = get_post_meta( $order_id );

        $location = orddd_common::orddd_get_order_location( $order_id );
        $shipping_method = orddd_common::orddd_get_order_shipping_method( $order_id );
        $product_category = orddd_common::orddd_get_cart_product_categories( $order_id );
        $shipping_class = orddd_common::orddd_get_cart_shipping_classes( $order_id );

        $timeslot_field_label = orddd_common::orddd_get_delivery_time_field_label( $shipping_method, $product_category, $shipping_class, $location, $order_id ); 

        $orddd = new stdClass();
        if ( isset( $data[ '_orddd_timestamp' ][ 0 ] ) && $data[ '_orddd_timestamp' ][ 0 ] != '' ) {
            $delivery_date = date( "d-m-Y", $data[ '_orddd_timestamp' ][ 0 ] );
            $shipping_address_1 = isset( $data[ '_shipping_address_1' ][ 0 ] ) ? $data[ '_shipping_address_1' ][ 0 ] : '';
            $shipping_address_2 = isset( $data[ '_shipping_address_2' ][ 0 ] ) ? $data[ '_shipping_address_2' ][ 0 ] : '';
            $orddd->client_address = __( $shipping_address_1 . " " . $shipping_address_2 , 'order-delivery-date');
            $orddd->client_full_address = str_replace( "<br/>", ", ", $order->get_formatted_shipping_address() );
            $orddd->client_city = __( $data[ '_shipping_city' ][ 0 ], 'order-delivery-date');
            $orddd->start = date( 'Y-m-d', strtotime( $delivery_date ) );
            $orddd->end = date( 'Y-m-d', strtotime( $delivery_date ) );
            if( isset( $data[ $timeslot_field_label ][ 0 ] ) && $data[ $timeslot_field_label ][ 0 ] != '' && $data[ $timeslot_field_label ][ 0 ] != 'NA'  && $data[ $timeslot_field_label ][ 0 ] != 'choose' && $data[ $timeslot_field_label ][ 0 ] != 'select' ) {
                $timeslot = explode( " - ", $data[ $timeslot_field_label ][ 0 ] );
                $from_time = date( "H:i", strtotime( $timeslot[ 0 ] ) );
                if( isset( $timeslot[ 1 ] ) && $timeslot[ 1 ] != '' ) {
                    $to_time = date( "H:i", strtotime( $timeslot[ 1 ] ) );
                    $orddd->end_time = $to_time;
                    $time_end = explode( ':', $to_time );
                } else {
                    $orddd->end_time = $from_time;
                    $time_end = explode( ':', $from_time );
                }
                $orddd->start_time = $from_time;
            } else {
                $from_time = date( "H:i", $data[ '_orddd_timestamp' ][ 0 ] );
                if( $from_time != '00:00' && $from_time != '00:01' && $from_time != '' ) {
                    $orddd->start_time = $from_time;
                    $orddd->end_time = $from_time;
                } else {
                    $orddd->start_time = "";
                    $orddd->end_time = "";
                }
            } 
            $orddd->client_email = $data[ '_billing_email' ][ 0 ];
            $orddd->client_name = $data[ '_shipping_first_name' ][ 0 ] . " " . $data[ '_shipping_last_name' ][ 0 ];
            $orddd->client_address = $shipping_address_1  . " " . $shipping_address_2;
            $orddd->client_full_address = str_replace( "<br/>", ", ", $order->get_formatted_shipping_address() );
            $orddd->client_phone = $data[ '_billing_phone' ][ 0 ];
            if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {
                $order_customer_note = $order->get_customer_note();
            } else {
                $order_customer_note = $order->customer_note;
            }
            $orddd->order_note  = $order_customer_note;
            $get_order_items = $order->get_items();
            $products = $product_with_qty = "";
            foreach( $get_order_items as $key => $value ) {
                $data = $value->get_data();
                $product_name = $value[ 'name' ];
                if( isset( $data[ 'variation_id' ] ) && $data[ 'variation_id' ] != 0 ) {
                    $_product = new WC_Product_Variation( $data[ 'variation_id' ] );
                    $variation_data = $_product->get_variation_attributes(); // variation data in array
                    if( is_array( $variation_data ) && count( $variation_data ) > 2 ) {
                        $meta_data = $data[ 'meta_data' ] ;
                        $i = 0;
                        foreach( $meta_data as $mkey => $mvalue ) {
                            $meta_key = $mvalue->get_data();
                            if( isset( $meta_key[ 'key' ] ) && array_key_exists( 'attribute_' . $meta_key[ 'key' ], $variation_data ) ) {
                                if( $i == 0 ) {
                                    $product_name .= ' - ';    
                                }
                                $product_name .= urldecode( $meta_key[ 'value' ] ) . ", ";
                            }
                            $i++;
                        }
                    } 
                }
                $product_name = rtrim( $product_name, ', ' );
                $products .=  $product_name . ", ";
                $product_with_qty .= $product_name . "\r\n(QTY: " . $value[ 'qty' ] . "), \r\n \r\n";
            }
            $products = substr( $products, 0, strlen( $products )-2 );
            $product_with_qty = substr( $product_with_qty, 0, strlen( $product_with_qty )-2 );
            $orddd->order_total  = strip_tags( $order->get_formatted_order_total() );
            $orddd->products = $products;
            $orddd->product_with_qty = $product_with_qty;
            if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {
                $order_date_time = $order->get_date_created();
            } else {
                $order_date_time = $order->post->post_date;
            }
            $orddd->order_date_time = $order_date_time;
            $order_date = date( "Y-m-d", strtotime( $order_date_time ) );
            $orddd->order_date = $order_date;
            $orddd->id = $order->get_order_number();
            $orddd->order_weblink = $order->get_edit_order_url();
            $gcal = new OrdddGcal();
            $href = $gcal->gcal( $orddd );
            $other_calendar_href = $gcal->other_cal( $orddd );
            if( get_option( 'orddd_calendar_in_same_window' ) == 'on' ) {
                ?>
                <div class="add_to_calendar">
                    <button id="calendar-toggle" class="dropbtn"><?php _e( 'Add To Calendar', 'order-delivery-date' ); ?><i class="claret"></i></button>
                    <div id="add_to_calendar_menu" class="add_to_calendar-content">
                        <a href="<?php echo esc_url( $href ); ?>" target= "_self" id="add_to_google_calendar" ><img class="icon" src="<?php echo plugins_url(); ?>/order-delivery-date/images/google-icon.ico"><?php _e( 'Add to Google Calendar', 'order-delivery-date' ); ?></a>
                        <a href="<?php echo esc_url( $other_calendar_href ); ?>" target="_self" id="add_to_other_calendar" ><img class="icon" src="<?php echo plugins_url(); ?>/order-delivery-date/images/calendar-icon.ico"><?php _e( 'Add to other Calendar', 'order-delivery-date' ); ?></a>
                    </div>
                </div>
                <?php 
            } else {
                ?>
                <div class="add_to_calendar">
                    <button id="calendar-toggle" class="dropbtn"><?php _e( 'Add To Calendar', 'order-delivery-date' ); ?><i class="claret"></i></button>
                    <div id="add_to_calendar_menu" class="add_to_calendar-content">
                        <a href="<?php echo esc_url( $href ); ?>" target= "_blank" id="add_to_google_calendar" ><img class="icon" src="<?php echo plugins_url(); ?>/order-delivery-date/images/google-icon.ico"><?php _e( 'Add to Google Calendar', 'order-delivery-date' ); ?></a>
                        <a href="<?php echo esc_url( $other_calendar_href ); ?>" target="_blank" id="add_to_other_calendar" ><img class="icon" src="<?php echo plugins_url(); ?>/order-delivery-date/images/calendar-icon.ico"><?php _e( 'Add to other Calendar', 'order-delivery-date' ); ?></a>
                    </div>
                </div>
                <?php 
            }
        }
    }
    
    /**
     * Adds buttons on My Account page using which customers can add deliveries into their calendars.
     *
     * @param WC_Order $action - Order Object
     * @param array $order - Order actions (Pay, Cancel, View)
     *
     * @hook woocommerce_my_account_my_orders_actions
     * @since 4.0
     */
    function orddd_add_to_calendar_customer_my_account( $order, $action ) {
        wp_enqueue_style( 'calendar-sync', plugins_url( '/css/calendar-sync.css', __FILE__ ) , '', '', false );
        if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {
            $action_id = $action->get_id();
        } else {
            $action_id = $action->id;
        }

        $data = get_post_meta( $action_id );

        $location = orddd_common::orddd_get_order_location( $action_id );
        $shipping_method = orddd_common::orddd_get_order_shipping_method( $action_id );
        $product_category = orddd_common::orddd_get_cart_product_categories( $action_id );
        $shipping_class = orddd_common::orddd_get_cart_shipping_classes( $action_id );

        $timeslot_field_label = orddd_common::orddd_get_delivery_time_field_label( $shipping_method, $product_category, $shipping_class, $location, $action_id ); 

        $orddd = new stdClass();
        if ( isset( $data[ '_orddd_timestamp' ][ 0 ] ) && $data[ '_orddd_timestamp' ][ 0 ] != '' ) {
            $delivery_date = date( "d-m-Y", $data[ '_orddd_timestamp' ][ 0 ] );
            $shipping_address_1 = isset( $data[ '_shipping_address_1' ][ 0 ] ) ? $data[ '_shipping_address_1' ][ 0 ] : '';
            $shipping_address_2 = isset( $data[ '_shipping_address_2' ][ 0 ] ) ? $data[ '_shipping_address_2' ][ 0 ] : '';
            $orddd->client_address = __( $shipping_address_1 . " " . $shipping_address_2 , 'order-delivery-date');
            $orddd->client_full_address = str_replace( "<br/>", ", ", $action->get_formatted_shipping_address() );
            $orddd->client_city = __( $data[ '_shipping_city' ][ 0 ], 'order-delivery-date');
            $orddd->start = date( 'Y-m-d', strtotime( $delivery_date ) );
            $orddd->end = date( 'Y-m-d', strtotime( $delivery_date ) );
            if( isset( $data[ $timeslot_field_label ][ 0 ] ) && $data[ $timeslot_field_label ][ 0 ] != '' && $data[ $timeslot_field_label ][ 0 ] != 'NA'  && $data[ $timeslot_field_label ][ 0 ] != 'choose' && $data[ $timeslot_field_label ][ 0 ] != 'select' ) {
                $timeslot = explode( " - ", $data[ $timeslot_field_label ][ 0 ] );
                $from_time = date( "H:i", strtotime( $timeslot[ 0 ] ) );
                if( isset( $timeslot[ 1 ] ) && $timeslot[ 1 ] != '' ) {
                    $to_time = date( "H:i", strtotime( $timeslot[ 1 ] ) );
                    $orddd->end_time = $to_time;
                } else {
                    $orddd->end_time = $from_time;
                }
                $orddd->start_time = $from_time;
                 
            } else {
                $from_time = date( "H:i", $data[ '_orddd_timestamp' ][ 0 ] );
                if( $from_time != '00:00' && $from_time != '00:01' && $from_time != '' ) {
                    $orddd->start_time = $from_time;
                    $orddd->end_time = $from_time;
                } else {
                    $orddd->start_time = "";
                    $orddd->end_time = "";
                }
            } 
            $orddd->client_email = $data[ '_billing_email' ][ 0 ];
            $orddd->client_name = $data[ '_shipping_first_name' ][ 0 ] . " " . $data[ '_shipping_last_name' ][ 0 ];
            $orddd->client_address = $shipping_address_1  . " " . $shipping_address_2;
            $orddd->client_full_address = str_replace( "<br/>", ", ", $action->get_formatted_shipping_address() );
            $orddd->client_phone = $data[ '_billing_phone' ][ 0 ];
            if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {
                $order_customer_note = $action->get_customer_note();
            } else {
                $order_customer_note = $action->customer_note;
            }
            $orddd->order_note  = $order_customer_note;
            $get_order_items = $action->get_items();
            $products = $product_with_qty = "";
            foreach( $get_order_items as $key => $value ) {
                $data = $value->get_data();
                $product_name = $value[ 'name' ];
                if( isset( $data[ 'variation_id' ] ) && $data[ 'variation_id' ] != 0 ) {
                    $_product = new WC_Product_Variation( $data[ 'variation_id' ] );
                    $variation_data = $_product->get_variation_attributes(); // variation data in array
                    if( is_array( $variation_data ) && count( $variation_data ) > 2 ) {
                        $meta_data = $data[ 'meta_data' ] ;
                        $i = 0;
                        foreach( $meta_data as $mkey => $mvalue ) {
                            $meta_key = $mvalue->get_data();
                            if( isset( $meta_key[ 'key' ] ) && array_key_exists( 'attribute_' . $meta_key[ 'key' ], $variation_data ) ) {
                                if( $i == 0 ) {
                                    $product_name .= ' - ';    
                                }
                                $product_name .= urldecode( $meta_key[ 'value' ] ) . ", ";
                            }
                            $i++;
                        }
                    } 
                }
                $product_name = rtrim( $product_name, ', ' );
                $products .=  $product_name . ", ";
                $product_with_qty .= $product_name . "\r\n(QTY: " . $value[ 'qty' ] . "), \r\n \r\n";
            }
            $products = substr( $products, 0, strlen( $products )-2 );
            $product_with_qty = substr( $product_with_qty, 0, strlen( $product_with_qty )-2 );
            $orddd->order_total  = $action->get_total();
            $orddd->products = $products;
            $orddd->product_with_qty = $product_with_qty;
            if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {
                $order_date_time = $action->get_date_created();
            } else {
                $order_date_time = $action->post->post_date;
            }
            $orddd->order_date_time = $order_date_time;
            $order_date = date( "Y-m-d", strtotime( $order_date_time ) );
            $orddd->order_date = $order_date;
            $orddd->id = $action->get_order_number();
            $gcal = new OrdddGcal();
            $href = $gcal->gcal( $orddd );
            $other_calendar_href = $gcal->other_cal( $orddd );
            $order[ 'add_to_google_calendar' ] = array( "url" => $href,
                "name"  => __( "Add to Google Calendar", 'order-delivery-date' ) );

            $order[ 'add_to_other_calendar' ] = array( "url" => $other_calendar_href,
                "name"  => __( "Add to other Calendar", 'order-delivery-date' ) );           
        }
        return $order;
    }
    
    /**
     * Adds buttons in the WooCommerce customer emails when Integration mode is selected as directly
     * using which customers can add deliveries/event into their calendars.
     *
     * @param WC_Order $order - Order Object
     * @param bool $sent_to_admin (default: false)
     * @param bool $plain_text (default: false)
     *
     * @hook woocommerce_email_customer_details
     * @since 4.0
     */
    function orddd_add_to_calendar_customer_email( $order, $sent_to_admin = false, $plain_text = false ) {
        if( $sent_to_admin === false ) {
            if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {
                $order_id = $order->get_id();
            } else {
                $order_id = $order->id;
            }
            $data = get_post_meta( $order_id );

            $location = orddd_common::orddd_get_order_location( $order_id );
            $shipping_method = orddd_common::orddd_get_order_shipping_method( $order_id );
            $product_category = orddd_common::orddd_get_cart_product_categories( $order_id );
            $shipping_class = orddd_common::orddd_get_cart_shipping_classes( $order_id );

            $timeslot_field_label = orddd_common::orddd_get_delivery_time_field_label( $shipping_method, $product_category, $shipping_class, $location, $order_id ); 

            $orddd = new stdClass();
            if ( isset( $data[ '_orddd_timestamp' ][ 0 ] ) && $data[ '_orddd_timestamp' ][ 0 ] != '' ) {
                $delivery_date = date( "d-m-Y", $data[ '_orddd_timestamp' ][ 0 ] );
                $shipping_address_1 = isset( $data[ '_shipping_address_1' ][ 0 ] ) ? $data[ '_shipping_address_1' ][ 0 ] : '';
                $shipping_address_2 = isset( $data[ '_shipping_address_2' ][ 0 ] ) ? $data[ '_shipping_address_2' ][ 0 ] : '';
                $orddd->client_address = __( $shipping_address_1 . " " . $shipping_address_2 , 'order-delivery-date');
                $orddd->client_full_address = str_replace( "<br/>", ", ", $order->get_formatted_shipping_address() );
                $orddd->client_city = __( $data[ '_shipping_city' ][ 0 ], 'order-delivery-date');
                $orddd->start = date( 'Y-m-d', strtotime( $delivery_date ) );
                $orddd->end = date( 'Y-m-d', strtotime( $delivery_date ) );
                if( isset( $data[ $timeslot_field_label ][ 0 ] ) && $data[ $timeslot_field_label ][ 0 ] != '' && $data[ $timeslot_field_label ][ 0 ] != 'NA'  && $data[ $timeslot_field_label ][ 0 ] != 'choose' && $data[ $timeslot_field_label ][ 0 ] != 'select' ) {
                    $timeslot = explode( " - ", $data[ $timeslot_field_label ][ 0 ] );
                    $from_time = date( "H:i", strtotime( $timeslot[ 0 ] ) );
                    if( isset( $timeslot[ 1 ] ) && $timeslot[ 1 ] != '' ) {
                        $to_time = date( "H:i", strtotime( $timeslot[ 1 ] ) );
                        $orddd->end_time = $to_time;
                    } else {
                        $orddd->end_time = $from_time;
                    }
                    $orddd->start_time = $from_time;
                     
                } else {
                    $from_time = date( "H:i", $data[ '_orddd_timestamp' ][ 0 ] );
                    if( $from_time != '00:00' && $from_time != '00:01' && $from_time != '' ) {
                        $orddd->start_time = $from_time;
                        $orddd->end_time = $from_time;
                    } else {
                        $orddd->start_time = "";
                        $orddd->end_time = "";
                    }
                } 
                $orddd->client_email = $data[ '_billing_email' ][ 0 ];
                $orddd->client_name = $data[ '_shipping_first_name' ][ 0 ] . " " . $data[ '_shipping_last_name' ][ 0 ];
                $orddd->client_address = $shipping_address_1  . " " . $shipping_address_2;
                $orddd->client_full_address = str_replace( "<br/>", ", ", $order->get_formatted_shipping_address() );
                $orddd->client_phone = $data[ '_billing_phone' ][ 0 ];
                if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {
                    $order_customer_note = $order->get_customer_note();
                } else {
                    $order_customer_note = $order->customer_note;
                }
                $orddd->order_note  = $order_customer_note;
                $get_order_items = $order->get_items();
                $products = $product_with_qty = "";
                foreach( $get_order_items as $key => $value ) {
                    $data = $value->get_data();
                    $product_name = $value[ 'name' ];
                    if( isset( $data[ 'variation_id' ] ) && $data[ 'variation_id' ] != 0 ) {
                        $_product = new WC_Product_Variation( $data[ 'variation_id' ] );
                        $variation_data = $_product->get_variation_attributes(); // variation data in array
                        if( is_array( $variation_data ) && count( $variation_data ) > 2 ) {
                            $meta_data = $data[ 'meta_data' ] ;
                            $i = 0;
                            foreach( $meta_data as $mkey => $mvalue ) {
                                $meta_key = $mvalue->get_data();
                                if( isset( $meta_key[ 'key' ] ) && array_key_exists( 'attribute_' . $meta_key[ 'key' ], $variation_data ) ) {
                                    if( $i == 0 ) {
                                        $product_name .= ' - ';    
                                    }
                                    $product_name .= urldecode( $meta_key[ 'value' ] ) . ", ";
                                }
                                $i++;
                            }
                        } 
                    }
                    $product_name = rtrim( $product_name, ', ' );
                    $products .=  $product_name . ", ";
                    $product_with_qty .= $product_name . "\r\n(QTY: " . $value[ 'qty' ] . "), \r\n \r\n";
                }
                $products = substr( $products, 0, strlen( $products )-2 );
                $product_with_qty = substr( $product_with_qty, 0, strlen( $product_with_qty )-2 );
                $orddd->order_total  = strip_tags( $order->get_formatted_order_total() );
                $orddd->products = $products;
                $orddd->product_with_qty = $product_with_qty;
                if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {
                    $order_date_time = $order->get_date_created();
                } else {
                    $order_date_time = $order->post->post_date;
                }
                $orddd->order_date_time = $order_date_time;
                $order_date = date( "Y-m-d", strtotime( $order_date_time ) );
                $orddd->order_date = $order_date;
                $orddd->id = $order->get_order_number();
                if ( ! class_exists( 'OrdddGcal' ) ) {
                    $this->orddd_setup_gcal_sync();
                }
                $gcal = new OrdddGcal();
                $href = $gcal->gcal( $orddd );
                $other_calendar_href = $gcal->other_cal( $orddd );

                if( get_option( 'orddd_calendar_in_same_window' ) == 'on' ) {
                    ?>
                    <table cellspacing="0" cellpadding="0" border="0" style="border-collapse: separate!important;border-radius: 3px;background-color: #00add8" class="add_to_gcal_button">
                        <tbody>
                            <tr>    
                                <td valign="middle" align="center" style="font-family:Helvetica;font-size:12px;padding:7px">
                                    <a style="font-weight:bold;letter-spacing:normal;line-height:100%;text-align:center;text-decoration:none;color:#f5f5f5;word-wrap:break-word" title="Add to Google Calendar" href="<?php echo esc_url( $href ); ?>" target="_self"><?php _e( 'Add to Google Calendar', 'order-delivery-date' ); ?></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <table cellspacing="0" cellpadding="0" border="0" style="border-collapse: separate!important;border-radius: 3px;background-color: #00add8" class="add_to_other_cal_button">
                        <tbody>
                            <tr>    
                                <td valign="middle" align="center" style="font-family:Helvetica;font-size:12px;padding:7px">
                                    <a style="font-weight:bold;letter-spacing:normal;line-height:100%;text-align:center;text-decoration:none;color:#f5f5f5;word-wrap:break-word" title="Add to other Calendar" href="<?php echo esc_url( $other_calendar_href ); ?>" target="_self"><?php _e( 'Add to other Calendar', 'order-delivery-date' ); ?></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php 
                } else {
                    ?>
                    <table cellspacing="0" cellpadding="0" border="0" style="border-collapse: separate!important;border-radius: 3px;background-color: #00add8" class="add_to_gcal_button">
                        <tbody>
                            <tr>    
                                <td valign="middle" align="center" style="font-family:Helvetica;font-size:12px;padding:7px">
                                    <a style="font-weight:bold;letter-spacing:normal;line-height:100%;text-align:center;text-decoration:none;color:#ffffff;word-wrap:break-word" title="Add to Google Calendar" href="<?php echo esc_url( $href ); ?>" target="_blank"><?php _e( 'Add to Google Calendar', 'order-delivery-date' ); ?></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <table cellspacing="0" cellpadding="0" border="0" style="border-collapse: separate!important;border-radius: 3px;background-color: #00add8" class="add_to_other_cal_button">
                        <tbody>
                            <tr>    
                                <td valign="middle" align="center" style="font-family:Helvetica;font-size:12px;padding:7px">
                                    <a style="font-weight:bold;letter-spacing:normal;line-height:100%;text-align:center;text-decoration:none;color:#f5f5f5;word-wrap:break-word" title="Add to other Calendar" href="<?php echo esc_url( $other_calendar_href ); ?>" target="_blank"><?php _e( 'Add to other Calendar', 'order-delivery-date' ); ?></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php 
                }
            }
        }
    }
    
    /**
     * Adds buttons in the WooCommerce customer emails when Integration mode is selected as manually
     * using which customers can add deliveries/event into their calendars.
     *
     * @param WC_Order $order - Order Object
     * @param bool $sent_to_admin (default: false)
     * @param bool $plain_text (default: false)
     *
     * @hook woocommerce_email_customer_details
     * @since 4.0
     */
    function orddd_admin_add_to_calendar_email_notification( $order, $sent_to_admin = true, $plain_text = false ) {
        if( $sent_to_admin === true ) {
            if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {
                $ordd_order_id = $order->get_id();
            } else {
                $ordd_order_id = $order->id;
            }

            $data = get_post_meta( $ordd_order_id );    

            $location = orddd_common::orddd_get_order_location( $ordd_order_id );
            $shipping_method = orddd_common::orddd_get_order_shipping_method( $ordd_order_id );
            $product_category = orddd_common::orddd_get_cart_product_categories( $ordd_order_id );
            $shipping_class = orddd_common::orddd_get_cart_shipping_classes( $ordd_order_id );

            $timeslot_field_label = orddd_common::orddd_get_delivery_time_field_label( $shipping_method, $product_category, $shipping_class, $location, $ordd_order_id );

            $orddd = new stdClass();
            if ( isset( $data[ '_orddd_timestamp' ][ 0 ] ) && $data[ '_orddd_timestamp' ][ 0 ] != '' ) {
                $delivery_date = date( "d-m-Y", $data[ '_orddd_timestamp' ][ 0 ] );
                $shipping_address_1 = isset( $data[ '_shipping_address_1' ][ 0 ] ) ? $data[ '_shipping_address_1' ][ 0 ] : '';
                $shipping_address_2 = isset( $data[ '_shipping_address_2' ][ 0 ] ) ? $data[ '_shipping_address_2' ][ 0 ] : '';
                $orddd->client_address = __( $shipping_address_1 . " " . $shipping_address_2 , 'order-delivery-date');
                $orddd->client_full_address = str_replace( "<br/>", ", ", $order->get_formatted_shipping_address() );
                $orddd->client_city = __( $data[ '_shipping_city' ][ 0 ], 'order-delivery-date');
                $orddd->start = date( 'Y-m-d', strtotime( $delivery_date ) );
                $orddd->end = date( 'Y-m-d', strtotime( $delivery_date ) );
                if( isset( $data[ $timeslot_field_label ][ 0 ] ) && $data[ $timeslot_field_label ][ 0 ] != '' && $data[ $timeslot_field_label ][ 0 ] != 'NA'  && $data[ $timeslot_field_label ][ 0 ] != 'choose' && $data[ $timeslot_field_label ][ 0 ] != 'select' ) {
                    $timeslot = explode( " - ", $data[ $timeslot_field_label ][ 0 ] );
                    $from_time = date( "H:i", strtotime( $timeslot[ 0 ] ) );
                    if( isset( $timeslot[ 1 ] ) && $timeslot[ 1 ] != '' ) {
                        $to_time = date( "H:i", strtotime( $timeslot[ 1 ] ) );
                        $orddd->end_time = $to_time;
                    } else {
                        $orddd->end_time = $from_time;
                    }
                    $orddd->start_time = $from_time;
                     
                } else {
                    $from_time = date( "H:i", $data[ '_orddd_timestamp' ][ 0 ] );
                    if( $from_time != '00:00' && $from_time != '00:01' && $from_time != '' ) {
                        $orddd->start_time = $from_time;
                        $orddd->end_time = $from_time;
                    } else {
                        $orddd->start_time = "";
                        $orddd->end_time = "";
                    }
                } 
                $orddd->client_email = $data[ '_billing_email' ][ 0 ];
                $orddd->client_name = $data[ '_shipping_first_name' ][ 0 ] . " " . $data[ '_shipping_last_name' ][ 0 ];
                $orddd->client_address = $shipping_address_1  . " " . $shipping_address_2;
                $orddd->client_full_address = str_replace( "<br/>", ", ", $order->get_formatted_shipping_address() );
                $orddd->client_phone = $data[ '_billing_phone' ][ 0 ];
                if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {
                    $order_customer_note = $order->get_customer_note();
                } else {
                    $order_customer_note = $order->customer_note;
                }
                $orddd->order_note  = $order_customer_note;
                $get_order_items = $order->get_items();
                $products = $product_with_qty = "";
                foreach( $get_order_items as $key => $value ) {
                    $data = $value->get_data();
                    $product_name = $value[ 'name' ];
                    if( isset( $data[ 'variation_id' ] ) && $data[ 'variation_id' ] != 0 ) {
                        $_product = new WC_Product_Variation( $data[ 'variation_id' ] );
                        $variation_data = $_product->get_variation_attributes(); // variation data in array
                        if( is_array( $variation_data ) && count( $variation_data ) > 2 ) {
                            $meta_data = $data[ 'meta_data' ] ;
                            $i = 0;
                            foreach( $meta_data as $mkey => $mvalue ) {
                                $meta_key = $mvalue->get_data();
                                if( isset( $meta_key[ 'key' ] ) && array_key_exists( 'attribute_' . $meta_key[ 'key' ], $variation_data ) ) {
                                    if( $i == 0 ) {
                                        $product_name .= ' - ';    
                                    }
                                    $product_name .= urldecode( $meta_key[ 'value' ] ) . ", ";
                                }
                                $i++;
                            }
                        } 
                    }
                    $product_name = rtrim( $product_name, ', ' );
                    $products .=  $product_name . ", ";
                    $product_with_qty .= $product_name . "\r\n(QTY: " . $value[ 'qty' ] . "), \r\n \r\n";
                }
                $products = substr( $products, 0, strlen( $products )-2 );
                $product_with_qty = substr( $product_with_qty, 0, strlen( $product_with_qty )-2 );
                $orddd->order_total  = strip_tags( $order->get_formatted_order_total() );
                $orddd->products = $products;
                $orddd->product_with_qty = $product_with_qty;
                if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {
                    $order_date_time = $order->get_date_created();
                } else {
                    $order_date_time = $order->post->post_date;
                }
                $orddd->order_date_time = $order_date_time;
                $order_date = date( "Y-m-d", strtotime( $order_date_time ) );
                $orddd->order_date = $order_date;
                $orddd->id = $order->get_order_number();
                if ( ! class_exists( 'OrdddGcal' ) ) {
                    $this->orddd_setup_gcal_sync();
                }
                $gcal = new OrdddGcal();
                $href = $gcal->gcal( $orddd );
                if( get_option( 'orddd_calendar_in_same_window' ) == 'on' ) {
                    ?>
                    <form method="post" action="<?php echo $href; ?>" target= "_self" id="add_to_google_calendar_form">
                        <input type="submit" id="add_to_google_calendar" name="add_to_google_calendar" value="<?php _e( 'Add to Google Calendar', 'order-delivery-date' ); ?>" />
                    </form>
                    <?php 
                } else {
                    ?>
                    <form method="post" action="<?php echo $href; ?>" target= "_blank" id="add_to_google_calendar_form">
                        <input type="submit" id="add_to_google_calendar" name="add_to_google_calendar" value="<?php _e( 'Add to Google Calendar', 'order-delivery-date' ); ?>" />
                    </form>
                    <?php 
                }
            }
        }
    }
    
    /**
     * Gets the ICAL Data from Google Calendar when Import is
     * performed (manual and automated) for all the Google
     * Calendars setup in the plugin.
     *
     * @since 9.1
     */

    function orddd_setup_import_events() {
        $ics_url_key = '';
        if( isset( $_POST[ 'ics_feed_key' ] ) ) {
            $ics_url_key = $_POST[ 'ics_feed_key' ];
        }

        $ics_feed_urls = get_option( 'orddd_ics_feed_urls' );
        if( $ics_feed_urls == '' || $ics_feed_urls == '{}' || $ics_feed_urls == '[]' || $ics_feed_urls == 'null' ) {
            $ics_feed_urls = array();
        }
        
        $ics_feed = '';
        if( is_array( $ics_feed_urls ) && count( $ics_feed_urls ) > 0 && isset( $ics_feed_urls[ $ics_url_key ] ) ) {
            $ics_feed = $ics_feed_urls[ $ics_url_key ];
            $ics_feed = str_replace( 'https://', '', $ics_feed );
        } 

        if ( $ics_feed == '' ) { 
            // it means it was called using cron, so we need to auto import for all the calendars saved
            // run the import for all the calendars saved
            if ( isset( $ics_feed_urls ) && is_array( $ics_feed_urls ) && count( $ics_feed_urls ) > 0 ) {
                foreach ( $ics_feed_urls as $ics_feed ) {
                    $ics_feed = str_replace( 'https://', '', $ics_feed );
                    $ical = new SG_Orddd_iCalReader( $ics_feed );
                    $ical_array = $ical->getEvents();
                    $this->import_events( $ical_array, 'cron' );
                }
            }
        } else {
            $ical = new SG_Orddd_iCalReader( $ics_feed );
            $ical_array = $ical->getEvents();
            $this->import_events( $ical_array, 'ajax' );
        }
    }

    /**
     * Creates orders when the events are imported from the Google Calendar 
     *
     * @globals array $orddd_date_formats
     * @since 4.0
     */
    function import_events( $ical_array, $called_from ) {
        global $orddd_date_formats;
        
        $gmt = false;
        if( has_filter( 'orddd_gmt_calculations' ) ) {
            $gmt = apply_filters( 'orddd_gmt_calculations', '' );
        }
        $current_time = current_time( 'timestamp', $gmt );

        $event_uids = get_option( 'orddd_event_uids_ids' );
        if( $event_uids == '' || $event_uids == '{}' || $event_uids == '[]' || $event_uids == 'null' ) {
            $event_uids = array();
        }

        if( isset( $ical_array ) ) {
            foreach( $ical_array as $key_event => $value_event ) {
               //   Do stuff with the event $event
                if( !in_array( $value_event->uid, $event_uids ) ) {
                    if ( !$current_time ) {
                        $tdif = 0;
                    } else {
                        $tdif = $current_time - time();
                    }
                    
                    $time_format = get_option( 'orddd_delivery_time_format' );
                    if ( $time_format == '1' ) {
                        $time_format_to_show = 'h:i A';
                    } else {
                        $time_format_to_show = 'H:i';
                    }
                    
                    if( $value_event->getStart() != "" ) {
                        $event_start = $value_event->getStart() + $tdif;
                        if( $event_start >= $current_time ) {
                            $my_post = array(
                                'post_type' => 'shop_order',
                                'post_status' => 'wc-pending'
                            );

                            // Insert the post into the database
                            $order_id = wp_insert_post( $my_post );
                            $order = wc_get_order( $order_id );

                            // Insert a order note for identifying an imported event from google calendar's order. 
                            $order->add_order_note( __( 'Order imported from Google Calendar event.', 'order-delivery-date' ) ) ;

                            // Insert the event summary and description as order note from google calendar.
                            $event_summary = $value_event->getSummary();
                            $event_description = $value_event->getDescription();

                            $order_note = '';
                            if( $event_summary != '' ) {
                                $order_note .= __( 'Event Summary: ', 'order-delivery-date' ) . $event_summary;
                            }

                            if( $event_description != '' ) {
                                if( $event_summary != '' ) {
                                    $order_note .= "<br>";
                                }
                                $order_note .= __( 'Event Description: ', 'order-delivery-date' ) . $event_description;
                            }

                            if( $order_note != '' ) {
                                $order->add_order_note( $order_note );
                            }
                        }
                    }
                   
                    if( $value_event->getEnd() != "" && $value_event->getStart() != "" ) {
                        $event_start = $value_event->getStart() + $tdif;
                        $event_end = $value_event->getEnd() + $tdif;
                        $event_date_to_update = date( $orddd_date_formats[ get_option( 'orddd_delivery_date_format' ) ], $event_start );
                        $event_timestamp = strtotime( date( "Y-m-d", $event_start ) );
                        if( $event_end >= $current_time && $event_start >= $current_time ) {
                            $lockout_date = date( "Y-m-d", $event_start );
                            $event_from_time = date( $time_format_to_show, $event_start );
                            $event_to_time = date( $time_format_to_show, $event_end );
                            $time_slot_for_event = $event_from_time . " - " . $event_to_time;
                            $existing_timeslots_str = get_option( 'orddd_delivery_time_slot_log' );
                            $existing_timeslots_arr = json_decode( $existing_timeslots_str );
                            if ( is_array( $existing_timeslots_arr ) && count( $existing_timeslots_arr ) > 0 ) {
                                if( $existing_timeslots_arr == 'null' ) {
                                    $existing_timeslots_arr = array();
                                }
                                foreach ( $existing_timeslots_arr as $k => $v ) {
                                    $fh = $v->fh;
                                    $fm = $v->fm;
                                    $th = $v->th;
                                    $tm = $v->tm;
                                    $from_time = $v->fh . ":" . $v->fm;
                                    $ft = date( $time_format_to_show, strtotime( $from_time ) );
                                    if ( $v->th != 00 ){
                                        $to_time = $v->th . ":" . $v->tm;
                                        $tt = date( $time_format_to_show, strtotime( $to_time ) );
                                        $key = $ft . " - " . $tt;
                                    } else {
                                        $key = $ft;
                                    }
                                    if ( $key == $time_slot_for_event ) {
                                        $event_date = date( "j-n-Y", $event_start );
                                        orddd_process::orddd_update_time_slot( $time_slot_for_event, $event_date );
                                        update_post_meta( $order_id, get_option( 'orddd_delivery_timeslot_field_label' ), esc_attr( $time_slot_for_event ) );
                                    }   
                                }
                            }
                            update_post_meta( $order_id, '_orddd_timestamp', $event_timestamp );
                            update_post_meta( $order_id, get_option( 'orddd_delivery_date_field_label' ), esc_attr( $event_date_to_update ) );
                            orddd_lockout_functions::orddd_maybe_reduce_delivery_lockout( $order_id );
                        }
                    } else if( $value_event->getStart() != "" && $value_event->getEnd() == "" ) {
                        $event_start = $value_event->getStart() + $tdif;
                        $event_date_to_update = date( $orddd_date_formats[ get_option( 'orddd_delivery_date_format' ) ], $event_start );
                        $event_timestamp = strtotime( date( "Y-m-d", $event_start ) );
                        if( $event_start >= $current_time ) {
                            $lockout_date = date( "Y-m-d", $event_start );
                            if( get_option( 'orddd_enable_time_slot' ) == "on" ) {
                                $event_from_time = date( $time_format_to_show, $event_start );
                                $existing_timeslots_str = get_option( 'orddd_delivery_time_slot_log' );
                                $existing_timeslots_arr = json_decode( $existing_timeslots_str );
                                if ( is_array( $existing_timeslots_arr ) && count( $existing_timeslots_arr ) > 0 ) {
                                    if( $existing_timeslots_arr == 'null' ) {
                                        $existing_timeslots_arr = array();
                                    }
                                    foreach ( $existing_timeslots_arr as $k => $v ) {
                                        $fh = $v->fh;
                                        $fm = $v->fm;
                                        $th = $v->th;
                                        $tm = $v->tm;
                                        $from_time = $v->fh . ":" . $v->fm;
                                        $ft = date( $time_format_to_show, strtotime( $from_time ) );
                                        if ( $v->th != 00 ){
                                            $to_time = $v->th . ":" . $v->tm;
                                            $tt = date( $time_format_to_show, strtotime( $to_time ) );
                                            $key = $ft . " - " . $tt;
                                        } else {
                                            $key = $ft;
                                        }
                                        if ( $key == $event_from_time ) {
                                            $event_date = date( "j-n-Y", $event_start );
                                            orddd_process::orddd_update_time_slot( $event_from_time, $event_date );
                                            update_post_meta( $order_id, get_option( 'orddd_delivery_timeslot_field_label' ), esc_attr( $event_from_time ) );
                                        }
                                    }
                                }
                            } else if( get_option( 'orddd_enable_delivery_time' ) == 'on' ) {
                                $event_date_to_update = date( $orddd_date_formats[ get_option( 'orddd_delivery_date_format' ) ] . " " . $time_format_to_show , $event_start );
                                $event_timestamp = $event_start;
                            }
                            update_post_meta( $order_id, '_orddd_timestamp', $event_timestamp );
                            update_post_meta( $order_id, get_option( 'orddd_delivery_date_field_label' ), esc_attr( $event_date_to_update ) );
                            orddd_lockout_functions::orddd_maybe_reduce_delivery_lockout( $order_id );
                        }
                    }               
                    
                    array_push( $event_uids, $value_event->uid );
                    update_option( 'orddd_event_uids_ids', $event_uids );

                    // 9.19.0 TEST: Comment below part if you want to test migration script & prevent post meta from being added for each googlge calendar event.
                    if( isset( $order_id ) && '' != $order_id ) {
                        update_post_meta( $order_id, '_orddd_gcal_event_id', $value_event->uid );
                    }
                    
                    $event_orders = get_option( 'orddd_event_order_ids' );
                    if( $event_orders == '' || $event_orders == '{}' || $event_orders == '[]' || $event_orders == 'null' ) {
                        $event_orders = array();
                    }
                    if( isset( $order_id ) && '' != $order_id ) {
                        array_push( $event_orders, $order_id );
                    }
                    update_option( 'orddd_event_order_ids', $event_orders );
                }
            }
            echo "All the Events are Imported.";
        }
        
        if( $called_from == 'ajax' ) {
            die();
        }
    }
    
    /**
     * Save the Google Import URL Feeds from
     * Order Delivery Date->Settings->Google Calendar Sync tab->Import Events
     * Called via AJAX
     *
     * @since 4.0
     */
    function save_ics_url_feed() {
        $ics_table_content = '';
        if( isset( $_POST[ 'ics_url' ] ) ) {
            $ics_url = $_POST[ 'ics_url' ];
        } else {
            $ics_url = '';
        }

        if( $ics_url != '' ) {
            $ics_feed_urls = get_option( 'orddd_ics_feed_urls' );
            if( $ics_feed_urls == '' || $ics_feed_urls == '{}' || $ics_feed_urls == '[]' || $ics_feed_urls == 'null' ) {
                $ics_feed_urls = array();
            }
            
            if( !in_array( $ics_url, $ics_feed_urls ) ) {
                array_push( $ics_feed_urls, $ics_url );
                update_option( 'orddd_ics_feed_urls', $ics_feed_urls );
                $ics_table_content = 'yes';
            }
        }
        
        echo $ics_table_content;
        die();
    }
    
    /**
     * Delete the Google Import URL Feeds from
     * Order Delivery Date->Settings->Google Calendar Sync tab->Import Events
     * Called via AJAX
     *
     * @since 4.0
     */
    function delete_ics_url_feed() {
        $ics_table_content = '';
        if( isset( $_POST[ 'ics_feed_key' ] ) ) {
            $ics_url_key = $_POST[ 'ics_feed_key' ];
        } else {
            $ics_url_key = '';
        }
        
        if( $ics_url_key != '' ) {
            $ics_feed_urls = get_option( 'orddd_ics_feed_urls' );
            if( $ics_feed_urls == '' || $ics_feed_urls == '{}' || $ics_feed_urls == '[]' || $ics_feed_urls == 'null' ) {
                $ics_feed_urls = array();
            }
        
            unset( $ics_feed_urls[ $ics_url_key ] );
            update_option( 'orddd_ics_feed_urls', $ics_feed_urls );
            $ics_table_content = 'yes';
        }
        
        echo $ics_table_content;
        die();
    }
    
    /**
     * Export deliveries to Google Calendar from the Order Delivery Date->Delivery Calendar->Add to Calendar button.
     * This is especially used to export deliveries for orders that were placed before
     * Automated Google Calendar Sync is enabled.
     *
     * @globals resource $wpdb
     * @since 4.0
     */
    public static function orddd_admin_delivery_calendar_events() {
        global $wpdb;
        
        $gmt = false;
        if( has_filter( 'orddd_gmt_calculations' ) ) {
            $gmt = apply_filters( 'orddd_gmt_calculations', '' );
        }
        $current_time = current_time( 'timestamp', $gmt );

        $total_orders_to_export = orddd_common::orddd_get_total_orders_to_export();
        $gcal = new OrdddGcal();
        $current_timestamp = strtotime( date( 'd-m-Y', $current_time ) );
        foreach( $total_orders_to_export as $key => $value ) {
            $event_details = array();
            $data = get_post_meta( $value );
            $order = new WC_Order( $value );
            
            $location = orddd_common::orddd_get_order_location( $value );
            $shipping_method = orddd_common::orddd_get_order_shipping_method( $value );
            $product_category = orddd_common::orddd_get_cart_product_categories( $value );
            $shipping_class = orddd_common::orddd_get_cart_shipping_classes( $value );

            $timeslot_field_label = orddd_common::orddd_get_delivery_time_field_label( $shipping_method, $product_category, $shipping_class, $location, $value );
            
            $date_field_label = get_option( 'orddd_delivery_date_field_label' );
            $orddd = new stdClass();
            if ( isset( $data[ '_orddd_timestamp' ][ 0 ] ) && $data[ '_orddd_timestamp' ][ 0 ] != '' && $data[ '_orddd_timestamp' ][ 0 ] >= $current_timestamp ) {
                $delivery_date = date( "d-m-Y", $data[ '_orddd_timestamp' ][ 0 ] );
                $event_details[ 'h_deliverydate' ] = $delivery_date;
                $event_details[ '_orddd_timestamp' ] = $data[ '_orddd_timestamp' ][ 0 ];
                if( isset( $data[ $date_field_label ][ 0 ] ) ) {
                    $event_details[ 'e_deliverydate' ] = $data[ $date_field_label ][ 0 ];
                } else {
                    $event_details[ 'e_deliverydate' ] = "";
                }
            } elseif ( isset( $data[ $date_field_label ][ 0 ] ) && $data[ $date_field_label ][ 0 ] != '' && $data[ $date_field_label ][ 0 ] != 'null' && $data[ $date_field_label ][ 0 ] != '{}' && $data[ $date_field_label ][ 0 ] != '[]' ) {
                $delivery_date_timestamp = strtotime( str_replace( ",", " ", $data[ $date_field_label ][ 0 ] ) );
                $delivery_date = date( "d-m-Y", $delivery_date_timestamp );
                $event_details[ 'h_deliverydate' ] = $delivery_date;
                $event_details[ 'e_deliverydate' ] = $data[ $date_field_label ][ 0 ];
            } elseif ( isset( $data[ ORDDD_DELIVERY_DATE_FIELD_LABEL ][ 0 ] ) && $data[ ORDDD_DELIVERY_DATE_FIELD_LABEL ][ 0 ] != '' && $data[ ORDDD_DELIVERY_DATE_FIELD_LABEL ][ 0 ] != 'null' && $data[ ORDDD_DELIVERY_DATE_FIELD_LABEL ][ 0 ] != '{}' && $data[ ORDDD_DELIVERY_DATE_FIELD_LABEL ][ 0 ] != '[]' ) {
                $delivery_date_timestamp = strtotime( $data[ ORDDD_DELIVERY_DATE_FIELD_LABEL ][ 0 ] );
                $delivery_date = date( "d-m-Y", $delivery_date_timestamp );
                $event_details[ 'h_deliverydate' ] = $delivery_date;
                if( isset( $data[ ORDDD_DELIVERY_DATE_FIELD_LABEL ][ 0 ] ) ) {
                    $event_details[ 'e_deliverydate' ] = $data[ ORDDD_DELIVERY_DATE_FIELD_LABEL ][ 0 ];
                } else {
                    $event_details[ 'e_deliverydate' ] = "";
                }
            }
            if( isset( $event_details[ 'h_deliverydate' ] ) && $event_details[ 'h_deliverydate' ] != "" ) {
                if( isset( $data[ $timeslot_field_label ][ 0 ] ) && $data[ $timeslot_field_label ][ 0 ] != '' && $data[ $timeslot_field_label ][ 0 ] != 'NA'  && $data[ $timeslot_field_label ][ 0 ] != 'choose' && $data[ $timeslot_field_label ][ 0 ] != 'select' ) {
                    $timeslot = explode( " - ", $data[ $timeslot_field_label ][ 0 ] );
                    $from_time = date( "H:i", strtotime( $timeslot[ 0 ] ) );
                    if( isset( $timeslot[ 1 ] ) && $timeslot[ 1 ] != '' ) {
                        $to_time = date( "H:i", strtotime( $timeslot[ 1 ] ) );
                        $time_slot = $from_time . " - " . $to_time;
                    } else {
                        $time_slot = $from_time;
                    }
                    $event_details[ 'time_slot' ] = $time_slot;
                     
                }
                $shipping_address_1 = isset( $data[ '_shipping_address_1' ][ 0 ] ) ? $data[ '_shipping_address_1' ][ 0 ] : '';
                $shipping_address_2 = isset( $data[ '_shipping_address_2' ][ 0 ] ) ? $data[ '_shipping_address_2' ][ 0 ] : '';

                $event_details[ 'billing_email' ] = $data[ '_billing_email' ][ 0 ];
                $event_details[ 'shipping_first_name' ] = $data[ '_shipping_first_name' ][ 0 ];
                $event_details[ 'shipping_last_name' ] = $data[ '_shipping_last_name' ][ 0 ];
                $event_details[ 'shipping_address_1' ] = $shipping_address_1;
                $event_details[ 'shipping_address_2' ] = $shipping_address_2;
                $event_details[ 'billing_phone' ]        = $data[ '_billing_phone' ][ 0 ];
                $event_details[ 'payment_method_title' ] = '' != $order->get_payment_method_title() ? $order->get_payment_method_title() : '';
				$event_details[ 'shipping_method_title' ] = '' != $order->get_shipping_method() ? $order->get_shipping_method() : '';

                $pickup_locations_label = '' != get_option( 'orddd_location_field_label' ) ? get_option( 'orddd_location_field_label' ) : 'Pickup Location';
                if( isset( $data[ $pickup_locations_label ][ 0 ] ) ) {
                    $event_details[ 'pickup_location' ] = $data[ $pickup_locations_label ][ 0 ];	
                } else {
                    $event_details[ 'pickup_location' ] = '';
                }

                $event_details[ 'order_weblink' ] = $order->get_edit_order_url();
			    $event_details[ 'order_status' ]  = $order->get_status();
                if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {
                    $order_customer_note = $order->get_customer_note();
                } else {
                    $order_customer_note = $order->customer_note;
                }
                $event_details[ 'order_comments' ]  = $order_customer_note;
                $gcal->insert_event( $event_details, $value, false );
            }
        }
        die();
    }


    public static function orddd_export_orders_again() {
        orddd_common::orddd_delete_exported_events();
        self::orddd_admin_delivery_calendar_events();

        wp_die();

    }

    /**
     * Cron Event to remove the past orders from the gcal options where the delivery date is older * than 15 days.
     * 
     * @since 9.7
     */
    public static function orddd_delete_events_db() {
        $event_order_ids = get_option( 'orddd_event_order_ids' );
        $event_uids = get_option( 'orddd_event_uids_ids' );

        $gmt = false;
        if( has_filter( 'orddd_gmt_calculations' ) ) {
            $gmt = apply_filters( 'orddd_gmt_calculations', '' );
        }

        $current_time = current_time( 'timestamp', $gmt );
        $current_date = date( 'j-n-Y', $current_time );
        $current_date_time = strtotime( $current_date );
        $days_compare = 15 * 86400; // calculate the number of seconds for 15 days
        
        if ( is_array( $event_order_ids ) && count( $event_order_ids ) > 0 ) {
			foreach ( $event_order_ids as $key => $value ) {
                $order_id = $value;
                
                $delivery_timestamp = get_post_meta( $order_id, '_orddd_timestamp', true );
                //Do not update the event order ids if the delivery timestamp for the order is blank.
                //This is generally a case when an event is added for an order and then the order is deleted permanently. 
                if( $delivery_timestamp != '' ) {
                    $days_diff = $current_date_time - $delivery_timestamp; 

                    //check if the difference between current date and delivery date is greater than 15 days. If $days_diff is greater than 0 then it is a past delivery date.

                    if( $days_diff > $days_compare ) {
                        unset( $event_order_ids[$key] );
                        update_option( 'orddd_event_order_ids', array_values($event_order_ids) );
                    }
                }
			}
        }
        
        if( is_array( $event_uids ) && count( $event_uids ) > 0 ) {
            foreach ( $event_uids as $key => $value ) {
                $order_id = $key;
                
                $delivery_timestamp = get_post_meta( $order_id, '_orddd_timestamp', true );
                //Do not update the event order ids if the delivery timestamp for the order is blank.
                //This is generally a case when an event is added for an order and then the order is deleted permanently. 
                if( $delivery_timestamp != '' ) {
                    $days_diff = $current_date_time - $delivery_timestamp;
                    
                    //check if the difference between current date and delivery date is greater than 15 days.If $days_diff is greater than 0 then it is a past delivery date.
                    
                    if( $days_diff > $days_compare ) {
                        unset( $event_uids[$key] );
                        update_option( 'orddd_event_uids_ids', $event_uids );

                        // 9.19.0 TEST: Comment below part if you want to test migration script & prevent google calendar post meta from being deleted for each order.
                        delete_post_meta( $order_id, '_orddd_gcal_event_id', $value_event->uid );
                    }
                }
			}
        }
    }
	
    /**
     * Add a recurring scheduled action for Cleanup of Imported GCal Events.
     *
     * @since 9.16.0
     */
    public function orddd_schedule_gcal_event_cleanup_action() {
        if ( function_exists( 'as_next_scheduled_action' ) ) {
            // If no action has been scheduled yet.
            if ( false === as_next_scheduled_action( 'orddd_clean_events_db' ) ) {
                // Remove the existing cron job.
                wp_clear_scheduled_hook( 'orddd_clean_events_db' );

                // Set the time to run at the next occurence of 01:00 hours as per the server timezone.
                $timezone = orddd_send_reminder::orddd_get_timezone_string();
                $string = '01:00:00' . $timezone;
                $timestamp = strtotime( $string ) < current_time('timestamp') ? strtotime( 'tomorrow ' . $string ) : strtotime( $string );
                // Add recurring action.
                as_schedule_recurring_action( $timestamp, 86400, 'orddd_clean_events_db' );
            }
        }
    }

    /**
     * Add a recurring scheduled action for import of events from GCal.
     *
     * @since 9.16.0
     */
    public function orddd_schedule_import_gcal_event_action() {
        
        // If action is not yet scheduled.
        if ( function_exists( 'as_next_scheduled_action' ) ) {
            if ( false === as_next_scheduled_action( 'orddd_import_events' ) ) {
                // Remove the existing cron job.
                wp_clear_scheduled_hook( 'orddd_import_events' );

                $orddd_wp_cron_seconds = 86400;
                if ( get_option( 'orddd_real_time_import' ) == 'on' ) {
                    $orddd_wp_cron_minutes = get_option( 'orddd_wp_cron_minutes' );
                    if( $orddd_wp_cron_minutes == '' || $orddd_wp_cron_minutes == '0' ) {
                        $orddd_wp_cron_seconds = 10 * 60 ;
                    } else {
                        $orddd_wp_cron_seconds = $orddd_wp_cron_minutes * 60 ;
                    }
                } 

                // Add recurring action.
                as_schedule_recurring_action( time(), $orddd_wp_cron_seconds, 'orddd_import_events' );
                
            }
        }
    }
 
    /** 
     * Add custom action for edit-order page to perform custom actions on order.
     *
     * @param array   $actions array of actions.
     * @return array  $actions array of actions after adding custom action.
     * @since 9.24.0
     */
    public static function orddd_add_custom_order_actions( $actions ) {
        $actions['orddd_order_export_to_calendar'] = __( 'Re-export to Google Calendar', 'order-delivery-date' );
        return $actions;
    }

    /** 
     * Sends the current order to the google calendar.
     *
     * @param array $order order object.
     * @since 9.24.0
     */
    public static function orddd_send_order_to_google_calendar( $order ) {
        $plugin_dir_path = plugin_dir_path( __FILE__ );
        require_once $plugin_dir_path . '/includes/class.gcal.php';
        $order_id     = $order->get_id();
        $order_status = $order->get_status();
        $gcal         = new OrdddGcal();
        if ( $gcal->is_active() && ( 'processing' === $order_status || 'completed' === $order_status ) ) {
            $gcal->delete_event( $order_id );
            $event_details = orddd_common::orddd_get_event_details( $order_id, true );
            $gcal->insert_event( $event_details, $order_id, false );
        }
    }
}
$orddd_calendar_sync = new orddd_calendar_sync();

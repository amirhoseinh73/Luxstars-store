<?php

/**
 * Order Delivery Date Pro for WooCommerce
 *
 * GDPR related fixes. 
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Pro-for-WooCommerce/Privacy
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

include_once( 'orddd-common.php' );

/**
 * GDPR related fixes. 
 *
 * @class orddd_privacy
 */
class orddd_privacy {
	/**
	 * Default Constructor
	 *
	 * @since 8.2
	 */

	public function __construct() {
        //Export Delivery Date & Time
		add_filter( "woocommerce_privacy_export_order_personal_data_props", array( &$this, "orddd_privacy_export_order_personal_data_props" ), 10, 2 );
        add_filter( "woocommerce_privacy_export_order_personal_data_prop", array( &$this, "orddd_privacy_export_order_personal_data_prop_callback" ), 10, 3 );

        // Admin notice for google calendar export
        add_action( 'admin_notices', array( &$this, 'orddd_privacy_admin_notices' ) );
        add_action( 'wp_ajax_orddd_dismiss_admin_notices', array( &$this, 'orddd_dismiss_admin_notices' ) );
	}

    /**
     * Add Delivery Date & Time row in the export data.
     *
     * @param array $props_to_export Properties to export
     * @param resource $order Order Object
     *
     * @return array Properties to export
     *
     * @since 8.2
     */
	function orddd_privacy_export_order_personal_data_props( $props_to_export, $order ) {
        $my_key_value   = array( 'delivery_details' => __( 'Delivery Date & Time', 'order-delivery-date' ) );
        $key_pos        = array_search( 'items', array_keys( $props_to_export ) );
        
        if ( $key_pos !== false ) {
            $key_pos++;
            
            $second_array       = array_splice( $props_to_export, $key_pos );        
            $props_to_export    = array_merge( $props_to_export, $my_key_value, $second_array );
        }

        return $props_to_export;
    }  

     /**
     * Add Delivery Date & Time row value in the export data.
     *
     * @param string $value Value to be displayed.
     * @param string $prop Row id.
     * @param resource $order Order Object
     *
     * @return array Properties to export
     *
     * @since 8.2
     */
    function orddd_privacy_export_order_personal_data_prop_callback( $value, $prop, $order ) {
        if ( $prop == "delivery_details" ) {
            $delivery_date = orddd_common::orddd_get_order_delivery_date( $order->get_id() );
            $delivery_time = orddd_common::orddd_get_order_timeslot( $order->get_id() );
            if( $delivery_time != '' ) {
                $value = $delivery_date . ' ' . $delivery_time;     
            } else {
                $value = $delivery_date;     
            }
        }
        return $value;
    }

    /**
     * Notice to inform administrator to mention about customer data being exported to google calendar.
     *
     * @since 8.2
     */
    function orddd_privacy_admin_notices() {
        if ( ( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == "calendar_sync_settings" ) || ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == "orddd_view_orders" ) ) {                
            $notice_shown = get_option( 'orddd_privacy_notice' );
            if ( 'dismissed' != $notice_shown ) {       
                echo '<div class="notice notice-warning orddd-privacy-notice is-dismissible"><p>';
                echo __( 'If you are syncing your deliveries to the Google calendar from <a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21" target="_blank">Order Delivery Date Pro for WooCommerce</a> plugin, then we will recommend you to mention about the customer data being exported to the Google Calendar from the plugin in your Privacy Policy as per the EU data protection laws through General Data Protection Regulation (“GDPR”).', 'order-delivery-date' );
                echo '</p></div>';
            }
        }
    }

    /**
     * Dismiss the privacy notice.
     *
     * @since 8.2
     */
    function orddd_dismiss_admin_notices() {
        if( isset( $_POST[ 'notice' ] ) && $_POST[ 'notice' ] == 'orddd-privacy-notice' ) {
            update_option( 'orddd_privacy_notice', 'dismissed' );
        }
    }
}

$orddd_privacy = new orddd_privacy();
<?php
/**
 * Order Delivery Date Pro for WooCommerce
 *
 * Update the necessary options in the database when plugin is updated. 
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Pro-for-WooCommerce/Update
 * @since       8.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if( is_admin() ) {
    include_once( 'orddd-common.php' ); 
}

/**
 * orddd_update Class
 *
 * @class orddd_update
 */

class orddd_update {

	/**
	 * Default Constructor
	 *
	 * @since 8.1
	 */
	public function __construct() {
		//Update plugin
	    add_action( 'admin_init', array( &$this, 'orddd_update_install' ) );

	    // Added in v9.6 for migrating orders that have a delivery date from Lite to Pro version
	    include_once( 'includes/orddd-import-lite-to-pro.php' );
	    global $orddd_version;
	    add_action( 'admin_menu', array( 'orddd_import_lite_to_pro', 'orddd_admin_menus' ) );
	    add_action( 'admin_init', array( 'orddd_import_lite_to_pro', 'orddd_migrate_admin_init' ) );
	    add_action( 'admin_notices', array( 'orddd_import_lite_to_pro', 'orddd_migrate_lite_to_pro_notice' ) );
	}

	/**
	 * Update the settings if required when the plugin is updated using the Automatic Updater.
	 *
	 * @globals resource $wpdb WordPress object
	 * @globals array $orddd_weekdays Weekdays Array
	 *
	 * @since 1.0
	 */
	public static function orddd_update_install() {
	    global $wpdb, $orddd_weekdays, $orddd_version;
	    
	    //code to set the option to on as default
	    $orddd_plugin_version = get_option( 'orddd_db_version' );
	    if ( $orddd_plugin_version != order_delivery_date::get_orddd_version() ) {
	        
            //Update Database version
            update_option( 'orddd_db_version', $orddd_version );
	        
            self::orddd_update_default_value_product_cat();

            self::orddd_update_location_label();
            
            self::orddd_enable_availability_display_update();

            self::orddd_show_partially_booked_dates_update();
		    
            self::orddd_migrate_to_scheduled_actions();
            
            self::orddd_update_default_values();
        }

	    //Update the shipping method with legacy prefix with the WooCommerce 2.6 update
        //TODO: Need to check if below code is needed anymore or not, if not, remove it
	    if ( defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, "2.6.0", '>=' ) ) {
	        if( get_option( 'orddd_update_shipping_method_id_delete' ) != 'yes' ) {
	            delete_option( 'orddd_update_shipping_method_id' );
	            update_option( 'orddd_update_shipping_method_id_delete', 'yes' );
	        }
	
	        if( get_option( 'orddd_update_shipping_method_id' ) != 'yes' ) {
	            $results = orddd_common::orddd_get_shipping_settings();
	            if( is_array( $results ) && count( $results ) > 0 ) {
	                foreach ( $results as $key => $value ) {
	                    $shipping_settings = get_option( $value->option_name );
	                    if ( isset( $shipping_settings[ 'delivery_settings_based_on' ] ) && $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] == 'shipping_methods' ) {
	                        $shipping_methods = $shipping_settings[ 'shipping_methods' ];
	                        foreach( $shipping_methods as $shipping_key => $shipping_value ) {
	                            if ( $shipping_value == 'flat_rate' ) {
	                                $shipping_settings[ 'shipping_methods' ][ $shipping_key ] = 'legacy_flat_rate';
	                            }
	
	                            if ( $shipping_value == 'free_shipping' ) {
	                                $shipping_settings[ 'shipping_methods' ][ $shipping_key ] = 'legacy_free_shipping';
	                            }
	
	                            if ( $shipping_value == 'international_delivery' ) {
	                                $shipping_settings[ 'shipping_methods' ][ $shipping_key ] = 'legacy_international_delivery';
	                            }
	
	                            if ( $shipping_value == 'local_pickup' ) {
	                                $shipping_settings[ 'shipping_methods' ][ $shipping_key ] = 'legacy_local_pickup';
	                            }
	
	                            if ( $shipping_value == 'local_delivery' ) {
	                                $shipping_settings[ 'shipping_methods' ][ $shipping_key ] = 'legacy_local_delivery';
	                            }
	                        }
	                    }
	                    update_option( $value->option_name, $shipping_settings );
	                }
	            }
	            update_option( 'orddd_update_shipping_method_id', 'yes' );
	        }
	    }


        // This condition confirm that the lite plugin active, so we need to perform further action.
        if ( in_array( 'order-delivery-date-for-woocommerce/order_delivery_date.php', (array) get_option( 'active_plugins', array() ) ) || 
            ( isset( $_GET [ 'orddd_plugin_link' ] ) && 'orddd-update' == $_GET ['orddd_plugin_link'] ) || 
        	'yes' === get_option( 'orddd_lite_installed' ) ) {
            require_once( "includes/orddd-import-lite-to-pro.php" );
        }

	}

   /**	
     * By default enable the Enable Delivery Date checkbox for all the product categories	
     *	
     * @since 8.1	
     */	
    public static function orddd_update_default_value_product_cat() {	
        global $wpdb;	
        if( get_option( 'update_delivery_product_category' ) != 'yes' ) {	
            $terms = $wpdb->get_results( 'SELECT term_id FROM ' . $wpdb->prefix . 'term_taxonomy WHERE taxonomy="product_cat"' );	
            foreach( $terms as $term_key => $term_value ) {	
                foreach( $term_value as $key => $v ) {	
                    if( $key == 'term_id') {	
                        $category_id = $term_value->term_id;	
                        update_term_meta( $category_id, 'orddd_delivery_date_for_product_category', 'on' );	
                    }	
                }	
            }	
            update_option( 'update_delivery_product_category', 'yes');	
        }
    }
    
    /**
     * Update Delivery Locations label for the checkout page. 
     *
     * @since 8.4
     */
    public static function orddd_update_location_label() {
         if ( get_option( 'orddd_update_location_label' ) != 'yes' ) {
            update_option( 'orddd_location_field_label', 'Pickup Location' );
            update_option( 'orddd_update_location_label', 'yes' );
        }
    }

    /**
     * Update default value of Display availability on date setting on Additional settings link.
     *
     * @since 8.9
     */
    public static function orddd_enable_availability_display_update() {
        if( 'yes' != get_option( 'orddd_enable_availability_display_update' ) ) {
            update_option( 'orddd_enable_availability_display', 'on' );
            update_option( 'orddd_enable_availability_display_update', 'yes' );
        }
    }

    /**
     * Update default value of Show Partially Booked Dates on the Delivery Calendar setting on Additional settings link.
     *
     * @since 8.9
     */
    public static function orddd_show_partially_booked_dates_update() {
        if( 'yes' != get_option( 'orddd_show_partially_booked_dates_update' ) ) {
            update_option( 'orddd_show_partially_booked_dates', 'on' );
            update_option( 'orddd_show_partially_booked_dates_update', 'yes' );
        }
    }

    public static function orddd_update_default_values() {
        if( '' === get_option( 'orddd_estimated_date_text' ) || false === get_option( 'orddd_estimated_date_text' ) ) {
            update_option( 'orddd_estimated_date_text', ORDDD_DELIVERY_ESTIMATE_TEXT );
        }
        
        if ( false === get_option( 'orddd_auto_populate_first_pickup_location' ) ) {
            update_option( 'orddd_auto_populate_first_pickup_location', 'on' );
        }

        if ( '' === get_option( 'orddd_delivery_dates_in_dropdown' ) || false === get_option( 'orddd_delivery_dates_in_dropdown' ) ) {
            update_option( 'orddd_delivery_dates_in_dropdown', 'no' );
        }
    }
	
    /**
     * Migrate Reminder email cron jobs to scheduled actions.
     *
     * @since 9.16.0
     */
    public static function orddd_migrate_to_scheduled_actions() {
	$timezone = orddd_send_reminder::orddd_get_timezone_string();
	// Convert Cron Jobs to Scheduled Actions - Reminder emails admin.
	if ( false === as_next_scheduled_action( 'orddd_auto_reminder_emails_admin' ) && 'on' === get_option( 'orddd_reminder_for_admin', '' ) ) {
            	wp_clear_scheduled_hook( 'orddd_auto_reminder_emails_admin' );
		$reminder_emails_admin_frequency = apply_filters( 'orddd_reminder_emails_admin_frequency', 86400 );
            	as_schedule_recurring_action( strtotime( '19:00:00 ' . $timezone ), $reminder_emails_admin_frequency, 'orddd_auto_reminder_emails_admin' );
        }

        // Convert cron jobs to scheduled actions - reminder emails customers.
        if ( false === as_next_scheduled_action( 'orddd_auto_reminder_emails' ) && 0 < get_option( 'orddd_reminder_email_before_days', 0 ) ) {
            	wp_clear_scheduled_hook( 'orddd_auto_reminder_emails' );
            	$reminder_time = apply_filters( 'orddd_modify_reminder_email_time', strtotime( '07:00:00 ' . $timezone ), $timezone );
		$reminder_emails_frequency = apply_filters( 'orddd_reminder_emails_frequency', 86400 );
            	as_schedule_recurring_action( $reminder_time, $reminder_emails_frequency, 'orddd_auto_reminder_emails' );
        }
        
    }
}

$orddd_update = new orddd_update();

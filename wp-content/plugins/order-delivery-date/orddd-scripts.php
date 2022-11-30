<?php
/**
 * Order Delivery Date Pro for WooCommerce
 *
 * Load JS and CSS files in admin and on frontend.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Pro-for-WooCommerce/Scripts
 * @since       8.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * orddd_scripts Class
 *
 * @class orddd_scripts
 */
class orddd_scripts {

	/**
	 * Default Constructor
	 *
	 * @since 8.1
	 */
	public function __construct() {
		// Admin Scripts
		add_action( 'admin_enqueue_scripts', array( &$this, 'orddd_my_enqueue_js' ), 1000001 );
		add_action( 'admin_enqueue_scripts', array( &$this, 'orddd_my_enqueue_css' ), 1000001 );
		add_action( 'admin_enqueue_scripts', array( &$this, 'orddd_delivery_enqueue_js' ) );
        add_action( 'admin_enqueue_scripts', array( &$this, 'orddd_delivery_enqueue_css' ) );

		add_action( 'admin_enqueue_scripts', array( &$this, 'orddd_admin_dequeue_scripts' ), PHP_INT_MAX );

		$orddd_shopping_cart_hook = orddd_common::orddd_get_shopping_cart_hook();
        add_action( 'wp_enqueue_scripts',  array( &$this, 'orddd_front_scripts_js' ), 100 );
        add_action( 'wp_enqueue_scripts',  array( &$this, 'orddd_front_scripts_css' ), 100 );

        add_action( 'wp_enqueue_scripts', array( &$this, 'orddd_front_dequeue_scripts' ), 1000001 );
		add_action( 'init', array( &$this, 'orddd_schedule_weekly_lockout_cleanup_action' ) );
	}

	/**
	 * Load JS files on Admin
	 * 
	 * @hook admin_enqueue_scripts
	 * @globals array $orddd_languages Languages array
	 * @globals array $orddd_version Current plugin version 
	 * 
	 * @param string $hook Current page
	 * @since 1.0
	 */
	public function orddd_my_enqueue_js( $hook ) {
        global $orddd_languages, $orddd_version, $orddd_weekdays;

        wp_enqueue_script(
            'orddd_dismiss_notice',
            plugins_url( '/js/dismiss-notice.js', __FILE__ ),
            '',
            '',
            false
        );
        
        if ( 'order-delivery-date_page_orddd_system_status_page' == $hook ) {        
            wp_enqueue_script( 'orddd-system-status', plugins_url( '/js/orddd-system-status.js', __FILE__ ), '', $orddd_version, false );
        }

        if ( 'order-delivery-date_page_orddd_send_reminder_page' == $hook ) {            
            wp_enqueue_script( 'orddd-delivery-reminder', plugins_url( '/js/orddd-send-reminder.js', __FILE__ ), '', $orddd_version, false );
            $ajax_url           = get_admin_url() . 'admin-ajax.php';
            wp_localize_script( 'orddd-delivery-reminder', 'orddd_reminder_params', array( 'ajax_url' => $ajax_url ) );
        }

        if ( 'toplevel_page_order_delivery_date' == $hook ) {
            wp_enqueue_script( 'themeswitcher-orddd', plugins_url( '/js/jquery.themeswitcher.min.js', __FILE__ ), array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-datepicker' ), $orddd_version, false );

			foreach ( $orddd_languages as $key => $value ) {
                wp_enqueue_script( $value, plugins_url( "/js/i18n/jquery.ui.datepicker-$key.js", __FILE__ ), array( 'jquery', 'jquery-ui-datepicker' ), $orddd_version, false );
            }
            
            wp_enqueue_script( 'wp-color-picker' ); 

            //Remove the select2 library from Bakery theme.
            wp_deregister_script( 'select2' );
            wp_register_script( 'select2', plugins_url() . '/woocommerce/assets/js/select2/select2.min.js', array( 'jquery', 'jquery-ui-widget', 'jquery-ui-core' ), $orddd_version );
            wp_enqueue_script( 'select2' );
            
            wp_register_script( 'datepick', plugins_url().'/order-delivery-date/js/jquery.datepick.js', '', $orddd_version, false );
            wp_enqueue_script( 'datepick' );

            wp_enqueue_script( 'tiptip', plugins_url() . '/woocommerce/assets/js/jquery-tiptip/jquery.tipTip.min.js', array(), WC_VERSION );

            wp_enqueue_script( 'orddd-common', plugins_url() . '/order-delivery-date/js/ordd-common-settings.js', array( 'jquery', 'jquery-ui-core' ), WC_VERSION );
            if ( 'woodmart' === get_option( 'template' ) ) {
                wp_deregister_script( 'jquery-datetimepicker' );
            }


            wp_enqueue_script( 'timepicker', plugins_url() . '/order-delivery-date/js/jquery.timepicker.min.js', array(), WC_VERSION );
            wp_enqueue_script( 'orddd-bulk-timeslots', plugins_url() . '/order-delivery-date/js/orddd-bulk-time-slots.js', array(), WC_VERSION );
            $currency_symbol = get_woocommerce_currency_symbol();

            $jsArgs = array(
                        'holidaynameText'      => __( 'Name', 'order-delivery-date' ),
				        'holidaydateText'      => __( 'Date', 'order-delivery-date' ),
				        'holidaytypeText'      => __( 'Type', 'order-delivery-date' ),
				        'holidayactionText'    => __( 'Actions', 'order-delivery-date' ),
				        'holidaydeleteText'    => __( 'Delete', 'order-delivery-date' ),
				        'holidayrecurringText' => __( 'Recurring', 'order-delivery-date' ),
                        'holidaycurrentText'   => __( 'Current Year', 'order-delivery-date' ),
                        'specificDateName'     => __( 'Delivery Dates', 'order-delivery-date' ),
                        'specificChargesText'  => __( 'Additional Charges', 'order-delivery-date' ),
                        'specificChargesLabel' => __( 'Checkout Label', 'order-delivery-date' ),
                        'currency'             => $currency_symbol,
                        'orddd_weekdays'       => wp_json_encode( $orddd_weekdays ),
                        'ajax_url'             => get_admin_url() . 'admin-ajax.php',
                        'plugins_url'          => plugins_url(),
				    );
            wp_localize_script( 'orddd-common', 'localizeStrings', $jsArgs );

            $timeslotArgs = array(
                'timeslotDayText'          => __( 'Delivery days/dates', 'order-delivery-date' ),
                'timeslotText'             => __( 'Time Slot', 'order-delivery-date' ),
                'timeslotLockoutText'      => __( 'Maximum Order Deliveries per time slot', 'order-delivery-date' ),
                'timeslotChargesText'      => __( 'Additional Charges for time slot', 'order-delivery-date' ),
                'timeslotChargesLabelText' => __( 'Checkout Label', 'order-delivery-date' ),
                'timeslotActionsText'      => __( 'Actions', 'order-delivery-date' ),
                'currency'                 => $currency_symbol,
                'orddd_weekdays'           => wp_json_encode( $orddd_weekdays ),
                'time_format'              => get_option( 'orddd_delivery_time_format' ),
                'ajax_url'                 => get_admin_url() . 'admin-ajax.php',
            );
            wp_localize_script( 'orddd-bulk-timeslots', 'timeslotStrings', $timeslotArgs );

            if( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'advance_settings' ) {
                 // Localize and enqueue our js.
                $base_url = admin_url( add_query_arg( array(
                    'page'    => 'order_delivery_date',
                    'action'  => 'advance_settings',
                ), 'admin.php' ) );

                $advance_settings = get_option( 'orddd_advance_settings' );
                if( '' == $advance_settings || '{}' == $advance_settings || '[]' == $advance_settings ) {
                    $advance_settings = array();
                }

                wp_register_script( 'orddd-advance-settings-script', plugins_url('/js/orddd-advance-settings.js', __FILE__ ), array( 'jquery', 'underscore', 'backbone','wp-util', 'jquery-blockui' ) );

                wp_localize_script( 'orddd-advance-settings-script', 'htmlAdvanceSettingsLocalizeScript', array(
                    'base_url'                => $base_url,
                    'page'          => ! empty( $_GET[ 'p' ] ) ? absint( sanitize_text_field( $_GET[ 'p' ] ) ) : 1,
                    'orddd_advance_settings' => array_values( $advance_settings ),
                    'default_settings'  => array(
                        'row_id'       => 0,
                        'additional_charges'  => '',
                        'delivery_charges_label'    => '',
                        'orddd_weekdays'          => '',
                        'orddd_disable_same_day_delivery_after_hours' => '',
                        'orddd_disable_next_day_delivery_after_hours' => '',
                        'orddd_minimumOrderDays' => '',
                        'orddd_before_cutoff_weekday'    => '',
                        'orddd_after_cutoff_weekday'    => '',
                    ),
                    'strings'       => array(
                        'no_rows_selected' => __( 'No row(s) selected', 'order-delivery-date' ),
                        'unload_confirmation_msg' => __( 'Your changed data will be lost if you leave this page without saving.', 'order-delivery-date' ),
                        'success_message' => __( 'Settings saved.', 'order-delivery-date' ),
                    ),
                ) );

                wp_enqueue_script( 'orddd-advance-settings-script' );
            }

            if( isset( $_GET['action'] ) && ( 'general_settings' === $_GET['action'] && isset( $_GET['section'] ) && 'time_settings' === $_GET['section'] ) || isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) {
                wp_register_script( 'orddd-additional-delivery-charge', plugins_url('/js/orddd-additional-delivery-charge.js', __FILE__ ), array( 'jquery' ) );
                wp_enqueue_script( 'orddd-additional-delivery-charge' );
            }
            if( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'orddd_locations' ) {
                 // Localize and enqueue our js.
                $base_url = admin_url( add_query_arg( array(
                    'page'    => 'order_delivery_date',
                    'action'  => 'orddd_locations',
                ), 'admin.php' ) );

                $locations = get_option( 'orddd_locations' );
                if( '' == $locations || '{}' == $locations || '[]' == $locations ) {
                    $locations = array();
                }

                wp_register_script( 'orddd-locations-script', plugins_url('/js/orddd-locations.js', __FILE__ ), array( 'jquery', 'underscore', 'backbone','wp-util', 'jquery-blockui' ) );

                wp_localize_script( 'orddd-locations-script', 'locations', array(
                    'base_url'                => $base_url,
                    'page'          => ! empty( $_GET[ 'p' ] ) ? absint( sanitize_text_field( $_GET[ 'p' ] ) ) : 1,
                    'orddd_locations' => array_values( $locations ),
                    'default_settings'  => array(
                        'row_id'       => 0,
                        'address1'  => '',
                        'address2'    => '',
                        'city'          => '',
                        'state' => '',
                        'postcode' => '',
                        'country' => '',
                    ),
                    'strings'       => array(
                        'no_rows_selected' => __( 'No row(s) selected', 'order-delivery-date' ),
                        'unload_confirmation_msg' => __( 'Your changed data will be lost if you leave this page without saving.', 'order-delivery-date' ),
                        'success_message' => __( 'Settings saved.', 'order-delivery-date' ),
                    ),
                ) );

                wp_enqueue_script( 'orddd-locations-script' );
            }
		}
		
		$current_screen = get_current_screen();
		if ( 'on' == get_option( 'orddd_enable_delivery_date' ) ) {

            if ( isset( $_GET['post_type'] ) && 'shop_order' === $_GET['post_type'] ) {
                wp_enqueue_script(
                    'orddd-shop-order',
                    plugins_url( '/js/orddd-shop-order.js', __FILE__ ),
                    array( 'jquery' ),
                    $orddd_version,
                    false
                );
            }

            if( $current_screen->id == 'shop_order' || 
              ( $current_screen->id == 'shop_subscription' && 'on' == get_option( 'orddd_enable_woo_subscriptions_compatibility' ) ) ) {
			    
                wp_enqueue_script( 'moment-orddd', plugins_url('/js/moment.min.js', __FILE__ ), '', $orddd_version, true );

                wp_enqueue_script( 'initialize-datepicker-functions-orddd', plugins_url( '/js/initialize-datepicker-functions.js', __FILE__ ), array( 'initialize-datepicker-orddd' ), $orddd_version, false );

			    wp_enqueue_script( 'initialize-datepicker-orddd', plugins_url( '/js/initialize-datepicker.js', __FILE__ ), array( 'jquery' ), $orddd_version, false );

			    wp_enqueue_script( 'orddd-js', plugins_url( '/js/orddd-admin-delivery.js', __FILE__ ), '', $orddd_version, false );

			    $is_admin = is_admin() ? true : false;
			    $jsArgs = array(
                        'selectText'      => __( 'Select a time slot', 'order-delivery-date' ),
                        'clearText'       => __( 'Clear', 'order-delivery-date' ),
                        'asapText'        => __( 'As Soon As Possible', 'order-delivery-date' ),
				        'wooVersion'      => get_option( 'woocommerce_version' ),
			            'is_admin'        => $is_admin,
			            'bookedText'      => __( 'Booked', 'order-delivery-date' ),
		            	'cutOffTimeText'  => __( 'Cut-off time over', 'order-delivery-date' ),
                        'success_delivery_date_message' => __( '<b>Successfully edited the delivery date. Please wait until the page reloads.</b>', 'order-delivery-date' )
				    );
                wp_localize_script( 'initialize-datepicker-orddd', 'jsL10n', $jsArgs );
			    
                $orddd_settings = orddd_common::orddd_localize_data_script();
                wp_localize_script( 'initialize-datepicker-functions-orddd', 'orddd_params', $orddd_settings ); 
                wp_localize_script( 'initialize-datepicker-orddd', 'orddd_params', $orddd_settings ); 
                wp_localize_script( 'orddd-js', 'orddd_params', $orddd_settings ); 

                $admin_params = orddd_admin_delivery_class::orddd_localize_admin_scripts();
                wp_localize_script( 'initialize-datepicker-functions-orddd', 'orddd_admin_params', $admin_params ); 
                wp_localize_script( 'initialize-datepicker-orddd', 'orddd_admin_params', $admin_params ); 
                wp_localize_script( 'orddd-js', 'orddd_admin_params', $admin_params );

			    $time_settings_for_shipping_methods =  orddd_common::orddd_time_settings_enable_for_custom_delivery();
			    
			    if ( get_option( 'orddd_enable_delivery_time' ) == 'on' || $time_settings_for_shipping_methods == 'yes' ) {
			        wp_dequeue_script( 'jquery-ui-timepicker-addon' );
			        wp_enqueue_script( 'jquery-ui-orddd-timepicker-addon', plugins_url( '/js/jquery-ui-timepicker-addon.js', __FILE__ ), array( 'jquery', 'jquery-ui-slider', 'jquery-ui-core', 'jquery-ui-datepicker' ), $orddd_version, false );
			    
			        //This array is used to make the time slider text/lavble compatiable with the WPML...localize our js
			        $aryArgs = array(
			            'timeText'      => __( 'Time', 'order-delivery-date' ),
			            'hourText'      => __( 'Hour', 'order-delivery-date' ),
			            'currentText'   => __( 'Now', 'order-delivery-date' ),
			            'closeText'     => __( 'Done', 'order-delivery-date' ),
			            'timeOnlyTitle' => __( 'Choose Time', 'order-delivery-date' ),
			            'minuteText'    => __( 'Minute', 'order-delivery-date' ),
			            'secondText'    => __( 'Second', 'order-delivery-date' ),
			            'millisecText'  => __( 'Millisecond', 'order-delivery-date' ),
			        );
			    
			        //Pass the array to the enqueued JS
			        wp_localize_script( 'jquery-ui-orddd-timepicker-addon', 'objectL10n', $aryArgs );
			        wp_dequeue_script( 'jquery-ui-sliderAccess' );
			        wp_enqueue_script( 'jquery-ui-orddd-sliderAccess', plugins_url( '/js/jquery-ui-sliderAccess.js', __FILE__ ), '', $orddd_version, false );
			    }
			}
            do_action( 'orddd_include_admin_scripts' );
        }

        if ( 'toplevel_page_order_delivery_date' === $current_screen->id && 
             isset( $_GET[ 'action' ] ) && 'shipping_based' === $_GET[ 'action' ] ) {
            wp_enqueue_script( 'orddd-custom-setting-activate-js', plugins_url() . '/order-delivery-date/js/custom_setting_activate.js' );
        }

        if ( $current_screen->id != 'edit-shop_order' ) {
            wp_enqueue_script( 'orddd_import_lite_data', plugins_url( '/js/orddd_import_lite_data.js', __FILE__ ) , '', $orddd_version, false );
        }
	}
    
	/**
	 * Load CSS files on Admin
	 * 
	 * @hook admin_enqueue_scripts
	 * @globals array $orddd_version Current plugin version 
	 * 
	 * @param string $hook Current page
	 * @since 1.0
	 */ 
	
	public static function orddd_my_enqueue_css( $hook ) {
	    global $orddd_version;
        if ( 'woodmart' === get_option( 'template' ) ) {
            wp_dequeue_style( 'woodmart-jquery-ui' );
        }
        if ( 'toplevel_page_order_delivery_date' == $hook ) {
            wp_enqueue_style( 'wp-color-picker' ); 
            wp_enqueue_style( 'order-delivery-date', plugins_url( '/css/order-delivery-date.css', __FILE__ ) , '', $orddd_version, false );
            wp_style_add_data( 'order-delivery-date', 'rtl', 'replace' );
            wp_register_style( 'jquery-ui-style-orddd', esc_url( plugins_url( '/css/themes/smoothness/jquery-ui.css', __FILE__ ) ), '', $orddd_version, false );
            wp_enqueue_style( 'jquery-ui-style-orddd' );
            wp_enqueue_style( 'datepicker', plugins_url( '/css/datepicker.css', __FILE__ ) , '', $orddd_version, false );
            wp_enqueue_style( 'orddd-datepick', plugins_url('/css/jquery.datepick.css', __FILE__ ) , '', $orddd_version, false );
            wp_enqueue_style( 'timepicker-css', plugins_url() . '/order-delivery-date/css/jquery.timepicker.min.css', array(), WC_VERSION );
        }
        
        $current_screen = get_current_screen();
        if ( 'on' == get_option( 'orddd_enable_delivery_date' ) ) {
			if( $current_screen->id == 'shop_order' || 
                ( $current_screen->id == 'shop_subscription' && 'on' == get_option( 'orddd_enable_woo_subscriptions_compatibility' ) ) ) {
                wp_enqueue_style( 'order-delivery-date', plugins_url( '/css/order-delivery-date.css', __FILE__ ) , '', $orddd_version, false );
                wp_register_style( 'jquery-ui-style-orddd', esc_url( plugins_url( '/css/themes/smoothness/jquery-ui.css', __FILE__ ) ), '', $orddd_version, false );
                wp_enqueue_style( 'jquery-ui-style-orddd' );
                wp_enqueue_style( 'jquery-ui-timepicker-addon-orddd', plugins_url( '/css/jquery-ui-timepicker-addon.css', __FILE__ ), '', $orddd_version, false );
            }
        }
        
        if( $current_screen->id == 'edit-shop_order' || $current_screen->id == 'edit-product_cat' ) {
            wp_enqueue_style( 'order-delivery-date', plugins_url( '/css/order-delivery-date.css', __FILE__ ) , '', $orddd_version, false );
        }

        if ( 'toplevel_page_order_delivery_date' === $current_screen->id && 
             isset( $_GET[ 'action' ] ) && 'shipping_based' === $_GET[ 'action' ] ) {
            wp_enqueue_style( 'orddd-custom-setting-activate', plugins_url( '/css/custom_setting_activate.css', __FILE__ ) , '', $orddd_version, false );
        }

        if ( isset( $_GET['page'] ) && 'order_delivery_date' === $_GET['page'] ) {
            // Display a notice for the subscription addon.
            add_action( 'orddd_add_after_tab_content', array( 'orddd_settings', 'orddd_add_note' ) );
        }
	}

	/**
     * Load JS files on Delivery calendar page
     *
     * @hook admin_enqueue_scripts
     * @param string $hook - Page name
     * @since 2.8.7
     */
    
    public static function orddd_delivery_enqueue_js( $hook ) {
        if( $hook == 'order-delivery-date_page_orddd_view_orders' ) {
            wp_register_script( 'select2',             plugins_url() . '/woocommerce/assets/js/select2/select2.min.js', array( 'jquery', 'jquery-ui-widget', 'jquery-ui-core' ) );
            wp_enqueue_script( 'select2' );
            wp_enqueue_script( 'jquery' );
            wp_register_script( 'moment-orddd-js',     plugins_url( '/js/moment.min.js', __FILE__ ) );
            wp_register_script( 'lang-all-js',         plugins_url( '/js/fullcalendar/lib/locales-all.min.js', __FILE__ ) );
            wp_register_script( 'full-orddd-js',       plugins_url( '/js/fullcalendar/lib/main.min.js', __FILE__ ) );
            wp_register_script( 'orddd-images-loaded', plugins_url( '/js/imagesloaded.pkg.min.js', __FILE__ ) );
            wp_register_script( 'orddd-qtip',          plugins_url( '/js/jquery.qtip.min.js', __FILE__ ), array( 'jquery', 'orddd-images-loaded' ) );

            wp_enqueue_script( 'orddd-qtip' );
            wp_enqueue_script( 'moment-orddd-js' );
            wp_enqueue_script( 'full-orddd-js' );
            wp_enqueue_script( 'lang-all-js' );
            wp_enqueue_script( 'orddd-images-loaded' );
            wp_enqueue_script( 'jquery-ui-position' );
            wp_enqueue_script( 'jquery-ui-selectmenu' );

            wp_enqueue_script( 'orddd-calendar-js',    plugins_url( '/js/orddd-view-calendar.js', __FILE__ ));

            $holidays_arr = array();
            $holidays = get_option( 'orddd_delivery_date_holidays' );
            if ( $holidays != '' && $holidays != '{}' && $holidays != '[]' && $holidays != 'null' ) {
                $holidays_arr = json_decode( get_option( 'orddd_delivery_date_holidays' ) );
            }
            $holidays_str = "";
            foreach ( $holidays_arr as $k => $v ) {
                if( isset( $v->r_type ) && $v->r_type == 'on' ) {
                    $holiday_date_arr = explode( "-", $v->d );
                    $recurring_date = $holiday_date_arr[ 0 ] . "-" . $holiday_date_arr[1];
                    $holidays_str .= '"' . $recurring_date . '",';    
                } else {
                    $holidays_str .= '"' . $v->d . '",';  
                }
            }
            
            $holidays_str = substr( $holidays_str, 0, strlen( $holidays_str )-1 );

            $language_selected = get_option( 'orddd_language_selected' );
            if ( '' === $language_selected ) {
                $language_selected = "en-GB";
            }

            $args = array(
                'orddd_holiday_color' => get_option( 'orddd_holiday_color' ),
                'orddd_holidays'      => $holidays_str,
                'calendar_language'   => $language_selected,
                'admin_url'           => get_admin_url(),
                'ajax_url'            => get_admin_url() . 'admin-ajax.php',
            );

            //Pass the array to the enqueued JS
            wp_localize_script( 'full-orddd-js', 'jsArgs', $args );
            wp_localize_script( 'orddd-calendar-js', 'jsArgs', $args );

            self::localize_script();
        }
    }
    
    /**
     * Load JS file for the data in the Delivery calendar
     *
     * 
     * @since 2.8.7
     */
    public static function localize_script() {
        $js_vars = array();
        $schema = is_ssl() ? 'https':'http';
        $js_vars[ 'ajaxurl' ] = admin_url( 'admin-ajax.php', $schema );
        $js_vars[ 'pluginurl' ] = admin_url() . 'admin.php?action=orddd-adminend-events-jsons';
        wp_localize_script( 'orddd-calendar-js', 'orddd', $js_vars );
    }
    

    /**
     * Load CSS files on Delivery calendar page
     *  
     * @hook admin_enqueue_scripts
     * @param string $hook - Page name
     * @since 2.8.7
     */
    public static function orddd_delivery_enqueue_css( $hook ) {
        global $orddd_version;
        if ( $hook == 'order-delivery-date_page_orddd_view_orders' ) {
            $calendar_theme = get_option( 'orddd_calendar_theme' );
            if ( $calendar_theme == '' ) {
                $calendar_theme = 'base';
            }
            wp_register_style( 'jquery-ui-style-orddd',               esc_url( plugins_url( "/css/themes/$calendar_theme/jquery-ui.css", __FILE__ ) ), '', $orddd_version, false );
            wp_enqueue_style( 'jquery-ui-style-orddd' );
            wp_enqueue_style( 'fullcalendar-orddd',             plugins_url( '/js/fullcalendar/lib/main.min.css', __FILE__ ) );
            // this is for the hover effect
            wp_enqueue_style( 'qtip-orddd-css',                 plugins_url( '/css/jquery.qtip.min.css', __FILE__ ), array() );
            wp_enqueue_style( 'order-delivery-date',            plugins_url( '/css/order-delivery-date.css', __FILE__ ) , '', '', false );
        } else {
            return;
        }
    }


    /**
     * Load JS file on Frontend
     * 
     * @hook woocommerce_after_checkout_billing_form
 	 * @hook woocommerce_after_checkout_shipping_form
     * @hook woocommerce_before_order_notes
     * @hook woocommerce_after_order_notes
     *
     * @globals int $orddd_version Current plugin version
     * @since 1.0
     */
	public static function orddd_front_scripts_js() {
        global $orddd_version, $current_user;
        $is_dropdown = get_option( 'orddd_delivery_dates_in_dropdown' );

        wp_register_script( 'select2',             plugins_url() . '/woocommerce/assets/js/select2/select2.min.js', array( 'jquery', 'jquery-ui-widget', 'jquery-ui-core' ) );
        wp_enqueue_script( 'select2' );
        
        wp_register_script( 'orddd-availability-widget', plugins_url( '/js/orddd-availability-widget.js', __FILE__ ), array( 'jquery', 'jquery-ui-datepicker' ), $orddd_version, true );
        wp_localize_script( 'orddd-availability-widget', 'availability_widget', array( 'current_postcode' => __( 'Current Postcode: ', 'order-delivery-date' ) ) );
        
        wp_dequeue_script( 'initialize-datepicker' );
        wp_register_script( 'initialize-datepicker-orddd', plugins_url( '/js/initialize-datepicker.js', __FILE__ ), '', $orddd_version, true );
        wp_register_script      ( 'initialize-datepicker-functions-orddd', plugins_url( '/js/initialize-datepicker-functions.js', __FILE__ ), '', $orddd_version, true );
        wp_register_script ( 'accessibility-orddd', plugins_url( '/js/accessibility.js', __FILE__ ), '', $orddd_version, true );
        wp_register_script ( 'orddd-date-dropdown', plugins_url( '/js/initialize-date-dropdown.js', __FILE__ ), '', $orddd_version, true );

        $is_admin = is_admin() ? true : false;
        $jsArgs = array(
            'selectText'                    => __( 'Select a time slot', 'order-delivery-date' ),
            'clearText'                     => __( 'Clear', 'order-delivery-date' ),
            'asapText'                      => __( 'As Soon As Possible', 'order-delivery-date' ),
            'NAText'                        => __( 'No time slots are available', 'order-delivery-date' ),
            'wooVersion'                    => get_option( 'woocommerce_version' ),
            'is_admin'                      => $is_admin,
            'bookedText'                    => __( 'Booked', 'order-delivery-date' ),
            'cutOffTimeText'                => __( 'Cut-off time over', 'order-delivery-date' ), 
            'success_delivery_date_message' => __( '<b>Successfully edited the delivery date. Please wait until the page reloads.</b>', 'order-delivery-date' ),
            'is_dropdown_field'             => get_option( 'orddd_delivery_dates_in_dropdown', '' ),
            'is_timeslot_list_view'         => get_option( 'orddd_time_slots_in_list_view', '' ),
            'emptyListText'                 => apply_filters( 'orddd_modify_empty_timeslot_list_text', __( 'Select a date to view time slots', 'order-delivery-date' ) )
        );
        wp_localize_script( 'initialize-datepicker-functions-orddd', 'jsL10n', $jsArgs ); 
        wp_localize_script( 'initialize-datepicker-orddd', 'jsL10n', $jsArgs ); 
        wp_localize_script( 'orddd-date-dropdown', 'jsL10n', $jsArgs ); 
        
        $orddd_settings = orddd_common::orddd_localize_data_script();
        wp_localize_script( 'initialize-datepicker-functions-orddd', 'orddd_params', $orddd_settings ); 
        wp_localize_script( 'initialize-datepicker-orddd', 'orddd_params', $orddd_settings ); 
        wp_localize_script( 'orddd-date-dropdown', 'orddd_params', $orddd_settings ); 

        $accessibility_args = array(
            'orddd_field_name'       => $orddd_settings['orddd_field_name'],
            'orddd_number_of_months' => $orddd_settings['orddd_number_of_months']
        );
        wp_localize_script( 'accessibility-orddd', 'orddd_access', $accessibility_args ); 
        
        if ( isset( $_GET[ 'lang' ] ) && $_GET[ 'lang' ] != '' && $_GET[ 'lang' ] != null ) {
            $language_selected = $_GET[ 'lang' ];
        } else {
            $language_selected = get_option( 'orddd_language_selected' );
            if ( defined( 'ICL_LANGUAGE_CODE' ) ) { 
                if( constant( 'ICL_LANGUAGE_CODE' ) != '' ) {
                    $wpml_current_language = constant( 'ICL_LANGUAGE_CODE' );
                    if ( !empty( $wpml_current_language ) ) {
                        $language_selected = $wpml_current_language;
                    } else {
                        $language_selected = get_option( 'orddd_language_selected' );
                    }
                }
            }
            if ( $language_selected == "" ) {
                $language_selected = "en-GB";
            }
        }

        wp_register_script( 'orddd_language', plugins_url( "/js/i18n/jquery.ui.datepicker-$language_selected.js", __FILE__ ), array( 'jquery', 'jquery-ui-datepicker' ), $orddd_version, true );

        $is_delivery_enabled = orddd_common::orddd_is_delivery_enabled();
        $allow_customers_to_edit_date_enabled = get_option( 'orddd_allow_customers_to_edit_date' );
        $is_enabled_on_cart_page = get_option( 'orddd_delivery_date_on_cart_page' );
        $is_view_subscription_page = orddd_common::orddd_is_view_subscription_page();

        $time_settings_for_shipping_methods =  orddd_common::orddd_time_settings_enable_for_custom_delivery();
        if ( get_option( 'orddd_enable_delivery_time' ) == 'on' || $time_settings_for_shipping_methods == 'yes' ) {
            wp_dequeue_script( 'jquery-ui-timepicker-addon' );
            wp_dequeue_script( 'jquery-ui-timepicker' ); // For Checkout Manager Plugin.
            wp_dequeue_script( 'wcrp-jqueryuitimepickeraddon' );
            wp_register_script( 'jquery-ui-orddd-timepicker-addon', plugins_url( '/js/jquery-ui-timepicker-addon.js', __FILE__ ), array( 'jquery', 'jquery-ui-slider' ), $orddd_version, true );
            // This array is used to make the time slider text/lavble compatiable with the WPML...localize our js
            $aryArgs = array(
                'timeText'      => __( 'Time', 'order-delivery-date' ),
                'hourText'      => __( 'Hour', 'order-delivery-date' ),
                'currentText'   => __( 'Now', 'order-delivery-date' ),
                'closeText'     => __( 'Done', 'order-delivery-date' ),
                'timeOnlyTitle' => __( 'Choose Time', 'order-delivery-date' ),
                'minuteText'    => __( 'Minute', 'order-delivery-date' ),
                'secondText'    => __( 'Second', 'order-delivery-date' ),
                'millisecText'  => __( 'Millisecond', 'order-delivery-date' ),
                'microsecText'  => __( 'Microsecond', 'order-delivery-date' )
            );

            //Pass the array to the enqueued JS
            wp_localize_script( 'jquery-ui-orddd-timepicker-addon', 'objectL10n', $aryArgs );

            wp_dequeue_script( 'jquery-ui-sliderAccess' );
            wp_dequeue_script( 'wcrp-jqueryuislideraccess' );
            wp_register_script( 'jquery-ui-orddd-sliderAccess', plugins_url( '/js/jquery-ui-sliderAccess.js', __FILE__ ), array( 'jquery', 'jquery-ui-button' ), $orddd_version, true );
        }

        if( 'yes' == $is_delivery_enabled && ( is_checkout() && !is_wc_endpoint_url( 'order-received' ) && !is_wc_endpoint_url( 'order-pay' ) || ( 'on' == $allow_customers_to_edit_date_enabled && ( is_wc_endpoint_url( 'view-order' ) || ( true === $is_view_subscription_page ) ) ) || ( is_cart() && 'on' == $is_enabled_on_cart_page ) ) ) {
            $roles = array();
            if ( has_filter( 'orddd_disable_delivery_for_user_roles' ) ) {
                $roles = apply_filters( 'orddd_disable_delivery_for_user_roles', $roles );
            }
            
            $current_user_role = '';
            if( isset( $current_user->roles[0] ) ) {
                $current_user_role = $current_user->roles[0];
            }

    	    if ( get_option( 'orddd_enable_delivery_date' ) == 'on' && ( orddd_process::woo_product_has_delivery() === 'on' ) && !in_array( $current_user_role, $roles ) ) {
    	    	//$delivery_calendar_for_shipping_methods = orddd_common::orddd_is_delivery_calendar_enabled_for_custom_delivery();
        		if( 'delivery_calendar' == get_option( 'orddd_delivery_checkout_options' ) || 'on' == get_option( 'orddd_enable_shipping_based_delivery' )  ) {

                    wp_enqueue_script( 'orddd_language' );
                    wp_enqueue_script( 'moment-orddd', plugins_url('/js/moment.min.js', __FILE__ ), '', $orddd_version, true );
                    
                    //Conflict with the bootstrap datepicker which is used by Bakery Custom Post Types plugin & Bakery theme. 
                    wp_deregister_script( 'bootstrap-datepicker' );
                    wp_dequeue_script( 'bootstrap-datepicker' );
                    wp_dequeue_script( 'bootstrap-multiselect' );
                    wp_enqueue_script( 'initialize-datepicker-functions-orddd' );

                    if( 'yes' === $is_dropdown ) {
                        wp_enqueue_script( 'orddd-date-dropdown' );
                    } else {
                        wp_enqueue_script( 'initialize-datepicker-orddd' );
                        wp_enqueue_script( 'accessibility-orddd' );
                    }
                  
                    if( true == wp_script_is( 'jquery-ui-orddd-timepicker-addon', 'registered' ) ) {
                        wp_enqueue_script( 'jquery-ui-orddd-timepicker-addon' );
                    }
                    
                    if( true == wp_script_is( 'jquery-ui-orddd-sliderAccess', 'registered' ) ) {
                        wp_enqueue_script( 'jquery-ui-orddd-sliderAccess' );
                    }
                } else {
                    wp_register_script( 'orddd-text-block', plugins_url( '/js/orddd-text-block.js', __FILE__ ), '', $orddd_version, true );
                    $args = array(
                        'orddd_auto_populate_first_pickup_location' => get_option( 'orddd_auto_populate_first_pickup_location' ),
                    );

                    wp_localize_script( 'orddd-text-block','orddd_text_params', $args );

                    wp_enqueue_script( 'orddd-text-block' );

                }
    	    }
    		do_action( 'orddd_include_front_scripts' );
        }

        if( 'yes' == $is_delivery_enabled && is_wc_endpoint_url( 'order-received' ) ) {
            wp_register_script( 'orddd-remove-storage', plugins_url( '/js/orddd-remove-storage.js', __FILE__ ), '', $orddd_version, true );
            wp_enqueue_script( 'orddd-remove-storage' );
        }
	}

	/**
	 * Load CSS files on Frontend
	 * 
	 * @hook woocommerce_after_checkout_billing_form
 	 * @hook woocommerce_after_checkout_shipping_form
 	 * @hook woocommerce_before_order_notes
 	 * @hook woocommerce_after_order_notes
     *
	 * @globals int $orddd_version Current plugin version
     * @since 1.0
	 */
	public static function orddd_front_scripts_css() {
        global $orddd_version, $current_user;
        $calendar_theme = get_option( 'orddd_calendar_theme' );
        if ( $calendar_theme == '' ) {
            $calendar_theme = 'base';
        }
        
        wp_dequeue_style( 'jquery-ui-style-orddd' );
        wp_register_style( 'jquery-ui-style-orddd', esc_url( plugins_url( "/css/themes/$calendar_theme/jquery-ui.css", __FILE__ ) ), '', $orddd_version, false );

        wp_register_style( 'orddd-datepicker', plugins_url( '/css/datepicker.css', __FILE__ ) , '', $orddd_version, false );

        $is_delivery_enabled = orddd_common::orddd_is_delivery_enabled();
        $allow_customers_to_edit_date_enabled = get_option( 'orddd_allow_customers_to_edit_date' );
        $is_enabled_on_cart_page = get_option( 'orddd_delivery_date_on_cart_page' );
        $is_view_subscription_page = orddd_common::orddd_is_view_subscription_page();

        $time_settings_for_shipping_methods =  orddd_common::orddd_time_settings_enable_for_custom_delivery();

        if ( get_option( 'orddd_enable_delivery_time' ) == 'on' || $time_settings_for_shipping_methods == 'yes' ) {
            wp_register_style( 'jquery-ui-timepicker-addon-orddd', plugins_url( '/css/jquery-ui-timepicker-addon.css', __FILE__ ), '', $orddd_version, false );
            wp_enqueue_style( 'jquery-ui-timepicker-addon-orddd' );
        }

        if( 'yes' == $is_delivery_enabled && ( is_checkout() && !is_wc_endpoint_url( 'order-received' ) && !is_wc_endpoint_url( 'order-pay' ) || ( 'on' == $allow_customers_to_edit_date_enabled && ( is_wc_endpoint_url( 'view-order' ) || ( true === $is_view_subscription_page ) ) ) || ( is_cart() && 'on' == $is_enabled_on_cart_page ) ) ) {
    	   
            $roles = array();
            if ( has_filter( 'orddd_disable_delivery_for_user_roles' ) ) {
                $roles = apply_filters( 'orddd_disable_delivery_for_user_roles', $roles );
            }
            
            $current_user_role = '';
            if( isset( $current_user->roles[0] ) ) {
                $current_user_role = $current_user->roles[0];
            }

            if ( get_option( 'orddd_enable_delivery_date' ) == 'on' && ( orddd_process::woo_product_has_delivery() === 'on' ) && !in_array( $current_user_role, $roles ) ) {
                $delivery_calendar_for_shipping_methods = orddd_common::orddd_is_delivery_calendar_enabled_for_custom_delivery();
                if( 'delivery_calendar' == get_option( 'orddd_delivery_checkout_options' ) || 'yes' == $delivery_calendar_for_shipping_methods ) {
                    wp_enqueue_style( 'jquery-ui-style-orddd' );
                    wp_enqueue_style( 'orddd-datepicker' );
                }
            }
        }
    }

    /**
     * Dequeue files fron Frontend
     * 
     * @since 9.5
     */
    public static function orddd_admin_dequeue_scripts( $hook ) {
        if ( 'toplevel_page_order_delivery_date' == $hook || 
            'toplevel_page_orddd_send_reminder_page' == $hook || 
            'toplevel_page_edd_sample_license_page' == $hook ||
            'toplevel_page_orddd_view_orders' == $hook ) {

            $dequeue_scripts = apply_filters( 'orddd_dequeue_admin_scripts', array( 
                'wpmm_scripts_admin',
                'themeswitcher',
                'wc-enhanced-select'  // this has been done to make plugin compatible with WooCommerce Order Status & Actions Manager plugin
            ) );
            
            foreach( $dequeue_scripts as $script ) {
                wp_dequeue_script( $script );
            }

            $dequeue_styles = apply_filters( 'orddd_dequeue_admin_styles', array( 
                'wpmm-select2',
                'wpmm_fontawesome_css_admin',
                'wpmm_icofont_css_admin', 
                'wpmm_css_admin'
            ) );
            
            foreach( $dequeue_styles as $style ) {
                wp_dequeue_style( $style );
            }
        }
    }


    /**
     * Dequeue files fron Frontend
     * 
     * @since 9.5
     */
    public static function orddd_front_dequeue_scripts() {

        if ( is_checkout() ) {

            /* making below css change for storefront theme as it overwrites the datepicker header */
            $theme = wp_get_theme();
            if ( $theme->exists() && $theme->Name === 'Storefront' ) {
                ?>
                <style>table th { background-color:initial!important; }</style>
                <?php 
            }
        }
    }
	
    /**
     * Add Scheduled action to delete lockout data on a weekly basis.
     *
     * @since 9.16.0
     */
    public function orddd_schedule_weekly_lockout_cleanup_action() {
        // If action is not yet scheduled.
        if ( function_exists( 'as_next_scheduled_action' ) ) {
            if ( false === as_next_scheduled_action( 'orddd_delete_old_lockout_data_action' ) ) {

                // Remove the existing cron job.
                wp_clear_scheduled_hook( 'orddd_delete_old_lockout_data_action' );
                
                // Add recurring action.
                $week_in_seconds = apply_filters( 'orddd_lockout_cleanup_frequency', 86400 * 7 );
                as_schedule_recurring_action( time(), $week_in_seconds, 'orddd_delete_old_lockout_data_action' );
                
            }
        }
    }
}
$orddd_scripts = new orddd_scripts();

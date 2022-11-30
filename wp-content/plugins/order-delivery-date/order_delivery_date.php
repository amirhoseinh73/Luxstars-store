<?php
/**
 * Plugin Name: Order Delivery Date Pro for WooCommerce
 * Plugin URI: https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/
 * Description: This plugin allows customers to choose their preferred Order Delivery Date & Delivery Time during checkout.
 * Author: Tyche Softwares
 * Version: 9.27.0
 * Author URI: https://www.tychesoftwares.com/about
 * Contributor: Tyche Softwares, https://www.tychesoftwares.com/
 * Text Domain: order-delivery-date
 * Requires PHP: 5.6
 * WC requires at least: 3.0.0
 * WC tested up to: 5.5.2
 * Copyright: © 2009-2019 Tyche Softwares.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Current Order Delivery Date Pro version
 *
 * @since 1.0
 */

global $orddd_version;
$orddd_version = '9.27.0';

if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater if it doesn't already exist.
	include dirname( __FILE__ ) . '/plugin-updates/EDD_SL_Plugin_Updater.php';
}

require_once 'orddd-config.php';
require_once 'lang.php';
require_once 'orddd-widget.php';
require_once 'orddd-availability-widget.php';
require_once 'orddd-update.php';
require_once 'orddd-send-reminder.php';

/**
 * Retrieve our license key from the DB
 *
 * @since 1.0
 */
$license_key = trim( get_option( 'edd_sample_license_key_odd_woo' ) );

/**
 * Define Url for the license checker.
 *
 * @since 2.5
 */
define( 'EDD_SL_STORE_URL_ODD_WOO', 'http://www.tychesoftwares.com/' );

/**
 * Define Download name for the license checker.
 *
 * @since 2.5
 */
define( 'EDD_SL_ITEM_NAME_ODD_WOO', 'Order Delivery Date Pro for Woocommerce' );

/**
 * Setup the updater
 *
 * @since 2.5
 */
$edd_updater = new EDD_SL_Plugin_Updater(
	EDD_SL_STORE_URL_ODD_WOO,
	__FILE__,
	array(
		'version'   => '9.27.0',        // current version number.
		'license'   => $license_key,    // license key (used get_option above to retrieve from DB).
		'item_name' => EDD_SL_ITEM_NAME_ODD_WOO,    // name of this plugin.
		'author'    => 'Ashok Rane',  // author of this plugin.
	)
);

/**
* Schedule an action if it's not already scheduled for tracking data
*
* @since 6.8
*/

if ( ! wp_next_scheduled( 'ts_tracker_send_event' ) ) {
	wp_schedule_event( time(), 'daily_once', 'ts_tracker_send_event' );
}

if ( ! class_exists( 'order_delivery_date' ) ) {

	/**
	 * Main order_delivery_date Class
	 *
	 * @class order_delivery_date
	 */
	class order_delivery_date {

		/**
		 * Default Constructor.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			/**
			 * Including files
			 */

			add_action( 'init', array( &$this, 'orddd_include_files' ), 5 );
			add_action( 'admin_init', array( &$this, 'orddd_include_files' ) );

			// Installation.
			register_activation_hook( __FILE__, array( &$this, 'orddd_activate' ) );
			// Deactivation.
			register_deactivation_hook( __FILE__, array( &$this, 'orddd_deactivate' ) );

			// Cron to run script for deleting past date lockouts.
			add_filter( 'cron_schedules', array( &$this, 'orddd_add_cron_schedule' ) );
			add_action( 'orddd_delete_old_lockout_data_action', array( &$this, 'orddd_delete_old_lockout_data_cron' ) );

			// Capabilities to access Order Delivery Menu.
			add_action( 'admin_init', array( &$this, 'orddd_capabilities' ) );

			// Settings link and Documentation link added on the Plugins page.
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( &$this, 'orddd_plugin_settings_link' ) );
			add_filter( 'plugin_row_meta', array( &$this, 'orddd_plugin_row_meta' ), 10, 2 );

			// Language Translation.
			add_action( 'init', array( &$this, 'orddd_update_po_file' ) );

			// Ajax calls.
			add_action( 'init', array( &$this, 'orddd_load_ajax' ) );
			add_action( 'init', array( &$this, 'orddd_add_component_file' ) );

			// Check for Lite version.
			add_action( 'admin_init', array( &$this, 'orddd_check_if_lite_active' ) );

			add_action( 'admin_init', array( &$this, 'orddd_update_mdt_settings' ) );
			add_action( 'admin_notices', array( &$this, 'orddd_mdt_notice' ) );
			
			add_action( 'admin_init', array( &$this, 'orddd_migrate_orders_schedule_action' ) );

			// Add Action Scheduler Library.
			add_action( 'plugins_loaded', array( &$this, 'orddd_load_as_library' ) );
		}

		/**
		 *
		 *
		 * @since 8.7
		 */

		public function orddd_include_files() {
			self::orddd_check_if_woocommerce_active();
			global $orddd_date_formats;
			$orddd_date_formats = apply_filters( 'orddd_custom_date_formats', $orddd_date_formats );
			include_once 'integration.php';
			include_once 'orddd-process.php';
			include_once 'orddd-shipping-multiple-address.php';

			include_once 'orddd-common.php';
			include_once 'orddd-scripts.php';

			include_once 'orddd-calendar-sync.php';
			include_once 'class-orddd-email-manager.php';
			include_once 'orddd-locations.php';
			include_once 'orddd-cutoff-time-functions.php';
			include_once 'class-orddd-lockout-days.php';
			include_once 'includes/class-orddd-lockout-functions.php';
			include_once 'includes/class-custom-delivery-functions.php';
			include_once 'class-orddd-functions.php';
			include_once 'includes/class-orddd-rest-api.php';

			if ( class_exists( 'WC_Local_Pickup_Plus_Loader' ) ) {
				$pickup_location_settings = get_option( 'woocommerce_local_pickup_plus_settings', true );
				if ( isset( $pickup_location_settings['enabled'] ) &&
					'yes' == $pickup_location_settings['enabled'] &&
					isset( $pickup_location_settings['enable_per_item_selection'] ) &&
					'per-order' == $pickup_location_settings['enable_per_item_selection'] ) {
					include_once 'orddd-lpp-compatibility.php';
				}
			}

			if ( is_admin() ) {
				include_once 'filter.php';
				include_once 'orddd-settings.php';
				include_once 'class-orddd-license.php';
				include_once 'orddd-view-deliveries.php';
				include_once 'orddd-admin-delivery.php';
				include_once 'includes/adminend-events-jsons.php';
				include_once 'orddd-privacy.php';
				include_once 'includes/class-orddd-import-export.php';

				if ( is_plugin_active( 'wc-tree-table-rate-shipping/wc-tree-table-rate-shipping.php' ) ) {
					include_once 'includes/class-orddd-tree-table-rate.php';
				}
			}
		}
		/**
		 * Add Default settings to WordPress options table when plugin is installed.
		 *
		 * @hook register_activation_hook
		 * @globals resource $wpdb WordPress Object
		 * @globals array $orddd_weekdays Weekdays array
		 *
		 * @since 1.0
		 */
		public function orddd_activate() {
			if ( ! self::orddd_check_woo_installed() ) {
				return;
			}

			global $wpdb, $orddd_weekdays;

			// Check if installed for the first time.
			add_option( 'orddd_pro_installed', 'yes' );

			// Date Settings
			add_option( 'orddd_enable_delivery_date', '' );
			foreach ( $orddd_weekdays as $n => $day_name ) {
				add_option( $n, 'checked' );
			}
			add_option( 'orddd_minimumOrderDays', '0' );
			add_option( 'orddd_number_of_dates', '30' );
			add_option( 'orddd_date_field_mandatory', '' );
			add_option( 'orddd_lockout_date_after_orders', '' );
			add_option( 'orddd_lockout_days', '' );
			add_option( 'orddd_delivery_checkout_options', 'delivery_calendar' );

			// Specific delivery dates
			add_option( 'orddd_enable_specific_delivery_dates', '' );
			add_option( 'orddd_delivery_dates', '' );

			// Time options
			add_option( 'orddd_enable_delivery_time', '' );
			add_option( 'orddd_delivery_from_hours', '' );
			add_option( 'orddd_delivery_to_hours', '' );
			add_option( 'orddd_delivery_time_format', '2' );

			// Same day delivery options
			add_option( 'orddd_enable_same_day_delivery', '' );
			add_option( 'orddd_disable_same_day_delivery_after_hours', '' );
			add_option( 'orddd_disable_same_day_delivery_after_minutes', '' );

			// Next day delivery options
			add_option( 'orddd_enable_next_day_delivery', '' );
			add_option( 'orddd_disable_next_day_delivery_after_hours', '' );
			add_option( 'orddd_disable_next_day_delivery_after_minutes', '' );

			// Holidays
			add_option( 'orddd_delivery_date_holidays', '' );

			// Appearance options
			add_option( 'orddd_delivery_date_format', ORDDD_DELIVERY_DATE_FORMAT );
			add_option( 'orddd_delivery_date_field_label', ORDDD_DELIVERY_DATE_FIELD_LABEL );
			add_option( 'orddd_delivery_date_field_placeholder', ORDDD_DELIVERY_DATE_FIELD_PLACEHOLDER );
			add_option( 'orddd_delivery_date_field_note', ORDDD_DELIVERY_DATE_FIELD_NOTE );
			add_option( 'orddd_number_of_months', '1' );
			add_option( 'orddd_calendar_theme', ORDDD_CALENDAR_THEME );
			add_option( 'orddd_calendar_theme_name', ORDDD_CALENDAR_THEME_NAME );
			add_option( 'orddd_language_selected', 'en-GB' );
			add_option( 'orddd_delivery_date_fields_on_checkout_page', 'billing_section' );
			add_option( 'orddd_no_fields_for_virtual_product', 'on' );
			add_option( 'orddd_cut_off_time_color', '#ff0000' );
			add_option( 'orddd_booked_dates_color', '#ff0000' );
			add_option( 'orddd_holiday_color', '#ff0000' );
			add_option( 'orddd_available_dates_color', '#90EE90' );
			add_option( 'orddd_location_field_label', ORDDD_PICKUP_DATE_FIELD_LABEL );
			add_option( 'orddd_delivery_dates_in_dropdown', 'no' );

			// Time slot
			add_option( 'orddd_time_slot_mandatory', '' );
			add_option( 'orddd_delivery_timeslot_format', '2' );
			add_option( 'orddd_delivery_timeslot_field_label', ORDDD_DELIVERY_TIMESLOT_FIELD_LABEL );
			add_option( 'orddd_show_first_available_time_slot_as_selected', '' );
			add_option( 'orddd_time_slots_in_list_view', '' );
			add_option( 'orddd_auto_populate_first_pickup_location', 'on' );

			// Additional Settings
			add_option( 'orddd_show_filter_on_orders_page_check', 'on' );
			add_option( 'orddd_show_column_on_orders_page_check', 'on' );
			add_option( 'orddd_show_fields_in_csv_export_check', 'on' );
			add_option( 'orddd_show_fields_in_pdf_invoice_and_packing_slips', 'on' );
			add_option( 'orddd_show_fields_in_invoice_and_delivery_note', 'on' );
			add_option( 'orddd_show_fields_in_cloud_print_orders', 'on' );
			add_option( 'orddd_enable_default_sorting_of_column', 'on' );
			add_option( 'orddd_enable_tax_calculation_for_delivery_charges', '' );
			add_option( 'orddd_amazon_payments_advanced_gateway_compatibility', '' );
			add_option( 'orddd_enable_autofill_of_delivery_date', 'on' );
			add_option( 'orddd_enable_availability_display', 'on' );
			add_option( 'orddd_show_partially_booked_dates', 'on' );
			add_option( 'orddd_add_delivery_in_order_notes', 'on' );

			// Extra Options
			add_option( 'orddd_abp_hrs', 'HOURS' );
			add_option( 'update_weekdays_value', 'yes' );
			add_option( 'update_placeholder_value', 'no' );

			// Google Calendar Sync settings
			add_option( 'orddd_calendar_event_location', 'ADDRESS' );
			add_option( 'orddd_calendar_event_summary', 'SITE_NAME - ORDER_NUMBER' );
			add_option( 'orddd_calendar_event_description', 'CLIENT (EMAIL), <br> PRODUCT_WITH_QTY' );

			do_action( 'orddd_plugin_activate' );

			orddd_update::orddd_update_install();
			
			$timezone      = orddd_send_reminder::orddd_get_timezone_string();
			// Check if the reminder email actions need to be scheduled.
			if ( function_exists( 'as_next_scheduled_action' ) ) {
				if ( false === as_next_scheduled_action( 'orddd_auto_reminder_emails' ) && 0 < get_option( 'orddd_reminder_email_before_days', 0 ) ) {
					$reminder_time = apply_filters( 'orddd_modify_reminder_email_time', strtotime( '07:00:00 ' . $timezone ), $timezone );
					$reminder_emails_frequency = apply_filters( 'orddd_reminder_emails_frequency', 86400 );
					as_schedule_recurring_action( $reminder_time, $reminder_emails_frequency, 'orddd_auto_reminder_emails' );
				}
	
				if ( false === as_next_scheduled_action( 'orddd_auto_reminder_emails_admin' ) && 'on' === get_option( 'orddd_reminder_for_admin', '' ) ) {
					$reminder_time = apply_filters( 'orddd_modify_admin_reminder_email_time', strtotime( '19:00:00 ' . $timezone ), $timezone );
					$reminder_emails_admin_frequency = apply_filters( 'orddd_reminder_emails_admin_frequency', 86400 );
					as_schedule_recurring_action( $reminder_time, $reminder_emails_admin_frequency, 'orddd_auto_reminder_emails_admin' );
				}
			}
		}

		/**
		 * Function checks if the WooCommerce plugin is active or not. If it is not active then it will display a notice.
		 *
		 * @hook admin_init
		 *
		 * @since 5.3
		 */

		function orddd_check_if_woocommerce_active() {
			if ( ! self::orddd_check_woo_installed() ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
					deactivate_plugins( plugin_basename( __FILE__ ) );
					add_action( 'admin_notices', array( 'order_delivery_date', 'orddd_disabled_notice' ) );
					if ( isset( $_GET['activate'] ) ) {
						unset( $_GET['activate'] );
					}
				}
			}
		}

		/**
		 * Check if WooCommerce is active.
		 *
		 * @return bool True if WooCommerce is active, else false.
		 * @since 5.3
		 */
		public static function orddd_check_woo_installed() {
			if ( class_exists( 'WooCommerce' ) ) {
				return true;
			} else {
				return false;
			}
		}


		/**
		 * Run a cron once in week to delete old records for lockout
		 *
		 * @hook cron_schedules
		 *
		 * @param array $schedules Existing Cron Schedules
		 *
		 * @return array Array of schedules
		 * @since 4.0
		 */
		function orddd_add_cron_schedule( $schedules ) {
			$schedules['weekly'] = array(
				'interval' => 604800,  // one week in seconds
				'display'  => __( 'Once in a Week', 'order-delivery-date' ),
			);
			return $schedules;
		}

		/**
		 * Hook into that action that'll fire once a week
		 *
		 * @hook orddd_delete_old_lockout_data_action
		 * @since 4.0
		 */
		function orddd_delete_old_lockout_data_cron() {
			$plugin_dir_path = plugin_dir_path( __FILE__ );
			require_once $plugin_dir_path . 'orddd-run-script.php';
		}


		/**
		 * Display a notice in the admin Plugins page if the plugin is activated while WooCommerce is deactivated.
		 *
		 * @hook admin_notices
		 * @since 5.3
		 */
		public static function orddd_disabled_notice() {
			$class   = 'notice notice-error';
			$message = __( 'Order Delivery Date Pro for WooCommerce plugin requires WooCommerce installed and activate.', 'order-delivery-date' );
			printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
		}

		/**
		 * Settings link on Plugins page
		 *
		 * @hook plugin_action_links_order-delivery-date
		 *
		 * @param array $links
		 * @return array
		 * @since 1.0
		 */
		public function orddd_plugin_settings_link( $links ) {
			$setting_link['settings'] = '<a href="' . esc_url( get_admin_url( null, 'admin.php?page=order_delivery_date' ) ) . '">Settings</a>';
			$links                    = $setting_link + $links;
			return $links;
		}

		/**
		 * Documentation links on Plugins page
		 *
		 * @hook plugin_row_meta
		 *
		 * @param array  $links
		 * @param string $file
		 * @return array
		 *
		 * @since 1.0
		 */
		public function orddd_plugin_row_meta( $links, $file ) {
			if ( $file == plugin_basename( __FILE__ ) ) {
				unset( $links[2] );
				$row_meta = array(
					'plugin_site' => '<a href="' . esc_url( apply_filters( 'orddd_plugin_site_url', 'https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/' ) ) . '" title="' . esc_attr( __( 'Visit plugin site', 'order-delivery-date' ) ) . '">' . __( 'Visit plugin site', 'order-delivery-date' ) . '</a>',
					'docs'        => '<a href="' . esc_url( apply_filters( 'orddd_docs_url', 'https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/' ) ) . '" title="' . esc_attr( __( 'View Documentation', 'order-delivery-date' ) ) . '">' . __( 'Docs', 'order-delivery-date' ) . '</a>',
					'support'     => '<a href="' . esc_url( apply_filters( 'orddd_support_url', 'https://tychesoftwares.freshdesk.com/support/tickets/new' ) ) . '" title="' . esc_attr( __( 'Submit Ticket', 'order-delivery-date' ) ) . '">' . __( 'Submit Ticket', 'order-delivery-date' ) . '</a>',
				);
				return array_merge( $links, $row_meta );
			}
			return (array) $links;
		}

		/**
		 * Load Localization files.
		 *
		 * @hook init
		 *
		 * @return string $loaded Text domain
		 * @since 2.6.3
		 */
		public function orddd_update_po_file() {
			$domain = 'order-delivery-date';
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
			if ( $loaded = load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '-' . $locale . '.mo' ) ) {
				return $loaded;
			} else {
				load_plugin_textdomain( $domain, false, basename( dirname( __FILE__ ) ) . '/languages/' );
			}
		}

		/**
		 * Capability to allow shop manager to edit settings
		 *
		 * @hook admin_init
		 * @since 3.1
		 */
		public function orddd_capabilities() {
			$role = get_role( 'shop_manager' );
			if ( $role != '' ) {
				$role->add_cap( 'manage_options' );
			}
		}

		/**
		 * Used to load ajax functions required by plugin.
		 *
		 * @since 1.0
		 */
		public function orddd_load_ajax() {
			if ( ! is_user_logged_in() ) {
				add_action( 'wp_ajax_nopriv_orddd_order_calendar_content', array( 'orddd_class_view_deliveries', 'orddd_order_calendar_content' ) );
				add_action( 'wp_ajax_nopriv_orddd_update_delivery_date', array( 'orddd_process', 'orddd_update_delivery_date' ) );
				add_action( 'wp_ajax_nopriv_orddd_get_zone_id', array( 'orddd_common', 'orddd_get_zone_id' ) );
				add_action( 'wp_ajax_nopriv_orddd_save_reminder_message', array( 'orddd_send_reminder', 'orddd_save_reminder_message' ) );
				add_action( 'wp_ajax_nopriv_check_for_dates_orddd', array( 'ORDDD_Functions', 'check_for_dates_orddd' ) );
			} else {
				add_action( 'wp_ajax_orddd_display_timeslots_admin', array( 'orddd_process', 'orddd_display_timeslots_admin' ) );
				add_action( 'wp_ajax_orddd_order_calendar_content', array( 'orddd_class_view_deliveries', 'orddd_order_calendar_content' ) );
				add_action( 'wp_ajax_orddd_get_zone_id', array( 'orddd_common', 'orddd_get_zone_id' ) );
				add_action( 'wp_ajax_orddd_save_reminder_message', array( 'orddd_send_reminder', 'orddd_save_reminder_message' ) );

				add_action( 'wp_ajax_orddd_toggle_custom_setting_status', array( 'orddd_shipping_based_settings', 'orddd_toggle_custom_setting_status' ) );
				add_action( 'wp_ajax_orddd_get_time_slots_between_interval', array( 'orddd_shipping_based_settings', 'orddd_get_time_slots_between_interval' ) );
				add_action( 'wp_ajax_orddd_edit_time_slot', array( 'Orddd_Time_Slot_Settings', 'orddd_edit_time_slot' ) );
				add_action( 'wp_ajax_orddd_clone_custom_settings', array( 'Orddd_Shipping_Based_Settings', 'orddd_clone_custom_settings' ) );
				add_action( 'wp_ajax_check_for_dates_orddd', array( 'ORDDD_Functions', 'check_for_dates_orddd' ) );

				/**
				 * This function will import the delivery dates of the lite version.
				 *
				 * @since: 9.6
				 */
				add_action( 'wp_ajax_orddd_import_lite_data', array( 'orddd_import_lite_to_pro', 'orddd_import_lite_data' ) );
				add_action( 'wp_ajax_orddd_do_not_import_lite_data', array( 'orddd_import_lite_to_pro', 'orddd_do_not_import_lite_data' ) );
			}
			add_action( 'wc_ajax_orddd_update_delivery_session', array( 'orddd_process', 'orddd_update_delivery_session' ) );
			add_action( 'wc_ajax_orddd_update_delivery_date', array( 'orddd_process', 'orddd_update_delivery_date' ) );
		}

		/**
		 * It will load the boilerplate components file. In this file we have included all boilerplate files.
		 * We need to inlcude this file after the init hook.
		 *
		 * @hook init
		 */

		public static function orddd_add_component_file() {
			if ( true === is_admin() ) {
				include_once 'includes/ordd-all-component.php';
			}
		}

		/**
		 * Returns version number of the plugin
		 *
		 * @return string Plugin version number
		 * @since 1.0
		 */
		public static function get_orddd_version() {
			$plugin_data    = get_plugin_data( __FILE__ );
			$plugin_version = $plugin_data['Version'];
			return $plugin_version;
		}

		/**
		 * Function checks if the Order Delivery Date Lite version is active or not. If it is active then it will deactivate the lite version.
		 *
		 * @hook admin_init
		 *
		 * @since 8.7
		 */

		public static function orddd_check_if_lite_active() {
			$is_insatlled = self::orddd_check_lite_installed();
			if ( self::orddd_check_lite_installed() ) {
				if ( is_plugin_active( 'order-delivery-date-for-woocommerce/order_delivery_date.php' ) ) {
					deactivate_plugins( 'order-delivery-date-for-woocommerce/order_delivery_date.php' );
					if ( isset( $_GET['activate'] ) ) {
						unset( $_GET['activate'] );
					}
				}
			}
		}

		/**
		 * Check if Order Delivery Date lite is active.
		 *
		 * @return bool True if Lite version is active, else false.
		 * @since 8.7
		 */
		public static function orddd_check_lite_installed() {
			if ( class_exists( 'order_delivery_date_lite' ) ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Display admin notice for MDT & same day/next day.
		 */
		public static function orddd_mdt_notice() {
			if( isset( $_GET['page'] ) && 'order_delivery_date' === $_GET['page'] ) {
				$class   = 'notice notice-info is-dismissible';
				$message = __( 'The Minimum Delivery Time and Same day/Next day cut-off can now be configured together. Please refer <a href="https://www.tychesoftwares.com/how-does-minimum-delivery-time-work-with-same-day-next-day-cutoff-settings/" target="_blank">this post</a> to know more.', 'order-delivery-date' );
				printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
			}
		}

		/**
		 * Update Minimum delivery time in version 9.14.
		 */
		public static function orddd_update_mdt_settings() {
			$updated          = get_option( 'orddd_mdt_updated' );
			$general_settings = Orddd_Import_Export::orddd_get_delivery_settings();
			
			$data 			  = array();
			$custom_data 	  = array();
			foreach ( $general_settings as $dk => $dv ) {
				$data[ $dv ] = get_option( $dv );
			}

			$custom_data = array();
			$custom_data['orddd_enable_shipping_based_delivery']     = get_option( 'orddd_enable_shipping_based_delivery' );
			$custom_data['orddd_shipping_based_settings_option_key'] = get_option( 'orddd_shipping_based_settings_option_key' );

			$results = orddd_common::orddd_get_shipping_settings();
			if ( is_array( $results ) && count( $results ) > 0 ) {
				foreach ( $results as $key => $value ) {
					$shipping_settings = get_option( $value->option_name );
					$custom_data[ $value->option_name ] = $shipping_settings;
				}
			}

			if( !$updated || 'yes' != $updated ) {
				update_option('orddd_general_settings_9_14', json_encode( $data ) );
				update_option('orddd_custom_settings_9_14', json_encode( $custom_data ) );

				if( 'on' == get_option('orddd_enable_same_day_delivery') || 'on' == get_option('orddd_enable_next_day_delivery' ) ) {
					update_option( 'orddd_minimumOrderDays', '' );
					$advanced_settings = get_option('orddd_advance_settings');

					foreach( $advanced_settings as $key => $value ) {
						$advanced_settings[$key]["orddd_minimumOrderDays"] = '';
					}

					update_option( 'orddd_advance_settings', $advanced_settings );
				}

				

				if ( is_array( $results ) && count( $results ) > 0 ) {
					foreach ( $results as $key => $value ) {
						$shipping_settings = get_option( $value->option_name );
						$custom_data[ $value->option_name ] = $shipping_settings;
						if( ( isset( $shipping_settings['same_day'] ) && ( $shipping_settings['same_day']['after_hours'] > 0 ) ) || ( isset( $shipping_settings['next_day'] ) && ( $shipping_settings['next_day']['after_hours'] > 0 ) ) ) {
							$shipping_settings['minimum_delivery_time'] = '';
						}

						update_option( $value->option_name, $shipping_settings );
					}
				}

				update_option( 'orddd_mdt_updated', 'yes' );
			}
		}

		/**
		 * Migrate the new post meta for lockout increased/reduced in the already generated orders.
		 *
		 * @return void
		 * @since 9.19.0
		 */
		public static function orddd_migrate_orders_schedule_action() {
			$migrated_custom_schedule = get_option( 'orddd_migrate_post_meta_orddd_delivery_schedule_id' );
			$migrated_lockout_meta    = get_option( 'orddd_migrate_post_meta_orddd_lockout_reduced' );
			$migrated_gcal            = get_option( 'orddd_migrate_post_meta_orddd_gcal_event_id' );
			if ( function_exists( 'as_next_scheduled_action' ) ) {
				if ( false === as_next_scheduled_action( 'orddd_migrate_orders_post_meta' ) && ( ! $migrated_custom_schedule || 'yes' != $migrated_custom_schedule ) && ( 'yes' !== $migrated_lockout_meta ) ) {
					as_schedule_recurring_action( time(), 900, 'orddd_migrate_orders_post_meta' );
				}

				if ( false === as_next_scheduled_action( 'orddd_migrate_orders_gcal_post_meta' ) && ( ! $migrated_gcal || 'yes' != $migrated_gcal ) ) {
					as_schedule_recurring_action( time(), 900, 'orddd_migrate_orders_gcal_post_meta' );
				}

				if( 'yes' === $migrated_custom_schedule && 'yes' === $migrated_lockout_meta ) {
					as_unschedule_action( 'orddd_migrate_orders_post_meta' );
				}

				if( 'yes' === $migrated_gcal ) {
					as_unschedule_action( 'orddd_migrate_orders_gcal_post_meta' );
				}
			}
		}
		
		/**
		 * Load the Action Scheduler Library.
		 *
		 * @since 9.16.0
		 */
		public function orddd_load_as_library() {
			if ( self::orddd_check_woo_installed() ) {
				if ( version_compare( WOOCOMMERCE_VERSION, "4.0.0" ) < 0 ) {
					require_once( 'includes/libraries/action-scheduler/action-scheduler.php' );
				} else {
					require_once( WP_PLUGIN_DIR . '/woocommerce/packages/action-scheduler/action-scheduler.php' );
				}
			}
		}

		/**
		 * Run some actions on plugin deactivation.
		 *
		 * @since 9.16.0
		 */
		public function orddd_deactivate() {
			if ( function_exists( 'as_next_scheduled_action' ) ) {
				// Import Gcal events.
				if ( false !== as_next_scheduled_action( 'orddd_import_events' ) ) {
					as_unschedule_action( 'orddd_import_events' );
				}
				// Clean Gcal event records.
				if ( false !== as_next_scheduled_action( 'orddd_clean_events_db' ) ) {
					as_unschedule_action( 'orddd_clean_events_db' );
				}
				// Cleanup old Lockout data.
				if ( false !== as_next_scheduled_action( 'orddd_delete_old_lockout_data_action' ) ) {
					as_unschedule_action( 'orddd_delete_old_lockout_data_action' );
				}
				// Send reminder emails.
				if ( false !== as_next_scheduled_action( 'orddd_auto_reminder_emails' ) ) {
					as_unschedule_action( 'orddd_auto_reminder_emails' );
				}
				// Send reminder emails admin.
				if ( false !== as_next_scheduled_action( 'orddd_auto_reminder_emails_admin' ) ) {
					as_unschedule_action( 'orddd_auto_reminder_emails_admin' );
				}
			}
			
		}
	}
}
$order_delivery_date = new order_delivery_date();

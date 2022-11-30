<?php
/**
 * Order Delivery Date Plugin for WooCommerce
 *
 * Class for Import/Export of Delivery Settings in admin.
 *
 * @author   Tyche Softwares
 * @package  Order-Delivery-Date-Pro-for-WooCommerce/Import-Export
 * @category Classes
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Orddd_Import_Export' ) ) {

	/**
	 * Orddd_Import_Export class
	 *
	 * @class Orddd_Import_Export
	 */
	class Orddd_Import_Export {

		/**
		 * Settings labels array
		 *
		 * @var $general_settings_export
		 * @since 9.9
		 */
		public static $general_settings_export;

		/**
		 * Settings labels array
		 *
		 * @var $custom_settings_export
		 * @since 9.9
		 */
		public static $custom_settings_export;

		/**
		 * Default Construct
		 *
		 * @since 9.9
		 */
		public function __construct() {
			add_action( 'orddd_add_settings_tab', array( &$this, 'orddd_add_import_export_tab' ) );
			add_action( 'orddd_add_tab_content', array( &$this, 'orddd_add_import_export_content' ) );
			add_action( 'admin_init', array( &$this, 'orddd_import_export_action' ) );

			if ( isset( $_GET['page'] ) && 'order_delivery_date' === $_GET['page'] ) {
				$this::$general_settings_export = self::orddd_get_delivery_settings();
			}
		}

		/**
		 * Add Delivery Settings to $general_settings_export array
		 *
		 * @since 9.9
		 */
		public static function orddd_get_delivery_settings() {
			global $wpdb, $orddd_weekdays, $orddd_shipping_days;
			$settings = array(
				'orddd_enable_delivery_date',
				'orddd_delivery_checkout_options',
				'orddd_enable_day_wise_settings',
				'orddd_minimumOrderDays',
				'orddd_number_of_dates',
				'orddd_date_field_mandatory',
				'orddd_lockout_date_after_orders',
				'orddd_global_lockout_custom',
				'orddd_lockout_date_quantity_based',
				'orddd_enable_shipping_days',
				'orddd_min_between_days',
				'orddd_enable_specific_delivery_dates',
				'orddd_delivery_dates',
				'orddd_enable_delivery_time',
				'orddd_delivery_from_hours',
				'orddd_delivery_from_mins',
				'orddd_delivery_to_hours',
				'orddd_delivery_to_mins',
				'orddd_enable_same_day_delivery',
				'orddd_disable_same_day_delivery_after_hours',
				'orddd_disable_same_day_delivery_after_minutes',
				'orddd_same_day_additional_charges',
				'orddd_enable_next_day_delivery',
				'orddd_disable_next_day_delivery_after_hours',
				'orddd_disable_next_day_delivery_after_minutes',
				'orddd_next_day_additional_charges',
				'orddd_delivery_date_holidays',
				'orddd_language_selected',
				'orddd_delivery_date_format',
				'orddd_delivery_time_format',
				'start_of_week',
				'orddd_number_of_months',
				'orddd_calendar_theme_name',
				'orddd_location_field_label',
				'orddd_delivery_date_field_label',
				'orddd_delivery_timeslot_field_label',
				'orddd_delivery_date_field_placeholder',
				'orddd_delivery_date_field_note',
				'orddd_delivery_date_fields_on_checkout_page',
				'orddd_custom_hook_for_fields_placement',
				'orddd_delivery_date_on_cart_page',
				'orddd_holiday_color',
				'orddd_booked_dates_color',
				'orddd_cut_off_time_color',
				'orddd_available_dates_color',
				'orddd_enable_time_slot',
				'orddd_time_slot_mandatory',
				'orddd_time_slot_asap',
				'orddd_global_lockout_time_slots',
				'orddd_auto_populate_first_available_time_slot',
				'orddd_delivery_time_slot_log',
				'orddd_disable_time_slot_log',
				'orddd_show_column_on_orders_page_check',
				'orddd_enable_default_sorting_of_column',
				'orddd_show_filter_on_orders_page_check',
				'orddd_enable_autofill_of_delivery_date',
				'orddd_enable_tax_calculation_for_delivery_charges',
				'orddd_no_fields_for_virtual_product',
				'orddd_no_fields_for_featured_product',
				'orddd_allow_customers_to_edit_date',
				'orddd_send_email_to_admin_when_date_updated',
				'orddd_enable_availability_display',
				'orddd_show_partially_booked_dates',
				'orddd_show_fields_in_csv_export_check',
				'orddd_show_fields_in_pdf_invoice_and_packing_slips',
				'orddd_show_fields_in_invoice_and_delivery_note',
				'orddd_show_fields_in_cloud_print_orders',
				'orddd_shipping_multiple_address_compatibility',
				'orddd_amazon_payments_advanced_gateway_compatibility',
				'orddd_advance_settings',
				'additional_charges_orddd_weekday_0',
				'delivery_charges_label_orddd_weekday_0',
				'additional_charges_orddd_weekday_1',
				'delivery_charges_label_orddd_weekday_1',
				'additional_charges_orddd_weekday_2',
				'delivery_charges_label_orddd_weekday_2',
				'additional_charges_orddd_weekday_3',
				'delivery_charges_label_orddd_weekday_3',
				'additional_charges_orddd_weekday_4',
				'delivery_charges_label_orddd_weekday_4',
				'additional_charges_orddd_weekday_5',
				'delivery_charges_label_orddd_weekday_5',
				'additional_charges_orddd_weekday_6',
				'delivery_charges_label_orddd_weekday_6',
				'orddd_calendar_event_location',
				'orddd_calendar_event_summary',
				'orddd_calendar_event_description',
				'orddd_add_to_calendar_order_received_page',
				'orddd_add_to_calendar_customer_email',
				'orddd_add_to_calendar_my_account_page',
				'orddd_calendar_in_same_window',
				'orddd_calendar_sync_integration_mode',
				'orddd_calendar_details_1',
				'orddd_admin_add_to_calendar_delivery_calendar',
				'orddd_admin_add_to_calendar_email_notification',
				'orddd_locations',
				'orddd_pickup_location_mandatory',
				'orddd_reminder_email_before_days',
				'orddd_reminder_message',
				'orddd_reminder_subject',
				'orddd_real_time_import',
				'orddd_wp_cron_minutes',
				'orddd_business_opening_time',
				'orddd_business_closing_time',
			);

			foreach ( $orddd_weekdays as $wk => $wv ) {
				$settings[] = $wk;
			}

			foreach ( $orddd_shipping_days as $swk => $swv ) {
				$settings[] = $swk;
			}

			return $settings;
		}

		/**
		 * Add Export/Import tab on Order Delivery Date -> Settings menu.
		 *
		 * @since 9.9
		 */
		public function orddd_add_import_export_tab() {
			$action = 'general_settings';

			if ( isset( $_GET['action'] ) ) {
				$action = sanitize_text_field( $_GET['action'] );
			}

			$active_import_export_settings = '';
			if ( 'orddd_import_export_settings' === $action ) {
				$active_import_export_settings = 'nav-tab-active';
			}

			?>
			<a href="admin.php?page=order_delivery_date&action=orddd_import_export_settings" class="nav-tab <?php echo esc_attr( $active_import_export_settings ); ?>"> <?php esc_attr_e( 'Export/Import', 'order-delivery-date' ); ?>
			</a>
			<?php
		}

		/**
		 * Add content to Export/Import tab on Order Delivery Date -> Settings menu.
		 *
		 * @since 9.9
		 */
		public static function orddd_add_import_export_content() {
			$action = 'general_settings';
			if ( isset( $_GET['action'] ) ) {
				$action = sanitize_text_field( $_GET['action'] );
			}

			if ( 'orddd_import_export_settings' === $action ) {
				include_once 'views/html-import-export-settings.php';
			}
		}

		/**
		 * Call export or import functions based on the button click request
		 *
		 * @since 9.9
		 */
		public function orddd_import_export_action() {
			if ( isset( $_GET['action1'] ) &&
			( 'export_delivery_settings' === $_GET['action1'] ||
				'export_custom_delivery_settings' === $_GET['action1'] )
			) {
				self::orddd_export( $_GET['action1'] );
			}

			if ( ! isset( $_GET['action1'] ) &&
				isset( $_FILES['orddd-import-file'] ) ) {
				self::orddd_import();
			}
		}

		/**
		 * Export the delivery settings based on the action
		 *
		 * @param string $action Action performed.
		 */
		public function orddd_export( $action ) {
			$charset = get_option( 'blog_charset' );
			$data    = array();

			if ( 'export_delivery_settings' === $action ) {
				$delivery_settings = self::$general_settings_export;
				foreach ( $delivery_settings as $dk => $dv ) {
					$data[ $dv ] = get_option( $dv );
				}
			} elseif ( 'export_custom_delivery_settings' === $action ) {
				$data['orddd_enable_shipping_based_delivery']     = get_option( 'orddd_enable_shipping_based_delivery' );
				$data['orddd_shipping_based_settings_option_key'] = get_option( 'orddd_shipping_based_settings_option_key' );

				$results = orddd_common::orddd_get_shipping_settings( 0 );
				if ( is_array( $results ) && count( $results ) > 0 ) {
					foreach ( $results as $key => $value ) {
						$shipping_settings                               = get_option( $value->option_name );
						$shipping_settings['delivery_settings_based_on'] = array();

						if ( isset( $shipping_settings['shipping_methods'] ) ) {
							$shipping_settings['shipping_methods'] = array();
						}

						if ( isset( $shipping_settings['product_categories'] ) ) {
							$shipping_settings['product_categories'] = array();
						}

						if ( isset( $shipping_settings['shipping_methods_for_categories'] ) ) {
							$shipping_settings['shipping_methods_for_categories'] = array();
						}

						$data[ $value->option_name ] = $shipping_settings;
					}
				}
			}

			// Set the download headers.
			header( 'Content-disposition: attachment; filename=orddd-export-delivery-settings.json' );
			header( 'Content-Type: text/plain; charset=' . $charset );

			// Serialize the export data.
			echo serialize( $data );

			// Start the download.
			die();
		}

		/**
		 * Import the delivery settings based on the action
		 */
		public static function orddd_import() {
			// Make sure WordPress upload support is loaded.
			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			// Setup internal vars.
			$overrides = array(
				'test_form' => false,
				'test_type' => false,
				'mimes'     => array( 'json' => 'text/plain' ),
			);

			$file = array();
			if ( isset( $_FILES['orddd-import-file'] ) ) {
				$file = wp_handle_upload( $_FILES['orddd-import-file'], $overrides );
			}

			if( strpos( $file['url'], 'zip' ) !== false ) {
				add_action( 'admin_notices', array( 'Orddd_Import_Export', 'orddd_import_failed' ) );
				return;
			}

			// Get the upload data.
			if ( isset( $file['url'] ) ) {
				$raw  = wp_remote_get( $file['url'] );
				$data = unserialize( wp_remote_retrieve_body( $raw ) );

				// Remove the uploaded file.
				unlink( $file['file'] );

				// Import delivery options.
				foreach ( $data as $option_key => $option_value ) {
					update_option( $option_key, $option_value );
				}
				add_action( 'admin_notices', array( 'Orddd_Import_Export', 'orddd_import_success' ) );
			} else {
				add_action( 'admin_notices', array( 'Orddd_Import_Export', 'orddd_import_failed' ) );
			}
		}

		/**
		 * Show the success message on import
		 */
		public static function orddd_import_success() {
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Delivery Settings Imported Successfully.', 'order-delivery-date' ); ?></p>
			</div>
			<?php
		}

		/**
		 * Show the failed message on import
		 */
		public static function orddd_import_failed() {
			?>
			<div class="notice notice-error is-dismissible">
				<p><?php esc_html_e( 'Error importing settings! Please check that you uploaded a export file.', 'order-delivery-date' ); ?></p>
			</div>
			<?php
		}
	} // end of class
	$orddd_import_export = new Orddd_Import_Export();
}

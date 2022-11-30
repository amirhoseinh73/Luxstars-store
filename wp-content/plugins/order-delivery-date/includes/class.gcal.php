<?php
/**
 * Order Delivery Date Pro for WooCommerce
 *
 * Sync Delivery Dte & Time to Google Calendar
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Pro-for-WooCommerce/Google-Calendar
 * @since       4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'OrdddGcal' ) ) {
	/**
	 * Sync Delivery Dte & Time to Google Calendar.
	 *
	 * @class OrdddGcal
	 */
	class OrdddGcal {

		/**
		 * Default constructor
		 *
		 * @globals resource $wpdb
		 * @since 4.0
		 */
		function __construct() {
			global $wpdb;

			$gmt = false;
			if ( has_filter( 'orddd_gmt_calculations' ) ) {
				$gmt = apply_filters( 'orddd_gmt_calculations', '' );
			}

			$current_time = current_time( 'timestamp', $gmt );

			$this->plugin_dir = plugin_dir_path( __FILE__ );
			$this->plugin_url = plugins_url( basename( dirname( __FILE__ ) ) );
			$this->local_time = $current_time;

			if ( ! $this->start_of_week = get_option( 'start_of_week' ) ) {
				$this->start_of_week = 0;
			}

			$this->time_format = get_option( 'time_format' );
			if ( empty( $this->time_format ) ) {
				$this->time_format = 'H:i';
			}

			$this->date_format = get_option( 'date_format' );
			if ( empty( $this->date_format ) ) {
				$this->date_format = 'Y-m-d';
			}

			$this->datetime_format = $this->date_format . ' ' . $this->time_format;

			require_once $this->plugin_dir . '/external/google/Client.php';

			add_action( 'init', array( &$this, 'orddd_init' ), 12 );

			// Prevent exceptions to kill the page.
			if ( ( isset( $_POST['gcal_api_test'] ) && 1 == $_POST['gcal_api_test'] )
				|| ( isset( $_POST['gcal_import_now'] ) && $_POST['gcal_import_now'] ) ) {
				set_exception_handler( array( &$this, 'exception_error_handler' ) );
			}

			// Set log file location.
			$uploads = wp_upload_dir();
			if ( isset( $uploads['basedir'] ) ) {
				$this->uploads_dir = $uploads['basedir'] . '/';
			} else {
				$this->uploads_dir = WP_CONTENT_DIR . '/uploads/';
			}

			$this->log_file = $this->uploads_dir . 'orddd-log.txt';

			add_action( 'wp_ajax_display_nag', array( &$this, 'display_nag' ) );
		}

		/**
		 * Refresh the page with the exception as GET parameter, so that page is not killed
		 *
		 * @since 4.0
		 */
		function exception_error_handler( $exception ) {
			// If we don't remove these GETs there will be an infinite loop.
			if ( ! headers_sent() ) {
				wp_redirect(
					esc_url(
						add_query_arg(
							array(
								'gcal_api_test_result' => urlencode( $exception ),
								'gcal_import_now'      => false,
								'gcal_api_test'        => false,
								'gcal_api_pre_test'    => false,
							)
						)
					)
				);
			} else {
				$this->log( $exception );
			}
		}

		/**
		 * Displays nag
		 *
		 * @hook wp_ajax_display_nag
		 * @since 4.0
		 */
		function display_nag() {
			$error      = false;
			$message    = '';
			$upload_dir = wp_upload_dir();
			if ( isset( $_POST['gcal_api_test'] ) && 1 == $_POST['gcal_api_test'] ) {
				if ( $result = $this->is_not_suitable() ) {
					$message .= $result;
				} else {
					// Insert a test event.
					$result = $this->insert_event( array(), 0, true );
					if ( $result ) {
						$message .= __( '<b>Test is successful</b>. Please REFRESH your Google Calendar and check that test appointment has been saved.', 'order-delivery-date' );
					} else {
						$message .= __( '<b>Test failed</b>. Please inspect your log file for more info here: ' . $upload_dir['baseurl'] . '/orddd-log.txt', 'order-delivery-date' );
					}
				}
			}

			if ( isset( $_POST['gcal_api_test_result'] ) && '' != $_POST['gcal_api_test_result'] ) {
				$m = stripslashes( urldecode( $_POST['gcal_api_test_result'] ) );
				// Get rid of unnecessary information.
				if ( strpos( $m, 'Stack trace' ) !== false ) {
					$temp = explode( 'Stack trace', $m );
					$m    = $temp[0];
				}
				if ( strpos( $this->get_selected_calendar(), 'group.calendar.google.com' ) === false ) {
					$add = '<br />' . __( 'Do NOT use your primary Google calendar, but create a new one.', 'order-delivery-date' );
				} else {
					$add = '';
				}
				$message = __( 'The following error has been reported by Google Calendar API:<br />', 'order-delivery-date' ) . $m . '<br />' .
						   __( '<b>Recommendation:</b> Please double check your settings.' . $add, 'order-delivery-date' );
			}

			echo $message;
			die();
		}

		/**
		 * Copy the key file to the uploads folder.
		 *
		 * @hook init
		 * @since 4.0
		 */
		function orddd_init() {
			if ( 'disabled' != $this->get_api_mode() && '' != $this->get_api_mode() ) {
				// Try to create key file folder if it doesn't exist.
				$this->create_key_file_folder();
				$kff = $this->key_file_folder();

				// Copy index.php to this folder and to uploads folder.
				if ( is_dir( $kff ) && ! file_exists( $kff . 'index.php' ) ) {
					@copy( $this->plugin_dir . 'gcal/key/index.php', $kff . 'index.php' );
				}
				if ( is_dir( $this->uploads_dir ) && ! file_exists( $this->uploads_dir . 'index.php' ) ) {
					@copy( $this->plugin_dir . 'gcal/key/index.php', $this->uploads_dir . 'index.php' );
				}

				// Copy key file to uploads folder.
				$kfn = $this->get_key_file() . '.p12';
				if ( $kfn && is_dir( $kff ) && ! file_exists( $kff . $kfn ) && file_exists( $this->plugin_dir . 'gcal/key/' . $kfn ) ) {
					@copy( $this->plugin_dir . 'gcal/key/' . $kfn, $kff . $kfn );
				}
			}
		}


		/**
		 * Return GCal API mode (Manually, Directly or Disabled )
		 *
		 * @return string Setting value
		 * @since 4.0
		 */
		function get_api_mode() {
			return get_option( 'orddd_calendar_sync_integration_mode' );
		}

		/**
		 * Return GCal service account
		 *
		 * @return string
		 * @since 4.0
		 */
		function get_service_account() {
			$gcal_service_account_arr = get_option( 'orddd_calendar_details_1' );
			if ( isset( $gcal_service_account_arr['orddd_calendar_service_acc_email_address'] ) ) {
				$gcal_service_account = $gcal_service_account_arr['orddd_calendar_service_acc_email_address'];
			} else {
				$gcal_service_account = '';
			}
			return $gcal_service_account;
		}

		/**
		 * Return GCal key file name without the extension
		 *
		 * @return string Key file name
		 * @since 4.0
		 */
		function get_key_file() {
			$gcal_key_file_arr = get_option( 'orddd_calendar_details_1' );
			if ( isset( $gcal_key_file_arr['orddd_calendar_key_file_name'] ) ) {
				$gcal_key_file = $gcal_key_file_arr['orddd_calendar_key_file_name'];
			} else {
				$gcal_key_file = '';
			}

			return $gcal_key_file;
		}

		/**
		 * Return GCal selected calendar ID
		 *
		 * @return string Calendar ID
		 * @since 4.0
		 */
		function get_selected_calendar() {
			$gcal_selected_calendar_arr = get_option( 'orddd_calendar_details_1' );
			if ( isset( $gcal_selected_calendar_arr['orddd_calendar_id'] ) ) {
				$gcal_selected_calendar = $gcal_selected_calendar_arr['orddd_calendar_id'];
			} else {
				$gcal_selected_calendar = '';
			}
			return $gcal_selected_calendar;
		}

		/**
		 * Return GCal Summary (name of Event)
		 *
		 * @return string
		 * @since 4.0
		 */
		function get_summary() {
			return get_option( 'orddd_calendar_event_summary' );
		}

		/**
		 * Return GCal description
		 *
		 * @return string
		 * @since 4.0
		 */
		function get_description() {
			return get_option( 'orddd_calendar_event_description' );
		}

		/**
		 * Checks if php version and extentions are correct
		 *
		 * @return string Error is not Suitable. Empty string means suitable.
		 * @since 4.0
		 */
		function is_not_suitable() {
			if ( version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
				return __( 'Google PHP API Client <b>requires at least PHP 5.3</b>', 'order-delivery-date' );
			}

			// Disabled for now.
			if ( false && memory_get_usage() < 31000000 ) {
				return sprintf( __( 'Google PHP API Client <b>requires at least 32 MByte Server RAM</b>. Please check this link how to increase it: %s', 'order-delivery-date' ), '<a href="http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">' . __( 'Increasing_memory_allocated_to_PHP', 'order-delivery-date' ) . '</a>' );
			}

			if ( ! function_exists( 'curl_init' ) ) {
				return __( 'Google PHP API Client <b>requires the CURL PHP extension</b>', 'order-delivery-date' );
			}

			if ( ! function_exists( 'json_decode' ) ) {
				return __( 'Google PHP API Client <b>requires the JSON PHP extension</b>', 'order-delivery-date' );
			}

			if ( ! function_exists( 'http_build_query' ) ) {
				return __( 'Google PHP API Client <b>requires http_build_query()</b>', 'order-delivery-date' );
			}

			// Dont continue further if this is pre check.
			if ( isset( $_POST['gcal_api_pre_test'] ) && 1 == $_POST['gcal_api_pre_test'] ) {
				return __( 'Your server installation meets requirements.', 'order-delivery-date' );
			}

			if ( ! $this->_file_exists() ) {
				return __( '<b>Key file does not exist</b>', 'order-delivery-date' );
			}

			return '';
		}

		/**
		 * Checks if key file exists
		 *
		 * @return bool
		 * @since 4.0
		 */
		function _file_exists() {
			if ( file_exists( $this->key_file_folder() . $this->get_key_file() . '.p12' ) ) {
				return true;
			} elseif ( file_exists( $this->plugin_dir . 'gcal/key/' . $this->get_key_file() . '.p12' ) ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Get contents of the key file
		 *
		 * @return string
		 * @since 4.0
		 */
		function _file_get_contents() {
			if ( file_exists( $this->key_file_folder() . $this->get_key_file() . '.p12' ) ) {
				return @file_get_contents( $this->key_file_folder() . $this->get_key_file() . '.p12' );
			} elseif ( file_exists( $this->plugin_dir . 'gcal/key/' . $this->get_key_file() . '.p12' ) ) {
				return @file_get_contents( $this->plugin_dir . 'gcal/key/' . $this->get_key_file() . '.p12' );
			} else {
				return '';
			}
		}

		/**
		 * Try to create an encrypted key file folder
		 *
		 * @return string
		 * @since 4.0
		 */

		function create_key_file_folder() {
			if ( ! is_dir( $this->uploads_dir . 'orddd_uploads/' ) ) {
				@mkdir( $this->uploads_dir . 'orddd_uploads/' );
			}
		}

		/**
		 * Return key file folder name
		 *
		 * @return string
		 * @since 4.0
		 */
		function key_file_folder() {
			return $this->uploads_dir . 'orddd_uploads/';
		}

		/**
		 * Checks for settings and prerequisites
		 *
		 * @return bool
		 * @since 4.0
		 */
		function is_active() {
			// If integration is disabled, nothing to do.
			if ( 'disabled' == $this->get_api_mode() || '' == $this->get_api_mode() || ! $this->get_api_mode() ) {
				return false;
			}
			if ( $this->is_not_suitable() ) {
				return false;
			}

			if ( $this->get_key_file() && $this->get_service_account() && $this->get_selected_calendar() ) {
				return true;
			}
			// None of the other cases are allowed.
			return false;
		}

		/**
		 * Connects to GCal API
		 *
		 * @return bool Return true if connected successfully, else false.
		 * @since 4.0
		 */
		function connect() {
			// Disallow faultly plugins to ruin what we are trying to do here.
			@ob_start();

			if ( ! $this->is_active() ) {
				return false;
			}
			// Just in case.
			require_once $this->plugin_dir . '/external/google/Client.php';

			$config = new Orddd_Google_OrdddGoogleConfig(
				apply_filters(
					'orddd-gcal-client_parameters',
					array(
					// 'cache_class' => 'Google_Cache_Null', // For an example.
					)
				)
			);

			$this->client = new Orddd_Google_Client( $config );
			$this->client->setApplicationName( 'Order Delivery Date' );

			$key = $this->_file_get_contents();
			$this->client->setAssertionCredentials(
				new Orddd_Google_Auth_AssertionCredentials(
					$this->get_service_account(),
					array( 'https://www.googleapis.com/auth/calendar' ),
					$key
				)
			);

			$this->service = new Orddd_Google_Service_Calendar( $this->client );

			return true;
		}

		/**
		 * Creates a Google Event object and set its parameters
		 *
		 * @param array $app Data to export
		 * @since 4.0
		 */
		function set_event_parameters( $app ) {
			$gmt = false;
			if ( has_filter( 'orddd_gmt_calculations' ) ) {
				$gmt = apply_filters( 'orddd_gmt_calculations', '' );
			}

			$current_time = current_time( 'timestamp', $gmt );

			$email           = '';
			$order_date_time = '';

			if ( isset( $app->order_date_time ) ) {
				$order_date_time = date( 'M d, Y H:i:s', strtotime( $app->order_date_time ) );
			}

			$site_name             = get_bloginfo( 'name' );
			$full_address          = ( isset( $app->client_full_address ) ) ? $app->client_full_address : '';
			$client_name           = ( isset( $app->client_name ) ) ? $app->client_name : '';
			$start_date            = ( isset( $app->start ) ) ? $app->start : '';
			$end_date              = ( isset( $app->end ) ) ? $app->end : '';
			$start_time            = ( isset( $app->start_time ) ) ? $app->start_time : '';
			$end_time              = ( isset( $app->end_time ) ) ? $app->end_time : '';
			$client_address        = ( isset( $app->client_address ) ) ? $app->client_address : '';
			$products              = ( isset( $app->products ) ) ? $app->products : '';
			$product_with_qty      = ( isset( $app->product_with_qty ) ) ? $app->product_with_qty : '';
			$product_with_cat      = ( isset( $app->product_with_cat ) ) ? $app->product_with_cat : '';
			$client_city           = ( isset( $app->client_city ) ) ? $app->client_city : '';
			$order_date            = ( isset( $app->order_date ) ) ? $app->order_date : '';
			$id                    = ( isset( $app->id ) ) ? $app->id : '';
			$order_total           = ( isset( $app->order_total ) ) ? $app->order_total : '';
			$client_phone          = ( isset( $app->client_phone ) ) ? $app->client_phone : '';
			$order_note            = ( isset( $app->order_note ) ) ? $app->order_note : '';
			$client_full_address   = ( isset( $app->client_full_address ) ) ? $app->client_full_address : '';
			$client_address        = ( isset( $app->client_address ) ) ? $app->client_address : '';
			$client_email          = ( isset( $app->client_email ) ) ? $app->client_email : '';
			$shipping_method_title = ( isset( $app->shipping_method_title ) ) ? $app->shipping_method_title : '';
			$payment_method_title  = ( isset( $app->payment_method_title ) ) ? $app->payment_method_title : '';
			$pickup_location       = ( isset( $app->pickup_location ) ) ? $app->pickup_location : '';
			$order_weblink         = ( isset( $app->order_weblink ) ) ? $app->order_weblink : '';
			$order_status          = ( isset( $app->order_status ) ) ? $app->order_status : '';

			$shipping_first_name   = ( isset( $app->shipping_first_name ) ) ? $app->shipping_first_name : '';
			$shipping_last_name    = ( isset( $app->shipping_last_name ) ) ? $app->shipping_last_name : '';
			$shipping_company      = ( isset( $app->shipping_company ) ) ? $app->shipping_company : '';
			$shipping_address_1    = ( isset( $app->shipping_address_1 ) ) ? $app->shipping_address_1 : '';
			$shipping_address_2    = ( isset( $app->shipping_address_2 ) ) ? $app->shipping_address_2 : '';
			$shipping_city         = ( isset( $app->shipping_city ) ) ? $app->shipping_city : '';
			$shipping_state        = ( isset( $app->shipping_state ) ) ? $app->shipping_state : '';
			$shipping_postcode     = ( isset( $app->shipping_postcode ) ) ? $app->shipping_postcode : '';
			$shipping_phone        = ( isset( $app->shipping_phone ) ) ? $app->shipping_phone : '';
			$shipping_name         = ( isset( $app->shipping_name ) ) ? $app->shipping_name : '';
			$shipping_address      = ( isset( $app->shipping_address ) ) ? $app->shipping_address : '';
			$shipping_full_address = ( isset( $app->shipping_full_address ) ) ? $app->shipping_full_address : '';

			if ( get_option( 'orddd_calendar_event_location' ) != '' ) {
				$location = str_replace(
					array(
						'FULL_ADDRESS_SHIP',
						'ADDRESS_SHIP',
						'FULL_ADDRESS',
						'ADDRESS',
						'CITY',
						'ORDER_DATE_TIME',
						'PICKUP_LOCATION',
					),
					array(
						$shipping_full_address,
						$shipping_address,
						$full_address,
						$client_address,
						$client_city,
						$order_date_time,
						$pickup_location,
					),
					get_option( 'orddd_calendar_event_location' )
				);
			} else {
				$location = get_bloginfo( 'description' );
			}

			$summary = str_replace(
				array(
					'CLIENT_SHIP',
					'PHONE_SHIP',
					'FULL_ADDRESS_SHIP',
					'ADDRESS_SHIP',
					'SITE_NAME',
					'CLIENT',
					'PRODUCTS',
					'PRODUCT_WITH_QTY',
					'PRODUCT_WITH_CATEGORY',
					'ORDER_DATE_TIME',
					'ORDER_DATE',
					'ORDER_NUMBER',
					'PRICE',
					'PHONE',
					'NOTE',
					'FULL_ADDRESS',
					'ADDRESS',
					'EMAIL',
					'SHIPPING_METHOD_TITLE',
					'PAYMENT_METHOD_TITLE',
					'PICKUP_LOCATION',
					'ORDER_WEBLINK',
					'ORDER_STATUS',
				),
				array(
					$shipping_name,
					$shipping_phone,
					$shipping_full_address,
					$shipping_address,
					$site_name,
					$client_name,
					$products,
					$product_with_qty,
					$product_with_cat,
					$order_date_time,
					$order_date,
					$id,
					$order_total,
					$client_phone,
					$order_note,
					$client_full_address,
					$client_address,
					$client_email,
					$shipping_method_title,
					$payment_method_title,
					$pickup_location,
					$order_weblink,
					$order_status,
				),
				$this->get_summary()
			);

			$description = str_replace(
				array(
					'CLIENT_SHIP',
					'PHONE_SHIP',
					'FULL_ADDRESS_SHIP',
					'ADDRESS_SHIP',
					'SITE_NAME',
					'CLIENT',
					'PRODUCTS',
					'PRODUCT_WITH_QTY',
					'PRODUCT_WITH_CATEGORY',
					'ORDER_DATE_TIME',
					'ORDER_DATE',
					'ORDER_NUMBER',
					'PRICE',
					'PHONE',
					'NOTE',
					'FULL_ADDRESS',
					'ADDRESS',
					'EMAIL',
					'SHIPPING_METHOD_TITLE',
					'PAYMENT_METHOD_TITLE',
					'PICKUP_LOCATION',
					'ORDER_WEBLINK',
					'ORDER_STATUS',
				),
				array(
					$shipping_name,
					$shipping_phone,
					$shipping_full_address,
					$shipping_address,
					$site_name,
					$client_name,
					$products,
					$product_with_qty,
					$product_with_cat,
					$order_date_time,
					$order_date,
					$id,
					$order_total,
					$client_phone,
					$order_note,
					$client_full_address,
					$client_address,
					$client_email,
					$shipping_method_title,
					$payment_method_title,
					$pickup_location,
					$order_weblink,
					$order_status,
				),
				$this->get_description()
			);

			// replace fields from other tyche plugins.
			$deposit_payment = ( isset( $app->deposit_payment ) ) ? get_woocommerce_currency_symbol() . $app->deposit_payment : '';
			$future_payments = ( isset( $app->future_payments ) ) ? get_woocommerce_currency_symbol() . $app->future_payments : '';

			$description = str_replace(
				array( 'DEPOSIT_PAYMENT', 'FUTURE_PAYMENTS' ),
				array( $deposit_payment, $future_payments ),
				$description
			);

			// Find time difference from Greenwich as GCal asks UTC.
			if ( ! $current_time ) {
				$tdif = 0;
			} else {
				$tdif = $current_time - time();
			}

			if ( $start_time == '' && $end_time == '' ) {
				$start = new Orddd_Google_Service_Calendar_EventDateTime();
				$start->setDate( date( 'Y-m-d', strtotime( $start_date ) ) );

				$end = new Orddd_Google_Service_Calendar_EventDateTime();
				$end->setDate( date( 'Y-m-d', strtotime( $end_date . '+1 day' ) ) );
			} elseif ( $end_time == '' ) {
				$start = new Orddd_Google_Service_Calendar_EventDateTime();
				$start->setDateTime( date( 'Y-m-d\TH:i:s\Z', strtotime( $start_date . ' ' . $start_time ) - $tdif ) );

				$end = new Orddd_Google_Service_Calendar_EventDateTime();
				$end->setDateTime( date( 'Y-m-d\TH:i:s\Z', strtotime( '+30 minutes', strtotime( $end_date . ' ' . $start_time ) ) - $tdif ) );
			} else {
				$start = new Orddd_Google_Service_Calendar_EventDateTime();
				$start->setDateTime( date( 'Y-m-d\TH:i:s\Z', strtotime( $start_date . ' ' . $start_time ) - $tdif ) );

				$end = new Orddd_Google_Service_Calendar_EventDateTime();
				$end->setDateTime( date( 'Y-m-d\TH:i:s\Z', strtotime( $end_date . ' ' . $end_time ) - $tdif ) );
			}

			if ( isset( $app->client_email ) ) {
				$email = $app->client_email;
			}

			$attendee1 = new Orddd_Google_Service_Calendar_EventAttendee();
			$attendee1->setEmail( $email );
			$attendees = array( $attendee1 );

			$this->event = new Orddd_Google_Service_Calendar_Event();
			$this->event->setLocation( $location );
			$this->event->setStart( $start );
			$this->event->setEnd( $end );
			$this->event->setSummary(
				apply_filters(
					'orddd-gcal-set_summary',
					$summary
				)
			);
			$this->event->setDescription(
				apply_filters(
					'orddd-gcal-set_description',
					$description
				)
			);
		}

		/**
		 * Delete event from Gcal when an order is cancelled.
		 *
		 * @param int $order_id Order ID
		 * @since 4.0
		 */
		function delete_event( $order_id ) {
			if ( ! $this->connect() ) {
				return false;
			}
			// calendar ID.
			$calendar_id = $this->get_selected_calendar();

			// get the event UID.
			$event_uid = '';
			$event_id  = '';

			$event_uids = get_option( 'orddd_event_uids_ids' );
			if ( is_array( $event_uids ) && count( $event_uids ) > 0 ) {
				if ( isset( $event_uids[ $order_id ] ) ) {
					$event_id  = $event_uids[ $order_id ];
					$event_uid = str_replace( '@google.com', '', $event_id );
				}
			}
			if ( $event_uid != '' && $calendar_id != '' ) {
				try {
					$deletedEvent = $this->service->events->delete( $calendar_id, $event_uid );
					// 9.19.0 TEST: Comment below part if you want to test migration script & prevent google calendar post meta from being deleted for each order.
					delete_post_meta( $order_id, '_orddd_gcal_event_id', $event_id );
				} catch ( Exception $e ) {
				}
			}
		}

		/**
		 * Inserts an event to the selected calendar with delivery date & time
		 *
		 * @globals resource $wpdb
		 *
		 * @param array $event_details Event details
		 * @param int   $event_id Even ID
		 * @param bool  $test Check if it is a test event or not.
		 *
		 * @return bool True if event inserted successfully, else false.
		 * @since 4.0
		 */
		function insert_event( $event_details, $event_id, $test = false ) {
			if ( ! $this->connect() ) {
				return false;
			}
			global $wpdb;
			$user = get_user_by( 'email', get_option( 'admin_email' ) );
			if ( isset( $user->ID ) ) {
				$address_1    = get_user_meta( $user->ID, 'shipping_address_1' );
				$address_2    = get_user_meta( $user->ID, 'shipping_address_2' );
				$first_name   = get_user_meta( $user->ID, 'shipping_first_name' );
				$last_name    = get_user_meta( $user->ID, 'shipping_last_name' );
				$phone        = get_user_meta( $user->ID, 'billing_phone' );
				$city         = get_user_meta( $user->ID, 'shipping_city' );
				$full_address = orddd_common::orddd_get_formatted_shipping_customer_address( $user->ID );
			} else {
				$address_1    = '';
				$address_2    = '';
				$first_name   = '';
				$last_name    = '';
				$phone        = '';
				$city         = '';
				$full_address = '';
			}
			$app = new stdClass();
			if ( $test ) {
				$app->start        = date( 'Y-m-d', $this->local_time );
				$app->end          = date( 'Y-m-d', $this->local_time );
				$app->start_time   = date( 'H:i:s', $this->local_time + 600 );
				$app->end_time     = date( 'H:i:s', $this->local_time + 2400 );
				$app->client_email = get_option( 'admin_email' );
				if ( isset( $first_name[0] ) && isset( $last_name[0] ) ) {
					$app->client_name = $first_name[0] . ' ' . $last_name[0];
				} else {
					$app->client_name = '';
				}
				if ( isset( $address_1[0] ) && isset( $address_2[0] ) ) {
					$app->client_address = $address_1[0] . ' ' . $address_2[0];
				} else {
					$app->client_address = '';
				}
				$app->client_full_address = $full_address;
				if ( isset( $city[0] ) ) {
					$app->client_city = __( $city[0], 'order-delivery-date' );
				} else {
					$app->client_city = '';
				}

				if ( isset( $phone[0] ) ) {
					$app->client_phone = $phone[0];
				} else {
					$app->client_phone = '';
				}
				$app->order_note       = '';
				$app->order_total      = '';
				$app->products         = '';
				$app->product_with_qty = '';
				$app->product_with_cat = '';
				$app->order_date_time  = '';
				$app->order_date       = '';
				$app->id               = '';
			} else {
				if ( isset( $event_details['h_deliverydate'] ) && $event_details['h_deliverydate'] != '' ) {
					$delivery_date           = $event_details['h_deliverydate'];
					$order                   = wc_get_order( $event_id );
					$google_start_event_date = '';
					if ( has_filter( 'orddd_google_start_event_date' ) ) {
						$google_start_event_date = apply_filters( 'orddd_google_start_event_date', $google_start_event_date );
					}

					if ( '' != $google_start_event_date ) {
						$app->start = date( 'Y-m-d', strtotime( $google_start_event_date, strtotime( $delivery_date ) ) );
					} else {
						$app->start = date( 'Y-m-d', strtotime( $delivery_date ) );
					}

					$end_date = '';
					if ( has_filter( 'orddd_to_add_end_date_to_gcal' ) ) {
						$end_date = apply_filters( 'orddd_to_add_end_date_to_gcal', $event_details, $event_id, $test );
					}
					if ( '' != $end_date ) {
						$app->end = date( 'Y-m-d', strtotime( $end_date ) );
					} else {
						if ( '' != $google_start_event_date ) {
							$app->end = date( 'Y-m-d', strtotime( $google_start_event_date, strtotime( $delivery_date ) ) );
						} else {
							$app->end = date( 'Y-m-d', strtotime( $delivery_date ) );
						}
					}

					if ( isset( $event_details['time_slot'] ) &&
						$event_details['time_slot'] != '' &&
						$event_details['time_slot'] != 'NA' &&
						$event_details['time_slot'] != 'choose' &&
						$event_details['time_slot'] != 'select' ) {
						$timeslot  = explode( ' - ', $event_details['time_slot'] );
						$from_time = date( 'H:i', strtotime( $timeslot[0] ) );
						if ( isset( $timeslot[1] ) && $timeslot[1] != '' ) {
							$to_time       = date( 'H:i', strtotime( $timeslot[1] ) );
							$app->end_time = $to_time;
						} else {
							$app->end_time = $from_time;
						}
						$app->start_time = $from_time;
					} elseif ( isset( $event_details['_orddd_timestamp'] ) && $event_details['_orddd_timestamp'] != '' ) {
						$time_settings = date( 'H:i', $event_details['_orddd_timestamp'] );
						if ( $time_settings != '00:01' && $time_settings != '' && $time_settings != '00:00' ) {
							$app->start_time = $time_settings;
							$app->end_time   = $time_settings;
						} else {
							$app->start_time = '';
							$app->end_time   = '';
						}
					} elseif ( isset( $event_details['orddd_time_settings_selected'] ) && $event_details['orddd_time_settings_selected'] != '' ) {
						$from_time       = date( 'H:i', strtotime( $event_details['orddd_time_settings_selected'] ) );
						$app->start_time = $from_time;
						$app->end_time   = $from_time;
					} elseif ( get_option( 'orddd_enable_delivery_time' ) == 'on' ) {
						$time_settings_arr = explode( ' ', $event_details['e_deliverydate'] );
						array_pop( $time_settings_arr );
						$time_settings   = end( $time_settings_arr );
						$from_time       = date( 'H:i', strtotime( $time_settings ) );
						$app->start_time = $from_time;
						$app->end_time   = $from_time;
					} else {
						$app->start_time = '';
						$app->end_time   = '';
					}

					if ( has_filter( 'orddd_to_add_end_time_to_gcal' ) ) {
						$app->end_time = apply_filters( 'orddd_to_add_end_time_to_gcal', $event_details, $event_id, $test );
					}

					$app->client_email = $event_details['billing_email'];
					if ( get_option( 'woocommerce_calc_shipping' ) == 'yes' ) {
						if ( get_option( 'woocommerce_ship_to_destination' ) == 'shipping' ) {
							if ( isset( $event_details['billing_first_name'] ) && isset( $event_details['billing_last_name'] ) ) {
								$app->client_name = $event_details['billing_first_name'] . ' ' . $event_details['billing_last_name'];
							} else {
								$app->client_name = '';
							}

							$billing_address = '';
							if ( isset( $event_details['billing_address_1'] ) ) {
								$billing_address .= $event_details['billing_address_1'] . ' ';
							}
							if ( isset( $event_details['billing_address_2'] ) ) {
								$billing_address .= $event_details['billing_address_2'];
							}
							$app->client_address = $billing_address;

							if ( isset( $event_details['billing_city'] ) ) {
								$app->client_city = $event_details['billing_city'];
							} else {
								$app->client_city = '';
							}

							$app->client_full_address = str_replace( '<br/>', ",\r\n", $order->get_formatted_billing_address() );
							
							// Todo: this same block is also repeated in below condition, we could move it to a common function if possible
							if ( ( isset( $event_details['shipping_first_name'] ) && $event_details['shipping_first_name'] != '' ) && 
								( isset( $event_details['shipping_last_name'] ) && $event_details['shipping_last_name'] != '' ) ) {
								$app->shipping_name = $event_details['shipping_first_name'] . ' ' . $event_details['shipping_last_name'];
							} else {
								$app->shipping_name = '';
							}

							$shipping_address = '';
							if ( isset( $event_details['shipping_address_1'] ) ) {
								$shipping_address .= $event_details['shipping_address_1'] . ' ';
							}
							if ( isset( $event_details['shipping_address_2'] ) ) {
								$shipping_address .= $event_details['shipping_address_2'];
							}
							$app->shipping_address = $shipping_address;

							if ( ( isset( $event_details['shipping_city'] ) && $event_details['shipping_city'] != '' ) ) {
								$app->shipping_city = $event_details['shipping_city'];
							} else {
								$app->shipping_city = '';
							}

							$app->shipping_full_address = str_replace( '<br/>', ",\r\n", $order->get_formatted_shipping_address() );

							// if we need to, then set shipping details.
							if ( isset( $event_details['ship_to_different_address'] ) ) {
								// shipping address.
								$address               = '';
								$address              .= ( isset( $event_details['shipping_address_1'] ) ) ? $event_details['shipping_address_1'] : '';
								$address              .= ( isset( $event_details['shipping_address_2'] ) ) ? $event_details['shipping_address_2'] : '';
								$app->shipping_address = $address;

								if ( ( isset( $event_details['shipping_first_name'] ) && $event_details['shipping_first_name'] != '' ) && ( isset( $event_details['shipping_last_name'] ) && $event_details['shipping_last_name'] != '' ) ) {
									$app->shipping_name = $event_details['shipping_first_name'] . ' ' . $event_details['shipping_last_name'];
								}

								$app->shipping_full_address = str_replace( '<br/>', ",\r\n", $order->get_formatted_shipping_address() );
								$app->shipping_city         = ( isset( $event_details['shipping_city'] ) && $event_details['shipping_city'] != '' ) ? $event_details['shipping_city'] : '';
							}
							// Todo end
						} elseif ( get_option( 'woocommerce_ship_to_destination' ) == 'billing' ) {
							if ( ( isset( $event_details['billing_first_name'] ) && $event_details['billing_first_name'] != '' ) && ( isset( $event_details['billing_last_name'] ) && $event_details['billing_last_name'] != '' ) ) {
								$app->client_name = $event_details['billing_first_name'] . ' ' . $event_details['billing_last_name'];
							} else {
								$app->client_name = '';
							}

							if ( ( isset( $event_details['billing_address_1'] ) && $event_details['billing_address_1'] != '' ) ) {
								$app->client_address = $event_details['billing_address_1'] . ' ' . $event_details['billing_address_2'];
							} else {
								$app->client_address = '';
							}

							if ( isset( $event_details['billing_city'] ) && $event_details['billing_city'] != '' ) {
								$app->client_city = $event_details['billing_city'];
							} else {
								$app->client_city = '';
							}

							$app->client_full_address = str_replace( '<br/>', ",\r\n", $order->get_formatted_billing_address() );

							if ( ( isset( $event_details['shipping_first_name'] ) && $event_details['shipping_first_name'] != '' ) && ( isset( $event_details['shipping_last_name'] ) && $event_details['shipping_last_name'] != '' ) ) {
								$app->shipping_name = $event_details['shipping_first_name'] . ' ' . $event_details['shipping_last_name'];
							} else {
								$app->shipping_name = '';
							}

							$shipping_address = '';
							if ( isset( $event_details['shipping_address_1'] ) ) {
								$shipping_address .= $event_details['shipping_address_1'] . ' ';
							}
							if ( isset( $event_details['shipping_address_2'] ) ) {
								$shipping_address .= $event_details['shipping_address_2'];
							}
							$app->shipping_address = $shipping_address;

							if ( ( isset( $event_details['shipping_city'] ) && $event_details['shipping_city'] != '' ) ) {
								$app->shipping_city = $event_details['shipping_city'];
							} else {
								$app->shipping_city = '';
							}

							$app->shipping_full_address = str_replace( '<br/>', ",\r\n", $order->get_formatted_shipping_address() );

							// if we need to, then set shipping details.
							if ( isset( $event_details['ship_to_different_address'] ) ) {
								// shipping address.
								$address               = '';
								$address              .= ( isset( $event_details['shipping_address_1'] ) ) ? $event_details['shipping_address_1'] : '';
								$address              .= ( isset( $event_details['shipping_address_2'] ) ) ? $event_details['shipping_address_2'] : '';
								$app->shipping_address = $address;

								if ( ( isset( $event_details['shipping_first_name'] ) && $event_details['shipping_first_name'] != '' ) && ( isset( $event_details['shipping_last_name'] ) && $event_details['shipping_last_name'] != '' ) ) {
									$app->shipping_name = $event_details['shipping_first_name'] . ' ' . $event_details['shipping_last_name'];
								}

								$app->shipping_full_address = str_replace( '<br/>', ",\r\n", $order->get_formatted_shipping_address() );
								$app->shipping_city         = ( isset( $event_details['shipping_city'] ) && $event_details['shipping_city'] != '' ) ? $event_details['shipping_city'] : '';
							}
						} elseif ( get_option( 'woocommerce_ship_to_destination' ) == 'billing_only' ) {
							if ( isset( $event_details['billing_first_name'] ) && isset( $event_details['billing_last_name'] ) ) {
								$app->client_name = $event_details['billing_first_name'] . ' ' . $event_details['billing_last_name'];
							} else {
								$app->client_name = '';
							}

							if ( isset( $event_details['billing_address_1'] ) && isset( $event_details['billing_address_2'] ) ) {
								$app->client_address = $event_details['billing_address_1'] . ' ' . $event_details['billing_address_2'];
							} else {
								$app->client_address = '';
							}

							if ( isset( $event_details['billing_city'] ) ) {
								$app->client_city = $event_details['billing_city'];
							} else {
								$app->client_city = '';
							}

							$app->client_full_address = str_replace( '<br/>', ",\r\n", $order->get_formatted_billing_address() );

							// default Shipping to billing
							$app->shipping_name         = $app->client_name;
							$app->shipping_address      = $app->client_address;
							$app->shipping_full_address = $app->client_full_address;
							$app->shipping_city         = $app->client_city;
						}
					} else {
						if ( isset( $event_details['billing_first_name'] ) && isset( $event_details['billing_last_name'] ) ) {
							$app->client_name = $event_details['billing_first_name'] . ' ' . $event_details['billing_last_name'];
						} else {
							$app->client_name = '';
						}

						if ( isset( $event_details['billing_address_1'] ) && isset( $event_details['billing_address_2'] ) ) {
							$app->client_address = $event_details['billing_address_1'] . ' ' . $event_details['billing_address_2'];
						} else {
							$app->client_address = '';
						}

						if ( isset( $event_details['billing_city'] ) ) {
							$app->client_city = $event_details['billing_city'];
						} else {
							$app->client_city = '';
						}

						$app->client_full_address = str_replace( '<br/>', ',', $order->get_formatted_billing_address() );
					}

					// lets add the shipping details.
					if ( isset( $event_details['ship_to_different_address'] ) ) {
						$app->shipping_first_name = $event_details['shipping_first_name'];
						$app->shipping_last_name  = $event_details['shipping_last_name'];
						$app->shipping_company    = $event_details['shipping_company'];
						$app->shipping_address_1  = $event_details['shipping_address_1'];
						$app->shipping_address_2  = $event_details['shipping_address_2'];
						$app->shipping_city       = $event_details['shipping_city'];
						$app->shipping_state      = $event_details['shipping_state'];
						$app->shipping_postcode   = $event_details['shipping_postcode'];
						$app->shipping_phone      = $event_details['shipping_phone'];
					}
					$app->shipping_method_title = $event_details['shipping_method_title'];
					$app->payment_method_title  = $event_details['payment_method_title'];
					$app->pickup_location       = $event_details['pickup_location'];
					$app->client_phone          = $event_details['billing_phone'];
					$app->order_note            = $event_details['order_comments'];
					$app->order_weblink         = $event_details['order_weblink'];
					$app->order_status          = ucfirst( $event_details['order_status'] );
					$get_order_items            = $order->get_items();

					$products          = '';
					$product_with_qty  = '';
					$product_with_cat  = '';
					foreach ( $get_order_items as $key => $value ) {
						$data         = $value->get_data();
						$product_name = $value['name'];
						if ( isset( $data['variation_id'] ) && $data['variation_id'] != 0 ) {
							$_product       = new WC_Product_Variation( $data['variation_id'] );
							$product_name   = $_product->get_title();
							$variation_data = $_product->get_variation_attributes(); // variation data in array
							if ( is_array( $variation_data ) && count( $variation_data ) > 0 ) {
								$meta_data = $data['meta_data'];
								$i         = 0;
								foreach ( $meta_data as $mkey => $mvalue ) {
									$meta_key = $mvalue->get_data();
									if ( isset( $meta_key['key'] ) &&
										array_key_exists( 'attribute_' . $meta_key['key'], $variation_data ) ) {
										if ( $i == 0 ) {
											$product_name .= ' - ';
										}
										$attribute_name_arr = explode( 'pa_', $meta_key['key'] );
										if ( isset( $attribute_name_arr[1] ) && '' !== $attribute_name_arr[1] ) {
											$attribute_name = ucfirst( $attribute_name_arr[1] );
										} else {
											$attribute_name = ucfirst( $meta_key['key'] );
										}
										$product_name .= $attribute_name . ': ' . urldecode( $meta_key['value'] ) . ', ';
									}
									$i++;
								}
							}
						}

						$term       = 'product_cat';
						$cats       = '';
						$product_id = $value[ 'product_id' ];
						$terms      = wp_get_post_terms( $product_id, $term, array( 'fields' => 'names' ) );

						if ( $terms && ! is_wp_error( $terms ) ) {
							$cats .= '[' . implode( ', ', $terms ) . '] ';
						}
						$product_name      = rtrim( $product_name, ', ' );

						// fetch any custom product options.
						$product_options = '';
						$product_options = apply_filters(
							'orddd_gcal_add_product_options',
							$product_options,
							$product_name,
							$product_id,
							$value
						);

						$products         .= "\r\n" . $product_name . $product_options . ', ';
						$product_with_qty .= "\r\n" . $product_name . ' (Qty: ' . $value['qty'] . ')' . $product_options . ', ';
						$product_with_cat .= "\r\n" . $cats . $product_name . ' (Qty: ' . $value['qty'] . ')' . $product_options . ', ';
					}
					$products              = substr( $products, 0, strlen( $products ) - 2 );
					$product_with_qty      = substr( $product_with_qty, 0, strlen( $product_with_qty ) - 2 );
					$product_with_cat      = substr( $product_with_cat, 0, strlen( $product_with_cat ) - 2 );
					$app->order_total      = strip_tags( $order->get_formatted_order_total() );
					$app->products         = $products;
					$app->product_with_qty = $product_with_qty;
					$app->product_with_cat = $product_with_cat;

					// Iterating through order shipping items.
					foreach ( $order->get_items( 'shipping' ) as $item_id => $shipping_item_obj ) {
						$app->shipping_method_title     = $shipping_item_obj->get_method_title();
						$app->shipping_method_id        = $shipping_item_obj->get_method_id(); // The method ID
						$app->shipping_method_total     = $shipping_item_obj->get_total();
						$app->shipping_method_total_tax = $shipping_item_obj->get_total_tax();
						$app->shipping_method_taxes     = $shipping_item_obj->get_taxes();
					}

					$order_post           = get_post( $event_id );
					$app->order_date_time = date( 'Y-m-d H:i:s', strtotime( $order_post->post_date ) );
					$order_date           = date( 'Y-m-d', strtotime( $order_post->post_date ) );
					$app->order_date      = $order_date;
					$app->id              = $order->get_order_number();

					// add data of tyche plugins.
					if ( isset( $event_details['deposit_payment'] ) ) {
						$app->deposit_payment = $event_details['deposit_payment'];
					}
					if ( isset( $event_details['future_payments'] ) ) {
						$app->future_payments = $event_details['future_payments'];
					}
				}
			}

			// Create Event object and set parameters.
			$this->set_event_parameters( $app );
			// Insert event.
			try {
				$createdEvent = $this->service->events->insert( $this->get_selected_calendar(), $this->event );
				$uid          = $createdEvent->iCalUID;

				if ( isset( $order ) ) {
					$order->add_order_note( __( 'Order has been exported to Google Calendar.', 'order-delivery-date' ) );
				}

				$event_orders = get_option( 'orddd_event_order_ids' );
				if ( $event_orders == '' || $event_orders == '{}' || $event_orders == '[]' || $event_orders == 'null' ) {
					$event_orders = array();
				}
				array_push( $event_orders, $event_id );
				update_option( 'orddd_event_order_ids', $event_orders );

				$event_uids = get_option( 'orddd_event_uids_ids' );
				if ( $event_uids == '' || $event_uids == '{}' || $event_uids == '[]' || $event_uids == 'null' ) {
					$event_uids = array();
				}
				$event_uids[ $event_id ] = $uid;
				update_option( 'orddd_event_uids_ids', $event_uids );

				// 9.19.0 TEST: Comment below part if you want to test migration script & prevent post meta from being added for each googlge calendar event.
				if ( false === $test ) {
					update_post_meta( $event_id, '_orddd_gcal_event_id', $uid );
				}
				return true;
			} catch ( Exception $e ) {
				$this->log( 'Insert went wrong: ' . $e->getMessage() );
				return false;
			}
		}

		/**
		 * Logs the error if something went wrong.
		 *
		 * @param string $message Message to be sent to the log file.
		 * @since 4.0
		 */
		function log( $message = '' ) {
			if ( $message ) {
				$to_put = '<b>[' . date_i18n( $this->datetime_format, $this->local_time ) . ']</b> ' . $message;
				// Prevent multiple messages with same text and same timestamp
				if ( ! file_exists( $this->log_file ) || strpos( @file_get_contents( $this->log_file ), $to_put ) === false ) {
					@file_put_contents( $this->log_file, $to_put . chr( 10 ) . chr( 13 ), FILE_APPEND );
				}
			}
		}

		/**
		 * Build GCal url for GCal Button. It requires UTC time.
		 *
		 * @param resource $orddd Event Details Object
		 *
		 * @return string URL
		 * @since 4.0
		 */
		function gcal( $orddd ) {
			if ( ! isset( $orddd->order_weblink ) ) {
				$orddd->order_weblink = '';
			}
			// Find time difference from Greenwich as GCal asks UTC.
			$summary = str_replace(
				array( 'SITE_NAME', 'CLIENT', 'PRODUCTS', 'PRODUCT_WITH_QTY', 'ORDER_DATE_TIME', 'ORDER_DATE', 'ORDER_NUMBER', 'PRICE', 'PHONE', 'NOTE', 'FULL_ADDRESS', 'ADDRESS', 'EMAIL', 'ORDER_WEBLINK' ),
				array( get_bloginfo( 'name' ), $orddd->client_name, $orddd->products, $orddd->product_with_qty, $orddd->order_date_time, $orddd->order_date, $orddd->id, $orddd->order_total, $orddd->client_phone, $orddd->order_note, $orddd->client_full_address, $orddd->client_address, $orddd->client_email, urlencode( $orddd->order_weblink ) ),
				$this->get_summary()
			);

			$description = str_replace(
				array( 'SITE_NAME', 'CLIENT', 'PRODUCTS', 'PRODUCT_WITH_QTY', 'ORDER_DATE_TIME', 'ORDER_DATE', 'ORDER_NUMBER', 'PRICE', 'PHONE', 'NOTE', 'FULL_ADDRESS', 'ADDRESS', 'EMAIL', 'ORDER_WEBLINK' ),
				array( get_bloginfo( 'name' ), $orddd->client_name, $orddd->products, $orddd->product_with_qty, $orddd->order_date_time, $orddd->order_date, $orddd->id, $orddd->order_total, $orddd->client_phone, $orddd->order_note, $orddd->client_full_address, $orddd->client_address, $orddd->client_email, urlencode( $orddd->order_weblink ) ),
				$this->get_description()
			);

			if ( $orddd->start_time == '' && $orddd->end_time == '' ) {
				$start = strtotime( $orddd->start );
				$end   = strtotime( $orddd->end . '+1 day' );

				$gmt_start = date( 'Ymd', $start );
				$gmt_end   = date( 'Ymd', $end );
			} elseif ( $orddd->end_time == '' ) {
				$start = strtotime( $orddd->start . ' ' . $orddd->start_time );
				$end   = strtotime( $orddd->end . ' ' . $orddd->start_time );

				$gmt_start = get_gmt_from_date( date( 'Y-m-d H:i:s', $start ), 'Ymd\THis\Z' );
				$gmt_end   = get_gmt_from_date( date( 'Y-m-d H:i:s', $end ), 'Ymd\THis\Z' );
			} else {
				$start = strtotime( $orddd->start . ' ' . $orddd->start_time );
				$end   = strtotime( $orddd->end . ' ' . $orddd->end_time );

				$gmt_start = get_gmt_from_date( date( 'Y-m-d H:i:s', $start ), 'Ymd\THis\Z' );
				$gmt_end   = get_gmt_from_date( date( 'Y-m-d H:i:s', $end ), 'Ymd\THis\Z' );
			}

			if ( get_option( 'orddd_calendar_event_location' ) != '' ) {
				$location = str_replace( array( 'FULL_ADDRESS', 'ADDRESS_SHIP', 'ADDRESS', 'CITY' ), array( $orddd->client_full_address, $orddd->client_address, $orddd->client_address, $orddd->client_city ), get_option( 'orddd_calendar_event_location' ) );
			} else {
				$location = get_bloginfo( 'description' );
			}

			$param = array(
				'action'   => 'TEMPLATE',
				'text'     => $summary,
				'dates'    => $gmt_start . '/' . $gmt_end,
				'location' => $location,
				'details'  => $description,
			);

			return esc_url(
				add_query_arg(
					array( $param, $start, $end ),
					'http://www.google.com/calendar/event'
				)
			);
		}

		/**
		 * Build url for Other calendar Button. It requires UTC time.
		 *
		 * @param resource $orddd Event Details Object
		 *
		 * @return string URL
		 * @since 4.0
		 */

		function other_cal( $orddd ) {
			$gmt = false;
			if ( has_filter( 'orddd_gmt_calculations' ) ) {
				$gmt = apply_filters( 'orddd_gmt_calculations', '' );
			}
			$current_time = current_time( 'timestamp', $gmt );

			// Find time difference from Greenwich as GCal asks UTC.
			$summary = str_replace(
				array( 'SITE_NAME', 'CLIENT', 'PRODUCTS', 'PRODUCT_WITH_QTY', 'ORDER_DATE_TIME', 'ORDER_DATE', 'ORDER_NUMBER', 'PRICE', 'PHONE', 'NOTE', 'FULL_ADDRESS', 'ADDRESS', 'EMAIL' ),
				array( get_bloginfo( 'name' ), $orddd->client_name, $orddd->products, $orddd->product_with_qty, $orddd->order_date_time, $orddd->order_date, $orddd->id, $orddd->order_total, $orddd->client_phone, $orddd->order_note, $orddd->client_full_address, $orddd->client_address, $orddd->client_email ),
				$this->get_summary()
			);

			$description = str_replace(
				array( 'SITE_NAME', 'CLIENT', 'PRODUCTS', 'PRODUCT_WITH_QTY', 'ORDER_DATE_TIME', 'ORDER_DATE', 'ORDER_NUMBER', 'PRICE', 'PHONE', 'NOTE', 'FULL_ADDRESS', 'ADDRESS', 'EMAIL' ),
				array( get_bloginfo( 'name' ), $orddd->client_name, $orddd->products, $orddd->product_with_qty, $orddd->order_date_time, $orddd->order_date, $orddd->id, $orddd->order_total, $orddd->client_phone, $orddd->order_note, $orddd->client_full_address, $orddd->client_address, $orddd->client_email ),
				$this->get_description()
			);

			if ( $orddd->start_time == '' && $orddd->end_time == '' ) {
				$gmt_start = strtotime( $orddd->start );
				$gmt_end   = strtotime( '+1 day', strtotime( $orddd->end ) );
			} elseif ( $orddd->end_time == '' ) {
				$time_start = explode( ':', $orddd->start_time );
				$gmt_start  = strtotime( $orddd->start ) + $time_start[0] * 60 * 60 + $time_start[1] * 60 + ( time() - $current_time );
				$gmt_end    = strtotime( $orddd->end ) + $time_start[0] * 60 * 60 + $time_start[1] * 60 + ( time() - $current_time );
			} else {
				$time_start = explode( ':', $orddd->start_time );
				$time_end   = explode( ':', $orddd->end_time );
				$gmt_start  = strtotime( $orddd->start ) + $time_start[0] * 60 * 60 + $time_start[1] * 60 + ( time() - $current_time );
				$gmt_end    = strtotime( $orddd->end ) + $time_end[0] * 60 * 60 + $time_end[1] * 60 + ( time() - $current_time );
			}

			if ( get_option( 'orddd_calendar_event_location' ) != '' ) {
				$location = str_replace( array( 'FULL_ADDRESS', 'ADDRESS', 'CITY' ), array( $orddd->client_full_address, $orddd->client_address, $orddd->client_city ), get_option( 'orddd_calendar_event_location' ) );
			} else {
				$location = get_bloginfo( 'description' );
			}

			return plugins_url( "order-delivery-date/includes/ical.php?event_date_start=$gmt_start&amp;event_date_end=$gmt_end&amp;current_time=$current_time&amp;summary=$summary&amp;description=$description&amp;event_location=$location" );
		}
	}
}

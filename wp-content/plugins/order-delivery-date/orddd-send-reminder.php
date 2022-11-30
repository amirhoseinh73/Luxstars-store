<?php
/**
 * Order Delivery Date Pro for WooCommerce
 *
 * Menu page for sending manual reminder emails and setting automatic reminders for deliveries.
 * It is being used in admin.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Pro-for-WooCommerce/Reminder-Emails
 * @since       8.6
 * @category    Classes
 */

if ( ! class_exists( 'orddd_send_reminder' ) ) {

	/**
	 * Reminder Emails for Customers & Admin.
	 */
	class orddd_send_reminder {
		/**
		 * Default Constructor
		 *
		 * @since 8.6
		 */
		public function __construct() {
			// Add a sub menu in the main menu of the plugin if added.
			add_action( 'orddd_add_submenu', array( &$this, 'orddd_re_add_submenu' ) );
			add_action( 'admin_init', array( &$this, 'orddd_send_automatic_reminder' ), 10 );
			add_filter( 'woocommerce_screen_ids', array( $this, 'orddd_add_screen_id' ) );
			add_action( 'admin_init', array( &$this, 'orddd_modify_admin_reminder_email' ) );
		}

		/**
		 * Add the id of the Send reminder page into screen ids page.
		 *
         * @param array $screen_ids Page IDs.
		 * @since 8.6
		 */
		public static function orddd_add_screen_id( $screen_ids ) {
			$screen_ids[] = 'order-delivery-date_page_orddd_send_reminder_page';
			return $screen_ids;
		}

		/**
		 * Adds a submenu to main menu.
		 *
		 * @since 8.6
		 */
		public function orddd_re_add_submenu() {
			$page = add_submenu_page(
				'order_delivery_date',
				__( 'Send Reminder', 'order-delivery-date' ),
				__( 'Send Reminder', 'order-delivery-date' ),
				'manage_woocommerce',
				'orddd_send_reminder_page',
				array( 'orddd_send_reminder', 'orddd_send_reminder_page' )
			);

		}

		/**
		 * Add content to the Send Reminder page.
		 *
		 * @since 8.6
		 */
		public static function orddd_send_reminder_page() {
			if ( ! empty( $_POST ) && check_admin_referer( 'orddd_delivery_reminder' ) ) {
				$order_ids = isset( $_POST['orddd_reminder_order_id'] ) && '' != $_POST['orddd_reminder_order_id'] ? $_POST['orddd_reminder_order_id'] : ''; //phpcs:ignore
				$subject   = isset( $_POST['orddd_reminder_subject'] ) && '' != $_POST['orddd_reminder_subject'] ? $_POST['orddd_reminder_subject'] : 'Delivery Reminder'; //phpcs:ignore
				$message   = isset( $_POST['orddd_reminder_message'] ) && '' != $_POST['orddd_reminder_message'] ? $_POST['orddd_reminder_message'] : ''; //phpcs:ignore
				$mailer    = WC()->mailer();
				$reminder  = $mailer->emails['ORDDD_Email_Delivery_Reminder'];

				if ( is_array( $order_ids ) && ! empty( $order_ids ) ) {
					foreach ( $order_ids as $key => $value ) {
						$reminder->trigger( $value, $subject, $message );
						echo '<div class="updated fade"><p>' . __( 'Reminder sent successfully', 'order-delivery-date' ) . '</p></div>';
					}
				}
			}

			$all_order_ids = orddd_common::orddd_get_all_future_orders();
			wc_get_template(
				'orddd-reminder-email-view.php',
				array(
					'order_ids' => $all_order_ids,
				),
				'order-delivery-date/',
				ORDDD_TEMPLATE_PATH
			);
		}

		/**
		 * Add a setting for automatic reminders to set the number of days
		 *
		 * @since 8.6
		 */

		public static function orddd_send_automatic_reminder() {
			add_settings_section(
				'orddd_reminder_section',
				'',
				array( 'orddd_send_reminder', 'orddd_reminder_settings_section_callback' ),
				'orddd_send_reminder_page'
			);
			add_settings_field(
				'orddd_reminder_email_before_days',
				__( 'Number of days for reminder before Delivery Date', 'order-delivery-date' ),
				array( 'orddd_send_reminder', 'orddd_reminder_email_before_days_callback' ),
				'orddd_send_reminder_page',
				'orddd_reminder_section',
				array( __( 'Send the reminder email to customers X number of days before the Delivery Date.', 'order-delivery-date' ) )
			);

			add_settings_field(
				'orddd_reminder_for_admin',
				__( 'Enable reminder emails for admin', 'order-delivery-date' ),
				array( 'orddd_send_reminder', 'orddd_reminder_email_admin_callback' ),
				'orddd_send_reminder_page',
				'orddd_reminder_section',
				array( __( 'Reminders will be sent to admin for the next day\'s deliveries.', 'order-delivery-date' ) )
			);

			register_setting(
				'orddd_reminder_settings',
				'orddd_reminder_email_before_days'
			);

			register_setting(
				'orddd_reminder_settings',
				'orddd_reminder_for_admin'
			);

		}

		public static function orddd_reminder_settings_section_callback() {}

		/**
		 * Callback function to add a setting to set X number of days to send reminder to
		 * customers before the delivery date
		 *
		 * @param array $args Extra arguments containing label & class for the field.
		 * @since 4.10.0
		 */
		public static function orddd_reminder_email_before_days_callback( $args ) {
			$reminder_email_before_days = get_option( 'orddd_reminder_email_before_days', 0 );
			$timezone                   = self::orddd_get_timezone_string();

			$reminder_time = apply_filters( 'orddd_modify_reminder_email_time', strtotime( '07:00:00 ' . $timezone ), $timezone );
			if ( $reminder_email_before_days > 0 ) {
				if ( false === as_next_scheduled_action( 'orddd_auto_reminder_emails' ) ) {
					$reminder_emails_frequency = apply_filters( 'orddd_reminder_emails_frequency', 86400 );
					as_schedule_recurring_action( $reminder_time, $reminder_emails_frequency, 'orddd_auto_reminder_emails' );
				}
			} else {
				as_unschedule_action( 'orddd_auto_reminder_emails' );
			}

			echo '<input type="number" name="orddd_reminder_email_before_days" id="orddd_reminder_email_before_days" value="' . $reminder_email_before_days . '"/>'; //phpcs:ignore
			$html = '<label for="orddd_reminder_email_before_days"> ' . $args[0] . '</label>';
			echo $html;
		}

		/**
		 * Callback function to add a setting to enable reminder emails for Admin
		 *
		 * @param array $args Extra arguments containing label & class for the field
		 * @since 9.6
		 */
		public static function orddd_reminder_email_admin_callback( $args ) {
			$checked  = '';
			$timezone = self::orddd_get_timezone_string();

			$reminder_time = apply_filters( 'orddd_modify_admin_reminder_email_time', strtotime( '19:00:00 ' . $timezone ), $timezone );

			if ( get_option( 'orddd_reminder_for_admin' ) == 'on' ) {
				$checked = 'checked';
				if ( false === as_next_scheduled_action( 'orddd_auto_reminder_emails_admin' ) ) {
					$reminder_emails_admin_frequency = apply_filters( 'orddd_reminder_emails_admin_frequency', 86400 );
					as_schedule_recurring_action( $reminder_time, $reminder_emails_admin_frequency, 'orddd_auto_reminder_emails_admin' );
				}
			} else {
				as_unschedule_action( 'orddd_auto_reminder_emails_admin' );
			}

			echo '<input type="checkbox" name="orddd_reminder_for_admin" id="orddd_reminder_for_admin" class="day-checkbox" value="on" ' . $checked . ' />';

			$html = '<label for="orddd_reminder_for_admin"> ' . $args[0] . '</label>';
			echo $html;
		}



		/**
		 * Ajax call for saving the email draft on Manual Reminder page
		 *
		 * @since 8.6
		 */
		public static function orddd_save_reminder_message() {
			$message = $_POST['message']; //phpcs:ignore
			$subject = $_POST['subject']; //phpcs:ignore

			if ( isset( $message ) && '' != $message ) {
				update_option( 'orddd_reminder_message', $message );
			}

			if ( isset( $subject ) && '' != $subject ) {
				update_option( 'orddd_reminder_subject', $subject );
			}
		}

		/**
		 * Change the admin reminder cron time to 7PM
		 */
		public static function orddd_modify_admin_reminder_email() {

			$timezone     = self::orddd_get_timezone_string();
			$current_time = current_time( 'timestamp' );
			$time_changed = get_option( 'orddd_modify_admin_reminder_cron' );

			// If the next cron time is not 7PM and the option is not set then schedule the cron to 7PM.We are changing the time to 7pm from earlier time of 10am. As a lot of orders would also come in for the next day after 10am.

			if ( get_option( 'orddd_reminder_for_admin' ) == 'on'
					&& as_next_scheduled_action( 'orddd_auto_reminder_emails_admin' ) != strtotime( '19:00:00 ' . $timezone )
					&& 'yes' != $time_changed ) {

				as_unschedule_action( 'orddd_auto_reminder_emails_admin' );
				$reminder_emails_admin_frequency = apply_filters( 'orddd_reminder_emails_admin_frequency', 86400 );
				as_schedule_recurring_action( strtotime( '19:00:00 ' . $timezone ), $reminder_emails_admin_frequency, 'orddd_auto_reminder_emails_admin' );
				update_option( 'orddd_modify_admin_reminder_cron', 'yes' );

			}
		}

		/**
		 * Calculate the local timezone based on the WordPress site settings
		 *
		 * @since 9.7
		 */
		public static function orddd_get_timezone_string() {

			$timezone   = get_option( 'timezone_string' );
			$gmt_offset = get_option( 'gmt_offset' );

			// Remove old Etc mappings. Fallback to gmt_offset.
			if ( ! empty( $timezone ) && false !== strpos( $timezone, 'Etc/GMT' ) ) {
				$timezone = '';
			}

			if ( empty( $timezone ) && 0 != $gmt_offset ) {
				// Use gmt_offset.
				$gmt_offset   *= 3600; // convert hour offset to seconds.
				$allowed_zones = timezone_abbreviations_list();

				foreach ( $allowed_zones as $abbr ) {
					foreach ( $abbr as $city ) {
						if ( $city['offset'] == $gmt_offset ) {
							$timezone = $city['timezone_id'];
							break 2;
						}
					}
				}
			}

			// Issue with the timezone selected, set to 'UTC'.
			if ( empty( $timezone ) ) {
				$timezone = 'UTC';
			}

			return $timezone;
		}
	}
	new orddd_send_reminder();
}

/**
 * Scheduled event for the automatic reminder emails
 *
 * @since 4.10.0
 */
function orddd_send_auto_reminder_emails() {
	$gmt = false;
	if ( has_filter( 'orddd_gmt_calculations' ) ) {
		$gmt = apply_filters( 'orddd_gmt_calculations', '' );
	}

	$current_time = current_time( 'timestamp', $gmt );

	// TODO: Modify the below function or create a new function such that it only fetches the
	// orders whose delivery date is falling on the date that comes after
	// orddd_reminder_email_before_days days value.
	$future_orders              = orddd_common::orddd_get_all_future_orders();
	$reminder_email_before_days = get_option( 'orddd_reminder_email_before_days' );
	if ( '' === $reminder_email_before_days ) {
		$reminder_email_before_days = 0;
	}

	$mailer            = WC()->mailer();
	$reminder          = $mailer->emails['ORDDD_Email_Delivery_Reminder'];
	$current_date      = date( 'j-n-Y', $current_time );
	$current_date_time = strtotime( $current_date );
	foreach ( $future_orders as $key => $value ) {
		$orddd_timestamp      = get_post_meta( $value->ID, '_orddd_timestamp', true );
		$orddd_date           = date( 'j-n-Y', $orddd_timestamp );
		$orddd_date_timestamp = strtotime( $orddd_date );
		$days_diff            = absint( ( $orddd_date_timestamp - $current_date_time ) );
		if ( $days_diff == absint( $reminder_email_before_days * 86400 ) ) {
			$reminder->trigger( $value->ID );
		}
	}
}
add_action( 'orddd_auto_reminder_emails', 'orddd_send_auto_reminder_emails' );


/**
 * Send reminder emails to admin when the action 'orddd_auto_reminder_emails_admin' is scheduled
 * via cron
 *
 * @since 9.6
 */
function orddd_send_auto_reminder_emails_admin() {
	$mailer   = WC()->mailer();
	$reminder = $mailer->emails['ORDDD_Email_Admin_Delivery_Reminder'];
	$reminder->trigger();

}
add_action( 'orddd_auto_reminder_emails_admin', 'orddd_send_auto_reminder_emails_admin' );


/**
 * Fetch all the orders with status wc-processing or wc-completed and with the next day's delivery dates
 *
 * @since 9.6
 */
function orddd_get_tomorrows_orders() {
	global $wpdb;
	$timezone = orddd_send_reminder::orddd_get_timezone_string();

	$tomorrow_midnight  = strtotime( 'tomorrow midnight' );
	$day_after_tomorrow = strtotime( 'tomorrow + 1 day' );

	$orddd_query = "SELECT ID, post_status FROM `" . $wpdb->prefix . "posts` WHERE post_type ='shop_order' AND post_status NOT IN ( 'wc-on-hold', 'wc-cancelled', 'wc-failed','wc-refunded', 'trash' ) AND ID IN ( SELECT post_id FROM `" . $wpdb->prefix . "postmeta` WHERE ( meta_key = '_orddd_timestamp' AND meta_value >= '" . $tomorrow_midnight . "' AND meta_value < '" . $day_after_tomorrow . "' ) )";

	$results = $wpdb->get_results( $orddd_query );
	$report  = array();
	$i       = 0;
	foreach ( $results as $rkey => $rval ) {
		$order       = new WC_Order( $rval->ID );
		$order_items = $order->get_items();
		$i           = $rval->ID;

		$products = array();
		foreach ( $order_items as $item ) {
			$report[ $i ] = new stdClass();

			// Order ID.
			$report[ $i ]->order_id = $rval->ID;

			// Product Name.
			$product_name = html_entity_decode( $item['name'], ENT_COMPAT, 'UTF-8' );
			array_push(
				$products,
				array(
					'product'  => $product_name,
					'quantity' => $item['quantity'],
				)
			);
			$report[ $i ]->product_name = $products;

			// Quantity.
			$report[ $i ]->quantity = $item['quantity'];

			// Billing Address.
			$billing                       = $order->get_formatted_billing_address();
			$billing                       = str_replace( '\n', ',', $billing );
			$billing                       = str_replace( PHP_EOL, ',', $billing );
			$billing                       = str_replace( '<br/>', ',', $billing );
			$report[ $i ]->billing_address = $billing;

			// Shipping Address.
			$shipping                       = $order->get_formatted_shipping_address();
			$shipping                       = str_replace( '\n', ',', $shipping );
			$shipping                       = str_replace( PHP_EOL, ',', $shipping );
			$shipping                       = str_replace( '<br/>', ',', $shipping );
			$report[ $i ]->shipping_address = $shipping;

			// Shipping Method.
			$report[ $i ]->shipping_method = $order->get_shipping_method();

			// Pickup Location.
			$report[ $i ]->pickup_location = orddd_common::orddd_get_order_formatted_location( $rval->ID );

			// Delivery Date.
			$report[ $i ]->delivery_date = orddd_common::orddd_get_order_delivery_date( $rval->ID );

			// Delivery Time.
			$delivery_date_timestamp = get_post_meta( $rval->ID, '_orddd_timestamp', true );
			$delivery_time           = date( 'H:i', $delivery_date_timestamp );

			if ( $delivery_time != '00:00' && $delivery_time != '00:01' ) {
				$time_format_to_show         = orddd_common::orddd_get_time_format();
				$delivery_time               = date( $time_format_to_show, $delivery_date_timestamp );
				$report[ $i ]->delivery_time = $delivery_time;
			} else {
				$report[ $i ]->delivery_time = orddd_common::orddd_get_order_timeslot( $rval->ID );
			}

			// Order Date.
			$order_date = '';
			if ( version_compare( WOOCOMMERCE_VERSION, '3.0.0' ) < 0 ) {
				$order_date = $order->completed_date;
			} else {
				$order_post = get_post( $rval->ID );
				$post_date  = strtotime( $order_post->post_date );
				$order_date = date( 'Y-m-d H:i:s', $post_date );
			}

			$report[ $i ]->order_date = $order_date;
		}
	}

	return $report;
}

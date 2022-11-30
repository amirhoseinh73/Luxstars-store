<?php
/**
 * Order Calendar Sync Settings in admin.
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Pro-for-WooCommerce/Admin/Settings/Google-Calendar-Sync
 * @since 4.0
 * @category Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Orddd_Calendar_Sync_Settings class
 *
 * @class Orddd_Calendar_Sync_Settings
 */
class Orddd_Calendar_Sync_Settings {

	/**
	 * Callback for adding Google Calendar Sync General Settings
	 */
	public static function orddd_calendar_sync_general_settings_callback() {}

	/**
	 * Callback for adding the Event Location field in the Google sync settings
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 4.0
	 */
	public static function orddd_calendar_event_location_callback( $args ) {
		$google_calendar_location = get_option( 'orddd_calendar_event_location' );
		?>
		<input size="90" type="text" name="orddd_calendar_event_location" id="orddd_calendar_event_location" value="<?php echo esc_attr( $google_calendar_location ); ?>" />
		<label for="orddd_calendar_event_location"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding the Event Summary name field in the Google sync settings
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 4.0
	 */
	public static function orddd_calendar_event_summary_callback( $args ) {
		$gcal_summary = get_option( 'orddd_calendar_event_summary' );
		echo '<input id="orddd_calendar_event_summary" name="orddd_calendar_event_summary" value="' . esc_attr( $gcal_summary ) . '" size="90" name="gcal_summary" type="text"/>';
	}


	/**
	 * Callback for adding the Event description field in the Google sync settings
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 4.0
	 */
	public static function orddd_calendar_event_description_callback( $args ) {
		$gcal_description = get_option( 'orddd_calendar_event_description' );
		?>
		<textarea id="orddd_calendar_event_description" name="orddd_calendar_event_description" cols="90" rows="4" name="gcal_description"><?php echo wp_kses_post( $gcal_description ); ?></textarea>
		<label for="orddd_calendar_event_description"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Customer Google Calendar sync
	 */
	public static function orddd_calendar_sync_customer_settings_callback() { }

	/**
	 * Callback for adding the Add to Calendar button on Order Received Page
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 4.0
	 */
	public static function orddd_add_to_calendar_order_received_page_callback( $args ) {
		$add_to_calendar_order_received = '';
		if ( 'on' === get_option( 'orddd_add_to_calendar_order_received_page' ) ) {
			$add_to_calendar_order_received = 'checked';
		}

		?>
		<input type="checkbox" name="orddd_add_to_calendar_order_received_page" id="orddd_add_to_calendar_order_received_page" class="day-checkbox" value="on" <?php echo esc_attr( $add_to_calendar_order_received ); ?> />
		<label for="orddd_add_to_calendar_order_received_page"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}


	/**
	 * Callback for adding the Add to Calendar button in the customer notification email
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 4.0
	 */
	public static function orddd_add_to_calendar_customer_email_callback( $args ) {
		$add_to_calendar_customer_email = '';
		if ( 'on' === get_option( 'orddd_add_to_calendar_customer_email' ) ) {
			$add_to_calendar_customer_email = 'checked';
		}
		?>
		<input type="checkbox" name="orddd_add_to_calendar_customer_email" id="orddd_add_to_calendar_customer_email" class="day-checkbox" value="on" <?php echo esc_attr( $add_to_calendar_customer_email ); ?> />
		<label for="orddd_add_to_calendar_customer_email"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding the Add to Calendar button on My Account page
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 4.0
	 */
	public static function orddd_add_to_calendar_my_account_page_callback( $args ) {
		$orddd_add_to_calendar_my_account_page = '';
		if ( 'on' === get_option( 'orddd_add_to_calendar_my_account_page' ) ) {
			$orddd_add_to_calendar_my_account_page = 'checked';
		}
		?>
		<input type="checkbox" name="orddd_add_to_calendar_my_account_page" id="orddd_add_to_calendar_my_account_page" class="day-checkbox" value="on" <?php echo esc_attr( $orddd_add_to_calendar_my_account_page ); ?>/>
		<label for="orddd_add_to_calendar_my_account_page"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback to open the calendar in the same window and tab
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 4.0
	 */
	public static function orddd_calendar_in_same_window_callback( $args ) {
		$google_calendar_same_window = '';
		if ( 'on' === get_option( 'orddd_calendar_in_same_window' ) ) {
			$google_calendar_same_window = 'checked';
		}

		?>
		<input type="checkbox" name="orddd_calendar_in_same_window" id="orddd_calendar_in_same_window" class="day-checkbox" value="on" <?php echo esc_attr( $google_calendar_same_window ); ?> />
		<label for="orddd_calendar_in_same_window"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding admin Google Calendar sync
	 */
	public static function orddd_calendar_sync_admin_settings_section_callback() { }

	/**
	 * Callback to select the type of Calendar sync integration - automatically, manually or disabled
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 4.0
	 */
	public static function orddd_calendar_sync_integration_mode_callback( $args ) {
		$sync_directly = '';
		$sync_manually = '';
		$sync_disable  = 'checked';
		if ( 'manually' === get_option( 'orddd_calendar_sync_integration_mode' ) ) {
			$sync_manually = 'checked';
			$sync_disable  = '';
		} elseif ( 'directly' === get_option( 'orddd_calendar_sync_integration_mode' ) ) {
			$sync_directly = 'checked';
			$sync_disable  = '';
		}

		?>
		<input type="radio" name="orddd_calendar_sync_integration_mode" id="orddd_calendar_sync_integration_mode" value="directly" <?php echo esc_attr( $sync_directly ); ?>/><?php esc_html_e( 'Sync Automatically', 'order-delivery-date' ); ?>&nbsp;&nbsp;
		<input type="radio" name="orddd_calendar_sync_integration_mode" id="orddd_calendar_sync_integration_mode" value="manually" <?php echo esc_attr( $sync_manually ); ?>/><?php esc_html_e( 'Sync Manually', 'order-delivery-date' ); ?>&nbsp;&nbsp;
		<input type="radio" name="orddd_calendar_sync_integration_mode" id="orddd_calendar_sync_integration_mode" value="disabled" <?php echo esc_attr( $sync_disable ); ?> /><?php esc_html_e( 'Disabled', 'order-delivery-date' ); ?>

		<label for="orddd_calendar_sync_integration_mode"><?php echo wp_kses_post( $args[0] ); ?></label>	
		<?php
	}

	/**
	 * Display the stepd for syncing the Google Calendar on clicking 'Show me how'
	 *
	 * @since 4.0
	 */
	public static function orddd_sync_calendar_instructions_callback() {
		esc_html_e( 'To set up Google Calendar API, please click on "Show me how" link and carefully follow these steps:', '' );
		?>
		<span class="description" >
			<a href="#orddd-instructions" id="show_instructions" data-target="api-instructions" class="orddd-info_trigger" title="' . __( 'Click to toggle instructions', 'order-delivery-date' ) . '"><?php esc_html_e( 'Show me how', 'order-delivery-date' ); ?></a>
		</span>
		<div class="description orddd-info_target api-instructions" style="display: none;">
			<ul style="list-style-type:decimal;">
				<li><?php esc_html_e( 'Google Calendar API requires PHP V5.3+ and some PHP extensions.', 'order-delivery-date' ); ?> </li>
				<li><?php esc_html_e( 'Go to Google APIs console by clicking ', 'order-delivery-date' ); ?><a href="https://code.google.com/apis/console/" target="_blank">https://code.google.com/apis/console/</a><?php esc_html_e( '. Login to your Google account if you are not already logged in.', 'order-delivery-date' ); ?></li>
				<li><?php esc_html_e( "Click on 'Create Project'. Name the project 'Deliveries' (or use your chosen name instead) and create the project.", 'order-delivery-date' ); ?></li>
				<li><?php esc_html_e( 'Click on APIs & Services from the left side panel. Select the Project created. ', 'order-delivery-date' ); ?></li>
				<li><?php esc_html_e( "Click on 'Enable APIs and services' on the dashboard. Search for 'Google Calendar API' and enable this API.", 'order-delivery-date' ); ?></li>
				<li><?php esc_html_e( "Go to 'Credentials' menu in the left side pane and click on 'CREATE CREDENTIALS' link and from the dropdown that appears select 'Service account.'", 'order-delivery-date' ); ?></li>
				<li><?php esc_html_e( "Enter Service account name, id, and description and Create the service account.", 'order-delivery-date' ); ?></li>
				<li><?php esc_html_e( "In the next step assign Owner role under Service account permissions, keep options in the third optional step empty and click on Done button.", 'order-delivery-date' ); ?></li>
				<li><?php esc_html_e( "Now edit the Service account that you have created and under the 'Keys' section click on Add Key>> Create New Key, in the popup that opens select 'P12' option and click on the CREATE button. A file with extension .p12 will be downloaded.", 'order-delivery-date' ); ?></li>
				<li>
				<?php
				$uploads_dir = wp_upload_dir();
				esc_html_e( 'Using your FTP client program ( e.g.: ', 'order-delivery-date' );
				?>
				<a href="https://filezilla-project.org/" target="_blank">FileZilla</a>, 
				<a href="https://winscp.net/eng/index.php" target="_blank">WinSCP</a>
				<?php esc_html_e( '), copy this key file to folder:', 'order-delivery-date' ); ?>
				<strong><?php echo esc_url( $uploads_dir['basedir'] ) . '/orddd_uploads/.'; ?></strong>
				<?php esc_html_e( ' This file is required as you will grant access to your Google Calendar account even if you are not online. So this file serves as a proof of your consent to access to your Google calendar account.', 'order-delivery-date' ); ?>
				<br><b><?php esc_html_e( 'Note:', 'order-delivery-date' ); ?></b>
				<?php esc_html_e( 'This file cannot be uploaded in any other way. If you do not have FTP access, ask the website admin to do it for you.', 'order-delivery-date' ); ?>
				</li>
				<li><?php esc_html_e( "Enter the name of the key file to 'Key file name' setting of Order Delivery Date. Exclude the extention .p12.", 'order-delivery-date' ); ?></li>
				<li><?php esc_html_e( "Copy 'Service Account ID' from Manage service account under API service-> Credentials of Google apis console and paste it to 'Service account email address' setting of Order Delivery Date.", 'order-delivery-date' ); ?></li>
				<li><?php esc_html_e( 'Open your Google Calendar by clicking this link: ', 'order-delivery-date' ); ?><a href="https://www.google.com/calendar/render" target="_blank">https://www.google.com/calendar/render</a></li>
				<li><?php esc_html_e( "Create a new Calendar by clicking on '+' sign next to 'Other Calendars' section on left side pane. Try NOT to use your primary calendar.", 'order-delivery-date' ); ?></li>
				<li><?php esc_html_e( 'Give a name to the new calendar, e.g. Order Delivery Date calendar. Check that Calendar Time Zone setting matches with time zone setting of your WordPress website. Otherwise there will be a time shift.', 'order-delivery-date' ); ?></li>		
				<li><?php esc_html_e( "Create the calendar and once it is created click on the Configure link which will appear at the end of the page, this will redirect you to Calendar Settings section. Paste already copied 'Service Account ID' from Manage service account of Google APIs console to 'Add People' field under 'Share with specific people'.", 'order-delivery-date' ); ?></li>
				<li><?php esc_html_e( "Set 'Permission Settings' of this person as 'Make changes to events' and add the person.", 'order-delivery-date' ); ?></li>
				<li><?php esc_html_e( "Now copy 'Calendar ID' value from Integrate Calendar section and paste the value to 'Calendar to be used' field of Order Delivery Date settings.", 'order-delivery-date' ); ?></li>
				<li><?php esc_html_e( "After saving the settings, you can test the connection by clicking on the 'Test Connection' link.", 'order-delivery-date' ); ?></li>
				<li><?php esc_html_e( 'If you get a success message, you should see a test event inserted into the Google Calendar and you are ready to go. If you get an error message, double check your settings.', 'order-delivery-date' ); ?></li>
			</ul>
		</div>
		<?php
	}

	/**
	 * Callback for adding Key File name field to enter the file name without extension
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 4.0
	 */
	public static function orddd_calendar_key_file_name_callback( $args ) {
		$gcal_key_file_arr = get_option( 'orddd_calendar_details_1' );
		$gcal_key_file     = '';
		if ( isset( $gcal_key_file_arr['orddd_calendar_key_file_name'] ) ) {
			$gcal_key_file = $gcal_key_file_arr['orddd_calendar_key_file_name'];
		}
		?>
		<input id="orddd_calendar_details_1[orddd_calendar_key_file_name]" name= "orddd_calendar_details_1[orddd_calendar_key_file_name]" value="<?php echo esc_attr( $gcal_key_file ); ?>" size="90" name="gcal_key_file" type="text" />
		<label for="orddd_calendar_key_file_name"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding the 'Serveice Account Email Address' field in the settings
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 4.0
	 */
	public static function orddd_calendar_service_acc_email_address_callback( $args ) {
		$gcal_service_account_arr = get_option( 'orddd_calendar_details_1' );
		if ( isset( $gcal_service_account_arr['orddd_calendar_service_acc_email_address'] ) ) {
			$gcal_service_account = $gcal_service_account_arr['orddd_calendar_service_acc_email_address'];
		} else {
			$gcal_service_account = '';
		}

		?>
		<input id="orddd_calendar_details_1[orddd_calendar_service_acc_email_address]" name="orddd_calendar_details_1[orddd_calendar_service_acc_email_address]" value="<?php echo esc_attr( $gcal_service_account ); ?>" size="90" name="gcal_service_account" type="text"/>
		<label for="orddd_calendar_service_acc_email_address"><?php echo wp_kses_post( $args[0] ); ?></label>';
		<?php
	}

	/**
	 * Callback for adding the 'Calendar to be used' field in the settings to enter the Calendar ID
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 4.0
	 */
	public static function orddd_calendar_id_callback( $args ) {
		$gcal_selected_calendar_arr = get_option( 'orddd_calendar_details_1' );
		if ( isset( $gcal_selected_calendar_arr['orddd_calendar_id'] ) ) {
			$gcal_selected_calendar = $gcal_selected_calendar_arr['orddd_calendar_id'];
		} else {
			$gcal_selected_calendar = '';
		}

		?>
		<input id="orddd_calendar_details_1[orddd_calendar_id]" name="orddd_calendar_details_1[orddd_calendar_id]" value="<?php echo esc_attr( $gcal_selected_calendar ); ?>" size="90" name="gcal_selected_calendar" type="text" />
		<label for="orddd_calendar_id"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding the 'Test Connection' link and checks if the connection is succesful or not
	 *
	 * @since 4.0
	 */
	public static function orddd_calendar_test_connection_callback() {
		print "<a href='admin.php?page=order_delivery_date&action=calendar_sync_settings' id='test_connection'>" . esc_html__( 'Test Connection', 'order-delivery-date' ) . "</a> 
			<img src='" . esc_url( plugins_url() ) . "/order-delivery-date/images/ajax-loader.gif' id='test_connection_ajax_loader'>";
		print "<div id='test_connection_message'></div>";
	}

	/**
	 * Callback for adding the 'Add to Calendar' button in the New Order email notification
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 4.0
	 */
	public static function orddd_admin_add_to_calendar_email_notification_callback( $args ) {
		$orddd_admin_add_to_calendar_email_notification = '';
		if ( 'on' === get_option( 'orddd_admin_add_to_calendar_email_notification' ) ) {
			$orddd_admin_add_to_calendar_email_notification = 'checked';
		}
		?>
		<input type="checkbox" name="orddd_admin_add_to_calendar_email_notification" id="orddd_admin_add_to_calendar_email_notification" value="on" <?php echo esc_attr( $orddd_admin_add_to_calendar_email_notification ); ?> />
		<label for="orddd_admin_add_to_calendar_email_notification"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding the 'Add to Calendar' button in the admin Delivery Calendar page
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 4.0
	 */
	public static function orddd_admin_add_to_calendar_delivery_calendar_callback( $args ) {
		$orddd_admin_add_to_calendar_view_deliveries = '';
		if ( 'on' === get_option( 'orddd_admin_add_to_calendar_delivery_calendar' ) ) {
			$orddd_admin_add_to_calendar_view_deliveries = 'checked';
		}
		?>
		<input type="checkbox" name="orddd_admin_add_to_calendar_delivery_calendar" id="orddd_admin_add_to_calendar_delivery_calendar" value="on" <?php echo esc_attr( $orddd_admin_add_to_calendar_view_deliveries ); ?> />
		<label for="orddd_admin_add_to_calendar_delivery_calendar"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Display the description for the Import Events section
	 *
	 * @since 4.0
	 */
	public static function orddd_calendar_import_ics_feeds_section_callback() {
		_e( 'Events will be imported using the ICS Feed url. Each event will create a new WooCommerce Order. The event\'s date & time will be set as that order\'s Delivery Date & Time. <br>Lockout will be updated for global settings for the set Delivery Date & Time.', 'order-delivery-date' );
	}

	/**
	 * Callback for adding instructions to set up Import events using ics feed urls
	 *
	 * @since 4.0
	 */
	public static function orddd_ics_feed_url_instructions_callback() {
		esc_html_e( 'To set up Import events using ics feed urls, please click on "Show me how" link and carefully follow these steps:', 'order-delivery-date' );
		?>
		<span class="ics-feed-description" >
			<a href="#orddd-ics-feed-instructions" id="show_instructions" data-target="api-instructions" class="orddd_ics_feed-info_trigger" title="<?php esc_html_e( 'Click to toggle instructions', 'order-delivery-date' ); ?>"><?php esc_html_e( 'Show me how', 'order-delivery-date' ); ?></a>
		</span>
		<div class="ics-feed-description orddd_ics_feed-info_target api-instructions" style="display: none;">
			<ul style="list-style-type:decimal;">
				<li><?php esc_html_e( 'Open your Google Calendar by clicking this link:', 'order-delivery-date' ); ?><a href="https://www.google.com/calendar/render" target="_blank">https://www.google.com/calendar/render</a></li>
				<li><?php esc_html_e( 'Select the calendar to be imported and click "Calendar settings".', 'order-delivery-date' ); ?></li>
				<li><?php esc_html_e( 'Click on "ICAL" button in Calendar Address option.', 'order-delivery-date' ); ?></li>		
				<li><?php esc_html_e( 'Copy the basic.ics file URL. <i>If you are importing events from a private calendar please copy the basic.ics file URL for private calendar.</i>', 'order-delivery-date' ); ?></li>
				<li><?php esc_html_e( 'Paste this link in the text box under Google Calendar Sync tab -> Import Events section.', 'order-delivery-date' ); ?></li>
				<li><?php esc_html_e( 'Save the URL.', 'order-delivery-date' ); ?></li>
				<li><?php esc_html_e( 'Click on "Import Events" button to import the events from the calendar.', 'order-delivery-date' ); ?></li>
				<li><?php esc_html_e( 'You can import multiple calendars by using ics feeds. Add them using the Add New Ics Feed url button.', 'order-delivery-date' ); ?></li>
			</ul>
		</div>
		<?php
	}

	/**
	 * Callback for adding the 'iCalendar/.ics Feed URL' field in the Import Events section
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 4.0
	 */
	public static function orddd_ics_feed_url_callback( $args ) {
		echo '<table id="orddd_ics_url_list">';
		$ics_feed_urls = get_option( 'orddd_ics_feed_urls' );
		if ( '' === $ics_feed_urls ||
			'{}' === $ics_feed_urls ||
			'[]' === $ics_feed_urls ||
			'null' === $ics_feed_urls ) {
			$ics_feed_urls = array();
		}

		if ( is_array( $ics_feed_urls ) && count( $ics_feed_urls ) > 0 ) {
			foreach ( $ics_feed_urls as $key => $value ) {
				?>
				<tr id="<?php echo esc_attr( $key ); ?>">
					<td class='ics_feed_url'>
						<input type='text' id='orddd_ics_fee_url_$key' size='60' value='<?php echo esc_attr( $value ); ?>'>
					</td>
					<td class='ics_feed_url'>
						<input type='button' value='Save' id='save_ics_url' class='save_button' name='<?php echo esc_attr( $key ); ?>' disabled='disabled'>
					</td>
					<td class='ics_feed_url'>
						<input type='button' class='save_button' id='<?php echo esc_attr( $key ); ?>' name='import_ics' value='Import Events'>
					</td>
					<td class='ics_feed_url'>
						<input type='button' class='save_button' id='<?php echo esc_attr( $key ); ?>' value='Delete' name='delete_ics_feed'>
					</td>
					<td class='ics_feed_url'>
						<div id='import_event_message'>
							<img src='<?php echo esc_url( plugins_url() ); ?>/order-delivery-date/images/ajax-loader.gif'>
						</div>
						<div id='success_message' ></div>
					</td>
				</tr>
				<?php
			}
		} else {
			echo "<tr id='0' >
				<td class='ics_feed_url'>
					<input type='text' id='orddd_ics_fee_url_0' size='60' >
				</td>
				<td class='ics_feed_url'>
					<input type='button' value='Save' id='save_ics_url' class='save_button' name='0' >
				</td>
				<td class='ics_feed_url'>
					<input type='button' class='save_button' id='0' name='import_ics' value='Import Events' disabled='disabled'>
				</td>
				<td class='ics_feed_url'>
					<input type='button' class='save_button' id='0' name='delete_ics_feed' value='Delete' disabled='disabled'>
				</td>
				<td class='ics_feed_url'>
					<div id='import_event_message'>
						<img src='" . esc_url( plugins_url() ) . "/order-delivery-date/images/ajax-loader.gif'>
					</div>
					<div id='success_message' ></div>
				</td>
			</tr>";
		}
		echo '</table>';

		echo "<input type='button' class='save_button' id='add_new_ics_feed' name='add_new_ics_feed' value='" . esc_html__( 'Add New Ics feed url', 'order-delivery-date' ) . "'>";
	}

	/**
	 * Callback for adding 'Real Time Import' checkbox in the Import Events section.
	 *
	 * @param array $args Extra arguments containing label & class for the checkbox.
	 * @since 9.7
	 */
	public static function orddd_real_time_import_callback( $args ) {
		$orddd_real_time_import = '';
		if ( 'on' === get_option( 'orddd_real_time_import' ) ) {
			$orddd_real_time_import = 'checked';
		}

		?>
		<input type="checkbox" name="orddd_real_time_import" id="orddd_real_time_import" class="day-checkbox" value="on" <?php echo esc_attr( $orddd_real_time_import ); ?> />
		<label for="orddd_real_time_import"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for accepting minutes for automated WP cron to import events from google calendar.
	 *
	 * @param array $args Extra arguments containing label & class for the minutes google calendar text box.
	 * @since 9.7
	 */
	public static function orddd_wp_cron_minutes_callback( $args ) {
		$orddd_wp_cron_minutes = get_option( 'orddd_wp_cron_minutes' );
		if ( '' === $orddd_wp_cron_minutes ) {
			$orddd_wp_cron_minutes = '10';
		}
		?>
		<input id="orddd_wp_cron_minutes" name= "orddd_wp_cron_minutes" value="<?php echo esc_attr( $orddd_wp_cron_minutes ); ?>" type="text" />
		<label for="orddd_wp_cron_minutes"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}
	
	/**
	 * Update the Schedule Action frequency when the same is updated in the settings.
	 *
	 * @param string $old_value - Old Value of the setting.
	 * @param string $new_value - New Value of the setting.
	 * @since 9.16.0
	 */
	public static function orddd_update_wp_cron_minutes( $old_value, $new_value ) {
		
		if ( 'on' === get_option( 'orddd_real_time_import', '' ) ) {
			// Now if there's an action scheduled, it needs to be updated with the new frequency.
			if ( false !== as_next_scheduled_action( 'orddd_import_events' ) ) {
				as_unschedule_action( 'orddd_import_events' );
			}
			$new_value = $new_value > 0 ? intval( $new_value ) : 10;
			if ( is_integer( $new_value ) && 0 < $new_value ) {
				$new_interval = $new_value * 60;
			}
			
			as_schedule_recurring_action( time(), $new_interval, 'orddd_import_events' );
			
		}
	}

	/**
	 * Add/Remove the scheduled action based on the setting.
	 *
	 * @param string $old_value - Old Value of the setting.
	 * @param string $new_value - New Value of the setting.
	 *
	 * @since 9.16.0
	 */
	public static function orddd_update_real_time_import( $old_value, $new_value ) {
	
		// Now if there's an action scheduled, it needs to be removed.
		if ( false !== as_next_scheduled_action( 'orddd_import_events' ) ) {
			as_unschedule_action( 'orddd_import_events' );
		}
		$cron_interval = 'on' === $new_value ? intval( get_option( 'orddd_wp_cron_minutes', 10 ) ) * 60 : 86400;
		as_schedule_recurring_action( time(), $cron_interval, 'orddd_import_events' );
		
	}

}
?>

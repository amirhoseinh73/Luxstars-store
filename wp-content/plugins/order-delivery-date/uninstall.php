<?php
/**
 * Order Delivery Date Uninstall
 *
 * Deletes all the settings for the plugin from the database when plugin is uninstalled.
 *
 * @author      Tyche Softwares
 * @category    Core
 * @package     Order-Delivery-Date-Pro-for-WooCommerce/Uninstall
 * @version     7.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$orddd_weekdays = array(
	'orddd_weekday_0' => __( 'Sunday', 'order-delivery-date' ),
	'orddd_weekday_1' => __( 'Monday', 'order-delivery-date' ),
	'orddd_weekday_2' => __( 'Tuesday', 'order-delivery-date' ),
	'orddd_weekday_3' => __( 'Wednesday', 'order-delivery-date' ),
	'orddd_weekday_4' => __( 'Thursday', 'order-delivery-date' ),
	'orddd_weekday_5' => __( 'Friday', 'order-delivery-date' ),
	'orddd_weekday_6' => __( 'Saturday', 'order-delivery-date' ),
);

$orddd_shipping_days = array(
	'orddd_shipping_day_0' => __( 'Sunday', 'order-delivery-date' ),
	'orddd_shipping_day_1' => __( 'Monday', 'order-delivery-date' ),
	'orddd_shipping_day_2' => __( 'Tuesday', 'order-delivery-date' ),
	'orddd_shipping_day_3' => __( 'Wednesday', 'order-delivery-date' ),
	'orddd_shipping_day_4' => __( 'Thursday', 'order-delivery-date' ),
	'orddd_shipping_day_5' => __( 'Friday', 'order-delivery-date' ),
	'orddd_shipping_day_6' => __( 'Saturday', 'order-delivery-date' ),
);

if ( is_multisite() ) {
	$orddd_blog_list = get_sites();

	foreach ( $orddd_blog_list as $orddd_blog_list_key => $orddd_blog_list_value ) {
		$orddd_blog_id = $orddd_blog_list_value->blog_id;

		if ( $orddd_blog_id > 1 ) {
			$wpdb_prefix = $wpdb->prefix . $orddd_blog_id . '_';
		} else {
			$wpdb_prefix = $wpdb->prefix;
		}

		delete_blog_option( $orddd_blog_id, 'orddd_db_version' );
		delete_blog_option( $orddd_blog_id, 'orddd_enable_delivery_date' );
		delete_blog_option( $orddd_blog_id, 'orddd_minimumOrderDays' );
		delete_blog_option( $orddd_blog_id, 'orddd_number_of_dates' );
		delete_blog_option( $orddd_blog_id, 'orddd_date_field_mandatory' );
		delete_blog_option( $orddd_blog_id, 'orddd_lockout_date_after_orders' );
		delete_blog_option( $orddd_blog_id, 'orddd_lockout_date_quantity_based' );
		delete_blog_option( $orddd_blog_id, 'orddd_lockout_days' );
		delete_blog_option( $orddd_blog_id, 'orddd_global_lockout_custom' );
		delete_blog_option( $orddd_blog_id, 'orddd_show_fields_in_csv_export_check' );
		delete_blog_option( $orddd_blog_id, 'orddd_show_fields_in_pdf_invoice_and_packing_slips' );
		delete_blog_option( $orddd_blog_id, 'orddd_show_fields_in_invoice_and_delivery_note' );
		delete_blog_option( $orddd_blog_id, 'orddd_show_fields_in_cloud_print_orders' );
		delete_blog_option( $orddd_blog_id, 'orddd_show_filter_on_orders_page_check' );
		delete_blog_option( $orddd_blog_id, 'orddd_show_column_on_orders_page_check' );
		delete_blog_option( $orddd_blog_id, 'orddd_enable_default_sorting_of_column' );
		delete_blog_option( $orddd_blog_id, 'orddd_enable_tax_calculation_for_delivery_charges' );
		delete_blog_option( $orddd_blog_id, 'orddd_amazon_payments_advanced_gateway_compatibility' );

		delete_blog_option( $orddd_blog_id, 'orddd_enable_shipping_days' );
		delete_blog_option( $orddd_blog_id, 'orddd_business_opening_time' );
		delete_blog_option( $orddd_blog_id, 'orddd_business_closing_time' );

		// Time options.
		delete_blog_option( $orddd_blog_id, 'orddd_enable_delivery_time' );
		delete_blog_option( $orddd_blog_id, 'orddd_delivery_from_hours' );
		delete_blog_option( $orddd_blog_id, 'orddd_delivery_to_hours' );
		delete_blog_option( $orddd_blog_id, 'orddd_delivery_time_format' );

		// Same day delivery options.
		delete_blog_option( $orddd_blog_id, 'orddd_enable_same_day_delivery' );
		delete_blog_option( $orddd_blog_id, 'orddd_disable_same_day_delivery_after_hours' );
		delete_blog_option( $orddd_blog_id, 'orddd_disable_same_day_delivery_after_minutes' );
		delete_blog_option( $orddd_blog_id, 'orddd_same_day_additional_charges' );

		// Next day delivery options.
		delete_blog_option( $orddd_blog_id, 'orddd_enable_next_day_delivery' );
		delete_blog_option( $orddd_blog_id, 'orddd_disable_next_day_delivery_after_hours' );
		delete_blog_option( $orddd_blog_id, 'orddd_disable_next_day_delivery_after_minutes' );
		delete_blog_option( $orddd_blog_id, 'orddd_next_day_additional_charges' );

		// Appearance options.
		delete_blog_option( $orddd_blog_id, 'orddd_delivery_date_field_label' );
		delete_blog_option( $orddd_blog_id, 'orddd_delivery_date_field_placeholder' );
		delete_blog_option( $orddd_blog_id, 'orddd_delivery_date_field_note' );
		delete_blog_option( $orddd_blog_id, 'orddd_delivery_date_format' );
		delete_blog_option( $orddd_blog_id, 'orddd_number_of_months' );
		delete_blog_option( $orddd_blog_id, 'orddd_calendar_theme' );
		delete_blog_option( $orddd_blog_id, 'orddd_calendar_theme_name' );
		delete_blog_option( $orddd_blog_id, 'orddd_language_selected' );
		delete_blog_option( $orddd_blog_id, 'orddd_delivery_date_fields_on_checkout_page' );
		delete_blog_option( $orddd_blog_id, 'orddd_no_fields_for_virtual_product' );
		delete_blog_option( $orddd_blog_id, 'orddd_custom_hook_for_fields_placement' );
		delete_blog_option( $orddd_blog_id, 'orddd_location_field_label' );
		delete_blog_option( $orddd_blog_id, 'orddd_calendar_display_mode' );
		delete_blog_option( $orddd_blog_id, 'orddd_delivery_dates_in_dropdown' );

		// Holiday options.
		delete_blog_option( $orddd_blog_id, 'orddd_delivery_date_holidays' );

		// Specific delivery dates.
		delete_blog_option( $orddd_blog_id, 'orddd_enable_specific_delivery_dates' );
		delete_blog_option( $orddd_blog_id, 'orddd_delivery_dates' );
		delete_blog_option( $orddd_blog_id, 'additional_charges_1' );
		delete_blog_option( $orddd_blog_id, 'additional_charges_2' );
		delete_blog_option( $orddd_blog_id, 'additional_charges_3' );
		delete_blog_option( $orddd_blog_id, 'specific_charges_label_1' );
		delete_blog_option( $orddd_blog_id, 'specific_charges_label_2' );
		delete_blog_option( $orddd_blog_id, 'specific_charges_label_3' );

		// Time slot.
		delete_blog_option( $orddd_blog_id, 'orddd_delivery_time_slot_log' );
		delete_blog_option( $orddd_blog_id, 'orddd_lockout_time_slot' );
		delete_blog_option( $orddd_blog_id, 'orddd_enable_time_slot' );
		delete_blog_option( $orddd_blog_id, 'orddd_time_slot_mandatory', '' );
		delete_blog_option( $orddd_blog_id, 'orddd_delivery_timeslot_field_label', '' );
		delete_blog_option( $orddd_blog_id, 'orddd_specific_array_format', '' );
		delete_blog_option( $orddd_blog_id, 'orddd_delivery_timeslot_format' );
		delete_blog_option( $orddd_blog_id, 'orddd_show_first_available_time_slot_as_selected' );
		delete_blog_option( $orddd_blog_id, 'orddd_global_lockout_time_slots' );
		delete_blog_option( $orddd_blog_id, 'orddd_auto_populate_first_available_time_slot' );
		delete_blog_option( $orddd_blog_id, 'orddd_time_slots_in_list_view' );

		// Additional settings.
		delete_blog_option( $orddd_blog_id, 'orddd_enable_autofill_of_delivery_date' );
		delete_blog_option( $orddd_blog_id, 'orddd_enable_availability_display' );
		delete_blog_option( $orddd_blog_id, 'orddd_enable_availability_display_update' );
		delete_blog_option( $orddd_blog_id, 'orddd_allow_tracking' );
		delete_blog_option( $orddd_blog_id, 'orddd_show_partially_booked_dates' );
		delete_blog_option( $orddd_blog_id, 'orddd_show_partially_booked_dates_update' );
		delete_blog_option( $orddd_blog_id, 'orddd_abp_hrs' );
		delete_blog_option( $orddd_blog_id, 'update_weekdays_value' );
		delete_blog_option( $orddd_blog_id, 'orddd_add_delivery_in_order_notes' );
		delete_blog_option( $orddd_blog_id, 'orddd_auto_populate_first_pickup_location' );

		// Settings by Shipping methods.
		delete_blog_option( $orddd_blog_id, 'orddd_enable_shipping_based_delivery' );
		delete_blog_option( $orddd_blog_id, 'orddd_shipping_based_settings_option_key' );

		// Google Calendar Sync settings.
		delete_blog_option( $orddd_blog_id, 'orddd_calendar_event_location' );
		delete_blog_option( $orddd_blog_id, 'orddd_add_to_calendar_order_received_page' );
		delete_blog_option( $orddd_blog_id, 'orddd_add_to_calendar_customer_email' );
		delete_blog_option( $orddd_blog_id, 'orddd_add_to_calendar_my_account_page' );
		delete_blog_option( $orddd_blog_id, 'orddd_calendar_in_same_window' );
		delete_blog_option( $orddd_blog_id, 'orddd_calendar_sync_integration_mode' );
		delete_blog_option( $orddd_blog_id, 'orddd_calendar_event_summary' );
		delete_blog_option( $orddd_blog_id, 'orddd_calendar_event_description' );
		delete_blog_option( $orddd_blog_id, 'orddd_admin_add_to_calendar_email_notification' );
		delete_blog_option( $orddd_blog_id, 'orddd_admin_add_to_calendar_delivery_calendar' );
		delete_blog_option( $orddd_blog_id, 'orddd_calendar_details_1' );
		delete_blog_option( $orddd_blog_id, 'orddd_ics_feed_urls' );

		// Extra Options.
		delete_blog_option( $orddd_blog_id, 'update_time_slot_log_for_tv' );
		delete_blog_option( $orddd_blog_id, 'orddd_abp_hrs' );
		delete_blog_option( $orddd_blog_id, 'update_weekdays_value' );
		delete_blog_option( $orddd_blog_id, 'update_delivery_product_category' );
		delete_blog_option( $orddd_blog_id, 'update_placeholder_value' );
		delete_blog_option( $orddd_blog_id, 'orddd_update_shipping_delivery_settings_based' );
		delete_blog_option( $orddd_blog_id, 'orddd_update_time_slot_for_shipping_delivery' );
		delete_blog_option( $orddd_blog_id, 'orddd_default_sorting' );
		delete_blog_option( $orddd_blog_id, 'orddd_tax_calculation_enabled' );
		delete_blog_option( $orddd_blog_id, 'orddd_delivery_date_on_checkout_page_enabled' );
		delete_blog_option( $orddd_blog_id, 'orddd_update_additional_charges_records' );
		delete_blog_option( $orddd_blog_id, 'orddd_update_time_format' );
		delete_blog_option( $orddd_blog_id, 'orddd_update_auto_populate_first_available_time_slot' );
		delete_blog_option( $orddd_blog_id, 'orddd_update_shipping_method_id' );
		delete_blog_option( $orddd_blog_id, 'orddd_update_shipping_method_id_delete' );

		delete_blog_option( $orddd_blog_id, 'orddd_delivery_checkout_options' );
		delete_blog_option( $orddd_blog_id, 'orddd_advance_settings' );
		delete_blog_option( $orddd_blog_id, 'orddd_update_advance_settings' );
		delete_blog_option( $orddd_blog_id, 'orddd_update_delivery_checkout_options' );

		delete_blog_option( $orddd_blog_id, 'orddd_enable_day_wise_settings' );
		delete_blog_option( $orddd_blog_id, 'orddd_min_between_days' );
		delete_blog_option( $orddd_blog_id, 'orddd_max_between_days' );
		delete_blog_option( $orddd_blog_id, 'orddd_time_slot_for_delivery_days' );
		delete_blog_option( $orddd_blog_id, 'orddd_disable_time_slot_log' );
		delete_blog_option( $orddd_blog_id, 'orddd_delivery_date_on_cart_page' );
		delete_blog_option( $orddd_blog_id, 'orddd_no_fields_for_featured_product' );
		delete_blog_option( $orddd_blog_id, 'orddd_allow_customers_to_edit_date' );
		delete_blog_option( $orddd_blog_id, 'orddd_disable_edit_after_cutoff' );
		delete_blog_option( $orddd_blog_id, 'orddd_send_email_to_admin_when_date_updated' );
		delete_blog_option( $orddd_blog_id, 'orddd_shipping_multiple_address_compatibility' );

		delete_blog_option( $orddd_blog_id, 'widget_orddd_availability_widget' );
		delete_blog_option( $orddd_blog_id, 'orddd_update_holiday_type' );
		delete_blog_option( $orddd_blog_id, 'orddd_location_field_label' );
		delete_blog_option( $orddd_blog_id, 'orddd_update_location_label' );

		delete_blog_option( $orddd_blog_id, 'orddd_pro_welcome_page_shown' );
		delete_blog_option( $orddd_blog_id, 'orddd_pro_welcome_page_shown_time' );
		delete_blog_option( $orddd_blog_id, 'orddd_installation_wizard_license_key' );

		delete_blog_option( $orddd_blog_id, 'orddd_pro_installed' );
		delete_blog_option( $orddd_blog_id, 'orddd_cut_off_time_color' );
		delete_blog_option( $orddd_blog_id, 'orddd_booked_dates_color' );
		delete_blog_option( $orddd_blog_id, 'orddd_holiday_color' );
		delete_blog_option( $orddd_blog_id, 'orddd_available_dates_color' );
		delete_blog_option( $orddd_blog_id, 'orddd_update_holiday_type' );

		delete_blog_option( $orddd_blog_id, 'edd_sample_license_key_odd_woo' );
		delete_blog_option( $orddd_blog_id, 'edd_sample_license_status_odd_woo' );

		delete_blog_option( $orddd_blog_id, 'orddd_locations' );
		delete_blog_option( $orddd_blog_id, 'orddd_modify_admin_reminder_cron' );

		delete_blog_option( $orddd_blog_id, 'orddd_lite_data_imported' );
		delete_blog_option( $orddd_blog_id, 'orddd_import_page_displayed' );

		// Date options.
		foreach ( $orddd_weekdays as $n => $day_name ) {
			delete_blog_option( $orddd_blog_id, $n );
			delete_blog_option( $orddd_blog_id, 'additional_charges_' . $n );
			delete_blog_option( $orddd_blog_id, 'delivery_charges_label_' . $n );
		}

		// Shipping days.
		foreach ( $orddd_shipping_days as $n => $day_name ) {
			delete_blog_option( $orddd_blog_id, $n );
		}

		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb_prefix}termmeta'; " ) ) {
			$categories = $wpdb->get_results(
				$wpdb->prepare(
					"DELETE FROM `{$wpdb_prefix}termmeta` WHERE meta_key = %s",
					'orddd_delivery_date_for_product_category'
				)
			);  // WPCS: db call ok, WPCS: cache ok.
		}
	}
} else {
	delete_option( 'orddd_db_version' );

	// Date options.
	foreach ( $orddd_weekdays as $n => $day_name ) {
		delete_option( $n );
		delete_option( 'additional_charges_' . $n );
		delete_option( 'delivery_charges_label_' . $n );
	}

	delete_option( 'orddd_enable_delivery_date' );
	delete_option( 'orddd_minimumOrderDays' );
	delete_option( 'orddd_number_of_dates' );
	delete_option( 'orddd_date_field_mandatory' );
	delete_option( 'orddd_lockout_date_after_orders' );
	delete_option( 'orddd_lockout_date_quantity_based' );
	delete_option( 'orddd_lockout_days' );
	delete_option( 'orddd_global_lockout_custom' );
	delete_option( 'orddd_show_fields_in_csv_export_check' );
	delete_option( 'orddd_show_fields_in_pdf_invoice_and_packing_slips' );
	delete_option( 'orddd_show_fields_in_invoice_and_delivery_note' );
	delete_option( 'orddd_show_fields_in_cloud_print_orders' );
	delete_option( 'orddd_show_filter_on_orders_page_check' );
	delete_option( 'orddd_show_column_on_orders_page_check' );
	delete_option( 'orddd_enable_default_sorting_of_column' );
	delete_option( 'orddd_enable_tax_calculation_for_delivery_charges' );
	delete_option( 'orddd_amazon_payments_advanced_gateway_compatibility' );

	// Shipping days.
	foreach ( $orddd_shipping_days as $n => $day_name ) {
		delete_option( $n );
	}
	delete_option( 'orddd_enable_shipping_days' );
	delete_option( 'orddd_business_opening_time' );
	delete_option( 'orddd_business_closing_time' );

	// Time options.
	delete_option( 'orddd_enable_delivery_time' );
	delete_option( 'orddd_delivery_from_hours' );
	delete_option( 'orddd_delivery_to_hours' );
	delete_option( 'orddd_delivery_time_format' );

	// Same day delivery options.
	delete_option( 'orddd_enable_same_day_delivery' );
	delete_option( 'orddd_disable_same_day_delivery_after_hours' );
	delete_option( 'orddd_disable_same_day_delivery_after_minutes' );
	delete_option( 'orddd_same_day_additional_charges' );

	// Next day delivery options.
	delete_option( 'orddd_enable_next_day_delivery' );
	delete_option( 'orddd_disable_next_day_delivery_after_hours' );
	delete_option( 'orddd_disable_next_day_delivery_after_minutes' );
	delete_option( 'orddd_next_day_additional_charges' );

	// Appearance options.
	delete_option( 'orddd_delivery_date_field_label' );
	delete_option( 'orddd_delivery_date_field_placeholder' );
	delete_option( 'orddd_delivery_date_field_note' );
	delete_option( 'orddd_delivery_date_format' );
	delete_option( 'orddd_number_of_months' );
	delete_option( 'orddd_calendar_theme' );
	delete_option( 'orddd_calendar_theme_name' );
	delete_option( 'orddd_language_selected' );
	delete_option( 'orddd_delivery_date_fields_on_checkout_page' );
	delete_option( 'orddd_no_fields_for_virtual_product' );
	delete_option( 'orddd_custom_hook_for_fields_placement' );
	delete_option( 'orddd_location_field_label' );
	delete_option( 'orddd_calendar_display_mode' );
	delete_option( 'orddd_delivery_dates_in_dropdown' );

	// Holiday options.
	delete_option( 'orddd_delivery_date_holidays' );

	// Specific delivery dates.
	delete_option( 'orddd_enable_specific_delivery_dates' );
	delete_option( 'orddd_delivery_dates' );
	delete_option( 'additional_charges_1' );
	delete_option( 'additional_charges_2' );
	delete_option( 'additional_charges_3' );
	delete_option( 'specific_charges_label_1' );
	delete_option( 'specific_charges_label_2' );
	delete_option( 'specific_charges_label_3' );

	// Time slot.
	delete_option( 'orddd_delivery_time_slot_log' );
	delete_option( 'orddd_lockout_time_slot' );
	delete_option( 'orddd_enable_time_slot' );
	delete_option( 'orddd_time_slot_mandatory', '' );
	delete_option( 'orddd_delivery_timeslot_field_label', '' );
	delete_option( 'orddd_specific_array_format', '' );
	delete_option( 'orddd_delivery_timeslot_format' );
	delete_option( 'orddd_show_first_available_time_slot_as_selected' );
	delete_option( 'orddd_global_lockout_time_slots' );
	delete_option( 'orddd_auto_populate_first_available_time_slot' );
	delete_option( 'orddd_time_slots_in_list_view' );

	// Additional settings.
	delete_option( 'orddd_enable_autofill_of_delivery_date' );
	delete_option( 'orddd_enable_availability_display' );
	delete_option( 'orddd_enable_availability_display_update' );
	delete_option( 'orddd_allow_tracking' );
	delete_option( 'orddd_show_partially_booked_dates' );
	delete_option( 'orddd_show_partially_booked_dates_update' );
	delete_option( 'orddd_add_delivery_in_order_notes' );
	delete_option( 'orddd_auto_populate_first_pickup_location' );

	delete_option( 'orddd_abp_hrs' );
	delete_option( 'update_weekdays_value' );

	// Settings by Shipping methods.
	delete_option( 'orddd_enable_shipping_based_delivery' );
	delete_option( 'orddd_shipping_based_settings_option_key' );

	require_once 'orddd-common.php';
	$filter_inactive_schedules = 0;
	$results                   = orddd_common::orddd_get_shipping_settings( $filter_inactive_schedules );
	foreach ( $results as $key => $value ) {
		delete_option( $value->option_name );

		// Remove options for inactive custom settings too.
		$str = explode( '_', $value->option_name );
		delete_option( 'orddd_shipping_settings_status_' . $str[4] );
	}

	// Google Calendar Sync settings.
	delete_option( 'orddd_calendar_event_location' );
	delete_option( 'orddd_add_to_calendar_order_received_page' );
	delete_option( 'orddd_add_to_calendar_customer_email' );
	delete_option( 'orddd_add_to_calendar_my_account_page' );
	delete_option( 'orddd_calendar_in_same_window' );
	delete_option( 'orddd_calendar_sync_integration_mode' );
	delete_option( 'orddd_calendar_event_summary' );
	delete_option( 'orddd_calendar_event_description' );
	delete_option( 'orddd_admin_add_to_calendar_email_notification' );
	delete_option( 'orddd_admin_add_to_calendar_delivery_calendar' );
	delete_option( 'orddd_calendar_details_1' );
	delete_option( 'orddd_ics_feed_urls' );

	// Extra Options.
	delete_option( 'update_time_slot_log_for_tv' );
	delete_option( 'orddd_abp_hrs' );
	delete_option( 'update_weekdays_value' );
	delete_option( 'update_delivery_product_category' );
	delete_option( 'update_placeholder_value' );
	delete_option( 'orddd_update_shipping_delivery_settings_based' );
	delete_option( 'orddd_update_time_slot_for_shipping_delivery' );
	delete_option( 'orddd_default_sorting' );
	delete_option( 'orddd_tax_calculation_enabled' );
	delete_option( 'orddd_delivery_date_on_checkout_page_enabled' );
	delete_option( 'orddd_update_additional_charges_records' );
	delete_option( 'orddd_update_time_format' );
	delete_option( 'orddd_update_auto_populate_first_available_time_slot' );
	delete_option( 'orddd_update_shipping_method_id' );
	delete_option( 'orddd_update_shipping_method_id_delete' );

	delete_option( 'orddd_delivery_checkout_options' );
	delete_option( 'orddd_advance_settings' );
	delete_option( 'orddd_update_advance_settings' );
	delete_option( 'orddd_update_delivery_checkout_options' );

	delete_option( 'orddd_enable_day_wise_settings' );
	delete_option( 'orddd_min_between_days' );
	delete_option( 'orddd_max_between_days' );
	delete_option( 'orddd_time_slot_for_delivery_days' );
	delete_option( 'orddd_disable_time_slot_log' );
	delete_option( 'orddd_delivery_date_on_cart_page' );
	delete_option( 'orddd_no_fields_for_featured_product' );
	delete_option( 'orddd_allow_customers_to_edit_date' );
	delete_option( 'orddd_disable_edit_after_cutoff' );
	delete_option( 'orddd_send_email_to_admin_when_date_updated' );
	delete_option( 'orddd_shipping_multiple_address_compatibility' );

	delete_option( 'widget_orddd_availability_widget' );
	delete_option( 'orddd_update_holiday_type' );
	delete_option( 'orddd_location_field_label' );
	delete_option( 'orddd_update_location_label' );

	delete_option( 'orddd_pro_welcome_page_shown' );
	delete_option( 'orddd_pro_welcome_page_shown_time' );
	delete_option( 'orddd_installation_wizard_license_key' );

	delete_option( 'orddd_pro_installed' );
	delete_option( 'orddd_cut_off_time_color' );
	delete_option( 'orddd_booked_dates_color' );
	delete_option( 'orddd_holiday_color' );
	delete_option( 'orddd_available_dates_color' );
	delete_option( 'orddd_update_holiday_type' );

	delete_option( 'edd_sample_license_key_odd_woo' );
	delete_option( 'edd_sample_license_status_odd_woo' );

	delete_option( 'orddd_locations' );
	delete_option( 'orddd_modify_admin_reminder_cron' );

	delete_option( 'orddd_lite_data_imported' );
	delete_option( 'orddd_import_page_displayed' );

	do_action( 'orddd_plugin_deactivate' );

	$categories = array();
	if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}term_taxonomy'; " ) ) {
		$categories = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT term_id FROM `{$wpdb->prefix}term_taxonomy` WHERE taxonomy = %s",
				'product_cat'
			)
		); // WPCS: db call ok, WPCS: cache ok.
	}

	foreach ( $categories as $category_id ) {
		delete_term_meta( $category_id->term_id, 'orddd_delivery_date_for_product_category' );
	}
}


// Unschedule events that have been setup.
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


<?php
/**
 * Order Delivery Date Settings
 *
 * It has all the settings declarations in the admin.
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Pro-for-WooCommerce/Admin/Settings
 * @since 2.8.3
 * @category Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once 'includes/class-orddd-date-settings.php';
require_once 'includes/class-orddd-shipping-days-settings.php';
require_once 'includes/class-orddd-time-settings.php';
require_once 'includes/class-orddd-holidays-settings.php';
require_once 'includes/class-orddd-appearance-settings.php';
require_once 'includes/orddd-delivery-days-settings.php';
require_once 'includes/class-orddd-time-slot-settings.php';
require_once 'includes/class-orddd-additional-settings.php';
require_once 'includes/class-orddd-shipping-based-settings.php';
require_once 'includes/class-orddd-calendar-sync-settings.php';

/**
 * Add order delivery date settings. 
 *
 * @since 2.8.3
 */
class orddd_settings {

	/**
	 * Default Constructor
	 *
	 * @since 2.8.3
	 */
	public function __construct() {
		add_action( 'wp_ajax_orddd_advance_settings_save_changes', array( &$this, 'orddd_advance_settings_save_changes' ) );

		// WordPress Administration Menu.
		add_action( 'admin_menu', array( &$this, 'orddd_menu' ) );

		// Delete Settings.
		add_action( 'admin_init', array( &$this, 'orddd_delete_settings' ) );

		// Setting Registration.
		if ( ( isset( $_GET['action'] ) &&
			'orddd_import_export_settings' !== $_GET['action'] ) ||
			! isset( $_GET['action'] ) ) {
			add_action( 'admin_init', array( &$this, 'order_delivery_date_admin_settings' ) );
			add_action( 'admin_init', array( &$this, 'orddd_integration_of_plugins' ), 15 );
			add_action( 'admin_init', array( &$this, 'orddd_time_settings' ) );
			add_action( 'admin_init', array( &$this, 'orddd_holidays_settings' ) );
			add_action( 'admin_init', array( &$this, 'orddd_disable_time_slot_settings' ) );
			add_action( 'admin_init', array( &$this, 'orddd_appearance_settings' ) );
			add_action( 'admin_init', array( &$this, 'orddd_delivery_days_settings' ) );
			add_action( 'admin_init', array( &$this, 'orddd_time_slot_settings' ) );
			add_action( 'admin_init', array( &$this, 'orddd_shipping_based_delivery_callback' ) );
			add_action( 'admin_init', array( &$this, 'orddd_shipping_based_settings_callback' ) );
			add_action( 'admin_init', array( &$this, 'orddd_calendar_sync_settings_callback' ) );
		}

		// Enable Delivery Date checkbox on Product Categories page.
		add_action( 'product_cat_add_form_fields', array( &$this, 'orddd_enable_for_product_category' ) );
		add_action( 'product_cat_edit_form_fields', array( &$this, 'orddd_edit_delivery_field_for_product_category' ), 10, 2 );
		add_action( 'created_term', array( &$this, 'orddd_save_category_fields' ), 10, 3 );
		add_action( 'edit_term', array( &$this, 'orddd_save_category_fields' ), 10, 3 );
		add_filter( 'manage_edit-product_cat_columns', array( &$this, 'orddd_product_cat_columns' ) );
		add_filter( 'manage_product_cat_custom_column', array( &$this, 'orddd_product_cat_column' ), 10, 3 );

		add_filter( 'woocommerce_screen_ids', array( &$this, 'set_wc_screen_ids' ) );
		add_action( 'update_option_orddd_wp_cron_minutes', array( 'Orddd_Calendar_Sync_Settings', 'orddd_update_wp_cron_minutes' ), 10, 2 );
		add_action( 'update_option_orddd_real_time_import', array( 'Orddd_Calendar_Sync_Settings', 'orddd_update_real_time_import' ), 10, 2 );
	} 

	/**
	 * Add screen ids to WooCommerce in order to make wc_help_tip work
	 *
	 * @param array $screen Screen IDs array
	 * @return array Screen IDs array
	 * @since 8.2
	 */
	public function set_wc_screen_ids( $screen ) {
		$screen[] = 'order-delivery-date_page_orddd_view_orders';
		$screen[] = 'toplevel_page_order_delivery_date';
		return $screen;
	}

	/**
	 * Add settings fields & Register settings in Date Settings tab using Settings API
	 *
	 * @since 2.8.3
	 */
	public function order_delivery_date_admin_settings() {
		global $orddd_weekdays, $orddd_shipping_days;
		// First, we register a section. This is necessary since all future options must belong to one.
		add_settings_section(
			'orddd_date_settings_section',      // ID used to identify this section and with which to register options.
			__( 'Order Delivery Date Settings', 'order-delivery-date' ),        // Title to be displayed on the administration page.
			array( 'Orddd_Date_Settings', 'orddd_delivery_date_setting' ),      // Callback used to render the description of the section.
			'orddd_date_settings_page'              // Page on which to add this section of options.
		);

		add_settings_field(
			'orddd_enable_delivery_date',
			__( 'Enable Delivery Date:', 'order-delivery-date' ),
			array( 'Orddd_Date_Settings', 'orddd_enable_delivery_date_callback' ),
			'orddd_date_settings_page',
			'orddd_date_settings_section',
			array( __( 'Enable Delivery Date capture on the checkout page.', 'order-delivery-date' ) )
		);

		do_action( 'orddd_after_enable_delivery_date_setting' );

		add_settings_field(
			'orddd_delivery_checkout_options',
			__( 'Delivery Checkout options:', 'order-delivery-date' ),
			array( 'Orddd_Date_Settings', 'orddd_delivery_checkout_options_callback' ),
			'orddd_date_settings_page',
			'orddd_date_settings_section',
			array( __( 'Choose the delivery date option to be displayed on the checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_delivery_days',
			__( 'Delivery Days:', 'order-delivery-date' ),
			array( 'Orddd_Date_Settings', 'orddd_delivery_days_callback' ),
			'orddd_date_settings_page',
			'orddd_date_settings_section',
			array( __( 'Select weekdays for deliveries. <br> <em> Note: These weekdays will be visible on the Checkout page delivery date calendar. </em>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_enable_day_wise_settings',
			__( 'Weekday Settings:', 'order-delivery-date' ),
			array( 'Orddd_Date_Settings', 'orddd_enable_day_wise_settings_callback' ),
			'orddd_date_settings_page',
			'orddd_date_settings_section',
			array( __( 'Enable this setting to add the following for each weekday: delivery charges & it\'s label, same day cut-off time, next day cut-off time and Minimum Delivery Time (in hours).' ) )
		);

		add_settings_field(
			'orddd_minimumOrderDays',
			__( 'Minimum Delivery time (in hours):', 'order-delivery-date' ),
			array( 'Orddd_Date_Settings', 'orddd_minimum_delivery_time_callback' ),
			'orddd_date_settings_page',
			'orddd_date_settings_section',
			array( __( 'Minimum number of hours required to prepare for delivery.<br><em> Note: This setting will be applied with Same day & Next day cutoff. Please refer <a href="https://www.tychesoftwares.com/how-does-minimum-delivery-time-work-with-same-day-next-day-cutoff-settings/" target="_blank">this post</a> to know more.</em>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_number_of_dates',
			__( 'Number of dates to choose:', 'order-delivery-date' ),
			array( 'Orddd_Date_Settings', 'orddd_number_of_dates_callback' ),
			'orddd_date_settings_page',
			'orddd_date_settings_section',
			array( __( 'Number of dates available for delivery.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_date_field_mandatory',
			__( 'Mandatory field?:', 'order-delivery-date' ),
			array( 'Orddd_Date_Settings', 'orddd_date_field_mandatory_callback' ),
			'orddd_date_settings_page',
			'orddd_date_settings_section',
			array( __( 'Selection of delivery date on the checkout page will become mandatory.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lockout_date_after_orders',
			__( 'Maximum Order Deliveries per day (based on per order):', 'order-delivery-date' ),
			array( 'Orddd_Date_Settings', 'orddd_lockout_date_after_orders_callback' ),
			'orddd_date_settings_page',
			'orddd_date_settings_section',
			array( __( 'A date will become unavailable for further deliveries once these many orders are placed for delivery for that date.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_max_order_total_for_charges',
			__( 'Minimum order amount for waiving off delivery charges:', 'order-delivery-date' ),
			array( 'Orddd_Date_Settings', 'orddd_max_order_total_for_charges_callback' ),
			'orddd_date_settings_page',
			'orddd_date_settings_section',
			array( __( 'When Order Subtotal reaches this minimum amount, all delivery charges from the plugin will be removed. Keep it 0 if you don\'t want to use this option.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_global_lockout_custom',
			__( 'Use global maximum orders per day for custom settings:', 'order-delivery-date' ),
			array( 'Orddd_Date_Settings', 'orddd_global_lockout_custom_callback' ),
			'orddd_date_settings_page',
			'orddd_date_settings_section',
			array( __( 'Maximum Order Deliveries per day setting will considered for the custom settings.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_lockout_date_quantity_based',
			__( 'Enable Maximum Deliveries based on per product quantity:', 'order-delivery-date' ),
			array( 'Orddd_Date_Settings', 'orddd_lockout_date_quantity_based_callback' ),
			'orddd_date_settings_page',
			'orddd_date_settings_section',
			array( __( 'If this is enabled, then the date and time (if enabled) will become unavailable for further deliveries once these many product quantities are selected for delivery for that date and time (if enabled). For example, if an order has a product for 2 quantity in the cart, then the availability for a date will be reduced by 2. If this setting is disabled, then the availability will be reduced by 1.<br><i>Note: The availability will be reduced by product quantities for General settings as well as for Custom Delivery Settings, if this setting is enabled.</i>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_text_block_between_days',
			__( 'Delivery Range', 'order-delivery-date' ),
			array( 'Orddd_Date_Settings', 'orddd_text_block_between_days_callback' ),
			'orddd_date_settings_page',
			'orddd_date_settings_section',
			array( __( '</br>Interval of days it takes to deliver an order after submitting the order.', 'order-delivery-date' ) )
		);

		// Finally, we register the fields with WordPress.
		register_setting(
			'orddd_date_settings',
			'orddd_enable_delivery_date'
		);

		register_setting(
			'orddd_date_settings',
			'orddd_delivery_checkout_options'
		);
		foreach ( $orddd_weekdays as $n => $day_name ) {
			register_setting(
				'orddd_date_settings',
				$n,
				array( 'Orddd_Date_Settings', $n . '_save' )
			);
		}

		register_setting(
			'orddd_date_settings',
			'orddd_enable_day_wise_settings'
		);
		register_setting(
			'orddd_date_settings',
			'orddd_minimumOrderDays'
		);

		register_setting(
			'orddd_date_settings',
			'orddd_number_of_dates'
		);

		register_setting(
			'orddd_date_settings',
			'orddd_date_field_mandatory'
		);

		register_setting(
			'orddd_date_settings',
			'orddd_lockout_date_after_orders'
		);

		register_setting(
			'orddd_date_settings',
			'orddd_max_order_total_for_charges'
		);

		register_setting(
			'orddd_date_settings',
			'orddd_global_lockout_custom'
		);

		register_setting(
			'orddd_date_settings',
			'orddd_lockout_date_quantity_based'
		);

		// Shipping Days section.
		add_settings_section(
			'orddd_shipping_days_settings_section',
			__( 'Business Days Settings', 'order-delivery-date' ),
			array( 'Orddd_Shipping_Days_Settings', 'orddd_shipping_days_settings_section_callback' ),
			'orddd_date_settings_page'
		);

		add_settings_field(
			'orddd_enable_shipping_days',
			__( 'Enable Business days based calculation:', 'order-delivery-date' ),
			array( 'Orddd_Shipping_Days_Settings', 'orddd_enable_shipping_days_callback' ),
			'orddd_date_settings_page',
			'orddd_shipping_days_settings_section',
			array( __( 'Calculate Minimum Delivery Time, Same Day cut-off and Next Day cut-off based on the business days selected.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_days',
			__( 'Business Days:', 'order-delivery-date' ),
			array( 'Orddd_Shipping_Days_Settings', 'orddd_shipping_days_callback' ),
			'orddd_date_settings_page',
			'orddd_shipping_days_settings_section',
			array( __( 'Business days of your store.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_business_opening_time',
			__( 'Opening Time:', 'order-delivery-date' ),
			array( 'Orddd_Shipping_Days_Settings', 'orddd_business_opening_time_callback' ),
			'orddd_date_settings_page',
			'orddd_shipping_days_settings_section',
			array( __( 'What time does your business start delivering orders?', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_business_closing_time',
			__( 'Closing Time:', 'order-delivery-date' ),
			array( 'Orddd_Shipping_Days_Settings', 'orddd_business_closing_time_callback' ),
			'orddd_date_settings_page',
			'orddd_shipping_days_settings_section',
			array( __( 'What time does your business stop delivering orders?', 'order-delivery-date' ) )
		);

		register_setting(
			'orddd_date_settings',
			'orddd_enable_shipping_days'
		);

		foreach ( $orddd_shipping_days as $n => $day_name ) {
			register_setting(
				'orddd_date_settings',
				$n,
				array( 'Orddd_Shipping_Days_Settings', $n . '_save' )
			);
		}

		register_setting(
			'orddd_date_settings',
			'orddd_min_between_days'
		);

		register_setting(
			'orddd_date_settings',
			'orddd_max_between_days'
		);

		register_setting(
			'orddd_date_settings',
			'orddd_business_opening_time'
		);

		register_setting(
			'orddd_date_settings',
			'orddd_business_closing_time'
		);
	}

	/**
     * Add a reference to the subscription addon and link to the site page.
     *
     * @return void
     * @since 9.20.0
     */
    public static function orddd_add_note() {
        echo '<div class="tyche-info">
            <p>' . __( '<a href="https://www.tychesoftwares.com/store/premium-plugins/woocommerce-subscriptions-compatibility-addon-for-order-delivery-date-pro-for-woocommerce-plugin/?utm_source=ordddprofooter&utm_medium=link&utm_campaign=OrderDeliveryDatePRoPlugin" target="_blank"><strong>Get our Subscriptions Addon</strong></a> & setup recurring deliveries with WooCommerce Subscriptions plugin & Order Delivery Date plugin.', 'order-delivery-date' ) . '</p>
        </div>';
	}
	
	/**
	 * Add settings fields & Register settings in Date Settings tab for Integration with our plugins
	 *
	 * @since 2.8.3
	 */
	public function orddd_integration_of_plugins() {

		add_settings_section(
			'orddd_additional_settings_section',
			__( 'Additional Settings', 'order-delivery-date' ),
			array( 'orddd_additional_settings', 'orddd_additional_settings_section_callback' ),
			'orddd_additional_settings_page'
		);

		add_settings_field(
			'orddd_show_column_on_orders_page_check',
			__( 'Show on Orders Listing Page:', 'order-delivery-date' ),
			array( 'orddd_additional_settings', 'orddd_show_column_on_orders_page_check_callback' ),
			'orddd_additional_settings_page',
			'orddd_additional_settings_section',
			array( __( 'Displays the Delivery Date on the WooCommerce->Orders page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_show_filter_on_orders_page_check',
			__( 'Show Filter on Orders Listing Page:', 'order-delivery-date' ),
			array( 'orddd_additional_settings', 'orddd_show_filter_on_orders_page_check_callback' ),
			'orddd_additional_settings_page',
			'orddd_additional_settings_section',
			array( __( 'Displays the Filter on the WooCommerce->Orders page that allows you to view orders to be delivered today, tomorrow or in any month.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_enable_autofill_of_delivery_date',
			__( 'Auto-populate first available Delivery date:', 'order-delivery-date' ),
			array( 'orddd_additional_settings', 'orddd_enable_autofill_of_delivery_date_callback' ),
			'orddd_additional_settings_page',
			'orddd_additional_settings_section',
			array( __( 'Auto-populate first available Delivery date when the checkout page loads.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_enable_tax_calculation_for_delivery_charges',
			__( 'Enable Tax calculation for Delivery charges', 'order-delivery-date' ),
			array( 'orddd_additional_settings', 'orddd_enable_tax_calculation_for_delivery_charges_callback' ),
			'orddd_additional_settings_page',
			'orddd_additional_settings_section',
			array( __( 'Enable Tax calculation for Delivery charges on the checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_no_fields_for_product_type',
			__( 'Disable the Delivery Date and Time Slot Fields for:', 'order-delivery-date' ),
			array( 'orddd_additional_settings', 'orddd_appearance_virtual_product_callback' ),
			'orddd_additional_settings_page',
			'orddd_additional_settings_section',
			array( __( '<br>Disable the Delivery Date and Time Slot on the Checkout page for Virtual products and Featured products.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_allow_customers_to_edit_date',
			__( 'Allow Customers to edit Delivery Date & Time:', 'order-delivery-date' ),
			array( 'orddd_additional_settings', 'orddd_allow_customers_to_edit_date_callback' ),
			'orddd_additional_settings_page',
			'orddd_additional_settings_section',
			array( __( 'When enabled, it will add Delivery Date & Time field on the My Account -> Orders -> View page. So customers will be able to edit the date and time once the order is placed.<br>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_enable_availability_display',
			__( 'Display availability on date', 'order-delivery-date' ),
			array( 'orddd_additional_settings', 'orddd_enable_availability_display_callback' ),
			'orddd_additional_settings_page',
			'orddd_additional_settings_section',
			array( __( 'When enabled, it will display the availability on hover of the dates in the delivery calendar on checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_show_partially_booked_dates',
			__( 'Show Partially Booked Dates on the Delivery Calendar', 'order-delivery-date' ),
			array( 'orddd_additional_settings', 'orddd_show_partially_booked_dates_callback' ),
			'orddd_additional_settings_page',
			'orddd_additional_settings_section',
			array( __( 'When enabled, it will show the dates with diagonally separated colors of Booked dates and Available Dates if 1 or more orders are placed for that date. <div class="orddd-tooltip">Preview here.<span class="orddd-tooltipimg"><img src="' . plugins_url() . '/order-delivery-date/images/partial-booked-dates.png"></span></div>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_add_delivery_in_order_notes',
			__( 'Display delivery date & time information in the WooCommerce mobile app.', 'order-delivery-date' ),
			array( 'orddd_additional_settings', 'orddd_add_delivery_in_order_notes_callback' ),
			'orddd_additional_settings_page',
			'orddd_additional_settings_section',
			array( __( 'When enabled, this will display the delivery date & time information in the WooCommerce mobile app. The information will be available in the Order Notes section in the app.', 'order-delivery-date' ) )
		);

		add_settings_section(
			'orddd_integration_with_other_plugins',
			__( 'Integration with Other Plugins:', 'order-delivery-date' ),
			array( 'orddd_additional_settings', 'orddd_integration_with_other_plugins_callback' ),
			'orddd_additional_settings_page'
		);

		add_settings_field(
			'orddd_show_fields_in_csv_export_check',
			__( 'WooCommerce Customer/ Order CSV Export plugin', 'order-delivery-date' ),
			array( 'orddd_additional_settings', 'orddd_show_fields_in_csv_export_check_callback' ),
			'orddd_additional_settings_page',
			'orddd_integration_with_other_plugins',
			array( __( 'Displays the Delivery details in the CSV Export File.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_show_fields_in_pdf_invoice_and_packing_slips',
			__( 'WooCommerce PDF Invoices & Packing Slips plugin', 'order-delivery-date' ),
			array( 'orddd_additional_settings', 'orddd_show_fields_in_pdf_invoice_and_packing_slips_callback' ),
			'orddd_additional_settings_page',
			'orddd_integration_with_other_plugins',
			array( __( 'Displays the Delivery details in the PDF Invoice and Packing Slips.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_show_fields_in_invoice_and_delivery_note',
			__( 'WooCommerce Print Invoice & Delivery Note plugin', 'order-delivery-date' ),
			array( 'orddd_additional_settings', 'orddd_show_fields_in_invoice_and_delivery_note_callback' ),
			'orddd_additional_settings_page',
			'orddd_integration_with_other_plugins',
			array( __( 'Displays the Delivery details in the Invoice and Delivery Note.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_show_fields_in_cloud_print_orders',
			__( 'WooCommerce Print orders plugin', 'order-delivery-date' ),
			array( 'orddd_additional_settings', 'orddd_show_fields_in_cloud_print_orders_callback' ),
			'orddd_additional_settings_page',
			'orddd_integration_with_other_plugins',
			array( __( 'Displays the Delivery details in the print copy of the order.', 'order-delivery-date' ) )
		);

		/**
		 * Add settings fields & Register settings in Additional Settings tab for Compatility with other plugins
		 *
		 * @since 2.8.3
		 */
		add_settings_section(
			'orddd_compatibility_with_other_plugins',
			__( 'Compatibility with Other Plugins:', 'order-delivery-date' ),
			array( 'orddd_additional_settings', 'orddd_compatibility_with_other_plugins_callback' ),
			'orddd_additional_settings_page'
		);

		add_settings_field(
			'orddd_shipping_multiple_address_compatibility',
			__( 'WooCommerce Shipping Multiple addresses', 'order-delivery-date' ),
			array( 'orddd_additional_settings', 'orddd_shipping_multiple_address_compatibility_callback' ),
			'orddd_additional_settings_page',
			'orddd_compatibility_with_other_plugins',
			array( __( 'When enabled, it will allow to choose a Delivery Date & Time (if enabled) for each shipping address chosen on checkout page with the WooCommerce Shipping Multiple addresses plugin.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_amazon_payments_advanced_gateway_compatibility',
			__( 'WooCommerce Amazon Payments Advanced Gateway', 'order-delivery-date' ),
			array( 'orddd_additional_settings', 'orddd_amazon_payments_advanced_gateway_compatibility_callback' ),
			'orddd_additional_settings_page',
			'orddd_compatibility_with_other_plugins',
			array( __( 'If enabled, it will add the Delivery Date and Time fields when the customer clicks on "Pay with Amazon" button.', 'order-delivery-date' ) )
		);

		register_setting(
			'orddd_additional_settings',
			'orddd_amazon_payments_advanced_gateway_compatibility'
		);

		register_setting(
			'orddd_additional_settings',
			'orddd_show_fields_in_csv_export_check'
		);

		register_setting(
			'orddd_additional_settings',
			'orddd_show_fields_in_pdf_invoice_and_packing_slips'
		);

		register_setting(
			'orddd_additional_settings',
			'orddd_show_fields_in_invoice_and_delivery_note'
		);

		register_setting(
			'orddd_additional_settings',
			'orddd_show_fields_in_cloud_print_orders'
		);

		register_setting(
			'orddd_additional_settings',
			'orddd_show_column_on_orders_page_check'
		);

		register_setting(
			'orddd_additional_settings',
			'orddd_enable_default_sorting_of_column'
		);

		register_setting(
			'orddd_additional_settings',
			'orddd_show_filter_on_orders_page_check'
		);

		register_setting(
			'orddd_additional_settings',
			'orddd_enable_autofill_of_delivery_date'
		);
		register_setting(
			'orddd_additional_settings',
			'orddd_enable_tax_calculation_for_delivery_charges'
		);

		register_setting(
			'orddd_additional_settings',
			'orddd_no_fields_for_virtual_product'
		);

		register_setting(
			'orddd_additional_settings',
			'orddd_no_fields_for_featured_product'
		);

		register_setting(
			'orddd_additional_settings',
			'orddd_allow_customers_to_edit_date'
		);

		register_setting(
			'orddd_additional_settings',
			'orddd_enable_availability_display'
		);

		register_setting(
			'orddd_additional_settings',
			'orddd_show_partially_booked_dates'
		);

		register_setting(
			'orddd_additional_settings',
			'orddd_send_email_to_admin_when_date_updated'
		);

		register_setting(
			'orddd_additional_settings',
			'orddd_disable_edit_after_cutoff'
		);
		
		register_setting(
			'orddd_additional_settings',
			'orddd_add_delivery_in_order_notes'
		);

		register_setting(
			'orddd_additional_settings',
			'orddd_shipping_multiple_address_compatibility'
		);

		do_action( 'orddd_add_new_settings' );
	}

	/**
	 * Add settings fields & Register settings in Time Settings tab
	 *
	 * @since 2.8.3
	 */
	public function orddd_time_settings() {

		add_settings_section(
			'orddd_time_settings',
			__( 'Order Delivery Time Settings', 'order-delivery-date' ),
			array( 'orddd_time_settings', 'orddd_delivery_time_settings_callback' ),
			'orddd_time_settings_page'
		);

		add_settings_section(
			'orddd_time_settings_section',
			__( 'Time Settings', 'order-delivery-date' ),
			array( 'orddd_time_settings', 'orddd_delivery_time_settings_callback' ),
			'orddd_time_settings_page'
		);

		add_settings_field(
			'orddd_enable_delivery_time',
			__( 'Enable delivery time capture:', 'order-delivery-date' ),
			array( 'orddd_time_settings', 'orddd_enable_delivery_time_capture_callback' ),
			'orddd_time_settings_page',
			'orddd_time_settings_section',
			array( __( 'Enable to choose the time for delivery on the checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_time_range',
			__( 'Time Range:', 'order-delivery-date' ),
			array( 'orddd_time_settings', 'orddd_time_range_callback' ),
			'orddd_time_settings_page',
			'orddd_time_settings_section',
			array( '<br>' . __( 'Select time range for the time sliders.', 'order-delivery-date' ) )
		);

		add_settings_section(
			'orddd_same_day_delivery_section',
			__( 'Same Day Delivery', 'order-delivery-date' ),
			array( 'orddd_time_settings', 'orddd_same_day_delivery_callback' ),
			'orddd_time_settings_page'
		);

		add_settings_field(
			'orddd_enable_same_day_delivery',
			__( 'Enable Same day delivery:', 'order-delivery-date' ),
			array( 'orddd_time_settings', 'orddd_enable_same_day_delivery_callback' ),
			'orddd_time_settings_page',
			'orddd_same_day_delivery_section',
			array( __( 'Enable same day delivery for the orders.', 'order-delivery-date' ) . '<br><i>' . __( 'This is very useful in cases when your customers are gifting items to their loved ones, especially on birthdays, anniversaries, etc.', 'order-delivery-date' ) . '</i>' )
		);

		add_settings_field(
			'cutoff_time_for_same_day_delivery_orders',
			__( 'Cut-off time for same day delivery orders:', 'order-delivery-date' ),
			array( 'orddd_time_settings', 'orddd_cutoff_time_for_same_day_delivery_orders_callback' ),
			'orddd_time_settings_page',
			'orddd_same_day_delivery_section',
			array( '<br>' . __( 'Current day will be disabled if an order is placed after the time mentioned in this field.', 'order-delivery-date' ) . '<br><i>' . __( 'The timezone is taken from the Settings -> General -> Timezone field.', 'order-delivery-date' ) . '</i>' )
		);

		add_settings_field(
			'orddd_same_day_additional_charges',
			__( 'Additional Charges for same day delivery:', 'order-delivery-date' ),
			array( 'orddd_time_settings', 'orddd_additional_charges_for_same_day_delivery_callback' ),
			'orddd_time_settings_page',
			'orddd_same_day_delivery_section',
			array( __( 'Set additional charges for same day delivery.', 'order-delivery-date' ) )
		);

		add_settings_section(
			'orddd_next_day_delivery_section',
			__( 'Next Day Delivery', 'order-delivery-date' ),
			array( 'orddd_time_settings', 'orddd_next_day_delivery_callback' ),
			'orddd_time_settings_page'
		);

		add_settings_field(
			'orddd_enable_next_day_delivery',
			__( 'Enable Next day delivery:', 'order-delivery-date' ),
			array( 'orddd_time_settings', 'orddd_enable_next_day_delivery_callback' ),
			'orddd_time_settings_page',
			'orddd_next_day_delivery_section',
			array( __( 'If you deliver on the next day, enable this option.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'cutoff_time_for_next_day_delivery_orders',
			__( 'Cut-off time for next day delivery orders:', 'order-delivery-date' ),
			array( 'orddd_time_settings', 'orddd_cutoff_time_for_next_day_delivery_orders_callback' ),
			'orddd_time_settings_page',
			'orddd_next_day_delivery_section',
			array( '<br>' . __( 'Next day will be disabled if an order is placed after the time mentioned in this field.', 'order-delivery-date' ) . '<br><i>' . __( 'The timezone is taken from the Settings -> General -> Timezone field.', 'order-delivery-date' ) . '</i>' )
		);

		add_settings_field(
			'orddd_next_day_additional_charges',
			__( 'Additional Charges for next day delivery:', 'order-delivery-date' ),
			array( 'orddd_time_settings', 'orddd_additional_charges_for_next_day_delivery_callback' ),
			'orddd_time_settings_page',
			'orddd_next_day_delivery_section',
			array( __( 'Set additional charges for next day delivery.', 'order-delivery-date' ) )
		);

		add_settings_section(
			'orddd_delivery_charges_based_on_days_section',
			__( 'Delivery charges based on number of days (beyond Next Day) / Rush delivery charges', 'order-delivery-date' ),
			array( 'orddd_time_settings', 'orddd_delivery_charges_based_on_days_section_callback' ),
			'orddd_time_settings_page'
		);

		add_settings_field(
			'orddd_delivery_charges_based_on_days_enable',
			__( 'Enable:', 'order-delivery-date' ),
			array( 'orddd_time_settings', 'orddd_delivery_charges_based_on_days_enable_callback' ),
			'orddd_time_settings_page',
			'orddd_delivery_charges_based_on_days_section',
			array( __( 'If you want to add delivery charges for days beyond the next day, enable this option.', 'order-delivery-date' ), __( 'Use same-day & next-day delivery options if you want to charge for that. In the options provided below, you can set the delivery charges for 2 days & beyond.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_delivery_charges_based_on_days',
			__( 'Additional Charges based on number of days from current date:', 'order-delivery-date' ),
			array( 'orddd_time_settings', 'orddd_delivery_charges_based_on_days_callback' ),
			'orddd_time_settings_page',
			'orddd_delivery_charges_based_on_days_section'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_enable_delivery_time'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_delivery_from_hours'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_delivery_from_mins'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_delivery_to_hours'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_delivery_to_mins'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_enable_same_day_delivery'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_disable_same_day_delivery_after_hours'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_disable_same_day_delivery_after_minutes'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_same_day_additional_charges'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_enable_next_day_delivery'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_disable_next_day_delivery_after_hours'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_disable_next_day_delivery_after_minutes'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_next_day_additional_charges'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_delivery_charges_based_on_days_enable'
		);

		register_setting(
			'orddd_time_settings',
			'orddd_additional_settings_based_on_days'
		);
	}

	/**
	 * Add settings field & Register settings in Holidays tab
	 *
	 * @since 2.8.4
	 */

	public function orddd_holidays_settings() {

		add_settings_section(
			'orddd_holidays_section',
			__( 'Add Holiday', 'order-delivery-date' ),
			array( 'orddd_holidays_settings', 'orddd_holidays_admin_setting_callback' ),
			'orddd_holidays_page'
		);

		add_settings_field(
			'orddd_holiday_name',
			__( 'Holiday Name:', 'order-delivery-date' ),
			array( 'orddd_holidays_settings', 'orddd_holidays_name_callback' ),
			'orddd_holidays_page',
			'orddd_holidays_section',
			array( __( '<br>Enter the name of the holiday here.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_holiday_from_date',
			__( 'From Date:', 'order-delivery-date' ),
			array( 'orddd_holidays_settings', 'orddd_holidays_from_date_callback' ),
			'orddd_holidays_page',
			'orddd_holidays_section'
		);

		add_settings_field(
			'orddd_holiday_to_date',
			__( 'To Date:', 'order-delivery-date' ),
			array( 'orddd_holidays_settings', 'orddd_holidays_to_date_callback' ),
			'orddd_holidays_page',
			'orddd_holidays_section',
			array( __( '<br>Leave the "To Date:" field unchanged for single day holidays.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_allow_recurring_holiday',
			__( 'Allow Recurring:', 'order-delivery-date' ),
			array( 'orddd_holidays_settings', 'orddd_allow_recurring_holiday_callback' ),
			'orddd_holidays_page',
			'orddd_holidays_section',
			array( __( 'Enable to block the holidays for all future years.', 'order-delivery-date' ) )
		);

		do_action( 'add_settings_in_holidays' );

		register_setting(
			'orddd_holiday_settings',
			'orddd_delivery_date_holidays',
			array( 'orddd_holidays_settings', 'orddd_delivery_date_holidays_callback' )
		);
	}

	/**
	 * Add settings field & Register settings to block time slots
	 *
	 * @since 2.8.3
	 */

	public function orddd_disable_time_slot_settings() {
		add_settings_section(
			'orddd_disable_time_slot_section',
			__( 'Block a Time Slot', 'order-delivery-date' ),
			array( 'orddd_holidays_settings', 'orddd_disable_time_slot_callback' ),
			'orddd_holidays_disable_page'
		);

		add_settings_field(
			'orddd_disable_time_slot_for_delivery_days',
			__( 'Block Time Slot for:', 'order-delivery-date' ),
			array( 'orddd_holidays_settings', 'orddd_disable_time_slot_for_delivery_days_callback' ),
			'orddd_holidays_disable_page',
			'orddd_disable_time_slot_section',
			array( __( 'Select "Dates" option to block time slots for individual dates. Select "Weekdays" option to block the time slots for a weekday or multiple weekdays.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_disable_time_slot_for_weekdays',
			__( 'Select Dates or Weekdays:', 'order-delivery-date' ),
			array( 'orddd_holidays_settings', 'orddd_disable_time_slot_for_weekdays_callback' ),
			'orddd_holidays_disable_page',
			'orddd_disable_time_slot_section',
			array( __( 'Select Dates or Weekdays for which you want to block the time slots.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_selected_time_slots_to_be_disabled',
			__( 'Select Time Slots to block:', 'order-delivery-date' ),
			array( 'orddd_holidays_settings', 'orddd_selected_time_slots_to_be_disabled_callback' ),
			'orddd_holidays_disable_page',
			'orddd_disable_time_slot_section',
			array( __( 'This will list all the time slots which are created in General Settings or in Custom Delivery Settings.', 'order-delivery-date' ) )
		);

		register_setting(
			'orddd_disable_time_slot_settings',
			'orddd_disable_time_slot_log',
			array( 'orddd_holidays_settings', 'orddd_disable_time_slots_callback' )
		);
	}

	/**
	 * Add settings fields & Register settings in Appearance tab
	 *
	 * @since 2.8.3
	 */

	public function orddd_appearance_settings() {

		add_settings_section(
			'orddd_calendar_appearance_section',
			__( 'Calendar Appearance', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_appearance_admin_setting_callback' ),
			'orddd_appearance_page'
		);

		add_settings_field(
			'orddd_delivery_dates_in_dropdown',
			__( 'Show delivery dates in dropdown:', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_delivery_dates_in_dropdown_callback' ),
			'orddd_appearance_page',
			'orddd_calendar_appearance_section',
			array( __( 'Display delivery dates as a dropdown select input. You can use this if you want few delivery dates.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_calendar_display_mode',
			__( 'Calendar Display Mode:', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_calendar_display_mode_callback' ),
			'orddd_appearance_page',
			'orddd_calendar_appearance_section',
			array( __( 'Whether you want the delivery calendar to be opened on click of input field or if you want it open by default when the checkout page loads.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_language_selected',
			__( 'Calendar Language:', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_appearance_calendar_language_callback' ),
			'orddd_appearance_page',
			'orddd_calendar_appearance_section',
			array( __( 'Choose a Language.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_delivery_date_format',
			__( 'Date Format:', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_appearance_date_formats_callback' ),
			'orddd_appearance_page',
			'orddd_calendar_appearance_section',
			array( '<br>' . __( 'The format in which the Delivery Date appears to the customers on the checkout page once the date is selected.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_delivery_time_format',
			__( 'Time Format:', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_time_format_callback' ),
			'orddd_appearance_page',
			'orddd_calendar_appearance_section',
			array( __( 'The time range will come in the selected format. If 12 hour format is selected, then the time slider will appear in am/pm format.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'start_of_week',
			__( 'First Day of Week:', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_appearance_first_day_of_week_callback' ),
			'orddd_appearance_page',
			'orddd_calendar_appearance_section',
			array( __( 'Choose the first day of week displayed on the Delivery Date calendar.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_number_of_months',
			__( 'Number of Months:', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_appearance_number_of_months_callback' ),
			'orddd_appearance_page',
			'orddd_calendar_appearance_section',
			array( __( 'The number of months to be shown on the calendar.', 'order-delivery-date' ) )
		);
		add_settings_field(
			'orddd_calendar_theme_name',
			__( 'Theme:', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_appearance_calendar_theme_callback' ),
			'orddd_appearance_page',
			'orddd_calendar_appearance_section',
			array( __( 'Select the theme for the calendar which blends with the design of your website.', 'order-delivery-date' ) )
		);

		add_settings_section(
			'orddd_field_appearance_section',
			__( 'Field Appearance', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_field_appearance_admin_setting_callback' ),
			'orddd_appearance_page'
		);

		add_settings_field(
			'orddd_location_field_label',
			__( 'Locations Label:', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_location_field_label_callback' ),
			'orddd_appearance_page',
			'orddd_field_appearance_section',
			array( __( 'Choose the label that is to be displayed for the delivery locations field on checkout page. HTML allowed.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_delivery_date_field_label',
			__( 'Date Field Label:', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_delivery_date_field_label_callback' ),
			'orddd_appearance_page',
			'orddd_field_appearance_section',
			array( __( 'Choose the label that is to be displayed for the field on checkout page. HTML allowed.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_delivery_timeslot_field_label',
			__( 'Time slot Field Label:', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_delivery_timeslot_field_label_callback' ),
			'orddd_appearance_page',
			'orddd_field_appearance_section',
			array( __( 'Choose a label that is to be displayed for the time slot field on the checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_delivery_date_field_placeholder',
			__( 'Field Placeholder Text:', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_delivery_date_field_placeholder_callback' ),
			'orddd_appearance_page',
			'orddd_field_appearance_section',
			array( __( 'Choose the placeholder text that is to be displayed for the field on checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_delivery_date_field_note',
			__( 'Field Note Text:', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_delivery_date_field_note_text_callback' ),
			'orddd_appearance_page',
			'orddd_field_appearance_section',
			array( '<br>' . __( 'Choose the note to be displayed below the delivery date field on checkout page. HTML allowed.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_estimated_date_text',
			__( 'Estimated delivery date text:', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_estimated_date_text_callback' ),
			'orddd_appearance_page',
			'orddd_field_appearance_section',
			array( '<br>' . __( 
			'%shipping_date% - This is calculated automatically based on Minimum Delivery Time. <br>
			%delivery_range_start_days%, %delivery_range_end_days% - This is the delivery range in days.', 'order-delivery-date' 
			) )
		);

		add_settings_field(
			'orddd_delivery_date_fields_on_checkout_page',
			__( 'Fields placement on the Checkout page:', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_delivery_date_in_shipping_section_callback' ),
			'orddd_appearance_page',
			'orddd_field_appearance_section',
			array( __( '</br>The Delivery Date fields will be displayed in the selected section.</br><i>Note: WooCommerce automatically hides the Shipping section fields for Virtual products.</i>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_delivery_date_on_cart_page',
			__( 'Delivery Date field on Cart page:', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_delivery_date_on_cart_page_callback' ),
			'orddd_appearance_page',
			'orddd_field_appearance_section',
			array( __( 'Add the Delivery Date & Time field on the cart page along with the Checkout page.' ) )
		);

		add_settings_section(
			'orddd_color_picker_section',
			__( 'Color Code Your Calendar', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_color_picker_admin_setting_callback' ),
			'orddd_appearance_page'
		);

		add_settings_field(
			'orddd_holiday_color',
			__( 'Holidays:', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_holiday_color_callback' ),
			'orddd_appearance_page',
			'orddd_color_picker_section',
			array( __( 'Color in which holidays should be shown on the calendar on checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_booked_dates_color',
			__( 'Booked Dates:', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_booked_dates_color_callback' ),
			'orddd_appearance_page',
			'orddd_color_picker_section',
			array( __( 'Color in which booked dates should be shown on the calendar on checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_cut_off_time_color',
			__( 'Cut-off time over Dates:', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_cut_off_time_color_callback' ),
			'orddd_appearance_page',
			'orddd_color_picker_section',
			array( __( 'Color in which cut-off time over dates should be shown on the calendar on checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_available_dates_color',
			__( 'Available Dates:', 'order-delivery-date' ),
			array( 'orddd_appearance_settings', 'orddd_available_dates_color_callback' ),
			'orddd_appearance_page',
			'orddd_color_picker_section',
			array( __( 'Color in which available dates should be shown on the calendar on checkout page.', 'order-delivery-date' ) )
		);

		register_setting(
			'orddd_appearance_settings',
			'orddd_calendar_display_mode'
		);

		register_setting(
			'orddd_appearance_settings',
			'orddd_delivery_dates_in_dropdown'
		);

		register_setting(
			'orddd_appearance_settings',
			'orddd_language_selected'
		);

		register_setting(
			'orddd_appearance_settings',
			'orddd_delivery_date_format'
		);

		register_setting(
			'orddd_appearance_settings',
			'orddd_delivery_time_format'
		);

		register_setting(
			'orddd_appearance_settings',
			'start_of_week'
		);

		register_setting(
			'orddd_appearance_settings',
			'orddd_location_field_label'
		);

		register_setting(
			'orddd_appearance_settings',
			'orddd_delivery_date_field_label'
		);

		register_setting(
			'orddd_appearance_settings',
			'orddd_delivery_timeslot_field_label'
		);

		register_setting(
			'orddd_appearance_settings',
			'orddd_delivery_date_field_placeholder'
		);

		register_setting(
			'orddd_appearance_settings',
			'orddd_delivery_date_field_note'
		);

		register_setting(
			'orddd_appearance_settings',
			'orddd_estimated_date_text'
		);

		register_setting(
			'orddd_appearance_settings',
			'orddd_number_of_months'
		);

		register_setting(
			'orddd_appearance_settings',
			'orddd_delivery_date_fields_on_checkout_page'
		);

		register_setting(
			'orddd_appearance_settings',
			'orddd_calendar_theme_name'
		);

		register_setting(
			'orddd_appearance_settings',
			'orddd_calendar_theme'
		);

		register_setting(
			'orddd_appearance_settings',
			'orddd_custom_hook_for_fields_placement'
		);

		register_setting(
			'orddd_appearance_settings',
			'orddd_delivery_date_on_cart_page'
		);

		register_setting(
			'orddd_appearance_settings',
			'orddd_holiday_color'
		);

		register_setting(
			'orddd_appearance_settings',
			'orddd_booked_dates_color'
		);

		register_setting(
			'orddd_appearance_settings',
			'orddd_cut_off_time_color'
		);

		register_setting(
			'orddd_appearance_settings',
			'orddd_available_dates_color'
		);
	}

	/**
	 * Add settings fields & Register settings for specific delivery dates
	 *
	 * @since 2.8.3
	 */

	public function orddd_delivery_days_settings() {

		add_settings_section(
			'orddd_delivery_days_section',
			__( 'Add Specific Delivery Dates', 'order-delivery-date' ),
			array( 'orddd_delivery_days_settings', 'orddd_delivery_days_admin_setting_callback' ),
			'orddd_delivery_days_page'
		);

		add_settings_field(
			'orddd_enable_specific_delivery_dates',
			__( 'Enable Specific Delivery Dates:', 'order-delivery-date' ),
			array( 'orddd_delivery_days_settings', 'orddd_delivery_days_enable_callback' ),
			'orddd_delivery_days_page',
			'orddd_delivery_days_section',
			array( __( 'Enable this option to choose specific delivery dates on the checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_delivery_date_1',
			__( 'Specific Delivery Date:', 'order-delivery-date' ),
			array( 'orddd_delivery_days_settings', 'orddd_delivery_days_datepicker_1_callback' ),
			'orddd_delivery_days_page',
			'orddd_delivery_days_section',
			array( '' )
		);

		add_settings_field(
			'orddd_delivery_date_2',
			__( 'Specific Delivery Date:', 'order-delivery-date' ),
			array( 'orddd_delivery_days_settings', 'orddd_delivery_days_datepicker_2_callback' ),
			'orddd_delivery_days_page',
			'orddd_delivery_days_section',
			array( '' )
		);

		add_settings_field(
			'orddd_delivery_date_3',
			__( 'Specific Delivery Date:', 'order-delivery-date' ),
			array( 'orddd_delivery_days_settings', 'orddd_delivery_days_datepicker_3_callback' ),
			'orddd_delivery_days_page',
			'orddd_delivery_days_section',
			array( '' )
		);

		register_setting(
			'orddd_delivery_days_settings',
			'orddd_enable_specific_delivery_dates'
		);

		register_setting(
			'orddd_delivery_days_settings',
			'orddd_delivery_dates',
			array( 'orddd_delivery_days_settings', 'orddd_delivery_dates_callback' )
		);
	}

	/**
	 * Add settings fields & Register settings for time slots in the 'Time Slot' tab
	 *
	 * @since 2.8.3
	 */

	public function orddd_time_slot_settings() {

		add_settings_section(
			'orddd_time_slot_section',
			__( 'Time Slot Settings', 'order-delivery-date' ),
			array( 'orddd_time_slot_settings', 'orddd_time_slot_admin_settings_callback' ),
			'orddd_time_slot_page'
		);

		add_settings_field(
			'orddd_enable_time_slot',
			__( 'Enable time slot capture:', 'order-delivery-date' ),
			array( 'orddd_time_slot_settings', 'orddd_time_slot_enable_callback' ),
			'orddd_time_slot_page',
			'orddd_time_slot_section',
			array( __( 'Allows the customer to choose a time slot for delivery on the checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_time_slot_mandatory',
			__( 'Mandatory field?:', 'order-delivery-date' ),
			array( 'orddd_time_slot_settings', 'orddd_time_slot_mandatory_callback' ),
			'orddd_time_slot_page',
			'orddd_time_slot_section',
			array( __( 'Selection of Time slot on the checkout page will become mandatory.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_time_slot_asap',
			__( "Show 'As Soon As Possible' option:", 'order-delivery-date' ),
			array( 'orddd_time_slot_settings', 'orddd_time_slot_asap_callback' ),
			'orddd_time_slot_page',
			'orddd_time_slot_section',
			array( __( 'A new option will be added in the Time slot dropdown on checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_global_lockout_time_slots',
			__( 'Global Maximum Order Deliveries for Time slots:', 'order-delivery-date' ),
			array( 'orddd_time_slot_settings', 'orddd_global_lockout_time_slots_callback' ),
			'orddd_time_slot_page',
			'orddd_time_slot_section',
			array( __( 'Maximum deliveries/orders applied to all the Time slots if the individual Maximum Order Deliveries for Time slots is blank for Custom Delivery Settings.<br><i>Note: Leave blank for Unlimited Deliveries.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_auto_populate_first_available_time_slot',
			__( 'Auto-populate first available delivery time slot:', 'order-delivery-date' ),
			array( 'orddd_time_slot_settings', 'orddd_show_first_available_time_slot_callback' ),
			'orddd_time_slot_page',
			'orddd_time_slot_section',
			array( __( 'Auto-populate first available Delivery time slot when the date is selected on the checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_time_slots_in_list_view',
			__( 'Show time slots in list view:', 'order-delivery-date' ),
			array( 'orddd_time_slot_settings', 'orddd_time_slots_in_list_view_callback' ),
			'orddd_time_slot_page',
			'orddd_time_slot_section',
			array( __( 'Display time slots as clickable buttons instead of dropdown list.', 'order-delivery-date' ) )
		);

		// Add time slot section.
		add_settings_section(
			'orddd_add_time_slot_section',
			__( 'Add Time Slot <a href=https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/setup-delivery-date-with-time/?utm_source=userwebsite&utm_medium=link&utm_campaign=OrderDeliveryDateProSetting" target="_blank" class="dashicons dashicons-external" style="line-height:unset;"></a>', 'order-delivery-date' ),
			array( 'orddd_time_slot_settings', 'orddd_add_time_slot_admin_settings_callback' ),
			'orddd_individual_time_slot_page'
		);

		add_settings_field(
			'orddd_time_slot_for_delivery_days',
			__( 'Time Slot for:', 'order-delivery-date' ),
			array( 'orddd_time_slot_settings', 'orddd_time_slot_for_delivery_days_callback' ),
			'orddd_individual_time_slot_page',
			'orddd_add_time_slot_section',
			array( __( 'Select Weekday option or Specific delivery dates option to create a time slot.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_time_slot_for_weekdays',
			__( 'Select Delivery Days/Dates:', 'order-delivery-date' ),
			array( 'orddd_time_slot_settings', 'orddd_time_slot_for_weekdays_callback' ),
			'orddd_individual_time_slot_page',
			'orddd_add_time_slot_section',
			array( __( 'Select Delivery Days/Dates for which you want to create an exclusive Time Slot. To create a time slot for all the weekdays, select "All".', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_time_from_hours',
			__( 'Time From/To:', 'order-delivery-date' ),
			array( 'orddd_time_slot_settings', 'orddd_time_from_hours_callback' ),
			'orddd_individual_time_slot_page',
			'orddd_add_time_slot_section',
			array( __( 'Start time for the time slot.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_time_slot_lockout',
			__( 'Maximum Order Deliveries per time slot (based on per order):', 'order-delivery-date' ),
			array( 'orddd_time_slot_settings', 'orddd_time_slot_lockout_callback' ),
			'orddd_individual_time_slot_page',
			'orddd_add_time_slot_section',
			array( __( 'A time slot will become unavailable for further deliveries once these many orders are placed for delivery for that time slot. <br> <em>Note: If Max order deliveries is set, then that will get priority over time slot lockout.</em>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_time_slot_additional_charges',
			__( 'Additional Charges for time slot and Checkout label:', 'order-delivery-date' ),
			array( 'orddd_time_slot_settings', 'orddd_time_slot_additional_charges_callback' ),
			'orddd_individual_time_slot_page',
			'orddd_add_time_slot_section',
			array( __( 'Add delivery charges (if applicable) for time slot and add the label to be displayed on Checkout page.', 'order-delivery-date' ) )
		);

		// Bulk Add time slots section.
		add_settings_section(
			'orddd_bulk_time_slot_section',
			__( 'Bulk Add Time Slots <a href=https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/setup-delivery-date-with-time/?utm_source=userwebsite&utm_medium=link&utm_campaign=OrderDeliveryDateProSetting" target="_blank" class="dashicons dashicons-external" style="line-height:unset;"></a>', 'order-delivery-date' ),
			array( 'orddd_time_slot_settings', 'orddd_bulk_time_slot_admin_settings_callback' ),
			'orddd_bulk_time_slot_page'
		);

		add_settings_field(
			'orddd_bulk_time_slot_for_delivery_days',
			__( 'Time Slot for:', 'order-delivery-date' ),
			array( 'orddd_time_slot_settings', 'orddd_bulk_time_slot_for_delivery_days_callback' ),
			'orddd_bulk_time_slot_page',
			'orddd_bulk_time_slot_section',
			array( __( 'Select Weekday option or Specific delivery dates option to create a time slot.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_time_slot_for_weekdays_bulk',
			__( 'Select Delivery Days/Dates:', 'order-delivery-date' ),
			array( 'orddd_time_slot_settings', 'orddd_time_slot_for_weekdays_bulk_callback' ),
			'orddd_bulk_time_slot_page',
			'orddd_bulk_time_slot_section',
			array( __( 'Select Delivery Days/Dates for which you want to create an exclusive Time Slot. To create a time slot for all the weekdays, select "All".', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_time_slot_duration',
			__( 'Time Slot Duration:', 'order-delivery-date' ),
			array( 'orddd_time_slot_settings', 'orddd_time_slot_duration_callback' ),
			'orddd_bulk_time_slot_page',
			'orddd_bulk_time_slot_section',
			array( __( 'X minutes per time slot or Duration of each time slot in minutes', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_time_slot_interval',
			__( 'Interval between time slots:', 'order-delivery-date' ),
			array( 'orddd_time_slot_settings', 'orddd_time_slot_interval_callback' ),
			'orddd_bulk_time_slot_page',
			'orddd_bulk_time_slot_section',
			array( __( 'Minutes between each time slot if applicable.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_time_slot_starts_from',
			__( 'Time Slot Starts From:', 'order-delivery-date' ),
			array( 'orddd_time_slot_settings', 'orddd_time_slot_starts_from_callback' ),
			'orddd_bulk_time_slot_page',
			'orddd_bulk_time_slot_section',
			array( __( 'Start time for the time slots.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_time_slot_ends_at',
			__( 'Time Slot Ends At:', 'order-delivery-date' ),
			array( 'orddd_time_slot_settings', 'orddd_time_slot_ends_at_callback' ),
			'orddd_bulk_time_slot_page',
			'orddd_bulk_time_slot_section',
			array( __( 'End time for the time slots.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_bulk_time_slot_lockout',
			__( 'Maximum Order Deliveries per time slot (based on per order):', 'order-delivery-date' ),
			array( 'orddd_time_slot_settings', 'orddd_bulk_time_slot_lockout_callback' ),
			'orddd_bulk_time_slot_page',
			'orddd_bulk_time_slot_section',
			array( __( 'A time slot will become unavailable for further deliveries once these many orders are placed for delivery for that time slot. <br> <em>Note: If Max order deliveries is set, then that will get priority over time slot lockout.</em>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_bulk_time_slot_additional_charges',
			__( 'Additional Charges for time slot and Checkout label:', 'order-delivery-date' ),
			array( 'orddd_time_slot_settings', 'orddd_bulk_time_slot_additional_charges_callback' ),
			'orddd_bulk_time_slot_page',
			'orddd_bulk_time_slot_section',
			array( __( 'Add delivery charges (if applicable) for time slot and add the label to be displayed on Checkout page.', 'order-delivery-date' ) )
		);

		register_setting(
			'orddd_time_slot_settings',
			'orddd_enable_time_slot'
		);

		register_setting(
			'orddd_time_slot_settings',
			'orddd_time_slot_mandatory'
		);

		register_setting(
			'orddd_time_slot_settings',
			'orddd_time_slot_asap'
		);
		register_setting(
			'orddd_time_slot_settings',
			'orddd_global_lockout_time_slots'
		);
		register_setting(
			'orddd_time_slot_settings',
			'orddd_auto_populate_first_available_time_slot'
		);
		
		register_setting(
			'orddd_time_slot_settings',
			'orddd_time_slots_in_list_view'
		);

		register_setting(
			'orddd_time_slot_settings',
			'orddd_time_slot_for_delivery_days'
		);

		register_setting(
			'orddd_time_slot_settings',
			'orddd_delivery_time_slot_log',
			array( 'orddd_time_slot_settings', 'orddd_delivery_time_slot_callback' )
		);
	}

	/**
	 * Add settings fields & Register settings for Custom Delivery Settings
	 *
	 * @since 3.0
	 */
	public function orddd_shipping_based_delivery_callback() {

		add_settings_section(
			'orddd_shipping_based_delivery_section',
			__( 'Custom Delivery Settings', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_delivery_settings_callback' ),
			'orddd_shipping_based_delivery_page'
		);

		add_settings_field(
			'orddd_enable_shipping_based_delivery',
			__( 'Custom delivery:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_enable_shipping_based_delivery_callback' ),
			'orddd_shipping_based_delivery_page',
			'orddd_shipping_based_delivery_section',
			array( __( 'Enable custom Delivery Date and Time Settings.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_enable_shipping_based_delivery_save',
			'',
			array( 'orddd_shipping_based_settings', 'orddd_enable_shipping_based_delivery_save_callback' ),
			'orddd_shipping_based_delivery_page',
			'orddd_shipping_based_delivery_section'
		);

		register_setting(
			'orddd_shipping_based_delivery_settings',
			'orddd_enable_shipping_based_delivery'
		);
	}

	/**
	 * Add settings fields & Register settings for Custom Delivery Date & Time
	 *
	 * @since 3.0
	 */
	public function orddd_shipping_based_settings_callback() {

		add_settings_section(
			'orddd_shipping_based_section',
			__( 'Custom Delivery Date and Time settings', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_admin_settings_callback' ),
			'orddd_shipping_based_page'
		);

		add_settings_field(
			'orddd_custom_delivery_type',
			__( 'Settings based on:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_custom_delivery_type_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_section'
		);

		add_settings_section(
			'orddd_shipping_based_date_settings_section',
			__( 'Date Settings', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_date_settings_callback' ),
			'orddd_shipping_based_page'
		);

		add_settings_field(
			'orddd_shipping_methods_for_product_categories',
			__( 'Shipping Methods:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_methods_for_product_categories_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_date_settings_section'
		);

		add_settings_field(
			'orddd_categories_for_pickup_locations',
			__( 'Product Categories:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_categories_for_pickup_locations_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_date_settings_section'
		);

		add_settings_field(
			'orddd_enable_shipping_based_delivery_date',
			__( 'Enable Delivery Date:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_enable_shipping_based_delivery_date_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_date_settings_section',
			array( __( 'Enable Delivery Date capture on the checkout page for the shipping method.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_delivery_checkout_options',
			__( 'Delivery Checkout options:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_delivery_checkout_options_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_date_settings_section',
			array( __( 'Choose the delivery date option to be displayed on the checkout page.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_delivery_type',
			__( 'Delivery Type:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_delivery_type_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_date_settings_section',
			array( __( 'Select Delivery type.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_weekdays',
			__( 'Delivery Days:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_weekdays_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_date_settings_section',
			array( __( '&nbsp;&nbsp;Select weekdays for deliveries and its Delivery Charges (if any).', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_specific_day',
			__( 'Specific Delivery Dates:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_specific_days_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_date_settings_section',
			array( __( '&nbsp;&nbsp;Select specific delivery date and add additional charges for the date (if any).', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_minimumOrderDays',
			__( 'Minimum Delivery time (in hours):', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_minimum_delivery_time_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_date_settings_section',
			array(
				__(
					'Minimum number of hours required to prepare for delivery. 
            <br><em> Note: This setting will be applied with Same day & Next day cutoff. Please refer <a href="https://www.tychesoftwares.com/how-does-minimum-delivery-time-work-with-same-day-next-day-cutoff-settings/" target="_blank">this post</a> to know more.</em>',
					'order-delivery-date'
				),
			)
		);

		do_action( 'orddd_after_shipping_based_minimum_delivery_time' );

		add_settings_field(
			'orddd_shipping_based_number_of_dates',
			__( 'Number of dates to choose:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_number_of_dates_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_date_settings_section',
			array( __( 'Number of dates available for delivery.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_date_field_mandatory',
			__( 'Mandatory field?:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_date_field_mandatory_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_date_settings_section',
			array( __( 'Selection of delivery date on the checkout page will become mandatory.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_date_lockout',
			__( 'Maximum Order Deliveries per day (based on per order):', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_date_lockout_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_date_settings_section',
			array( __( 'A date will become unavailable for further deliveries once these many orders are placed for delivery for that date.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_max_order_total_for_charges',
			__( 'Minimum order amount for waiving off delivery charges:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_max_order_total_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_date_settings_section',
			array( __( 'When Order Subtotal reaches this minimum amount, all delivery charges from the plugin will be removed. Keep it 0 if you don\'t want to use this option.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_text_block_between_days',
			__( 'Delivery Range', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_text_block_between_days_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_date_settings_section',
			array( __( '</br>Interval of days it takes to deliver an order after submitting the order.', 'order-delivery-date' ) )
		);

		add_settings_section(
			'orddd_shipping_based_time_settings_section',
			__( 'Time Settings', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_time_settings_callback' ),
			'orddd_shipping_based_page'
		);

		add_settings_field(
			'orddd_shipping_based_time_settings',
			__( 'Time Settings:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_time_sliders_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_time_settings_section',
			array( __( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Select time range for the time sliders.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_sameday_cutoff',
			__( 'Cut-off time for same day delivery orders:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_sameday_cutoff_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_time_settings_section',
			array( __( '<br>Current day will be disabled if an order is placed after the time mentioned in this field.<br><i>The timezone is taken from the Settings -> General -> Timezone field.</i>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_same_day_additional_charges',
			__( 'Additional Charges for same day delivery:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_same_day_additional_charges_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_time_settings_section',
			array( __( 'Set additional charges for same day delivery.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_nextday_cutoff',
			__( 'Cut-off time for next day delivery orders:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_nextday_cutoff_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_time_settings_section',
			array( __( '<br>Next day will be disabled if an order is placed after the time mentioned in this field.<br><i>The timezone is taken from the Settings -> General -> Timezone field.</i>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_next_day_additional_charges',
			__( 'Additional Charges for next day delivery:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_next_day_additional_charges_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_time_settings_section',
			array( __( 'Set additional charges for next day delivery.', 'order-delivery-date' ) )
		);

		// next days delivery charges settings
		add_settings_section(
			'orddd_shipping_based_delivery_charges_based_on_days_section',
			__( 'Delivery charges based on number of days (beyond Next Day) / Rush delivery charges', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_delivery_charges_based_on_days_section_callback' ),
			'orddd_shipping_based_page'
		);

		add_settings_field(
			'orddd_shipping_based_delivery_charges_based_on_days_enable',
			__( 'Enable:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_delivery_charges_based_on_days_enable_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_delivery_charges_based_on_days_section',
			array( __( 'If you want to add delivery charges for days beyond the next day, enable this option.', 'order-delivery-date' ), __( 'Use same-day & next-day delivery options if you want to charge for that. In the options provided below, you can set the delivery charges for 2 days & beyond.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_delivery_charges_based_on_days',
			__( 'Additional Charges based on number of days from current date:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_delivery_charges_based_on_days_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_delivery_charges_based_on_days_section'
		);

		// Time slot section
		add_settings_section(
			'orddd_shipping_based_timeslot_section',
			__( 'Time Slot', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_timeslot_callback' ),
			'orddd_shipping_based_page'
		);

		add_settings_field(
			'orddd_shipping_based_timeslot_field_mandatory',
			__( 'Mandatory field?:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_timeslot_field_mandatory_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_timeslot_section',
			array( __( 'Selection of Time slot on the checkout page will become mandatory.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_timeslot_field_asap',
			__( "Show 'As Soon As Possible' option:", 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_timeslot_field_asap_callback' ),
			'orddd_shipping_based_page',
			'orddd_shipping_based_timeslot_section',
			array( __( 'A new option will be added in the Time slot dropdown on checkout page.', 'order-delivery-date' ) )
		);

		/** Individual time slots */
		add_settings_section(
			'orddd_shipping_based_individual_timeslots_section',
			__( 'Add Individual Time Slots', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_individual_timeslots_callback' ),
			'orddd_shipping_based_individual_timeslot_page'
		);

		add_settings_field(
			'orddd_shipping_based_time_slot_for_delivery_days',
			__( 'Time Slot for:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_time_slot_for_delivery_days_callback' ),
			'orddd_shipping_based_individual_timeslot_page',
			'orddd_shipping_based_individual_timeslots_section',
			array( __( 'Select Weekday option or Specific delivery dates option to create a time slot.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_time_slot_for_weekdays',
			__( 'Select Delivery Days/Dates:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_time_slot_for_weekdays_callback' ),
			'orddd_shipping_based_individual_timeslot_page',
			'orddd_shipping_based_individual_timeslots_section',
			array( __( 'Select Delivery Days/Dates for which you want to create an exclusive Time Slot. To create a time slot for all the weekdays, select "All".', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_time_from_hours',
			__( 'Time Slot From/To:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_time_from_hours_callback' ),
			'orddd_shipping_based_individual_timeslot_page',
			'orddd_shipping_based_individual_timeslots_section',
			array( __( 'Select start and end time for the Time slot.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_time_slot_lockout',
			__( 'Maximum Order Deliveries per time slot (based on per order):', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_time_slot_lockout_callback' ),
			'orddd_shipping_based_individual_timeslot_page',
			'orddd_shipping_based_individual_timeslots_section',
			array( __( 'A time slot will become unavailable for further deliveries once these many orders are placed for delivery for that time slot.<br><i>Note: Leave the field blank if the Global Maximum Order Deliveries for Time slots from the General Settings -> Time slot link should be considered.</i>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_time_slot_additional_charges',
			__( 'Additional Charges for time slot and Checkout label:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_time_slot_additional_charges_callback' ),
			'orddd_shipping_based_individual_timeslot_page',
			'orddd_shipping_based_individual_timeslots_section',
			array( __( 'Add delivery charges (if applicable) for time slot and add the label to be displayed on Checkout page.', 'order-delivery-date' ) )
		);

		/** Bulk Add time slots */
		add_settings_section(
			'orddd_shipping_based_bulk_timeslots_section',
			__( 'Bulk Add Time Slots', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_bulk_timeslots_callback' ),
			'orddd_shipping_based_bulk_timeslot_page'
		);

		add_settings_field(
			'orddd_shipping_based_time_slot_for_delivery_days_bulk',
			__( 'Time Slot for:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_time_slot_for_delivery_days_bulk_callback' ),
			'orddd_shipping_based_bulk_timeslot_page',
			'orddd_shipping_based_bulk_timeslots_section',
			array( __( 'Select Weekday option or Specific delivery dates option to create a time slot.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_time_slot_for_weekdays_bulk',
			__( 'Select Delivery Days/Dates:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_time_slot_for_weekdays_bulk_callback' ),
			'orddd_shipping_based_bulk_timeslot_page',
			'orddd_shipping_based_bulk_timeslots_section',
			array( __( 'Select Delivery Days/Dates for which you want to create an exclusive Time Slot. To create a time slot for all the weekdays, select "All".', 'order-delivery-date' ) )
		);
		add_settings_field(
			'orddd_shipping_based_time_slot_duration',
			__( 'Time Slot Duration:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_time_slot_duration_callback' ),
			'orddd_shipping_based_bulk_timeslot_page',
			'orddd_shipping_based_bulk_timeslots_section',
			array( __( 'X minutes per time slot or Duration of each time slot in minutes', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_time_slot_interval',
			__( 'Interval between time slots:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_time_slot_interval_callback' ),
			'orddd_shipping_based_bulk_timeslot_page',
			'orddd_shipping_based_bulk_timeslots_section',
			array( __( 'Minutes between each time slot if applicable.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_time_slot_starts_from',
			__( 'Time Slot Starts From:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_time_slot_starts_from_callback' ),
			'orddd_shipping_based_bulk_timeslot_page',
			'orddd_shipping_based_bulk_timeslots_section',
			array( __( 'Start time for the time slots.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_time_slot_ends_at',
			__( 'Time Slot Ends At:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_time_slot_ends_at_callback' ),
			'orddd_shipping_based_bulk_timeslot_page',
			'orddd_shipping_based_bulk_timeslots_section',
			array( __( 'End time for the time slots.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_time_slot_lockout_bulk',
			__( 'Maximum Order Deliveries per time slot (based on per order):', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_time_slot_lockout_bulk_callback' ),
			'orddd_shipping_based_bulk_timeslot_page',
			'orddd_shipping_based_bulk_timeslots_section',
			array( __( 'A time slot will become unavailable for further deliveries once these many orders are placed for delivery for that time slot.<br><i>Note: Leave the field blank if the Global Maximum Order Deliveries for Time slots from the General Settings -> Time slot link should be considered.</i>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_time_slot_additional_charges_bulk',
			__( 'Additional Charges for time slot and Checkout label:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_time_slot_additional_charges_bulk_callback' ),
			'orddd_shipping_based_bulk_timeslot_page',
			'orddd_shipping_based_bulk_timeslots_section',
			array( __( 'Add delivery charges (if applicable) for time slot and add the label to be displayed on Checkout page.', 'order-delivery-date' ) )
		);

		add_settings_section(
			'orddd_shipping_based_save_timeslot',
			'',
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_bulk_timeslots_callback' ),
			'orddd_shipping_based_save_timeslot_page'
		);
		add_settings_field(
			'orddd_shipping_based_time_slot_save',
			'',
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_time_slot_save_callback' ),
			'orddd_shipping_based_save_timeslot_page',
			'orddd_shipping_based_save_timeslot'
		);

		add_settings_section(
			'orddd_shipping_based_holidays_section',
			__( 'Holidays', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_holidays_callback' ),
			'orddd_shipping_based_holidays_page'
		);

		add_settings_field(
			'orddd_enable_global_holidays',
			__( 'Use Global Holidays:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_enable_global_holidays_callback' ),
			'orddd_shipping_based_holidays_page',
			'orddd_shipping_based_holidays_section',
			array( __( 'Use same holidays as added under General Settings -> Holidays link. You can add more holidays below.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_holiday_name',
			__( 'Name:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_holiday_name_callback' ),
			'orddd_shipping_based_holidays_page',
			'orddd_shipping_based_holidays_section',
			array( __( 'Enter the name of the holiday here.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_holiday_from_date',
			__( 'Holiday Range:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_holiday_from_date_callback' ),
			'orddd_shipping_based_holidays_page',
			'orddd_shipping_based_holidays_section',
			array( __( 'Select the start and end date range for holiday here.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_allow_recurring_holiday',
			__( 'Allow Recurring:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_allow_recurring_holiday_callback' ),
			'orddd_shipping_based_holidays_page',
			'orddd_shipping_based_holidays_section',
			array( __( 'Enable to block the holidays for every year. For example, national holidays like 15th August (India\'s Independence Day) should be disabled for every year.', 'order-delivery-date' ) )
		);

		do_action( 'add_settings_in_shipping_holidays' );

		add_settings_field(
			'orddd_shipping_based_holiday_save',
			'',
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_holiday_save_callback' ),
			'orddd_shipping_based_holidays_page',
			'orddd_shipping_based_holidays_section'
		);

		add_settings_section(
			'orddd_shipping_based_appearance_section',
			__( 'Appearance', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_appearance_section_callback' ),
			'orddd_shipping_based_appearance_page'
		);

		add_settings_field(
			'orddd_shipping_based_delivery_date_field_label',
			__( 'Date Field Label:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_delivery_date_field_label_callback' ),
			'orddd_shipping_based_appearance_page',
			'orddd_shipping_based_appearance_section',
			array( __( 'Choose the label that is to be displayed for the field on checkout page. HTML allowed.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_shipping_based_delivery_timeslot_field_label',
			__( 'Time slot Field Label:', 'order-delivery-date' ),
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_delivery_timeslot_field_label_callback' ),
			'orddd_shipping_based_appearance_page',
			'orddd_shipping_based_appearance_section',
			array( __( 'Choose a label that is to be displayed for the time slot field on the checkout page.', 'order-delivery-date' ) )
		);

		$row_id = '';
		if ( ( isset( $_GET['action'] ) && sanitize_text_field( $_GET['action'] ) == 'shipping_based' ) && ( isset( $_GET['mode'] ) && sanitize_text_field( $_GET['mode'] ) == 'edit' ) ) { // phpcs:ignore
			if ( isset( $_GET['row_id'] ) ) {
				$row_id = $_GET['row_id'];
			}
		} else {
			if ( isset( $_POST['edit_row_id'] ) ) {
				$row_id = $_POST['edit_row_id'];
			}
		}

		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );

		register_setting(
			'orddd_shipping_based_settings',
			'orddd_shipping_based_settings_' . $option_key,
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_settings_save_callback' )
		);

		register_setting(
			'orddd_shipping_based_settings',
			'orddd_shipping_based_settings_option_key',
			array( 'orddd_shipping_based_settings', 'orddd_shipping_based_settings_option_key_callback' )
		);
	}

	/**
	 * Add settings fields & Register settings to sync Google Calendar
	 *
	 * @since 4.0
	 */
	public function orddd_calendar_sync_settings_callback() {

		add_settings_section(
			'orddd_calendar_sync_general_settings_section',
			__( 'General Settings', 'order-delivery-date' ),
			array( 'orddd_calendar_sync_settings', 'orddd_calendar_sync_general_settings_callback' ),
			'orddd_calendar_sync_settings_page'
		);

		add_settings_field(
			'orddd_calendar_event_location',
			__( 'Event Location', 'order-delivery-date' ),
			array( 'orddd_calendar_sync_settings', 'orddd_calendar_event_location_callback' ),
			'orddd_calendar_sync_settings_page',
			'orddd_calendar_sync_general_settings_section',
			array( __( '<br>Enter the text that will be used in the location field of the calendar event. If left empty, the website description will be used. <br><i>Note: You can use PICKUP_LOCATION, ADDRESS, FULL_ADDRESS, ADDRESS_SHIP, FULL_ADDRESS_SHIP and CITY placeholders which will be replaced by their real values.</i>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_calendar_event_summary',
			__( 'Event summary (name)', 'order-delivery-date' ),
			array( 'orddd_calendar_sync_settings', 'orddd_calendar_event_summary_callback' ),
			'orddd_calendar_sync_settings_page',
			'orddd_calendar_sync_general_settings_section'
		);

		add_settings_field(
			'orddd_calendar_event_description',
			__( 'Event Description', 'order-delivery-date' ),
			array( 'orddd_calendar_sync_settings', 'orddd_calendar_event_description_callback' ),
			'orddd_calendar_sync_settings_page',
			'orddd_calendar_sync_general_settings_section',
			array( __( '<br>For the above 2 fields, you can use the following placeholders which will be replaced by their real values:&nbsp;SITE_NAME, CLIENT, PRODUCTS, PRODUCT_WITH_QTY, PRODUCT_WITH_CATEGORY, ORDER_DATE_TIME, ORDER_DATE, ORDER_NUMBER, PRICE, PHONE, NOTE, ADDRESS, FULL_ADDRESS, CLIENT_SHIP, FULL_ADDRESS_SHIP, SHIPPING_METHOD_TITLE, PAYMENT_METHOD_TITLE, PICKUP_LOCATION, ORDER_WEBLINK, ORDER_STATUS, EMAIL (Client\'s email)	', 'order-delivery-date' ) )
		);

		add_settings_section(
			'orddd_calendar_sync_customer_settings_section',
			__( 'Customer Add to Calendar button Settings', 'order-delivery-date' ),
			array( 'orddd_calendar_sync_settings', 'orddd_calendar_sync_customer_settings_callback' ),
			'orddd_calendar_sync_settings_page'
		);

		add_settings_field(
			'orddd_add_to_calendar_order_received_page',
			__( 'Show Add to Calendar button on Order received page', 'order-delivery-date' ),
			array( 'orddd_calendar_sync_settings', 'orddd_add_to_calendar_order_received_page_callback' ),
			'orddd_calendar_sync_settings_page',
			'orddd_calendar_sync_customer_settings_section',
			array( __( 'Show Add to Calendar button on the Order Received page for the customers.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_add_to_calendar_customer_email',
			__( 'Show Add to Calendar button in the Customer notification email', 'order-delivery-date' ),
			array( 'orddd_calendar_sync_settings', 'orddd_add_to_calendar_customer_email_callback' ),
			'orddd_calendar_sync_settings_page',
			'orddd_calendar_sync_customer_settings_section',
			array( __( 'Show Add to Calendar button in the Customer notification email.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_add_to_calendar_my_account_page',
			__( 'Show Add to Calendar button on My account', 'order-delivery-date' ),
			array( 'orddd_calendar_sync_settings', 'orddd_add_to_calendar_my_account_page_callback' ),
			'orddd_calendar_sync_settings_page',
			'orddd_calendar_sync_customer_settings_section',
			array( __( 'Show Add to Calendar button on My account page for the customers.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_calendar_in_same_window',
			__( 'Open Calendar in Same Window', 'order-delivery-date' ),
			array( 'orddd_calendar_sync_settings', 'orddd_calendar_in_same_window_callback' ),
			'orddd_calendar_sync_settings_page',
			'orddd_calendar_sync_customer_settings_section',
			array( __( 'As default, the Calendar is opened in a new tab or window. If you check this option, user will be redirected to the Calendar from the same page, without opening a new tab or window.', 'order-delivery-date' ) )
		);

		add_settings_section(
			'orddd_calendar_sync_admin_settings_section',
			__( 'Admin Calendar Sync Settings', 'order-delivery-date' ),
			array( 'orddd_calendar_sync_settings', 'orddd_calendar_sync_admin_settings_section_callback' ),
			'orddd_calendar_sync_settings_page'
		);

		add_settings_field(
			'orddd_calendar_sync_integration_mode',
			__( 'Integration Mode', 'order-delivery-date' ),
			array( 'orddd_calendar_sync_settings', 'orddd_calendar_sync_integration_mode_callback' ),
			'orddd_calendar_sync_settings_page',
			'orddd_calendar_sync_admin_settings_section',
			array( __( '<br>Select method of integration. "Sync Automatically" will add the delivery events to the Google calendar, which is set in the "Calendar to be used" field, automatically when a customer places an order. Also, an "Add to Calendar" button is added on the Delivery Calendar page in admin to Sync past orders. <br>"Sync Manually" will add an "Add to Google Calendar" button in emails received by admin and New customer order.<br>"Disabled" will disable the integration with Google Calendar.<br>Note: Import of the events will work manually using .ics link.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_sync_calendar_instructions',
			__( 'Instructions', 'order-delivery-date' ),
			array( 'orddd_calendar_sync_settings', 'orddd_sync_calendar_instructions_callback' ),
			'orddd_calendar_sync_settings_page',
			'orddd_calendar_sync_admin_settings_section'
		);

		add_settings_field(
			'orddd_calendar_key_file_name',
			__( 'Key file name', 'order-delivery-date' ),
			array( 'orddd_calendar_sync_settings', 'orddd_calendar_key_file_name_callback' ),
			'orddd_calendar_sync_settings_page',
			'orddd_calendar_sync_admin_settings_section',
			array( __( '<br>Enter key file name here without extention, e.g. ab12345678901234567890-privatekey.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_calendar_service_acc_email_address',
			__( 'Service account email address', 'order-delivery-date' ),
			array( 'orddd_calendar_sync_settings', 'orddd_calendar_service_acc_email_address_callback' ),
			'orddd_calendar_sync_settings_page',
			'orddd_calendar_sync_admin_settings_section',
			array( __( '<br>Enter Service account email address here, e.g. 1234567890@developer.gserviceaccount.com.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_calendar_id',
			__( 'Calendar to be used', 'order-delivery-date' ),
			array( 'orddd_calendar_sync_settings', 'orddd_calendar_id_callback' ),
			'orddd_calendar_sync_settings_page',
			'orddd_calendar_sync_admin_settings_section',
			array( __( '<br>Enter the ID of the calendar in which your deliveries will be saved, e.g. abcdefg1234567890@group.calendar.google.com.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_calendar_test_connection',
			'',
			array( 'orddd_calendar_sync_settings', 'orddd_calendar_test_connection_callback' ),
			'orddd_calendar_sync_settings_page',
			'orddd_calendar_sync_admin_settings_section'
		);

		add_settings_field(
			'orddd_admin_add_to_calendar_delivery_calendar',
			__( 'Show "Export to Google Calendar" button on Delivery Calendar page', 'order-delivery-date' ),
			array( 'orddd_calendar_sync_settings', 'orddd_admin_add_to_calendar_delivery_calendar_callback' ),
			'orddd_calendar_sync_settings_page',
			'orddd_calendar_sync_admin_settings_section',
			array( __( 'Show "Export to Google Calendar" button on the Order Delivery Date -> Delivery Calendar page.<br><i>Note: This button can be used to export the already placed orders with future deliveries from the current date to the calendar used above.</i>', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_admin_add_to_calendar_email_notification',
			__( 'Show Add to Calendar button in New Order email notification', 'order-delivery-date' ),
			array( 'orddd_calendar_sync_settings', 'orddd_admin_add_to_calendar_email_notification_callback' ),
			'orddd_calendar_sync_settings_page',
			'orddd_calendar_sync_admin_settings_section',
			array( __( 'Show "Add to Calendar" button in the New Order email notification.', 'order-delivery-date' ) )
		);

		add_settings_section(
			'orddd_calendar_import_ics_feeds_section',
			__( 'Import Events', 'order-delivery-date' ),
			array( 'orddd_calendar_sync_settings', 'orddd_calendar_import_ics_feeds_section_callback' ),
			'orddd_calendar_sync_settings_page'
		);

		add_settings_field(
			'orddd_ics_feed_url_instructions',
			__( 'Instructions', 'order-delivery-date' ),
			array( 'orddd_calendar_sync_settings', 'orddd_ics_feed_url_instructions_callback' ),
			'orddd_calendar_sync_settings_page',
			'orddd_calendar_import_ics_feeds_section'
		);

		add_settings_field(
			'orddd_ics_feed_url',
			__( 'iCalendar/.ics Feed URL', 'order-delivery-date' ),
			array( 'orddd_calendar_sync_settings', 'orddd_ics_feed_url_callback' ),
			'orddd_calendar_sync_settings_page',
			'orddd_calendar_import_ics_feeds_section'
		);

		add_settings_field(
			'orddd_real_time_import',
			__( 'Import frequency', 'order-delivery-date' ),
			array( 'orddd_calendar_sync_settings', 'orddd_real_time_import_callback' ),
			'orddd_calendar_sync_settings_page',
			'orddd_calendar_import_ics_feeds_section',
			array( __( 'Import events from Google calendar based on the time set below. By default, all events from the Google calendar will be imported once every 24 hours.', 'order-delivery-date' ) )
		);

		add_settings_field(
			'orddd_wp_cron_minutes',
			__( 'Enter Import frequency (in minutes)', 'order-delivery-date' ),
			array( 'orddd_calendar_sync_settings', 'orddd_wp_cron_minutes_callback' ),
			'orddd_calendar_sync_settings_page',
			'orddd_calendar_import_ics_feeds_section',
			array( __( 'The duration in minutes at which events from the Google Calendar ICS feeds will be imported automatically in the store. <i>Note: Setting this to a lower value then 10 minutes may impact the performance of your store.</i>' ) )
		);

		register_setting(
			'orddd_calendar_sync_settings',
			'orddd_calendar_event_location'
		);

		register_setting(
			'orddd_calendar_sync_settings',
			'orddd_add_to_calendar_order_received_page'
		);

		register_setting(
			'orddd_calendar_sync_settings',
			'orddd_add_to_calendar_customer_email'
		);

		register_setting(
			'orddd_calendar_sync_settings',
			'orddd_add_to_calendar_my_account_page'
		);

		register_setting(
			'orddd_calendar_sync_settings',
			'orddd_calendar_in_same_window'
		);

		register_setting(
			'orddd_calendar_sync_settings',
			'orddd_calendar_sync_integration_mode'
		);

		register_setting(
			'orddd_calendar_sync_settings',
			'orddd_calendar_details_1'
		);

		register_setting(
			'orddd_calendar_sync_settings',
			'orddd_calendar_event_summary'
		);

		register_setting(
			'orddd_calendar_sync_settings',
			'orddd_calendar_event_description'
		);

		register_setting(
			'orddd_calendar_sync_settings',
			'orddd_admin_add_to_calendar_email_notification'
		);

		register_setting(
			'orddd_calendar_sync_settings',
			'orddd_admin_add_to_calendar_delivery_calendar'
		);

		register_setting(
			'orddd_calendar_sync_settings',
			'orddd_real_time_import'
		);

		register_setting(
			'orddd_calendar_sync_settings',
			'orddd_wp_cron_minutes'
		);
	}

	/**
	 * Add Order Delivery Date menu in the Admin Dashboard
	 *
	 * @since 2.8.3
	 */
	public function orddd_menu() {
		add_menu_page( 'Order Delivery Date', 'Order Delivery Date', 'manage_woocommerce', 'order_delivery_date', array( &$this, 'order_delivery_date_settings' ) );
		$page = add_submenu_page( 'order_delivery_date', __( 'Delivery Calendar', 'order-delivery-date' ), __( 'Delivery Calendar', 'order-delivery-date' ), 'manage_woocommerce', 'orddd_view_orders', array( 'orddd_class_view_deliveries', 'orddd_view_calendar_orders_page' ) );
		$page = add_submenu_page( 'order_delivery_date', __( 'Settings', 'order-delivery-date' ), __( 'Settings', 'order-delivery-date' ), 'manage_woocommerce', 'order_delivery_date', array( &$this, 'order_delivery_date_settings' ) );
		$page = add_submenu_page( 'order_delivery_date', 'Activate License', 'Activate License', 'manage_woocommerce', 'edd_sample_license_page', array( 'orddd_license', 'orddd_edd_sample_license_page' ) );
		remove_submenu_page( 'order_delivery_date', 'order_delivery_date' );
		do_action( 'orddd_add_submenu' );
	}

	/**
	 * Display Settings in Order Delivery Date menu
	 *
	 * @since 2.8.3
	 */
	public function order_delivery_date_settings() {
		global $orddd_date_formats, $orddd_number_of_months, $orddd_time_formats, $orddd_calendar_themes, $orddd_weekdays, $orddd_calendar_languages, $wpdb, $woocommerce;
		$plugin_path             = plugins_url();
		$check_prev              = array();
		$action                  = '';
		$active_general_settings = '';
		$calendar_sync_settings  = '';
		$active_shipping_based   = '';
		$active_advance_settings = '';
		if ( isset( $_GET['action'] ) ) { //phpcs:ignore
			$action = sanitize_text_field( $_GET['action'] ); //phpcs:ignore
		} else {
			$action = 'general_settings';
		}

		if ( isset( $_GET['mode'] ) ) { //phpcs:ignore
			$mode = sanitize_text_field( $_GET['mode'] ); //phpcs:ignore
		} else {
			$mode = '';
		}

		if ( 'general_settings' === $action ) {
			$active_general_settings = 'nav-tab-active';
		}

		if ( 'advance_settings' === $action ) {
			$active_advance_settings = 'nav-tab-active';
		}

		if ( 'shipping_based' === $action ) {
			$active_shipping_based = 'nav-tab-active';
		}

		if ( 'calendar_sync_settings' === $action ) {
			$calendar_sync_settings = 'nav-tab-active';
		}
		?>
		<h2><?php _e( 'Order Delivery Date and Time Settings', 'order-delivery-date' ); ?></h2>
		<?php
		settings_errors();
		?>
		<div class="wrap woocommerce">
			<nav class="nav-tab-wrapper woo-nav-tab-wrapper" id="orddd_settings_tabs">
				<a href="admin.php?page=order_delivery_date&action=general_settings" class="nav-tab <?php echo esc_html( $active_general_settings ); ?>"><?php _e( 'General Settings', 'order-delivery-date' ); ?> </a>
				<?php
				if ( get_option( 'orddd_enable_day_wise_settings' ) == 'on' ) {
					?>
					<a href="admin.php?page=order_delivery_date&action=advance_settings" class="nav-tab <?php echo esc_html( $active_advance_settings ); ?>"> <?php _e( 'Weekday Settings', 'order-delivery-date' ); ?> </a>
				<?php } ?>
				<a href="admin.php?page=order_delivery_date&action=shipping_based" class="nav-tab <?php echo esc_html( $active_shipping_based ); ?>"> <?php _e( 'Custom Delivery Settings', 'order-delivery-date' ); ?> </a>
				<a href="admin.php?page=order_delivery_date&action=calendar_sync_settings" class="nav-tab <?php echo esc_html( $calendar_sync_settings ); ?>"> <?php _e( 'Google Calendar Sync', 'order-delivery-date' ); ?> 
				</a>
				<?php
					do_action( 'orddd_add_settings_tab' );
				?>
			</nav>
		</div>
		<?php
		do_action( 'orddd_add_tab_content' );
		if ( $action == 'general_settings' ) {
			$date_settings_class = $shipping_days_class = $delivery_date_class = $time_settings_class = $holidays_class = $appearance_class = $time_slot_class = $additional_settings_class = $section = '';
			if ( isset( $_GET['section'] ) ) {
				$section = sanitize_text_field( $_GET['section'] );
			} else {
				$section = '';
			}

			if ( $section == 'date_settings' || $section == '' ) {
				$date_settings_class = 'current';
			}

			if ( $section == 'delivery_dates' ) {
				$delivery_date_class = 'current';
			}

			if ( $section == 'time_settings' ) {
				$time_settings_class = 'current';
			}

			if ( $section == 'holidays' ) {
				$holidays_class = 'nav-tab-active';
			}

			if ( $section == 'appearance' ) {
				$appearance_class = 'current';
			}

			if ( $section == 'time_slot' ) {
				$time_slot_class = 'current';
			}

			if ( $section == 'additional_settings' ) {
				$additional_settings_class = 'current';
			}

			?>
			<ul class="subsubsub" id="orddd_general_settings_list">
				<li>
					<a href="admin.php?page=order_delivery_date&action=general_settings&section=date_settings" class="<?php echo $date_settings_class; ?>"><?php _e( 'Date Settings', 'order-delivery-date' ); ?> </a> |
				</li>
				<li>
					<a href="admin.php?page=order_delivery_date&action=general_settings&section=delivery_dates" class="<?php echo $delivery_date_class; ?>"><?php _e( 'Specific Delivery Dates', 'order-delivery-date' ); ?> </a> | 
				</li>
				<li>
					<a href="admin.php?page=order_delivery_date&action=general_settings&section=time_settings" class="<?php echo $time_settings_class; ?>"><?php _e( 'Time Settings', 'order-delivery-date' ); ?> </a> | 
				</li>
				<li>
					<a href="admin.php?page=order_delivery_date&action=general_settings&section=holidays" class="<?php echo $holidays_class; ?>"><?php _e( 'Holidays', 'order-delivery-date' ); ?> </a> |
				</li>
				<li>
					<a href="admin.php?page=order_delivery_date&action=general_settings&section=appearance" class="<?php echo $appearance_class; ?>"><?php _e( 'Appearance', 'order-delivery-date' ); ?> </a> |
				</li>
				<li>
					<a href="admin.php?page=order_delivery_date&action=general_settings&section=time_slot" class="<?php echo $time_slot_class; ?>"><?php _e( 'Time Slot', 'order-delivery-date' ); ?> </a> |
				</li>
				<li>
					<a href="admin.php?page=order_delivery_date&action=general_settings&section=additional_settings" class="<?php echo $additional_settings_class; ?>"><?php _e( 'Additional Settings', 'order-delivery-date' ); ?> </a>
				</li>
				<?php do_action( 'orddd_general_settings_links', $section ); ?>
			</ul>
			<br class="clear">
			<?php
			if ( $section == 'date_settings' || $section == '' ) {
				print( '<div id="content">
                    <form method="post" action="options.php">' );
						settings_fields( 'orddd_date_settings' );
						do_settings_sections( 'orddd_date_settings_page' );
						submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true );
					print( '</form>
                </div>' );
			}

			if ( $section == 'delivery_dates' ) {
				print( '<div id="content">
                    <div class="orddd-col-left" >
                        <form method="post" action="options.php">' );
							settings_fields( 'orddd_delivery_days_settings' );
							do_settings_sections( 'orddd_delivery_days_page' );
							submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true );
						print( '</form>
                    </div>
                </div>' );
				echo "<div class='orddd-col-right'><h3 id='delivery_date_table_head'>" . __( 'Specific Delivery Dates', 'order-delivery-date' ) . '</h3>';
				include_once 'includes/class-view-delivery-days.php';
				$orddd_table = new ORDDD_View_Delivery_Dates_Table();
				$orddd_table->orddd_prepare_items();
				?>
					<div id = "orddd_delivery_dates_list">
						<form id="delivery-dates" method="POST" >
							<input type="hidden" name="page" value="order_delivery_date" />
							<input type="hidden" name="tab" value="general_settings" />
							<input type="hidden" name="section" value="delivery_dates" />
							<?php $orddd_table->display(); ?>
						</form>
					</div>
				</div>
				<?php
			}

			if ( $section == 'time_settings' ) {
				print( '<div id="content">
                    <form method="post" action="options.php">' );
						settings_fields( 'orddd_time_settings' );
						do_settings_sections( 'orddd_time_settings_page' );
						submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true );
					print( '</form>
                </div>' );
			}

			if ( $section == 'holidays' ) {
				print( '<div id="content">
		        	<div class="orddd-col-left" >
	                    <form method="post" action="options.php">
	                    	<div>' );
							settings_fields( 'orddd_holiday_settings' );
							do_settings_sections( 'orddd_holidays_page' );
							submit_button( __( 'Create Holidays', 'order-delivery-date' ), 'primary', 'save', true );
						print( '</form>
                	</div>
            	</div>' );

				echo "<div class='orddd-col-right'><h3 id='holidays_table_head'>" . __( 'Holidays', 'order-delivery-date' ) . '</h3>';
				?>
				<p><?php echo __( 'Even though you can have different holidays for the Custom Delivery Settings, but if you want these below holidays to be applied to the Custom Delivery Settings, please enable the <strong> Use Global Holidays </strong> setting when adding/editing the custom delivery setting.', 'order-delivery-date' ) ?></p>
				<?php
					include_once 'includes/class-view-holidays.php';
					$orddd_table = new ORDDD_View_Holidays_Table();
					$orddd_table->orddd_prepare_items();
				?>
					<div id = "orddd_holidays_list">
						<form id="holidays" method="POST" >
							<input type="hidden" name="page" value="order_delivery_date" />
							<input type="hidden" name="tab" value="general_settings" />
							<input type="hidden" name="section" value="holidays" />
							<?php $orddd_table->display(); ?>
						</form>
					</div>
				</div>
				<?php
			}

			if ( $section == 'appearance' ) {
				print( '<div id="content">
                    <form method="post" action="options.php">' );
						settings_fields( 'orddd_appearance_settings' );
						do_settings_sections( 'orddd_appearance_page' );
						submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true );
					print( '</form>
    			</div>' );
			}

			if ( $section == 'time_slot' ) {
				print( '<div id="content">
                    <form method="post" action="options.php">' );
						settings_fields( 'orddd_time_slot_settings' );
						do_settings_sections( 'orddd_time_slot_page' );
				?>
						<section>
							<button id="orddd_individual" class="button button-secondary">Add Individual Time Slots</button>
							<button id="orddd_bulk" class="button button-secondary">Add Time slots in Bulk</button>
						</section>

						<section id="orddd_individual_time_slot_page">
							<?php do_settings_sections( 'orddd_individual_time_slot_page' ); ?>
						</section>

						<section id="orddd_bulk_time_slot_page">
							<?php do_settings_sections( 'orddd_bulk_time_slot_page' ); ?>
						</section>

						<section id="orddd_save_settings">
							<?php submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true ); ?>
						</section>
						<input type="hidden" name="orddd_individual_or_bulk" id="orddd_individual_or_bulk" value="individual">
						<?php
						wp_nonce_field( 'orddd_save_time_slot_field_nonce', 'orddd_save_time_slot_field' );
						print( '</form>
                </div>' );

						$existing_timeslots_str = get_option( 'orddd_disable_time_slot_log' );
						$existing_timeslots_arr = array();
						if ( $existing_timeslots_str == 'null' || $existing_timeslots_str == '' || $existing_timeslots_str == '{}' || $existing_timeslots_str == '[]' ) { // phpcs:ignore
							$existing_timeslots_arr = array();
						} else {
							$existing_timeslots_arr = json_decode( $existing_timeslots_str );
						}
						?>
				<a href="admin.php?page=order_delivery_date&action=general_settings&section=block_time_slot_settings" class="block_time_slot">
				<?php
				_e( 'Block Time Slots', 'order-delivery-date' );
					echo ' (' . count( $existing_timeslots_arr ) . ')';
				?>

				</a>
				<?php
				echo "<h3 id='timeslots_table_head'>" . __( 'Time Slots', 'order-delivery-date' ) . '</h3>';
				include_once 'includes/class-view-time-slots.php';
				$orddd_table = new ORDDD_View_Time_Slots_Table();
				$orddd_table->orddd_prepare_items();
				?>
				<div id = "orddd_time_slot_list">
					<form id="time-slot" method="POST" >
						<input type="hidden" name="page" value="order_delivery_date" />
						<input type="hidden" name="tab" value="general_settings" />
						<input type="hidden" name="section" value="time_slot" />
						<?php $orddd_table->display(); ?>
					</form>
				</div>
				<?php

			}

			if ( $section == 'block_time_slot_settings' ) {
				?>
				<a href="admin.php?page=order_delivery_date&action=general_settings&section=time_slot" class="back_to_time_slot"><?php _e( 'Back to Time Slots', 'order-delivery-date' ); ?> </a>
				<?php
				print( '<div id="content">
                    <form method="post" action="options.php">' );
						settings_fields( 'orddd_disable_time_slot_settings' );
						do_settings_sections( 'orddd_holidays_disable_page' );
						submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true );
					print( '</form>
                </div>' );
				echo "<h3 id='block_timeslot_table_head'>" . __( 'Blocked Time Slots', 'order-delivery-date' ) . '</h3>';
				include_once 'includes/class-view-disable-time-slots.php';
				$orddd_table_test = new ORDDD_View_Disable_Time_Slots_Table();
				$orddd_table_test->orddd_prepare_items();
				?>
				<div id = "orddd_disable_time_slot_list">
					<form id="time-slot" method="POST" >
						<input type="hidden" name="page" value="order_delivery_date" />
						<input type="hidden" name="tab" value="general_settings" />
						<input type="hidden" name="section" value="block_time_slot_settings" />
						<?php $orddd_table_test->display(); ?>
					</form>
				</div>
				<?php
			}

			if ( $section == 'additional_settings' ) {
				print( '<div id="content">
                    <form method="post" action="options.php">' );
						settings_fields( 'orddd_additional_settings' );
						do_settings_sections( 'orddd_additional_settings_page' );
						submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true );
					print( '</form>
                </div>' );
			}
		} elseif ( $action == 'advance_settings' ) {

			include_once 'includes/views/html-advance-settings.php';

		} elseif ( $action == 'shipping_based' && $mode != 'add_settings' && $mode != 'edit' ) {
			print( '<div id="content">
                <form method="post" action="options.php">' );
					settings_fields( 'orddd_shipping_based_delivery_settings' );
					do_settings_sections( 'orddd_shipping_based_delivery_page' );
				print( '</form>
            </div>' );

			$number_of_shipping_settings = orddd_common::orddd_get_shipping_settings_count();
			if ( $number_of_shipping_settings === 0 &&
				get_option( 'orddd_enable_shipping_based_delivery' ) == 'on' ) {
				echo 'There are no custom delivery settings created. Please <a href=\'admin.php?page=order_delivery_date&action=shipping_based&mode=add_settings\' class=\'add_shipping_setting\'>click here</a> to add delivery date and time settings.';
			} else {
				echo '<a href=\'admin.php?page=order_delivery_date&action=shipping_based&mode=add_settings\' class=\'add_shipping_setting\'>' . __( 'Add Custom Delivery Settings', 'order-delivery-date' ) . '</a>&nbsp;<a href="https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/custom-delivery-settings/?utm_source=userwebsite&utm_medium=link&utm_campaign=OrderDeliveryDateProSetting" target="_blank" class="dashicons dashicons-external" style="line-height:unset;"></a>';
				include_once 'includes/class-view-shipping-settings.php';
				$orddd_table = new ORDDD_View_Shipping_Settings_Table();
				$orddd_table->orddd_prepare_items();
				?>
				<div id = "orddd_shipping_based_settings_list">
					<form id="custom-delivery-settings" method="POST" >
						<input type="hidden" name="page" value="order_delivery_date" />
						<input type="hidden" name="tab" value="shipping_based" />
						<?php $orddd_table->display(); ?>
					</form>                        
				</div>
				<?php
			}
		} elseif ( $action == 'shipping_based' && ( $mode == 'add_settings' || $mode == 'edit' ) ) {
			print( '<div id="content">
                <form method="post" action="options.php">' );
					settings_fields( 'orddd_shipping_based_settings' );
					do_settings_sections( 'orddd_shipping_based_page' );
			?>
					<section>
						<button id="orddd_individual" class="button button-secondary">Add Individual Time Slots</button>
						<button id="orddd_bulk" class="button button-secondary">Add Time slots in Bulk</button>
					</section>
						<section id="orddd_individual_time_slot_page">
							<?php do_settings_sections( 'orddd_shipping_based_individual_timeslot_page' ); ?>
						</section>

						<section id="orddd_bulk_time_slot_page">
							<?php do_settings_sections( 'orddd_shipping_based_bulk_timeslot_page' ); ?>
						</section>
						<input type="hidden" name="orddd_individual_or_bulk" id="orddd_individual_or_bulk" value="individual">
					<?php

					do_settings_sections( 'orddd_shipping_based_save_timeslot_page' );
					do_settings_sections( 'orddd_shipping_based_holidays_page' );
					do_settings_sections( 'orddd_shipping_based_appearance_page' );
					print( '<table>
                        <tr>
                            <td>' );
							submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true );
							print( '</td>
                            <td>' );
								$other_attributes = array( 'formaction' => 'admin.php?page=order_delivery_date&action=shipping_based' );
								submit_button( __( 'Cancel', 'order-delivery-date' ), '', 'cancel', true, $other_attributes );
							print( '</td>
                        </tr>
                    </table>
                </form>
            </div>' );
		} elseif ( $action == 'calendar_sync_settings' ) {
			print( '<div id="content">
                <form method="post" action="options.php">' );
					settings_fields( 'orddd_calendar_sync_settings' );
					do_settings_sections( 'orddd_calendar_sync_settings_page' );
					submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true );
			   print( '</form>
            </div>' );
		}

        do_action( 'orddd_add_after_tab_content' );

	}

	/**
	 * Remove the element from the array passed
	 *
	 * @param array  $array
	 * @param string $key
	 * @param string $value
	 * @return array
	 */
	public static function removeElementWithValue( $array, $key, $value ) {
		foreach ( $array as $subKey => $subArray ) {
			if ( $subArray[ $key ] == $value ) {
				unset( $array[ $subKey ] );
			}
		}
		return $array;
	}

	/**
	 * Delete Holidays, Delivery Date or Time slots
	 *
	 * @since
	 */
	public function orddd_delete_settings() {
		if ( ( isset( $_POST['page'] ) && sanitize_text_field( $_POST['page'] ) == 'order_delivery_date' ) &&
			( isset( $_POST['tab'] ) && sanitize_text_field( $_POST['tab'] ) == 'shipping_based' ) ) {
			if ( ( isset( $_POST['action'] ) && sanitize_text_field( $_POST['action'] ) == 'orddd_delete' ) ||
				( isset( $_POST['action2'] ) && sanitize_text_field( $_POST['action2'] ) == 'orddd_delete' ) ) {
				$custom_delivery_setting = array();
				if ( isset( $_POST['custom_delivery_setting'] ) ) {
					$custom_delivery_setting = $_POST['custom_delivery_setting'];
				}
				foreach ( $custom_delivery_setting as $c_key => $c_value ) {
					delete_option( 'orddd_shipping_based_settings_' . $c_value );
				}
			}
			wp_safe_redirect( admin_url( '/admin.php?page=order_delivery_date&action=shipping_based' ) );
		}

		if ( ( isset( $_POST['page'] ) && sanitize_text_field( $_POST['page'] ) == 'order_delivery_date' ) &&
			( isset( $_POST['tab'] ) && sanitize_text_field( $_POST['tab'] ) == 'general_settings' ) &&
			( isset( $_POST['section'] ) && sanitize_text_field( $_POST['section'] ) == 'delivery_dates' ) ) {
			if ( ( isset( $_POST['action'] ) && sanitize_text_field( $_POST['action'] ) == 'orddd_delete' ) ||
				( isset( $_POST['action2'] ) && sanitize_text_field( $_POST['action2'] ) == 'orddd_delete' ) ) {
				$delivery_date = array();
				if ( isset( $_POST['delivery_date'] ) ) {
					$delivery_date = $_POST['delivery_date'];
				}

				foreach ( $delivery_date as $d_key => $d_value ) {
					// Delivery Dates
					$delivery_dates = get_option( 'orddd_delivery_dates' );
					if ( $delivery_dates != '' && $delivery_dates != '{}' && $delivery_dates != '[]' && $delivery_dates != 'null' ) {
						$delivery_dates_new_arr = json_decode( $delivery_dates, true );
					} else {
						$delivery_dates_new_arr = array();
					}
					$new_arr       = array();
					$i             = 0;
					$delete_status = 'N';
					foreach ( $delivery_dates_new_arr as $key => $value ) {
						foreach ( $value as $k => $v ) {
							if ( $v == $d_value ) {
								$delete_status = 'Y';
								unset( $delivery_dates_new_arr[ $key ] );
							}
						}
						if ( $delete_status == 'N' ) {
							$new_arr[ $i ] = $value;
							$i++;
						}
						$delete_status = 'N';
					}
					$delivery_dates_new_arr = $new_arr;
					update_option( 'orddd_delivery_dates', json_encode( $delivery_dates_new_arr ) );

					// Time Slots
					$time_slot_str    = get_option( 'orddd_delivery_time_slot_log' );
					$time_slot        = json_decode( $time_slot_str );
					$timeslot_new_arr = array();
					if ( $time_slot == 'null' || $time_slot == 'null' || $time_slot == '' || $time_slot == '{}' || $time_slot == '[]' ) {
						$time_slot = array();
					}

					foreach ( $time_slot as $key => $v ) {
						$arr = $v->fh . ':' . $v->fm . ' - ' . $v->th . ':' . $v->tm;
						if ( gettype( json_decode( $v->dd ) ) == 'array' && count( json_decode( $v->dd ) ) > 0 ) {
							$dd         = json_decode( $v->dd );
							$new_dd_str = '[';
							$count_dd   = 0;
							if ( is_array( $dd ) ) {
								$count_dd = count( $dd );
							}
							for ( $i = 0; $i < $count_dd; $i++ ) {
								if ( $dd[ $i ] == $d_value ) {
									// do nothing as this time slot needs to be deleted
								} else {
									$new_dd_str .= '"' . $dd[ $i ] . '",';
								}
							}
							$new_dd_str = substr( $new_dd_str, 0, strlen( $new_dd_str ) - 1 );
							if ( trim( $new_dd_str ) != '' ) {
								$new_dd_str        .= ']';
								$timeslot_new_arr[] = array(
									'tv'                 => $v->tv,
									'dd'                 => $new_dd_str,
									'lockout'            => $v->lockout,
									'additional_charges' => $v->additional_charges,
									'additional_charges_label' => $v->additional_charges_label,
									'fh'                 => $v->fh,
									'fm'                 => $v->fm,
									'th'                 => $v->th,
									'tm'                 => $v->tm,
								);
							}
						} else {
							$timeslot_new_arr[] = array(
								'tv'                       => $v->tv,
								'dd'                       => $v->dd,
								'lockout'                  => $v->lockout,
								'additional_charges'       => $v->additional_charges,
								'additional_charges_label' => $v->additional_charges_label,
								'fh'                       => $v->fh,
								'fm'                       => $v->fm,
								'th'                       => $v->th,
								'tm'                       => $v->tm,
							);
						}
					}

					$timeslot_jarr = wp_json_encode( $timeslot_new_arr );
					update_option( 'orddd_delivery_time_slot_log', $timeslot_jarr );
				}
			}
			wp_safe_redirect( admin_url( '/admin.php?page=order_delivery_date&action=general_settings&section=delivery_dates' ) );
		}

		if ( ( isset( $_POST['page'] ) && sanitize_text_field( $_POST['page'] ) == 'order_delivery_date' ) &&
			( isset( $_POST['tab'] ) && sanitize_text_field( $_POST['tab'] ) == 'general_settings' ) &&
			( isset( $_POST['section'] ) && sanitize_text_field( $_POST['section'] ) == 'holidays' ) ) {
			if ( ( isset( $_POST['action'] ) && sanitize_text_field( $_POST['action'] ) == 'orddd_delete' ) ||
				( isset( $_POST['action2'] ) && sanitize_text_field( $_POST['action2'] ) == 'orddd_delete' ) ) {

				$holiday = array();
				if ( isset( $_POST['holiday'] ) ) {
					$holiday = $_POST['holiday'];
				}
				foreach ( $holiday as $h_key => $h_value ) {
					$holidays         = get_option( 'orddd_delivery_date_holidays' );
					$holidays_arr     = json_decode( $holidays );
					$holidays_new_arr = array();
					if ( $holidays_arr != '' || ( is_array( $holidays_arr ) && count( $holidays_arr ) > 0 ) ) {
						foreach ( $holidays_arr as $k => $v ) {
							$r_type = isset( $v->r_type ) ? $v->r_type : '';
							$holidays_new_arr[] = array(
								'n'      => $v->n,
								'd'      => $v->d,
								'r_type' => $r_type,
							);
						}
					}

					$cnt           = count( $holidays_new_arr );
					$new_arr       = self::removeElementWithValue( $holidays_new_arr, 'd', $h_value );
					$holidays_jarr = json_encode( $new_arr );
					update_option( 'orddd_delivery_date_holidays', $holidays_jarr );
				}
			}
			wp_safe_redirect( admin_url( '/admin.php?page=order_delivery_date&action=general_settings&section=holidays' ) );
		}
		
		if ( ( isset( $_POST['page'] ) && sanitize_text_field( $_POST['page'] ) == 'order_delivery_date' ) && ( isset( $_POST['tab'] ) && sanitize_text_field( $_POST['tab'] ) == 'general_settings' ) && ( isset( $_POST['section'] ) && sanitize_text_field( $_POST['section'] ) == 'time_slot' ) ) {
			
			if ( ( isset( $_POST['action'] ) && sanitize_text_field( $_POST['action'] ) == 'orddd_delete' ) || ( isset( $_POST['action2'] ) && sanitize_text_field( $_POST['action2'] ) == 'orddd_delete' ) ) {
				$time_slot_to_delete = array();
				if ( isset( $_POST['time_slot'] ) ) {
					$time_slot_to_delete = $_POST['time_slot'];
				} 
				foreach ( $time_slot_to_delete as $t_key => $t_value ) {
					$time_values   = explode( ',', $t_value );
					$date_to_check = $fh = $fm = $th = $tm = $tv = '';
					if ( isset( $time_values[0] ) ) {
						$date_to_check = $time_values[0];
					}
					if ( isset( $time_values[1] ) ) {
						$fh = $time_values[1];
					}

					if ( isset( $time_values[2] ) ) {
						$fm = $time_values[2];
					}

					if ( isset( $time_values[3] ) ) {
						$th = $time_values[3];
					}

					if ( isset( $time_values[4] ) ) {
						$tm = $time_values[4];
					}

					if ( isset( $time_values[5] ) ) {
						$tv = $time_values[5];
					}

					$time_slot_str    = get_option( 'orddd_delivery_time_slot_log' );
					$time_slots       = json_decode( $time_slot_str );
					$timeslot_new_arr = array();
					if ( $time_slots == 'null' || $time_slots == 'null' || $time_slots == '' || $time_slots == '{}' || $time_slots == '[]' ) {
						$time_slots = array();
					}

					foreach ( $time_slots as $key => $v ) {
						if ( gettype( json_decode( $v->dd ) ) == 'array' && count( json_decode( $v->dd ) ) > 0 ) {
							$dd         = json_decode( $v->dd );
							$new_dd_str = '[';
							$count_dd   = 0;
							if ( is_array( $dd ) ) {
								$count_dd = count( $dd );
							}
							for ( $i = 0; $i < $count_dd; $i++ ) {
								if ( $fh == $v->fh && $fm == $v->fm && $th == $v->th && $tm == $v->tm && $date_to_check == $dd[ $i ] && $tv == $v->tv ) {
									// do nothing as this time slot needs to be deleted
								} else {
									$new_dd_str .= '"' . $dd[ $i ] . '",';
								}
							}
							$new_dd_str = substr( $new_dd_str, 0, strlen( $new_dd_str ) - 1 );
							if ( trim( $new_dd_str ) != '' ) {
								$new_dd_str        .= ']';
								$timeslot_new_arr[] = array(
									'tv'                 => $v->tv,
									'dd'                 => $new_dd_str,
									'lockout'            => $v->lockout,
									'additional_charges' => $v->additional_charges,
									'additional_charges_label' => $v->additional_charges_label,
									'fh'                 => $v->fh,
									'fm'                 => $v->fm,
									'th'                 => $v->th,
									'tm'                 => $v->tm,
								);
							}
						} else {
							if ( $fh == $v->fh && $fm == $v->fm && $th == $v->th && $tm == $v->tm && $date_to_check == $v->dd && $tv == $v->tv ) {
								unset( $v );
							} else {
								$timeslot_new_arr[] = array(
									'tv'                 => $v->tv,
									'dd'                 => $v->dd,
									'lockout'            => $v->lockout,
									'additional_charges' => $v->additional_charges,
									'additional_charges_label' => $v->additional_charges_label,
									'fh'                 => $v->fh,
									'fm'                 => $v->fm,
									'th'                 => $v->th,
									'tm'                 => $v->tm,
								);
							}
						}
					}
					$timeslot_jarr = json_encode( $timeslot_new_arr );
					update_option( 'orddd_delivery_time_slot_log', $timeslot_jarr );
				}
			}
			wp_safe_redirect( admin_url( '/admin.php?page=order_delivery_date&action=general_settings&section=time_slot' ) );
		}

		if ( ( isset( $_POST['page'] ) && sanitize_text_field( $_POST['page'] ) == 'order_delivery_date' ) && ( isset( $_POST['tab'] ) && sanitize_text_field( $_POST['tab'] ) == 'general_settings' ) && ( isset( $_POST['section'] ) && sanitize_text_field( $_POST['section'] ) == 'block_time_slot_settings' ) ) {
			if ( ( isset( $_POST['action'] ) && sanitize_text_field( $_POST['action'] ) == 'orddd_delete' ) || ( isset( $_POST['action2'] ) && sanitize_text_field( $_POST['action2'] ) == 'orddd_delete' ) ) {
				$block_time_slot_to_delete = array();
				if ( isset( $_POST['block_time_slot'] ) ) {
					$block_time_slot_to_delete = $_POST['block_time_slot'];
				}

				foreach ( $block_time_slot_to_delete as $t_key => $t_value ) {
					$time_values   = explode( ',', $t_value );
					$date_to_check = $time_Slot = '';
					if ( isset( $time_values[0] ) ) {
						$date_to_check = $time_values[0];
					}
					if ( isset( $time_values[1] ) ) {
						$time_Slot = $time_values[1];
					}

					$disable_time_slot_str    = get_option( 'orddd_disable_time_slot_log' );
					$disable_time_slots       = json_decode( $disable_time_slot_str );
					$disable_timeslot_new_arr = array();
					if ( $disable_time_slots == 'null' || $disable_time_slots == 'null' || $disable_time_slots == '' || $disable_time_slots == '{}' || $disable_time_slots == '[]' ) {
						$disable_time_slots = array();
					}

					$timeslot_disable_new_arr = array();
					foreach ( $disable_time_slots as $disable_key => $disable_v ) {
						$time_slots = json_decode( $disable_v->ts );
						if ( ( isset( $time_Slot ) && in_array( $time_Slot, $time_slots ) ) && ( isset( $date_to_check ) && $date_to_check == $disable_v->dd ) ) {
							// do nothing as this time slot needs to be deleted
							$key = array_search( $time_Slot, $time_slots );
							unset( $time_slots[ $key ] );

							if ( is_array( $time_slots ) && count( $time_slots ) == 0 ) {
								unset( $disable_time_slots[ $disable_key ] );
							}

							$new_ts_str = '[';
							foreach ( $time_slots as $time_slot_key => $time_slot_value ) {
								$new_ts_str .= '"' . $time_slot_value . '",';
							}
							$new_ts_str = substr( $new_ts_str, 0, strlen( $new_ts_str ) - 1 );

							if ( trim( $new_ts_str ) != '' ) {
								$new_ts_str                .= ']';
								$timeslot_disable_new_arr[] = array(
									'dtv' => $disable_v->dtv,
									'dd'  => $disable_v->dd,
									'ts'  => $new_ts_str,
								);
							}
						} else {
							$timeslot_disable_new_arr[] = array(
								'dtv' => $disable_v->dtv,
								'dd'  => $disable_v->dd,
								'ts'  => $disable_v->ts,
							);
						}
					}
					$disable_timeslot_jarr = json_encode( $timeslot_disable_new_arr );
					update_option( 'orddd_disable_time_slot_log', $disable_timeslot_jarr );
				}
			}
			wp_safe_redirect( admin_url( '/admin.php?page=order_delivery_date&action=general_settings&section=block_time_slot_settings' ) );
		}
	}

	/**
	 * Add Enable Delivery Date checkbox on Product-> Categories page
	 *
	 * @since 2.8.6
	 */

	public function orddd_enable_for_product_category() {
		?>
		<div class="form-field">
		  <table>
			  <tr>
				  <td><input type="checkbox" name="orddd_delivery_date_for_product_category" id="orddd_delivery_date_for_product_category" checked="checked"/></td>
				  <td><label for="orddd_delivery_date_for_product_category"><?php _e( 'Enable Delivery Dates?', 'order-delivery-date' ); ?></label></td>
				  <input type="hidden" name="orddd_delivery_date_for_product_category_value" id="orddd_delivery_date_for_product_category_value" value="" />
			  </tr>
		  </table>
		</div>
		<?php
	}

	/**
	 * Edit Enable Delivery Date checkbox on Product-> Categories page
	 *
	 * @param resource $term Product category term object.
	 * @param string   $taxanomy Taxonomy slug
	 *
	 * @since 2.8.6
	 */
	public function orddd_edit_delivery_field_for_product_category( $term, $taxonomy ) {
		$display_type = get_term_meta( $term->term_id, 'orddd_delivery_date_for_product_category', true );
		$checked      = $display_type == 'on' ? 'checked="checked"' : '';
		?>
		<tr class="form-field">
		   <th scope="row" valign="top"><label><?php _e( 'Enable Delivery Dates?', 'order-delivery-date' ); ?></label></th>
		   <td>
		      <input type="checkbox" name="orddd_delivery_date_for_product_category" id="orddd_delivery_date_for_product_category" <?php echo $checked; ?> />
			  <input type="hidden" name="orddd_delivery_date_for_product_category_value" id="orddd_delivery_date_for_product_category_value" value="" />
		   </td>
		</tr>
		<?php
	}

	/**
	 * Save the value of Enable Delivery Date checkbox on Product-> Categories page
	 *
	 * @param int    $term_id Product category id.
	 * @param int    $tt_id Term taxonomy ID.
	 * @param string $taxonomy Taxonomy slug.
	 *
	 * @since 2.8.6
	 */
	public function orddd_save_category_fields( $term_id, $tt_id, $taxonomy ) {
		if ( isset( $_POST['orddd_delivery_date_for_product_category'] ) ) {
			update_term_meta( $term_id, 'orddd_delivery_date_for_product_category', sanitize_text_field( esc_attr( $_POST['orddd_delivery_date_for_product_category'] ) ) );
		} elseif( isset( $_POST['orddd_delivery_date_for_product_category_value'] ) ) {
			update_term_meta( $term_id, 'orddd_delivery_date_for_product_category', sanitize_text_field( esc_attr( $_POST['orddd_delivery_date_for_product_category_value'] ) ) );
		} elseif ( ( isset( $_POST['newproduct_cat'] ) && $_POST['newproduct_cat'] != '' ) || ( isset( $_POST['action'] ) && $_POST['action'] == 'wpml_save_job_ajax' ) )  {
			update_term_meta( $term_id, 'orddd_delivery_date_for_product_category', 'on' );
		}else {
			update_term_meta( $term_id, 'orddd_delivery_date_for_product_category', 'on' );
		}
		delete_transient( 'wc_term_counts' );
	}

	/**
	 * Add delivery date column
	 *
	 * @access public
	 * @param mixed $columns
	 * @return array
	 */
	public function orddd_product_cat_columns( $columns ) {
		$columns['delivery'] = __( 'Enable Delivery Dates', 'order-delivery-date' );
		return $columns;
	}

	/**
	 * Add delivery date column value
	 *
	 * @access public
	 * @param mixed $columns
	 * @param mixed $column
	 * @param mixed $id
	 * @return array
	 */
	public function orddd_product_cat_column( $columns, $column, $id ) {
		if ( $column == 'delivery' ) {
			$delivery_date = get_term_meta( $id, 'orddd_delivery_date_for_product_category', true );
			$data          = $delivery_date ? 'Yes' : 'No';
			$columns      .= $data;
		}
		return $columns;
	}
	/**
	 * Save settings.
	 */
	public function orddd_advance_settings_save_changes() {
		$advance_settings = get_option( 'orddd_advance_settings' );

		if ( '' === $advance_settings ||
			'{}' === $advance_settings ||
			'[]' === $advance_settings ) {
			$advance_settings = array();
		}

		$changes = $_POST['changes'];
		foreach ( $changes as $row_id => $data ) {
			$row_id_arr = explode( '-', $row_id );
			if ( isset( $row_id_arr[0] ) && 'new' == $row_id_arr[0] ) {
				foreach ( $data as $data_key => $data_value ) {
					if ( 'additional_charges' == $data_key ) {
						$fee_var = 'additional_charges_' . $data['orddd_weekdays'];
						update_option( $fee_var, $data_value );
					} elseif ( 'delivery_charges_label' == $data_key ) {
						$fee_label_var = 'delivery_charges_label_' . $data['orddd_weekdays'];
						update_option( $fee_label_var, $data_value );
					}
				}
				$id                      = $row_id_arr[1];
				$data['row_id']          = $id;
				$advance_settings[ $id ] = $data;
			} elseif ( isset( $data['deleted'] ) ) {
				if ( isset( $data['newRow'] ) ) {
					// So the user added and deleted a new row.
					// That's fine, it's not in the database anyways. NEXT!
					continue;
				}
				foreach ( $advance_settings[ $row_id ] as $data_key => $data_value ) {
					if ( 'additional_charges' == $data_key ) {
						$fee_var = 'additional_charges_' . $advance_settings[ $row_id ]['orddd_weekdays'];
						update_option( $fee_var, '' );
					} elseif ( 'delivery_charges_label' == $data_key ) {
						$fee_label_var = 'delivery_charges_label_' . $advance_settings[ $row_id ]['orddd_weekdays'];
						update_option( $fee_label_var, '' );
					}
				}
				unset( $advance_settings[ $row_id ] );
			} else {
				foreach ( $data as $data_key => $data_value ) {
					$previous_advance_settings = $advance_settings[ $row_id ];
					if ( 'orddd_weekdays' == $data_key ) {
						$fee_var          = 'additional_charges_' . $data_value;
						$previous_fee_var = 'additional_charges_' . $previous_advance_settings['orddd_weekdays'];
						update_option( $fee_var, $previous_advance_settings['additional_charges'] );
						update_option( $previous_fee_var, '' );

						$fee_label_var          = 'delivery_charges_label_' . $data_value;
						$previous_fee_label_var = 'delivery_charges_label_' . $previous_advance_settings['orddd_weekdays'];
						update_option( $fee_label_var, $previous_advance_settings['delivery_charges_label'] );
						update_option( $previous_fee_label_var, '' );
					} elseif ( 'additional_charges' == $data_key ) {
						$previous_advance_settings = $advance_settings[ $row_id ];
						$fee_var                   = 'additional_charges_' . $previous_advance_settings['orddd_weekdays'];
						update_option( $fee_var, $data['additional_charges'] );
					} elseif ( 'delivery_charges_label' == $data_key ) {
						$fee_label_var = 'delivery_charges_label_' . $previous_advance_settings['orddd_weekdays'];
						update_option( $fee_label_var, $data['delivery_charges_label'] );
					}
					$advance_settings[ $row_id ][ $data_key ] = $data_value;
				}
			}
		}

		update_option( 'orddd_advance_settings', $advance_settings );

		wp_send_json_success(
			array(
				'orddd_advance_settings' => $advance_settings,
			)
		);

	}
}
$orddd_settings = new orddd_settings();

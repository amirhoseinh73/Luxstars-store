<?php
/**
 * Order Delivery Date Additional Settings in General Settings in admin.
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Pro-for-WooCommerce/Admin/Settings/General
 * @since 2.8.3
 * @category Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Orddd_Additional_Settings class
 *
 * @class Orddd_Additional_Settings
 */
class Orddd_Additional_Settings {

	/**
	 * Callback for adding Additional Settings tab settings
	 */
	public static function orddd_additional_settings_section_callback() { }

	/**
	 * Callback for adding Delivery date column on WooCommerce->Orders page setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_show_column_on_orders_page_check_callback( $args ) {
		$orddd_show_column_on_orders_page_check = '';
		$orddd_enable_default_sorting_of_column = '';
		if ( 'on' === get_option( 'orddd_show_column_on_orders_page_check' ) ) {
			$orddd_show_column_on_orders_page_check = 'checked';
		}

		if ( 'on' === get_option( 'orddd_enable_default_sorting_of_column' ) ) {
			$orddd_enable_default_sorting_of_column = 'checked';
		}

		?>
		<input type="checkbox" name="orddd_show_column_on_orders_page_check" id="orddd_show_column_on_orders_page_check" class="day-checkbox" <?php echo esc_attr( $orddd_show_column_on_orders_page_check ); ?>/>
		<label for="orddd_show_column_on_orders_page_check"><?php echo wp_kses_post( $args[0] ); ?></label></br>
		<?php

		if ( 'on' === get_option( 'orddd_show_column_on_orders_page_check' ) ) {
			?>
			<input type="checkbox" name="orddd_enable_default_sorting_of_column" id="orddd_enable_default_sorting_of_column" class="day-checkbox" <?php echo esc_attr( $orddd_enable_default_sorting_of_column ); ?>/>
			<label for="orddd_enable_default_sorting_of_column"><?php echo esc_attr( __( 'Enable default sorting of orders (in descending order) by Delivery Date on WooCommerce -> Orders page', 'order-delivery-date' ) ); ?></label>
			<?php
		} else {
			?>
			<input type="checkbox" name="orddd_enable_default_sorting_of_column" id="orddd_enable_default_sorting_of_column" class="day-checkbox" <?php echo esc_attr( $orddd_enable_default_sorting_of_column ); ?>/>
			<label for="orddd_enable_default_sorting_of_column" id="orddd_enable_default_sorting_of_column"><?php echo esc_attr( __( 'Enable default sorting of orders (in descending order) by Delivery Date on WooCommerce -> Orders page', 'order-delivery-date' ) ); ?></label>
			<?php
		}
	}

	/**
	 * Callback for adding Filter on WooCommerce->Orders page setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_show_filter_on_orders_page_check_callback( $args ) {
		$orddd_show_filter_on_orders_page_check = '';
		if ( 'on' === get_option( 'orddd_show_filter_on_orders_page_check' ) ) {
			$orddd_show_filter_on_orders_page_check = 'checked';
		}

		?>
		<input type="checkbox" name="orddd_show_filter_on_orders_page_check" id="orddd_show_filter_on_orders_page_check" class="day-checkbox" <?php echo esc_attr( $orddd_show_filter_on_orders_page_check ); ?> />
		<label for="orddd_show_filter_on_orders_page_check"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for hiding Delivery Date fields on the checkout page for Virtual product setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_appearance_virtual_product_callback( $args ) {
		$orddd_no_fields_for_virtual_product  = '';
		$orddd_no_fields_for_featured_product = '';
		if ( 'on' === get_option( 'orddd_no_fields_for_virtual_product' ) ) {
			$orddd_no_fields_for_virtual_product = 'checked';
		}

		echo '<input type="checkbox" name="orddd_no_fields_for_virtual_product" id="orddd_no_fields_for_virtual_product" class="day-checkbox"' . esc_attr( $orddd_no_fields_for_virtual_product ) . '/><label class="orddd_no_fields_for_product_type">' . esc_attr( __( 'Virtual Products', 'order-delivery-date' ) ) . '</label>';

		if ( 'on' === get_option( 'orddd_no_fields_for_featured_product' ) ) {
			$orddd_no_fields_for_featured_product = 'checked';
		}

		?>
		<input type="checkbox" name="orddd_no_fields_for_featured_product" id="orddd_no_fields_for_featured_product" class="day-checkbox"<?php echo esc_attr( $orddd_no_fields_for_featured_product ); ?>/><label class="orddd_no_fields_for_product_type"><?php echo esc_attr( __( 'Featured products', 'order-delivery-date' ) ); ?></label>
		<label for="orddd_no_fields_for_product_type"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Integration with Other Plugins settings
	 */
	public static function orddd_integration_with_other_plugins_callback() { }

	/**
	 * Callback for adding Delivery date and/or Time slot in csv export setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_show_fields_in_csv_export_check_callback( $args ) {
		$orddd_show_fields_in_csv_export_check = '';
		if ( 'on' === get_option( 'orddd_show_fields_in_csv_export_check' ) ) {
			$orddd_show_fields_in_csv_export_check = 'checked';
		}

		?>
		<input type="checkbox" name="orddd_show_fields_in_csv_export_check" id="orddd_show_fields_in_csv_export_check" class="day-checkbox" <?php echo esc_attr( $orddd_show_fields_in_csv_export_check ); ?>/>
		<label for="orddd_show_fields_in_csv_export_check"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Delivery date and/or Time slot in PDF invoices and Packing slips setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_show_fields_in_pdf_invoice_and_packing_slips_callback( $args ) {
		$orddd_show_fields_in_pdf_invoice_and_packing_slips = '';
		if ( 'on' === get_option( 'orddd_show_fields_in_pdf_invoice_and_packing_slips' ) ) {
			$orddd_show_fields_in_pdf_invoice_and_packing_slips = 'checked';
		}
		?>
		<input type="checkbox" name="orddd_show_fields_in_pdf_invoice_and_packing_slips" id="orddd_show_fields_in_pdf_invoice_and_packing_slips" class="day-checkbox" <?php echo esc_attr( $orddd_show_fields_in_pdf_invoice_and_packing_slips ); ?>/>
		<label for="orddd_show_fields_in_pdf_invoice_and_packing_slips"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Delivery date and/or Time slot in Print Invoice and Packing slips setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_show_fields_in_invoice_and_delivery_note_callback( $args ) {
		$orddd_show_fields_in_invoice_and_delivery_note = '';
		if ( 'on' === get_option( 'orddd_show_fields_in_invoice_and_delivery_note' ) ) {
			$orddd_show_fields_in_invoice_and_delivery_note = 'checked';
		}

		?>
		<input type="checkbox" name="orddd_show_fields_in_invoice_and_delivery_note" id="orddd_show_fields_in_invoice_and_delivery_note" class="day-checkbox" <?php echo esc_attr( $orddd_show_fields_in_invoice_and_delivery_note ); ?>/>
		<label for="orddd_show_fields_in_invoice_and_delivery_note"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Delivery date and/or Time slot in Cloud print setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_show_fields_in_cloud_print_orders_callback( $args ) {
		$orddd_show_fields_in_cloud_print_orders_check = '';
		if ( 'on' === get_option( 'orddd_show_fields_in_cloud_print_orders' ) ) {
			$orddd_show_fields_in_cloud_print_orders_check = 'checked';
		}

		?>
		<input type="checkbox" name="orddd_show_fields_in_cloud_print_orders" id="orddd_show_fields_in_cloud_print_orders" class="day-checkbox" <?php echo esc_attr( $orddd_show_fields_in_cloud_print_orders_check ); ?>/>
		<label for="orddd_show_fields_in_cloud_print_orders"><?php echo wp_kses_post( $args[0] ); ?></label>';
		<?php
	}

	/**
	 * Callback for enabling tax calculation on the checkout page for Delivery Charges
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_enable_tax_calculation_for_delivery_charges_callback( $args ) {
		$orddd_enable_tax_calculation_for_delivery_charges = '';
		if ( 'on' === get_option( 'orddd_enable_tax_calculation_for_delivery_charges' ) ) {
			$orddd_enable_tax_calculation_for_delivery_charges = 'checked';
		}
		?>
		<input type="checkbox" name="orddd_enable_tax_calculation_for_delivery_charges" id="orddd_enable_tax_calculation_for_delivery_charges" class="day-checkbox" <?php echo esc_attr( $orddd_enable_tax_calculation_for_delivery_charges ); ?>/>
		<label for="orddd_enable_tax_calculation_for_delivery_charges"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Compatibility with other plugin section
	 */
	public static function orddd_compatibility_with_other_plugins_callback() {}

	/**
	 * Enable Compatibility with WooCommerce Shipping Multiple Addresses plugin
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_multiple_address_compatibility_callback( $args ) {
		$orddd_shipping_multiple_address_compatibility = '';
		if ( 'on' === get_option( 'orddd_shipping_multiple_address_compatibility' ) ) {
			$orddd_shipping_multiple_address_compatibility = 'checked';
		}
		?>
		<input type="checkbox" name="orddd_shipping_multiple_address_compatibility" id="orddd_shipping_multiple_address_compatibility" class="day-checkbox" <?php echo esc_attr( $orddd_shipping_multiple_address_compatibility ); ?>/>
		<label for="orddd_shipping_multiple_address_compatibility"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Enable Compatibility with WooCommerce Amazon Payments Advanced Gateway
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_amazon_payments_advanced_gateway_compatibility_callback( $args ) {
		$orddd_amazon_payments_advanced_gateway_compatibility = '';
		if ( 'on' === get_option( 'orddd_amazon_payments_advanced_gateway_compatibility' ) ) {
			$orddd_amazon_payments_advanced_gateway_compatibility = 'checked';
		}
		?>
		<input type="checkbox" name="orddd_amazon_payments_advanced_gateway_compatibility" id="orddd_amazon_payments_advanced_gateway_compatibility" class="day-checkbox" <?php echo esc_attr( $orddd_amazon_payments_advanced_gateway_compatibility ); ?> />
		<label for="orddd_amazon_payments_advanced_gateway_compatibility"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Autofill date & time on the checkout page
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_enable_autofill_of_delivery_date_callback( $args ) {
		$orddd_enable_autofill_of_delivery_date = '';
		if ( 'on' === get_option( 'orddd_enable_autofill_of_delivery_date' ) ) {
			$orddd_enable_autofill_of_delivery_date = 'checked';
		}
		?>
		<input type="checkbox" name="orddd_enable_autofill_of_delivery_date" id="orddd_enable_autofill_of_delivery_date" class="day-checkbox" <?php echo esc_attr( $orddd_enable_autofill_of_delivery_date ); ?>/>
		<label for="orddd_enable_autofill_of_delivery_date"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Enable custom to display the availability on hover of the calendar date on the checkout page.
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_enable_availability_display_callback( $args ) {
		$orddd_enable_labels = '';
		if ( 'on' === get_option( 'orddd_enable_availability_display' ) ) {
			$orddd_enable_labels = 'checked';
		}

		?>
		<input type="checkbox" name="orddd_enable_availability_display" id="orddd_enable_availability_display" class="day-checkbox" <?php echo esc_attr( $orddd_enable_labels ); ?> />
		<label for="orddd_enable_availability_display"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}


	/**
	 * Enable customers to show partially booked dates with diagonal seperate colors for booked and available dates.
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 9.7
	 */
	public static function orddd_show_partially_booked_dates_callback( $args ) {
		$partially_booked_dates = '';
		if ( 'on' === get_option( 'orddd_show_partially_booked_dates' ) ) {
			$partially_booked_dates = 'checked';
		}
		?>
		<input type="checkbox" name="orddd_show_partially_booked_dates" id="orddd_show_partially_booked_dates" class="day-checkbox" <?php echo esc_attr( $partially_booked_dates ); ?> />
		<label for="orddd_show_partially_booked_dates"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	public static function orddd_add_delivery_in_order_notes_callback( $args ) {
		$delivery_in_notes_enabled = '';
		if ( 'on' === get_option( 'orddd_add_delivery_in_order_notes' ) ) {
			$delivery_in_notes_enabled = 'checked';
		}
		?>
		<input type="checkbox" name="orddd_add_delivery_in_order_notes" id="orddd_add_delivery_in_order_notes" class="day-checkbox" <?php echo esc_attr( $delivery_in_notes_enabled ); ?> />
		<label for="orddd_add_delivery_in_order_notes"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Enable customers to edit or modify the deliveru date
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_allow_customers_to_edit_date_callback( $args ) {
		$orddd_allow_customers_to_edit_date          = '';
		$orddd_send_email_to_admin_when_date_updated = '';
		$orddd_disable_edit_after_cutoff = '';

		if ( 'on' === get_option( 'orddd_allow_customers_to_edit_date' ) ) {
			$orddd_allow_customers_to_edit_date = 'checked';
		}

		if ( 'on' === get_option( 'orddd_send_email_to_admin_when_date_updated' ) ) {
			$orddd_send_email_to_admin_when_date_updated = 'checked';
		}

		if ( 'on' === get_option( 'orddd_disable_edit_after_cutoff' ) ) {
			$orddd_disable_edit_after_cutoff = 'checked';
		}
		?>
		<input type="checkbox" name="orddd_allow_customers_to_edit_date" id="orddd_allow_customers_to_edit_date" class="day-checkbox" <?php echo esc_attr( $orddd_allow_customers_to_edit_date ); ?>/>
		<label for="orddd_allow_customers_to_edit_date"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php

		if ( 'on' === get_option( 'orddd_allow_customers_to_edit_date' ) ) {
			?>
			<input type="checkbox" name="orddd_send_email_to_admin_when_date_updated" id="orddd_send_email_to_admin_when_date_updated" class="day-checkbox" <?php echo esc_attr( $orddd_send_email_to_admin_when_date_updated ); ?>/>
			<label for="orddd_send_email_to_admin_when_date_updated"><?php echo esc_attr( __( 'Send a notification to the Admin when the Delivery Date & Time is updated by the customers.', 'order-delivery-date' ) ); ?></label>
			<br>
			<input type="checkbox" name="orddd_disable_edit_after_cutoff" id="orddd_disable_edit_after_cutoff" class="day-checkbox" <?php echo esc_attr( $orddd_disable_edit_after_cutoff ); ?>/>
			<label for="orddd_disable_edit_after_cutoff"><?php echo esc_attr( __( 'Do not allow customers to edit the delivery date after cut off time has passed.', 'order-delivery-date' ) ); ?></label>
			<?php
		} else {
			?>
			<input type="checkbox" name="orddd_send_email_to_admin_when_date_updated" id="orddd_send_email_to_admin_when_date_updated" class="day-checkbox" <?php echo esc_attr( $orddd_send_email_to_admin_when_date_updated ); ?> />
			<label for="orddd_send_email_to_admin_when_date_updated" id="orddd_send_email_to_admin_when_date_updated"><?php echo esc_attr( __( 'When enabled, email notification will be sent to the admin when the Delivery Date & Time is edited by the customers on the My Account -> Orders -> View page. So customers will be able to edit the date and time once the order is placed.', 'order-delivery-date' ) ); ?></label>

			<br>
			<input type="checkbox" name="orddd_disable_edit_after_cutoff" id="orddd_disable_edit_after_cutoff" class="day-checkbox" <?php echo esc_attr( $orddd_disable_edit_after_cutoff ); ?>/>
			<label for="orddd_disable_edit_after_cutoff"><?php echo esc_attr( __( 'Do not allow customers to edit the delivery date after cut off time has passed.', 'order-delivery-date' ) ); ?></label>
			<?php
		}
	}
}

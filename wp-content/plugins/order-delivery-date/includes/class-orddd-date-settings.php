<?php
/**
 * Display Date Settings for General Settings in admin.
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
 * Orddd Date Settings class
 *
 * @class Orddd_Date_Settings
 */
class Orddd_Date_Settings {

	/**
	 * Callback for adding Date Settings tab settings
	 */
	public static function orddd_delivery_date_setting() { }

	/**
	 * Callback for adding Enable Delivery Date setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_enable_delivery_date_callback( $args ) {
		$enable_delivery_date = '';
		if ( 'on' === get_option( 'orddd_enable_delivery_date' ) ) {
			$enable_delivery_date = 'checked';
		}
		?>
		<input type="checkbox" name="orddd_enable_delivery_date" id="orddd_enable_delivery_date" class="day-checkbox" value="on" <?php echo esc_attr( $enable_delivery_date ); ?> />
		<label for="orddd_enable_delivery_date"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for delivery checkout option to select Calendar or Text Block
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_delivery_checkout_options_callback( $args ) {
		global $orddd_weekdays;
		$orddd_delivery_checkout_options_delivery_calendar = 'checked';
		$orddd_delivery_checkout_options_text_block        = '';
		if ( 'text_block' === get_option( 'orddd_delivery_checkout_options' ) ) {
			$orddd_delivery_checkout_options_text_block        = 'checked';
			$orddd_delivery_checkout_options_delivery_calendar = '';
		} elseif ( 'delivery_calendar' === get_option( 'orddd_delivery_checkout_options' ) ) {
			$orddd_delivery_checkout_options_delivery_calendar = 'checked';
			$orddd_delivery_checkout_options_text_block        = '';
		}

		?>
		<p>
			<label>
				<input type="radio" name="orddd_delivery_checkout_options" id="orddd_delivery_checkout_options" value="delivery_calendar"<?php echo esc_attr( $orddd_delivery_checkout_options_delivery_calendar ); ?>/><?php esc_html_e( 'Calendar', 'order-delivery-date' ); ?>
			</label>
			<label>
				<input type="radio" name="orddd_delivery_checkout_options" id="orddd_delivery_checkout_options" value="text_block"<?php echo esc_attr( $orddd_delivery_checkout_options_text_block ); ?>/><?php esc_html_e( 'Text block', 'order-delivery-date' ); ?>
			</label>
		</p>
		<label for="orddd_delivery_checkout_options"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback to add the Delivery Range field for the text block
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_text_block_between_days_callback( $args ) {
		$orddd_min_between_days = get_option( 'orddd_min_between_days' );
		if ( '' === $orddd_min_between_days ) {
			$orddd_min_between_days = 1;
		}

		$orddd_max_between_days = get_option( 'orddd_max_between_days' );
		if ( '' === $orddd_max_between_days ) {
			$orddd_max_between_days = 3;
		}
		?>
		<label for="orddd_text_block_between_days">Between 
			<input id="orddd_min_between_days" name="orddd_min_between_days" type="number" value="<?php echo esc_attr( $orddd_min_between_days ); ?>" style="width:50px;" min="1" step="1"> 
			and 
			<input id="orddd_max_between_days" name="orddd_max_between_days" type="number" value="<?php echo esc_attr( $orddd_max_between_days ); ?>" style="width:50px;" min="1" step="1"> 
			days.
		</label>
		<label for="orddd_text_block_between_days"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Delivery Weekdays setting
	 *
	 * @param string $input Value of the weekday setting.
	 *
	 * @return string $input
	 *
	 * @todo Unused Function. Need to check and remove it.
	 * @since 2.8.3
	 */
	public static function orddd_weekday_0_save( $input ) {
		$input = self::return_orddd_weekday_input( 'orddd_weekday_0' );
		return $input;
	}

	/**
	 * Callback for adding Delivery Weekdays setting
	 *
	 * @param string $input Value of the weekday setting.
	 *
	 * @return string $input
	 *
	 * @todo Unused Function. Need to check and remove it.
	 * @since 2.8.3
	 */
	public static function orddd_weekday_1_save( $input ) {
		$input = self::return_orddd_weekday_input( 'orddd_weekday_1' );
		return $input;
	}

	/**
	 * Callback for adding Delivery Weekdays setting
	 *
	 * @param string $input Value of the weekday setting.
	 *
	 * @return string $input
	 *
	 * @todo Unused Function. Need to check and remove it.
	 * @since 2.8.3
	 */
	public static function orddd_weekday_2_save( $input ) {
		$input = self::return_orddd_weekday_input( 'orddd_weekday_2' );
		return $input;
	}

	/**
	 * Callback for adding Delivery Weekdays setting
	 *
	 * @param string $input Value of the weekday setting.
	 *
	 * @return string $input
	 *
	 * @todo Unused Function. Need to check and remove it.
	 * @since 2.8.3
	 */
	public static function orddd_weekday_3_save( $input ) {
		$input = self::return_orddd_weekday_input( 'orddd_weekday_3' );
		return $input;
	}

	/**
	 * Callback for adding Delivery Weekdays setting
	 *
	 * @param string $input Value of the weekday setting.
	 *
	 * @return string $input
	 *
	 * @todo Unused Function. Need to check and remove it.
	 * @since 2.8.3
	 */
	public static function orddd_weekday_4_save( $input ) {
		$input = self::return_orddd_weekday_input( 'orddd_weekday_4' );
		return $input;
	}

	/**
	 * Callback for adding Delivery Weekdays setting
	 *
	 * @param string $input Value of the weekday setting.
	 *
	 * @return string $input
	 *
	 * @todo Unused Function. Need to check and remove it.
	 * @since 2.8.3
	 */
	public static function orddd_weekday_5_save( $input ) {
		$input = self::return_orddd_weekday_input( 'orddd_weekday_5' );
		return $input;
	}

	/**
	 * Callback for adding Delivery Weekdays setting
	 *
	 * @param string $input Value of the weekday setting.
	 *
	 * @return string $input
	 *
	 * @todo Unused Function. Need to check and remove it.
	 * @since 2.8.3
	 */
	public static function orddd_weekday_6_save( $input ) {
		$input = self::return_orddd_weekday_input( 'orddd_weekday_6' );
		return $input;
	}

	/**
	 * Return the selected weekdays
	 *
	 * @todo Unused Function. Need to check and remove it.
	 * @param string $weekday Weekday string to be saved.
	 * @return string $input
	 * @since 2.8.3
	 */
	public static function return_orddd_weekday_input( $weekday ) {
		global $orddd_weekdays;
		$input = '';
		if ( isset( $_POST['orddd_weekdays'] ) ) {
			$weekdays = $_POST['orddd_weekdays'];
			if ( in_array( $weekday, $weekdays, true ) ) {
				$input = 'checked';
			}
		}
		return $input;
	}

	/**
	 * Callback function to select weekdays for deliveries
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_delivery_days_callback( $args ) {
		global $orddd_weekdays;
		$currency_symbol = get_woocommerce_currency_symbol();

		echo '<select class="orddd_weekdays" id="orddd_weekdays" name="orddd_weekdays[]" placeholder="Select Weekdays" multiple="multiple">';
		foreach ( $orddd_weekdays as $n => $day_name ) {
			if ( 'checked' === get_option( $n ) ) {
				print( '<option name="' . esc_attr( $n ) . '" value="' . esc_attr( $n ) . '" selected>' . esc_attr( $day_name ) . '</option>' );
			} else {
				print( '<option name="' . esc_attr( $n ) . '" value="' . esc_attr( $n ) . '">' . esc_attr( $day_name ) . '</option>' );
			}
		}
		echo '</select>';
		?>
		<label for="orddd_delivery_days"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback to add Weekday Settings field
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_enable_day_wise_settings_callback( $args ) {
		$enable_day_wise_settings = '';
		if ( 'on' === get_option( 'orddd_enable_day_wise_settings' ) ) {
			$enable_day_wise_settings = 'checked';
		}
		?>
		<input type="checkbox" name="orddd_enable_day_wise_settings" id="orddd_enable_day_wise_settings" class="day-checkbox" value="on" <?php echo esc_attr( $enable_day_wise_settings ); ?> />
		<label for="orddd_enable_day_wise_settings"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Minimum Delivery Time(in hours) setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_minimum_delivery_time_callback( $args ) {
		?>
		<input type="number" min="0" name="orddd_minimumOrderDays" id="orddd_minimumOrderDays" value="<?php echo esc_attr( get_option( 'orddd_minimumOrderDays' ) ); ?>" step="0.25"/>
		<label for="orddd_minimumOrderDays"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Number of dates to choose setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_number_of_dates_callback( $args ) {
		?>
		<input type="number" min="0" step="1" name="orddd_number_of_dates" id="orddd_number_of_dates" value="<?php echo esc_attr( get_option( 'orddd_number_of_dates' ) ); ?>"/>
		<label for="orddd_number_of_dates"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Show Delivery Date in customer notification email setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_show_delivery_date_in_customer_notification_email_callback( $args ) {
		$plugin_path                          = plugins_url();
		$show_delivery_date_in_customer_email = '';
		if ( 'on' === get_option( 'orddd_show_delivery_date_in_customer_email' ) ) {
			$show_delivery_date_in_customer_email = 'checked';
		}
		?>
		<input type="checkbox" name="orddd_show_delivery_date_in_customer_email" id="orddd_show_delivery_date_in_customer_email" class="day-checkbox" <?php echo esc_attr( $show_delivery_date_in_customer_email ); ?>/>
		<label for="orddd_show_delivery_date_in_customer_email"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Delivery Date field mandatory setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_date_field_mandatory_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_date_field_mandatory" id="orddd_date_field_mandatory" class="day-checkbox" value="checked" <?php echo esc_attr( get_option( 'orddd_date_field_mandatory' ) ); ?>/>
		<label for="orddd_date_field_mandatory"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Lockout date after X orders setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_lockout_date_after_orders_callback( $args ) {
		$max_lockout = intval( get_option( 'orddd_lockout_date_after_orders' ) );
		?>
		<input type="number" min="0" step="1" name="orddd_lockout_date_after_orders" id="orddd_lockout_date_after_orders" value="<?php echo esc_attr( $max_lockout ); ?>"/>
		<label for="orddd_lockout_date_after_orders"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	public static function orddd_global_lockout_custom_callback( $args ) {
		$orddd_global_lockout_custom = '';
		if ( 'on' === get_option( 'orddd_global_lockout_custom' ) ) {
			$orddd_global_lockout_custom = 'checked';
		}
		?>
		<input type="checkbox" name="orddd_global_lockout_custom" id="orddd_global_lockout_custom" value="on" <?php echo esc_attr( $orddd_global_lockout_custom ); ?>/>
		<label for="orddd_global_lockout_custom"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback to add the Maximum Deliveries based on per product quantity setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_lockout_date_quantity_based_callback( $args ) {
		$orddd_lockout_date_quantity_based = '';
		if ( 'on' === get_option( 'orddd_lockout_date_quantity_based' ) ) {
			$orddd_lockout_date_quantity_based = 'checked';
		}

		?>
		<input type="checkbox" name="orddd_lockout_date_quantity_based" id="orddd_lockout_date_quantity_based" value="on" <?php echo esc_attr( $orddd_lockout_date_quantity_based ); ?>/>
		<label for="orddd_lockout_date_quantity_based"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding max order total required to remove delivery charges.
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 9.25.0
	 */
	public static function orddd_max_order_total_for_charges_callback( $args ) {
		$max_lockout = intval( get_option( 'orddd_max_order_total_for_charges' ) );
		?>
		<input type="number" min="0" step="0.1" name="orddd_max_order_total_for_charges" id="orddd_max_order_total_for_charges" value="<?php echo esc_attr( $max_lockout ); ?>"/>
		<label for="orddd_max_order_total_for_charges"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}
}

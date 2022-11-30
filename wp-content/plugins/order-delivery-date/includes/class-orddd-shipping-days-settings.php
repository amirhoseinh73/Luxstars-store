<?php
/**
 * Order Delivery Shipping Days Settings in admin.
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Pro-for-WooCommerce/Admin/Settings/Custom-Delivery
 * @since 2.4
 * @category Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Orddd Shipping Days Settings class
 *
 * @class Orddd_Shipping_Days_Settings
 */
class Orddd_Shipping_Days_Settings {

	/**
	 * Callback for adding Shipping days tab settings
	 */
	public static function orddd_shipping_days_settings_section_callback() {
		?>
		<?php _e( 'Please enable the business days of your store so all the calculations of cut-off time and minimum delivery time will be done based on these weekdays and not based on delivery weekdays. You can leave this unchanged if you handle delivery & shipping by yourself. Please refer <a href="https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/setup-shipping-days/?utm_source=userwebsite&utm_medium=link&utm_campaign=OrderDeliveryDateProSetting" target="_blank"> this post </a> to know more.', 'order-delivery-date' ); ?>
		<?php
	}

	/**
	 * Callback for adding Enable time slot setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 */
	public static function orddd_enable_shipping_days_callback( $args ) {
		$orddd_enable_shipping_days = '';
		if ( 'on' === get_option( 'orddd_enable_shipping_days' ) ) {
			$orddd_enable_shipping_days = 'checked';
		}

		?>
		<input type="checkbox" name="orddd_enable_shipping_days" id="orddd_enable_shipping_days" class="day-checkbox" <?php echo esc_attr( $orddd_enable_shipping_days ); ?>/>
		<label for="orddd_enable_shipping_days"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Shipping Weekdays setting
	 *
	 * @param string $input Weekday slug.
	 * @return string
	 */
	public static function orddd_shipping_day_0_save( $input ) {
		$input = self::return_orddd_shipping_day_input( 'orddd_shipping_day_0' );
		return $input;
	}

	/**
	 * Callback for adding Shipping Weekdays setting
	 *
	 * @param string $input Weekday slug.
	 * @return string
	 */
	public static function orddd_shipping_day_1_save( $input ) {
		$input = self::return_orddd_shipping_day_input( 'orddd_shipping_day_1' );
		return $input;
	}

	/**
	 * Callback for adding Shipping Weekdays setting
	 *
	 * @param string $input Weekday slug.
	 * @return string
	 */
	public static function orddd_shipping_day_2_save( $input ) {
		$input = self::return_orddd_shipping_day_input( 'orddd_shipping_day_2' );
		return $input;
	}

	/**
	 * Callback for adding Shipping Weekdays setting
	 *
	 * @param string $input Weekday slug.
	 * @return string
	 */
	public static function orddd_shipping_day_3_save( $input ) {
		$input = self::return_orddd_shipping_day_input( 'orddd_shipping_day_3' );
		return $input;
	}

	/**
	 * Callback for adding Shipping Weekdays setting
	 *
	 * @param string $input Weekday slug.
	 * @return string
	 */
	public static function orddd_shipping_day_4_save( $input ) {
		$input = self::return_orddd_shipping_day_input( 'orddd_shipping_day_4' );
		return $input;
	}

	/**
	 * Callback for adding Shipping Weekdays setting
	 *
	 * @param string $input Weekday slug.
	 * @return string
	 */
	public static function orddd_shipping_day_5_save( $input ) {
		$input = self::return_orddd_shipping_day_input( 'orddd_shipping_day_5' );
		return $input;
	}

	/**
	 * Callback for adding Shipping Weekdays setting
	 *
	 * @param string $input Weekday slug.
	 * @return string
	 */
	public static function orddd_shipping_day_6_save( $input ) {
		$input = self::return_orddd_shipping_day_input( 'orddd_shipping_day_6' );
		return $input;
	}

	/**
	 * Check if the selected weekday is valid
	 *
	 * @param string $weekday Weekday slug.
	 * @return string $input
	 */
	public static function return_orddd_shipping_day_input( $weekday ) {
		global $orddd_shipping_days;
		$input = '';
		if ( isset( $_POST['orddd_shipping_days'] ) ) {
			$weekdays = $_POST['orddd_shipping_days'];
			if ( in_array( $weekday, $weekdays, true ) ) {
				$input = 'checked';
			}
		}
		return $input;
	}

	/**
	 * Callback for selecting weekdays if 'Weekdays' option is selected
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 */
	public static function orddd_shipping_days_callback( $args ) {
		global $orddd_shipping_days;
		echo '<select class="orddd_shipping_days" id="orddd_shipping_days" name="orddd_shipping_days[]" placeholder="Select Weekdays" multiple="multiple">';
		foreach ( $orddd_shipping_days as $n => $day_name ) {
			if ( 'checked' === get_option( $n ) ) {
				print( '<option name="' . esc_attr( $n ) . '" value="' . esc_attr( $n ) . '" selected>' . esc_attr( $day_name ) . '</option>' );
			} else {
				print( '<option name="' . esc_attr( $n ) . '" value="' . esc_attr( $n ) . '">' . esc_attr( $day_name ) . '</option>' );
			}
		}
		echo '</select>';
		?>
		<label for="orddd_shipping_days"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	public static function orddd_business_opening_time_callback( $args ) {
		?>
		<input type="text" name="orddd_business_opening_time" id="orddd_business_opening_time" value="<?php echo esc_attr( get_option( 'orddd_business_opening_time' ) ); ?>"/>
		<label for="orddd_business_opening_time"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	public static function orddd_business_closing_time_callback( $args ) {
		?>
		<input type="text" name="orddd_business_closing_time" id="orddd_business_closing_time" value="<?php echo esc_attr( get_option( 'orddd_business_closing_time' ) ); ?>"/>
		<label for="orddd_business_closing_time"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}
}

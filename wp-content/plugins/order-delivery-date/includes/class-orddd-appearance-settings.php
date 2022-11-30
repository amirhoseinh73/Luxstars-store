<?php
/**
 * Order Delivery Date Appearance Settings in admin.
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
 * Orddd_Appearance_Settings class
 *
 * @class Orddd_Appearance_Settings
 */
class Orddd_Appearance_Settings {

	/**
	 * Callback for adding Appearance tab settings
	 */
	public static function orddd_appearance_admin_setting_callback() { }

	/**
	 * Callback for adding Field Appearance section settings.
	 */
	public static function orddd_field_appearance_admin_setting_callback() { }

	public static function orddd_calendar_display_mode_callback( $args ) {
		$display_mode = get_option( 'orddd_calendar_display_mode' );
		if ( '' === $display_mode ) {
			$display_mode = 'input_calendar';
		}
		?>
			<select id="orddd_calendar_display_mode" name="orddd_calendar_display_mode">
				<option value='input_calendar' <?php echo ( 'input_calendar' === $display_mode ) ? 'selected' : ''; ?>>Open calendar on click of input field</option>
				<option value='inline_calendar' <?php echo ( 'inline_calendar' === $display_mode ) ? 'selected' : ''; ?>>Show calendar always open</option>
			</select>

			<label for="orddd_calendar_display_mode"><?php echo wp_kses_post( $args[0] ); ?></label>

		<?php
	}
  
  public static function orddd_delivery_dates_in_dropdown_callback( $args ) {
		$dates_in_dropdown = false !== get_option( 'orddd_delivery_dates_in_dropdown' ) ? get_option( 'orddd_delivery_dates_in_dropdown' ) : 'no';
		?>
			<select id="orddd_delivery_dates_in_dropdown" name="orddd_delivery_dates_in_dropdown">
				<option value="yes" <?php echo ( $dates_in_dropdown === 'yes' ) ? "selected" : "" ?>>Yes</option>
				<option value="no" <?php echo ( $dates_in_dropdown === 'no' ) ? "selected" : "" ?>>No</option>
			</select>

			<label for="orddd_delivery_dates_in_dropdown"><?php echo wp_kses_post( $args[0] ); ?></label>

		<?php
	}

  /**
	 * Callback for adding Calendar Language setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_appearance_calendar_language_callback( $args ) {
		global $orddd_languages;
		$language_selected = get_option( 'orddd_language_selected' );
		if ( '' === $language_selected ) {
			$language_selected = 'en-GB';
		}

		echo '<select id="orddd_language_selected" name="orddd_language_selected">';

		foreach ( $orddd_languages as $key => $value ) {
			$sel = '';
			if ( $key === $language_selected ) {
				$sel = 'selected';
			}
			?>
			<option value='<?php echo esc_attr( $key ); ?>' <?php echo esc_attr( $sel ); ?>><?php echo esc_attr( $value ); ?></option>
			<?php
		}

		echo '</select>';
		?>
		<label for="orddd_language_selected"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Date formats setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_appearance_date_formats_callback( $args ) {
		global $orddd_date_formats;

		echo '<select name="orddd_delivery_date_format" id="orddd_delivery_date_format" size="1">';

		foreach ( $orddd_date_formats as $k => $format ) {
			printf(
				"<option %s value='%s'>%s</option>\n",
				selected( $k, get_option( 'orddd_delivery_date_format' ), false ),
				esc_attr( $k ),
				esc_attr( gmdate( $format ) )
			);
		}
		echo '</select>';
		?>
		<label for="orddd_delivery_date_format"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Time format for time sliders setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_time_format_callback( $args ) {
		global $orddd_time_formats;
		echo '<select name="orddd_delivery_time_format" id="orddd_delivery_time_format" size="1">';

		foreach ( $orddd_time_formats as $k => $format ) {
			printf(
				"<option %s value='%s'>%s</option>\n",
				selected( $k, get_option( 'orddd_delivery_time_format' ), false ),
				esc_attr( $k ),
				esc_attr( $format )
			);
		}

		echo '</select>';

		?>
		<label for="orddd_delivery_time_format"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding First day of week setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_appearance_first_day_of_week_callback( $args ) {
		global $orddd_days;
		$day_selected = get_option( 'start_of_week' );
		if ( '' === $day_selected ) {
			$day_selected = 0;
		}

		echo '<select id="start_of_week" name="start_of_week">';

		foreach ( $orddd_days as $key => $value ) {
			$sel = '';
			if ( $key === intval( $day_selected ) ) {
				$sel = ' selected ';
			}
			?>
			<option value='<?php echo esc_attr( $key ); ?>' <?php echo esc_attr( $sel ); ?>><?php echo esc_attr( $value ); ?></option>
			<?php
		}
		echo '</select>';
		?>
		<label for="start_of_week"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Locations field label setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_location_field_label_callback( $args ) {
		echo '<input type="text" name="orddd_location_field_label" id="orddd_location_field_label" value="' . esc_attr( get_option( 'orddd_location_field_label' ) ) . '" maxlength="40"/>';
		?>
		<label for="orddd_location_field_label"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Delivery Date field label setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_delivery_date_field_label_callback( $args ) {
		?>
		<input type="text" name="orddd_delivery_date_field_label" id="orddd_delivery_date_field_label" value="<?php echo esc_attr( get_option( 'orddd_delivery_date_field_label' ) ); ?>" maxlength="40"/>
		<label for="orddd_delivery_date_field_label"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Time slot field label setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_delivery_timeslot_field_label_callback( $args ) {
		?>
		<input type="text" name="orddd_delivery_timeslot_field_label" id="orddd_delivery_timeslot_field_label" value="<?php echo esc_attr( get_option( 'orddd_delivery_timeslot_field_label' ) ); ?>" maxlength="40"/>
		<label for="orddd_delivery_timeslot_field_label"><?php echo esc_attr( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Delivery Date field placeholder setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_delivery_date_field_placeholder_callback( $args ) {
		?>
		<input type="text" name="orddd_delivery_date_field_placeholder" id="orddd_delivery_date_field_placeholder" value="<?php echo esc_attr( get_option( 'orddd_delivery_date_field_placeholder' ) ); ?>" maxlength="40"/>
		<label for="orddd_delivery_date_field_placeholder"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Delivery Date field note text setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_delivery_date_field_note_text_callback( $args ) {
		?>
		<textarea rows="4" cols="70" name="orddd_delivery_date_field_note" id="orddd_delivery_date_field_note"><?php echo esc_attr( stripslashes( get_option( 'orddd_delivery_date_field_note' ) ) ); ?></textarea>
		<label for="orddd_delivery_date_field_note"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Delivery Date field note text setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 9.22.0
	 */
	public static function orddd_estimated_date_text_callback( $args ) {
		?>
		<textarea rows="4" cols="70" name="orddd_estimated_date_text" id="orddd_estimated_date_text"><?php echo esc_attr( stripslashes( get_option( 'orddd_estimated_date_text' ) ) ); ?></textarea>
		<label for="orddd_estimated_date_text"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Number of months setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_appearance_number_of_months_callback( $args ) {
		global $orddd_number_of_months;
		echo '<select name="orddd_number_of_months" id="orddd_number_of_months" size="1">';

		foreach ( $orddd_number_of_months as $k => $v ) {
			printf(
				"<option %s value='%s'>%s</option>\n",
				selected( $k, get_option( 'orddd_number_of_months' ), false ),
				esc_attr( $k ),
				esc_attr( $v )
			);
		}
		echo '</select>';

		?>
		<label for="orddd_number_of_months"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Delivery Date fields in Shipping section setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_delivery_date_in_shipping_section_callback( $args ) {

		$orddd_date_in_billing                  = 'checked';
		$orddd_date_in_shipping                 = '';
		$orddd_date_before_order_notes          = '';
		$orddd_date_after_order_notes           = '';
		$orddd_date_after_your_order_table      = '';
		$orddd_custom_hook_for_fields_placement = '';
		if ( 'billing_section' === get_option( 'orddd_delivery_date_fields_on_checkout_page' ) ) {
			$orddd_date_in_billing = 'checked';
		} elseif ( 'shipping_section' === get_option( 'orddd_delivery_date_fields_on_checkout_page' ) ) {
			$orddd_date_in_shipping = 'checked';
		} elseif ( 'before_order_notes' === get_option( 'orddd_delivery_date_fields_on_checkout_page' ) ) {
			$orddd_date_before_order_notes = 'checked';
		} elseif ( 'after_order_notes' === get_option( 'orddd_delivery_date_fields_on_checkout_page' ) ) {
			$orddd_date_after_order_notes = 'checked';
		} elseif ( 'after_your_order_table' === get_option( 'orddd_delivery_date_fields_on_checkout_page' ) ) {
			$orddd_date_after_your_order_table = 'checked';
		} elseif ( 'custom' === get_option( 'orddd_delivery_date_fields_on_checkout_page' ) ) {
			$orddd_custom_hook_for_fields_placement = 'checked';
		}

		echo '<input type="radio" name="orddd_delivery_date_fields_on_checkout_page" id="orddd_delivery_date_fields_on_checkout_page" value="billing_section" ' . esc_attr( $orddd_date_in_billing ) . '>' . esc_attr( __( 'In Billing Section', 'order-delivery-date' ) ) . '<br>
			 <input type="radio" name="orddd_delivery_date_fields_on_checkout_page" id="orddd_delivery_date_fields_on_checkout_page" value="shipping_section" ' . esc_attr( $orddd_date_in_shipping ) . '>' . esc_attr( __( 'In Shipping Section', 'order-delivery-date' ) ) . '<br>
		     <input type="radio" name="orddd_delivery_date_fields_on_checkout_page" id="orddd_delivery_date_fields_on_checkout_page" value="before_order_notes" ' . esc_attr( $orddd_date_before_order_notes ) . '>' . esc_attr( __( 'Before Order Notes', 'order-delivery-date' ) ) . '<br>
		     <input type="radio" name="orddd_delivery_date_fields_on_checkout_page" id="orddd_delivery_date_fields_on_checkout_page" value="after_order_notes" ' . esc_attr( $orddd_date_after_order_notes ) . '>' . esc_attr( __( 'After Order Notes', 'order-delivery-date' ) ) . '<br>
		     <input type="radio" name="orddd_delivery_date_fields_on_checkout_page" id="orddd_delivery_date_fields_on_checkout_page" value="after_your_order_table" ' . esc_attr( $orddd_date_after_your_order_table ) . '>' . esc_attr( __( 'Between Your Order & Payment Section', 'order-delivery-date' ) ) . '&nbsp;&nbsp;<br>
		     <input type="radio" name="orddd_delivery_date_fields_on_checkout_page" id="orddd_delivery_date_fields_on_checkout_page" value="custom" ' . esc_attr( $orddd_custom_hook_for_fields_placement ) . '>' . esc_attr( __( 'Custom:', 'order-delivery-date' ) ) . '&nbsp;&nbsp;&nbsp;
		     <input type="text" name="orddd_custom_hook_for_fields_placement" id="orddd_custom_hook_for_fields_placement" value="' . esc_attr( get_option( 'orddd_custom_hook_for_fields_placement' ) ) . '" placeholder="Add a custom hook" style="width:400px;"/>';

		?>
		<label for="orddd_delivery_date_fields_on_checkout_page"><br><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for hiding Delivery Date fields on the checkout page for Featured product setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_appearance_featured_product_callback( $args ) {
		?>
		<label for="orddd_no_fields_for_featured_product"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Calendar theme setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_appearance_calendar_theme_callback( $args ) {
		global $orddd_calendar_themes;
		$language_selected = get_option( 'orddd_language_selected' );
		if ( '' === $language_selected ) {
			$language_selected = 'en-GB';
		}

		$calendar_theme = get_option( 'orddd_calendar_theme' );
		if ( '' === $calendar_theme ) {
			$calendar_theme = 'smoothness';
		}

		$calendar_theme_name = get_option( 'orddd_calendar_theme_name' );
		if ( '' === $calendar_theme_name ) {
			$calendar_theme_name = 'Smoothness';
		}

		echo '<input type="hidden" name="orddd_calendar_theme" id="orddd_calendar_theme" value="' . esc_attr( $calendar_theme ) . '">';
        echo '<input type="hidden" name="orddd_calendar_theme_name" id="orddd_calendar_theme_name" value="' . esc_attr( $calendar_theme_name ) . '">';
        echo '<input type="hidden" name="orddd_calendar_themes" id="orddd_calendar_themes" value="' . esc_attr( wp_json_encode( $orddd_calendar_themes ) ) . '">';
        echo '<input type="hidden" name="orddd_image_path" id="orddd_image_path" value="' . esc_url( plugins_url() ) . '/order-delivery-date/images/" >';
        echo '<input type="hidden" name="orddd_css_path" id="orddd_css_path" value="' . esc_url( plugins_url() ) . '/order-delivery-date/css/datepicker.css" >';

		echo '<style>.form-table th, .form-wrap label { color:inherit; }</style>';
		echo '<div id="switcher"></div>
		<br><strong>' . esc_html( __( 'Preview theme:', 'order-delivery-date' ) ) . '</strong><br>
		<div id="datepicker" style="width:300px"></div>';

		?>
		<label for="orddd_calendar_theme_name"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding the setting to display Delivery Date on cart page
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_delivery_date_on_cart_page_callback( $args ) {
		$delivery_date_on_cart_page = '';
		if ( 'on' === get_option( ' orddd_delivery_date_on_cart_page' ) ) {
			$delivery_date_on_cart_page = 'checked';
		}

		?>
		<input type="checkbox" name="orddd_delivery_date_on_cart_page" id="orddd_delivery_date_on_cart_page" class="day-checkbox" <?php echo esc_attr( $delivery_date_on_cart_page ); ?>/>
		<label for="orddd_delivery_date_on_cart_page"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Color Picker Settings section
	 *
	 * @since 8.4
	 */
	public static function orddd_color_picker_admin_setting_callback() { }

	/**
	 * Callback for adding Holidays Color setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 8.4
	 */
	public static function orddd_holiday_color_callback( $args ) {
		$orddd_holiday_color = get_option( 'orddd_holiday_color' );
		?>
		<input id="orddd_holiday_color"  name="orddd_holiday_color" class="cpa-color-picker" value="<?php echo esc_attr( $orddd_holiday_color ); ?>">
		<label for="orddd_holiday_color"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Booked Dates Color setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 8.4
	 */
	public static function orddd_booked_dates_color_callback( $args ) {
		$orddd_booked_dates_color = get_option( 'orddd_booked_dates_color' );
		?>
		<input id="orddd_booked_dates_color"  name="orddd_booked_dates_color" class="cpa-color-picker" value="<?php echo esc_attr( $orddd_booked_dates_color ); ?>">
		<label for="orddd_booked_dates_color"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Cut-off Time over dates Color setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 8.4
	 */
	public static function orddd_cut_off_time_color_callback( $args ) {
		$orddd_cut_off_time_color = get_option( 'orddd_cut_off_time_color' );
		?>
		<input type="text" id="orddd_cut_off_time_color"  name="orddd_cut_off_time_color" class="cpa-color-picker" value="<?php echo esc_attr( $orddd_cut_off_time_color ); ?>">
		<label for="orddd_cut_off_time_color"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding available dates Color setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 8.4
	 */
	public static function orddd_available_dates_color_callback( $args ) {
		$orddd_available_dates_color = get_option( 'orddd_available_dates_color' );
		?>
		<input type="text" id="orddd_available_dates_color"  name="orddd_available_dates_color" class="cpa-color-picker" value="<?php echo esc_attr( $orddd_available_dates_color ); ?>">
		<label for="orddd_available_dates_color"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}
}

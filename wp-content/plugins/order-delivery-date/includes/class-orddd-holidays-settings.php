<?php
/**
 * Display Holiday Settings in General Settings in admin.
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Pro-for-WooCommerce/Admin/Settings/General
 * @since 2.8.4
 * @category Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Orddd_Holidays_Settings class
 *
 * @class Orddd_Holidays_Settings
 */
class Orddd_Holidays_Settings {

	/**
	 * Callback for adding Holidays tab settings
	 */
	public static function orddd_holidays_admin_setting_callback() {}

	/**
	 * Callback for adding Holiday name setting
	 *
	 * @since 2.8.4
	 */
	public static function orddd_holidays_name_callback() {
		?>
		<input type="text" name="orddd_holiday_name" id="orddd_holiday_name" class="orddd_holiday_name" <?php echo esc_attr( stripslashes( get_option( 'orddd_holiday_name' ) ) ); ?>/>
		<?php
	}

	/**
	 * Callback for adding Holiday start date setting
	 *
	 * @since 2.8.4
	 */
	public static function orddd_holidays_from_date_callback() { 
		$current_language = get_option( 'orddd_language_selected' );
		$day_selected     = get_option( 'start_of_week' );

		?>
		<input type="text" name="orddd_holiday_from_date" id="orddd_holiday_from_date" class="orddd_holiday_from_date" <?php echo esc_attr( get_option( 'orddd_holiday_from_date' ) ); ?> readonly="readonly" autocomplete="off" style="background:white;"/>
		<input type="hidden" name="orddd_start_of_week" id="orddd_start_of_week" value="<?php echo $day_selected; ?>" >
		<?php
	}

	/**
	 * Callback for adding Holiday end date setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.4
	 */
	public static function orddd_holidays_to_date_callback( $args ) {
		$current_language = get_option( 'orddd_language_selected' );
		$day_selected     = get_option( 'start_of_week' );
		?>
		<input type="text" name="orddd_holiday_to_date" id="orddd_holiday_to_date" class="orddd_holiday_to_date" <?php echo esc_attr( get_option( 'orddd_holiday_to_date' ) ); ?> readonly="readonly" autocomplete="off" style="background:white;"/>
		<label for="orddd_holiday_to_date"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Allow Recurring Holidays settings
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 8.0
	 */
	public static function orddd_allow_recurring_holiday_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_allow_recurring_holiday" id="orddd_allow_recurring_holiday" class="day-checkbox" />
		<label for="orddd_allow_recurring_holiday"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for saving the Holidays
	 *
	 * @param array $input Setting input.
	 * @since 2.8.4
	 */
	public static function orddd_delivery_date_holidays_callback( $input ) {
		$holidays          = get_option( 'orddd_delivery_date_holidays' );
		$holiday_dates_arr = array();
		$holidays_new_arr  = array();

		$orddd_allow_recurring_holiday = '""';
		$holiday_name                  = '';

		if ( '' === $holidays ||
			'{}' === $holidays ||
			'[]' === $holidays ||
			'null' === $holidays ) {
			$holidays_arr = array();
		} else {
			$holidays_arr = json_decode( $holidays );
		}

		foreach ( $holidays_arr as $k => $v ) {
			if ( isset( $v->r_type ) ) {
				$holidays_new_arr[] = array(
					'n'      => $v->n,
					'd'      => $v->d,
					'r_type' => $v->r_type,
				);
			} else {
				$holidays_new_arr[] = array(
					'n'      => $v->n,
					'd'      => $v->d,
					'r_type' => '',
				);
			}

			$holidays_new_arr    = apply_filters( 'ordd_check_existing_products', $holidays_new_arr, $v );
			$holiday_dates_arr[] = $v->d;
		}

		if ( isset( $_POST['orddd_holiday_name'] ) ) {
			$holiday_name = str_replace( "\'", "'", $_POST['orddd_holiday_name'] );
			$holiday_name = str_replace( '\"', '"', $holiday_name );
		}

		if ( isset( $_POST['orddd_allow_recurring_holiday'] ) ) {
			$orddd_allow_recurring_holiday = $_POST['orddd_allow_recurring_holiday'];
		}

		if ( isset( $_POST['orddd_holiday_from_date'] ) && '' !== $_POST['orddd_holiday_from_date'] && isset( $_POST['orddd_holiday_to_date'] ) && '' !== $_POST['orddd_holiday_to_date'] ) {
			$date_from_arr = explode( '-', $_POST['orddd_holiday_from_date'] );
			$date_to_arr   = explode( '-', $_POST['orddd_holiday_to_date'] );
			$tstmp_from    = gmdate( 'd-n-Y', gmmktime( 0, 0, 0, $date_from_arr[0], $date_from_arr[1], $date_from_arr[2] ) );
			$tstmp_to      = gmdate( 'd-n-Y', gmmktime( 0, 0, 0, $date_to_arr[0], $date_to_arr[1], $date_to_arr[2] ) );
			$holiday_dates = orddd_common::orddd_get_betweendays( $tstmp_from, $tstmp_to );
			$holiday_date  = '';
			$output        = array();
			foreach ( $holiday_dates as $k => $v ) {
				$v1 = gmdate( ORDDD_HOLIDAY_DATE_FORMAT, strtotime( $v ) );
				if ( ! in_array( $v1, $holiday_dates_arr, true ) ) {
					$holidays_new_arr[] = array(
						'n'      => $holiday_name,
						'd'      => $v1,
						'r_type' => $orddd_allow_recurring_holiday,
					);

					$holidays_new_arr = apply_filters( 'ordd_add_holiday_products', $holidays_new_arr, $v1 );
				}
			}
		}

		$holidays_jarr = wp_json_encode( $holidays_new_arr );
		$output        = $holidays_jarr;
		return $output;
	}

	/**
	 * Text to display on the Block Time Slots page
	 *
	 * @since 2.8.4
	 */
	public static function orddd_disable_time_slot_callback() {
		echo 'Use this if you want to hide or block a Time Slot temporarily.';
	}

	/**
	 * Callback to add setting to block time slots
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.4
	 */
	public static function orddd_disable_time_slot_for_delivery_days_callback( $args ) {
		global $orddd_weekdays;
		$orddd_disable_time_slot_for_weekdays = '';
		$orddd_disable_time_slot_for_dates    = 'checked';
		if ( 'weekdays' === get_option( 'orddd_disable_time_slot_for_delivery_days' ) ) {
			$orddd_disable_time_slot_for_weekdays = 'checked';
			$orddd_disable_time_slot_for_dates    = '';
		} elseif ( 'dates' === get_option( 'orddd_disable_time_slot_for_delivery_days' ) ) {
			$orddd_disable_time_slot_for_dates    = 'checked';
			$orddd_disable_time_slot_for_weekdays = '';
		}

		?>
		<p><label><input type="radio" name="orddd_disable_time_slot_for_delivery_days" id="orddd_disable_time_slot_for_delivery_days" value="dates"<?php echo esc_attr( $orddd_disable_time_slot_for_dates ); ?>/><?php esc_html_e( 'Dates', 'order-delivery-date' ); ?></label>
		<label><input type="radio" name="orddd_disable_time_slot_for_delivery_days" id="orddd_disable_time_slot_for_delivery_days" value="weekdays"<?php echo esc_attr( $orddd_disable_time_slot_for_weekdays ); ?>/><?php esc_html_e( 'Weekdays', 'order-delivery-date' ); ?></label></p>
		<label for="orddd_disable_time_slot_for_delivery_days"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}


	/**
	 * Callback to add the setting for disabling time slots for weekdays
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.4
	 */
	public static function orddd_disable_time_slot_for_weekdays_callback( $args ) {
		global $orddd_weekdays;
		?>

			<div class="disable_time_slot_options disable_time_slot_for_weekdays">
            	<select class="orddd_disable_time_slot_for_weekdays" id="orddd_disable_time_slot_for_weekdays" name="orddd_disable_time_slot_for_weekdays[]" multiple="multiple" placeholder="Select Weekdays">
             		<option name="all" value="all">All</option>
		<?php
		$weekdays_arr = array();
		foreach ( $orddd_weekdays as $n => $day_name ) {
			$weekdays[ $n ] = $day_name;
			?>
				<option name="<?php echo esc_attr( $n ); ?>" value="<?php echo esc_attr( $n ); ?>"><?php echo esc_attr( $weekdays[ $n ] ); ?></option>
			<?php
		}
		?>
		</select></div>
		<div class="disable_time_slot_options disable_time_slot_for_dates">
        	<textarea rows="4" cols="40" name="disable_time_slot_for_dates" id="disable_time_slot_for_dates" placeholder="Select Dates"></textarea>
		<?php

		$delivery_arr     = array();
		$current_language = get_option( 'orddd_language_selected' );
		?>
		</div>
		<label for="orddd_disable_time_slot_for_weekdays"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback to add the setting to select time slots to disable
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.4
	 */
	public static function orddd_selected_time_slots_to_be_disabled_callback( $args ) {

		printf( '<select class="orddd_selected_time_slots_to_be_disabled" id="orddd_selected_time_slots_to_be_disabled" name="orddd_selected_time_slots_to_be_disabled[]" multiple="multiple" placeholder="Select Time slots">' );

		$time_slot_key_arr = self::get_all_timeslots();

		if ( isset( $time_slot_key_arr ) && is_array( $time_slot_key_arr ) && count( $time_slot_key_arr ) > 0 ) {
			foreach ( $time_slot_key_arr as $ts_key => $ts_value ) {
				echo "<option value='" . esc_attr( $ts_value ) . "'>" . esc_attr( $ts_value ) . "</option>\n";
			}
		}
		echo '</select>';

		?>
		<label for="orddd_selected_time_slots_to_be_disabled"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Get all the saved time slots
	 *
	 * @param string $format_requested Time slot format.
	 * @return array
	 * @since 2.8.4
	 */
	public static function get_all_timeslots( $format_requested = '' ) {

		global $orddd_weekdays, $wpdb;

		$time_slot_arr     = array();
		$time_slot_key_arr = array();
	    $time_format_to_show      = orddd_common::orddd_get_time_format(); 

		if ( 'on' === get_option( 'orddd_enable_time_slot' ) ) {
			$time_slot_select = get_option( 'orddd_delivery_time_slot_log' );

			if ( '' !== $time_slot_select &&
				'{}' !== $time_slot_select &&
				'[]' !== $time_slot_select &&
				'null' !== $time_slot_select ) {
				$time_slot_arr = json_decode( $time_slot_select );
			}
			if ( is_array( $time_slot_arr ) && count( $time_slot_arr ) > 0 ) {
				if ( 'null' === $time_slot_arr ) {
					$time_slot_arr = array();
				}
				foreach ( $time_slot_arr as $k => $v ) {
					$from_time = $v->fh . ':' . trim( $v->fm );
					// Send in format as requested.
					
					$ft =  date( $time_format_to_show, strtotime( $from_time ) );
					if ( $v->th != 00 || ( $v->th == 00 && $v->tm != 00 ) ) {
						$to_time = $v->th . ":" . $v->tm;
						$tt = date( $time_format_to_show, strtotime( $to_time ) );
						$time_slot_key = $ft . " - " . $tt;
					} else {
						$time_slot_key = $ft;
					}
					if ( ! in_array( $time_slot_key, $time_slot_key_arr, true ) ) {
						$time_slot_key_arr[] = $time_slot_key;
					}
				}
			}
		}

		if ( 'on' === get_option( 'orddd_enable_shipping_based_delivery' ) ) {
			$results = orddd_common::orddd_get_shipping_settings();

			$time_format = get_option( 'orddd_delivery_time_format' );
			foreach ( $results as $key => $value ) {
				$shipping_settings   = get_option( $value->option_name );
				$time_slots_settings = '';
				if ( isset( $shipping_settings['time_slots'] ) && '' !== $shipping_settings['time_slots'] ) {
					$timeslot_settings = explode( '},', $shipping_settings['time_slots'] );
					$time_slot_str     = '';
					foreach ( $timeslot_settings as $hk => $hv ) {
						$specific_dates = '';
						if ( '' !== $hv ) {
							if ( '' !== $format_requested ) {
								$time_format_to_show = $format_requested;
							} else {
								if ( '1' === $time_format ) {
									$time_format_to_show = 'h:i A';
								} else {
									$time_format_to_show = 'H:i';
								}
							}
							$hv_str = str_replace( '}', '', $hv );
							$hv_str = str_replace( '{', '', $hv_str );

							$time_slot_charges_lable_str        = strrchr( $hv_str, ':' );
							$time_slot_charges_lable_str_length = strlen( $time_slot_charges_lable_str );
							$additional_charges_label           = substr( $time_slot_charges_lable_str, 1, $time_slot_charges_lable_str_length );

							$time_slot_charges_string     = substr( $hv_str, 0, -( $time_slot_charges_lable_str_length ) );
							$time_slot_charges_str        = strrchr( $time_slot_charges_string, ':' );
							$time_slot_charges_str_length = strlen( $time_slot_charges_str );
							$additional_charges           = substr( $time_slot_charges_str, 1, $time_slot_charges_str_length );

							$lockout_string     = substr( $time_slot_charges_string, 0, -( $time_slot_charges_str_length ) );
							$lockout_str        = strrchr( $lockout_string, ':' );
							$lockout_str_length = strlen( $lockout_str );
							$lockout            = substr( $lockout_str, 1, $lockout_str_length );

							$allpos        = array();
							$offset        = 0;
							$time_slot_str = substr( $lockout_string, 0, -( $lockout_str_length ) );
							$pos           = strpos( $time_slot_str, ':', $offset );
							while ( false !== $pos ) {
								$offset   = $pos + 1;
								$allpos[] = $pos;
								$pos      = strpos( $time_slot_str, ':', $offset );
							}

							$time_slot_pos = $allpos[1];
							$time_slot     = substr( $time_slot_str, ( $time_slot_pos ) + 1 );

							$time_slot_arr = explode( ' - ', $time_slot );
							$from_time     = gmdate( $time_format_to_show, strtotime( $time_slot_arr[0] ) );
							if ( isset( $time_slot_arr[1] ) ) {
								$to_time          = gmdate( $time_format_to_show, strtotime( $time_slot_arr[1] ) );
								$custom_time_slot = $from_time . ' - ' . $to_time;
							} else {
								$custom_time_slot = $from_time;
							}
							if ( ! in_array( $custom_time_slot, $time_slot_key_arr, true ) ) {
								$time_slot_key_arr[] = $custom_time_slot;
							}
						}
					}
				}
			}
		}

		$time_slot_key_arr['asap'] = __( 'As Soon As Possible.', 'order-delivery-date' );
		return $time_slot_key_arr;
	}

	/**
	 * Callback to disable the selected time slots
	 *
	 * @return string $timeslot_jarr JSON Encoded values for selected time slots
	 * @since 2.8.4
	 */
	public static function orddd_disable_time_slots_callback() {
		$disable_timeslot        = get_option( 'orddd_disable_time_slot_log' );
		$disable_devel_dates     = array();
		$selected_time_slot      = '';
		$disable_time_slot_value = '';

		if ( isset( $_POST['orddd_disable_time_slot_for_delivery_days'] ) ) {
			$disable_time_slot_value = $_POST['orddd_disable_time_slot_for_delivery_days'];
			if ( 'weekdays' === $disable_time_slot_value ) {
				if ( isset( $_POST['orddd_disable_time_slot_for_weekdays'] ) ) {
					$disable_devel_dates = $_POST['orddd_disable_time_slot_for_weekdays'];
				}
			} elseif ( 'dates' === $disable_time_slot_value ) {
				if ( isset( $_POST['disable_time_slot_for_dates'] ) ) {
					$disable_devel_dates = explode( ',', $_POST['disable_time_slot_for_dates'] );
				}
			}
		}

		if ( isset( $_POST['orddd_selected_time_slots_to_be_disabled'] ) ) {
			$selected_time_slot = wp_json_encode( $_POST['orddd_selected_time_slots_to_be_disabled'] );
		}

		$disable_timeslot_new_arr = array();
		if ( 'null' === $disable_timeslot ||
			'' === $disable_timeslot ||
			'{}' === $disable_timeslot ||
			'[]' === $disable_timeslot ) {
			$timeslot_arr = array();
		} else {
			$timeslot_arr = json_decode( $disable_timeslot );
		}

		if ( isset( $timeslot_arr ) && is_array( $timeslot_arr ) && count( $timeslot_arr ) > 0 ) {
			foreach ( $timeslot_arr as $k => $v ) {
				$disable_timeslot_new_arr[] = array(
					'dtv' => $v->dtv,
					'dd'  => $v->dd,
					'ts'  => $v->ts,
				);
			}
		}

		if ( is_array( $disable_devel_dates ) && count( $disable_devel_dates ) > 0 && '' !== $selected_time_slot ) {
			foreach ( $disable_devel_dates as $key => $value ) {
				if ( 'dates' === $disable_time_slot_value ) {
					$disable_date          = explode( '-', $value );
					$delivery_disable_date = gmdate( 'n-j-Y', gmmktime( 0, 0, 0, $disable_date[0], $disable_date[1], $disable_date[2] ) );
				} else {
					$delivery_disable_date = $value;
				}
				$disable_timeslot_new_arr[] = array(
					'dtv' => $disable_time_slot_value,
					'dd'  => $delivery_disable_date,
					'ts'  => $selected_time_slot,
				);
			}
		}
		$timeslot_jarr = wp_json_encode( $disable_timeslot_new_arr );
		return $timeslot_jarr;
	}
}

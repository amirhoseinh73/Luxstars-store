<?php
/**
 * Defines common functions related to fetching the delivery dates.
 *
 * @package Order-Delivery-Date-Pro-for-WooCommerce/Common-Functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Common functions to be used in plugin.
 */
class ORDDD_Functions {

	/**
	 * Fetch the dates to be displayed in the dropdown on checkout page.
	 *
	 * @return array
	 */
	public static function orddd_get_dates_for_dropdown( $number_of_dates ) {
		global $orddd_date_formats, $orddd_weekdays;
		$options['select']     = apply_filters( 'orddd_modify_select_dropdown_text', __( 'Select a delivery date', 'order-delivery-date' ) );
		$current_time          = orddd_get_current_time();
		$current_date          = gmdate( 'j-n-Y', $current_time );
		$next_day              = gmdate( 'j-n-Y', strtotime( $current_date . ' +1 day' ) );
		$date_format           = get_option( 'orddd_delivery_date_format' );
		$count                 = 0;
		$delivery_time_seconds = orddd_get_minimum_delivery_time();
		$holidays_str          = self::orddd_get_holidays_str();
		$lockout_days_str      = self::orddd_get_booked_days_str();
		$same_day_cutoff       = orddd_get_cutoff_timestamp( 'same_day' );
		$next_day_cutoff       = orddd_get_cutoff_timestamp( 'next_day' );
		$delivery_dates_arr    = array();
		$holidays      		   = self::orddd_get_holidays_array( $holidays_str );
		$booked_days   		   = self::orddd_get_booked_days_array( $lockout_days_str );

		if ( 'on' === get_option( 'orddd_enable_specific_delivery_dates' ) ) {
			$delivery_dates = get_option( 'orddd_delivery_dates' );
			if ( $delivery_dates != '' && $delivery_dates != '{}' && $delivery_dates != '[]' && $delivery_dates != 'null' ) {
				$delivery_dates_arr = json_decode( get_option( 'orddd_delivery_dates' ) );
			}
		}

		$dates_to_check = array();
		usort( $delivery_dates_arr, array( 'orddd_common', 'bkap_sort_specific_dates_array' ) );

		foreach ( $delivery_dates_arr as $k => $v ) {
			$date = explode( "-", $v->date );

			if ( in_array( $v->date, $holidays, true ) || in_array( $date[0] . "-" . $date[1], $holidays, true ) || in_array( $v->date, $booked_days, true ) ) {
				continue;
			}
			$lockout_date_split = explode( '-', $v->date );
			$date_lockout       = $lockout_date_split[1] . '-' . $lockout_date_split[0] . '-' . $lockout_date_split[2];
			$specific_timestamp = strtotime( $date_lockout );

			if ( $specific_timestamp >= $current_time ) {
				array_push( $dates_to_check, $v->date );
			}
		}

	    $is_all_past_specific_dates = orddd_common::orddd_get_if_past_specific_dates( $dates_to_check );

		$is_all_disable_weekdays = true;
		foreach ( $orddd_weekdays as $n => $day_name ) {
			$weekdays_to_check[ $n ] = get_option( $n );
			if ( $weekdays_to_check[ $n ] == 'checked' ) {
			   $is_all_disable_weekdays = false;
			}
		}

		$min_date_array = orddd_common::get_min_date(
			$delivery_time_seconds,
			array(
				'enabled'    => get_option( 'orddd_enable_delivery_time' ),
				'from_hours' => get_option( 'orddd_delivery_from_hours' ),
				'to_hours'   => get_option( 'orddd_delivery_to_hours' ),
				'from_mins'  => get_option( 'orddd_delivery_from_mins' ),
				'to_mins'    => get_option( 'orddd_delivery_to_mins' ),
			),
			$holidays_str,
			$lockout_days_str
		);
		$min_date       = $min_date_array['min_date'];

		while ( $count < $number_of_dates ) {
			$timestamp = strtotime( $min_date );
			$weekday   = date( 'w', $timestamp );
			$m         = date( 'n', $timestamp );
			$d         = date( 'j', $timestamp );
			$y         = date( 'Y', $timestamp );

			$highest_specific_date = end( $dates_to_check );
			if( 'Yes' == $is_all_past_specific_dates && $is_all_disable_weekdays ) {
				break;
			}

			if ( $is_all_disable_weekdays && $count >= count( $dates_to_check ) ) {
				break;
			}	

			if ( $is_all_disable_weekdays && $timestamp > strtotime( $highest_specific_date ) ) {
				break;
			}

			if ( ! $is_all_disable_weekdays ) {
				if ( ( 'checked' !== get_option( 'orddd_weekday_' . $weekday ) && ! in_array( $m . '-' . $d . '-' . $y, $dates_to_check ) ) || in_array( $m . '-' . $d . '-' . $y, $holidays ) || in_array( $m . '-' . $d , $holidays ) || in_array( $m . '-' . $d . '-' . $y, $booked_days ) ) {
					$min_date = gmdate( 'j-n-Y', strtotime( '+1 day', $timestamp ) );
					continue;
				}
			}

			if ( 'on' === get_option( 'orddd_enable_same_day_delivery' ) && $current_date == $min_date && $current_time >= $same_day_cutoff ) {
				$min_date = gmdate( 'j-n-Y', strtotime( '+1 day', $timestamp ) );
				continue;
			}

			if ( 'on' === get_option( 'orddd_enable_next_day_delivery' ) && ( $current_date == $min_date || $next_day == $min_date ) && $current_time >= $next_day_cutoff ) {
				$min_date = gmdate( 'j-n-Y', strtotime( '+1 day', $timestamp ) );
				continue;
			}

			if ( $is_all_disable_weekdays && ! empty( $dates_to_check ) ) {
				if ( ! in_array( date( 'n-j-Y', $timestamp ), $dates_to_check ) ) {
					$min_date = gmdate( 'j-n-Y', strtotime( '+1 day', $timestamp ) );
					if ( $number_of_dates <= $count && count( $options ) <= $number_of_dates ) {
						$count = 0;
					}
					continue;
				}
			}
			
			$delivery_date_format            = date_i18n( $orddd_date_formats[ $date_format ], $timestamp );
			$delivery_date_value             = date( 'j-n-Y', $timestamp );
			$options[ $delivery_date_value ] = $delivery_date_format;
			$min_date                        = gmdate( 'j-n-Y', strtotime( '+1 day', $timestamp ) );
			$count++;
			if ( $number_of_dates <= $count && count( $options ) <= $number_of_dates ) {
				$count = 0;
			}
			if ( count( $options ) > $number_of_dates ) {
				break;
			}
		}
		return $options;
	}

	/**
	 * Ajax call to add dates to dropdown for custom settings.
	 *
	 * @return string
	 */
	public static function check_for_dates_orddd( $number_of_dates, $custom_setting_id ) {
		global $orddd_date_formats;

		if ( 0 == $custom_setting_id ) {
			$options = self::orddd_get_dates_for_dropdown( $number_of_dates );
			return $options;
		}
		$custom_settings = get_option( 'orddd_shipping_based_settings_' . $custom_setting_id );
		$date_format     = get_option( 'orddd_delivery_date_format' );
		$count           = 0;
		$current_time    = orddd_get_current_time();
		$current_date    = date( 'j-n-Y', $current_time );
		$current_day     = date( 'd', $current_time );
		$current_month   = date( 'm', $current_time );
		$current_year    = date( 'Y', $current_time );
		$next_day        = date( 'j-n-Y', strtotime( $current_date . ' +1 day' ) );
		if ( isset( $custom_settings['minimum_delivery_time'] ) && $custom_settings['minimum_delivery_time'] > 0 ) {
			$delivery_time_seconds = $custom_settings['minimum_delivery_time'] * 60 * 60;
		} else {
			$delivery_time_seconds = 0;
		}
		$holidays_str          = orddd_common::orddd_get_custom_holidays( $custom_settings );
		$lockout_days_str      = orddd_common::orddd_get_custom_lockout_days( $custom_settings );

		$holidays    = self::orddd_get_holidays_array( $holidays_str );
		$booked_days = self::orddd_get_booked_days_array( $lockout_days_str );

		$specific_dates = orddd_common::orddd_get_all_shipping_specific_dates( $custom_settings );
	    $is_all_past_specific_dates = orddd_common::orddd_get_if_past_specific_dates( $specific_dates );
		$dates_to_check = array();
		$specific_dates_sorted_array = array();
		
		foreach ( $specific_dates as $date ) {
			$split_delivery_date_1 = explode( "-", $date );
			$delivery_day_1 = strtotime( $split_delivery_date_1[ 1 ] . "-" . $split_delivery_date_1[ 0 ] . "-" . $split_delivery_date_1[ 2 ] );
			$specific_dates_sorted_array[] = $delivery_day_1;

			if ( in_array( $date, $holidays, true ) || in_array( $split_delivery_date_1[ 0 ] . "-" . $split_delivery_date_1[ 1 ], $holidays, true ) || in_array( $date, $booked_days, true ) ) {
				continue;
			}
			$lockout_date_split = explode( '-', $date );
			$date_lockout       = $lockout_date_split[1] . '-' . $lockout_date_split[0] . '-' . $lockout_date_split[2];
			$specific_timestamp = strtotime( $date_lockout );
			if ( $specific_timestamp >= $current_time ) {
				array_push( $dates_to_check, $date );
			}
		}

		sort( $specific_dates_sorted_array );
		$highest_delivery_date = end( $specific_dates_sorted_array );

		$same_day_cut_off = orddd_get_highest_same_day();
		$custom_same_day  = array();
		if ( is_array( $same_day_cut_off ) && count( $same_day_cut_off ) > 0 ) {
			$custom_same_day = $same_day_cut_off;
		} else {
			if ( isset( $custom_settings['same_day'] ) ) {
				$custom_same_day = $custom_settings['same_day'];
			}
		}

		// Nexy Day Delivery
		$next_day_cut_off = orddd_get_highest_next_day();
		$custom_next_day  = array();
		if ( is_array( $next_day_cut_off ) && count( $next_day_cut_off ) > 0 ) {
			$custom_next_day = $next_day_cut_off;
		} else {
			if ( isset( $custom_settings['next_day'] ) ) {
				$custom_next_day = $custom_settings['next_day'];
			}
		}

		$is_all_disable_weekdays = true;
		if ( isset( $custom_settings[ 'delivery_type' ][ 'weekdays' ] ) && 'on' === $custom_settings[ 'delivery_type' ][ 'weekdays' ] ) {
			if ( isset( $custom_settings[ 'weekdays' ] ) ) {
				foreach ( $custom_settings[ 'weekdays' ] as $sk => $sv ) {
					if( isset( $sv[ 'enable' ] ) ) {
						$is_all_disable_weekdays = false;
					}
				}	
			}
		}

		$min_date_array = orddd_common::get_min_date(
			$delivery_time_seconds,
			array(
				'enabled'    => '',
				'from_hours' => '',
				'from_mins'  => '',
				'to_hours'   => '',
				'to_mins'    => '',
			),
			$holidays_str,
			$lockout_days_str,
			$custom_settings
		);

		$min_date          = $min_date_array['min_date'];
		$custom_weekdays   = isset( $custom_settings['weekdays'] ) ? $custom_settings['weekdays'] : array();
		$options['select'] = apply_filters( 'orddd_modify_select_dropdown_text', __( 'Select a delivery date', 'order-delivery-date' ) );
		// If there is no value found for number of dates in custom settings then it will check in global settings for that value.
		if ( ! $number_of_dates ) {
			$number_of_dates = get_option( 'orddd_number_of_dates' );
			// If there is no value found for number of dates in global settings then it will use the default value.
			if ( ! $number_of_dates ) {
				$number_of_dates = apply_filters( 'orddd_modify_select_dropdown_number_of_dates', 10 );
			}
		}
		while ( $count < $number_of_dates ) {
			$timestamp = strtotime( $min_date );
			$weekday   = date( 'w', $timestamp );
			$m         = date( 'n', $timestamp );
			$d         = date( 'j', $timestamp );
			$y         = date( 'Y', $timestamp );

			if( 'Yes' == $is_all_past_specific_dates && $is_all_disable_weekdays ) {
				break;
			}

			if( $is_all_disable_weekdays && $timestamp > $highest_delivery_date ) {
				break;
			}

			$custom_weekday = isset( $custom_weekdays ) && isset( $custom_weekdays[ 'orddd_weekday_' . $weekday ] ) && is_array( $custom_weekdays[ 'orddd_weekday_' . $weekday ] ) ? $custom_weekdays[ 'orddd_weekday_' . $weekday ] : array();
			if ( ! $is_all_disable_weekdays ) {
				if ( ( ! isset( $custom_weekday['enable'] ) && ! in_array( $m . '-' . $d . '-' . $y, $dates_to_check ) ) || ( isset( $custom_weekday['enable'] ) && 'checked' !== $custom_weekday['enable'] )
				|| in_array( $m . '-' . $d . '-' . $y, $holidays ) 
				|| in_array( $m . '-' . $d , $holidays )
				|| in_array( $m . '-' . $d . '-' . $y, $booked_days ) ) {
					$min_date = gmdate( 'j-n-Y', strtotime( '+1 day', $timestamp ) );
					$count++;
					if ( $number_of_dates <= $count && count( $options ) <= $number_of_dates ) {
						$count = 0;
					}
					continue;
				}
			}

			if ( ! ( isset( $custom_same_day['after_hours'] ) && $custom_same_day['after_hours'] == 0 && isset( $custom_same_day['after_minutes'] ) && $custom_same_day['after_minutes'] == '00' ) ) {
				if ( isset( $custom_same_day['after_hours'] ) ) {
					$cut_off_hour   = isset( $custom_same_day['after_hours'] ) ? $custom_same_day['after_hours'] : 0;
					$cut_off_minute = isset( $custom_same_day['after_minutes'] ) ? $custom_same_day['after_minutes'] : 0;

					$cut_off_timestamp = gmmktime( $cut_off_hour, $cut_off_minute, 0, $current_month, $current_day, $current_year );

					if ( $current_date == $min_date && $current_time >= $cut_off_timestamp ) {
						$min_date = gmdate( 'j-n-Y', strtotime( '+1 day', $timestamp ) );
						continue;
					}
				}
			}

			if ( ! ( isset( $custom_next_day['after_hours'] ) && $custom_next_day['after_hours'] == 0 && isset( $custom_next_day['after_minutes'] ) && $custom_next_day['after_minutes'] == 00 ) ) {
				if( isset( $custom_next_day['after_hours'] ) ) {
					$cut_off_hour   = isset( $custom_next_day['after_hours'] ) ? $custom_next_day['after_hours'] : 0;
					$cut_off_minute = isset( $custom_next_day['after_minutes'] ) ? $custom_next_day['after_minutes'] : 0;
	
					$cut_off_timestamp = gmmktime( $cut_off_hour, $cut_off_minute, 0, $current_month, $current_day, $current_year );
					if ( $current_date == $min_date ) {
						$min_date = gmdate( 'j-n-Y', strtotime( '+1 day', $timestamp ) );
						continue;
					}
					if ( ( $current_date == $min_date || $next_day == $min_date ) && $current_time >= $cut_off_timestamp ) {
						$min_date = gmdate( 'j-n-Y', strtotime( '+1 day', $timestamp ) );
						continue;
					}
				}
			}

			if ( $is_all_disable_weekdays && ! empty( $dates_to_check ) ) {
				if ( ! in_array( date( 'n-j-Y', $timestamp ), $dates_to_check ) ) {
					$min_date = gmdate( 'j-n-Y', strtotime( '+1 day', $timestamp ) );
					if ( $number_of_dates <= $count && count( $options ) <= $number_of_dates ) {
						$count = 0;
					}
					continue;
				}
			}

			$delivery_date_format            = date_i18n( $orddd_date_formats[ $date_format ], $timestamp );
			$delivery_date_value             = date( 'j-n-Y', $timestamp );
			$options[ $delivery_date_value ] = $delivery_date_format;
			$min_date                        = gmdate( 'j-n-Y', strtotime( '+1 day', $timestamp ) );
			$count++;
			if ( $number_of_dates <= $count && count( $options ) <= $number_of_dates ) {
				$count = 0;
			}
			if ( count( $options ) > $number_of_dates ) {
				break;
			}
		}
		return $options;
	}

	/**
	 * Returns the holidays from global settings in string format.
	 *
	 * @return string
	 */
	public static function orddd_get_holidays_str() {
		$holidays_arr = array();
		$holidays     = get_option( 'orddd_delivery_date_holidays' );
		if ( $holidays != '' && $holidays != '{}' && $holidays != '[]' && $holidays != 'null' ) {
			$holidays_arr = json_decode( get_option( 'orddd_delivery_date_holidays' ) );
		}
		$holidays_str = '';
		foreach ( $holidays_arr as $k => $v ) {
			// Replace single quote in the holiday name with the html entities
			// @todo: Need to fix the double quotes issue in the holiday name.
			// An error comes in console when the holiday name contains double quotes in it.
			$name = str_replace( "'", '&apos;', $v->n );
			$name = str_replace( '"', '&quot;', $name );
			$name = str_replace( '/', ' ', $name );
			$name = str_replace( '-', ' ', $name );

			if ( isset( $v->r_type ) && $v->r_type == 'on' ) {
				$holiday_date_arr = explode( '-', $v->d );
				$recurring_date   = $holiday_date_arr[0] . '-' . $holiday_date_arr[1];
				$holidays_str    .= '"' . $name . ':' . $recurring_date . '",';
			} else {
				$holidays_str .= '"' . $name . ':' . $v->d . '",';
			}
		}
		$holidays_str = apply_filters( 'ordd_add_to_holidays_str', $holidays_str );
		$holidays_str = substr( $holidays_str, 0, strlen( $holidays_str ) - 1 );

		return $holidays_str;
	}

	/**
	 * Returns the booked days from global settings in string format.
	 *
	 * @return string
	 */
	public static function orddd_get_booked_days_str() {
		$lockout_date_after_order = get_option( 'orddd_lockout_date_after_orders' );
		$lockout_days_str         = '';
		$booked_days              = ORDDD_Lockout_Days::orddd_get_booked_dates();

		foreach ( $booked_days as $booked_day ) {
			$lockout_days_str .= '"' . $booked_day . '",';
		}

		if ( 'on' === get_option( 'orddd_enable_time_slot' ) ) {
			$booked_timeslot_days = ORDDD_Lockout_Days::orddd_get_booked_timeslot_days();
			$blocked_days 		  = ORDDD_Lockout_Days::orddd_get_blocked_timeslot_days();

			foreach ( $booked_timeslot_days as $booked_day ) {
				$lockout_days_str .= '"' . $booked_day . '",';
			}

			foreach ( $blocked_days as $booked_day ) {
				$lockout_days_str .= '"' . $booked_day . '",';	
			}
		}

		$lockout_days_str = substr( $lockout_days_str, 0, strlen( $lockout_days_str ) - 1 );

		return $lockout_days_str;
	}

	/**
	 * Returns the holidays in array format.
	 *
	 * @param string $holidays_str
	 * @return array
	 */
	public static function orddd_get_holidays_array( $holidays_str ) {
		$holidays = array();

		$holidays_arr = explode( ',', $holidays_str );
		foreach ( $holidays_arr as $hkey => $hval ) {
			$hval           = str_replace( '"', '', $hval );
			$hval           = str_replace( '\\', '', $hval );
			$holidays_arr_1 = explode( ':', $hval );
			if ( isset( $holidays_arr_1[1] ) ) {
				$holidays[] = $holidays_arr_1[1];
			}
		}

		return $holidays;
	}

	/**
	 * Returns the booked days in array format.
	 *
	 * @param string $lockout_days_str
	 * @return array
	 */
	public static function orddd_get_booked_days_array( $lockout_days_str ) {
		$booked_days = array();

		$lockout_arr = explode( ',', $lockout_days_str );
		foreach ( $lockout_arr as $lkey => $lval ) {
			$lval          = str_replace( '"', '', $lval );
			$lval          = str_replace( '\\', '', $lval );
			$booked_days[] = $lval;
		}

		return $booked_days;
	}
}

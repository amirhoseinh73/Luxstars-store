<?php
/**
 * Check the lockout of general & custom settings.
 *
 * @package order-delivery-date
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Canculate the date & time availability for general & custom settings.
 */
class ORDDD_Lockout_Days {

	/**
	 * Get the current time timestamp.
	 *
	 * @return string
	 */
	public static function orddd_get_current_date_time() {
		$gmt = false;
		if ( has_filter( 'orddd_gmt_calculations' ) ) {
			$gmt = apply_filters( 'orddd_gmt_calculations', '' );
		}

		$current_time = current_time( 'timestamp', $gmt );
		return $current_time;
	}

	/**
	 * Return the dates which have been booked fully.
	 *
	 * @return array
	 */
	public static function orddd_get_booked_dates() {

		// Check the lockout for the specific dates.
		$specific_dates = array();
		$booked_dates   = array();
		$global_lockout = get_option( 'orddd_global_lockout_custom' );
		$total_orders   = array();

		if ( 'on' == $global_lockout ) {
			$total_orders = self::orddd_get_total_global_orders();
			$date_lockout = get_option( 'orddd_lockout_date_after_orders' );

			foreach ( $total_orders as $date => $lockout ) {
				if( $lockout >= $date_lockout ) {
					array_push( $booked_dates, $date );
				}
			}
		}

		if ( 'on' === get_option( 'orddd_enable_specific_delivery_dates' ) ) {
			$lockout_days = self::orddd_get_specific_dates_availability();
			foreach ( $lockout_days as $date => $available_lockout ) {
				if ( '' !== $available_lockout && $available_lockout <= 0 ) {
					array_push( $booked_dates, $date );
				}
			}
		}

		// Check the lockout for the weekdays if the date is not present in specific date. If it is a specific date then consider the specific date lockout.
		if ( get_option( 'orddd_lockout_date_after_orders' ) > 0 ) {
			$lockout_days = self::orddd_weekdays_availability();
			foreach ( $lockout_days as $date => $available_lockout ) {
				if ( '' !== $available_lockout && $available_lockout <= 0 ) {
					array_push( $booked_dates, $date );
				}
			}
		}

		return $booked_dates;
	}

	/**
	 * Return the dates where all timeslots have been booked.
	 *
	 * @return array
	 */
	public static function orddd_get_booked_timeslot_days() {
		$current_time           = self::orddd_get_current_date_time();
		$current_date           = date( 'j-n-Y', $current_time );
		$booked_dates           = array();
		$existing_timeslots_arr = json_decode( get_option( 'orddd_delivery_time_slot_log' ) );
		$time_format_to_show    = orddd_common::orddd_get_time_format();
		$delivery_days          = array();
		$specific_dates         = array();
		$lockout_arr            = array();

		foreach ( $existing_timeslots_arr as $k => $v ) {
			$from_time = date( $time_format_to_show, strtotime( $v->fh . ':' . trim( $v->fm, ' ' ) ) );
			$to_time   = date( $time_format_to_show, strtotime( $v->th . ':' . trim( $v->tm, ' ' ) ) );
			if ( $v->th != '' && $v->th != '00' && $v->tm != '' && $v->tm != '00' ) {
				$timeslot = $from_time . ' - ' . $to_time;
			} else {
				$timeslot = $from_time;
			}
			$dd = json_decode( $v->dd );

			if ( is_array( $dd ) && count( $dd ) > 0 ) {
				foreach ( $dd as $dkey => $dval ) {
					if ( 'specific_dates' == $v->tv ) {
						$lockout_date_split = explode( '-', $dval );
						$date_lockout       = $lockout_date_split[0] . '-' . $lockout_date_split[1] . '-' . $lockout_date_split[2];
						if ( isset( $specific_dates[ $date_lockout ] ) ) {
							$specific_dates[ $date_lockout ][ $timeslot ] = $v->lockout;
						} else {
							$specific_dates[ $date_lockout ] = array( $timeslot => $v->lockout );
						}
					} else {
						if ( isset( $delivery_days[ $dval ] ) ) {
							$delivery_days[ $dval ][ $timeslot ] = $v->lockout;
						} else {
							$delivery_days[ $dval ] = array( $timeslot => $v->lockout );
						}
					}
				}
			}
		}

		$timeslot_dates = self::orddd_get_timeslot_availability();
		$lockout_arr    = self::orddd_get_booked_timeslots( $timeslot_dates, $current_date );

		// For time slot lockout the date format saved in the database is j-n-Y. And the date format we add in the booked days array is n-j-Y.
		foreach ( $lockout_arr as $date => $timeslot ) {
			$lockout_date_split = explode( '-', $date );
			$date_lockout       = $lockout_date_split[1] . '-' . $lockout_date_split[0] . '-' . $lockout_date_split[2];
			$count 				= 0;
			$specific_date 		= date( 'n-j-Y', strtotime( $date ) );
			$weekday 	   		= date( 'w', strtotime( $date ) );
			
			foreach ( $timeslot as $key => $time ) {
				if ( is_array( $specific_dates ) && count( $specific_dates ) > 0 && get_option( 'orddd_enable_specific_delivery_dates' ) == 'on' ) {
					if ( isset( $specific_dates[ $specific_date ] ) && is_array( $lockout_arr[ $date ] ) ) {
						if ( isset( $specific_dates[ $specific_date ][ $time ] ) ) {
							$count++;
						}
						continue;
					}
				}

				if ( is_array( $delivery_days ) && count( $delivery_days ) > 0 ) {
					if ( isset( $delivery_days[ 'orddd_weekday_' . $weekday ] ) && is_array( $lockout_arr[ $date ] ) && isset( $delivery_days[ 'orddd_weekday_' . $weekday ][ $time ] ) ) {
						$count++;
					}
				}
			}

			if ( isset( $specific_dates[ $specific_date ] ) && count( $specific_dates[ $specific_date ] ) === $count ) {
				array_push( $booked_dates, $date_lockout );
			} elseif ( count( $delivery_days[ 'orddd_weekday_' . $weekday ] ) === $count ) {
				array_push( $booked_dates, $date_lockout );
			}
		}

		return $booked_dates;
	}

	/**
	 * Get the specific dates remaining availability.
	 *
	 * @return array
	 */
	public static function orddd_get_specific_dates_availability() {
		$current_time = self::orddd_get_current_date_time();
		$current_date = date( 'j-n-Y', $current_time );

		// Partially booked dates for only date for general settings.
		$lockout_days_arr = array();
		$lockout_days     = get_option( 'orddd_lockout_days' );
		if ( $lockout_days != '' && $lockout_days != '{}' && $lockout_days != '[]' && $lockout_days != 'null' ) {
			$lockout_days_arr = json_decode( get_option( 'orddd_lockout_days' ) );
		}
		$specific_dates     = array();
		$delivery_days      = array();
		$all_specific_dates = array();
		$date_lockout       = get_option( 'orddd_lockout_date_after_orders' );
		$global_lockout     = get_option( 'orddd_global_lockout_custom' );
		$total_orders       = array();

		if ( 'on' == $global_lockout ) {
			$total_orders = self::orddd_get_total_global_orders();
		}

		$delivery_dates = get_option( 'orddd_delivery_dates' );

		if ( $delivery_dates != '' && $delivery_dates != '{}' && $delivery_dates != '[]' && $delivery_dates != 'null' ) {
			$delivery_days = json_decode( $delivery_dates );
		}

		foreach ( $lockout_days_arr as $k => $v ) {
			$date               = $v->d;
			$lockout_date_split = explode( '-', $v->d );
			$date_lockout_time  = strtotime( $lockout_date_split[1] . '-' . $lockout_date_split[0] . '-' . $lockout_date_split[2] );
			foreach ( $delivery_days as $key => $value ) {
				if ( 'on' == $global_lockout ) {
					if ( isset( $total_orders[ $date ] ) && $date == $value->date ) {
						$available_orders        = $date_lockout - $total_orders[ $date ];
						$specific_dates[ $date ] = $available_orders;
					} elseif ( $date == $value->date ) {
						$specific_dates[ $date ] = $date_lockout;
					}
				} elseif ( $date_lockout_time >= strtotime( $current_date ) && $date == $value->date && $value->max_orders !== '' ) {
					$available_orders        = $value->max_orders - $v->o;
					$specific_dates[ $date ] = $available_orders;
				} elseif ( $date_lockout_time >= strtotime( $current_date ) && $date == $value->date && $value->max_orders == '' ) {
					$specific_dates[ $date ] = '';
				}
			}
		}

		return $specific_dates;
	}

	/**
	 * Get the weekdays remaining availability in general settings.
	 *
	 * @return array
	 */
	public static function orddd_weekdays_availability() {
		$current_time = self::orddd_get_current_date_time();
		$current_date = date( 'j-n-Y', $current_time );

		// Partially booked dates for only date for general settings.
		$lockout_days_arr = array();
		$lockout_days     = get_option( 'orddd_lockout_days' );
		if ( $lockout_days != '' && $lockout_days != '{}' && $lockout_days != '[]' && $lockout_days != 'null' ) {
			$lockout_days_arr = json_decode( get_option( 'orddd_lockout_days' ) );
		}

		$delivery_days  = array();
		$specific_dates = self::orddd_get_specific_dates_availability();
		$date_lockout   = get_option( 'orddd_lockout_date_after_orders' );
		$global_lockout = get_option( 'orddd_global_lockout_custom' );
		$total_orders   = array();

		if ( 'on' == $global_lockout ) {
			$total_orders = self::orddd_get_total_global_orders();
		}

		$date_arr = array();

		foreach ( $lockout_days_arr as $k => $v ) {
			if ( isset( $specific_dates ) && array_key_exists( $v->d, $specific_dates ) ) {
				continue;
			}
			$date               = $v->d;
			$lockout_date_split = explode( '-', $date );
			$date_lockout_time  = strtotime( $lockout_date_split[1] . '-' . $lockout_date_split[0] . '-' . $lockout_date_split[2] );
			if ( $date_lockout_time >= strtotime( $current_date ) && ! in_array( $date, $date_arr ) ) {
				if ( 'on' == $global_lockout ) {
					if ( isset( $total_orders[ $date ] ) ) {
						$available_orders       = $date_lockout - $total_orders[ $date ];
						$delivery_days[ $date ] = $available_orders;
					} else {
						$delivery_days[ $date ] = $date_lockout;
					}
				} elseif ( $date_lockout > 0 && $date_lockout != '' ) {
					$available_orders       = $date_lockout - $v->o;
					$delivery_days[ $date ] = $available_orders;
				} else {
					$delivery_days[ $date ] = '';
				}
				$date_arr[] = $v->d;
			}
		}
		return $delivery_days;
	}

	/**
	 * Get the general settings timeslots availability.
	 *
	 * @return array
	 */
	public static function orddd_get_timeslot_availability() {
		$lockout_timeslots_arr = array();
		$current_time          = self::orddd_get_current_date_time();
		$current_date          = date( 'j-n-Y', $current_time );
		$current_weekday       = date( 'w', $current_time );
		$time_format_to_show   = orddd_common::orddd_get_time_format();

		$lockout_timeslots_days = get_option( 'orddd_lockout_time_slot' );
		if ( $lockout_timeslots_days != '' && $lockout_timeslots_days != '{}' && $lockout_timeslots_days != '[]' && $lockout_timeslots_days != 'null' ) {
			$lockout_timeslots_arr = json_decode( get_option( 'orddd_lockout_time_slot' ) );
		}

		$existing_timeslots_arr = json_decode( get_option( 'orddd_delivery_time_slot_log' ) );

		$dates         = array();
		$delivery_days = array();

		foreach ( $existing_timeslots_arr as $k => $v ) {
			$from_time = date( $time_format_to_show, strtotime( $v->fh . ':' . trim( $v->fm, ' ' ) ) );
			$to_time   = date( $time_format_to_show, strtotime( $v->th . ':' . trim( $v->tm, ' ' ) ) );
			if ( $v->th != '' && $v->th != '00' && $v->tm != '' && $v->tm != '00' ) {
				$timeslot = $from_time . ' - ' . $to_time;
			} else {
				$timeslot = $from_time;
			}
			$dd = json_decode( $v->dd );

			if ( is_array( $dd ) && count( $dd ) > 0 ) {
				foreach ( $dd as $dkey => $dval ) {
					if ( isset( $delivery_days[ $dval ] ) ) {
						$delivery_days[ $dval ][ $timeslot ] = $v->lockout;
					} else {
						$delivery_days[ $dval ] = array( $timeslot => $v->lockout );
					}
				}
			}
		}

		// Get all time slots for current date so that we can determine the past time slots as well.
		foreach ( $delivery_days as $key => $value ) {
			if ( $key == $current_date && isset( $delivery_days[ $current_date ] ) ) {
				$dates[ $current_date ] = $delivery_days[ $current_date ];
			} elseif ( $key == 'orddd_weekday_' . $current_weekday && isset( $delivery_days[ 'orddd_weekday_' . $current_weekday ] ) ) {
				$dates[ $current_date ] = $delivery_days[ 'orddd_weekday_' . $current_weekday ];
			}
		}

		foreach ( $lockout_timeslots_arr as $k => $v ) {
			$date    = date( 'j-n-Y', strtotime( $v->d ) );
			$weekday = date( 'w', strtotime( $v->d ) );

			$lockout_date_split = explode( '-', $date );
			$date_lockout       = $lockout_date_split[1] . '-' . $lockout_date_split[0] . '-' . $lockout_date_split[2];
			$date_lockout_time  = strtotime( $date_lockout );
			$timeslot           = orddd_common::orddd_change_time_slot_format( $v->t, $time_format_to_show );

			if ( isset( $delivery_days[ $date_lockout ][ $timeslot ] ) ) {
				if ( '' !== $delivery_days[ $date_lockout ][ $timeslot ] ) {
					$dates[ $date ][ $timeslot ] = $delivery_days[ $date_lockout ][ $timeslot ] - $v->o;
				} else {
					$dates[ $date ][ $timeslot ] = '';
				}
			} elseif ( isset( $delivery_days[ 'orddd_weekday_' . $weekday ][ $timeslot ] ) ) {
				if ( '' != $delivery_days[ 'orddd_weekday_' . $weekday ][ $timeslot ] && 
					 '0' != $delivery_days[ 'orddd_weekday_' . $weekday ][ $timeslot ] ) {
					$dates[ $date ][ $timeslot ] = $delivery_days[ 'orddd_weekday_' . $weekday ][ $timeslot ] - $v->o;
				} else {
					$dates[ $date ][ $timeslot ] = '';
				}
			} elseif( isset( $delivery_days[ 'all' ][ $timeslot ] ) ) {
				if( '' != $delivery_days[ 'all' ][ $timeslot ] && '0' != $delivery_days[ 'all' ][ $timeslot ] ) {
					$dates[ $date ][ $timeslot ] = $delivery_days[ 'all' ][ $timeslot ] - $v->o;
				} else {
					$dates[ $date ][ $timeslot ] = '';
				}
			}
		}
		return $dates;
	}

	/**
	 * Return the dates which are fully booked for a custom setting.
	 *
	 * @param array $shipping_settings Shipping settings.
	 * @return array
	 */
	public static function orddd_get_custom_booked_dates( $shipping_settings ) {
		// Check the lockout for the specific dates.
		$specific_dates = array();
		$booked_dates   = array();
		$global_lockout = get_option( 'orddd_global_lockout_custom' );
		$total_orders   = array();

		if ( 'on' == $global_lockout && '' === $shipping_settings['date_lockout'] ) {
			$total_orders = self::orddd_get_total_global_orders();
			$date_lockout = get_option( 'orddd_lockout_date_after_orders' );

			foreach ( $total_orders as $date => $lockout ) {
				if( $lockout >= $date_lockout ) {
					array_push( $booked_dates, $date );
				}
			}
		}

		if ( isset( $shipping_settings['specific_dates'] ) ) {
			$lockout_days = self::orddd_get_custom_specific_dates_availability( $shipping_settings );
			foreach ( $lockout_days as $date => $available_lockout ) {
				if ( '' !== $available_lockout && $available_lockout <= 0 ) {
					array_push( $booked_dates, $date );
				}
			}
		}

		// Check the lockout for the weekdays if the date is not present in specific date. If it is a specific date then consider the specific date lockout.
		if ( isset( $shipping_settings['date_lockout'] ) ) {
			$lockout_days = self::orddd_get_custom_weekdays_availability( $shipping_settings );

			foreach ( $lockout_days as $date => $available_lockout ) {
				if ( '' !== $available_lockout && $available_lockout <= 0 ) {
					array_push( $booked_dates, $date );
				}
			}
		}

		return $booked_dates;
	}

	/**
	 * Get the weekdays availability for custom settings.
	 *
	 * @param array $shipping_settings Shipping settings.
	 * @return array
	 */
	public static function orddd_get_custom_weekdays_availability( $shipping_settings ) {
		$current_time = self::orddd_get_current_date_time();
		$current_date = date( 'j-n-Y', $current_time );

		// Partially booked dates for only date for general settings.
		$lockout_days_arr = array();
		$lockout_days     = isset( $shipping_settings['orddd_lockout_date'] ) ? $shipping_settings['orddd_lockout_date'] : '';
		if ( $lockout_days != '' && $lockout_days != '{}' && $lockout_days != '[]' && $lockout_days != 'null' ) {
			$lockout_days_arr = json_decode( $shipping_settings['orddd_lockout_date'] );
		}

		$delivery_days  = array();
		$specific_dates = self::orddd_get_custom_specific_dates_availability( $shipping_settings );
		$date_lockout   = $shipping_settings['date_lockout'];

		$global_lockout     = get_option( 'orddd_global_lockout_custom' );
		$general_max_orders = get_option( 'orddd_lockout_date_after_orders' );
		$total_orders       = array();

		if ( 'on' == $global_lockout ) {
			$total_orders = self::orddd_get_total_global_orders();
		}
		$date_arr = array();
		foreach ( $lockout_days_arr as $k => $v ) {
			if ( isset( $specific_dates ) && array_key_exists( $v->d, $specific_dates ) ) {
				continue;
			}
			$date               = $v->d;
			$lockout_date_split = explode( '-', $date );
			$lockout_date       = $lockout_date_split[1] . '-' . $lockout_date_split[0] . '-' . $lockout_date_split[2];
			$date_lockout_time  = strtotime( $lockout_date );
			if ( $date_lockout_time >= strtotime( $current_date ) && ! in_array( $date, $date_arr ) ) {
				if ( 'on' == $global_lockout && '' === $shipping_settings['date_lockout'] ) {
					if ( isset( $total_orders[ $date ] ) ) {
						$available_orders       = $general_max_orders - $total_orders[ $date ];
						$delivery_days[ $date ] = $available_orders;
					} else {
						$delivery_days[ $date ] = $general_max_orders;
					}
				} elseif ( $date_lockout > 0 && $date_lockout != '' ) {
					$available_orders       = $date_lockout - $v->o;
					$delivery_days[ $date ] = $available_orders;
				} else {
					$delivery_days[ $date ] = '';
				}
				$date_arr[] = $v->d;
			}
		}
		return $delivery_days;
	}

	/**
	 * Get the specific dates availability for custom settings.
	 *
	 * @param array $shipping_settings Shipping settings.
	 * @return array
	 */
	public static function orddd_get_custom_specific_dates_availability( $shipping_settings ) {
		$current_time = self::orddd_get_current_date_time();
		$current_date = date( 'j-n-Y', $current_time );

		// Partially booked dates for only date for general settings.
		$lockout_days_arr = array();
		$lockout_days     = isset( $shipping_settings['orddd_lockout_date'] ) ? $shipping_settings['orddd_lockout_date'] : '';
		if ( $lockout_days != '' && $lockout_days != '{}' && $lockout_days != '[]' && $lockout_days != 'null' ) {
			$lockout_days_arr = json_decode( $shipping_settings['orddd_lockout_date'] );
		}
		$specific_dates     = array();
		$delivery_days      = array();
		$all_specific_dates = array();

		// For global max deliveries for custom settings.
		$global_lockout     = get_option( 'orddd_global_lockout_custom' );
		$general_max_orders = get_option( 'orddd_lockout_date_after_orders' );
		$total_orders       = array();
		if ( 'on' == $global_lockout ) {
			$total_orders = self::orddd_get_total_global_orders();
		}

		$specific_days_settings = explode( ',', $shipping_settings['specific_dates'] );

		foreach ( $lockout_days_arr as $k => $v ) {
			$date               = $v->d;
			$lockout_date_split = explode( '-', $v->d );
			$date_lockout_time  = strtotime( $lockout_date_split[1] . '-' . $lockout_date_split[0] . '-' . $lockout_date_split[2] );
			foreach ( $specific_days_settings as $sk => $sv ) {
				if ( $sv != '' ) {
					$sv                = str_replace( '}', '', $sv );
					$sv                = str_replace( '{', '', $sv );
					$specific_date_arr = explode( ':', $sv );

					if ( 'on' == $global_lockout && '' == $specific_date_arr[3] ) {
						if ( isset( $total_orders[ $date ] ) && $date == $specific_date_arr[0] ) {
							$available_orders        = $general_max_orders - $total_orders[ $date ];
							$specific_dates[ $date ] = $available_orders;
						} elseif( $date == $specific_date_arr[0] ) {
							$specific_dates[ $date ] = $general_max_orders;
						}
					} elseif ( $date_lockout_time >= strtotime( $current_date ) && $date == $specific_date_arr[0] && '' != $specific_date_arr[3] ) {
						$available_orders        = $specific_date_arr[3] - $v->o;
						$specific_dates[ $date ] = $available_orders;
					} elseif ( $date_lockout_time >= strtotime( $current_date ) && $date == $specific_date_arr[0] && '' == $specific_date_arr[3] ) {
						$specific_dates[ $date ] = '';
					}
				}
			}
		}

		return $specific_dates;
	}

	/**
	 * Get the dates for which all the timeslots have been booked for custom settings.
	 *
	 * @param array $shipping_settings Shipping settings.
	 * @return array
	 */
	public static function orddd_get_custom_booked_timeslots( $shipping_settings ) {
		$current_time        = self::orddd_get_current_date_time();
		$current_date        = date( 'j-n-Y', $current_time );
		$booked_dates        = array();
		$lockout_arr         = array();
		$delivery_days       = array();
		$specific_dates      = array();
		$time_slots          = explode( '},', $shipping_settings['time_slots'] );
		$time_format_to_show = orddd_common::orddd_get_time_format();

		foreach ( $time_slots as $key => $value ) {
			if ( '' !== $value ) {
				$timeslot_values = orddd_common::get_timeslot_values( $value );
				$timeslot        = $timeslot_values['time_slot'];

				$time_slot_arr      = explode( ' - ', $timeslot_values['time_slot'] );
				$from_time_hour_arr = explode( ':', $time_slot_arr[0] );
				// Convert the time slot in the selected time format.
				$from_time     = date( $time_format_to_show, strtotime( trim( $time_slot_arr[0] ) ) );
				$from_time_arr = explode( ':', $from_time );
				if ( isset( $time_slot_arr[1] ) ) {
					$to_time  = date( $time_format_to_show, strtotime( trim( $time_slot_arr[1] ) ) );
					$timeslot = $from_time . ' - ' . $to_time;
				} else {
					$timeslot = $from_time;
				}

				$lockout = '';
				if ( 'specific_dates' == $timeslot_values['delivery_days_selected'] ) {
					foreach ( $timeslot_values['selected_days'] as $dkey => $dval ) {
						if ( $timeslot_values['lockout'] != '' && $timeslot_values['lockout'] != '0' ) {
							$lockout = $timeslot_values['lockout'];
						} elseif ( get_option( 'orddd_global_lockout_time_slots' ) != '0' && get_option( 'orddd_global_lockout_time_slots' ) != '' ) {
							$lockout = get_option( 'orddd_global_lockout_time_slots' );
						} else {
							$lockout = '';
						}

						if ( isset( $specific_dates[ $dval ] ) ) {
							$specific_dates[ $dval ][ $timeslot ] = $lockout;
						} else {
							$specific_dates[ $dval ] = array( $timeslot => $lockout );
						}
					}
				} else {
					foreach ( $timeslot_values['selected_days'] as $dkey => $dval ) {
						if ( $timeslot_values['lockout'] != '' && $timeslot_values['lockout'] != '0' ) {
							$lockout = $timeslot_values['lockout'];
						} elseif ( get_option( 'orddd_global_lockout_time_slots' ) != '0' && get_option( 'orddd_global_lockout_time_slots' ) != '' ) {
							$lockout = get_option( 'orddd_global_lockout_time_slots' );
						} else {
							$lockout = '';
						}

						if ( isset( $delivery_days[ $dval ] ) ) {
							$delivery_days[ $dval ][ $timeslot ] = $lockout;
						} else {
							$delivery_days[ $dval ] = array( $timeslot => $lockout );
						}
					}
				}
			}
		}

		$timeslot_dates = self::orddd_get_custom_timeslot_availability( $shipping_settings );
		$lockout_arr    = self::orddd_get_booked_timeslots( $timeslot_dates, $current_date );

		// For time slot lockout the date format saved in the database is j-n-Y. And the date format we add in the booked days array is n-j-Y.
		foreach ( $lockout_arr as $date => $timeslot ) {
			$lockout_date_split = explode( '-', $date );
			$date_lockout       = $lockout_date_split[1] . '-' . $lockout_date_split[0] . '-' . $lockout_date_split[2];
			$count 				= 0;
			$specific_date 		= date( 'n-j-Y', strtotime( $date ) );
			$weekday 	   		= date( 'w', strtotime( $date ) );

			foreach ( $timeslot as $key => $time ) {
				if ( isset( $shipping_settings['delivery_type']['specific_dates'] ) && 'on' === $shipping_settings['delivery_type']['specific_dates'] && isset( $specific_dates[ $specific_date ] ) && is_array( $lockout_arr[ $date ] ) ) {
					if ( isset( $specific_dates[ $specific_date ][ $time ] ) ) {
						$count++;
					}
					continue;
				}

				if ( is_array( $delivery_days ) && count( $delivery_days ) > 0 ) {
					if ( isset( $delivery_days[ 'orddd_weekday_' . $weekday . '_custom_setting' ] ) && is_array( $lockout_arr[ $date ] ) && isset( $delivery_days[ 'orddd_weekday_' . $weekday . '_custom_setting' ][ $time ] ) ) {
						$count++;
					}
				}
			}

			if ( isset( $specific_dates[ $specific_date ] ) && count( $specific_dates[ $specific_date ] ) === $count ) {
				array_push( $booked_dates, $date_lockout );
			} elseif ( count( $delivery_days[ 'orddd_weekday_' . $weekday . '_custom_setting' ] ) === $count ) {
				array_push( $booked_dates, $date_lockout );
			}
		}

		return $booked_dates;
	}

	/**
	 * Get the timeslot availability for custom settings.
	 *
	 * @param array $shipping_settings.
	 * @return array
	 */
	public static function orddd_get_custom_timeslot_availability( $shipping_settings ) {
		$current_time        = self::orddd_get_current_date_time();
		$current_date        = date( 'j-n-Y', $current_time );
		$current_weekday     = date( 'w', $current_time );
		$dates               = array();
		$delivery_days       = array();
		$time_format_to_show = orddd_common::orddd_get_time_format();

		if ( isset( $shipping_settings['time_slots'] ) && '' != $shipping_settings['time_slots'] ) {
			$time_slots = explode( '},', $shipping_settings['time_slots'] );

			foreach ( $time_slots as $key => $value ) {
				if ( '' !== $value ) {
					$timeslot_values    = orddd_common::get_timeslot_values( $value );
					$timeslot           = $timeslot_values['time_slot'];
					$time_slot_arr      = explode( ' - ', $timeslot_values['time_slot'] );
					$from_time_hour_arr = explode( ':', $time_slot_arr[0] );
					// Convert the time slot in the selected time format.
					$from_time     = date( $time_format_to_show, strtotime( trim( $time_slot_arr[0] ) ) );
					$from_time_arr = explode( ':', $from_time );
					if ( isset( $time_slot_arr[1] ) ) {
						$to_time  = date( $time_format_to_show, strtotime( trim( $time_slot_arr[1] ) ) );
						$timeslot = $from_time . ' - ' . $to_time;
					} else {
						$timeslot = $from_time;
					}

					$lockout = '';
					foreach ( $timeslot_values['selected_days'] as $dkey => $dval ) {
						if ( $timeslot_values['lockout'] != '' && $timeslot_values['lockout'] != '0' ) {
							$lockout = $timeslot_values['lockout'];
						} elseif ( get_option( 'orddd_global_lockout_time_slots' ) != '0' && get_option( 'orddd_global_lockout_time_slots' ) != '' ) {
							$lockout = get_option( 'orddd_global_lockout_time_slots' );
						} else {
							$lockout = '';
						}

						if ( isset( $delivery_days[ $dval ] ) ) {
							$delivery_days[ $dval ][ $timeslot ] = $lockout;
						} else {
							$delivery_days[ $dval ] = array( $timeslot => $lockout );
						}
					}
				}
			}

			// Get all time slots for current date so that we can determine the past time slots as well.
			foreach ( $delivery_days as $key => $value ) {
				if ( $key == $current_date && isset( $delivery_days[ $current_date ] ) ) {
					$dates[ $current_date ] = $delivery_days[ $current_date ];
				} elseif ( $key == 'orddd_weekday_' . $current_weekday . '_custom_setting' && isset( $delivery_days[ 'orddd_weekday_' . $current_weekday . '_custom_setting' ] ) ) {
					$dates[ $current_date ] = $delivery_days[ 'orddd_weekday_' . $current_weekday . '_custom_setting' ];
				}
			}

			if ( isset( $shipping_settings['orddd_lockout_time_slot'] ) ) {
				$lockout_time = $shipping_settings['orddd_lockout_time_slot'];
				if ( $lockout_time == '' || $lockout_time == '{}' || $lockout_time == '[]' || $lockout_time == 'null' ) {
					$lockout_time_arr = array();
				} else {
					$lockout_time_arr = json_decode( $lockout_time );
				}

				foreach ( $lockout_time_arr as $k => $v ) {
					$date    = date( 'j-n-Y', strtotime( $v->d ) );
					$weekday = date( 'w', strtotime( $v->d ) );

					$lockout_date_split = explode( '-', $date );
					$date_lockout       = $lockout_date_split[1] . '-' . $lockout_date_split[0] . '-' . $lockout_date_split[2]; // m-d-y
					$date_lockout_time  = strtotime( $date_lockout );
					$timeslot           = orddd_common::orddd_change_time_slot_format( $v->t, $time_format_to_show );

					if ( isset( $delivery_days[ $date_lockout ][ $timeslot ] ) ) {
						if ( '' != $delivery_days[ $date_lockout ][ $timeslot ] ) {
							$dates[ $date ][ $timeslot ] = $delivery_days[ $date_lockout ][ $timeslot ] - $v->o;
						} else {
							$dates[ $date ][ $timeslot ] = '';
						}
					} elseif ( isset( $delivery_days[ 'orddd_weekday_' . $weekday . '_custom_setting' ][ $timeslot ] ) ) {
						if ( '' != $delivery_days[ 'orddd_weekday_' . $weekday . '_custom_setting' ][ $timeslot ] && 
							 '0' != $delivery_days[ 'orddd_weekday_' . $weekday . '_custom_setting' ][ $timeslot ] ) {
							$dates[ $date ][ $timeslot ] = $delivery_days[ 'orddd_weekday_' . $weekday . '_custom_setting' ][ $timeslot ] - $v->o;
						} else {
							$dates[ $date ][ $timeslot ] = '';
						}
					}
				}
			}
		}
		return $dates;
	}

	/**
	 * Get the total number of orders including all custom settings & general settings.
	 *
	 * @return array
	 */
	public static function orddd_get_total_global_orders() {
		$current_time = self::orddd_get_current_date_time();
		$current_date = date( 'j-n-Y', $current_time );

		$global_lockout_custom = get_option( 'orddd_global_lockout_custom' );
		$date_lockout          = get_option( 'orddd_lockout_date_after_orders' );
		$general_lockout       = '' != get_option( 'orddd_lockout_days' ) ? json_decode( get_option( 'orddd_lockout_days' ) ) : array();
		$total_orders          = array();
		$results               = orddd_common::orddd_get_shipping_settings();

		if ( is_array( $general_lockout ) && count( $general_lockout ) > 0 ) {
			foreach ( $general_lockout as $k => $v ) {
				$date               = $v->d;
				$lockout_date_split = explode( '-', $v->d );
				$date_lockout_time  = strtotime( $lockout_date_split[1] . '-' . $lockout_date_split[0] . '-' . $lockout_date_split[2] );
				if ( $date_lockout_time >= strtotime( $current_date ) ) {
					if ( isset( $total_orders[ $date ] ) ) {
						$total_orders[ $date ] = $total_orders[ $date ] + $v->o;
					} else {
						$total_orders[ $date ] = $v->o;
					}
				}
			}
		}

		if ( is_array( $results ) && count( $results ) > 0 ) {
			foreach ( $results as $key => $value ) {
				$shipping_settings = get_option( $value->option_name );

				$custom_lockout = array();
				if ( isset( $shipping_settings['orddd_lockout_date'] ) && 
					 '' != $shipping_settings['orddd_lockout_date'] &&
					 'null' != $shipping_settings['orddd_lockout_date'] ) {
					$custom_lockout    = json_decode( $shipping_settings['orddd_lockout_date'] );
				}

				foreach ( $custom_lockout as $k => $v ) {
					$date               = $v->d;
					$lockout_date_split = explode( '-', $v->d );
					$date_lockout_time  = strtotime( $lockout_date_split[1] . '-' . $lockout_date_split[0] . '-' . $lockout_date_split[2] );
					if ( $date_lockout_time >= strtotime( $current_date ) ) {
						if ( isset( $total_orders[ $date ] ) ) {
							$total_orders[ $date ] = $total_orders[ $date ] + $v->o;
						} else {
							$total_orders[ $date ] = $v->o;
						}
					}
				}
			}
		}

		return $total_orders;
	}

	/**
	 * 
	 * Gets the lockout dates by checking certain conditions
	 *
	 * @param array $timeslot_dates Array of available time slots along with their dates
	 * @param array $current_date   Current / Today's date
	 * 
	 * @return array $lockout_arr 
	 * 
	 * @since 9.17.1
	 */
	public static function orddd_get_booked_timeslots( $timeslot_dates, $current_date ) {
		$current_time = self::orddd_get_current_date_time();
		$lockout_arr  = array();
		foreach ( $timeslot_dates as $key => $value ) {
			foreach ( $value as $time => $available_lockout ) {

				// Below we are fetching the timestamp of the first time slot for current date in $check_time variable.
				$time_arr   = explode( ' - ', $time );
				$check_time = strtotime( $current_date . ' ' . $time_arr[0] );

				// todo: Commenting out below condition from the 'if' block. It was added to fix a condition when a date has 2 time slots & 1 time slot is full whereas the other one is available.
				// ( $available_lockout <= 0 || ( $key == $current_date && $check_time < $current_time ) )
				if ( '' !== $available_lockout && $available_lockout <= 0 ) {
					if ( isset( $lockout_arr[ $key ] ) ) {
						array_push( $lockout_arr[ $key ], $time );
					} else {
						$lockout_arr[ $key ] = array( $time );
					}
				}
			}
		}
		return $lockout_arr;
	}

	/**
	 * Return the dates where all timeslots for a day are blocked through Block Timeslot setting.
	 *
	 * @return array
	 */
	public static function orddd_get_blocked_timeslot_days() {
		$current_time           = self::orddd_get_current_date_time();
		$current_date           = date( 'j-n-Y', $current_time );
		$booked_dates           = array();
		$existing_timeslots_arr = json_decode( get_option( 'orddd_delivery_time_slot_log' ) );
		$time_format_to_show    = orddd_common::orddd_get_time_format();
		$delivery_days          = array();
		$specific_dates         = array();
		$lockout_arr            = array();

		foreach ( $existing_timeslots_arr as $k => $v ) {
			$from_time = date( $time_format_to_show, strtotime( $v->fh . ':' . trim( $v->fm, ' ' ) ) );
			$to_time   = date( $time_format_to_show, strtotime( $v->th . ':' . trim( $v->tm, ' ' ) ) );
			if ( $v->th != '' && $v->th != '00' && $v->tm != '' && $v->tm != '00' ) {
				$timeslot = $from_time . ' - ' . $to_time;
			} else {
				$timeslot = $from_time;
			}
			$dd = json_decode( $v->dd );

			if ( is_array( $dd ) && count( $dd ) > 0 ) {
				foreach ( $dd as $dkey => $dval ) {
					if ( 'specific_dates' == $v->tv ) {
						$lockout_date_split = explode( '-', $dval );
						$date_lockout       = $lockout_date_split[0] . '-' . $lockout_date_split[1] . '-' . $lockout_date_split[2];
						if ( isset( $specific_dates[ $date_lockout ] ) ) {
							$specific_dates[ $date_lockout ][ $timeslot ] = $v->lockout;
						} else {
							$specific_dates[ $date_lockout ] = array( $timeslot => $v->lockout );
						}
					} else {
						if ( isset( $delivery_days[ $dval ] ) ) {
							$delivery_days[ $dval ][ $timeslot ] = $v->lockout;
						} else {
							$delivery_days[ $dval ] = array( $timeslot => $v->lockout );
						}
					}
				}
			}
		}

		$lockout_arr = orddd_common::orddd_get_disabled_timeslot();

		// For time slot lockout the date format saved in the database is j-n-Y. And the date format we add in the booked days array is n-j-Y.
		foreach ( $lockout_arr as $date => $timeslot ) {

			if ( strpos( $date, 'orddd_weekday' ) !== false ) {
				continue;
			}
			$lockout_date_split = explode( '-', $date );
			$date_lockout       = $lockout_date_split[1] . '-' . $lockout_date_split[0] . '-' . $lockout_date_split[2];
			$count 				= 0;
			$specific_date 		= date( 'n-j-Y', strtotime( $date ) );
			$weekday 	   		= date( 'w', strtotime( $date ) );

			foreach ( $timeslot as $key => $time ) {
				if ( is_array( $specific_dates ) && count( $specific_dates ) > 0 && get_option( 'orddd_enable_specific_delivery_dates' ) == 'on' ) {
					if ( isset( $specific_dates[ $specific_date ] ) && is_array( $lockout_arr[ $date ] ) ) {
						if ( isset( $specific_dates[ $specific_date ][ $time ] ) ) {
							$count++;
						}
						continue;
					}
				}

				if ( is_array( $delivery_days ) && count( $delivery_days ) > 0 ) {
					if ( isset( $delivery_days[ 'orddd_weekday_' . $weekday ] ) && is_array( $lockout_arr[ $date ] ) && isset( $delivery_days[ 'orddd_weekday_' . $weekday ][ $time ] ) ) {
						$count++;
					}
				}
			}

			if ( isset( $specific_dates[ $specific_date ] ) && count( $specific_dates[ $specific_date ] ) === $count ) {
				array_push( $booked_dates, $date );
			} elseif ( isset( $delivery_days[ 'orddd_weekday_' . $weekday ] ) && is_array( $delivery_days[ 'orddd_weekday_' . $weekday ] ) && count( $delivery_days[ 'orddd_weekday_' . $weekday ] ) === $count ) {
				array_push( $booked_dates, $date );
			}
		}

		return $booked_dates;
	}

	/**
	 * Get the dates for which all the timeslots have been blocked for custom settings.
	 *
	 * @param array $shipping_settings Shipping settings.
	 * @return array
	 */
	public static function orddd_get_custom_blocked_timeslots( $shipping_settings ) {
		$current_time        = self::orddd_get_current_date_time();
		$current_date        = date( 'j-n-Y', $current_time );
		$booked_dates        = array();
		$lockout_arr         = array();
		$delivery_days       = array();
		$specific_dates      = array();
		$time_slots          = explode( '},', $shipping_settings['time_slots'] );
		$time_format_to_show = orddd_common::orddd_get_time_format();

		foreach ( $time_slots as $key => $value ) {
			if ( '' !== $value ) {
				$timeslot_values = orddd_common::get_timeslot_values( $value );
				$timeslot        = $timeslot_values['time_slot'];

				$time_slot_arr      = explode( ' - ', $timeslot_values['time_slot'] );
				$from_time_hour_arr = explode( ':', $time_slot_arr[0] );
				// Convert the time slot in the selected time format.
				$from_time     = date( $time_format_to_show, strtotime( trim( $time_slot_arr[0] ) ) );
				$from_time_arr = explode( ':', $from_time );
				if ( isset( $time_slot_arr[1] ) ) {
					$to_time  = date( $time_format_to_show, strtotime( trim( $time_slot_arr[1] ) ) );
					$timeslot = $from_time . ' - ' . $to_time;
				} else {
					$timeslot = $from_time;
				}

				$lockout = '';
				if ( 'specific_dates' == $timeslot_values['delivery_days_selected'] ) {
					foreach ( $timeslot_values['selected_days'] as $dkey => $dval ) {
						if ( $timeslot_values['lockout'] != '' && $timeslot_values['lockout'] != '0' ) {
							$lockout = $timeslot_values['lockout'];
						} elseif ( get_option( 'orddd_global_lockout_time_slots' ) != '0' && get_option( 'orddd_global_lockout_time_slots' ) != '' ) {
							$lockout = get_option( 'orddd_global_lockout_time_slots' );
						} else {
							$lockout = '';
						}

						if ( isset( $specific_dates[ $dval ] ) ) {
							$specific_dates[ $dval ][ $timeslot ] = $lockout;
						} else {
							$specific_dates[ $dval ] = array( $timeslot => $lockout );
						}
					}
				} else {
					foreach ( $timeslot_values['selected_days'] as $dkey => $dval ) {
						if ( $timeslot_values['lockout'] != '' && $timeslot_values['lockout'] != '0' ) {
							$lockout = $timeslot_values['lockout'];
						} elseif ( get_option( 'orddd_global_lockout_time_slots' ) != '0' && get_option( 'orddd_global_lockout_time_slots' ) != '' ) {
							$lockout = get_option( 'orddd_global_lockout_time_slots' );
						} else {
							$lockout = '';
						}

						if ( isset( $delivery_days[ $dval ] ) ) {
							$delivery_days[ $dval ][ $timeslot ] = $lockout;
						} else {
							$delivery_days[ $dval ] = array( $timeslot => $lockout );
						}
					}
				}
			}
		}

		$lockout_arr    = orddd_common::orddd_get_disabled_timeslot();
		// For time slot lockout the date format saved in the database is j-n-Y. And the date format we add in the booked days array is n-j-Y.
		foreach ( $lockout_arr as $date => $timeslot ) {
			$count 		   = 0;
			$weekday 	   = '';
			$specific_date = '';
			if( false !== strpos( $date, 'orddd_weekday') ) {
				$weekday = $date;
			} else {
				$specific_date = date( 'n-j-Y', strtotime( $date ) );
			}

			foreach ( $timeslot as $key => $time ) {
				if ( isset( $shipping_settings['delivery_type']['specific_dates'] ) && 'on' === $shipping_settings['delivery_type']['specific_dates'] && isset( $specific_dates[ $specific_date ] ) && is_array( $lockout_arr[ $date ] ) ) {
					if ( isset( $specific_dates[ $specific_date ][ $time ] ) ) {
						$count++;
					}
					continue;
				}

				if ( is_array( $delivery_days ) && count( $delivery_days ) > 0 ) {
					if ( isset( $delivery_days[ 'orddd_weekday_' . $weekday . '_custom_setting' ] ) && is_array( $lockout_arr[ $date ] ) && isset( $delivery_days[ 'orddd_weekday_' . $weekday . '_custom_setting' ][ $time ] ) ) {
						$count++;
					}
				}
			}

			if ( isset( $specific_dates[ $specific_date ] ) && count( $specific_dates[ $specific_date ] ) === $count ) {
				array_push( $booked_dates, $date );
			} elseif ( isset( $delivery_days[ 'orddd_weekday_' . $weekday . '_custom_setting' ] ) && is_array( $delivery_days[ 'orddd_weekday_' . $weekday . '_custom_setting' ] ) && count( $delivery_days[ 'orddd_weekday_' . $weekday . '_custom_setting' ] ) === $count ) {
				array_push( $booked_dates, $date );
			}
		}

		return $booked_dates;
	}
}

$orddd_lockout_days = new ORDDD_Lockout_Days();

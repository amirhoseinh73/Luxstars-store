<?php
/**
 * Order Delivery Date Pro for WooCommerce
 *
 * Availability Widget added to show the available delivery dates in the calendar on the frontend.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Pro-for-WooCommerce/Frontend/Widgets
 * @since       8.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class for adding availability widget on the frontend
 *
 * @class orddd_widget
 */

class orddd_widget {

	/**
	 * Default Constructor
	 *
	 * @since 8.6
	 */
	public function __construct() {
		// Register and load the widget
		add_action( 'widgets_init', array( &$this, 'orddd_load_widget' ) );
		add_action( 'wc_ajax_orddd_show_availability_calendar', array( &$this, 'orddd_show_availability_calendar' ), 10, 1 );
	}

	/**
	 * Registers the Availability Widget
	 *
	 * @hook orddd_load_widget
	 * @since 8.6
	 */
	public function orddd_load_widget() {
		register_widget( 'orddd_availability_widget' );
	}

	/**
	 * Updates the availability of the dates based on the postcode when the Show availability button is clicked.
	 *
	 * @hook wp_ajax_nopriv_orddd_show_availability_calendar
	 * @hook wp_ajax_orddd_show_availability_calendar
	 * @since 8.6
	 */
	public function orddd_show_availability_calendar() { 
		global $wpdb;

		$postcode 		 = isset( $_POST['billing_postcode'] ) ? $_POST['billing_postcode']: '';
		$method_id		 = '';
		$instance_id	 = '';
		$default_country = get_option( 'woocommerce_default_country' );
		if( strpos( $default_country, ':' ) !== false ) {
			$default_country_arr = explode( ":", $default_country );
			$country = $default_country_arr[0];
		} else {
			$country = $default_country;
		}
		$state						= '';
		$matching_zone_id           = orddd_common::orddd_get_matching_zone_id( $postcode, $country, $state, '' );
		$zone_shipping_methods = "SELECT instance_id, method_id FROM {$wpdb->prefix}woocommerce_shipping_zone_methods WHERE zone_id = '{$matching_zone_id}' AND method_order IN( SELECT MIN(method_order) FROM {$wpdb->prefix}woocommerce_shipping_zone_methods WHERE zone_id = '{$matching_zone_id}' )";

		$results = $wpdb->get_results( $zone_shipping_methods );

		if ( isset( $results[0] ) ){
			$method_id 		= $results[0]->method_id;
			$instance_id 	= $results[0]->instance_id;
		}

		$zone_str 					= orddd_common::orddd_get_shipping_id( $matching_zone_id, $method_id, $instance_id );
		$zone_array					= explode( "-", $zone_str );
		$shipping_method            = $zone_array[1];
		$partially_booked_dates_str = self::get_partially_booked_dates( $shipping_method );
		echo $shipping_method . '&' . $partially_booked_dates_str;
		die();
	}

	/**
	 * Returns the availability of the dates.
	 * Dates will be returned as partially booked dates if one or more orders are placed.
	 * Else, it will be returned as Fully Available date.
	 *
	 * If the lockout is set to zero or blank, on hover of date, 'Unlimited' will be shown.
	 * Else, the remaining orders for that date. i.e.
	 *
	 * @since 8.6
	 */
	public static function get_partially_booked_dates( $shipping_method, $shipping_settings = array() ) {
		global $wpdb;

		$gmt = false;
		if ( has_filter( 'orddd_gmt_calculations' ) ) {
			$gmt = apply_filters( 'orddd_gmt_calculations', '' );
		}
		$current_time = current_time( 'timestamp', $gmt );
		$current_date = date( 'j-n-Y', $current_time );

		$time_format_to_show        = orddd_common::orddd_get_time_format();
		$available_deliveries       = '';
		$partially_lockout_dates    = '';
		$shipping_settings_to_check = array();
		$is_custom_enabled          = 'no';
		if ( get_option( 'orddd_enable_shipping_based_delivery' ) == 'on' ) {

			// Fetch Custom Delivery Settings for which dates should be checked for partially booked dates.
			if ( '' != $shipping_method ) {
				$results = orddd_common::orddd_get_shipping_settings();
				if ( is_array( $results ) && count( $results ) > 0 && $shipping_method != '' ) {
					foreach ( $results as $key => $value ) {
						$shipping_methods  = array();
						$shipping_settings = get_option( $value->option_name );
						if ( isset( $shipping_settings['delivery_settings_based_on'][0] ) &&
							$shipping_settings['delivery_settings_based_on'][0] == 'shipping_methods' ) {
							if ( isset( $shipping_settings['shipping_methods'] ) && in_array( $shipping_method, $shipping_settings['shipping_methods'] ) ) {
								$shipping_settings_to_check = $shipping_settings;
							}
						}
					}
				}
			} elseif ( is_array( $shipping_settings ) && count( $shipping_settings ) > 0 ) {
				$shipping_settings_to_check = $shipping_settings;
			}

			if ( is_array( $shipping_settings_to_check ) && count( $shipping_settings_to_check ) > 0 ) {

				if ( isset( $shipping_settings_to_check['delivery_type']['specific_dates'] ) && 'on' == $shipping_settings_to_check['delivery_type']['specific_dates'] ) {
					$delivery_dates = explode( ',', $shipping_settings_to_check['specific_dates'] );
					foreach ( $delivery_dates as $key => $value ) {
						if ( $value != '' ) {
							$sv                = str_replace( '}', '', $value );
							$sv                = str_replace( '{', '', $sv );
							$specific_date_arr = explode( ':', $sv );

							if ( isset( $specific_date_arr[3] ) && '' != $specific_date_arr[3] ) {
								$available_deliveries .= "'" . $specific_date_arr[0] . '>' . __( 'Available Deliveries: ', 'order-delivery-date' ) . $specific_date_arr[3] . "',";
							} elseif ( '' === $specific_date_arr[3] && 'on' === get_option( 'orddd_global_lockout_custom' ) ) {
								$date_lockout          = get_option( 'orddd_lockout_date_after_orders' );
								$available_deliveries .= "'>" . __( 'Available Deliveries: ', 'order-delivery-date' ) . $date_lockout . "',";
							} else {
								$available_deliveries .= "'" . $specific_date_arr[0] . '>' . __( 'Available Deliveries: ', 'order-delivery-date' ) . __( 'Unlimited', 'order-delivery-date' ) . "',";
							}
						}
					}

					$lockout_specific_days = ORDDD_Lockout_Days::orddd_get_custom_specific_dates_availability( $shipping_settings_to_check );
					foreach ( $lockout_specific_days as $date => $available_lockout ) {
						if ( $available_lockout > 0 ) {
							$partially_lockout_dates .= "'" . $date . '>' . __( 'Available Deliveries: ', 'order-delivery-date' ) . ( $available_lockout ) . "',";

							$available_deliveries .= "'" . $date . '>' . __( 'Available Deliveries: ', 'order-delivery-date' ) . $available_lockout . "',";
						} elseif ( '' === $available_lockout ) {
							$partially_lockout_dates .= "'" . $date . '>' . __( 'Available Deliveries: ', 'order-delivery-date' ) . __( 'Unlimited', 'order-delivery-date' ) . "',";

							$available_deliveries .= "'" . $date . '>' . __( 'Available Deliveries: ', 'order-delivery-date' ) . __( 'Unlimited', 'order-delivery-date' ) . "',";
						}
					}
				}

				if ( isset( $shipping_settings_to_check['date_lockout'] ) ) {
					$lockout_days = ORDDD_Lockout_Days::orddd_get_custom_weekdays_availability( $shipping_settings_to_check );
					foreach ( $lockout_days as $date => $available_lockout ) {
						if ( $available_lockout > 0 ) {
							$partially_lockout_dates .= "'" . $date . '>' . __( 'Available Deliveries: ', 'order-delivery-date' ) . $available_lockout . "',";

							$available_deliveries .= "'" . $date . '>' . __( 'Available Deliveries: ', 'order-delivery-date' ) . $available_lockout . "',";
						} elseif ( '' === $available_lockout ) {
							$partially_lockout_dates .= "'" . $date . '>' . __( 'Available Deliveries: ', 'order-delivery-date' ) . __( 'Unlimited', 'order-delivery-date' ) . "',";

							$available_deliveries .= "'" . $date . '>' . __( 'Available Deliveries: ', 'order-delivery-date' ) . __( 'Unlimited', 'order-delivery-date' ) . "',";
						}
					}
				}

				if ( isset( $shipping_settings_to_check['date_lockout'] ) && $shipping_settings_to_check['date_lockout'] > 0 && $shipping_settings_to_check['date_lockout'] != '' ) {
					$date_lockout          = $shipping_settings_to_check['date_lockout'];
					$available_deliveries .= "'>" . __( 'Available Deliveries: ', 'order-delivery-date' ) . $date_lockout . "',";
				} elseif ( isset( $shipping_settings_to_check['date_lockout'] ) && '' == $shipping_settings_to_check['date_lockout'] && 'on' == get_option( 'orddd_global_lockout_custom' ) ) {
					$date_lockout          = get_option( 'orddd_lockout_date_after_orders' );
					$available_deliveries .= "'>" . __( 'Available Deliveries: ', 'order-delivery-date' ) . $date_lockout . "',";
				} else {
					$available_deliveries .= "'>" . __( 'Available Deliveries: ', 'order-delivery-date' ) . __( 'Unlimited', 'order-delivery-date' ) . "',";
				}
				// Partially booked dates and available dates for the time sots of Custom Delivery Settings.
				if ( isset( $shipping_settings_to_check['time_slots'] ) && $shipping_settings_to_check['time_slots'] != '' ) {

					// Get Minimum Delivery Time Data.
					$minimum_delivery_time = 0;
					if ( isset( $shipping_settings_to_check['minimum_delivery_time'] ) ) {
						$minimum_delivery_time = $shipping_settings_to_check['minimum_delivery_time'];
						if ( '' == $minimum_delivery_time ) {
							$minimum_delivery_time = 0;
						}
					}

					$delivery_time_seconds = $minimum_delivery_time * 60 * 60;
					$holidays_str          = orddd_common::orddd_get_custom_holidays( $shipping_settings_to_check );
					$lockout_days_str      = orddd_common::orddd_get_custom_lockout_days( $shipping_settings_to_check );

					// Time Settings.
					$time_slider_enabled = '';
					$from_hours          = '';
					$from_mins           = '';
					$to_hours            = '';
					$to_mins             = '';
					if ( isset( $shipping_settings_to_check['time_settings'] ) ) {
						$time_settings = $shipping_settings_to_check['time_settings'];
						if ( isset( $time_settings['from_hours'] ) && $time_settings['from_hours'] != 0
							&& isset( $time_settings['to_hours'] ) && $time_settings['to_hours'] != 0 ) {
							$from_hour_values = orddd_common::orddd_get_shipping_from_time( $time_settings, $shipping_settings_to_check, $holidays_str, $lockout_days_str );
							if ( is_array( $from_hour_values ) && count( $from_hour_values ) ) {
								$from_hours = $from_hour_values['from_hours'];
								$from_mins  = $from_hour_values['from_minutes'];
							}

							$to_hours = $time_settings['to_hours'];
							$to_mins  = $time_settings['to_mins'];

							$time_slider_enabled = 'on';
						}
					}

					// Fetch the first available date after calculating the minimum delivery time.
					// Time is used to check for the available timeslots for that date.
					$min_date_array = orddd_common::get_min_date(
						$delivery_time_seconds,
						array(
							'enabled'    => $time_slider_enabled,
							'from_hours' => $from_hours,
							'from_mins'  => $from_mins,
							'to_hours'   => $to_hours,
							'to_mins'    => $to_mins,
						),
						$holidays_str,
						$lockout_days_str,
						$shipping_settings
					);

					$lockout_arr      = array();
					$lockout_time_arr = array();
					$date             = array();
					$previous_orders  = 0;
					$specific_dates   = array();
					$delivery_days    = array();

					$time_slots = explode( '},', $shipping_settings_to_check['time_slots'] );
					// Sort the multidimensional array.
					usort( $time_slots, array( 'orddd_common', 'orddd_custom_sort' ) );
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

							if ( 'specific_dates' == $timeslot_values['delivery_days_selected'] ) {
								foreach ( $timeslot_values['selected_days'] as $dkey => $dval ) {
									if ( $timeslot_values['lockout'] != '' && $timeslot_values['lockout'] != '0' ) {
										$specific_dates[ $dval ][ $timeslot ] = $timeslot_values['lockout'];
									} elseif ( get_option( 'orddd_global_lockout_time_slots' ) != '0' && get_option( 'orddd_global_lockout_time_slots' ) != '' ) {
										$specific_dates[ $dval ][ $timeslot ] = get_option( 'orddd_global_lockout_time_slots' );
									} else {
										$specific_dates[ $dval ][ $timeslot ] = '';
									}
								}
							} else {
								foreach ( $timeslot_values['selected_days'] as $dkey => $dval ) {
									if ( $timeslot_values['lockout'] != '' && $timeslot_values['lockout'] != '0' ) {
										$delivery_days[ $dval ][ $timeslot ] = $timeslot_values['lockout'];
									} elseif ( get_option( 'orddd_global_lockout_time_slots' ) != '0' && '' !== get_option( 'orddd_global_lockout_time_slots' ) ) {
										$delivery_days[ $dval ][ $timeslot ] = get_option( 'orddd_global_lockout_time_slots' );
									} else {
										$delivery_days[ $dval ][ $timeslot ] = '';
									}
								}
							}
						}
					}

					$timeslot_lockout_dates = ORDDD_Lockout_Days::orddd_get_custom_timeslot_availability( $shipping_settings_to_check );

					$partially_lockout_dates = "'available_slots>" . __( 'Available Delivery Slots', 'order-delivery-date' ) . "--',";
					$available_deliveries    = "'available_slots>" . __( 'Available Delivery Slots', 'order-delivery-date' ) . "',";

					// For time slot lockout the date format saved in the database is j-n-Y. 
					foreach ( $timeslot_lockout_dates as $date => $value ) {
						$available_timeslot_deliveries = '';
						$lockout_date_arr              = explode( '-', $date );
						$lockout_date                  = $lockout_date_arr[1] . '-' . $lockout_date_arr[0] . '-' . $lockout_date_arr[2];
						$date_lockout_time             = strtotime( $date );

						if ( $date_lockout_time >= strtotime( $current_date ) ) {
							if ( is_array( $specific_dates ) && count( $specific_dates ) > 0 ) {
								if ( isset( $specific_dates[ $lockout_date ] ) ) {
									$time_slots = $specific_dates[ $lockout_date ];
									foreach ( $time_slots as $time => $lockout ) {
										$is_past_timeslot = self::orddd_is_past_timeslot( $time, $delivery_time_seconds, $min_date_array );
										if ( $date == $current_date && $is_past_timeslot ) {
											continue;
										}
										if ( $lockout == 0 || '' == $lockout ) {
											$available_timeslot_deliveries .= $time . ': ' . __( 'Unlimited', '   order-delivery-date' ) . '--';
										} else {
											if ( isset( $timeslot_lockout_dates[ $date ][ $time ] ) ) {
												$available_lockout = $timeslot_lockout_dates[ $date ][ $time ];
												if ( $available_lockout > 0 ) {
													$available_timeslot_deliveries .= $time . ': ' . $available_lockout . '--';
													$available_deliveries          .= "'" . $lockout_date . '>' . $time . ': ' . $available_lockout . "',";
												}
											} else {
												$available_timeslot_deliveries .= $time . ': ' . $lockout . '--';
												$available_deliveries          .= "'" . $lockout_date . '>' . $time . ': ' . $lockout . "',";
											}
										}
									}

									$partially_lockout_dates .= "'" . $lockout_date . '>' . $available_timeslot_deliveries . "',";
								}
							}

							if ( is_array( $delivery_days ) && count( $delivery_days ) > 0 ) {

								$weekday = date( 'w', $date_lockout_time );

								if ( isset( $specific_dates[ $date ] ) ) {
									continue;
								}

								if ( isset( $delivery_days[ 'orddd_weekday_' . $weekday . '_custom_setting' ] ) ) {
									$time_slots = $delivery_days[ 'orddd_weekday_' . $weekday . '_custom_setting' ];
								} elseif ( isset( $delivery_days['all'] ) ) {
									$time_slots = $delivery_days['all'];
								}

								foreach ( $time_slots as $time => $lockout ) {
									$is_past_timeslot = self::orddd_is_past_timeslot( $time, $delivery_time_seconds, $min_date_array );
									if ( $date == $current_date && $is_past_timeslot ) {
										continue;
									}
									if ( $lockout == '0' || '' == $lockout ) {
										$available_timeslot_deliveries .= $time . ': ' . __( 'Unlimited', 'order-delivery-date' ) . '--';
										$available_deliveries          .= "'" . $lockout_date . '>' . $time . ': ' . __( 'Unlimited', 'order-delivery-date' ) . "',";
									} else {
										if ( isset( $timeslot_lockout_dates[ $date ][ $time ] ) ) {
											$available_lockout = $timeslot_lockout_dates[ $date ][ $time ];
											if ( $available_lockout > 0 ) {
												$available_timeslot_deliveries .= $time . ': ' . $available_lockout . '--';
												$available_deliveries          .= "'" . $lockout_date . '>' . $time . ': ' . $available_lockout . "',";
											}
										} else {
											$available_timeslot_deliveries .= $time . ': ' . $lockout . '--';
											$available_deliveries          .= "'" . $lockout_date . '>' . $time . ': ' . $lockout . "',";
										}
									}
								}

								$partially_lockout_dates .= "'" . $lockout_date . '>' . $available_timeslot_deliveries . "',";
							}
						}
					}

					if ( is_array( $specific_dates ) && count( $specific_dates ) > 0 ) {
						foreach ( $specific_dates as $del_days_key => $del_days_val ) {
							$time_slots = $specific_dates[ $del_days_key ];
							foreach ( $time_slots as $time => $lockout ) {
								if ( '' == $lockout || '0' == $lockout ) {
									$available_deliveries .= "'" . $del_days_key . '>' . $time . ': ' . __( 'Unlimited', '   order-delivery-date' ) . "',";
								} else {
									$available_deliveries .= "'" . $del_days_key . '>' . $time . ': ' . $lockout . "',";
								}
							}
						}
					}

					if ( is_array( $delivery_days ) && count( $delivery_days ) > 0 ) {
						foreach ( $delivery_days as $del_days_key => $del_days_val ) {
							$time_slots = $delivery_days[ $del_days_key ];
							foreach ( $time_slots as $time => $lockout ) {
								if ( '' == $lockout || '0' == $lockout ) {
									$available_deliveries .= "'" . $del_days_key . '>' . $time . ': ' . __( 'Unlimited', '   order-delivery-date' ) . "',";
								} else {
									$available_deliveries .= "'" . $del_days_key . '>' . $time . ': ' . $lockout . "',";
								}
							}
						}
					}
				}
				$is_custom_enabled = 'yes';
			}
		}

		// change the condition for the global settings
		if ( 'no' == $is_custom_enabled ) {
			$date_lockout = get_option( 'orddd_lockout_date_after_orders' );

			if ( 'on' == get_option( 'orddd_enable_specific_delivery_dates' ) ) {
				$delivery_dates = get_option( 'orddd_delivery_dates' );
				$delivery_days  = array();
				if ( $delivery_dates != '' && $delivery_dates != '{}' && $delivery_dates != '[]' && $delivery_dates != 'null' ) {
					$delivery_days = json_decode( $delivery_dates );
				}

				$lockout_specific_days = ORDDD_Lockout_Days::orddd_get_specific_dates_availability();
				foreach ( $lockout_specific_days as $date => $available_lockout ) {
					if ( $available_lockout > 0 ) {
						$partially_lockout_dates .= "'" . $date . '>' . __( 'Available Deliveries: ', 'order-delivery-date' ) . ( $available_lockout ) . "',";

						$available_deliveries .= "'" . $date . '>' . __( 'Available Deliveries: ', 'order-delivery-date' ) . $available_lockout . "',";
					} elseif ( '' === $available_lockout ) {
						$partially_lockout_dates .= "'" . $date . '>' . __( 'Available Deliveries: ', 'order-delivery-date' ) . __( 'Unlimited', 'order-delivery-date' ) . "',";

						$available_deliveries .= "'" . $date . '>' . __( 'Available Deliveries: ', 'order-delivery-date' ) . __( 'Unlimited', 'order-delivery-date' ) . "',";
					}
				}

				foreach ( $delivery_days as $key => $value ) {
					if ( isset( $value->max_orders ) && '' != $value->max_orders ) {
						$available_deliveries .= "'" . $value->date . '>' . __( 'Available Deliveries: ', 'order-delivery-date' ) . $value->max_orders . "',";
					} elseif ( '' == $value->max_orders ) {
						$available_deliveries .= "'" . $value->date . '>' . __( 'Available Deliveries: ', 'order-delivery-date' ) . __( 'Unlimited', 'order-delivery-date' ) . "',";
					}
				}
			}

			if ( $date_lockout > 0 ) {
				$lockout_days = ORDDD_Lockout_Days::orddd_weekdays_availability();
				foreach ( $lockout_days as $date => $available_lockout ) {
					if ( $available_lockout > 0 ) {
						$partially_lockout_dates .= "'" . $date . '>' . __( 'Available Deliveries: ', 'order-delivery-date' ) . $available_lockout . "',";

						$available_deliveries .= "'" . $date . '>' . __( 'Available Deliveries: ', 'order-delivery-date' ) . $available_lockout . "',";
					} elseif ( '' === $available_lockout ) {
						$partially_lockout_dates .= "'" . $date . '>' . __( 'Available Deliveries: ', 'order-delivery-date' ) . __( 'Unlimited', 'order-delivery-date' ) . "',";

						$available_deliveries .= "'" . $date . '>' . __( 'Available Deliveries: ', 'order-delivery-date' ) . __( 'Unlimited', 'order-delivery-date' ) . "',";
					}
				}
			}

			if ( $date_lockout > 0 && $date_lockout != '' ) {
				$available_deliveries .= "'>" . __( 'Available Deliveries: ', 'order-delivery-date' ) . $date_lockout . "',";
			} else {
				$available_deliveries .= "'>" . __( 'Available Deliveries: ', 'order-delivery-date' ) . __( 'Unlimited', 'order-delivery-date' ) . "',";
			}

			// Partially booked dates for Time slots.
			if ( get_option( 'orddd_enable_time_slot' ) == 'on' ) {
				// Check for Minimum Delivery Time to display the time slots.
				$minimum_delivery_time = get_option( 'orddd_minimumOrderDays' );
				if ( '' == $minimum_delivery_time ) {
					$minimum_delivery_time = 0;
				}

				$timeslot_lockout_dates = ORDDD_Lockout_Days::orddd_get_timeslot_availability();

				$delivery_time_seconds = $minimum_delivery_time * 60 * 60;
				$holidays_str          = wp_cache_get( 'orddd_general_delivery_date_holidays' );
				$lockout_days_str      = wp_cache_get( 'orddd_general_lockout_days_str' );

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

				$date                  = array();
				$lockout_timeslots_arr = array();
				$previous_orders       = 0;

				$specific_dates   = array();
				$delivery_days    = array();
				$previous_lockout = 0;

				$existing_timeslots_arr = json_decode( get_option( 'orddd_delivery_time_slot_log' ) );
				usort( $existing_timeslots_arr, array( 'orddd_common', 'orddd_custom_sort' ) );

				$partially_lockout_dates = "'available_slots>" . __( 'Available Delivery Slots', 'order-delivery-date' ) . "--',";
				$available_deliveries    = "'available_slots>" . __( 'Available Delivery Slots', 'order-delivery-date' ) . "',";

				$delivery_days  = array();
				$specific_dates = array();

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
								$lockout_date_split                           = explode( '-', $dval );
								$date_lockout                                 = $lockout_date_split[0] . '-' . $lockout_date_split[1] . '-' . $lockout_date_split[2];
								$specific_dates[ $date_lockout ][ $timeslot ] = $v->lockout;
							} else {
								$delivery_days[ $dval ][ $timeslot ] = $v->lockout;
							}
						}
					}
				}

				foreach ( $timeslot_lockout_dates as $date => $value ) {
					$available_timeslot_deliveries = '';
					$lockout_date_arr              = explode( '-', $date );
					$lockout_date                  = $lockout_date_arr[1] . '-' . $lockout_date_arr[0] . '-' . $lockout_date_arr[2];
					$date_lockout_time             = strtotime( $date );
					$time_slots = array();

					if ( $date_lockout_time >= strtotime( $current_date ) ) {
						if ( is_array( $specific_dates ) && count( $specific_dates ) > 0 ) {
							if ( isset( $specific_dates[ $lockout_date ] ) ) {
								$time_slots = $specific_dates[ $lockout_date ];
								foreach ( $time_slots as $time => $lockout ) {
									$is_past_timeslot = self::orddd_is_past_timeslot( $time, $delivery_time_seconds, $min_date_array );
									if ( $date == $current_date && $is_past_timeslot ) {
										continue;
									}
									if ( $lockout == 0 ) {
										$available_timeslot_deliveries .= $time . ': ' . __( 'Unlimited', '   order-delivery-date' ) . '--';
									} else {
										if ( isset( $timeslot_lockout_dates[ $date ][ $time ] ) ) {
											$available_lockout = $timeslot_lockout_dates[ $date ][ $time ];
											if ( $available_lockout > 0 ) {
												$available_timeslot_deliveries .= $time . ': ' . $available_lockout . '--';
												$available_deliveries          .= "'" . $lockout_date . '>' . $time . ': ' . $available_lockout . "',";
											}
										} else {
											$available_timeslot_deliveries .= $time . ': ' . $lockout . '--';
											$available_deliveries          .= "'" . $lockout_date . '>' . $time . ': ' . $lockout . "',";

										}
									}
								}
							}
						}

						if ( is_array( $delivery_days ) && count( $delivery_days ) > 0 ) {
							$weekday = date( 'w', $date_lockout_time );
							if ( isset( $delivery_days[ 'orddd_weekday_' . $weekday ] ) ) {
								$time_slots = $delivery_days[ 'orddd_weekday_' . $weekday ];
							}
							
							if ( isset( $delivery_days['all'] ) ) {
								foreach( $delivery_days['all'] as $k => $v ) {
									$time_slots[ $k ] = $v;
								}
							}
							
							foreach ( $time_slots as $time => $lockout ) {
								$is_past_timeslot = self::orddd_is_past_timeslot( $time, $delivery_time_seconds, $min_date_array );
								if ( ( $date == $current_date && $is_past_timeslot ) || isset( $specific_dates[ $lockout_date ] ) ) {
									continue;
								}
								if ( $lockout == 0 ) {
									$available_timeslot_deliveries .= $time . ': ' . __( 'Unlimited', 'order-delivery-date' ) . '--';
									$available_deliveries          .= "'" . $lockout_date . '>' . $time . ': ' . __( 'Unlimited', 'order-delivery-date' ) . "',";
								} else {
									if ( isset( $timeslot_lockout_dates[ $date ][ $time ] ) ) {
										$available_lockout = $timeslot_lockout_dates[ $date ][ $time ];
										if ( $available_lockout > 0 ) {
											$available_timeslot_deliveries .= $time . ': ' . $available_lockout . '--';
											$available_deliveries          .= "'" . $lockout_date . '>' . $time . ': ' . $available_lockout . "',";
										}
									} else {
										$available_timeslot_deliveries .= $time . ': ' . $lockout . '--';
										$available_deliveries          .= "'" . $lockout_date . '>' . $time . ': ' . $lockout . "',";

									}
								}
							}
						}
					}

					$partially_lockout_dates .= "'" . $lockout_date . '>' . $available_timeslot_deliveries . "',";
				}

				if ( is_array( $specific_dates ) && count( $specific_dates ) > 0 ) {
					foreach ( $specific_dates as $del_days_key => $del_days_val ) {
						$time_slots 		= $specific_dates[ $del_days_key ];
						$lockout_date_split = explode( '-', $del_days_key );
						$date_lockout       = $lockout_date_split[1] . '-' . $lockout_date_split[0] . '-' . $lockout_date_split[2];
						if ( isset( $timeslot_lockout_dates[ $date_lockout ] ) ) {
							continue;
						}
						foreach ( $time_slots as $time => $lockout ) {
							if ( '' == $lockout || '0' == $lockout ) {
								$available_deliveries .= "'" . $del_days_key . '>' . $time . ': ' . __( 'Unlimited', '   order-delivery-date' ) . "',";
							} else {
								$available_deliveries .= "'" . $del_days_key . '>' . $time . ': ' . $lockout . "',";
							}
						}
					}
				}

				if ( is_array( $delivery_days ) && count( $delivery_days ) > 0 ) {
					foreach ( $delivery_days as $del_days_key => $del_days_val ) {
						$time_slots = $delivery_days[ $del_days_key ];
						foreach ( $time_slots as $time => $lockout ) {
							if ( '' == $lockout || '0' == $lockout ) {
								$available_deliveries .= "'" . $del_days_key . '>' . $time . ': ' . __( 'Unlimited', '   order-delivery-date' ) . "',";
							} else {
								$available_deliveries .= "'" . $del_days_key . '>' . $time . ': ' . $lockout . "',";
							}
						}
					}
				}
			}
		}
		$partially_lockout_dates  = trim( $partially_lockout_dates, ',' );
		$partially_lockout_dates .= '&' . $available_deliveries;
		return $partially_lockout_dates;
	}

	/**
	 * Check if a time slot has passed for the current date.
	 *
	 * @param string $timeslot Time slot to check.
	 * @param int    $delivery_time_seconds Minimum delivery time in seconds.
	 * @param array  $min_date_array Min Date & time.
	 */
	public static function orddd_is_past_timeslot( $timeslot, $delivery_time_seconds, $min_date_array ) {
		$gmt = false;
		if ( has_filter( 'orddd_gmt_calculations' ) ) {
			$gmt = apply_filters( 'orddd_gmt_calculations', '' );
		}
		$current_time = current_time( 'timestamp', $gmt );
		$current_date = date( 'j-n-Y', $current_time );
		$timeslot 	  = orddd_common::orddd_change_time_slot_format( $timeslot );

		$time_slot_arr       = explode( ' - ', $timeslot );
		$from_time_hour_arr  = explode( ':', $time_slot_arr[0] );

		// Convert the time slot in the selected time format.
		$from_time     = date( 'G:i', strtotime( trim( $time_slot_arr[0] ) ) );
		$from_time_arr = explode( ':', $from_time );
		if ( isset( $time_slot_arr[1] ) ) {
			$to_time  = date( 'G:i', strtotime( trim( $time_slot_arr[1] ) ) );
			$timeslot = $from_time . ' - ' . $to_time;
		} else {
			$timeslot = $from_time;
		}

		// Hide the past timeslots for the current date.
		$min_time_on_last_slot = apply_filters( 'orddd_min_delivery_on_last_slot', false );
		if ( $delivery_time_seconds == 0 ) {
			$last_slot = date( 'G:i', current_time( 'timestamp' ) );
		} else {
			$last_slot = $min_date_array['min_hour'] . ':' . $min_date_array['min_minute'];
		}

		$minimum_date_set = $min_date_array['min_date'] . ' ' . $last_slot;
		$current_date     = date( 'j-n-Y', current_time( 'timestamp' ) );
		$from_date        = date( 'j-n-Y', current_time( 'timestamp' ) ) . ' ' . $from_time;

		if ( $min_time_on_last_slot ) {
			$minimum_date_set = $min_date_array['min_date'] . ' ' . $last_slot;
			$current_date     = date( 'j-n-Y', current_time( 'timestamp' ) );
			$from_date        = date( 'j-n-Y', current_time( 'timestamp' ) ) . ' ' . $to_time;

			$from_date_str        = strtotime( $from_date );
			$minimum_date_set_str = strtotime( $minimum_date_set );
		} else {
			$from_date_str        = strtotime( $from_date );
			$minimum_date_set_str = strtotime( $minimum_date_set );
		}

		if ( ( $current_date == $min_date_array['min_date'] && $minimum_date_set_str < $from_date_str ) && ( isset( $from_time_hour_arr[0] ) && ( $from_time_hour_arr[0] > $min_date_array['min_hour'] || ( $from_time_hour_arr[0] == $min_date_array['min_hour'] && isset( $from_time_hour_arr[1] ) && $from_time_hour_arr[1] > $min_date_array['min_minute'] ) ) ) ) {
			return false;
		}

		return true;
	}
}
$orddd_widget = new orddd_widget();

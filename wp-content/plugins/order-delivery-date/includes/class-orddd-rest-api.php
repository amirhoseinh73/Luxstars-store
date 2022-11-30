<?php
/**
 * Order Delivery Date REST API endpoints.
 *
 * @package Order-Delivery-Date-Pro-for-WooCommerce/REST-API
 * @since 9.27.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Increase or decrease availability for deliveries
 */
class ORDDD_Rest_API {

	/**
	 * Constructor function for creating the API endpoint.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'orddd_create_settings_endpoint' ) );

	}

	/**
	 * Register the REST API routes & endpoints.
	 *
	 * @return void
	 */
	public function orddd_create_settings_endpoint() {
		register_rest_route(
			'orddd/v1',
			'delivery_schedule',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'orddd_get_all_settings' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'orddd/v1',
			'delivery_schedule/(?P<id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'orddd_get_settings' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Display all the settings on the delivery_schedule endpoint.
	 *
	 * @param WP_REST_Request $request API Request.
	 * @return JSON
	 */
	public function orddd_get_all_settings( WP_REST_Request $request ) {
		$all_settings 	  = array();
		$general_settings = self::orddd_get_general_settings();
        $general_settings = array( 'delivery_schedule_id' => '0' ) + $general_settings;
		$settings         = orddd_common::orddd_get_shipping_settings();

		$settings_based_on = $request->get_param( 'mode' );
		$category 		   = $request->get_param( 'category' );
		$shipping_method   = $request->get_param( 'shipping_method' );
		$pickup_location   = $request->get_param( 'pickup_location' );
		$shipping_class    = $request->get_param( 'shipping_class' );
		$delivery_date     = $request->get_param( 'date' );
		$multiple_ids      = $request->get_param( 'ids' );
		$setting_ids       = array();

		array_push( $all_settings, $general_settings );
		foreach ( $settings as $key => $value ) {
            $shipping_setting_key        = $value->option_name;
			$custom_setting_id_arr       = explode( 'orddd_shipping_based_settings_', $shipping_setting_key );
			$custom_delivery_schedule_id = $custom_setting_id_arr[1];
            $settings                    = get_option( $value->option_name );
            $settings                    = array( 'delivery_schedule_id' => $custom_delivery_schedule_id ) + $settings;

			array_push( $all_settings, $settings );
		}

		if ( $settings_based_on ) {
			$all_settings = self::orddd_get_settings_based_on( $settings_based_on );
            $custom_setting_id = '';
			if ( 'category' == $settings_based_on && $category ) {
				$categories       = explode( ',', $category );
				$shipping_classes = $shipping_class ? explode( ',', $shipping_class ) : array();

				$custom_setting_id                    = orddd_custom_delivery_functions::orddd_get_custom_delivery_schedule_id( $pickup_location, $shipping_method, $categories, $shipping_classes  );
			} elseif ( 'shipping_class' === $settings_based_on && $shipping_class ) {
				$shipping_classes  = explode( ',', $shipping_class );
				$custom_setting_id = orddd_custom_delivery_functions::orddd_get_custom_delivery_schedule_id( $pickup_location, $shipping_method, $category, $shipping_classes );
			} elseif ( ( 'shipping_method' === $settings_based_on && $shipping_method ) || ( 'pickup_location' === $settings_based_on && $pickup_location ) ) {
				$custom_setting_id = orddd_custom_delivery_functions::orddd_get_custom_delivery_schedule_id( $pickup_location, $shipping_method, $category, $shipping_class );
			}

            if( '' !== $custom_setting_id ) {
                $all_settings = self::orddd_get_custom_settings_by_id( $custom_setting_id );
            }

		}

		/** To get the common time slots for multiple category settings */
		if ( $delivery_date ) {
			if ( $multiple_ids ) {
				$setting_ids = explode( ',', $multiple_ids );
			}
			$all_settings = orddd_process::check_for_time_slot_orddd( $delivery_date, 0, $setting_ids );
		}

		if ( empty( $all_settings ) ) {
			return new WP_Error( 'no_results', 'No Custom Settings found.', array( 'status' => 200 ) );
		}

		return rest_ensure_response( $all_settings );
	}

	/**
	 * Display the general or custom settings based on the parameters on endpoint.
	 *
	 * @param WP_REST_Request $request API Request.
	 * @return JSON
	 */
	public function orddd_get_settings( WP_REST_Request $request ) {
		$setting_id  = $request['id'];
		$date_regexp = '/^([0-9]{4})[\-]([0]?[1-9]|[1][0-2])[\-]([0]?[1-9]|[1|2][0-9]|[3][0|1])$/';

		$delivery_date     = $request->get_param( 'date' );
		$number_of_dates   = $request->get_param( 'number_of_dates' );
		$settings_based_on = $request->get_param( 'mode' );
		$category 		   = $request->get_param( 'category' );
		$shipping_method   = $request->get_param( 'shipping_method' );
		$pickup_location   = $request->get_param( 'pickup_location' );
		$multiple_ids      = $request->get_param( 'ids' );
		$setting_ids       = array();

		if ( $delivery_date && ! preg_match( $date_regexp, $delivery_date ) ) {
			return new WP_Error( 'invalid_dates', 'Please specify date in YYYY-MM-DD format!', array( 'status' => 200 ) );
		}
		if ( '0' == $setting_id ) {
			$custom_setting = self::orddd_get_general_settings();
		} else {
			$custom_setting = self::orddd_get_custom_settings_by_id( $setting_id );
		}

		if ( $delivery_date ) {
			if ( $multiple_ids ) {
				$setting_ids = explode( ',', $multiple_ids );
			}
			$custom_setting = orddd_process::check_for_time_slot_orddd( $delivery_date, $setting_id, $setting_ids );
		}

		if ( $number_of_dates ) {
			$custom_setting = ORDDD_Functions::check_for_dates_orddd( $number_of_dates, $setting_id );
		}

		return rest_ensure_response( $custom_setting );
	}

	/**
	 * Get all the general settings.
	 *
	 * @return array
	 */
	public function orddd_get_general_settings() {
		global $orddd_weekdays, $orddd_shipping_days;
		$general_settings                                    = array();
		$general_settings['orddd_enable_delivery_date']      = get_option( 'orddd_enable_delivery_date' );
		$general_settings['orddd_delivery_checkout_options'] = get_option( 'orddd_delivery_checkout_options' );
		$general_settings['minimum_delivery_time']           = get_option( 'orddd_minimumOrderDays' );
		$general_settings['number_of_dates']                 = get_option( 'orddd_number_of_dates' );
		$general_settings['date_mandatory_field']            = get_option( 'orddd_date_field_mandatory' );
		$general_settings['date_lockout']                    = get_option( 'orddd_lockout_date_after_orders' );
		$general_settings['max_order_total']                 = get_option( 'orddd_max_order_total_for_charges' );
		$general_settings['orddd_min_between_days']          = get_option( 'orddd_min_between_days' );
		$general_settings['orddd_max_between_days']          = get_option( 'orddd_max_between_days' );
		$general_settings['weekdays']                        = array();
		foreach ( $orddd_weekdays as $n => $day_name ) {
			$general_settings['weekdays'][ $n ] = get_option( $n );
		}
		$general_settings['business_days'] = array();
		foreach ( $orddd_shipping_days as $n => $day_name ) {
			$general_settings['business_days'][ $n ] = get_option( $n );
		}
		$general_settings['time_settings']                  = array(
			'from_hours' => get_option( 'orddd_delivery_from_hours' ),
			'from_mins'  => get_option( 'orddd_delivery_from_mins' ),
			'to_hours'   => get_option( 'orddd_delivery_to_hours' ),
			'to_mins'    => get_option( 'orddd_delivery_to_mins' ),
		);
		$general_settings['orddd_enable_same_day_delivery'] = get_option( 'orddd_enable_same_day_delivery' );
		$general_settings['same_day']                       = array(
			'after_hours'        => get_option( 'orddd_disable_same_day_delivery_after_hours' ),
			'after_mins'         => get_option( 'orddd_disable_same_day_delivery_after_minutes' ),
			'additional_charges' => get_option( 'orddd_same_day_additional_charges' ),
		);
		$general_settings['orddd_enable_next_day_delivery'] = get_option( 'orddd_enable_next_day_delivery' );

		$general_settings['next_day']                                = array(
			'after_hours'        => get_option( 'orddd_disable_next_day_delivery_after_hours' ),
			'after_mins'         => get_option( 'orddd_disable_next_day_delivery_after_minutes' ),
			'additional_charges' => get_option( 'orddd_next_day_additional_charges' ),
		);
		$general_settings['orddd_additional_settings_based_on_days'] = get_option( 'orddd_additional_settings_based_on_days' );
		$general_settings['specific_dates']                          = json_decode( get_option( 'orddd_delivery_dates' ) );
		$general_settings['timeslot_mandatory_field']                = get_option( 'orddd_time_slot_mandatory' );
		$general_settings['time_slots']                              = json_decode( get_option( 'orddd_delivery_time_slot_log' ) );
		$general_settings['holidays']                                = json_decode( get_option( 'orddd_delivery_date_holidays' ) );
		$general_settings['orddd_lockout_date']                      = json_decode( get_option( 'orddd_lockout_days' ) );
		$general_settings['orddd_lockout_time_slot']                 = json_decode( get_option( 'orddd_lockout_time_slot' ) );
		$general_settings['orddd_delivery_date_field_label']         = get_option( 'orddd_delivery_date_field_label' );
		$general_settings['orddd_delivery_timeslot_field_label']     = get_option( 'orddd_delivery_timeslot_field_label' );
		$general_settings['orddd_location_field_label']              = get_option( 'orddd_location_field_label' );
		$general_settings['orddd_delivery_date_field_placeholder']   = get_option( 'orddd_delivery_date_field_placeholder' );
		$general_settings['orddd_delivery_date_field_note']          = get_option( 'orddd_delivery_date_field_note' );
		$general_settings['orddd_estimated_date_text']               = get_option( 'orddd_estimated_date_text' );
		$general_settings['field_placement_on_checkout']             = get_option( 'orddd_delivery_date_fields_on_checkout_page' );
		$general_settings['delivery_date_on_cart_page']              = get_option( 'orddd_delivery_date_on_cart_page' );
		$general_settings['appearance']                              = array(
			'delivery_dates_in_dropdown' => get_option( 'orddd_delivery_dates_in_dropdown' ),
			'calendar_display_mode'      => get_option( 'orddd_calendar_display_mode' ),
			'language_selected'          => get_option( 'orddd_language_selected' ),
			'date_format'                => get_option( 'orddd_delivery_date_format' ),
			'time_format'                => get_option( 'orddd_delivery_time_format' ),
			'first_day_of_week'          => get_option( 'start_of_week' ),
			'calendar_theme'             => get_option( 'orddd_calendar_theme' ),
		);

		return $general_settings;
	}

	/**
	 * Get the custom settings based on the setting ID.
	 *
	 * @param int $setting_id Custom Setting ID.
	 * @return array
	 */
	public function orddd_get_custom_settings_by_id( $setting_id ) {
		$custom_setting = orddd_custom_delivery_functions::orddd_get_delivery_schedule_settings_by_id( $setting_id );
		$settings       = array();

        if( empty( $custom_setting ) ) {
            return array();
        }

		foreach ( $custom_setting as $key => $value ) {
			if ( 'specific_dates' === $key ) {
				$custom_setting['specific_dates'] = json_decode( $value );
			}

			if ( 'orddd_lockout_date' === $key ) {
				$custom_setting['orddd_lockout_date'] = json_decode( $value );
			}

			if ( 'orddd_lockout_time_slot' === $key ) {
				$custom_setting['orddd_lockout_time_slot'] = json_decode( $value );
			}
		}

        $custom_setting = array( 'delivery_schedule_id' => $setting_id ) + $custom_setting;
		return $custom_setting;
	}

	/**
	 * Get all the settings based on the mode parameter.
	 *
	 * @param string $mode Settings based on.
	 * @return array
	 */
	public function orddd_get_settings_based_on( $mode ) {
		$results           = orddd_common::orddd_get_shipping_settings();
		$custom_settings   = array();
		$settings_based_on = '';

		if ( 'shipping_method' === $mode || 'shipping_class' === $mode ) {
			$settings_based_on = 'shipping_methods';
		} elseif ( 'category' === $mode ) {
			$settings_based_on = 'product_categories';
		} elseif ( 'pickup_location' === $mode ) {
			$settings_based_on = 'orddd_locations';
		}

		foreach ( $results as $key => $value ) {
			$shipping_settings = get_option( $value->option_name );

			if( isset( $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] ) && $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] == $settings_based_on ) {
                $shipping_setting_key        = $value->option_name;
				$custom_setting_id_arr       = explode( 'orddd_shipping_based_settings_', $shipping_setting_key );
				$custom_delivery_schedule_id = $custom_setting_id_arr[1];
                $custom_settings[]           = array( 'delivery_schedule_id' => $custom_delivery_schedule_id ) + $shipping_settings;

			}
		}

		return $custom_settings;
	}
}

$orddd_rest_api = new ORDDD_Rest_API();

<?php
/**
 * Display General Settings -> Time slot settings in admin.
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Pro-for-WooCommerce/Admin/Settings/General
 * @since 2.4
 * @category Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Orddd_Time_Slot_Settings class
 *
 * @class Orddd_Time_Slot_Settings
 */
class Orddd_Time_Slot_Settings {

	/**
	 * Callback for adding Time slot tab settings.
	 */
	public static function orddd_time_slot_admin_settings_callback() { }

	/**
	 * Callback for adding Enable time slot setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_time_slot_enable_callback( $args ) {
		$enable_time_slot = '';
		if ( 'on' === get_option( 'orddd_enable_time_slot' ) ) {
			$enable_time_slot = 'checked';
		}
		?>
		<input type="checkbox" name="orddd_enable_time_slot" id="orddd_enable_time_slot" class="day-checkbox" <?php echo esc_attr( $enable_time_slot ); ?>/>
		<label for="orddd_enable_time_slot"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}


	/**
	 * Callback for adding Time slot field mandatory setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_time_slot_mandatory_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_time_slot_mandatory" id="orddd_time_slot_mandatory" class="timeslot-checkbox" value="checked" <?php echo esc_attr( get_option( 'orddd_time_slot_mandatory' ) ); ?>/>
		<label for="orddd_time_slot_mandatory"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding As soon as possible option in time slot dropdown on checkout page
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 7.9
	 */
	public static function orddd_time_slot_asap_callback( $args ) {
		?>
		<input type="checkbox" name="orddd_time_slot_asap" id="orddd_time_slot_asap" class="timeslot-checkbox" value="checked" <?php echo esc_attr( get_option( 'orddd_time_slot_asap' ) ); ?> />
		<label for="orddd_time_slot_asap"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Global lockout for Time slot setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_global_lockout_time_slots_callback( $args ) {
		?>
		<input type="number" min="0" step="1" name="orddd_global_lockout_time_slots" id="orddd_global_lockout_time_slots" value="<?php echo esc_attr( get_option( 'orddd_global_lockout_time_slots' ) ); ?>"/>
		<label for="orddd_global_lockout_time_slots"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Show first available Time slot setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_show_first_available_time_slot_callback( $args ) {
		$orddd_show_select = '';
		if ( 'on' === get_option( 'orddd_auto_populate_first_available_time_slot' ) ) {
			$orddd_show_select = 'checked';
		}
		?>
		<input type='checkbox' name='orddd_auto_populate_first_available_time_slot' id='orddd_auto_populate_first_available_time_slot' value='on' <?php echo esc_attr( $orddd_show_select ); ?>>
		<label for="orddd_auto_populate_first_available_time_slot"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Show first available Time slot setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 9.22.0
	 */
	public static function orddd_time_slots_in_list_view_callback( $args ) {
		$orddd_show_select = '';
		if ( 'on' === get_option( 'orddd_time_slots_in_list_view' ) ) {
			$orddd_show_select = 'checked';
		}
		?>
		<input type='checkbox' name='orddd_time_slots_in_list_view' id='orddd_time_slots_in_list_view' value='on' <?php echo esc_attr( $orddd_show_select ); ?>>
		<label for="orddd_time_slots_in_list_view"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Time slot settings Extra arguments containing label & class for the field
	 */
	public static function orddd_add_time_slot_admin_settings_callback() {

	}

	/**
	 * Callback to add time slots for weekday or specific dates
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_time_slot_for_delivery_days_callback( $args ) {
		global $orddd_weekdays;
		$orddd_time_slot_for_weekdays       = 'checked';
		$orddd_time_slot_for_specific_dates = '';
		if ( 'weekdays' === get_option( 'orddd_time_slot_for_delivery_days' ) ) {
			$orddd_time_slot_for_weekdays       = 'checked';
			$orddd_time_slot_for_specific_dates = '';
		} elseif ( 'specific_dates' === get_option( 'orddd_time_slot_for_delivery_days' ) ) {
			$orddd_time_slot_for_specific_dates = 'checked';
			$orddd_time_slot_for_weekdays       = '';
		}

		?>
		<p><label><input type="radio" name="orddd_time_slot_for_delivery_days" id="orddd_time_slot_for_delivery_days" class="orddd_time_slot_for_delivery_days" value="weekdays"<?php echo esc_attr( $orddd_time_slot_for_weekdays ); ?>/><?php esc_html_e( 'Weekdays', 'order-delivery-date' ); ?></label>
		<label><input type="radio" name="orddd_time_slot_for_delivery_days" id="orddd_time_slot_for_delivery_days" class="orddd_time_slot_for_delivery_days" value="specific_dates"<?php echo esc_attr( $orddd_time_slot_for_specific_dates ); ?>/><?php esc_html_e( 'Specific Dates', 'order-delivery-date' ); ?></label></p>
		<input type="hidden" name="specific_dates_enabled" id="specific_dates_enabled" value="<?php echo get_option( 'orddd_enable_specific_delivery_dates' ); ?>">
		<?php
		$alldays = array();
		foreach ( $orddd_weekdays as $n => $day_name ) {
			$alldays[ $n ] = get_option( $n );
		}

		$alldayskeys = array_keys( $alldays );
		$checked     = 'No';
		foreach ( $alldayskeys as $key ) {
			if ( 'checked' === $alldays[ $key ] ) {
				$checked = 'Yes';
			}
		}
		?>
		<label for="orddd_time_slot_for_delivery_days"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Weekdays for Time slot setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_time_slot_for_weekdays_callback( $args ) {
		global $orddd_weekdays;
		foreach ( $orddd_weekdays as $n => $day_name ) {
			$alldays[ $n ] = get_option( $n );
		}
		$alldayskeys = array_keys( $alldays );
		$checked     = 'No';
		foreach ( $alldayskeys as $key ) {
			if ( 'checked' === $alldays[ $key ] ) {
				$checked = 'Yes';
			}
		}

		printf(
			'<div class="time_slot_options time_slot_for_weekdays">
             <select class="orddd_time_slot_for_weekdays" id="orddd_time_slot_for_weekdays" name="orddd_time_slot_for_weekdays[]" multiple="multiple" placeholder="Select Weekdays">
                <option name="all" value="all">All</option>'
		);
		$weekdays_arr = array();
		foreach ( $orddd_weekdays as $n => $day_name ) {
			if ( 'checked' === get_option( $n ) ) {
				$weekdays[ $n ] = $day_name;
				printf( '<option name="' . esc_attr( $n ) . '" value="' . esc_attr( $n ) . '">' . esc_attr( $weekdays[ $n ] ) . '</option>' );
			}
		}

		if ( 'No' === $checked ) {
			foreach ( $orddd_weekdays as $n => $day_name ) {
				$weekdays[ $n ] = $day_name;
				printf( '<option name="' . esc_attr( $n ) . '" value="' . esc_attr( $n ) . '">' . esc_attr( $weekdays[ $n ] ) . '</option>' );
			}
		}
		print( '</select></div>' );

		printf(
			'<div class="time_slot_options time_slot_for_specific_dates">
            <select class="orddd_time_slot_for_weekdays" id="orddd_select_delivery_dates" name="orddd_select_delivery_dates[]" multiple="multiple" placeholder="Select Specific Delivery Dates" >'
		);

		$delivery_arr          = array();
		$delivery_dates_select = get_option( 'orddd_delivery_dates' );
		if ( '' !== $delivery_dates_select &&
			'{}' !== $delivery_dates_select &&
			'[]' !== $delivery_dates_select &&
			'null' !== $delivery_dates_select ) {
			$delivery_arr = json_decode( $delivery_dates_select );
		}
		foreach ( $delivery_arr as $key => $value ) {
			foreach ( $value as $k => $v ) {
				if ( 'date' === $k ) {
					$date            = explode( '-', $v );
					$date_to_display = gmdate( 'm-d-Y', gmmktime( 0, 0, 0, $date[0], $date[1], $date[2] ) );
					$temp_arr[ $k ]  = $date_to_display;
				} else {
					$temp_arr[ $k ] = $v;
				}
			}
			printf(
				'<option value=' . esc_attr( $temp_arr['date'] ) . '>' . esc_attr( $temp_arr['date'] ) . "</option>\n"
			);
		}
		printf( '</select></div>' );
		?>
		<label for="orddd_time_slot_for_weekdays"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding From hours for Time slot setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_time_from_hours_callback( $args ) {
		?>
		<section class="add-timeslot">
			<input type="text" name="orddd_time_from_hours[]" id="orddd_time_from_hours" value=""/>
			To
			<input type="text" name="orddd_time_to_hours[]" id="orddd_time_to_hours" value=""/>

			<a href="#" id="add_another_slot" role="button">+ Add another slot</a>
		</section>
		<?php
	}

	/**
	 * Callback for adding To hours for Time slot setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_time_to_hours_callback( $args ) {
		?>
		<input type="text" name="orddd_time_to_hours" id="orddd_time_to_hours" value="<?php echo esc_attr( get_option( 'orddd_time_to_hours' ) ); ?>"/>
		<label for="orddd_time_to_hours"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Lockout Time slot after X orders setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_time_slot_lockout_callback( $args ) {
		?>
		<input type="number" min="0" step="1" name="orddd_time_slot_lockout" id="orddd_time_slot_lockout"/>
		<label for="orddd_time_slot_lockout"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback to add additional charges for a time slot
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_time_slot_additional_charges_callback( $args ) {
		?>
		<input type="text" name="orddd_time_slot_additional_charges" id="orddd_time_slot_additional_charges" placeholder="Charges"/>
		<input type="text" name="orddd_time_slot_additional_charges_label" id="orddd_time_slot_additional_charges_label" placeholder="Time slot Charges Label" />
		<label for="orddd_time_slot_additional_charges"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Time slot settings Extra arguments containing label & class for the field
	 */
	public static function orddd_bulk_time_slot_admin_settings_callback() {}

	/**
	 * Callback to add time slots for weekday or specific dates
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 9.20.0
	 */
	public static function orddd_bulk_time_slot_for_delivery_days_callback( $args ) {
		global $orddd_weekdays;
		$orddd_time_slot_for_weekdays       = 'checked';
		$orddd_time_slot_for_specific_dates = '';
		if ( 'weekdays' === get_option( 'orddd_bulk_time_slot_for_delivery_days' ) ) {
			$orddd_time_slot_for_weekdays       = 'checked';
			$orddd_time_slot_for_specific_dates = '';
		} elseif ( 'specific_dates' === get_option( 'orddd_bulk_time_slot_for_delivery_days' ) ) {
			$orddd_time_slot_for_specific_dates = 'checked';
			$orddd_time_slot_for_weekdays       = '';
		}

		?>
		<p><label><input type="radio" name="orddd_bulk_time_slot_for_delivery_days" id="orddd_bulk_time_slot_for_delivery_days" value="weekdays"<?php echo esc_attr( $orddd_time_slot_for_weekdays ); ?>/><?php esc_html_e( 'Weekdays', 'order-delivery-date' ); ?></label>
		<label><input type="radio" name="orddd_bulk_time_slot_for_delivery_days" id="orddd_bulk_time_slot_for_delivery_days" value="specific_dates"<?php echo esc_attr( $orddd_time_slot_for_specific_dates ); ?>/><?php esc_html_e( 'Specific Dates', 'order-delivery-date' ); ?></label></p>
		
		<?php
		$alldays = array();
		foreach ( $orddd_weekdays as $n => $day_name ) {
			$alldays[ $n ] = get_option( $n );
		}

		$alldayskeys = array_keys( $alldays );
		$checked     = 'No';
		foreach ( $alldayskeys as $key ) {
			if ( 'checked' === $alldays[ $key ] ) {
				$checked = 'Yes';
			}
		}
		?>
		<label for="orddd_bulk_time_slot_for_delivery_days"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Weekdays for Time slot setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_time_slot_for_weekdays_bulk_callback( $args ) {
		global $orddd_weekdays;
		foreach ( $orddd_weekdays as $n => $day_name ) {
			$alldays[ $n ] = get_option( $n );
		}
		$alldayskeys = array_keys( $alldays );
		$checked     = 'No';
		foreach ( $alldayskeys as $key ) {
			if ( 'checked' === $alldays[ $key ] ) {
				$checked = 'Yes';
			}
		}

		printf(
			'<div class="time_slot_options_bulk time_slot_for_bulk_weekdays">
             <select class="orddd_time_slot_for_weekdays" id="orddd_time_slot_for_weekdays_bulk" name="orddd_time_slot_for_weekdays_bulk[]" multiple="multiple" placeholder="Select Weekdays">
                <option name="all" value="all">All</option>'
		);
		$weekdays_arr = array();
		foreach ( $orddd_weekdays as $n => $day_name ) {
			if ( 'checked' === get_option( $n ) ) {
				$weekdays[ $n ] = $day_name;
				printf( '<option name="' . esc_attr( $n ) . '" value="' . esc_attr( $n ) . '">' . esc_attr( $weekdays[ $n ] ) . '</option>' );
			}
		}

		if ( 'No' === $checked ) {
			foreach ( $orddd_weekdays as $n => $day_name ) {
				$weekdays[ $n ] = $day_name;
				printf( '<option name="' . esc_attr( $n ) . '" value="' . esc_attr( $n ) . '">' . esc_attr( $weekdays[ $n ] ) . '</option>' );
			}
		}
		print( '</select></div>' );

		printf(
			'<div class="time_slot_options_bulk time_slot_for_bulk_specific_dates">
            <select class="orddd_time_slot_for_weekdays" id="orddd_select_delivery_dates_bulk" name="orddd_select_delivery_dates_bulk[]" multiple="multiple" placeholder="Select Specific Delivery Dates" >'
		);

		$delivery_arr          = array();
		$delivery_dates_select = get_option( 'orddd_delivery_dates' );
		if ( '' !== $delivery_dates_select &&
			'{}' !== $delivery_dates_select &&
			'[]' !== $delivery_dates_select &&
			'null' !== $delivery_dates_select ) {
			$delivery_arr = json_decode( $delivery_dates_select );
		}
		foreach ( $delivery_arr as $key => $value ) {
			foreach ( $value as $k => $v ) {
				if ( 'date' === $k ) {
					$date            = explode( '-', $v );
					$date_to_display = gmdate( 'm-d-Y', gmmktime( 0, 0, 0, $date[0], $date[1], $date[2] ) );
					$temp_arr[ $k ]  = $date_to_display;
				} else {
					$temp_arr[ $k ] = $v;
				}
			}
			printf(
				'<option value=' . esc_attr( $temp_arr['date'] ) . '>' . esc_attr( $temp_arr['date'] ) . "</option>\n"
			);
		}
		printf( '</select></div>' );
		?>
		<label for="orddd_time_slot_for_weekdays_bulk"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback function for time duration in the bulk time slot.
	 *
	 * @param array $args Extra arguments.
	 * @since 9.20.0
	 * @return void
	 */
	public static function orddd_time_slot_duration_callback( $args ) {
		?>
		<input type="number" min="0" step="1" name="orddd_time_slot_duration" id="orddd_time_slot_duration" value="<?php echo esc_attr( get_option( 'orddd_time_slot_duration' ) ); ?>"/>
		<label for="orddd_time_slot_duration"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback function for time interval in the bulk time slot.
	 *
	 * @param array $args Extra arguments.
	 * @since 9.20.0
	 * @return void
	 */
	public static function orddd_time_slot_interval_callback( $args ) {
		?>
		<input type="number" min="0" step="1" name="orddd_time_slot_interval" id="orddd_time_slot_interval" value="<?php echo esc_attr( get_option( 'orddd_time_slot_interval' ) ); ?>"/>
		<label for="orddd_time_slot_interval"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback function for start time in the bulk time slot.
	 *
	 * @param array $args Extra arguments.
	 * @since 9.20.0
	 * @return void
	 */
	public static function orddd_time_slot_starts_from_callback( $args ) {
		?>
		<input type="text" name="orddd_time_slot_starts_from" id="orddd_time_slot_starts_from" value="<?php echo esc_attr( get_option( 'orddd_time_slot_starts_from' ) ); ?>"/>
		<label for="orddd_time_slot_starts_from"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback function for end time in the bulk time slot.
	 *
	 * @param array $args Extra arguments.
	 * @return void
	 * @since 9.20.0
	 */
	public static function orddd_time_slot_ends_at_callback( $args ) {
		?>
		<input type="text" name="orddd_time_slot_ends_at" id="orddd_time_slot_ends_at" value="<?php echo esc_attr( get_option( 'orddd_time_slot_ends_at' ) ); ?>"/>
		<label for="orddd_time_slot_ends_at"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Lockout Time slot after X orders setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_bulk_time_slot_lockout_callback( $args ) {
		?>
		<input type="number" min="0" step="1" name="orddd_bulk_time_slot_lockout" id="orddd_bulk_time_slot_lockout"/>
		<label for="orddd_bulk_time_slot_lockout"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback to add additional charges for a time slot
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.4
	 */
	public static function orddd_bulk_time_slot_additional_charges_callback( $args ) {
		?>
		<input type="text" name="orddd_bulk_time_slot_additional_charges" id="orddd_bulk_time_slot_additional_charges" placeholder="Charges"/>
		<input type="text" name="orddd_bulk_time_slot_additional_charges_label" id="orddd_bulk_time_slot_additional_charges_label" placeholder="Time slot Charges Label" />
		<label for="orddd_bulk_time_slot_additional_charges_label"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for saving time slots
	 *
	 * @param array $data Time slot data.
	 * @return string
	 * @since 2.4
	 */
	public static function orddd_delivery_time_slot_callback( $data ) {
		global $orddd_weekdays;

		if ( ! empty( $_POST ) && isset( $_POST['orddd_mode'] ) && 'edit' === $_POST['orddd_mode'] ) { // phpcs:ignore
			return $data;
		}

		if ( ! check_admin_referer( 'orddd_save_time_slot_field_nonce', 'orddd_save_time_slot_field' ) ) {
			return;
		}

		foreach ( $orddd_weekdays as $n => $day_name ) {
			$alldays[ $n ] = get_option( $n );
		}
		$alldayskeys = array_keys( $alldays );

		$timeslot         = get_option( 'orddd_delivery_time_slot_log' );
		$timeslot_new_arr = array();
		if ( 'null' === $timeslot || '' === $timeslot || '{}' === $timeslot || '[]' === $timeslot ) {
			$timeslot_arr = array();
		} else {
			$timeslot_arr = json_decode( $timeslot );
		}

		if ( isset( $timeslot_arr ) && is_array( $timeslot_arr ) && count( $timeslot_arr ) > 0 ) {
			foreach ( $timeslot_arr as $k => $v ) {
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

		$selected_dates     	  = '';
		$time_slot_value 		  = '';
		$lockouttime              = '';
		$additional_charges       = '';
		$additional_charges_label = '';

		if ( isset( $_POST['orddd_bulk_time_slot_for_delivery_days'] ) && '' !== $_POST['orddd_bulk_time_slot_for_delivery_days'] && isset( $_POST['orddd_individual_or_bulk'] ) && 'bulk' == $_POST['orddd_individual_or_bulk'] ) { // phpcs:ignore
			$time_slot_value = $_POST['orddd_bulk_time_slot_for_delivery_days']; // phpcs:ignore
			if ( isset( $_POST['orddd_bulk_time_slot_lockout'] ) ) {
				$lockouttime = sanitize_text_field( wp_unslash( $_POST['orddd_bulk_time_slot_lockout'] ) );
			}
	
			if ( isset( $_POST['orddd_bulk_time_slot_additional_charges'] ) ) {
				$additional_charges = sanitize_text_field( wp_unslash( $_POST['orddd_bulk_time_slot_additional_charges'] ) );
			}
	
			if ( isset( $_POST['orddd_bulk_time_slot_additional_charges_label'] ) ) {
				$additional_charges_label = sanitize_text_field( wp_unslash( $_POST['orddd_bulk_time_slot_additional_charges_label'] ) );
			}

			if ( 'weekdays' === $time_slot_value ) {
				if ( isset( $_POST['orddd_time_slot_for_weekdays_bulk'] ) ) { // phpcs:ignore
					$orddd_time_slot_for_weekdays = $_POST['orddd_time_slot_for_weekdays_bulk']; // phpcs:ignore
	
					// Add all the individual enabled weekdays if 'all' is selected.
					if ( in_array( 'all', $orddd_time_slot_for_weekdays, true ) ) {
						$weekdays = array();
						foreach ( $alldayskeys as $key ) {
							if ( 'checked' === $alldays[ $key ] ) {
								array_push( $weekdays, $key );
							}
						}
					} else {
						$weekdays = $_POST['orddd_time_slot_for_weekdays_bulk']; // phpcs:ignore
					}
	
					$selected_dates = wp_json_encode( $weekdays );
				}
			} elseif ( 'specific_dates' === $time_slot_value ) {
				if ( isset( $_POST['orddd_select_delivery_dates_bulk'] ) ) { // phpcs:ignore
					$devel_dates_arr = $_POST['orddd_select_delivery_dates_bulk']; // phpcs:ignore
					$dates_arr       = array();
					foreach ( $devel_dates_arr as $key => $value ) {
						$date              = explode( '-', $value );
						$date_to_store     = gmdate( 'n-j-Y', gmmktime( 0, 0, 0, $date[0], $date[1], $date[2] ) );
						$dates_arr[ $key ] = $date_to_store;
					}
					$selected_dates = wp_json_encode( $dates_arr );
				}
			}
		} elseif ( isset( $_POST['orddd_time_slot_for_delivery_days'] ) && '' !== $_POST['orddd_time_slot_for_delivery_days'] ) { // phpcs:ignore 
			$time_slot_value = $_POST['orddd_time_slot_for_delivery_days']; // phpcs:ignore
			if ( isset( $_POST['orddd_time_slot_lockout'] ) ) {
				$lockouttime = sanitize_text_field( wp_unslash( $_POST['orddd_time_slot_lockout'] ) );
			}
	
			if ( isset( $_POST['orddd_time_slot_additional_charges'] ) ) {
				$additional_charges = sanitize_text_field( wp_unslash( $_POST['orddd_time_slot_additional_charges'] ) );
			}
	
			if ( isset( $_POST['orddd_time_slot_additional_charges_label'] ) ) {
				$additional_charges_label = sanitize_text_field( wp_unslash( $_POST['orddd_time_slot_additional_charges_label'] ) );
			}

			if ( 'weekdays' === $time_slot_value ) {
				if ( isset( $_POST['orddd_time_slot_for_weekdays'] ) ) { // phpcs:ignore
					$orddd_time_slot_for_weekdays = $_POST['orddd_time_slot_for_weekdays']; // phpcs:ignore
	
					// Add all the individual enabled weekdays if 'all' is selected.
					if ( in_array( 'all', $orddd_time_slot_for_weekdays, true ) ) {
						$weekdays = array();
						foreach ( $alldayskeys as $key ) {
							if ( 'checked' === $alldays[ $key ] ) {
								array_push( $weekdays, $key );
							}
						}
					} else {
						$weekdays = $_POST['orddd_time_slot_for_weekdays']; // phpcs:ignore
					}
	
					$selected_dates = wp_json_encode( $weekdays );
				}
			} elseif ( 'specific_dates' === $time_slot_value ) {
				if ( isset( $_POST['orddd_select_delivery_dates'] ) ) { // phpcs:ignore
					$devel_dates_arr = $_POST['orddd_select_delivery_dates']; // phpcs:ignore
					$dates_arr       = array();
					foreach ( $devel_dates_arr as $key => $value ) {
						$date              = explode( '-', $value );
						$date_to_store     = gmdate( 'n-j-Y', gmmktime( 0, 0, 0, $date[0], $date[1], $date[2] ) );
						$dates_arr[ $key ] = $date_to_store;
					}
					$selected_dates = wp_json_encode( $dates_arr );
				}
			}
		}

		if ( ( ( ! isset( $_POST['orddd_time_slot_for_weekdays'] ) && ! isset( $_POST['orddd_select_delivery_dates'] ) ) && ( ! isset( $_POST['orddd_time_slot_for_weekdays_bulk'] ) && ! isset( $_POST['orddd_select_delivery_dates_bulk'] ) ) )
		&& ( ( ! empty( $_POST['orddd_time_from_hours'] ) && '' !== $_POST['orddd_time_from_hours'][0] )
		|| ( isset( $_POST['orddd_time_slot_starts_from'] ) && '' !== $_POST['orddd_time_slot_starts_from'] ) ) ) {

			add_settings_error( 'orddd_delivery_time_slot_log_error', 'time_slot_save_error', 'Please Select Delivery Days/Dates for the Time slot', 'error' );

		} elseif ( isset( $_POST['orddd_time_slot_starts_from'] ) && '' !== $_POST['orddd_time_slot_starts_from'] ) {
			$duration = isset( $_POST['orddd_time_slot_duration'] ) && '' !== $_POST['orddd_time_slot_duration'] ? wp_unslash( sanitize_text_field( wp_unslash( $_POST['orddd_time_slot_duration'] ) ) ) : 60;

			$frequency = isset( $_POST['orddd_time_slot_interval'] ) && '' !== $_POST['orddd_time_slot_interval'] ? sanitize_text_field( wp_unslash( $_POST['orddd_time_slot_interval'] ) ) : 0;

			$time_starts_from = isset( $_POST['orddd_time_slot_starts_from'] ) && '' !== $_POST['orddd_time_slot_starts_from'] ? sanitize_text_field( wp_unslash( $_POST['orddd_time_slot_starts_from'] ) ) : '';
			$time_ends_at     = isset( $_POST['orddd_time_slot_ends_at'] ) && '' !== $_POST['orddd_time_slot_ends_at'] ? sanitize_text_field( wp_unslash( $_POST['orddd_time_slot_ends_at'] ) ) : $time_starts_from;

			if ( 0 == $duration) {
				add_settings_error( 'orddd_delivery_time_slot_log_error', 'time_slot_save_error', 'Please Set the Time Slot Duration to be Greater than 0.', 'error' );
			} elseif ( '' !== $time_starts_from ) {
				$duration_in_secs  = $duration * 60;
				$frequency_in_secs = $frequency * 60;
				$array_of_time     = array();
				$start_time        = strtotime( $time_starts_from );
				$end_time          = strtotime( $time_ends_at );

				while ( $start_time <= $end_time ) {
					$from_hours  = gmdate( 'G:i', $start_time );
					$start_time += $duration_in_secs;

					if ( $start_time > $end_time ) {
						break;
					}
					$to_hours        = gmdate( 'G:i', $start_time );
					$array_of_time[] = $from_hours . ' - ' . $to_hours;
					if ( $frequency_in_secs > 0 ) {
						$start_time += $frequency_in_secs;
					}
				}

				$timeslot_new_arr = self::orddd_save_timeslots( $array_of_time, $timeslot_new_arr, $time_slot_value, $selected_dates, $lockouttime, $additional_charges, $additional_charges_label );
			}
		} elseif ( isset( $_POST['orddd_time_from_hours'] ) && '' !== $_POST['orddd_time_from_hours'] ) {
			$from_hours = isset( $_POST['orddd_time_from_hours'] ) && '' !== $_POST['orddd_time_from_hours'] ? wp_unslash( array_map( 'sanitize_text_field', wp_unslash( $_POST['orddd_time_from_hours'] ) ) ) : '';
			$to_hours = isset( $_POST['orddd_time_to_hours'] ) && '' !== $_POST['orddd_time_to_hours'] ? wp_unslash( array_map( 'sanitize_text_field', wp_unslash( $_POST['orddd_time_to_hours'] ) ) ) : $from_hours;
			$array_of_time = array();
			if ( ! empty( $from_hours ) ) {
				foreach ( $from_hours as $key => $from_hour ) {

					if ( '' === $from_hour ) {
						continue;
					}
					if ( '' === $to_hours[ $key ] ) {
						$array_of_time[] = $from_hour;
					} else {
						$array_of_time[] = $from_hour . ' - ' . $to_hours[ $key ];
					}
				}

				$timeslot_new_arr = self::orddd_save_timeslots( $array_of_time, $timeslot_new_arr, $time_slot_value, $selected_dates, $lockouttime, $additional_charges, $additional_charges_label );
			}
		}

		$timeslot_jarr = wp_json_encode( $timeslot_new_arr );
		return $timeslot_jarr;
	}


	/**
	 * Save the timeslots for weekdays or specific dates.
	 *
	 * @param array  $array_of_time Array of the time slots to save.
	 * @param array  $timeslot_new_arr Existing time slots array.
	 * @param string $time_slot_value Time slot for weekdays or specific dates.
	 * @param string $selected_dates Selected weekdays or specific dates.
	 * @param int    $lockouttime Maximum order deliveries for the time slot.
	 * @param string $additional_charges Additional charges for time slot.
	 * @param string $additional_charges_label Additional charges label.
	 * @return array
	 * @since 9.20.0
	 */
	public static function orddd_save_timeslots( $array_of_time, $timeslot_new_arr, $time_slot_value, $selected_dates, $lockouttime, $additional_charges, $additional_charges_label ) {
		$time_format = orddd_common::orddd_get_time_format();

		foreach ( $array_of_time as $timeslot ) {
			if( 'h:i A' == $time_format ) {
				$timeslot = orddd_common::orddd_change_time_slot_format( $timeslot );
			}

			$timeslot_array = explode( ' - ', $timeslot );
			$from_time      = explode( ':', $timeslot_array[0] );
			$to_time        = explode( ':', $timeslot_array[1] );

			$from_hour   = $from_time[0];
			$from_minute = $from_time[1];
			$to_hour     = $to_time[0];
			$to_minute   = $to_time[1];

			$from_hour_new   = gmdate( 'G', gmmktime( $from_hour, $from_minute, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) ) );
			$from_minute_new = gmdate( 'i ', gmmktime( $from_hour, $from_minute, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) ) );
			$to_hour_new     = gmdate( 'G', gmmktime( $to_hour, $to_minute, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) ) );
			$to_minute_new   = gmdate( 'i ', gmmktime( $to_hour, $to_minute, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) ) );

			$timeslot_present = 'no';
			foreach ( $timeslot_new_arr as $key => $value ) {

				$fh = $value['fh'];
				$fm = $value['fm'];
				$th = $value['th'];
				$tm = $value['tm'];

				if ( 'weekdays' === $value['tv'] &&
					gettype( json_decode( $value['dd'] ) ) === 'array' &
					count( json_decode( $value['dd'] ) ) > 0 ) {
					$dd = json_decode( $value['dd'] );

					if ( 'all' === $dd[0] &&
						$fh === $from_hour_new &&
						$fm === $from_minute_new &&
						$th === $to_hour_new &&
						$tm === $to_minute_new ) {
						$timeslot_present = 'yes';
						break;
					} else {
						foreach ( $dd as $id => $day ) {
							if ( isset( $_POST['orddd_time_slot_for_weekdays'] ) &&
							in_array( $day, $_POST['orddd_time_slot_for_weekdays'], true ) &&
							$fh === $from_hour_new &&
							$fm === $from_minute_new &&
							$th === $to_hour_new &&
							$tm === $to_minute_new ) {
								$timeslot_present = 'yes';
								break;

							}
						}
					}
				} elseif ( 'specific_dates' === $value['tv'] ) {
					$dd = json_decode( $value['dd'] );
					foreach ( $dd as $id => $day ) {
						if ( isset( $_POST['orddd_select_delivery_dates'] ) &&
						in_array( $day, $_POST['orddd_select_delivery_dates'], true ) &&
						$fh === $from_hour_new &&
						$fm === $from_minute_new &&
						$th === $to_hour_new &&
						$tm === $to_minute_new ) {
							$timeslot_present = 'yes';
							break;

						}
					}
				}
			}

			if ( 'no' === $timeslot_present ) {
				$timeslot_new_arr[] = array(
					'tv'                       => $time_slot_value,
					'dd'                       => $selected_dates,
					'lockout'                  => $lockouttime,
					'additional_charges'       => $additional_charges,
					'additional_charges_label' => $additional_charges_label,
					'fh'                       => $from_hour_new,
					'fm'                       => $from_minute_new,
					'th'                       => $to_hour_new,
					'tm'                       => $to_minute_new,
				);
			}
		}

		return $timeslot_new_arr;
	}

	/**
	 * Ajax call to save the edited time slot.
	 *
	 * @return void
	 * @since 9.20.0
	 */
	public static function orddd_edit_time_slot() {
		$timeslot         = get_option( 'orddd_delivery_time_slot_log' );
		$time_format	  = orddd_common::orddd_get_time_format();
		$timeslot_new_arr = array();
		if ( 'null' === $timeslot || '' === $timeslot || '{}' === $timeslot || '[]' === $timeslot ) {
			$timeslot_arr = array();
		} else {
			$timeslot_arr = json_decode( $timeslot );
		}

		if ( isset( $timeslot_arr ) && is_array( $timeslot_arr ) && count( $timeslot_arr ) > 0 ) {
			foreach ( $timeslot_arr as $k => $v ) {
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

		$from_hours               = isset( $_POST['orddd_time_from_hours'] ) && '' !== $_POST['orddd_time_from_hours'] ? wp_unslash( $_POST['orddd_time_from_hours'] ) : ''; // phpcs:ignore
		$from_time_old            = isset( $_POST['from_time_old'] ) && '' !== $_POST['from_time_old'] ? wp_unslash( $_POST['from_time_old'] ) : ''; // phpcs:ignore
		$to_hours                 = isset( $_POST['orddd_time_to_hours'] ) && '' !== $_POST['orddd_time_to_hours'] ? wp_unslash( $_POST['orddd_time_to_hours'] ) : ''; // phpcs:ignore
		$to_time_old              = isset( $_POST['to_time_old'] ) && '' !== $_POST['to_time_old'] ? wp_unslash( $_POST['to_time_old'] ) : ''; // phpcs:ignore
		$time_slot_value          = isset( $_POST['time_slot_for'] ) && '' !== $_POST['time_slot_for'] ? wp_unslash( $_POST['time_slot_for'] ) : ''; // phpcs:ignore
		$selected_dates           = isset( $_POST['weekday'] ) && '' !== $_POST['weekday'] ? wp_unslash( $_POST['weekday'] ) : ''; // phpcs:ignore
		$lockouttime              = isset( $_POST['orddd_time_slot_lockout'] ) ? wp_unslash( $_POST['orddd_time_slot_lockout'] ) : ''; // phpcs:ignore
		$additional_charges       = isset( $_POST['orddd_time_slot_additional_charges'] ) ? wp_unslash( $_POST['orddd_time_slot_additional_charges'] ) : ''; // phpcs:ignore
		$additional_charges_label = isset( $_POST['orddd_time_slot_additional_charges_label'] ) ? wp_unslash( $_POST['orddd_time_slot_additional_charges_label'] ) : ''; // phpcs:ignore

		if( 'All' === $selected_dates ) {
			$selected_dates = strtolower( $selected_dates );
		}

		if ( '' !== $from_hours ) {
			$from_time_old = date( 'H:i', strtotime( $from_time_old ) );
			$from_time 	   = explode( ':', $from_time_old );
			$from_hour     = $from_time[0];
			$from_minute   = $from_time[1];

			$to_time   = '';
			$to_hour   = 0;
			$to_minute = 0;

			if ( '' !== $to_time_old ) {
				$to_time_old = date( 'H:i', strtotime( $to_time_old ) );
				$to_time     = explode( ':', $to_time_old );
				$to_hour     = $to_time[0];
				$to_minute   = $to_time[1];
			}

			// For old time slot.
			$from_hour_old   = gmdate( 'G', gmmktime( $from_hour, $from_minute, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) ) );
			$from_minute_old = gmdate( 'i ', gmmktime( $from_hour, $from_minute, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) ) );
			$to_hour_old     = gmdate( 'G', gmmktime( $to_hour, $to_minute, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) ) );
			$to_minute_old   = gmdate( 'i ', gmmktime( $to_hour, $to_minute, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) ) );

			// For new time slot.
			$from_hours = date( 'H:i', strtotime( $from_hours ) );
			$from_time   = explode( ':', $from_hours );
			$from_hour   = $from_time[0];
			$from_minute = $from_time[1];

			$to_time   = '';
			$to_hour   = 0;
			$to_minute = 0;

			if ( '' !== $to_hours ) {
				$to_hours = date( 'H:i', strtotime( $to_hours ) );
				$to_time   = explode( ':', $to_hours );
				$to_hour   = $to_time[0];
				$to_minute = $to_time[1];
			}
			$from_hour_new   = gmdate( 'G', gmmktime( $from_hour, $from_minute, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) ) );
			$from_minute_new = gmdate( 'i ', gmmktime( $from_hour, $from_minute, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) ) );
			$to_hour_new     = gmdate( 'G', gmmktime( $to_hour, $to_minute, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) ) );
			$to_minute_new   = gmdate( 'i ', gmmktime( $to_hour, $to_minute, 0, gmdate( 'm' ), gmdate( 'd' ), gmdate( 'Y' ) ) );

			$timeslot_present = 'no';
			$added_days       = '';
			foreach ( $timeslot_new_arr as $key => $value ) {
				$timeslot_present = 'no';

				$fh = $value['fh'];
				$fm = $value['fm'];
				$th = $value['th'];
				$tm = $value['tm'];

				if ( 'weekdays' === $value['tv'] &&
					gettype( json_decode( $value['dd'] ) ) === 'array' &&
					count( json_decode( $value['dd'] ) ) > 0 ) {
					$dd = json_decode( $value['dd'] );
					
					if ( 'all' === $selected_dates &&
						$fh === $from_hour_old &&
						$fm === $from_minute_old &&
						$th === $to_hour_old &&
						$tm === $to_minute_old ) {
						$timeslot_present = 'yes';
					} else {

						foreach ( $dd as $id => $day ) {
							if ( $selected_dates === $day &&
							$fh === $from_hour_old &&
							$fm === $from_minute_old &&
							$th === $to_hour_old &&
							$tm === $to_minute_old ) {
								$timeslot_present = 'yes';
								$added_days       = $dd;
								break;
							}
						}
					}
				} elseif ( 'specific_dates' === $value['tv'] ) {
					$dd = json_decode( $value['dd'] );
					foreach ( $dd as $id => $day ) {
						if ( $selected_dates === $day &&
						$fh === $from_hour_old &&
						$fm === $from_minute_old &&
						$th === $to_hour_old &&
						$tm === $to_minute_old ) {
							$timeslot_present = 'yes';
							break;
						}
					}
				}

				if ( 'yes' === $timeslot_present ) {
					$unset = false;
					if ( is_array( $added_days ) && count( $added_days ) > 1 ) {
						foreach ( $added_days as $id => $day ) {
							if ( $selected_dates === $day ) {
								unset( $added_days[ $id ] );
								$unset = true;
								break;
							}
						}
					}

					if ( $unset ) {
						$timeslot_new_arr[ $key ]['dd'] = wp_json_encode( array_values( $added_days ) );

						$timeslot_new_arr[] = array(
							'tv'                       => $time_slot_value,
							'dd'                       => wp_json_encode( array( $selected_dates ) ),
							'lockout'                  => $lockouttime,
							'additional_charges'       => $additional_charges,
							'additional_charges_label' => $additional_charges_label,
							'fh'                       => $from_hour_new,
							'fm'                       => $from_minute_new,
							'th'                       => $to_hour_new,
							'tm'                       => $to_minute_new,
						);
					} else {
						$timeslot_new_arr[ $key ] = array(
							'tv'                       => $time_slot_value,
							'dd'                       => wp_json_encode( array( $selected_dates ) ),
							'lockout'                  => $lockouttime,
							'additional_charges'       => $additional_charges,
							'additional_charges_label' => $additional_charges_label,
							'fh'                       => $from_hour_new,
							'fm'                       => $from_minute_new,
							'th'                       => $to_hour_new,
							'tm'                       => $to_minute_new,
						);
					}
				}
			}

			$timeslot_jarr = wp_json_encode( $timeslot_new_arr );

			update_option( 'orddd_delivery_time_slot_log', $timeslot_jarr );
			wp_send_json( 'success' );
		} else {
			wp_send_json( 'error' );
		}
	}
}

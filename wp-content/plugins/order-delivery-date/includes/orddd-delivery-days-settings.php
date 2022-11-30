<?php

/**
 * Display General Settings -> Specific Delivery Dates Settings in admin.
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Pro-for-WooCommerce/Admin/Settings/General
 * @since 2.8.3
 * @category Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class orddd_delivery_days_settings {
	
    /**
     * Callback for adding Delivery Dates tab settings
     */
	public static function orddd_delivery_days_admin_setting_callback() { 
		echo __( '<em>Add delivery charges (if applicable), maximum order deliveries per day and select the specific delivery date.</em>', 'order-delivery-date' );
	}
	
	/**
	 * Callback for adding Enable Specific date setting
	 *
	 * @param array $args Extra arguments for outputting the field
	 * @since 2.8.3
	 */
	
	public static function orddd_delivery_days_enable_callback( $args ) {
		$enable_delivery_dates = "";
		if ( get_option( 'orddd_enable_specific_delivery_dates' ) == 'on' ) {
			$enable_delivery_dates = "checked";
		}
		
		echo '<input type="checkbox" name="orddd_enable_specific_delivery_dates" id="orddd_enable_specific_delivery_dates" class="day-checkbox" ' . $enable_delivery_dates . '/>';
		
		$html = '<label for="orddd_enable_specific_delivery_dates"> ' . $args[0] . '</label>';
		echo $html;
	}
	
	/**
	 * Callback to add first Specific date field
	 *
	 * @param array $args Extra arguments for outputting the field
	 * @since 2.8.3
	 * 
	 */
	
	public static function orddd_delivery_days_datepicker_1_callback( $args ) {
		$currency_symbol = get_woocommerce_currency_symbol();
        $day_selected = get_option( 'start_of_week' );
				
		echo '<input readonly="" type="text" name="orddd_delivery_date_1" id="orddd_delivery_date_1" class="day-checkbox" placeholder="Select Date"/>' . $currency_symbol . '<input class="orddd_specific_charges" type="text" name="additional_charges_1" id="additional_charges_1" placeholder="Charges" disabled/>';
		echo '<input class="orddd_specific_charges_label" type="text" name="specific_charges_label_1" id="specific_charges_label_1" placeholder="Delivery Charges Label" disabled />';
		echo '<input class="orddd_max_orders_specific" type="number" min="0" step="1" name="orddd_max_orders_specific_1" id="orddd_max_orders_specific_1" placeholder="Max Order Deliveries" style="margin-top:6px;"/>';
								
		echo '<input type="hidden" name="start_of_week" id="start_of_week" value="' . $day_selected . '">';
	    $html = '<label for="orddd_delivery_date_1"> ' . $args[0] . '</label>';
	    echo $html;
	}
	
	/**
	 * Callback to add second Specific date field
	 *
	 * @param array $args Extra arguments for outputting the field
	 * @since 2.8.3
	 */
	
	public static function orddd_delivery_days_datepicker_2_callback( $args ) {
		$currency_symbol = get_woocommerce_currency_symbol();
        $day_selected = get_option( 'start_of_week' );
	
	    echo '<input readonly="" type="text" name="orddd_delivery_date_2" id="orddd_delivery_date_2" class="day-checkbox" placeholder="Select Date"/>' . $currency_symbol . '<input class="orddd_specific_charges" type="text" name="additional_charges_2" id="additional_charges_2" placeholder="Charges" disabled />';
		echo '<input class="orddd_specific_charges_label" type="text" name="specific_charges_label_2" id="specific_charges_label_2" placeholder="Delivery Charges Label" disabled />';
		echo '<input class="orddd_max_orders_specific" type="number" min="0" step="1" name="orddd_max_orders_specific_2" id="orddd_max_orders_specific_2" placeholder="Max Order Deliveries" style="margin-top:6px;" />';
	
	    $html = '<label for="orddd_delivery_date_2"> ' . $args[0] . '</label>';
	    echo $html;
	}
	
	/**
	 * Callback to add third Specific date field
	 *
	 * @param array $args Extra arguments for outputting the field
	 * @since 2.8.3
	 */
	
	public static function orddd_delivery_days_datepicker_3_callback( $args ) {
		$currency_symbol = get_woocommerce_currency_symbol();
        $day_selected = get_option( 'start_of_week' );
	
	    echo '<input readonly="" type="text" name="orddd_delivery_date_3" id="orddd_delivery_date_3" class="day-checkbox" placeholder="Select Date"/>' . $currency_symbol . '<input class="orddd_specific_charges" type="text" name="additional_charges_3" id="additional_charges_3" placeholder="Charges" disabled />';
        echo '<input class="orddd_specific_charges_label" type="text" name="specific_charges_label_3" id="specific_charges_label_3" placeholder="Delivery Charges Label" disabled />';
		echo '<input class="orddd_max_orders_specific" type="number" min="0" step="1" name="orddd_max_orders_specific_3" id="orddd_max_orders_specific_3" placeholder="Max Order Deliveries" style="margin-top:6px;" />';
	    $html = '<label for="orddd_delivery_date_3"> ' . $args[0] . '</label>';
	    echo $html;
	}
	
	/**
	 * Save the specific delivery dates
	 * 
	 * @param array $input 
	 * @return string $delivery_dates_str JSON Encoded value for specific delivery dates
	 * @since 2.8.3
	 */
	
	public static function orddd_delivery_dates_callback( $input ){
	    $holidays = get_option( 'orddd_delivery_dates' );
	    if ( $holidays == '' || $holidays == '{}' || $holidays == '[]' || $holidays == 'null' ){
	        $holidays_arr = array();
	    } else {
	        $holidays_arr = json_decode( $holidays );
	    }
	    $holidays_new_arr = $dates_temp = array();
	    foreach ( $holidays_arr as $key => $value ) {
	        foreach ( $value as $k => $v ) {
	            $temp_arr[ $k ] = $v;
	            if( $k == 'date' ) {
	                $dates_temp[] = $v;
	            }
	        }
	        $holidays_new_arr[] = $temp_arr;
	    }
	    $delivery_dates_arr = array();
	    if ( !isset( $holidays_new_arr ) ) {
	        $holidays_new_arr = array();
	    }
	    if ( isset( $_POST[ 'orddd_delivery_date_1' ] ) && $_POST[ 'orddd_delivery_date_1' ] != "" ) {
	        $date_1_arr = explode( "-", $_POST[ 'orddd_delivery_date_1' ] ); 
	        $tstmp1 = gmmktime( 0, 0, 0, $date_1_arr[ 0 ], $date_1_arr[ 1 ], $date_1_arr[ 2 ] );
	        $holiday_date_1 = date( ORDDD_HOLIDAY_DATE_FORMAT, $tstmp1 );
	        if( !in_array( $holiday_date_1, $dates_temp ) ) {
	            $delivery_dates_arr[ 'date' ] = $holiday_date_1;
	            if ( isset( $_POST[ 'additional_charges_1' ] ) ) {
	                $delivery_dates_arr[ 'fees' ] = $_POST[ 'additional_charges_1' ];
	            } else {
	                $delivery_dates_arr[ 'fees' ] = '0';
	            }
	            if ( isset( $_POST[ 'specific_charges_label_1' ] ) ) {
	                $delivery_dates_arr[ 'label' ] = $_POST[ 'specific_charges_label_1' ];
	            } else {
	                $delivery_dates_arr[ 'label' ] = 'Delivery Charges';
				}
				
				if( isset( $_POST['orddd_max_orders_specific_1'] ) ) {
	                $delivery_dates_arr[ 'max_orders' ] = $_POST[ 'orddd_max_orders_specific_1' ];
				}else {
					$delivery_dates_arr[ 'max_orders' ] = '0';
				}
	            array_push( $holidays_new_arr, $delivery_dates_arr );
	        }
	    }
	    if ( isset( $_POST[ 'orddd_delivery_date_2' ] ) && $_POST[ 'orddd_delivery_date_2' ] != "" ) {
	        $date_2_arr = explode( "-", $_POST[ 'orddd_delivery_date_2' ] );
	        $tstmp2 = gmmktime( 0, 0, 0, $date_2_arr[ 0 ], $date_2_arr[ 1 ], $date_2_arr[ 2 ] );
	        $holiday_date_2 = date( ORDDD_HOLIDAY_DATE_FORMAT, $tstmp2 );
	        if ( !in_array( $holiday_date_2, $dates_temp ) ) {
	            $delivery_dates_arr[ 'date' ] = $holiday_date_2;
	            if ( isset( $_POST[ 'additional_charges_2' ] ) ) {
	                $delivery_dates_arr[ 'fees' ] = $_POST[ 'additional_charges_2' ];
	            } else {
	                $delivery_dates_arr[ 'fees' ] = '0';
	            }
	            if ( isset( $_POST[ 'specific_charges_label_2' ] ) ) {
	                $delivery_dates_arr[ 'label' ] = $_POST[ 'specific_charges_label_2' ];
	            } else {
	                $delivery_dates_arr[ 'label' ] = 'Delivery Charges';
				}
				if( isset( $_POST['orddd_max_orders_specific_2'] ) ) {
	                $delivery_dates_arr[ 'max_orders' ] = $_POST[ 'orddd_max_orders_specific_2' ];
				}else {
					$delivery_dates_arr[ 'max_orders' ] = '0';
				}
	            array_push( $holidays_new_arr, $delivery_dates_arr );
	        }
	    }
	    if ( isset( $_POST[ 'orddd_delivery_date_3' ] ) && $_POST[ 'orddd_delivery_date_3' ] != "" ) {
	        $date_3_arr = explode( "-", $_POST[ 'orddd_delivery_date_3' ] );
	        $tstmp3 = gmmktime( 0, 0, 0, $date_3_arr[ 0 ], $date_3_arr[ 1 ], $date_3_arr[ 2 ] );
	        $holiday_date_3 = date( ORDDD_HOLIDAY_DATE_FORMAT, $tstmp3 );
	        if ( !in_array( $holiday_date_3, $dates_temp ) ) {
	            $delivery_dates_arr[ 'date' ] = $holiday_date_3;
	            if ( isset( $_POST[ 'additional_charges_3' ] ) ) {
	                $delivery_dates_arr[ 'fees' ] = $_POST[ 'additional_charges_3' ];
	            } else {
	                $delivery_dates_arr[ 'fees' ] = '0';
	            }
	            if ( isset( $_POST[ 'specific_charges_label_3' ] ) ) {
	                $delivery_dates_arr[ 'label' ] = $_POST[ 'specific_charges_label_3' ];
	            } else {
	                $delivery_dates_arr[ 'label' ] = 'Delivery Charges';
				}
				if( isset( $_POST['orddd_max_orders_specific_3'] ) ) {
	                $delivery_dates_arr[ 'max_orders' ] = $_POST[ 'orddd_max_orders_specific_3' ];
				}else {
					$delivery_dates_arr[ 'max_orders' ] = '0';
				}
	            array_push( $holidays_new_arr, $delivery_dates_arr );
	        }
	    }
	    $delivery_dates_str = json_encode( $holidays_new_arr );
	    return $delivery_dates_str;
	}
}
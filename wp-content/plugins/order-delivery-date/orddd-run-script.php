<?php 

/**
 * Order Delivery Date Pro for WooCommerce
 *
 * Deletes all the past lockout data from the options variables when the cron is run.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Pro-for-WooCommerce/Script
 * @since       4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $wpdb;

$gmt = false;
if( has_filter( 'orddd_gmt_calculations' ) ) {
    $gmt = apply_filters( 'orddd_gmt_calculations', '' );
}
$current_time = current_time( 'timestamp', $gmt );

/**
 * To delete previous time slot in the orddd_lockout_time_slot log till current date
 * @since 4.0
 */
$lockout_time_arr = $lockout_time = '';
$lockout_time = get_option( 'orddd_lockout_time_slot' );
if ( $lockout_time != '' && $lockout_time != '{}' && $lockout_time != '[]' && $lockout_time != 'null' ) {
   $lockout_time_arr = json_decode( $lockout_time );
   $lockout_time_new_arr = array();
    foreach ( $lockout_time_arr as $k => $v ) {
        $timestamp = strtotime( $v->d );
        $current_timestamp = strtotime( date( 'd-m-Y', $current_time ) );
        if( $timestamp >= $current_timestamp ) {
            $lockout_time_new_arr[] = array( 'o' => $v->o, 't' => $v->t, 'd' => $v->d );
        }
    }
    $lockout_time_jarr = json_encode( $lockout_time_new_arr );
    update_option( 'orddd_lockout_time_slot', $lockout_time_jarr );
}

/**
* To Delete previous dates in the orddd_lockout_days log until current date
* @since 4.0
*/
$lockout_days = $lockout_days_arr = '';
$lockout_days = get_option( 'orddd_lockout_days' );
if ( $lockout_days != '' && $lockout_days != '{}' && $lockout_days != '[]' && $lockout_days != 'null' ) {
    $lockout_days_arr = json_decode( $lockout_days );
    $lockout_days_new_arr = array();
    foreach ( $lockout_days_arr as $k1 => $v1 ) {
        $date_arr = explode( '-', $v1->d );
        $m = $date_arr[ 0 ]; $d = $date_arr[ 1 ]; $y = $date_arr[ 2 ];
        $date_timestamp = gmmktime( 0, 0 ,0 , $m, $d, $y );
        $current_date_timestamp = strtotime( date( 'd-m-Y', $current_time ) ); // change it to the wordpress current timestamp
        if( $date_timestamp >= $current_date_timestamp ) {
            $lockout_days_new_arr[] = array( 'o' => $v1->o, 'd' => $v1->d );
        }
    }
    $lockout_days_jarr = json_encode( $lockout_days_new_arr );
    update_option( 'orddd_lockout_days', $lockout_days_jarr );
}

/**
* To delete previous time slot and date for shipping methods in the orddd_lockout_time_slotand orddd_lockout_days log log till current date
* @since 4.0
*/

$results = orddd_common::orddd_get_shipping_settings();

$shipping_settings = array();
$shipping_lockout_time_new_arr = array();
$shipping_lockout_time = '';
$shipping_lockout_time_arr = '';
$lockout_date_array = '';
$lockout_date_arr = '';

if( get_option( 'orddd_enable_shipping_based_delivery' ) == 'on' ) { 
    foreach ( $results as $key => $value ) {
        $shipping_settings = get_option( $value->option_name );
        
        $shipping_lockout_time = $lockout_date_array = "";
        if( isset( $shipping_settings[ 'orddd_lockout_time_slot' ] ) ) {
            $shipping_lockout_time = $shipping_settings[ 'orddd_lockout_time_slot' ];
        }
        if( isset( $shipping_settings[ 'orddd_lockout_date' ] ) ) {
            $lockout_date_array = $shipping_settings[ 'orddd_lockout_date' ];
        }
        
        if ( $shipping_lockout_time != '' && $shipping_lockout_time != '{}' && $shipping_lockout_time != '[]' && $shipping_lockout_time != 'null' ) {
            $shipping_lockout_time_arr = json_decode( $shipping_lockout_time );
            $shipping_lockout_time_new_arr = array();
            foreach ( $shipping_lockout_time_arr as $k2 => $v2 ) {
                $shipping_timestamp = strtotime( $v2->d );
                $shipping_current_timestamp = strtotime( date( 'd-m-Y', $current_time ) );
                if( $shipping_timestamp >= $shipping_current_timestamp ) {
                    $shipping_lockout_time_new_arr[] = array( 'o' => $v2->o, 't' => $v2->t, 'd' => $v2->d );
                }                
            }
            $shipping_settings[ 'orddd_lockout_time_slot' ] = json_encode( $shipping_lockout_time_new_arr );
            update_option( $value->option_name, $shipping_settings );
        }
        
        if ( $lockout_date_array != '' && $lockout_date_array != '{}' && $lockout_date_array != '[]' && $lockout_date_array != 'null' ) {
            $lockout_date_arr = json_decode( $lockout_date_array );
            $shipping_lockout_days_new_arr = array();
            foreach ( $lockout_date_arr as $k3 => $v3 ) {
                $date_arr = explode( '-', $v3->d );
                $month = $date_arr[ 0 ]; $day = $date_arr[ 1 ]; $year = $date_arr[ 2 ];
                $shipping_date_timestamp = gmmktime( 0, 0, 0, $month, $day, $year );
                $shipping_current_date_timestamp = strtotime( date( 'd-m-Y', $current_time ) );
                if( $shipping_date_timestamp >= $shipping_current_date_timestamp ) {
                    $shipping_lockout_days_new_arr[] = array( 'o' => $v3->o, 'd' => $v3->d );
                }
            }
            $shipping_settings[ 'orddd_lockout_date' ] = json_encode( $shipping_lockout_days_new_arr );
            update_option( $value->option_name, $shipping_settings );
        }
    }
}
?>
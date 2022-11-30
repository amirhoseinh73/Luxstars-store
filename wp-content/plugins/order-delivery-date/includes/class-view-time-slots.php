<?php 

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Display Time slots table on General Settings -> Time slots link in admin.
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Pro-for-WooCommerce/Admin/Settings/General
 * @since 2.8.3
 */

class ORDDD_View_Time_Slots_Table extends WP_List_Table {

	/**
	 * URL of this page
	 *
	 * @var string
	 * @since 1.4.1
	 */
	public $base_url;
	
	/**
	 * Get things started
	 *
	 * @see WP_List_Table::__construct()
	 */
	public function __construct() {

		global $status, $page;
		// Set parent defaults
		parent::__construct( array(
                'singular' => __( 'time_slot', 'order-delivery-date' ), //singular name of the listed records
                'plural'   => __( 'time_slots', 'order-delivery-date' ), //plural name of the listed records
				'ajax'      => false             			// Does this table support ajax?
		) );
		$this->process_bulk_action();
		$this->base_url = admin_url( 'admin.php?page=order_delivery_date&action=general_settings&section=time_slot' );
	}
	
	/**
	 * Add delete option in the bulk actions dropdown
	 * 
	 * @since 2.8.3
	 */
	public function get_bulk_actions() {
	    return array(
	        'orddd_delete' => __( 'Delete', 'order-delivery-date' )
	    );
	}
	
	/**
	 * Add the check box for the items to select 
	 * 
	 * @param string $item 
	 * @return string
	 * @since 2.8.3
	 **/
	function column_cb( $item ) {
	    $dd = '';
	    if( isset( $item->dd ) ) {
	        $dd = $item->dd;
	        return sprintf(
	            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
	            'time_slot',
	            $dd . ',' . $item->fh . ',' . $item->fm . ',' . $item->th . ',' . $item->tm . ',' . $item->tv
	        );
	    }
	}
	
	/**
	 * Prepare items to display in the table
	 * 
	 * @since 2.8.3
	 */
	public function orddd_prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = array(); // No hidden columns
		$data     = $this->orddd_time_slot_data();
		$sortable = array();
		$status   = isset( $_GET['status'] ) ? $_GET['status'] : 'any';
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items = $data;
	}
	
	/**
	 * Return columns to add in the table
	 * 
	 * @return array $columns Columns to be displayed in the table
	 * @since 2.8.4
	 */
	public function get_columns() {
		$columns = array(
		        'cb'                 					 =>  '<input type="checkbox" />',
				'delivery_days_dates' 					 => __( 'Delivery Days/Dates', 'order-delivery-date' ),
				'time_slot'  							 => __( 'Time Slot', 'order-delivery-date' ),
				'maximum_order_deliveries_per_time_slot' => __( 'Maximum Order Deliveries per time slot', 'order-delivery-date' ),
		        'additional_charges_for_time_slot'       => __( 'Additional Charges for time slot', 'order-delivery-date' ),
				'time_slot_checkout_label'  			 => __( 'Checkout label', 'order-delivery-date' ),
				'timeslot_actions'						 => __( 'Actions'),
		);
		return apply_filters( 'orddd_shipping_settings_table_columns', $columns );
	}
	
	/**
	 * Displays the data in the table
	 * 
	 * @return array $return_time_slot Data of all time slots
	 * @since 2.8.3
	 */
	public function orddd_time_slot_data() { 
		global $wpdb, $woocommerce, $orddd_weekdays;	

		$time_format = get_option( 'orddd_delivery_time_format' );
        $time_format_to_show = 'H:i';
        if ( $time_format == '1' ) {
            $time_format_to_show = 'h:i A';
        }

        $currency_symbol = get_woocommerce_currency_symbol();

		$return_time_slot = array();		
		if ( isset( $_POST[ 'current_date' ] ) ) {
		    $current_date = $_POST[ 'current_date' ];
		}
		
		$lockout_time = get_option( 'orddd_lockout_time_slot' );
		$existing_timeslots_str = get_option( 'orddd_delivery_time_slot_log' );
		$existing_timeslots_arr = json_decode( $existing_timeslots_str );
		if( $existing_timeslots_arr == 'null' ) {
	        $existing_timeslots_arr = array();
	    }	

		if ( is_array( $existing_timeslots_arr ) && count( $existing_timeslots_arr ) > 0 ) {
		    $i = 0;
		    // Sort the multidimensional array
	     	usort( $existing_timeslots_arr, array( 'orddd_common', 'orddd_custom_sort' ) );
		    foreach ( $existing_timeslots_arr as $k => $v ) {
		        $from_time = $v->fh . ":" . $v->fm;
		        $ft =  date( $time_format_to_show, strtotime( $from_time ) );
		        if ( $v->th != 00 || ( $v->th == 00 && $v->tm != 00 ) ) {
		            $to_time = $v->th . ":" . $v->tm;
		            $tt = date( $time_format_to_show, strtotime( $to_time ) );
		            $key = $ft . " - " . $tt;
		        } else {
		            $key = $ft;
		        }

		        $additional_charges = 0;
		        if( isset(  $v->additional_charges ) ) {
		            $additional_charges =  $v->additional_charges;
		        }
			
				$additional_charges_label = "";
		        if( isset(  $v->additional_charges_label ) ) {
		            $additional_charges_label =  $v->additional_charges_label;
		        }

		        if ( gettype( json_decode( $v->dd ) ) == 'array' && count( json_decode( $v->dd ) ) > 0 ) {
		            $dd = json_decode( $v->dd );
		            foreach( $dd as $dkey => $dval ) {
						$return_time_slot[ $i ] = new stdClass();
						$return_time_slot[ $i ]->id = $i + 1;
		                $return_time_slot[ $i ]->fh = $v->fh;
		                $return_time_slot[ $i ]->fm = $v->fm;
		                $return_time_slot[ $i ]->th = $v->th;
		                $return_time_slot[ $i ]->tm = $v->tm;
		                $return_time_slot[ $i ]->tv = $v->tv;
		                if ( isset( $v->tv ) && $v->tv == 'specific_dates' ) {
		                    $date = explode( "-", $dval );
		                    $date_to_display = date( "m-d-Y", gmmktime( 0, 0, 0, $date[0], $date[1], $date[2] ) );
		                    $return_time_slot[ $i ]->delivery_days_dates = $date_to_display;
		                    $return_time_slot[ $i ]->dd = $dval;
    		                $return_time_slot[ $i ]->time_slot = $key;
                            $return_time_slot[ $i ]->maximum_order_deliveries_per_time_slot = $v->lockout;
                            if( $additional_charges != "" ) {
                                $return_time_slot[ $i ]->additional_charges_for_time_slot = $currency_symbol . '' . $additional_charges;
                            } else {
                                $return_time_slot[ $i ]->additional_charges_for_time_slot = '';
                            }
							$return_time_slot[ $i ]->time_slot_checkout_label = $additional_charges_label;
							$return_time_slot[ $i ]->timeslot_actions = sprintf( '<a href="#" class="edit_timeslot"><span class="dashicons dashicons-edit"></span></a>' );
		                } else {
					        if( isset( $orddd_weekdays[ $dval ] ) ) {
					            $return_time_slot[ $i ]->delivery_days_dates = $orddd_weekdays[ $dval ];
					            $return_time_slot[ $i ]->dd = $dval;
					        } else if ( $dval == 'all' ) {
					            $return_time_slot[ $i ]->delivery_days_dates = "All";
					            $return_time_slot[ $i ]->dd = $dval;
					        } else {
					            $return_time_slot[ $i ]->delivery_days_dates = "";
					            $return_time_slot[ $i ]->dd = "";
					        }
					        $return_time_slot[ $i ]->time_slot = $key;
					        $return_time_slot[ $i ]->maximum_order_deliveries_per_time_slot = $v->lockout;
                            if( $additional_charges != "" ) {
								$return_time_slot[ $i ]->additional_charges_for_time_slot = $currency_symbol . '' . $additional_charges;
								$return_time_slot[ $i ]->additional_charges_without_currency = $additional_charges;
								
                            } else {
								$return_time_slot[ $i ]->additional_charges_for_time_slot = '';
								$return_time_slot[ $i ]->additional_charges_without_currency = '';
								
                            }
							$return_time_slot[ $i ]->time_slot_checkout_label = $additional_charges_label;
							$return_time_slot[ $i ]->timeslot_actions = sprintf( '<a href="#" class="edit_timeslot"><span class="dashicons dashicons-edit"></span></a>' );
						}
		                $i++;
		            }
		        } else {
		            $return_time_slot[ $i ] = new stdClass();
		            $return_time_slot[ $i ]->fh = $v->fh;
		            $return_time_slot[ $i ]->fm = $v->fm;
		            $return_time_slot[ $i ]->th = $v->th;
		            $return_time_slot[ $i ]->tm = $v->tm;
		            $return_time_slot[ $i ]->tv = $v->tv;
		            $dd = $v->dd;
                    if( isset( $orddd_weekdays[ $dd ] ) ) {
                        $return_time_slot[ $i ]->delivery_days_dates = $orddd_weekdays[ $dd ];
                        $return_time_slot[ $i ]->dd = $dd;
					} else if ( $dd == 'all' ) {
					    $return_time_slot[ $i ]->delivery_days_dates = "All";
					    $return_time_slot[ $i ]->dd = $dd;
					} else {
					    $return_time_slot[ $i ]->delivery_days_dates = "";
					    $return_time_slot[ $i ]->dd = "";
					}
					$return_time_slot[ $i ]->time_slot = $key;
					$return_time_slot[ $i ]->maximum_order_deliveries_per_time_slot = $v->lockout;
					if( $additional_charges != "" ) {
					   $return_time_slot[ $i ]->additional_charges_for_time_slot = $currency_symbol . '' . $additional_charges;
					   $return_time_slot[ $i ]->additional_charges_without_currency = $additional_charges;
					}  else {
						$return_time_slot[ $i ]->additional_charges_for_time_slot = '';
						$return_time_slot[ $i ]->additional_charges_without_currency = '';
						
                    }
					$return_time_slot[ $i ]->time_slot_checkout_label = $additional_charges_label;
					$return_time_slot[ $i ]->timeslot_actions = sprintf( '<a href="#" class="edit_timeslot"><span class="dashicons dashicons-edit"></span></a>' );
					$i++;
		        }
            }
        }
		return apply_filters( 'orddd_shipping_settings_table_data', $return_time_slot );
	}
	
	/**
	 * Add Edit and Delete link in each row of the table data
	 * 
	 * @param resource $shipping_settings
	 * @param string $column_name
	 * @return array
	 */
	public function column_default( $timeslot_settings, $column_name ) {
	    $value = isset( $timeslot_settings->$column_name ) ? $timeslot_settings->$column_name : '';
		return apply_filters( 'bkap_booking_table_column_default', $value, $timeslot_settings, $column_name );
	}	
}
?>
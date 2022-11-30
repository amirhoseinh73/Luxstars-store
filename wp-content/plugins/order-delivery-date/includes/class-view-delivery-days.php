<?php 

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Display Specific Delivery Dates Table in General Settings in admin.
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Pro-for-WooCommerce/Admin/Settings/General
 * @since 1.4.1
 */

class ORDDD_View_Delivery_Dates_Table extends WP_List_Table {

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
                'singular' => __( 'delivery_date', 'order-delivery-date' ), //singular name of the listed records
                'plural'   => __( 'delivery_dates', 'order-delivery-date' ), //plural name of the listed records
				'ajax'      => false             			// Does this table support ajax?
		) );
		$this->process_bulk_action();
		$this->base_url = admin_url( 'admin.php?page=order_delivery_date&action=general_settings&section=delivery_dates' );
	}
	
	public function get_bulk_actions() {
	    return array(
	        'orddd_delete' => __( 'Delete', 'order-delivery-date' )
	    );
	}
	
	/**
	 * Add the check box for the items to bulk select
	 * 
	 * @param $item
	 * @since 1.4.1
	 */
	function column_cb( $item ){
	    $row_id = '';
	    if( isset( $item->dd ) && "" != $item->dd ){
	        $row_id = $item->dd;
	        return sprintf(
	            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
	            'delivery_date',
	            $row_id
	        );
	    }
	}
	
	/**
	 * Prepare items to display in the table
	 * 
	 * @since 1.4.1
	 */
	public function orddd_prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = array(); // No hidden columns
		$data     = $this->orddd_shipping_settings_data();
		$sortable = array();
		$status   = isset( $_GET['status'] ) ? $_GET['status'] : 'any';
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items = $data;
	}
	
	/**
	 * Return columns to add in the table
	 * 
	 * @return array $columns Name of the columns in the table
	 * @since 1.4.1
	 */
	public function get_columns() {
		$columns = array(
		        'cb'                 =>  '<input type="checkbox" />',
				'delivery_date'                    => __( 'Date', 'order-delivery-date' ),
				'delivery_date_additional_charges' => __( 'Additional Charges', 'order-delivery-date' ),
				'delivery_date_checkout_label'     => __( 'Label', 'order-delivery-date' ),
				'delivery_date_lockout'			   => __( 'Maximum Orders', 'order-delivery-date' ),
		);
		return apply_filters( 'orddd_delivery_dates_table_columns', $columns );
	}
	
	/**
	 * Displays the data in the table
	 * 
	 * @return array $return_delivery_dates 
	 * @since 1.4.1
	 */
	
	public function orddd_shipping_settings_data() { 
		global $wpdb, $woocommerce, $orddd_weekdays;	
		$return_delivery_dates = $holidays_arr = array();
		$delivery_dates = get_option( 'orddd_delivery_dates' );
		if ( $delivery_dates != '' && $delivery_dates != '{}' && $delivery_dates != '[]' && $delivery_dates != 'null' ) {
		    $holidays_arr = json_decode( $delivery_dates );
		}
		
		$array_format = get_option( 'orddd_specific_array_format' );
		if ( isset( $array_format ) && $array_format == '' ) {
		    $array_format = "N";
		}

		if ( $array_format == "N" ) {
		    $holidays_new_arr = array();
		    foreach ( $holidays_arr as $key => $value ) {
		        $temp_arr[ 'date' ] = $value;
		        $temp_arr[ 'fees' ] = '0';
		        $temp_arr[ 'label' ] = '';
		        $holidays_new_arr[] = $temp_arr;
		    }
		    $delivery_dates_str = json_encode( $holidays_new_arr );
		    update_option( 'orddd_delivery_dates', $delivery_dates_str );
		    update_option( 'orddd_specific_array_format', 'Y' );
		    $delivery_dates = get_option( 'orddd_delivery_dates' );
		    if ( $delivery_dates != '' && $delivery_dates != '{}' && $delivery_dates != '[]' && $delivery_dates != 'null' ) {
		        $holidays_arr = json_decode( $delivery_dates );
		    }
		}

		// Sort the specific dates array
     	usort( $holidays_arr, array( 'orddd_common', 'orddd_sort_specific_dates' ) );

		$currency_symbol = get_woocommerce_currency_symbol();
		$specific_date_count = 0;
		foreach ( $holidays_arr as $key => $value ) {
		    $date_arr = explode( "-", $value->date );
		    $date_to_display = date( "m-d-Y", gmmktime( 0, 0, 0, $date_arr[0], $date_arr[1], $date_arr[2] ) ); 
		    $return_delivery_dates[ $key ] = new stdClass();
		    $return_delivery_dates[ $key ]->delivery_date = $date_to_display;
		    $return_delivery_dates[ $key ]->dd = $value->date;
		    if( isset( $value->fees ) && $value->fees != '' ) {
                $return_delivery_dates[ $key ]->delivery_date_additional_charges = $currency_symbol . $value->fees;
		    } else {
		        $return_delivery_dates[ $key ]->delivery_date_additional_charges = '';
		    }
		    
            if( isset( $value->label ) ) {
                $return_delivery_dates[ $key ]->delivery_date_checkout_label = $value->label;
            } else {
                $return_delivery_dates[ $key ]->delivery_date_checkout_label = "";
			}
			
			if( isset( $value->max_orders ) ) {
                $return_delivery_dates[ $key ]->delivery_date_lockout = $value->max_orders;
            } else {
                $return_delivery_dates[ $key ]->delivery_date_lockout = "";
            }
        }
        return apply_filters( 'orddd_delivery_dates_table_data', $return_delivery_dates );
	}

	/**
	 * Add Edit and Delete link in each row of the table data
	 * 
	 * @param resource $shipping_settings
	 * @param string $column_name
	 * @return array
	 * @since 1.4.1
	 */
	public function column_default( $delivery_date_settings, $column_name ) {
	    $value = isset( $delivery_date_settings->$column_name ) ? $delivery_date_settings->$column_name : '';
		return apply_filters( 'bkap_booking_table_column_default', $value, $delivery_date_settings, $column_name );
	}	
}
?>
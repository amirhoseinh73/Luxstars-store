<?php 
/**
 * Order Delivery Date Pro for WooCommerce
 *
 * Handles the display and filtering of delivery details on WooCommerce->Orders in admin.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Pro-for-WooCommerce/Filter
 * @since       2.7
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

include_once( dirname( __FILE__ ) . '/orddd-common.php' );

/**
 * orddd_filter Class
 *
 * @class orddd_filter
 */
class orddd_filter {

	/**
	 * Default Constructor
	 *
	 * @since 8.1
	 */
	public function __construct() {

		//Delivery Date & Time on WooCommerce Edit Order page in Admin
		if ( get_option( 'orddd_delivery_date_fields_on_checkout_page' ) == 'billing_section' || get_option( 'orddd_delivery_date_fields_on_checkout_page' ) == 'after_your_order_table' || get_option( 'orddd_delivery_date_fields_on_checkout_page' ) == 'custom' ) {
		    add_action( 'woocommerce_admin_order_data_after_billing_address',  array( &$this, 'orddd_display_delivery_date_admin_order_meta') , 10, 1 );
		    add_action( 'woocommerce_admin_order_data_after_billing_address',  array( &$this, 'orddd_display_time_slot_admin_order_meta' ), 10, 1 );
		} else if ( get_option( 'orddd_delivery_date_fields_on_checkout_page' ) == 'shipping_section'|| get_option( 'orddd_delivery_date_fields_on_checkout_page' ) == 'before_order_notes' || get_option( 'orddd_delivery_date_fields_on_checkout_page' ) == 'after_order_notes' ) {
		    add_action( 'woocommerce_admin_order_data_after_shipping_address', array( &$this, 'orddd_display_delivery_date_admin_order_meta') , 10, 1 );
		    add_action( 'woocommerce_admin_order_data_after_shipping_address', array( &$this, 'orddd_display_time_slot_admin_order_meta' ), 10, 1 );   
		}
			
		// Delivery date & Time in list of orders on WooCommerce Edit Order page in Admin
		if ( get_option( 'orddd_show_column_on_orders_page_check' ) == 'on' ) {
		    add_filter( 'manage_edit-shop_order_columns',          array( &$this, 'orddd_woocommerce_order_delivery_date_column' ), 20, 1 );
		    add_action( 'manage_shop_order_posts_custom_column',   array( &$this, 'orddd_woocommerce_custom_column_value' ), 20, 1 );
		    add_filter( 'manage_edit-shop_order_sortable_columns', array( &$this, 'orddd_woocommerce_custom_column_value_sort' ) );
		    add_filter( 'request',                                 array( &$this, 'orddd_woocommerce_delivery_date_orderby' ) );			     
		}
		
		//Filter to sort orders based on Delivery dates 
		if ( get_option( 'orddd_show_filter_on_orders_page_check' ) == 'on' ) {
		    add_action( 'restrict_manage_posts',                array( &$this, 'orddd_restrict_orders' ), 15 );
		    add_filter( 'request',                              array( &$this, 'orddd_add_filterable_field' ) );
		    add_filter( 'woocommerce_shop_order_search_fields', array( &$this, 'orddd_add_search_fields' ) );
		}

		// Delivery date, Delivery Time & Pickup Location in Order Preview in Admin
		add_filter( 'woocommerce_admin_order_preview_get_order_details', array( &$this, 'orddd_admin_order_preview_add_delivery_date' ), 20, 2 );

		// Allow Free Delivery option added on the Add new Coupon page. 
		add_action( 'woocommerce_coupon_options', array( &$this, 'orddd_allow_free_delivery_coupon_option' ), 10, 2 );
		add_action( 'woocommerce_coupon_options_save', array( &$this, 'orddd_save_free_delivery_coupon_save' ), 10, 2 );
	}

	/**
	 * This function is used to add the custom plugin column 
	 * Delivery Date on WooCommerce->Orders page.
	 * 
	 * @param array $columns - The Existing columns for the WooCommerce->Orders table.
	 * @return array $new_columns - Updated list of column names.
	 * 
	 * @hook manage_edit-shop_order_columns
	 * @since 2.7
	 */
	public static function orddd_woocommerce_order_delivery_date_column( $columns ) {
		// get all columns up to and excluding the 'order_actions' column
		$new_columns = array();
		foreach ( $columns as $name => $value ) {
			if ( $name == 'wc_actions' ) {
				prev( $columns );
				break;
			}
			$new_columns[ $name ] = $value;
		}
		// inject our columns
		$new_columns[ 'order_delivery_date' ] = get_option( 'orddd_delivery_date_field_label' );
		// add the 'order_actions' column, and any others
		foreach ( $columns as $name => $value ) {
			$new_columns[ $name ] = $value;
		}
		return $new_columns;
	}

	/**
	 * This function echoes the delivery details to the 
	 * 'Delivery Date' column on WooCommerce->Orders for each order.
	 * 
	 * @param string $column - Column Name
	 * 
	 * @hook manage_shop_order_posts_custom_column
	 * @since 2.7
     */
	public static function orddd_woocommerce_custom_column_value( $column ) {
		global $post, $orddd_date_formats;
		if ( $column == 'order_delivery_date' ) {
			$delivery_date_formatted = orddd_common::orddd_get_order_delivery_date( $post->ID  );   		
    		if ( $delivery_date_formatted != "" ) {
    			echo $delivery_date_formatted;
    			$time_slot = orddd_common::orddd_get_order_timeslot( $post->ID );
            	echo '<p>' . $time_slot . '</p>';
    		}
		}
		do_action( 'orddd_add_value_to_woocommerce_custom_column', $column, $post->ID );
	}

	/**
	 * Adds the Delivery Date column in WooCommerce->Orders
     * as a sortable column. Mentions the meta key present in
     * post meta table that can be used for sorting.
     * 
     * @param array $columns - List of sortable columns
     * @return array - Sortable columns with the plugin column included.array
     * 
     * @hook manage_edit-shop_order_sortable_columns
     * @since 2.7
	 */
	public static function orddd_woocommerce_custom_column_value_sort( $columns ) {
		$columns[ 'order_delivery_date' ] = '_orddd_timestamp';
		return $columns;
	}

	/**
	 * Delivery date column orderby. 
	 * 
	 * Helps WooCommerce understand using the value based on which a column should be sorted.
	 * The delivery date is stored as a timestamp in the _orddd_timestamp variable in wp_postmeta
	 * 
	 * @param array $vars - Query variables
	 * @return array $vars - Updated Query variables.
	 * 
	 * @hook request
	 * @since 2.7
	 */
	public static function orddd_woocommerce_delivery_date_orderby( $vars ) {
		if( get_option( "orddd_show_column_on_orders_page_check" ) == 'on' ) {
            $delivery_field_label = '_orddd_timestamp';
            if ( isset( $vars[ 'orderby' ] ) && $vars[ 'orderby' ] != '' ) {
                if ( $delivery_field_label == $vars[ 'orderby' ] ) {
                    $sorting_vars = array( 'orderby'  => array( 'meta_value_num' => $vars[ 'order' ], 'date' => 'ASC' ) );
                    if ( !isset( $_GET[ 'order_delivery_date_filter' ] ) || $_GET['order_delivery_date_filter'] == '') {
                        $sorting_vars[ 'meta_query' ] = array(  'relation' => 'OR', 
                            array (
								'key'	  => $delivery_field_label, 
								'value'	  => '', 
								'compare' => 'NOT EXISTS'
							),
							array (
									'key'	  => $delivery_field_label,
									'compare' => 'EXISTS'
								)
							);
                    }
                    $vars = array_merge( $vars, $sorting_vars );
                }
            } else if( 'on' == get_option( 'orddd_enable_default_sorting_of_column' ) ) {
            	//Sorting by descending delivery dates will only be done on the WooCommerce Orders page. 
                if ( isset( $vars[ 'post_type' ] ) && 'shop_order' != $vars[ 'post_type' ] ) {
                    return $vars;
                }
                $sorting_vars = array(
                    'orderby'  => array( 'meta_value_num' => 'DESC', 'date' => 'ASC' ),
                    'order'	   => 'DESC' );
                if ( !isset( $_GET[ 'order_delivery_date_filter' ] ) || $_GET[ 'order_delivery_date_filter'     ] == '' ) {
                    $sorting_vars[ 'meta_query' ] = array( 'relation' => 'OR', 
    						array (
    								'key'	  => $delivery_field_label, 
    								'value'	  => '', 
    								'compare' => 'NOT EXISTS'
    							),
    						array (
    								'key'	  => $delivery_field_label,
    								'compare' => 'EXISTS'
    							)
                        );
                }
                $vars = array_merge( $vars, $sorting_vars );
            }
		}
		return $vars;
	}
	
	/**
	 * Prints a dropdown to filter the orders based on Delivery Dates
	 * in WooCommerce->Orders.
	 * 
	 * @hook restrict_manage_posts
	 * @since 2.7
	 */
	public static function orddd_restrict_orders() {
		global $typenow, $wpdb, $wp_locale;

		if ( 'shop_order' != $typenow ) {
			return;
		}

		$gmt = false;
		if ( has_filter( 'orddd_gmt_calculations' ) ) {
			$gmt = apply_filters( 'orddd_gmt_calculations', '' );
		}

		$current_time      = current_time( 'timestamp', $gmt );
		$javascript        = '';
		$filter_field_name = 'order_delivery_date_filter';
		$db_field_name     = '_orddd_timestamp';
		$date_display      = 'display:none;';
		$startdate         = '';
		$enddate           = '';

		$months = $wpdb->get_results( $wpdb->prepare( "
		SELECT YEAR( FROM_UNIXTIME( meta_value ) ) as year, MONTH( FROM_UNIXTIME( meta_value ) ) as month, CAST( meta_value AS UNSIGNED ) AS meta_value_num
		FROM " . $wpdb->postmeta . "
		WHERE meta_key = %s
		GROUP BY year, month
		ORDER BY meta_value_num DESC", $db_field_name ) );

		$month_count = 0;
		if ( is_array( $months ) ) {
			$month_count = count( $months );			
		}

		if ( ! $month_count || ( 1 == $month_count && 0 == $months[0]->month ) ) {
			return;
		}

		if ( isset( $_GET[ $filter_field_name ] ) && 'today' == $_GET[ $filter_field_name ] ) {
			$m = $_GET[ $filter_field_name ];
		} elseif ( isset( $_GET[ $filter_field_name ] ) && $_GET[ $filter_field_name ] == 'tomorrow' ) {
			$m = $_GET[ $filter_field_name ];
		} elseif ( isset( $_GET[ $filter_field_name ] ) && $_GET[ $filter_field_name ] == 'custom' ) {
			$m            = $_GET[ $filter_field_name ];
			$date_display = '';
			$startdate    = isset( $_GET[ 'orddd_custom_startdate' ] ) ? $_GET[ 'orddd_custom_startdate' ] : '';
			$enddate      = isset( $_GET[ 'orddd_custom_enddate' ] ) ? $_GET[ 'orddd_custom_enddate' ] : '';
		} else {
			$m = isset( $_GET[ $filter_field_name ] ) ? (int) $_GET[ $filter_field_name ] : 0;
		}

		$today_name          = __( 'Today', 'order-delivery-date' );
		$tomorrow_name       = __( 'Tomorrow', 'order-delivery-date' );
		$custom_filter_label = __( 'Custom', 'order-delivery-date' );

		$today_option = array( 'year' => date( 'Y', $current_time ), 
							   'month' => 'today', 
							   'meta_value_num' => $current_time, 
							   'month_name' => $today_name );

		$tomorrow_date = date( 'Y-m-d', strtotime( '+1 day', $current_time ) );
		$tomorrow_time = strtotime( $tomorrow_date );
		$tomorrow_option = array( 'year' => date( 'Y', $tomorrow_time ), 
								  'month' => 'tomorrow', 
								  'meta_value_num' => $tomorrow_time, 
								  'month_name' => $tomorrow_name );
		$custom          = array( 
								  'year' => '', 
								  'month' => 'custom', 
								  'meta_value_num' => '',
								  'month_name' => $custom_filter_label );
		array_unshift( $months, (object)$today_option, (object)$tomorrow_option, (object)$custom );
		?>
		<select name="order_delivery_date_filter" id="order_delivery_date_filter" class="orddd_filter">
			<option value=""><?php _e( "Show all Delivery Dates", "order-delivery-date" ); ?></option>
			<?php
			foreach ( $months as $arc_row ) {
				if ( $arc_row->month != 'today' && $arc_row->month != 'tomorrow' && $arc_row->month != 'custom' ) {
					if ( 0 == $arc_row->year || '1969' == $arc_row->year ) {
						continue;
					}
					$month = zeroise( $arc_row->month, 2 );
					$year = $arc_row->year;
					printf( '<option %s value="%s">%s</option>',
						selected( $m, $year . $month, false ),
						esc_attr( $arc_row->year . $month ),
						/* translators: 1: month name, 2: 4-digit year */
						sprintf( __( '%1$s %2$d', 'order-delivery-date' ), $wp_locale->get_month( $month ), $year )
					);
				} else {
					$arc_row->year = $year = '';
					$month = $arc_row->month;
					printf( '<option %s value="%s">%s</option>',
						selected( $m, $arc_row->month, false ),
						$arc_row->month,
						$arc_row->month_name
					);
				}
			}
		?>
		</select>

		<input type="text" name="orddd_custom_startdate" id="orddd_custom_startdate" class="orddd_datepicker" value="<?php echo $startdate; ?>" style="width:100px;<?php echo $date_display; ?>" placeholder="<?php esc_html_e( 'Start Date', 'woocommerce-booking' ); ?>" readonly>
		<input type="text" name="orddd_custom_enddate" id="orddd_custom_enddate" class="orddd_datepicker" value="<?php echo $enddate; ?>" style="width:100px;<?php echo $date_display; ?>" placeholder="<?php esc_html_e( 'End Date', 'woocommerce-booking' ); ?>" readonly>
		<?php
	}
	
	/**
	 * Filter the orders displayed in WooCommerce->Orders
	 * based on the Delivery Dates filter dropdown.
	 *
	 * @param array $vars - Query Variables
	 * @return array $vars - Updated Query Variables
	 * 
	 * @hook request
	 * @since 2.7
	 */
	public static function orddd_add_filterable_field( $vars ) {
		global $typenow;
		if ( 'shop_order' != $typenow ) {
			return $vars;
		}

		$gmt = false;
		if( has_filter( 'orddd_gmt_calculations' ) ) {
			$gmt = apply_filters( 'orddd_gmt_calculations', '' );
		}
		$current_time = current_time( 'timestamp', $gmt );

		$meta_queries = array( 'relation' => 'AND' );

		// if the field is filterable and selected by the user
		if ( isset( $_GET[ 'order_delivery_date_filter' ] ) && $_GET[ 'order_delivery_date_filter' ] ) {
			$date = $_GET[ 'order_delivery_date_filter' ];
			if ( $date == 'today' ) {
				// from the start to the end of the month
			    $current_date = date( 'Y-m-d', $current_time );
			     
			    $from_date = date( 'Y-m-d H:i:s', strtotime( $current_date . '00:00:00' ) );
			    $to_date = date( 'Y-m-d H:i:s', strtotime( $current_date . '23:59:59' ) );
			    
				$meta_queries[] = array(
					'key'     => '_orddd_timestamp',
					'value'   => array( strtotime( $from_date ), strtotime( $to_date ) ),
					'type'    => 'NUMERIC',
					'compare' => 'BETWEEN'
				);
			} else if ( $date == 'tomorrow' ) {				
				$current_date = date( 'Y-m-d', strtotime('+1 day', $current_time ) );
			     
			    $from_date = date( 'Y-m-d H:i:s', strtotime( $current_date . '00:00:00' ) );
			    $to_date = date( 'Y-m-d H:i:s', strtotime( $current_date . '23:59:59' ) );
			    
				$meta_queries[] = array(
					'key'     => '_orddd_timestamp',
					'value'   => array( strtotime( $from_date ), strtotime( $to_date ) ),
					'type'    => 'NUMERIC',
					'compare' => 'BETWEEN'
				);
			} else if ( $date == 'custom' ) {
				
				$current_date = date( 'Y-m-d', $current_time );
				$startdate    = isset( $_GET[ 'orddd_custom_startdate' ] ) && '' !== $_GET[ 'orddd_custom_startdate' ] ? $_GET[ 'orddd_custom_startdate' ] : $current_date;
				$enddate      = isset( $_GET[ 'orddd_custom_enddate' ] ) && '' !== $_GET[ 'orddd_custom_enddate' ] ? $_GET[ 'orddd_custom_enddate' ] : $startdate;
			    $from_date    = date( 'Y-m-d H:i:s', strtotime( $startdate . '00:00:00' ) );
			    $to_date      = date( 'Y-m-d H:i:s', strtotime( $enddate . '23:59:59' ) );
			    
				$meta_queries[] = array(
					'key'     => '_orddd_timestamp',
					'value'   => array( strtotime( $from_date ), strtotime( $to_date ) ),
					'type'    => 'NUMERIC',
					'compare' => 'BETWEEN'
				);
			} else {
				// from the start to the end of the month
				$from_date = substr( $date, 0, 4 ) . '-' . substr( $date, 4, 2 ) . '-01';
				$to_date   = substr( $date, 0, 4 ) . '-' . substr( $date, 4, 2 ) . '-' . date( 't', strtotime( $from_date ) );
				$meta_queries[] = array(
					'key'     => '_orddd_timestamp',
					'value'   => array( strtotime( $from_date.' 00:00:00' ), strtotime( $to_date .' 23:59:59' ) ),
					'type'    => 'NUMERIC',
					'compare' => 'BETWEEN'
				);
			}
		}
		// update the query vars with our meta filter queries, if needed
		if ( is_array( $meta_queries ) && count( $meta_queries ) > 1 ) {
			$vars = array_merge(
				$vars,
				array( 'meta_query' => $meta_queries )
			);
		}
		return $vars;
	}

	/** 
	 * Adds the Delivery Date field to the set of searchable fields so that
	 * the orders can be searched based on Delivery details.
	 *
	 * @param array $search_fields - Array of post meta fields to search by 
	 * @return array $search_fields - Updated array of post meta fields to search by 
	 *  
	 * @hook woocommerce_shop_order_search_fields
	 * @since 2.7 
	 */
	public static function orddd_add_search_fields( $search_fields ) {
		$results = orddd_common::orddd_get_shipping_settings();
		foreach ( $results as $key => $value ) {
			$shipping_settings     = get_option( $value->option_name );
			$orddd_date_field_label   = orddd_common::orddd_get_shipping_date_field_label( $shipping_settings );
			array_push( $search_fields, $orddd_date_field_label );
		}
		
		array_push( $search_fields, get_option( 'orddd_delivery_date_field_label' ) );
		return $search_fields;
	}

	/**
	 * Echoes the Delivery date on WooCommerce->Orders->Edit Order page.
	 * 
	 * @param WC_Order $order - Order object
	 * 
	 * @hook woocommerce_admin_order_data_after_billing_address
	 *       woocommerce_admin_order_data_after_shipping_address
	 * @since 2.7      
	 */
	public static function orddd_display_delivery_date_admin_order_meta( $order ) {
		global $orddd_date_formats;
		
		if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
            $order_id = $order->get_id();
        } else {
            $order_id = $order->id;
        }
		
		$delivery_date_formatted = orddd_common::orddd_get_order_delivery_date( $order_id );

		$location = orddd_common::orddd_get_order_location( $order_id );
        if( '' != $location ) {
            $locations_label = '' != get_option( 'orddd_location_field_label' ) ? get_option( 'orddd_location_field_label' ) : 'Pickup Location';
            $address = get_post_meta( $order_id, '_pickup_location', true );

			if( ! $address ) {
				$address = get_post_meta( $order_id, $locations_label, true );
			}

            echo '<p><strong>' . __( $locations_label, 'order-delivery-date' ) . ': </strong>' . $address;
        }

		$field_date_label = orddd_custom_delivery_functions::orddd_fetch_delivery_date_field_label( $order_id );

		if( '' !== $delivery_date_formatted ) {
			echo '<p><strong>' . __( $field_date_label, 'order-delivery-date' ) . ': </strong>' . $delivery_date_formatted;
		}

		$estimated_text_block = orddd_common::orddd_get_order_estimated_text_block( $order_id );
		if( '' !== $estimated_text_block ) {
			echo '<strong> Delivery Information: </strong><br>';
			echo $estimated_text_block;
		}
	}
	
	/**
	 * Echoes the Delivery time on WooCommerce->Orders->Edit Order page.
	 * 
	 * @param WC_Order $order - Order object
	 * 
	 * @hook woocommerce_admin_order_data_after_billing_address
	 *       woocommerce_admin_order_data_after_shipping_address
	 * @since 2.7      
	 */
	public static function orddd_display_time_slot_admin_order_meta( $order ) {
		if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
            $order_id = $order->get_id();
        } else {
            $order_id = $order->id;
        }
        
		$time_slot = orddd_common::orddd_get_order_timeslot( $order_id );
		$time_field_label = orddd_custom_delivery_functions::orddd_fetch_time_slot_field_label( $order_id );

		if ( $time_slot != '' && $time_slot != '' ) {
			echo '<p><strong>' . __( $time_field_label, 'order-delivery-date' ) . ': </strong>' . $time_slot . '</p>';
		}
	}


	/**
	 * Echoes the Delivery Date Delivery time & Pickup Location on Order Preview page in Admin
	 * 
	 * @param $data 
	 * @param WC_Order $order - Order object
	 * 
	 * @hook woocommerce_admin_order_preview_get_order_details
	 * @since 9.5
	 */
	public static function orddd_admin_order_preview_add_delivery_date( $data, $order ) {
		global $orddd_date_formats;

		$order_id = $order->get_id();
		$orddd_timestamp = $order->get_meta( '_orddd_timestamp' );
		$delivery_date_formatted = orddd_common::orddd_get_order_delivery_date( $order_id );
		$field_date_label = orddd_custom_delivery_functions::orddd_fetch_delivery_date_field_label( $order_id );

		$orddd_timeslot  = $order->get_meta( '_orddd_time_slot' );

		if ( '' != $orddd_timestamp ) {
			$delivery_date = date( $orddd_date_formats[ get_option( 'orddd_delivery_date_format') ], $orddd_timestamp );
	        $data[ 'payment_via' ] = $data[ 'payment_via' ] . '<br>' . '<strong>'.$field_date_label.'</strong>' . $delivery_date_formatted;

	        if ( '' != $orddd_timeslot ) {
	        	$data[ 'payment_via' ] = $data[ 'payment_via' ] . ',<br>' . $orddd_timeslot;
	    	}

	    	$pickup_location_label = get_option( 'orddd_location_field_label' );
	    	$pickup_location 	   = $order->get_meta( $pickup_location_label );
	    	if ( '' != $pickup_location ) {
	    		$data[ 'payment_via' ] = $data[ 'payment_via' ] . '<br>' .
	    								 '<strong>' . $pickup_location_label . '</strong>' . 
	    								 $pickup_location;
	    	}
		}

		$estimated_text_block = orddd_common::orddd_get_order_estimated_text_block( $order_id );
		if( '' !== $estimated_text_block ) {
			$data[ 'payment_via' ] = $data[ 'payment_via' ] . '<br>' . '<strong> Delivery Information: </strong>' . strip_tags( $estimated_text_block );
		}
    	return $data;
	}

	/**
	 * Add 'Allow free delivery' checkbox on Add new coupons page. 
	 * Which when enabled will remove the delivery charges on checkout page
	 * if this coupon code is applied.
	 *
	 * @param $coupon_id  
	 * @param WC_Coupon $coupon - Coupon object
	 * 
	 * @hook woocommerce_coupon_options
	 * @since 9.7
	 */
	public static function orddd_allow_free_delivery_coupon_option( $coupon_id, $coupon ) {
		if ( 'on' == get_option( 'orddd_enable_delivery_date' ) ) {
			woocommerce_wp_checkbox(
				array(
					'id'          => 'orddd_free_delivery',
					'label'       => __( 'Allow free delivery', 'order-delivery-date' ),
					'description' => __( 'Check this box if the coupon grants free delivery.', 'order-delivery-date' )
				)
			);
		}
	}

	/**
	 * Save 'Allow free delivery' checkbox value in db. 
	 *
	 * @param $post_id  
	 * @param WC_Coupon $coupon - Coupon object
	 * 
	 * @hook woocommerce_coupon_options_save
	 * @since 9.7
	 */
	public static function orddd_save_free_delivery_coupon_save( $post_id, $coupon ) {
		$free_delivery_checkbox_value = '';
		if( isset( $_POST[ 'orddd_free_delivery' ] ) ) {
			$free_delivery_checkbox_value = $_POST[ 'orddd_free_delivery' ];
		}
		update_post_meta( $post_id, 'orddd_free_delivery', $free_delivery_checkbox_value );
	}
}
$orddd_filter = new orddd_filter();
?>
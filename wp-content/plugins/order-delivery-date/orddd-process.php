<?php
/**
 * Order Delivery Date Pro for WooCommerce
 *
 * Processes performed on the frontend checkout page.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Pro-for-WooCommerce/Frontend/Checkout-Page-Processes
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

include_once( 'orddd-common.php' );
include_once( 'includes/class-orddd-lockout-functions.php' );
include_once( 'includes/class-custom-delivery-functions.php' );

/**
 * Class for adding processes to be performed on the checkout page
 *
 * @class orddd_process
 */
class orddd_process {
    
    /**
     * Default Constructor
     *
     * @since 8.1
     */
    public function __construct() {
        //Delivery Date & Time on Order received page
        add_filter( 'woocommerce_order_details_after_order_table', array( &$this, 'orddd_add_delivery_date_to_order_page_woo' ), 100 );
        add_filter( 'woocommerce_order_details_after_order_table', array( &$this, 'orddd_add_time_slot_to_order_page_woo' ), 100 );
        //Estimated Text Block information on Order received page
        add_filter( 'woocommerce_order_details_after_order_table', array( &$this, 'orddd_add_text_block_to_order_page_woo' ) );
        
        //Delivery Date & Time on My Account -> Orders page
        add_filter( 'woocommerce_my_account_my_orders_columns',                    array( &$this, 'orddd_my_account_my_orders_columns' ), 10, 1 );
        add_filter( 'woocommerce_my_account_my_orders_column_order-delivery-date', array( &$this, 'orddd_my_account_my_orders_query' ), 10, 1 );

        //Remove Delivery Date & Time fields if required on the checkout page
        add_filter( 'woocommerce_checkout_fields', array( &$this, 'orddd_remove_delivery_field' ) );
        
        $orddd_shopping_cart_hook = orddd_common::orddd_get_shopping_cart_hook();
        add_action( $orddd_shopping_cart_hook,  array( &$this, 'orddd_date_after_checkout_billing_form' ) );
        add_action( $orddd_shopping_cart_hook,  array( &$this, 'orddd_text_block_after_checkout_billing_form' ) );
        
        if( 'on' == get_option( 'orddd_delivery_date_on_cart_page' ) ) {
            add_action( 'woocommerce_cart_collaterals', array( &$this, 'orddd_date_after_checkout_billing_form' ), 2 );
            add_action( 'woocommerce_cart_collaterals', array( &$this, 'orddd_text_block_after_checkout_billing_form' ), 2 );
        }

		add_filter( 'woocommerce_checkout_posted_data', array( &$this, 'orddd_add_delivery_data_to_posted_data' ), 10, 1 );
        add_action( 'woocommerce_after_checkout_validation', array( &$this, 'orddd_validate_date' ), 10, 2 );
        add_action( 'woocommerce_after_checkout_validation', array( &$this, 'orddd_validate_time_slot' ), 10, 2 );
        add_action( 'woocommerce_after_checkout_validation', array( &$this, 'orddd_validate_available_time' ), 10, 2 );
        add_action( 'woocommerce_after_checkout_validation', array( &$this, 'orddd_availability_check' ), 10, 2 );

        //Calculate Delivery charges on the checkout page
        add_action( 'woocommerce_cart_calculate_fees',         array( &$this, 'orddd_add_delivery_date_fee' ) );
        add_action( 'woocommerce_cart_totals_before_shipping', array( &$this, 'orddd_add_delivery_date_fee' ) );
        
        // 9.19.0 TEST: Uncomment below 2 lines if you want to test as per old methods that we were using upto 9.18.0.
        // Add Delivery Date & Time field records in database when order is placed.
        add_action( 'woocommerce_checkout_update_order_meta', array( &$this, 'orddd_update_order_meta_delivery_date' ), 11 );
        add_action( 'woocommerce_checkout_update_order_meta', array( &$this, 'orddd_update_order_meta_time_slot'  ), 11 );

        add_action( 'woocommerce_payment_complete',        array( 'orddd_lockout_functions', 'orddd_maybe_reduce_delivery_lockout' ), 11 );
        add_action( 'woocommerce_order_status_completed',  array( 'orddd_lockout_functions', 'orddd_maybe_reduce_delivery_lockout' ), 11 );
        add_action( 'woocommerce_order_status_processing', array( 'orddd_lockout_functions', 'orddd_maybe_reduce_delivery_lockout' ), 11 );
        add_action( 'woocommerce_order_status_on-hold',    array( 'orddd_lockout_functions', 'orddd_maybe_reduce_delivery_lockout' ), 11 );

        add_action( 'woocommerce_order_status_cancelled', array( 'orddd_lockout_functions', 'orddd_maybe_increase_delivery_lockout' ), 11, 1 );
        add_action( 'woocommerce_order_status_pending',   array( 'orddd_lockout_functions', 'orddd_maybe_increase_delivery_lockout' ), 11, 1 );
        add_action( 'woocommerce_order_status_refunded' , array( 'orddd_lockout_functions', 'orddd_maybe_increase_delivery_lockout' ), 11, 1 );
        add_action( 'woocommerce_order_status_failed' ,   array( 'orddd_lockout_functions', 'orddd_maybe_increase_delivery_lockout' ), 11, 1 );

        // When an order is cancelled from front end or Paypal page.
        add_action( 'woocommerce_cancelled_order', array( 'orddd_lockout_functions', 'orddd_maybe_increase_delivery_lockout' ), 11 );

        //Add Estimated shipping timestamp field records in database when order is placed
        add_action( 'woocommerce_checkout_update_order_meta', array( &$this, 'orddd_update_order_meta_text_block' ) );
        
        //Delivery Date & Time field added in the Customer notification email
        if ( version_compare( get_option( 'woocommerce_version' ), "2.3", '>=' ) > 0 ) {
            add_filter( 'woocommerce_email_order_meta_fields', array( &$this, 'orddd_add_delivery_date_to_order_woo_new' ), 11, 3 );
            add_filter( 'woocommerce_email_order_meta_fields', array( &$this, 'orddd_add_time_slot_to_order_woo_new' ), 11, 3 );
            //Estimated Text Block information added in the Customer notification email
            add_filter( 'woocommerce_email_order_meta_fields', array( &$this, 'orddd_add_text_block_to_order_woo_new' ), 11, 3 );
        } else {
            add_filter( 'woocommerce_email_order_meta_keys', array( &$this, 'orddd_add_delivery_date_to_order_woo_deprecated' ), 11, 1 );
            add_filter( 'woocommerce_email_order_meta_keys', array( &$this, 'orddd_add_time_slot_to_order_woo_deprecated' ), 11, 1 );
        }

        //Add required Hidden fields on the cart page 
        add_action( 'woocommerce_after_cart_table', array( &$this, 'show_hidden_fields' ) );
    }

    /**
     * Remove Delivery Date & Time fields on the checkout page when delivery not enabled.
     *
     * @hook woocommerce_checkout_fields
     * @globals $current_user Current logged-in user
     * 
     * @param array $fields Checkout page fields
     * @return array Checkout page fields
     * @since 1.0
     */
    public static function orddd_remove_delivery_field( $fields ) {
        global $current_user;
        $roles = array();
        if ( has_filter( 'orddd_disable_delivery_for_user_roles' ) ) {
            $roles = apply_filters( 'orddd_disable_delivery_for_user_roles', $roles );
        }
        
        $current_user_role = '';
        if( isset( $current_user->roles[0] ) ) {
            $current_user_role = $current_user->roles[0];
        }
        
        if( ( orddd_process::woo_product_has_delivery() === 'on' ) && ( 'yes' == orddd_common::orddd_is_delivery_enabled() ) && ( !in_array( $current_user_role, $roles ) ) ) {
            
        } else {
            if ( has_filter( 'orddd_shopping_cart_hook' ) ) {
                $orddd_shopping_cart_hook = apply_filters( 'orddd_shopping_cart_hook', '' );
            } else {
                $orddd_shopping_cart_hook = ORDDD_SHOPPING_CART_HOOK;
            }
            remove_action( $orddd_shopping_cart_hook,          array( 'order_delivery_date', 'orddd_front_scripts_js' ) );
            remove_action( $orddd_shopping_cart_hook,          array( 'order_delivery_date', 'orddd_front_scripts_css' ) );
             
            remove_action( $orddd_shopping_cart_hook,          array( 'orddd_process','orddd_date_after_checkout_billing_form' ) );           
            remove_action( 'woocommerce_after_checkout_validation', array( 'orddd_process', 'orddd_validate_available_time' ), 10, 2 );
            remove_action( 'woocommerce_after_checkout_validation', array( 'orddd_process', 'orddd_availability_check' ), 10, 2 );
            
            remove_action( $orddd_shopping_cart_hook,  array( 'orddd_process','orddd_text_block_after_checkout_billing_form' ) );
        }
        return $fields;
    }
        
    /** 
     * Delivery information text message on the checkout page.
     *
     * @hook woocommerce_after_checkout_billing_form
     * @hook woocommerce_after_checkout_shipping_form
     * @hook woocommerce_before_order_notes
     * @hook woocommerce_after_order_notes
     * @hook woocommerce_cart_collaterals
     *
     * @param resource $checkout Checkout Page Object
     * @since 6.7
     */

    public static function orddd_text_block_after_checkout_billing_form( $checkout = '' ) {
    	global $current_user;
        $roles = array();
        if ( has_filter( 'orddd_disable_delivery_for_user_roles' ) ) {
            $roles = apply_filters( 'orddd_disable_delivery_for_user_roles', $roles );
        }
        
        $current_user_role = '';
        if( isset( $current_user->roles[0] ) ) {
            $current_user_role = $current_user->roles[0];
        }

        $is_delivery_enabled = orddd_common::orddd_is_delivery_enabled();
		if ( get_option( 'orddd_enable_delivery_date' ) == 'on' && $is_delivery_enabled == 'yes' && ( orddd_process::woo_product_has_delivery() === 'on' ) && !in_array( $current_user_role, $roles ) ) {
        	$display = 'none';
        
	        $orddd_is_text_block_enabled_for_custom = orddd_common::orddd_is_text_block_enabled_for_custom_delivery();
	        if( 'text_block' == get_option( 'orddd_delivery_checkout_options' ) || 'yes' == $orddd_is_text_block_enabled_for_custom ) {
	            $display = 'block';
	        } 

	        $minimum_delivery_time = 0;
	        if( '' !=  get_option( 'orddd_minimumOrderDays' ) ) {
	            $minimum_delivery_time = get_option( 'orddd_minimumOrderDays' );
	        }

	        ?>
	        <div class="orddd_text_block" style="display:<?php echo $display; ?>">
	            <h3><?php _e( 'Delivery Information' ); ?></h3>
	            <?php
	            $orddd_min_between_days = get_option( 'orddd_min_between_days' );
	            $orddd_max_between_days = get_option( 'orddd_max_between_days' );
	            $delivery_time_seconds = $minimum_delivery_time * 60 * 60;
                $shipping_date = orddd_common::orddd_get_text_block_shipping_date( $delivery_time_seconds ); 
                if( 'text_block' == get_option( 'orddd_delivery_checkout_options' ) && 'yes' == $orddd_is_text_block_enabled_for_custom ) {
                    $hidden_variables = orddd_common::load_hidden_fields();
    		        echo $hidden_variables;
                } 
                $ordd_shipping_date  = orddd_common::delivery_date_language( $shipping_date['shipping_date'], strtotime( $shipping_date['shipping_date'] ), false );
                $estimated_date_text = str_replace(
					array(
						'%shipping_date%',
						'%delivery_range_start_days%',
						'%delivery_range_end_days%',
					),
					array(
						'<span id = "shipping_date">'.$ordd_shipping_date.'</span>',
						'<span id = "orddd_min_range">'.$orddd_min_between_days.'</span>',
						'<span id = "orddd_max_range">'.$orddd_max_between_days.'</span>',
					),
					get_option( 'orddd_estimated_date_text' )
				);
	            ?>
	        
	            <p><?php echo __( $estimated_date_text, 'order-delivery-date') ?></p>
	            <input type='hidden' name='orddd_estimated_shipping_date' id='orddd_estimated_shipping_date' value= '<?php echo $shipping_date[ 'hidden_shipping_date' ]; ?>' >
	        </div>
	        <?php    
	    }
    }

    /** 
     * Update Estimated shipping timestamp to the database.
     *
     * @hook woocommerce_checkout_update_order_meta
     *
     * @param resource $order_id Order Id
     * @since 8.7
     */
    public static function orddd_update_order_meta_text_block( $order_id ) {
        $orddd_is_text_block_enabled_for_custom = orddd_common::orddd_is_text_block_enabled_for_custom_delivery();
        if( 'text_block' == get_option( 'orddd_delivery_checkout_options' ) || 'yes' == $orddd_is_text_block_enabled_for_custom ) {
            $orddd_estimated_shipping_date = '';

            if( isset( $_POST[ 'orddd_estimated_shipping_date' ] ) && '' != $_POST[ 'orddd_estimated_shipping_date' ] ) {     
                $orddd_estimated_shipping_date = $_POST[ 'orddd_estimated_shipping_date' ];
            }
            
            $orddd_estimated_shipping_timestamp = orddd_common::orddd_get_timestamp( $orddd_estimated_shipping_date, '', '' );

            if( isset( $orddd_estimated_shipping_timestamp ) && '' != $orddd_estimated_shipping_timestamp ) {
                update_post_meta( $order_id, 'orddd_estimated_shipping_timestamp', $orddd_estimated_shipping_timestamp );
            }
        }
    }

    /** 
     * Display Estimated Text Block information to the Order Received page.
     *
     * @hook woocommerce_order_details_after_order_table
     *
     * @param resource $order WC_Order Object
     * @since 8.7
     */
    public static function orddd_add_text_block_to_order_page_woo( $order ) {
        $orddd_is_text_block_enabled_for_custom = orddd_common::orddd_is_text_block_enabled_for_custom_delivery();
        if( 'text_block' == get_option( 'orddd_delivery_checkout_options' ) || 'yes' == $orddd_is_text_block_enabled_for_custom ) {
            if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
                $order_id = $order->get_id();
            } else {
                $order_id = $order->id;
            }
            
            $orddd_text_block = orddd_common::orddd_get_order_estimated_text_block( $order_id );
            if( '' != $orddd_text_block ) {
                $opening_tag = '<p>';
                $closing_tag = '</p>';
                if( has_filter( 'orddd_pre_delivery_date_display' ) ) {
                    $tags = explode( "...", htmlentities( apply_filters( 'orddd_pre_delivery_date_display', '<p>...</p>' ) ) );
                    if( isset( $tags[ 0 ] ) ) {
                        $opening_tag = $tags[ 0 ];    
                    } 
                    
                    if( isset( $tags[ 1 ] ) ) {
                        $closing_tag = $tags[ 1 ];    
                    }
                } 
                 
                echo html_entity_decode( $opening_tag );
                echo '<span class="orddd_text_block">' . __( '<b>Delivery Information', 'order-delivery-date' ) . ':</b></br> </span> ' . $orddd_text_block;
                echo html_entity_decode( $closing_tag );
            }
        }
    }
     /**
     * Display Estimated TextBlock information in Customer notification email
     *
     * @hook woocommerce_email_order_meta_fields
     *
     * @param array $fields Fields to add in customer notification email
     * @param bool $sent_to_admin Whether to send emails to admin or not
     * @param resource $order Order Object
     * @return $fields Fields to add in customer notification email
     * @since 8.7
     */
    public static function orddd_add_text_block_to_order_woo_new( $fields, $sent_to_admin, $order ) {
        $orddd_is_text_block_enabled_for_custom = orddd_common::orddd_is_text_block_enabled_for_custom_delivery();
        if( 'text_block' == get_option( 'orddd_delivery_checkout_options' ) || 'yes' == $orddd_is_text_block_enabled_for_custom ) {
            
            if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
                $order_id = $order->get_id();
            } else {
                $order_id = $order->id;
            }
            
            $orddd_text_block = orddd_common::orddd_get_order_estimated_text_block( $order_id );

            if( has_filter( 'orddd_email_before_delivery_date' ) ) {
                $fields = apply_filters( 'orddd_email_before_delivery_date', $fields );
            }

            if( '' != $orddd_text_block ) {
                $text_block_label = __( '<b>Delivery Information', 'order-delivery-date' );
                $fields[ $text_block_label ] = array(
                    'label' => $text_block_label,
                    'value' => $orddd_text_block,
                );  
            }
        }
        
        return $fields;
    }

    /**
     * Delivery date field on the checkout page.
     * 
     * @globals resource $wpdb WordPress object
     * @globals array $orddd_date_formats Date formats array
     * @globals resource $post Post object
     * @globals resource $woocommerce WooCommerce Object
     * @globals array $orddd_languages Languages array
     * @globals array $orddd_weekdays Weekdays array
     * @globals array $orddd_shipping_days Shipping weekdays array 
     * 
     * @hook woocommerce_after_checkout_billing_form
     * @hook woocommerce_after_checkout_shipping_form
     * @hook woocommerce_before_order_notes
     * @hook woocommerce_after_order_notes
     * @hook woocommerce_cart_collaterals
     *
     * @param resource $checkout Checkout Page Object
     * @since 1.0
     */
    public static function orddd_date_after_checkout_billing_form( $checkout = "" ) {    
        global $current_user;
        $roles = array();

        if ( 'on' !== get_option( 'orddd_enable_delivery_date' ) ) {
            return false;
        }

        $roles               = apply_filters( 'orddd_disable_delivery_for_user_roles', $roles ); 
        $current_user_role   = isset( $current_user->roles[0] ) ? $current_user->roles[0] : '';
        $is_delivery_enabled = orddd_common::orddd_is_delivery_enabled();

        if( $is_delivery_enabled == 'yes' && ( orddd_process::woo_product_has_delivery() === 'on' ) && !in_array( $current_user_role, $roles ) ) {
            $orddd_is_delivery_calendar_enabled_for_custom = orddd_common::orddd_is_delivery_calendar_enabled_for_custom_delivery();

            if( 'delivery_calendar' == get_option( 'orddd_delivery_checkout_options' ) || 'yes' == $orddd_is_delivery_calendar_enabled_for_custom ) {
                $hidden_variables = orddd_common::load_hidden_fields();
    		    echo $hidden_variables;

                $orddd_holiday_color         = get_option( 'orddd_holiday_color' );
                $orddd_booked_dates_color    = get_option( 'orddd_booked_dates_color' );
                $orddd_cut_off_time_color    = get_option( 'orddd_cut_off_time_color' );
                $orddd_available_dates_color = get_option( 'orddd_available_dates_color' );

				echo '<style type="text/css">
					.holidays {
						background-color: ' . $orddd_holiday_color . ' !important;
					}

					.booked_dates {
						background-color: ' . $orddd_booked_dates_color . ' !important;
					}

					.cut_off_time_over {
						background-color: ' . $orddd_cut_off_time_color . ' !important;
					} 

                    .available-deliveries, .available-deliveries a {
                        background: ' . $orddd_available_dates_color . ' !important;
                    }  

                    .partially-booked, .partially-booked a {
                        background: linear-gradient(to bottom right, ' . $orddd_booked_dates_color . '59 0%, ' . $orddd_booked_dates_color . '59 50%, ' . $orddd_available_dates_color . ' 50%, ' . $orddd_available_dates_color . ' 100%) !important;
                    }  

				</style>';

                $time_slots_for_shipping_methods =  orddd_common::orddd_time_slots_enable_for_custom_delivery();
                $validate_date_field             = ( 'checked' == get_option( 'orddd_date_field_mandatory' ) ) ? true :false;
                $validate_time_field             = ( 'checked' == get_option( 'orddd_time_slot_mandatory' ) ) ? true :false;
                $is_dropdown                     = get_option( 'orddd_delivery_dates_in_dropdown' );
                $number_of_dates                 = get_option( 'orddd_number_of_dates' );
                $options                         = 'yes' === $is_dropdown ? ORDDD_Functions::orddd_get_dates_for_dropdown( $number_of_dates ) : array();
                $is_inline                       = 'inline_calendar' === get_option( 'orddd_calendar_display_mode' ) ?true: false;
                $date_field_label                = '' !== get_option( 'orddd_delivery_date_field_label' ) ? get_option('orddd_delivery_date_field_label' ) : __( 'Delivery Date', 'order-delivery-date' );
                $time_field_label                = '' !== get_option( 'orddd_delivery_timeslot_field_label' ) ? get_option('orddd_delivery_timeslot_field_label' ) : __( 'Delivery Time', 'order-delivery-date' );
                $time_slot_options               = array();
                $time_slot_enabled               = false;
                
                // Add time slot values if enabled.
                if( get_option( 'orddd_enable_time_slot' ) == 'on' || 
                ( get_option( 'orddd_enable_shipping_based_delivery' ) == 'on' && 'yes' == $time_slots_for_shipping_methods )) {
                    $time_slot_str = get_option( 'orddd_delivery_time_slot_log' );
                    $time_slots    = json_decode( $time_slot_str, true );
                    $time_slot_options = array ( __( "Select a time slot", "order-delivery-date" ) );
                    $time_slot_enabled = true;
                }

                do_action( 'orddd_before_checkout_fields', $checkout );

                wc_get_template(
                    'orddd-datepicker-template.php',
                    array(
                        'is_dropdown'       => $is_dropdown,
                        'date_field_label'  => $date_field_label,
                        'options'           => $options,
                        'checkout'          => $checkout,
                        'validate_date_field' => $validate_date_field,
                        'is_inline'         => $is_inline,
                        'time_slot_enabled' => $time_slot_enabled,
                        'time_field_label'  => $time_field_label,
                        'time_slot_options' => $time_slot_options,
                        'validate_time_field' => $validate_time_field,
                    ),
                    'order-delivery-date/',
                    ORDDD_TEMPLATE_PATH
                );

                do_action( 'orddd_after_checkout_fields', $checkout );
            }
        }
	}

	/**
     * Add delivery date/time fields to posted data.
     * This data is then available for validations.
     *
     * @param array $data - Posted Data.
     * @return array $data - Posted Data including plugin data.
     */
    public static function orddd_add_delivery_data_to_posted_data( $data ) {

        $data[ 'e_deliverydate' ]  = isset( $_POST[ 'e_deliverydate' ] ) ? wc_clean( wp_unslash( $_POST[ 'e_deliverydate' ] ) ) : ''; // WPCS: input var ok, CSRF ok
        $data[ 'h_deliverydate' ]  = isset( $_POST[ 'h_deliverydate' ] ) ? wc_clean( wp_unslash( $_POST[ 'h_deliverydate' ] ) ) : ''; // WPCS: input var ok, CSRF ok
        $data[ 'orddd_time_slot' ] = isset( $_POST[ 'orddd_time_slot' ] ) ? wc_clean( wp_unslash( $_POST[ 'orddd_time_slot' ] ) ) : ''; // WPCS: input var ok, CSRF ok
        return $data;

    }
    
	/**
	 * Validate delivery date field if mandatory
     * 
     * @globals resource $current_user Object of current logged-in User
     * @hook woocommerce_afetr_checkout_validation
     *
     * @since 1.0
	 */
	 
	public static function orddd_validate_date( $data, $errors ) {
		global $current_user;
        $roles = array();
        if ( has_filter( 'orddd_disable_delivery_for_user_roles' ) ) {
            $roles = apply_filters( 'orddd_disable_delivery_for_user_roles', $roles );
        }
        
        $current_user_role = '';
        if( isset( $current_user->roles[0] ) ) {
            $current_user_role = $current_user->roles[0];
        }

        $delivery_schedule_hidden_var = isset( $_POST['orddd_unique_custom_settings'] ) ? $_POST['orddd_unique_custom_settings'] : 'global_settings';

        if ( 'global_settings' === $delivery_schedule_hidden_var || '' === $delivery_schedule_hidden_var ) {
			$delivery_schedule_id = 0;
		} else {
			$delivery_schedule_parts = explode( 'custom_settings_', $delivery_schedule_hidden_var );
			$delivery_schedule_id    = $delivery_schedule_parts[1];
		}

        $date_mandatory_for_shipping_method = false;
        $custom_settings = array();
        if ( 0 != $delivery_schedule_id ) {
            $custom_settings = orddd_custom_delivery_functions::orddd_get_delivery_schedule_settings_by_id( $delivery_schedule_id );

            $date_mandatory_for_shipping_method = isset(  $custom_settings['date_mandatory_field'] ) && 'checked' ==  $custom_settings['date_mandatory_field'] ? true : false;
        }

	    $date_mandatory = "No";
	    if( $date_mandatory_for_shipping_method ) {
            $date_mandatory = "Yes";
	    } else if( get_option( 'orddd_date_field_mandatory' ) == 'checked' && get_option( 'orddd_enable_delivery_date' ) == 'on' ) {
	        $date_mandatory = "Yes";
	    } 

        if( function_exists( 'wcms_session_get' ) ) {
            $sess_cart_addresses = wcms_session_get( 'cart_item_addresses' );                                                   
            if ( !empty( $sess_cart_addresses ) ) {
                $date_mandatory = "No";
            }
        }

	    if ( $date_mandatory == "Yes" ) {
            global $woocommerce;
			$is_delivery_enabled = orddd_common::orddd_is_delivery_enabled();
			$fieldset_key = 'e_deliverydate';
            if( isset( $data[ 'e_deliverydate' ] ) ) {
                $delivery_date = $data[ 'e_deliverydate' ];
            } else if( isset( $_POST[ 'e_deliverydate' ] ) ) {
                $delivery_date = wc_clean( wp_unslash( $_POST[ 'e_deliverydate' ] ) );
            } else {
                $delivery_date = '';
            }
		
            do_action( 'orddd_before_date_validation', $delivery_date );
            
            $orddd_is_delivery_calendar_enabled_for_custom = orddd_common::orddd_is_delivery_calendar_enabled_for_custom_delivery();

            $shipping_method = '';
            $product_category = '';
            $shipping_class = '';
            $location = '';
            $categories = array();
            $shipping_classes = array();

            $orddd_zone_id = 0;
            if( isset( $_POST[ 'orddd_zone_id' ] ) ) {
                $orddd_zone_id = $_POST[ 'orddd_zone_id' ];    
            }

            if( isset( $_POST[ 'orddd_locations' ] ) && $_POST[ 'orddd_locations' ] != '' ) {
                $location = "orddd_" . $_POST[ 'orddd_locations' ];
            }

            if( isset( $_POST[ 'shipping_method' ][ 0 ] ) && $_POST[ 'shipping_method'][ 0 ] != '' && is_array( $_POST[ 'shipping_method' ] ) ) {
                $shipping_method = $_POST[ 'shipping_method' ][ 0 ];
                if( false !== strpos( $shipping_method, 'usps' ) ) {
                    $shipping_method = $orddd_zone_id . ":" . $shipping_method;
                }
                if( strpos( $shipping_method, 'wf_fedex_woocommerce_shipping' )  === false && false !== strpos( $shipping_method, 'fedex' ) && is_array( explode( ":", $shipping_method ) ) && count( explode( ":", $shipping_method ) ) < 3 ) {
                    $shipping_method = $orddd_zone_id . ":" . $shipping_method;
                }
            } else if( isset( $_POST[ 'shipping_method' ] ) && $_POST[ 'shipping_method' ] != '' ) {
                $shipping_method = $_POST[ 'shipping_method' ];
                if( false !== strpos( $shipping_method, 'usps') ) {
                    $shipping_method = $orddd_zone_id . ":" . $shipping_method;
                }
                if( strpos( $shipping_method, 'wf_fedex_woocommerce_shipping' )  === false && false !== strpos( $shipping_method, 'fedex' ) && is_array( explode( ":", $shipping_method ) ) && count( explode( ":", $shipping_method ) ) < 3 ) {
                    $shipping_method = $orddd_zone_id . ":" . $shipping_method;
                }
            }

            if( isset( $_POST[ 'orddd_category_settings_to_load' ] ) && $_POST[ 'orddd_category_settings_to_load'] != '' ) {
                $product_category = $_POST[ 'orddd_category_settings_to_load' ];
                $categories = explode( ",", $product_category );
            }

            if( isset( $_POST[ 'orddd_shipping_class_settings_to_load' ] ) && $_POST[ 'orddd_shipping_class_settings_to_load' ] != '' ) {
                $shipping_class = $_POST[ 'orddd_shipping_class_settings_to_load' ];
                $shipping_classes = explode( ",", $shipping_class );
            }

            $delivery_date_label = orddd_common::orddd_get_delivery_date_field_label( $shipping_method, $categories, $shipping_classes, $location ); 

            if( $is_delivery_enabled == 'yes' && ( orddd_process::woo_product_has_delivery() === 'on' ) && !in_array( $current_user_role, $roles ) && ( 'delivery_calendar' == get_option( 'orddd_delivery_checkout_options' ) || 'yes' == $orddd_is_delivery_calendar_enabled_for_custom ) ) {
			     //Check if set, if its not set add an error.
                if ( '' == $delivery_date || 'select' == $delivery_date ) {
                    $message = '<strong>' . $delivery_date_label . '</strong>' . ' ' . __( 'is a required field.', 'order-delivery-date' );
                    $errors->add(
                        'required-field',
                        $message,
                        array(
                            'id' => $fieldset_key
                        )
                    );
                }
            }
            
            do_action( 'orddd_after_date_validation', $delivery_date );
	    }
	}
	
	/**
	 * Validate Time slot field if mandatory
     * 
     * @globals resource $current_user Object of current logged-in User
     * @hook woocommerce_after_checkout_validation
     *
     * @since 1.0
	 */
	
	public static function orddd_validate_time_slot( $data, $errors ) {
        global $current_user;
        $roles = array();
        if ( has_filter( 'orddd_disable_delivery_for_user_roles' ) ) {
            $roles = apply_filters( 'orddd_disable_delivery_for_user_roles', $roles );
        }
        
        $current_user_role = '';
        if( isset( $current_user->roles[0] ) ) {
            $current_user_role = $current_user->roles[0];
        }
        
        $delivery_schedule_hidden_var = isset( $_POST['orddd_unique_custom_settings'] ) ? $_POST['orddd_unique_custom_settings'] : 'global_settings';

        if ( 'global_settings' === $delivery_schedule_hidden_var || '' === $delivery_schedule_hidden_var ) {
			$delivery_schedule_id = 0;
		} else {
			$delivery_schedule_parts = explode( 'custom_settings_', $delivery_schedule_hidden_var );
			$delivery_schedule_id    = $delivery_schedule_parts[1];
		}

        $time_slot_enable_for_shipping_method = false;
        $time_slot_mandatory_for_shipping_method = false;
        $custom_settings = array();
        if ( 0 != $delivery_schedule_id ) {
            $custom_settings = orddd_custom_delivery_functions::orddd_get_delivery_schedule_settings_by_id( $delivery_schedule_id );

            $time_slot_enable_for_shipping_method = isset( $custom_settings['enable_shipping_based_delivery'] ) && 'on' == $custom_settings['enable_shipping_based_delivery'] ? true: false;
            $time_slot_mandatory_for_shipping_method = isset(  $custom_settings['timeslot_mandatory_field'] ) && 'checked' ==  $custom_settings['timeslot_mandatory_field'] ? true : false;
        }

        $timeslot_mandatory = "No";
	    if ( $time_slot_enable_for_shipping_method && $time_slot_mandatory_for_shipping_method) {
	        $timeslot_mandatory = "Yes";
	    } else if( ! $time_slot_enable_for_shipping_method && ( strpos( $_POST['orddd_unique_custom_settings'], 'global_settings' ) !== false || '' === $_POST['orddd_unique_custom_settings'] ) ) {
	        if( get_option( 'orddd_enable_time_slot' ) == 'on' && get_option( 'orddd_time_slot_mandatory' ) == 'checked' && get_option( 'orddd_enable_delivery_date' ) == 'on' ) {
	           $timeslot_mandatory = "Yes";
	        }
	    }

        if( function_exists( 'wcms_session_get' ) ) {
            $sess_cart_addresses = wcms_session_get( 'cart_item_addresses' );      
            if ( !empty( $sess_cart_addresses ) ) {
                $timeslot_mandatory = "No";
            } 
        }
        
	    if( $timeslot_mandatory == "Yes" )  {
            global $woocommerce;
            $is_delivery_enabled = orddd_common::orddd_is_delivery_enabled();
			$fieldset_key = 'orddd_time_slot';
            if( isset( $data[ 'orddd_time_slot' ] ) ) {
                $ts = $data[ 'orddd_time_slot' ];
            } else if( isset( $_POST[ 'orddd_time_slot' ] ) ) {
                $ts = wc_clean( wp_unslash( $_POST[ 'orddd_time_slot' ] ) );
            } else {
                $ts = '';
            }
            
            $shipping_method = '';
            $product_category = '';
            $shipping_class = '';
            $location = '';
            $orddd_zone_id = 0;
            $categories = array();
            $shipping_classes = array();

            if( isset( $_POST[ 'orddd_zone_id' ] ) ) {
                $orddd_zone_id = $_POST[ 'orddd_zone_id' ];    
            }

            if( isset( $_POST[ 'orddd_locations' ] ) && $_POST[ 'orddd_locations'] != '' ) {
                $location = "orddd_" . $_POST[ 'orddd_locations' ];
            }

            if( isset( $_POST[ 'shipping_method' ][ 0 ] ) && $_POST[ 'shipping_method'][ 0 ] != '' && is_array( $_POST[ 'shipping_method' ] ) ) {
                $shipping_method = $_POST[ 'shipping_method' ][ 0 ];
                if( false !== strpos( $shipping_method, 'usps' ) ) {
                    $shipping_method = $orddd_zone_id . ":" . $shipping_method;
                }
                if( strpos( $shipping_method, 'wf_fedex_woocommerce_shipping' )  === false && false !== strpos( $shipping_method, 'fedex' ) && is_array( explode( ":", $shipping_method ) ) && count( explode( ":", $shipping_method ) ) < 3 ) {
                    $shipping_method = $orddd_zone_id . ":" . $shipping_method;
                }
            } else if( isset( $_POST[ 'shipping_method' ] ) && $_POST[ 'shipping_method' ] != '' ) {
                $shipping_method = $_POST[ 'shipping_method' ];
                if( false !== strpos( $shipping_method, 'usps') ) {
                    $shipping_method = $orddd_zone_id . ":" . $shipping_method;
                }
                if( strpos( $shipping_method, 'wf_fedex_woocommerce_shipping' )  === false && false !== strpos( $shipping_method, 'fedex' ) && is_array( explode( ":", $shipping_method ) ) && count( explode( ":", $shipping_method ) ) < 3 ) {
                    $shipping_method = $orddd_zone_id . ":" . $shipping_method;
                }
            }


            if( isset( $_POST[ 'orddd_category_settings_to_load' ] ) && $_POST[ 'orddd_category_settings_to_load'] != '' ) {
                $product_category = $_POST[ 'orddd_category_settings_to_load' ];
                $categories = explode( ",", $product_category );
            }

            if( isset( $_POST[ 'orddd_shipping_class_settings_to_load' ] ) && $_POST[ 'orddd_shipping_class_settings_to_load' ] != '' ) {
                $shipping_class = $_POST[ 'orddd_shipping_class_settings_to_load' ];
                $shipping_classes = explode( ",", $shipping_class );
            }

            $delivery_time_label = orddd_common::orddd_get_delivery_time_field_label( $shipping_method, $categories, $shipping_classes, $location ); 

            do_action( 'orddd_before_timeslot_validation', $ts );
		    $orddd_is_delivery_calendar_enabled_for_custom = orddd_common::orddd_is_delivery_calendar_enabled_for_custom_delivery();
            if( $is_delivery_enabled == 'yes' && ( orddd_process::woo_product_has_delivery() === 'on' ) && !in_array( $current_user_role, $roles ) && ( 'delivery_calendar' == get_option( 'orddd_delivery_checkout_options' ) || 'yes' == $orddd_is_delivery_calendar_enabled_for_custom ) ) {

                $allow_orders = '';
                if( has_filter( 'orddd_allow_orders_for_mandatory_time' ) ) {
                    $allow_orders = apply_filters( 'orddd_allow_orders_for_mandatory_time', '' );
                }
                if( 'yes' == $allow_orders ) {
                    if( $ts == '' || $ts == 'choose' || $ts == 'select' ) {
                        $message = '<strong>' . $delivery_time_label . '</strong>' . ' ' . __( 'is a required field.', 'order-delivery-date' );
                        $errors->add(
                            'required-field',
                            $message,
                            array(
                                'id' => $fieldset_key
                            )
                        );
                    }    
                } else {
                    if( $ts == '' || $ts == 'choose' || $ts == 'select' || $ts == 'NA' ) {
                        $message = '<strong>' . $delivery_time_label . '</strong>' . ' ' . __( 'is a required field.', 'order-delivery-date' );
                        $errors->add(
                            'required-field',
                            $message,
                            array(
                                'id' => $fieldset_key
                            )
                        );
                    }
                }
            }
            do_action( 'orddd_after_timeslot_validation', $ts );
        }
	}
	
	/**
	 * Add Delivery fee on the checkout page
     * 
     * @globals resource $woocommerce WooCommerce Object
     * @globals resource $wpdb WordPress Object
     * @globals array $orddd_weekdays Weekdays array
     * 
     * @hook woocommerce_cart_calculate_fees
     * @hook woocommerce_cart_totals_before_shipping
     *
     * @since 2.6
	 */
	public static function orddd_add_delivery_date_fee( $cart ) {
		global $woocommerce, $orddd_weekdays, $wpdb;
        $gmt = false;
        if( has_filter( 'orddd_gmt_calculations' ) ) {
            $gmt = apply_filters( 'orddd_gmt_calculations', '' );
        }
        $current_time = current_time( 'timestamp', $gmt );

		$free_coupon_enabled = 'no';
        $add_delivery_charges_for_free_coupon_code = 'no';
		$delivery_dates_array = array();
		$total_fees = 0;
		if( has_filter( 'orddd_add_delivery_charges_for_free_coupon_code' ) ) {
		    $add_delivery_charges_for_free_coupon_code = apply_filters( 'orddd_add_delivery_charges_for_free_coupon_code', $add_delivery_charges_for_free_coupon_code );
		}
		
		if ( 'yes' != $add_delivery_charges_for_free_coupon_code ) {
		    $applied_coupons = $woocommerce->cart->get_coupons();

    		foreach ( $applied_coupons as $applied_coupons_key => $applied_coupons_value ) {
                if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {
                    $data = $applied_coupons_value->get_data();
                }
                
                $orddd_free_delivery = ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">="  ) ) ? get_post_meta( $data['id'], 'orddd_free_delivery', true ) : $applied_coupons_value->orddd_free_delivery;

                if ( 'yes' == $orddd_free_delivery ) {
                    $free_coupon_enabled = 'yes';
                    break;
                }
    		}
		}

		if( $free_coupon_enabled != 'yes' ) {		
    		$delivery_date = '';
            $category_to_load = '';
            $shipping_class_to_load = '';
            $time_slot = '';
            $orddd_zone_id = '';
            $location = '';
            $lpp_location = '';
            $shipping_package_to_load = '';

            // Any new code added in post_data condition should be set in the below conditions too.
            $categories = array();
            $shipping_classes = array();
    		if ( isset( $_POST[ 'post_data' ] ) ) {
    			$delivery_date_type = preg_match( '/h_deliverydate=(.*?)&/', $_POST[ 'post_data' ], $matches );
    			if ( isset( $matches[ 1 ] ) ) {
    				$delivery_date = $matches[ 1 ];
    			}

                $lpp_locations = preg_match( '/lpp_selected_pickup_location=(.*?)&/', $_POST[ 'post_data' ], $lpp_location_load_match );
                if ( isset( $lpp_location_load_match[ 1 ] ) ) {
                    $lpp_location = 'orddd_pickup_location_' . urldecode( $lpp_location_load_match[ 1 ] );
                }

                $locations_type = preg_match( '/orddd_locations=(.*?)&/', $_POST[ 'post_data' ], $location_load_match );
                if ( isset( $location_load_match[ 1 ] ) ) {
                    $location = urldecode( $location_load_match[ 1 ] );
                }

    			$category_to_load_type = preg_match( '/orddd_category_settings_to_load=(.*?)&/', $_POST['post_data'], $category_to_load_match );
    			if ( isset( $category_to_load_match[ 1 ] ) ) {
    			    $category_to_load = urldecode( $category_to_load_match[ 1 ] );
                    $categories = explode( ",", $category_to_load );
    			}
    
    			$shipping_class_to_load_type = preg_match( '/orddd_shipping_class_settings_to_load=(.*?)&/', $_POST['post_data'], $shipping_class_to_load_match );
    			if ( isset( $shipping_class_to_load_match[ 1 ] ) ) {
    			    $shipping_class_to_load = urldecode( $shipping_class_to_load_match[ 1 ] );
                    $shipping_classes = explode( ",", $shipping_class_to_load );
    			} 
                
                // Check for the shipping methods for shipping packages from Advance Shipping Packages plugin for WooCommerce.
                // By Default, it will take the first shipping package method. It can be changed using orddd_shipping_package_to_load hook.
                if( class_exists( 'orddd_advance_shipping_compatibility' ) && 
                    class_exists( 'Advanced_Shipping_Packages_for_WooCommerce' ) ) {
                    $shipping_package_to_load_type = preg_match( '/orddd_shipping_package_to_load=(.*?)&/', $_POST['post_data'], $shipping_package_to_load_match );

                    if ( isset( $shipping_package_to_load_match[ 1 ] ) ) {
                        $shipping_package_to_load = urldecode( $shipping_package_to_load_match[ 1 ] );
                    } 
                }

    			$time_slot_type = preg_match( '/&orddd_time_slot=(.*?)&/', $_POST[ 'post_data' ], $matches );
    			if ( isset( $matches[ 1 ] ) ) {
    			    $time_slot = urldecode( $matches[ 1 ] );
    			}

                $orddd_zone_id_type = preg_match( '/&orddd_zone_id=(.*?)&/', $_POST[ 'post_data' ], $matches );
                if ( isset( $matches[ 1 ] ) ) {
                    $orddd_zone_id = urldecode( $matches[ 1 ] );
                }
    		}
           
            //If $_POST is blank then the variables are to be set here. $_POST can be blank while Place Order button is clicked.
            $delivery_on_cart = get_option( 'orddd_delivery_date_on_cart_page' );
            $is_cart          = is_cart();
            $is_ajax          = is_ajax();

    		if ( $delivery_date == '' ) {
    			if ( isset( $_POST[ 'h_deliverydate' ] ) ) {
                    $delivery_date = $_POST[ 'h_deliverydate' ];
                    if( $is_cart && 'on' == $delivery_on_cart ) {
                        WC()->session->set( 'h_deliverydate', $delivery_date );
                    }
    			} else if( ( $is_cart || $is_ajax ) && 'on' == $delivery_on_cart && WC()->session->get( 'h_deliverydate' ) ) {
                    $delivery_date = WC()->session->get( 'h_deliverydate' ) ;
                } else {
    				$delivery_date = '';
    			}
    		}
    		
    		if( $time_slot == "" ) {
    		    if ( isset( $_POST[ 'orddd_time_slot' ] ) ) {
                    $time_slot = $_POST[ 'orddd_time_slot' ];
                    if( $is_cart && 'on' == $delivery_on_cart ) {
                        WC()->session->set( 'orddd_time_slot', $time_slot );
                    }
    		    } else if( ( $is_cart || $is_ajax ) && 'on' == $delivery_on_cart && WC()->session->get( 'orddd_time_slot' ) ) {
                    $time_slot = WC()->session->get( 'orddd_time_slot' ) ;
                } else {
    		        $time_slot = '';
    		    }
    		}
    		
            if ( $delivery_date == '' ) {
                if ( isset( $_POST[ 'hidden_h_deliverydate' ] ) ) {
                    $delivery_date =  $_POST[ 'hidden_h_deliverydate' ] ;
                    if( $is_cart && 'on' == $delivery_on_cart ) {
                        WC()->session->set( 'hidden_h_deliverydate', $delivery_date );
                    }
                } else if( ( $is_cart || $is_ajax ) && 'on' == $delivery_on_cart && WC()->session->get( 'hidden_h_deliverydate' ) ) {
                    $delivery_date = WC()->session->get( 'hidden_h_deliverydate' ) ;
                } else {
                    $delivery_date = '';
                }
            }
            
            if( $time_slot == "" ) {
                if ( isset( $_POST[ 'hidden_timeslot' ]  ) ) {
                    $time_slot =   $_POST[ 'hidden_timeslot' ] ;
                    if( $is_cart && 'on' == $delivery_on_cart ) {
                        WC()->session->set( 'hidden_timeslot', $time_slot );
                    }
                } else if( ( $is_cart || $is_ajax ) && 'on' == $delivery_on_cart && WC()->session->get( 'hidden_timeslot' ) ) {
                    $time_slot = WC()->session->get( 'hidden_timeslot' ) ;
                } else {
                    $time_slot = '';
                }
            }

            if ( $orddd_zone_id == '' ) {
                if ( isset( $_POST[ 'orddd_zone_id' ] ) ) {
                    $orddd_zone_id = $_POST[ 'orddd_zone_id' ];
                    WC()->session->set( 'orddd_zone_id', $orddd_zone_id );
                }else if( ( $is_cart || $is_ajax ) && 'on' == $delivery_on_cart && WC()->session->get( 'orddd_zone_id' ) ) {
                    $orddd_zone_id = WC()->session->get( 'orddd_zone_id' );
                }
            }

            if( is_array( $categories ) && count( $categories ) == 0 ) {
                if ( isset( $_POST[ 'orddd_category_settings_to_load' ] ) ) {
                    $category_to_load = $_POST[ 'orddd_category_settings_to_load' ];
                    $categories = explode( ",", $category_to_load );
                    if( $is_cart && 'on' == $delivery_on_cart ) {
                        WC()->session->set( 'orddd_category_settings_to_load', $category_to_load );
                    }
                } else if( ( $is_cart || $is_ajax ) && 'on' == $delivery_on_cart && WC()->session->get( 'orddd_category_settings_to_load' ) ) {
                    $category_to_load = WC()->session->get( 'orddd_category_settings_to_load' );
                    $categories = explode( ",", $category_to_load );
                }
            }

            if( is_array( $categories ) && count( $categories ) == 0 ) {
                $categories = orddd_common::orddd_get_cart_product_categories( '' );
            }

            if( $shipping_class_to_load == '' ) {
                if( isset( $_POST[ 'orddd_shipping_class_settings_to_load' ] ) ) {
                    $shipping_class_to_load = $_POST[ 'orddd_shipping_class_settings_to_load' ];
                    $shipping_classes = explode( ",", $shipping_class_to_load );

                    if( $is_cart && 'on' == $delivery_on_cart ) {
                        WC()->session->set( 'orddd_shipping_class_settings_to_load', $shipping_class_to_load );
                    }
                } else if( ( $is_cart || $is_ajax ) && 'on' == $delivery_on_cart && WC()->session->get( 'orddd_shipping_class_settings_to_load' ) ) {
                    $shipping_class_to_load = WC()->session->get( 'orddd_shipping_class_settings_to_load' );
                    $shipping_classes = explode( ",", $category_to_load );
                }
            }

            if( $shipping_package_to_load == '' &&
                class_exists( 'orddd_advance_shipping_compatibility' ) && 
                class_exists( 'Advanced_Shipping_Packages_for_WooCommerce' ) ) {
                if( isset( $_POST[ 'orddd_shipping_package_to_load' ] ) ) {
                    $shipping_package_to_load = $_POST[ 'orddd_shipping_package_to_load' ];
                }
            }

            if( $location == '' ) {
                if( isset( $_POST[ 'orddd_locations' ] ) ) {
                    $location = $_POST[ 'orddd_locations' ];

                    if( $is_cart && 'on' == $delivery_on_cart ) {
                        WC()->session->set( 'orddd_locations', $location );
                    }
                } elseif ( isset( $_POST[ 'hidden_location' ]  ) ) {
                    $location = $_POST[ 'hidden_location' ];

                    if( $is_cart && 'on' == $delivery_on_cart ) {
                        WC()->session->set( 'orddd_locations', $location );
                    }
                } else if( ( $is_cart || $is_ajax ) && 'on' == $delivery_on_cart && WC()->session->get( 'orddd_locations' ) ) {
                    $location = WC()->session->get( 'orddd_locations' );
                }
            }

            if( $lpp_location == '' ) {
                if( isset( $_POST[ 'lpp_selected_pickup_location' ] ) ) {
                    $lpp_location = 'orddd_pickup_location_' . $_POST[ 'lpp_selected_pickup_location' ];
                }
            }
            
            $shipping_method = '';
            
            // For the Advanced Shipping packages plugin.
            if( $shipping_package_to_load != '' && isset( $_POST[ 'shipping_method' ][ $shipping_package_to_load ] ) &&  $_POST[ 'shipping_method'][ $shipping_package_to_load ] != '' && is_array( $_POST[ 'shipping_method' ] ) ) {
                $shipping_method = $_POST[ 'shipping_method' ][ $shipping_package_to_load ] . ":" . $shipping_package_to_load;
            }else if( isset( $_POST[ 'shipping_method' ][ 0 ] ) && is_array( $_POST[ 'shipping_method' ] ) ) {
                $shipping_method = $_POST[ 'shipping_method' ][ 0 ];
            } else if( isset( $_POST[ 'shipping_method' ] ) && is_array( $_POST[ 'shipping_method' ] ) ) {
                $shipping_method = '';
                if( isset(  $_POST[ 'shipping_method' ][ 'undefined' ] ) ) {
                    $shipping_method = $_POST[ 'shipping_method' ][ 'undefined' ];
                }
            } else if( isset( $_POST[ 'shipping_method' ] ) && $_POST[ 'shipping_method' ] != '' ) {
                $shipping_method = $_POST[ 'shipping_method' ];
            } else if( isset( $_POST[ 'hidden_shipping_method' ] ) && $_POST[ 'hidden_shipping_method' ] != '' ) {
                $shipping_method = $_POST[ 'hidden_shipping_method' ];
            } elseif( isset( $_POST['shipping_method[0]'] ) && '' !== $_POST['shipping_method[0]'] ) {
                // For WooCommerce Paypal Payments plugin
                $shipping_method = $_POST[ 'shipping_method[0]' ];
            }

            if( false !== strpos( $shipping_method, 'usps' ) ) {
                $shipping_method = $orddd_zone_id . ":" . $shipping_method;
            }

            if( strpos( $shipping_method, 'wf_fedex_woocommerce_shipping' )  === false && false !== strpos( $shipping_method, 'fedex' ) && is_array( explode( ":", $shipping_method ) ) && count( explode( ":", $shipping_method ) ) < 3 ) {
                $shipping_method = $orddd_zone_id . ":" . $shipping_method;
            }

            if( 'on' == $delivery_on_cart ) {
                if( ( $is_cart || $is_ajax ) && '' == $shipping_method &&  WC()->session->get( 'orddd_shipping_method' ) ) {
                    $shipping_method = WC()->session->get( 'orddd_shipping_method' );
                } elseif ( $is_cart ) {
                    WC()->session->set( 'orddd_shipping_method', $shipping_method );
                }
            }

    		$current_date = date( 'j-n-Y' , $current_time );

    		// next day date
    		$next_date = date( "j-n-Y", strtotime( "+1 day", strtotime( $current_date ) ) );
            $current_weekday = date( 'w', $current_time );

            $additional_weekdays_settings = array();
            if ( get_option( 'orddd_enable_day_wise_settings' ) == 'on' ) {
        		$advance_settings = get_option( 'orddd_advance_settings' );
                if( '' == $advance_settings || '{}' == $advance_settings || '[]' == $advance_settings ) {
                    $advance_settings = array();
                }
                    
                foreach ( $advance_settings as $row_id => $data ) {
                    if( 'orddd_weekday_' . $current_weekday == $data[ 'orddd_weekdays' ] ) {
                        $additional_weekdays_settings = $data;
                    }
                }
            }

            $shipping_settings_to_check = array();
            $shipping_based_timeslot_fees =  "No";
            if( get_option( 'orddd_enable_shipping_based_delivery' ) ) {
                $shipping_settings_to_check = orddd_common::orddd_get_custom_settings( $shipping_method, $shipping_classes, $categories, $location, $lpp_location );
                if( is_array( $shipping_settings_to_check ) && count( $shipping_settings_to_check ) > 0 ) {
                    $shipping_based_timeslot_fees = "Yes";
                }
    		}
            
            $time_slot_fees_to_add = 0;

    		if( $time_slot != '' && $time_slot != 'choose' && $time_slot != 'NA' && $time_slot != 'select' ) {
    		    //$specific_timeslot_fees = $shipping_based_timeslot = 'No';

                $time_slot_arr = explode( " - ", $time_slot );
                $from_time = date( "H:i", strtotime( $time_slot_arr[ 0 ] ) );
                if( isset( $time_slot_arr[1] ) && $time_slot_arr[1] != "" ) {
                    $to_time = date( "H:i", strtotime( $time_slot_arr[ 1 ] ) );
                    $timeslot_selected = $from_time . " - " . $to_time;
                } else {
                    $timeslot_selected = $from_time;
                }

                if( is_array( $shipping_settings_to_check ) && count( $shipping_settings_to_check ) > 0 ) {
                    $time_slot_charges_label = '';
                    foreach ( $shipping_settings_to_check as $setting_key => $setting_value ) {
                        $delivery_dates_str = $setting_to_load_value = '';
                        if( isset( $setting_value[ 'time_slots' ] ) && $setting_value[ 'time_slots' ] != '' ) {
                            $time_slot_settings = explode( '},', $setting_value[ 'time_slots' ] );
                            $time_slot_str = '';
                            foreach( $time_slot_settings as $hk => $hv ) {
                                if( $hv != '' ) {
                                    $timeslot_values = orddd_common::get_timeslot_values( $hv );
                                    $additional_charges = $timeslot_values[ 'additional_charges' ];
                                    $additional_charges_label = $timeslot_values[ 'additional_charges_label' ];
                                    if( $timeslot_values[ 'delivery_days_selected' ] == 'weekdays' ) {
                                        $weekday = date( "w", strtotime( $delivery_date ) );
                                        foreach( $timeslot_values[ 'selected_days' ] as $key => $val ) {
                                            if( $timeslot_selected == $timeslot_values[ 'time_slot' ] && ( $val == "orddd_weekday_" . $weekday . "_custom_setting" || $val == "all" ) ) {
                                                if( $time_slot_fees_to_add < $additional_charges ) {
                                                    $time_slot_fees_to_add = $additional_charges;
                                                    if( isset( $additional_charges_label ) && $additional_charges_label != "" ) {
                                                        $time_slot_charges_label = $additional_charges_label;
                                                    } else {
                                                        $time_slot_charges_label = 'Time Slot Charges';
                                                    }
                                                }
                                            }
                                        }
                                    } else if( $timeslot_values[ 'delivery_days_selected' ] == 'specific_dates' ) {
                                        foreach( $timeslot_values[ 'selected_days' ] as $key => $val ) {
                                            $specific_delivery_date = date( 'n-j-Y', strtotime( $delivery_date ) );
                                            if( $timeslot_selected == $timeslot_values[ 'time_slot' ] && $val == $specific_delivery_date ) {
                                                if( $additional_charges > 0 && $additional_charges != "" ) {
                                                    if( $time_slot_fees_to_add < $additional_charges ) {
                                                        $time_slot_fees_to_add = $additional_charges;
                                                        if( isset( $additional_charges_label ) && $additional_charges_label != "" ) {
                                                            $time_slot_charges_label = $additional_charges_label;
                                                        } else {
                                                            $time_slot_charges_label = 'Time Slot Charges';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }  
                    }
                }
                
                if( $shipping_based_timeslot_fees == 'No' ) {
                    $timeslot_log_arr = array();
                    $delivery_dates_arr = array();
                    $temp_date_arr = array();

                    $timeslot_log_str = get_option( 'orddd_delivery_time_slot_log' );
                    if ( $timeslot_log_str == 'null' || $timeslot_log_str == '' || $timeslot_log_str == '{}' || $timeslot_log_str == '[]' ) {
                        $timeslot_log_arr = array();
                    } else {
                        $timeslot_log_arr = json_decode( $timeslot_log_str );
                    }
                    
                    $delivery_dates = get_option( 'orddd_delivery_dates' );
                    if ( $delivery_dates != '' && $delivery_dates != '{}' && $delivery_dates != '[]' && $delivery_dates != 'null' ) {
                        $delivery_dates_arr = json_decode( get_option( 'orddd_delivery_dates' ) );
                    }
                    
                    foreach( $delivery_dates_arr as $dk => $dv ) {
                        $temp_date_arr[] =  $dv->date;
                    }
                    
                    $date_to_check = date( "n-j-Y", strtotime( $delivery_date ) );
                    foreach( $timeslot_log_arr as $k => $v ) {
                        $ft = $v->fh . ":" . trim( $v->fm );
                        $ft = date( 'H:i', strtotime( $ft ) );

                        if ( ( $v->th != 0 ) || ( $v->th == 0 && $v->tm != 0 ) ){
                            $tt = $v->th . ":" . trim( $v->tm );
                            $tt = date( 'H:i', strtotime( $tt ) );
                            $time_slot_key = $ft . " - " . $tt;
                        } else {
                            $time_slot_key = $from_time;
                        }
                        if ( gettype( json_decode( $v->dd ) ) == 'array' && count( json_decode( $v->dd ) ) > 0 && get_option( 'orddd_enable_specific_delivery_dates' ) == "on" && in_array( $date_to_check, $temp_date_arr ) ) {
                            $dd = json_decode( $v->dd );
                            if ( is_array( $dd ) &&  count( $dd ) > 0 ) {
                                foreach( $dd as $dkey => $dval ) {
                                    if( $timeslot_selected == $time_slot_key && $dval == $date_to_check ) {
                                        $additional_charges = $v->additional_charges;
                                        $additional_charges_label = $v->additional_charges_label;
                                        if( $additional_charges > 0 && $additional_charges != "" ) {
                                            if( $time_slot_fees_to_add < $additional_charges ) {
                                                $time_slot_fees_to_add = $additional_charges;
                                                if( isset( $additional_charges_label ) && $additional_charges_label != "" ) {
                                                    $time_slot_charges_label = $additional_charges_label;
                                                } else {
                                                    $time_slot_charges_label = 'Time Slot Charges';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $weekday = date( "w", strtotime( $delivery_date ) );
                            if ( gettype( json_decode( $v->dd ) ) == 'array' && count( json_decode( $v->dd ) ) > 0 ) {
                                $dd = json_decode( $v->dd );
                                foreach( $dd as $dkey => $dval ) {
                                    if( $timeslot_selected == $time_slot_key && ( $dval == "orddd_weekday_" . $weekday || $dval == "all" ) ) {
                                        $additional_charges = $v->additional_charges;
                                        $additional_charges_label = $v->additional_charges_label;
                                        if( $additional_charges > 0 && $additional_charges != "" ) {
                                            if( $time_slot_fees_to_add < $additional_charges ) {
                                                $time_slot_fees_to_add = $additional_charges;
                                                if( isset( $additional_charges_label ) && $additional_charges_label != "" ) {
                                                    $time_slot_charges_label = $additional_charges_label;
                                                } else {
                                                    $time_slot_charges_label = 'Time Slot Charges';
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                if(  $timeslot_selected == $time_slot_key && ( $v->dd == "orddd_weekday_" . $weekday || $v->dd == "all" ) ) {
                                    $additional_charges = $v->additional_charges;
                                    $additional_charges_label = $v->additional_charges_label;
                                    if( $additional_charges > 0 && $additional_charges != "" ) {
                                       if( $time_slot_fees_to_add < $additional_charges ) {
                                            $time_slot_fees_to_add = $additional_charges;
                                            if( isset( $additional_charges_label ) && $additional_charges_label != "" ) {
                                                $time_slot_charges_label = $additional_charges_label;
                                            } else {
                                                $time_slot_charges_label = 'Time Slot Charges';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
                if ( class_exists( 'WOOCS' ) ) {
                    global $WOOCS;
                    if ( $WOOCS->is_multiple_allowed ) {
                        $time_slot_fees_to_add = $WOOCS->woocs_exchange_value( floatval( $time_slot_fees_to_add ) );
                    }
                }
    		}

    		if ( $delivery_date != '' ) {
                $specific_fees = "No";
                $shipping_settings_exists = "No";
                $i = 1;
                $fees_to_add = 0;
                $specific_charges_label = '';
                $delivery_charges_label = '';
                $same_day_fees = 0;
                $next_day_fees = 0;
                $remove_charges = false;

                if( is_array( $shipping_settings_to_check ) && count( $shipping_settings_to_check ) > 0 ) {
                    foreach ( $shipping_settings_to_check as $setting_key => $setting_value ) {
                        if( isset( $setting_value[ 'delivery_type' ] ) ) {
                            $delivery_type = $setting_value[ 'delivery_type' ];
                        }

                        // Don't add charges if the order total is more than the amount set in the settings.
                        if( isset( $setting_value['max_order_total'] ) && $setting_value['max_order_total'] > 0 ) {
                            $remove_charges = self::orddd_to_remove_charges( $setting_value['max_order_total'] );
                            if( $remove_charges ) {
    		                    WC()->session->set( '_total_delivery_charges', 0 );
                                return false;
                            }
                        }

                        if( isset( $delivery_type[ 'specific_dates' ] ) && $delivery_type[ 'specific_dates' ] == 'on' ) {
                            $specific_days_settings = explode( ',', rtrim( $setting_value[ 'specific_dates' ], "," ) );
                            $date = '';
                            if ( !preg_match( '/[A-Za-z]/', $delivery_date ) ) {
                                $date = date( 'n-j-Y', strtotime( $delivery_date ) );
                            }

                            foreach( $specific_days_settings as $sk => $sv ) {
                                if( $sv != '' ) {
                                    $specific_delivery_str = str_replace( '}', '', $sv );
                                    $specific_delivery_str = str_replace( '{', '', $specific_delivery_str );
                                    $specific_date_arr = explode( ':', $specific_delivery_str );
                                    if ( $date == $specific_date_arr[ 0 ] ) {
                                        if( $fees_to_add < $specific_date_arr[ 1 ] ) {
                                            $fees_to_add = $specific_date_arr[ 1 ];
                                            $delivery_charges_label = $specific_date_arr[ 2 ];
                                            $specific_fees = "Yes";
                                        }

                                        /**
                                         * Filter orddd_custom_specific_delivery_dates can be used to 
                                         * override the specific charges added based on some conditions
                                         * like if you only want the charges applied on a specific 
                                         * product or variation id
                                         */
                                        if ( has_filter( 'orddd_custom_specific_delivery_dates' ) ) {
                                            $specific_date_params = apply_filters( 'orddd_custom_specific_delivery_dates', $specific_fees, $specific_date_arr[ 0 ], $fees_to_add, $delivery_charges_label );
                                            if ( isset( $specific_date_params ) && is_array( $specific_date_params ) ) {
                                                $specific_fees = $specific_date_params[ 'specific_fees' ];
                                                $fees_to_add   = $specific_date_params[ 'specific_day_charges' ];
                                                $delivery_charges_label = $specific_date_params[ 'specific_charges_label' ];
                                            }
                                        }                                        
                                    }
                                    
                                    $delivery_dates_array[] = $specific_date_arr[ 0 ];
                                }
                            }
                        }

                        if( isset( $setting_value[ 'same_day' ] ) ) {
                            $same_day = $setting_value[ 'same_day' ];
                            if( isset( $same_day[ 'after_hours' ] ) && $same_day[ 'after_hours' ] == 0 && isset( $same_day [ 'after_minutes' ] ) && $same_day[ 'after_minutes' ] == '00' ) {
                            } else {
                                if( isset( $same_day[ 'additional_charges' ] ) && $same_day[ 'additional_charges' ] != 0 && $same_day[ 'additional_charges' ] != '' ) {
                                    if ( $current_date == $delivery_date ) {
                                        if( $same_day_fees < $same_day[ 'additional_charges' ] ) {
                                            $same_day_fees = $same_day[ 'additional_charges' ];
                                            $same_day_delivery_charges_label = __( 'Same Day Delivery Charges', 'order-delivery-date' );    
                                        }
                                        
                                    }
                                }
                            }
                        }
                        
                        if( isset( $setting_value[ 'next_day' ] ) ) {
                            $next_day = $setting_value[ 'next_day' ];
                            if( isset( $next_day[ 'after_hours' ] ) && $next_day[ 'after_hours' ] == 0 && isset( $next_day [ 'after_minutes' ] ) && $next_day[ 'after_minutes' ] == '00' ) {
                            } else {
                                if( isset( $next_day[ 'additional_charges' ] ) && $next_day[ 'additional_charges' ] != 0 && $next_day[ 'additional_charges' ] != '' ) {
                                    if ( $next_date == $delivery_date ) {
                                        if( $next_day_fees < $next_day[ 'additional_charges' ] ) {
                                            $next_day_fees = $next_day[ 'additional_charges' ];
                                            $next_day_delivery_charges_label = __( 'Next Day Delivery Charges', 'order-delivery-date' );    
                                        }
                                    }
                                }
                            }
                        }
                        $shipping_settings_exists = "Yes";

                        if ( isset( $setting_value['orddd_delivery_charges_based_on_days_enable'] ) && 'on' === $setting_value['orddd_delivery_charges_based_on_days_enable'] && $fees_to_add == 0 && $same_day_fees == 0 && $next_day_fees == 0 ) {
                            // calculates number of day for selected delivery date from today to get delivery charge for that day.
                            $no_of_day_from_today = orddd_common::orddd_get_next_available_day( $delivery_date, $shipping_settings_to_check );

                            if ( in_array( $no_of_day_from_today, $setting_value['orddd_additional_settings_based_on_days']['number'] ) ) {
                                $key_for_additional = array_search( $no_of_day_from_today, $setting_value['orddd_additional_settings_based_on_days']['number'] );
                                $additional_label   = $setting_value['orddd_additional_settings_based_on_days']['label'][ $key_for_additional ];
                                $additional_charge  = $setting_value['orddd_additional_settings_based_on_days']['charge'][ $key_for_additional ];
                                if ( $additional_charge ) {
                                    $fees_to_add = $additional_charge;
                                }
                                if ( $additional_label ) {
                                    $delivery_charges_label = $additional_label;
                                }
                            }
                        }

                        if( !$fees_to_add && isset( $delivery_type[ 'weekdays' ] ) && $delivery_type[ 'weekdays' ] == 'on' ) {
                            $day = '';
                            if ( !preg_match('/[A-Za-z]/', $delivery_date ) ) {
                                $day = date( 'w', strtotime( $delivery_date ) );
                            }

                            $weekdays_settings = $setting_value[ 'weekdays' ];
                            foreach ( $orddd_weekdays as $n => $day_name ) {
                                if( $n == 'orddd_weekday_'. $day ) {
                                    $weekday = $weekdays_settings[ $n ];
                                    if( isset( $weekday[ 'additional_charges' ] ) && $weekday[ 'additional_charges' ] != '' && $weekday[ 'additional_charges' ] != 0 && $fees_to_add < $weekday[ 'additional_charges' ] ) {
                                        $fees_to_add = $weekday[ 'additional_charges' ];
                                        $delivery_charges_label = $weekday[ 'delivery_charges_label' ];
                                    }
                                }
                            }
                        }
                    }
                }
                
                if( $shipping_settings_exists == 'No' ) {
                    // Don't add charges if the order total is more than the amount set in the settings.
                    $max_order_total = get_option( 'orddd_max_order_total_for_charges' );
                    $remove_charges = self::orddd_to_remove_charges( $max_order_total );
                    if( $remove_charges ) {
                        WC()->session->set( '_total_delivery_charges', 0 );
                        return false;
                    }

                    if( get_option( 'orddd_enable_specific_delivery_dates' ) == 'on' ) {
                        $delivery_dates_arr = array();
    				    $delivery_dates = get_option( 'orddd_delivery_dates' );
    				    if ( $delivery_dates != '' && $delivery_dates != '{}' && $delivery_dates != '[]' && $delivery_dates != 'null' ) {
    					   $delivery_dates_arr = json_decode( get_option( 'orddd_delivery_dates' ) );
    				    } 

    				    if( is_array( $delivery_dates_arr ) &&  count( $delivery_dates_arr ) > 0 ) {
                            $date = '';
    				        if( !preg_match( '/[A-Za-z]/', $delivery_date ) ) {
    				            $date = date( 'n-j-Y', strtotime( $delivery_date ) );
                            }

                            foreach ( $delivery_dates_arr as $key => $value ) {
                                foreach ( $value as $k => $v ) {
                                    $temp_arr[ $k ] = $v;
                                }

                                if ( $date == $temp_arr[ 'date' ] ) {
                                    $fees_to_add = $temp_arr[ 'fees' ];
                                    $delivery_charges_label = $temp_arr[ 'label' ];
                                    $specific_fees = 'Yes';

                                    /**
                                     * Filter orddd_global_specific_delivery_dates can be used to 
                                     * override the specific charges added based on some conditions
                                     * like if you only want the charges applied on a specific 
                                     * product or variation id
                                     */
                                    if ( has_filter( 'orddd_global_specific_delivery_dates' ) ) {
                                        $specific_date_params = apply_filters( 'orddd_global_specific_delivery_dates', $specific_fees, $specific_date_arr[ 0 ], $fees_to_add, $delivery_charges_label );
                                        if ( isset( $specific_date_params ) && is_array( $specific_date_params ) ) {
                                            $specific_fees = $specific_date_params[ 'specific_fees' ];
                                            $fees_to_add   = $specific_date_params[ 'specific_day_charges' ];
                                            $delivery_charges_label = $specific_date_params[ 'specific_charges_label' ];
                                        }
                                    }                                    
                                }
                                $delivery_dates_array[] = $temp_arr[ 'date' ];
                            }
                        }
                    }
    			
                    if ( get_option( 'orddd_enable_same_day_delivery' ) == 'on' ) {
                        if( !preg_match( '/[A-Za-z]/', $delivery_date ) ) {                        
    				        if ( $current_date == $delivery_date ) {
    				            $same_day_delivery_charges_label = __( 'Same Day Delivery Charges', 'order-delivery-date' );
                                $same_day_fees = get_option( 'orddd_same_day_additional_charges' );
                            }
                        }
                    }
                    
                    if ( get_option( 'orddd_enable_next_day_delivery' ) == 'on' ) {
                        if( !preg_match( '/[A-Za-z]/', $delivery_date ) ) {
                            $add_next_day_charges = 'no';
                            if ( $next_date == $delivery_date ) {
                                $add_next_day_charges = 'yes'; 
                            } else {
                                if( is_array( $additional_weekdays_settings ) &&  count( $additional_weekdays_settings ) > 0 && isset( $additional_weekdays_settings[ 'orddd_before_cutoff_weekday' ] ) ) {
                                    $delivery_date_weekday = date( 'w', strtotime( $delivery_date ) );
                                    if( 'orddd_weekday_' . $delivery_date_weekday == $additional_weekdays_settings[ 'orddd_before_cutoff_weekday' ] ) {
                                        $add_next_day_charges = 'yes';
                                    }
                                }
                            }

                            if( 'yes' == $add_next_day_charges ) {
                                $next_day_delivery_charges_label = __( 'Next Day Delivery Charges', 'order-delivery-date' );
                                if ( has_filter( 'orddd_change_next_day_delivery_charges_label' ) ) {
                                    $next_day_delivery_charges_label = apply_filters( 'orddd_change_next_day_delivery_charges_label', $next_day_delivery_charges_label );
                                }
                                
                                $next_day_fees = get_option( 'orddd_next_day_additional_charges' );
                            } 
                        }
                    }

                    
                    if ( 'on' === get_option( 'orddd_delivery_charges_based_on_days_enable' ) && $fees_to_add == 0 && $same_day_fees == 0 && $next_day_fees == 0 ) {
                         // calculates number of day for selected delivery date from today to get delivery charge for that day.
                        $no_of_day_from_today = orddd_common::orddd_get_next_available_day( $delivery_date, $shipping_settings_to_check );

                        $orddd_additional_settings_based_on_days = get_option( 'orddd_additional_settings_based_on_days' );
                        if ( in_array( $no_of_day_from_today, $orddd_additional_settings_based_on_days['number'] ) ) {
                            $key_for_additional = array_search( $no_of_day_from_today, $orddd_additional_settings_based_on_days['number'] );
                            $additional_label   = $orddd_additional_settings_based_on_days['label'][ $key_for_additional ];
                            $additional_charge  = $orddd_additional_settings_based_on_days['charge'][ $key_for_additional ];
                            if ( $additional_charge ) {
                                $fees_to_add = $additional_charge;
                            }
                            if ( $additional_label ) {
                                $delivery_charges_label = $additional_label;
                            }
                        }
                    } 
                    
                    if ( !$fees_to_add && 'on' == get_option( 'orddd_enable_day_wise_settings' ) ) {
                        $day = '';
                        if( !preg_match('/[A-Za-z]/', $delivery_date ) ) {
                            $day = date( 'w', strtotime( $delivery_date ) );
                        }

                        $fee_var = "additional_charges_orddd_weekday_" . $day;
                        $fees_to_add = get_option( $fee_var );

                        $delivery_charges_var = "delivery_charges_label_orddd_weekday_" . $day;
                        $delivery_charges_label = get_option( $delivery_charges_var );
                    }
                }

                if( has_filter( 'orddd_add_delivery_date_fees' ) ) {
                    $fees_to_add = apply_filters('orddd_add_delivery_date_fees', $delivery_date, $fees_to_add );
                }

                if ( class_exists( 'WOOCS' ) ) {
                    global $WOOCS;
                    if ( $WOOCS->is_multiple_allowed ) {
                        $fees_to_add = $WOOCS->woocs_exchange_value( floatval( $fees_to_add ) );
                    }
                }

                if( $time_slot_fees_to_add > 0 && $time_slot_fees_to_add != "" ) {
                    if( get_option( 'orddd_enable_tax_calculation_for_delivery_charges' ) == 'on' ) {
                        $shipping_tax_class = get_option( 'woocommerce_shipping_tax_class' );
                        if( is_object( $cart ) ) {
                            WC()->cart->add_fee( __( $time_slot_charges_label, 'order-delivery-date' ), $time_slot_fees_to_add , true, $shipping_tax_class );
                        }
                    } else {
                        if( is_object( $cart ) ) {
                            WC()->cart->add_fee( __( $time_slot_charges_label, 'order-delivery-date' ), $time_slot_fees_to_add , false );
                        }
                    }
                   	$total_fees += $time_slot_fees_to_add;
                }

                $fees_to_add = floatval( $fees_to_add );
                
                if ( $fees_to_add != 0 ) {
                  
                    if( isset( $delivery_charges_label ) && $delivery_charges_label != '' ) {
                        if( get_option( 'orddd_enable_tax_calculation_for_delivery_charges' ) == 'on' ) {
                            $shipping_tax_class = get_option( 'woocommerce_shipping_tax_class' );
                            if( is_object( $cart ) ) {
                                WC()->cart->add_fee( __( $delivery_charges_label, 'order-delivery-date' ), $fees_to_add , true, $shipping_tax_class );
                            }
                        } else {
                            if( is_object( $cart ) ) {
                                WC()->cart->add_fee( __( $delivery_charges_label, 'order-delivery-date' ), $fees_to_add , false );
                            }
                        }
                    } else {
                        if( get_option( 'orddd_enable_tax_calculation_for_delivery_charges' ) == 'on' ) {
                            $shipping_tax_class = get_option( 'woocommerce_shipping_tax_class' );
                            if( is_object( $cart ) ) {
                                WC()->cart->add_fee( __( 'Delivery Charges', 'order-delivery-date' ), $fees_to_add , true, $shipping_tax_class );
                            }
                        } else {
                            if( is_object( $cart ) ) {
                                WC()->cart->add_fee( __( 'Delivery Charges', 'order-delivery-date' ), $fees_to_add , false );    
                            }
                            
                        }
                    }
                    
                    $total_fees += floatval( $fees_to_add );
                }
                
                if ( has_filter( 'orddd_change_same_day_delivery_charges_label' ) ) {
                    $same_day_delivery_charges_label = apply_filters( 'orddd_change_same_day_delivery_charges_label', $same_day_delivery_charges_label );
                }

                if ( $same_day_fees > 0 ) {
                    if ( class_exists( 'WOOCS' ) ) {
                        global $WOOCS;
                        if ( $WOOCS->is_multiple_allowed ) {
                            $same_day_fees = $WOOCS->woocs_exchange_value( floatval( $same_day_fees ) );
                        }
                    }
                    if( get_option( 'orddd_enable_tax_calculation_for_delivery_charges' ) == 'on' ) {
                        $shipping_tax_class = get_option( 'woocommerce_shipping_tax_class' );
                        if( is_object( $cart ) ) {
                            WC()->cart->add_fee( __( $same_day_delivery_charges_label, 'order-delivery-date' ), $same_day_fees, true, $shipping_tax_class );
                        }
                    } else {
                        if( is_object( $cart ) ) {
                            WC()->cart->add_fee( __( $same_day_delivery_charges_label, 'order-delivery-date' ), $same_day_fees, false );
                        }
                    }
                    $total_fees += $same_day_fees;
                }

                if ( has_filter( 'orddd_change_same_day_delivery_charges_label' ) ) {
                    $next_day_delivery_charges_label = apply_filters( 'orddd_change_same_day_delivery_charges_label', $next_day_delivery_charges_label );
                }

                if ( $next_day_fees > 0 ) {
                    if ( class_exists( 'WOOCS' ) ) {
                        global $WOOCS;
                        if ( $WOOCS->is_multiple_allowed ) {
                            $next_day_fees = $WOOCS->woocs_exchange_value( floatval( $next_day_fees ) );
                        }
                    }
                    if( get_option( 'orddd_enable_tax_calculation_for_delivery_charges' ) == 'on' ) {
                        $shipping_tax_class = get_option( 'woocommerce_shipping_tax_class' );
                        if( is_object( $cart ) ) {
                            WC()->cart->add_fee( __( $next_day_delivery_charges_label, 'order-delivery-date' ), $next_day_fees, true, $shipping_tax_class );
                        }
                    } else {
                        if( is_object( $cart ) ) {
                            WC()->cart->add_fee( __( $next_day_delivery_charges_label, 'order-delivery-date' ), $next_day_fees, false );
                        }
                    }
                    $total_fees += $next_day_fees;
                }	
    		}

    		WC()->session->set( '_total_delivery_charges', $total_fees );
		}
	}
	
    /**
     * Remove charges if the cart total is more than the amount set.
     *
     * @param float $max_order_total
     * @return bool
     * 
     * @since 9.25.0
     */
    public static function orddd_to_remove_charges( $max_order_total ) {
        $cart_subtotal       = WC()->cart->get_cart_contents_total();
        $cart_shipping_total = WC()->cart->get_shipping_total();
        $cart_fees_total     = WC()->cart->get_fee_total();
        $cart_total_tax      = wc_round_tax_total( WC()->cart->get_cart_contents_tax() + WC()->cart->get_shipping_tax() + WC()->cart->get_fee_tax() );
        
        $cart_total = $cart_subtotal + $cart_shipping_total + $cart_fees_total + $cart_total_tax;

        if( $cart_total >= $max_order_total && $max_order_total > 0 ) {
            return true;
        }

        return false;
    }

	/**
	 * Add Time slot drop down on select of the date on checkout page
     *
     * @hook wp_ajax_nopriv_check_for_time_slot_orddd
     * @hook wp_ajax_check_for_time_slot_orddd
     *
     * @globals array $orddd_weekdays Weekdays array
     * @globals resource $wpdb WordPress Object
     *
     * @since 2.4
	 */
	public static function check_for_time_slot_orddd( $delivery_date, $setting_id, $common_ids ) {
		
        $time_format_to_show          = orddd_common::orddd_get_time_format(); 
        $time_slots_to_show_timestamp = orddd_common::orddd_get_timeslot_display( $delivery_date, $setting_id, $common_ids );
        $auto_populate_time_slot      = get_option( 'orddd_auto_populate_first_available_time_slot' );

		asort( $time_slots_to_show_timestamp );
		if ( is_array( $time_slots_to_show_timestamp ) && count( $time_slots_to_show_timestamp ) > 1 ) {
			unset( $time_slots_to_show_timestamp[ 'NA' ] );
        }

        //Additional time slot to check
        $additional_time_slot = '';
        if( has_filter( 'orddd_populate_delivery_time' ) ) {
            $additional_time_slot_str = apply_filters( 'orddd_populate_delivery_time', '' );

            $additional_time_slot_arr = explode( " - ", $additional_time_slot_str );
            $ft = '';
            if( isset( $additional_time_slot_arr[ 0 ] ) ) {
                $ft = date( $time_format_to_show, strtotime( $additional_time_slot_arr[ 0 ] ) );
            } 

            if( isset( $additional_time_slot_arr[ 1 ] ) ) {
                $tt = date( $time_format_to_show, strtotime( $additional_time_slot_arr[ 1 ] ) );
                $additional_time_slot = $ft . " - " . $tt;
            } else {
                $additional_time_slot = $ft;
            }
        }

        $timeslots = array();
        foreach ( $time_slots_to_show_timestamp as $key => $value ) {
            $current_date           = isset( $_POST[ 'current_date' ] ) ? $_POST[ 'current_date' ] : false;
            $timeslot_charges       = orddd_common::orddd_get_timeslot_charges( $setting_id, $key, $current_date );
            $timeslot_charges_label = '';
            
            if ( "" != $timeslot_charges ) {
                $timeslot_charges_label = "(" . $timeslot_charges . ")";
                if( has_filter( 'orddd_timeslot_charges_label' ) ) {
                    $timeslot_charges_label = apply_filters( 'orddd_timeslot_charges_label', $timeslot_charges_label );
                }
            }

            $key_i18n = '';
            if ( 'asap' !== $key && 'NA' !== $key ) {

                if( $time_format_to_show === 'h:i A' ) {
                    $key = orddd_common::orddd_change_time_slot_format( $key, $time_format_to_show );
                }

                $time_arr = explode( ' - ', $key );

                $from_time = date_i18n( $time_format_to_show, strtotime( $current_date . " " . $time_arr[0] ) );
                if ( isset( $time_arr[1] ) ) {
					$to_time  = date_i18n( $time_format_to_show, strtotime( $current_date . ' ' . $time_arr[1] ) );
					$key_i18n = $from_time . ' - ' . $to_time;
				} else {
					$key_i18n = $from_time;
				}
            }
            
            $timeslots[] = array( 
                'time_slot' => $key,
                'time_slot_i18n' => $key_i18n,
                'charges' => $timeslot_charges_label,
            );
        }

        return $timeslots;
	}
	
    /**
     * Display all the timeslots on Edit order page.
     *
     * @hook wp_ajax_orddd_display_timeslots_admin
     *
     * @since 9.27.0
     */
    public static function orddd_display_timeslots_admin() {
        $time_format_to_show = orddd_common::orddd_get_time_format(); 
        
        // Time slot selected for the order. This is for the edit order page in admin. 
		$time_slot_for_order = '';
		if( isset( $_POST[ 'order_id' ] ) ) {
		    $order_id            = $_POST[ 'order_id' ];
            $time_slot_for_order = orddd_common::orddd_get_order_timeslot( $order_id );
		}

		$time_slot_arr = orddd_holidays_settings::get_all_timeslots( $time_format_to_show );

        // removing the asap element as it doesn't appear at right place in the dropdown for admin
        unset( $time_slot_arr[ 'asap' ] );

		$time_slots_to_show_timestamp = array_flip( $time_slot_arr );
		asort( $time_slots_to_show_timestamp );
		if ( is_array( $time_slots_to_show_timestamp ) && count( $time_slots_to_show_timestamp ) > 1 ) {
			unset( $time_slots_to_show_timestamp[ 'NA' ] );
        }

        // adding back the asap element in the dropdown
        $asap_array['asap']           = __( 'As Soon As Possible.', 'order-delivery-date' ) ;
        $time_slots_to_show_timestamp = $asap_array + $time_slots_to_show_timestamp;

        if( __( 'As Soon As Possible.', 'order-delivery-date' ) == $time_slot_for_order ) {
            $time_slot_for_order = 'asap';
        }

        $i = 1;
        $time_slot_var = '';
        // Changing the seperator for time slots from comma(,) to backslash(/) to avoid conflicts with the 
        // decimal seperator and thousand seperator. 
        $time_slot_var .= "select/";
        $timeslots = array();
        foreach ( $time_slots_to_show_timestamp as $key => $value ) {
            
            $sel = '';
            if( $time_slot_for_order != '' && $key == $time_slot_for_order ) {
                $sel = 'selected';
            }

            $current_date = isset( $_POST[ 'current_date' ] ) ? $_POST[ 'current_date' ] : false;
            
            $timeslot_charges = orddd_common::orddd_get_timeslot_charges( '0', $key, $current_date );

            $timeslot_charges_label = '';
            if ( "" != $timeslot_charges ) {
                $timeslot_charges_label = "(" . $timeslot_charges . ")";
                if( has_filter( 'orddd_timeslot_charges_label' ) ) {
                    $timeslot_charges_label = apply_filters( 'orddd_timeslot_charges_label', $timeslot_charges_label );
                }
            }

            $key_i18n = '';
            if ( 'asap' !== $key && 'NA' !== $key ) {

                if( $time_format_to_show === 'h:i A' ) {
                    $key = orddd_common::orddd_change_time_slot_format( $key, $time_format_to_show );
                }

                $time_arr = explode( ' - ', $key );

                $from_time = date_i18n( $time_format_to_show, strtotime( $current_date . " " . $time_arr[0] ) );
                if ( isset( $time_arr[1] ) ) {
					$to_time  = date_i18n( $time_format_to_show, strtotime( $current_date . ' ' . $time_arr[1] ) );
					$key_i18n = $from_time . ' - ' . $to_time;
				} else {
					$key_i18n = $from_time;
				}
            }

            if( $sel != '' ) {
                $time_slot_var .=  $key . "_" . $key_i18n . "_" . $timeslot_charges_label . "_" . $sel . "/";
            } else {
                $time_slot_var .=  $key . "_" . $key_i18n . "_" . $timeslot_charges_label . "/";
            }

            $i++;
        }

        $time_slot_var = substr( $time_slot_var, 0, strlen( $time_slot_var )-1 );
        wp_send_json( $time_slot_var );
    }
	/**
     * 
     * 
	 * Add selected delivery date in the post meta
	 * 
     * @hook woocommerce_checkout_update_order_meta
     * @globals resource $wpdb WordPress Object
     * @globals resource $woocommerce WooCommerce Object
     * 
	 * @param int $order_id Order ID
     * @since 1.0
	 */
	public static function orddd_update_order_meta_delivery_date( $order_id ) {
	    global $wpdb, $woocommerce, $orddd_date_formats;

        $shipping_method          = '';
        $product_category         = '';
        $categories               = array();
        $shipping_classes         = array();
        $shipping_class           = '';
        $location                 = '';
        $orddd_zone_id            = 0;
        $shipping_package_to_load = '';
		$selected_date_format     = get_option( 'orddd_delivery_date_format' );
    
        if( ! $order_id ) {
            return false;
        }

        $order = wc_get_order( $order_id );

        if( isset( $_POST[ 'orddd_zone_id' ] ) ) {
            $orddd_zone_id = $_POST[ 'orddd_zone_id' ];    
        }

        if( isset( $_POST[ 'orddd_locations' ] ) && $_POST[ 'orddd_locations' ] != '' ) {
            $location = $_POST[ 'orddd_locations' ];
        }

        // Check for the shipping methods for shipping packages from Advance Shipping Packages plugin for WooCommerce.
        // By Default, it will take the first shipping package method. It can be changed using orddd_shipping_package_to_load hook.
        if( class_exists( 'orddd_advance_shipping_compatibility' ) && 
            class_exists( 'Advanced_Shipping_Packages_for_WooCommerce' ) ) {
            if( isset( $_POST[ 'orddd_shipping_package_to_load' ] ) ) {
                $shipping_package_to_load = $_POST[ 'orddd_shipping_package_to_load' ];
            }
        }

        if( $shipping_package_to_load != '' &&
            isset( $_POST[ 'shipping_method' ][ $shipping_package_to_load ] ) && 
            $_POST[ 'shipping_method'][ $shipping_package_to_load ] != '' && 
            is_array( $_POST[ 'shipping_method' ] ) ) {
            $shipping_method = $_POST[ 'shipping_method' ][ $shipping_package_to_load ] . ":" . $shipping_package_to_load;
        }else if( isset( $_POST[ 'shipping_method' ][ 0 ] ) && 
            $_POST[ 'shipping_method'][ 0 ] != '' && 
            is_array( $_POST[ 'shipping_method' ] ) ) {
            $shipping_method = $_POST[ 'shipping_method' ][ 0 ];
            if( false !== strpos( $shipping_method, 'usps' ) ) {
                $shipping_method = $orddd_zone_id . ":" . $shipping_method;
            }
            if( strpos( $shipping_method, 'wf_fedex_woocommerce_shipping' )  === false && false !== strpos( $shipping_method, 'fedex' ) && is_array( explode( ":", $shipping_method ) ) && count( explode( ":", $shipping_method ) ) < 3 ) {
                $shipping_method = $orddd_zone_id . ":" . $shipping_method;
            }
        } else if( isset( $_POST[ 'shipping_method' ] ) && $_POST[ 'shipping_method' ] != '' ) {
            if ( is_array( $_POST[ 'shipping_method' ] ) ) {
                $shipping_method = current( $_POST[ 'shipping_method' ] );
            } else {
                $shipping_method = $_POST[ 'shipping_method' ];
            }
            if( false !== strpos( $shipping_method, 'usps') ) {
                $shipping_method = $orddd_zone_id . ":" . $shipping_method;
            }

            if( strpos( $shipping_method, 'wf_fedex_woocommerce_shipping' )  === false && false !== strpos( $shipping_method, 'fedex' ) && is_array( explode( ":", $shipping_method ) ) && count( explode( ":", $shipping_method ) ) < 3 ) {
                $shipping_method = $orddd_zone_id . ":" . $shipping_method;
            }
        }

        $orddd_settings = orddd_common::orddd_localize_data_script();
        if( isset( $orddd_settings['orddd_category_settings_to_load'] ) && $orddd_settings[ 'orddd_category_settings_to_load'] != '' ) {
            $product_category = $orddd_settings['orddd_category_settings_to_load'];
            $categories = explode( ",", $product_category );
        }

        if( isset( $orddd_settings['orddd_shipping_class_settings_to_load'] ) && $orddd_settings['orddd_shipping_class_settings_to_load'] != '' ) {
            $shipping_class = $orddd_settings['orddd_shipping_class_settings_to_load'];
            $shipping_classes = explode( ",", $shipping_class );
        }

        $delivery_date_label = orddd_common::orddd_get_delivery_date_field_label( $shipping_method, $categories, $shipping_classes, $location ); 

        // Compatibility with 'WooCommerce Beanstream Gateway' plugin.
        if( !isset( $_POST[ 'is_my_account' ] ) ) {
            $payment_method = get_post_meta( $order_id, '_payment_method', true );    
            if ( 'beanstream' == $payment_method ) {            
                if ( WC()->cart->needs_payment() ) {
                    // Payment Method
                    $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
                } else {
                    $available_gateways = array();
                }
                $result = $available_gateways[ $payment_method ]->process_payment( $order_id );
                if ( !isset( $result[ 'result' ] ) ) {
                    return;
                }
            }
        }

        $total_fees = WC()->session->get( '_total_delivery_charges' );
        if( '' != $total_fees && null != $total_fees ) {
        	update_post_meta( $order_id, '_total_delivery_charges', $total_fees );   
        	WC()->session->__unset( '_total_delivery_charges' );   
        	WC()->session->__unset( 'h_deliverydate' );   //
        	WC()->session->__unset( 'hidden_h_deliverydate' );   
        	WC()->session->__unset( 'orddd_time_slot' );   
        	WC()->session->__unset( 'hidden_timeslot' );   
        	WC()->session->__unset( 'orddd_zone_id' );   
        	WC()->session->__unset( 'orddd_category_settings_to_load' );   
        	WC()->session->__unset( 'orddd_shipping_class_settings_to_load' );   
        	WC()->session->__unset( 'orddd_locations' );   
        	WC()->session->__unset( 'orddd_shipping_method' );   
        } else {
        	update_post_meta( $order_id, '_total_delivery_charges', '0' );   
        }

        if( $location != '' && $location != 'select_location' ) {
            update_post_meta( $order_id, '_orddd_location', $location );      
            $address = '';
            $locations = get_option( 'orddd_locations', true );
            if( is_array( $locations ) && count( $locations ) > 0 ) {
                foreach ( $locations as $key => $value ) {
                    if( isset( $value[ 'row_id' ] ) && $value[ 'row_id' ] == $location ) {
                        $address = orddd_locations::orddd_get_formatted_address( $value, true );
                    }
                }
            }

            if( $address != '' ) {
                $locations_label = '' != get_option( 'orddd_location_field_label' ) ? get_option( 'orddd_location_field_label' ) : 'Pickup Location';
                update_post_meta( $order_id, $locations_label, $address );      
                update_post_meta( $order_id, '_pickup_location', $address );      
            }
        }

		if ( isset( $_POST[ 'e_deliverydate' ] ) && $_POST[ 'e_deliverydate' ] != '' ) {
		    if( isset( $_POST[ 'h_deliverydate' ] ) ) {	    
                $delivery_date = $_POST[ 'h_deliverydate' ];
		    } else {
		        $delivery_date = '';
		    }
		    $date_format = 'dd-mm-y';
		    
		    if( has_filter( 'orddd_before_delivery_date_update' ) ) {
			   $delivery_date = apply_filters( 'orddd_before_delivery_date_update', $delivery_date );
			}
			
            $shipping_based = "No";
            $results = orddd_common::orddd_get_shipping_settings();
            $shipping_settings = array();
            $shipping_based_lockout = "No";
            if( get_option( 'orddd_enable_shipping_based_delivery' ) == 'on' && is_array( $results ) && count( $results ) > 0 ) {
                foreach ( $results as $key => $value ) {
                    $shipping_based_lockout = "No";
                    $shipping_methods = array();
                    $shipping_settings = get_option( $value->option_name );
                    $is_category_based      = false;

                    if( isset( $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] ) && $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] == 'shipping_methods' ) {
                        if( isset( $shipping_settings[ 'shipping_methods' ] ) ) {
                            $shipping_methods = $shipping_settings[ 'shipping_methods' ];
                        }
                    } else if( isset( $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] ) && $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] == 'product_categories' ) {
                        if ( isset( $shipping_settings['shipping_methods_for_categories'] ) ) {
                            $shipping_methods = $shipping_settings[ 'shipping_methods_for_categories' ];
                            $shipping_method_for_category = true;
                        } else {
                            $shipping_method_for_category = false;
                        }

                        if( isset( $shipping_settings[ 'product_categories' ] ) ) {
                            $selected_categories = $shipping_settings[ 'product_categories' ];
                        } 
                        $is_category_based = true;
                    }
                    
                    if( has_filter( 'orddd_get_shipping_method' ) ) {
                        $shipping_methods_values = apply_filters( 'orddd_get_shipping_method', $shipping_settings, $_POST, $shipping_methods, $shipping_method  );    
                        $shipping_methods = $shipping_methods_values[ 'shipping_methods' ];
                        $shipping_method = $shipping_methods_values[ 'shipping_method' ];    
                    }
                    
                    if( !$is_category_based && in_array( $shipping_method, $shipping_methods ) ) {
                        $shipping_based = "Yes"; 
                    } elseif( $is_category_based && ( in_array( $product_category, $selected_categories ) ) ) {
                        $shipping_based = "Yes"; 

                        if ( $shipping_method_for_category && !in_array( $shipping_method, $shipping_methods ) ){
                            $shipping_based = "No"; 
                        }
                    }

                    if( 'Yes' === $shipping_based ) {
                        if( isset( $_POST[ 'time_setting_enable_for_shipping_method' ] ) && $_POST[ 'time_setting_enable_for_shipping_method' ] == 'on' ) {
                            $time_setting[ 'enable' ] = $_POST[ 'time_setting_enable_for_shipping_method' ];
                            $time_setting[ 'time_selected' ] = $_POST[ 'orddd_time_settings_selected' ];
                        } else {
                            $time_setting = '';
                        }  
                    }
                }

                if( is_plugin_active( 'woocommerce-shipping-canada-post/woocommerce-shipping-canada-post.php' ) ) {
                    $canada_post_id = WC()->session->get('chosen_shipping_methods');
                    update_post_meta( $order_id, '_orddd_canada_post_id', $canada_post_id[0] );
                }
            } 

            if ( $shipping_based == "No" ) {
                if( get_option( 'orddd_enable_delivery_time' ) == 'on' ) {
                    $time_setting[ 'enable' ] = get_option( 'orddd_enable_delivery_time' );
                    $time_setting[ 'time_selected' ] = $_POST[ 'orddd_time_settings_selected' ];
                } else {
                    $time_setting = '';
                }
            }

            $e_deliverydate = esc_attr( $_POST[ 'e_deliverydate' ] );
            if( 'yes' === get_option( 'orddd_delivery_dates_in_dropdown' ) ) {
                $e_deliverydate = date_i18n( $orddd_date_formats[ $selected_date_format ], strtotime( $e_deliverydate ) );
            }
          
            update_post_meta( $order_id, $delivery_date_label , $e_deliverydate );
            $timestamp = orddd_common::orddd_get_timestamp( $delivery_date, $date_format, $time_setting );
            
			update_post_meta( $order_id, '_orddd_timestamp', $timestamp );
						
            do_action( 'orddd_after_delivery_date_update', $order_id, $delivery_date, $_POST );

            $category_settings_applied = orddd_custom_delivery_functions::orddd_get_common_categories( $categories, $shipping_method );
            $shipping_class_settings_applied = orddd_custom_delivery_functions::orddd_get_common_shipping_classes( $shipping_classes );
             
            if( is_array( $category_settings_applied ) && count( $category_settings_applied ) > 1 ) {
                $count = 0;
                foreach( $category_settings_applied as $id => $category ) {                  
                    update_post_meta( $order_id, '_orddd_delivery_schedule_id_' . $count, $id );
                    $count++;
                }
                update_post_meta( $order_id, '_orddd_total_settings_applied', count( $category_settings_applied ) );
            } elseif ( is_array( $shipping_class_settings_applied ) && count( $shipping_class_settings_applied ) > 1 ) {
                $count = 0;
                foreach( $shipping_class_settings_applied as $id => $category ) {                  
                    update_post_meta( $order_id, '_orddd_delivery_schedule_id_' . $count, $id );
                    $count++;
                }
                update_post_meta( $order_id, '_orddd_total_settings_applied', count( $shipping_class_settings_applied ) );
            }
            
            if ( isset( $_POST[ 'orddd_unique_custom_settings' ] ) && '' !== $_POST[ 'orddd_unique_custom_settings' ] ) {
                $delivery_schedule_hidden_var = $_POST[ 'orddd_unique_custom_settings' ];
                orddd_custom_delivery_functions::orddd_update_delivery_schedule_id( $order_id, $delivery_schedule_hidden_var );
            } else {
                orddd_custom_delivery_functions::orddd_update_delivery_schedule_id( $order_id, 'global_settings' );
            }

            if ( class_exists( 'WC_Subscriptions' ) && get_option( 'orddd_enable_woo_subscriptions_compatibility' ) == 'on' ) {
                $subscriptions = wcs_get_subscriptions_for_order( $order_id, array( 'order_type' => 'any' ) );
                
                if( is_array( $subscriptions ) && count( $subscriptions ) > 0 ) {
                    $subscription = array_values( $subscriptions )[0];
                    $subscription_id = $subscription->get_id();
                    update_post_meta( $subscription_id, '_orddd_timestamp', $timestamp );
                    update_post_meta( $subscription_id, $delivery_date_label , $e_deliverydate );
                }
                
            }
		} else {
		    $is_delivery_enabled = orddd_common::orddd_is_delivery_enabled();
            if( $is_delivery_enabled == 'yes' ) {
                update_post_meta( $order_id, get_option( 'orddd_delivery_date_field_label' ), '' );
		    }	
		}
	}
	
	/**
	 * Add selected time slot in the post meta
	 *
	 * @hook woocommerce_checkout_update_order_meta
     * @globals resource $wpdb WordPress Object
     * @globals resource $woocommerce WooCommerce Object
     * 
     * @param int $order_id Order ID
     * @since 1.0
	 */
	
	public static function orddd_update_order_meta_time_slot( $order_id ) {
        $shipping_method = '';
        $product_category = '';
        $shipping_class = '';
        $location = '';
        $categories = array();
        $shipping_classes = array();
        $shipping_package_to_load = '';
        $order_time_slot = '';
        if( ! $order_id ) {
            return false;
        }

        $order = wc_get_order( $order_id );

		if ( ! is_object( $order ) ) {
			return;
        }
            
        $orddd_zone_id = 0;
        if( isset( $_POST[ 'orddd_zone_id' ] ) ) {
            $orddd_zone_id = $_POST[ 'orddd_zone_id' ];    
        }


        if( isset( $_POST[ 'orddd_locations' ] ) && $_POST[ 'orddd_locations'] != '' ) {
            $location = $_POST[ 'orddd_locations' ];
        }

        // Check for the shipping methods for shipping packages from Advance Shipping Packages plugin for WooCommerce.
        // By Default, it will take the first shipping package method. It can be changed using orddd_shipping_package_to_load hook.
        if( class_exists( 'orddd_advance_shipping_compatibility' ) && 
            class_exists( 'Advanced_Shipping_Packages_for_WooCommerce' ) ) {
            if( isset( $_POST[ 'orddd_shipping_package_to_load' ] ) ) {
                $shipping_package_to_load = $_POST[ 'orddd_shipping_package_to_load' ];
            }
        }
   
        if( $shipping_package_to_load != '' &&
            isset( $_POST[ 'shipping_method' ][ $shipping_package_to_load ] ) && 
            $_POST[ 'shipping_method'][ $shipping_package_to_load ] != '' && 
            is_array( $_POST[ 'shipping_method' ] ) ) {
            $shipping_method = $_POST[ 'shipping_method' ][ $shipping_package_to_load ] . ":" . $shipping_package_to_load;
        }else if( isset( $_POST[ 'shipping_method' ][ 0 ] ) && $_POST[ 'shipping_method'][ 0 ] != '' && is_array( $_POST[ 'shipping_method' ] ) ) {
            $shipping_method = $_POST[ 'shipping_method' ][ 0 ];
            if( false !== strpos( $shipping_method, 'usps' ) ) {
                $shipping_method = $orddd_zone_id . ":" . $shipping_method;
            }
            if( strpos( $shipping_method, 'wf_fedex_woocommerce_shipping' )  === false && false !== strpos( $shipping_method, 'fedex' ) && is_array( explode( ":", $shipping_method ) ) && count( explode( ":", $shipping_method ) ) < 3 ) {
                $shipping_method = $orddd_zone_id . ":" . $shipping_method;
            }
        } else if( isset( $_POST[ 'shipping_method' ] ) && $_POST[ 'shipping_method' ] != '' ) {
            $shipping_method = $_POST[ 'shipping_method' ];
            if( false !== strpos( $shipping_method, 'usps') ) {
                $shipping_method = $orddd_zone_id . ":" . $shipping_method;
            }
            if( strpos( $shipping_method, 'wf_fedex_woocommerce_shipping' )  === false && false !== strpos( $shipping_method, 'fedex' ) && is_array( explode( ":", $shipping_method ) ) && count( explode( ":", $shipping_method ) ) < 3 ) {
                $shipping_method = $orddd_zone_id . ":" . $shipping_method;
            }
        }

        if( isset( $_POST[ 'orddd_category_settings_to_load' ] ) && $_POST[ 'orddd_category_settings_to_load'] != '' ) {
            $product_category = $_POST[ 'orddd_category_settings_to_load' ];
            $categories = explode( ",", $product_category );
        }

        if( isset( $_POST[ 'orddd_shipping_class_settings_to_load' ] ) && $_POST[ 'orddd_shipping_class_settings_to_load' ] != '' ) {
            $shipping_class = $_POST[ 'orddd_shipping_class_settings_to_load' ];
            $shipping_classes = explode( ",", $shipping_class );
        }

        $time_slot_label = orddd_common::orddd_get_delivery_time_field_label( $shipping_method, $categories, $shipping_classes, $location ); 

        $delivery_date_label = orddd_common::orddd_get_delivery_date_field_label( $shipping_method, $categories, $shipping_classes, $location ); 
        // Compatibility with 'WooCommerce Beanstream Gateway' plugin.
        if( !isset( $_POST[ 'is_my_account' ] ) ) {
            $payment_method = get_post_meta( $order_id, '_payment_method', true );
            if ( 'beanstream' == $payment_method ) {
                if ( WC()->cart->needs_payment() ) {
                    // Payment Method
                    $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
                }
                if ( isset( $available_gateways ) && '' != $available_gateways ) {             
                    $result = $available_gateways[ $payment_method ]->process_payment( $order_id );
                    if ( !isset( $result[ 'result' ] ) ) {
                        return;
                    }
                }
            }
        }

	    if ( isset( $_POST[ 'orddd_time_slot' ] ) && $_POST[ 'orddd_time_slot' ] != '' ) {
			$time_slot = $_POST[ 'orddd_time_slot' ];
			
	        if( has_filter( 'orddd_before_timeslot_update' ) ) {
			    $time_slot = apply_filters( 'orddd_before_timeslot_update', $time_slot );
			}
			
            $h_deliverydate = '';
            if( isset( $_POST[ 'h_deliverydate' ] ) ) {
                $h_deliverydate = $_POST[ 'h_deliverydate' ];
            }

			if ( $time_slot != '' && $time_slot != 'choose' && $time_slot != 'NA' && $time_slot != 'select' ) {
                if( 'asap' == $time_slot ) {
                    update_post_meta( $order_id, $time_slot_label, esc_attr( __( 'As Soon As Possible.', 'order-delivery-date' ) ) );
                    update_post_meta( $order_id, '_orddd_time_slot', esc_attr( __( 'As Soon As Possible.', 'order-delivery-date' ) ) );
                } else {
                    $order_time_slot = $time_slot;
                    $time_format = get_option( 'orddd_delivery_time_format' );
                    $time_slot_arr = explode( ' - ' , $time_slot );

                    if ( $time_format == '1' ) {
                        $from_time = date( 'H:i', strtotime( $time_slot_arr[ 0 ] ) );
                        if( isset( $time_slot_arr[ 1 ] ) ) {
                            $to_time = date( 'H:i', strtotime( $time_slot_arr[ 1 ] ) );
                            $order_time_slot = $from_time . " - " . $to_time;
                        } else {
                            $order_time_slot = $from_time;
                        }
                    }else {
                        $from_time = date( 'H:i', strtotime( $time_slot_arr[ 0 ] ) );
                    }
                    update_post_meta( $order_id, $time_slot_label, esc_attr( $time_slot ) );
                    update_post_meta( $order_id, '_orddd_time_slot', $order_time_slot );

                    // Store the timestamp of delivery date along with the start time of the time slot in the postmeta table
                    // with meta key _orddd_timeslot_timestamp
                    $time_setting = array( 
                        'enable' => 'on', 
                        'time_selected' => $from_time 
                    );

                    $timestamp = orddd_common::orddd_get_timestamp( $h_deliverydate, 'dd-mm-y', $time_setting );
                    update_post_meta( $order_id, '_orddd_timeslot_timestamp', $timestamp );
                }
                
                if ( class_exists( 'WC_Subscriptions' ) && get_option( 'orddd_enable_woo_subscriptions_compatibility' ) == 'on' ) {
                    $subscriptions = wcs_get_subscriptions_for_order( $order_id, array( 'order_type' => 'any' ) );
                    if( is_array( $subscriptions ) && count( $subscriptions ) > 0 ) {
                        $subscription = array_values( $subscriptions )[0];
                        $subscription_id = $subscription->get_id();
                        update_post_meta( $subscription_id, '_orddd_timeslot_timestamp', $timestamp );
                        update_post_meta( $subscription_id, $time_slot_label , $order_time_slot );
                    }
                }
            }
           
			do_action( 'orddd_after_timeslot_update', $time_slot );
        }
        
        if( 'on' == get_option('orddd_add_delivery_in_order_notes') ) {
            $delivery_date_formatted = orddd_common::orddd_get_order_delivery_date( $order_id );
            $time_slot               = orddd_common::orddd_get_order_timeslot( $order_id );
            $note = sprintf( __( 'Delivery details: <br><strong>%1$s</strong>: %2$s', 'order-delivery-date' ), $delivery_date_label, $delivery_date_formatted );

            if( '' !== $time_slot ) {
                $note .= sprintf( __( '<br> <strong>%1$s</strong>: %2$s', 'order-delivery-date' ), $time_slot_label, $order_time_slot );
            }
            $order->add_order_note( $note );
        }
       
	}
	
	/**
	 * Display delivery date on Order Recieved Page & on My Account page
	 * 
     * @hook woocommerce_order_details_after_order_table
     * 
	 * @param resource $order WC_Order Object
     * @since 1.0
	 */
	public static function orddd_add_delivery_date_to_order_page_woo( $order ) {
        
        // TODO: The below css file needs to be loaded only for the My Account page & not for order received page
        wp_enqueue_style( 'orddd-datepicker' );

        if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
            $order_id = $order->get_id();
        } else {
            $order_id = $order->id;
        }

        $orddd_hide_delivery_on_my_account = 'no';
        if ( has_filter( 'orddd_hide_delivery_on_my_account' ) ) {
            $orddd_hide_delivery_on_my_account = apply_filters( 'orddd_hide_delivery_on_my_account', $orddd_hide_delivery_on_my_account );
        }

        $delivery_date_formatted = orddd_common::orddd_get_order_delivery_date( $order_id );
        $delivery_date_timestamp = get_post_meta( $order_id, '_orddd_timestamp', true );

        $location = orddd_common::orddd_get_order_location( $order_id );
        
        $opening_tag = '<p>';
        $closing_tag = '</p>';
        if( has_filter( 'orddd_pre_delivery_date_display' ) ) {
            $tags = explode( "...", htmlentities( apply_filters( 'orddd_pre_delivery_date_display', '<p>...</p>' ) ) );    
            //print_r( $tags );
            if( isset( $tags[ 0 ] ) ) {
                $opening_tag = $tags[ 0 ];    
            } 
            
            if( isset( $tags[ 1 ] ) ) {
                $closing_tag = $tags[ 1 ];    
            }
        } 

        do_action( 'orddd_before_checkout_delivery_date', $order );

        // Display Pickup location 
        if( '' != $location ) {
            $locations_label = '' != get_option( 'orddd_location_field_label' ) ? get_option( 'orddd_location_field_label' ) : 'Pickup Location';
            $address = get_post_meta( $order_id, $locations_label, true );
            
            echo html_entity_decode( $opening_tag );
            
            echo '<span class="orddd_pickup_locations"><strong>' . __( $locations_label, 'order-delivery-date' ) . ':</strong></span> ' . $address;
        }

        // Display Delivery Date
        // TODO: We should replace the below 4 functions with a single function call once we have the custom delivery schedule ids for all orders, like:
        
        $delivery_date_label = orddd_custom_delivery_functions::orddd_fetch_delivery_date_field_label( $order_id );

        if( $delivery_date_formatted != '' && ( !is_account_page() || 'no' == $orddd_hide_delivery_on_my_account ) ) {            
            echo html_entity_decode( $opening_tag );

            echo '<span class="orddd_delivery_date">' . __( $delivery_date_label, 'order-delivery-date' ) . ':</span> ' . $delivery_date_formatted;

        	if( 'on' == get_option( 'orddd_allow_customers_to_edit_date' ) &&  is_account_page() ) {
                $order_exclude_statuses = array( 'wc-completed', 'wc-cancelled', 'wc-refunded', 'wc-failed' );
                
                $order_statuses_arr = array();
                if( has_filter( 'orddd_edit_field_for_order_statuses' ) ) {
                    $order_statuses_arr = apply_filters( 'orddd_edit_field_for_order_statuses', $order_statuses_arr ); 
                }

                if( version_compare( get_option( 'woocommerce_version ' ), '3.0.0', ">=" ) ) {
                    $order_post_status = $order->get_status();  
                    if( $order_post_status != 'trash' ) {
                        $order_post_status = "wc-" . $order_post_status;
                    }
                } else {
                    $order_post_status = $order->post->post_status;
                }

                if( !in_array( $order_post_status, $order_exclude_statuses ) || 
                	( is_array( $order_statuses_arr ) && 
                		count( $order_statuses_arr ) > 0 && 
                		in_array( $order_post_status, $order_statuses_arr )
                	)
                ) {
                    //Hide the Edit button if the cut off time has passed.
                    if( 'on' == get_option('orddd_disable_edit_after_cutoff' ) ) {
                        $gmt = false;
                        if( has_filter( 'orddd_gmt_calculations' ) ) {
                            $gmt = apply_filters( 'orddd_gmt_calculations', '' );
                        }
                        $current_time = current_time( 'timestamp', $gmt );
                        $current_day = date( 'd', $current_time );
                        $current_month = date( 'm', $current_time );
                        $current_year = date( 'Y', $current_time );

                        $delivery_date = date( 'Y-m-d', $delivery_date_timestamp );
                        $current_date = date( 'Y-m-d', $current_time );
                        $next_day = date( 'Y-m-d', strtotime( $current_date." +1 day"));
                        $enable_edit = "yes";

                        $cutoff_same_day_hour = '' !== get_option( 'orddd_disable_same_day_delivery_after_hours' ) ? get_option( 'orddd_disable_same_day_delivery_after_hours' ) : 0;
                        $cutoff_same_day_minute = '' !== get_option( 'orddd_disable_same_day_delivery_after_minutes' ) ? get_option( 'orddd_disable_same_day_delivery_after_minutes' ) : 0 ;

                        $cutoff_next_day_hour = '' !== get_option( 'orddd_disable_next_day_delivery_after_hours' ) ? get_option( 'orddd_disable_next_day_delivery_after_hours' ): 0;
                        $cutoff_next_day_minute = '' !== get_option( 'orddd_disable_next_day_delivery_after_minutes' ) ? get_option( 'orddd_disable_next_day_delivery_after_minutes' ): 0;

                        $cutoff_same_day_timestamp = gmmktime( $cutoff_same_day_hour, $cutoff_same_day_minute, 0, $current_month, $current_day, $current_year );

                        $cutoff_next_day_timestamp = gmmktime( $cutoff_next_day_hour, $cutoff_next_day_minute, 0, $current_month, $current_day, $current_year );

                        if( $delivery_date < $current_date ) {
                            $enable_edit = "no";
                        }

                        if ( get_option( 'orddd_enable_delivery_date' ) == 'on' && get_option( 'orddd_enable_next_day_delivery' ) == 'on' && get_option( 'orddd_enable_same_day_delivery' ) == 'on' ) {

                            if( ( $delivery_date <= $current_date && $current_time > $cutoff_same_day_timestamp ) || ( $delivery_date <= $next_day && $current_time > $cutoff_next_day_timestamp ) ) {
                                $enable_edit = "no";
                            }

                        }else if ( get_option( 'orddd_enable_same_day_delivery' ) == 'on' && get_option( 'orddd_enable_delivery_date' ) == 'on' ) {
                            
                            if( $delivery_date <= $current_date && $current_time > $cutoff_same_day_timestamp ) {
                                $enable_edit = "no";
                            }

                        }else if ( get_option( 'orddd_enable_next_day_delivery' ) == 'on' && get_option( 'orddd_enable_delivery_date' ) == 'on' ) {
                 
                            if( $delivery_date <= $next_day && $current_time > $cutoff_next_day_timestamp ) {
                                $enable_edit = "no";
                            }
                        }

                        if ( get_option( 'orddd_enable_delivery_date' ) == 'on' && get_option( 'orddd_enable_next_day_delivery' ) != 'on' && get_option( 'orddd_enable_same_day_delivery' ) != 'on' ) {
                            $cut_off_hour   = '' != get_option( 'orddd_minimumOrderDays' ) ? get_option( 'orddd_minimumOrderDays' ) : 0 ; //24 hours
                            $cut_off_seconds = $cut_off_hour * 60 * 60;

                            if ( isset( $cut_off_hour ) && $cut_off_hour > 0 ) {
                                
                                $difference_timestamp = $delivery_date_timestamp - $cut_off_seconds;
                                        
                                if ( $current_time > $difference_timestamp ) {
                                    $enable_edit = "no";
                                }
                            }
                        }

                        if( "yes" == $enable_edit ) {
                            echo '<span class="orddd-edit-div">
                            <a href="javascript:void(0)" id="edit_delivery_date">' . __( 'Edit', 'order-delivery-date' ) . '</a>
                            </span>';
                        }else {
                            echo '<p>'. __( 'You won\'t be able to edit the date as cut off time has passed.', 'order-delivery-date' ).'</p>';
                        }
                    } else {
                        echo '<span class="orddd-edit-div">
                            <a href="javascript:void(0)" id="edit_delivery_date">' . __( 'Edit', 'order-delivery-date' ) . '</a>
                            </span>';
                    }
        		    
                }
        	}

            echo html_entity_decode( $closing_tag );
        }

        if( 'on' == get_option( 'orddd_allow_customers_to_edit_date' ) && is_account_page() ) {
            echo "<input type='hidden' id='orddd_field_label' name='orddd_field_label' value='" . $delivery_date_label . "'/>";
        }
	}
	
	/**
	 * Display Time slot on Order Recieved Page
	 * 
     * @hook woocommerce_order_details_after_order_table
     * 
     * @param resource $order WC_Order Object
     * @since 1.0
	 */
	
	public static function orddd_add_time_slot_to_order_page_woo( $order ) {
        if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
            $order_id = $order->get_id();
        } else {
            $order_id = $order->id;
        }
    
        $location = orddd_common::orddd_get_order_location( $order_id );
        $time_field_label = orddd_custom_delivery_functions::orddd_fetch_time_slot_field_label( $order_id ); 
        $account_params   = array();

        $orddd_hide_delivery_on_my_account = 'no';
        if ( has_filter( 'orddd_hide_delivery_on_my_account' ) ) {
            $orddd_hide_delivery_on_my_account = apply_filters( 'orddd_hide_delivery_on_my_account', $orddd_hide_delivery_on_my_account );
        }

        $opening_tag = '<p>';
        $closing_tag = '</p>';
        if( has_filter( 'orddd_pre_delivery_date_display' ) ) {
            $tags = explode( "...", htmlentities( apply_filters( 'orddd_pre_delivery_date_display', '<p>...</p>' ) ) );    
            //print_r( $tags );
            if( isset( $tags[ 0 ] ) ) {
                $opening_tag = $tags[ 0 ];    
            } 
            
            if( isset( $tags[ 1 ] ) ) {
                $closing_tag = $tags[ 1 ];    
            }
        } 

	    $order_page_time_slot = orddd_common::orddd_get_order_timeslot( $order_id );
        if( $order_page_time_slot != "" && $order_page_time_slot != '' && ( !is_account_page() || 'no' == $orddd_hide_delivery_on_my_account ) ) {
            
            echo html_entity_decode( $opening_tag );
            
            echo '<span class="orddd_delivery_time">' . __( $time_field_label, 'order-delivery-date' ) . ': </span> ' . $order_page_time_slot;
        }
        
        if( 'on' == get_option( 'orddd_allow_customers_to_edit_date' ) && is_account_page() ) {
            $data = get_post_meta( $order_id );
            if( isset( $data[ '_orddd_timestamp' ][ 0 ] ) && $data[ '_orddd_timestamp' ][ 0 ] != '' ) {
                $default_date = date( "d-m-Y", $data[ '_orddd_timestamp' ][ 0 ] );
                $default_h_deliverydate = date( "j-n-Y", $data[ '_orddd_timestamp' ][ 0 ] );
                if( get_option( 'orddd_enable_delivery_time' ) == 'on' ) {
                    $time_settings_arr = explode( " ", $data[ '_orddd_timestamp' ][ 0 ] );
                    $time_settings_arr_1 = array_pop( $time_settings_arr );
                    $time_settings = date( "H:i", $time_settings_arr_1 );
                    $default_date = $default_date . " " . $time_settings;
                    $default_h_deliverydate = $default_h_deliverydate . " " . $time_settings;
                }
            } elseif ( isset( $data[ get_option( 'orddd_delivery_date_field_label' ) ][ 0 ] ) && $data[ get_option( 'orddd_delivery_date_field_label' ) ][ 0 ] != '' ) {
                $default_date = date( "d-m-Y", strtotime( str_replace( ",", " ", $data[ get_option( 'orddd_delivery_date_field_label' ) ][ 0 ] ) ) );
                $default_h_deliverydate = date( "j-n-Y", strtotime( str_replace( ",", " ", $data[ get_option( 'orddd_delivery_date_field_label' ) ][ 0 ] ) ) );
                if( get_option( 'orddd_enable_delivery_time' ) == 'on' ) {
                    $time_settings_arr = explode( " ", $data[ get_option( 'orddd_delivery_date_field_label' ) ][ 0 ] );
                    $time_settings_arr_1 = array_pop( $time_settings_arr );
                    $time_settings = date( "H:i", strtotime( $time_settings_arr_1 ) );
                    $default_date = $default_date . " " . $time_settings;
                    $default_h_deliverydate = $default_h_deliverydate . " " . $time_settings;
                }
            } else {
                $default_date = '';
                $default_h_deliverydate = '';
            }

            $account_params['orddd_my_account_default_date']   = $default_date;
            $account_params['orddd_my_account_default_h_date'] = $default_h_deliverydate;
            
            
            $zone_id = '';
            if( ( is_account_page() ) && isset( $order_id ) && '' != $order_id ) {
                $zone_id = orddd_common::orddd_get_zone_id_for_order( $order_id );
            }
 
            $account_params['orddd_zone_id'] = $zone_id;

            echo "<div id='orddd_edit_div' style=''>";
                $shipping_method = orddd_common::orddd_get_order_shipping_method( $order_id );
                if( class_exists( 'WC_Subscriptions' ) && get_option( 'orddd_enable_woo_subscriptions_compatibility' ) == 'on' && get_option( 'orddd_woocommerce_subscriptions_compatibility' ) == 'on' && wcs_order_contains_renewal( $order ) ) {
                    // We take the parent order ID as the instance_id in renewal order is not set and the shipping method comes incorrect.
                    $subscriptions = wcs_get_subscriptions_for_renewal_order( $order );
                    $subscription  = array_pop( $subscriptions );
        
                    $subscription_id = $subscription->get_id();
                    $shipping_method = orddd_common::orddd_get_order_shipping_method( $subscription_id );
                }
                orddd_scripts::orddd_front_scripts_js();
                orddd_scripts::orddd_front_scripts_css();
                orddd_process::orddd_date_after_checkout_billing_form();
                echo "</br>";                

                echo "<input type='button' id='update_date' value='" . __( "Update", "order-delivery-date" ) . "'/>
                <span class='orddd-edit-div'>
                    <a href='javascript:void(0)' id='cancel_delivery_date'>" . __( "Cancel", "order-delivery-date" ) . "</a>
                </span>     
                <div id='display_update_message'></div>               
            </div>
            </br>";
            $account_params['shipping_method'] = $shipping_method;
            $account_params['orddd_location'] = $location;
            $account_params['orddd_my_account_order_id'] = $order_id;
            $account_params['orddd_timeslot_field_label'] = $time_field_label;

            $categories = orddd_common::orddd_get_cart_product_categories( $order_id );
            $category_to_send = implode( ",", $categories );
            
            $shipping_classes = orddd_common::orddd_get_cart_shipping_classes( $order_id );
            $shipping_class_to_send = implode( ",", $shipping_classes );
            
            $account_params['orddd_category_settings_to_load'] 		 = $category_to_send;
            $account_params['orddd_shipping_class_settings_to_load'] = $shipping_class_to_send;

            // Compatibility with WooCommerce Subscriptions plugin.
            if ( class_exists( 'WC_Subscriptions' ) && get_option( 'orddd_enable_woo_subscriptions_compatibility' ) == 'on' && get_option( 'orddd_woocommerce_subscriptions_compatibility' ) == 'on' ) {
                if ( class_exists( 'ws_addon_for_orddd' ) ) {
                    $subscrition_var = ws_addon_for_orddd::orddd_check_order_subscription_period( $order_id );
                }
                if( isset( $subscrition_var[ 'orddd_if_renewal_subscription' ] ) ) {
                    $account_params['orddd_if_renewal_subscription'] = "yes";
                }
                if( isset( $subscrition_var[ 'orddd_number_of_dates_for_subscription' ] ) ) {
                    $account_params['orddd_number_of_dates_for_subscription'] = $subscrition_var[ 'orddd_number_of_dates_for_subscription' ];

                }
                if( isset( $subscrition_var[ 'orddd_start_date_for_subscription' ] ) ) {
                    $account_params['orddd_start_date_for_subscription'] = date( "j-n-Y", strtotime( $default_date ) );
                }
            }

            wp_localize_script( 'initialize-datepicker-functions-orddd', 'orddd_account_params', $account_params ); 
            wp_localize_script( 'initialize-datepicker-orddd', 'orddd_account_params', $account_params );
            wp_localize_script( 'orddd-date-dropdown', 'orddd_account_params', $account_params );
        }
	}
	 
	/**
	 * Add Delivery Date field column on My Account Orders Page
	 *
     * @hook woocommerce_my_account_my_orders_columns
     * 
	 * @param array $columns My Account page columns array
     * @return array My Account page columns array with Delivery date & Time column added
     * @since 5.7
	 */
	public static function orddd_my_account_my_orders_columns( $columns ) {
	    $new_columns = array();
        $orddd_hide_delivery_on_my_account = 'no';
        if ( has_filter( 'orddd_hide_delivery_on_my_account' ) ) {
            $orddd_hide_delivery_on_my_account = apply_filters( 'orddd_hide_delivery_on_my_account', $orddd_hide_delivery_on_my_account );
        }

	    if( 'on' == get_option( 'orddd_enable_delivery_date' ) && 'no' == $orddd_hide_delivery_on_my_account ) {
	        foreach( $columns as $column_key => $column_value ) {
	            $new_columns[ $column_key ] = $column_value;
	            if( 'order-date' == $column_key ) {
	                $new_columns[ 'order-delivery-date' ] = __( get_option( 'orddd_delivery_date_field_label' ), 'order-delivery-date' );
	            }
	        }
	    } else {
	        $new_columns = $columns;
	    }
	    return $new_columns;
	}
	
	/**
     * Add Delivery Date field column data on My Account Orders Page
     *
     * @hook woocommerce_my_account_my_orders_column_order-delivery-date
     *
     * @param resource $order Order Object
     * @since 5.7
     */

	public static function orddd_my_account_my_orders_query( $order ) {
        
        if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
            $order_id = $order->get_id();
        } else {
            $order_id = $order->id;
        }

	    $delivery_date_formatted = orddd_common::orddd_get_order_delivery_date( $order_id );
	    if( $delivery_date_formatted != '' ) {
			echo '<p>' . apply_filters( 'orddd_my_account_delivery_date', $delivery_date_formatted, $order_id ) . '</p>';
		}

		$order_page_time_slot = orddd_common::orddd_get_order_timeslot( $order_id );
		if( $order_page_time_slot != "" && $order_page_time_slot != '' ) {
		    echo '<p>' . apply_filters( 'orddd_my_account_delivery_time', $order_page_time_slot, $order_id ) . '</p>';
		}
		
		if( $delivery_date_formatted != '' && 'on' == get_option( 'orddd_allow_customers_to_edit_date' ) ) {
			$order_exclude_statuses = array( 'wc-completed', 'wc-cancelled', 'wc-refunded', 'wc-failed' );
            
            $order_statuses_arr = array();
            if( has_filter( 'orddd_edit_field_for_order_statuses' ) ) {
                $order_statuses_arr = apply_filters( 'orddd_edit_field_for_order_statuses', $order_statuses_arr ); 
            }

            if( version_compare( get_option( 'woocommerce_version ' ), '3.0.0', ">=" ) ) {
                $order_post_status = $order->get_status();  
                if( $order_post_status != 'trash' ) {
                    $order_post_status = "wc-" . $order_post_status;
                }
            } else {
                $order_post_status = $order->post->post_status;
            }

            if( !in_array( $order_post_status, $order_exclude_statuses ) || 
            	( is_array( $order_statuses_arr ) && 
            		count( $order_statuses_arr ) > 0 && 
            		in_array( $order_post_status, $order_statuses_arr )
            	)
            ) {
                echo '<a href="' . $order->get_view_order_url() . '" class="button view">' . __( 'Change', 'order-delivery-date' ) . '</a>';
            }
		}
	}
	
    /**
     * Update Delivery Date & Time in the database when edited from My Account page
     *
     * @hook wp_ajax_nopriv_orddd_update_delivery_date
     * @hook wp_ajax_orddd_update_delivery_date
     *
     * @since 5.7
     */

	public static function orddd_update_delivery_date() {
        $order_id = '';
        $delivery_date = '';
        $time_slot = '';
	    if( isset( $_POST[ 'order_id' ] ) ) {
	        $order_id = sanitize_text_field( $_POST[ 'order_id' ] );
	    }
	     
	    if( isset( $_POST[ 'h_deliverydate' ] ) ) {
	        $delivery_date = sanitize_text_field( $_POST[ 'h_deliverydate' ] );
	    }
	     
	    if( isset( $_POST[ 'orddd_time_slot' ] ) ) {
	        $time_slot = sanitize_text_field( $_POST[ 'orddd_time_slot' ] );
	    }
        
        orddd_lockout_functions::orddd_maybe_increase_delivery_lockout( $order_id );

        orddd_process::orddd_update_order_meta_delivery_date( $order_id );
        orddd_process::orddd_update_order_meta_time_slot( $order_id );
        
        orddd_common::orddd_update_delivery_charges( $delivery_date, $time_slot, $order_id );
        orddd_lockout_functions::orddd_maybe_reduce_delivery_lockout( $order_id );

	    // Delete the Event from the Google Calendar
	    
        if( 'directly' === get_option( 'orddd_calendar_sync_integration_mode' ) ) {
            $gcal = new OrdddGcal();
	        $gcal->delete_event( $order_id );
	        $event_details = orddd_common::orddd_get_event_details( $order_id );
            $gcal->insert_event( $event_details, $order_id, false );	        
	    }

	    if( 'on' == get_option( 'orddd_send_email_to_admin_when_date_updated' ) ) {
	    	ORDDD_Email_Manager::orddd_send_email_on_update( $order_id, 'customer' );
	    }
	    die();
	}
	
	/**
	 * Display Time slot in Customer notification email for the WooCommerce version below 2.3
	 *
     * @hook woocommerce_email_order_meta_keys
     *
	 * @param array $keys Array of custom fields to be added in the email
     * @return array Array of custom fields to be added in the email
     * @since 1.0
	 */
	
	public static function orddd_add_time_slot_to_order_woo_deprecated( $keys ) {
        // Display Pickup location 
	    if( get_option( 'orddd_enable_time_slot' ) == 'on' || ( isset( $_POST[ 'time_slot_enable_for_shipping_method' ] ) && $_POST[ 'time_slot_enable_for_shipping_method' ] == 'on' ) ) {
	        $keys[] = get_option( 'orddd_delivery_timeslot_field_label' );
	    }
	    	
	    if( has_filter( 'orddd_email_after_delivery_details' ) ) {
	        $keys = apply_filters( 'orddd_email_after_delivery_details', $keys );
	    }
	
	    return $keys;
	}
	
	
	/**
	 * Display Delivery Date in Customer notification email for the WooCommerce version below 2.3
	 *
     * @hook woocommerce_email_order_meta_keys
     *
	 * @param array $keys Array of custom fields to be added in the email
     * @return array Array of custom fields to be added in the email
     * @since 1.0
     */
	
	public static function orddd_add_delivery_date_to_order_woo_deprecated( $keys ) {
	    if( has_filter( 'orddd_email_before_delivery_date' ) ) {
            $keys = apply_filters( 'orddd_email_before_delivery_date', $keys );
	    }
	     
	    if ( get_option( 'orddd_enable_delivery_date' ) == 'on' ) {
 			$keys[] = get_option( 'orddd_delivery_date_field_label' );
 		}
 		
 		if( get_option( 'orddd_enable_time_slot' ) != 'on' ) {
            if( has_filter( 'orddd_email_after_delivery_details' ) ) {
                $keys = apply_filters( 'orddd_email_after_delivery_details', $keys );
            }
 		}
 		return $keys;
	}
	
	
	/**
	 * Display Time slot in Customer notification email
	 *
     * @hook woocommerce_email_order_meta_fields
     *
     * @param array $fields Fields to add in customer notification email
     * @param bool $sent_to_admin Whether to send emails to admin or not
     * @param resource $order Order Object
     * @return $fields Fields to add in customer notification email
     * @since 1.0
     */
	
	public static function orddd_add_time_slot_to_order_woo_new( $fields, $sent_to_admin, $order  ) {
        if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
            $order_id = $order->get_id();
        } else {
            $order_id = $order->id;
        }

        $time_field_label   = orddd_custom_delivery_functions::orddd_fetch_time_slot_field_label( $order_id ); 
        $time_slot_selected = orddd_common::orddd_get_order_timeslot( $order_id );
        
        if( ( is_array( $time_slot_selected ) && is_array( $time_slot_selected ) && count( $time_slot_selected ) > 0 ) || '' != $time_slot_selected ) {
    		$fields[ $time_field_label ] = array(
			    'label' => __( $time_field_label, 'order-delivery-date' ),
			    'value' => $time_slot_selected,
			);	
    	}
			
		if( has_filter( 'orddd_email_after_delivery_details' ) ) {
            $fields = apply_filters( 'orddd_email_after_delivery_details', $fields );
		}
		
		return $fields;
	}
	
	
	/**
	 * Display Delivery Date in Customer notification email
	 *
     * @hook woocommerce_email_order_meta_fields
     *
     * @param array $fields Fields to add in customer notification email
     * @param bool $sent_to_admin Whether to send emails to admin or not
     * @param resource $order Order Object
     * @return $fields Fields to add in customer notification email
     * @since 1.0
     */
	
	public static function orddd_add_delivery_date_to_order_woo_new( $fields, $sent_to_admin, $order ) {
	    if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
            $order_id = $order->get_id();
        } else {
            $order_id = $order->id;
        }

        $location           = orddd_common::orddd_get_order_location( $order_id );
        $date_field_label   = orddd_custom_delivery_functions::orddd_fetch_delivery_date_field_label( $order_id ); 
        
        if( has_filter( 'orddd_email_before_delivery_date' ) ) {
	        $fields = apply_filters( 'orddd_email_before_delivery_date', $fields );
	    }
	    
        if( '' != $location ) {
            $locations_label = '' != get_option( 'orddd_location_field_label' ) ? get_option( 'orddd_location_field_label' ) : 'Pickup Location';
            $address = get_post_meta( $order_id, $locations_label, true );
            $fields[ $locations_label ] = array(
                'label' => __( $locations_label, 'order-delivery-date' ),
                'value' => $address,
            );  
        }

	    if ( get_option( 'orddd_enable_delivery_date' ) == 'on' ) {
	    	$delivery_date_selected = get_post_meta( $order_id, $date_field_label, true );
	    	if( $delivery_date_selected != '' || ( is_array( $delivery_date_selected ) && is_array( $delivery_date_selected ) && count( $delivery_date_selected ) > 0 ) ) {
	    		$fields[ $date_field_label ] = array(
		            'label' => __( $date_field_label, 'order-delivery-date' ),
		            'value' => $delivery_date_selected,
		        );	
	    	}
	    }
	    
	    if( get_option( 'orddd_enable_time_slot' ) != 'on' ) {
	        if( has_filter( 'orddd_email_after_delivery_details' ) ) {
	            $fields = apply_filters( 'orddd_email_after_delivery_details', $fields );
	        }
	    }
	    
	    return $fields;
	}

	/**
	 * Update number of orders placed for Delivery date in options table
	 *
     * @globals resource $wpdb WordPress Object
     *
	 * @param string $delivery_date Selected Delivery Date
     * @since 1.0
	 */
	public static function orddd_update_lockout_days( $delivery_date, $called_from = '', $order_id = '' ) {
        global $wpdb;
        
        if( '' === $delivery_date ) {
            return;
        }

	    if( get_option( 'orddd_lockout_date_quantity_based' ) == 'on' ) {
	        $total_quantities = orddd_common::orddd_get_total_product_quantities( $called_from, $order_id );
	    } else {
	        $total_quantities = 1;
        }
        
        $tstmp = strtotime( $delivery_date );
        $lockout_date = date( ORDDD_LOCKOUT_DATE_FORMAT, $tstmp );

        $shipping_settings_to_check = array();
        $shipping_based_lockout = "No";
        if( get_option( 'orddd_enable_shipping_based_delivery' ) ) {
            $custom_delivery_schedule_id = get_post_meta( $order_id, '_orddd_delivery_schedule_id', true );

            if( isset( $custom_delivery_schedule_id ) && 0 != $custom_delivery_schedule_id ) {
                $option_name 				                 = 'orddd_shipping_based_settings_' . $custom_delivery_schedule_id;
                $shipping_settings_to_check[ $option_name ]  = get_option( $option_name );
                
                $total_settings_applied = get_post_meta( $order_id, '_orddd_total_settings_applied', true );
                if( isset( $total_settings_applied ) && '' != $total_settings_applied ) {
                    $count = 0;
                    $shipping_settings_to_check = array();
    
                    while( $count < $total_settings_applied ) {
                        $custom_delivery_schedule_id  = get_post_meta( $order_id, '_orddd_delivery_schedule_id_' . $count, true );
                        $option_name 				  = 'orddd_shipping_based_settings_' . $custom_delivery_schedule_id;
                        $shipping_settings_to_check[ $option_name] = get_option( $option_name );
                        $count++;
                    }
                }

                $shipping_based_lockout = "Yes";
            }
        }


        if( has_filter( 'orddd_get_shipping_method' ) ) {
            $shipping_methods_values = apply_filters( 'orddd_get_shipping_method', $shipping_settings_to_check, $_POST, $shipping_methods, $shipping_method  );  
            $shipping_methods = $shipping_methods_values[ 'shipping_methods' ];
            $shipping_method = $shipping_methods_values[ 'shipping_method' ];
        }

        if( is_array( $shipping_settings_to_check ) && count( $shipping_settings_to_check ) > 0 ) {
            foreach ( $shipping_settings_to_check as $setting_key => $setting_value ) {
               
                if( isset( $setting_value['delivery_settings_based_on'][0] ) && 'product_categories' === $setting_value['delivery_settings_based_on'][0] ) {
                    $categories =  isset( $setting_value['product_categories'] ) ? $setting_value['product_categories'] : array();
					$type       = 'product_cat';
                } elseif( isset( $setting_value['delivery_settings_based_on'][0] ) && 'shipping_methods' === $setting_value['delivery_settings_based_on'][0] ) {
                    $categories = array();
                    $classes =  isset( $setting_value['shipping_methods'] ) ? $setting_value['shipping_methods'] : array();
                    foreach( $classes as $class ) {
                        $shipping_class_term = get_term_by( 'slug', $class, 'product_shipping_class' );
                        if ( $shipping_class_term ) {
                            array_push( $categories, $class );
                        }
                    }
                    $type = 'product_shipping_class';
                }

                if( 'on' === get_option( 'orddd_lockout_date_quantity_based' ) && is_array( $categories ) && count( $categories ) > 0 ) {
                    $total_quantities = orddd_common::orddd_get_total_quantities_for_categories( $categories, $type, $called_from, $order_id );
                }

                if( isset( $setting_value[ 'orddd_lockout_date' ] ) ) {
                    $lockout_date_array = $setting_value[ 'orddd_lockout_date' ];
                    if ( $lockout_date_array == '' || $lockout_date_array == '{}' || $lockout_date_array == '[]' || $lockout_date_array == 'null' ) {
                        $lockout_date_arr = array();
                    } else {
                        $lockout_date_arr = json_decode( $lockout_date_array );
                    }
                } else {
                    $lockout_date_arr = array();
                }
                   
                $lockout_days_new_arr  = array();
                $existing_days = array();
                foreach ( $lockout_date_arr as $k => $v ) {
                    $orders = $v->o;		                       
                    if ( $lockout_date == $v->d ) {
                        $orders = $v->o + $total_quantities;
                    }
                    $existing_days[] = $v->d;
                    $lockout_days_new_arr[] = array( 'o' => $orders, 'd' => $v->d );
                }

                // add the currently selected date if it does not already exist
                if ( !in_array( $lockout_date, $existing_days ) ) {
                    $lockout_days_new_arr[] = array( 'o' => $total_quantities,
                        'd' => $lockout_date );
                }

                $setting_value[ 'orddd_lockout_date' ] = json_encode( $lockout_days_new_arr );
                update_option( $setting_key, $setting_value );
            }
        }
		
        if( $shipping_based_lockout == "No" ) {
            $lockout_days = get_option( 'orddd_lockout_days' );
            $timeslot_new_arr = array();
            if ( $lockout_days == '' || $lockout_days == '{}' || $lockout_days == '[]' || $lockout_days == 'null' ) {
                $lockout_days_arr = array();
            } else {
                $lockout_days_arr = json_decode( $lockout_days );
            }
            //existing lockout days
            $lockout_days_new_arr = array();
            $existing_days = array();
            foreach ( $lockout_days_arr as $k => $v ) {
                $orders = $v->o;
                if ( $lockout_date == $v->d ) {
                    $orders = $v->o + $total_quantities;
                }
                $existing_days[] = $v->d;
                $lockout_days_new_arr[] = array( 'o' => $orders, 'd' => $v->d );
            }
        	// add the currently selected date if it does not already exist
            if ( !in_array( $lockout_date, $existing_days ) ) {
                $lockout_days_new_arr[] = array( 'o' => $total_quantities,
                    'd' => $lockout_date );
            }
            $lockout_days_jarr = json_encode( $lockout_days_new_arr );
            update_option( 'orddd_lockout_days', $lockout_days_jarr );
	   }

       do_action( 'orddd_after_lockout_date_update' );

	}
	
	/**
	 * Update number of order for Delivery date and Time slot in options table
	 *
     * @globals resource $wpdb WordPress Object
     *
     * @param string $timeslt Selected time slot on the checkout page
	 * @param string $del Selected Delivery date on the checkout page
     * 
     * @since 1.0
	 */
	
	public static function orddd_update_time_slot( $timeslt, $del, $order_id = '' ) {
	    global $wpdb;
        
        if( '' === $timeslt || __( 'As Soon As Possible.', 'order-delivery-date' ) === $timeslt ) {
            return;
        }

	    $time_format_to_show = orddd_common::orddd_get_time_format(); 
        
	    if( get_option( 'orddd_lockout_date_quantity_based' ) == 'on' ) {
	        $total_quantities = orddd_common::orddd_get_total_product_quantities( '', $order_id );
	    } else {
	        $total_quantities = 1;
        }
        
        $lockout_date = $del;

        $shipping_settings_to_check = array();
        $shipping_based_lockout     =  "No";
        if( get_option( 'orddd_enable_shipping_based_delivery' ) ) {
            $custom_delivery_schedule_id = get_post_meta( $order_id, '_orddd_delivery_schedule_id', true );

            if( 0 != $custom_delivery_schedule_id ) {
                $option_name 				                 = 'orddd_shipping_based_settings_' . $custom_delivery_schedule_id;
                $shipping_settings_to_check[ $option_name ]  = get_option( $option_name );
                
                $total_settings_applied = get_post_meta( $order_id, '_orddd_total_settings_applied', true );
                if( isset( $total_settings_applied ) && '' != $total_settings_applied ) {
                    $count = 0;
                    $shipping_settings_to_check = array();
    
                    while( $count < $total_settings_applied ) {
                        $custom_delivery_schedule_id  = get_post_meta( $order_id, '_orddd_delivery_schedule_id_' . $count, true );
                        $option_name 				  = 'orddd_shipping_based_settings_' . $custom_delivery_schedule_id;
                        $shipping_settings_to_check[ $option_name] = get_option( $option_name );
                        $count++;
                    }
                }
                $shipping_based_lockout = "Yes";
            }
        }


        if( has_filter( 'orddd_get_shipping_method' ) ) {
            $shipping_methods_values = apply_filters( 'orddd_get_shipping_method', $shipping_settings_to_check, $_POST, $shipping_methods, $shipping_method  );  
            $shipping_methods = $shipping_methods_values[ 'shipping_methods' ];
            $shipping_method = $shipping_methods_values[ 'shipping_method' ];
        }

        if( is_array( $shipping_settings_to_check ) && count( $shipping_settings_to_check ) > 0 ) {
            foreach ( $shipping_settings_to_check as $setting_key => $setting_value ) {

                if( isset( $setting_value['delivery_settings_based_on'][0] ) && 'product_categories' === $setting_value['delivery_settings_based_on'][0] ) {
                    $categories =  isset( $setting_value['product_categories'] ) ? $setting_value['product_categories'] : array();
                    $type       = 'product_cat';
                } else if( isset( $setting_value['delivery_settings_based_on'][0] ) && 'shipping_methods' === $setting_value['delivery_settings_based_on'][0] ) {
                    $categories = array();
                    $classes =  isset( $setting_value['shipping_methods'] ) ? $setting_value['shipping_methods'] : array();
                    foreach( $classes as $class ) {
                        $shipping_class_term = get_term_by( 'slug', $class, 'product_shipping_class' );
                        if ( $shipping_class_term ) {
                            array_push( $categories, $class );
                        }
                    }
                    $type = 'product_shipping_class';
                }

                if( 'on' === get_option( 'orddd_lockout_date_quantity_based' ) && is_array( $categories ) && count( $categories ) > 0 ) {
                    $total_quantities = orddd_common::orddd_get_total_quantities_for_categories( $categories, $type, $called_from, $order_id );
                }
                
                $lockout = $previous_lockout = 0;
                $specific_dates = $delivery_days =  array();
                $lockout_time_new_arr = array();
                $time_slots = explode( '},', $setting_value[ 'time_slots' ] );
		            
                if( isset( $setting_value[ 'orddd_lockout_time_slot' ] ) ) {
                    $lockout_time = $setting_value[ 'orddd_lockout_time_slot' ];
                    if ( $lockout_time == '' || $lockout_time == '{}' || $lockout_time == '[]' || $lockout_time == 'null' ) {
                        $lockout_time_arr = array();
                    } else {
                        $lockout_time_arr = json_decode( $lockout_time );
                    }
                } else {
                    $lockout_time_arr = array();
                }
                    
                $existing_timeslots = $existing_dates = array();
                $timeslt = orddd_common::orddd_change_time_slot_format( $timeslt, $time_format_to_show );
                
                foreach ( $lockout_time_arr as $k => $v ) {
                    $orders = $v->o;
                    if ( $timeslt == $v->t && $lockout_date == $v->d ) {
                        $orders = $v->o + $total_quantities;
                    }
                    $existing_timeslots[ $v->d ][] = $v->t;
                    $existing_dates[] = $v->d;
                    $lockout_time_new_arr[] = array( 'o' => $orders, 't' => $v->t, 'd' => $v->d );
                }

                $lockout_time_new_arr = self::orddd_overlap_timeslot_lockout( $lockout_time_new_arr, $lockout_date, $timeslt, $shipping_settings_to_check, "yes", $order_id );
               
                // add the currently selected date if it does not already exist
                if ( ( ( isset( $existing_timeslots[ $lockout_date ] ) && !in_array( $timeslt, $existing_timeslots[ $lockout_date ] ) ) ) || !in_array( $lockout_date, $existing_dates ) ) {
                    $lockout_time_new_arr[] = array( 'o' => $total_quantities,
                        't' => $timeslt,
                        'd' => $lockout_date );
                }
                $setting_value[ 'orddd_lockout_time_slot' ] = json_encode( $lockout_time_new_arr );
                update_option( $setting_key, $setting_value );
            }

            $shipping_based_lockout = "Yes";
        }

	    if( $shipping_based_lockout == "No" ) {
	        $lockout_time = get_option( 'orddd_lockout_time_slot' );
            if ( $lockout_time == '' || $lockout_time == '{}' || $lockout_time == '[]' || $lockout_time == 'null' ) {
                $lockout_time_arr = array();
            } else {
                $lockout_time_arr = json_decode( $lockout_time );
            }
            $existing_timeslots = $existing_dates = array();
            $lockout_time_new_arr = array();
            $timeslt = orddd_common::orddd_change_time_slot_format( $timeslt, $time_format_to_show );

            foreach ( $lockout_time_arr as $k => $v ) {
                $orders = $v->o;
                if ( $timeslt == $v->t && $lockout_date == $v->d ) {
                    $orders = $v->o + $total_quantities;
                }
                $existing_timeslots[ $v->d ][] = $v->t;
                $existing_dates[] = $v->d;
                $lockout_time_new_arr[] = array( 'o' => $orders, 't' => $v->t, 'd' => $v->d );
            }

            $lockout_time_new_arr = self::orddd_overlap_timeslot_lockout( $lockout_time_new_arr, $lockout_date, $timeslt, '', "no", $order_id );

            
	       // add the currently selected date if it does not already exist
            if ( ( isset( $existing_timeslots[ $lockout_date ] ) && !in_array( $timeslt, $existing_timeslots[ $lockout_date ] ) ) || !in_array( $lockout_date, $existing_dates ) ) {
                $lockout_time_new_arr[] = array( 'o' => $total_quantities,
											 't' => $timeslt,
											 'd' => $lockout_date );
            }
          
            $lockout_time_jarr = json_encode( $lockout_time_new_arr );
            update_option( 'orddd_lockout_time_slot', $lockout_time_jarr );
        }

        do_action( 'orddd_after_lockout_time_update' );
    }


    /**
     * Reduce the lockout of the overlapping timeslots
     * 
     * @param array $lockout_time_new_arr Array of the tine slots and their lockouts
     * @param string $lockout_date Selected date on checkout page
     * @param string $timeslt Selected time slot on the checkout page
     * @param array $shipping_settings_to_check Shipping settings 
     * @param string $is_custom check if custom shipping ids enabled
     * @return $lockout_time_new_arr Updated lockout array 
     * 
     * @since 9.7
     */
    public static function orddd_overlap_timeslot_lockout( $lockout_time_new_arr, $lockout_date, $timeslt, $shipping_settings_to_check, $is_custom, $order_id ) {

	    $time_format_to_show = orddd_common::orddd_get_time_format(); 

        if( get_option( 'orddd_lockout_date_quantity_based' ) == 'on' ) {
	        $total_quantities = orddd_common::orddd_get_total_product_quantities( '', $order_id);
	    } else {
	        $total_quantities = 1;
	    }

        $all_timeslots = orddd_common::orddd_get_timeslots( $lockout_date, date('w', strtotime($lockout_date) ), date('n J Y', strtotime($lockout_date) ), $shipping_settings_to_check);

        $selected_timeslot = explode(" - ", $timeslt );
        $selected_date = date( 'Y-m-d', strtotime($lockout_date) );

        //Get the start & end timestamp for the selected timeslot
        $selected_timeslot_arr = array( 'start' => strtotime( $selected_date." ".$selected_timeslot[0] ) );
        if ( isset( $selected_timeslot[1] ) ) {
            $selected_timeslot_arr['end'] = strtotime( $selected_date." ".$selected_timeslot[1] );
        }

        if( "yes" == $is_custom ) {
            $weekday = "orddd_weekday_".date('w', strtotime($selected_date) )."_custom_setting";
        }else {
            $weekday = "orddd_weekday_".date('w', strtotime($selected_date) );
        }
       
        // Check if the other timeslots fall in between the selected timeslot. If yes, then update its lockout too
        foreach( $all_timeslots as $key => $value ) {

            if( $key == $weekday ) {
                foreach( $value as $time => $total_lockout ) {
                        $time = orddd_common::orddd_change_time_slot_format( $time, $time_format_to_show );

                        if( $time == $timeslt ) {
                            continue;
                        }

                        $start_time = explode(" - ", $time );
                        $timeslot_arr = array('start' => strtotime( $selected_date." ".$start_time[0] ), 'end' => strtotime( $selected_date." ".$start_time[1] ) );

                    //If the start or end time of a timeslot falls in between the selected timeslot then update its lockout
                    if( ( $timeslot_arr['start'] >= $selected_timeslot_arr['start'] && $timeslot_arr['start'] < $selected_timeslot_arr['end'] ) || ( $timeslot_arr['end'] > $selected_timeslot_arr['start'] &&$timeslot_arr['end'] <= $selected_timeslot_arr['end'] ) ) {
                        $date_found = "no";
                        
                        foreach ( $lockout_time_new_arr as $k => $v ) {

                            if( $v['d'] == $lockout_date && $v['t'] == $time ) {
                                $lockout_time_new_arr[$k]['o'] = $lockout_time_new_arr[$k]['o'] + $total_quantities;
                                $date_found = "yes";
                            }
                        }

                        if( $date_found == "no" ) {
                            $lockout_time_new_arr[] = array( 'o' => $total_quantities,
                                                         't' => $time,
                                                         'd' => $lockout_date );
                        }
                    }
                }
            }
        }
        return $lockout_time_new_arr;
    }
    
    /**
     * Check the availability of the selected delivery date & time
     *
     * @hook woocommerce_after_checkout_validation
     * @globals resource $wpdb WordPress Object
     * @globals resource $current_user Current logged in user object
     * 
     * @since 1.0
     */

    public static function orddd_availability_check( $data, $errors ) {
    	global $wpdb, $current_user, $orddd_date_formats;
        $roles = array();
        if ( has_filter( 'orddd_disable_delivery_for_user_roles' ) ) {
            $roles = apply_filters( 'orddd_disable_delivery_for_user_roles', $roles );
        }
        
        $current_user_role = '';
        if( isset( $current_user->roles[0] ) ) {
            $current_user_role = $current_user->roles[0];
        }
        $delivery_date = '';
        $date_format   = get_option( 'orddd_delivery_date_format' );
        if( ( orddd_process::woo_product_has_delivery() === 'on' ) && ( 'yes' == orddd_common::orddd_is_delivery_enabled() ) && ( !in_array( $current_user_role, $roles ) ) ) {
			$fieldset_key_date = 'e_deliverydate';
            if( isset( $data[ 'e_deliverydate' ] ) && $data[ 'e_deliverydate' ] != '' ) {
                $delivery_date = $data[ 'e_deliverydate' ];
            }

            if( '' != $delivery_date ) {
    	        $total_quantities               = orddd_common::orddd_get_total_product_quantities();
    	        $time_slot                      = '';
    	        $delivery_date                  = '';
    	        $lockout_date                   = "";
    	        $select_time_slot               = '';
    	        $select_time_slot_str           = '';
    	        $select_from_time               = 0;
    	        $select_to_time                 = 0;
                $available_time_slot_quantities = "";
                $available_date_quantities      = '';
                $shipping_based_timeslot        = "No"; 
                $shipping_based_date            = "No";
                $lockout_date_format            = "";
                $is_specific                    = 'no';
                $orddd_params                   = orddd_common::orddd_localize_data_script();

                $fieldset_key_timeslot = 'orddd_time_slot';
    	    	if( isset( $data[ 'orddd_time_slot' ] ) && $data[ 'orddd_time_slot' ] != '' && $data[ 'orddd_time_slot' ] != 'choose' && $data[ 'orddd_time_slot' ] != 'NA' && $data[ 'orddd_time_slot' ] != 'select' ) {
    	        	$time_slot = $data[ 'orddd_time_slot' ];
                    $select_time_slot = explode( " - ", $time_slot );
                    $select_from_time = date( "G:i", strtotime( $select_time_slot[ 0 ] ) );
                    if( isset( $select_time_slot[ 1 ] ) && $select_time_slot[ 1 ] != '' ) {
                        $select_to_time = date( "G:i", strtotime( $select_time_slot[ 1 ] ) );
                        $select_time_slot_str = $select_from_time. ' - '. $select_to_time;
                    } else {
                        $select_time_slot_str = $select_from_time;
                    }
    	        }

                if( isset( $_POST[ 'h_deliverydate' ] ) && $_POST[ 'h_deliverydate' ] != '' ) {
                    $lockout_date = date( "j-n-Y", strtotime( $_POST[ 'h_deliverydate' ] ) );
                    $lockout_date_format = date( ORDDD_LOCKOUT_DATE_FORMAT, strtotime( $lockout_date ) );
                    $delivery_date_selected_weekday = date( 'N', strtotime( $_POST[ 'h_deliverydate' ] ) );
                    $delivery_date_weekday_value = 'orddd_weekday_' . $delivery_date_selected_weekday;
                }

	        	if( get_option( 'orddd_enable_shipping_based_delivery' ) == 'on' ) {
                    $delivery_date_weekday_value = 'orddd_weekday_' . $delivery_date_selected_weekday . '_custom_setting';

	        		$results = orddd_common::orddd_get_shipping_settings();

                    $orddd_zone_id     = 0;
                    if( isset( $_POST[ 'orddd_zone_id' ] ) ) {
                        $orddd_zone_id = $_POST[ 'orddd_zone_id' ];
                    }

	            	$shipping_settings = array();
	            	$shipping_methods  = array();
	            	$shipping_method   = '';

                    $cart_product_quantities = orddd_common::orddd_get_individual_product_quantities();
                    $is_category_based = false;
                    $shipping_method_for_category = false;
                    $selected_shipping_class = '';
                    $categories_names_str = '';
                    if( is_array( $results ) && count( $results ) > 0 ) {
	                	foreach ( $results as $key => $value ) {
                            $shipping_settings = get_option( $value->option_name );
                            if ( isset( $shipping_settings['product_categories'] ) && ! empty( $shipping_settings['product_categories'] ) ) {
                                $categories_products = array();
                                $category_names      = array();
                                $product_categories = $shipping_settings['product_categories'];
                                foreach ( $product_categories as $category ) {
                                    $catObj = get_term_by( 'slug', $category, 'product_cat' );
                                    $category_names[] = $catObj->name;
                                }

                                $categories_names_str = implode( ',', $category_names );
                            }
                            $formatted_locout_date = date('Y-m-d', strtotime( $lockout_date ) );
                            $orddd_format_date     = date_i18n( $orddd_date_formats[ $date_format ], strtotime( $formatted_locout_date ) );
                            $orddd_formatted_date  = orddd_common::delivery_date_language( $orddd_format_date, strtotime( $formatted_locout_date ) );
                            if( isset( $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] ) && $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] == 'orddd_locations' ) {
	                            if( isset( $shipping_settings[ 'orddd_locations' ] ) ) {
	                                $shipping_methods = $shipping_settings[ 'orddd_locations' ];
	                            } 

	                            if( isset( $_POST[ 'orddd_locations' ] ) && $_POST[ 'orddd_locations'] != '' ) {
	                                $shipping_method = $_POST[ 'orddd_locations' ];
                                }

                            } else if( isset( $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] ) && $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] == 'shipping_methods' ) {
	                            if( isset( $shipping_settings[ 'shipping_methods' ] ) ) {
	                                $shipping_methods = $shipping_settings[ 'shipping_methods' ];
	                            } 
	                            
	                            if( isset( $_POST[ 'shipping_method' ][ 0 ] ) && $_POST[ 'shipping_method'][ 0 ] != '' && is_array( $_POST[ 'shipping_method' ] ) ) {
	                                $shipping_method = $_POST[ 'shipping_method' ][ 0 ];
	                                if( false !== strpos( $shipping_method, 'usps' ) ) {
	                                    $shipping_method = $orddd_zone_id . ":" . $shipping_method;
	                                }
	                                if( strpos( $shipping_method, 'wf_fedex_woocommerce_shipping' )  === false && false !== strpos( $shipping_method, 'fedex' ) && is_array( explode( ":", $shipping_method ) ) && count( explode( ":", $shipping_method ) ) < 3 ) {
	                                    $shipping_method = $orddd_zone_id . ":" . $shipping_method;
	                                }
	                            } else if( isset( $_POST[ 'shipping_method' ] ) && $_POST[ 'shipping_method' ] != '' ) {
	                                $shipping_method = $_POST[ 'shipping_method' ];
	                                if( false !== strpos( $shipping_method, 'usps' ) ) {
	                                    $shipping_method = $orddd_zone_id . ":" . $shipping_method;
	                                }
	                                if( strpos( $shipping_method, 'wf_fedex_woocommerce_shipping' )  === false && false !== strpos( $shipping_method, 'fedex' ) && is_array( explode( ":", $shipping_method ) ) && count( explode( ":", $shipping_method ) ) < 3 ) {
	                                    $shipping_method = $orddd_zone_id . ":" . $shipping_method;
	                                }
                                }

                                if ( isset( $orddd_params[ 'orddd_shipping_class_settings_to_load' ] ) && $orddd_params[ 'orddd_shipping_class_settings_to_load'] != '' ) {
	                                $selected_shipping_class = $orddd_params[ 'orddd_shipping_class_settings_to_load' ];
                                }
                                // If 2 products of different classes are added to cart with different custom settings then we need to split them into an array.
                                if( strpos( $selected_shipping_class, ',' ) !== false ) {
                                    $selected_shipping_class = explode( ',', $selected_shipping_class );
	                            }
	                        } else if( isset( $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] ) && $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] == 'product_categories' ) {

                                if ( isset( $shipping_settings['shipping_methods_for_categories'] )  ) {
                                    $shipping_methods = $shipping_settings['shipping_methods_for_categories'];
                                    if( isset( $_POST[ 'shipping_method' ][ 0 ] ) && $_POST[ 'shipping_method'][ 0 ] != '' && is_array( $_POST[ 'shipping_method' ] ) ) {
                                        $shipping_method = $_POST[ 'shipping_method' ][ 0 ];
                                        if( false !== strpos( $shipping_method, 'usps' ) ) {
                                            $shipping_method = $orddd_zone_id . ":" . $shipping_method;
                                        }
                                        if( strpos( $shipping_method, 'wf_fedex_woocommerce_shipping' )  === false && false !== strpos( $shipping_method, 'fedex' ) && is_array( explode( ":", $shipping_method ) ) && count( explode( ":", $shipping_method ) ) < 3 ) {
                                            $shipping_method = $orddd_zone_id . ":" . $shipping_method;
                                        }
                                    } else if( isset( $_POST[ 'shipping_method' ] ) && $_POST[ 'shipping_method' ] != '' ) {
                                        $shipping_method = $_POST[ 'shipping_method' ];
                                        if( false !== strpos( $shipping_method, 'usps' ) ) {
                                            $shipping_method = $orddd_zone_id . ":" . $shipping_method;
                                        }
                                        if( strpos( $shipping_method, 'wf_fedex_woocommerce_shipping' )  === false && false !== strpos( $shipping_method, 'fedex' ) && is_array( explode( ":", $shipping_method ) ) && count( explode( ":", $shipping_method ) ) < 3 ) {
                                            $shipping_method = $orddd_zone_id . ":" . $shipping_method;
                                        }
                                    }
                                    $shipping_method_for_category = true;
                                } else {
                                    $shipping_method_for_category = false;
                                }

	                            if( isset( $shipping_settings[ 'product_categories' ] ) ) {
	                                $categories  = $shipping_settings[ 'product_categories' ];
	                            } 

	                            if( isset( $orddd_params[ 'orddd_category_settings_to_load' ] ) && $orddd_params[ 'orddd_category_settings_to_load'] != '' ) {
	                                $selected_category = $orddd_params[ 'orddd_category_settings_to_load' ];
                                }
                                
                                // If 2 products of different categories are added to cart with different custom settings then we need to split them into an array.
                                if( strpos( $selected_category, ',' ) !== false ) {
                                    $selected_category = explode( ',', $selected_category );
	                            }
                                $is_category_based = true;
                            }

                            // Check whether custom settings are added for the categories in the cart. The first max deliveries from the first custom setting will be considered at the time of checkout.
                            $in_array = false;
                            $current_category = '';
                            if( $is_category_based && is_array( $selected_category ) ) {
                                foreach( $selected_category as $method ) {
                                    if( in_array( $method, $categories ) ) {
                                        $current_category = $method;
                                        $in_array = true;
                                        break;
                                    }
                                }
                                if( $shipping_method_for_category && !in_array( $shipping_method, $shipping_methods ) ) {
                                    $in_array = false;
                                }
                            } elseif ( $is_category_based && in_array( $selected_category, $categories ) ) {
                                $current_category = $selected_category;
                                $in_array = true;
                                if( $shipping_method_for_category && !in_array( $shipping_method, $shipping_methods ) ) {
                                    $in_array = false;
                                }
                            } elseif ( !$is_category_based && in_array( $shipping_method, $shipping_methods ) ) {
                                $current_category = $shipping_method;
                                $in_array = true;
                            } elseif( !$is_category_based && in_array( $selected_shipping_class, $shipping_methods ) ) {
                                $current_category = $selected_shipping_class;
                                $in_array = true;
                            }
	                        if( $in_array ) {
                                $is_custom_date_lockout_reached = "No";
                                $specific_dates = array();

                                //Calculate the available quantities for the specific dates based on the lockout
                                if( isset( $shipping_settings['delivery_type']['specific_dates'] ) && 'on' == $shipping_settings['delivery_type']['specific_dates'] && isset( $shipping_settings['specific_dates'] ) && '' != $shipping_settings['specific_dates'] ) {
                                    $specific_lockout = "no";

                                    $lockout_date_array = array();
	                                if( isset( $shipping_settings[ 'orddd_lockout_date' ] ) ) {
	                                    $lockout_date_arr = $shipping_settings[ 'orddd_lockout_date' ];
	                                    if ( $lockout_date_arr != '' && $lockout_date_arr != '{}' && $lockout_date_arr != '[]' && $lockout_date_arr != 'null' ) {
	                                       $lockout_date_array = (array) json_decode( $lockout_date_arr );
	                                    }
	                                } 
                                    
                                    $specific_days_settings = explode( ',', $shipping_settings[ 'specific_dates' ] );
                                    $available_date_quantities = 0;
                                    foreach( $specific_days_settings as $sk => $sv ) {
                                        if( $sv != '' ) {
                                            $sv = str_replace( '}', '', $sv );
                                            $sv = str_replace( '{', '', $sv );
                                            $specific_date_arr = explode( ':', $sv );
                                            array_push( $specific_dates, $specific_date_arr[0] );
                                        }
                                        if( $lockout_date_format == $specific_date_arr[0] && '' != $specific_date_arr[3] ) {
                                            $specific_lockout = "yes";
                                            $available_date_quantities = $specific_date_arr[3];

                                            foreach ( $lockout_date_array as $k => $v ) {
                                                if ( $v->d == $lockout_date_format && $lockout_date_format == $specific_date_arr[0] && '' != $specific_date_arr[3] ) {
                                                    $available_date_quantities = $specific_date_arr[3] - $v->o;
                                                    break;
                                                }
                                            }
                                            break;
                                        }
                                    }

                                    //Check if lockout is set for specific dates and if not then don't show these messages.
                                    if( "yes" == $specific_lockout ) {
                                        $qty_to_check = $total_quantities;
                                        if( $is_category_based ) {
                                            $qty_to_check = 0;
                                            foreach ( $cart_product_quantities as $cartkey => $product_quantity ) {
                                                $terms = get_the_terms( $cartkey, 'product_cat' );
                                                
                                                foreach( $terms as $key => $value ) {
                                                    if( $value->slug == $current_category ) {
                                                        $qty_to_check += $product_quantity;
                                                    }
                                                }
                                            }
                                        }

                                        if( $available_date_quantities < $qty_to_check && $available_date_quantities > 0 && $available_date_quantities != '' ) {
                                            $message = sprintf( __( '%1$s has only %2$s deliveries available for the date.', 'order-delivery-date' ), $lockout_date, $available_date_quantities );
                                            $message = apply_filters( 'orddd_deliveries_message_for_quantities_of_specific_date', $message, $orddd_formatted_date, $categories_names_str, $available_date_quantities );
                                            $errors->add(
                                                'validation',
                                                $message,
                                                array(
                                                    'id' => $fieldset_key_date
                                                )
                                            );
                                            $is_date_lockout_reached = 'Yes';
                                        } else if( $available_date_quantities <= 0 ) {
                                            $message = sprintf( __( '%s is not available for delivery. Please select a new delivery date.', 'order-delivery-date' ), $lockout_date );
                                            $message = apply_filters( 'orddd_unavailable_message_when_no_quantity_of_specific_date', $message, $orddd_formatted_date, $categories_names_str );
                                            $errors->add(
                                                'validation',
                                                $message,
                                                array(
                                                    'id' => $fieldset_key_date
                                                )
                                            );
                                            $is_date_lockout_reached = 'Yes';
                                        }
                                    }
                                }
                                
                                // Calculate the available quantities for the weekday lockout. Don't consider the weekday lockout if the date is a specific date.
	                            if( isset( $shipping_settings[ 'date_lockout' ] ) && $shipping_settings[ 'date_lockout' ] != '' && $shipping_settings[ 'date_lockout' ] != '0' ) {
                                    $lockout_date_array = array();
	                                if( isset( $shipping_settings[ 'orddd_lockout_date' ] ) ) {
	                                    $lockout_date_arr = $shipping_settings[ 'orddd_lockout_date' ];
	                                    if ( $lockout_date_arr != '' && $lockout_date_arr != '{}' && $lockout_date_arr != '[]' && $lockout_date_arr != 'null' ) {
	                                       $lockout_date_array = (array) json_decode( $lockout_date_arr );
	                                    }
                                    } 
                                    
                                    $available_date_quantities = $shipping_settings[ 'date_lockout' ];
	                                if( is_array( $lockout_date_array ) && count( $lockout_date_array ) > 0 ) {
	                                    foreach( $lockout_date_array as $k => $v ) {
                                            //If specific date then consider specific date lockout
                                            if( is_array( $specific_dates ) && in_array( $lockout_date_format, $specific_dates ) ) {
                                                $is_specific = "yes";
                                                continue;
                                            }
	                                        if ( $lockout_date_format == $v->d ) {
	                                            $available_date_quantities = $shipping_settings[ 'date_lockout' ] - $v->o;
	                                            break;
	                                        } 
	                                    }
                                    } 
                                   
                                    $qty_to_check = $total_quantities;
                                    if( $is_category_based ) {
                                        $qty_to_check = 0;
                                        foreach ( $cart_product_quantities as $cartkey => $product_quantity ) {
                                            $terms = get_the_terms( $cartkey, 'product_cat' );
                                            if ( false !== $terms ) {
                                                foreach( $terms as $key => $value ) {
                                                    if( $value->slug == $current_category ) {
                                                        $qty_to_check += $product_quantity;
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    // TODO: evaluate the code below & verify if it's necessary or not. if yes, need to see when will it be executed 
                                    if( "no" == $is_specific ) {
                                        if( $available_date_quantities < $qty_to_check && 
                                            $available_date_quantities > 0 && 
                                            $available_date_quantities != '' ) {
                                            $message = sprintf( __( '%1$s has only %2$s deliveries available.', 'order-delivery-date' ), $lockout_date, $available_date_quantities );
                                            $message = apply_filters( 'orddd_deliveries_message_for_quantities_of_date', $message, $orddd_formatted_date, $categories_names_str, $available_date_quantities );
                                            $errors->add(
                                                'validation',
                                                $message,
                                                array(
                                                    'id' => $fieldset_key_date
                                                )
                                            );
                                            $is_custom_date_lockout_reached = 'Yes';
                                        } else if( $available_date_quantities <= 0 ) {
                                            $message = sprintf( __( '%1s is not available for delivery on %2s. Please select a new delivery date.', 'order-delivery-date' ), $categories_names_str, $orddd_formatted_date );
                                            $message = apply_filters( 'orddd_unavailable_message_for_lockout' , $message, $orddd_formatted_date, $categories_names_str );
                                            $errors->add(
                                                'validation',
                                                $message,
                                                array(
                                                    'id' => $fieldset_key_date
                                                )
                                            );
                                            $is_custom_date_lockout_reached = 'Yes';
                                        }
                                    }
	                            }
                               
                                $shipping_based_date = "Yes";
                                
	                        	if( '' != $time_slot && isset( $shipping_settings[ 'time_slots' ] ) ) {
	    	                        $lockout          = 0; 
                                    $previous_lockout = 0;
	    	                        $specific_dates   = array();
                                    $delivery_days    = array();
	    	                        $time_slots       = explode( '},', $shipping_settings[ 'time_slots' ] );
                                    $lockout_time_arr = array();
                                    if( isset( $shipping_settings[ 'orddd_lockout_time_slot' ] ) ) {
                                        $lockout_time = $shipping_settings[ 'orddd_lockout_time_slot' ];
                                        if ( $lockout_time != '' && $lockout_time != '{}' && $lockout_time != '[]' && $lockout_time != 'null' ) {
                                            $lockout_time_arr = (array) json_decode( $lockout_time );
                                        } 
                                    }
                                     
	                                foreach( $time_slots as $tk => $tv ) {
	    	                            if( $tv != '' ) {
	    	                                $timeslot_values = orddd_common::get_timeslot_values( $tv );
                                            if( $timeslot_values[ 'time_slot' ] == $select_time_slot_str && 
                                                in_array( $delivery_date_weekday_value, $timeslot_values[ 'selected_days' ] ) ) {
	                                            $lockout = $timeslot_values[ 'lockout' ];
	    	                                    if( $timeslot_values[ 'lockout' ] != 0 && $timeslot_values[ 'lockout' ] != "" ) {
                                                    $available_time_slot_quantities = $lockout;
	                                                if( is_array( $lockout_time_arr ) && count( $lockout_time_arr ) > 0 ) {
	    	                                            foreach ( $lockout_time_arr as $k => $v ) {
	    	                                                if ( $time_slot == $v->t && $lockout_date == $v->d ) {
	    	                                                    $available_time_slot_quantities = $lockout - $v->o;
	    	                                                    break; 
	    	                                                }
	    	                                            }
	    	                                        } 
	                                                if( $available_time_slot_quantities < $total_quantities && $available_time_slot_quantities > 0 && $available_time_slot_quantities != '' && $is_custom_date_lockout_reached == 'No' ) {
	    	                                            $message = sprintf( __( '%1$s has only %2$s deliveries available for the time slot %3$s', 'order-delivery-date' ), $lockout_date, $available_time_slot_quantities, $time_slot );
                                                        $message = apply_filters( 'orddd_deliveries_message_for_timeslots' , $message, $orddd_formatted_date, $categories_names_str, $available_time_slot_quantities, $time_slot );
														$errors->add(
                                                            'validation',
                                                            $message,
                                                            array(
                                                                'id' => $fieldset_key_timeslot
                                                            )
                                                        );
	    	                                        } else if ( $available_time_slot_quantities <= 0 && $is_custom_date_lockout_reached == 'No' ) {
                                                        $message = sprintf( __( '%1$s for %2$s is not available for delivery. Please select a new time slot.', 'order-delivery-date' ), $time_slot, $lockout_date );
                                                        $message = apply_filters( 'orddd_unavailable_message_for_timeslots' , $message, $orddd_formatted_date, $categories_names_str, $time_slot );
                                                        $errors->add(
                                                            'validation',
                                                            $message,
                                                            array(
                                                                'id' => $fieldset_key_timeslot
                                                            )
                                                        );
                                                    }
	    	                                    } else if( get_option( 'orddd_global_lockout_time_slots' ) != 0 && get_option( 'orddd_global_lockout_time_slots' ) != "" ) {
                                                    $available_time_slot_quantities = get_option( 'orddd_global_lockout_time_slots' );
	    	                                        if( is_array( $lockout_time_arr ) && count( $lockout_time_arr ) > 0 ) {
	    	                                            foreach ( $lockout_time_arr as $k => $v ) {
	    	                                                if ( $time_slot == $v->t && $lockout_date == $v->d ) {
	    	                                                    $available_time_slot_quantities = get_option( 'orddd_global_lockout_time_slots' ) - $v->o;
	    	                                                    break;
	    	                                                }
	    	                                            }
	    	                                        } 
	                                                
	    	                                        if( $available_time_slot_quantities < $total_quantities && $available_time_slot_quantities != 0 && $available_time_slot_quantities != '' && $is_custom_date_lockout_reached == 'No' ) {
	    	                                            $message = sprintf( __( '%1$s has only %2$s deliveries available for the time slot %3$s', 'order-delivery-date' ), $lockout_date, $available_time_slot_quantities, $_POST[ 'orddd_time_slot' ] );
                                                        $message = apply_filters( 'orddd_deliveries_message_for_timeslots' , $message, $orddd_formatted_date, $categories_names_str, $available_time_slot_quantities, $time_slot );
	    	                                            $errors->add(
                                                            'validation',
                                                            $message,
                                                            array(
                                                                'id' => $fieldset_key_timeslot
                                                            )
                                                        );
	    	                                        } else if ( $available_time_slot_quantities == 0 && $is_custom_date_lockout_reached == 'No' ) {
                                                        $message = sprintf( __( '%1$s for %2$s is not available for delivery. Please select a new time slot.', 'order-delivery-date' ), $time_slot, $lockout_date ) . " ";
                                                        $message = apply_filters( 'orddd_unavailable_message_for_timeslots' , $message, $orddd_formatted_date, $categories_names_str, $time_slot );
                                                        $errors->add(
                                                            'validation',
                                                            $message,
                                                            array(
                                                                'id' => $fieldset_key_timeslot
                                                            )
                                                        );
                                                    }
	    	                                    }
	    	                                }
	    	                            }
	    	                        }
	    	                        $shipping_based_timeslot = "Yes";
	    	                    }
	                        }
	                    }
					}
	        	}
	        	
	        	// Global Settings
                $is_date_lockout_reached = 'No';

                if( $shipping_based_date == "No" ) {
                    $lockout = get_option( 'orddd_lockout_date_after_orders' );

                    $lockout_days = get_option( 'orddd_lockout_days' );
                    $lockout_days_arr = array();
	                if ( $lockout_days != '' && $lockout_days != '{}' && $lockout_days != '[]' && $lockout_days != 'null' ) {
	                    $lockout_days_arr = json_decode( $lockout_days );
	                }
                    $specific_dates = array();

                    if( 'on' == get_option('orddd_enable_specific_delivery_dates') ) {
                        $delivery_dates = get_option( 'orddd_delivery_dates' );
                        $delivery_days = array();

                        if ( $delivery_dates != '' && $delivery_dates != '{}' && $delivery_dates != '[]' && $delivery_dates != 'null' ) {
                            $delivery_days = json_decode( $delivery_dates );
                        }
                        $is_specific_lockout = "no";

                        if( is_array( $delivery_days ) && count( $delivery_days ) > 0 ) {
                            foreach( $delivery_days as $key => $value ) {
                                $date = $value->date;
                                $max_orders = isset( $value->max_orders ) ? $value->max_orders : '';
                                array_push( $specific_dates, $date );
    
                                if( $lockout_date_format == $date && $max_orders != '' ) {
                                    $is_specific_lockout = "yes";
                                    $available_date_quantities = $max_orders;
                                }
    
                                $date_found = "no";
                                if( is_array( $lockout_days_arr ) && count( $lockout_days_arr ) > 0 ) {
                                    foreach ( $lockout_days_arr as $k => $v ) {
                                        if ( $lockout_date_format == $date && $lockout_date_format == $v->d ) {
                                            $available_date_quantities = $max_orders - $v->o;
                                            $date_found = "yes";
                                            break;
                                        } 
                                    }
                                } 
    
                                if( "yes" == $date_found ) {
                                    break;
                                }
                            }
                        }

                        if( "yes" == $is_specific_lockout ) {
                            if( $available_date_quantities < $total_quantities && $available_date_quantities > 0 && $available_date_quantities != '' ) {
                                $message = sprintf( __( '%1$s has only %2$s deliveries available for the date.', 'order-delivery-date'), $lockout_date, $available_date_quantities );
                                $message = apply_filters( 'orddd_deliveries_message_for_quantities_of_specific_date', $message, $orddd_formatted_date, $categories_names_str, $available_date_quantities );
                                $errors->add(
                                    'validation',
                                    $message,
                                    array(
                                        'id' => $fieldset_key_date
                                    )
                                );
                                $is_date_lockout_reached = 'Yes';
                            } else if( $available_date_quantities <= 0 ) {
                                $message = sprintf( __( '%s is not available for delivery. Please select a new delivery date.', 'order-delivery-date'), $lockout_date );
                                $message = apply_filters( 'orddd_unavailable_message_when_no_quantity_of_specific_date', $message, $orddd_formatted_date, $categories_names_str );
                                $errors->add(
                                    'validation',
                                    $message,
                                    array(
                                        'id' => $fieldset_key_date
                                    )
                                );
                                $is_date_lockout_reached = 'Yes';
                            }
                        }
                    }

	                if( $lockout != "0" && $lockout != "" ) {
	                    $lockout_days = get_option( 'orddd_lockout_days' );
                        $lockout_days_arr = array();
	                    if ( $lockout_days != '' && $lockout_days != '{}' && $lockout_days != '[]' && $lockout_days != 'null' ) {
	                        $lockout_days_arr = json_decode( $lockout_days );
	                    }

                        $available_date_quantities = $lockout;
                        $is_specific = "no";
	                    if( is_array( $lockout_days_arr ) && count( $lockout_days_arr ) > 0 ) {
	                        foreach ( $lockout_days_arr as $k => $v ) {
                                //If specific date then consider specific date lockout
                                if( is_array( $specific_dates ) && in_array( $lockout_date_format, $specific_dates ) ) {
                                    $is_specific = "yes";
                                    continue;
                                }
	                            if ( $lockout_date_format == $v->d ) {
	                                $available_date_quantities = $lockout - $v->o;
	                                break;
	                            } 
	                        }
	                    } 

                        if( "no" == $is_specific ) {
                            if( $available_date_quantities < $total_quantities && $available_date_quantities > 0 && $available_date_quantities != '' ) {
                                $message = sprintf( __( '%1$s has only %2$s deliveries available for the date.', 'order-delivery-date' ), $lockout_date, $available_date_quantities );
                                $message = apply_filters( 'orddd_deliveries_message_for_quantities_of_specific_date', $message, $orddd_formatted_date, $categories_names_str, $available_date_quantities );
                                $errors->add(
                                    'validation',
                                    $message,
                                    array(
                                        'id' => $fieldset_key_date
                                    )
                                );
                                $is_date_lockout_reached = 'Yes';
                            } else if( $available_date_quantities <= 0 ) {
                                $message = sprintf( __( '%s is not available for delivery. Please select a new delivery date.', 'order-delivery-date' ), $lockout_date );
                                $message = apply_filters( 'orddd_unavailable_message_when_no_quantity_of_specific_date', $message, $orddd_formatted_date, $categories_names_str );
                                $errors->add(
                                    'validation',
                                    $message,
                                    array(
                                        'id' => $fieldset_key_date
                                    )
                                );
                                $is_date_lockout_reached = 'Yes';
                            }
                        }
                    }
	            }

                if( $shipping_based_timeslot == "No" ) {
	                $lockout = 0;
	                $existing_timeslots_str = get_option( 'orddd_delivery_time_slot_log' );
	                $existing_timeslots_arr = json_decode( $existing_timeslots_str );
	                if ( is_array( $existing_timeslots_arr ) && count( $existing_timeslots_arr ) > 0 ) {
	                    foreach ( $existing_timeslots_arr as $k => $v ) {
	                        $from_time = $v->fh . ":" . trim( $v->fm );
	                        if ( $v->th != 00 ){
	                            $to_time = $v->th . ":" . trim( $v->tm );
	                            $time_slot_key = $from_time . " - " . $to_time;
	                        } else {
	                            $time_slot_key = $from_time;
	                        }

	                        if ( gettype( json_decode( $v->dd ) ) == 'array' && count( json_decode( $v->dd ) ) > 0 && get_option( 'orddd_enable_specific_delivery_dates' ) == "on" ) {
	                            $specific_dates_arr = array();
                                $delivery_dates_arr = array();

	                            $delivery_dates = get_option( 'orddd_delivery_dates' );
	                            if ( $delivery_dates != '' && $delivery_dates != '{}' && $delivery_dates != '[]' && $delivery_dates != 'null' ) {
	                                $delivery_dates_arr = json_decode( get_option( 'orddd_delivery_dates' ) );
	                            } 

	                            foreach( $delivery_dates_arr as $key => $value ) {
	                                $specific_dates_arr[] = $value->date;
	                            }

	                            $dd = json_decode( $v->dd );
	                            foreach( $dd as $dkey => $dval ) {
	                                if( in_array( $lockout_date_format, $specific_dates_arr ) ) {
	                                    if( $time_slot_key == $select_time_slot_str && $lockout_date_format == $dval ) {
	                                        $lockout = $v->lockout;
	                                        break;
	                                    }    
	                                } else {
	                                    $weekday = date( 'w', strtotime( $lockout_date ) );
	                                    if( $time_slot_key == $select_time_slot_str && ( $dval == "orddd_weekday_" . $weekday || $dval == "all" ) ) {
	                                        $lockout = $v->lockout;
	                                        break;
	                                    }
	                                }
	                            }
	                        } else {
	                            $weekday = date( 'w', strtotime( $lockout_date ) );
	                            if ( gettype( json_decode( $v->dd ) ) == 'array' && count( json_decode( $v->dd ) ) > 0 ) {
	                                $dd = json_decode( $v->dd );
	                                foreach( $dd as $dkey => $dval ) {
	                                    if( $time_slot_key == $select_time_slot_str && ( $dval == "orddd_weekday_" . $weekday || $dval == "all" ) ) {
	                                        $lockout = $v->lockout;
	                                        break;
	                                    }
	                                }
	                            } else {
	                                $dd = $v->dd;
	                                if( $time_slot_key == $select_time_slot_str && ( $dd == "orddd_weekday_" . $weekday || $dd == "all" ) ) {
	                                    $lockout = $v->lockout;
	                                }
	                            }
	                        }
	                    }
	                }  

                    $time_slot_lockout_there = 'no';
	                if( $lockout != "0" && $lockout != "" ) {                   
                        $lockout_time_arr = array();
	                    $lockout_time = get_option( 'orddd_lockout_time_slot' );
	                    if ( $lockout_time != '' && $lockout_time != '{}' && $lockout_time != '[]' && $lockout_time != 'null' ) {
                            $lockout_time_arr = (array) json_decode( $lockout_time );
	                    }
                        
                        $available_time_slot_quantities = $lockout;
	                    if( is_array( $lockout_time_arr ) && count( $lockout_time_arr ) > 0 ) {
	                        foreach ( $lockout_time_arr as $k => $v ) {
	                            if ( $time_slot == $v->t && $lockout_date == $v->d ) {
	                                $available_time_slot_quantities = $lockout - $v->o;
	                                break;
	                            } 
	                        }
	                    } 

	                    if( $available_time_slot_quantities < $total_quantities && $available_time_slot_quantities > 0 && $available_time_slot_quantities != '' && $is_date_lockout_reached == 'No' ) {
	                        $message = sprintf( __( '%1$s has only %2$s deliveries available for the time slot %3$s.', 'order-delivery-date' ), $lockout_date, $available_time_slot_quantities, $time_slot );
                            $message = apply_filters( 'orddd_deliveries_message_for_timeslots' , $message, $orddd_formatted_date, $categories_names_str, $available_time_slot_quantities, $time_slot );
	                        $errors->add(
                                'validation',
                                $message,
                                array(
                                    'id' => $fieldset_key_timeslot
                                )
                            );
	                    } else if( $available_time_slot_quantities <= 0 && $is_date_lockout_reached == 'No' ) {
                            $message = sprintf( __( '%1$s for %2$ is not available for delivery. Please select a new time slot.', 'order-delivery-date' ), $time_slot, $lockout_date );
                            $message = apply_filters( 'orddd_unavailable_message_for_timeslots' , $message, $orddd_formatted_date, $categories_names_str, $time_slot );
                            $errors->add(
                                'validation',
                                $message,
                                array(
                                    'id' => $fieldset_key_timeslot
                                )
                            );
                        }
	                }
	            }
	        }
	    }
    }

	/**
	 * Validates the delivery date while placing the order whether the date is still available or not. 
     * 
     * @hook woocommerce_after_checkout_validation
     * @globals resource $wpdb WordPress Object
     * @globals resource $current_user Current logged in user object
     * 
     * @since 4.5
	 */
	public static function orddd_validate_available_time( $data, $errors ) {
	    global $wpdb, $current_user;

        $gmt = false;
        if( has_filter( 'orddd_gmt_calculations' ) ) {
            $gmt = apply_filters( 'orddd_gmt_calculations', '' );
        }
        $current_time = current_time( 'timestamp', $gmt );
        $current_date   = date( 'd', $current_time );
        $current_month  = date( 'm', $current_time );
        $current_year   = date( 'Y', $current_time );
	    
	    $roles = array();
        if ( has_filter( 'orddd_disable_delivery_for_user_roles' ) ) {
            $roles = apply_filters( 'orddd_disable_delivery_for_user_roles', $roles );
        }
        
        $current_user_role = '';
        if( isset( $current_user->roles[0] ) ) {
            $current_user_role = $current_user->roles[0];
        }
        
        if( ( orddd_process::woo_product_has_delivery() === 'on' ) && ( 'yes' == orddd_common::orddd_is_delivery_enabled() ) && ( !in_array( $current_user_role, $roles ) ) ) {
			$fieldset_key = 'e_deliverydate';
		    if( isset( $data[ 'h_deliverydate' ] ) ) {
		        $delivery_date = $data[ 'h_deliverydate' ];
		    } else {
		        $delivery_date = '';
            }
            
            if( '' === $delivery_date ) {
                return;
            }

		    $current_day = date( 'j-n-Y', $current_time );
		    $next_day = date( "j-n-Y", strtotime( "+1 day", strtotime( $current_day ) ) );
		    $delivery_date_timestamp = strtotime( $delivery_date );
		     
            $results = orddd_common::orddd_get_shipping_settings();
		    $shipping_settings =  array();
            $shipping_settings_exists = "No";
            
            if ( isset( $data[ 'orddd_time_slot' ] ) ) {
                $time_slot = $data[ 'orddd_time_slot' ];
            }

		    if( get_option( 'orddd_enable_shipping_based_delivery' ) == 'on' && 
                is_array( $results ) && count( $results ) > 0 ) {
                $shipping_method = '';
                $product_categories = array();
                $shipping_class = '';
                $orddd_zone_id = 0;
                $location = '';
                $shipping_settings_to_check = array();
                if( isset( $_POST[ 'orddd_zone_id' ] ) ) {
                    $orddd_zone_id = $_POST[ 'orddd_zone_id' ];    
                }

                if ( isset( $_POST['orddd_locations'] ) ) {
                    $location = $_POST['orddd_locations'];
                }

                if( isset( $_POST[ 'shipping_method' ][ 0 ] ) && $_POST[ 'shipping_method'][ 0 ] != '' && is_array( $_POST[ 'shipping_method' ] ) ) {
                    $shipping_method = $_POST[ 'shipping_method' ][ 0 ];
                    if( false !== strpos( $shipping_method, 'usps' ) ) {
                        $shipping_method = $orddd_zone_id . ":" . $shipping_method;
                    }
                    if( strpos( $shipping_method, 'wf_fedex_woocommerce_shipping' )  === false && false !== strpos( $shipping_method, 'fedex' ) && is_array( explode( ":", $shipping_method ) ) && count( explode( ":", $shipping_method ) ) < 3 ) {
                        $shipping_method = $orddd_zone_id . ":" . $shipping_method;
                    }
                } else if( isset( $_POST[ 'shipping_method' ] ) && $_POST[ 'shipping_method' ] != '' ) {
                    $shipping_method = $_POST[ 'shipping_method' ];
                    if( false !== strpos( $shipping_method, 'usps') ) {
                        $shipping_method = $orddd_zone_id . ":" . $shipping_method;
                    }
                    if( strpos( $shipping_method, 'wf_fedex_woocommerce_shipping' )  === false && false !== strpos( $shipping_method, 'fedex' ) && is_array( explode( ":", $shipping_method ) ) && count( explode( ":", $shipping_method ) ) < 3 ) {
                        $shipping_method = $orddd_zone_id . ":" . $shipping_method;
                    }
                }


                if( isset( $_POST[ 'orddd_category_settings_to_load' ] ) && $_POST[ 'orddd_category_settings_to_load'] != '' ) {
                    $product_categories = explode( ',', $_POST[ 'orddd_category_settings_to_load' ] );
                }

                if( isset( $_POST[ 'orddd_shipping_class_settings_to_load' ] ) && $_POST[ 'orddd_shipping_class_settings_to_load' ] != '' ) {
                    $shipping_class = $_POST[ 'orddd_shipping_class_settings_to_load' ];
                }

                foreach ( $results as $key => $value ) {
                    $shipping_methods = array();
                    $shipping_settings = get_option( $value->option_name );
                    if ( isset( $shipping_settings['delivery_settings_based_on'][0] ) &&
                    'orddd_locations' === $shipping_settings['delivery_settings_based_on'][0] ) {
                        if ( in_array( $location, $shipping_settings['orddd_locations'], true ) ) {
                            $shipping_settings_exists   = 'Yes';
                            $shipping_settings_to_check = $shipping_settings;
                        }
                    } elseif ( isset( $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] ) && $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] == 'shipping_methods' ) {
                        if( has_filter( 'orddd_get_shipping_method' ) ) {
                            $shipping_methods_values = apply_filters( 'orddd_get_shipping_method', $shipping_settings, $_POST, $shipping_settings[ 'shipping_methods' ], $shipping_method );    
                            $shipping_settings[ 'shipping_methods' ] = $shipping_methods_values[ 'shipping_methods' ];
                            $shipping_method  = $shipping_methods_values[ 'shipping_method' ];
                        }

                        if( in_array( $shipping_method, $shipping_settings[ 'shipping_methods' ] ) ) {
                            $shipping_settings_exists   = "Yes";
                            $shipping_settings_to_check = $shipping_settings;
                        }
                    }   
                }

                if( 'No' == $shipping_settings_exists ) {
                    foreach( $product_categories as $pkey => $pvalue ) {
                        foreach ( $results as $key => $value ) {
                            $shipping_methods = array();
                            $shipping_settings = get_option( $value->option_name );
                            if( isset( $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] ) && $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] == 'product_categories' ) {
                                if( in_array( $pvalue, $shipping_settings[ 'product_categories' ] ) ) {
                                    if( isset( $shipping_settings[ 'shipping_methods_for_categories' ] ) && (in_array( $shipping_method, $shipping_settings[ 'shipping_methods_for_categories' ] )  || in_array( $shipping_class, $shipping_settings[ 'shipping_methods_for_categories' ] ) ) ) {
                                        $shipping_settings_exists = "Yes";
                                        $is_combination_enabled = 'yes';
                                        $shipping_settings_to_check = $shipping_settings;
                                    }
                                }
                            }   
                        }
                    }
                }

                if( 'No' == $shipping_settings_exists ) {
                    foreach( $product_categories as $pkey => $pvalue ) {
                        foreach ( $results as $key => $value ) {
                            $shipping_settings = get_option( $value->option_name );
                            if( isset( $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] ) && $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] == 'product_categories' ) {
                                if( in_array( $pvalue, $shipping_settings[ 'product_categories' ] ) ) {
                                    if( !isset( $shipping_settings[ 'shipping_methods_for_categories' ] ) ) {
                                        $shipping_settings_exists = "Yes";
                                        $is_combination_enabled = 'yes';
                                        $shipping_settings_to_check = $shipping_settings;
                                    }
                                }
                            }   
                        }
                    }
                }

                if( 'No' == $shipping_settings_exists ) {
                    foreach ( $results as $key => $value ) {
                        $shipping_methods = array();
                        $shipping_settings = get_option( $value->option_name );
                        if( isset( $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] ) && $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] == 'shipping_methods' ) {
                            if( in_array( $shipping_class, $shipping_settings[ 'shipping_methods' ] ) ) {
                                $shipping_settings_exists = "Yes";
                                $is_combination_enabled = 'yes';
                                $shipping_settings_to_check = $shipping_settings;
                            }
                        }   
                    }
                }
                
                if( "Yes" == $shipping_settings_exists && 'on' === $shipping_settings_to_check['enable_shipping_based_delivery'] ) {

                    $same_day_cut_off = orddd_get_highest_same_day();
                    $custom_same_day = array();
                    if( is_array( $same_day_cut_off ) && count( $same_day_cut_off ) > 0 ) {
                        $custom_same_day = $same_day_cut_off;
                    } else {
                        if( isset( $shipping_settings_to_check[ 'same_day' ] ) ) {
                            $custom_same_day = $shipping_settings_to_check[ 'same_day' ];       
                        }
                    }

                    if( isset( $custom_same_day[ 'after_hours' ] ) && $custom_same_day[ 'after_hours' ] == 0 && isset( $custom_same_day[ 'after_minutes' ] ) && $custom_same_day[ 'after_minutes' ] == 00 ) {
                    } else if( is_array( $custom_same_day ) && count( $custom_same_day ) > 0 ) {

                        if ( isset( $custom_same_day[ 'after_hours' ] ) ) {
                            $cut_off_hour = $custom_same_day[ 'after_hours' ];
                            $cut_off_minute = $custom_same_day[ 'after_minutes' ];
                             
                            $cut_off_timestamp = gmmktime( $cut_off_hour, $cut_off_minute, 0, $current_month, $current_date, $current_year );
                            if ( $delivery_date == $current_day ) {
                                if ( $cut_off_timestamp < $current_time ) {
                                    $message = __( 'Cut-off time for same day delivery has expired. Please select another date for delivery.', 'order-delivery-date' );
                                    $errors->add(
                                        'validation',
                                        $message,
                                        array(
                                            'id' => $fieldset_key
                                        )
                                    );
                                }
                            }
                        }
                        
                    }

                    //Nexy Day Delivery
                    $next_day_cut_off = orddd_get_highest_next_day();
                    $custom_next_day = array();
                    if( is_array( $next_day_cut_off ) && count( $next_day_cut_off ) > 0 ) {
                        $custom_next_day = $next_day_cut_off;
                    } else {
                        if( isset( $shipping_settings_to_check[ 'next_day' ] ) ) {
                            $custom_next_day = $shipping_settings_to_check[ 'next_day' ];       
                        }
                    }

                    if ( isset( $custom_next_day[ 'after_hours' ] ) && $custom_next_day[ 'after_hours' ] == 0 && isset( $custom_next_day[ 'after_minutes' ] ) && $custom_next_day[ 'after_minutes' ] == 00 ) {
                    } else if ( is_array( $custom_next_day ) && count( $custom_next_day ) > 0 ) {

                        if ( isset( $custom_next_day[ 'after_hours' ] ) ) {
                            $cut_off_hour       = $custom_next_day[ 'after_hours' ];
                            $cut_off_minute     = $custom_next_day[ 'after_minutes' ];
                            $cut_off_timestamp  = gmmktime( $cut_off_hour, $cut_off_minute, 0, $current_month, $current_date, $current_year );
                            
                            if ( $delivery_date == $next_day ) {
                                if ( $cut_off_timestamp < $current_time ) {
                                    $message = __( 'Cut-off time for next day delivery has expired. Please select another date for delivery.', 'order-delivery-date' );
                                    $errors->add(
                                        'validation',
                                        $message,
                                        array(
                                            'id' => $fieldset_key
                                        )
                                    );
                                }
                            }
                        }
                    }

                    if( ( isset( $custom_same_day[ 'after_hours' ] ) && $custom_same_day[ 'after_hours' ] == 0 && isset( $custom_same_day[ 'after_minutes' ] ) && $custom_same_day[ 'after_minutes' ] == 00 ) && ( isset( $custom_next_day[ 'after_hours' ] ) && $custom_next_day[ 'after_hours' ] == 0 && isset( $custom_next_day[ 'after_minutes' ] ) && $custom_next_day[ 'after_minutes' ] == 00 ) ) {
                        $minimum_time  = orddd_get_higher_minimum_delivery_time();
                        $cut_off_hour  = 0;

                        if ( '' !== $minimum_time && 0 != $minimum_time ) { //phpcs:ignore
                            $cut_off_hour = $minimum_time;
                        } else {
                            if ( isset( $shipping_settings_to_check['minimum_delivery_time'] ) && '' !== $shipping_settings_to_check['minimum_delivery_time'] ) {
                                $cut_off_hour = $shipping_settings_to_check['minimum_delivery_time'];
                                if ( '' === $cut_off_hour ) {
                                    $cut_off_hour = 0;
                                }
                            }
                        }
                        if ( isset( $cut_off_hour ) && $cut_off_hour > 0 ) {
                            $cut_off_hour   = 24 - $cut_off_hour;
                            $cut_off_minute = 0 ;
                            if ( $delivery_date !== $current_day ) {
                                $current_date   = date( 'd', $delivery_date_timestamp );
                                $current_month  = date( 'm', $delivery_date_timestamp );
                                $current_year   = date( 'Y', $delivery_date_timestamp );
                            }
        
                            $cut_off_timestamp = gmmktime( $cut_off_hour, $cut_off_minute, 0, $current_month, $current_date, $current_year );
                  
                            if ( $cut_off_timestamp < $current_time ) {
                                $message = __( 'Cut-off time for the delivery day has expired. Please select another date for delivery.', 'order-delivery-date' );
                                $errors->add(
                                    'validation',
                                    $message,
                                    array(
                                        'id' => $fieldset_key
                                    )
                                );
                            }
                        }
                    }
                   
                    if( isset( $shipping_settings_to_check['time_slots'] ) && '' !== $shipping_settings_to_check['time_slots'] && $delivery_date == $current_day && 'select' !== $time_slot && 'asap' !== $time_slot ) {
                        $time_slot_arr = explode( ' - ', $time_slot );
                        $cut_off_hour  = 0;
                        $from_time = $time_slot_arr[0];
                        $to_time   = $time_slot_arr[1];

                        $minimum_time     = orddd_get_higher_minimum_delivery_time();

                        if ( '' !== $minimum_time && 0 !== $minimum_time ) { //phpcs:ignore
                            $cut_off_hour = $minimum_time;
                        } else {
                            if ( isset( $shipping_settings_to_check['minimum_delivery_time'] ) && '' !== $shipping_settings_to_check['minimum_delivery_time'] ) {
                                $cut_off_hour = $shipping_settings_to_check['minimum_delivery_time'];
                                if ( '' === $cut_off_hour ) {
                                    $cut_off_hour = 0;
                                }
                            }
                        }

                        $min_time_in_secs = $cut_off_hour * 60 * 60;
                        $min_time_on_last_slot = apply_filters( 'orddd_min_delivery_on_last_slot', false );
                        if ( $min_time_on_last_slot ) {
                            $delivery_time = strtotime( $delivery_date . " " . $to_time );
                        } else {
                            $delivery_time = strtotime( $delivery_date . " " . $from_time );   
                        }
                        
                        if( $min_time_in_secs > 0 ) {
                            $delivery_time = $delivery_time - $min_time_in_secs;
                        }
                     
                        if( $current_time > $delivery_time ) {
                            $message = __( 'The selected time slot has expired. Please select another time slot for delivery.', 'order-delivery-date' );
                            $errors->add(
                                'validation',
                                $message,
                                array(
                                    'id' => $fieldset_key
                                )
                            );
                        }
                    }
                }
		    }

		    if ( $shipping_settings_exists == "No" ) {
		        if ( get_option( 'orddd_enable_same_day_delivery' ) == 'on' && get_option( 'orddd_enable_delivery_date' ) == 'on' ) {
		            $cut_off_hour = get_option( 'orddd_disable_same_day_delivery_after_hours' );
		            $cut_off_minute = get_option( 'orddd_disable_same_day_delivery_after_minutes' );

	                if( 'on' == get_option( 'orddd_enable_day_wise_settings' ) ) {
	                    $current_weekday = "orddd_weekday_" . date( "w", $current_time );
	                    $advance_settings = get_option( 'orddd_advance_settings' );
	                    if( '' == $advance_settings || '{}' == $advance_settings || '[]' == $advance_settings) {
	                        $advance_settings = array();
	                    }
	                    foreach( $advance_settings as $ak => $av ) {
	                        if( $current_weekday == $av[ 'orddd_weekdays' ] ) {
	                            if( "" != $av[ 'orddd_disable_same_day_delivery_after_hours' ] ) {
	                                $cut_off_time = explode( ":", $av[ 'orddd_disable_same_day_delivery_after_hours' ] );
	                                $cut_off_hour = $cut_off_time[ 0 ];
	                                $cut_off_minute = $cut_off_time[ 1 ];    
	                            }
	                        }
	                    }
	                }   

		            $cut_off_timestamp = gmmktime( $cut_off_hour, $cut_off_minute, 0, $current_month, $current_date, $current_year );
		            if ( $delivery_date == $current_day ) {
		                if ( $cut_off_timestamp < $current_time ) {
		                    $message = __( 'Cut-off time for same day delivery has expired. Please select another date for delivery.', 'order-delivery-date' );
		                    $errors->add(
                                'validation',
                                $message,
                                array(
                                    'id' => $fieldset_key
                                )
                            );
		                }
		            }
		        }
		
		        if ( get_option( 'orddd_enable_next_day_delivery' ) == 'on' && get_option( 'orddd_enable_delivery_date' ) == 'on' ) {
		            $cut_off_hour = get_option( 'orddd_disable_next_day_delivery_after_hours' );
		            $cut_off_minute = get_option( 'orddd_disable_next_day_delivery_after_minutes' );

	                if( 'on' == get_option( 'orddd_enable_day_wise_settings' ) ) {
	                    $current_weekday = "orddd_weekday_" . date( "w", $current_time );
	                    $advance_settings = get_option( 'orddd_advance_settings' );
	                    if( '' == $advance_settings || '{}' == $advance_settings || '[]' == $advance_settings) {
	                        $advance_settings = array();
	                    }
	                    foreach( $advance_settings as $ak => $av ) {
	                        if( $current_weekday == $av[ 'orddd_weekdays' ] ) {
	                            if( "" != $av[ 'orddd_disable_next_day_delivery_after_hours' ] ) {
	                                $cut_off_time = explode( ":", $av[ 'orddd_disable_next_day_delivery_after_hours' ] );
	                                $cut_off_hour = $cut_off_time[0];
	                                $cut_off_minute = $cut_off_time[1];
	                            }
	                        }
	                    }
	                }

		            $cut_off_timestamp = gmmktime( $cut_off_hour, $cut_off_minute, 0, $current_month, $current_date, $current_year );
		            if ( $delivery_date == $next_day ) {
		                if ( $cut_off_timestamp < $current_time ) {
		                    $message = __( 'Cut-off time for next day delivery has expired. Please select another date for delivery.', 'order-delivery-date' );
		                    $errors->add(
                                'validation',
                                $message,
                                array(
                                    'id' => $fieldset_key
                                )
                            );
		                }
		            }
		        } else if ( get_option( 'orddd_enable_delivery_date' ) == 'on' && get_option( 'orddd_enable_next_day_delivery' ) != 'on' && get_option( 'orddd_enable_same_day_delivery' ) != 'on' ) {
                    $cut_off_hour   = get_option( 'orddd_minimumOrderDays' );

                    if ( isset( $cut_off_hour ) && $cut_off_hour > 0 ) {
                        $cut_off_hour   = 24 - $cut_off_hour;
                        $cut_off_minute = 0 ;
                        
                        if ( $delivery_date !== $current_day ) {
                            $current_date   = date( 'd', $delivery_date_timestamp );
                            $current_month  = date( 'm', $delivery_date_timestamp );
                            $current_year   = date( 'Y', $delivery_date_timestamp );
                        }
    
                        $cut_off_timestamp = gmmktime( $cut_off_hour, $cut_off_minute, 0, $current_month, $current_date, $current_year );
                        if ( $cut_off_timestamp < $current_time ) {
                            $message = __( 'Cut-off time for the delivery day has expired. Please select another date for delivery.', 'order-delivery-date' );
                            $errors->add(
                                'validation',
                                $message,
                                array(
                                    'id' => $fieldset_key
                                )
                            );
                        }
                    }
                }

                if( 'on' === get_option( 'orddd_enable_time_slot' ) && $delivery_date == $current_day && 'asap' !== $time_slot && 'select' !== $time_slot ) {
                    $time_slot_arr = explode( ' - ', $time_slot );
                   
                    $from_time        = $time_slot_arr[0];
                    $to_time          = $time_slot_arr[1];
                    $cut_off_hour     = '' !== get_option( 'orddd_minimumOrderDays' ) ? get_option( 'orddd_minimumOrderDays' ) : 0;
                    $min_time_in_secs = $cut_off_hour * 60 * 60;

					$min_time_on_last_slot = apply_filters( 'orddd_min_delivery_on_last_slot', false );
                    if ( $min_time_on_last_slot ) {
                        $delivery_time = strtotime( $delivery_date . " " . $to_time );
                    } else {
                        $delivery_time = strtotime( $delivery_date . " " . $from_time );   
                    }

                    if( $min_time_in_secs > 0 ) {
                        $delivery_time = $delivery_time - $min_time_in_secs;
                    }
                   
                    if( $current_time > $delivery_time ) {
                        $message = __( 'The selected time slot has expired. Please select another time slot for delivery.', 'order-delivery-date' );
                        $errors->add(
                            'validation',
                            $message,
                            array(
                                'id' => $fieldset_key
                            )
                        );
                    }
                }
		    }
		}
	}
	
	/**
	 * Check if the product has delivery enabled or not
	 *
     * @globals resource $woocommerce WooCommerce Object
     *
	 * @return bool True if delivery is enabled, else false
     * @since 2.8.6
	 */
	public static function woo_product_has_delivery() {
        $is_view_subscription_page = orddd_common::orddd_is_view_subscription_page();
        $product_category_enabled = 'off';

        if( is_plugin_active( 'woocommerce-one-page-checkout/woocommerce-one-page-checkout.php' ) ) {
            return orddd_integration::$product_category_enabled;
        }

        $is_frontend = ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! WC()->is_rest_api_request();
       
        if( ( is_cart() || is_checkout() || $is_frontend || ( isset( $_POST['amazon_access_token'] ) && '' !== $_POST['amazon_access_token'] ) ) && !is_wc_endpoint_url( 'view-order' ) && ( false === $is_view_subscription_page ) ) {
            global $woocommerce;

    	    foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
    	        $product = $values[ 'data' ];
                if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
                    $product_id = $product->get_id();
                } else {
                    $product_id = $product->id;
                }

                if ( 'product_variation' == $product->post_type ) {
                    $product_id = $values[ 'product_id' ];
                }

    	        $terms = get_the_terms( $product_id, 'product_cat' );
    	       
    	        $is_enabled = 'no';
    	        if( $terms == '' ) {
    	            if ( has_filter( 'orddd_remove_delivery_date_if_product_category_no' ) ) {
    	                $is_enabled = apply_filters( 'orddd_remove_delivery_date_if_product_category_no', $is_enabled );
    	            }
    	            if ( $is_enabled == 'yes' ) {
    	                $product_category_enabled = 'on';
    	            } else {
    	                return 'on';
    	            }
    	        } else {
    	            foreach ( $terms as $term ) {
    	                $categoryid = $term->term_id;
                        $delivery_date  = get_term_meta( $categoryid, 'orddd_delivery_date_for_product_category', true );
    	
    	                if ( has_filter( 'orddd_remove_delivery_date_if_product_category_no' ) ) {
    	                    $is_enabled = apply_filters( 'orddd_remove_delivery_date_if_product_category_no', $is_enabled );
    	                }
    	                if ( $is_enabled == 'yes' ) {
    	                    if( $delivery_date === 'on' ) {
    	                        $product_category_enabled = 'on';
    	                    } else {
    	                        $product_category_enabled = 'off';
    	                        break 2;
    	                    }
    	                } else {
    	                    if( $delivery_date === 'on' ) {
    	                        $product_category_enabled = 'on';
                                break 2;
    	                    } else {
    	                        $product_category_enabled = 'off';
    	                    }
    	                }
    	            }
    	        }
    	    }
    	    return $product_category_enabled;
        } elseif ( is_wc_endpoint_url( 'view-order' ) || ( true === $is_view_subscription_page ) ) {
            global $wp;
            if( isset( $wp->query_vars[ 'view-order' ] ) ) {
                $order_id = $wp->query_vars[ 'view-order' ];
            } else if( isset( $wp->query_vars[ 'view-subscription' ] ) ) {
                $order_id = $wp->query_vars[ 'view-subscription' ];
            }
            $order = new WC_Order( $order_id );
            $items = $order->get_items();
            foreach( $items as $key => $value ) {
                $product_id = $value[ 'product_id' ];
                $terms = $terms = get_the_terms( $product_id, 'product_cat' );
                $product_category_enabled = 'off';
                $is_enabled = 'no';
                if( $terms == '' ) {
                    if ( has_filter( 'orddd_remove_delivery_date_if_product_category_no' ) ) {
                        $is_enabled = apply_filters( 'orddd_remove_delivery_date_if_product_category_no', $is_enabled );
                    }
                    if ( $is_enabled == 'yes' ) {
                        $product_category_enabled = 'on';
                    } else {
                        return 'on';
                    }
                } else {
                    foreach ( $terms as $term ) {
                        $categoryid = $term->term_id;
                        $delivery_date  = get_term_meta( $categoryid, 'orddd_delivery_date_for_product_category', true );
        
                        if ( has_filter( 'orddd_remove_delivery_date_if_product_category_no' ) ) {
                            $is_enabled = apply_filters( 'orddd_remove_delivery_date_if_product_category_no', $is_enabled );
                        }
                        if ( $is_enabled == 'yes' ) {
                            if( $delivery_date === 'on' ) {
                                $product_category_enabled = 'on';
                            } else {
                                $product_category_enabled = 'off';
                                break 2;
                            }
                        } else {
                            if( $delivery_date === 'on' ) {
                                $product_category_enabled = 'on';
                                break 2;
                            } else {
                                $product_category_enabled = 'off';
                            }
                        }
                    }
                }
            }
            return $product_category_enabled;
        }
	}

    /**
     * Updates the Common delivery settings for different product categories added to the cart on Shipping method change.
     *
     * @hook wp_ajax_nopriv_orddd_update_delivery_session
     * @hook wp_ajax_orddd_update_delivery_session
     *
     * @since 2.8.6
     */
    public static function orddd_update_delivery_session() {
        $shipping_method = '';
        if( isset( $_POST[ 'shipping_method' ] ) ) {
        	$shipping_method = $_POST[ 'shipping_method' ];
        }

        $settings_based_on = 'categories';
        if( isset( $_POST['settings_based_on'] ) ) {
            $settings_based_on = $_POST['settings_based_on'];
        }

        $get_common_delivery_days_str = '';
	$common_settings_applied = false;
		// It checks if the weglot plugin is active or not and if it active then it will add a attribute for not translates the response string.
        if ( is_plugin_active('weglot/weglot.php') ) {
            $get_common_delivery_days_str = '<div id="get_common_delivery_days_str" data-wg-notranslate>';
        }
        if( get_option( 'orddd_enable_shipping_based_delivery' ) == 'on'  ) {
            $get_common_delivery_days = orddd_common::orddd_common_delivery_days_for_product_category( $shipping_method, true );
            
            if( is_array( $get_common_delivery_days[ 'orddd_common_weekdays' ] ) && count( $get_common_delivery_days[ 'orddd_common_weekdays' ] ) > 0 ) {
                $get_common_delivery_days_json = json_encode( $get_common_delivery_days[ 'orddd_common_weekdays' ] );
                $get_common_delivery_days_str .= $get_common_delivery_days_json;
            }

            $get_common_delivery_days_str .= "%%"; 
            if( is_array( $get_common_delivery_days[ 'orddd_common_specific_dates' ] ) && count( $get_common_delivery_days[ 'orddd_common_specific_dates' ] ) > 0 ) {
                $delivery_dates_str = "";
                foreach( $get_common_delivery_days[ 'orddd_common_specific_dates' ] as $key => $value ) {
                    $delivery_dates_str .= '"' . $value . '",';
                }
                $delivery_dates_str = substr( $delivery_dates_str, 0, strlen( $delivery_dates_str )-1 );
                $get_common_delivery_days_str .= $delivery_dates_str;
            }

            $get_common_delivery_days_str .= "%%";

            if( is_array( $get_common_delivery_days[ 'orddd_common_holidays' ] ) && count( $get_common_delivery_days[ 'orddd_common_holidays' ] ) > 0 ) {
				$holidays_str = "";
	        	foreach( $get_common_delivery_days[ 'orddd_common_holidays' ] as $key => $value ) {
	                $holidays_str .= '"' . $value . '",';
	            }
	            $holidays_str = substr( $holidays_str, 0, strlen( $holidays_str )-1 );
	         	$get_common_delivery_days_str .= $holidays_str;   
			}

            $get_common_delivery_days_str .= "%%";
            if( is_array( $get_common_delivery_days[ 'orddd_common_locked_days' ] ) && count( $get_common_delivery_days[ 'orddd_common_locked_days' ] ) > 0 ) {
                $locked_days_str = "";
                foreach( $get_common_delivery_days[ 'orddd_common_locked_days' ] as $key => $value ) {
                    $locked_days_str .= '"' . $value . '",';
                }
                $locked_days_str = substr( $locked_days_str, 0, strlen( $locked_days_str )-1 );
                $get_common_delivery_days_str .= $locked_days_str;   
            }

			$get_common_delivery_days_str .= "%%";
            if( isset( $get_common_delivery_days[ 'orddd_is_days_common' ] ) ) {
                $get_common_delivery_days_str .= $get_common_delivery_days[ 'orddd_is_days_common' ];
            }
            
            $get_common_delivery_days_str .= "%%";
            if( isset( $get_common_delivery_days[ 'orddd_categories_settings_common' ] ) ) {
                $get_common_delivery_days_str .= $get_common_delivery_days[ 'orddd_categories_settings_common' ];
                if( 'yes' == $get_common_delivery_days[ 'orddd_categories_settings_common' ] ) {
                    $common_settings_applied = true;
                }
            }

            // update hidden variables for categories & shipping classes too
            // added in v9.6 to make 1 single ajax call after a cart item is deleted
            $get_common_delivery_days_str .= "%%";
            if( isset( $get_common_delivery_days[ 'orddd_category_settings_to_load' ] ) ) {
                $get_common_delivery_days_str .= $get_common_delivery_days[ 'orddd_category_settings_to_load' ];
            }

            $get_common_delivery_days_str .= "%%";
            if( isset( $get_common_delivery_days[ 'orddd_shipping_class_settings_to_load' ] ) ) {
                $get_common_delivery_days_str .= $get_common_delivery_days[ 'orddd_shipping_class_settings_to_load' ];
            }

            $get_common_delivery_days_str .= "%%";
            $partially_booked_dates_str = orddd_widget::get_partially_booked_dates( $shipping_method );
            $get_common_delivery_days_str .= $partially_booked_dates_str;

            // This will return the hidden variables for the custom delivery settings only when the cart item is deleted or Undo action is performed.
            // This is mainly added when combinations of settings are there for multiple product categories or shipping classes. 
            if( $common_settings_applied || 'category_shipping' === $settings_based_on || ( isset( $_POST[ 'called_from' ] ) && $_POST[ 'called_from' ] == 'cart_delete' ) ) {
                $get_common_delivery_days_str .= "%%";
                $hidden_vars_str = orddd_common::orddd_get_shipping_based_settings();
                $get_common_delivery_days_str .= $hidden_vars_str;
            }
        }

        if ( is_plugin_active('weglot/weglot.php') ) {
            $get_common_delivery_days_str .= '</div>';
        }
        wp_send_json( $get_common_delivery_days_str );
        die();
    }

    /**
     * Add hidden fields on the Cart page.
     *
     * @hook woocommerce_after_cart_table
     *
     * @since 7.0
     */
    public static function show_hidden_fields() {
        echo '<input type="hidden" name="hidden_e_deliverydate" id="hidden_e_deliverydate" value="">';
        echo '<input type="hidden" name="hidden_h_deliverydate" id="hidden_h_deliverydate" value="">';
        echo '<input type="hidden" name="hidden_timeslot" id="hidden_timeslot" value="">';
        echo '<input type="hidden" name="hidden_shipping_method" id="hidden_shipping_method" value="">';
        echo '<input type="hidden" name="hidden_shipping_class" id="hidden_shipping_class" value="">';
        echo '<input type="hidden" name="hidden_location" id="hidden_location" value="">';
    }
}
$orddd_process = new orddd_process();
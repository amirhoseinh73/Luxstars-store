<?php
/**
 * Order Delivery Date Pro for WooCommerce
 *
 * Handles the Add/Edit of the Delivery Date and Time in the admin order.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Pro-for-WooCommerce/Admin/Edit-Order
 * @since       3.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once 'includes/class-orddd-lockout-functions.php';

/**
 * Main class which will handle the Add/Edit of Delivery information in the WooCommerce Add/Edit Orders page.
 *
 * @class orddd_admin_delivery_class
 */
class orddd_admin_delivery_class {

	/**
	 * Default Constructor.
	 *
	 * @since 3.2
	 */
	public function __construct() {
		add_action( 'wp_ajax_orddd_save_order_items', array( &$this, 'orddd_load_delivery_dates' ) );
		add_action( 'wp_ajax_orddd_remove_order_item', array( &$this, 'orddd_remove_order_item' ) );
		add_action( 'woocommerce_saved_order_items', array( &$this, 'orddd_woocommerce_saved_order_items' ), 10, 2 );
		add_action( 'wp_trash_post', array( 'orddd_common', 'orddd_cancel_delivery_for_trashed' ), 10, 1 );
		add_action( 'untrash_post', array( 'orddd_common', 'orddd_untrash_order' ), 10, 1 );
		add_action( 'wp_ajax_save_delivery_dates', array( &$this, 'save_delivery_dates' ) );
		// Display Order Delivery Date meta box on Add/Edit Orders Page.
		if ( get_option( 'orddd_enable_delivery_date' ) == 'on' ) {
			add_action( 'add_meta_boxes', array( &$this, 'orddd_admin_delivery_box' ) );
		}
		add_filter( 'woocommerce_hidden_order_itemmeta', array( &$this, 'orddd_hidden_order_itemmeta' ) );
	}

	/**
	 * Meta box for Delivery date and/or Time slot in WooCommerce Add/Edit Orders page.
	 *
	 * @hook add_meta_boxes
	 * @since 3.2
	 */
	public static function orddd_admin_delivery_box() {
		add_meta_box( 'order-delivery-date', __( 'Edit Order Delivery Date and/or Time', 'order-delivery-date' ), array( 'orddd_admin_delivery_class', 'orddd_meta_box' ), 'shop_order', 'normal', 'core' );
		if ( 'on' === get_option( 'orddd_enable_woo_subscriptions_compatibility' ) ) {
			add_meta_box( 'order-delivery-date', __( 'Edit Order Delivery Date and/or Time', 'order-delivery-date' ), array( 'orddd_admin_delivery_class', 'orddd_meta_box' ), 'shop_subscription', 'normal', 'core' );
		}
	}

	/**
	 * Delivery Date and/or Time slot fields in the Meta box
	 *
	 * @param resource $order - Order Details.
	 * @param array    $post - Post Details.
	 *
	 * @globals resource $wpdb
	 * @globals array $orddd_date_formats
	 * @globals resource $post
	 * @globals resource $woocommerce
	 * @globals array $orddd_languages
	 * @globals array $orddd_weekdays
	 * @since 3.2
	 */
	public static function orddd_meta_box( $order, $post ) {
		global $wpdb, $orddd_date_formats, $post, $woocommerce, $orddd_languages, $orddd_weekdays;
		if ( get_option( 'orddd_enable_delivery_date' ) == 'on' ) {
			$field_name      = 'e_deliverydate';
			$orddd_post_type = $post->post_type;
			$order_id        = $order->ID;

			$date_field_label = orddd_custom_delivery_functions::orddd_fetch_delivery_date_field_label( $order_id );
			$time_field_label = orddd_custom_delivery_functions::orddd_fetch_time_slot_field_label( $order_id );

			$get_order_item_ids_query = 'SELECT * FROM `' . $wpdb->prefix . 'woocommerce_order_items` WHERE order_id = %d';
			$results_order_item_ids   = $wpdb->get_results( $wpdb->prepare( $get_order_item_ids_query, $order_id ) );
			$product_id               = $shipping_method = '';
			foreach ( $results_order_item_ids as $key => $value ) {
				$order_item_id      = $value->order_item_id;
				$get_itemmeta_query = 'SELECT * FROM `' . $wpdb->prefix . 'woocommerce_order_itemmeta` WHERE order_item_id = %d';
				$results            = $wpdb->get_results( $wpdb->prepare( $get_itemmeta_query, $order_item_id ) );
				foreach ( $results as $key => $value ) {
					if ( $value->meta_key == '_product_id' ) {
						$product_id = $value->meta_value;
					}
					if ( $value->meta_key == 'method_id' ) {
						$shipping_method = $value->meta_value;
					}
				}
			}

			$hidden_variables = orddd_common::load_hidden_fields( '', $order_id );
			echo $hidden_variables;

			// Default the fees.
			$fee = get_post_meta( $order_id, '_total_delivery_charges', true );
			if ( '' != $fee || '{}' != $fee || '[]' != $fee ) {
				$fee_name = 'Delivery Charges:';
			} else {
				$fee      = 0;
				$fee_name = '';
				foreach ( $order->get_items( 'fee' ) as $item_id => $item_fee ) {
					if ( $item_fee->get_total() != '' && $item_fee->get_total() > 0 ) {
						$fee_name = ( $item_fee->get_name() != '' ) ? $item_fee->get_name() : __( 'Delivery Charges:', 'order-delivery-date' );
						$fee     += $item_fee->get_total();
					}
				}
				$fee_name = ( ( is_array( $order->get_items( 'fee' ) ) && count( $order->get_items( 'fee' ) ) ) > 1 || $fee_name == '' ) ? 'Delivery Charges:' : $fee_name;
			}

			$disabled = '';
			if ( 'auto-draft' == get_post_status( $order_id ) ) {
				$disabled = 'disabled';
			}

			wc_get_template(
				'orddd-admin-datepicker.php',
				array(
					'date_field_label'  => $date_field_label,
					'field_name'        => $field_name,
					'time_slot_enabled' => get_option( 'orddd_enable_time_slot' ),
					'time_field_label'  => $time_field_label,
					'fee_name' 			=> $fee_name,
					'fee' 				=> $fee,
				),
				'order-delivery-date/',
				ORDDD_TEMPLATE_PATH
			);

		}
	}

	/**
	 * Check if delivery is enabled for the products in the order.
	 *
	 * @return void
	 */
	public static function orddd_is_delivery_enabled( $order ) {
		$delivery_enabled = true;
		$items            = $order->get_items();

		if ( get_option( 'orddd_no_fields_for_virtual_product' ) == 'on' && get_option( 'orddd_no_fields_for_featured_product' ) == 'on' ) {
			foreach ( $items as $key => $value ) {
				$product_id = $value['product_id'];
				if ( $product_id != 0 ) {
					if ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', '>=' ) ) {
						$product = wc_get_product( $product_id );
					} else {
						$product = get_product( $product_id );
					}
					if ( $product->is_virtual() == false && $product->is_featured() == false ) {
						$delivery_enabled = true;
						break;
					} else {
						$delivery_enabled = false;
					}
				}
			}
		} elseif ( get_option( 'orddd_no_fields_for_virtual_product' ) == 'on' && get_option( 'orddd_no_fields_for_featured_product' ) != 'on' ) {
			foreach ( $items as $key => $value ) {
				$product_id = $value['product_id'];
				if ( $product_id != 0 ) {
					if ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', '>=' ) ) {
						$product = wc_get_product( $product_id );
					} else {
						$product = get_product( $product_id );
					}
					if ( $product->is_virtual() == false ) {
						$delivery_enabled = true;
						break;
					} else {
						$delivery_enabled = false;
					}
				}
			}
		} elseif ( get_option( 'orddd_no_fields_for_virtual_product' ) != 'on' && get_option( 'orddd_no_fields_for_featured_product' ) == 'on' ) {
			foreach ( $items as $key => $value ) {
				$product_id = $value['product_id'];
				if ( $product_id != 0 ) {
					if ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', '>=' ) ) {
						$product = wc_get_product( $product_id );
					} else {
						$product = get_product( $product_id );
					}
					if ( $product->is_featured() == false ) {
						$delivery_enabled = true;
						break;
					} else {
						$delivery_enabled = false;
					}
				}
			}
		}

		return $delivery_enabled;
	}

	/**
	 * Send admin side data to JS.
	 */
	public static function orddd_localize_admin_scripts() {
		global $wp_query, $post;

		$screen          = get_current_screen();
		$screen_id       = $screen ? $screen->id : '';
		$order_id 		 = isset( $post->ID ) ? $post->ID : '';
		$orddd_post_type = isset( $post->post_type ) ? $post->post_type : '';
		$data            = get_post_meta( $order_id );
		$admin_params	 = array();

		$admin_params['orddd_admin_url'] = get_admin_url();
		$admin_params['orddd_order_id']  = $order_id;
		$admin_params['orddd_post_type'] = $orddd_post_type;

		if ( is_plugin_active( 'woocommerce-shipping-canada-post/woocommerce-shipping-canada-post.php' ) ) {
			$orddd_canada_post_id = get_post_meta( $order_id, '_orddd_canada_post_id', true );
			$admin_params['orddd_canada_post_id']  = $orddd_canada_post_id;
		}

		$date_field_label = orddd_custom_delivery_functions::orddd_fetch_delivery_date_field_label( $order_id );
		$time_field_label = orddd_custom_delivery_functions::orddd_fetch_time_slot_field_label( $order_id );

		$fixed_time        = 'off';
		$default_date_time = '';
	
		if ( isset( $data['_orddd_timestamp'][0] ) && $data['_orddd_timestamp'][0] != '' ) {
			$default_date           = date( 'd-m-Y', $data['_orddd_timestamp'][0] );
		
			$default_h_deliverydate = date( 'j-n-Y', $data['_orddd_timestamp'][0] );
			$time_selected          = date( 'H:i', $data['_orddd_timestamp'][0] );
			if ( $time_selected != '00:01' && $time_selected != '' && $time_selected != '00:00' ) {
				$fixed_time        = 'on';
				$default_date_time = $time_selected;
			}
		} elseif ( isset( $data[ $date_field_label ][0] ) && $data[ $date_field_label ][0] != '' ) {
			$default_date           = date( 'd-m-Y', strtotime( str_replace( ',', ' ', $data[ $date_field_label ][0] ) ) );
			$default_h_deliverydate = date( 'j-n-Y', strtotime( str_replace( ',', ' ', $data[ $date_field_label ][0] ) ) );
			if ( get_option( 'orddd_enable_delivery_time' ) == 'on' ) {
				$time_settings_arr   = explode( ' ', $data[ $date_field_label ][0] );
				$time_settings_arr_1 = array_pop( $time_settings_arr );
				$time_settings       = date( 'H:i', strtotime( $time_settings_arr_1 ) );
				$default_date_time   = $time_settings;
			}
		} else {
			$default_date           = '';
			$default_h_deliverydate = '';
		}
		
		$admin_params['orddd_default_date']   = $default_date;
		$admin_params['orddd_default_h_date'] = $default_h_deliverydate;
		$admin_params['default_date_time']    = $default_date_time;
		$admin_params['orddd_fixed_time']     = $fixed_time;

		$order            = new WC_Order( $order_id );
		$items            = $order->get_items();
		$delivery_enabled = self::orddd_is_delivery_enabled( $order );

		$enable_delivery_date_for_category = 'on';
		foreach ( $items as $key => $value ) {
			$product_id                        = $value['product_id'];
			$enable_delivery_date_for_category = orddd_common::orddd_admin_product_has_delivery( $product_id );
			if ( $enable_delivery_date_for_category === 'on' ) {
				break;
			}
		}

		$location = orddd_common::orddd_get_order_location( $order_id );

		$admin_params['orddd_delivery_enabled'] 				 = $delivery_enabled;
		$admin_params['orddd_field_name']						 = 'e_deliverydate';
		$admin_params['orddd_enable_delivery_date_for_category'] = $enable_delivery_date_for_category;
		$admin_params['orddd_field_name_admin'] 	 			 = $date_field_label;
		$admin_params['orddd_time_field_name_admin'] 			 = $time_field_label;
		$admin_params['orddd_location'] 			 			 = $location;

		return $admin_params;
	}

	/**
	 * Save Delivery date and/or Time slot
	 *
	 * @param int   $order_id - Order Id.
	 * @param array $items - Order items to save.
	 *
	 * @hook woocommerce_saved_order_items
	 * @since 3.2
	 */
	public static function orddd_woocommerce_saved_order_items( $order_id, $items ) {
		if ( isset( $items['meta'] ) ) {

			$date_field_label = orddd_custom_delivery_functions::orddd_fetch_delivery_date_field_label( $order_id );
			$time_field_label = orddd_custom_delivery_functions::orddd_fetch_time_slot_field_label( $order_id );

			$meta               = $items['meta'];
			$previous_time_slot = $previous_date = '';
			foreach ( $meta as $key => $value ) {
				if ( $value['key'] == get_option( 'orddd_delivery_date_field_label' ) ) {
					$previous_date = $value['value'];
				}
				if ( $value['key'] == get_option( 'orddd_delivery_timeslot_field_label' ) ) {
					$previous_time_slot = $value['value'];
				}
			}

			if ( isset( $items['e_deliverydate'] ) && $items['e_deliverydate'] != '' && $items['e_deliverydate'] != $previous_date ) {
				update_post_meta( $order_id, $date_field_label, esc_attr( $items['e_deliverydate'] ) );
			}

			if ( isset( $items['orddd_time_slot'] ) && $items['orddd_time_slot'] != '' && $items['orddd_time_slot'] != 'select' && $items['orddd_time_slot'] != 'NA' ) {
				$time_slot       = $items['orddd_time_slot'];
				$order_time_slot = '';
				$time_slot_arr   = explode( ' - ', $time_slot );

				if ( $time_slot_arr[0] == 'asap' ) {
					$from_time = __( 'As Soon As Possible.', 'order-delivery-date' );
				} else {
					$from_time = date( 'H:i', strtotime( $time_slot_arr[0] ) );
				}

				if ( isset( $time_slot_arr[1] ) ) {
					$to_time         = date( 'H:i', strtotime( $time_slot_arr[1] ) );
					$order_time_slot = $from_time . ' - ' . $to_time;
				} else {
					$order_time_slot = $from_time;
				}

				if ( isset( $items['e_deliverydate'] ) && $items['e_deliverydate'] != '' && $items['e_deliverydate'] != $previous_date ) {
					update_post_meta( $order_id, $time_field_label, esc_attr( $time_slot ) );
					update_post_meta( $order_id, '_orddd_time_slot', $order_time_slot );
				} elseif ( $items['orddd_time_slot'] != $previous_time_slot ) {
					update_post_meta( $order_id, $time_field_label, esc_attr( $time_slot ) );
					update_post_meta( $order_id, '_orddd_time_slot', $order_time_slot );
				}
			}
		}
	}

	/**
	 * Save Delivery date and/or Time slot
	 *
	 * @globals resource $wpdb
	 * @globals array $orddd_weekdays
	 * @since 3.2
	 */
	public static function save_delivery_dates() {
		global $wpdb, $orddd_weekdays;

		$delivery_details_updated = 'yes';
		$notes_array              = array();

		$order_id = isset( $_POST['order_id'] ) ? $_POST['order_id'] : '';

		if ( isset( $order_id ) && $order_id > 0 && false !== get_post_status( $order_id ) ) {
			$order        = new WC_Order( $order_id );
			$order_status = $order->get_status();

			$orddd_fees = ( isset( $_POST['orddd_charges'] ) && is_numeric( $_POST['orddd_charges'] ) ) ? $_POST['orddd_charges'] : 0;

			$date_field_label         = orddd_custom_delivery_functions::orddd_fetch_delivery_date_field_label( $order_id );
			$time_field_label         = orddd_custom_delivery_functions::orddd_fetch_time_slot_field_label( $order_id );
			$charges_label            = '';
			$additional_charges_label = '';
			$time_setting             = array();

			$free_coupon_enabled = $add_delivery_charges_for_free_coupon_code = 'no';
			if ( has_filter( 'orddd_add_delivery_charges_for_free_coupon_code' ) ) {
				$add_delivery_charges_for_free_coupon_code = apply_filters( 'orddd_add_delivery_charges_for_free_coupon_code', $add_delivery_charges_for_free_coupon_code );
			}

			if ( 'yes' != $add_delivery_charges_for_free_coupon_code ) {
				$applied_coupons = $order->get_used_coupons();
				foreach ( $applied_coupons as $applied_coupons_key => $applied_coupons_value ) {
					$is_free_coupon = new WC_Coupon( $applied_coupons_value );
					if ( $is_free_coupon->free_shipping == 'yes' ) {
							$free_coupon_enabled = 'yes';
							break;
					}
				}
			}

			$selected_time = '';
			$categories    = array();
			$shipping_classes    = array();
			if ( isset( $_POST['orddd_time_settings_selected'] ) ) {
				$selected_time = date( 'H:i', strtotime( $_POST['orddd_time_settings_selected'] ) );
			}

			if( isset( $_POST['orddd_category_settings_to_load'] ) && $_POST['orddd_category_settings_to_load'] != '' ) {
				$product_category = $_POST[ 'orddd_category_settings_to_load' ];
				$categories = explode( ",", $product_category );
			}
	
			if( isset( $_POST['orddd_shipping_class_settings_to_load'] ) && $_POST['orddd_shipping_class_settings_to_load'] != '' ) {
				$shipping_class = $_POST[ 'orddd_shipping_class_settings_to_load' ];
				$shipping_classes = explode( ",", $shipping_class );
			}

			$date_selected            = 'no';
			$timeslot_selected        = 'no';
			$delivery_details_updated = 'no';
			if ( ( isset( $_POST['e_deliverydate'] ) && $_POST['e_deliverydate'] != '' ) ) {
				$delivery_date = '';
				$time_slot     = '';

				$previous_order_date          = '';
				$previous_order_weekday_check = '';
				$previous_order_h_date        = '';
				$previous_order_timeslot      = '';
				$previous_charges_label       = '';
				$previous_selected_time       = '';
				$previous_order_date_check    = '';
				$data                         = get_post_meta( $order_id );

				if ( isset( $data['_orddd_timestamp'][0] ) && $data['_orddd_timestamp'][0] != '' ) {
					$previous_order_h_date        = date( 'j-n-Y', $data['_orddd_timestamp'][0] );
					$previous_order_date_check    = date( 'n-j-Y', $data['_orddd_timestamp'][0] );
					$previous_order_weekday_check = date( 'w', $data['_orddd_timestamp'][0] );
					$previous_selected_time       = date( 'H:i', $data['_orddd_timestamp'][0] );
				}

				if ( isset( $data[ $date_field_label ][0] ) &&
					$data[ $date_field_label ][0] != '' ) {

					$previous_order_date = $data[ $date_field_label ][0];

					if ( '' == $previous_order_h_date ) {
						$delivery_date_timestamp      = strtotime( str_replace( ',', ' ', $data[ $date_field_label ][0] ) );
						$previous_order_h_date        = date( 'j-n-Y', $delivery_date_timestamp );
						$previous_order_date_check    = date( 'n-j-Y', $delivery_date_timestamp );
						$previous_order_weekday_check = date( 'w', $delivery_date_timestamp );
						$previous_selected_time       = date( 'H:i', $delivery_date_timestamp );
					}
				}

				if ( isset( $data[ $time_field_label ][0] ) && $data[ $time_field_label ][0] != '' ) {
					$previous_order_timeslot = $data[ $time_field_label ][0];
				}
				$custom_delivery_schedule_id = get_post_meta( $order_id, '_orddd_delivery_schedule_id', true );

				if ( ! isset( $custom_delivery_schedule_id ) || '' === $custom_delivery_schedule_id ) {

					$category_settings_applied 		 = orddd_custom_delivery_functions::orddd_get_common_categories( $categories );
					$shipping_class_settings_applied = orddd_custom_delivery_functions::orddd_get_common_categories( $shipping_classes );

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
				}
				orddd_lockout_functions::orddd_maybe_increase_delivery_lockout( $order_id );

				if ( isset( $_POST['e_deliverydate'] ) && $_POST['e_deliverydate'] != '' &&
					$_POST['e_deliverydate'] != $previous_order_date ) {

					$notes_array[] = sprintf( __( '%1$s is updated from %2$s to %3$s', 'order-delivery-date' ), $date_field_label, $previous_order_date, $_POST['e_deliverydate'] );
					update_post_meta( $order_id, $date_field_label, $_POST['e_deliverydate'] );
					$delivery_details_updated = 'yes';
				}

				if ( isset( $_POST['h_deliverydate'] ) && $_POST['h_deliverydate'] != '' ) {
					$delivery_date = $_POST['h_deliverydate'];
					$date_format   = 'dd-mm-y';
					if ( $previous_order_h_date != $_POST['h_deliverydate'] ||
						( $previous_order_h_date == $_POST['h_deliverydate'] &&
						( $selected_time != $previous_selected_time || $selected_time == '' ) ) ) {

						$time_setting                  = array();
						$time_setting['enable']        = 'on';
						$time_setting['time_selected'] = $selected_time;

						$timestamp = orddd_common::orddd_get_timestamp( $delivery_date, $date_format, $time_setting );
						update_post_meta( $order_id, '_orddd_timestamp', $timestamp );
					}
				}
				$date_selected = 'yes';
			}

			if ( isset( $_POST['orddd_time_slot'] ) && $_POST['orddd_time_slot'] != '' && $_POST['orddd_time_slot'] != 'select' && $_POST['orddd_time_slot'] != 'NA' ) {
				$time_slot = $_POST['orddd_time_slot'];
				if ( $previous_order_h_date != $_POST['h_deliverydate'] ) {
					$delivery_details_updated = 'yes';
					update_post_meta( $order_id, $time_field_label, esc_attr( $time_slot ) );
					update_post_meta( $order_id, '_orddd_time_slot', esc_attr( $time_slot ) );
				} elseif ( $time_slot != $previous_order_timeslot ) {
					$delivery_details_updated = 'yes';

					if ( $time_slot == 'asap' ) {
						update_post_meta( $order_id, $time_field_label, esc_attr( __( 'As Soon As Possible.', 'order-delivery-date' ) ) );
						update_post_meta( $order_id, '_orddd_time_slot', esc_attr( __( 'As Soon As Possible.', 'order-delivery-date' ) ) );
						$time_slot = __( 'As Soon As Possible.', 'order-delivery-date' );
					} else {
						update_post_meta( $order_id, $time_field_label, esc_attr( $time_slot ) );
						update_post_meta( $order_id, '_orddd_time_slot', esc_attr( $time_slot ) );
					}
				}
				$notes_array[]     = sprintf( __( '%1$s is updated from %2$s to %3$s', 'order-delivery-date' ), $time_field_label, $previous_order_timeslot, $time_slot );
				$timeslot_selected = 'yes';
			} elseif ( isset( $_POST['orddd_time_slot'] ) &&
					  ( $_POST['orddd_time_slot'] == 'select' ||
						$_POST['orddd_time_slot'] == 'NA' ) ) {
				$timeslot_selected = 'no';
			} else {
				$timeslot_selected = 'yes';
			}

			orddd_lockout_functions::orddd_maybe_reduce_delivery_lockout( $order_id );

			// Update the Delivery Charges
			update_post_meta( $order_id, '_total_delivery_charges', $orddd_fees );

			// Add order notes mentioning the same.
			if ( is_array( $notes_array ) && count( $notes_array ) > 0 ) {
				foreach ( $notes_array as $msg ) {
					$order->add_order_note( __( $msg, 'order-delivery-date' ) );
				}
			}

			if ( 'yes' == $delivery_details_updated && isset( $_POST['orddd_notify_customer'] ) &&
				$_POST['orddd_notify_customer'] == 'yes' ) {
				ORDDD_Email_Manager::orddd_send_email_on_update( $order_id, 'admin' );
			}

			echo $date_selected . ',' . $timeslot_selected . ',' . $delivery_details_updated;

			// Add the Event to the Google Calendar
			if ( 'directly' === get_option( 'orddd_calendar_sync_integration_mode' ) ) {
				if ( $order_status == 'processing' || $order_status == 'completed' ) {
					$gcal          = new OrdddGcal();
					$event_details = orddd_common::orddd_get_event_details( $order_id );
					$gcal->insert_event( $event_details, $order_id, false );

					if ( isset( $_POST['orddd_post_type'] ) && $_POST['orddd_post_type'] == 'shop_subscription' ) {

						if ( class_exists( 'WC_Subscription' ) ) {
							$subscription_order = new WC_Subscription( $order_id );

							if ( isset( $subscription_order->order ) ) {
								$parent_order_id = $subscription_order->order->id;
								$gcal->delete_event( $parent_order_id );
							}
						}
					}
				}
			}
		}
		die();
	}

	/**
	 * Load JS code for Custom Delivery settings
	 *
	 * @globals resource $wpdb
	 * @since 3.2
	 */
	public static function orddd_load_delivery_dates() {
		global $wpdb;
		$product_id = '';
		$field_name = 'e_deliverydate';
		if ( isset( $_POST['order_id'] ) ) {
			$order_id = $_POST['order_id'];
		} else {
			$order_id = '';
		}

		$order            = new WC_Order( $order_id );
		$items            = $order->get_items();
		$delivery_enabled = self::orddd_is_delivery_enabled( $order );
		$results		  = array();

		$enable_delivery_date_for_category = 'on';
		foreach ( $items as $key => $value ) {
			$product_id                        = $value['product_id'];
			$enable_delivery_date_for_category = orddd_common::orddd_admin_product_has_delivery( $product_id );
			if ( $enable_delivery_date_for_category === 'on' ) {
				break;
			}
		}

		if ( $delivery_enabled && $enable_delivery_date_for_category === 'on' && 'on' === get_option( 'orddd_enable_delivery_date' ) ) {
			$results = self::orddd_check_custom_settings( $order_id );

		} else {
			$results['settings_based_on'] = '';
		}

		wp_send_json( $results );
	}

	/**
	 * Reset Settings on delete of order item
	 *
	 * @globals resource $wpdb
	 * @since 3.2
	 */
	public static function orddd_remove_order_item() {
		global $wpdb;
		$product_id = '';

		if ( isset( $_POST['order_id'] ) ) {
			$order_id = $_POST['order_id'];
		} else {
			$order_id = '';
		}

		$order    = new WC_Order( $order_id );
		$items    = $order->get_items();
		$results  = array();

		$delivery_enabled = self::orddd_is_delivery_enabled( $order );
		$enable_delivery_date_for_category = 'on';
		foreach ( $items as $key => $value ) {
			$product_id                        = $value['product_id'];
			$enable_delivery_date_for_category = orddd_common::orddd_admin_product_has_delivery( $product_id );
			if ( $enable_delivery_date_for_category === 'on' ) {
				break;
			}
		}
		$enable_delivery_date = 'yes';
		if ( $delivery_enabled && $enable_delivery_date_for_category === 'on' && 'on' == get_option( 'orddd_enable_delivery_date' ) ) {
			$results = self::orddd_check_custom_settings( $order_id );
		} else {
			$results['settings_based_on'] = '';
		}

		wp_send_json( $results );
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $order_id
	 * @return void
	 */
	public static function orddd_check_custom_settings( $order_id ) {
		$custom_setting_id = orddd_custom_delivery_functions::orddd_get_delivery_schedule_id( $order_id );

		if( '0' == $custom_setting_id ) {
			$result['settings_based_on'] = 'global_settings';
			return $result;
		}
		$shipping_settings = orddd_custom_delivery_functions::orddd_get_delivery_schedule_settings_by_id( $custom_setting_id );
		$order             = new WC_Order( $order_id );
		$items             = $order->get_items();
		$terms			   = array();
		$shipping_class	   = '';
		$result 		   = array();
		$result['settings_based_on'] = 'custom_settings';

		foreach ( $items as $key => $value ) {
			$product_id 		= $value['product_id'];
			$_product       	= wc_get_product( $product_id );
			$shipping_class 	= $_product->get_shipping_class();
			$terms_str  		= get_the_terms( $product_id, 'product_cat' );

			if ( '' !== $terms_str ) {
				foreach ( $terms_str as $term => $val ) {
					$id = orddd_common::get_base_product_category( $val->term_id );
					array_push( $terms, $id );
				}
			}
		}

		if ( isset( $shipping_settings['delivery_settings_based_on'][0] ) && $shipping_settings['delivery_settings_based_on'][0] == 'product_categories' ) {
			if ( isset( $shipping_settings['product_categories'] ) ) {
				$product_categories = $shipping_settings['product_categories'];

				foreach ( $terms as $term => $val ) {
					if ( in_array( $val->slug, $product_categories ) ) {
						$category_to_send = $val->slug;
						if ( isset( $shipping_settings['enable_shipping_based_delivery'] ) ) {
							// echo '<script type="text/javascript">
							// 	jQuery( "#orddd_category_settings_to_load" ).val("' . $category_to_send . '");
							// 	</script>';
							$result['settings_based_on']			   = 'product_categories';
							$result['orddd_category_settings_to_load'] = $category_to_send;
							break;
						}
					}
				}
			}
		} else if ( isset( $shipping_settings['delivery_settings_based_on'][0] ) && $shipping_settings['delivery_settings_based_on'][0] == 'shipping_methods' ) {
			if ( isset( $shipping_settings['shipping_methods'] ) ) {
				$shipping_methods = $shipping_settings['shipping_methods'];
				if ( in_array( $shipping_class, $shipping_methods, true ) ) {
					$shipping_class_to_send = $shipping_class;
					if ( isset( $shipping_settings['enable_shipping_based_delivery'] ) ) {
						// echo '<script type="text/javascript">
						// 	jQuery( "#orddd_shipping_class_settings_to_load" ).val("' . $shipping_class_to_send . '");
						// </script>';
						$result['settings_based_on']			   		 = 'shipping_class';
						$result['orddd_shipping_class_settings_to_load'] = $shipping_class_to_send;
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Hide the order item meta from the Edit order page
	 * 
	 * @param array $meta_keys Hidden meta keys.
	 * @return array
	 */
	public static function orddd_hidden_order_itemmeta( $meta_keys ) {
		$meta_keys[] = '_orddd_product_lockout_reduced';
		return $meta_keys;
	}
}

$orddd_admin_delivery_class = new orddd_admin_delivery_class();

<?php
/**
 * Order Delivery Date Lockout functions that add post meta & remove post meta to identify whether lockout for an order is reduced or not. They also call the functions in other files that update the quantity of orders placed.
 *
 * @package order-delivery-date
 * @since 9.19.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Increase or decrease availability for deliveries
 */
class orddd_lockout_functions {

	/**
	 * When a payment is complete, we can reduce delivery date availability for order & items within an order.
	 *
	 * @since 9.19
	 * @param int $order_id Order ID.
	 */
	public static function orddd_maybe_reduce_delivery_lockout( $order_id ) {

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		$lockout_reduced = self::get_lockout_reduced( $order_id );
		$trigger_reduce  = apply_filters( 'orddd_payment_complete_reduce_lockout', ! $lockout_reduced, $order_id );

		// Only continue if we're reducing availability.
		if ( ! $trigger_reduce ) {
			return;
		}

		self::orddd_reduce_delivery_lockout( $order_id );

		// Ensure availability is marked as "reduced" in case payment complete or other lockout actions are called.
		self::set_lockout_reduced( $order_id, true );
	}

	/**
	 * Reduce lockout levels for order & items within an order, if lockout has not already been reduced.
	 *
	 * @since 9.19.0
	 * @param int|WC_Order $order_id Order ID or order instance.
	 */
	public static function orddd_reduce_delivery_lockout( $order_id ) {
		if ( is_a( $order_id, 'WC_Order' ) ) {
			$order    = $order_id;
			$order_id = $order->get_id();
		} else {
			$order = wc_get_order( $order_id );
		}

		// We need an order, and a store with lockout enabled, to continue.
		if ( ! $order || ! apply_filters( 'orddd_can_reduce_delivery_lockout', true, $order ) ) {
			return;
		}

		// TODO: need to reduce item-wise lockout, add condition here for quantity based lockout or order based.
		$timestamp     = get_post_meta( $order_id, '_orddd_timestamp', true );
		
		// If the date is not mandatory then timestamp can be blank.
		if( '' === $timestamp ) {
			return;
		}

		$delivery_date = date( 'j-n-Y', $timestamp );

		$time_slot = get_post_meta( $order_id, '_orddd_time_slot', true );

		orddd_process::orddd_update_lockout_days( $delivery_date, '', $order_id );
		orddd_process::orddd_update_time_slot( $time_slot, $delivery_date, $order_id );

		// TODO: Need to put the code of the lockout updation here. Currently the code simply adds item meta.
		// We are having to do this here as current code in live copy in orddd_common::orddd_get_total_product_quantities() simply calculates the total quantity before performing the actual update in database.
		if ( 'on' === get_option( 'orddd_lockout_date_quantity_based' ) ) {
			foreach ( $order->get_items() as $item ) {
				if ( ! $item->is_type( 'line_item' ) ) {
					continue;
				}

				// Only reduce stock once for each item.
				$product            = $item->get_product();
				$item_stock_reduced = $item->get_meta( '_orddd_product_lockout_reduced', true );

				if ( $item_stock_reduced || ! $product ) {
					continue;
				}
				$item->add_meta_data( '_orddd_product_lockout_reduced', 'yes', true );
				$item->save();
			}
		}

		self::orddd_add_order_note( $order, 'reduce' );
		// NEED FEEDBACK: We have an action 'orddd_after_delivery_date_update'. Should we add another action like the one below?
		do_action( 'orddd_reduce_delivery_lockout', $order );
	}

	/**
	 * When a payment is cancelled, restore lockout.
	 *
	 * @since 9.19.0
	 * @param int $order_id Order ID.
	 */
	public static function orddd_maybe_increase_delivery_lockout( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		$lockout_reduced  = self::get_lockout_reduced( $order_id );
		$trigger_increase = (bool) $lockout_reduced;

		// Only continue if we're increasing lockout.
		if ( ! $trigger_increase ) {
			return;
		}

		self::orddd_increase_delivery_lockout( $order );

		// Ensure lockout is not marked as "reduced" anymore.
		self::set_lockout_reduced( $order_id, false );
	}

	/**
	 * Increase lockout levels for the order & also items within an order.
	 *
	 * @since 9.19.0
	 * @param int|WC_Order $order_id Order ID.
	 */
	public static function orddd_increase_delivery_lockout( $order_id ) {
		if ( is_a( $order_id, 'WC_Order' ) ) {
			$order    = $order_id;
			$order_id = $order->get_id();
		} else {
			$order = wc_get_order( $order_id );
		}

		// We need an order, and a store with delivery management to continue.
		// Todo: Need to add this condition in below if: 'yes' !== get_option( 'woocommerce_manage_stock' )
		// We can give an option for stores that don't have any lockout, thus we can skip all lockout checks.
		if ( ! $order || ! apply_filters( 'orddd_can_restore_delivery_lockout', true, $order ) ) {
			return;
		}

		// TODO: need to reduce item-wise lockout & add item-wise meta, add condition here for quantity based lockout or order based.
		orddd_common::orddd_cancel_delivery( $order_id );

		// TODO: Need to loop over all cart items here.
		// We are having to do this here as current code in live copy in orddd_common::orddd_get_total_product_quantities() simply calculates the total quantity before performing the actual update in database.
		if ( 'on' === get_option( 'orddd_lockout_date_quantity_based' ) ) {
			foreach ( $order->get_items() as $item ) {
				if ( ! $item->is_type( 'line_item' ) ) {
					continue;
				}

				// Only reduce stock once for each item.
				$product            = $item->get_product();
				$item_stock_reduced = $item->get_meta( '_orddd_product_lockout_reduced', true );

				if ( ! $item_stock_reduced || ! $product ) {
					continue;
				}
				$item->update_meta_data( '_orddd_product_lockout_reduced', 'no' );
				$item->save();
			}
		}

		self::orddd_add_order_note( $order, 'increase' );

		do_action( 'orddd_restore_order_lockout', $order );
	}

	/**
	 * Gets information about whether delivery date availability was reduced.
	 *
	 * @param int $order_id Order ID.
	 * @return bool
	 */
	public static function get_lockout_reduced( $order_id ) {
		return wc_string_to_bool( get_post_meta( $order_id, '_orddd_lockout_reduced', true ) );
	}

	/**
	 * Stores information about whether delivery date availability was reduced.
	 *
	 * @param int  $order_id Order ID.
	 * @param bool $set      True or false.
	 */
	public static function set_lockout_reduced( $order_id, $set ) {
		update_post_meta( $order_id, '_orddd_lockout_reduced', wc_bool_to_string( $set ) );
	}

	/**
	 * Gets information about whether delivery date availability was reduced for the product.
	 *
	 * @param int $order_id Order ID.
	 * @return bool
	 */
	public static function get_item_lockout_reduced( $order_id ) {
		return wc_string_to_bool( get_post_meta( $order_id, '_is_orddd_lockout_reduced', true ) );
	}


	/**
	 * Sets information about whether delivery date availability was reduced for the product.
	 *
	 * @param int  $order_id Order ID.
	 * @param bool $set To set or not.
	 * @return void
	 */
	public static function set_item_lockout_reduced( $order_id, $set ) {
		update_post_meta( $order_id, '_is_orddd_lockout_reduced', wc_bool_to_string( $set ) );
	}

	/**
	 * Set the order note when the lockout is reduced or increased.
	 *
	 * @param WC_Order $order order object.
	 * @param string   $status whether to reduce or increase the lockout.
	 * @return void
	 */
	public static function orddd_add_order_note( $order, $status ) {
		$order_id 		  	= $order->get_id();
		$custom_schedule_id = orddd_custom_delivery_functions::orddd_get_delivery_schedule_id( $order_id );

		if ( isset( $custom_schedule_id ) && 0 != $custom_schedule_id ) {
			$option_name 				= 'orddd_shipping_based_settings_' . $custom_schedule_id;
			$shipping_settings_to_check = get_option( $option_name );
			$lockout_enabled 		    = isset( $shipping_settings_to_check['date_lockout'] ) ? $shipping_settings_to_check['date_lockout'] : 0;
		} else {
			$lockout_enabled = get_option( 'orddd_lockout_date_after_orders' );
		}

		if ( $lockout_enabled > 0 ) {
			$delivery_date_formatted = orddd_common::orddd_get_order_delivery_date( $order_id );
			if ( 'reduce' === $status ) {
				/* translators: %s: Delivery Date */
				$order->add_order_note( sprintf( __( 'Delivery lockout reduced for %s', 'order-delivery-date' ), $delivery_date_formatted ) );
			} elseif ( 'increase' === $status ) {
				/* translators: %s: Delivery Date */
				$order->add_order_note( sprintf( __( 'Delivery lockout increased for %s', 'order-delivery-date' ), $delivery_date_formatted ) );
			}
		}
	}

}

$orddd_lockout_functions = new orddd_lockout_functions();

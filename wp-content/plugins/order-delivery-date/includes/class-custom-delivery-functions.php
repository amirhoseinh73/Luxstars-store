<?php
/**
 * Order Delivery Date Custom delivery schedule functions. Add custom delivery schedule id to post meta & also do the migration for old orders to have post meta.
 *
 * @package order-delivery-date
 * @since 9.19.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Fetch or Set custom delivery schedule settings
 */
class orddd_custom_delivery_functions {

	/**
	 * Add the new post meta to existing orders.
	 */
	public function __construct() {
		// Executing the migration code only if the customer is in our plugin's dashboard on any page, except for the Custom shipping settings page as that one fetches the inactive shipping methods too, which results in giving back the inactive custom delivery schedule id for some orders.
		add_action( 'orddd_migrate_orders_post_meta', array( 'orddd_custom_delivery_functions', 'orddd_migrate_orders_to_add_post_meta' ), 11 );
		add_action( 'orddd_migrate_orders_gcal_post_meta', array( 'orddd_custom_delivery_functions', 'orddd_migrate_orders_to_add_gcal_post_meta' ), 11 );
	}

	/**
	 * Get the label of custom delivery schedule for an order.
	 *
	 * @since 9.19.0
	 * @param int $order_id Order ID.
	 */
	public static function orddd_fetch_delivery_date_field_label( $order_id ) {

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		$delivery_date_field_label = '' !== get_option( 'orddd_delivery_date_field_label' ) ? get_option( 'orddd_delivery_date_field_label' ) : ORDDD_DELIVERY_DATE_FIELD_LABEL;

		$custom_delivery_schedule_id = self::orddd_get_delivery_schedule_id( $order_id );
		$custom_settings             = array();

		if ( 0 != $custom_delivery_schedule_id ) {
			$custom_settings = get_option( 'orddd_shipping_based_settings_' . $custom_delivery_schedule_id );
		}

		if ( get_option( 'orddd_enable_shipping_based_delivery' ) === 'on' && is_array( $custom_settings ) && count( $custom_settings ) > 0 ) {
			if ( isset( $custom_settings['orddd_shipping_based_delivery_date_field_label'] ) && '' !== $custom_settings['orddd_shipping_based_delivery_date_field_label'] ) {
				$delivery_date_field_label = $custom_settings['orddd_shipping_based_delivery_date_field_label'];
			}
		}

		return $delivery_date_field_label;
	}

	/**
	 * Get the time slot label of custom delivery schedule for an order.
	 *
	 * @since 9.19.0
	 * @param int $order_id Order ID.
	 */
	public static function orddd_fetch_time_slot_field_label( $order_id ) {

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		$delivery_time_field_label = '' !== get_option( 'orddd_delivery_timeslot_field_label' ) ? get_option( 'orddd_delivery_timeslot_field_label' ) : ORDDD_DELIVERY_TIMESLOT_FIELD_LABEL;

		$custom_delivery_schedule_id = self::orddd_get_delivery_schedule_id( $order_id );
		$custom_settings             = array();

		if ( 0 != $custom_delivery_schedule_id ) {
			$custom_settings = get_option( 'orddd_shipping_based_settings_' . $custom_delivery_schedule_id );
		}

		if ( get_option( 'orddd_enable_shipping_based_delivery' ) === 'on' && is_array( $custom_settings ) && count( $custom_settings ) > 0 ) {
			if ( isset( $custom_settings['orddd_shipping_based_delivery_timeslot_field_label'] ) && '' !== $custom_settings['orddd_shipping_based_delivery_timeslot_field_label'] ) {
				$delivery_time_field_label = $custom_settings['orddd_shipping_based_delivery_timeslot_field_label'];
			}
		}

		return $delivery_time_field_label;
	}

	/**
	 * Update custom delivery schedule id for an order
	 *
	 * @param int    $order_id Order ID.
	 * @param string $delivery_schedule_hidden_var Hidden variable that contains custom delivery schedule information.
	 * @since 9.19.0
	 */
	public static function orddd_update_delivery_schedule_id( $order_id, $delivery_schedule_hidden_var ) {

		$delivery_schedule_id = self::orddd_pre_process_delivery_schedule_id( $order_id, $delivery_schedule_hidden_var );
		update_post_meta( $order_id, '_orddd_delivery_schedule_id', $delivery_schedule_id );
	}

	/**
	 * Do any pre-processing on the delivery schedule id before inserting in database
	 *
	 * @param int    $order_id Order ID.
	 * @param string $delivery_schedule_hidden_var Hidden variable that contains custom delivery schedule information.
	 *
	 * @return int ID of custom delivery schedule.
	 * @since 9.19.0
	 */
	public static function orddd_pre_process_delivery_schedule_id( $order_id, $delivery_schedule_hidden_var ) {
		if ( 'global_settings' === $delivery_schedule_hidden_var ) {
			$delivery_schedule_id = 0;
		} else {
			$delivery_schedule_parts = explode( 'custom_settings_', $delivery_schedule_hidden_var );
			$delivery_schedule_id    = $delivery_schedule_parts[1];
		}
		return apply_filters( 'orddd_pre_process_delivery_schedule_id', $delivery_schedule_id );
	}

	/**
	 * Get delivery schedule id for an order
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return int ID of custom delivery schedule.
	 * @since 9.19.0
	 */
	public static function orddd_get_delivery_schedule_id( $order_id ) {
		return get_post_meta( $order_id, '_orddd_delivery_schedule_id', true );
	}

	/**
	 * Return custom setting by id
	 *
	 * @globals resource $wpdb WordPress object
	 *
	 * @param string $delivery_schedule_id ID of custom shipping method.
	 * @return array Settings of custom delivery schedule.
	 * @since 9.19.0
	 */
	public static function orddd_get_delivery_schedule_settings_by_id( $delivery_schedule_id ) {

		$delivery_schedule_settings = get_option( 'orddd_shipping_based_settings_' . $delivery_schedule_id );

		return $delivery_schedule_settings;
	}

	/**
	 * Migrates all orders with future delivery dates to add following postmeta:
	 * _orddd_lockout_reduced - yes|no - 'no' for orders that are cancelled/failed/trashed/refunded, 'yes' for rest all orders
	 * _orddd_delivery_schedule_id - numeric id of the custom delivery schedule, 0 for global settings
	 *
	 * @since 9.19.0
	 */
	public static function orddd_migrate_orders_to_add_post_meta() {
		$total_orders_to_export = array();

		$custom_delivery_status = array( 'wc-pending', 'wc-cancelled', 'wc-failed', 'wc-refunded', 'trash' );
		$results                = self::orddd_get_all_future_orders_to_add_post_meta( $custom_delivery_status, '_orddd_delivery_schedule_id' );

		foreach ( $results as $key => $value ) {
			$order_id = $value['ID'];

			$location         = orddd_common::orddd_get_order_location( $order_id );
			$shipping_method  = orddd_common::orddd_get_order_shipping_method( $order_id );
			$categories       = orddd_common::orddd_get_cart_product_categories( $order_id );
			$shipping_classes = orddd_common::orddd_get_cart_shipping_classes( $order_id );

			$custom_delivery_schedule_id = self::orddd_get_custom_delivery_schedule_id( $location, $shipping_method, $categories, $shipping_classes );
			update_post_meta( $order_id, '_orddd_delivery_schedule_id', $custom_delivery_schedule_id );
		}

		// To update _orddd_lockout_reduced meta. These statuses will be excluded in the SQL query to fetch future orders.
		$lockout_reduced_yes = array( 'wc-pending', 'wc-cancelled', 'wc-failed', 'wc-refunded', 'trash' );
		$results             = self::orddd_get_all_future_orders_to_add_post_meta( $lockout_reduced_yes, '_orddd_lockout_reduced' );

		foreach ( $results as $key => $value ) {
			$order_id = $value['ID'];
			update_post_meta( $order_id, '_orddd_lockout_reduced', wc_bool_to_string( true ) );
		}

		$lockout_reduced_no = array( 'wc-on-hold', 'wc-processing', 'wc-completed' );
		$results            = self::orddd_get_all_future_orders_to_add_post_meta( $lockout_reduced_no, '_orddd_lockout_reduced' );

		foreach ( $results as $key => $value ) {
			$order_id = $value['ID'];
			update_post_meta( $order_id, '_orddd_lockout_reduced', wc_bool_to_string( false ) );
		}

	}

	/**
	 * Migrates all orders with google calendar event uid to add following postmeta:
	 * _orddd_gcal_event_id - numeric id of the google calendar event
	 *
	 * @since 9.19.0
	 */
	public static function orddd_migrate_orders_to_add_gcal_post_meta() {

		$results = self::orddd_get_all_future_orders_to_add_gcal_post_meta();

		$event_orders = get_option( 'orddd_event_order_ids' );
		if ( '' == $event_orders || '{}' == $event_orders || '[]' == $event_orders || 'null' == $event_orders ) {
			$event_orders = array();
		}

		$event_uids = get_option( 'orddd_event_uids_ids' );
		if ( '' == $event_uids || '{}' == $event_uids || '[]' == $event_uids || 'null' == $event_uids ) {
			$event_uids = array();
		}

		$is_present = array();
		foreach ( $results as $key => $value ) {
			$order_id = $value['ID'];

			if ( in_array( $order_id, $event_orders ) && isset( $event_uids[ $order_id ] ) ) {
				$event_uid    = $event_uids[ $order_id ];
				$is_present[] = $order_id;
				update_post_meta( $order_id, '_orddd_gcal_event_id', $event_uid );
			}
		}

		if ( is_array( $is_present ) && count( $is_present ) == 0 ) {
			update_option( 'orddd_migrate_post_meta_orddd_gcal_event_id', 'yes' );
		}
	}

	/**
	 * Based on the order id, this function will return the id of the custom delivery schedule.
	 *
	 * @param int $order_id Order ID.
	 * @return int $custom_delivery_schedule_id Custom delivery schedule id as is present in wp_options.
	 * @since 9.19.0
	 */
	public static function orddd_get_custom_delivery_schedule_id( $location, $shipping_method, $categories, $shipping_classes ) {
		$shipping_based_lockout          = 'No';
		$shipping_based_timeslot_lockout = 'No';
		$shipping_settings_to_check      = array();
		$custom_delivery_schedule_id     = ''; // setting default to blank, instead of 0.

		$results = orddd_common::orddd_get_shipping_settings();

		$shipping_settings = array();

		if ( get_option( 'orddd_enable_shipping_based_delivery' ) == 'on' && is_array( $results ) && count( $results ) > 0 ) {
			foreach ( $results as $key => $value ) {
				$shipping_methods  = array();
				$shipping_settings = get_option( $value->option_name );
				if ( isset( $shipping_settings['delivery_settings_based_on'][0] ) && 'orddd_locations' === $shipping_settings['delivery_settings_based_on'][0] ) {

					if ( isset( $shipping_settings['orddd_locations'] ) && in_array( $location, $shipping_settings['orddd_locations'] ) ) {
						if ( isset( $shipping_settings['categories_for_pickup_locations'] ) ) {
							foreach ( $categories as $pkey => $pvalue ) {
								if ( in_array( $pvalue, $shipping_settings['categories_for_pickup_locations'], true ) ) {
									$shipping_based_lockout                            = 'Yes';
									$shipping_settings_to_check[ $value->option_name ] = $shipping_settings;
									break;
								}
							}
						} elseif ( ! isset( $shipping_settings['categories_for_pickup_locations'] ) ) {
							$shipping_based_lockout                            = 'Yes';
							$shipping_settings_to_check[ $value->option_name ] = $shipping_settings;
							break;
						}
					}
				}
			}

			if ( 'No' === $shipping_based_lockout ) {
				foreach ( $results as $key => $value ) {
					$shipping_methods  = array();
					$shipping_settings = get_option( $value->option_name );
					if ( isset( $shipping_settings['delivery_settings_based_on'][0] ) && 'shipping_methods' == $shipping_settings['delivery_settings_based_on'][0] ) {
						if ( has_filter( 'orddd_get_shipping_method' ) ) {
							$shipping_methods_values               = apply_filters( 'orddd_get_shipping_method', $shipping_settings, $_POST, $shipping_settings['shipping_methods'], $shipping_method );
							$shipping_settings['shipping_methods'] = $shipping_methods_values['shipping_methods'];
							$shipping_method                       = $shipping_methods_values['shipping_method'];
						}

						if ( in_array( $shipping_method, $shipping_settings['shipping_methods'] ) ) {
							$shipping_based_lockout                            = 'Yes';
							$shipping_settings_to_check[ $value->option_name ] = $shipping_settings;
							break;
						}
					}
				}
			}

			if ( 'No' === $shipping_based_lockout ) {
				foreach ( $categories as $pkey => $pvalue ) {
					foreach ( $results as $key => $value ) {
						$shipping_methods  = array();
						$shipping_settings = get_option( $value->option_name );
						if ( isset( $shipping_settings['delivery_settings_based_on'][0] ) && 'product_categories' == $shipping_settings['delivery_settings_based_on'][0] ) {
							if ( in_array( $pvalue, $shipping_settings['product_categories'] ) ) {
								if ( isset( $shipping_settings['shipping_methods_for_categories'] ) && ( in_array( $shipping_method, $shipping_settings['shipping_methods_for_categories'] ) ) ) {
									$shipping_based_lockout                            = 'Yes';
									$shipping_settings_to_check[ $value->option_name ] = $shipping_settings;
									break;
								}elseif ( isset( $shipping_settings['shipping_methods_for_categories'] )  ) {
									foreach( $shipping_classes as $skey => $svalue ) {
										if( in_array( $svalue, $shipping_settings[ 'shipping_methods_for_categories' ] ) ) {
											$shipping_settings_exists = 'Yes';
											$shipping_settings[ 'is_combination_enabled' ] = 'yes';
											$custom_settings[] = $shipping_settings;
										}
									}
								}
							}
						}
					}
				}
			}

			if ( 'No' === $shipping_based_lockout ) {
				foreach ( $categories as $pkey => $pvalue ) {
					foreach ( $results as $key => $value ) {
						$shipping_settings = get_option( $value->option_name );
						if ( isset( $shipping_settings['delivery_settings_based_on'][0] ) && 'product_categories' == $shipping_settings['delivery_settings_based_on'][0] ) {
							if ( in_array( $pvalue, $shipping_settings['product_categories'] ) ) {
								if ( ! isset( $shipping_settings['shipping_methods_for_categories'] ) ) {
									$shipping_based_lockout                            = 'Yes';
									$shipping_settings_to_check[ $value->option_name ] = $shipping_settings;
									break;
								}
							}
						}
					}
				}
			}

			if ( 'No' === $shipping_based_lockout ) {
				foreach ( $shipping_classes as $skey => $svalue ) {
					foreach ( $results as $key => $value ) {
						$shipping_settings = get_option( $value->option_name );
						if ( isset( $shipping_settings['delivery_settings_based_on'][0] ) && 'shipping_methods' == $shipping_settings['delivery_settings_based_on'][0] ) {
							if ( in_array( $svalue, $shipping_settings['shipping_methods'] ) ) {
								$shipping_based_lockout                            = 'Yes';
								$shipping_settings_to_check[ $value->option_name ] = $shipping_settings;
								break;
							}
						}
					}
				}
			}

			// Fetch the custom delivery schedule id here in $custom_delivery_schedule_id variable.
			if ( count( $shipping_settings_to_check ) === 0 ) {
				$custom_delivery_schedule_id = 0;
			} else {
				$shipping_setting_key        = $value->option_name;
				$custom_setting_id_arr       = explode( 'orddd_shipping_based_settings_', $shipping_setting_key );
				$custom_delivery_schedule_id = $custom_setting_id_arr[1];
			}
		} else {
			// This means custom settings are disabled.
			$custom_delivery_schedule_id = 0;
			$orders[ $order_id ]         = $custom_delivery_schedule_id;
		}
		return $custom_delivery_schedule_id;
	}

	/**
	 * Return all orders with future deliveries.
	 * There is a function present in orddd_common::orddd_get_all_future_orders(). But we are creating another one here so we can only fetch those orders where the post meta is not already updated. This will be useful when this function is to be run multiple times. So it will ignore those orders that are already updated.
	 *
	 * @param array  $exclude_statuses Exclude the statuses when the fetching future orders.
	 * @param string $meta_key Meta key to check.
	 * @return array All order with future deliveries.
	 * @since 9.19.0
	 */
	public static function orddd_get_all_future_orders_to_add_post_meta( $exclude_statuses, $meta_key ) {
		global $wpdb;

		$gmt = false;
		if ( has_filter( 'orddd_gmt_calculations' ) ) {
			$gmt = apply_filters( 'orddd_gmt_calculations', '' );
		}
		$current_time = current_time( 'timestamp', $gmt );

		$results = orddd_common::orddd_get_all_future_orders( $exclude_statuses );

		$orders_to_update = array();
		$count            = 0;
		foreach ( $results as $key => $value ) {
			if ( $count > 50 ) {
				break;
			}
			$order_id                    = $value->ID;
			$post_status                 = $value->post_status;
			$custom_delivery_schedule_id = get_post_meta( $order_id, $meta_key, true );

			if ( ! isset( $custom_delivery_schedule_id ) || '' === $custom_delivery_schedule_id ) {
				$orders_to_update[] = array(
					'ID'          => $order_id,
					'post_status' => $post_status,
				);
			}
			$count++;
		}

		if ( is_array( $orders_to_update ) && count( $orders_to_update ) === 0 ) {
			update_option( 'orddd_migrate_post_meta' . $meta_key, 'yes' );
		}
		return $orders_to_update;
	}

	/**
	 * Return all orders with future delivery dates to add google calendar post meta: _orddd_gcal_event_id
	 * There is a function present in orddd_common::orddd_get_all_future_orders(). But we are creating another one here so we can only fetch those orders where the post meta is not already updated. This will be useful when this function is to be run multiple times. So it will ignore those orders that are already updated.
	 * This will fetch orders that are: processing, completed, on-hold
	 *
	 * @return array All order with future deliveries.
	 * @since 9.19.0
	 */
	public static function orddd_get_all_future_orders_to_add_gcal_post_meta() {
		global $wpdb;

		$gmt = false;
		if ( has_filter( 'orddd_gmt_calculations' ) ) {
			$gmt = apply_filters( 'orddd_gmt_calculations', '' );
		}
		$current_time = current_time( 'timestamp', $gmt );

		$results = orddd_common::orddd_get_all_future_orders( array( 'wc-pending', 'wc-cancelled', 'wc-failed', 'wc-refunded', 'trash' ) );

		$orders_to_update = array();
		$count            = 0;

		foreach ( $results as $key => $value ) {
			if ( $count > 50 ) {
				break;
			}
			$order_id      = $value->ID;
			$post_status   = $value->post_status;
			$gcal_event_id = get_post_meta( $order_id, '_orddd_gcal_event_id', true );
			if ( ! isset( $gcal_event_id ) || '' === $gcal_event_id ) {
				$orders_to_update[] = array(
					'ID'          => $order_id,
					'post_status' => $post_status,
				);
			}
			$count++;
		}

		if ( is_array( $orders_to_update ) && count( $orders_to_update ) == 0 ) {
			update_option( 'orddd_migrate_post_meta_orddd_gcal_event_id', 'yes' );
		}

		return $orders_to_update;
	}

	/**
	 * Get all the category settings applied on checkout.
	 *
	 * @param array $categories Product Categories added in the cart.
	 * @return array
	 */
	public static function orddd_get_common_categories( $categories, $shipping_method = '' ) {
		$settings = array();

		if ( 'on' === get_option( 'orddd_enable_shipping_based_delivery' ) ) {
			$shipping_settings = orddd_common::orddd_get_shipping_settings();
			if ( is_array( $shipping_settings ) && count( $shipping_settings ) > 0 ) {
				foreach ( $shipping_settings as $key => $value ) {
					$shipping_settings_to_check = get_option( $value->option_name );
					$custom_setting_id_arr      = explode( 'orddd_shipping_based_settings_', $value->option_name );
					$custom_setting_id          = $custom_setting_id_arr[1];

					if ( isset( $shipping_settings_to_check['enable_shipping_based_delivery'] ) && isset( $shipping_settings_to_check['delivery_settings_based_on'][0] ) && isset( $shipping_settings_to_check['product_categories'] ) && 'product_categories' === $shipping_settings_to_check['delivery_settings_based_on'][0] ) {
						if ( isset( $shipping_settings_to_check['shipping_methods_for_categories'] ) ) {
							$shipping_methods = $shipping_settings_to_check['shipping_methods_for_categories'];
							$shipping_method_for_category = true;
							foreach ( $categories as $category ) {
								if ( in_array( $category, $shipping_settings_to_check['product_categories'], true ) && in_array( $shipping_method, $shipping_methods ) ) {
									$settings[ $custom_setting_id ] = $category;
									break;
								}
							}
						} else {
							foreach ( $categories as $category ) {
								if ( in_array( $category, $shipping_settings_to_check['product_categories'], true ) ) {
									$settings[ $custom_setting_id ] = $category;
									break;
								}
							}
						}
					}
				}
			}
		}

		return $settings;
	}

	/**
	 * Get all the category settings applied on checkout.
	 *
	 * @param array $shipping_classes Shipping classes on checkout.
	 * @return array
	 */
	public static function orddd_get_common_shipping_classes( $shipping_classes ) {
		$settings = array();

		if ( 'on' === get_option( 'orddd_enable_shipping_based_delivery' ) ) {
			$shipping_settings = orddd_common::orddd_get_shipping_settings();
			if ( is_array( $shipping_settings ) && count( $shipping_settings ) > 0 ) {
				foreach ( $shipping_settings as $key => $value ) {
					$shipping_settings_to_check = get_option( $value->option_name );
					$custom_setting_id_arr      = explode( 'orddd_shipping_based_settings_', $value->option_name );
					$custom_setting_id          = $custom_setting_id_arr[1];

					if ( isset( $shipping_settings_to_check['enable_shipping_based_delivery'] ) && isset( $shipping_settings_to_check['delivery_settings_based_on'][0] ) && isset( $shipping_settings_to_check['shipping_methods'] ) && 'shipping_methods' === $shipping_settings_to_check['delivery_settings_based_on'][0] ) {
						foreach ( $shipping_classes as $shipping_class ) {
							if ( in_array( $shipping_class, $shipping_settings_to_check['shipping_methods'], true ) ) {
								$settings[ $custom_setting_id ] = $shipping_class;
								break;
							}
						}
					}
				}
			}
		}

		return $settings;
	}
}

$orddd_custom_delivery_functions = new orddd_custom_delivery_functions();

<?php
/**
 * Common functions for same day/next day & minimum deliver time.
 *
 * @package order-delivery-date/cutoff-functions
 */

/**
 * Get the current timestamp
 */
function orddd_get_current_time() {
	$gmt = false;
	if ( has_filter( 'orddd_gmt_calculations' ) ) {
		$gmt = apply_filters( 'orddd_gmt_calculations', '' );
	}
	$current_time = current_time( 'timestamp', $gmt );

	return $current_time;
}

/**
 * Get the Business opening time.
 *
 * @param Date $date Date for which the opening time is returned.
 * @return string
 */
function orddd_get_business_opening_time( $date ) {
	global $orddd_days;
	$business_days_enabled = get_option( 'orddd_enable_shipping_days' );
	$business_opening_time = false !== get_option( 'orddd_business_opening_time' ) ? get_option( 'orddd_business_opening_time' ): '';
	$opening_time          = strtotime( $date . ' 12:01 AM' );
	$current_weekday       = date( 'w', $opening_time );
	$weekday_opening_time  = apply_filters(
		'orddd_modify_weekday_opening_time',
		array(
			'weekday'      => array(),
			'opening_time' => '',
		)
	);

	if ( 'on' === $business_days_enabled && '' !== $business_opening_time ) {
		$opening_time = strtotime( $date . ' ' . $business_opening_time );
		if ( ! empty( $weekday_opening_time['weekday'] ) ) {
			foreach ( $weekday_opening_time['weekday'] as $key => $value ) {
				$weekday = array_search( $value, $orddd_days, true );
				if ( $weekday == $current_weekday ) {
					$opening_time = strtotime( $date . ' ' . $weekday_opening_time['opening_time'] );
					break;
				}
			}
		}
	}

	return $opening_time;
}

/**
 * Get the Business closing time.
 *
 * @param Date $date Date for which the closing time is returned.
 * @return string
 */
function orddd_get_business_closing_time( $date ) {
	global $orddd_days;
	$business_days_enabled = get_option( 'orddd_enable_shipping_days' );
	$business_closing_time = false !== get_option( 'orddd_business_closing_time' ) ? get_option( 'orddd_business_closing_time' ): '';
	$closing_time          = strtotime( $date . ' 11:59 PM' );
	$current_weekday       = date( 'w', $closing_time );

	$weekday_closing_time = apply_filters(
		'orddd_modify_weekday_closing_time',
		array(
			'weekday'      => array(),
			'closing_time' => '',
		)
	);

	if ( 'on' === $business_days_enabled && '' !== $business_closing_time ) {
		$closing_time = strtotime( $date . ' ' . $business_closing_time );
		if ( ! empty( $weekday_closing_time['weekday'] ) ) {
			foreach ( $weekday_closing_time['weekday'] as $key => $value ) {
				$weekday = array_search( $value, $orddd_days, true );
				if ( $weekday == $current_weekday ) {
					$closing_time = strtotime( $date . ' ' . $weekday_closing_time['closing_time'] );
					break;
				}
			}
		}
	}
	return $closing_time;
}

/**
 * Get the same day/next day cutoff
 *
 * @param string $cutoff_day Same day/Next day.
 */
function orddd_get_cutoff_timestamp( $cutoff_day = 'same_day' ) {
	$current_time  = orddd_get_current_time();
	$current_day   = gmdate( 'd', $current_time );
	$current_month = gmdate( 'm', $current_time );
	$current_year  = gmdate( 'Y', $current_time );

	$cutoff_hours = '' !== get_option( 'orddd_disable_same_day_delivery_after_hours' ) ? get_option( 'orddd_disable_same_day_delivery_after_hours' ) : 0;
	$cutoff_mins  = '' !== get_option( 'orddd_disable_same_day_delivery_after_minutes' ) ? get_option( 'orddd_disable_same_day_delivery_after_minutes' ) : 0;

	if ( 'next_day' === $cutoff_day ) {
		$cutoff_hours = '' !== get_option( 'orddd_disable_next_day_delivery_after_hours' ) ? get_option( 'orddd_disable_next_day_delivery_after_hours' ) : 0;
		$cutoff_mins  = '' !== get_option( 'orddd_disable_next_day_delivery_after_minutes' ) ? get_option( 'orddd_disable_next_day_delivery_after_minutes' ) : 0;
	}

	if ( 'on' === get_option( 'orddd_enable_day_wise_settings' ) ) {
		$current_weekday  = 'orddd_weekday_' . gmdate( 'w', $current_time );
		$advance_settings = false !== get_option( 'orddd_advance_settings' ) ? get_option( 'orddd_advance_settings' ) : array();

		if ( '' !== $advance_settings && '{}' !== $advance_settings && '[]' !== $advance_settings ) {
			foreach ( $advance_settings as $ak => $av ) {
				if ( $current_weekday === $av['orddd_weekdays'] ) {
					if ( 'same_day' === $cutoff_day && '' !== $av['orddd_disable_same_day_delivery_after_hours'] ) {
						$cut_off_time = explode( ':', $av['orddd_disable_same_day_delivery_after_hours'] );
						$cutoff_hours = $cut_off_time[0];
						$cutoff_mins  = $cut_off_time[1];
					} elseif ( 'next_day' === $cutoff_day && '' !== $av['orddd_disable_next_day_delivery_after_hours'] ) {
						$cut_off_time = explode( ':', $av['orddd_disable_next_day_delivery_after_hours'] );
						$cutoff_hours = $cut_off_time[0];
						$cutoff_mins  = $cut_off_time[1];
					}
				}
			}
		}
	}

	$cut_off_timestamp = gmmktime( $cutoff_hours, $cutoff_mins, 0, $current_month, $current_day, $current_year );

	return apply_filters( 'orddd_modify_cutoff_timestamp', $cut_off_timestamp, $cutoff_day );
}

/**
 * Get the minimum delivery time in seconds for general settings.
 */
function orddd_get_minimum_delivery_time() {
	$current_time          = orddd_get_current_time();
	$minimum_delivery_time = '' !== get_option( 'orddd_minimumOrderDays' ) ? get_option( 'orddd_minimumOrderDays' ) * 60 * 60 : 0;

	if ( 'on' === get_option( 'orddd_enable_day_wise_settings' ) ) {
		$current_weekday  = 'orddd_weekday_' . gmdate( 'w', $current_time );
		$advance_settings = false !== get_option( 'orddd_advance_settings' ) ? get_option( 'orddd_advance_settings' ) : array();

		if ( '' !== $advance_settings && '{}' !== $advance_settings && '[]' !== $advance_settings ) {
			foreach ( $advance_settings as $ak => $av ) {
				if ( $current_weekday === $av['orddd_weekdays'] ) {
					if ( '' !== $av['orddd_minimumOrderDays'] ) {
						$minimum_delivery_time = $av['orddd_minimumOrderDays'] * 60 * 60;
					}
				}
			}
		}
	}

	return apply_filters( 'orddd_modify_minimum_delivery_time', $minimum_delivery_time );
}

/**
 * Get the minimum delivery time for custom settings.
 */
function orddd_get_minimum_delivery_time_custom() {
	if ( 'on' === get_option( 'orddd_enable_shipping_based_delivery' ) ) {
		$shipping_method    = '';
		$shipping_class     = '';
		$location           = '';
		$shipping_settings  = array();
		$product_categories = array();

		if ( isset( $_POST['orddd_location'] ) ) { //phpcs:ignore
			$location = $_POST['orddd_location']; //phpcs:ignore
		}

		if ( isset( $_POST['shipping_method'] ) ) { //phpcs:ignore
			$shipping_method = $_POST['shipping_method']; //phpcs:ignore
		}

		if ( isset( $_POST['shipping_class'] ) ) { //phpcs:ignore
			$shipping_class   = $_POST['shipping_class']; //phpcs:ignore
			$shipping_classes = explode( ',', $shipping_class );
		}

		if ( isset( $_POST['product_category'] ) ) { //phpcs:ignore
			$product_category   = $_POST['product_category']; //phpcs:ignore
			$product_categories = explode( ',', $product_category );
		}

		$results                  = orddd_common::orddd_get_shipping_settings();
		$custom_settings          = array();
		$shipping_settings_exists = 'No';

		foreach ( $results as $key => $value ) {
			$shipping_settings = get_option( $value->option_name );
			if ( isset( $shipping_settings['delivery_settings_based_on'][0] ) &&
			'orddd_locations' === $shipping_settings['delivery_settings_based_on'][0] ) {
				if ( in_array( $location, $shipping_settings['orddd_locations'], true ) ) {
					if ( isset( $shipping_settings['categories_for_pickup_locations'] ) ) {
						foreach ( $product_categories as $pkey => $pvalue ) {
							if ( in_array( $pvalue, $shipping_settings['categories_for_pickup_locations'], true ) ) {
								$shipping_settings_exists = 'Yes';
								$custom_settings[]        = $shipping_settings;
								break;
							}
						}
					} elseif ( ! isset( $shipping_settings['categories_for_pickup_locations'] ) ) {
						$shipping_settings_exists = 'Yes';
						$custom_settings[]        = $shipping_settings;
					}
				}
			} elseif ( isset( $shipping_settings['delivery_settings_based_on'][0] ) && 'shipping_methods' === $shipping_settings['delivery_settings_based_on'][0] ) {
				if ( has_filter( 'orddd_get_shipping_method' ) ) {
					$shipping_methods_values               = apply_filters( 'orddd_get_shipping_method', $custom_settings, $_POST, $shipping_settings['shipping_methods'], $shipping_method ); //phpcs:ignore
					$shipping_settings['shipping_methods'] = $shipping_methods_values['shipping_methods'];
					$shipping_method                       = $shipping_methods_values['shipping_method'];
				}

				if ( isset( $shipping_settings[ 'shipping_methods' ] ) && in_array( $shipping_method, $shipping_settings['shipping_methods'], true ) ) {
					$shipping_settings_exists = 'Yes';
					$custom_settings[]        = $shipping_settings;
				}
			} elseif ( isset( $shipping_settings['delivery_settings_based_on'][0] ) && 'product_categories' === $shipping_settings['delivery_settings_based_on'][0] ) {
				foreach ( $product_categories as $pkey => $pvalue ) {
					if ( isset( $shipping_settings[ 'product_categories' ] ) && in_array( $pvalue, $shipping_settings['product_categories'], true ) ) {
						if ( isset( $shipping_settings['shipping_methods_for_categories'] )
							&& ( in_array( $shipping_method, $shipping_settings['shipping_methods_for_categories'], true )
							|| in_array( $shipping_class, $shipping_settings['shipping_methods_for_categories'], true ) ) ) {
							$shipping_settings_exists = 'Yes';
							$custom_settings[]        = $shipping_settings;
						} elseif ( ! isset( $shipping_settings['shipping_methods_for_categories'] ) ) {
							$shipping_settings_exists = 'Yes';
							$custom_settings[]        = $shipping_settings;
						}
					}
				}
			} elseif ( isset( $shipping_settings['delivery_settings_based_on'][0] ) && 'shipping_methods' === $shipping_settings['delivery_settings_based_on'][0] ) {
				foreach ( $shipping_classes as $skey => $svalue ) {
					if ( isset( $shipping_settings[ 'shipping_methods' ] ) && in_array( $svalue, $shipping_settings['shipping_methods'], true ) ) {
						$shipping_settings_exists = 'Yes';
						$custom_settings[]        = $shipping_settings;
					}
				}
			}
		}

		$min_hour = 0;
		if ( 'Yes' === $shipping_settings_exists ) {
			$minimum_time     = orddd_get_higher_minimum_delivery_time();
			$same_day_enabled = 'No';
			$next_day_enabled = 'No';
			foreach ( $custom_settings as $key => $val ) {
				if ( isset( $val['same_day'] ) ) {
					$same_day = $val['same_day'];
					if ( isset( $same_day['after_hours'] ) && $same_day['after_hours'] == 0 && isset( $same_day['after_minutes'] ) && $same_day['after_minutes'] == 00 ) {
						$same_day_enabled = 'No';
					} else {
						$same_day_enabled = 'Yes';
					}
				}

				if ( isset( $val['next_day'] ) ) {
					$next_day = $val['next_day'];
					if ( isset( $next_day['after_hours'] ) && $next_day['after_hours'] == 0 && isset( $next_day['after_minutes'] ) && $next_day['after_minutes'] == 00 ) {
						$next_day_enabled = 'No';
					} else {
						$next_day_enabled = 'Yes';
					}
				}

				if ( '' !== $minimum_time && 0 != $minimum_time ) { //phpcs:ignore
					$min_hour = $minimum_time;
				} else {
					if ( isset( $val['minimum_delivery_time'] ) && '' !== $val['minimum_delivery_time'] ) {
						$min_hour = $val['minimum_delivery_time'];
						if ( '' === $min_hour ) {
							$min_hour = 0;
						}
					}
				}
			}
		}
	}

	$minimum_delivery_time = $min_hour * 60 * 60;
	return apply_filters( 'orddd_modify_minimum_delivery_time_custom', $minimum_delivery_time, $custom_settings );
}

/**
 * Get the highest minimum delivery time for 2 product categories or shipping classes.
 * The MDT of shipping methods is not being passed back from this function.
 */
function orddd_get_higher_minimum_delivery_time() {
	$minimum_delivery_time = wp_cache_get( 'orddd_get_higher_minimum_delivery_time' );

	if ( false === $minimum_delivery_time ) {
		global $wpdb;
		$minimum_delivery_time = 0;
		$terms_id              = array();
		$shipping_classes      = array();

		$orddd_shipping_based_delivery = get_option( 'orddd_enable_shipping_based_delivery' );
		if ( 'on' === $orddd_shipping_based_delivery ) {
			$results = orddd_common::orddd_get_shipping_settings();

			if ( isset( WC()->cart ) ) {
				foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					$product_id = $values['data']->get_id();
					if ( 'product_variation' === $values['data']->post_type ) {
						$product_id = $values['product_id'];
					}

					$terms          = get_the_terms( $product_id, 'product_cat' );
					$shipping_class = get_the_terms( $product_id, 'product_shipping_class' );

					if ( ! $shipping_class ) {
						$shipping_class = array();
					}
					// get the category IDs.
					if ( '' !== $terms ) {
						foreach ( $terms as $term => $val ) {
							$id = orddd_common::get_base_product_category( $val->term_id );

							array_push( $terms_id, $id );
						}
					}

					if ( '' !== $shipping_class ) {
						foreach ( $shipping_class as $id => $val ) {
							array_push( $shipping_classes, $val->slug );
						}
					}
				}
			}

			// get the current selected shipping method on cart/checkout page.
			$current_selected_shipping_method = '';
			if ( isset( WC()->session ) ) {
				$shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
				if ( is_array( $shipping_methods ) && count( $shipping_methods ) > 0 ) {
					$current_selected_shipping_method = $shipping_methods[ 0 ];
				}
			}

			$shipping_methods_with_custom_settings = orddd_common::orddd_get_shipping_methods_with_custom_settings();

			if ( is_array( $results ) && count( $results ) > 0 ) {
				foreach ( $results as $key => $value ) {
					$shipping_settings = get_option( $value->option_name );

					if ( isset( $shipping_settings['delivery_settings_based_on'][0] ) &&
					    'product_categories' === $shipping_settings['delivery_settings_based_on'][0] ) {
						if ( isset( $shipping_settings['product_categories'] ) ) {
							// In the 'if' block, we check MDT for custom settings where product categories are set without shipping methods.
							// In the 'else' block, we check MDT for custom settings where product categories are with shipping methods.
							if ( ! isset( $shipping_settings['shipping_methods_for_categories'] ) ) {
								$product_category = $shipping_settings['product_categories'];
								foreach ( $terms_id as $term => $val ) {
									$cat_slug = orddd_common::ordd_get_cat_slug( $val );
									if ( in_array( $cat_slug, $product_category, true ) && $minimum_delivery_time < $shipping_settings['minimum_delivery_time'] && '' !== $shipping_settings['minimum_delivery_time'] ) {
										$minimum_delivery_time = $shipping_settings['minimum_delivery_time'];
										break;
									}
								}
							} elseif ( isset( $shipping_settings['shipping_methods_for_categories'] ) ) {
								$product_category = $shipping_settings['product_categories'];
								$shipping_methods_for_categories = $shipping_settings[ 'shipping_methods_for_categories' ];

								// In the 'if' condition below, we check that the shipping method should not have it's own custom setting and that that shipping method is not the currently selected shipping method on checkout.
								// We check for the shipping method for it's own custom setting because if we don't do that, then it will return the MDT of that custom setting from this function. Once it returns a non-zero value from this function, then it ignores the MDT of the custom setting based on this shipping method.
								// Example below explains it well: 
								// Custom settings: 1. Product Category A, Shipping method: Flat Rate
								// Custom settings: 2. Product Category B, Shipping method: Flat Rate
								// Custom settings: 3. Shipping method: Flat Rate
								// If we don't check whether "Flat Rate" has it's own custom setting, then it will ignore it's MDT if it will get a non-zero MDT from the below block.
								if ( !orddd_common::orddd_shipping_method_is_custom_check( $shipping_methods_for_categories )   && !in_array( $current_selected_shipping_method, $shipping_methods_with_custom_settings ) ) {
									foreach ( $terms_id as $term => $val ) {
										$cat_slug = orddd_common::ordd_get_cat_slug( $val );
										if ( in_array( $cat_slug, $product_category, true ) && in_array( $current_selected_shipping_method, $shipping_settings['shipping_methods_for_categories'], true ) && $minimum_delivery_time < $shipping_settings['minimum_delivery_time'] && '' !== $shipping_settings['minimum_delivery_time'] ) {
											$minimum_delivery_time = $shipping_settings['minimum_delivery_time'];
											break;
										}
									}
								}
							}
						}
					} elseif ( isset( $shipping_settings['delivery_settings_based_on'][0] ) && 
							'shipping_methods' === $shipping_settings['delivery_settings_based_on'][0] ) {
						if ( isset( $shipping_settings['shipping_methods'] ) ) {
							$shipping_methods = $shipping_settings['shipping_methods'];

							if ( '' !== $shipping_classes ) {
								foreach ( $shipping_classes as $id => $class_slug ) {
									if ( in_array( $class_slug, $shipping_methods, true ) && 
										$minimum_delivery_time < $shipping_settings['minimum_delivery_time'] && 
										'' !== $shipping_settings['minimum_delivery_time'] ) {
										$minimum_delivery_time = $shipping_settings['minimum_delivery_time'];
										break;
									}
								}
							}
						}
					}
				}
			}
			wp_cache_set( 'orddd_get_higher_minimum_delivery_time', $minimum_delivery_time );
		}
	}
	return $minimum_delivery_time;
}

/**
 * Get the highest same day cutoff from 2 product categories or shipping classes.
 */
function orddd_get_highest_same_day() {
	$results          = orddd_common::orddd_get_shipping_settings();
	$same_day         = array();
	$same_day_hours   = 0;
	$same_day_min     = 00;
	$terms_id         = array();
	$shipping_classes = array();

	$lowest_cut_off = apply_filters( 'orddd_get_lowest_same_day', false );
	
	if ( isset( WC()->cart ) ) {
		foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
			$product_id = $values['data']->get_id();
			if ( 'product_variation' === $values['data']->post_type ) {
				$product_id = $values['product_id'];
			}

			$terms          = get_the_terms( $product_id, 'product_cat' );
			$shipping_class = get_the_terms( $product_id, 'product_shipping_class' );

			if ( ! $shipping_class ) {
				$shipping_class = array();
			}
			// get the category IDs.
			if ( '' !== $terms ) {
				foreach ( $terms as $term => $val ) {
					$id = orddd_common::get_base_product_category( $val->term_id );
					array_push( $terms_id, $id );
				}
			}

			if ( '' !== $shipping_class ) {
				foreach ( $shipping_class as $id => $val ) {
					array_push( $shipping_classes, $val->slug );
				}
			}
		}
	}

	$orddd_shipping_based_delivery = get_option( 'orddd_enable_shipping_based_delivery' );
	if ( 'on' === $orddd_shipping_based_delivery && is_array( $results ) && count( $results ) > 0 ) {
		// get the current selected shipping method on cart/checkout page.
		$current_selected_shipping_method = '';
		if ( isset( WC()->session ) ) {
			$shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
			if ( is_array( $shipping_methods ) && count( $shipping_methods ) > 0 ) {
				$current_selected_shipping_method = $shipping_methods[ 0 ];
			}
		}

		if ( isset( $_POST['shipping_method'] ) && $_POST['shipping_method'] !== $current_selected_shipping_method ) {
			$current_selected_shipping_method = $_POST['shipping_method'];
		}
		$shipping_methods_with_custom_settings = orddd_common::orddd_get_shipping_methods_with_custom_settings();
		
		foreach ( $results as $key => $value ) {
			$shipping_settings = get_option( $value->option_name );
			$calculate_cutoff  = false;

			if ( isset( $shipping_settings['delivery_settings_based_on'][0] ) && 'product_categories' === $shipping_settings['delivery_settings_based_on'][0] ) {
				if ( isset( $shipping_settings['product_categories'] ) && ! isset( $shipping_settings['shipping_methods_for_categories'] ) ) {
					$product_category = $shipping_settings['product_categories'];
					$calculate_cutoff = true;
				} elseif( isset( $shipping_settings['product_categories'] ) && isset( $shipping_settings['shipping_methods_for_categories'] ) ) {
					$product_category = $shipping_settings['product_categories'];
					$shipping_methods_for_categories = $shipping_settings[ 'shipping_methods_for_categories' ];

					if ( !orddd_common::orddd_shipping_method_is_custom_check( $shipping_methods_for_categories ) && !in_array( $current_selected_shipping_method, $shipping_methods_with_custom_settings ) && in_array( $current_selected_shipping_method, $shipping_settings['shipping_methods_for_categories'], true ) ) {
						$calculate_cutoff = true;
					}
				}

				if ( $calculate_cutoff ) {
					foreach ( $terms_id as $term => $val ) {
						$cat_slug = orddd_common::ordd_get_cat_slug( $val );
	
						if ( in_array( $cat_slug, $product_category, true ) && isset( $shipping_settings['same_day'] ) && $shipping_settings['same_day']['after_hours'] > 0  ) { //phpcs:ignore
	
							if ( $lowest_cut_off && ( $same_day_hours > $shipping_settings['same_day']['after_hours'] || ( $same_day_hours === $shipping_settings['same_day']['after_hours'] && $same_day_min === $shipping_settings['same_day']['after_minutes'] ) || ( $same_day_hours == 0 ) ) ) {
								// same day is enabled.
								$same_day                      = $shipping_settings['same_day'];
								$same_day['same_day_disabled'] = 'no';
								$same_day_hours                = $shipping_settings['same_day']['after_hours'];
								$same_day_min                  = $shipping_settings['same_day']['after_minutes'];
							} elseif ( ! $lowest_cut_off && ( $same_day_hours < $shipping_settings['same_day']['after_hours'] || ( $same_day_hours === $shipping_settings['same_day']['after_hours'] && $same_day_min === $shipping_settings['same_day']['after_minutes'] ) || ( $same_day_hours == 0 ) ) ) {
								// same day is enabled.
								$same_day                      = $shipping_settings['same_day'];
								$same_day['same_day_disabled'] = 'no';
								$same_day_hours                = $shipping_settings['same_day']['after_hours'];
								$same_day_min                  = $shipping_settings['same_day']['after_minutes'];
							}
						} elseif ( in_array( $cat_slug, $product_category, true ) && ( ! isset( $shipping_settings['same_day'] ) || ( isset( $shipping_settings['same_day'] ) && '0' === $shipping_settings['same_day']['after_hours'] && '00' === $shipping_settings['same_day']['after_minutes'] ) ) && ( isset( $shipping_settings['next_day'] ) && '0' !== $shipping_settings['next_day']['after_hours'] ) ) {
	
							// same day is not set, but next day is set.
							$same_day = array( 'same_day_disabled' => 'yes' );
							break 2;
						}
					}
				}
			} elseif ( isset( $shipping_settings['delivery_settings_based_on'][0] ) && 'shipping_methods' === $shipping_settings['delivery_settings_based_on'][0] ) {
				if ( isset( $shipping_settings['shipping_methods'] ) ) {
					$shipping_methods = $shipping_settings['shipping_methods'];
					foreach ( $shipping_classes as $id => $class_slug ) {

						if ( in_array( $class_slug, $shipping_methods, true ) && isset( $shipping_settings['same_day'] ) &&
						$shipping_settings['same_day']['after_hours'] > 0 ) { //phpcs:ignore

							if ( $lowest_cut_off && ( $same_day_hours > $shipping_settings['same_day']['after_hours'] || ( $same_day_hours === $shipping_settings['same_day']['after_hours'] && $same_day_min === $shipping_settings['same_day']['after_minutes'] ) || $same_day_hours == 0 ) ) {
								// same day is enabled.
								$same_day                      = $shipping_settings['same_day'];
								$same_day['same_day_disabled'] = 'no';
								$same_day_hours                = $shipping_settings['same_day']['after_hours'];
								$same_day_min                  = $shipping_settings['same_day']['after_minutes'];
							} elseif ( ! $lowest_cut_off && ( $same_day_hours < $shipping_settings['same_day']['after_hours'] || ( $same_day_hours === $shipping_settings['same_day']['after_hours'] && $same_day_min === $shipping_settings['same_day']['after_minutes'] ) || $same_day_hours == 0 ) ) {
								// same day is enabled.
								$same_day                      = $shipping_settings['same_day'];
								$same_day['same_day_disabled'] = 'no';
								$same_day_hours                = $shipping_settings['same_day']['after_hours'];
								$same_day_min                  = $shipping_settings['same_day']['after_minutes'];
							}
						} elseif ( in_array( $class_slug, $shipping_methods, true ) && ( ! isset( $shipping_settings['same_day'] ) || ( isset( $shipping_settings['same_day'] ) && '0' === $shipping_settings['same_day']['after_hours'] && '00' === $shipping_settings['same_day']['after_minutes'] ) ) && ( isset( $shipping_settings['next_day'] ) && '0' !== $shipping_settings['next_day']['after_hours'] ) ) {

							// same day is not set, but next day is set.
							if ( isset( $shipping_settings['next_day'] ) && $shipping_settings['next_day']['after_hours'] > 0 ) {

								$same_day = array( 'same_day_disabled' => 'yes' );
								break 2;
							}
						}
					}
				}
			} else {
				$same_day = array();
			}
		}
	}
	return $same_day;
}


/**
 * Get the highest next day cutoff from 2 product categories or shipping classes.
 */
function orddd_get_highest_next_day() {
	$next_day       = array();
	$next_day_hours = 0;
	$next_day_min   = 00;

	$results          = orddd_common::orddd_get_shipping_settings();
	$terms_id         = array();
	$shipping_classes = array();

	$lowest_cut_off = apply_filters( 'orddd_get_lowest_next_day', false );

	if ( isset( WC()->cart ) ) {
		foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
			$product_id = $values['data']->get_id();
			if ( 'product_variation' === $values['data']->post_type ) {
				$product_id = $values['product_id'];
			}

			$terms          = get_the_terms( $product_id, 'product_cat' );
			$shipping_class = get_the_terms( $product_id, 'product_shipping_class' );

			if ( ! $shipping_class ) {
				$shipping_class = array();
			}
			// get the category IDs.
			if ( '' !== $terms ) {
				foreach ( $terms as $term => $val ) {
					$id = orddd_common::get_base_product_category( $val->term_id );
					array_push( $terms_id, $id );
				}
			}

			if ( '' !== $shipping_class ) {
				foreach ( $shipping_class as $id => $val ) {
					array_push( $shipping_classes, $val->slug );
				}
			}
		}
	}

	$orddd_shipping_based_delivery = get_option( 'orddd_enable_shipping_based_delivery' );
	if ( 'on' === $orddd_shipping_based_delivery && is_array( $results ) && count( $results ) > 0 ) {
		// get the current selected shipping method on cart/checkout page.
		$current_selected_shipping_method = '';
		if ( isset( WC()->session ) ) {
			$shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
			if ( is_array( $shipping_methods ) && count( $shipping_methods ) > 0 ) {
				$current_selected_shipping_method = $shipping_methods[ 0 ];
			}
		}

		if ( isset( $_POST['shipping_method'] ) && $_POST['shipping_method'] !== $current_selected_shipping_method ) {
			$current_selected_shipping_method = $_POST['shipping_method'];
		}

		$shipping_methods_with_custom_settings = orddd_common::orddd_get_shipping_methods_with_custom_settings();

		foreach ( $results as $key => $value ) {
			$shipping_settings = get_option( $value->option_name );
			$calculate_cutoff  = false;
		
			if ( isset( $shipping_settings['delivery_settings_based_on'][0] ) && 'product_categories' === $shipping_settings['delivery_settings_based_on'][0] ) {
				if ( isset( $shipping_settings['product_categories'] ) && ! isset( $shipping_settings['shipping_methods_for_categories'] ) ) {
					$product_category = $shipping_settings['product_categories'];
					$calculate_cutoff = true;
				} elseif( isset( $shipping_settings['product_categories'] ) && isset( $shipping_settings['shipping_methods_for_categories'] ) ) {
					$product_category = $shipping_settings['product_categories'];
					$shipping_methods_for_categories = $shipping_settings[ 'shipping_methods_for_categories' ];

					if ( !orddd_common::orddd_shipping_method_is_custom_check( $shipping_methods_for_categories ) && !in_array( $current_selected_shipping_method, $shipping_methods_with_custom_settings ) && in_array( $current_selected_shipping_method, $shipping_settings['shipping_methods_for_categories'], true ) ) {
						$calculate_cutoff = true;
					}
				}
				

				if ( $calculate_cutoff ) {
					foreach ( $terms_id as $term => $val ) {
						$cat_slug = orddd_common::ordd_get_cat_slug( $val );
						if ( in_array( $cat_slug, $product_category, true ) && isset( $shipping_settings['next_day'] ) && $shipping_settings['next_day']['after_hours'] > 0 ) { //phpcs:ignore

							if ( $lowest_cut_off && ( $next_day_hours > $shipping_settings['next_day']['after_hours'] || ( $next_day_hours == $shipping_settings['next_day']['after_hours'] && $next_day_min == $shipping_settings['next_day']['after_minutes'] ) || ( $next_day_hours == 0 ) ) ) {
								// next day enabled.
								$next_day                      = $shipping_settings['next_day'];
								$next_day['next_day_disabled'] = 'no';
								$next_day_hours                = $shipping_settings['next_day']['after_hours'];
								$next_day_min                  = $shipping_settings['next_day']['after_minutes'];
							} elseif ( ! $lowest_cut_off && ( $next_day_hours < $shipping_settings['next_day']['after_hours'] || ( $next_day_hours == $shipping_settings['next_day']['after_hours'] && $next_day_min == $shipping_settings['next_day']['after_minutes'] ) || ( $next_day_hours == 0 ) ) ) {
								// next day enabled.
								$next_day                      = $shipping_settings['next_day'];
								$next_day['next_day_disabled'] = 'no';
								$next_day_hours                = $shipping_settings['next_day']['after_hours'];
								$next_day_min                  = $shipping_settings['next_day']['after_minutes'];
							}
						} elseif ( in_array( $cat_slug, $product_category, true ) && ( ! isset( $shipping_settings['next_day'] ) || ( isset( $shipping_settings['next_day'] ) && '0' === $shipping_settings['next_day']['after_hours'] && '00' === $shipping_settings['next_day']['after_minutes'] ) ) && ( ! isset( $shipping_settings['same_day'] ) || ( isset( $shipping_settings['same_day'] ) && '0' === $shipping_settings['same_day']['after_hours'] && '00' === $shipping_settings['same_day']['after_minutes'] ) ) ) {

							// next day is disabled & same day is disabled.
							// if next_day_hours are not set.
							if ( 0 == $next_day_hours ) { //phpcs:ignore
								$next_day = array( 'next_day_disabled' => 'yes' );
								break;
							}
						}
					}
				}
			} elseif ( isset( $shipping_settings['delivery_settings_based_on'][0] ) && 'shipping_methods' === $shipping_settings['delivery_settings_based_on'][0] ) {
				if ( isset( $shipping_settings['shipping_methods'] ) ) {
					$shipping_methods = $shipping_settings['shipping_methods'];
					foreach ( $shipping_classes as $id => $class_slug ) {
						if ( in_array( $class_slug, $shipping_methods, true ) && isset( $shipping_settings['next_day'] ) && $shipping_settings['next_day']['after_hours'] > 0 ) { //phpcs:ignore

							if ( $lowest_cut_off && ( $next_day_hours > $shipping_settings['next_day']['after_hours'] || ( $next_day_hours == $shipping_settings['next_day']['after_hours'] && $next_day_min == $shipping_settings['next_day']['after_minutes'] ) || $next_day_hours == 0 ) ) {
								// next day enabled.
								$next_day                      = $shipping_settings['next_day'];
								$next_day['next_day_disabled'] = 'no';
								$next_day_hours                = $shipping_settings['next_day']['after_hours'];
								$next_day_min                  = $shipping_settings['next_day']['after_minutes'];
							} elseif ( ! $lowest_cut_off && ( $next_day_hours < $shipping_settings['next_day']['after_hours'] || ( $next_day_hours == $shipping_settings['next_day']['after_hours'] && $next_day_min == $shipping_settings['next_day']['after_minutes'] ) || $next_day_hours == 0 ) ) {
								// next day enabled.
								$next_day                      = $shipping_settings['next_day'];
								$next_day['next_day_disabled'] = 'no';
								$next_day_hours                = $shipping_settings['next_day']['after_hours'];
								$next_day_min                  = $shipping_settings['next_day']['after_minutes'];
							}

						} elseif ( in_array( $class_slug, $shipping_methods, true ) && ( ! isset( $shipping_settings['next_day'] )
						|| ( isset( $shipping_settings['next_day'] ) && '0' === $shipping_settings['next_day']['after_hours'] && '00' === $shipping_settings['next_day']['after_minutes'] ) ) && ( ! isset( $shipping_settings['same_day'] ) || ( isset( $shipping_settings['same_day'] ) && '0' === $shipping_settings['same_day']['after_hours'] && '00' === $shipping_settings['same_day']['after_minutes'] ) ) ) {

							// next day is not set, same day is not set.
							$next_day = array( 'next_day_disabled' => 'yes' );
							break 2;

						}
					}
				}
			}
		}
	}
	return $next_day;
}

/**
 * Calculate the difference between current time and first available date.
 *
 * @param int    $delivery_time_seconds Minimum delivery time in seconds.
 * @param string $current_time Current time.
 * @param array  $min_date_array Array with min date & time.
 * @return int
 */
function orddd_calculate_cutoff_time_slots( $delivery_time_seconds, $current_time, $min_date_array ) {
	$current_date          = date( 'Y-m-d G:i', $current_time ); //phpcs:ignore
	$current_date_to_check = new DateTime( $current_date );

	if ( $min_date_array['min_date'] === $current_date && 0 === $delivery_time_seconds ) {
		$last_slot = date( 'G:i', $current_time );
	} else {
		$last_slot = $min_date_array['min_hour'] . ':' . $min_date_array['min_minute'];
	}

	$ordd_date_two = $min_date_array['min_date'] . ' ' . $last_slot;
	$ordd_date_two = date( 'Y-m-d G:i', strtotime( $ordd_date_two ) ); //phpcs:ignore
	$min_date      = new DateTime( $ordd_date_two );

	if ( version_compare( phpversion(), '5.3.0', '>' ) ) {
		$difference = $min_date->diff( $current_date_to_check );
	} else {
		$difference = orddd_common::dateTimeDiff( $min_date, $current_date_to_check );
	}

	if ( $difference->days > 0 ) {
		$days_in_hour  = $difference->h + ( $difference->days * 24 );
		$difference->h = $days_in_hour;
	}

	if ( $difference->i > 0 ) {
		$min_in_hour              = $difference->h + ( $difference->i / 60 );
		$diff_min_hour_in_seconds = $min_in_hour * 60 * 60;
	} else {
		$diff_min_hour_in_seconds = $difference->h * 60 * 60;
	}

	$delivery_time_seconds = ( $diff_min_hour_in_seconds > $delivery_time_seconds && 0 !== $delivery_time_seconds ) ? ( $diff_min_hour_in_seconds ) : $delivery_time_seconds;

	return $delivery_time_seconds;
}

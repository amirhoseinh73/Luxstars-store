<?php
/**
 *  Compatiblity with Tree Table Rate plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Functions for Tree Table rate plugin.
 */
class ORDDD_Tree_Table_Rate {

	/**
	 * Return the shipping zone title and ID for Tree table rate plugin.
	 *
	 * @param Object $shipping_zone_value Shipping zone settings.
	 * @return array
	 */
	public static function orddd_get_tree_table_shipping_zones( $shipping_default_value, $shipping_zone_value, $is_global = false ) {
		$shipping_default_zones = array(
			'shipping_default_zone_title' => 'Tree Table Rate',
			'shipping_default_zone_id'    => '',
		);
		
        if( $is_global ) {
		    $option_settings    = get_option( 'woocommerce_tree_table_rate_settings' );
        } else {
            $option_settings    = get_option( 'woocommerce_tree_table_rate_' . $shipping_zone_value->instance_id . '_settings' );
        }

		$rules                = json_decode( $option_settings['rule'], true );
		$wc_rate_ids_counters = array();

		$children = isset( $rules['children'] ) ? $rules['children'] : array();

		foreach ( $children as $tkey => $tvalue ) {
			if ( true == $tvalue['meta']['enable'] ) {

				if ( is_array( $tvalue['children'] ) && count( $tvalue['children'] ) > 0 ) {
					foreach ( $tvalue['children'] as $id => $data ) {
						$title = $data['meta']['title'];
						$id    = Orddd_Shipping_Based_Settings::orddd_get_shipping_package_id( $title );
						isset( $wc_rate_ids_counters[ $id ] ) ? $wc_rate_ids_counters[ $id ]++ : ( $wc_rate_ids_counters[ $id ] = 0 );
						if ( ( $count = $wc_rate_ids_counters[ $id ] ) > 0 ) {
							$id .= '_' . ( $count + 1 );
						}

						if ( $is_global ) {
							$id    = 'tree_table_rate' . ':' . $id;
							$title = 'Tree Table Rate -> ' . $title;
						} else {
							$id    = $shipping_zone_value->get_rate_id() . ':' . $id;
							$title = $shipping_default_value['zone_name'] . ' -> ' . $shipping_zone_value->title . ' -> ' . $data['meta']['title'];
						}

						$shipping_default_zones = array(
							'shipping_default_zone_title' => $title,
							'shipping_default_zone_id'    => $id,
						);
					}
				} else {
					$title = $tvalue['meta']['title'];
					$id    = Orddd_Shipping_Based_Settings::orddd_get_shipping_package_id( $title );
					isset( $wc_rate_ids_counters[ $id ] ) ? $wc_rate_ids_counters[ $id ]++ : ( $wc_rate_ids_counters[ $id ] = 0 );
					if ( ( $count = $wc_rate_ids_counters[ $id ] ) > 0 ) {
						$id .= '_' . ( $count + 1 );
					}
					if ( $is_global ) {
						$id    = 'tree_table_rate' . ':' . $id;
						$title = 'Tree Table Rate -> ' . $title;
					} else {
						$id    = $shipping_zone_value->get_rate_id() . ':' . $id;
						$title = $shipping_default_value['zone_name'] . ' -> ' . $shipping_zone_value->title . ' -> ' . $tvalue['meta']['title'];
					}

					$shipping_default_zones = array(
						'shipping_default_zone_title' => $title,
						'shipping_default_zone_id'    => $id,
					);
				}
			}
		}

		return $shipping_default_zones;
	}
}

<?php if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/*
	Plugin Name: Easy Upload Files During Checkout
	Plugin URI: https://androidbubble.com/blog/wordpress/plugins/easy-upload-files-during-checkout
	Description: Attach files during checkout process on cart page with ease.
	Version: 2.8.8
	Author: Fahad Mahmood
	Author URI: https://www.androidbubbles.com
	Text Domain: easy-upload-files-during-checkout
	Domain Path: /languages
	License: GPL2

	This WordPress Plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or any later version. This free software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this software. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
*/

	$plugins_activated = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
if ( ! in_array( 'woocommerce/woocommerce.php', $plugins_activated ) ) {
	return; // Check if WooCommerce is active
}

	// if(isset($_POST['post_password'])){ return;exit; }
if ( ! is_admin() ) {

	$actual_link = ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

	$arr1 = explode( '/', home_url() );
	$arr2 = explode( '/', $actual_link );


	$arr3 = array_intersect( $arr1, $arr2 );



	$arr3 = array_filter( $arr3, 'strlen' );

	$REQUEST_URI = $_SERVER['REQUEST_URI'];

	if ( ! empty( $arr3 ) ) {
		foreach ( $arr3 as $uri ) {
			$REQUEST_URI = str_replace( $uri, '', $REQUEST_URI );
		}
		$REQUEST_URI = str_replace( '//', '', $REQUEST_URI );

		if ( substr( $REQUEST_URI, -1, 1 ) == '/' ) {

			$REQUEST_URI = substr( $REQUEST_URI, 0, -1 );

			$REQUEST_URI = explode( '/', $REQUEST_URI );

			$REQUEST_URI = end( $REQUEST_URI );

		}
	}





	global $wpdb;

	$query = "SELECT post_password FROM $wpdb->posts WHERE post_name='" . esc_attr( $REQUEST_URI ) . "' LIMIT 1";
	$res   = $wpdb->get_row( $query );

	if ( ! empty( $res ) ) {
		if ( isset( $res->post_password ) && trim( $res->post_password ) != '' ) {
			return;
		}
	}
}
function eufdc_unique_identity() {
	$client  = array_key_exists( 'HTTP_CLIENT_IP', $_SERVER ) ? $_SERVER['HTTP_CLIENT_IP'] : '';
	$forward = array_key_exists( 'HTTP_X_FORWARDED_FOR', $_SERVER ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '';
	$remote  = array_key_exists( 'REMOTE_ADDR', $_SERVER ) ? $_SERVER['REMOTE_ADDR'] : '';

	if ( filter_var( $client, FILTER_VALIDATE_IP ) ) {
		$ip = $client;
	} elseif ( filter_var( $forward, FILTER_VALIDATE_IP ) ) {
		$ip = $forward;
	} else {
		$ip = $remote;
	}

	return $ip;
}

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	global $easy_ufdc_page, $easy_ufdc_req, $easy_ufdc_error, $wufdc_dir, $wufdc_dir_url, $ufdc_premium_link, $ufdc_custom,
		   $wufdc_pro_file, $eufdc_data, $easy_ufdc_error_default, $default_upload_dir, $easy_ufdc_page_checkout_refresh,
		   $eufdc_default_parent_post_id, $wc_ufdc_upload_dir, $eufdc_product_round, $easy_ufdc_success, $easy_ufdc_multiple, $is_view_order, $eufdc_input_text_label, $eufdc_items_attachments, $eufdc_localize_arr, $easy_ufdc_max_uploadsize;


	$easy_ufdc_max_uploadsize_deafult = trim(get_option( 'easy_ufdc_max_uploadsize', ini_get('upload_max_filesize') ));	
	$easy_ufdc_max_uploadsize = filter_var($easy_ufdc_max_uploadsize_deafult, FILTER_SANITIZE_NUMBER_INT);	
	$easy_ufdc_max_uploadsize = (substr($easy_ufdc_max_uploadsize_deafult, -1, 1)=='G'?$easy_ufdc_max_uploadsize*1024:$easy_ufdc_max_uploadsize);
		
	$eufdc_localize_arr = array();
	$eufdc_input_text_label       = get_option( 'eufdc_input_text_label', __( 'File Description', 'easy-upload-files-during-checkout' ) );
	$is_view_order                = false;
	$eufdc_product_round          = 0;
	$eufdc_default_parent_post_id = str_replace( array( '.', ':' ), '', eufdc_unique_identity() );// date('Ymdd');
	// $eufdc_default_parent_post_id = 1921680105;//date('Ymdd');



	$easy_ufdc_page_checkout_refresh = get_option( 'easy_ufdc_page_checkout_refresh', 'yes' );
	$default_upload_dir              = wp_upload_dir();
	$default_upload_dir['basedir']   = str_replace( '\\', '/', $default_upload_dir['basedir'] );

	$wc_ufdc_upload_dir = stripslashes( get_option( 'woocommerce_ufdc_upload_dir', $default_upload_dir['basedir'] ) );
	$wc_ufdc_upload_dir = str_replace( '\\', '/', $wc_ufdc_upload_dir );

if ( is_dir( $wc_ufdc_upload_dir ) ) {
	@chmod( $wc_ufdc_upload_dir, 0755 );
}

	$easy_ufdc_error_default   = __( 'Do you have something to attach?', 'easy-upload-files-during-checkout' );
	$easy_ufdc_success_default = __( 'File has been uploaded successfully.', 'easy-upload-files-during-checkout' );

	$ufdc_premium_link       = 'https://shop.androidbubbles.com/product/woocommerce-upload-files-checkout';// https://shop.androidbubble.com/products/wordpress-plugin?variant=36439507665051';//
	$easy_ufdc_page          = get_option( 'easy_ufdc_page' );
	$eufdc_items_attachments = get_option( 'eufdc_items_attachments', true );

	$easy_ufdc_req           = get_option( 'easy_ufdc_req' );
	$easy_ufdc_multiple      = get_option( 'easy_ufdc_multiple' );
	$wufdc_dir               = plugin_dir_path( __FILE__ );
	$wufdc_dir_url           = plugin_dir_url( __FILE__ );

	$eufdc_data     = get_plugin_data( __FILE__ );
	$wufdc_pro_file = $wufdc_dir . '/pro/eufdc_advanced.php';
	$ufdc_custom    = file_exists( $wufdc_pro_file );

	$easy_ufdc_error = stripslashes( trim( get_option( 'easy_ufdc_error' ) ) );

	$easy_ufdc_error = ( '' !== $easy_ufdc_error ? $easy_ufdc_error : $easy_ufdc_error_default );


	$easy_ufdc_success = stripslashes( trim( get_option( 'easy_ufdc_success' ) ) );
	$easy_ufdc_success = ( '' !== $easy_ufdc_success ? $easy_ufdc_success : $easy_ufdc_success_default );



	require_once 'inc/functions.php';


if ( is_admin() ) {
	require_once 'admin/ufdc-settings.php';
	add_action( 'admin_menu', 'easy_ufdc_admin_menu' );
}
	add_action( 'init', 'ufdc_custom_file_upload' );
	add_action( 'wp', 'ufdc_custom_init' );

if ( ! is_admin() ) {

	switch ( $easy_ufdc_page ) {
		case 'checkout':
			add_action( 'woocommerce_checkout_after_customer_details', 'add_file_to_upcoming_order' );
			break;
		case 'cart':
		case '':
			add_action( 'woocommerce_after_cart_table', 'add_file_to_upcoming_order' );
			add_action( 'wp_footer', 'ufdc_easy_ufdc_req' );
			break;
		case 'checkout_notes':
			add_action( 'woocommerce_after_order_notes', 'add_file_to_upcoming_order' );
			break;
		case 'register':
			 add_action( 'woocommerce_register_form_start', 'add_file_to_upcoming_order' );
			break;





		case 'customer_order':
			add_action(
				'woocommerce_view_order',
				function( $order_id ) {

					$is_view_order = true;

					?>

					<script type="text/javascript" language="JavaScript">

						eufdc_obj.is_view_order = true;
						eufdc_obj.is_woocommerce = true;


					</script>

						<?php

						if ( function_exists( 'eufdc_open_form' ) ) {
							add_action( 'wp_footer', 'eufdc_open_form' );
						}

				}
			);


			break;

		case 'checkout_above':
			if ( function_exists( 'eufdc_open_form' ) ) {
				add_action( 'woocommerce_before_checkout_form', 'eufdc_open_form', 20 );
			}
			break;		
		case 'checkout_above_content':
			if ( function_exists( 'eufdc_open_form' ) ) {
				
				function eufdc_filter_the_content( $content ) {
					ob_start();
					eufdc_open_form();
					$custom_content = ob_get_clean();
					$custom_content .= $content;
					return $custom_content;
				}
				add_filter( 'the_content', 'eufdc_filter_the_content' );		
				//add_filter( 'the_title', 'eufdc_filter_the_content' );							
			}
			break;
		case 'thank_you':
			if ( function_exists( 'eufdc_open_form' ) ) {
				add_action( 'wp_footer', 'eufdc_open_form' );
			}
			break;
	}
	add_action( 'wp', 'file_during_checkout' );// woocommerce_init.
	add_action( 'woocommerce_order_status_pending', 'wc_checkout_order_processed' );
	add_action( 'woocommerce_order_status_failed', 'wc_checkout_order_processed' );
	add_action( 'woocommerce_order_status_on-hold', 'wc_checkout_order_processed' );
	add_action( 'woocommerce_order_status_processing', 'wc_checkout_order_processed' );
	add_action( 'woocommerce_order_status_completed', 'wc_checkout_order_processed' );
	add_action( 'woocommerce_order_status_cancelled', 'wc_checkout_order_processed' );

}

	add_action( 'save_post', 'pre_wc_checkout_order_processed' );





if ( ! is_admin() ) {
	add_action( 'wp_enqueue_scripts', 'wufdc_enqueue_style' );
	add_action( 'wp_enqueue_scripts', 'wufdc_enqueue_script' );
	add_action( 'wp_enqueue_scripts', 'eufdc_enqueue_common_scripts' );
} else {
	add_action( 'add_meta_boxes', 'easy_ufdc_add_box_for_files', 10 );
	$plugin_link = plugin_basename( __FILE__ );
	add_action( 'admin_enqueue_scripts', 'wufdc_admin_enqueue_script' );
	add_filter( "plugin_action_links_$plugin_link", 'ufdc_plugin_links' );
	add_action( 'admin_enqueue_scripts', 'eufdc_enqueue_common_scripts' );
}
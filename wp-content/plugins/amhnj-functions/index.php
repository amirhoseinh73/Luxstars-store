<?php
/*
 Plugin Name: AMHNJ Functions
 Plugin URI: 
 Description: register woocommerce via mobile as email and username and send sms with sms.ir, require woocommerce plugin
 Author: amirhosein hasani
 Author URI: https://instagram.com/amirhoseinh73
 Version: 1.1.2
 WC requires at least: 5.5
 WC tested up to: 6.8
 Requires at least: 5.8
 Requires PHP: 7.2
 */

define('AMHNJ_FUNCTIONS_PLUGIN_FILE'       , __FILE__);
define('AMHNJ_FUNCTIONS_PLUGIN_DIR_PATH'   , plugin_dir_path(__FILE__));
define('AMHNJ_FUNCTIONS_PLUGIN_DIR_URL'    , plugin_dir_url(__FILE__));

define( 'AMHNJ_FUNCTIONS_PLUGIN_ADMIN_PATH' , AMHNJ_FUNCTIONS_PLUGIN_DIR_PATH . 'admin/');
define( 'AMHNJ_FUNCTIONS_PLUGIN_ADMIN_URL'  , AMHNJ_FUNCTIONS_PLUGIN_DIR_URL . 'admin/');

if ( is_admin() ) {
	require_once AMHNJ_FUNCTIONS_PLUGIN_ADMIN_PATH . 'admin.php';
}

add_filter( 'woocommerce_get_availability', 'change_out_of_stock_text_woocommerce', 1, 2);


function change_out_of_stock_text_woocommerce( $availability, $product_to_check ) {
    
    // Change Out of Stock Text
    if ( $product_to_check->is_in_stock() ) {
        $availability['availability'] = str_replace( "در انبار", "عدد از این محصول در انبار باقی مانده است!", $availability['availability'] );
    }
    return $availability;
}

function disable_shipping_calc_on_cart( $show_shipping ) {
    if( is_cart() ) {
        return false;
    }
    return $show_shipping;
}
add_filter( 'woocommerce_cart_ready_to_calc_shipping', 'disable_shipping_calc_on_cart', 99 );
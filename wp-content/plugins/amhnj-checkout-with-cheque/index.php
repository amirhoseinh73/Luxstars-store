<?php
/*
 Plugin Name: AMHNJ checkout payment with cheque
 Plugin URI: 
 Description: 
 Author: amirhosein hasani
 Author URI: https://instagram.com/amirhoseinh73
 Version: 1.1.2
 WC requires at least: 5.5
 WC tested up to: 6.8
 Requires at least: 5.8
 Requires PHP: 7.2
 */

define('AMHNJ_CHECKOUT_CHEQUE_PLUGIN_FILE'       , __FILE__);
define('AMHNJ_CHECKOUT_CHEQUE_PLUGIN_DIR_PATH'   , plugin_dir_path(__FILE__));
define('AMHNJ_CHECKOUT_CHEQUE_PLUGIN_DIR_URL'    , plugin_dir_url(__FILE__));

define('AMHNJ_CHECKOUT_CHEQUE_PLUGIN_ADMIN_PATH' , AMHNJ_CHECKOUT_CHEQUE_PLUGIN_DIR_PATH . 'admin/');
define('AMHNJ_CHECKOUT_CHEQUE_PLUGIN_ADMIN_URL'  , AMHNJ_CHECKOUT_CHEQUE_PLUGIN_DIR_URL . 'admin/');

if ( is_admin() ) {
	require_once AMHNJ_CHECKOUT_CHEQUE_PLUGIN_ADMIN_PATH . 'admin.php';
}

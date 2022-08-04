<?php
/*
 Plugin Name: Register mobile as username woocommerce
 Plugin URI: 
 Description: register woocommerce via mobile as email and username and send sms with sms.ir
 Author: amirhosein hasani
 Author URI: https://instagram.com/amirhoseinh73
 Version: 1.0.0
 */

define('AMHNJ_REGISTER_PLUGIN_FILE'       , __FILE__);
define('AMHNJ_REGISTER_PLUGIN_DIR_PATH'   , plugin_dir_path(__FILE__));
define('AMHNJ_REGISTER_PLUGIN_DIR_URL'    , plugin_dir_url(__FILE__));

define('AMHNJ_REGISTER_PLUGIN_ADMIN_PATH' , AMHNJ_REGISTER_PLUGIN_DIR_PATH . 'admin/');
define('AMHNJ_REGISTER_PLUGIN_ADMIN_URL'  , AMHNJ_REGISTER_PLUGIN_DIR_URL . 'admin/');

// define('AMHNJ_PROGRESS_PLUGIN_JS_URL'     , AMHNJ_PROGRESS_PLUGIN_DIR_URL . 'asset/js/');
// define('AMHNJ_PROGRESS_PLUGIN_CSS_URL'    , AMHNJ_PROGRESS_PLUGIN_DIR_URL . 'asset/css/');


if ( is_admin() ) {
	require_once AMHNJ_REGISTER_PLUGIN_ADMIN_PATH . 'admin.php';
}

// require_once VIRA_PLUGIN_DIR_PATH . 'capabilities.php';

date_default_timezone_set('Asia/Tehran');

require_once AMHNJ_REGISTER_PLUGIN_DIR_PATH . "fnuctions.php";
require_once AMHNJ_REGISTER_PLUGIN_DIR_PATH . "register-form.php";

add_shortcode( 'amhnj_register_form', 'register_form_shortcode' );

// Shortcode callback function
function register_form_shortcode() {
    ob_start();
    do_register_form();
    return ob_get_clean();
}
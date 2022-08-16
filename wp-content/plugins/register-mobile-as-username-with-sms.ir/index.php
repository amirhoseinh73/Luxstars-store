<?php
/*
 Plugin Name: AMHNJ Register mobile as username woocommerce
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

define('AMHNJ_REGISTER_PLUGIN_FILE'       , __FILE__);
define('AMHNJ_REGISTER_PLUGIN_DIR_PATH'   , plugin_dir_path(__FILE__));
define('AMHNJ_REGISTER_PLUGIN_DIR_URL'    , plugin_dir_url(__FILE__));

define('AMHNJ_REGISTER_PLUGIN_ADMIN_PATH' , AMHNJ_REGISTER_PLUGIN_DIR_PATH . 'admin/');
define('AMHNJ_REGISTER_PLUGIN_ADMIN_URL'  , AMHNJ_REGISTER_PLUGIN_DIR_URL . 'admin/');

// define('AMHNJ_PROGRESS_PLUGIN_JS_URL'     , AMHNJ_PROGRESS_PLUGIN_DIR_URL . 'asset/js/');
// define('AMHNJ_PROGRESS_PLUGIN_CSS_URL'    , AMHNJ_PROGRESS_PLUGIN_DIR_URL . 'asset/css/');

if ( ! function_exists( "amhnj_admin_notice__success" ) ) {
    function amhnj_admin_notice__success( $error_message ) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( $error_message, 'amhnj' ); ?></p>
        </div>
        <?php
    }
}

if ( ! function_exists( "amhnj_admin_notice__error" ) ) {
    function amhnj_admin_notice__error( $error_message ) {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php _e( $error_message, 'amhnj' ); ?></p>
        </div>
        <?php
    }
}

if ( ! function_exists( 'is_plugin_inactive' ) ) {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

if ( function_exists( "is_plugin_inactive" ) ) {
    if ( is_plugin_inactive( "woocommerce/woocommerce.php" ) ) {
        $error_message = "افزونه ووکامرس باید فعال باشد!";
        add_action( 'admin_notices', function() use( $error_message ) {
            amhnj_admin_notice__error( $error_message );
        }, 10, 1 );
    }
}

if ( is_admin() ) {
	require_once AMHNJ_REGISTER_PLUGIN_ADMIN_PATH . 'admin.php';
}

// require_once VIRA_PLUGIN_DIR_PATH . 'capabilities.php';

date_default_timezone_set('Asia/Tehran');

require_once AMHNJ_REGISTER_PLUGIN_DIR_PATH . "functions.php";
require_once AMHNJ_REGISTER_PLUGIN_DIR_PATH . "register-form.php";

// Shortcode callback function
add_shortcode( 'amhnj_register_form', 'register_form_shortcode' );
function register_form_shortcode() {
    ob_start();
    do_register_form();
    return ob_get_clean();
}

require_once AMHNJ_REGISTER_PLUGIN_DIR_PATH . "login-form.php";
// Shortcode callback function
add_shortcode( 'amhnj_login_form', 'login_form_shortcode' );
function login_form_shortcode() {
    ob_start();
    do_login_form();
    return ob_get_clean();
}
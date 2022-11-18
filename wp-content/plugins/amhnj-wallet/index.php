<?php
/*
 Plugin Name: AMHNJ Wallet Colleague
 Plugin URI: 
 Description: Wallet and Colleague
 Author: amirhosein hasani
 Author URI: https://instagram.com/amirhoseinh73
 Version: 1.1.2
 WC requires at least: 5.5
 WC tested up to: 6.8
 Requires at least: 5.8
 Requires PHP: 7.2
 */

define('AMHNJ_WALLET_PLUGIN_FILE'       , __FILE__);
define('AMHNJ_WALLET_PLUGIN_DIR_PATH'   , plugin_dir_path(__FILE__));
define('AMHNJ_WALLET_PLUGIN_DIR_URL'    , plugin_dir_url(__FILE__));

define('AMHNJ_WALLET_PLUGIN_ADMIN_PATH' , AMHNJ_WALLET_PLUGIN_DIR_PATH . 'admin/');
define('AMHNJ_WALLET_PLUGIN_ADMIN_URL'  , AMHNJ_WALLET_PLUGIN_DIR_URL . 'admin/');


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
	require_once AMHNJ_WALLET_PLUGIN_ADMIN_PATH . 'admin.php';
}

require_once AMHNJ_WALLET_PLUGIN_DIR_PATH . "functions.php";
require_once AMHNJ_WALLET_PLUGIN_DIR_PATH . "colleague-code-chcekout-page.php";
require_once AMHNJ_WALLET_PLUGIN_DIR_PATH . "customer-menu-items.php";

add_action('wp_enqueue_scripts', 'amh_nj_load_script_wallet');
function amh_nj_load_script_wallet() {
    wp_enqueue_script( "amh-nj-wallet", AMHNJ_WALLET_PLUGIN_DIR_URL . "/assets/js/script.js" );
}
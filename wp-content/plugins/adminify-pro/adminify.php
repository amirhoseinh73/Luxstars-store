<?php

/**
 * Plugin Name: WP Adminify Pro
 * Description: Supercharge your WordPress Adminify with <a href="https://wpadminify.com">WP Adminify</a> plugin. It has Completely WordPress White Label, Professional & Clean UI, Analytics, Charts, Menu UI, Light & Dark Mode, Menu Editor, Google Pagespeed Insights, Multisite Support and many more to get amazed.
 * Plugin URI: https://wpadminify.com
 * Author: 20script
 * Version: 2.0.0
 * Author URI: https://www.20script.ir
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: adminify
 * Domain Path: /languages
 * @fs_premium_only /Pro/, /Inc/Modules/AdminPages/
 *
 */
// No, Direct access Sir !!!
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
$jltwp_adminify_plugin_data = get_file_data( __FILE__, array(
    'Version'     => 'Version',
    'Plugin Name' => 'Plugin Name',
    'Author'      => 'Author',
    'Description' => 'Description',
    'Plugin URI'  => 'Plugin URI',
), false );
// Define Constants
define( 'WP_ADMINIFY', $jltwp_adminify_plugin_data['Plugin Name'] );
define( 'WP_ADMINIFY_VER', $jltwp_adminify_plugin_data['Version'] );
define( 'WP_ADMINIFY_TD', 'adminify' );
define( 'WP_ADMINIFY_FILE', __FILE__ );
define( 'WP_ADMINIFY_BASE', plugin_basename( __FILE__ ) );
define( 'WP_ADMINIFY_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'WP_ADMINIFY_URL', trailingslashit( plugins_url( '/', __FILE__ ) ) );
define( 'WP_ADMINIFY_ASSETS', WP_ADMINIFY_URL . 'assets/' );
define( 'WP_ADMINIFY_ASSETS_IMAGE', WP_ADMINIFY_ASSETS . 'images/' );
define( 'WP_ADMINIFY_ASSET_PATH', wp_upload_dir()['basedir'] . '/wp-adminify' );
define( 'WP_ADMINIFY_ASSET_URL', wp_upload_dir()['baseurl'] . '/wp-adminify' );
define( 'WP_ADMINIFY_DESC', $jltwp_adminify_plugin_data['Description'] );
define( 'WP_ADMINIFY_AUTHOR', $jltwp_adminify_plugin_data['Author'] );
define( 'WP_ADMINIFY_URI', $jltwp_adminify_plugin_data['Plugin URI'] );

if ( function_exists( 'jltwp_adminify' ) ) {
    jltwp_adminify()->set_basename( true, __FILE__ );
} else {
    
    if ( !function_exists( 'jltwp_adminify' ) ) {
        // Create a helper function for easy SDK access.
        function jltwp_adminify()
        {
            global  $jltwp_adminify ;
            
            if ( !isset( $jltwp_adminify ) ) {

				class adminifyFsNull {
					public function can_use_premium_code__premium_only() {
						return true;
					}
					public function is_plan__premium_only() {
						return 'agency';
					}
					public function is_premium() {
						return true;
					}
					public function is_plan() {
						return true;
					}
				}

                $jltwp_adminify = new adminifyFsNull();
            }
            
            return $jltwp_adminify;
        }
        
        // Init Freemius.
        jltwp_adminify();
        // Signal that SDK was initiated.
        do_action( 'jltwp_adminify_loaded' );
    }

}

// Autoload
require_once dirname( __FILE__ ) . '/vendor/autoload.php';
// Instantiate WP Adminify Class
if ( !class_exists( '\\WPAdminify\\WP_Adminify' ) ) {
    require_once dirname( __FILE__ ) . '/class-wp-adminify.php';
}
// Activation and Deactivation hooks

if ( class_exists( '\\WPAdminify\\WP_Adminify' ) ) {
    register_activation_hook( WP_ADMINIFY_FILE, array( '\\WPAdminify\\WP_Adminify', 'jltwp_adminify_activation_hook' ) );
    register_deactivation_hook( WP_ADMINIFY_FILE, array( '\\WPAdminify\\WP_Adminify', 'jltwp_adminify_deactivation_hook' ) );
}


if ( class_exists( '\\WPAdminify\\Inc\\Modules\\ActivityLogs\\Inc\\DB_Table' ) ) {
    register_activation_hook( WP_ADMINIFY_FILE, [ '\\WPAdminify\\Inc\\Modules\\ActivityLogs\\Inc\\DB_Table', 'activation_hook' ] );
    register_uninstall_hook( WP_ADMINIFY_FILE, [ '\\WPAdminify\\Inc\\Modules\\ActivityLogs\\Inc\\DB_Table', 'deactivation_hook' ] );
}

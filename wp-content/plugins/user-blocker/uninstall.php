<?php

/**
 * Fired when the plugin is uninstalled.
 *
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
$ub_delete_data = get_option('ub_delete_data', 0);

if($ub_delete_data == 1 ) {
    global $wpdb;
    global $wp_roles;
    delete_option('ublk_is_optin');
    delete_option('user_blocking_promo_time');
    delete_option('is_user_subscribed_cancled');
    delete_option('ublk_version');
    $get_roles = $wp_roles->roles;
    if ($GLOBALS['wp_roles']->is_role($get_role)) {
        delete_option($get_role . '_is_active');
        delete_option($get_role . '_block_msg_permenant');
        delete_option($get_role . '_block_day');
        delete_option($get_role . '_block_msg_day');
        delete_option($get_role . '_block_date');
        delete_option($get_role . '_block_msg_date');
        delete_option($get_role . '_block_msg_permenant');
    }
    delete_option('ub_delete_data');
}


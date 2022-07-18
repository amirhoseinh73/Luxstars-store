<?php

namespace WPAdminify\Pro\Classes;

use WPAdminify\Inc\Modules\DismissNotices\Dismiss_Admin_Notices;
// no direct access allowed
if (!defined('ABSPATH'))  exit;

/**
 * Package: Dismiss Notice
 * @package WP Adminify
 *
 * @author WP Adminify <support@wpadminify.com>
 */

class DismissNotice_Pro extends Dismiss_Admin_Notices
{
    public function __construct()
    {
        $this->remove_php_update_required_notice();
        $this->core_update_notice();
        $this->plugin_update_notice();
        $this->theme_update_notice();
    }


    /**
     * Remove "PHP Update Required" Notice
     */
    public function remove_php_update_required_notice()
    {
        if (!empty($this->options["remove_php_update_required_nag"])) {
            add_action('wp_dashboard_setup', [$this, 'adminify_remove_php_update_notice']);
        }
    }
    public function adminify_remove_php_update_notice()
    {
        remove_meta_box('dashboard_php_nag', 'dashboard', 'normal');
    }



    // WordPress Core Update Notice
    public function core_update_notice()
    {
        if (!empty($this->options["core_update_notice"])) {
            add_filter('update_footer', '__return_false', 20);
            add_filter('site_transient_update_core', [$this, 'core_update_notice_callback']);
        }
    }

    // WordPress Core Update Notice Callback
    public function core_update_notice_callback($site_transient_update_core)
    {
        if (!empty($site_transient_update_core) && !empty($site_transient_update_core->updates[0]) && !empty($site_transient_update_core->updates[0]->response)) {
            $site_transient_update_core->updates[0]->response = 'latest';
        }

        return $site_transient_update_core;
    }

    // Plugin Update Notice
    public function plugin_update_notice()
    {
        if (!empty($this->options["plugin_update_notice"])) {
            add_filter('site_transient_update_plugins', [$this, 'plugin_update_notice_callback']);
        }
    }

    // Plugin Update Notice Callback
    public function plugin_update_notice_callback($site_transient_update_plugins)
    {
        if (!empty($site_transient_update_plugins->response)) {
            unset($site_transient_update_plugins->response);
        }
        return $site_transient_update_plugins;
    }

    // Theme Update Notice
    public function theme_update_notice()
    {
        if (!empty($this->options["theme_update_notice"])) {
            add_filter('site_transient_update_plugins', [$this, 'theme_update_notice_callback']);
        }
    }

    // Theme Update Notice Callback
    public function theme_update_notice_callback($site_transient_update_themes)
    {
        if (!empty($site_transient_update_themes->response)) {
            unset($site_transient_update_themes->response);
        }
        return $site_transient_update_themes;
    }
}

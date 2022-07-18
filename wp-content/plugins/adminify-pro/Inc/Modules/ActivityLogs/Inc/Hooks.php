<?php

namespace WPAdminify\Inc\Modules\ActivityLogs\Inc;

use WPAdminify\Inc\Modules\ActivityLogs\Hooks\WP_Adminify_Logs_Attachments;
use WPAdminify\Inc\Modules\ActivityLogs\Hooks\WP_Adminify_Logs_Comments;
use WPAdminify\Inc\Modules\ActivityLogs\Hooks\WP_Adminify_Logs_Core;
use WPAdminify\Inc\Modules\ActivityLogs\Hooks\WP_Adminify_Logs_Menu;
use WPAdminify\Inc\Modules\ActivityLogs\Hooks\WP_Adminify_Logs_Options;
use WPAdminify\Inc\Modules\ActivityLogs\Hooks\WP_Adminify_Logs_Posts;
use WPAdminify\Inc\Modules\ActivityLogs\Hooks\WP_Adminify_Logs_Plugins;
use WPAdminify\Inc\Modules\ActivityLogs\Hooks\WP_Adminify_Logs_Taxonomy;
use WPAdminify\Inc\Modules\ActivityLogs\Hooks\WP_Adminify_Logs_Theme;
use WPAdminify\Inc\Modules\ActivityLogs\Hooks\WP_Adminify_Logs_Users;
use WPAdminify\Inc\Modules\ActivityLogs\Hooks\WP_Adminify_Logs_Widgets;

// no direct access allowed
if (!defined('ABSPATH'))  exit;

class Hooks
{
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        new WP_Adminify_Logs_Attachments();
        new WP_Adminify_Logs_Comments();
        new WP_Adminify_Logs_Core();
        new WP_Adminify_Logs_Menu();
        new WP_Adminify_Logs_Options();
        new WP_Adminify_Logs_Posts();
        new WP_Adminify_Logs_Plugins();
        new WP_Adminify_Logs_Taxonomy();
        new WP_Adminify_Logs_Theme();
        new WP_Adminify_Logs_Users();
        new WP_Adminify_Logs_Widgets();
    }
}

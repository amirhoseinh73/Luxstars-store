<?php

namespace WPAdminify\Inc\Classes\MenuStyles;

use WPAdminify\Inc\Admin\AdminSettings;
use WPAdminify\Inc\Admin\AdminSettingsModel;

// no direct access allowed
if (!defined('ABSPATH'))  exit;

class MenuStyleBase extends AdminSettingsModel
{
    protected $url;

    public function __construct()
    {
        $this->url = WP_ADMINIFY_URL . 'inc/classes/MenuStyles';
        $this->options = (array) AdminSettings::get_instance()->get();
        add_filter('admin_body_class', [$this, 'jltwp_adminify_admin_menu_body_class']);
    }

    public function jltwp_adminify_admin_menu_body_class($classes)
    {
        $this->options = (array) AdminSettings::get_instance()->get('menu_layout_settings');
        $menu_layout = !empty($this->options['layout_type']) ? $this->options['layout_type'] : 'vertical';
        $menu_mode = !empty($this->options['menu_mode']) ? $this->options['menu_mode'] : 'classic';

        $bodyclass = '';
        if ($menu_layout == 'vertical' && $menu_mode === 'icon_menu') {
            $classes .= " folded ";
        }
        return $classes . $bodyclass;
    }
}

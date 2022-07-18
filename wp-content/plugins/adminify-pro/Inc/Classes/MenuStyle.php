<?php

namespace WPAdminify\Inc\Classes;

use WPAdminify\Inc\Classes\MenuStyles\VerticalMainMenu;

// no direct access allowed
if (!defined('ABSPATH'))  exit;

class MenuStyle
{
    public function __construct()
    {
        $layout_type = (!empty($this->options['menu_layout_settings']['layout_type'])) ? $this->options['menu_layout_settings']['layout_type'] : 'vertical';
        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            if ($layout_type  == "vertical") {
                new VerticalMainMenu();
            } elseif ($layout_type == "horizontal") {
                new \WPAdminify\Pro\Classes\HorizontalMenu();
            }
        } else {
            new VerticalMainMenu();
        }
    }
}

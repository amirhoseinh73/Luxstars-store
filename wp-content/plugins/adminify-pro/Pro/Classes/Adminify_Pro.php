<?php

namespace WPAdminify\Pro\Classes;

use WPAdminify\Pro\Classes\OutputCSS_Body_Pro;
use \WPAdminify\Pro\Classes\ColoredPost;
use \WPAdminify\Pro\Classes\CustomColumns_Pro;
use \WPAdminify\Pro\Classes\Disable_Comments_Pro;
use \WPAdminify\Pro\Classes\DismissNotice_Pro;
use \WPAdminify\Pro\Classes\Tweaks_Pro;
use \WPAdminify\Pro\Classes\HorizontalMenu;
use \WPAdminify\Pro\Classes\WhiteLabel;
use \WPAdminify\Pro\Classes\Schedule_Dark_Mode;

// no direct access allowed
if (!defined('ABSPATH'))  exit;

class Adminify_Pro
{
    public function __construct()
    {
        $this->includes_classes();
    }

    public function includes_classes()
    {
        new ColoredPost();
        new CustomColumns_Pro();
        new Disable_Comments_Pro();
        new DismissNotice_Pro();
        new Tweaks_Pro();
        new HorizontalMenu();
        new OutputCSS_Body_Pro();
        new WhiteLabel();
        new Schedule_Dark_Mode();
    }
}

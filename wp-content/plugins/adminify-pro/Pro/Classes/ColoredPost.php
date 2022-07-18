<?php

namespace WPAdminify\Pro\Classes;

use WPAdminify\Inc\Admin\AdminSettings;
use WPAdminify\Inc\Admin\AdminSettingsModel;

// no direct access allowed
if (!defined('ABSPATH'))  exit;

/**
 * Colorful Posts/Pages/Custom Posts by Post Status
 * @package WP Adminify
 *
 * @author WP Adminify <support@wpadminify.com>
 */

class ColoredPost extends AdminSettingsModel
{
    public function __construct()
    {
        $this->options = (array) AdminSettings::get_instance()->get();
        add_action('admin_footer-edit.php', [$this, 'colored_style_admin_footer']);
    }

    /**
     * Styles on Admin Footer
     *
     * @return void
     */
    public function colored_style_admin_footer()
    { ?>
        <style type="text/css">
            <?php
            $default_post_statuses = (array)self::get_default_post_statuses();
            foreach ($default_post_statuses as $default_post_status) {
                echo $this->style_builder("status-" . $default_post_status["name"], $default_post_status["option_handle"]);
            } ?>
        </style>
<?php }


    private function style_builder($css_class, $options, $important = true)
    {
        $options = $this->options['post_status_bg_colors'];

        if ($options === false || empty($options)) :
            return "";
        endif;

        $style = "";
        foreach ($options as $key => $value) {
            $status_class = "status-" . $key;
            if ($status_class == $css_class) {
                $style = "." . $css_class . "{ background: " . $value . $style .= (($important == true) ? " !important" : "") . "; }\r\n";
            }
        }
        return $style;
    }


    /**
     * Default Posts Statuses
     *
     * @return void
     */
    public static function get_default_post_statuses()
    {
        $default_post_stati = array("publish", "pending", "future", "private", "draft", "trash");
        return self::get_post_statuses($default_post_stati);
    }


    public static function get_post_custom_statuses()
    {
        $default_post_stati = array("publish", "pending", "future", "private", "draft", "trash");

        return self::get_post_statuses(array(), $default_post_stati);
    }


    private static function get_post_statuses($include = array(), $exclude = array())
    {
        $post_stati = get_post_stati($post_stati = array(), "objects");

        $custom_post_statuses = array();

        foreach ($post_stati as $post_status) {

            if ($post_status->show_in_admin_status_list === false || (sizeof($include) > 0 && !in_array($post_status->name, $include)) || in_array($post_status->name, $exclude)) :
                continue;
            endif;

            $handle = "capl-color-" . sanitize_key($post_status->name);
            $custom_post_statuses[$post_status->name] = array(
                "option_handle" => $handle,
                "label"         => $post_status->label,
                "name"          => $post_status->name
            );
        }
        ksort($custom_post_statuses);
        return $custom_post_statuses;
    }
}

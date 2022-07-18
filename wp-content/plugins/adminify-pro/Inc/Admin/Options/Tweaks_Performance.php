<?php

namespace WPAdminify\Inc\Admin\Options;

use WPAdminify\Inc\Utils;
use WPAdminify\Inc\Admin\AdminSettingsModel;

if (!defined('ABSPATH')) {
    die;
} // Cannot access directly.

class Tweaks_Performance extends AdminSettingsModel
{
    public function __construct()
    {
        $this->tweaks_performance_settings();
    }

    public function get_defaults()
    {
        return [
            'remove_jquery_migrate'    => false,
            'remove_gutenberg_scripts' => false,
            'defer_parsing_js_footer'  => false,
            'cache_gzip_compression'   => false
        ];
    }


    /**
     * Tweaks: Performance Fields
     *
     * @param [type] $performance_fields
     *
     * @return void
     */
    public function tweaks_performance_fields(&$performance_fields)
    {
        $performance_fields[] = array(
            'type'    => 'subheading',
            'content'   => Utils::adminfiy_help_urls(
                __('Performance Section for speed up your website speed and other Configurations', WP_ADMINIFY_TD),
                'https://wpadminify.com/kb/wp-adminify-tweaks/',
                'https://www.youtube.com/playlist?list=PLqpMw0NsHXV-EKj9Xm1DMGa6FGniHHly8',
                'https://www.facebook.com/groups/jeweltheme',
                'https://wpadminify.com/support/'
            )
        );

        $performance_fields[] = array(
            'id'         => 'remove_jquery_migrate',
            'type'       => 'switcher',
            'title'      => esc_html__('Remove jQuery Migrate', WP_ADMINIFY_TD),
            'subtitle'   => esc_html__('Enable if you want to remove jQuery Migrate Script', WP_ADMINIFY_TD),
            'text_on'    => __('Yes', WP_ADMINIFY_TD),
            'text_off'   => __('No', WP_ADMINIFY_TD),
            'text_width' => 80,
            'default'    => $this->get_default_field('remove_jquery_migrate'),
        );

        $performance_fields[] = array(
            'id'         => 'remove_gutenberg_scripts',
            'type'       => 'switcher',
            'title'      => esc_html__('Remove Gutenberg Scripts', WP_ADMINIFY_TD),
            'subtitle'   => esc_html__('Remove Gutenberg Blocks Scripts/Styles', WP_ADMINIFY_TD),
            'text_on'    => __('Yes', WP_ADMINIFY_TD),
            'text_off'   => __('No', WP_ADMINIFY_TD),
            'text_width' => 80,
            'default'    => $this->get_default_field('remove_gutenberg_scripts'),
        );

        $performance_fields[] = array(
            'id'         => 'defer_parsing_js_footer',
            'type'       => 'switcher',
            'title'      => esc_html__('Enable Defer Parsing JS to Footer', WP_ADMINIFY_TD),
            'subtitle'   => esc_html__('Secure method for Defer Parsing of JavaScript moving ALL JS from Header to Footer', WP_ADMINIFY_TD),
            'text_on'    => __('Yes', WP_ADMINIFY_TD),
            'text_off'   => __('No', WP_ADMINIFY_TD),
            'text_width' => 80,
            'default'    => $this->get_default_field('defer_parsing_js_footer'),
        );

        $performance_fields[] = array(
            'id'         => 'cache_gzip_compression',
            'type'       => 'switcher',
            'title'      => esc_html__('Cache & GZIP Compressions', WP_ADMINIFY_TD),
            'subtitle'   => esc_html__('Browser Cache Expires & GZIP Compression', WP_ADMINIFY_TD),
            'text_on'    => __('Yes', WP_ADMINIFY_TD),
            'text_off'   => __('No', WP_ADMINIFY_TD),
            'text_width' => 80,
            'default'    => $this->get_default_field('cache_gzip_compression'),
        );
    }



    public function tweaks_performance_settings()
    {

        if (!class_exists('ADMINIFY')) {
            return;
        }

        $performance_fields = [];
        $this->tweaks_performance_fields($performance_fields);

        \ADMINIFY::createSection(
            $this->prefix,
            array(
                'title'       => __('Performance', WP_ADMINIFY_TD),
                'parent'      => 'tweaks_performance',
                'icon'        => 'fas fa-rocket',
                'fields'      => $performance_fields
            )
        );
    }
}

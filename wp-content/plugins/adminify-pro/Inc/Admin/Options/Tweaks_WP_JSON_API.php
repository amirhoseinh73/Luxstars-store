<?php

namespace WPAdminify\Inc\Admin\Options;

use WPAdminify\Inc\Utils;
use WPAdminify\Inc\Admin\AdminSettingsModel;

if (!defined('ABSPATH')) {
    die;
} // Cannot access directly.

class Tweaks_WP_JSON_API extends AdminSettingsModel
{
    public function __construct()
    {
        $this->tweaks_wp_json_api_settings();
    }

    public function get_defaults()
    {
        return [
            'disable_rest_api'      => false,
            'control_heartbeat_api' => false,
            'remove_api_head'       => false,
            'remove_powered'        => false,
            'remove_api_server'     => false,
            'disable_all_api'       => false,
        ];
    }


    public function tweaks_json_api_fields(&$json_api_fields)
    {

        $json_api_fields[] = array(
            'type'    => 'subheading',
            'content'   => Utils::adminfiy_help_urls(
                __('Cleanup your site from WP JSON API features.', WP_ADMINIFY_TD),
                'https://wpadminify.com/kb/wp-adminify-tweaks/',
                'https://www.youtube.com/playlist?list=PLqpMw0NsHXV-EKj9Xm1DMGa6FGniHHly8',
                'https://www.facebook.com/groups/jeweltheme',
                'https://wpadminify.com/support/'
            )
        );

        $json_api_fields[] = array(
            'id'         => 'disable_rest_api',
            'type'       => 'switcher',
            'title'      => __('Disable REST API', WP_ADMINIFY_TD),
            'text_on'    => __('Yes', WP_ADMINIFY_TD),
            'text_off'   => __('No', WP_ADMINIFY_TD),
            'text_width' => 80,
            'default'    => $this->get_default_field('disable_rest_api'),
        );

        $json_api_fields[] = array(
            'id'         => 'control_heartbeat_api',
            'type'       => 'switcher',
            'title'      => __('Control Heartbeat API', WP_ADMINIFY_TD),
            'text_on'    => __('Yes', WP_ADMINIFY_TD),
            'text_off'   => __('No', WP_ADMINIFY_TD),
            'text_width' => 80,
            'default'    => $this->get_default_field('control_heartbeat_api'),
        );

        $json_api_fields[] = array(
            'id'         => 'remove_api_head',
            'type'       => 'switcher',
            'title'      => __('Remove WP API Links and Scripts', WP_ADMINIFY_TD),
            'subtitle'   => __('Remove all WP JSON API links and scripts from head section', WP_ADMINIFY_TD),
            'label'      => __('This option does not disable WP API, just cleans head section from these links.', WP_ADMINIFY_TD),
            'text_on'    => __('Yes', WP_ADMINIFY_TD),
            'text_off'   => __('No', WP_ADMINIFY_TD),
            'text_width' => 80,
            'default'    => $this->get_default_field('remove_api_head'),
        );

        $json_api_fields[] = array(
            'id'         => 'remove_api_server',
            'type'       => 'switcher',
            'title'      => __('Remove WP API Link from HTTP Headers', WP_ADMINIFY_TD),
            'subtitle'   => __('Remove "Link:<...>; rel=https://api.w.org/" from server response HTTP headers', WP_ADMINIFY_TD),
            'label'      => __('This option does not disable WP API, just cleans HTTP headers.', WP_ADMINIFY_TD),
            'text_on'    => __('Yes', WP_ADMINIFY_TD),
            'text_off'   => __('No', WP_ADMINIFY_TD),
            'text_width' => 80,
            'default'    => $this->get_default_field('remove_api_server'),
        );

        $json_api_fields[] = array(
            'id'         => 'disable_all_api',
            'type'       => 'switcher',
            'title'      => __('Totally Disable WP API Feature', WP_ADMINIFY_TD),
            'subtitle'   => __('Disable WP JSON API functionality on your site', WP_ADMINIFY_TD),
            'label'      => __('WordPress API is used by external apps to get data from your site. If you are not using this feature you can disable this.', WP_ADMINIFY_TD),
            'text_on'    => __('Yes', WP_ADMINIFY_TD),
            'text_off'   => __('No', WP_ADMINIFY_TD),
            'text_width' => 80,
            'default'    => $this->get_default_field('disable_all_api'),
        );
    }


    public function tweaks_wp_json_api_settings()
    {

        if (!class_exists('ADMINIFY')) {
            return;
        }

        $json_api_fields = [];
        $this->tweaks_json_api_fields($json_api_fields);

        \ADMINIFY::createSection(
            $this->prefix,
            array(
                'title'       => __('WP JSON API', WP_ADMINIFY_TD),
                'parent'      => 'tweaks_performance',
                'icon'        => 'fas fa-file-code',
                'fields'      => $json_api_fields
            )
        );
    }
}

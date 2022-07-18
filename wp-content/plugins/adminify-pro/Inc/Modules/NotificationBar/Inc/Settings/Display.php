<?php

namespace WPAdminify\Inc\Modules\NotificationBar\Inc\Settings;

use WPAdminify\Inc\Modules\NotificationBar\Inc\Notification_Customize;

if (!defined('ABSPATH')) {
    die;
} // Cannot access directly.

class Display extends Notification_Customize
{
    public function __construct()
    {
        $this->display_notif_bar_customizer();
    }

    public function get_defaults()
    {
        return [
            'display_devices' => 'all',
            'display_pages'   => array('homepage', 'posts', 'pages')
        ];
    }


    /**
     * Notification Bar: Display Settings
     *
     * @param [type] $display_settings
     *
     * @return void
     */
    public function get_display_settings(&$display_settings)
    {
        $display_settings[] = array(
            'id'      => 'display_devices',
            'type'    => 'select',
            'title'   => __('Select devices want to display', WP_ADMINIFY_TD),
            'options' => array(
                'all'     => __('All Devices', WP_ADMINIFY_TD),
                'desktop' => __('Desktop', WP_ADMINIFY_TD),
                'mobile'  => __('Mobile', WP_ADMINIFY_TD),
            ),
            'default'  => $this->get_default_field('display_devices'),
        );

        $display_settings[] = array(
            'id'      => 'display_pages',
            'type'    => 'checkbox',
            'title'   => __('Where to Display', WP_ADMINIFY_TD),
            'options' => array(
                'homepage' => __('Homepage', WP_ADMINIFY_TD),
                'posts'    => __('Posts', WP_ADMINIFY_TD),
                'pages'    => __('Pages', WP_ADMINIFY_TD),
            ),
            'default'  => $this->get_default_field('display_pages'),
        );
    }

    /**
     * Notification bar: Display
     *
     * @return void
     */
    public function display_notif_bar_customizer()
    {

        $display_settings = [];
        $this->get_display_settings($display_settings);

        /**
         * Section: Display Settings
         */
        \ADMINIFY::createSection(
            $this->prefix,
            array(
                'assign' => 'display_section',
                'title'  => __('Display Settings', WP_ADMINIFY_TD),
                'fields' => $display_settings
            )
        );
    }
}

<?php

namespace WPAdminify\Inc\Modules\NotificationBar\Inc\Settings;

use WPAdminify\Inc\Utils;
use WPAdminify\Inc\Modules\NotificationBar\Inc\Notification_Customize;

if (!defined('ABSPATH')) {
    die;
} // Cannot access directly.

class General extends Notification_Customize
{
    public function __construct()
    {
        $this->general_notif_bar_customizer();
    }


    public function get_defaults()
    {
        return [
            'show_notif_bar'   => false,
            'content_align'    => 'center',
            'padding'   => array(
                'width'  => '',
                'height' => '',
                'unit'   => 'px',
            ),
            'display_position' => 'top',
            'display_type'     => 'fixed',
            'show_btn_close'   => true,
            'expires_in'       => 30,
            'close_btn_text'   => 'x',
        ];
    }


    /**
     * Notification bar: General Feilds
     *
     * @param [type] $general_fields
     *
     * @return void
     */
    public function general_fields(&$general_fields)
    {
        $general_fields[] = array(
            'id'       => 'show_notif_bar',
            'type'     => 'switcher',
            'title'    => __('Enable Notification Bar?', WP_ADMINIFY_TD),
            'text_on'  => 'Yes',
            'text_off' => 'No',
            'class'    => 'wp-adminify-cs',
            'default'  => $this->get_default_field('show_notif_bar'),
        );

        $general_fields[] = array(
            'id'      => 'content_align',
            'type'    => 'button_set',
            'title'   => __('Content Alignment', WP_ADMINIFY_TD),
            'options' => array(
                'left'   => __('Left', WP_ADMINIFY_TD),
                'center' => __('Center', WP_ADMINIFY_TD),
                'right'  => __('Right', WP_ADMINIFY_TD)
            ),
            'class'   => 'wp-adminify-cs',
            'default'    => $this->get_default_field('content_align'),
            'dependency' => array('show_notif_bar', '==', 'true', true),
        );

        $general_fields[] = array(
            'id'      => 'display_position',
            'type'    => 'button_set',
            'title'   => __('Display Position', WP_ADMINIFY_TD),
            'class'   => 'wp-adminify-cs',
            'options' => array(
                'top'    => __('Top', WP_ADMINIFY_TD),
                'bottom' => __('Bottom', WP_ADMINIFY_TD),
            ),
            'default'    => $this->get_default_field('display_position'),
            'dependency' => array('show_notif_bar', '==', 'true', true),
        );

        // $general_fields[] = array(
        //     'id'      => 'display_type',
        //     'type'    => 'button_set',
        //     'title'   => __('Position Type', WP_ADMINIFY_TD),
        //     'class'   => 'wp-adminify-cs',
        //     'options' => array(
        //         'fixed'     => __('Fixed', WP_ADMINIFY_TD),
        //         'on_scroll' => __('On Scroll', WP_ADMINIFY_TD),
        //     ),
        //     'default'    => $this->get_default_field('display_type'),
        //     'dependency' => array('show_notif_bar', '==', 'true', true),
        // );

        $general_fields[] = array(
            'id'         => 'show_btn_close',
            'type'       => 'switcher',
            'title'      => __('Show Close Button?', WP_ADMINIFY_TD),
            'text_on'    => 'Yes',
            'text_off'   => 'No',
            'class'      => 'wp-adminify-cs',
            'default'    => $this->get_default_field('show_btn_close'),
            'dependency' => array('show_notif_bar', '==', 'true', true),
        );

        $general_fields[] = array(
            'id'         => 'close_btn_text',
            'type'       => 'text',
            'class'      => 'wp-adminify-cs',
            'title'      => __('Close Button Text', WP_ADMINIFY_TD),
            'default'    => $this->get_default_field('close_btn_text'),
            'dependency' => array('show_notif_bar|show_btn_close', '==|==', 'true|true', true),
        );

        $general_fields[] = array(
            'id'         => 'expires_in',
            'type'       => 'number',
            'class'      => 'wp-adminify-cs',
            'title'      => __('Expires In', WP_ADMINIFY_TD),
            'default'    => $this->get_default_field('expires_in'),
            'dependency' => array('show_notif_bar', '==', 'true', true),
        );

        $general_fields[] = array(
            'id'         => 'padding',
            'type'       => 'dimensions',
            'class'      => 'wp-adminify-cs',
            'title'      => __('Padding', WP_ADMINIFY_TD),
            'units'      => array('px'),
            'default'    => $this->get_default_field('padding'),
            'dependency' => array('show_notif_bar', '==', 'true', true),
        );
    }

    /**
     * Notification bar: General
     *
     * @return void
     */
    public function general_notif_bar_customizer()
    {

        $general_fields = [];
        $this->general_fields($general_fields);

        // General Settings
        \ADMINIFY::createSection($this->prefix, array(
            'assign' => 'general_section',
            'title'  => __('General Settings', WP_ADMINIFY_TD),
            'fields' => $general_fields
        ));
    }
}

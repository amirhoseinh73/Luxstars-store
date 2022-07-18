<?php

namespace WPAdminify\Inc\Modules\LoginCustomizer\Inc\Settings;

use WPAdminify\Inc\Utils;
use WPAdminify\Inc\Modules\LoginCustomizer\Inc\Customize_Model;

if (!defined('ABSPATH')) {
    die;
} // Cannot access directly.

class Layout_Section extends Customize_Model
{
    public function __construct()
    {
        $this->layout_customizer();
    }

    public function get_defaults()
    {

        return [
            'alignment_login_width'      => 'fullwidth',
            'alignment_login_column'     => 'right',
            'alignment_login_horizontal' => 'center_center',
            'alignment_login_vertical'   => 'center_center',
            'alignment_login_bg'         => array(
                'background-color'              => '#009e44',
                'background-gradient-color'     => '#81d742',
                'background-gradient-direction' => '135deg'
            ),
            'alignment_login_bg_skew' => 0
        ];
    }


    public function layout_fields_settings(&$layout_fields)
    {
        $url = WP_ADMINIFY_URL . 'Inc/Modules/LoginCustomizer/assets/images/layouts/';

        $layout_fields[] = array(
            'id'      => 'alignment_login_width',
            'type'    => 'image_select',
            'title'   => __('Layout', WP_ADMINIFY_TD),
            'options' => array(
                'fullwidth'        => $url . 'width-full.png',
                'width_two_column' => $url . 'width-2column.png'
            ),
            'default' => $this->get_default_field('alignment_login_width'),
        );
        $layout_fields[] = array(
            'id'      => 'alignment_login_column',
            'type'    => 'image_select',
            'title'   => __('Column Alignment', WP_ADMINIFY_TD),
            'options' => array(
                'top'    => $url . 'column-top.png',
                'right'  => $url . 'column-right.png',
                'bottom' => $url . 'column-bottom.png',
                'left'   => $url . 'column-left.png',
            ),
            'default'    => $this->get_default_field('alignment_login_column'),
            'dependency' => array('alignment_login_width', '==', 'width_two_column'),
        );
        $layout_fields[] = array(
            'id'      => 'alignment_login_horizontal',
            'type'    => 'image_select',
            'title'   => __('Horizontal Alignment', WP_ADMINIFY_TD),
            'options' => array(
                'center_center' => $url . 'form-center.png',
                'left_center'   => $url . 'form-left-center.png',
                'right_center'  => $url . 'form-right-center.png'
            ),
            'default' => $this->get_default_field('alignment_login_horizontal'),
        );
        $layout_fields[] = array(
            'id'      => 'alignment_login_vertical',
            'type'    => 'image_select',
            'title'   => __('Vertical Alignment', WP_ADMINIFY_TD),
            'options' => array(
                'center_top'    => $url . 'form-center-top.png',
                'center_center' => $url . 'form-center-center.png',
                'center_bottom' => $url . 'form-center-bottom.png'
            ),
            'default' => $this->get_default_field('alignment_login_vertical'),
        );

        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $layout_fields[] = array(
                'id'                    => 'alignment_login_bg',
                'type'                  => 'background',
                'title'                 => __('Column Background', WP_ADMINIFY_TD),
                'background_color'      => true,
                'background_image'      => true,
                'background_position'   => true,
                'background_repeat'     => true,
                'background_attachment' => false,
                'background_size'       => true,
                'background_origin'     => false,
                'background_clip'       => false,
                'background_blend_mode' => true,
                'background_gradient'   => true,
                'default'               => $this->get_default_field('alignment_login_bg'),
                'dependency'            => array('alignment_login_width', '==', 'width_two_column'),
            );
        } else {
            $layout_fields[] = array(
                'type'       => 'notice',
                'title'      => __('Column Background', WP_ADMINIFY_TD),
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro(),
                'dependency' => array('alignment_login_width', '==', 'width_two_column'),
            );
        }


        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $layout_fields[] = array(
                'id'         => 'alignment_login_bg_skew',
                'type'       => 'slider',
                'title'      => __('Skew', WP_ADMINIFY_TD),
                'min'        => -100,
                'max'        => 100,
                'step'       => .25,
                'default'    => $this->get_default_field('alignment_login_bg_skew'),
                'dependency' => array('alignment_login_width', '==', 'width_two_column'),
            );
        } else {
            $layout_fields[] = array(
                'type'       => 'notice',
                'title'      => __('Skew', WP_ADMINIFY_TD),
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro(),
                'dependency' => array('alignment_login_width', '==', 'width_two_column'),
            );
        }
    }


    public function layout_customizer()
    {
        if (!class_exists('ADMINIFY')) {
            return;
        }

        $layout_fields = [];
        $this->layout_fields_settings($layout_fields);

        /**
         * Section: Layout Section
         */
        \ADMINIFY::createSection(
            $this->prefix,
            array(
                'assign' => 'jltwp_adminify_customizer_layout_section',
                'title'  => __('Layout', WP_ADMINIFY_TD),
                'fields' => $layout_fields
            )
        );
    }
}

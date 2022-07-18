<?php

namespace WPAdminify\Inc\Modules\LoginCustomizer\Inc\Settings;

use WPAdminify\Inc\Utils;

use WPAdminify\Inc\Modules\LoginCustomizer\Inc\Customize_Model;

if (!defined('ABSPATH')) {
    die;
} // Cannot access directly.

class Background_Section extends Customize_Model
{

    public function __construct()
    {
        $this->adminify_background_customizer();
    }

    public function get_defaults()
    {

        return [
            'jltwp_adminify_login_bg_video_type'        => 'self_hosted',
            'jltwp_adminify_login_bg_video_self_hosted' => '',
            'jltwp_adminify_login_bg_video_youtube'     => '',
            'jltwp_adminify_login_bg_video_loop'        => true,
            'jltwp_adminify_login_bg_video_poster'      => '',
            'jltwp_adminify_login_bg_slideshow'         => '',

            'jltwp_adminify_login_bg_type'      => 'color_image',
            'jltwp_adminify_login_bg_color_opt' => 'color',
            'jltwp_adminify_login_bg_color'     => array(
                'background-color'      => '',
                'background-position'   => 'center center',
                'background-repeat'     => 'repeat-x',
                'background-attachment' => 'fixed',
                'background-size'       => 'cover',
            ),
            'jltwp_adminify_login_gradient_bg' => array(
                'background-color'              => '#009e44',
                'background-gradient-color'     => '#81d742',
                'background-gradient-direction' => '135deg',
                'background-position'           => 'center center',
                'background-repeat'             => 'repeat-x',
                'background-attachment'         => 'fixed',
                'background-size'               => 'cover',
                'background-origin'             => 'border-box',
                'background-clip'               => 'padding-box',
                'background-blend-mode'         => 'normal',
            ),
        ];
    }

    /**
     * Background Settings
     */
    public function login_customizer_bg_settings(&$bg_fields)
    {
        $bg_fields[] = array(
            'id'      => 'jltwp_adminify_login_bg_type',
            'type'    => 'button_set',
            'options' => array(
                'color_image' => __('Color/Image', WP_ADMINIFY_TD),
                'video'       => __('Video', WP_ADMINIFY_TD),
                'slideshow'   => __('Slideshow', WP_ADMINIFY_TD),
            ),
            'default' => $this->get_default_field('jltwp_adminify_login_bg_type'),
        );

        $bg_fields[] = array(
            'id'      => 'jltwp_adminify_login_bg_color_opt',
            'type'    => 'button_set',
            'options' => array(
                'color'    => __('Color ', WP_ADMINIFY_TD),
                'gradient' => __('Gradient', WP_ADMINIFY_TD)
            ),
            'default'    => $this->get_default_field('jltwp_adminify_login_bg_color_opt'),
            'dependency' => array('jltwp_adminify_login_bg_type', '==', 'color_image', true),
        );
        $bg_fields[] = array(
            'id'         => 'jltwp_adminify_login_bg_color',
            'type'       => 'background',
            'title'      => 'Background',
            'default'    => $this->get_default_field('jltwp_adminify_login_bg_color'),
            'dependency' => array('jltwp_adminify_login_bg_type|jltwp_adminify_login_bg_color_opt', '==|==', 'color_image|color', true),
        );

        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $bg_fields[] = array(
                'id'                    => 'jltwp_adminify_login_gradient_bg',
                'type'                  => 'background',
                'title'                 => __('Background', WP_ADMINIFY_TD),
                'background_color'      => true,
                'background_image'      => true,
                'background_position'   => true,
                'background_repeat'     => true,
                'background_attachment' => true,
                'background_size'       => true,
                'background_origin'     => true,
                'background_clip'       => true,
                'background_blend_mode' => true,
                'background_gradient'   => true,
                'default'               => $this->get_default_field('jltwp_adminify_login_gradient_bg'),
                'dependency'            => array('jltwp_adminify_login_bg_type|jltwp_adminify_login_bg_color_opt', '==|==', 'color_image|gradient', true),
            );
        } else {
            $bg_fields[] = array(
                'type'       => 'notice',
                'title'      => __('Background', WP_ADMINIFY_TD),
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro(),
                'dependency' => array('jltwp_adminify_login_bg_type|jltwp_adminify_login_bg_color_opt', '==|==', 'color_image|gradient', true),
            );
        }

        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $bg_fields[] = array(
                'id'      => 'jltwp_adminify_login_bg_video_type',
                'type'    => 'button_set',
                'options' => array(
                    'self_hosted' => __('Self Hosted ', WP_ADMINIFY_TD),
                    'youtube'     => __('Youtube', WP_ADMINIFY_TD),
                ),
                'default'    => $this->get_default_field('jltwp_adminify_login_bg_video_type'),
                'dependency' => array('jltwp_adminify_login_bg_type', '==', 'video', true),
            );

            $bg_fields[] = array(
                'id'         => 'jltwp_adminify_login_bg_video_self_hosted',
                'type'       => 'media',
                'title'      => __('Upload Video', WP_ADMINIFY_TD),
                'library'    => 'video',
                'preview'    => true,
                'default'    => $this->get_default_field('jltwp_adminify_login_bg_video_self_hosted'),
                'dependency' => array('jltwp_adminify_login_bg_type|jltwp_adminify_login_bg_video_type', '==|==', 'video|self_hosted', true),
            );
            $bg_fields[] = array(
                'id'         => 'jltwp_adminify_login_bg_video_youtube',
                'type'       => 'text',
                'title'      => __('Youtube URL', WP_ADMINIFY_TD),
                'validate'   => 'adminify_validate_url',
                'dependency' => array('jltwp_adminify_login_bg_type|jltwp_adminify_login_bg_video_type', '==|==', 'video|youtube', true),
            );
            $bg_fields[] = array(
                'id'         => 'jltwp_adminify_login_bg_video_loop',
                'type'       => 'switcher',
                'title'      => __('Loop Video?', WP_ADMINIFY_TD),
                'text_on'    => 'Yes',
                'text_off'   => 'No',
                'default'    => $this->get_default_field('jltwp_adminify_login_bg_video_loop'),
                'class'      => 'wp-adminify-cs',
                'dependency' => array('jltwp_adminify_login_bg_type', '==', 'video', true),
            );
            $bg_fields[] = array(
                'id'         => 'jltwp_adminify_login_bg_video_poster',
                'type'       => 'media',
                'title'      => __('Poster Image', WP_ADMINIFY_TD),
                'library'    => 'image',
                'default'    => $this->get_default_field('jltwp_adminify_login_bg_video_poster'),
                'dependency' => array('jltwp_adminify_login_bg_type', '==', 'video', true),
            );
            $bg_fields[] = array(
                'id'          => 'jltwp_adminify_login_bg_slideshow',
                'type'        => 'gallery',
                'title'       => __('Slideshow Images', WP_ADMINIFY_TD),
                'add_title'   => __('Add Slide', WP_ADMINIFY_TD),
                'edit_title'  => __('Edit Slides', WP_ADMINIFY_TD),
                'clear_title' => __('Remove', WP_ADMINIFY_TD),
                'default'     => $this->get_default_field('jltwp_adminify_login_bg_slideshow'),
                'dependency'  => array('jltwp_adminify_login_bg_type', '==', 'slideshow', true),
            );
        } else {
            $bg_fields[] = array(
                'type'       => 'notice',
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro(),
                'dependency' => array('jltwp_adminify_login_bg_type', 'any', 'video,slideshow'),
            );
        }
    }


    public function adminify_background_customizer()
    {
        if (!class_exists('ADMINIFY')) {
            return;
        }

        $bg_fields = [];
        $this->login_customizer_bg_settings($bg_fields);

        /**
         * Section: Background Section
         */
        \ADMINIFY::createSection(
            $this->prefix,
            array(
                'assign' => 'jltwp_adminify_customizer_bg_section',
                'title'  => __('Background', WP_ADMINIFY_TD),
                'fields' => $bg_fields
            )
        );
    }
}

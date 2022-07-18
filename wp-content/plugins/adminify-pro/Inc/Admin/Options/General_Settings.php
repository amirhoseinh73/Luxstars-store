<?php

namespace WPAdminify\Inc\Admin\Options;

use WPAdminify\Inc\Utils;
use WPAdminify\Inc\Admin\AdminSettingsModel;


if (!defined('ABSPATH')) {
    die;
} // Cannot access directly.

class General_Settings extends AdminSettingsModel
{
    public function __construct()
    {
        $this->general_settings();
    }

    public function get_defaults()
    {
        return [
            'admin_general_google_font' => array(
                'font-family' => 'Nunito Sans',
                'font-weight' => '400',
                'type'        => 'google',
                'font-size'   => '',
                'line-height' => '',
                'color'       => '',
                'output'      => 'body'
            ),
            'admin_general_bg'          => 'color',
            'admin_general_bg_color'    => '',
            'admin_general_bg_gradient' => array(
                'background-color'              => '#0347FF',
                'background-gradient-color'     => '#fd1919',
                'background-gradient-direction' => '135deg'
            ),
            'admin_general_bg_image'             => '',
            'admin_general_bg_slideshow'         => '',
            'admin_general_bg_video_type'        => 'youtube',
            'admin_general_bg_video_self_hosted' => '',
            'admin_general_bg_video_youtube'     => '',
            'admin_general_bg_video_loop'        => true,
            'admin_general_bg_video_poster'      => '',
            'admin_glass_effect'                 => true,
            'admin_general_button_color'         => [
                'bg_color'           => '#0347FF',
                'hover_bg_color'     => '#fff',
                'text_color'         => '#fff',
                'hover_text_color'   => '#0347FF',
                'border_color'       => '#0347FF',
                'hover_border_color' => '#0347FF',
            ],
        ];
    }


    public function general_google_fonts(&$fields)
    {
        $fields[] = array(
            'type'    => 'subheading',
            'content'   => Utils::adminfiy_help_urls(
                __('Customize Settings', WP_ADMINIFY_TD),
                'https://wpadminify.com/kb/admin-customization/',
                'https://www.youtube.com/playlist?list=PLqpMw0NsHXV-EKj9Xm1DMGa6FGniHHly8',
                'https://www.facebook.com/groups/jeweltheme',
                'https://wpadminify.com/support/'
            )
        );

        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $fields[] = array(
                'id'             => 'admin_general_google_font',
                'type'           => 'typography',
                'title'          => __('Body Font', WP_ADMINIFY_TD),
                'line_height'    => true,
                'text_align'     => false,
                'text_transform' => false,
                'subset'         => false,
                'letter_spacing' => false,
                'default'        => $this->get_default_field('admin_general_google_font'),
            );
        } else {
            $fields[] =  array(
                'title'   => 'Body Font',
                'type'    => 'notice',
                'style'   => 'warning',
                'content' => Utils::adminify_upgrade_pro(),
            );
        }
    }

    public function general_gradient_bg(&$fields)
    {
        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $fields[] = array(
                'id'                    => 'admin_general_bg_gradient',
                'type'                  => 'background',
                'title'                 => __('Gradient Background', WP_ADMINIFY_TD),
                'background_color'      => true,
                'background_image'      => false,
                'background_position'   => false,
                'background_repeat'     => false,
                'background_attachment' => false,
                'background_size'       => false,
                'background_origin'     => false,
                'background_clip'       => false,
                'background_blend_mode' => false,
                'background_gradient'   => true,
                'default'               => $this->get_default_field('admin_general_bg_gradient'),
                'dependency'            => array('admin_general_bg', '==', 'gradient', true),
            );
        } else {
            $fields[] =  array(
                'title'   => '',
                'type'    => 'notice',
                'style'   => 'warning',
                'content' => Utils::adminify_upgrade_pro(),
                'dependency' => array('admin_general_bg', '==', 'gradient', true),
            );
        }
    }

    public function general_image_bg(&$fields)
    {
        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $fields[] = array(
                'id'                    => 'admin_general_bg_image',
                'type'                  => 'background',
                'title'                 => __('Background Image', WP_ADMINIFY_TD),
                'background_color'      => false,
                'background_image'      => true,
                'background_position'   => false,
                'background_repeat'     => false,
                'background_attachment' => false,
                'background_size'       => false,
                'background_origin'     => false,
                'background_clip'       => false,
                'background_blend_mode' => false,
                'background_gradient'   => false,
                'default'               => $this->get_default_field('admin_general_bg_image'),
                'dependency'            => array('admin_general_bg', '==', 'image', true),
            );
        } else {
            $fields[] =  array(
                'title'   => '',
                'type'    => 'notice',
                'style'   => 'warning',
                'content' => Utils::adminify_upgrade_pro(),
                'dependency' => array('admin_general_bg', '==', 'image', true),
            );
        }
    }


    public function general_slideshow_bg(&$fields)
    {
        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $fields[] = array(
                'id'          => 'admin_general_bg_slideshow',
                'type'        => 'gallery',
                'title'       => __('Slideshow Images', WP_ADMINIFY_TD),
                'add_title'   => __('Add Slide', WP_ADMINIFY_TD),
                'edit_title'  => __('Edit Slides', WP_ADMINIFY_TD),
                'clear_title' => __('Remove', WP_ADMINIFY_TD),
                'default'     => $this->get_default_field('admin_general_bg_slideshow'),
                'dependency'  => array('admin_general_bg', '==', 'slideshow', true),
            );
        } else {
            $fields[] =  array(
                'title'   => '',
                'type'    => 'notice',
                'style'   => 'warning',
                'content' => Utils::adminify_upgrade_pro(),
                'dependency' => array('admin_general_bg', '==', 'slideshow', true),
            );
        }
    }


    public function general_glass_effect_bg(&$fields)
    {
        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $fields[] = array(
                'id'         => 'admin_glass_effect',
                'type'       => 'switcher',
                'title'      => __('Glass Effect', WP_ADMINIFY_TD),
                'text_on'    => 'Enabled',
                'text_off'   => 'Disabled',
                'text_width' => 100,
                'default'     => $this->get_default_field('admin_glass_effect'),
            );
        } else {
            $fields[] =  array(
                'title'   => __('Glass Effect', WP_ADMINIFY_TD),
                'type'    => 'notice',
                'style'   => 'warning',
                'content' => Utils::adminify_upgrade_pro()
            );
        }
    }


    public function general_video_bg(&$fields)
    {
        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $fields[] = array(
                'id'      => 'admin_general_bg_video_type',
                'type'    => 'button_set',
                'title'   => __('Video Type', WP_ADMINIFY_TD),
                'options' => array(
                    'youtube'     => __('Youtube', WP_ADMINIFY_TD),
                    'self_hosted' => __('Self Hosted', WP_ADMINIFY_TD),
                ),
                'default'    => $this->get_default_field('admin_general_bg_video_type'),
                'dependency' => array('admin_general_bg', '==', 'video', true),
            );
            $fields[] = array(
                'id'         => 'admin_general_bg_video_self_hosted',
                'type'       => 'media',
                'title'      => __('Upload Video', WP_ADMINIFY_TD),
                'library'    => 'video',
                'preview'    => true,
                'default'    => $this->get_default_field('admin_general_bg_video_self_hosted'),
                'dependency' => array('admin_general_bg|admin_general_bg_video_type', '==|==', 'video|self_hosted', true),
            );
            $fields[] = array(
                'id'         => 'admin_general_bg_video_youtube',
                'type'       => 'text',
                'title'      => 'Youtube URL',
                'default'    => $this->get_default_field('admin_general_bg_video_youtube'),
                'dependency' => array('admin_general_bg|admin_general_bg_video_type', '==|==', 'video|youtube', true),
            );
            $fields[] = array(
                'id'       => 'admin_general_bg_video_loop',
                'type'     => 'switcher',
                'title'    => __('Loop Video?', WP_ADMINIFY_TD),
                'text_on'  => 'Yes',
                'text_off' => 'No',
                'class'    => 'wp-adminify-cs',
                'default'    => $this->get_default_field('admin_general_bg_video_loop'),
                'dependency' => array('admin_general_bg', '==', 'video', true),
            );
            $fields[] = array(
                'id'         => 'admin_general_bg_video_poster',
                'type'       => 'media',
                'title'      => __('Poster Image', WP_ADMINIFY_TD),
                'library'    => 'image',
                'default'    => $this->get_default_field('admin_general_bg_video_poster'),
                'dependency' => array('admin_general_bg', '==', 'video', true),
            );
        } else {
            $fields[] = array(
                'title'   => '',
                'type'    => 'notice',
                'style'   => 'warning',
                'content' => Utils::adminify_upgrade_pro(),
                'dependency' => array('admin_general_bg', '==', 'video', true),
            );
        }
    }


    public function general_fields(&$fields)
    {
        $fields[] = array(
            'type'    => 'subheading',
            'content' => __('Body Color', WP_ADMINIFY_TD)
        );

        $fields[] = array(
            'id'      => 'admin_general_bg',
            'type'    => 'button_set',
            'title'   => 'Background Type',
            'options' => array(
                'color'     => 'Color',
                'gradient'  => 'Gradient',
                'image'     => 'Image',
                'slideshow' => 'Slideshow',
                'video'     => 'Video',
            ),
            'default' => 'color'
        );

        $fields[] = array(
            'id'      => 'admin_general_bg_color',
            'type'    => 'color',
            'title'   => 'Background Color',
            'default' => '',
            'dependency' => array('admin_general_bg', '==', 'color', true),
        );
    }

    public function general_customization(&$fields)
    {
        $fields[] = array(
            'type'    => 'subheading',
            'content' => __('General Customization', WP_ADMINIFY_TD)
        );

        $fields[] = array(
            'id'        => 'admin_general_button_color',
            'type'      => 'color_group',
            'title'     => __('Button Color', WP_ADMINIFY_TD),
            'subtitle'  => __('Change Admin default Button Colors', WP_ADMINIFY_TD),
            'options'   => array(
                'bg_color'           => __('BG Color', WP_ADMINIFY_TD),
                'hover_bg_color'     => __('Hover BG Color', WP_ADMINIFY_TD),
                'text_color'         => __('Text Color', WP_ADMINIFY_TD),
                'hover_text_color'   => __('Hover Text Color', WP_ADMINIFY_TD),
                'border_color'       => __('Border Color', WP_ADMINIFY_TD),
                'hover_border_color' => __('Hover Border Color', WP_ADMINIFY_TD),
            ),
            'default'    => $this->get_default_field('admin_general_button_color'),
        );
    }

    public function general_settings()
    {
        if (!class_exists('ADMINIFY')) {
            return;
        }

        $fields = [];

        $this->general_google_fonts($fields);
        $this->general_fields($fields);
        $this->general_gradient_bg($fields);
        $this->general_image_bg($fields);
        $this->general_slideshow_bg($fields);
        $this->general_video_bg($fields);
        // self::general_glass_effect_bg($fields);
        $this->general_customization($fields);

        \ADMINIFY::createSection($this->prefix, array(
            'title'  => __('Customize', WP_ADMINIFY_TD),
            'icon'   => 'fas fa-fill-drip',
            'fields' => $fields
        ));
    }
}

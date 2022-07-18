<?php

namespace WPAdminify\Inc\Admin\Options;

use WPAdminify\Inc\Utils;
use WPAdminify\Inc\Admin\AdminSettingsModel;

if (!defined('ABSPATH')) {
    die;
} // Cannot access directly.



class MenuLayout extends AdminSettingsModel
{
    public function __construct()
    {
        $this->menu_layout_settings();
    }


    public function get_defaults()
    {
        return [
            'menu_layout_settings'  => [
                'layout_type'           => 'vertical',
                'menu_hover_submenu'    => 'classic',
                'active_menu_style'     => 'classic',
                'menu_mode'             => 'classic',
                'user_info'             => false,
                'user_info_content'     => 'text',
                'legacy_menu'           => false,
                'user_info_avatar'      => 'rounded',
                'horz_menu_type'        => 'both',
                'show_bloglink'         => true,
                'horz_dropdown_icon'    => true,
                'horz_toplinks'         => false,
                'horz_bubble_icon_hide' => false,
                'horz_long_menu_break'  => true,
                'menu_styles'           => [
                    'menu_typography'            => [
                        'font-family' => 'Nunito Sans',
                        'type'        => 'google'
                    ],
                    'menu_wrapper_padding'     => '',
                    'menu_vertical_padding'    => '',
                    'horz_menu_parent_padding' => '',
                    'submenu_wrapper_padding'  => '',
                    'submenu_vertical_space'   => '',
                    'parent_menu_colors'       => [
                        'wrap_bg'      => '',
                        'hover_bg'     => '',
                        'text_color'   => '',
                        'text_hover'   => '',
                        'active_color' => '',
                    ],
                    'sub_menu_colors'   => [
                        'wrap_bg'      => '',
                        'hover_bg'     => '',
                        'text_color'   => '',
                        'text_hover'   => '',
                        'active_bg'    => '',
                        'active_color' => '',
                    ],
                    'notif_colors'      => [
                        'notif_bg'    => '',
                        'notif_color' => ''
                    ]
                ],
                'user_info_style' => [
                    'info_text_color'       => '',
                    'info_text_hover_color' => '',
                    'info_text_border'      => [
                        'top'    => '',
                        'right'  => '',
                        'bottom' => '',
                        'left'   => '',
                        'style'  => 'solid',
                        'color'  => '',
                    ],
                    'info_icon_color'       => '',
                    'info_icon_hover_color' => ''
                ]
            ]

        ];
    }


    public function menu_layout_settings_tab(&$settings_tab)
    {
        $settings_tab[] = array(
            'id'          => 'layout_type',
            'type'        => 'button_set',
            'title'       => __('Menu Type', WP_ADMINIFY_TD),
            'options'     => array(
                'vertical'   => __('Vertical Menu', WP_ADMINIFY_TD),
                'horizontal' => __('Horizontal Menu', WP_ADMINIFY_TD),
            ),
            'default'        => $this->get_default_field('menu_layout_settings')['layout_type'],
        );

        $settings_tab[] = array(
            'type'       => 'notice',
            'style'      => 'warning',
            'content'    => Utils::adminify_upgrade_pro('Horizontal Menu Requires "Adminify UI" Module Enabled from "WP Adminify>Modules" list '),
            'dependency' => array('admin_ui|layout_type', '!=|==', 'true|horizontal', 'true')
        );

        $settings_tab[] = array(
            'id'          => 'menu_hover_submenu',
            'type'        => 'button_set',
            'title'       => __('Sub Menu Style', WP_ADMINIFY_TD),
            'options'     => array(
                'classic'   => __('Classic', WP_ADMINIFY_TD),
                'accordion' => __('Accordion', WP_ADMINIFY_TD),
                'toggle'    => __('Toggle', WP_ADMINIFY_TD),
            ),
            'dependency'    => array('layout_type', '==', 'vertical', 'true'),
            'default'       => $this->get_default_field('menu_layout_settings')['menu_hover_submenu'],
        );


        $settings_tab[] = array(
            'id'          => 'active_menu_style',
            'type'        => 'button_set',
            'title'       => __('Active Menu Style', WP_ADMINIFY_TD),
            'options'     => array(
                'classic'   => __('Classic', WP_ADMINIFY_TD),
                'rounded' => __('Rounded', WP_ADMINIFY_TD),
            ),
            'dependency'    => array('layout_type', '==', 'vertical', 'true'),
            'default'       => $this->get_default_field('menu_layout_settings')['active_menu_style'],
        );


        if (!jltwp_adminify()->can_use_premium_code__premium_only()) {
            $settings_tab[] = array(
                'type'       => 'notice',
                'title'      => __('Horizontal Menu', WP_ADMINIFY_TD),
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro(),
                'dependency' => array('admin_ui|layout_type', '==|==', 'true|horizontal', 'true'),
            );
        }

        $settings_tab[] = array(
            'id'          => 'menu_mode',
            'type'        => 'button_set',
            'title'       => __('Menu Mode', WP_ADMINIFY_TD),
            'options'     => array(
                'classic'   => __('Classic', WP_ADMINIFY_TD),
                'icon_menu' => __('Mini Icon', WP_ADMINIFY_TD),
                'rounded'   => __('Rounded', WP_ADMINIFY_TD),
            ),
            'default'        => $this->get_default_field('menu_layout_settings')['menu_mode'],
            'dependency' => array('layout_type', '==', 'vertical', 'true'),
        );

        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $settings_tab[] = array(
                'id'         => 'user_info',
                'type'       => 'switcher',
                'title'      => __('User Info', WP_ADMINIFY_TD),
                'label'      => __('Enable/Disable User Info on Admin Menu', WP_ADMINIFY_TD),
                'text_on'    => __('Show', WP_ADMINIFY_TD),
                'text_off'   => __('Hide', WP_ADMINIFY_TD),
                'text_width' => 100,
                'default'        => $this->get_default_field('menu_layout_settings')['user_info'],
                'dependency' => array('layout_type', '==', 'vertical', 'true'),
            );
        } else {
            $settings_tab[] = array(
                'type'       => 'notice',
                'title'      => __('User Info', WP_ADMINIFY_TD),
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro(),
                'dependency' => array('layout_type', '==', 'vertical', 'true'),
            );
        }


        $settings_tab[] = array(
            'id'      => 'user_info_content',
            'type'    => 'button_set',
            'title'   => __('Content Type', WP_ADMINIFY_TD),
            'options' => array(
                'text' => __('Text', WP_ADMINIFY_TD),
                'icon' => __('Icon', WP_ADMINIFY_TD),
            ),
            'default'        => $this->get_default_field('menu_layout_settings')['user_info_content'],
            'dependency' => array('user_info|layout_type', '==|==', 'true|vertical', 'true'),
        );
        $settings_tab[] = array(
            'id'      => 'user_info_avatar',
            'type'    => 'button_set',
            'title'   => __('Avatar Type', WP_ADMINIFY_TD),
            'options' => array(
                'rounded' => __('Rounded', WP_ADMINIFY_TD),
                'square'  => __('Square', WP_ADMINIFY_TD),
            ),
            'default'        => $this->get_default_field('menu_layout_settings')['user_info_avatar'],
            'dependency' => array('user_info|layout_type', '==|==', 'true|vertical', 'true'),
        );

        // array(
        //     'id'         => 'legacy_menu',
        //     'type'       => 'switcher',
        //     'title'      => 'Default Menu',
        //     'label'      => 'Enable for Legacy WordPress default menu',
        //     'text_on'    => 'Enabled',
        //     'text_off'   => 'Disabled',
        //     'text_width' => 100,
        //     'dependency' => array('layout_type', '==', 'vertical', 'true'),
        // ),

        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $settings_tab[] = array(
                'id'      => 'horz_menu_type',
                'type'    => 'button_set',
                'title'   => __('Menu Item Style', WP_ADMINIFY_TD),
                'options' => array(
                    'icons_only' => __('Icon Only', WP_ADMINIFY_TD),
                    'text_only'  => __('Text Only', WP_ADMINIFY_TD),
                    'both'       => __('Both', WP_ADMINIFY_TD),
                ),
                'default'        => $this->get_default_field('menu_layout_settings')['horz_menu_type'],
                'dependency' => array('admin_ui|layout_type', '==|==', 'true|horizontal', 'true'),
            );
        } else {
            $settings_tab[] = array(
                'type'       => 'notice',
                'title'      => __('Menu Item Style', WP_ADMINIFY_TD),
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro(),
                'dependency' => array('layout_type', '==', 'horizontal', 'true'),
            );
        }


        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $settings_tab[] = array(
                'id'         => 'show_bloglink',
                'type'       => 'switcher',
                'title'      => __('Show Blog Link', WP_ADMINIFY_TD),
                'label'      => __('Show Blog Site Link on beginning', WP_ADMINIFY_TD),
                'text_on'    => __('Show', WP_ADMINIFY_TD),
                'text_off'   => __('Hide', WP_ADMINIFY_TD),
                'text_width' => 100,
                'default'        => $this->get_default_field('menu_layout_settings')['show_bloglink'],
                'dependency' => array('admin_ui|layout_type', '==|==', 'true|horizontal', 'true'),
            );
        } else {
            $settings_tab[] = array(
                'type'       => 'notice',
                'title'      => __('Show Blog Link', WP_ADMINIFY_TD),
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro(),
                'dependency' => array('layout_type', '==', 'horizontal', 'true'),
            );
        }

        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $settings_tab[] = array(
                'id'         => 'horz_dropdown_icon',
                'type'       => 'switcher',
                'title'      => __('Dropdown Toggle Icon', WP_ADMINIFY_TD),
                'label'      => __('Show/Hide Dropdown Icon', WP_ADMINIFY_TD),
                'text_on'    => __('Show', WP_ADMINIFY_TD),
                'text_off'   => __('Hide', WP_ADMINIFY_TD),
                'text_width' => 100,
                'default'    => $this->get_default_field('menu_layout_settings')['horz_dropdown_icon'],
                'dependency' => array('admin_ui|layout_type', '==|==', 'true|horizontal', 'true'),
            );
        } else {
            $settings_tab[] = array(
                'type'       => 'notice',
                'title'      => __('Dropdown Toggle Icon', WP_ADMINIFY_TD),
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro(),
                'dependency' => array('layout_type', '==', 'horizontal', 'true'),
            );
        }

        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $settings_tab[] = array(
                'id'         => 'horz_toplinks',
                'type'       => 'switcher',
                'title'      => __('Top Menu Links', WP_ADMINIFY_TD),
                'label'      => __('Parent/Top Menu Links clickable', WP_ADMINIFY_TD),
                'text_on'    => __('Enabled', WP_ADMINIFY_TD),
                'text_off'   => __('Disabled', WP_ADMINIFY_TD),
                'text_width' => 100,
                'default'    => $this->get_default_field('menu_layout_settings')['horz_toplinks'],
                'dependency' => array('admin_ui|layout_type', '==|==', 'true|horizontal', 'true'),
            );
        } else {
            $settings_tab[] = array(
                'type'       => 'notice',
                'title'      => __('Top Menu Links', WP_ADMINIFY_TD),
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro(),
                'dependency' => array('layout_type', '==', 'horizontal', 'true'),
            );
        }


        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $settings_tab[] = array(
                'id'         => 'horz_bubble_icon_hide',
                'type'       => 'switcher',
                'title'      => __('Bubble Icon', WP_ADMINIFY_TD),
                'label'      => __('Show/Hide Update or Plugins Bubble Icon', WP_ADMINIFY_TD),
                'text_on'    => __('Show', WP_ADMINIFY_TD),
                'text_off'   => __('Hide', WP_ADMINIFY_TD),
                'text_width' => 100,
                'default'    => $this->get_default_field('menu_layout_settings')['horz_bubble_icon_hide'],
                'dependency' => array('admin_ui|layout_type', '==|==', 'true|horizontal', 'true'),
            );
        } else {
            $settings_tab[] = array(
                'type'       => 'notice',
                'title'      => __('Bubble Icon', WP_ADMINIFY_TD),
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro(),
                'dependency' => array('layout_type', '==', 'horizontal', 'true'),
            );
        }


        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $settings_tab[] = array(
                'id'         => 'horz_long_menu_break',
                'type'       => 'switcher',
                'title'      => __('Break Long Lists', WP_ADMINIFY_TD),
                'label'      => __('Break Menu Lines if Main menu gets longer lists and doesn\'t cover screen witdh', WP_ADMINIFY_TD),
                'text_on'    => __('Enable', WP_ADMINIFY_TD),
                'text_off'   => __('Disable', WP_ADMINIFY_TD),
                'text_width' => 100,
                'default'    => $this->get_default_field('menu_layout_settings')['horz_long_menu_break'],
                'dependency' => array('admin_ui|layout_type', '==|==', 'true|horizontal', 'true'),
            );
        } else {
            $settings_tab[] = array(
                'type'       => 'notice',
                'title'      => __('Break Long Lists', WP_ADMINIFY_TD),
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro(),
                'dependency' => array('layout_type', '==', 'horizontal', 'true'),
            );
        }
    }




    public function menu_layout_style_tab(&$menu_styles_tab)
    {
        $menu_styles_tab[] = array(
            'type'    => 'subheading',
            'content' => __('Menu Styles', WP_ADMINIFY_TD),
        );
        $menu_styles_tab[] = array(
            'id'                 => 'menu_typography',
            'type'               => 'typography',
            'title'              => __('Font Settings', WP_ADMINIFY_TD),
            'font_family'        => true,
            'font_weight'        => true,
            'font_style'         => true,
            'font_size'          => true,
            'line_height'        => true,
            'letter_spacing'     => true,
            'text_align'         => true,
            'text-transform'     => true,
            'color'              => false,
            'subset'             => false,
            'backup_font_family' => false,
            'font_variant'       => false,
            'word_spacing'       => false,
            'text_decoration'    => false,
            'default'            => $this->get_default_field('menu_layout_settings')['menu_styles']['menu_typography'],
        );

        $menu_styles_tab[] = array(
            'id'      => 'menu_wrapper_padding',
            'type'    => 'spacing',
            'title'   => __('Menu Wrapper Padding', WP_ADMINIFY_TD),
            'default' => $this->get_default_field('menu_layout_settings')['menu_styles']['menu_wrapper_padding'],
        );

        $menu_styles_tab[] = array(
            'id'         => 'menu_vertical_padding',
            'type'       => 'slider',
            'title'      => __('Parent Menu Vertical Padding', WP_ADMINIFY_TD),
            'unit'       => 'px',
            'min'        => 1,
            'max'        => 100,
            'step'       => 1,
            'default'    => $this->get_default_field('menu_layout_settings')['menu_styles']['menu_vertical_padding'],
            'dependency' => array('layout_type', '==', 'vertical', 'true'),
        );


        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $menu_styles_tab[] = array(
                'id'         => 'horz_menu_parent_padding',
                'type'       => 'slider',
                'title'      => __('Parent Menu Horizontal Padding', WP_ADMINIFY_TD),
                'unit'       => 'px',
                'min'        => 1,
                'max'        => 100,
                'step'       => 1,
                'default'    => $this->get_default_field('menu_layout_settings')['menu_styles']['horz_menu_parent_padding'],
                'dependency' => array('layout_type', '==', 'horizontal', 'true'),
            );
        } else {
            $menu_styles_tab[] = array(
                'type'       => 'notice',
                'title'    => __('Parent Menu Horizontal Padding', WP_ADMINIFY_TD),
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro(),
                'dependency' => array('layout_type', '==', 'horizontal', 'true'),
            );
        }

        $menu_styles_tab[] = array(
            'id'      => 'submenu_wrapper_padding',
            'type'    => 'spacing',
            'title'   => __('Sub Menu Wrapper Padding', WP_ADMINIFY_TD),
            'default' => $this->get_default_field('menu_layout_settings')['menu_styles']['submenu_wrapper_padding'],
        );

        $menu_styles_tab[] = array(
            'id'      => 'submenu_vertical_space',
            'type'    => 'slider',
            'title'   => __('Sub Menu Vertical Padding', WP_ADMINIFY_TD),
            'unit'    => 'px',
            'min'     => 1,
            'max'     => 100,
            'step'    => 1,
            'default' => $this->get_default_field('menu_layout_settings')['menu_styles']['submenu_vertical_space'],
        );


        $menu_styles_tab[] = array(
            'type'    => 'subheading',
            'content' => __('Color Settings', WP_ADMINIFY_TD),
        );

        $menu_styles_tab[] = array(
            'id'      => 'parent_menu_colors',
            'type'    => 'color_group',
            'title'   => __('Parent Menu Colors', WP_ADMINIFY_TD),
            'options' => array(
                'wrap_bg'      => __('Wrap BG', WP_ADMINIFY_TD),
                'hover_bg'     => __('Menu Hover BG', WP_ADMINIFY_TD),
                'text_color'   => __('Text Color', WP_ADMINIFY_TD),
                'text_hover'   => __('Text Hover', WP_ADMINIFY_TD),
                'active_bg'    => __('Active Menu BG', WP_ADMINIFY_TD),
                'active_color' => __('Active Menu Color', WP_ADMINIFY_TD),
            ),
            'default' => $this->get_default_field('menu_layout_settings')['menu_styles']['parent_menu_colors'],
        );
        $menu_styles_tab[] = array(
            'id'      => 'sub_menu_colors',
            'type'    => 'color_group',
            'title'   => __('Sub Menu Colors', WP_ADMINIFY_TD),
            'options' => array(
                'wrap_bg'      => __('Wrap BG', WP_ADMINIFY_TD),
                'hover_bg'     => __('Submenu Hover BG', WP_ADMINIFY_TD),
                'text_color'   => __('Text Color', WP_ADMINIFY_TD),
                'text_hover'   => __('Text Hover', WP_ADMINIFY_TD),
                'active_bg'    => __('Active Submenu BG', WP_ADMINIFY_TD),
                'active_color' => __('Active Submenu Color', WP_ADMINIFY_TD),
            ),
            'default' => $this->get_default_field('menu_layout_settings')['menu_styles']['sub_menu_colors'],
        );
        $menu_styles_tab[] = array(
            'id'        => 'notif_colors',
            'type'      => 'color_group',
            'title'     => __('Notification Colors', WP_ADMINIFY_TD),
            'options'   => array(
                'notif_bg'    => __('Background', WP_ADMINIFY_TD),
                'notif_color' => __('Text Color', WP_ADMINIFY_TD),
            ),
            'default' => $this->get_default_field('menu_layout_settings')['menu_styles']['notif_colors'],
        );
    }



    public function user_info_style_tab(&$user_info_styles_tab)
    {
        $user_info_styles_tab[] = array(
            'type'    => 'subheading',
            'content' => __('User Info Style', WP_ADMINIFY_TD),
        );

        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $user_info_styles_tab[] = array(
                'id'         => 'info_text_color',
                'type'       => 'color',
                'title'      => __('Link Color', WP_ADMINIFY_TD),
                'default'    => $this->get_default_field('menu_layout_settings')['user_info_style']['info_text_color'],
                'dependency' => array('user_info_content', '==', 'text', 'true'),
            );
        } else {
            $user_info_styles_tab[] = array(
                'type'       => 'notice',
                'title'      => __('Link Color', WP_ADMINIFY_TD),
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro()
            );
        }

        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $user_info_styles_tab[] = array(
                'id'         => 'info_text_hover_color',
                'type'       => 'color',
                'title'      => __('Hover Color', WP_ADMINIFY_TD),
                'default'    => $this->get_default_field('menu_layout_settings')['user_info_style']['info_text_hover_color'],
                'dependency' => array('user_info_content', '==', 'text', 'true'),
            );
        } else {
            $user_info_styles_tab[] = array(
                'type'       => 'notice',
                'title'      => __('Hover Color', WP_ADMINIFY_TD),
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro()
            );
        }

        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $user_info_styles_tab[] = array(
                'id'         => 'info_text_border',
                'type'       => 'border',
                'title'      => __('Border', WP_ADMINIFY_TD),
                'all'        => true,
                'default'    => $this->get_default_field('menu_layout_settings')['user_info_style']['info_text_border'],
                'dependency' => array('user_info_content', '==', 'text', 'true'),
            );
        } else {
            $user_info_styles_tab[] = array(
                'type'       => 'notice',
                'title'      => __('Border', WP_ADMINIFY_TD),
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro()
            );
        }


        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $user_info_styles_tab[] = array(
                'id'         => 'info_icon_color',
                'type'       => 'color',
                'title'      => __('Icon Color', WP_ADMINIFY_TD),
                'default'    => $this->get_default_field('menu_layout_settings')['user_info_style']['info_icon_color'],
                'dependency' => array('user_info_content', '==', 'icon', 'true'),
            );
        } else {
            $user_info_styles_tab[] = array(
                'type'       => 'notice',
                'title'      => __('Icon Color', WP_ADMINIFY_TD),
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro()
            );
        }


        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $user_info_styles_tab[] = array(
                'id'         => 'info_icon_hover_color',
                'type'       => 'color',
                'title'      => __('Hover Icon Color', WP_ADMINIFY_TD),
                'default'    => $this->get_default_field('menu_layout_settings')['user_info_style']['info_icon_hover_color'],
                'dependency' => array('user_info_content', '==', 'icon', 'true'),
            );
        } else {
            $user_info_styles_tab[] = array(
                'type'    => 'notice',
                'title'   => __('Hover Icon Color', WP_ADMINIFY_TD),
                'style'   => 'warning',
                'content' => Utils::adminify_upgrade_pro()
            );
        }
    }


    public function menu_styles_tab(&$styles_tab)
    {
        $menu_styles_tab = [];
        $user_info_styles_tab = [];
        $this->menu_layout_style_tab($menu_styles_tab);
        $this->user_info_style_tab($user_info_styles_tab);

        $styles_tab[] = array(
            'id'     => 'menu_styles',
            'type'   => 'fieldset',
            'title'  => '',
            'fields' => $menu_styles_tab,
        );

        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $styles_tab[] = array(
                'id'     => 'user_info_style',
                'type'   => 'fieldset',
                'title'  => '',
                'fields' => $user_info_styles_tab,
                'dependency' => array('layout_type|user_info', '==|==', 'vertical|true', 'true'),
            );
        } else {
            $styles_tab[] = array(
                'id'     => 'user_info_style',
                'type'   => 'fieldset',
                'title'  => '',
                'fields' => $user_info_styles_tab,
                'dependency' => array('layout_type', '==', 'vertical', 'true'),
            );
        }
    }


    public function menu_layout_settings()
    {

        if (!class_exists('ADMINIFY')) {
            return;
        }

        $settings_tab = [];
        $styles_tab = [];

        $this->menu_layout_settings_tab($settings_tab);
        $this->menu_styles_tab($styles_tab);

        // Menu Layout Section
        \ADMINIFY::createSection($this->prefix, array(
            'title'  => __('Menu Settings', WP_ADMINIFY_TD),
            'icon'   => 'fas fa-bars',
            'fields' => array(
                array(
                    'type'    => 'subheading',
                    'content'   => Utils::adminfiy_help_urls(
                        __('Menu Settings', WP_ADMINIFY_TD),
                        'https://wpadminify.com/kb/dashboard-menu-settings/',
                        'https://www.youtube.com/playlist?list=PLqpMw0NsHXV-EKj9Xm1DMGa6FGniHHly8',
                        'https://www.facebook.com/groups/jeweltheme',
                        'https://wpadminify.com/support/'
                    )
                ),
                array(
                    'id'    => 'menu_layout_settings',
                    'type'  => 'tabbed',
                    'title' => '',
                    'tabs'  => array(
                        array(
                            'title'  => 'Settings',
                            'fields' => $settings_tab,
                        ),
                        array(
                            'title'  => 'Styles',
                            'fields' => $styles_tab,
                        )
                    ),
                ),
            )
        ));
    }
}

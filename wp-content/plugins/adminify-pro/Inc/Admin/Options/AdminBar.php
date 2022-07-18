<?php

namespace WPAdminify\Inc\Admin\Options;

use WPAdminify\Inc\Utils;
use WPAdminify\Inc\Admin\AdminSettingsModel;

/**
 * WP Adminify
 * @package WP Admin Bar
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */

class AdminBar extends AdminSettingsModel
{
    public function __construct()
    {
        $this->admin_bar_settings();
    }

    public function get_defaults()
    {
        return [
            'admin_bar_settings'    => [
                'admin_bar_user_roles'        => [],
                'enable_admin_bar'            => true,
                'admin_bar_hide_frontend'     => 'show',
                'admin_bar_position'          => 'top',
                'admin_bar_menu'              => true,
                'admin_bar_search'            => true,
                'admin_bar_comments'          => true,
                'admin_bar_howdy_text'        => __('Howdy', WP_ADMINIFY_TD),
                'admin_bar_view_website'      => true,
                'admin_bar_dark_light_btn'    => true,
                'admin_bar_container'         => 'admin_bar_only',
                'admin_bar_light_bg'          => 'color',
                'admin_bar_light_bg_color'    => '',
                'admin_bar_light_bg_gradient' => [
                    'background-color'              => '',
                    'background-gradient-color'     => '',
                    'background-gradient-direction' => '135deg'
                ],
                'admin_bar_dark_bg'          => 'color',
                'admin_bar_dark_bg_color'    => '',
                'admin_bar_dark_bg_gradient' => [
                    'background-color'              => '',
                    'background-gradient-color'     => '',
                    'background-gradient-direction' => '135deg'
                ],
                'admin_bar_text_color' => '',
                'admin_bar_link_color' => [
                    'bg_color'    => '',
                    'link_color'  => '',
                    'hover_color' => ''
                ],
                'admin_bar_link_dropdown_color' => [
                    'wrapper_bg'  => '',
                    'bg_color'    => '',
                    'link_color'  => '',
                    'hover_color' => ''
                ],
                'admin_bar_icon_color' => ''
            ]
        ];
    }

    public function admin_bar_settings_user_roles(&$fields)
    {
        $fields[] = array(
            'id'          => 'admin_bar_user_roles',
            'type'        => 'select',
            'title'       => __('Disable for', WP_ADMINIFY_TD),
            'placeholder' => __('Select User roles you don\'t want to show', WP_ADMINIFY_TD),
            'options'     => 'roles',
            'multiple'    => true,
            'chosen'      => true,
            'default'     => $this->get_default_field('admin_bar_settings')['admin_bar_user_roles'],
        );

        $fields[] = array(
            'id'         => 'enable_admin_bar',
            'type'       => 'switcher',
            'title'      => __('Admin Bar', WP_ADMINIFY_TD),
            'label'      => __('Enable/Disable Admin Bar includes Logo, Search, Dark/Light Mode, User Info etc.', WP_ADMINIFY_TD),
            'text_on'    => __('Enabled', WP_ADMINIFY_TD),
            'text_off'   => __('Disabled', WP_ADMINIFY_TD),
            'text_width' => 100,
            'default'    => $this->get_default_field('admin_bar_settings')['enable_admin_bar'],
        );

        $fields[] = array(
            'id'      => 'admin_bar_hide_frontend',
            'type'    => 'button_set',
            'title'   => __('Frontend Admin Bar', WP_ADMINIFY_TD),
            'options' => array(
                'show' => __('Show', WP_ADMINIFY_TD),
                'hide' => __('Hide', WP_ADMINIFY_TD),
            ),
            'default'    => $this->get_default_field('admin_bar_settings')['admin_bar_hide_frontend'],
            'dependency' => array('enable_admin_bar', '==', 'true', 'true'),
        );

        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $fields[] = array(
                'id'      => 'admin_bar_position',
                'type'    => 'button_set',
                'title'   => __('Admin Bar Postion', WP_ADMINIFY_TD),
                'options' => array(
                    'top'    => __('Top', WP_ADMINIFY_TD),
                    'bottom' => __('Bottom', WP_ADMINIFY_TD),
                ),
                'default'    => $this->get_default_field('admin_bar_settings')['admin_bar_position'],
                'dependency' => array('enable_admin_bar', '==', 'true', 'true'),
            );
        } else {

            $fields[] = array(
                'id'      => 'admin_bar_position',
                'type'    => 'button_set',
                'title'   => __('Admin Bar Postion', WP_ADMINIFY_TD),
                'options' => array(
                    'top'         => __('Top', WP_ADMINIFY_TD),
                    'pro_feature' => __('Bottom', WP_ADMINIFY_TD),
                ),
                'default'    => $this->get_default_field('admin_bar_settings')['admin_bar_position'],
                'dependency' => array('enable_admin_bar', '==', 'true', 'true'),
            );
            $fields[] = array(
                'type'       => 'notice',
                'title'      => '',
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro(),
                'dependency' => array('enable_admin_bar|admin_bar_position', '==|==', 'true|pro_feature', 'true'),
            );
        }



        // array(
        //     'id'    => 'admin_bar_layout',
        //     'type'  => 'switcher',
        //     'title' => 'Default Admin bar'
        // ),

        $fields[] = array(
            'id'         => 'admin_bar_menu',
            'type'       => 'switcher',
            'title'      => __('Hide "WP Adminify" Menu', WP_ADMINIFY_TD),
            'default'    => $this->get_default_field('admin_bar_settings')['admin_bar_menu'],
            'text_on'    => __('Show', WP_ADMINIFY_TD),
            'text_off'   => __('Hide', WP_ADMINIFY_TD),
            'text_width' => '100',
            'dependency' => array('enable_admin_bar', '==', 'true', 'true'),
        );

        $fields[] = array(
            'id'         => 'admin_bar_search',
            'type'       => 'switcher',
            'title'      => __('Search Form', WP_ADMINIFY_TD),
            'default'    => $this->get_default_field('admin_bar_settings')['admin_bar_search'],
            'text_on'    => __('Show', WP_ADMINIFY_TD),
            'text_off'   => __('Hide', WP_ADMINIFY_TD),
            'text_width' => '100',
            'dependency' => array('admin_ui|enable_admin_bar', '==|==', 'true|true', 'true'),
        );

        $fields[] = array(
            'id'         => 'admin_bar_comments',
            'type'       => 'switcher',
            'title'      => __('Comments Icon', WP_ADMINIFY_TD),
            'text_on'    => __('Show', WP_ADMINIFY_TD),
            'text_off'   => __('Hide', WP_ADMINIFY_TD),
            'text_width' => '100',
            'default'    => $this->get_default_field('admin_bar_settings')['admin_bar_comments'],
            'dependency' => array('enable_admin_bar', '==', 'true', 'true'),
        );

        $fields[] = array(
            'id'         => 'admin_bar_howdy_text',
            'type'       => 'text',
            'title'      => __('"Howdy" Text', WP_ADMINIFY_TD),
            // 'text_width' => '100',
            'default'    => $this->get_default_field('admin_bar_settings')['admin_bar_howdy_text'],
            'dependency' => array('enable_admin_bar', '==', 'true', 'true'),
        );

        $fields[] = array(
            'id'         => 'admin_bar_view_website',
            'type'       => 'switcher',
            'title'      => 'View Website Icon',
            'text_on'    => __('Show', WP_ADMINIFY_TD),
            'text_off'   => __('Hide', WP_ADMINIFY_TD),
            'text_width' => '100',
            'default'    => $this->get_default_field('admin_bar_settings')['admin_bar_view_website'],
            'dependency' => array('admin_ui|enable_admin_bar', '==|==', 'true|true', 'true'),
        );
        $fields[] = array(
            'id'         => 'admin_bar_dark_light_btn',
            'type'       => 'switcher',
            'title'      => __('Light/Dark Switcher', WP_ADMINIFY_TD),
            'text_on'    => __('Show', WP_ADMINIFY_TD),
            'text_off'   => __('Hide', WP_ADMINIFY_TD),
            'text_width' => '100',
            'default'    => $this->get_default_field('admin_bar_settings')['admin_bar_dark_light_btn'],
            'dependency' => array('admin_ui|enable_admin_bar', '==|==', 'true|true', 'true'),
        );
    }



    /**
     * Style Tab Settings
     *
     * @return void
     */
    public function admin_bar_style_tab_settings(&$fields)
    {
        $fields[] = array(
            'id'          => 'admin_bar_container',
            'type'        => 'button_set',
            'title'       => __('Admin Bar Select', WP_ADMINIFY_TD),
            'description' => __('Select to change Colors of Full Container(with Admin bar and Navigation) or Admin Bar only', WP_ADMINIFY_TD),
            'options'     => array(
                'full_container' => __('Full Container', WP_ADMINIFY_TD),
                'admin_bar_only' => __('Admin Bar', WP_ADMINIFY_TD),
            ),
            'default'    => $this->get_default_field('admin_bar_settings')['admin_bar_container'],
            'dependency' => array('layout_type', '==', 'horizontal', 'true'),
        );

        if (!jltwp_adminify()->can_use_premium_code__premium_only()) {
            $fields[] = array(
                'type'       => 'notice',
                'title'      => __('', WP_ADMINIFY_TD),
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro(),
                'dependency' => array('admin_bar_container', '==', 'full_container', 'true'),
            );
        }


        $fields[] = array(
            'id'      => 'admin_bar_light_bg',
            'type'    => 'button_set',
            'title'   => __('Background', WP_ADMINIFY_TD),
            'options' => array(
                'color'    => __('Color', WP_ADMINIFY_TD),
                'gradient' => __('Gradient', WP_ADMINIFY_TD),
            ),
            'default'    => $this->get_default_field('admin_bar_settings')['admin_bar_light_bg'],
            'dependency' => array('admin_bar_mode', '==', 'light', 'true'),
        );

        $fields[] = array(
            'id'         => 'admin_bar_light_bg_color',
            'type'       => 'color',
            'title'      => __('Background Color', WP_ADMINIFY_TD),
            'default'    => $this->get_default_field('admin_bar_settings')['admin_bar_light_bg_color'],
            'dependency' => array('admin_bar_light_bg|admin_bar_mode', '==|==', 'color|light', 'true'),
        );


        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $fields[] = array(
                'id'                    => 'admin_bar_light_bg_gradient',
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
                'default'               => $this->get_default_field('admin_bar_settings')['admin_bar_light_bg_gradient'],
                'dependency'            => array('admin_bar_light_bg|admin_bar_mode', '==|==', 'gradient|light', 'true')
            );
        } else {
            $fields[] = array(
                'type'       => 'notice',
                'title'      => __('', WP_ADMINIFY_TD),
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro(),
                'dependency' => array('admin_bar_light_bg|admin_bar_mode', '==|==', 'gradient|light', 'true')
            );
        }


        // Dark Background
        $fields[] = array(
            'id'      => 'admin_bar_dark_bg',
            'type'    => 'button_set',
            'title'   => __('Background Type', WP_ADMINIFY_TD),
            'options' => array(
                'color'    => __('Color', WP_ADMINIFY_TD),
                'gradient' => __('Gradient', WP_ADMINIFY_TD),
            ),
            'default'    => $this->get_default_field('admin_bar_settings')['admin_bar_dark_bg'],
            'dependency' => array('admin_bar_mode', '==', 'dark', 'true'),
        );

        $fields[] = array(
            'id'         => 'admin_bar_dark_bg_color',
            'type'       => 'color',
            'title'      => __('Background Color', WP_ADMINIFY_TD),
            'default'    => $this->get_default_field('admin_bar_settings')['admin_bar_dark_bg_color'],
            'dependency' => array('admin_bar_dark_bg|admin_bar_mode', '==|==', 'color|dark', 'true'),
        );



        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $fields[] = array(
                'id'                    => 'admin_bar_dark_bg_gradient',
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
                'default'               => $this->get_default_field('admin_bar_settings')['admin_bar_dark_bg_gradient'],
                'dependency'            => array('admin_bar_dark_bg|admin_bar_mode', '==|==', 'gradient|dark', 'true')
            );
        } else {
            $fields[] = array(
                'type'       => 'notice',
                'title'      => __('', WP_ADMINIFY_TD),
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro(),
                'dependency' => array('admin_bar_dark_bg|admin_bar_mode', '==|==', 'gradient|dark', 'true')
            );
        }



        $fields[] = array(
            'id'      => 'admin_bar_text_color',
            'type'    => 'color',
            'title'   => __('Text Color', WP_ADMINIFY_TD),
            'default' => $this->get_default_field('admin_bar_settings')['admin_bar_text_color'],
        );

        $fields[] = array(
            'type'    => 'subheading',
            'content' => __('"New" Button Style', WP_ADMINIFY_TD)
        );
        $fields[] = array(
            'id'       => 'admin_bar_link_color',
            'type'     => 'color_group',
            'title'    => __('"New" Button color', WP_ADMINIFY_TD),
            'subtitle' => __('"New" Button Link colors active, hover, background etc ', WP_ADMINIFY_TD),
            'options'  => array(
                'bg_color'    => __('Background Color', WP_ADMINIFY_TD),
                'link_color'  => __('Text Color', WP_ADMINIFY_TD),
                'hover_color' => __('Hover Color', WP_ADMINIFY_TD),
            ),
            'default' => $this->get_default_field('admin_bar_settings')['admin_bar_link_color'],
        );
        $fields[] = array(
            'id'       => 'admin_bar_link_dropdown_color',
            'type'     => 'color_group',
            'title'    => __('"New" Dropdown', WP_ADMINIFY_TD),
            'subtitle' => __('"New" Dropdown Link colors active, hover, background etc ', WP_ADMINIFY_TD),
            'options'  => array(
                'wrapper_bg'  => __('Wrapper BG', WP_ADMINIFY_TD),
                'bg_color'    => __('Item Hover BG', WP_ADMINIFY_TD),
                'link_color'  => __('Link Color', WP_ADMINIFY_TD),
                'hover_color' => __('Hover Color', WP_ADMINIFY_TD),
            ),
            'default' => $this->get_default_field('admin_bar_settings')['admin_bar_link_dropdown_color'],
        );



        $fields[] = array(
            'type'    => 'subheading',
            'content' => __('Icon Color', WP_ADMINIFY_TD),
        );
        $fields[] = array(
            'id'      => 'admin_bar_icon_color',
            'type'    => 'color',
            'title'   => __('Color', WP_ADMINIFY_TD),
            'default' => $this->get_default_field('admin_bar_settings')['admin_bar_icon_color'],
        );
    }



    public function admin_bar_settings()
    {

        if (!class_exists('ADMINIFY')) {
            return;
        }

        $settings_tab_fields = [];
        $style_tab_fields    = [];
        $this->admin_bar_settings_user_roles($settings_tab_fields);
        $this->admin_bar_style_tab_settings($style_tab_fields);

        // Admin Bar Section
        \ADMINIFY::createSection($this->prefix, array(
            'title'  => __('Admin Bar', WP_ADMINIFY_TD),
            'icon'   => 'fas fa-user-shield',
            'fields' => array(
                array(
                    'type'    => 'subheading',
                    'content'   => Utils::adminfiy_help_urls(
                        __('Admin Bar Settings', WP_ADMINIFY_TD),
                        'https://wpadminify.com/kb/wp-admin-bar/',
                        'https://www.youtube.com/playlist?list=PLqpMw0NsHXV-EKj9Xm1DMGa6FGniHHly8',
                        'https://www.facebook.com/groups/jeweltheme',
                        'https://wpadminify.com/support/'
                    )
                ),
                array(
                    'id'    => 'admin_bar_settings',
                    'type'  => 'tabbed',
                    'title' => '',
                    'tabs'  => array(
                        array(
                            'title'  => __('Settings', WP_ADMINIFY_TD),
                            'fields' => $settings_tab_fields
                        ),
                        array(
                            'title'  => 'Styles',
                            'fields' => $style_tab_fields
                        ),
                    ),
                ),
            )
        ));
    }
}

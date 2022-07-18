<?php

namespace WPAdminify\Inc\Admin\Options;

use WPAdminify\Inc\Utils;
use WPAdminify\Inc\Admin\AdminSettingsModel;

// no direct access allowed
if (!defined('ABSPATH'))  exit;

/**
 * @package WPAdminify
 * Quick Menu
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */

class Module_QuickCircleMenu extends AdminSettingsModel
{

    public function __construct()
    {
        $this->quick_menu_settings();
    }

    public function get_defaults()
    {
        return [
            'quick_menus_user_roles' => [],
            'quick_menus'            => [
                [
                    'menu_title' => __('New Post', WP_ADMINIFY_TD),
                    'menu_link'  => [
                        'url'    => esc_url(admin_url('post-new.php')),
                        'target' => '_self'
                    ],
                    'menu_icon' => 'dashicons dashicons-edit-page'
                ],
                [
                    'menu_title' => __('New Page', WP_ADMINIFY_TD),
                    'menu_link'  => [
                        'url'    => esc_url(admin_url('post-new.php?post_type=page')),
                        'target' => '_self'
                    ],
                    'menu_icon' => 'dashicons dashicons-welcome-add-page'
                ],
                [
                    'menu_title' => __('New Media', WP_ADMINIFY_TD),
                    'menu_link'  => [
                        'url'    => esc_url(admin_url('media-new.php')),
                        'target' => '_self'
                    ],
                    'menu_icon' => 'dashicons dashicons-admin-media',
                ]
            ]
        ];
    }


    /**
     * User Roles
     *
     * @return void
     */
    public function quick_menu_user_roles(&$fields)
    {
        $fields[] = array(
            'type'    => 'subheading',
            'content'   => Utils::adminfiy_help_urls(
                __('Quick Menu Settings', WP_ADMINIFY_TD),
                'https://wpadminify.com/kb/floating-dashboard-quick-menu',
                'https://www.youtube.com/playlist?list=PLqpMw0NsHXV-EKj9Xm1DMGa6FGniHHly8',
                'https://www.facebook.com/groups/jeweltheme',
                'https://wpadminify.com/support/quick-circle-menu/'
            )
        );

        $fields[] = array(
            'id'          => 'quick_menus_user_roles',
            'type'        => 'select',
            'title'       => __('Disable for', WP_ADMINIFY_TD),
            'placeholder' => __('Select User roles you want to show', WP_ADMINIFY_TD),
            'options'     => 'roles',
            'multiple'    => true,
            'chosen'      => true,
            'default'     => $this->get_default_field('quick_menus_user_roles'),
        );
    }

    /**
     * Default Data Settings
     */
    public function quick_menu_default_settings(&$fields)
    {

        $fields[] = array(
            'type'    => 'subheading',
            'content' => __('Add/Remove Quick Menu', WP_ADMINIFY_TD),
        );

        if (jltwp_adminify()->can_use_premium_code__premium_only()) {

            $fields[] = array(
                'id'                     => 'quick_menus',
                'type'                   => 'group',
                'title'                  => '',
                'accordion_title_prefix' => __('Quick Menu: ', WP_ADMINIFY_TD),
                'accordion_title_number' => true,
                'accordion_title_auto'   => true,
                'button_title'           => __('Add New Quick Menu', WP_ADMINIFY_TD),
                'fields'                 => array(
                    array(
                        'id'    => 'menu_title',
                        'type'  => 'text',
                        'title' => __('Menu Title', WP_ADMINIFY_TD),
                    ),
                    array(
                        'id'           => 'menu_link',
                        'type'         => 'link',
                        'title'        => __('Link', WP_ADMINIFY_TD),
                        'add_title'    => __('Add Menu Link', WP_ADMINIFY_TD),
                        'edit_title'   => __('Edit Menu Link', WP_ADMINIFY_TD),
                        'remove_title' => __('Remove Menu Link', WP_ADMINIFY_TD),
                    ),
                    array(
                        'id'           => 'menu_icon',
                        'type'         => 'icon',
                        'title'        => __('Icon', WP_ADMINIFY_TD),
                        'button_title' => __('Add Menu Icon', WP_ADMINIFY_TD),
                        'remove_title' => __('Remove Menu Icon', WP_ADMINIFY_TD),
                    ),
                ),
                'default' => $this->get_default_field('quick_menus'),
            );
        } else {

            $fields[] = array(
                'id'                     => 'quick_menus',
                'type'                   => 'group',
                'title'                  => '',
                'accordion_title_prefix' => __('Quick Menu: ', WP_ADMINIFY_TD),
                'accordion_title_number' => true,
                'accordion_title_auto'   => true,
                'max'                    => 3,
                'max_text'               => __('Get <strong>Pro Version</strong> to Unlock this feature. <a href="https://wpadminify.com/pricing" target="_blank">Upgrade to Pro Now!</a>', WP_ADMINIFY_TD),
                'button_title'           => __('Add New Quick Menu', WP_ADMINIFY_TD),
                'fields'                 => array(
                    array(
                        'id'    => 'menu_title',
                        'type'  => 'text',
                        'title' => __('Menu Title', WP_ADMINIFY_TD),
                    ),
                    array(
                        'id'           => 'menu_link',
                        'type'         => 'link',
                        'title'        => __('Link', WP_ADMINIFY_TD),
                        'add_title'    => __('Add Menu Link', WP_ADMINIFY_TD),
                        'edit_title'   => __('Edit Menu Link', WP_ADMINIFY_TD),
                        'remove_title' => __('Remove Menu Link', WP_ADMINIFY_TD),
                    ),
                    array(
                        'id'           => 'menu_icon',
                        'type'         => 'icon',
                        'title'        => __('Icon', WP_ADMINIFY_TD),
                        'button_title' => __('Add Menu Icon', WP_ADMINIFY_TD),
                        'remove_title' => __('Remove Menu Icon', WP_ADMINIFY_TD),
                    ),
                ),
                'default' => $this->get_default_field('quick_menus'),
            );
        }
    }

    public function quick_menu_settings()
    {

        if (!class_exists('ADMINIFY')) {
            return;
        }

        $fields = [];
        $this->quick_menu_user_roles($fields);
        $this->quick_menu_default_settings($fields);


        // Quick Menu Section
        \ADMINIFY::createSection($this->prefix, array(
            'title'  => __('Quick Menu', WP_ADMINIFY_TD),
            'icon'   => 'fas fa-plane-departure',
            'parent' => 'module_settings',
            // 'dependency' => array('quick_menu', '==', 'true', '', 'visible'), // Section Dependency
            'fields' => $fields
        ));
    }
}

<?php

namespace WPAdminify\Inc\Modules\CustomHeaderFooter;

use WPAdminify\Inc\Utils;
// no direct access allowed
if (!defined('ABSPATH'))  exit;

/**
 * WPAdminify
 * @package Module: Dashboard Widget
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */

class CustomHeaderFooterSettings extends CustomHeaderFooterModel
{
    public function __construct()
    {
        // this should be first so the default values get stored
        $this->custom_header_footer_settings();
        parent::__construct((array) get_option($this->prefix));
    }


    public function get_defaults()
    {
        return [
            'custom_scripts' => array(
                'title'             => '',
                'display_on'        => 'full_site',
                'custom_posts'      => '',
                'custom_category'   => '',
                'custom_post_types' => '',
                'custom_pages'      => '',
                'custom_tags'       => '',
                'location'          => 'header',
                'device_type'       => 'all_devices',
                'script_type'       => 'css',
                'custom_js'         => '',
                'custom_css'        => '',
            )
        ];
    }


    /**
     * Settings Fields
     *
     * @return void
     */
    public function custom_header_footer_setting_fields(&$fields)
    {
        $fields[] = array(
            'id'      => 'title',
            'type'    => 'text',
            'title'   => __('Snippet Title', WP_ADMINIFY_TD),
            'default' => $this->get_default_field('custom_scripts')['title'],
        );

        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $fields[] = array(
                'id'          => 'display_on',
                'type'        => 'select',
                'title'       => __('Display on', WP_ADMINIFY_TD),
                'placeholder' => __('Select an option', WP_ADMINIFY_TD),
                'options'     => array(
                    'full_site'      => __('Full Site', WP_ADMINIFY_TD),
                    's_posts'        => __('Specific Posts', WP_ADMINIFY_TD),
                    's_pages'        => __('Specific Page', WP_ADMINIFY_TD),
                    's_categories'   => __('Specific Categories', WP_ADMINIFY_TD),
                    's_custom_posts' => __('Specific Post Types', WP_ADMINIFY_TD),
                    's_tags'         => __('Specific Tags', WP_ADMINIFY_TD)
                ),
                'default' => $this->get_default_field('custom_scripts')['display_on'],
            );
        } else {
            $fields[] = array(
                'id'          => 'display_on',
                'type'        => 'select',
                'title'       => __('Display on', WP_ADMINIFY_TD),
                'placeholder' => __('Select an option', WP_ADMINIFY_TD),
                'options'     => array(
                    'full_site'        => __('Full Site', WP_ADMINIFY_TD),
                    'posts_pro'        => __('Specific Posts (Pro)', WP_ADMINIFY_TD),
                    'pages_pro'        => __('Specific Page (Pro)', WP_ADMINIFY_TD),
                    'categories_pro'   => __('Specific Categories (Pro)', WP_ADMINIFY_TD),
                    'custom_posts_pro' => __('Specific Post Types (Pro)', WP_ADMINIFY_TD),
                    'tags_pro'         => __('Specific Tags (Pro)', WP_ADMINIFY_TD),
                ),
                'default' => $this->get_default_field('custom_scripts')['display_on'],
            );
        }

        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $fields[] = array(
                'id'          => 'custom_posts',
                'type'        => 'select',
                'title'       => __('Select Post', WP_ADMINIFY_TD),
                'placeholder' => __('Select a Post', WP_ADMINIFY_TD),
                'options'     => 'posts',
                'dependency'  => array('display_on', '==', 's_posts'),
                'default'     => $this->get_default_field('custom_scripts')['custom_posts'],
            );

            $fields[] = array(
                'id'          => 'custom_category',
                'type'        => 'select',
                'title'       => __('Select Category', WP_ADMINIFY_TD),
                'placeholder' => __('Select a category', WP_ADMINIFY_TD),
                'options'     => 'categories',
                'dependency'  => array('display_on', '==', 's_categories'),
                'default'     => $this->get_default_field('custom_scripts')['custom_category'],
            );

            $fields[] = array(
                'id'          => 'custom_post_types',
                'type'        => 'select',
                'title'       => __('Select Post Types', WP_ADMINIFY_TD),
                'placeholder' => __('Select a post type', WP_ADMINIFY_TD),
                'options'     => 'post_types',
                'dependency'  => array('display_on', '==', 's_custom_posts'),
                'default'     => $this->get_default_field('custom_scripts')['custom_post_types'],
            );

            $fields[] = array(
                'id'          => 'custom_pages',
                'type'        => 'select',
                'title'       => __('Select Page', WP_ADMINIFY_TD),
                'placeholder' => __('Select a Page', WP_ADMINIFY_TD),
                'options'     => 'pages',
                'dependency'  => array('display_on', '==', 's_pages'),
                'default'     => $this->get_default_field('custom_scripts')['custom_pages'],
            );

            $fields[] = array(
                'id'          => 'custom_tags',
                'type'        => 'select',
                'title'       => __('Select Tag', WP_ADMINIFY_TD),
                'placeholder' => __('Select a Tag', WP_ADMINIFY_TD),
                'options'     => 'tags',
                'dependency'  => array('display_on', '==', 's_tags'),
                'default'     => $this->get_default_field('custom_scripts')['custom_tags'],
            );
        }

        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $fields[] = array(
                'id'      => 'location',
                'type'    => 'button_set',
                'title'   => __('Location', WP_ADMINIFY_TD),
                'options' => array(
                    'header'         => __('Header', WP_ADMINIFY_TD),
                    'before_content' => __('Before Content', WP_ADMINIFY_TD),
                    'after_content'  => __('After Content', WP_ADMINIFY_TD),
                    'footer'         => __('Footer', WP_ADMINIFY_TD),
                ),
                'default' => $this->get_default_field('custom_scripts')['location'],
            );
        } else {
            $fields[] = array(
                'id'      => 'location',
                'type'    => 'button_set',
                'title'   => __('Location', WP_ADMINIFY_TD),
                'options' => array(
                    'header'             => __('Header', WP_ADMINIFY_TD),
                    'footer'             => __('Footer', WP_ADMINIFY_TD),
                    'content_before_pro' => __('Before Content (Pro)', WP_ADMINIFY_TD),
                    'content_after_pro'  => __('After Content (Pro)', WP_ADMINIFY_TD),
                ),
                'default' => $this->get_default_field('custom_scripts')['location'],
            );
        }

        if (jltwp_adminify()->can_use_premium_code__premium_only()) {

            $fields[] = array(
                'id'      => 'device_type',
                'type'    => 'button_set',
                'title'   => __('Display Device', WP_ADMINIFY_TD),
                'options' => array(
                    'all_devices' => __('Show on All Devices', WP_ADMINIFY_TD),
                    'desktop'     => __('Only Desktop', WP_ADMINIFY_TD),
                    'mobile'      => __('Only Mobile Devices', WP_ADMINIFY_TD),
                ),
                'default' => $this->get_default_field('custom_scripts')['device_type'],
            );
        } else {
            $fields[] = array(
                'id'      => 'device_type_notice',
                'type'    => 'notice',
                'title'   => __('Display Device', WP_ADMINIFY_TD),
                'style'   => 'warning',
                'content' => Utils::adminify_upgrade_pro()
            );
        }

        $fields[] = array(
            'id'      => 'script_type',
            'type'    => 'button_set',
            'title'   => __('Snippet Type', WP_ADMINIFY_TD),
            'options' => array(
                'css' => 'CSS',
                'js'  => 'JS'
            ),
            'default' => $this->get_default_field('custom_scripts')['script_type'],
        );

        $fields[] = array(
            'id'       => 'custom_js',
            'type'     => 'code_editor',
            'title'    => __('Custom JavaScript', WP_ADMINIFY_TD),
            'subtitle' => __('Add your Custom Script here', WP_ADMINIFY_TD),
            'settings' => array(
                'theme' => 'dracula',
                'mode'  => 'javascript',
            ),
            'dependency' => array('script_type', '==', 'js'),
            'default'    => $this->get_default_field('custom_scripts')['custom_js'],
        );

        $fields[] = array(
            'id'       => 'custom_css',
            'type'     => 'code_editor',
            'title'    => __('Custom CSS', WP_ADMINIFY_TD),
            'subtitle' => __('Add your Custom CSS here', WP_ADMINIFY_TD),
            'settings' => array(
                'theme' => 'mbo',
                'mode'  => 'css',
            ),
            'dependency' => array('script_type', '==', 'css'),
            'default'    => $this->get_default_field('custom_scripts')['custom_css'],
        );
    }


    public function custom_header_footer_settings()
    {
        if (!class_exists('ADMINIFY')) {
            return;
        }

        // WP Adminify Custom Header & Footer Options
        \ADMINIFY::createOptions($this->prefix, array(

            // Framework Title
            'framework_title' => 'WP Adminify Custom CSS/JS <small>by WP Adminify</small>',
            'framework_class' => 'adminify-custom-css-js',

            // menu settings
            'menu_title'      => 'Custom CSS / JS',
            'menu_slug'       => 'adminify-custom-css-js',
            'menu_type'       => 'submenu',                  // menu, submenu, options, theme, etc.
            'menu_capability' => 'manage_options',
            'menu_icon'       => '',
            'menu_position'   => 54,
            'menu_hidden'     => false,
            'menu_parent'     => 'wp-adminify-settings',

            // footer
            'footer_text'   => ' ',
            'footer_after'  => ' ',
            'footer_credit' => ' ',

            // menu extras
            'show_bar_menu'      => false,
            'show_sub_menu'      => false,
            'show_in_network'    => false,
            'show_in_customizer' => false,

            'show_search'        => false,
            'show_reset_all'     => false,
            'show_reset_section' => false,
            'show_footer'        => true,
            'show_all_options'   => true,
            'show_form_warning'  => true,
            'sticky_header'      => false,
            'save_defaults'      => true,
            'ajax_save'          => true,

            // admin bar menu settings
            'admin_bar_menu_icon'     => '',
            'admin_bar_menu_priority' => 45,


            // database model
            'database'       => 'options',   // options, transient, theme_mod, network(multisite support)
            'transient_time' => 0,


            // typography options
            'enqueue_webfont' => true,
            'async_webfont'   => false,

            // others
            'output_css' => false,

            // theme and wrapper classname
            'nav'   => 'normal',
            'theme' => 'dark',
            'class' => 'wp-adminify-custom-css-js',
        ));


        $fields = [];
        $this->custom_header_footer_setting_fields($fields);


        // Custom CSS/JS Settings
        \ADMINIFY::createSection($this->prefix, array(
            'title'  => __('Others', WP_ADMINIFY_TD),
            'icon'   => 'fas fa-bolt',
            'fields' => [
                [
                    'type'    => 'subheading',
                    'content'   => Utils::adminfiy_help_urls(
                        __('Header/Footer Snippets', WP_ADMINIFY_TD),
                        'https://wpadminify.com/kb/how-to-add-custom-css-or-js-in-full-site-or-specific-page/',
                        'https://www.youtube.com/playlist?list=PLqpMw0NsHXV-EKj9Xm1DMGa6FGniHHly8',
                        'https://www.facebook.com/groups/jeweltheme',
                        'https://wpadminify.com/support/header-footer-scripts/'
                    )
                ],
                [
                    'id'                    => 'custom_scripts',
                    'type'                  => 'group',
                    'title'                 => '',
                    'accordion_title_prefix' => __('Snippet Name: ', WP_ADMINIFY_TD),
                    'accordion_title_number' => true,
                    'accordion_title_auto'   => true,
                    'button_title'          => __('Add New Snippet', WP_ADMINIFY_TD),
                    'fields'                => $fields
                ],
            ]
        ));
    }
}

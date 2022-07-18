<?php

namespace WPAdminify\Inc\Modules\AdminPages;

// no direct access allowed
if (!defined('ABSPATH'))  exit;

/**
 * WPAdminify
 * @package Admin Pages
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */

class AdminPages_PostType
{
    public function __construct()
    {
        add_action('init', [$this, 'register_post_type']);
    }

    public function register_post_type()
    {
        $labels = array(
            'name'               => _x('Admin Pages', WP_ADMINIFY_TD),
            'singular_name'      => _x('Admin Page', WP_ADMINIFY_TD),
            'menu_name'          => _x('Admin Pages', WP_ADMINIFY_TD),
            'name_admin_bar'     => _x('Admin Page', WP_ADMINIFY_TD),
            'add_new_item'       => __('Add Admin Page', WP_ADMINIFY_TD),
            'new_item'           => __('New Admin Page', WP_ADMINIFY_TD),
            'edit_item'          => __('Edit Admin Page', WP_ADMINIFY_TD),
            'view_item'          => __('View Admin Page', WP_ADMINIFY_TD),
            'all_items'          => __('Admin Pages', WP_ADMINIFY_TD),
            'search_items'       => __('Search Admin Pages', WP_ADMINIFY_TD),
            'not_found'          => __('No Admin Pages found.', WP_ADMINIFY_TD),
            'not_found_in_trash' => __('No Admin Pages in Trash.', WP_ADMINIFY_TD),
        );

        $capabilities = array(
            'edit_post'          => apply_filters('jltwp_adminify_capability', 'manage_options'),
            'read_post'          => apply_filters('jltwp_adminify_capability', 'manage_options'),
            'delete_post'        => apply_filters('jltwp_adminify_capability', 'manage_options'),
            'delete_posts'       => apply_filters('jltwp_adminify_capability', 'manage_options'),
            'edit_posts'         => apply_filters('jltwp_adminify_capability', 'manage_options'),
            'edit_others_posts'  => apply_filters('jltwp_adminify_capability', 'manage_options'),
            'publish_posts'      => apply_filters('jltwp_adminify_capability', 'manage_options'),
            'read_private_posts' => apply_filters('jltwp_adminify_capability', 'manage_options'),
            'create_posts'       => apply_filters('jltwp_adminify_capability', 'manage_options'),
        );

        $args = array(
            'labels'              => $labels,
            'menu_icon'           => 'dashicons-format-gallery',
            'public'              => true,
            'exclude_from_search' => true,
            'show_in_menu'        => false,
            'show_in_rest'        => true,
            'query_var'           => false,
            'rewrite'             => false,
            'map_meta_cap'        => false,
            'capabilities'        => $capabilities,
            'has_archive'         => false,
            'hierarchical'        => false,
            'supports'            => array('title', 'editor'),
        );

        register_post_type('adminify_admin_page', $args);
    }
}

<?php

namespace WPAdminify\Pro\Classes;

use WPAdminify\Inc\Classes\CustomAdminColumns;

// no direct access allowed
if (!defined('ABSPATH'))  exit;

/**
 * Post Columns: Featured Image and ID
 * @package WP Adminify
 *
 * @author WP Adminify <support@wpadminify.com>
 */

class CustomColumns_Pro extends CustomAdminColumns
{
    public function __construct()
    {
        parent::__construct();
        add_action('admin_init', [$this, 'post_thumnails_column']);
        add_action('admin_init', [$this, 'adminify_post_page_id_column']);
    }

    /**
     * Featured Image: By Post ID
     *
     * @param [type] $post_id
     *
     * @return void
     */
    function adminify_admin_featured_image($post_id)
    {
        $post_thumbnail_id = get_post_thumbnail_id($post_id);
        if ($post_thumbnail_id) {
            $image = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
            return $image[0];
        }
    }


    /**
     * Post Thumbnails Column
     *
     * @return void
     */
    public function post_thumnails_column()
    {
        // If not True then return
        if (empty($this->options['post_thumb_column'])) {
            return;
        }

        // Get all the custom post types
        $post_types = get_post_types(array('public' => true), 'names', 'and');

        // Create array of allowed post types
        $post_types_with_thumbnail = array();

        // Inlcude WP default post types
        $post_types_with_thumbnail[] = 'post';
        //$post_types_with_thumbnail[] = 'page';

        foreach ($post_types as $post_type) {
            // Check if the post type supports thumbnails
            if (post_type_supports($post_type, 'thumbnail')) {
                // The include this post type to allow the image column
                $post_types_with_thumbnail[] = $post_type;
            }
        }

        // Restrict the custom column to post_types with thumbnail support
        $post_types = $post_types_with_thumbnail;

        // Exclude product post type, because WooCommerce has own thumbnail column
        if ($post_types === 'product') {
            return;
        }

        // Add custom column filter and action
        foreach ($post_types as $post_type) {
            add_filter("manage_{$post_type}_posts_columns", [$this, 'adminify_admin_column_head']);
            add_filter("manage_{$post_type}_posts_columns", [$this, 'adminify_admin_column_move']);
            add_action("manage_{$post_type}_posts_custom_column", [$this, 'adminify_admin_column_content'], 10, 2);
        }
    }

    /**
     * Column Head
     *
     * @param [type] $column
     *
     * @return void
     */
    function adminify_admin_column_head($column)
    {
        $column['featured_image'] = __('Thumbnail', WP_ADMINIFY_TD);
        return $column;
    }

    /**
     * Move Column: Before Title
     *
     * @param [type] $columns
     *
     * @return void
     */
    function adminify_admin_column_move($columns)
    {
        $new = array();
        foreach ($columns as $key => $title) {
            if ($key === 'title') {
                $new['featured_image'] = __('Thumbnail', WP_ADMINIFY_TD);
            }
            $new[$key] = $title;
        }
        return $new;
    }

    /**
     * Featured Image Content
     *
     * @param [featured_image] $column_name
     * @param [post_id] $post_id
     *
     * @return void
     */
    function adminify_admin_column_content($column_name, $post_id)
    {
        if ($column_name === 'featured_image') {
            $post_featured_image = $this->adminify_admin_featured_image($post_id);
            if ($post_featured_image) {
                echo '<img src="' . esc_url($post_featured_image) . '" />';
            } else {
                echo '<img style="width:55px;height:55px" src="' . WP_ADMINIFY_ASSETS . 'images/no-thumb.svg' . '" alt="' . __('No Thumbnail', WP_ADMINIFY_TD) . '"/>';
            }
        }
    }


    /**
     * Post/Page ID Column
     *
     * @return void
     */
    public function adminify_post_page_id_column()
    {
        // If not true then return
        if (empty($this->options['post_page_id_column'])) {
            return;
        }

        // Restrict the custom column to specific post_types
        $post_types = array('post', 'page', 'recipe', 'portfolio', 'product', 'team', 'service', 'testimonial', 'movie', 'book', 'download');
        if (empty($post_types)) {
            return;
        }
        foreach ($post_types as $post_type) {
            add_filter("manage_{$post_type}_posts_columns", [$this, 'adminify_post_page_id_column_head']);
            add_action("manage_{$post_type}_posts_custom_column", [$this, 'adminify_post_page_id_column_content'], 10, 2);
        }
    }

    function adminify_post_page_id_column_head($defaults)
    {
        $defaults['adminify_post_id'] = esc_html__('ID', WP_ADMINIFY_TD);
        return $defaults;
    }

    function adminify_post_page_id_column_content($column_name, $id)
    {
        if ($column_name === 'adminify_post_id') {
            echo esc_html($id);
        }
    }
}
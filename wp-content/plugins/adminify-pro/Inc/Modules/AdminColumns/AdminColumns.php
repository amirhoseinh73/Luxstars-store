<?php

namespace WPAdminify\Inc\Modules\AdminColumns;

use WPAdminify\Inc\Modules\AdminColumns\Lib\Adminify_Columns_Manager;
use WPAdminify\Inc\Modules\AdminColumns\Lib\Inc\Carbon_Admin_Columns_Manager;
use WPAdminify\Inc\Modules\AdminColumns\Lib\Inc\Carbon_Admin_Column;
use WPAdminify\Inc\Utils;

// no direct access allowed
if (!defined('ABSPATH'))  exit;

/**
 * WP Adminify
 * Module: Admin Columns
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */

class AdminColumns
{
    public $url;

    // Key: Unique Screen ID
    private $key;

    private $post_type;

    public $prefix;

    public function __construct()
    {
        $this->prefix = '_wpadminify_admin_columns_settings';

        if (is_admin()) {
            add_action('admin_menu', [$this, 'jltwp_adminify_admin_columns_menu'], 51);
            add_action('admin_enqueue_scripts', [$this, 'jltwp_adminify_admin_columns_enqueue_scripts'], 99);
            add_action('current_screen', [$this, 'manage_admin_columns']);
            add_filter('admin_body_class', [$this, 'jltwp_adminify_admin_columns_body_class']);
        }

        add_action('wp_ajax_adminify_admin_columns', [$this, 'handle_adminify_admin_columns']);
    }


    /**
     * Add Body Class
     */
    public function jltwp_adminify_admin_columns_body_class($classes)
    {
        $classes .= ' wp-adminify-admin-columns ';

        if (is_rtl()) {
            $classes .= ' wp-adminify-admin-columns-rtl ';
        }
        return $classes;
    }


    public function manage_admin_columns()
    {

        $screen = get_current_screen();

        $allowed_screens_base = ['edit', 'edit-tags'];

        if (!in_array($base = $screen->base, $allowed_screens_base)) return;

        new Adminify_Columns_Manager();

        if ($base == 'edit' && !empty($screen->post_type)) {
            $this->manage_post_type_admin_columns($screen->post_type);
        }

        if ($base == 'edit-tags' && !empty($screen->taxonomy)) {
            $this->manage_taxonomy_admin_columns($screen->taxonomy);
        }
    }

    public function get_width($width)
    {

        $_width = null;

        if (!empty($width_val = $width['value']) && $width_val != 'auto') {
            $_width = (int) $width_val;
            if ($width['unit'] == '%') $_width .= '%';
        }

        return $_width;
    }

    public function manage_post_type_admin_columns($post_type)
    {

        $columns                = adminify__get_post_type_all_columns( $post_type );
        $columns_keys           = array_keys($columns);
        $display_columns        = adminify_prepare_post_type_column_meta( $post_type );
        $display_column_keys    = wp_list_pluck($display_columns, 'name');
        $columns_manager        = Carbon_Admin_Columns_Manager::modify_columns('post', $post_type);

        array_unshift($display_column_keys, 'cb');
        array_unshift($display_column_keys, 'adminify_move');

        $remove_column_keys     = array_values(array_diff($columns_keys, $display_column_keys));

        // Remove Default Columns
        if (!empty($remove_column_keys)) $columns_manager->remove($remove_column_keys);

        // Modify Default Columns
        $columns = array_map(function ($column) {
            return Carbon_Admin_Column::create($column['label'])->set_name($column['name'])->set_width($this->get_width($column['width']))->set_callback('__return_false');
        }, $display_columns);

        // Add Custom Columns with callback

        // @todo implement

        $columns_manager->sort($display_column_keys)->add($columns);
    }

    public function manage_taxonomy_admin_columns($taxonomy)
    {
        $columns                = adminify__get_taxonomy_all_columns($taxonomy);
        $columns_keys           = array_keys($columns);
        $display_columns        = adminify_prepare_taxonomy_column_meta($taxonomy);
        $display_column_keys    = wp_list_pluck($display_columns, 'name');
        $remove_column_keys     = array_values(array_diff($columns_keys, $display_column_keys));
        $columns_manager        = Carbon_Admin_Columns_Manager::modify_columns('taxonomy', $taxonomy);

        // Remove Default Columns
        if (!empty($remove_column_keys)) $columns_manager->remove($remove_column_keys);

        // Modify Default Columns
        $columns = array_map(function ($column) {
            return Carbon_Admin_Column::create($column['label'])->set_name($column['name'])->set_width($this->get_width($column['width']));
        }, $display_columns);

        // Add Custom Columns with callback
        // @todo implement

        array_unshift($display_column_keys, 'cb');

        $columns_manager->sort($display_column_keys)->add($columns);
    }

    public function handle_adminify_admin_columns()
    {

        check_ajax_referer('adminify-admin-columns-secirity');

        if (!empty($_POST['route'])) {
            $route_handler = 'handle_' . $_POST['route'];
            if (is_callable(get_class($this), $route_handler)) $this->$route_handler($_POST);
        }

        wp_send_json_error(['message' => __('Something is wrong, no route found')], 400);
    }

    public function handle_get_taxonomy_data($data)
    {
        $data = adminify__get_taxonomies_columns();
        wp_send_json_success($data);
    }

    public function handle_get_post_type_data($data)
    {
        $data = adminify__get_post_types_columns();
        wp_send_json_success($data);
    }

    public function handle_save_columns_data($data)
    {

        $data = json_encode($data, JSON_UNESCAPED_SLASHES);
        $data = str_replace('\\\\', '', $data);
        $data = json_decode($data, true);

        $post_types = empty($data['post_types']) ? [] : $data['post_types'];
        $taxonomies = empty($data['taxonomies']) ? [] : $data['taxonomies'];

        foreach ($post_types as $post_type) {
            $display_columns = empty($post_type['display_columns']) ? [] : $post_type['display_columns'];
            update_option('_adminify_admin_columns_meta_' . $post_type['name'], $display_columns);
        }

        foreach ($taxonomies as $taxonomy) {
            $display_columns = empty($taxonomy['display_columns']) ? [] : $taxonomy['display_columns'];
            update_option('_adminify_admin_taxonomy_columns_meta_' . $taxonomy['name'], $display_columns);
        }

        wp_send_json_success([
            'post_types' => $post_types,
            'taxonomies' => $taxonomies
        ]);
    }


    public function jltwp_adminify_admin_columns_menu()
    {
        add_submenu_page(
            'wp-adminify-settings',
            esc_html__('Admin Columns by WP Adminify', WP_ADMINIFY_TD),
            esc_html__('Admin Columns', WP_ADMINIFY_TD),
            apply_filters('jltwp_adminify_capability', 'manage_options'),
            'adminify-admin-columns', // Page slug, will be displayed in URL
            [$this, 'jltwp_adminify_admin_columns_contents']
        );
    }


    public function jltwp_adminify_admin_columns_enqueue_scripts()
    {
        global $pagenow;

        if ('admin.php' === $pagenow && 'adminify-admin-columns' === $_GET['page']) {

            wp_enqueue_style('wp-adminify-admin-columns');

            // wp_enqueue_script('jquery');
            // wp_enqueue_script('jquery-ui-core');
            // wp_enqueue_script('jquery-ui-slider');

            $adminify_columns_data = [
                "ajax_nonce"    => wp_create_nonce('adminify-admin-columns-secirity'),
                "ajax_action"   => 'adminify_admin_columns',
                "ajaxurl"       => admin_url('admin-ajax.php'),
                "adminurl"      => admin_url(),
                "siteurl"       => home_url(),
                "is_pro"        => wp_validate_boolean(jltwp_adminify()->can_use_premium_code__premium_only()),
                'pro_notice'    => Utils::adminify_upgrade_pro()
            ];

            wp_localize_script('wp-adminify-admin-columns', 'adminify_columns_data', $adminify_columns_data);
            wp_enqueue_script('wp-adminify-admin-columns');
        }
    }


    public function jltwp_adminify_admin_columns_contents()
    {

?>

        <div class="wrap">
            <div class="wp-adminify--admin-columns--editor--container">
                <div class="wp-adminify--admin-columns--editor--settings mt-6">
                    <div id="wpadminify-admin-columns"></div>
                </div>
            </div>
        </div>

<?php
    }
}

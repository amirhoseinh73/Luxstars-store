<?php

namespace WPAdminify\Inc\Modules\AdminPages;

use WPAdminify\Inc\Classes\Icons_Library;
use WPAdminify\Inc\Modules\AdminPages\AdminPages_PostType;
use WPAdminify\Inc\Modules\AdminPages\AdminPages_MetaBoxes;
use WPAdminify\Inc\Modules\AdminPages\AdminPages_Output;

// no direct access allowed
if (!defined('ABSPATH'))  exit;

/**
 * WPAdminify
 * @package Admin Pages
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */

class AdminPages extends AdminPages_MetaBoxes
{
    protected $module_url;

    public function __construct()
    {
        $this->module_url = WP_ADMINIFY_URL . 'Inc/Modules/AdminPages';
        $this->options = (new AdminPages_MetaBoxes())->get();

        add_action('admin_menu', [$this, 'admin_pages_submenu'], 53);

        new Icons_Library();
        new AdminPages_PostType();
        new AdminPages_Output();

        add_filter('show_admin_bar', [$this, 'removeAdminBar']);
        add_action('wp_enqueue_scripts', [$this, 'addCustomScripts'], 99);
        add_filter('template_include', [$this, 'setAdminPageTemplate'], 99);

        // TO DO: Major Page builder supports.
        // add_action('elementor/init', [$this, 'jltwp_adminify_elementor_support']);
        // add_action('init', [$this, 'jltwp_adminify_beaver_support']);
        // add_action('init', [$this, 'jltwp_adminify_brizy_support']);
        // add_action('init', [$this, 'jltwp_adminify_divi_support']);
    }

    public function removeAdminBar($status)
    {
        if (!empty($_GET['bknd']) && $_GET['bknd']) return false;
        return $status;
    }

    public function addCustomScripts()
    {

        if (get_post_type() != 'adminify_admin_page') return;

        $post = get_post();

        $custom_css = get_post_meta($post->ID, '_wp_adminify_custom_css', true);
        $custom_js = get_post_meta($post->ID, '_wp_adminify_custom_js', true);

        $template_css = '
            body.single-adminify_admin_page { background: none; }
            body.single-adminify_admin_page h1.adminify-admin-page--title { font-size: 2.5em; margin: 30px 0; }
        ';

        $custom_css = $template_css . $custom_css;

        if (!empty($custom_css)) {
            printf('<style>%s</style>', $custom_css);
        }

        if (!empty($custom_js)) {
            printf('<script>%s</script>', $custom_js);
        }
    }

    public function setAdminPageTemplate($template)
    {

        global $post;

        if ($post->post_type == 'adminify_admin_page') {
            return plugin_dir_path(__FILE__) . 'AdminPageTemplate.php';
        }

        return $template;
    }

    /**
     * Submenu: Admin Pages
     *
     * @return void
     */
    public function admin_pages_submenu()
    {
        add_submenu_page(
            'wp-adminify-settings',
            esc_html__('Admin Pages by WP Adminify', WP_ADMINIFY_TD),
            esc_html__('Admin Pages', WP_ADMINIFY_TD),
            apply_filters('jltwp_adminify_capability', 'manage_options'),
            'edit.php?post_type=adminify_admin_page'
        );
    }

    /**
     * Automatically Add Elementor Support
     * for adminify_admin_page CTP
     *
     * @return void
     */
    public function jltwp_adminify_elementor_support()
    {
        // $post_types = (array)get_option('elementor_cpt_support');
        $post_types = get_option('elementor_cpt_support') ? get_option('elementor_cpt_support') : array();
        $post_types = array_merge($post_types, array('post', 'page', 'adminify_admin_page'));

        if (!in_array('adminify_admin_page', $post_types, true)) {
            array_push($post_types, 'adminify_admin_page');
            update_option('elementor_cpt_support', $post_types, true);
        }
    }

    /**
     * Automatically Add Beaver Builder Support
     * for adminify_admin_page CTP
     *
     * @return void
     */
    public function jltwp_adminify_beaver_support()
    {
        if (!class_exists('FLBuilderModel')) {
            return;
        }

        $post_types = \FLBuilderModel::get_post_types();

        if (!in_array('adminify_admin_page', $post_types, true)) {
            array_push($post_types, 'adminify_admin_page');
            \FLBuilderModel::update_admin_settings_option('_fl_builder_post_types', $post_types, true);
        }
    }


    /**
     * Automatically Add Brizy Builder Support
     * for adminify_admin_page CTP
     *
     * @return void
     */
    public function jltwp_adminify_brizy_support()
    {

        if (!class_exists('Brizy_Editor_Storage_Common')) {
            return;
        }

        $post_types = \Brizy_Editor_Storage_Common::instance()->get('post-types');

        if (!in_array('adminify_admin_page', $post_types, true)) {
            array_push($post_types, 'adminify_admin_page');
            \Brizy_Editor_Storage_Common::instance()->set('post-types', $post_types);
        }
    }


    /**
     * Automatically Add Divi Builder Support
     * for adminify_admin_page CTP
     *
     * @return void
     */
    public function jltwp_adminify_divi_support()
    {
        // Divi uses 2 option meta.
        $divi_integrations = array(
            'et_divi_builder_plugin' => 'et_pb_post_type_integration',
            'et_pb_builder_options'  => 'post_type_integration_main_et_pb_post_type_integration',
        );

        foreach ($divi_integrations as $option_name => $integration_key) {
            $options    = get_option($option_name, array());
            $post_types = isset($options[$integration_key]) ? $options[$integration_key] : array();

            if (!isset($post_types['adminify_admin_page']) || 'on' !== $post_types['adminify_admin_page']) {
                $options[$integration_key]['adminify_admin_page'] = 'on';

                update_option($option_name, $options, true);
            }
        }
    }
}

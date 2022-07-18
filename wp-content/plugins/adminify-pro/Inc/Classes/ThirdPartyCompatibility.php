<?php

namespace WPAdminify\Inc\Classes;

use WPAdminify\Inc\Utils;

// no direct access allowed
if (!defined('ABSPATH'))  exit;
/**
 * WPAdminify
 * Third Party Plugins Compatibility
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */

class ThirdPartyCompatibility
{
    public function __construct()
    {
        add_action('init', [$this, 'register_actions_on_init'], 0);
        add_action('admin_init', [$this, 'register_actions_on_admin_init'], 0);
        $this->enqueue_scripts();
    }

    public function register_actions_on_init()
    {
        // Brizy Builder
        if (isset($_REQUEST['brizy-edit-iframe'])) {
            add_filter('wp_adminify_defer_skip', '__return_true');
            add_filter('wp_adminify_skip_removing_dashicons', '__return_true');
        }
    }

    public function register_actions_on_admin_init()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts'], 999);
        add_filter('adminify_third_party_styles', [$this, 'register_compatability_styles']);

        // Fluent CRM
        add_action('fluentcrm_skip_no_conflict', '__return_true');

        // Fluent FORM
        add_action('fluentform_skip_no_conflict', '__return_true');
    }


    /**
     * Register Third Party Styles
     * @since 1.0.0
     */

    public function register_compatability_styles($plugin_supports)
    {

        if (!is_array($plugin_supports)) {
            $plugin_supports = array();
        }

        $plugin_supports['jetpack'] = WP_ADMINIFY_ASSETS . 'css/plugins/jetpack.css';
        $plugin_supports['akismet'] = WP_ADMINIFY_ASSETS . 'css/plugins/akismet.css';

        // Page Builders & Metabox
        $plugin_supports['gutenberg']               = WP_ADMINIFY_ASSETS . 'css/plugins/gutenberg.css';
        $plugin_supports['elementor']               = WP_ADMINIFY_ASSETS . 'css/plugins/elementor.css';
        $plugin_supports['siteorigin-page-builder'] = WP_ADMINIFY_ASSETS . 'css/plugins/siteorigin-page-builder.css';
        $plugin_supports['google-site-kit']         = WP_ADMINIFY_ASSETS . 'css/plugins/google-site-kit.css';
        $plugin_supports['meta-box']                = WP_ADMINIFY_ASSETS . 'css/plugins/meta-box.css';
        $plugin_supports['breeze']                  = WP_ADMINIFY_ASSETS . 'css/plugins/breeze.css';
        $plugin_supports['codepress-admin-columns'] = WP_ADMINIFY_ASSETS . 'css/plugins/codepress-admin-columns.css';
        $plugin_supports['admin-menu-editor']       = WP_ADMINIFY_ASSETS . 'css/plugins/admin-menu-editor.css';
        // $plugin_supports['admin-menu-editor-pro']      = WP_ADMINIFY_ASSETS . 'css/plugins/admin-menu-editor-pro.css';
        $plugin_supports['master-addons']           = WP_ADMINIFY_ASSETS . 'css/plugins/master-addons.css';
        $plugin_supports['advanced-custom-fields'] = WP_ADMINIFY_ASSETS . 'css/plugins/advanced-custom-fields.css';
        // $plugin_supports['advanced-custom-fields-pro'] = WP_ADMINIFY_ASSETS . 'css/plugins/advanced-custom-fields-pro.css';

        //Customizer
        $plugin_supports['loginpress']             = WP_ADMINIFY_ASSETS . 'css/plugins/loginpress.css';


        // E-Commerce
        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $plugin_supports['woocommerce']                 = WP_ADMINIFY_URL . 'Pro/assets/css/plugins/woocommerce.css';
            $plugin_supports['woofunnels-aero-checkout']    = WP_ADMINIFY_URL . 'Pro/assets/css/plugins/woofunnels-aero-checkout.css';
            $plugin_supports['brizy']                       = WP_ADMINIFY_URL . 'Pro/assets/css/plugins/brizy.css';
            $plugin_supports['beaver-builder-lite-version'] = WP_ADMINIFY_URL . 'Pro/assets/css/plugins/beaver-builder-lite-version.css';
            $plugin_supports['oxygen']                      = WP_ADMINIFY_URL . 'Pro/assets/css/plugins/oxygen.css';
            $plugin_supports['ali2woo']                     = WP_ADMINIFY_URL . 'Pro/assets/css/plugins/ali2woo.css';
            $plugin_supports['js_composer']                 = WP_ADMINIFY_URL . 'Pro/assets/css/plugins/js_composer.css';
            $plugin_supports['wpdatatables']                = WP_ADMINIFY_URL . 'Pro/assets/css/plugins/wpdatatables.css';
            $plugin_supports['wp-ultimo']                   = WP_ADMINIFY_URL . 'Pro/assets/css/plugins/wp-ultimo.css';
            $plugin_supports['gdpr-cookie-compliance']      = WP_ADMINIFY_URL . 'Pro/assets/css/plugins/gdpr-cookie-compliance.css';
            $plugin_supports['squirrly-seo']                = WP_ADMINIFY_URL . 'Pro/assets/css/plugins/squirrly-seo.css';
            $plugin_supports['squirrly-seo']                = WP_ADMINIFY_URL . 'Pro/assets/css/plugins/squirrly-seo.css';
            $plugin_supports['fluentform']                  = WP_ADMINIFY_URL . 'Pro/assets/css/plugins/fluentform.css';
            $plugin_supports['fluent-crm']                  = WP_ADMINIFY_URL . 'Pro/assets/css/plugins/fluent-crm.css';
        }
        // $plugin_supports['easy-digital-downloads'] = WP_ADMINIFY_ASSETS . 'css/plugins/easy-digital-downloads.css';

        // Security & Performance Plugins
        $plugin_supports['wordfence']              = WP_ADMINIFY_ASSETS . 'css/plugins/wordfence.css';
        $plugin_supports['litespeed-cache']        = WP_ADMINIFY_ASSETS . 'css/plugins/litespeed-cache.css';
        $plugin_supports['w3-total-cache']         = WP_ADMINIFY_ASSETS . 'css/plugins/w3-total-cache.css';
        $plugin_supports['wp-super-cache']         = WP_ADMINIFY_ASSETS . 'css/plugins/wp-super-cache.css';
        $plugin_supports['limit-login-attempts']   = WP_ADMINIFY_ASSETS . 'css/plugins/limit-login-attempts.css';
        $plugin_supports['autoptimize']            = WP_ADMINIFY_ASSETS . 'css/plugins/autoptimize.css';
        $plugin_supports['disable-comments']       = WP_ADMINIFY_ASSETS . 'css/plugins/disable-comments.css';
        $plugin_supports['better-search-replace']  = WP_ADMINIFY_ASSETS . 'css/plugins/better-search-replace.css';
        $plugin_supports['better-wp-security']     = WP_ADMINIFY_ASSETS . 'css/plugins/better-wp-security.css';
        $plugin_supports['wp-rocket']              = WP_ADMINIFY_ASSETS . 'css/plugins/wp-rocket.css';
        $plugin_supports['hide-my-wp']             = WP_ADMINIFY_ASSETS . 'css/plugins/hide-my-wp.css';
        $plugin_supports['swift-performance-lite'] = WP_ADMINIFY_ASSETS . 'css/plugins/swift-performance-lite.css';


        // Forum & Social Plugins
        $plugin_supports['bbpress']        = WP_ADMINIFY_ASSETS . 'css/plugins/bbpress.css';
        $plugin_supports['buddy-press']    = WP_ADMINIFY_ASSETS . 'css/plugins/buddy-press.css';
        $plugin_supports['instagram-feed'] = WP_ADMINIFY_ASSETS . 'css/plugins/instagram-feed.css';


        // Forms Plugins
        $plugin_supports['contact-form-7']          = WP_ADMINIFY_ASSETS . 'css/plugins/contact-form-7.css';
        $plugin_supports['forminator-contact-form'] = WP_ADMINIFY_ASSETS . 'css/plugins/forminator-contact-form.css';
        $plugin_supports['wpforms-lite']            = WP_ADMINIFY_ASSETS . 'css/plugins/wpforms.css';
        $plugin_supports['wpforms']                 = WP_ADMINIFY_ASSETS . 'css/plugins/wpforms.css';
        $plugin_supports['weforms']                 = WP_ADMINIFY_ASSETS . 'css/plugins/weforms.css';
        $plugin_supports['gravityforms']            = WP_ADMINIFY_ASSETS . 'css/plugins/gravityforms.css';
        $plugin_supports['aio-contact-lite']        = WP_ADMINIFY_ASSETS . 'css/plugins/aio-contact-lite.css';

        // Themes


        // SEO Plugins
        $plugin_supports['seo-by-rank-math']               = WP_ADMINIFY_ASSETS . 'css/plugins/seo-by-rank-math.css';
        $plugin_supports['yoast-seo']                      = WP_ADMINIFY_ASSETS . 'css/plugins/wordpress-seo.css';
        $plugin_supports['yoast-duplicate']                = WP_ADMINIFY_ASSETS . 'css/plugins/yoast-duplicate.css';
        $plugin_supports['redirection']                    = WP_ADMINIFY_ASSETS . 'css/plugins/redirection.css';
        $plugin_supports['wordpress-seo']                  = WP_ADMINIFY_ASSETS . 'css/plugins/wordpress-seo.css';
        $plugin_supports['wp-seopress']                    = WP_ADMINIFY_ASSETS . 'css/plugins/wp-seopress.css';
        $plugin_supports['all-in-one-seo-pack']            = WP_ADMINIFY_ASSETS . 'css/plugins/all-in-one-seo-pack.css';
        $plugin_supports['xml-sitemap']                    = WP_ADMINIFY_ASSETS . 'css/plugins/xml-sitemap.css';
        $plugin_supports['duplicate-page']                 = WP_ADMINIFY_ASSETS . 'css/plugins/duplicate-page.css';
        $plugin_supports['google-analytics-for-wordpress'] = WP_ADMINIFY_ASSETS . 'css/plugins/monster-insights-for-google-analytics.css';


        // Backup Pluins
        $plugin_supports['updrafts-backup']         = WP_ADMINIFY_ASSETS . 'css/plugins/updrafts-backup.css';
        $plugin_supports['updraftplus']             = WP_ADMINIFY_ASSETS . 'css/plugins/updraftplus.css';
        $plugin_supports['all-in-one-wp-migration'] = WP_ADMINIFY_ASSETS . 'css/plugins/all-in-one-wp-migration.css';
        $plugin_supports['wp-rocket']               = WP_ADMINIFY_ASSETS . 'css/plugins/wp-rocket.css';
        $plugin_supports['duplicate-wp-migration']  = WP_ADMINIFY_ASSETS . 'css/plugins/duplicate-wp-migration.css';
        $plugin_supports['backup']                  = WP_ADMINIFY_ASSETS . 'css/plugins/backup.css';

        // Mailing Service
        $plugin_supports['mailpoet']        = WP_ADMINIFY_ASSETS . 'css/plugins/mailpoet.css';
        $plugin_supports['mc4wp-mailchimp'] = WP_ADMINIFY_ASSETS . 'css/plugins/mc4wp-mailchimp.css';

        // Icons
        $plugin_supports['font-awesome'] = WP_ADMINIFY_ASSETS . 'css/plugins/font-awesome.css';

        // Blog / Post Related
        $plugin_supports['fakerpress'] = WP_ADMINIFY_ASSETS . 'css/plugins/fakerpress.css';
        $plugin_supports['post-types-order'] = WP_ADMINIFY_ASSETS . 'css/plugins/post-types-order.css';

        // Others
        $plugin_supports['modern-events-calendar-lite'] = WP_ADMINIFY_ASSETS . 'css/plugins/modern-events-calendar-lite.css';
        $plugin_supports['sfwd-lms']                    = WP_ADMINIFY_ASSETS . 'css/plugins/sfwd-lms.css';
        // $plugin_supports['visualizer']                  = WP_ADMINIFY_ASSETS . 'css/plugins/visualizer.css';
        // $plugin_supports['wpdatatables']                = WP_ADMINIFY_ASSETS . 'css/plugins/wpdatatables.css';
        $plugin_supports['cookie-notice']        = WP_ADMINIFY_ASSETS . 'css/plugins/cookie-notice.css';
        $plugin_supports['loco-translate']       = WP_ADMINIFY_ASSETS . 'css/plugins/loco-translate.css';
        $plugin_supports['insert-header-footer'] = WP_ADMINIFY_ASSETS . 'css/plugins/insert-header-footer.css';
        $plugin_supports['custom-post-type-ui'] = WP_ADMINIFY_ASSETS . 'css/plugins/custom-post-type-ui.css';
        $plugin_supports['notificationx'] = WP_ADMINIFY_ASSETS . 'css/plugins/notificationx.css';
        $plugin_supports['antispam-bee'] = WP_ADMINIFY_ASSETS . 'css/plugins/antispam-bee.css';
        $plugin_supports['filebird'] = WP_ADMINIFY_ASSETS . 'css/plugins/filebird.css';
        $plugin_supports['folders'] = WP_ADMINIFY_ASSETS . 'css/plugins/folders.css';
        $plugin_supports['media-library-plus'] = WP_ADMINIFY_ASSETS . 'css/plugins/media-library-plus.css';
        $plugin_supports['wp-media-folders'] = WP_ADMINIFY_ASSETS . 'css/plugins/wp-media-folders.css';
        $plugin_supports['wicked-folders'] = WP_ADMINIFY_ASSETS . 'css/plugins/wicked-folders.css';
        $plugin_supports['real-media-library-lite'] = WP_ADMINIFY_ASSETS . 'css/plugins/real-media-library-lite.css';
        $plugin_supports['real-category-library-lite'] = WP_ADMINIFY_ASSETS . 'css/plugins/real-category-library-lite.css';


        return $plugin_supports;
    }


    /**
     * Admin Enqueue Third Party Scripts/Styles
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        $plugin_supports = array();
        $plugin_supports = apply_filters('adminify_third_party_styles', $plugin_supports);

        if (is_multisite()) {
            $active_plugins = get_site_option('active_sitewide_plugins');
            foreach ($active_plugins as $active_path => $active_plugin) {
                if (is_plugin_active_for_network($active_path)) {

                    $string = explode('/', $active_path);
                    $pluginname = $string[0];
                    if (isset($plugin_supports[$pluginname])) {
                        if ($plugin_supports[$pluginname] != "") {
                            wp_register_style('wp-adminify_' . $pluginname . '_css', $plugin_supports[$pluginname], array(), WP_ADMINIFY_VER);
                            wp_enqueue_style('wp-adminify_' . $pluginname . '_css');
                        }
                    }
                }
            }
        } else {

            $activeplugins = get_option('active_plugins');

            foreach ($activeplugins as $plugin) {
                $string = explode('/', $plugin);
                $pluginname = $string[0];
                if (isset($plugin_supports[$pluginname])) {
                    if ($plugin_supports[$pluginname] != "") {
                        wp_register_style('wp-adminify_' . $pluginname . '_css', $plugin_supports[$pluginname], array(), WP_ADMINIFY_VER);
                        wp_enqueue_style('wp-adminify_' . $pluginname . '_css');
                    }
                }
            }
        }
    }
}

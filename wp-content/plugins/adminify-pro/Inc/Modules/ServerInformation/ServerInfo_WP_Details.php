<?php

namespace WPAdminify\Inc\Modules\ServerInformation;

use WPAdminify\Inc\Classes\ServerInfo;
use WPAdminify\Inc\Utils;

// no direct access allowed
if (!defined('ABSPATH'))  exit;

/**
 * WPAdminify
 * @package Server Information
 *
 * @author WP Adminify <support@wpadminify.com>
 */

class ServerInfo_WP_Details
{

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {


        $server_info = new ServerInfo();

        $help = '<span class="dashicons dashicons-editor-help"></span>';
        $enabled = '<span class="adminify-compability enable"><span class="dashicons dashicons-yes"></span> ' . esc_html__('Enabled', WP_ADMINIFY_TD) . '</span>';
        $disabled = '<span class="adminify-compability disable"><span class="dashicons dashicons-no"></span> ' . esc_html__('Disabled', WP_ADMINIFY_TD) . '</span>';
        $yes = '<span class="adminify-compability enable"><span class="dashicons dashicons-yes"></span> ' . esc_html__('Yes', WP_ADMINIFY_TD) . '</span>';
        $no = '<span class="adminify-compability disable"><span class="dashicons dashicons-no"></span> ' . esc_html__('No', WP_ADMINIFY_TD) . '</span>';
        $entered = '<span class="adminify-compability enable"><span class="dashicons dashicons-yes"></span> ' . esc_html__('Defined', WP_ADMINIFY_TD) . '</span>';
        $not_entered = '<span class="adminify-compability disable"><span class="dashicons dashicons-no"></span> ' . esc_html__('Not defined', WP_ADMINIFY_TD) . '</span>';
        $sec_key = '<span class="error"><span class="dashicons dashicons-warning"></span> ' . esc_html__('Please enter this security key in the wp-confiq.php file', WP_ADMINIFY_TD) . '!</span>';
?>

        <div class="wrap">
            <h1>
                <?php echo Utils::admin_page_title(esc_html__('Site Information', WP_ADMINIFY_TD)); ?>
            </h1>
        </div>


        <p><?php echo __('First, you can see the most important information about your WordPress installation at a glance. Learn more about the <a href="https://wordpress.org/about/requirements/" target="_blank" rel="noopener">requirements</a>', WP_ADMINIFY_TD); ?>.</p>
        </br>

        <table class="wp-list-table widefat posts mt-5">
            <thead>
                <tr>
                    <th width="35%" class="manage-column"><?php esc_html_e('Info', WP_ADMINIFY_TD); ?></th>
                    <th class="manage-column"><?php esc_html_e('Result', WP_ADMINIFY_TD); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width="35%"><?php esc_html_e('WP Version', WP_ADMINIFY_TD); ?>:</td>
                    <td><strong><?php bloginfo('version'); ?></strong></td>
                </tr>
                <tr>
                    <td><?php esc_html_e('PHP Version', WP_ADMINIFY_TD); ?>:</td>
                    <td><?php echo $server_info->get_php_version(); ?></td>
                </tr>
                <tr>
                    <td><?php esc_html_e('MySQL Version', WP_ADMINIFY_TD); ?>:</td>
                    <td><?php echo $server_info->get_mysql_version(); ?></td>
                </tr>
                <tr>
                    <td><?php esc_html_e('PHP Memory WP-Limit', WP_ADMINIFY_TD); ?>:</td>
                    <td><?php
                        $memory = $server_info->convert_memory_size(WP_MEMORY_LIMIT);

                        if ($memory < 67108864) {
                            echo '<span class="warning"><span class="dashicons dashicons-warning"></span> ' . sprintf(__('%s - For better performance, we recommend setting memory to at least 64MB. See: %s', WP_ADMINIFY_TD), size_format($memory), '<a href="https://wordpress.org/support/article/editing-wp-config-php/#increasing-memory-allocated-to-php" target="_blank" rel="noopener">' . __('Increasing memory allocated to PHP', WP_ADMINIFY_TD) . '</a>') . '</span>';
                        } else {
                            echo '<strong>' . size_format($memory) . '</strong>';
                        } ?>
                    </td>
                </tr>
                <tr>
                    <td><?php esc_html_e('PHP Memory Server-Limit', WP_ADMINIFY_TD); ?>:</td>
                    <td>
                        <?php
                        if (function_exists('memory_get_usage')) {
                            $system_memory = $server_info->convert_memory_size(@ini_get('memory_limit'));
                            $memory = max($memory, $system_memory);
                        }

                        if ($memory < 67108864) {
                            echo '<span class="warning"><span class="dashicons dashicons-warning"></span> ' . sprintf(__('%s - For better performance, we recommend setting memory to at least 64MB. See: %s', WP_ADMINIFY_TD), size_format($memory), '<a href="https://wordpress.org/support/article/editing-wp-config-php/#increasing-memory-allocated-to-php" target="_blank" rel="noopener">' . __('Increasing memory allocated to PHP', WP_ADMINIFY_TD) . '</a>') . '</span>';
                        } else {
                            echo '<strong>' . size_format($memory) . '</strong>';
                        } ?>
                    </td>
                </tr>
                <tr>
                    <td><?php esc_html_e('PHP Memory WP-Usage', WP_ADMINIFY_TD); ?>:</td>
                    <td>
                        <?php if ($server_info->get_wp_memory_usage()['MemLimitGet'] == '-1') {
                            echo $server_info->get_wp_memory_usage()['MemUsageFormat'] . ' ' . esc_html__('of', WP_ADMINIFY_TD) . ' ' . esc_html__('Unlimited', WP_ADMINIFY_TD) . ' (-1)';
                        } else {
                            echo $server_info->get_wp_memory_usage()['MemUsageFormat'] . ' ' . esc_html__('of', WP_ADMINIFY_TD) . ' ' . $server_info->get_wp_memory_usage()['MemLimitFormat'];
                        ?>

                            <div class="adminify-system-progress">
                                <div class="status-progressbar">
                                    <span><?php echo $server_info->get_wp_memory_usage()['MemUsageCalc'] . '% '; ?></span>
                                    <div style="width: <?php echo $server_info->get_wp_memory_usage()['MemUsageCalc']; ?>%"></div>
                                </div>
                            </div>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td><?php esc_html_e('PHP Memory Server-Usage', WP_ADMINIFY_TD); ?>:</td>
                    <td>
                        <?php if ($server_info->get_server_memory_usage()['MemLimitGet'] == '-1') {
                            echo $server_info->get_server_memory_usage()['MemUsageFormat'] . ' ' . esc_html__('of', WP_ADMINIFY_TD) . ' ' . esc_html__('Unlimited', WP_ADMINIFY_TD) . ' (-1)';
                        } else {
                            echo $server_info->get_server_memory_usage()['MemUsageFormat'] . ' ' . esc_html__('of', WP_ADMINIFY_TD) . ' ' . $server_info->get_server_memory_usage()['MemLimitFormat'];
                        ?>
                            <div class="adminify-system-progress">
                                <div class="status-progressbar">
                                    <span><?php echo $server_info->get_server_memory_usage()['MemUsageCalc'] . '% '; ?></span>
                                    <div style="width: <?php echo $server_info->get_server_memory_usage()['MemUsageCalc']; ?>%"></div>
                                </div>
                            </div>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td><?php esc_html_e('PHP Max Upload Size (WP)', WP_ADMINIFY_TD); ?>:</td>
                    <td><?php echo (int)ini_get('upload_max_filesize') . ' MB (' . size_format(wp_max_upload_size()) . ')'; ?></td>
                </tr>
                <tr>
                    <td><?php esc_html_e('WP Home URL', WP_ADMINIFY_TD); ?>:</td>
                    <td><?php echo get_home_url(); ?></td>
                </tr>
                <tr>
                    <td><?php esc_html_e('WP Site URL', WP_ADMINIFY_TD); ?>:</td>
                    <td><?php echo get_site_url(); ?></td>
                </tr>
                <tr>
                    <td><?php esc_html_e('Document Root', WP_ADMINIFY_TD); ?>:</td>
                    <td><?php echo get_home_path(); ?></td>
                </tr>
            </tbody>
        </table>

        <h2 class="pt-5"><?php echo esc_html__('Current Theme', WP_ADMINIFY_TD); ?></h2>

        <table class="wp-list-table widefat posts">
            <thead>
                <tr>
                    <th width="35%" class="manage-column"><?php echo esc_html__('Info', WP_ADMINIFY_TD); ?></th>
                    <th class="manage-column"><?php echo esc_html__('Result', WP_ADMINIFY_TD); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php include_once(ABSPATH . 'wp-admin/includes/theme-install.php');
                $active_theme = wp_get_theme();
                $theme_version = $active_theme->Version; ?>
                <tr>
                    <td><?php esc_html_e('Name', WP_ADMINIFY_TD); ?>:</td>
                    <td><?php echo esc_html($active_theme->Name); ?></td>
                </tr>
                <tr>
                    <td><?php esc_html_e('Version', WP_ADMINIFY_TD); ?>:</td>
                    <td>
                        <?php echo esc_html($theme_version); ?>
                    </td>
                </tr>
                <tr>
                    <td><?php esc_html_e('Author URL', WP_ADMINIFY_TD); ?>:</td>
                    <td><?php echo $active_theme->{'Author URI'}; ?></td>
                </tr>
                <tr>
                    <td><?php esc_html_e('Image Sizes', WP_ADMINIFY_TD); ?>:</td>
                    <td><?php echo implode(', ', get_intermediate_image_sizes()); ?></td>
                </tr>
                <tr>
                    <td><?php esc_html_e('WooCommerce Compatibility', WP_ADMINIFY_TD); ?>:</td>
                    <td>
                        <?php if (current_theme_supports('woocommerce')) {
                            echo $yes;
                        } else {
                            echo $no;
                        } ?>
                    </td>
                </tr>
                <tr>
                    <td><?php esc_html_e('Child Theme', WP_ADMINIFY_TD); ?>: <a href="https://developer.wordpress.org/themes/advanced-topics/child-themes/" target="_blank" rel="noopener"><?php echo $help; ?></a></td>
                    <td>
                        <?php echo is_child_theme() ? '<span class="yes"><span class="dashicons dashicons-yes"></span>Yes</span>' : '<span class="warning"><span class="dashicons dashicons-warning"></span> No. ' . sprintf(__('If you\'re want to modifying a theme, it safe to create a child theme.  See: <a href="%s" target="_blank" rel="noopener">How to create a child theme</a>', WP_ADMINIFY_TD), 'https://developer.wordpress.org/themes/advanced-topics/child-themes/') . '</span>'; ?>
                    </td>
                </tr>
                <?php if (is_child_theme()) :
                    $parent_theme = wp_get_theme($active_theme->Template); ?>
                    <tr>
                        <td><?php esc_html_e('Parent Theme Name', WP_ADMINIFY_TD); ?>:</td>
                        <td><?php echo esc_html($parent_theme->Name); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('Parent Theme Version', WP_ADMINIFY_TD); ?>:</td>
                        <td>
                            <?php echo esc_html($parent_theme->Version);
                            if (version_compare($parent_theme->Version, $update_theme_version, '<')) {
                                echo ' &ndash; <strong style="color:red;">' . sprintf(__('%s is available', WP_ADMINIFY_TD), esc_html($update_theme_version)) . '</strong>';
                            } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('Parent Theme Author URL', WP_ADMINIFY_TD); ?>:</td>
                        <td><?php echo $parent_theme->{'Author URI'}; ?></td>
                    </tr>
                <?php endif ?>
            </tbody>
        </table>

        <h2 class="is-pulled-left pt-5"><?php echo esc_html__('Active Plugins', WP_ADMINIFY_TD); ?></h2>

        <button class="adminify-copy-btn button is-pulled-right" data-text="COPY" data-text-copied="COPIED">
            <span class="icon icon-copy dashicons dashicons-admin-page"></span>
            <span>COPY</span>
        </button>

        <table class="wp-list-table widefat posts adminify-active-plugins-data">
            <thead>
                <tr>
                    <th width="35%" class="manage-column"><?php echo esc_html__('Name', WP_ADMINIFY_TD); ?></th>
                    <th class="manage-column"><?php echo esc_html__('Version', WP_ADMINIFY_TD); ?></th>
                    <th width="35%" class="manage-column"><?php echo esc_html__('Author', WP_ADMINIFY_TD); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $active_plugins = (array) get_option('active_plugins', array());

                if (is_multisite()) {
                    $network_activated_plugins = array_keys(get_site_option('active_sitewide_plugins', array()));
                    $active_plugins = array_merge($active_plugins, $network_activated_plugins);
                }

                foreach ($active_plugins as $plugin) {

                    $plugin_data = @get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
                    $dirname = dirname($plugin);
                    $version_string = '';
                    $network_string = '';

                    if (!empty($plugin_data['Name'])) {

                        // Link the plugin name to the plugin url if available.
                        $plugin_name = esc_html($plugin_data['Name']);

                        if (!empty($plugin_data['PluginURI'])) {
                            $plugin_name = '<a href="' . esc_url($plugin_data['PluginURI']) . '" title="' . esc_attr__('Visit plugin homepage', WP_ADMINIFY_TD) . '" target="_blank" rel="noopener">' . $plugin_name . '</a>';
                        }

                        if (strstr($dirname, 'wphave-admin-') && strstr($plugin_data['PluginURI'], 'woothemes.com')) {

                            if (false === ($version_data = get_transient(md5($plugin) . '_version_data'))) {
                                $changelog = wp_safe_remote_get('http://dzv365zjfbd8v.cloudfront.net/changelogs/' . $dirname . '/changelog.txt');
                                $cl_lines = explode("\n", wp_remote_retrieve_body($changelog));
                                if (!empty($cl_lines)) {
                                    foreach ($cl_lines as $line_num => $cl_line) {
                                        if (preg_match('/^[0-9]/', $cl_line)) {
                                            $date = str_replace('.', '-', trim(substr($cl_line, 0, strpos($cl_line, '-'))));
                                            $version = preg_replace('~[^0-9,.]~', '', stristr($cl_line, "version"));
                                            $update = trim(str_replace("*", "", $cl_lines[$line_num + 1]));
                                            $version_data = array('date' => $date, 'version' => $version, 'update' => $update, 'changelog' => $changelog);
                                            set_transient(md5($plugin) . '_version_data', $version_data, DAY_IN_SECONDS);
                                            break;
                                        }
                                    }
                                }
                            }

                            if (!empty($version_data['version']) && version_compare($version_data['version'], $plugin_data['Version'], '>')) {
                                $version_string = ' &ndash; <strong style="color:red;">' . esc_html(sprintf(_x('%s is available', 'Version info', WP_ADMINIFY_TD), $version_data['version'])) . '</strong>';
                            }

                            if ($plugin_data['Network'] != false) {
                                $network_string = ' &ndash; <strong style="color:black;">' . __('Network enabled', WP_ADMINIFY_TD) . '</strong>';
                            }
                        } ?>
                        <tr>
                            <td><?php echo $plugin_name; ?></td>
                            <td><?php echo esc_html($plugin_data['Version']) . $version_string . $network_string; ?></td>
                            <td><?php echo sprintf(_x('%s', 'by author', WP_ADMINIFY_TD), $plugin_data['Author']); ?></td>
                        </tr>
                <?php }
                } ?>
            </tbody>
        </table>


<?php

    }
}

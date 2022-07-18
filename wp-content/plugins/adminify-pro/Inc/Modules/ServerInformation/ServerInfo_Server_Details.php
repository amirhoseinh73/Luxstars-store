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

class ServerInfo_Server_Details
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
                <?php echo Utils::admin_page_title(esc_html__('Server Info', WP_ADMINIFY_TD)); ?>
            </h1>

            <p><?php echo __('Interesting information about your web server. You can also use <a href="http://linfo.sourceforge.net/" target="_blank" rel="noopener">linfo</a> or <a href="https://phpsysinfo.github.io/phpsysinfo/" target="_blank" rel="noopener">phpsysinfo</a> to get more information about the web server', WP_ADMINIFY_TD); ?>.</p>

            <p><?php echo __('In the most cases you can modify some server settings like "PHP Memory Limit" or "PHP Post Max Size" by upload and modify a <code>php.ini</code> file in the WordPress <code>/wp-admin/</code> folder. Learn more about <a href="https://www.wpbeginner.com/wp-tutorials/how-to-increase-the-maximum-file-upload-size-in-wordpress/" target="_blank" rel="noopener">here</a>', WP_ADMINIFY_TD); ?>.</p>


            <table class="wp-list-table widefat posts mt-6">
                <thead>
                    <tr>
                        <th style="width: 300px" class="manage-column"><?php echo esc_html__('Info', WP_ADMINIFY_TD); ?></th>
                        <th class="manage-column"><?php echo esc_html__('Result', WP_ADMINIFY_TD); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php esc_html_e('OS', WP_ADMINIFY_TD); ?>:</td>
                        <td><?php echo PHP_OS; ?> / <?php echo (PHP_INT_SIZE * 8) . __('Bit OS', WP_ADMINIFY_TD) . ' (' . php_uname() . ')'; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('Software', WP_ADMINIFY_TD); ?>:</td>
                        <td><?php echo esc_html($_SERVER['SERVER_SOFTWARE']); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('IP Address', WP_ADMINIFY_TD); ?>:</td>
                        <td><?php echo esc_html($_SERVER['SERVER_ADDR']); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('Web Port', WP_ADMINIFY_TD); ?>:</td>
                        <td><?php echo $_SERVER['SERVER_PORT']; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('Date / Time (WP)', WP_ADMINIFY_TD); ?>:</td>
                        <td><?php echo date('Y-m-d H:i:s', current_time('timestamp', 1)) . ' (' . current_time('mysql') . ')'; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('Timezone (WP)', WP_ADMINIFY_TD); ?>:</td>
                        <td><?php echo date_default_timezone_get() . ' (' . $server_info->get_wp_timezone() . ')'; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('Default Timezone is UTC', WP_ADMINIFY_TD); ?>:</td>
                        <td>
                            <?php $default_timezone = date_default_timezone_get();
                            if ('UTC' !== $default_timezone) {
                                echo $no . sprintf(__('Default timezone is %s - it should be UTC', WP_ADMINIFY_TD), $default_timezone) . '</span>';
                            } else {
                                echo $yes;
                            } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('Protocol', WP_ADMINIFY_TD); ?>:</td>
                        <td><?php echo php_uname('n'); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('Administrator', WP_ADMINIFY_TD); ?>:</td>
                        <td><?php //echo $_SERVER['SERVER_ADMIN']; 
                            ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('CGI Version', WP_ADMINIFY_TD); ?>:</td>
                        <td><?php echo $_SERVER['GATEWAY_INTERFACE']; ?></td>
                    </tr>
                    <tr class="table-border-top">
                        <td><?php esc_html_e('CPU Total', WP_ADMINIFY_TD); ?>:</td>
                        <td><?php echo $server_info->check_cpu_count() . ' / ' . $server_info->check_core_count() . ' ' . esc_html__('Cores', WP_ADMINIFY_TD); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('CPU Usage', WP_ADMINIFY_TD); ?>:</td>
                        <td>
                            <div class="adminify-system-progress">
                                <div class="status-progressbar">
                                    <span><?php echo $server_info->get_server_cpu_load_percentage() . '% '; ?></span>
                                    <div style="width: <?php echo $server_info->get_server_cpu_load_percentage(); ?>%"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('CPU Load Average', WP_ADMINIFY_TD); ?>:</td>
                        <td><?php echo $server_info->get_cpu_load_average(); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('Disk Space', WP_ADMINIFY_TD); ?>:</td>
                        <td>
                            <?php $disk_space = $server_info->get_server_disk_size();
                            if ($disk_space != 'N/A') {
                                echo esc_html__('Total', WP_ADMINIFY_TD) . ': ' . esc_html($disk_space['size']) . ' GB / ' . esc_html__('Free', WP_ADMINIFY_TD) . ': ' . esc_html($disk_space['free']) . ' GB / ' . esc_html__('Used', WP_ADMINIFY_TD) . ': ' . esc_html($disk_space['used']) . ' GB';
                            } else {
                                echo esc_html($disk_space);
                            } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('Disk Space Usage', WP_ADMINIFY_TD); ?>:</td>
                        <td>
                            <?php $disk_space = $server_info->get_server_disk_size();
                            if ($disk_space != 'N/A') { ?>
                                <div class="status-progressbar">
                                    <span><?php echo esc_html($disk_space['usage'] . '% '); ?></span>
                                    <div style="width: <?php echo $disk_space['usage']; ?>%"></div>
                                </div>
                            <?php echo esc_html(' ' . $disk_space['used'] . ' GB of ' . $disk_space['size'] . ' GB');
                            } else {
                                echo esc_html($disk_space);
                            } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('Memory (RAM) Total', WP_ADMINIFY_TD); ?>:</td>
                        <td>
                            <?php $server_ram = $server_info->get_server_ram_details();
                            if ($server_ram != 'N/A') {
                                echo esc_html($server_ram['MemTotal']) . ' GB';
                            } else {
                                echo esc_html($server_ram);
                            } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('Memory (RAM) Free', WP_ADMINIFY_TD); ?>:</td>
                        <td>
                            <?php $server_ram = $server_info->get_server_ram_details();
                            if ($server_ram != 'N/A') {
                                echo esc_html($server_ram['MemFree']) . ' GB';
                            } else {
                                echo esc_html($server_ram);
                            } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('Memory (RAM) Usage', WP_ADMINIFY_TD); ?>:</td>
                        <td>
                            <?php $server_ram = $server_info->get_server_ram_details();
                            if ($server_ram != 'N/A') { ?>
                                <div class="status-progressbar">
                                    <span>
                                        <?php echo esc_html($server_ram['MemUsagePercentage'] . '% '); ?>
                                    </span>
                                    <div style="width: <?php echo $server_ram['MemUsagePercentage']; ?>%"></div>
                                </div>
                            <?php echo esc_html(' ' . ($server_ram['MemTotal'] - $server_ram['MemFree']) . ' GB of ' . $server_ram['MemTotal'] . ' GB');
                            } else {
                                echo esc_html($server_ram);
                            } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('Memcached', WP_ADMINIFY_TD); ?>:</td>
                        <td>
                            <?php if (extension_loaded('memcache')) :
                                echo $yes;
                            else :
                                echo $no;
                            endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('Uptime', WP_ADMINIFY_TD); ?>:</td>
                        <td><?php echo $server_info->get_server_uptime(); ?></td>
                    </tr>
                    <tr class="table-border-top">
                        <td><?php esc_html_e('PHP Version', WP_ADMINIFY_TD); ?>:</td>
                        <td><?php echo $server_info->get_php_version(); ?></td>
                    </tr>
                    <?php if (function_exists('ini_get')) : ?>
                        <tr>
                            <td><?php esc_html_e('PHP Memory Limit (WP)', WP_ADMINIFY_TD); ?>:</td>
                            <td><?php echo $server_info->get_wp_memory_limit(); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('PHP Memory Usage', WP_ADMINIFY_TD); ?>:</td>
                            <td>
                                <?php if ($server_info->get_server_memory_usage()['MemLimitGet'] == '-1') { ?>
                                    <?php echo $server_info->get_server_memory_usage()['MemUsageFormat'] . ' ' . esc_html__('of', WP_ADMINIFY_TD) . ' ' . esc_html__('Unlimited', WP_ADMINIFY_TD) . ' (-1)'; ?>
                                <?php } else { ?>
                                    <?php echo $server_info->get_server_memory_usage()['MemUsageFormat'] . ' ' . esc_html__('of', WP_ADMINIFY_TD) . ' ' . $server_info->get_server_memory_usage()['MemLimitFormat']; ?>
                                    
                                    <div class="adminify-system-progress">
                                        <div class="status-progressbar"><span><?php echo $server_info->get_server_memory_usage()['MemUsageCalc'] . '% '; ?></span>
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
                            <td><?php esc_html_e('PHP Post Max Size', WP_ADMINIFY_TD); ?>:</td>
                            <td><?php echo size_format($server_info->convert_memory_size(ini_get('post_max_size'))); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('PHP Max Input Vars', WP_ADMINIFY_TD); ?>:</td>
                            <td><?php echo ini_get('max_input_vars'); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('PHP Max Execution Time', WP_ADMINIFY_TD); ?>:</td>
                            <td><?php echo ini_get('max_execution_time') . ' ' . esc_html__('Seconds', WP_ADMINIFY_TD); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('PHP Extensions', WP_ADMINIFY_TD); ?>:</td>
                            <td><?php echo esc_html(implode(', ', get_loaded_extensions())); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('GD Library', WP_ADMINIFY_TD); ?>:</td>
                            <td>
                                <?php $gdl = gd_info();
                                if ($gdl) {
                                    echo $yes . ' / ' . esc_html__('Version', WP_ADMINIFY_TD) . ': ' . $gdl['GD Version'];
                                } else {
                                    echo $no;
                                } ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('cURL Version', WP_ADMINIFY_TD); ?>:</td>
                            <td><?php echo $server_info->get_cURL_version(); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('SUHOSIN Installed', WP_ADMINIFY_TD); ?>:</td>
                            <td><?php echo extension_loaded('suhosin') ? '<span class="dashicons dashicons-yes"></span>' : '&ndash;'; ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (function_exists('ini_get')) : ?>
                        <tr>
                            <td><?php esc_html_e('PHP Error Log File Location', WP_ADMINIFY_TD); ?>:</td>
                            <td><?php echo ini_get('error_log'); ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php $fields = array();

                    // fsockopen/cURL.
                    $fields['fsockopen_curl']['name'] = 'fsockopen/cURL';

                    if (function_exists('fsockopen') || function_exists('curl_init')) {
                        $fields['fsockopen_curl']['success'] = true;
                    } else {
                        $fields['fsockopen_curl']['success'] = false;
                    }

                    // SOAP.
                    $fields['soap_client']['name'] = 'SoapClient';

                    if (class_exists('SoapClient')) {
                        $fields['soap_client']['success'] = true;
                    } else {
                        $fields['soap_client']['success'] = false;
                        $fields['soap_client']['note'] = sprintf(__('Your server does not have the %s class enabled - some gateway plugins which use SOAP may not work as expected.', 'bsi'), '<a href="https://php.net/manual/en/class.soapclient.php">SoapClient</a>');
                    }

                    // DOMDocument.
                    $fields['dom_document']['name'] = 'DOMDocument';

                    if (class_exists('DOMDocument')) {
                        $fields['dom_document']['success'] = true;
                    } else {
                        $fields['dom_document']['success'] = false;
                        $fields['dom_document']['note'] = sprintf(__('Your server does not have the %s class enabled - HTML/Multipart emails, and also some extensions, will not work without DOMDocument.', 'bsi'), '<a href="https://php.net/manual/en/class.domdocument.php">DOMDocument</a>');
                    }

                    // GZIP.
                    $fields['gzip']['name'] = 'GZip';

                    if (is_callable('gzopen')) {
                        $fields['gzip']['success'] = true;
                    } else {
                        $fields['gzip']['success'] = false;
                        $fields['gzip']['note'] = sprintf(__('Your server does not support the %s function - this is required to use the GeoIP database from MaxMind.', 'bsi'), '<a href="https://php.net/manual/en/zlib.installation.php">gzopen</a>');
                    }

                    // Multibyte String.
                    $fields['mbstring']['name'] = 'Multibyte String';

                    if (extension_loaded('mbstring')) {
                        $fields['mbstring']['success'] = true;
                    } else {
                        $fields['mbstring']['success'] = false;
                        $fields['mbstring']['note'] = sprintf(__('Your server does not support the %s functions - this is required for better character encoding. Some fallbacks will be used instead for it.', 'bsi'), '<a href="https://php.net/manual/en/mbstring.installation.php">mbstring</a>');
                    }

                    // Remote Get.
                    $fields['remote_get']['name'] = 'Remote Get Status';

                    $response = wp_remote_get('https://www.paypal.com/cgi-bin/webscr', array(
                        'timeout' => 60,
                        'user-agent' => 'BSI/' . 1.0,
                        'httpversion' => '1.1',
                        'body' => array(
                            'cmd' => '_notify-validate'
                        )
                    ));
                    $response_code = wp_remote_retrieve_response_code($response);
                    if ($response_code == 200) {
                        $fields['remote_get']['success'] = true;
                    } else {
                        $fields['remote_get']['success'] = false;
                    }

                    foreach ($fields as $field) {
                        $mark = !empty($field['success']) ? 'yes' : 'error'; ?>
                        <tr>
                            <td data-export-label="<?php echo esc_html($field['name']); ?>"><?php echo esc_html($field['name']); ?>:</td>
                            <td>
                                <span class="<?php echo $mark; ?>">
                                    <?php echo !empty($field['success']) ? $yes : $no; ?> <?php echo !empty($field['note']) ? wp_kses_data($field['note']) : ''; ?>
                                </span>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>


<?php
    }
}

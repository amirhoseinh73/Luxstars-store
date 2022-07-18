<?php

namespace WPAdminify\Pro\Classes;

use WPAdminify\Inc\Admin\AdminSettings;
use WPAdminify\Inc\Admin\AdminSettingsModel;

// no direct access allowed
if (!defined('ABSPATH'))  exit;

/**
 * @package WPAdminify
 * Schedule Dark Mode
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */
class Schedule_Dark_Mode extends AdminSettingsModel
{
    public function __construct()
    {
        add_filter('parent_file', [$this, 'add_head_script']);
    }

    public function add_head_script($data)
    {

        $keys = ['admin_bar_mode', 'enable_schedule_dark_mode', 'schedule_dark_mode_type', 'schedule_dark_mode_start_time', 'schedule_dark_mode_end_time'];

        $settings = (object) array_intersect_key((array) AdminSettings::get_instance()->get(), array_flip($keys));

        $settings->enable_schedule_dark_mode = wp_validate_boolean($settings->enable_schedule_dark_mode);

        if (true) {

?>

            <script type="text/javascript">
                var settings = <?php echo json_encode($settings); ?>;

                function get_the_switcher_btn() {
                    return document.querySelector('#light-dark-switcher-btn');
                }

                function adminify_get_sys_mode() {
                    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }

                function adminify_set_switcher_btn_mode(mode) {

                    var switcher_btn = get_the_switcher_btn();

                    var switcher_btn_handler = setInterval(function() {

                        if (switcher_btn) {

                            clearInterval(switcher_btn_handler);

                            if (mode == 'dark') {
                                switcher_btn.checked = true;
                            }

                            if (mode == 'light') {
                                switcher_btn.checked = false;
                            }

                        } else {
                            switcher_btn = get_the_switcher_btn();
                        }

                    }, 50);

                }

                function adminify_set_document_mode(mode) {

                    var body = document.querySelector('body');

                    if (mode == 'dark') {
                        body.className = body.className.replace('adminify-light-mode', '');
                        if (body.className.search('adminify-dark-mode') < 0) {
                            body.className += ' adminify-dark-mode';
                        }
                    }

                    if (mode == 'light') {
                        body.className = body.className.replace('adminify-dark-mode', '');
                        if (body.className.search('adminify-light-mode') < 0) {
                            body.className += ' adminify-light-mode';
                        }
                    }

                    adminify_set_switcher_btn_mode(mode);

                }

                function adminify_get_custom_mode() {

                    var time = Number(new Date().getHours() + '.' + new Date().getMinutes());
                    var start_time = Number(settings.schedule_dark_mode_start_time.replace(':', '.'));
                    var end_time = Number(settings.schedule_dark_mode_end_time.replace(':', '.'));

                    return (time >= start_time && time < end_time) ? 'dark' : 'light';

                }

                if (settings.enable_schedule_dark_mode) {

                    if (settings.schedule_dark_mode_type == 'system') {

                        adminify_set_document_mode(adminify_get_sys_mode());

                        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function() {
                            adminify_set_document_mode(adminify_get_sys_mode());
                        });

                    }

                    if (settings.schedule_dark_mode_type == 'custom' && settings.schedule_dark_mode_start_time && settings.schedule_dark_mode_end_time) {

                        adminify_set_document_mode(adminify_get_custom_mode());

                        setInterval(function() {
                            adminify_set_document_mode(adminify_get_custom_mode());
                        }, 1000 * 2);

                    }

                } else {

                    adminify_set_document_mode(settings.admin_bar_mode);

                }
            </script>

<?php

        }

        return $data;
    }
}

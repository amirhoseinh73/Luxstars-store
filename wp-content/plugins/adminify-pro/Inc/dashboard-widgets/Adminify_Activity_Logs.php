<?php

namespace WPAdminify\Inc\DashboardWidgets;

// no direct access allowed
if (!defined('ABSPATH'))  exit;

/**
 * Dashboard Widget: Activity Logs
 *
 * @return void
 */
/**
 * WPAdminify
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */

class Adminify_Activity_Logs
{
    public function __construct()
    {
        add_action('wp_dashboard_setup', [$this, 'jltwp_adminify_activity_logs']);
    }


    /**
     * Label: Activity Logs
     *
     * @return void
     */
    public function jltwp_adminify_activity_logs()
    {
        wp_add_dashboard_widget(
            'jltwp_adminify_dash_activity_logs',
            esc_html__('Activity Logs - Adminify', WP_ADMINIFY_TD),
            [$this, 'jltwp_adminify_activity_logs_details']
        );
    }



    /**
     * Dashboard Widgets: Logged in Users
     *
     * @return void
     */
    public function jltwp_adminify_activity_logs_details()
    {
        global $wpdb;

?>


        <div class="wp-adminify-users-activity-logs">
            <div class="wp-adminify-user-activity-logs-table">
                <table class="wp-adminify-user-activity-table">
                    <tr>
                        <th><?php echo esc_html__('User', WP_ADMINIFY_TD); ?></th>
                        <th><?php echo esc_html__('Type', WP_ADMINIFY_TD); ?></th>
                        <th><?php echo esc_html__('Action', WP_ADMINIFY_TD); ?></th>
                        <th><?php echo esc_html__('Description', WP_ADMINIFY_TD); ?></th>
                    </tr>

                    <?php

                    $jltma_activity_logs_data = $wpdb->get_results('SELECT * FROM `' . $wpdb->adminify_activity_logs . '` ORDER BY `log_id` DESC LIMIT 5');

                    if ($jltma_activity_logs_data) {
                        foreach ($jltma_activity_logs_data as $log_data) {
                            $user = get_user_by('id', $log_data->user_id);
                    ?>

                            <tr>
                                <td>
                                    <div class="wp-adminify-user-avatar is-pulled-left image">
                                        <?php echo get_avatar($log_data->user_id, 36, '', '', array('class' => 'is-rounded')); ?>
                                        <span class="user-status m-0 p-0"></span>
                                    </div>

                                    <h5 class="wp-adminify-user-name">
                                        <?php echo esc_html($user->display_name); ?>
                                    </h5>

                                    <div class="wp-adminify-user-details mt-2">
                                        <span class="wp-adminify-user-last-login">
                                            <?php echo sprintf('<strong>' . __('%s ago', WP_ADMINIFY_TD) . '</strong>', human_time_diff($log_data->log_time, current_time('timestamp'))); ?>
                                        </span>

                                        <span class="wp-adminify-user-last-logout">
                                            <?php echo date('H:i:s', $log_data->log_time); ?>,
                                            <?php echo date('d/m/Y', $log_data->log_time); ?>

                                        </span>
                                    </div>
                                </td>
                                <td><?php echo esc_html($log_data->object_type); ?></td>
                                <td><?php echo esc_html($log_data->action); ?></td>
                                <td><?php echo esc_html($log_data->object_name); ?></td>
                            </tr>
                    <?php }
                    } ?>

                </table>
            </div>
        </div>
<?php

    }
}

<?php

namespace WPAdminify\Inc\Admin\Options;

use WPAdminify\Inc\Admin\AdminSettingsModel;

if (!defined('ABSPATH')) {
  die;
} // Cannot access directly.

if (!class_exists('Modules')) {
  class Modules extends AdminSettingsModel
  {

    public function __construct()
    {
      $this->jltwp_adminify_general_options();
    }

    public function get_defaults()
    {
      return [
        'admin_ui'           => true,
        'folders'            => true,
        'admin_notices'      => true,
        'login_customizer'   => true,
        'admin_columns'      => true,
        'menu_editor'        => true,
        'dashboard_widgets'  => true,
        'pagespeed_insights' => true,
        'custom_css_js'      => true,
        'quick_menu'         => true,
        'menu_duplicator'    => true,
        'activity_logs'      => true,
        'admin_pages'        => true,
        'notification_bar'   => true,
        'post_duplicator'    => true,
        'post_types_order'   => true,
        'server_info'        => true,
        'sidebar_generator'  => true,
        'disable_comments'   => true
      ];
    }

    /**
     * Module Fields
     *
     * @param [type] $modules_fields
     *
     * @return void
     */
    public function module_fields(&$modules_fields)
    {
      $modules_fields[] = array(
        'id'         => 'admin_ui',
        'type'       => 'switcher',
        'title'      => __('Adminify UI', WP_ADMINIFY_TD),
        'subtitle'   => __('Choose WordPress Default or Adminify UI for your Dashboard. Default: Adminify UI', WP_ADMINIFY_TD),
        'text_on'    => __('Enabled', WP_ADMINIFY_TD),
        'text_off'   => __('Disabled', WP_ADMINIFY_TD),
        'text_width' => 100,
        'default'    => $this->get_default_field('admin_ui'),
      );

      $modules_fields[] = array(
        'id'         => 'folders',
        'type'       => 'switcher',
        'title'      => __('Folders', WP_ADMINIFY_TD),
        'subtitle'   => __('Categorize Post/Page/Media & Custom Post Types', WP_ADMINIFY_TD),
        'text_on'    => __('Enabled', WP_ADMINIFY_TD),
        'text_off'   => __('Disabled', WP_ADMINIFY_TD),
        'text_width' => 100,
        'default'    => $this->get_default_field('folders'),
      );

      $modules_fields[] = array(
        'id'         => 'login_customizer',
        'type'       => 'switcher',
        'title'      => __('Login Customizer', WP_ADMINIFY_TD),
        'subtitle'   => __('16 pre-built Templates, with Video,Slideshow,Gradient and many more', WP_ADMINIFY_TD),
        'text_on'    => __('Enabled', WP_ADMINIFY_TD),
        'text_off'   => __('Disabled', WP_ADMINIFY_TD),
        'text_width' => 100,
        'default'    => $this->get_default_field('login_customizer'),
      );

      $modules_fields[] = array(
        'id'         => 'admin_notices',
        'type'       => 'switcher',
        'title'      => __('Admin Notices', WP_ADMINIFY_TD),
        'subtitle'   => __('Enable/Disable Hide All admin Notices Module', WP_ADMINIFY_TD),
        'text_on'    => __('Enabled', WP_ADMINIFY_TD),
        'text_off'   => __('Disabled', WP_ADMINIFY_TD),
        'text_width' => 100,
        'default'    => $this->get_default_field('admin_notices'),
      );

      $modules_fields[] = array(
        'id'         => 'admin_columns',
        'type'       => 'switcher',
        'title'      => __('Admin Columns', WP_ADMINIFY_TD),
        'subtitle'   => __('Customize Column names post,page,products,media,taxonomoy etc', WP_ADMINIFY_TD),
        'text_on'    => __('Enabled', WP_ADMINIFY_TD),
        'text_off'   => __('Disabled', WP_ADMINIFY_TD),
        'text_width' => 100,
        'default'    => $this->get_default_field('admin_columns'),
      );

      $modules_fields[] = array(
        'id'         => 'menu_editor',
        'type'       => 'switcher',
        'title'      => __('Menu Editor', WP_ADMINIFY_TD),
        'subtitle'   => __('Advanced Menu Editor with restrict users, Custom Icons etc', WP_ADMINIFY_TD),
        'text_on'    => __('Enabled', WP_ADMINIFY_TD),
        'text_off'   => __('Disabled', WP_ADMINIFY_TD),
        'text_width' => 100,
        'default'    => $this->get_default_field('menu_editor'),
      );

      $modules_fields[] = array(
        'id'         => 'dashboard_widgets',
        'type'       => 'switcher',
        'title'      => __('Dashboard & Welcome Widget', WP_ADMINIFY_TD),
        'subtitle'   => __('Create Custom Dashboard & Sidebar Widgets', WP_ADMINIFY_TD),
        'text_on'    => __('Enabled', WP_ADMINIFY_TD),
        'text_off'   => __('Disabled', WP_ADMINIFY_TD),
        'text_width' => 100,
        'default'    => $this->get_default_field('dashboard_widgets'),
      );

      $modules_fields[] = array(
        'id'         => 'pagespeed_insights',
        'type'       => 'switcher',
        'title'      => __('Pagespeed Insights', WP_ADMINIFY_TD),
        'subtitle'   => __('Analyze Google Pagespeed from Dashboard', WP_ADMINIFY_TD),
        'text_on'    => __('Enabled', WP_ADMINIFY_TD),
        'text_off'   => __('Disabled', WP_ADMINIFY_TD),
        'text_width' => 100,
        'default'    => $this->get_default_field('pagespeed_insights'),
      );

      $modules_fields[] = array(
        'id'         => 'custom_css_js',
        'type'       => 'switcher',
        'title'      => __('Header/Footer Script', WP_ADMINIFY_TD),
        'subtitle'   => __('Inject Custom CSS/JS on Header or Footer', WP_ADMINIFY_TD),
        'text_on'    => __('Enabled', WP_ADMINIFY_TD),
        'text_off'   => __('Disabled', WP_ADMINIFY_TD),
        'text_width' => 100,
        'default'    => $this->get_default_field('custom_css_js'),
      );

      $modules_fields[] = array(
        'id'         => 'quick_menu',
        'type'       => 'switcher',
        'title'      => __('Quick Menu', WP_ADMINIFY_TD),
        'subtitle'   => __('Quick Menu for navigating quickly', WP_ADMINIFY_TD),
        'text_on'    => __('Enabled', WP_ADMINIFY_TD),
        'text_off'   => __('Disabled', WP_ADMINIFY_TD),
        'text_width' => 100,
        'default'    => $this->get_default_field('quick_menu'),
      );

      $modules_fields[] = array(
        'id'         => 'menu_duplicator',
        'type'       => 'switcher',
        'title'      => __('Menu Duplicator', WP_ADMINIFY_TD),
        'subtitle'   => __('Duplicate menu and Nav menu items also', WP_ADMINIFY_TD),
        'text_on'    => __('Enabled', WP_ADMINIFY_TD),
        'text_off'   => __('Disabled', WP_ADMINIFY_TD),
        'text_width' => 100,
        'default'    => $this->get_default_field('menu_duplicator'),
      );



      $modules_fields[] = array(
        'id'         => 'notification_bar',
        'type'       => 'switcher',
        'title'      => __('Notification Bar', WP_ADMINIFY_TD),
        'subtitle'   => __('Cookie Notice, Promotional Bar on frontend', WP_ADMINIFY_TD),
        'text_on'    => __('Enabled', WP_ADMINIFY_TD),
        'text_off'   => __('Disabled', WP_ADMINIFY_TD),
        'text_width' => 100,
        'default'    => $this->get_default_field('notification_bar'),
      );

      $modules_fields[] = array(
        'id'         => 'activity_logs',
        'type'       => 'switcher',
        'title'      => __('Activity Logs', WP_ADMINIFY_TD),
        'subtitle'   => __('Security check for all users Activity', WP_ADMINIFY_TD),
        'text_on'    => __('Enabled', WP_ADMINIFY_TD),
        'text_off'   => __('Disabled', WP_ADMINIFY_TD),
        'text_width' => 100,
        'default'    => $this->get_default_field('activity_logs'),
      );

      $modules_fields[] = array(
        'id'         => 'post_duplicator',
        'type'       => 'switcher',
        'title'      => __('Post Duplicator', WP_ADMINIFY_TD),
        'subtitle'   => __('Duplicate Post/Page and any Custom Post Type', WP_ADMINIFY_TD),
        'text_on'    => __('Enabled', WP_ADMINIFY_TD),
        'text_off'   => __('Disabled', WP_ADMINIFY_TD),
        'text_width' => 100,
        'default'    => $this->get_default_field('post_duplicator'),
      );

      $modules_fields[] = array(
        'id'         => 'admin_pages',
        'type'       => 'switcher',
        'title'      => __('Admin Pages', WP_ADMINIFY_TD),
        'subtitle'   => __('Custom Admin Pages hooks on Top/Sub Menu', WP_ADMINIFY_TD),
        'text_on'    => __('Enabled', WP_ADMINIFY_TD),
        'text_off'   => __('Disabled', WP_ADMINIFY_TD),
        'text_width' => 100,
        'default'    => $this->get_default_field('admin_pages'),
      );


      $modules_fields[] = array(
        'id'         => 'sidebar_generator',
        'type'       => 'switcher',
        'title'      => __('Sidebar Generator', WP_ADMINIFY_TD),
        'subtitle'   => __('Create Custom Sidebar Widgets', WP_ADMINIFY_TD),
        'text_on'    => __('Enabled', WP_ADMINIFY_TD),
        'text_off'   => __('Disabled', WP_ADMINIFY_TD),
        'text_width' => 100,
        'default'    => $this->get_default_field('sidebar_generator'),
      );

      $modules_fields[] = array(
        'id'         => 'post_types_order',
        'type'       => 'switcher',
        'title'      => __('Post Types Order', WP_ADMINIFY_TD),
        'subtitle'   => __('Post Types and Taxonomy Order', WP_ADMINIFY_TD),
        'text_on'    => __('Enabled', WP_ADMINIFY_TD),
        'text_off'   => __('Disabled', WP_ADMINIFY_TD),
        'text_width' => 100,
        'default'    => $this->get_default_field('post_types_order'),
      );


      $modules_fields[] = array(
        'id'         => 'server_info',
        'type'       => 'switcher',
        'title'      => __('Server Info', WP_ADMINIFY_TD),
        'subtitle'   => __('Server Info menu Module', WP_ADMINIFY_TD),
        'text_on'    => __('Enabled', WP_ADMINIFY_TD),
        'text_off'   => __('Disabled', WP_ADMINIFY_TD),
        'text_width' => 100,
        'default'    => $this->get_default_field('server_info'),
      );

      $modules_fields[] = array(
        'id'         => 'disable_comments',
        'type'       => 'switcher',
        'title'      => __('Disable Comments', WP_ADMINIFY_TD),
        'subtitle'   => __('Disable Comments for Post/Pages/Post Types etc', WP_ADMINIFY_TD),
        'text_on'    => __('Enabled', WP_ADMINIFY_TD),
        'text_off'   => __('Disabled', WP_ADMINIFY_TD),
        'text_width' => 100,
        'default'    => $this->get_default_field('disable_comments'),
      );
    }

    public function jltwp_adminify_general_options()
    {
      if (!class_exists('ADMINIFY')) {
        return;
      }

      $modules_fields = [];
      $this->module_fields($modules_fields);

      // Modules Section
      \ADMINIFY::createSection($this->prefix, array(
        'title'  => __('Modules', WP_ADMINIFY_TD),
        'icon'   => 'fas fa-layer-group',
        'class'  => 'wp-adminify-two-columns aminify-title-width-65',
        'fields' => $modules_fields
      ));
    }
  }
}

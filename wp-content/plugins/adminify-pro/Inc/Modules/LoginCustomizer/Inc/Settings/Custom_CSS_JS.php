<?php

namespace WPAdminify\Inc\Modules\LoginCustomizer\Inc\Settings;

use WPAdminify\Inc\Modules\LoginCustomizer\Inc\Customize_Model;

if (!defined('ABSPATH')) {
    die;
} // Cannot access directly.

class Custom_CSS_JS extends Customize_Model
{
    public function __construct()
    {
        $this->custom_css_js_customizer();
    }


    public function get_defaults()
    {
        return [
            'jltwp_adminify_customizer_custom_css' => '',
            'jltwp_adminify_customizer_custom_js'  => ';(function( $, window, document, undefined ) {
  "use strict";
  $(document).ready( function() {
    // Your JS code here
  });
})( jQuery, window, document );'

        ];
    }


    public function custom_css_js_fields_settings(&$custom_css_js_fields)
    {
        $custom_css_js_fields[] = array(
            'id'       => 'jltwp_adminify_customizer_custom_css',
            'type'     => 'code_editor',
            'title'    => 'CSS Editor',
            'subtitle' => sprintf(__('Custom CSS doesn\'t work on live. Save Settings and Preview %1$s login%2$s page or hit refresh after save Customer.', WP_ADMINIFY_TD), '<a href="' . wp_login_url() . '"title="Login" target="_blank">', '</a>'),
            'settings' => array(
                'theme' => 'dracula',
                'mode'  => 'css',
            ),
        );

        $custom_css_js_fields[] = array(
            'id'       => 'jltwp_adminify_customizer_custom_js',
            'type'     => 'code_editor',
            'title'    => 'JS Editor',
            'subtitle' => sprintf(__('Custom JS doesn\'t work on live. Save Settings and Preview %1$s login%2$s page or hit refresh after save Customer.', WP_ADMINIFY_TD), '<a href="' . wp_login_url() . '"title="Login" target="_blank">', '</a>'),
            'settings' => array(
                'theme' => 'dracula',
                'mode'  => 'javascript',
            ),
            'default'  => $this->get_default_field('jltwp_adminify_customizer_custom_js'),
        );
    }

    public function custom_css_js_customizer()
    {
        if (!class_exists('ADMINIFY')) {
            return;
        }

        $custom_css_js_fields = [];
        $this->custom_css_js_fields_settings($custom_css_js_fields);

        /**
         * Section: Custom CSS & JS Section
         */
        \ADMINIFY::createSection(
            $this->prefix,
            array(
                'assign' => 'jltwp_adminify_customizer_custom_css_js_section',
                'title'  => __('Custom CSS & JS', WP_ADMINIFY_TD),
                'fields' => $custom_css_js_fields
            )
        );
    }
}

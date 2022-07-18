<?php

namespace WPAdminify\Inc\Modules\LoginCustomizer\Inc\Settings;

use WPAdminify\Inc\Modules\LoginCustomizer\Inc\Customize_Model;

if (!defined('ABSPATH')) {
    die;
} // Cannot access directly.

class Others_Section extends Customize_Model
{
    public function __construct()
    {
        $this->others_section_customizer();
    }


    public function get_defaults()
    {
        return [
            'login_form_button_remember_me'   => false,
            'login_form_disable_login_shake'  => false,
            'login_form_disable_lost_pass'    => false,
            'login_form_disable_back_to_site' => false
        ];
    }


    public function login_form_others_settings(&$login_form_others)
    {

        $login_form_others[] = array(
            'id'       => 'login_form_button_remember_me',
            'type'     => 'switcher',
            'title'    => __('Hide Remember Me?', WP_ADMINIFY_TD),
            'text_on'  => 'Show',
            'text_off' => 'Hide',
            'default'  => $this->get_default_field('login_form_button_remember_me'),
            'class'    => 'wp-adminify-cs',
        );

        $login_form_others[] = array(
            'id'       => 'login_form_disable_login_shake',
            'type'     => 'switcher',
            'title'    => __('Disable Login shake?', WP_ADMINIFY_TD),
            'default'  => $this->get_default_field('login_form_disable_login_shake'),
            'text_on'  => 'Yes',
            'text_off' => 'No',
            'class'    => 'wp-adminify-cs',
        );

        $login_form_others[] = array(
            'id'       => 'login_form_disable_lost_pass',
            'type'     => 'switcher',
            'title'    => __('Disable Lost Password?', WP_ADMINIFY_TD),
            'default'  => $this->get_default_field('login_form_disable_lost_pass'),
            'text_on'  => 'Yes',
            'text_off' => 'No',
            'class'    => 'wp-adminify-cs',
        );

        $login_form_others[] = array(
            'id'       => 'login_form_disable_back_to_site',
            'type'     => 'switcher',
            'title'    => __('Disable "Back to Website" ?', WP_ADMINIFY_TD),
            'default'  => $this->get_default_field('login_form_disable_back_to_site'),
            'text_on'  => 'Yes',
            'text_off' => 'No',
            'class'    => 'wp-adminify-cs',
        );
    }


    public function others_section_customizer()
    {
        if (!class_exists('ADMINIFY')) {
            return;
        }

        $login_form_others    = [];
        $this->login_form_others_settings($login_form_others);

        /**
         * Section: Others Settings
         */
        \ADMINIFY::createSection(
            $this->prefix,
            array(
                'assign' => 'jltwp_adminify_customizer_login_others_section',
                'title'  => __('Others', WP_ADMINIFY_TD),
                'fields' => $login_form_others
            )
        );
    }
}

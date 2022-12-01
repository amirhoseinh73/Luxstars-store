<?php

function amhnj_enqueue_admin_script()
{
    wp_enqueue_script( 'amhnj-admin-script-functions', AMHNJ_FUNCTIONS_PLUGIN_ADMIN_URL . 'js/script.js', array(), '1.0.0');
}

add_action('admin_enqueue_scripts', 'amhnj_enqueue_admin_script');

function amhnj_enqueue_admin_style() {
    wp_enqueue_style( 'amhnj-admin-style-functions', AMHNJ_FUNCTIONS_PLUGIN_ADMIN_URL . 'css/style.css', array(), '1.0.0' );
}

add_action( 'admin_enqueue_scripts', 'amhnj_enqueue_admin_style' );
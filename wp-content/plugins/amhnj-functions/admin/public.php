<?php

// function enqueue_admin_wholesaler_register()
// {
//     wp_enqueue_script( 'admin-script-user-register-wholesaler', AMHNJ_REGISTER_WHOLESALER_PLUGIN_ADMIN_URL . 'js/script.js', array(), '1.0.0');
// }

// add_action('admin_enqueue_scripts', 'enqueue_admin_wholesaler_register');

function amhnj_enqueue_admin_style() {
    wp_enqueue_style( 'amhnj-admin-style-user', AMHNJ_FUNCTIONS_PLUGIN_ADMIN_URL . 'css/style.css', array(), '1.0.0' );
}

add_action( 'admin_enqueue_scripts', 'amhnj_enqueue_admin_style' );
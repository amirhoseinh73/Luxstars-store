<?php

function enqueue_admin_wallet_register() {
    wp_enqueue_script( 'admin-script-wallet', AMHNJ_WALLET_PLUGIN_ADMIN_URL . 'js/script.js', array(), '1.0.0');
}

add_action('admin_enqueue_scripts', 'enqueue_admin_wallet_register');
<?php

function enqueue_admin()
{
    wp_enqueue_style('admin-style', AMHNJ_REGISTER_PLUGIN_ADMIN_PATH . 'css/style.css', array(), '1.0.0');
    // wp_enqueue_style('bootstrap', AHH_PROGRESS_PLUGIN_ADMIN_URL . 'css/bootstrap.min.css', array(), '1.0.0');
}

add_action('admin_enqueue_scripts', 'enqueue_admin');
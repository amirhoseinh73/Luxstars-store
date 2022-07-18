<?php

// no direct access allowed
if (!defined('ABSPATH'))  exit;

function notification_bar_sections($wp_customize)
{

    $wp_customize->add_section(
        'general_section',
        array(
            'title' => esc_html__('General Settings', WP_ADMINIFY_TD),
            'panel' => 'jltwp_notification_bar_panel',
        )
    );

    $wp_customize->add_setting('twitter', array('sanitize_callback'     => 'sanitize_text_field'));

    $wp_customize->add_section(
        'content_section',
        array(
            'title' => esc_html__('Content Section', WP_ADMINIFY_TD),
            'panel' => 'jltwp_notification_bar_panel',
        )
    );

    $wp_customize->add_section(
        'layout_section',
        array(
            'title' => esc_html__('Layout Section', WP_ADMINIFY_TD),
            'panel' => 'jltwp_notification_bar_panel',
        )
    );

    $wp_customize->add_section(
        'display_section',
        array(
            'title' => esc_html__('Display Section', WP_ADMINIFY_TD),
            'panel' => 'jltwp_notification_bar_panel',
        )
    );

    $wp_customize->add_section(
        'style_section',
        array(
            'title' => esc_html__('Style Options', WP_ADMINIFY_TD),
            'panel' => 'jltwp_notification_bar_panel',
        )
    );
};

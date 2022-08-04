<?php

add_action( 'admin_menu', 'admin_menu' );

function admin_menu() {
	add_menu_page(
		__( 'register with mobile settings' ),
		__( 'تنظیمات ثبت نام با موبایل' ),
		'manage_options',
		'settings',
        'register_mobile_global_settings',
		'dashicons-schedule',//dashicons-schedule,dashicons-businessperson
		3
	);
}
<?php

// register_activation_hook( __FILE__, 'install' );
// register_activation_hook( __FILE__, 'jal_install_data' );

function install() {
    global $wpdb;

   $table_name = $wpdb->prefix . "progress_discount_ahh"; 

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id smallint(4) NOT NULL AUTO_INCREMENT,
        range_1 varchar(50) DEFAULT NULL,
        range_2 varchar(50) DEFAULT NULL,
        range_3 varchar(50) DEFAULT NULL,
        created_at datetime DEFAULT NULL,
        updated_at datetime DEFAULT NULL,
        PRIMARY KEY  (id)
        ) $charset_collate;
    ";

    $wpdb->query($sql);

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    add_option('my_db_version', '1.0.0');

}
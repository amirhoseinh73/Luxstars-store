<?php

require_once AMHNJ_REGISTER_WHOLESALER_PLUGIN_ADMIN_PATH . "public.php";

function wpseq_270133_users( $columns ) {

    unset( $columns[ 'email' ] );
    unset( $columns[ 'posts' ] );

    return $columns;
}
add_filter( 'manage_users_columns', 'wpseq_270133_users' );
<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/colleague.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! is_user_logged_in() ) {
    exit;
}

$userInfo = wp_get_current_user();

$colleagueCode = get_user_meta( $userInfo->ID, "colleague-code" );
if ( ! isset( $colleagueCode ) || empty( $colleagueCode ) ) {
    $bytes = random_bytes( 4 );
    $colleagueCode = strtoupper( bin2hex( $bytes ) );
    
    do {
        $updateUserMeta = add_user_meta( $userInfo->ID, "colleague-code", $colleagueCode, true );
    } while( ! $updateUserMeta );
}

?>
<div class="alert alert-info d-flex flex-row flex-wrap my-2">
    <span class="alert-heading col-auto">
        کد شما برای معرفی ما به دوستان خود:
    </span>

    <span class="alert-heading col text-right">
        <?= $colleagueCode[ 0 ] ?>
    </span>
</div>

<article class="p-3">
    <section class="text-dark">
        <p>تعداد کاربرانی که با کد شما خرید کرده اند:</p>
    </section>
    <section class="text-dark">
        <p>سود شما:</p>
    </section>
</article>
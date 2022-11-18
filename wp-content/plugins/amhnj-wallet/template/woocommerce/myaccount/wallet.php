<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/wallet.php.
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

$walletAmount = get_user_meta( $userInfo->ID, "wallet-amount" );

?>

<div class="alert alert-info d-flex flex-row flex-wrap my-2">
    <span class="alert-heading col-auto">
        مبلغ کیف پول شما:
    </span>

    <span class="alert-heading col text-right">
        <?= $walletAmount[ 0 ] ?>
        تومان
    </span>
</div>

<article class="p-3">
    <section class="row align-items-center mt-5">
        <div class="col-auto pr-0">
            <p class="text-dark mb-0">مبلغ مورد نظر برای شارژ:</p>
        </div>
        <div class="col-2">
            <input type="number" class="form-control" value="10000"/>
        </div>
        <div class="col-auto pr-0 pl-5">
            <p class="text-dark mb-0">تومان</p>
        </div>
        <div class="col pl-0">
            <button type="button" class="btn btn-primary px-5">ثبت و پرداخت</button>
        </div>
    </section>

    <section class="row mt-5">
        <div class="col-12 px-0">
            <p class="text-dark">از این طریق میتوانید درخواست تسویه خود را ثبت کنید</p>
        </div>
        <div class="col-12 col-sm-6 mx-auto mt-3">
            <button type="button" class="btn btn-info btn-block">ثبت درخواست</button>
        </div>
    </section>

    <section class="row mt-5">
        <div class="col-12 px-0">
            <p class="text-dark">درخواست های تسویه قبلی</p>
        </div>
        <div class="col-12 mx-auto">
            <table class="table table-striped table-light table-responsive-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>تاریخ</th>
                        <th>مبلغ</th>
                        <th>وضعیت</th>
                        <th>شماره پیگیری</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </section>
</article>

<?php
$args = array(
    // "date_before" => date( "Y-m-d" ),
    // "date_created" => date( "Y-m-d" )
    "customer_id" => $userInfo->ID
);

foreach( wc_get_orders( $args ) as $order ) {
    print_r( $order );
}
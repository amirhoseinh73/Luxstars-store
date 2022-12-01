<?php

add_action( "woocommerce_before_cart", "amhnj_order_status_cart_add_status" );
function amhnj_order_status_cart_add_status() {
    echo amhnj_order_status_html( "first" );
}

add_action( "woocommerce_before_checkout_form", "amhnj_order_status_checkout_form" );
function amhnj_order_status_checkout_form() {
    echo amhnj_order_status_html( "second" );
}

add_action( "woocommerce_before_thankyou", "amhnj_order_status_order_submit_add_status" );
function amhnj_order_status_order_submit_add_status() {
    echo amhnj_order_status_html( "third" );
}

function amhnj_order_status_html( $active = "first" ) {
    return "<section class='row order-status-beautiful-section justify-content-between align-items-center'>
        <div class='col-auto d-flex flex-column align-items-center justify-content-center order-status-item " . ( $active === "first" ? 'active' : '' ) . "'>
            <span class='fal fa-cart-shopping'></span>
            <p>بررسی سبد خرید</p>
        </div>
        <div class='col order-status-line'></div>
        <div class='col-auto d-flex flex-column align-items-center justify-content-center order-status-item " . ( $active === "second" ? 'active' : '' ) . "'>
            <span class='fal fa-address-book'></span>
            <p>تکمیل اطلاعات و ثبت آدرس</p>
        </div>
        <div class='col order-status-line'></div>
        <div class='col-auto d-flex flex-column align-items-center justify-content-center order-status-item " . ( $active === "third" ? 'active' : '' ) . "'>
            <span class='fal fa-user-cog'></span>
            <p>در انتظار تایید اپراتور</p>
        </div>
    </section>";
}
<?php

add_filter( 'woocommerce_calculated_total', 'change_calculated_total', 10, 2 );
function change_calculated_total( $total ) {
    if ( user_is_wholesaler() ) {
        // var_dump( $_GET );
        if ( isset( $_GET[ "pay_way" ] ) ) {
            return calc_wholesaler_total_cart( $_GET[ "pay_way" ] );
        }
        if ( isset( $_SESSION[ "pay_way" ] ) ) {
            return calc_wholesaler_total_cart( $_SESSION[ "pay_way" ] );
        }
        return calc_wholesaler_total_cart( "cash" );
    }
    return $total;
}

function calc_wholesaler_total_cart( $which_pay = "cash" ) {
    $price = 0;
    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

        switch( $which_pay ) {
            default:
            case "cash":
                $price += intval( $_product->get_meta( '_major_price_cash' ) ) * intval( $cart_item['quantity'] );
                break;
            case "45":
                $price += intval( $_product->get_meta( '_major_price_45' ) ) * intval( $cart_item['quantity'] );
                break;
            case "90":
                $price += intval( $_product->get_meta( '_major_price_90' ) ) * intval( $cart_item['quantity'] );
                break;
        }

    }
    return $price;
}

add_action( 'wp_footer', 'add_js_to_footer' ); 
 
function add_js_to_footer() {
    if ( user_is_wholesaler() ) {
        if ( is_cart() || ( is_cart() && is_checkout() ) ) {
            enqueue_js_cart();
        }
        enqueue_js_add_banner();

    }
}

function enqueue_js_cart() {
    wp_enqueue_script( 'cart-calc', AMHNJ_MULTIPLE_PRICE_PLUGIN_DIR_URL . 'js/cart-calc.js', array(), '1.0.0');
}

function enqueue_js_add_banner() {
    wp_enqueue_script( 'add-banner-wholesaler', AMHNJ_MULTIPLE_PRICE_PLUGIN_DIR_URL . 'js/add-banner-after-payment.js', array(), '1.0.0');
}
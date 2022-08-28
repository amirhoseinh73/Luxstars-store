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

// function so_27023433_disable_checkout_script(){
//     wp_dequeue_script( 'wc-checkout' );
//     wp_dequeue_script( 'wc-cart-fragments' );
// }
// add_action( 'wp_enqueue_scripts', 'so_27023433_disable_checkout_script' );

// add_action( 'wp_print_footer_scripts', function(){
//     wp_dequeue_script( 'wc-cart-fragments' );
// } );

// function filter_woocommerce_update_cart_action_cart_updated( $cart_updated ) {
//     $cart_updated = false;

//     if ( $cart_updated == false ) {
//         if ( user_is_wholesaler() ) {
//             $payment_way = $_POST[ "wholesaler_payment_way" ];
//             if ( isset( $payment_way ) && ! empty( $payment_way ) ) {
//                 add_filter( 'woocommerce_calculated_total', 'change_calculated_total', 10, 2 );
//                 function change_calculated_total( $total, $payment_way ) {
//                     var_dump( $payment_way );
//                     return calc_wholesaler_total_cart( $payment_way );
//                 }
//             }
//             wc_add_notice( __( "sawfa", 'woocommerce' ), 'error' );
//         }

//         wc_add_notice( __( "bbb", 'woocommerce' ), 'error' );
//     }

//     return $cart_updated;
// }
// add_filter( 'woocommerce_update_cart_action_cart_updated', 'filter_woocommerce_update_cart_action_cart_updated', 10, 1 );

// Change the line total price
// add_filter( 'woocommerce_get_discounted_price', 'calculate_discounted_price', 10, 2 );
// // Display the line total price
// add_filter( 'woocommerce_cart_item_subtotal', 'display_discounted_price', 10, 2 );

// function calculate_discounted_price( $price, $values ) {
//     // You have all your data on $values;
//     $price /= 10;
//     return $price;
// }

// // wc_price => format the price with your own currency
// function display_discounted_price( $values, $item ) {
//     return wc_price( $item[ 'line_total' ] );
// }

// function woo_add_cart_fee() {
//     global $woocommerce;
//     $woocommerce->cart->add_fee( __('Shipping Cost', 'woocommerce'), 100 );
//   }
//   add_action( 'woocommerce_before_calculate_totals', 'woo_add_cart_fee');


// add_filter('woocommerce_cart_item_subtotal','additional_shipping_cost',10,3);
// function additional_shipping_cost($subtotal, $values, $cart_item_key) {
//     global $post;
//     //Get the custom field value
//     $custom_shipping_cost = get_post_meta($post->ID, 'custom_shipping_cost', true);

//     //Just for testing, you can remove this line
//     $custom_shipping_cost = 10;

//     //Check if we have a custom shipping cost, if so, display it below the item price
//     if ($custom_shipping_cost) {
//         return $subtotal.'<br>+'.woocommerce_price($custom_shipping_cost).' Shipping Cost';
//     } else {
//         return $subtotal;   
//     }
// }

// function woo_add_cart_fee() {
//     global $woocommerce;

//     $extra_shipping_cost = 0;
//     //Loop through the cart to find out the extra costs
//     foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
//         //Get the product info
//         $_product = $values['data'];

//         //Get the custom field value
//         $custom_shipping_cost = get_post_meta($_product->id, 'custom_shipping_cost', true);

//         //Just for testing, you can remove this line
//         $custom_shipping_cost = 10;

//         //Adding together the extra costs
//         $extra_shipping_cost = $extra_shipping_cost + $custom_shipping_cost;
//     }

//     //Lets check if we actually have a fee, then add it
//     if ($extra_shipping_cost) {
//         $woocommerce->cart->add_fee( __('Shipping Cost', 'woocommerce'), $extra_shipping_cost );
//     }
// }
// add_action( 'woocommerce_before_calculate_totals', 'woo_add_cart_fee');
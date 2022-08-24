<?php

add_filter( 'woocommerce_calculated_total', 'change_calculated_total', 10, 2 );
function change_calculated_total( $total, $cart ) {
    return $total + 300;
}

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
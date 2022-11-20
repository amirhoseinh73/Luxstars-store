<?php

add_action( 'woocommerce_product_options_general_product_data', 'custom_add_major_price_fields' );
function custom_add_major_price_fields() {
    // Print a custom text field
    woocommerce_wp_text_input( array(
        'id' => '_major_price_cash',
        'label' => 'قیمت عمده فروشی نقدی',
        'description' => 'قیمت عمده فروشی در پرداخت نقدی',
        'desc_tip' => 'true',
        'placeholder' => '10000'
    ) );
    woocommerce_wp_text_input( array(
        'id' => '_major_price_45',
        'label' => 'قیمت عمده فروشی 45 روزه',
        'description' => 'قیمت عمده فروشی در پرداخت 45 روزه',
        'desc_tip' => 'true',
        'placeholder' => '10000'
    ) );
    woocommerce_wp_text_input( array(
        'id' => '_major_price_90',
        'label' => 'قیمت عمده فروشی 90 روزه',
        'description' => 'قیمت عمده فروشی در پرداخت 90 روزه',
        'desc_tip' => 'true',
        'placeholder' => '10000'
    ) );
}

add_action( 'woocommerce_process_product_meta', 'custom_save_major_price_fields', 10, 1 );
function custom_save_major_price_fields( $post_id ) {
    $product = wc_get_product( $post_id );

    $major_price_cash = isset( $_POST['_major_price_cash'] ) ? $_POST['_major_price_cash'] : '';
    $major_price_45 = isset( $_POST['_major_price_45'] ) ? $_POST['_major_price_45'] : '';
    $major_price_90 = isset( $_POST['_major_price_90'] ) ? $_POST['_major_price_90'] : '';

    $product->update_meta_data( '_major_price_cash', sanitize_text_field( $major_price_cash ) );
    $product->update_meta_data( '_major_price_45', sanitize_text_field( $major_price_45 ) );
    $product->update_meta_data( '_major_price_90', sanitize_text_field( $major_price_90 ) );
    $product->save();
}

function major_prices() {
    global $post;

    // Get product
    $product = wc_get_product( $post->ID );

    // Check for the custom field value
    $major_price_cash = $product->get_meta( '_major_price_cash' );
    $major_price_45 = $product->get_meta( '_major_price_45' );
    $major_price_90 = $product->get_meta( '_major_price_90' );

    // Display
    // if( $major_price_cash ) {
    //     echo '<p class="major-price">' . $major_price_cash . '</p>';
    // }

    return (object)array(
        "_cash" => $major_price_cash ?: 0,
        "_45" => $major_price_45 ?: 0,
        "_90" => $major_price_90 ?: 0
    );
}
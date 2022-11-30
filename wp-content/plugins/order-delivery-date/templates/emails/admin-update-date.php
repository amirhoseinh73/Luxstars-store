<?php
/**
 * Order Delivery Date Pro for WooCommerce
 *
 * HTML template for an email sent to the admin or customer when the delivery details are edited.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Pro-for-WooCommerce/Templates/Emails/Admin-Update-Date
 * @since       5.7
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$order = new WC_order( $order_id );
$delivery_date_formatted = orddd_common::orddd_get_order_delivery_date( $order_id );
$order_page_time_slot = orddd_common::orddd_get_order_timeslot( $order_id );
if( 'admin' == $updated_by ) {
    $opening_paragraph = __( 'The Delivery Date & Time has been updated by the Administrator. The details of the order and the updated delivery details are as follows:', 'order-delivery-date' );
} else {
    $opening_paragraph = __( 'The Delivery Date & Time has been updated by the customer. The details of the order and the updated delivery details are as follows:', 'order-delivery-date' );
}

$date_field_label = orddd_custom_delivery_functions::orddd_fetch_delivery_date_field_label( $order_id );
$time_field_label = orddd_custom_delivery_functions::orddd_fetch_time_slot_field_label( $order_id );

do_action( 'woocommerce_email_header', $email_heading );

?><p><?php echo $opening_paragraph; ?></p><?php

do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

if( $delivery_date_formatted != '' ) {
    ?> 
    <p>
        <strong><?php echo __( $date_field_label, 'order-delivery-date' ); ?> </strong><?php echo $delivery_date_formatted; ?>
    </p>
    <?php 
}

if( $order_page_time_slot != "" && $order_page_time_slot != '' ) {
    ?>
    <p>
        <strong><?php echo __( $time_field_label, 'order-delivery-date' ); ?> </strong><?php echo $order_page_time_slot; ?>
    </p>
    <?php 
}

do_action( 'woocommerce_email_footer' );
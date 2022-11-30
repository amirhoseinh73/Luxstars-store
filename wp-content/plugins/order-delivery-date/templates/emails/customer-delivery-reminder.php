<?php
/**
 * Customer delivery confirmed email
 */

do_action( 'woocommerce_email_header', $email_heading, $email );
if( $message !== "" ) {
	echo wpautop( wptexturize( $message ) );
} else {
	if ( $delivery ) {
		$billing_first_name = ( version_compare( WOOCOMMERCE_VERSION, "3.0.0" ) < 0 ) ? $delivery->billing_first_name : $delivery->get_billing_first_name(); ?>
		<p><?php printf( __( 'Hello %s,', 'order-delivery-date' ), $billing_first_name ); ?></p><?php
	}

	$delivery_date = orddd_common::orddd_get_order_delivery_date( $delivery->get_id() );
	$delivery_time = orddd_common::orddd_get_order_timeslot( $delivery->get_id() );

	?>
	<p><?php printf( __( 'You have an upcoming delivery on %s %s.', 'order-delivery-date' ), $delivery_date, $delivery_time );?></p>
	<p><?php _e( 'The details of your order are shown below.', 'order-delivery-date' ); ?></p>
	<?php
	do_action( 'woocommerce_email_order_details', $delivery, $sent_to_admin, $plain_text, $email );
}
?><p><a href="<?php echo $delivery->get_view_order_url(); ?>"> View Order </a></p><?php
do_action( 'woocommerce_email_footer', $email ); ?> 
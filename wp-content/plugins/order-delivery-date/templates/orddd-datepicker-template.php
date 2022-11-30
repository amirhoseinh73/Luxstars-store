<?php
/**
 * Template for Date & time fields on Cart, Checkout & My Account page.
 *
 * @package Order-Delivery-Date/Templates
 */

?>
<div id="orddd-checkout-fields">

<?php
/** Delivery Date Field */
do_action( 'orddd_before_checkout_delivery_date', $checkout );

if ( 'yes' === $is_dropdown ) {
	if ( is_cart() ) {
		$custom_attributes = array( 'style' => 'max-width:300px;' );
	} else {
		$custom_attributes = array();
	}

	woocommerce_form_field(
		'e_deliverydate',
		array(
			'type'              => 'select',
			'label'             => __( $date_field_label, 'order-delivery-date' ),
			'required'          => $validate_date_field,
			'options'           => $options,
			'placeholder'       => __( get_option( 'orddd_delivery_date_field_placeholder' ), 'order-delivery-date' ),
			'custom_attributes' => $custom_attributes,
			'class'             => array( 'form-row-wide' ),
		)
	);
} else {
	if ( is_cart() ) {
		$custom_attributes = array(
			'style'    => 'cursor:text !important;max-width:300px;  background:transparent;',
			'readonly' => 'readonly',
		);
	} else {
		$custom_attributes = array(
			'style'    => 'cursor:text !important; background:transparent;',
			'readonly' => 'readonly',
		);
	}

	woocommerce_form_field(
		'e_deliverydate',
		array(
			'type'              => 'text',
			'label'             => __( $date_field_label, 'order-delivery-date' ),
			'required'          => $validate_date_field,
			'placeholder'       => __( get_option( 'orddd_delivery_date_field_placeholder' ), 'order-delivery-date' ),
			'custom_attributes' => $custom_attributes,
			'autocomplete'      => 'off',
			'class'             => array( 'form-row-wide' ),
		)
	);
}

if ( $is_inline ) {
	echo '<div id="orddd_datepicker"></div>';
}

do_action( 'orddd_after_checkout_delivery_date', $checkout );

/** Time Slot Feild */

if ( $time_slot_enabled ) {
	if ( is_cart() ) {
		$custom_attributes = array(
			'disabled' => 'disabled',
			'style'    => 'cursor:not-allowed !important;max-width:300px;',
		);
	} else {
		$custom_attributes = array(
			'disabled' => 'disabled',
			'style'    => 'cursor:not-allowed !important;',
		);
	}

	do_action( 'orddd_before_checkout_time_slot', $checkout );

	woocommerce_form_field(
		'orddd_time_slot',
		array(
			'type'              => 'select',
			'label'             => __( $time_field_label, 'order-delivery-date' ),
			'required'          => $validate_time_field,
			'options'           => $time_slot_options,
			'validate'          => array( 'required' ),
			'custom_attributes' => $custom_attributes,
			'class'             => array( 'form-row-wide' ),
		)
	);

	do_action( 'orddd_after_checkout_time_slot', $checkout );

}
?>

</div>

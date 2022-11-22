<?php

if ( function_exists( "user_is_wholesaler" ) && ! user_is_wholesaler() ) {
    //add inputs for colleague code to checkout page
    add_action( "woocommerce_before_checkout_form", "active_colleague_code" );
    add_action( "woocommerce_after_checkout_billing_form", "active_colleague_code_hidden" );
    
    //submit colleague code and update wallet colleague user
    add_action( 'woocommerce_checkout_update_order_meta', 'submitColleagueCodeInOrder', 10, 2 );

    //add ajax in checkout page for submit colleague
    add_action('wp_footer', 'add_ajax_for_collegue_discount' );

    //ajax action
    add_action('wp_ajax_ajax_submit_colleague_code_to_order', 'addDiscountToOrderWithColleagueCode');
    add_action( 'wp_ajax_nopriv_ajax_submit_colleague_code_to_order', 'addDiscountToOrderWithColleagueCode' );
}

function active_colleague_code() {
   echo "<section class='row mx-0 my-4 woocommerce-info'>
      <div class='col-auto pr-0'><p class='text-dark fw-500'>در صورتی که کد معرف دارید، وارد کنید:</p></div>
      <div class='col-auto'><input id='amhnj_colleague_code_customer' type='text' class='form-control form-control-sm custom-input-box-shadow' /></div>
      <div class='col-auto'><button id='ajax-discount-colleague' type='button' class='btn-boot-3'>اعمال تخفیف</button></div>
   </section>";
}

function active_colleague_code_hidden() {
   echo "<input id='amhnj_colleague_code_customer_hidden' name='amhnj_colleague_code_customer_hidden' type='hidden' value='' />";
   echo "<script type='text/javascript'>fillHiddenInputByShowedInput()</script>";
}

function submitColleagueCodeInOrder( $order_id, $posted ) {
    if ( ! isset( $_POST[ "amhnj_colleague_code_customer_hidden" ] ) ) return;

    $colleagueCode = $_POST[ "amhnj_colleague_code_customer_hidden" ];
    if ( ! empty( $colleagueCode ) ) {
        $order = wc_get_order( $order_id );
        $order->update_meta_data( 'colleague-code', $colleagueCode );
        $order->save();

        $updateWalletColleagueFN = function() use( $order, $colleagueCode ) {
            $userColleague = getUserByMetaData( "colleague-code", $colleagueCode );
            if ( empty( $userColleague ) ) return false;
            $orderAmount = $order->get_total(); //- $order->get_shipping_total()
            $oldWalletAmount = get_user_meta( $userColleague->ID, "wallet-amount" );
            $newWalletAmount = intval( $oldWalletAmount[ 0 ] ) + ( intval( $orderAmount ) * 10 / 100 );

            return update_user_meta( $userColleague->ID, "wallet-amount", $newWalletAmount );
        };

        $resultIsColleagueTrue = $updateWalletColleagueFN();
        // if ( ! $resultIsColleagueTrue ) return;
   }
}

function add_ajax_for_collegue_discount() {
    // Only on Checkout
    if( ! ( is_checkout() && ! is_wc_endpoint_url() ) ) return;
    ?>
    <script type="text/javascript">
    jQuery(function($){
        if (typeof wc_checkout_params === 'undefined') return false;

        $(document.body).on("click", "#ajax-discount-colleague" ,function(evt) {
            evt.preventDefault();

            const $form = $( "form.woocommerce-checkout" );
            if ( $form.is( ".processing" ) ) return

            const $colleagueForm = $( this ).parents( ".woocommerce-info" )
            if ( ! $colleagueForm || $colleagueForm.is( ".processing" ) ) return

            $.ajax({
                type:    'POST',
                url: wc_checkout_params.ajax_url,
                contentType: "application/x-www-form-urlencoded; charset=UTF-8",
                enctype: 'multipart/form-data',
                dataType: "json",
                data: {
                    'action': 'ajax_submit_colleague_code_to_order',
                    'fields': $('form.checkout').serializeArray(),
                    'user_id': <?php echo get_current_user_id(); ?>,
                    'amhnj_colleague_code_customer_hidden': $( "#amhnj_colleague_code_customer_hidden" ).val()
                },
                beforeSend: function() {
                    $form.addClass( "processing" ).block({
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    });
                    $colleagueForm.addClass( "processing" ).block({
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    });
                    $( ".woocommerce-error" ).remove()
                    $( ".woocommerce-checkout .amhnj-checkout-alert" ).remove();
                },
                success: function (result) {
                    $( ".woocommerce-checkout .page-content .woocommerce" ).prepend( result.message );
                    if ( result.status === "success" ) jQuery('body').trigger('update_checkout');
                },
                error: function(error) {
                    console.log(error); // For testing (to be removed)
                },
                complete: function() {
                    $form.removeClass( "processing" ).unblock();
                    $colleagueForm.removeClass( "processing" ).unblock();
                }
            });
        });
    });
    </script>
    <?php
}

function addDiscountToOrderWithColleagueCode() {
    //check discount is calculate to customer or not
    if ( ! isset( $_POST[ "amhnj_colleague_code_customer_hidden" ] ) ) return;

    $colleagueCode = $_POST[ "amhnj_colleague_code_customer_hidden" ];
    if ( empty( $colleagueCode ) ) {
        echo returnResultAjaxJSON( "failed", "<p class='woocommerce-error amhnj-checkout-alert'>کد معرف وارد شده صحیح نیست!</p>" );
        die;
    }

    $colleagueCode = trim( $colleagueCode );

    $userColleague = (array)getUserByMetaData( "colleague-code", $colleagueCode );
    if ( empty( $userColleague ) ) {
        echo returnResultAjaxJSON( "failed", "<p class='woocommerce-error amhnj-checkout-alert'>کد معرف وارد شده صحیح نیست!</p>" );
        die;
    }

    $addDiscount = function () use( $colleagueCode ) {
		if ( ! defined( 'DOING_AJAX'  ) ) {
			return;
		}
		
        if ( ! wc_get_coupon_id_by_code( $colleagueCode ) ) {
            $coupon = new WC_Coupon();
            $coupon->set_code( $colleagueCode ); // Coupon code
            $coupon->set_discount_type( "percent" ); // Coupon type %
            $coupon->set_amount( 10 ); // Discount amount
            // $coupon->set_usage_limit( 1 );
            $coupon->save();
        }

		if ( ! in_array( $colleagueCode, WC()->cart->get_applied_coupons() ) ) {
            WC()->cart->apply_coupon( $colleagueCode );
        }
	};
    
    $addDiscount();
    
    echo returnResultAjaxJSON( "success", "<p class='woocommerce-message amhnj-checkout-alert'>10 درصد تخفیف برای سفارش شما به دلیل معرف برای شما اعمال شد.</p>" );
    die;
}
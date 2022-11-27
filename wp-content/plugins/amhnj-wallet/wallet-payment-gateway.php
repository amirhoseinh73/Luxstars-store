<?php

add_filter( 'woocommerce_payment_gateways', 'add_payment_gateway_wallet' );
function add_payment_gateway_wallet( $gateways ) {
	$gateways[] = 'AMHNJ_Wallet_Gateway'; // your class name is here
	return $gateways;
}

add_action( 'plugins_loaded', 'paymentGateWayClass' );
function paymentGateWayClass() {

	class AMHNJ_Wallet_Gateway extends WC_Payment_Gateway {

 		public function __construct() {

            $this->id = 'amhnj_wallet_gateway';
            $this->icon = '';
            $this->has_fields = false;
            $this->method_title = 'Wallet Gateway';
            $this->method_description = 'پرداخت سفارش توسط کیف پول';

            $this->supports = array(
                'products'
            );

            $this->init_form_fields();

            $this->init_settings();
            $this->title = $this->get_option( 'title' );
            $this->description = $this->get_option( 'description' );
            $this->enabled = $this->get_option( 'enabled' );
            // $this->testmode = 'yes' === $this->get_option( 'testmode' );
            // $this->private_key = $this->testmode ? $this->get_option( 'test_private_key' ) : $this->get_option( 'private_key' );
            // $this->publishable_key = $this->testmode ? $this->get_option( 'test_publishable_key' ) : $this->get_option( 'publishable_key' );

            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

            add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
            
            // You can also register a webhook here
            // add_action( 'woocommerce_api_{webhook name}', array( $this, 'webhook' ) );

 		}

 		public function init_form_fields(){

		    $this->form_fields = array(
                'enabled' => array(
                    'title'       => 'Enable/Disable',
                    'label'       => 'Enable Wallet Gateway',
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'This controls the title which the user sees during checkout.',
                    'default'     => 'کیف پول',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'This controls the description which the user sees during checkout.',
                    'default'     => 'پرداخت توسط کیف پول',
                ),
                // 'testmode' => array(
                //     'title'       => 'Test mode',
                //     'label'       => 'Enable Test Mode',
                //     'type'        => 'checkbox',
                //     'description' => 'Place the payment gateway in test mode using test API keys.',
                //     'default'     => 'yes',
                //     'desc_tip'    => true,
                // ),
                // 'test_publishable_key' => array(
                //     'title'       => 'Test Publishable Key',
                //     'type'        => 'text'
                // ),
                // 'test_private_key' => array(
                //     'title'       => 'Test Private Key',
                //     'type'        => 'password',
                // ),
                // 'publishable_key' => array(
                //     'title'       => 'Live Publishable Key',
                //     'type'        => 'text'
                // ),
                // 'private_key' => array(
                //     'title'       => 'Live Private Key',
                //     'type'        => 'password'
                // )
            );
	
	 	}

		public function payment_fields() {

            if ( $this->description ) {
                if ( $this->testmode ) {
                    $this->description .= ' TEST MODE ENABLED. In test mode, you can use the card numbers listed in <a href="#">documentation</a>.';
                    $this->description  = trim( $this->description );
                }
                echo wpautop( wp_kses_post( $this->description ) );
            }
        
            // echo '<fieldset id="wc-' . esc_attr( $this->id ) . '-wallet-form" class="wc-wallet-card-form wc-payment-form" style="background:red;">';
            echo "<div class='background-color:transparent'>";

            do_action( 'woocommerce_wallet_gateway_form_start', $this->id );
            // do_action( 'woocommerce_credit_card_form_start', $this->id );
        
            // echo '<div class="form-row form-row-wide"><label>Card Number <span class="required">*</span></label>
            //     <input id="misha_ccNo" type="text" autocomplete="off">
            //     </div>
            //     <div class="form-row form-row-first">
            //         <label>Expiry Date <span class="required">*</span></label>
            //         <input id="misha_expdate" type="text" autocomplete="off" placeholder="MM / YY">
            //     </div>
            //     <div class="form-row form-row-last">
            //         <label>Card Code (CVC) <span class="required">*</span></label>
            //         <input id="misha_cvv" type="password" autocomplete="off" placeholder="CVC">
            //     </div>
            //     <div class="clear"></div>';
            $walletAmount = getWalletAmount();
            echo "موجودی کیف پول شما: ";
            echo number_format( $walletAmount );
            echo " تومان";
        
            do_action( 'woocommerce_wallet_gateway_form_ثدی', $this->id );
            // do_action( 'woocommerce_credit_card_form_end', $this->id );
        
            // echo '<div class="clear"></div></fieldset>';
				 
		}

		/*
		 * Custom CSS and JS, in most cases required only when you decided to go with a custom credit card form
		 */
	 	public function payment_scripts() {

		    if ( ! is_checkout() && ! isset( $_GET['pay_for_order'] ) ) {
                return;
            }
        
            if ( 'no' === $this->enabled ) {
                return;
            }
        
            // if ( empty( $this->private_key ) || empty( $this->publishable_key ) ) {
            //     return;
            // }
        
            if ( ! $this->testmode && ! is_ssl() ) {
                return;
            }

            return;
        
            // wp_enqueue_script( 'misha_js', 'https://www.mishapayments.com/api/token.js' );
        
            // and this is our custom JS in your plugin directory that works with token.js
            // wp_register_script( 'woocommerce_misha', plugins_url( 'misha.js', __FILE__ ), array( 'jquery', 'misha_js' ) );
        
            // in most payment processors you have to use PUBLIC KEY to obtain a token
            // wp_localize_script( 'woocommerce_misha', 'misha_params', array(
            //     'publishableKey' => $this->publishable_key
            // ) );
        
            // wp_enqueue_script( 'woocommerce_misha' );
	
	 	}

		public function validate_fields() {

            $walletAmount = getWalletAmount();
            if( $walletAmount === 0 ) {
                wc_add_notice(  'کیف پول شما خالیست!', 'error' );
                return false;
            }
            return true;

		}

		public function process_payment( $order_id ) {

            global $woocommerce;
 
            $order = wc_get_order( $order_id );
         
            // $args = array();


            // $response = wp_remote_post( '{payment processor endpoint}', $args );
         
            // if( !is_wp_error( $response ) ) {
         
                //  $body = json_decode( $response['body'], true );
         
                 // it could be different depending on your payment processor
                //  if ( $body['response']['responseCode'] == 'APPROVED' ) {
         
            $walletAmount = getWalletAmount();
            
            $orderAmount = intval( $order->get_total() );

            if ( $walletAmount < $orderAmount ) {
                wc_add_notice(  'موجودی کیف پول شما کافی نیست!', 'error' );
                return;
            }

            updateWalletAmount( $walletAmount - $orderAmount );
            
            $order->payment_complete();
            $order->reduce_order_stock();
    
            // $order->add_order_note( 'Hey, your order is paid! Thank you!', true );
    
            $woocommerce->cart->empty_cart();
    
            // Redirect to the thank you page
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url( $order )
            );
         
                //  } else {
                //     wc_add_notice(  'Please try again.', 'error' );
                //     return;
                // }
         
            // } else {
            //     wc_add_notice( 'Connection error.', 'error' );
            //     return;
            // }
					
	 	}

		/*
		 * In case you need a webhook, like PayPal IPN etc
		 */
		public function webhook() {

            $order = wc_get_order( $_GET['id'] );
            $order->payment_complete();
            $order->reduce_order_stock();
        
            update_option('webhook_debug', $_GET);
					
	 	}
 	}
}
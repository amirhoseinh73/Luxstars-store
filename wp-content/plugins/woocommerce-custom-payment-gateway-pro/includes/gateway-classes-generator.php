<?php
$gateways = json_decode(get_option('wpruby_generated_custom_gatwayes'));
if($gateways){
    foreach($gateways as $gateway){
        $class_name =  'custom_' . md5($gateway->name);
        eval("
            class ".$class_name." extends WC_Custom_Payment_Gateway{
                public function __construct(){
                    parent::__construct(true);
                    \$this->id = '".substr($class_name, 0, 22)."';
                    \$this->method_title = '".$gateway->name."';
                    \$this->title = __('Custom Payment','woocommerce-custom-payment-gateway');
                    \$this->has_fields = true;

                    \$this->init_form_fields();
                    \$this->init_settings();


					\$this->enabled = \$this->get_option('enabled');
					\$this->title = \$this->get_option('title');
					\$this->gateway_icon = \$this->get_option('gateway_icon');
					\$this->debug_mode = \$this->get_option('debug_mode');
			
			
					\$this->description = \$this->get_option('description');
					\$this->order_status = \$this->get_option('order_status');
					\$this->customer_note = \$this->get_option('customer_note');
					\$this->customized_form = \$this->get_option('customized_form');
			
					\$this->enable_api = \$this->get_option('enable_api');
					\$this->redirect_to_api_url = \$this->get_option('redirect_to_api_url');
			
					\$this->api_url_to_ping = \$this->get_option('api_url_to_ping');
					\$this->api_method = \$this->get_option('api_method');
					\$this->api_post_data_type = \$this->get_option('api_post_data_type');
			
			
					\$this->extra_api_atts = \$this->get_option('extra_api_atts');
					\$this->wc_api_atts = \$this->get_option('wc_api_atts');

                    // Debug mode, only administrators can use the gateway.
                    if(\$this->debug_mode == 'yes'){
                        if( !current_user_can('administrator') ){
                            \$this->enabled = 'no';
                        }
                    }

                    add_action('woocommerce_update_options_payment_gateways_'.\$this->id, array(\$this, 'process_admin_options'));
                    add_action( 'woocommerce_receipt_'.\$this->id, array(\$this, 'receipt_page') );

                }
            }
        ");
    }
}
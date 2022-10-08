<?php

class WC_Custom_Payment_Gateway extends WC_Payment_Gateway {


	public $sizes = array( 'small', 'medium', 'large' );
	public $required_options = array( 'yes', 'no' );
	public $date_formats = array( 'mm/dd/yy', 'dd/mm/yy', 'yy-mm-dd', 'd M, y', 'd MM, y', 'DD, d MM, yy', );

	public $data = array();
	protected $api_data = array();
	protected $customized_form = array();
	protected $debug_mode;
	protected $order_status;
	protected $customer_note;
	protected $enable_api;
	protected $redirect_to_api_url;
	protected $api_url_to_ping;
	protected $api_method;
	protected $api_post_data_type;
	protected $extra_api_atts;
	protected $wc_api_atts;


	public function __construct( $child = false ) {
		$this->id           = 'custom_payment';
		$this->method_title = __( 'Custom Payment Pro', 'woocommerce-custom-payment-gateway' );
		$this->title        = __( 'Custom Payment', 'woocommerce-custom-payment-gateway' );
		$this->has_fields   = true;

		$this->init_form_fields();
		$this->init_settings();


		$this->enabled      = $this->get_option( 'enabled' );
		$this->title        = $this->get_option( 'title' );
		$this->gateway_icon = $this->get_option( 'gateway_icon' );
		$this->debug_mode   = $this->get_option( 'debug_mode' );


		$this->description     = $this->get_option( 'description' );
		$this->order_status    = $this->get_option( 'order_status' );
		$this->customer_note   = $this->get_option( 'customer_note' );
		$this->customized_form = $this->get_option( 'customized_form' );

		$this->enable_api          = $this->get_option( 'enable_api' );
		$this->redirect_to_api_url = $this->get_option( 'redirect_to_api_url' );

		$this->api_url_to_ping    = apply_filters( 'custom_payment_gateways_api_url', $this->get_option( 'api_url_to_ping' ) );
		$this->api_method         = $this->get_option( 'api_method' );
		$this->api_post_data_type = $this->get_option( 'api_post_data_type' );


		$this->extra_api_atts = $this->get_option( 'extra_api_atts' );
		$this->wc_api_atts    = $this->get_option( 'wc_api_atts' );

		// Debug mode, only administrators can use the gateway.
		if ( $this->debug_mode == 'yes' ) {
			if ( ! current_user_can( 'administrator' ) ) {
				$this->enabled = 'no';
			}
		}

		add_action( 'woocommerce_receipt_custom_payment', array( $this, 'receipt_page' ) );

		if ( $child === false ) {
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array(
				$this,
				'process_admin_options'
			) );
		}

		add_action( 'woocommerce_api_' . strtolower( get_class( $this ) ), array(
			$this,
			'process_returned_response'
		) );
	}

	public function receipt_page( $order ) {
		$data         = WC()->session->get( 'custom_payment_' . $this->id . '_payload' );
		$request_body = $this->get_request_body( $data, $order );

		$customgateway_args_array = array();
		foreach ( $request_body as $key => $value ) {
			$customgateway_args_array[] = '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
		}
		echo '<form action="' . $this->api_url_to_ping . '" method="post" id="customgateway_payment_form">
				' . implode( '', $customgateway_args_array ) . '
			</form>';
		echo '<script>jQuery(document).ready(function(){
			jQuery("#customgateway_payment_form").submit();

		});</script>';
	}

	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-custom-payment-gateway' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Custom Payment', 'woocommerce-custom-payment-gateway' ),
				'default' => 'no'
			),
			'title'   => array(
				'title'       => __( 'Method Title', 'woocommerce-custom-payment-gateway' ),
				'type'        => 'text',
				'description' => __( 'The title of the gateway which will show to the user on the checkout page.', 'woocommerce-custom-payment-gateway' ),
				'default'     => __( 'Custom Payment', 'woocommerce-custom-payment-gateway' ),
			),

			'gateway_icon'        => array(
				'title'       => __( 'Gateway Icon', 'woocommerce-custom-payment-gateway' ),
				'type'        => 'text',
				'description' => __( 'Icon URL for the gateway that will show to the user on the checkout page.', 'woocommerce-custom-payment-gateway' ),
				'default'     => __( 'http://', 'woocommerce-custom-payment-gateway' ),
			),
			'description'         => array(
				'title'       => __( 'Customer Message', 'woocommerce-custom-payment-gateway' ),
				'css'         => 'width:50%;',
				'type'        => 'textarea',
				'default'     => 'None of the custom payment options are suitable for you? please drop us a note about your favourable payment option and we will contact you as soon as possible.',
				'description' => __( 'The message which you want it to appear to the customer on the checkout page.', 'woocommerce-custom-payment-gateway' ),

			),
			'customer_note'       => array(
				'title'       => __( 'Customer Note', 'woocommerce-custom-payment-gateway' ),
				'type'        => 'textarea',
				'css'         => 'width:50%;',
				'default'     => '',
				'description' => __( 'A note for the customer after the Checkout process.', 'woocommerce-custom-payment-gateway' ),

			),
			'order_status'        => array(
				'title'       => __( 'Order Status After The Checkout', 'woocommerce-custom-payment-gateway' ),
				'type'        => 'select',
				'options'     => wc_get_order_statuses(),
				'default'     => 'wc-pending',
				'description' => __( 'The default order status if this gateway used in payment.', 'woocommerce-custom-payment-gateway' ),
			),
			'customized_form'     => array(
				'type' => 'customized_form',
			),
			'advanced'            => array(
				'title'       => __( 'Advanced options<hr>', 'woocommerce-custom-payment-gateway' ),
				'type'        => 'title',
				'description' => '',
			),
			'enable_api'          => array(
				'title'   => __( 'API requests', 'woocommerce-custom-payment-gateway' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable the gateway to request an API URL after the checkout process.', 'woocommerce-custom-payment-gateway' ),
				'default' => 'no'
			),
			'api_url_to_ping'     => array(
				'title'       => __( 'API URL', 'woocommerce-custom-payment-gateway' ),
				'type'        => 'text',
				'description' => __( 'The gateway will send the payment data to this URL after placing the order.', 'woocommerce-custom-payment-gateway' ),
				'default'     => '',
				'placeholder' => 'http://'
			),
			'redirect_to_api_url' => array(
				'title'   => __( 'Redirect the Customer to the API URL', 'woocommerce-custom-payment-gateway' ),
				'type'    => 'checkbox',
				'label'   => __( '', 'woocommerce-custom-payment-gateway' ),
				'default' => 'no'
			),
			'api_method'          => array(
				'title'       => __( 'Request method', 'woocommerce-custom-payment-gateway' ),
				'type'        => 'select',
				'options'     => array(
					'post' => 'POST',
					'get'  => 'GET',
				),
				'default'     => 'post',
				'description' => __( 'The request method to request the API URL.', 'woocommerce-custom-payment-gateway' ),
			),
			'api_post_data_type'  => array(
				'title'       => __( 'POST requests data type', 'woocommerce-custom-payment-gateway' ),
				'type'        => 'select',
				'options'     => array(
					'form' => 'FORM DATA',
					'json' => 'JSON',
				),
				'default'     => 'form',
				'description' => __( 'Change this only if you want to send the API data as a JSON object. 
				This option will only work if POST method is selected and if the user wont be redirected to the API URL.st', 'woocommerce-custom-payment-gateway' ),
			),
			'wc_api_atts'         => array(
				'type' => 'wc_api_atts',
			),
			'extra_api_atts'      => array(
				'type' => 'extra_api_atts',
			),
			'debug_mode' => array(
				'title'       => __( 'Enable Debug Mode', 'woocommerce-custom-payment-gateway' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable ', 'woocommerce-custom-payment-gateway' ),
				'default'     => 'no',
				'description' => __( 'If debug mode is enabled, the payment gateway will be activated just for the administrator. You can use the debug mode to make sure that the gateway work as you expected.' ),
			),
		);
	}


	/**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function admin_options() {
		include_once( dirname( __FILE__ ) . '/includes/views/admin_options_html.php' );
	}


	public function validate_fields() {
		$data = array();
		foreach ( $this->customized_form as $key => $field ) {
			// if instruction continue
			if ( $field['field_type'] == 'instructions' ) {
				continue;
			}

			// credit card needs different saving process
			if ( $field['field_type'] == 'ccform' ) {
				$cc_field   = new Credit_Card_Field();
				$field_id   = $this->id . '_' . $key;
				$cardNumber = ( isset( $_POST[ $field_id . '-card-number' ] ) ) ? $_POST[ $field_id . '-card-number' ] : '-';
				$cardExpiry = ( isset( $_POST[ $field_id . '-card-expiry' ] ) ) ? $_POST[ $field_id . '-card-expiry' ] : '-';
				$cardCVC    = ( isset( $_POST[ $field_id . '-card-cvc' ] ) ) ? $_POST[ $field_id . '-card-cvc' ] : '-';

				if ( $cardNumber == '-' || trim( $cardNumber ) == '' || ! $cc_field->is_valid( str_replace( " ", "", $cardNumber ) ) ) {
					wc_add_notice( '"Card Number"' . __( ' must be valid.', 'woocommerce-custom-payment-gateway' ), 'error' );

					return false;
				}
				if ( $cardExpiry == '-' || trim( $cardExpiry ) == '' ) {
					wc_add_notice( '"Card Expiry"' . __( ' must be not empty.', 'woocommerce-custom-payment-gateway' ), 'error' );

					return false;
				}
				if ( $cardCVC == '-' || trim( $cardCVC ) == '' ) {
					wc_add_notice( '"Card CVC"' . __( ' must be not empty.', 'woocommerce-custom-payment-gateway' ), 'error' );

					return false;
				}

				$data[]['Card Number'] = $cardNumber;
				$data[]['Card Expiry'] = $cardExpiry;
				$data[]['Card CVC']    = $cardCVC;

				if ( isset( $field['elements']['ccard-number-api-parameter']['value'] ) && trim( $field['elements']['ccard-number-api-parameter']['value'] ) != '' ) {
					$this->api_data[ $field['elements']['ccard-number-api-parameter']['value'] ] = $cardNumber;
				}
				if ( isset( $field['elements']['ccard-expiry-date-api-parameter']['value'] ) && trim( $field['elements']['ccard-expiry-date-api-parameter']['value'] ) != '' ) {
					$this->api_data[ $field['elements']['ccard-expiry-date-api-parameter']['value'] ] = $cardExpiry;
				}
				if ( isset( $field['elements']['ccard-cvc-code-api-parameter']['value'] ) && trim( $field['elements']['ccard-cvc-code-api-parameter']['value'] ) != '' ) {
					$this->api_data[ $field['elements']['ccard-cvc-code-api-parameter']['value'] ] = $cardCVC;
				}
			} else {
				$value      = isset( $_POST[ $this->id . '_' . $key ] ) ? $_POST[ $this->id . '_' . $key ] : '-';
				$field_name = ( isset( $field['elements']['name']['value'] ) && trim( $field['elements']['name']['value'] ) != '' ) ? $field['elements']['name']['value'] : ucfirst( $field['field_type'] );

				if ( $field['elements']['required']['value'] === 'yes' ) {
					if ( is_array( $value ) ) {
						if ( empty( $value ) ) {
							wc_add_notice( '"' . $field_name . '"' . __( ' must be not empty.', 'woocommerce-custom-payment-gateway' ), 'error' );

							return false;
						}
					} else {
						if ( '' === trim( $value ) ) {
							wc_add_notice( '"' . $field_name . '"' . __( ' must be not empty.', 'woocommerce-custom-payment-gateway' ), 'error' );

							return false;
						}
						if ( '-' === trim( $value ) ) {
							wc_add_notice( '"' . $field_name . '"' . __( ' is required.', 'woocommerce-custom-payment-gateway' ), 'error' );

							return false;
						}
					}
					if ( ! $this->validate_field( $field['field_type'], $value, $field_name ) ) {
						return false;
					}
				}
			}

			$data[][ $field_name ] = ( is_array( $value ) ) ? implode( ', ', $value ) : $value;
			$this->data['data']    = $data;
			if ( isset( $field['elements']['api-parameter']['value'] ) && trim( $field['elements']['api-parameter']['value'] ) != '' ) {
				$this->api_data[ $field['elements']['api-parameter']['value'] ] = ( is_array( $value ) ) ? implode( ', ', $value ) : $value;
			}

		}

		WC()->session->set( 'custom_payment_' . $this->id . '_payload', $this->api_data );
		return true;
	}


	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new WC_Order( $order_id );

		update_post_meta( (int) $order_id, 'woocommerce_customized_payment_data', $this->data );
		update_post_meta( (int) $order_id, 'woocommerce_customized_customer_note', $this->customer_note );


		// Update order status
		$order->update_status( $this->order_status );

		// Reduce stock levels
		if ( function_exists( 'wc_reduce_stock_levels' ) ) {
			wc_reduce_stock_levels( $order_id );
		} else {
			$order->reduce_order_stock();
		}


		if ( trim( $this->customer_note ) != '' ) {
			$order->add_order_note( $this->customer_note, 1 );
		}
		// Remove cart
		$woocommerce->cart->empty_cart();

		// ping to URL.

		if ( $this->api_url_to_ping && $this->enable_api == 'yes' ) {
			$is_return = $this->ping_api( $this->api_data, $order_id, $order );
			if ( isset( $is_return['redirect'] ) ) {
				return array(
					'result'   => 'success',
					'redirect' => $is_return['redirect']
				);
			}
		}

		// Return thankyou redirect
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order )
		);
	}


	public function validate_field( $field_type, $value, $field_name ) {
		$args  = array( 'name' => $field_name );
		$field = Fields_Factory::make( $field_type, $args );

		if ( ! $field instanceof Validatable ) {
			return true;
		}

		if ( ! $field->is_valid( $value ) ) {
			wc_add_notice( $field->get_invalid_message(), 'error' );

			return false;
		}

		return true;
	}

	public function payment_fields() {

		?>
		<?php if ( trim( $this->description ) != '' ): ?>
            <fieldset><?php echo $this->description; ?></fieldset>
		<?php endif; ?>
        <fieldset>
		<?php
		$current_field = 1;
		if ( is_array( $this->customized_form ) ) {
			foreach ( $this->customized_form as $key => $field ): ?>
                <p class="form-row form-row-wide">
					<?php
					$this->render_checkout_field( $field, $key, $current_field );
					$current_field ++;
					?>
                </p>
                <div class="clear"></div>
			<?php endforeach; ?>
            </fieldset>
			<?php

		}

	}

	public function render_checkout_field( $field, $key, $current_field ) {
		$field_name        = ( isset( $field['elements']['name']['value'] ) && trim( $field['elements']['name']['value'] ) != '' ) ? $field['elements']['name']['value'] : '';
		$date_format        = ( isset( $field['elements']['date-format']['value'] ) && trim( $field['elements']['date-format']['value'] ) != '' ) ? $field['elements']['date-format']['value'] : '';
		$field_default     = ( isset( $field['elements']['default-value']['value'] ) ) ? $field['elements']['default-value']['value'] : '';
		$field_id          = $this->id . '_' . $key;
		$css_class         = ( isset( $field['elements']['css-classes']['value'] ) ) ? $field['elements']['css-classes']['value'] : '';
		$field_size        = ( isset( $field['elements']['size']['value'] ) ) ? $field['elements']['size']['value'] . '-field' : '';
		$field_description = ( isset( $field['elements']['description']['value'] ) and trim( $field['elements']['description']['value'] ) !== '' ) ? '<span class="hint--top hint--info" data-hint="' . esc_attr( $field['elements']['description']['value'] ) . '">&#8505;</span>' : '';
		$required          = ( ( isset( $field['elements']['required']['value'] ) ) && $field['elements']['required']['value'] === 'yes' ) ? '<span class="required">*</span>' : '';
		$instructions      = ( isset( $field['elements']['instructions-(html-tags-allowed)'] ) ) ? $field['elements']['instructions-(html-tags-allowed)']['value'] : '';
		$options           = ( isset( $field['elements']['options'] ) ) ? $field['elements']['options']['value'] : array();
		$args              = array(
			'id'            => $field_id,
			'name'          => $field_name,
			'css_class'     => $css_class,
			'size'          => $field_size,
			'description'   => $field_description,
			'required'      => $required,
			'default_value' => $field_default,
			'instructions'  => $instructions,
			'options'       => $options,
			'date_format'   => $date_format,
		);
		$field             = Fields_Factory::make( $field['field_type'], $args );
		echo $field->get_label();
		echo $field->get_html();
	}

	public function validate_customized_form_field( $k ) {
		$fields           = array();
		$elements_counter = 0;
		foreach ( $_POST as $key => $value ) {
			if ( strpos( $key, 'field_' ) === 0 ) {
				$key_elements                                                    = explode( '_', $key );
				$field_id                                                        = $key_elements[4];
				$fields[ $field_id ]['elements'][ $key_elements[2] ]['type']     = $key_elements[3];
				$fields[ $field_id ]['elements'][ $key_elements[2] ]['function'] = $key_elements[2];
				$fields[ $field_id ]['elements'][ $key_elements[2] ]['value']    = $_POST[ $key ];
				$fields[ $field_id ]['field_type']                               = $key_elements[1];
				$elements_counter ++;
			}
		}

		return $fields;
	}

	public function validate_extra_api_atts_field( $k ) {
		$attributes = array();
		if ( ! isset( $_POST['extra_keys'] ) ) {
			return '';
		}
		if ( ! isset( $_POST['extra_values'] ) ) {
			return '';
		}
		foreach ( $_POST['extra_keys'] as $key => $value ) {
			$attributes[ $value ] = $_POST['extra_values'][ $key ];
		}

		return $attributes;
	}

	public function validate_wc_api_atts_field( $k ) {
		$attributes = array();
		if ( ! isset( $_POST['wc_keys'] ) ) {
			return '';
		}
		if ( ! isset( $_POST['wc_values'] ) ) {
			return '';
		}
		foreach ( $_POST['wc_keys'] as $key => $value ) {
			$attributes[ $value ] = $_POST['wc_values'][ $key ];
		}

		return $attributes;
	}

	public function render_field( $field, $field_id, $current_field ) {
		$field_title = '';
		$html_form   = '';
		foreach ( $field['elements'] as $key => $item ) {
			$field_type = $field['field_type'];
			$item_name  = ucfirst( str_replace( '-', ' ', $item['function'] ) );
			$field_name = 'field_' . $field_type . '_' . strtolower( $item['function'] ) . '_' . $item['type'] . '_' . $field_id;
			switch ( $item['type'] ) {
				case 'text':
					if ( $item['function'] == 'name' ) {
						if ( trim( $item['value'] ) == '' ) {
							if ( in_array( $field_type, array( 'ccform', 'url' ) ) ) {
								$field_title = strtoupper( $field_type );
							} else {
								$field_title = ucfirst( $field_type );
							}
						} else {
							$field_title = $item['value'];
						}
					}
					$html_form .= '<p class="description description-wide"><label>' . $item_name . '<br/><input class="widefat code" type="text" name="' . $field_name . '" value="' . esc_attr( $item['value'] ) . '" /></label></p>';
					break;
				case 'password':
					$html_form .= '<p class="description description-wide"><label>' . $item_name . '<br/><input class="widefat code" type="password" name="' . $field_name . '" value="' . esc_attr( $item['value'] ) . '" /></label></p>';
					break;
				case 'time':
					$html_form .= '<p class="description description-wide"><label>' . $item_name . '<br/><input class="widefat code" type="time" name="' . $field_name . '" value="' . esc_attr( $item['value'] ) . '" /></label></p>';
					break;
				case 'textarea':
					$html_form .= '<p class="description description-wide"><label>' . $item_name . '<br/><textarea class="widefat code" name="' . $field_name . '">' . stripslashes( $item['value'] ) . '</textarea></label></p>';
					break;
				case 'select':
					$options = '';
					if ( $item['function'] == 'date-format' ) {
						foreach ( $this->date_formats as $format ) {
							$options .= '<option value="' . $format . '"' . selected( $item['value'], $format, false ) . '>' . $format . '</option>';
						}
					} elseif ( $item['function'] == 'required' ) {
						foreach ( $this->required_options as $option ) {
							$options .= '<option value="' . $option . '"' . selected( $item['value'], $option, false ) . '>' . ucfirst( $option ) . '</option>';
						}
					} else {
						foreach ( $this->sizes as $size ) {
							$options .= '<option value="' . $size . '"' . selected( $item['value'], $size, false ) . '>' . ucfirst( $size ) . '</option>';
						}
					}

					$html_form .= '<p class="description description-thin"><label>' . $item_name . '<br/><select class="widefat code" name="' . $field_name . '">' . $options . '</label></select></p>';
					break;

				case 'options':
					$html_form .= '<p class="description description-wide"<label>' . $item_name . '<br/>';
					$html_form .= '<ul class="field_options" id="' . $field_name . '">';
					foreach ( $item['value'] as $option ) {
						$html_form .= '<li><input name="' . $field_name . '[]" class="code" value="' . $option . '" type="text" /><span class="delete_option dashicons dashicons-trash"></span><span class="dashicons  dashicons-menu"></span></li>';
					}
					$html_form .= '</ul>';
					$html_form .= '<a class="add-option-btn button-secondary" data-field="' . $field_name . '" href="javascript:void(0)"><span class="dashicons dashicons-plus-alt"></span>Add Option</a>';
					$html_form .= '</label></p>';
					break;
			}
		}

		$new_element = '<li id="field_' . $current_field . '" class="group ' . $field_type . '">' . '<h3>' . $field_title . ' <div class="controls"><label>' . ucfirst( $field_type ) . '</label><a href="javascript:void(0)" class="delete_field_from_header"><span class="dashicons dashicons-trash"></span></a> </div></h3>' . '<div class="form_details">' . $html_form . '<a href="javascript:void(0)" class="delete_field"><span class="dashicons dashicons-trash"></span>' . __( 'Delete', 'woocommerce-custom-payment-gateway' ) . '</a> </div>' . '</li>';

		return $new_element;
	}

	public function get_request_body( $api_data, $order_id ) {
		$request_body = array();
		if ( is_array( $this->extra_api_atts ) && ! empty( $this->extra_api_atts ) ) {
			$request_body = $this->extra_api_atts;
		}

		if ( ! empty( $api_data ) ) {
			$request_body = array_merge( $api_data, $request_body );
		}

		if ( isset( $this->wc_api_atts ) && is_array( $this->wc_api_atts ) ) {
			$wc_data = array();
			$order   = new WC_Order( $order_id );
			foreach ( $this->wc_api_atts as $key => $value ) {
				switch ( $value ) {
					case 'order_id':
						$wc_data[ $key ] = $order->get_id();
						break;
					case 'order_total':
						$wc_data[ $key ] = $order->get_total();
						break;
					case 'billing_first_name':
						$wc_data[ $key ] = $order->get_billing_first_name();
						break;
					case 'billing_last_name':
						$wc_data[ $key ] = $order->get_billing_last_name();
						break;
					case 'billing_postcode':
						$wc_data[ $key ] = $order->get_billing_postcode();
						break;
					case 'billing_address_1':
						$wc_data[ $key ] = $order->get_billing_address_1();
						break;
					case 'billing_address_2':
						$wc_data[ $key ] = $order->get_billing_address_2();
						break;
					case 'billing_city':
						$wc_data[ $key ] = $order->get_billing_city();
						break;
					case 'billing_state':
						$wc_data[ $key ] = $order->get_billing_state();
						break;
					case 'billing_country':
						$wc_data[ $key ] = $order->get_billing_country();
						break;
					case 'billing_email':
						$wc_data[ $key ] = $order->get_billing_email();
						break;
					case 'billing_phone':
						$wc_data[ $key ] = $order->get_billing_phone();
						break;
					case 'billing_ip_address':
						$wc_data[ $key ] = $order->get_customer_ip_address();
						break;
					case 'return_url':
						$wc_data[ $key ] = $this->get_return_url( $order );
						break;
					case 'customer_id':
						$wc_data[ $key ] = $order->get_customer_id();
						break;
				}
			}
			$wc_data = apply_filters( 'custom_payment_gateways_api_data', array_merge( $wc_data, $request_body ), $order_id );

			return $wc_data;

		}
	}

	public function ping_api( $api_data, $order_id, $order = false ) {
		$request_body = $this->get_request_body( $api_data, $order_id );

		if ( 'yes' === $this->redirect_to_api_url ) {

			if ( "get" === $this->api_method ) {
				return array( 'redirect' => $this->api_url_to_ping . '?' . http_build_query( $request_body ) );
			}

			return array( 'redirect' => $order->get_checkout_payment_url( true ) );
		}


		if ( $this->api_post_data_type === 'json' && $this->api_method === 'post' ) {

			$headers = array( 'Content-Type' => 'application/json; charset=utf-8' );
			wp_remote_post( $this->api_url_to_ping, array(
					'headers' => apply_filters( 'custom_payment_gateways_json_post_headers', $headers ),
					'body'    => json_encode( $request_body ),
					'method'  => 'POST',
				) );

			return null;
		}

		wp_remote_post( $this->api_url_to_ping, array(
				'method' => strtoupper( $this->api_method ),
				'body'   => $request_body,
			) );

		return null;

	}


	public function generate_customized_form_html() {
		ob_start();
		include_once( dirname( __FILE__ ) . '/includes/views/customized_form_html.php' );

		return ob_get_clean();
	}

	public function generate_extra_api_atts_html() {
		ob_start();
		include_once( dirname( __FILE__ ) . '/includes/views/extra_api_atts_html.php' );

		return ob_get_clean();
	}

	public function generate_wc_api_atts_html() {
		ob_start();
		include_once( dirname( __FILE__ ) . '/includes/views/wc_api_attributes_html.php' );

		return ob_get_clean();
	}

	public function get_icon() {

		if ( trim( $this->gateway_icon ) === 'http://' ) {
			return '';
		}

		if ( trim( $this->gateway_icon ) != '' ) {
			return '<img class="customized_payment_icon" src="' . esc_attr( $this->gateway_icon ) . '" />';
		}

		return '';
	}

	/**
	 * For developers to process returned URLs from 3rd-party gateways
	 * @since 1.3.8
	 */
	public function process_returned_response() {
		do_action( 'custom_payment_process_returned_result' );
		exit;
	}

}

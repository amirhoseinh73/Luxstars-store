<?php


class Currency_Field extends Custom_Payment_Field implements Validatable {


	public function get_name() {
		return 'Currency';
	}

	public function get_html() {
		return '<input placeholder="'.get_woocommerce_currency_symbol().'" id="'.$this->get_id().'" class="input-text small-field '.$this->get_css_class().'" type="number" min="0" name="'.$this->get_id().'" value="'.esc_attr($this->get_default_value()).'" >';
	}

	/**
	 * @param string $value
	 *
	 * @return bool
	 */
	public function is_valid( $value ) {
		return is_numeric($value);

	}

	public function get_invalid_message() {
		return __('Please enter a valid number at "','woocommerce-custom-payment-gateway') . $this->get_name();
	}
}
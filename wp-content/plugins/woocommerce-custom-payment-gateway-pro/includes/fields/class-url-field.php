<?php


class URL_Field extends Custom_Payment_Field implements Validatable {

	public function get_name() {
		return 'URL';
	}

	public function get_html() {
		return '<input placeholder="http://" id="'.$this->get_id().'" class="input-text '.$this->get_css_class().' '.$this->get_size().'" type="text" name="'.$this->get_id().'" value="'.esc_attr($this->get_default_value()).'">';
	}

	/**
	 * @param string $value
	 *
	 * @return bool
	 */
	public function is_valid( $value ) {
		return (filter_var($value, FILTER_VALIDATE_URL) === true);
	}

	public function get_invalid_message() {
		return __('Please enter a valid URL at "','woocommerce-custom-payment-gateway') . $this->get_name();
	}
}
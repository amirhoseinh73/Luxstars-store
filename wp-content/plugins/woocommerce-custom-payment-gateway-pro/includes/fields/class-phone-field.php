<?php


class Phone_Field extends Custom_Payment_Field implements Validatable {

	public function get_name() {
		return 'Phone Number';
	}

	public function get_html() {
		return '<input placeholder="" id="'.$this->get_id().'" class="input-text '.$this->get_css_class().' '.$this->get_size().'" type="text" name="'.$this->get_id().'" value="'.esc_attr($this->get_default_value()).'">';
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
<?php


class Instructions_Field extends Custom_Payment_Field {

	private $instructions;
	/**
	 * Instructions_Field constructor.
	 *
	 * @param array $arguments
	 */
	public function __construct($arguments) {
		parent::__construct($arguments);
		$this->instructions = $arguments['instructions'];
	}

	public function get_name() {
		return 'Instructions';
	}

	public function get_html() {
		return '<div class="woocommerce-info">'. stripslashes($this->instructions)  .'</div>';
	}
}
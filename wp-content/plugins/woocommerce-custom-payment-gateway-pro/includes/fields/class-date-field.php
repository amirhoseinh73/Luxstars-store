<?php


class Date_Field extends Custom_Payment_Field  {

	private $date_format = 'dd-mm-yy';

	/**
	 * Date_Field constructor.
	 *
	 * @param $arguments
	 */
	public function __construct( $arguments ) {
		parent::__construct($arguments);
		$this->date_format = (isset($arguments['date_format']))? $arguments['date_format']: $this->date_format;
	}

	public function get_html() {
		return '<input data-defaultdate="0" id="'.$this->get_id() .'" data-dateformat="'. $this->get_date_format(). '"  class="custom_payment_date_input '.$this->get_css_class().' '.$this->get_size().'" type="text" name="'.$this->get_id().'" >';
	}
	private function get_date_format(){
		return $this->date_format;
		//$field['elements']['date-format']['value']
	}
}
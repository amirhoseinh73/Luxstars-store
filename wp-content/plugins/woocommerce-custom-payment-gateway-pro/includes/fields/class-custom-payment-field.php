<?php

abstract class Custom_Payment_Field {
	private $id;
	private $is_required = false;
	private $description = '';
	private $css_class;
	private $size;
	private $default_value;
	private $options = array();
	private $name;

	public function __construct($arguments = array()) {
		$defaults = array(
			'id'            => 0,
			'name'          => '',
			'required'      => false,
			'description'   => '',
			'css_class'     =>  '',
			'size'          => 'small',
			'default_value' => '',
			'options'       => array(),
		);

		$arguments = wp_parse_args($arguments, $defaults);

		$this->id = $arguments['id'];
		$this->is_required = (bool)$arguments['required'];
		$this->description = $arguments['description'];
		$this->css_class = $arguments['css_class'];
		$this->size = $arguments['size'];
		$this->default_value = $arguments['default_value'];
		$this->options = $arguments['options'];
		$this->name = $arguments['name'];
	}

	/**
	 * @return mixed
	 */
	public function get_default_value() {
		return $this->default_value;
	}

	/**
	 * @return mixed
	 */
	public function get_size() {
		return $this->size;
	}

	public function get_name(){
		return $this->name;
	}

	public function get_id() {
		return $this->id;
	}

	public function is_required() {
		return $this->is_required;
	}

	public function get_description() {
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function get_label(){
		return	($this->get_name() != '')?'<label for="'.$this->get_id().'">'.$this->get_name() . ' ' . $this->is_required() .' '.	$this->get_description() .' </label> ': '';
	}

	public function get_css_class(){
		return $this->css_class;
	}

	/**
	 * @return array
	 */
	public function get_options(){
		return $this->options;
	}

	public abstract function get_html();

}
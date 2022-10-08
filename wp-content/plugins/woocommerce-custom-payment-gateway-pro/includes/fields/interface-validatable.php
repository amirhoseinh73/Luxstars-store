<?php


interface Validatable {
	/**
	 * @param mixed $value
	 * @return boolean
	 */
	public function is_valid($value);

	/**
	 * @return string
	 */
	public function get_invalid_message();
}
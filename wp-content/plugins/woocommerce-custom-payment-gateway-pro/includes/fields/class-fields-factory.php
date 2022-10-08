<?php


class Fields_Factory {
	/**
	 * @param string $field_type
	 *
	 * @param array $args
	 *
	 * @return null|Custom_Payment_Field
	 */
	public static function make($field_type, $args= array()){
		switch ($field_type){
			case 'signature':
				return new Signature_Field($args);
				break;
			case 'text':
				return new Text_Field($args);
			case 'password':
				return new Password_Field($args);
			case 'time':
				return new Time_Field($args);
			case 'textarea':
				return new TextArea_Field($args);
			case 'checkbox':
				return new Checkbox_Field($args);
			case 'radio':
				return new Radio_Field($args);
			case 'select':
				return new Select_Field($args);
			case 'email':
				return new Email_Field($args);
			case 'date':
				return new Date_Field($args);
			case 'currency':
				return new Currency_Field($args);
			case 'url':
				return new URL_Field($args);
			case 'phone':
				return new Phone_Field($args);
			case 'number':
				return new Number_Field($args);
			case 'instructions':
				return new Instructions_Field($args);
			case 'ccform':
				return new Credit_Card_Field($args);
		}
		return null;
	}
}
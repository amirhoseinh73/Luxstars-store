<?php


class TextArea_Field extends Custom_Payment_Field {

	public function get_name() {
		return 'Text Area';
	}

	public function get_html() {
		return '<textarea id="'.$this->get_id().'" class="input-text '.$this->get_css_class().' '.$this->get_size().'" name="'.$this->get_id().'">'.stripslashes($this->get_default_value()).'</textarea>';
	}
}
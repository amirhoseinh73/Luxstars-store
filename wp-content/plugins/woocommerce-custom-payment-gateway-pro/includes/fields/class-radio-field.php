<?php


class Radio_Field extends Custom_Payment_Field {

	public function get_name() {
		return 'Radio';
	}

	public function get_html() {

		$html = '';
		foreach($this->get_options() as $option){
			$html .= '<input id="'.$this->get_id().'" '. checked($this->get_default_value(), $option, false) .' class="input-checkbox '.$this->get_css_class().'" type="radio" name="'.$this->get_id().'" value="'.$option.'">' . $option.'<br/>';
		}
		return $html;
	}
}
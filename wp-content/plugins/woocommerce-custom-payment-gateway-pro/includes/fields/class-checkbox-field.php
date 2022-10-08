<?php


class Checkbox_Field extends Custom_Payment_Field {

	public function get_name() {
		return 'Checkbox';
	}

	public function get_html() {
		// $this->get_options was $field['elements']['options']['value']
		$html = '';
		foreach($this->get_options() as $option){
			$html .= '<input id="'.$this->get_id().'" '. checked($this->get_default_value(), $option, false) .' class="input-checkbox '.$this->get_css_class().'" type="checkbox" name="'.$this->get_id().'[]" value="'.$option.'">' . $option . '<br/>';
		}
		return $html;
	}
}
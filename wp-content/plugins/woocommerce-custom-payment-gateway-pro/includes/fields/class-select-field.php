<?php


class Select_Field extends Custom_Payment_Field {

	public function get_name() {
		return 'Select';
	}

	public function get_html() {
		// $this->get_options() was $field['elements']['options']['value']
		$html = '<select id="'.$this->get_id().'" name="'.$this->get_id().'">';
		foreach($this->get_options() as $option){
			$html .= '<option '. selected($this->get_default_value(), $option, false) .' value="'.$option.'">'.$option.'</option>';
		}
		$html .= '</select>';
		return $html;
	}
}
<?php


class UploadFile_FIELD extends Custom_Payment_Field {

	public function get_html() {
		// $this->get_options was $field['elements']['options']['value']
		return '<input id="'.$this->get_id().'" class="input-text '.$this->get_css_class().'" type="file" name="'.$this->get_id().'" accept="image/png,image/jpg,image/jpeg,application/pdf">';
	}
}
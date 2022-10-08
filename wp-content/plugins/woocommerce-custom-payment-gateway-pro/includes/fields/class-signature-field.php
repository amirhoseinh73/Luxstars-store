<?php

class Signature_Field extends Custom_Payment_Field{

	/**
	 * Signature_Field constructor.
	 *
	 * @param $arguments
	 */
	public function __construct($arguments) {
		parent::__construct($arguments);
		add_action('wp_enqueue_scripts', array($this, 'add_signature_pad_javascript'));
	}

	public function add_signature_pad_javascript(){
		wp_enqueue_script('signature_pad',plugins_url('includes/assets/js/signature_pad.min.js', __FILE__) );
	}

	public function get_html() {

		$id = $this->get_id();
		$input = '<input id="'.$this->get_id().'" class="input-hidden" type="hidden" name="'.$this->get_id().'" value="">';

		$html = $input . "
		<canvas id='signature_$id' style='background: #ffffff; width:100%; border:1px dashed #6b6666;'  height='200'></canvas>
		<a href='#' id='clear_signature_$id'>". __('Clear', 'woocommerce-custom-payment-gateway') ."</a>
		<script type='application/javascript'>
			var canvas_$id = document.getElementById('signature_$id');
			var signaturePad_$id = new SignaturePad(canvas_$id, {
			  height: 200,
			  onEnd: function() {
			    document.getElementById('". $this->get_id() ."').value = signaturePad_$id.toDataURL();
			  }
			});
			
			function resize_canvas_$id() {
			     var ratio = Math.max(window.devicePixelRatio || 1, 1);
			     canvas_$id.width = canvas_$id.offsetWidth * ratio;
			     canvas_$id.height = canvas_$id.offsetHeight * ratio;
			     canvas_$id.getContext(\"2d\").scale(ratio, ratio);
			}
			
			window.addEventListener('resize', resize_canvas_$id);
			document.getElementById('clear_signature_$id').addEventListener('click', function (e) {
			  e.preventDefault();
			  signaturePad_$id.clear();
			  document.getElementById('". $this->get_id() ."').value = '';
			});
		</script>
		";
		return $html;
	}

}
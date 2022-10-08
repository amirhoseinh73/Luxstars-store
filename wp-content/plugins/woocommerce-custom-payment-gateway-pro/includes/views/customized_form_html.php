<tr valign="top">
	<th scope="row" class="titledesc">
		<label for="woocommerce_auspost_debug_mode">Custom Form</label>
	</th>
	<td class="forminp">
		<fieldset>
			<div id="custom_payment_form_components">
				<ul class="form_components_col1">
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="text" id="form-element-text"><b></b>Text</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="password" id="form-element-password"><b></b>Password</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="checkbox" id="form-element-checkbox"><b></b>Checkbox</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="signature" id="form-element-username"><b></b>Customer Signature</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="select" id="form-element-select"><b></b>Select</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="date" id="form-element-datepicker"><b></b>Date</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="url" id="form-element-url"><b></b>URL</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="number" id="form-element-digits"><b></b>Number</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="ccform" id="form-element-file"><b></b>CCard Form</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="textarea" id="form-element-textarea"><b></b>Textarea</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="radio" id="form-element-radio"><b></b>Radio</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="email" id="form-element-email"><b></b>Email</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="time" id="form-element-time"><b></b>Time</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="currency" id="form-element-currency"><b></b>Currency</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="phone" id="form-element-phone"><b></b>Phone</a></li>
					<li><a href="javascript:void(0);" class="draggable-form-item" data-type="instructions" id="form-element-instructions"><b></b>Instructions</a></li>
				</ul>
			</div>
			<div id="custom_payment_form_fields">
				<ul id="fields_wrap">
					<?php
					$current_field = 1;
					if(is_array($this->customized_form)){
						foreach($this->customized_form as $key => $field): ?>
							<?php
							echo $this->render_field($field, $key, $current_field);
							$current_field++;
							?>
						<?php
						endforeach;
					}
					?>
				</ul>
			</div>
		</fieldset>
	</td>
</tr>
<script type="text/javascript">
  var fields_counter = <?php echo (!$this->customized_form)?0:max((array_keys($this->customized_form))); ?>;
</script>
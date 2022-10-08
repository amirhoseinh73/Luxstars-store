<tr valign="top">
	<th scope="row" class="titledesc">
		<label for="woocommerce_auspost_debug_mode"><?php _e('WooCommerce API Parameters', 'woocommerce-custom-payment-gateway'); ?></label>
	</th>
	<td class="forminp">
		<fieldset>
			<table id="customized_payment_wc_attrs" class="wp-list-table widefat fixed striped posts">
				<thead>
				<tr>
					<th class="column-key"><?php _e('Key', 'woocommerce-custom-payment-gateway'); ?></th>
					<th><?php _e('Value', 'woocommerce-custom-payment-gateway'); ?></th>
					<th class="column-featured"></th>
				</tr>
				</thead>
				<tbody>

				<?php if(isset($this->wc_api_atts) and is_array($this->wc_api_atts)): ?>
					<?php foreach($this->wc_api_atts as $key => $value): ?>
						<tr>
							<td><input name="wc_keys[]" class="widefat" value="<?php echo esc_attr($key); ?>" type="text">

							</td>
							<td><select name="wc_values[]">
									<option <?php selected($value, 'order_id'); ?> value="order_id">Order ID</option>
									<option <?php selected($value, 'order_total'); ?> value="order_total">Order Total</option>
									<option <?php selected($value, 'customer_id'); ?> value="customer_id">Customer ID</option>
									<option <?php selected($value, 'billing_first_name'); ?> value="billing_first_name">Customer First Name</option>
									<option <?php selected($value, 'billing_last_name'); ?> value="billing_last_name">Customer Last Name</option>
									<option <?php selected($value, 'billing_postcode'); ?> value="billing_postcode">Customer Postcode</option>
									<option <?php selected($value, 'billing_address_1'); ?> value="billing_address_1">Customer Address line 1</option>
									<option <?php selected($value, 'billing_address_2'); ?> value="billing_address_2">Customer Address line 2</option>
									<option <?php selected($value, 'billing_city'); ?> value="billing_city">Customer City</option>
									<option <?php selected($value, 'billing_state'); ?> value="billing_state">Customer State</option>
									<option <?php selected($value, 'billing_country'); ?> value="billing_country">Customer Country</option>
									<option <?php selected($value, 'billing_email'); ?> value="billing_email">Customer Email</option>
									<option <?php selected($value, 'billing_phone'); ?> value="billing_phone">Customer Phone</option>
									<option <?php selected($value, 'billing_ip_address'); ?> value="billing_ip_address">Customer IP Address</option>
									<option <?php selected($value, 'return_url'); ?> value="return_url">Order Return URL</option>
								</select></td>
							<td><a class="delete_api_key" href="javascript:void(0);"><span class="dashicons  dashicons-trash"></span></a></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
				<tfoot>
				<tr>
					<th colspan="3"><a id="add_row_wc_atts_button" href="javascript:void(0);" class="button"><?php _e('Add Row', 'woocommerce-custom-payment-gateway'); ?></a></th>
				</tr>
				</tfoot>
			</table>
		</fieldset>
	</td>
</tr>
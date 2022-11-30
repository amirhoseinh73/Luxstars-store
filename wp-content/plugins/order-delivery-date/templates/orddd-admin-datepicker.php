<?php
/**
 * Template for Date & time fields on Admin side.
 *
 * @package Order-Delivery-Date/Templates
 */

?>
<table id="admin_delivery_fields" >
	<tr id="admin_delivery_date_field" >
		<td><label class ="orddd_delivery_date_field_label"><?php echo $date_field_label; ?>: </label></td>
		<td>
			<input type="text" id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" class="<?php echo $field_name; ?>" readonly/>
		</td>
	</tr>

	<?php if ( 'on' === $time_slot_enabled ) { ?>

		<tr id="admin_time_slot_field">
			<td><label for="orddd_time_slot" class=""><?php echo $time_field_label; ?>: </label></td>
			<td><select name="orddd_time_slot" id="orddd_time_slot" class="orddd_admin_time_slot" disabled="disabled" placeholder="">
					<option value="select">Select a time slot</option>
				</select>
			</td>
		</tr>

	<?php } ?>

	<tr id='delivery_charges'>
		<td><label for='del_charges'><?php echo $fee_name; ?></label></td>
		<td><input type='number' min='0' value='$fee' step='0.001' id='del_charges' /></td>
	</tr>

	<tr>
		<td colspan='2'>
			<small>
				<?php echo __( 'Any change in Delivery charges here will not change the order total. You will need to update    the Item section above for delivery charges to reflect in order total.', 'order-delivery-date' ); ?>
			</small>
			<br>
			<small>
				<em><?php echo __( 'Note: If you are creating the order manually, you can update the delivery date &    time after creating the order.', 'order-delivery-date' ); ?></em>
			</small>
		</td>
	</tr>

	<tr id="save_delivery_date_button">
		<td><input type="button" value="<?php echo __( 'Update', 'order-delivery-date' ); ?>" id="save_delivery_date" class="save_button"></td>
		<td><input type="button" value="<?php echo __( 'Update & Notify Customer', 'order-delivery-date' ); ?>" id="save_delivery_date_and_notify" class="save_button"></td>
		<td><font id="orddd_update_notice"></font></td>
	</tr>

</table>
<div id="is_virtual_product"></div>

<?php
/**
 * Order Delivery Date Pro for WooCommerce
 *
 * Adds Settings table on Weekday Settings tab in Admin.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Pro-for-WooCommerce/Admin/Weekday-Settings
 * @since       6.8
 *
 * @globals array $orddd_weekdays Weekdays Array
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $orddd_weekdays;

if ( function_exists( 'wc_help_tip' ) ) {
	$weekday_help_tip          = wc_help_tip( __( 'Current Weekday as per WordPress timezone when the customer visits your website to place the order. This is a required field.', 'order-delivery-date' ) );
	$charges_help_tip          = wc_help_tip( __( 'Delivery charges to be applied when the customer selects the Delivery Date.', 'order-delivery-date' ) );
	$charges_label_help_tip    = wc_help_tip( __( 'Delivery charges label to be displayed on the checkout page for the Delivery Charges', 'order-delivery-date' ) );
	$same_day_cut_off_help_tip = wc_help_tip( __( 'Current day will be disabled if an order is placed after the time mentioned in this field. The timezone is taken from the Settings -> General ->Timezone Field. <br><br> Note: It will be applied only when the Enable Same day delivery setting is enabled from the General Settings -> Time settings link.', 'order-delivery-date' ) );
	$next_day_cut_off_help_tip = wc_help_tip( __( 'Next day will be disabled if an order is placed after the time mentioned in this field. The timezone is taken from the Settings -> General ->Timezone Field. <br><br> Note: It will be applied only when the Enable Next day delivery setting is enabled from the General Settings -> Time settings link.', 'order-delivery-date' ) );
	$weekday_before_help_tip   = wc_help_tip( __( 'First available weekday which should be enabled if an order is placed before the next day cut-off time mentioned.', 'order-delivery-date' ) );
	$weekday_after_help_tip    = wc_help_tip( __( 'First available weekday which should be enabled if an order is placed after the next day cut-off time mentioned.', 'order-delivery-date' ) );
	$minimum_time_help_tip     = wc_help_tip( __( 'Minimum number of hours required to prepare for delivery. <br><br> Note: Minimum Time column will be disabled when Same day cut-off and/or Next day cut-off setting is enabled from the General Settings -> Time settings link.', 'order-delivery-date' ) );
} else {
	$weekday_help_tip          = sprintf( '<img class="help_tip" data-tip="%s" src="%s/assets/images/help.png" height="16" width="16" />', __( 'Current Weekday as per WordPress timezone when the customer visits your website to place the order. This is a required field.', 'order-delivery-date' ), esc_url( WC()->plugin_url() ) );
	$charges_help_tip          = sprintf( '<img class="help_tip" data-tip="%s" src="%s/assets/images/help.png" height="16" width="16" />', __( 'Delivery charges to be applied when the customer selects the Delivery Date.', 'order-delivery-date' ), esc_url( WC()->plugin_url() ) );
	$charges_label_help_tip    = sprintf( '<img class="help_tip" data-tip="%s" src="%s/assets/images/help.png" height="16" width="16" />', __( 'Delivery charges label to be displayed on the checkout page for the Delivery Charges', 'order-delivery-date' ), esc_url( WC()->plugin_url() ) );
	$same_day_cut_off_help_tip = sprintf( '<img class="help_tip" data-tip="%s" src="%s/assets/images/help.png" height="16" width="16" />', __( 'Current day will be disabled if an order is placed after the time mentioned in this field. The timezone is taken from the Settings -> General ->Timezone Field. <br><br> Note: It will be applied only when the Enable Same day delivery setting is enabled from the General Settings -> Time settings link.', 'order-delivery-date' ), esc_url( WC()->plugin_url() ) );
	$next_day_cut_off_help_tip = sprintf( '<img class="help_tip" data-tip="%s" src="%s/assets/images/help.png" height="16" width="16" />', __( 'Next day will be disabled if an order is placed after the time mentioned in this field. The timezone is taken from the Settings -> General ->Timezone Field. <br><br> Note: It will be applied only when the Enable Next day delivery setting is enabled from the General Settings -> Time settings link.', 'order-delivery-date' ), esc_url( WC()->plugin_url() ) );
	$weekday_before_help_tip   = sprintf( '<img class="help_tip" data-tip="%s" src="%s/assets/images/help.png" height="16" width="16" />', __( 'First available weekday which should be enabled if an order is placed before the next day cut-off time mentioned.', 'order-delivery-date' ), esc_url( WC()->plugin_url() ) );
	$weekday_after_help_tip    = sprintf( '<img class="help_tip" data-tip="%s" src="%s/assets/images/help.png" height="16" width="16" />', __( 'First available weekday which should be enabled if an order is placed after the next day cut-off time mentioned.', 'order-delivery-date' ), esc_url( WC()->plugin_url() ) );
	$minimum_time_help_tip     = sprintf( '<img class="help_tip" data-tip="%s" src="%s/assets/images/help.png" height="16" width="16" />', __( 'Minimum number of hours required to prepare for delivery. <br><br> Note: Minimum Time column will be disabled when Same day cut-off and/or Next day cut-off setting is enabled from the General Settings -> Time settings link.', 'order-delivery-date' ), esc_url( WC()->plugin_url() ) );
}

?>

<div class="orddd_weekday_settings_heading">
	<?php esc_html_e( 'Use the below table if you want to create different settings for each weekday. You can fill in the required settings & leave the rest as blank if not needed. For example, if you only want delivery charges per weekday, but not Minimum Time or Same-day delivery, then you can enter the charges & leave other fields blank. If multiple settings are added for one Weekday, then settings which are added last will be considered.', 'order-delivery-date' ); ?>
	<a href="https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/weekday-settings/?utm_source=userwebsite&utm_medium=link&utm_campaign=OrderDeliveryDateProSetting" target="_blank" class="dashicons dashicons-external" style="line-height:unset;"></a>
</div>
<br>
<table class="orddd-advance-settings-table widefat">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Weekday', 'order-delivery-date' ); ?>&nbsp;<abbr class=\"required\" title=\"required\" style="color: red;">*</abbr>&nbsp;<?php echo wp_kses_post( $weekday_help_tip ); ?></th>
			<th><?php esc_html_e( 'Charges', 'order-delivery-date' ); ?>&nbsp;<?php echo wp_kses_post( $charges_help_tip ); ?></th>
			<th><?php esc_html_e( 'Checkout label', 'order-delivery-date' ); ?>&nbsp;<?php echo wp_kses_post( $charges_label_help_tip ); ?></th>
			<th><?php esc_html_e( 'Same day cut-off', 'order-delivery-date' ); ?>&nbsp;<?php echo wp_kses_post( $same_day_cut_off_help_tip ); ?></th>
			<th><?php esc_html_e( 'Next day cut-off', 'order-delivery-date' ); ?>&nbsp;<?php echo wp_kses_post( $next_day_cut_off_help_tip ); ?></th>
			<th><?php esc_html_e( 'Weekday before next day cut-off time', 'order-delivery-date' ); ?>&nbsp;<?php echo wp_kses_post( $weekday_before_help_tip ); ?></th>
			<th><?php esc_html_e( 'Weekday after next day cut-off time', 'order-delivery-date' ); ?>&nbsp;<?php echo wp_kses_post( $weekday_after_help_tip ); ?></th>
			<th><?php esc_html_e( 'Minimum Time', 'order-delivery-date' ); ?><?php echo wp_kses_post( $minimum_time_help_tip ); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="8"> 
				<a class="button plus orddd_advance_settings_insert"><?php esc_html_e( 'Insert row', 'order-delivery-date' ); ?></a>
				<a class="button minue orddd_advance_settings_remove"><?php esc_html_e( 'Remove selected row', 'order-delivery-date' ); ?></a>
			</th> 
		</tr>
	</tfoot>
	<tbody id="settings">
		<tr>
			<th colspan="10" style="text-align: center;"><?php esc_html_e( 'Loading&hellip;', 'woocommerce' ); ?></th>
		</tr>
	</tbody>
</table>
<br>
<div>
	<?php esc_html_e( 'If the cut-off time for Same day / Next day delivery is same for all the weekdays then ', 'order-delivery-date' ); ?>
	<a href="admin.php?page=order_delivery_date&action=general_settings&section=time_settings"><?php esc_html_e( 'click here', 'order-delivery-date' ); ?></a>
	<?php esc_html_e( 'to make the necessary changes.', 'order-delivery-date' ); ?>
</div>

<script type="text/html" id="tmpl-orddd-advance-setting-rows">
	<?php /* translators: %s: Row id */ ?>
	<tr class="orddd_advance_setting_row" data-tip="<?php echo esc_attr( sprintf( __( 'Advance Setting Row: %s', 'order-delivery-date' ), '{{ data.row_id }}' ) ); ?>" data-id="{{ data.row_id }}">
		<td class="weekdays">
			<select class="orddd_weekdays" id="orddd_weekdays" name="orddd_weekdays[{{ data.row_id }}]" placeholder="Select Weekdays" data-attribute="orddd_weekdays">
				<option name="select_weekday" value=""><?php esc_html_e( 'Weekday', 'order-delivery-date' ); ?></option>
				<?php
				foreach ( $orddd_weekdays as $n => $day_name ) {
					?>
					<option name="<?php echo esc_attr( $n ); ?>" value="<?php echo esc_attr( $n ); ?>" <# if ( data.orddd_weekdays == "<?php echo esc_attr( $n ); ?>" ) { #> selected <# } #> > <?php echo esc_attr( $day_name ); ?> </option> 
					<?php
				}
				?>
			</select>
		</td>
		<td class="additional_charges">
			<input type="text" value="{{ data.additional_charges }}" name="additional_charges[{{ data.row_id }}]" class="orddd_additional_charges" data-attribute="additional_charges" placeholder="In <?php echo esc_attr( get_woocommerce_currency_symbol() ); ?>" />
		</td>

		<td class="delivery_charges_label">
			<input type="text" value="{{ data.delivery_charges_label }}" name="delivery_charges_label[{{ data.row_id }}]" class="orddd_delivery_charges_label" data-attribute="delivery_charges_label" placeholder="Checkout Label" />
		</td>

		<td class="disable_same_day_delivery_after_hours">
			<?php
			if ( 'on' === get_option( 'orddd_enable_same_day_delivery' ) ) {
				?>
				<select class="orddd_disable_same_day_delivery_after_hours" id="orddd_disable_same_day_delivery_after_hours" name="orddd_disable_same_day_delivery_after_hours[{{ data.row_id }}]" placeholder="Select Weekdays" data-attribute="orddd_disable_same_day_delivery_after_hours">
				<?php
			} else {
				?>
				<select class="orddd_disable_same_day_delivery_after_hours" id="orddd_disable_same_day_delivery_after_hours" name="orddd_disable_same_day_delivery_after_hours[{{ data.row_id }}]" placeholder="Select Weekdays" data-attribute="orddd_disable_same_day_delivery_after_hours" disabled="disabled">
				<?php
			}
			?>
				<option name="time" value=""> <?php esc_html_e( 'Time', 'order-delivery-date' ); ?></option> 
				<?php
				$from_start_ts = strtotime( '00:00' );
				$to_end_ts     = strtotime( '23:59' );
				while ( $from_start_ts <= $to_end_ts ) {
					$time_to_display = gmdate( 'H:i', $from_start_ts );

					?>
					<option name="<?php echo esc_attr( $time_to_display ); ?>" value="<?php echo esc_attr( $time_to_display ); ?>" <# if ( data.orddd_disable_same_day_delivery_after_hours == "<?php echo esc_attr( $time_to_display ); ?>" ) { #> selected <# } #> > <?php echo esc_attr( $time_to_display ); ?> </option> 
					<?php

					$from_start_ts = $from_start_ts + 900;
				}
				?>
			</select>   
		</td>

		<td class="disable_next_day_delivery_after_hours">
			<?php
			if ( 'on' === get_option( 'orddd_enable_next_day_delivery' ) ) {
				?>
				<select class="orddd_disable_next_day_delivery_after_hours" id="orddd_disable_next_day_delivery_after_hours" name="orddd_disable_next_day_delivery_after_hours[{ data.row_id }}]" placeholder="Select Weekdays" data-attribute="orddd_disable_next_day_delivery_after_hours">
				<?php
			} else {
				?>
				<select class="orddd_disable_next_day_delivery_after_hours" id="orddd_disable_next_day_delivery_after_hours" name="orddd_disable_next_day_delivery_after_hours[{ data.row_id }}]" placeholder="Select Weekdays" data-attribute="orddd_disable_next_day_delivery_after_hours" disabled="disabled">
				<?php
			}
			?>
				<option name="time" value=""> <?php esc_html_e( 'Time', 'order-delivery-date' ); ?></option> 
				<?php
				$from_start_ts = strtotime( '00:00' );
				$to_end_ts     = strtotime( '23:59' );
				while ( $from_start_ts <= $to_end_ts ) {
					$time_to_display = gmdate( 'H:i', $from_start_ts );

					?>
					<option name="<?php echo esc_attr( $time_to_display ); ?>" value="<?php echo esc_attr( $time_to_display ); ?>" <# if ( data.orddd_disable_next_day_delivery_after_hours == "<?php echo esc_attr( $time_to_display ); ?>" ) { #> selected <# } #> > <?php echo esc_attr( $time_to_display ); ?> </option>
					<?php

					$from_start_ts = $from_start_ts + 900;
				}
				?>
			</select>   
		</td>

		<td class="before_cutoff_weekday">
			<?php
			if ( 'on' === get_option( 'orddd_enable_next_day_delivery' ) ) {
				?>
				<select class="orddd_before_cutoff_weekday" id="orddd_before_cutoff_weekday" name="orddd_before_cutoff_weekday[{ data.row_id }}]" data-attribute="orddd_before_cutoff_weekday">
				<?php
			} else {
				?>
				<select class="orddd_before_cutoff_weekday" id="orddd_before_cutoff_weekday" name="orddd_before_cutoff_weekday[{ data.row_id }}]" data-attribute="orddd_before_cutoff_weekday"  disabled="disabled">
				<?php
			}
			?>
				<option name="select_weekday" value=""><?php esc_html_e( 'Weekday', 'order-delivery-date' ); ?> </option>
				<?php
				foreach ( $orddd_weekdays as $n => $day_name ) {
					?>
					<option name="<?php echo esc_attr( $n ); ?>" value="<?php echo esc_attr( $n ); ?>" <# if ( data.orddd_before_cutoff_weekday == "<?php echo esc_attr( $n ); ?>" ) { #> selected <# } #> > <?php echo esc_attr( $day_name ); ?> </option> 
					<?php
				}
				?>
			</select>
		</td> 	

		<td class="after_cutoff_weekday">
			<?php
			if ( 'on' === get_option( 'orddd_enable_next_day_delivery' ) ) {
				?>
				<select class="orddd_after_cutoff_weekday" id="orddd_after_cutoff_weekday" name="orddd_after_cutoff_weekday[{ data.row_id }}]" data-attribute="orddd_after_cutoff_weekday">
				<?php
			} else {
				?>
				<select class="orddd_after_cutoff_weekday" id="orddd_after_cutoff_weekday" name="orddd_after_cutoff_weekday[{ data.row_id }}]" data-attribute="orddd_after_cutoff_weekday" disabled="disabled">
				<?php
			}
			?>

				<option name="select_weekday" value=""><?php esc_html_e( 'Weekday', 'order-delivery-date' ); ?> </option>
				<?php
				foreach ( $orddd_weekdays as $n => $day_name ) {
					?>
					<option name="<?php echo esc_attr( $n ); ?>" value="<?php echo esc_attr( $n ); ?>" <# if ( data.orddd_after_cutoff_weekday == "<?php echo esc_attr( $n ); ?>" ) { #> selected <# } #> > <?php echo esc_attr( $day_name ); ?> </option> 
					<?php
				}
				?>
			</select>
		</td>

		<td class="minimumOrderDays">
				<input type="text" value="{{ data.orddd_minimumOrderDays }}" name="orddd_minimumOrderDays[{{ data.row_id }}]" class="orddd_minimumOrderDays" data-attribute="orddd_minimumOrderDays" placeholder="In Hours, eg. 5"/>
		</td>
	</tr>
</script>

<script type="text/html" id="tmpl-orddd-advance-setting-rows-empty">
	<tr>
		<th colspan="10" style="text-align:center"><?php esc_html_e( 'No Weekday Settings found. Click on Insert row button to add settings for each weekday.', 'order-delivery-date' ); ?></th>
	</tr>
</script>

<?php submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true ); ?>

<?php
/**
 * Order Delivery Date Pro for WooCommerce
 *
 * Adds Locations settings in Admin
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Pro-for-WooCommerce/Admin/Locations
 * @since       8.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$locations = get_option( 'orddd_locations' );
if ( '' === $locations || '{}' === $locations || '[]' === $locations ) {
	$locations = array();
}
?>
<div class="orddd_locations_heading"> <?php esc_html_e( 'Use the below table to add different pickup locations of your store.', 'order-delivery-date' ); ?></div>
<br>
<div class="wrap">
	<table class="orddd-locations-table wc_input_table widefat">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Address 1', 'order-delivery-date' ); ?></th>
				<th><?php esc_html_e( 'Address 2', 'order-delivery-date' ); ?></th>
				<th><?php esc_html_e( 'City', 'order-delivery-date' ); ?></th>
				<th><?php esc_html_e( 'State', 'order-delivery-date' ); ?></th>
				<th><?php esc_html_e( 'Postcode', 'order-delivery-date' ); ?></th>
				<th><?php esc_html_e( 'Country', 'order-delivery-date' ); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th colspan="8"> 
					<a class="button plus orddd_locations_insert"><?php esc_html_e( 'Insert row', 'order-delivery-date' ); ?></a>
					<a class="button minue orddd_locations_remove"><?php esc_html_e( 'Remove selected row', 'order-delivery-date' ); ?></a>
				</th> 
			</tr>
		</tfoot>
		<tbody id="locations_settings">
			<tr>
				<th colspan="10" style="text-align: center;"><?php esc_html_e( 'Loading&hellip;', 'woocommerce' ); ?></th>
			</tr>
		</tbody>
	</table>
</div>

<script type="text/html" id="tmpl-orddd-locations-rows">
	<?php /* translators: %s: Row id */ ?>
	<tr class="orddd_locations_row" data-tip="<?php echo esc_attr( sprintf( __( 'Pickup Location Row: %s', 'order-delivery-date' ), '{{ data.row_id }}' ) ); ?>" data-id="{{ data.row_id }}">
		<td class="address_1">
			<input type="text" value="{{ data.address_1 }}" name="address_1[{{ data.row_id }}]" class="orddd_address_1" data-attribute="address_1" />
		</td>

		<td class="address_2">
			<input type="text" value="{{ data.address_2 }}" name="address_2[{{ data.row_id }}]" class="orddd_address_2" data-attribute="address_2" />
		</td>

		<td class="city" >
			<input type="text" value="{{ data.city }}" name="city[{{ data.row_id }}]" class="orddd_city" data-attribute="city" />
		</td>

		<td class="state">
			<input type="text" value="{{ data.state }}" name="state[{{ data.row_id }}]" class="orddd_state" data-attribute="state" />
		</td>

		<td class="postcode">
			<input type="text" value="{{ data.postcode }}" name="postcode[{{ data.row_id }}]" class="orddd_postcode" data-attribute="postcode" />
		</td>

		<td class="country">
			<input type="text" value="{{ data.country }}" name="country[{{ data.row_id }}]" class="orddd_country" data-attribute="country" />
		</td>   
	</tr>
</script>

<script type="text/html" id="tmpl-orddd-locations-rows-empty">
	<tr>
		<th colspan="10" style="text-align:center"><?php esc_html_e( 'No Locations found. Click on Insert row button to add locations.', 'order-delivery-date' ); ?></th>
	</tr>
</script>

<?php submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save_locations', true ); ?>

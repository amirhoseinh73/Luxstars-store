<?php
/**
 * Order Delivery Date Pro for WooCommerce
 *
 * Add Export/Import tab content in Admin
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Pro-for-WooCommerce/Admin/Export-Import
 * @since       9.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wrap">
	<form method="post" enctype="multipart/form-data">
		<table class="orddd_export_import_table widefat" cellspacing="0">
			<tbody class="import_export">
				<tr class="export_settings">
					<th>
						<strong class="name"><?php echo esc_html( 'Export Delivery Settings' ); ?></strong>
						<p class="description"><?php echo wp_kses_post( 'Click the button to export the delivery settings for this plugin.' ); ?></p>
						<p class="description"><b><?php echo wp_kses_post( 'Notes: <br>1. Key file will not be exported while exporting google calendar sync settings. It has to be uploaded manually.<br>2. Import ICS feeds will not be exported. You need to add them once other settings are imported.<br>3. Settings for individual Product Categories will not be exported. Delivery needs to be enabled manually for the categories.', 'order-delivery-date' ); ?></b></p>
					</th>
					<td class="run-setting">
						<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=order_delivery_date&action=orddd_import_export_settings&action1=export_delivery_settings' ), 'export_action' ) ); ?>" class="button button-large export_settings"><?php echo esc_html( 'Export Settings' ); ?></a>
					</td>
				</tr>

				<tr class="export_custom_settings">
					<th>
						<strong class="name"><?php echo esc_html( 'Export Custom Delivery Settings' ); ?></strong>
						<p class="description"><?php echo wp_kses_post( 'Click the button to export the custom delivery settings for this plugin.' ); ?></p>
						<p class="description"><b><?php echo wp_kses_post( "Note: 'Settings based on' option will not be exported for the Custom Delivery Settings. <br>You need to map this option for individual setting after importing it.", 'order-delivery-date' ); ?></b></p>
					</th>
					<td class="run-setting">
						<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=order_delivery_date&action=orddd_import_export_settings&action1=export_custom_delivery_settings' ), 'export_custom_action' ) ); ?>" class="button button-large export_settings"><?php echo esc_html( 'Export Settings' ); ?></a>
					</td>
				</tr>

				<tr class="import_settings">
					<th>
						<strong class="name"><?php echo esc_html( 'Import Delivery Settings' ); ?></strong>
						<p class="description"><?php echo wp_kses_post( 'Upload a file to import delivery settings for this plugin.' ); ?></p>
						<p class="choose-file"><input type="file" name="orddd-import-file" class="orddd-import-file" /></p>
					</th>
					<td class="run-setting">
						<input type="submit" class="button button-large export_settings" value="<?php echo esc_html_e( 'Import Settings', 'order-delivery-date' ); ?>">
						<p class="success-message"></p>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>

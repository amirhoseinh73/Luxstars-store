<?php
/**
 * Order Delivery Date Pro for WooCommerce
 *
 * Display the events in the Delivery Calendar in admin.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Pro-for-WooCommerce/Delivery-Calendar
 * @since       2.8.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once 'orddd-common.php';

/**
 * Display the events in the Delivery Calendar.
 *
 * @class orddd_class_view_deliveries
 */
class orddd_class_view_deliveries {

	/**
	 * Default constructor
	 *
	 * @since 8.2
	 */
	public function __construct() {
		add_action( 'admin_init', array( &$this, 'orddd_data_export' ) );
	}

	/**
	 * Delivery Calendar page
	 *
	 * @since 2.8.7
	 */
	public static function orddd_view_calendar_orders_page() {
		?>
	   <div class="wrap">
			<h2><?php _e( 'Delivery Calendar', 'order-delivery-date' ); ?></h2>
			<table class="orddd_delivery_table">
				<tr>
					<td>
						<label class="orddd_label" for="orddd_filter_delivery_calendar"><?php esc_html_e('Select a view:', 'order-delivery-date'); ?></label>
						<select class="orddd_filter_delivery_calendar" id="orddd_filter_delivery_calendar">
							<optgroup label="<?php _e( 'Filter Deliveries by', 'order-delivery-date' ); ?>">
								<option name="product" value="product"><?php _e( 'Products', 'order-delivery-date' ); ?></option>
								<option name="order" value="order"><?php _e( 'Orders', 'order-delivery-date' ); ?></option>
							</optgroup>
						</select>
						<input type="hidden" id="prev_event_type" value="product"/>
					</td>
					<td>
						<?php
						$order_status         = wc_get_order_statuses();
						$default_order_status = '';
						?>
						<label class="orddd_label" for="orddd_filter_by_order_status"><?php esc_html_e('Select status:', 'order-delivery-date'); ?></label>
						<select class="orddd_filter_by_order_status" id="orddd_filter_by_order_status" multiple="multiple">
							<?php
							foreach ( $order_status as $order_status_key => $order_status_name ) {
								if ( $order_status_key == 'wc-pending' || $order_status_key == 'wc-processing' || $order_status_key == 'wc-on-hold' || $order_status_key == 'wc-completed' ) {
									?>
									<option name="<?php echo $order_status_name; ?>" value="<?php echo $order_status_key; ?>" selected><?php echo $order_status_name; ?></option>
									<?php
									$default_order_status .= $order_status_key . ',';
								} elseif ( $order_status_key != 'wc-cancelled' && $order_status_key != 'wc-refunded' && $order_status_key != 'wc-failed' ) {
									?>
									<option name="<?php echo $order_status_name; ?>" value="<?php echo $order_status_key; ?>" selected><?php echo $order_status_name; ?></option>
									<?php
									$default_order_status .= $order_status_key . ',';
								}
							}
							?>
						</select>
						<?php $default_order_status = substr( $default_order_status, 0, strlen( $default_order_status ) - 1 ); ?>
						<input type="hidden" id="prev_order_status" value="<?php echo $default_order_status; ?>"/>
					</td>
					<?php if ( class_exists( 'WC_Shipping_Zones' ) ) { ?>
					<td>
						<?php
						$shipping_methods_to_display = Orddd_Shipping_Based_Settings::orddd_get_shipping_methods();
						if ( ! empty( $shipping_methods_to_display ) ) {
							?>
							<label class="orddd_label" for="orddd_filter_by_order_shipping"><?php esc_html_e('Select shipping method:', 'order-delivery-date'); ?></label>
							<select class="orddd_filter_by_order_shipping" id="orddd_filter_by_order_shipping" multiple="multiple">
							<?php
							foreach ( $shipping_methods_to_display as $shipping_default_value ) {
								if ( isset( $shipping_default_value['title'] ) ) {
									?>
									<option value="<?php echo $shipping_default_value['method_key']; ?>"><?php echo $shipping_default_value['title']; ?></option>
									<?php
								}
							}
							?>
							</select>
							<?php
						}
						?>
						<input type="hidden" id="prev_order_shipping" value=""/>
					</td>
					<?php } ?>
					<td style="width: 100px;">
						<label class="orddd_label">&nbsp;</label>
						<a href="javascript:;" id="orddd_filter_calendar_data" class="button button-primary orddd_filter_btn"><?php _e( 'Apply filters', 'order-delivery-date' ); ?></a>
					</td>
					<td>
						<?php do_action( 'orddd_before_export_actions' ); ?>

						<a href="<?php echo esc_url( add_query_arg( 'download', 'orddd_data.print' ) ); ?>" target="_blank" style="float:right;margin:5px;" class="button-secondary orddd-tooltip" id="orddd_print_orders"><?php _e( 'Print', 'order-delivery-date' ); ?><span class="orddd-tooltiptext"><?php _e( 'Print the data by using the Order status filter and Month, Week and Day option of the calendar to filter the print data.', 'order-delivery-date' ); ?></span></a>

						<a href="<?php echo esc_url( add_query_arg( 'download', 'orddd_data.csv' ) ); ?>" style="float:right;margin:5px;" class="button-secondary orddd-tooltip" id="orddd_csv_orders"><?php _e( 'CSV', 'order-delivery-date' ); ?><span class="orddd-tooltiptext"><?php _e( 'Export Deliveries in CSV format. You can use the Order status filter and Month, Week and Day option of the calendar to filter the export data.', 'order-delivery-date' ); ?></span></a>


					</td>
				</tr>
			</table>
			<div id="orddd_events_loader">
				<div class="orddd_events_loader_wrapper">
					Loading delivery events...<img src=<?php echo plugins_url() . '/order-delivery-date/images/ajax-loader.gif'; ?>>
				</div>
			</div>
			<div id='calendar' style="padding:10px"></div> 
			</br>
			<div><?php _e( '* Orders that are imported from the ICS feed URL will not be displayed on the calendar under "Products" view until the products are added for those orders. Such orders will however continue to show on the calendar in the "Orders" view.', 'order-delivery-date' ); ?></div>
			</br>
			<?php
			if ( 'directly' === get_option( 'orddd_calendar_sync_integration_mode' ) &&
				 'on' === get_option( 'orddd_admin_add_to_calendar_delivery_calendar' ) ) {
				$total_orders_to_export = orddd_common::orddd_get_total_orders_to_export();
				$event_orders           = get_option( 'orddd_event_order_ids' );
				?>
				<hr>
				<p>* To export all orders to your Google calendar, please click the "Export to Google Calendar" button. 
				<?php
				if ( '' != $event_orders ) {
					echo 'If you have already exported the orders once & wish to re-export all orders again, please click on the "Re-export to Google Calendar" button.';
				}
				?>
				</p>
				<div style="display:flex; margin-top: 20px;">
					<div id="orddd_add_to_calendar">
						<input type="button" class="button-primary" id="orddd_admin_add_to_calendar_delivery" value="<?php _e( 'Export to Google Calendar', 'order-delivery-date' ); ?>">
					</div>

					<?php if ( $event_orders != '' ) : ?>
						<div id="orddd_re_export_to_calendar" style="margin-left: 10px;">
							<input type="button" class="button-secondary" id="orddd_admin_export_to_calendar" value="<?php _e( 'Re-export to Google Calendar', 'order-delivery-date' ); ?>">

						</div>
					<?php endif; ?>
					<?php $orders_to_export = is_array( $total_orders_to_export ) ? count( $total_orders_to_export ) : 0; ?>

					<input type="hidden" name="orders_to_export" id="orders_to_export" value="<?php echo $orders_to_export; ?>">
				</div>
				<div id="orddd_update_event_message"></div>
			<?php } ?>
		</div>
		<?php
	}


	/**
	 * Called during AJAX request for qtip content for a calendar item
	 *
	 * @hook wp_ajax_nopriv_orddd_order_calendar_content , wp_ajax_orddd_order_calendar_content
	 * @since 2.8.7
	 */
	public static function orddd_order_calendar_content() {
		global $orddd_date_formats, $wpdb;
		$content = $delivery_date_timestamp = '';
		if ( ! empty( $_REQUEST['order_id'] ) && ! empty( $_REQUEST['event_value'] ) ) {
			$order              = new WC_Order( $_REQUEST['order_id'] );
			$order_items        = $order->get_items();
			$billing_first_name = ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', '>=' ) ) ? $order->get_billing_first_name() : $order->billing_first_name;
			$billing_last_name  = ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', '>=' ) ) ? $order->get_billing_last_name() : $order->billing_last_name;
			$order_id           = ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', '>=' ) ) ? $order->get_id() : $order->id;

			if ( $_REQUEST['event_type'] == 'product' ) {
				$value[]                 = $_REQUEST['event_value'];
				$product_id              = $_REQUEST['event_product_id'];
				$product_name            = $_REQUEST['product_name'];
				$product_quantity        = $_REQUEST['event_product_qty'];
				$delivery_date_formatted = orddd_common::orddd_get_order_delivery_date( $order_id );
				$order_page_time_slot    = orddd_common::orddd_get_order_timeslot( $order_id );
				$content                 = '<table>
                    <tr><td> <strong>Order:</strong></td><td><a href="post.php?post=' . $order_id . '&action=edit">#' . $order->get_order_number() . ' </a></td></tr>
	       		    <tr><td> <strong>Product Name:</strong></td><td> ' . $product_name . ' x' . $product_quantity . '</td></tr>
			        <tr><td> <strong>Customer Name:</strong></td><td> ' . $billing_first_name . ' ' . $billing_last_name . '</td></tr>';
				if ( isset( $delivery_date_formatted ) && $delivery_date_formatted != '0000-00-00' && isset( $_REQUEST['event_date'] ) && $_REQUEST['event_date'] == '' ) {

					$content .= '<tr> <td> <strong>Delivery Date:</strong></td><td> ' . $delivery_date_formatted . '</td></tr>';
				} elseif ( isset( $_REQUEST['event_date'] ) && $_REQUEST['event_date'] != '' ) {
					$content .= '<tr> <td> <strong>Delivery Date:</strong></td><td> ' . $_REQUEST['event_date'] . '</td></tr>';
				}
				if ( isset( $order_page_time_slot ) && $order_page_time_slot != '' && isset( $_REQUEST['event_date'] ) && $_REQUEST['event_date'] == '' ) {
					$content .= '<tr> <td> <strong>Time Slot:</strong></td><td> ' . $order_page_time_slot . '</td></tr>';
				} elseif ( isset( $_REQUEST['event_timeslot'] ) && $_REQUEST['event_timeslot'] != '' ) {
					$content .= '<tr> <td> <strong>Time Slot:</strong></td><td> ' . $_REQUEST['event_timeslot'] . '</td></tr>';
				}

				$custom_fields = '';
				if ( has_filter( 'orddd_add_custom_field_value_to_qtip' ) ) {
					$custom_fields = apply_filters( 'orddd_add_custom_field_value_to_qtip', $order_id );
				}

				if ( $custom_fields != '' ) {
					$content .= $custom_fields;
				}

				$content .= '</table>';
				if ( $product_id ) {
					$post_image = get_the_post_thumbnail( $product_id, array( 100, 100 ) );
					if ( ! empty( $post_image ) ) {
						$content = '<div class="orddd_product_image">' . $post_image . '</div>' . $content;
					}
				}
			} elseif ( $_REQUEST['event_type'] == 'order' ) {
				$delivery_date_formatted = orddd_common::orddd_get_order_delivery_date( $order_id );
				$order_page_time_slot    = orddd_common::orddd_get_order_timeslot( $order_id );

				$value[] = $_REQUEST['event_value'];
				$content = '<table>
                    <tr> <td> <strong>Order:</strong></td><td><a href="post.php?post=' . $order_id . '&action=edit">#' . $order->get_order_number() . ' </a> </td> </tr>
                    <tr> <td> <strong>Customer Name:</strong></td><td> ' . $billing_first_name . ' ' . $billing_last_name . '</td> </tr>';

				if ( isset( $delivery_date_formatted ) && $delivery_date_formatted != '0000-00-00' && isset( $_REQUEST['event_date'] ) && $_REQUEST['event_date'] == '' ) {
					$content .= '<tr> <td> <strong>Delivery Date:</strong></td><td> ' . $delivery_date_formatted . '</td></tr>';
				} elseif ( isset( $_REQUEST['event_date'] ) && $_REQUEST['event_date'] != '' ) {
					$content .= '<tr> <td> <strong>Delivery Date:</strong></td><td> ' . $_REQUEST['event_date'] . '</td></tr>';
				}

				if ( isset( $order_page_time_slot ) && $order_page_time_slot != '' && isset( $_REQUEST['event_date'] ) && $_REQUEST['event_date'] == '' ) {
					$content .= '<tr> <td> <strong>Time Slot:</strong></td><td> ' . $order_page_time_slot . '</td></tr>';
				} elseif ( isset( $_REQUEST['event_timeslot'] ) && $_REQUEST['event_timeslot'] != '' ) {
					$content .= '<tr> <td> <strong>Time Slot:</strong></td><td> ' . $_REQUEST['event_timeslot'] . '</td></tr>';
				}

				$custom_fields = '';
				if ( has_filter( 'orddd_add_custom_field_value_to_qtip' ) ) {
					$custom_fields = apply_filters( 'orddd_add_custom_field_value_to_qtip', $order_id );
				}

				if ( $custom_fields != '' ) {
					$content .= $custom_fields;
				}

				$product_name = '';
				if ( isset( $_REQUEST['event_product_id'] ) && $_REQUEST['event_product_id'] != '' ) {
					$product_name = get_the_title( $_REQUEST['event_product_id'] );
				} else {
					$variable_product_name = '';
					foreach ( $order_items as $item ) {
						$data = $item->get_data();
						if ( isset( $data['variation_id'] ) && $data['variation_id'] != 0 ) {
							$_product              = new WC_Product_Variation( $data['variation_id'] );
							$variable_product_name = '<a href="' . get_edit_post_link( $data['product_id'] ) . '" target="_blank">' . $_product->get_title();
							$variation_data        = $_product->get_variation_attributes(); // variation data in array.
							$meta_data 		= $data['meta_data'];

							if ( is_array( $variation_data ) && count( $variation_data ) > 0 ) {
								$variable_product_data = '';

								foreach( $meta_data as $key => $value ) {
									$mdata =  $value->get_data();
									$taxonomy = $mdata['key'];
									$attribute_name = wc_attribute_label( $taxonomy, $_product );
									if ( taxonomy_exists( $taxonomy ) ) {
										$attribute_value = get_term_by( 'slug', $mdata['value'], $taxonomy )->name;
									} else {
										$attribute_value = $mdata['value']; // For custom product attributes.
									}
									$variable_product_data .=  $attribute_name . ': ' . $attribute_value . ', ';
								}
								$variable_product_name = rtrim( $variable_product_name . ' - ' . $variable_product_data, ', ' ) . ' x' . $item['quantity'] . '</a><br><br>';
								$product_name 		  .= $variable_product_name;
							}
						} else {
							$product_name .= '<a href="' . get_edit_post_link( $data['product_id'] ) . '" target="_blank">' . $item['name'] . ' x' . $item['quantity'] . '</a><br><br>';
						}
						$product_name = apply_filters( 'orddd_modify_calendar_product_info', $product_name, $item );
					}
				}
				$content .= '<tr> <td> <strong>Item Details:</strong></td><td> ' . $product_name . '</td> </tr>';
				$content .= '</table>';
			}
		}
		echo $content;
		die();
	}

	/**
	 * This function will download CSV or Print Deliveries based on the CSV on Print button is clicked
	 *
	 * @since 2.0
	 * @global $wpdb Global wpdb object
	 */

	public function orddd_data_export() {
		global $wpdb;

		if ( isset( $_GET['download'] ) && ( $_GET['download'] == 'orddd_data.csv' ) && isset( $_GET['page'] ) && $_GET['page'] = 'orddd_view_orders' ) {
			$report = self::orddd_generate_data();
			$csv    = self::orddd_generate_csv( $report );

			header( 'Content-type: application/x-msdownload' );
			header( 'Content-Disposition: attachment; filename=orddd_data.csv' );
			header( 'Pragma: no-cache' );
			header( 'Expires: 0' );
			echo "\xEF\xBB\xBF";
			echo $csv;
			exit;
		} elseif ( isset( $_GET['download'] ) && ( $_GET['download'] == 'orddd_data.print' ) && isset( $_GET['page'] ) && $_GET['page'] = 'orddd_view_orders' ) {
			$report = self::orddd_generate_data();

			$print_data_columns  = "
                                    <tr>
                                        <th style='border:1px solid black;padding:5px;'>" . __( 'Order ID', 'order-delivery-date' ) . "</th>
                                        <th style='border:1px solid black;padding:5px;'>" . __( 'Products', 'order-delivery-date' ) . "</th>
                                        <th style='border:1px solid black;padding:5px;'>" . __( 'Billing Address', 'order-delivery-date' ) . "</th>
                                        <th style='border:1px solid black;padding:5px;'>" . __( 'Shipping Address', 'order-delivery-date' ) . "</th>
                                        <th style='border:1px solid black;padding:5px;'>" . __( 'Shipping Method', 'order-delivery-date' ) . "</th>
										<th style='border:1px solid black;padding:5px;'>" . __( get_option( 'orddd_location_field_label' ), 'order-delivery-date' ) . "</th>
                                        <th style='border:1px solid black;padding:5px;'>" . __( 'Delivery Date', 'order-delivery-date' ) . "</th>
                                        <th style='border:1px solid black;padding:5px;'>" . __( 'Delivery Time', 'order-delivery-date' ) . "</th>
                                        <th style='border:1px solid black;padding:5px;'>" . __( 'Order Date', 'order-delivery-date' ) . '</th>
                                    </tr>';
			$print_data_row_data = '';

			foreach ( $report as $key => $value ) {
				// Currency Symbol.
				// The order currency is fetched to ensure the correct currency is displayed if the site uses multi-currencies.
				$the_order       = wc_get_order( $value->order_id );
				$currency        = ( version_compare( WOOCOMMERCE_VERSION, '3.0.0' ) < 0 ) ? $the_order->get_order_currency() : $the_order->get_currency();
				$currency_symbol = get_woocommerce_currency_symbol( $currency );

				$print_data_row_data .= "<tr>
                                        <td style='border:1px solid black;padding:5px;'>" . $value->order_id . "</td>
                                        <td style='border:1px solid black;padding:5px;'>";

				foreach ( $value->product_name as $id => $data ) {
					$print_data_row_data .= $data['product'] . ' x ' . $data['quantity'] . '<br>';
				}

				$print_data_row_data .= "</td>
                                        <td style='border:1px solid black;padding:5px;'>" . $value->billing_address . "</td>
                                        <td style='border:1px solid black;padding:5px;'>" . $value->shipping_address . "</td>
                                        <td style='border:1px solid black;padding:5px;'>" . $value->shipping_method . "</td>
										<td style='border:1px solid black;padding:5px;'>" . $value->pickup_location . "</td>
                                        <td style='border:1px solid black;padding:5px;'>" . $value->delivery_date . "</td>
                                        <td style='border:1px solid black;padding:5px;'>" . $value->delivery_time . "</td>
                                        <td style='border:1px solid black;padding:5px;'>" . $value->order_date . '</td>
                                        </tr>';
			}
			$print_data_columns  = apply_filters( 'orddd_print_columns', $print_data_columns );
			$print_data_row_data = apply_filters( 'orddd_print_rows', $print_data_row_data, $report );
			$print_data          = "<table style='border:1px solid black;border-collapse:collapse;'>" . $print_data_columns . $print_data_row_data . '</table>';
			$print_data          = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body><table style='border:1px solid black;border-collapse:collapse;'>" . $print_data_columns . $print_data_row_data . '</table></body></html>';
			echo $print_data;
			exit;
		}

		do_action( 'orddd_print_summary_data' );
	}

	/**
	 * This function will generate the data require for CSV and Print of bookings
	 *
	 * @since 2.0
	 * @param string $tab_status selected filter E.g status for Booking from today onwards is 'future'
	 * @global object $wpdb Global wpdb object
	 * @return array $report All booking details required to show on old View Bookings page.
	 */
	function orddd_generate_data() {
		global $wpdb;
		if ( isset( $_GET['orderType'] ) && ( $_GET['orderType'] != '' ) ) {
			$order_status1 = $_GET['orderType'];
			$order_status  = explode( ',', $order_status1 );
		} else {
			$all_order_status = wc_get_order_statuses();
			$order_status     = array();
			foreach ( $all_order_status as $order_status_key => $order_status_name ) {
				if ( $order_status_key == 'wc-pending' || $order_status_key == 'wc-processing' || $order_status_key == 'wc-on-hold' || $order_status_key == 'wc-completed' ) {
					$order_status[] = $order_status_key;
				} elseif ( $order_status_key != 'wc-cancelled' && $order_status_key != 'wc-refunded' && $order_status_key != 'wc-failed' ) {
					$order_status[] = $order_status_key;
				}
			}
		}
		$order_shipping = array();
		if ( isset( $_GET['orderShipping'] ) && ( $_GET['orderShipping'] != '' ) ) {
			$order_shipping1 = $_GET['orderShipping'];
			$order_shipping  = explode( ',', $order_shipping1 );
		}
		$event_start = $event_start_timestamp = $event_end = $event_end_timestamp = '';
		if ( isset( $_GET['start'] ) ) {
			$event_start           = $_GET['start'];
			$event_start_timestamp = strtotime( $_GET['start'] );
		}

		if ( isset( $_GET['end'] ) ) {
			$event_end           = $_GET['end'];
			$event_end_timestamp = strtotime( $_GET['end'] );
		}

		// Start and end date is same then check for same date with end time ie 10th Nov 2018 23:59:59.
		if ( $event_start_timestamp != '' && $event_start_timestamp == $event_end_timestamp ) {
			$event_end_timestamp += 86399;
		}

		$date_str                  = orddd_common::str_to_date_format();
		$delivery_date_field_label = esc_sql( get_option( 'orddd_delivery_date_field_label' ) );

		$orddd_query = "SELECT ID, post_status FROM `" . $wpdb->prefix . "posts` WHERE post_type = 'shop_order' AND post_status IN ( " . "'" . implode( "','", $order_status ) . "'" . ') AND ID IN ( SELECT post_id FROM `' . $wpdb->prefix . "postmeta` WHERE ( meta_key = '_orddd_timestamp' AND meta_value >= '" . $event_start_timestamp . "' AND meta_value <= '" . $event_end_timestamp . "' ) OR ( meta_key = '" . $delivery_date_field_label . "' AND STR_TO_DATE( meta_value, '" . $date_str . "' ) >= '" . $event_start . "' AND STR_TO_DATE( meta_value, '" . $date_str . "' ) <= '" . $event_end . "' ) OR ( meta_key = '" . ORDDD_DELIVERY_DATE_FIELD_LABEL . "' AND STR_TO_DATE( meta_value, '" . $date_str . "' ) >= '" . $event_start . "' AND STR_TO_DATE( meta_value, '" . $date_str . "' ) <= '" . $event_end . "' ) ) ORDER BY ID";
		$results     = $wpdb->get_results( $orddd_query );

		$report = array();
		$i      = 0;

		// It will check if the woocommerce product addons plugin is active or not.
		$is_addon_plugin_active = is_plugin_active( 'woocommerce-product-addons/woocommerce-product-addons.php' );

		foreach ( $results as $rkey => $rval ) {
			$order       = new WC_Order( $rval->ID );
			$order_items = $order->get_items();
			if ( $order->get_items( 'shipping' ) ) {
				foreach( $order->get_items( 'shipping' ) as $item_id => $item ) {
					$shipping_method_id          = $item->get_method_id();
					$shipping_method_instance_id = $item->get_instance_id();
					$shipping_method_to_check    = $shipping_method_id . ':' . $shipping_method_instance_id;
				}
				if ( ! empty( $order_shipping ) && ! in_array( $shipping_method_to_check, $order_shipping ) ) {
					continue;
				}
			}
			if ( 'order' == $_GET['eventType'] ) {
				$i = $rval->ID;
			}
			$products = array();
			foreach ( $order_items as $item ) {
				if ( 'product' == $_GET['eventType'] ) {
					$products = array();
				}

				$report[ $i ] = new stdClass();

				// Order ID.
				$report[ $i ]->order_id = $rval->ID;

				// Product Name.
				$product_name = html_entity_decode( $item['name'], ENT_COMPAT, 'UTF-8' );
				$data         = $item->get_data();

				if ( $is_addon_plugin_active || ( isset( $data['variation_id'] ) && $data['variation_id'] != 0 ) ) {
					if ( isset( $data['variation_id'] ) && $data['variation_id'] ) {
						$_product       = new WC_Product_Variation( $data['variation_id'] );
						$product_name   = $_product->get_title();
						$variation_data = $_product->get_variation_attributes(); // variation data in array
					}
					$meta_data = $data['meta_data'];

					if ( $is_addon_plugin_active || ( is_array( $variation_data ) && count( $variation_data ) > 0 ) ) {
						$product_name .= ' - ';
						if ( isset( $_product ) && ! empty( $_product ) ) {
							foreach( $meta_data as $key => $value ) {
								$mdata =  $value->get_data();
								$taxonomy = $mdata['key'];
								$attribute_name = wc_attribute_label( $taxonomy, $_product );

								if( substr( $attribute_name, 0, 1 ) === '_' ) {
									continue;
								}
								if ( taxonomy_exists( $taxonomy ) ) {
									$attribute_value = get_term_by( 'slug', $mdata['value'], $taxonomy )->name;
								} else {
									$attribute_value = $mdata['value']; // For custom product attributes.
								}
								
								$product_name .=  $attribute_name . ': ' . $attribute_value . ', ';
							}
						}
						$product_name = rtrim( $product_name, ', ' );
					}
				}

				array_push(
					$products,
					array(
						'product'  => $product_name,
						'quantity' => $item['quantity'],
					)
				);
				$report[ $i ]->product_name = $products;

				// Quantity
				$report[ $i ]->quantity = $item['quantity'];

				// Billing Address
				$billing                       = $order->get_formatted_billing_address();
				$billing                       = str_replace( '\n', ',', $billing );
				$billing                       = str_replace( PHP_EOL, ',', $billing );
				$billing                       = str_replace( '<br/>', ',', $billing );
				$report[ $i ]->billing_address = $billing;

				// Shipping Address
				$shipping                       = $order->get_formatted_shipping_address();
				$shipping                       = str_replace( '\n', ',', $shipping );
				$shipping                       = str_replace( PHP_EOL, ',', $shipping );
				$shipping                       = str_replace( '<br/>', ',', $shipping );
				$report[ $i ]->shipping_address = $shipping;

				// Shipping Method
				$report[ $i ]->shipping_method = $order->get_shipping_method();

				// Pickup Location
				$report[ $i ]->pickup_location = orddd_common::orddd_get_order_formatted_location( $rval->ID );

				// Delivery Date
				$report[ $i ]->delivery_date = orddd_common::orddd_get_order_delivery_date( $rval->ID );

				// Delivery Time
				$report[ $i ]->delivery_time = orddd_common::orddd_get_order_timeslot( $rval->ID );

				// Order Date
				$order_date = '';
				if ( version_compare( WOOCOMMERCE_VERSION, '3.0.0' ) < 0 ) {
					$order_date = $order->completed_date;
				} else {
					$order_post = get_post( $rval->ID );
					$post_date  = strtotime( $order_post->post_date );
					$order_date = date( 'Y-m-d H:i:s', $post_date );
				}

				$report[ $i ]->order_date = $order_date;
				if ( 'product' == $_GET['eventType'] ) {
					$i++;
				}
			}
		}
		return apply_filters( 'orddd_export_data', $report );
	}

	/**
	 * This function will create the string to be required for CSV download
	 *
	 * @since 8.2
	 * @param array $report Array of all delivery details
	 * @return string $csv Returns the strings which is created based on the delivery details
	 */

	function orddd_generate_csv( $report ) {
		// Column Names
		$csv  = 'Order ID,Products,Billing Address,Shipping Address,Shipping Method,' . __( get_option( 'orddd_location_field_label' ), 'order-delivery-date' ) . ',Delivery Date,Delivery Time,Order Date';
		$csv .= "\n";
		foreach ( $report as $key => $value ) {

			// Order ID
			$order_id         = $value->order_id;
			$product_name     = $value->product_name;
			$quantity         = $value->quantity;
			$billing_address  = $value->billing_address;
			$shipping_address = $value->shipping_address;
			$shipping_method  = $value->shipping_method;
			$pickup_location  = $value->pickup_location;
			$delivery_date    = $value->delivery_date;
			$delivery_time    = $value->delivery_time;
			$order_date       = $value->order_date;
			$product_name     = str_replace( '"', '""', $product_name );

			if ( 'product' == $_GET['eventType'] ) {
				$break = '';
			} else {
				$break = "\n";
			}
			// Create the data row
			$csv .= $order_id . ',"';
			foreach ( $product_name as $id => $data ) {
				$name = str_replace( '<br>', "\n", $data['product'] );
				$csv .= strip_tags( $name ) . ' x ' . $data['quantity'] . $break;
			}

			// Create the data row
			$csv .= '","' . $billing_address . '","' . $shipping_address . '","' . $shipping_method . '","' . $pickup_location . '","' . $delivery_date . '","' . $delivery_time . '","' . $order_date . '"';
			$csv .= "\n";
		}
		$csv = apply_filters( 'orddd_csv_data', $csv, $report );
		return $csv;
	}
}

$orddd_class_view_deliveries = new orddd_class_view_deliveries();

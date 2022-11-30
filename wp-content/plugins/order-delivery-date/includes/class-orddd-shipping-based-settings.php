<?php
/**
 * Display Custom Delivery Settings in admin.
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Pro-for-WooCommerce/Admin/Settings/Custom-Delivery
 * @since 2.8.3
 * @category Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Orddd_Shipping_Based_Settings class
 *
 * @class Orddd_Shipping_Based_Settings
 */
class Orddd_Shipping_Based_Settings {

	/**
	 * Callback for adding Custom Delivery settings tab settings
	 */
	public static function orddd_shipping_based_delivery_settings_callback() {}

	/**
	 * Callback for adding Enable custom delivery settings checkbox setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_enable_shipping_based_delivery_callback( $args ) {
		$enable_shipping_based_delivery = '';
		if ( 'on' === get_option( 'orddd_enable_shipping_based_delivery' ) ) {
			$enable_shipping_based_delivery = 'checked';
		}
		?>
		<input type="checkbox" name="orddd_enable_shipping_based_delivery" id="orddd_enable_shipping_based_delivery" class="day-checkbox" <?php echo esc_attr( $enable_shipping_based_delivery ); ?>/>
		<label for="orddd_enable_shipping_based_delivery"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Save changes button
	 *
	 * @since 2.8.3
	 */
	public static function orddd_enable_shipping_based_delivery_save_callback() {
		submit_button( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save', true );
	}

	/**
	 * Callback for adding Add settings link on the Custom Delivery page
	 *
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_admin_settings_callback() {
		echo '<a href=\'admin.php?page=order_delivery_date&action=shipping_based\' class=\'view_shipping_setting\'>' . esc_html__( 'View Settings', 'order-delivery-date' ) . '</a>';
	}

	/**
	 * Callback for adding Shipping based on setting
	 *
	 * @since 2.8.3
	 */
	public static function orddd_custom_delivery_type_callback() {
		global $woocommerce, $wpdb;
		$shipping_methods_stored   = array();
		$product_categories_stored = array();
		$shipping_method_str       = 'checked';
		$row_id                    = '';
		$option_key                = '';
		$product_categories_str    = '';

		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['shipping_methods'] ) ) {
					$shipping_methods_stored = $shipping_methods_arr['shipping_methods'];
				}
				if ( isset( $shipping_methods_arr['product_categories'] ) ) {
					$product_categories_stored = $shipping_methods_arr['product_categories'];
				}

				if ( isset( $shipping_methods_arr['delivery_settings_based_on'][0] ) && 'shipping_methods' === $shipping_methods_arr['delivery_settings_based_on'][0] ) {
					$shipping_method_str    = 'checked';
					$product_categories_str = '';
				} elseif ( isset( $shipping_methods_arr['delivery_settings_based_on'][0] ) && 'product_categories' === $shipping_methods_arr['delivery_settings_based_on'][0] ) {
					$shipping_method_str    = '';
					$product_categories_str = 'checked';
				}
			}
		}

		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );

		$shipping_methods_to_display = self::orddd_get_shipping_methods();

		// Fetch Product Categories.
		$args = array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => 0,
		);

		$all_categories = get_categories( $args );

		?>
		<p>
			<label>
				<input type="radio" name="orddd_shipping_based_settings_<?php echo esc_attr( $option_key ); ?>[delivery_settings_based_on][]" value="shipping_methods" id="orddd_delivery_settings_type" class="input-radio" <?php echo esc_attr( $shipping_method_str ); ?>/><?php esc_html_e( 'Shipping methods', 'order-delivery-date' ); ?>
			</label>
		</p>
		<div class="delivery_type_options delivery_type_shipping_methods">
			<select class="orddd_shipping_methods" id="orddd_shipping_methods" name="orddd_shipping_based_settings_<?php echo esc_attr( $option_key ); ?>[shipping_methods][]" multiple="multiple" placeholder="<?php esc_html_e( 'Choose Shipping methods', 'order-delivery-date' ); ?>">
				<?php

				foreach ( $shipping_methods_to_display as $sk => $sv ) {
					if ( in_array( $sv['method_key'], $shipping_methods_stored ) ) {
						// phpcs:ignore
						echo '<option value="' . esc_attr( $sv['method_key'] ) . '" selected>' . esc_html__( $sv['title'], 'order-delivery-date' ) . '</option>';
					} else {
						// phpcs:ignore
						echo '<option value="' . esc_attr( $sv['method_key'] ) . '">' . esc_html__( $sv['title'], 'order-delivery-date' ) . '</option>';
					}
				}

				?>
			</select>
		</div>      
		<p>
			<label>
				<input type="radio" name="orddd_shipping_based_settings_<?php echo esc_attr( $option_key ); ?>[delivery_settings_based_on][]" value="product_categories" id="orddd_delivery_settings_type" class="input-radio" <?php echo esc_attr( $product_categories_str ); ?>/><?php esc_html_e( 'Product categories', 'order-delivery-date' ); ?>
			</label>
		</p>
		<div class="delivery_type_options delivery_type_product_categories">          
			<select class="orddd_shipping_methods" id="orddd_shipping_methods" name="orddd_shipping_based_settings_<?php echo esc_attr( $option_key ); ?>[product_categories][]" multiple="multiple" placeholder="<?php esc_html_e( 'Choose Product categories', 'order-delivery-date' ); ?>">
				<?php
				foreach ( $all_categories as $key => $value ) {
					$delivery_date = get_term_meta( $value->term_id, 'orddd_delivery_date_for_product_category', true );
					if ( 'on' === $delivery_date ) {
						if ( in_array( $value->slug, $product_categories_stored, true ) ) {
							// phpcs:ignore
							echo '<option value="' . esc_attr( $value->slug ) . '" selected>' . esc_html__( $value->name, 'order-delivery-date' ) . '</option>';
						} else {
							// phpcs:ignore
							echo '<option value="' . esc_attr( $value->slug ) . '">' . esc_html__( $value->name, 'order-delivery-date' ) . '</option>';
						}
					}
				}
				?>
			</select>
			<br>
			<small><?php esc_html_e( 'Delivery settings created for the above product categories will be applied to the translated categories in other languages as well.', 'order-delivery-date' ); ?></small>
		</div>
		<input type="hidden" name="is_shipping_based_page" id="is_shipping_based_page" value="yes"/>
		<?php
		do_action( 'orddd_after_custom_product_categories', $option_key );
	}

	/**
	 * Callback for adding custom date settings
	 */
	public static function orddd_shipping_based_date_settings_callback() {
		if ( isset( $_GET['row_id'] ) ) {
			$row_id               = $_GET['row_id'];
			$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );

			if ( ! isset( $shipping_methods_arr['enable_shipping_based_delivery'] ) && get_option( 'orddd_shipping_settings_status_' . $row_id ) ) {
				delete_option( 'orddd_shipping_settings_status_' . $row_id );
			}
		}
	}

	/**
	 * Callback for adding custom Enable delivery date checkbox setting
	 *
	 * @since 2.8.3
	 */
	public static function orddd_shipping_methods_for_product_categories_callback() {
		$row_id                                 = '';
		$edit                                   = '';
		$shipping_methods_for_categories_stored = array();
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['shipping_methods_for_categories'] ) ) {
					$shipping_methods_for_categories_stored = $shipping_methods_arr['shipping_methods_for_categories'];
				}
			}
		}

		$option_key                  = orddd_common::get_shipping_setting_option_key( $row_id );
		$shipping_methods_to_display = self::orddd_get_shipping_methods();
		?>
		<select class="orddd_shipping_methods_for_categories" id="orddd_shipping_methods_for_categories" name="orddd_shipping_based_settings_<?php echo esc_attr( $option_key ); ?>[shipping_methods_for_categories][]" multiple="multiple" placeholder="<?php esc_html_e( 'Choose Shipping methods', 'order-delivery-date' ); ?>">
			<?php
			foreach ( $shipping_methods_to_display as $sk => $sv ) {
				if ( in_array( $sv['method_key'], $shipping_methods_for_categories_stored ) ) {
					// phpcs:ignore
					echo '<option value="' . esc_attr( $sv['method_key'] ) . '" selected>' . esc_attr__( $sv['title'], 'order-delivery-date' ) . '</option>';
				} else {
					// phpcs:ignore
					echo '<option value="' . esc_attr( $sv['method_key'] ) . '">' . esc_attr__( $sv['title'], 'order-delivery-date' ) . '</option>';
				}
			}
			?>
		</select>
		<?php
	}

	/**
	 * Callback for adding custom Enable delivery date checkbox setting
	 *
	 * @since 2.8.3
	 */
	public static function orddd_categories_for_pickup_locations_callback() { 
		$row_id                                 = '';
		$edit                                   = '';
		$product_categories_stored = array();

		// Fetch Product Categories.
		$args = array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => 0,
		);
	
		$all_categories = get_categories( $args );

		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['categories_for_pickup_locations'] ) ) {
					$product_categories_stored = $shipping_methods_arr['categories_for_pickup_locations'];
				}
			}
		}

		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );
		?>
		<select class="orddd_categories_for_pickup_locations" id="orddd_categories_for_pickup_locations" name="orddd_shipping_based_settings_<?php echo esc_attr( $option_key ); ?>[categories_for_pickup_locations][]" multiple="multiple" placeholder="<?php esc_html_e( 'Choose Product Categories:', 'order-delivery-date' ); ?>">
			<?php
			foreach ( $all_categories as $key => $value ) {
				$delivery_date = get_term_meta( $value->term_id, 'orddd_delivery_date_for_product_category', true );
				if ( 'on' === $delivery_date ) {
					if ( in_array( $value->slug, $product_categories_stored, true ) ) {
						// phpcs:ignore
						echo '<option value="' . esc_attr( $value->slug ) . '" selected>' . esc_html__( $value->name, 'order-delivery-date' ) . '</option>';
					} else {
						// phpcs:ignore
						echo '<option value="' . esc_attr( $value->slug ) . '">' . esc_html__( $value->name, 'order-delivery-date' ) . '</option>';
					}
				}
			}
			?>
		</select>
		<?php
	}

	/**
	 * Callback for adding settings to selected weekdays
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_enable_shipping_based_delivery_date_callback( $args ) {
		$enable_shipping_based_delivery_settings = 'checked';
		$weekday_delivery                        = 'checked';
		$row_id                                  = '';
		$edit                                    = '';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$edit                 = 'yes';
				$weekday_delivery     = '';
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['enable_shipping_based_delivery'] ) && 'on' === $shipping_methods_arr['enable_shipping_based_delivery'] ) {
					$enable_shipping_based_delivery_settings = 'checked';
				} else {
					$enable_shipping_based_delivery_settings = '';
				}
				if ( isset( $shipping_methods_arr['delivery_type'] ) ) {
					$delivery_type = $shipping_methods_arr['delivery_type'];
					if ( isset( $delivery_type['weekdays'] ) && 'on' === $delivery_type['weekdays'] ) {
						$weekday_delivery = 'checked';
					}
				}
			}
		}
		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );
		?>
		<input type="hidden" name="weekday_delivery" id="weekday_delivery" value="<?php echo esc_attr( $weekday_delivery ); ?>" >
		<input type="hidden" name="is_edit" id="is_edit" value="<?php echo esc_attr( $edit ); ?>" >
		<input type="checkbox" name="orddd_shipping_based_settings_<?php echo esc_attr( $option_key ); ?>[enable_shipping_based_delivery]" id="orddd_enable_shipping_based_delivery_date" class="day-checkbox" value="on" <?php echo esc_attr( $enable_shipping_based_delivery_settings ); ?> />
		<label for="orddd_enable_delivery_date"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding custom delivery checkout options
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_delivery_checkout_options_callback( $args ) {
		$orddd_delivery_checkout_options_delivery_calendar = 'checked';
		$orddd_delivery_checkout_options_text_block        = '';
		$row_id = '';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['orddd_delivery_checkout_options'] ) && 'text_block' === $shipping_methods_arr['orddd_delivery_checkout_options'] ) {
					$orddd_delivery_checkout_options_text_block        = 'checked';
					$orddd_delivery_checkout_options_delivery_calendar = '';
				} elseif ( isset( $shipping_methods_arr['orddd_delivery_checkout_options'] ) && 'delivery_calendar' === $shipping_methods_arr['orddd_delivery_checkout_options'] ) {
					$orddd_delivery_checkout_options_delivery_calendar = 'checked';
					$orddd_delivery_checkout_options_text_block        = '';
				}
			}
		}
		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );
		echo '<p><label><input type="radio" name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[orddd_delivery_checkout_options]" id="orddd_delivery_checkout_options" value="delivery_calendar"' . esc_attr( $orddd_delivery_checkout_options_delivery_calendar ) . '/>' . esc_html__( 'Calendar', 'order-delivery-date' ) . '</label>
        <label><input type="radio" name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[orddd_delivery_checkout_options]" id="orddd_delivery_checkout_options" value="text_block"' . esc_attr( $orddd_delivery_checkout_options_text_block ) . '/>' . esc_html__( 'Text block', 'order-delivery-date' ) . '</label></p>';
		?>
		<label for="orddd_enable_shipping_based_delivery_date"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/** Callback for adding between days range for textblock
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_text_block_between_days_callback( $args ) {
		$orddd_min_between_days = 1;
		$orddd_max_between_days = 3;

		$row_id = '';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['orddd_min_between_days'] ) && '' !== $shipping_methods_arr['orddd_min_between_days'] ) {
					$orddd_min_between_days = $shipping_methods_arr['orddd_min_between_days'];
				}
				if ( isset( $shipping_methods_arr['orddd_max_between_days'] ) && '' !== $shipping_methods_arr['orddd_max_between_days'] ) {
					$orddd_max_between_days = $shipping_methods_arr['orddd_max_between_days'];
				}
			}
		}

		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );

		echo '<label for="orddd_text_block_between_days">Between 
            <input id="orddd_min_between_days" name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[orddd_min_between_days]" type="number" value="' . esc_attr( $orddd_min_between_days ) . '" style="width:50px;" min="1" step="1"> 
            and 
            <input id="orddd_max_between_days" name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[orddd_max_between_days]" type="number" value="' . esc_attr( $orddd_max_between_days ) . '" style="width:50px;" min="1" step="1"> 
            days.
        </label>';
		?>
		<label for="orddd_text_block_between_days"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}
	/**
	 * Callback for adding custom delivery type setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_delivery_type_callback( $args ) {
		$weekday_delivery       = 'checked';
		$specific_date_delivery = '';
		$row_id                 = '';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['delivery_type'] ) ) {
					$delivery_type    = $shipping_methods_arr['delivery_type'];
					$weekday_delivery = '';
					if ( isset( $delivery_type['weekdays'] ) && 'on' === $delivery_type['weekdays'] ) {
						$weekday_delivery = 'checked';
					}

					if ( isset( $delivery_type['specific_dates'] ) && 'on' === $delivery_type['specific_dates'] ) {
						$specific_date_delivery = 'checked';
					}
				}
			}
		}
		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );

		echo '<input type="checkbox" name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[delivery_type][weekdays]" id="orddd_shipping_delivery_type_weekdays" ' . esc_attr( $weekday_delivery ) . '/><div class="orddd_shipping_weekdays">' . esc_html__( 'Weekdays', 'order-delivery-date' ) . '</div>';
		echo '<input type="checkbox" name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[delivery_type][specific_dates]" id="orddd_shipping_delivery_type_specific_days" ' . esc_attr( $specific_date_delivery ) . '/><div class="orddd_shipping_specific_dates">' . esc_html__( 'Specific Delivery Dates', 'order-delivery-date' ) . '</div>';

		echo '<label for="orddd_shipping_delivery_type">' . wp_kses_post( $args[0] ) . '</label>';
	}

	/**
	 * Callback for adding custom Weekdays setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_weekdays_callback( $args ) {
		global $orddd_weekdays;
		$enable_weekdays        = array();
		$enabled_weekday        = 'checked';
		$additional_charges     = '';
		$row_id                 = '';
		$delivery_charges_label = '';
		$currency_symbol        = get_woocommerce_currency_symbol();
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['weekdays'] ) ) {
					$weekdays_settings = $shipping_methods_arr['weekdays'];
					foreach ( $orddd_weekdays as $wk => $wv ) {
						$weekday = $weekdays_settings[ $wk ];
						if ( isset( $weekday['enable'] ) && 'checked' === $weekday['enable'] ) {
							$enable_weekdays[]                  = $wk;
							$weekdays_additional_charges[ $wk ] = $weekday['additional_charges'];
							if ( isset( $weekday['delivery_charges_label'] ) ) {
								$weekdays_delivery_charges_label[ $wk ] = $weekday['delivery_charges_label'];
							} else {
								$weekdays_delivery_charges_label[ $wk ] = '';
							}
						}
					}
				}
			}
		}
		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );

		echo '<fieldset class="days-fieldset" id="weekdays_fieldset" disabled="disabled">
        	<legend><b>' . esc_html__( 'Weekdays & Additional Charges:', 'order-delivery-date' ) . '</b></legend>
                <table id="orddd_weekdays_table">';
				echo '<tr>
                        <th class="orddd_custom_fieldset_padding"></th>
                        <th class="orddd_custom_fieldset_padding">' . esc_html__( 'Enable', 'order-delivery-date' ) . '</th>
                        <th class="orddd_custom_fieldset_padding"></th>
                        <th class="orddd_custom_fieldset_padding">' . esc_html__( 'Charges', 'order-delivery-date' ) . '</th>
                        <th class="orddd_custom_fieldset_label">' . esc_html__( 'Checkout page Label', 'order-delivery-date' ) . '</th>
                </tr>';
		foreach ( $orddd_weekdays as $n => $day_name ) {
			if ( in_array( $n, $enable_weekdays, true ) ) {
				$enabled_weekday        = 'checked';
				$additional_charges     = $weekdays_additional_charges[ $n ];
				$delivery_charges_label = $weekdays_delivery_charges_label[ $n ];

			} elseif ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
				$enabled_weekday        = '';
				$additional_charges     = '';
				$delivery_charges_label = '';
			}
			echo '<tr>
						<td class="orddd_custom_fieldset_padding"><input type="checkbox" name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[weekdays][' . esc_attr( $n ) . '][enable]" id="' . esc_attr( $n ) . '_custom_setting" value="checked" ' . esc_attr( $enabled_weekday ) . '/></td>';
			// phpcs:ignore						
			echo '<td class="orddd_custom_fieldset_padding"><label class="ord_label" for="' . esc_attr( $day_name ) . '">' . esc_html__( $day_name, 'order-delivery-date' ) . '</label></td>
                        <td class="orddd_custom_fieldset_padding">' . esc_attr( $currency_symbol ) . '</td>
                	    <td class="orddd_custom_fieldset_padding"><input type="text" name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[weekdays][' . esc_attr( $n ) . '][additional_charges] id="additional_charges_' . esc_attr( $n ) . '"  class="orddd_custom_additional_charges" value="' . esc_attr( $additional_charges ) . '"/></td>
                	    <td class="orddd_custom_fieldset_padding"><input type="text" name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[weekdays][' . esc_attr( $n ) . '][delivery_charges_label] id="delivery_charges_label_' . esc_attr( $n ) . '"  class="orddd_custom_charges_label" value="' . esc_attr( $delivery_charges_label ) . '"/></td>    
                    </tr>';
		}
				echo '</table>
            </fieldset>';
		?>
		<label for="orddd_delivery_days"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding custom delivery dates setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_specific_days_callback( $args ) {
		$currency_symbol = get_woocommerce_currency_symbol();
		$day_selected    = get_option( 'start_of_week' );



		echo '<input type="text" name="orddd_delivery_date" id="orddd_delivery_date" class="day-checkbox" disabled="disabled" placeholder="Select Date"/>
		    ' . esc_attr( $currency_symbol ) . '<input class="orddd_custom_specific_charges" type="text" name="additional_charges" id="additional_charges" disabled="disabled" placeholder="Charges"/>
            <input class="orddd_specific_charges_label" type="text" name="specific_charges_label" id="specific_charges_label" disabled="disabled" placeholder="Delivery Charges Label"/>&nbsp;
            <input class="orddd_max_orders_specific" type="number" min="0" step="1" name="orddd_max_orders_specific" id="orddd_max_orders_specific" placeholder="Maximum Orders" />&nbsp;
            <input type="button" value="' . esc_html__( 'Save', 'order-delivery-date' ) . '" id="save_specific_date" class="save_button" disabled="disabled">';

		echo '<label for="orddd_shipping_based_specific_day">' . wp_kses_post( $args[0] ) . '</label>';

		$specific_date_hidden_str = '';
		$row_id                   = '';
		echo '<table id="orddd_specific_date_list">';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['specific_dates'] ) ) {
					$specific_days_settings = explode( ',', $shipping_methods_arr['specific_dates'] );
					echo '<tr class="orddd_common_list_tr">
                            <th class="orddd_specific_date_list">' . esc_html__( 'Delivery Dates', 'order-delivery-date' ) . '</th>
                            <th class="orddd_specific_date_list">' . esc_html__( 'Additional Charges', 'order-delivery-date' ) . '</th>
                            <th class="orddd_specific_date_list">' . esc_html__( 'Checkout page Label', 'order-delivery-date' ) . '</th>
                            <th class="orddd_specific_date_list">' . esc_html__( 'Maximum Orders', 'order-delivery-date' ) . '</th>
                            <th class="orddd_specific_date_list">' . esc_html__( 'Actions', 'order-delivery-date' ) . '</th>
                        </tr>';
					$i = 0;
					foreach ( $specific_days_settings as $sk => $sv ) {
						$specific_date_value = str_replace( '}', '', $sv );
						$specific_date_value = str_replace( '{', '', $specific_date_value );
						$specific_date_arr   = explode( ':', $specific_date_value );
						if ( isset( $specific_date_arr[0] ) && '' !== $specific_date_arr[0] ) {
							$date_arr = explode( '-', trim( $specific_date_arr[0] ) );
							$date_str = gmdate( 'm-d-Y', gmmktime( 0, 0, 0, $date_arr[0], $date_arr[1], $date_arr[2] ) );
							echo '<tr class="orddd_common_list_tr" id="orddd_delete_specific_dates_' . esc_attr( $i ) . '">
                                <td class="orddd_specific_date_list" id="orddd_specific_date">' . esc_attr( $date_str ) . '</td>';
							if ( '' !== $specific_date_arr[1] ) {
								echo '<td class="orddd_specific_date_list" id="orddd_additional_charges">' . esc_attr( $currency_symbol ) . '' . esc_attr( $specific_date_arr[1] ) . '</td>';
							} else {
								echo '<td class="orddd_specific_date_list" id="orddd_additional_charges"></td>';
							}
								echo '<td class="orddd_specific_date_list" id="orddd_specific_charges_label">' . esc_attr( $specific_date_arr[2] ) . '</td>
                                <td class="orddd_specific_date_list" id="orddd_max_orders_specific">' . esc_attr( $specific_date_arr[3] ) . '</td>
                                <td class="orddd_specific_date_list"><a href="javascript:void(0)" class="confirmation_specific_date" id="orddd_delete_specific_dates_' . esc_attr( $i ) . '">' . esc_html__( 'Delete', 'order-delivery-date' ) . '</a></td>
                            </tr>';
							$specific_date_hidden_str .= $sv . ',';
						}
						$i++;
					}
				}
			}
		}
		echo '</table>';
		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );
		echo '<input type="hidden" id="start_of_week" name="start_of_week" value='. $day_selected .'>';
		echo '<input type="hidden" id="orddd_specific_date_hidden"  name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[specific_dates]" value="' . esc_attr( $specific_date_hidden_str ) . '"/>';
	}

	/**
	 * Callback for adding custom Minimum delivery time(in hours) setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_minimum_delivery_time_callback( $args ) {
		$minimum_delivery_time = '';
		$row_id                = '';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['minimum_delivery_time'] ) ) {
					$minimum_delivery_time = $shipping_methods_arr['minimum_delivery_time'];
				}
			}
		}

		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );
		?>
		<input type="text" name="orddd_shipping_based_settings_<?php echo esc_attr( $option_key ); ?>[minimum_delivery_time]" id="orddd_shipping_based_minimumOrderDays" value="<?php echo esc_attr( $minimum_delivery_time ); ?>"/>
		<label for="orddd_shipping_based_minimumOrderDays"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding custom number of dates to choose setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_number_of_dates_callback( $args ) {
		$number_of_dates = 30;
		$row_id          = '';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['number_of_dates'] ) ) {
					$number_of_dates = $shipping_methods_arr['number_of_dates'];
				}
			}
		}

		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );

		echo '<input type="number" min="0" step="1" name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[number_of_dates]" id="orddd_shipping_based_number_of_dates" value="' . esc_attr( $number_of_dates ) . '"/>';
		echo '<label for="orddd_shipping_based_number_of_dates"> ' . wp_kses_post( $args[0] ) . '</label>';
	}

	/**
	 * Callback for adding custom delivery date field mandatory setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_date_field_mandatory_callback( $args ) {
		$mandatory_field = '';
		$row_id          = '';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['date_mandatory_field'] ) ) {
					$mandatory_field = $shipping_methods_arr['date_mandatory_field'];
				}
			}
		}

		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );
		echo '<input type="checkbox" name="orddd_shipping_based_settings_' . $option_key . '[date_mandatory_field]" id="orddd_shipping_based_date_field_mandatory" value="checked"' . esc_attr( $mandatory_field ) . '/>';

		echo '<label for="orddd_shipping_based_date_field_mandatory">' . wp_kses_post( $args[0] ) . '</label>';
	}

	/**
	 * Callback for adding custom Maximum Order Deliveries per day (based on per order) setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_date_lockout_callback( $args ) {
		$lockout = '';
		$row_id  = '';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['date_lockout'] ) ) {
					$lockout = $shipping_methods_arr['date_lockout'];
				}
			}
		}

		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );
		echo '<input type="number" min="0" step="1" name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[date_lockout]" id="orddd_shipping_based_date_lockout" value="' . esc_attr( $lockout ) . '"/>';

		echo '<label for="orddd_shipping_based_date_lockout"> ' . wp_kses_post( $args[0] ) . '</label>';
	}

	/**
	 * Callback for adding max order total required to remove delivery charges.
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 9.25.0
	 */
	public static function orddd_shipping_based_max_order_total_callback( $args ) {
		$max_order_total = '';
		$row_id  = '';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['max_order_total'] ) ) {
					$max_order_total = $shipping_methods_arr['max_order_total'];
				}
			}
		}

		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );
		echo '<input type="number" min="0" step="0.1" name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[max_order_total]" id="orddd_shipping_based_max_order_total_for_charges" value="' . esc_attr( $max_order_total ) . '"/>';

		echo '<label for="orddd_shipping_based_max_order_total_for_charges"> ' . wp_kses_post( $args[0] ) . '</label>';
	}


	/**
	 * Callback for adding custom Time settings
	 */
	public static function orddd_shipping_based_time_settings_callback() { }

	/**
	 * Callback for adding custom Time range setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_time_sliders_callback( $args ) {
		$from_hours = '';
		$to_hours   = '';
		$from_mins  = '';
		$to_mins    = 0;
		$row_id     = '';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['time_settings'] ) ) {
					$time_settings = $shipping_methods_arr['time_settings'];
					$from_hours    = $time_settings['from_hours'];
					$from_mins     = isset( $time_settings['from_mins'] ) ? $time_settings['from_mins'] : '0';
					$to_hours      = $time_settings['to_hours'];
					$to_mins       = isset( $time_settings['to_mins'] ) ? $time_settings['to_mins'] : '0';
				}
			}
		}

		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );

		$step_min = apply_filters( 'orddd_time_slider_minute_step', 5 );

		echo '<select name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[time_settings][from_hours]" id="orddd_delivery_from_hours" size="1">';
		for ( $i = 0; $i <= 23; $i++ ) {
			printf(
				"<option %s value='%s' >%s</option>\n",
				selected( $i, $from_hours, false ),
				esc_attr( $i ),
				esc_attr( $i )
			);
		}
		echo '</select>&nbsp;:&nbsp';
		echo '<select name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[time_settings][from_mins]" id="orddd_delivery_from_mins" size="1">';
		// time options.
		for ( $i = 0; $i <= 59; ) {
			printf(
				"<option %s value='%s'>%s</option>\n",
				selected( $i, $from_mins, false ),
				esc_attr( $i ),
				esc_attr( $i )
			);
			$i += $step_min;
		}
		echo '</select>&nbsp;-&nbsp';
		echo '<select name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[time_settings][to_hours]" id="orddd_delivery_to_hours" size="1">';
		for ( $i = 0; $i <= 23; $i++ ) {
			printf(
				"<option %s value='%s'>%s</option>\n",
				selected( $i, $to_hours, false ),
				esc_attr( $i ),
				esc_attr( $i )
			);
		}

		echo '</select>&nbsp;:&nbsp;';
		echo '<select name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[time_settings][to_mins]" id="orddd_delivery_to_mins" size="1">';
		for ( $i = 0; $i <= 59; ) {
			printf(
				"<option %s value='%s'>%s</option>\n",
				selected( $i, $to_mins, false ),
				esc_attr( $i ),
				esc_attr( $i )
			);
			$i += $step_min;
		}
		echo '</select>';
		echo '<label for="orddd_shipping_based_time_settings"> ' . wp_kses_post( $args[0] ) . '</label>';
	}

	/**
	 * Callback for adding custom Same day cut-off setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_sameday_cutoff_callback( $args ) {
		$after_hours   = 0;
		$after_minutes = 0;
		$row_id        = '';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['same_day'] ) ) {
					$same_day      = $shipping_methods_arr['same_day'];
					$after_hours   = $same_day['after_hours'];
					$after_minutes = $same_day['after_minutes'];
				}
			}
		}
		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );
		echo '<fieldset>';
		echo '<label for="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[same_day][after_hours]">' . esc_html__( 'Hours:', 'order-delivery-date' ) . '</label><select name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[same_day][after_hours]" id="orddd_shipping_based_sameday_after_hours" size="1">';
		for ( $i = 0; $i <= 23; $i++ ) {
			printf(
				'<option %svalue="%s">%s</option>',
				selected( $i, $after_hours, false ),
				esc_attr( $i ),
				esc_attr( $i )
			);
		}

		echo '</select>&nbsp;&nbsp;<label for="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[same_day][after_minutes]">' . esc_html__( 'Mins:', 'order-delivery-date' ) . '</label><select name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[same_day][after_minutes]" id="orddd_shipping_based_sameday_after_minutes" size="1">';
		for ( $i = 0; $i <= 59; $i++ ) {
			if ( $i < 10 ) {
				$i = '0' . $i;
			}
			printf(
				'<option %svalue="%s">%s</option>',
				selected( $i, $after_minutes, false ),
				esc_attr( $i ),
				esc_attr( $i )
			);
		}
		echo '</select>';
		echo '</fieldset>';
		echo '<label for="orddd_shipping_based_sameday_cutoff"> ' . wp_kses_post( $args[0] ) . '</label>';
	}

	/**
	 * Callback for adding custom next day cut-off setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_nextday_cutoff_callback( $args ) {
		$after_hours   = 0;
		$after_minutes = 0;
		$row_id        = '';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['next_day'] ) ) {
					$next_day      = $shipping_methods_arr['next_day'];
					$after_hours   = $next_day['after_hours'];
					$after_minutes = $next_day['after_minutes'];
				}
			}
		}
		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );
		echo '<fieldset>';

		echo '<label for="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[next_day][after_hours]">' . esc_html__( 'Hours:', 'order-delivery-date' ) . '</label><select name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[next_day][after_hours]" id="orddd_shipping_based_nextday_after_hours" size="1">';
		for ( $i = 0; $i <= 23; $i++ ) {
			printf(
				'<option %svalue="%s">%s</option>',
				selected( $i, $after_hours, false ),
				esc_attr( $i ),
				esc_attr( $i )
			);
		}

		echo '</select>&nbsp;&nbsp;<label for="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[next_day][after_minutes]">' . esc_html__( 'Mins:', 'order-delivery-date' ) . '</label><select name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[next_day][after_minutes]" id="orddd_shipping_based_nextday_after_minutes" size="1">';
		for ( $i = 0; $i <= 59; $i++ ) {
			if ( $i < 10 ) {
				$i = '0' . $i;
			}
			printf(
				'<option %svalue="%s">%s</option>',
				selected( $i, $after_minutes, false ),
				esc_attr( $i ),
				esc_attr( $i )
			);
		}
		echo '</select>';
		echo '</fieldset>';

		echo '<label for="orddd_shipping_based_nextday_cutoff"> ' . wp_kses_post( $args[0] ) . '</label>';
	}

	/**
	 * Callback for adding custom same day additional charges setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_same_day_additional_charges_callback( $args ) {
		$additional_charges = $row_id = '';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['same_day'] ) ) {
					$same_day           = $shipping_methods_arr['same_day'];
					$additional_charges = $same_day['additional_charges'];
				}
			}
		}
		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );
		echo '<input type="text" name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[same_day][additional_charges]" id="orddd_shipping_based_same_day_additional_charges" value="' . esc_attr( $additional_charges ) . '"/>';
		echo '<label for="orddd_shipping_based_same_day_additional_charges"> ' . wp_kses_post( $args[0] ) . '</label>';
	}

	/**
	 * Callback for adding custom next day additional charges setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_next_day_additional_charges_callback( $args ) {
		$additional_charges = '';
		$row_id             = '';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['next_day'] ) ) {
					$next_day           = $shipping_methods_arr['next_day'];
					$additional_charges = $next_day['additional_charges'];
				}
			}
		}
		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );
		echo '<input type="text" name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[next_day][additional_charges]" id="orddd_shipping_based_next_day_additional_charges" value="' . esc_attr( $additional_charges ) . '"/>';

		echo '<label for="orddd_shipping_based_next_day_additional_charges"> ' . wp_kses_post( $args[0] ) . '</label>';
	}

	/**
	 * Callback for adding custom Holidays setting
	 */
	public static function orddd_shipping_based_holidays_callback() { }

	/**
	 * Callback for enabling General Settings Holidays
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_enable_global_holidays_callback( $args ) {
		$enable_global_holidays = '';
		$row_id                 = '';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['enable_global_holidays'] ) ) {
					$enable_global_holidays = $shipping_methods_arr['enable_global_holidays'];
				}
			}
		}

		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );
		echo '<input type="checkbox" name="orddd_shipping_based_settings_' . esc_attr( $option_key ) . '[enable_global_holidays]" id="orddd_enable_global_holidays" value="checked" ' . esc_attr( $enable_global_holidays ) . '/>';
		echo '<label for="orddd_enable_global_holidays"> ' . wp_kses_post( $args[0] ) . '</label>';
	}

	/**
	 * Callback for adding custom holiday name setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_holiday_name_callback( $args ) {
		echo '<input type="text" name="orddd_shipping_based_holiday_name" id="orddd_shipping_based_holiday_name" class="day-checkbox"/>';
		echo '<label for="orddd_shipping_based_holiday_name"> ' . wp_kses_post( $args[0] ) . '</label>';
	}

	/**
	 * Callback for adding custom holiday from date and to date setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_holiday_from_date_callback( $args ) {
		$day_selected = get_option( 'start_of_week' );

		echo '<input type="text" name="orddd_shipping_based_holiday_from_date" id="orddd_shipping_based_holiday_from_date" class="day-checkbox" placeholder= "' . esc_html__( 'From', 'order-delivery-date' ) . '" readonly="readonly" autocomplete="off" style="background:white;"/>&nbsp;';
		echo '<input type="text" name="orddd_shipping_based_holiday_to_date" id="orddd_shipping_based_holiday_to_date" class="day-checkbox" placeholder= "' . esc_html__( 'To', 'order-delivery-date' ) . '" readonly="readonly" autocomplete="off" style="background:white;" />&nbsp;';
		echo '<input type="hidden" value="' . esc_attr( $day_selected ) . '" name="orddd_holiday_start_day">'; // add a hidden value to set the first day of calendar in js.

		echo '<label for="orddd_shipping_based_holiday_from_date"> ' . wp_kses_post( $args[0] ) . '</label>';
	}

	/**
	 * Callback for adding custom recurring holiday date
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 *
	 * @since 8.0
	 */
	public static function orddd_shipping_based_allow_recurring_holiday_callback( $args ) {
		echo '<input type="checkbox" name="orddd_shipping_based_allow_recurring_holiday" id="orddd_shipping_based_allow_recurring_holiday" class="day-checkbox" />';
		echo '<label for="orddd_shipping_based_allow_recurring_holiday"> ' . wp_kses_post( $args[0] ) . '</label>';
	}

	/**
	 * Callback for displaying the saved holidays in a table
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_holiday_save_callback( $args ) {
		echo '<input type="button" value="' . esc_html__( 'Save', 'order-delivery-date' ) . '" id="save_holidays" class="save_button">';

		$holiday_hidden_str = '';
		$row_id             = '';
		echo '<table id="orddd_holidays_list" class="order-delivery-holiday-list-settings">';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				$holiday_str          = '';
				if ( isset( $shipping_methods_arr['holidays'] ) && '' !== $shipping_methods_arr['holidays'] ) {
					$holiday_settings = explode( ',', $shipping_methods_arr['holidays'] );
					$holiday_str      = '';

					$holidays_view_table = '<tr class="orddd_common_list_tr">
                            <th class="orddd_holidays_list">' . __( 'Name', 'order-delivery-date' ) . '</th>
                            <th class="orddd_holidays_list">' . __( 'Date', 'order-delivery-date' ) . '</th>
                            <th class="orddd_holidays_list">' . __( 'Type', 'order-delivery-date' ) . '</th>
                            <th class="orddd_holidays_list">' . __( 'Actions', 'order-delivery-date' ) . '</th>
                        </tr>';

					$holidays_view_table = apply_filters( 'orddd_shipping_holidays_table', $holidays_view_table );

					echo $holidays_view_table;

					$i = 0;
					foreach ( $holiday_settings as $hk => $hv ) {
						$holiday_value = str_replace( '}', '', $hv );
						$holiday_value = str_replace( '{', '', $holiday_value );
						$holiday_arr   = explode( ':', $holiday_value );
						if ( isset( $holiday_arr[1] ) && '' !== $holiday_arr[1] ) {
							$date_arr = explode( '-', trim( $holiday_arr[1] ) );
							$date_str = gmdate( 'm-d-Y', gmmktime( 0, 0, 0, $date_arr[0], $date_arr[1], $date_arr[2] ) );

							if ( isset( $holiday_arr[2] ) && 'on' === $holiday_arr[2] ) {
								$recurring_type = __( 'Recurring', 'order-delivery-date' );
							} else {
								$recurring_type = __( 'Current Year', 'order-delivery-date' );
							}

							$holidays_table_data = '<tr class="orddd_common_list_tr" id="orddd_delete_holidays_' . $i . '">
                                <td class="orddd_holidays_list" id="orddd_holiday_name">' . trim( $holiday_arr[0] ) . '</td>
                                <td class="orddd_holidays_list" id="orddd_holiday_date">' . $date_str . '</td>
                                <td class="orddd_holidays_list" id="orddd_allow_recurring_type">' . $recurring_type . '</td>
                                <td class="orddd_holidays_list"><a href="javascript:void(0)" class="confirmation_holidays" id="orddd_delete_holidays_' . $i . '">' . __( 'Delete', 'order-delivery-date' ) . '</a></td>
                            </tr>';

							$holidays_table_data = apply_filters(
								'orddd_shipping_holidays_table_data',
								$holidays_table_data,
								$holiday_arr,
								$date_str,
								$recurring_type,
								$i
							);

							echo $holidays_table_data;

							$holiday_hidden_str .= $hv . ',';
						}
						$i++;
					}
				}
			}
		}
		echo '</table>';
		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );
		echo '<input type="hidden" id="orddd_holiday_hidden"  name="orddd_shipping_based_settings_' . $option_key . '[holidays]" value="' . $holiday_hidden_str . '"/>';
	}

	/**
	 * Callback for adding custom Time slot settings
	 *
	 * @since 2.8.3
	 */

	public static function orddd_shipping_based_timeslot_callback() { }

	public static function orddd_shipping_based_individual_timeslots_callback() { }

	public static function orddd_shipping_based_bulk_timeslots_callback() { }


	/**
	 * Callback for adding custom time slot from hour and to hour setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */

	public static function orddd_shipping_based_time_from_hours_callback( $args ) {
		?>
		<section class="custom-add-timeslot">
			<input type="text" name="orddd_shipping_based_time_from_hours[]" id="orddd_shipping_based_time_from_hours" value=""/>
			To
			<input type="text" name="orddd_shipping_based_time_to_hours[]" id="orddd_shipping_based_time_to_hours" value=""/>

			<a href="#" id="custom_add_another_slot" role="button">+ Add another slot</a>
		</section>
		<?php
	}

	/**
	 * Callback for adding custom Maximum Order Deliveries per time slot (based on per order) setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_time_slot_lockout_callback( $args ) {
		echo '<input type="number" min="0" step="1" name="orddd_shipping_based_time_slot_lockout" id="orddd_shipping_based_time_slot_lockout"/>';

		$html = '<label for="orddd_shipping_based_time_slot_lockout"> ' . $args[0] . '</label>';
		echo $html;
	}

	/**
	 * Callback for adding custom Additional Charges for time slot and Checkout label setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_time_slot_additional_charges_callback( $args ) {
		echo '<input type="text" name="orddd_shipping_based_time_slot_additional_charges" id="orddd_shipping_based_time_slot_additional_charges" placeholder="Charges"/>';
		echo '<input type="text" name="orddd_shipping_based_time_slot_additional_charges_label" id="orddd_shipping_based_time_slot_additional_charges_label" placeholder="Time slot Charges Label"/>';

		$html = '<label for="orddd_shipping_based_time_slot_additional_charges"> ' . $args[0] . '</label>';
		echo $html;
	}
	/**
	 * Callback for adding custom time slot field mandatory setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_timeslot_field_mandatory_callback( $args ) {
		$mandatory_field = '';
		$row_id          = '';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['timeslot_mandatory_field'] ) ) {
					$mandatory_field = $shipping_methods_arr['timeslot_mandatory_field'];
				}
			}
		}

		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );
		echo '<input type="checkbox" name="orddd_shipping_based_settings_' . $option_key . '[timeslot_mandatory_field]" id="orddd_shipping_based_timeslot_field_mandatory" value="checked" ' . $mandatory_field . '/>';

		$html = '<label for="orddd_shipping_based_timeslot_field_mandatory"> ' . $args[0] . '</label>';
		echo $html;
	}

	/**
	 * Callback for adding custom as soon as possible option for time slot dropdown on checkout page.
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 7.9
	 */
	public static function orddd_shipping_based_timeslot_field_asap_callback( $args ) {
		$mandatory_field = '';
		$row_id          = '';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['timeslot_asap_option'] ) ) {
					$mandatory_field = $shipping_methods_arr['timeslot_asap_option'];
				}
			}
		}

		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );
		echo '<input type="checkbox" name="orddd_shipping_based_settings_' . $option_key . '[timeslot_asap_option]" id="orddd_shipping_based_timeslot_field_mandatory" value="checked" ' . $mandatory_field . '/>';

		$html = '<label for="orddd_shipping_based_timeslot_field_asap"> ' . $args[0] . '</label>';
		echo $html;
	}

	/**
	 * Callback for adding the options to select weekdays or specific dates
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_time_slot_for_delivery_days_callback( $args ) {
		global $orddd_weekdays;

		$orddd_shipping_based_time_slot_for_weekdays       = 'checked';
		$orddd_shipping_based_time_slot_for_specific_dates = '';
		if ( 'weekdays' === get_option( 'orddd_shipping_based_time_slot_for_delivery_days' ) ) {
			$orddd_shipping_based_time_slot_for_weekdays       = 'checked';
			$orddd_shipping_based_time_slot_for_specific_dates = '';
		} elseif ( 'specific_dates' === get_option( 'orddd_shipping_based_time_slot_for_delivery_days' ) ) {
			$orddd_shipping_based_time_slot_for_specific_dates = 'checked';
			$orddd_shipping_based_time_slot_for_weekdays       = '';
		}

		?>
		<p><label><input type="radio" name="orddd_shipping_based_time_slot_for_delivery_days" id="orddd_shipping_based_time_slot_for_delivery_days" value="weekdays"<?php echo esc_attr( $orddd_shipping_based_time_slot_for_weekdays ); ?>/><?php esc_html_e( 'Weekdays', 'order-delivery-date' ); ?></label>
		<label><input type="radio" name="orddd_shipping_based_time_slot_for_delivery_days" id="orddd_shipping_based_time_slot_for_delivery_days" value="specific_dates"<?php echo esc_attr( $orddd_shipping_based_time_slot_for_specific_dates ); ?>/><?php esc_html_e( 'Specific Delivery Dates', 'order-delivery-date' ); ?></label></p>
		<label for="orddd_shipping_based_time_slot_for_delivery_days"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding time slots based on weekdays
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_time_slot_for_weekdays_callback( $args ) {
		global $orddd_weekdays;
		$specific_date_option = '';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$edit                 = 'yes';
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['specific_dates'] ) ) {
					$specific_days_settings = explode( ',', $shipping_methods_arr['specific_dates'] );
					foreach ( $specific_days_settings as $sk => $sv ) {
						$specific_date_value = str_replace( '}', '', $sv );
						$specific_date_value = str_replace( '{', '', $specific_date_value );
						$specific_date_arr   = explode( ':', $specific_date_value );
						if ( isset( $specific_date_arr[0] ) && '' !== $specific_date_arr[0] ) {
							$date_arr              = explode( '-', trim( $specific_date_arr[0] ) );
							$date_str              = gmdate( 'm-d-Y', gmmktime( 0, 0, 0, $date_arr[0], $date_arr[1], $date_arr[2] ) );
							$specific_date_option .= "<option value='" . $specific_date_arr[0] . "'>" . $date_str . '</option>';
						}
					}
				}
			}
		}

		echo '<div class="shipping_based_time_slot_options shipping_based_time_slot_for_weekdays">
            <select class="orddd_shipping_based_time_slot_for_weekdays" id="orddd_shipping_based_time_slot_for_weekdays" name="orddd_shipping_based_time_slot_for_weekdays[]" multiple="multiple" placeholder="Select Weekdays">
                <option name="all" value="all">All</option>
            </select>
        </div>';

		echo '<div class="shipping_based_time_slot_options shipping_based_time_slot_for_specific_dates">
            <select class="orddd_shipping_based_select_delivery_dates" id="orddd_shipping_based_select_delivery_dates" name="orddd_shipping_based_select_delivery_dates[]" multiple="multiple" placeholder="Select Specific Delivery Dates"> ' . $specific_date_option . ' </select>
        </div>';

		$html = '<label for="orddd_shipping_based_time_slot_for_weekdays"> ' . $args[0] . '</label>';
		echo $html;
	}

	/**
	 * Callback for adding the options to select weekdays or specific dates
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_time_slot_for_delivery_days_bulk_callback( $args ) {
		global $orddd_weekdays;

		$orddd_shipping_based_time_slot_for_weekdays       = 'checked';
		$orddd_shipping_based_time_slot_for_specific_dates = '';
		if ( 'weekdays' === get_option( 'orddd_shipping_based_time_slot_for_delivery_days' ) ) {
			$orddd_shipping_based_time_slot_for_weekdays       = 'checked';
			$orddd_shipping_based_time_slot_for_specific_dates = '';
		} elseif ( 'specific_dates' === get_option( 'orddd_shipping_based_time_slot_for_delivery_days' ) ) {
			$orddd_shipping_based_time_slot_for_specific_dates = 'checked';
			$orddd_shipping_based_time_slot_for_weekdays       = '';
		}

		?>
		<p><label><input type="radio" name="orddd_shipping_based_time_slot_for_delivery_days_bulk" id="orddd_shipping_based_time_slot_for_delivery_days_bulk" value="weekdays"<?php echo esc_attr( $orddd_shipping_based_time_slot_for_weekdays ); ?>/><?php esc_html_e( 'Weekdays', 'order-delivery-date' ); ?></label>
		<label><input type="radio" name="orddd_shipping_based_time_slot_for_delivery_days_bulk" id="orddd_shipping_based_time_slot_for_delivery_days_bulk" value="specific_dates"<?php echo esc_attr( $orddd_shipping_based_time_slot_for_specific_dates ); ?>/><?php esc_html_e( 'Specific Delivery Dates', 'order-delivery-date' ); ?></label></p>
		<label for="orddd_shipping_based_time_slot_for_delivery_days_bulk"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding time slots based on weekdays
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 9.20.0
	 */
	public static function orddd_shipping_based_time_slot_for_weekdays_bulk_callback( $args ) {
		global $orddd_weekdays;
		$specific_date_option = '';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$edit                 = 'yes';
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['specific_dates'] ) ) {
					$specific_days_settings = explode( ',', $shipping_methods_arr['specific_dates'] );
					foreach ( $specific_days_settings as $sk => $sv ) {
						$specific_date_value = str_replace( '}', '', $sv );
						$specific_date_value = str_replace( '{', '', $specific_date_value );
						$specific_date_arr   = explode( ':', $specific_date_value );
						if ( isset( $specific_date_arr[0] ) && '' !== $specific_date_arr[0] ) {
							$date_arr              = explode( '-', trim( $specific_date_arr[0] ) );
							$date_str              = gmdate( 'm-d-Y', gmmktime( 0, 0, 0, $date_arr[0], $date_arr[1], $date_arr[2] ) );
							$specific_date_option .= "<option value='" . $specific_date_arr[0] . "'>" . $date_str . '</option>';
						}
					}
				}
			}
		}

		echo '<div class="shipping_based_time_slot_options_bulk shipping_based_time_slot_for_bulk_weekdays">
            <select class="orddd_shipping_based_time_slot_for_weekdays" id="orddd_shipping_based_time_slot_for_weekdays_bulk" name="orddd_shipping_based_time_slot_for_weekdays_bulk[]" multiple="multiple" placeholder="Select Weekdays">
                <option name="all" value="all">All</option>
            </select>
        </div>';

		echo '<div class="shipping_based_time_slot_options_bulk shipping_based_time_slot_for_bulk_specific_dates">
            <select class="orddd_shipping_based_select_delivery_dates" id="orddd_shipping_based_select_delivery_dates_bulk" name="orddd_shipping_based_select_delivery_dates_bulk[]" multiple="multiple" placeholder="Select Specific Delivery Dates"> ' . $specific_date_option . ' </select>
        </div>';

		$html = '<label for="orddd_shipping_based_time_slot_for_weekdays_bulk"> ' . $args[0] . '</label>';
		echo $html;
	}

	/**
	 * Callback for adding time slot duration
	 *
	 * @param array $args Extra arguments.
	 * @return void
	 */
	public static function orddd_time_slot_duration_callback( $args ) {
		?>
		<input type="number" min="0" step="1" name="orddd_shipping_based_time_slot_duration" id="orddd_shipping_based_time_slot_duration" value=""/>
		<label for="orddd_shipping_based_time_slot_duration"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding time slot interval
	 *
	 * @param array $args Extra arguments.
	 * @return void
	 */
	public static function orddd_time_slot_interval_callback( $args ) {
		?>
		<input type="number" min="0" step="1" name="orddd_shipping_based_time_slot_interval" id="orddd_shipping_based_time_slot_interval" value=""/>
		<label for="orddd_shipping_based_time_slot_interval"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding start time.
	 *
	 * @param array $args Extra arguments.
	 * @return void
	 */
	public static function orddd_time_slot_starts_from_callback( $args ) {
		?>
		<input type="text" name="orddd_shipping_based_time_slot_starts_from" id="orddd_shipping_based_time_slot_starts_from" value=""/>
		<label for="orddd_shipping_based_time_slot_starts_from"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding end time of duration.
	 *
	 * @param array $args Extra arguments.
	 * @return void
	 */
	public static function orddd_time_slot_ends_at_callback( $args ) {
		?>
		<input type="text" name="orddd_shipping_based_time_slot_ends_at" id="orddd_shipping_based_time_slot_ends_at" value=""/>
		<label for="orddd_shipping_based_time_slot_ends_at"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding custom Maximum Order Deliveries per time slot (based on per order) setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 9.20.0
	 */
	public static function orddd_shipping_based_time_slot_lockout_bulk_callback( $args ) {
		echo '<input type="number" min="0" step="1" name="orddd_shipping_based_time_slot_lockout_bulk" id="orddd_shipping_based_time_slot_lockout_bulk"/>';

		$html = '<label for="orddd_shipping_based_time_slot_lockout_bulk"> ' . $args[0] . '</label>';
		echo $html;
	}

	/**
	 * Callback for adding custom Additional Charges for time slot and Checkout label setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 9.20.0
	 */
	public static function orddd_shipping_based_time_slot_additional_charges_bulk_callback( $args ) {
		echo '<input type="text" name="orddd_shipping_based_time_slot_additional_charges_bulk" id="orddd_shipping_based_time_slot_additional_charges_bulk" placeholder="Charges"/>';
		echo '<input type="text" name="orddd_shipping_based_time_slot_additional_charges_label_bulk" id="orddd_shipping_based_time_slot_additional_charges_label_bulk" placeholder="Time slot Charges Label"/>';

		$html = '<label for="orddd_shipping_based_time_slot_additional_charges_bulk"> ' . $args[0] . '</label>';
		echo $html;
	}

	/**
	 * Callback for displaying the saved time slots in a table
	 *
	 * @param array $args Extra arguments containing label & class for the field
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_time_slot_save_callback( $args ) {
		global $orddd_weekdays;
		$currency_symbol = get_woocommerce_currency_symbol();
		echo '<input type="button" style="position:absolute;" value="' . __( 'Save', 'order-delivery-date' ) . '" id="save_timeslots" class="save_button">';

		$time_slot_hidden_str = '';
		$row_id               = '';
		echo '<table id="orddd_time_slot_list" class="order-delivery-time-slot-list-settings" style="margin-top:50px">';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_methods_arr['time_slots'] ) && '' !== $shipping_methods_arr['time_slots'] ) {
					$time_slot_settings = explode( '},', $shipping_methods_arr['time_slots'] );
					$time_slot_str      = '';
					echo '<tr class="orddd_common_list_tr">
                        <th class="orddd_holidays_list">' . __( 'Delivery Days/Dates', 'order-delivery-date' ) . '</th>
                        <th class="orddd_holidays_list">' . __( 'Time Slot', 'order-delivery-date' ) . '</th>
                        <th class="orddd_holidays_list">' . __( 'Maximum Order Deliveries per time slot', 'order-delivery-date' ) . '</th>
                        <th class="orddd_holidays_list">' . __( 'Additional Charges for time slot', 'order-delivery-date' ) . '</th>
                        <th class="orddd_holidays_list">' . __( 'Checkout Label', 'order-delivery-date' ) . '</th>
                        <th class="orddd_holidays_list">' . __( 'Actions', 'order-delivery-date' ) . '</th>
                    </tr>';
					$i = 0;
					foreach ( $time_slot_settings as $hk => $hv ) {
						if ( '' !== $hv ) {
							$timeslot_values = orddd_common::get_timeslot_values( $hv );

							if ( 'weekdays' === $timeslot_values['delivery_days_selected'] ) {
								foreach ( $timeslot_values['selected_days'] as $key => $val ) {
									foreach ( $orddd_weekdays as $k => $v ) {
										if ( $k . '_custom_setting' === $val ) {
											echo '<tr class="orddd_common_list_tr" id="orddd_delete_time_slot_' . $i . '">
                                                <td class="orddd_holidays_list" id="orddd_delivery_day">' . $v . '</td>
                                                <td class="orddd_holidays_list" id="orddd_time_slot">' . $timeslot_values['time_slot'] . '</td>
                                                <td class="orddd_holidays_list" id="orddd_time_slot_lockout">' . $timeslot_values['lockout'] . '</td>';
											if ( '' !== $timeslot_values['additional_charges'] ) {
												echo '<td class="orddd_holidays_list" id="orddd_time_slot_lockout">' . $currency_symbol . '' . $timeslot_values['additional_charges'] . '</td>';
											} else {
												echo '<td class="orddd_holidays_list" id="orddd_time_slot_lockout"></td>';
											}
												echo '<td class="orddd_holidays_list" id="orddd_time_slot_lockout">' . $timeslot_values['additional_charges_label'] . '</td>
                                                <td class="orddd_holidays_list"><a href="javascript:void(0)" class="edit_time_slot" id="orddd_edit_time_slot_' . $i . '">' . __( 'Edit', 'order-delivery-date' ) . '</a> | <a href="javascript:void(0)" class="confirmation_time_slot" id="orddd_delete_time_slot_' . $i . '">' . __( 'Delete', 'order-delivery-date' ) . '</a></td>
                                            </tr>';
										}
									}
									if ( 'all' === $val ) {
										echo '<tr class="orddd_common_list_tr" id="orddd_delete_time_slot_' . $i . '">
                                                <td class="orddd_holidays_list" id="orddd_delivery_day">All</td>
                                                <td class="orddd_holidays_list" id="orddd_time_slot">' . $timeslot_values['time_slot'] . '</td>
                                                <td class="orddd_holidays_list" id="orddd_time_slot_lockout">' . $timeslot_values['lockout'] . '</td>';
										if ( '' !== $timeslot_values['additional_charges'] ) {
											echo '<td class="orddd_holidays_list" id="orddd_time_slot_lockout">' . $currency_symbol . '' . $timeslot_values['additional_charges'] . '</td>';
										} else {
											echo '<td class="orddd_holidays_list" id="orddd_time_slot_lockout"></td>';
										}
												echo '<td class="orddd_holidays_list" id="orddd_time_slot_lockout">' . $timeslot_values['additional_charges_label'] . '</td>
                                                <td class="orddd_holidays_list"><a href="javascript:void(0)" class="edit_time_slot" id="orddd_edit_time_slot_' . $i . '">' . __( 'Edit', 'order-delivery-date' ) . '</a> | <a href="javascript:void(0)" class="confirmation_time_slot" id="orddd_delete_time_slot_' . $i . '">' . __( 'Delete', 'order-delivery-date' ) . '</a></td>
                                            </tr>';
									}
									$i++;
								}
							} elseif ( 'specific_dates' === $timeslot_values['delivery_days_selected'] ) {
								foreach ( $timeslot_values['selected_days'] as $key => $val ) {
									$date_arr           = explode( '-', trim( $val ) );
									$date_str           = gmdate( 'm-d-Y', gmmktime( 0, 0, 0, $date_arr[0], $date_arr[1], $date_arr[2] ) );
									$additional_charges = $timeslot_values['additional_charges'];
									echo '<tr class="orddd_common_list_tr" id="orddd_delete_time_slot_' . $i . '">
                                        <td class="orddd_holidays_list" id="orddd_delivery_day">' . $date_str . '</td>
                                        <td class="orddd_holidays_list" id="orddd_time_slot">' . $timeslot_values['time_slot'] . '</td>
                                        <td class="orddd_holidays_list" id="orddd_time_slot_lockout">' . $timeslot_values['lockout'] . '</td>';
									if ( $additional_charges != '' ) {
										echo '<td class="orddd_holidays_list" id="orddd_time_slot_lockout">' . $currency_symbol . '' . $timeslot_values['additional_charges'] . '</td>';
									} else {
										echo '<td class="orddd_holidays_list" id="orddd_time_slot_lockout"></td>';
									}
										echo '<td class="orddd_holidays_list" id="orddd_time_slot_lockout">' . $timeslot_values['additional_charges_label'] . '</td>
                                        <td class="orddd_holidays_list"><a href="javascript:void(0)" class="edit_time_slot" id="orddd_edit_time_slot_' . $i . '">' . __( 'Edit', 'order-delivery-date' ) . '</a> | <a href="javascript:void(0)" class="confirmation_time_slot" id="orddd_delete_time_slot_' . $i . '">' . __( 'Delete', 'order-delivery-date' ) . '</a></td>
                                    </tr>';
									$i++;
								}
							}
							$time_slot_hidden_str .= $hv . '},';
						}
					}
				}
				echo '<input type="hidden" id="edit_row_id"  name="edit_row_id" value="' . $row_id . '"/>';
			}
		}
		echo '</table>';
		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );
		echo '<input type="hidden" id="orddd_time_slot_hidden"  name="orddd_shipping_based_settings_' . $option_key . '[time_slots]" value="' . $time_slot_hidden_str . '"/>';
	}

	/**
	 * Callback for adding a unique key to each row while saving the Custom delivery settings
	 *
	 * @param array $input
	 * @since 2.8.3
	 */

	public static function orddd_shipping_based_settings_option_key_callback( $input ) {
		$row_id                      = '';
		$is_pickup_location_selected = 'no';

		if ( ! empty( $_POST ) && isset( $_POST['setting_id'] ) && '' !== $_POST['setting_id'] ) { // phpcs:ignore
			return $input;
		}

		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id = get_option( 'orddd_shipping_based_settings_option_key' );
			}
		} else {
			if ( isset( $_POST['edit_row_id'] ) ) {
				$row_id = get_option( 'orddd_shipping_based_settings_option_key' );
			}
		}

		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );

		if ( isset( $_POST[ 'orddd_shipping_based_settings_' . $option_key ] ) ) {
			$shipping_settings = $_POST[ 'orddd_shipping_based_settings_' . $option_key ];
		} else {
			$shipping_settings = array();
		}

		if ( has_filter( 'is_pickup_location_selected' ) ) {
			$is_pickup_location_selected = apply_filters( 'is_pickup_location_selected', $_POST, $option_key );
		}

		if ( ( isset( $shipping_settings['shipping_methods'] ) && is_array( $shipping_settings['shipping_methods'] ) && count( $shipping_settings['shipping_methods'] ) > 0 ) || ( isset( $shipping_settings['product_categories'] ) && is_array( $shipping_settings['product_categories'] ) && count( $shipping_settings['product_categories'] ) > 0 ) || ( isset( $shipping_settings['orddd_locations'] ) && is_array( $shipping_settings['orddd_locations'] ) && count( $shipping_settings['orddd_locations'] ) > 0 ) ) {
		} elseif ( 'yes' === $is_pickup_location_selected ) {
		} else {
			$option_key = get_option( 'orddd_shipping_based_settings_option_key' );
		}

		return $option_key;
	}

	/**
	 * Callback for saving custom shipping methods
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_settings_save_callback( $input ) {
		$row_id = '';
		if ( ( isset( $_GET['action'] ) && 'shipping_based' === $_GET['action'] ) && ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id = $_GET['row_id'];
			}
		} else {
			if ( isset( $_POST['edit_row_id'] ) ) {
				$row_id = $_POST['edit_row_id'];
			}
		}

		if ( ( isset( $input['shipping_methods'] ) && is_array( $input['shipping_methods'] ) && count( $input['shipping_methods'] ) > 0 ) || ( isset( $input['product_categories'] ) && is_array( $input['product_categories'] ) && count( $input['product_categories'] ) > 0 ) || ( isset( $input['orddd_locations'] ) && is_array( $input['orddd_locations'] ) && count( $input['orddd_locations'] ) > 0 ) ) {
			$new_input = $input;

			if ( '' !== $row_id ) {
				$option_key        = orddd_common::get_shipping_setting_option_key( $row_id );
				$shipping_settings = get_option( 'orddd_shipping_based_settings_' . $row_id );
				if ( isset( $shipping_settings['orddd_lockout_date'] ) ) {
					$new_input['orddd_lockout_date'] = $shipping_settings['orddd_lockout_date'];
				}
				if ( isset( $shipping_settings['orddd_lockout_time_slot'] ) ) {
					$new_input['orddd_lockout_time_slot'] = $shipping_settings['orddd_lockout_time_slot'];
				}
			}
			$_REQUEST['_wp_http_referer'] = get_admin_url() . 'admin.php?page=order_delivery_date&action=shipping_based&settings-updated=true';
		} else {
			$option_key = orddd_common::get_shipping_setting_option_key( $row_id );
			unregister_setting( 'orddd_shipping_based_settings', 'orddd_shipping_based_settings_' . $option_key );

			if ( 'on' === get_option( 'orddd_enable_shipping_based_delivery' ) ) {
				if ( isset( $input['delivery_settings_based_on'] ) && 'shipping_methods' === $input['delivery_settings_based_on'][0] ) {
					add_settings_error( 'orddd_shipping_based_settings_' . $option_key, 'shipping_methods_error', 'Please select shipping methods', 'error' );
				} elseif ( isset( $input['delivery_settings_based_on'] ) && 'product_categories' === $input['delivery_settings_based_on'][0] ) {
					add_settings_error( 'orddd_shipping_based_settings_' . $option_key, 'shipping_methods_error', 'Please select product categories', 'error' );
				} elseif ( isset( $input['delivery_settings_based_on'] ) && 'orddd_locations' === $input['delivery_settings_based_on'][0] ) {
					add_settings_error( 'orddd_shipping_based_settings_' . $option_key, 'shipping_methods_error', 'Please select locations', 'error' );
				}
			}
			$new_input = false;
		}

		if ( has_filter( 'orddd_save_custom_settings' ) ) {
			$new_input = apply_filters( 'orddd_save_custom_settings', $new_input, $input, $row_id );
		}

		wp_cache_delete( 'orddd_get_shipping_settings_result' );
		return $new_input;
	}

	/**
	 * Get all the WooCommerce shipping methods
	 *
	 * Compatible Plugins:
	 * WooCommerce Table Rate Shipping plugin by WooCommerce
	 * WooCommerce USPS Shipping plugin by Automattic
	 * WooCommerce Fedex Shipping plugin by WooCommerce
	 * WooCommerce FedEx Shipping Plugin with Print Label by Xadapter
	 * Flexible Shipping plugin by WP Desk
	 * WooCommerce UPS Shipping – Live Rates and Access Points plugin by WP Desk
	 * WooCommerce UPS Shipping by WooCommerce
	 * Table rate shipping plugin by Bolder elements
	 * Weight based Shippng plugin by weightbasedshipping.com
	 *
	 * @since 2.8.3
	 */
	public static function orddd_get_shipping_methods() {
		global $wpdb, $woocommerce;
		$shipping_classes = array();

		// WooCommerce default Shipping methods.
		$active_methods   = array();
		$shipping_methods = $woocommerce->shipping->load_shipping_methods();
		foreach ( $shipping_methods as $id => $shipping_method ) {
			if ( defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, '2.6', '>=' ) > 0 ) {
				if ( isset( $shipping_method->id ) && false !== strstr( $shipping_method->id, 'legacy' ) ) {
					$title                 = $shipping_method->title . ' (Legacy)';
					$active_methods[ $id ] = array(
						'title'      => $title,
						'tax_status' => $shipping_method->tax_status,
					);
				}
			} else {
				if ( isset( $shipping_method->enabled ) && 'yes' === $shipping_method->enabled ) {
					$active_methods[ $id ] = array(
						'title'      => $shipping_method->title,
						'tax_status' => $shipping_method->tax_status,
					);
				}
			}
		}

		// Default WooCommerce Shipping zones from verison 2.6
		$shipping_default_zones = array();
		if ( defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, '2.6', '>=' ) > 0 ) {
			if ( class_exists( 'WC_Shipping_Zones' ) ) {
				$shipping_zone_class = new WC_Shipping_Zones();
				$shipping_zones      = array();
				if ( method_exists( $shipping_zone_class, 'get_zones' ) ) {
					$shipping_zones = $shipping_zone_class->get_zones();
				}
				foreach ( $shipping_zones as $shipping_default_key => $shipping_default_value ) {
					if ( isset( $shipping_default_value['shipping_methods'] ) ) {
						foreach ( $shipping_default_value['shipping_methods'] as $key => $value ) {
							if ( 'table_rate' == $value->id ) {
								// Custom delivery settings for WooCommerce Table Rate Shipping plugin by WooCommerce.
								$table_rate_shipping_classes = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix . "woocommerce_shipping_table_rates` WHERE shipping_method_id = {$value->instance_id} ORDER BY rate_order ASC;" );
								foreach ( $table_rate_shipping_classes as $tkey => $tvalue ) {
									$option_settings = get_option( 'woocommerce_table_rate_' . $value->instance_id . '_settings' );

									if ( '' == $option_settings['calculation_type'] && '' !=  $tvalue->rate_label ) {
										$title = $shipping_default_value['zone_name'] . ' -> ' . $value->title . ' -> ' . $tvalue->rate_label;
										$id    = $value->id . ':' . $value->instance_id . ':' . $tvalue->rate_id;
									} elseif( '' == $option_settings['calculation_type'] && '' ==  $tvalue->rate_label ) {
										$title = $shipping_default_value['zone_name'] . ' -> ' . $value->title ;
										$id    = $value->id . ':' . $value->instance_id . ':' . $tvalue->rate_id;
									} else {
										$title = $shipping_default_value['zone_name'] . ' -> ' . $value->title;
										$id    = $value->id . ':' . $value->instance_id;
									}
									$shipping_default_zones[] = array(
										'shipping_default_zone_title' => $title,
										'shipping_default_zone_id' => $id,
									);
								}
							} elseif ( 'usps' == $value->id ) {
								// Custom Delivery Settings for WooCommerce USPS Shipping plugin by Automattic.
								$usps_settings  = $value->instance_settings;
								$usps_title     = $shipping_default_value['zone_name'] . ' -> ' . $value->title . ' -> ';
								$express_title  = $usps_title . 'Priority Mail Express Flat Rate®';
								$priority_title = $usps_title . 'Priority Mail Flat Rate®';

								if ( 'yes' == $usps_settings['enable_flat_rate_boxes'] ) {
									$express_id = $shipping_default_value['zone_id'] . ':' . 'usps' . ':' . 'flat_rate_box_express';
									if ( isset( $usps_settings['flat_rate_express_title'] ) && '' != $usps_settings['flat_rate_express_title'] ) {
										$express_title = $usps_title . $usps_settings['flat_rate_express_title'];
									}

									$priority_id = $shipping_default_value['zone_id'] . ':' . 'usps' . ':' . 'flat_rate_box_priority';
									if ( isset( $usps_settings['flat_rate_priority_title'] ) && '' != $usps_settings['flat_rate_priority_title'] ) {
										$priority_title = $usps_title . $usps_settings['flat_rate_priority_title'];
									}

									$shipping_default_zones[] = array(
										'shipping_default_zone_title' => $express_title,
										'shipping_default_zone_id' => $express_id,
									);

									$shipping_default_zones[] = array(
										'shipping_default_zone_title' => $priority_title,
										'shipping_default_zone_id' => $priority_id,
									);
								} elseif ( 'priority' == $usps_settings['enable_flat_rate_boxes'] ) {
									$priority_id = $shipping_default_value['zone_id'] . ':' . 'usps' . ':' . 'flat_rate_box_priority';
									if ( isset( $usps_settings['flat_rate_priority_title'] ) && '' != $usps_settings['flat_rate_priority_title'] ) {
										$priority_title = $usps_title . $usps_settings['flat_rate_priority_title'];
									}

									$shipping_default_zones[] = array(
										'shipping_default_zone_title' => $priority_title,
										'shipping_default_zone_id' => $priority_id,
									);
								} elseif ( 'express' == $usps_settings['enable_flat_rate_boxes'] ) {
									$express_id = $shipping_default_value['zone_id'] . ':' . 'usps' . ':' . 'flat_rate_box_express';
									if ( isset( $usps_settings['flat_rate_express_title'] ) && '' != $usps_settings['flat_rate_express_title'] ) {
										$express_title = $usps_title . $usps_settings['flat_rate_express_title'];
									}

									$shipping_default_zones[] = array(
										'shipping_default_zone_title' => $express_title,
										'shipping_default_zone_id' => $express_id,
									);
								}

								if ( 'yes' == $usps_settings['enable_standard_services'] ) {
									$usps_services = $usps_settings['services'];
									foreach ( $usps_services as $usps_skey => $usps_svalue ) {
										$usps_service_name = $usps_svalue['name'];
										if ( '' == $usps_service_name ) {
											$usps_service_name = orddd_common::orddd_get_shipping_service_name( $usps_skey );
										}
										$id                       = $shipping_default_value['zone_id'] . ':' . 'usps' . ':' . $usps_skey;
										$shipping_default_zones[] = array(
											'shipping_default_zone_title' => $usps_title . $usps_service_name,
											'shipping_default_zone_id' => $id,
										);
									}
								}
							} elseif ( 'fedex' == $value->id && is_plugin_active( 'woocommerce-shipping-fedex/woocommerce-shipping-fedex.php' ) ) {
								// Custom delivery settings for WooCommerce Fedex Shipping plugin by WooCommerce

								$fedex_instance = $value->instance_id;
								$fedex_settings = $value->instance_settings;
								$fedex_title    = $shipping_default_value['zone_name'] . ' -> ' . $value->title . ' -> ';

								$fedex_services = $fedex_settings['services'];
								if ( is_array( $fedex_services ) && count( $fedex_services ) > 0 ) {
									foreach ( $fedex_services as $fedex_services_key => $fedex_services_value ) {
										$fedex_services_enabled = $fedex_services_value['enabled'];
										if ( '1' == $fedex_services_enabled ) {
											$fedex_services_name = $fedex_services_value['name'];
											if ( '' == $fedex_services_name ) {
												$fedex_services_name = orddd_common::orddd_get_fedex_service_name( $fedex_services_key );
											}
											$fedex_id = 'fedex' . ':' . $fedex_instance . ':' . $fedex_services_key;

											$shipping_default_zones[] = array(
												'shipping_default_zone_title' => $fedex_title . $fedex_services_name,
												'shipping_default_zone_id' => $fedex_id,
											);
										}
									}
								}
							} elseif ( 'fedex' == $value->id ) {
								// Custom delivery settings for WooCommerce FedEx Shipping Plugin with Print Label by Xadapter

								$fedex_instance = $value->instance_id;
								$fedex_settings = $value->instance_settings;
								$fedex_title    = $shipping_default_value['zone_name'] . ' -> ' . $value->title . ' -> ';

								$fedex_services = $fedex_settings['services'];
								if ( is_array( $fedex_services ) && count( $fedex_services ) > 0 ) {
									foreach ( $fedex_services as $fedex_services_key => $fedex_services_value ) {
										$fedex_services_enabled = $fedex_services_value['enabled'];
										if ( '1' == $fedex_services_enabled ) {
											$fedex_services_name = $fedex_services_value['name'];
											if ( '' == $fedex_services_name ) {
												$fedex_services_name = orddd_common::orddd_get_fedex_service_name( $fedex_services_key );
											}
											$fedex_id                 = $shipping_default_value['zone_id'] . ':' . 'fedex' . ':' . $fedex_services_key;
											$shipping_default_zones[] = array(
												'shipping_default_zone_title' => $fedex_title . $fedex_services_name,
												'shipping_default_zone_id' => $fedex_id,
											);
										}
									}
								}
							} elseif ( 'flexible_shipping' == $value->id ) {
								// Custom Delivery Settings for Flexible Shipping plugin by WP Desk.
								$flexible_methods = get_option( $value->shipping_methods_option );

								foreach ( $flexible_methods as $flexible_methods_key => $flexible_methods_value ) {
									$flexible_title = $shipping_default_value['zone_name'] . ' -> ' . $value->title . ' -> ';
									if ( 'yes' == $flexible_methods_value['method_enabled'] ) {

										$flexible_title .= $flexible_methods_value['method_title'];

										$shipping_default_zones[] = array(
											'shipping_default_zone_title' => $flexible_title,
											'shipping_default_zone_id' => $flexible_methods_value['id_for_shipping'],
										);
									}
								}
							} elseif ( 'flexible_shipping_ups' == $value->id ) {
								// Custom Delivery Setting for WooCommerce UPS Shipping – Live Rates and Access Points plugin by WP Desk.
								$flexible_methods = get_option( 'woocommerce_flexible_shipping_ups_' . $value->instance_id . '_settings' );
								if ( isset( $flexible_methods['custom_services'] ) && $flexible_methods['custom_services'] == 'yes' ) {
									$flexible_shipping_services = $flexible_methods['services'];
									$flexible_title             = $shipping_default_value['zone_name'] . ' -> ' . $value->title . ' -> ';
									foreach ( $flexible_shipping_services as $service_key => $service_value ) {
										if ( isset( $service_value['enabled'] ) && $service_value['enabled'] == true ) {
											$flexible_title          .= $service_value['name'];
											$shipping_default_zones[] = array(
												'shipping_default_zone_title' => $flexible_title,
												'shipping_default_zone_id' => $value->id . ':' . $value->instance_id . ':' . $service_key,
											);
										}
									}
								}

								if ( isset( $flexible_methods['fallback'] ) && $flexible_methods['fallback'] == 'yes' ) {
									$flexible_title           = $shipping_default_value['zone_name'] . ' -> ' . $value->title;
									$id                       = $value->id . ':' . $value->instance_id . ':fallback';
									$shipping_default_zones[] = array(
										'shipping_default_zone_title' => $flexible_title,
										'shipping_default_zone_id' => $id,
									);
								}
							} elseif ( 'ups' == $value->id ) {
								// Custom Delivery Settings for WooCommerce UPS Shipping by WooCommerce
								$ups_instance = $value->instance_id;
								$ups_settings = $value->instance_settings;
								$ups_title    = $shipping_default_value['zone_name'] . ' -> ' . $value->title . ' -> ';

								$ups_services = $ups_settings['services'];
								foreach ( $ups_services as $ups_services_key => $ups_services_value ) {
									$ups_services_enabled = $ups_services_value['enabled'];
									if ( '1' == $ups_services_enabled ) {
										$ups_services_name = $ups_services_value['name'];
										if ( '' == $ups_services_name ) {
											$ups_services_name = orddd_common::orddd_get_ups_service_name( $ups_services_key );
										}
										$ups_id                   = 'ups' . ':' . $ups_instance . ':' . $ups_services_key;
										$shipping_default_zones[] = array(
											'shipping_default_zone_title' => $ups_title . $ups_services_name,
											'shipping_default_zone_id' => $ups_id,
										);
									}
								}
							} elseif ( 'betrs_shipping' == $value->id ) {
								// Custom Delivery Settings for Table rate shipping plugin by Bolder elements.
								$betrs_instance          = $value->instance_id;
								$betrs_options_save_name = $value->id . '_options-' . $betrs_instance;
								$betrs_shipping_options  = get_option( $betrs_options_save_name );

								$betrs_settings = $betrs_shipping_options['settings'];

								foreach ( $betrs_settings as $betrs_settings_key => $betrs_settings_value ) {
									$betrs_title  = $shipping_default_value['zone_name'] . ' -> ' . $value->title . ' -> ';
									$betrs_title .= $betrs_settings_value['title'];
									if ( '' == $betrs_title ) {
										$betrs_title .= 'Table Rate';
									}

									$betrs_id                 = $value->id . ':' . $betrs_instance . '-' . $betrs_settings_value['option_id'];
									$shipping_default_zones[] = array(
										'shipping_default_zone_title' => $betrs_title,
										'shipping_default_zone_id' => $betrs_id,
									);
								}
							} elseif ( 'wbs' == $value->id && is_plugin_active( 'weight-based-shipping-for-woocommerce/plugin.php' ) ) {
								// Custom Delivery Settings for Weight based Shippng plugin by weightbasedshipping.com
								$wbs_settings = get_option( 'wbs_' . $value->instance_id . '_config' );
								if ( isset( $wbs_settings['enabled'] ) && $wbs_settings['enabled'] == 1 ) {
									foreach ( $wbs_settings['rules'] as $rk => $rv ) {
										if ( isset( $rv['meta']['enabled'] ) && $rv['meta']['enabled'] == 1 ) {
											$package_title            = $rv['meta']['title'];
											$package_id               = self::orddd_get_shipping_package_id( $package_title );
											$title                    = $shipping_default_value['zone_name'] . ' -> ' . $value->title . ' -> ' . $package_title;
											$id                       = $value->id . ':' . $value->instance_id . ':' . $package_id;
											$shipping_default_zones[] = array(
												'shipping_default_zone_title' => $title,
												'shipping_default_zone_id' => $id,
											);
										}
									}
								}
							} elseif ( 'canada_post' == $value->id ) {
								$settings = $value->custom_services;
								foreach ( $settings as $service => $options ) {
									if ( $options['enabled'] ) {
										$title                        = $shipping_default_value['zone_name'] . ' -> ' . $value->title . ' -> ' . $service;
											$id                       = $value->id . ':' . $service;
											$shipping_default_zones[] = array(
												'shipping_default_zone_title' => $title,
												'shipping_default_zone_id' => $id,
											);
									}
								}
							} elseif ( 'tree_table_rate' == $value->id ) {
								$shipping_default_zones[] = ORDDD_Tree_Table_Rate::orddd_get_tree_table_shipping_zones( $shipping_default_value, $value );
							} else {
								// WooCommerce Default Shipping methods for shipping zones.
								$title                    = $shipping_default_value['zone_name'] . ' -> ' . $value->title;
								$id                       = $value->id . ':' . $value->instance_id;
								$shipping_default_zones[] = array(
									'shipping_default_zone_title' => $title,
									'shipping_default_zone_id' => $id,
								);
							}
						}
					}
				}

				$raw_shipping_method = 'SELECT instance_id, method_id FROM `' . $wpdb->prefix . 'woocommerce_shipping_zone_methods` WHERE zone_id = 0';
				$results             = $wpdb->get_results( $raw_shipping_method );
				foreach ( $results as $result_key => $result_value ) {
					$wc_shipping     = WC_Shipping::instance();
					$allowed_classes = $wc_shipping->get_shipping_method_class_names();
					if ( ! empty( $results ) && in_array( $result_value->method_id, array_keys( $allowed_classes ) ) ) {
						if ( isset( $allowed_classes[ $result_value->method_id ] ) ) {
							$class_name = $allowed_classes[ $result_value->method_id ];
							if ( is_object( $class_name ) ) {
								$class_name = get_class( $class_name );
							}
							$default_shipping_method = new $class_name( $result_value->instance_id );
							if ( $default_shipping_method != '' ) {
								if ( 'table_rate' != $default_shipping_method->id && 'flexible_shipping_ups' != $default_shipping_method->id ) {
									// Default Shipping methods for Rest of the World Shipping zone.
									$title                    = 'Rest of the World' . ' -> ' . $default_shipping_method->title;
									$id                       = $default_shipping_method->id . ':' . $result_value->instance_id;
									$shipping_default_zones[] = array(
										'shipping_default_zone_title' => $title,
										'shipping_default_zone_id' => $id,
									);
								}

								if ( 'fedex' == $default_shipping_method->id ) {
									// Rest of the World shipping zone with WooCommerce Fedex Shipping plugin by WooCommerce.
									$fedex_settings = get_option( 'woocommerce_fedex_' . $result_value->instance_id . '_settings' );
									$fedex_services = $fedex_settings['services'];
									if ( is_array( $fedex_services ) && count( $fedex_services ) > 0 ) {
										foreach ( $fedex_services as $fedex_services_key => $fedex_services_value ) {
											$fedex_services_enabled = $fedex_services_value['enabled'];
											if ( '1' == $fedex_services_enabled ) {
												$fedex_services_name = $fedex_services_value['name'];
												if ( '' == $fedex_services_name ) {
													$fedex_services_name = orddd_common::orddd_get_fedex_service_name( $fedex_services_key );
												}
												$title                    = 'Rest of the World' . ' -> ' . $default_shipping_method->title . ' -> ' . $fedex_services_name;
												$id                       = 0 . ':' . 'fedex' . ':' . $fedex_services_key;
												$shipping_default_zones[] = array(
													'shipping_default_zone_title' => $title,
													'shipping_default_zone_id' => $id,
												);
											}
										}
									}
								}

								if ( 'table_rate' == $default_shipping_method->id ) {
									// Rest of the World shipping zone with WooCommerce Table Rate Shipping plugin by WooCommerce
									$table_rate_shipping_classes = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix . "woocommerce_shipping_table_rates` WHERE shipping_method_id = {$default_shipping_method->instance_id} ORDER BY rate_order ASC;" );
									foreach ( $table_rate_shipping_classes as $tkey => $tvalue ) {
										$option_settings = get_option( 'woocommerce_table_rate_' . $default_shipping_method->instance_id . '_settings' );

										if ( '' == $option_settings['calculation_type'] ) {
											$title = 'Rest of the World' . ' -> ' . $default_shipping_method->title . ' -> ' . $tvalue->rate_label;
											$id    = $default_shipping_method->id . ':' . $default_shipping_method->instance_id . ':' . $tvalue->rate_id;
										} else {
											$title = 'Rest of the World' . ' -> ' . $default_shipping_method->title;
											$id    = $default_shipping_method->id . ':' . $default_shipping_method->instance_id;
										}
										$shipping_default_zones[] = array(
											'shipping_default_zone_title' => $title,
											'shipping_default_zone_id' => $id,
										);
									}
								}

								if ( 'flexible_shipping' == $default_shipping_method->id ) {
									// Rest of the World shipping zone with Flexible Shipping plugin by WP Desk
									$flexible_methods = get_option( $default_shipping_method->shipping_methods_option );

									foreach ( $flexible_methods as $flexible_methods_key => $flexible_methods_value ) {
										$flexible_title = 'Rest of the World' . ' -> ' . $default_shipping_method->title . ' -> ';
										if ( 'yes' == $flexible_methods_value['method_enabled'] ) {

											$flexible_title .= $flexible_methods_value['method_title'];

											$shipping_default_zones[] = array(
												'shipping_default_zone_title' => $flexible_title,
												'shipping_default_zone_id' => $flexible_methods_value['id_for_shipping'],
											);
										}
									}
								}

								if ( 'flexible_shipping_ups' == $default_shipping_method->id ) {
									// Rest of the World shipping zone with WooCommerce UPS Shipping – Live Rates and Access Points plugin by WP Desk
									$flexible_methods = get_option( 'woocommerce_flexible_shipping_ups_' . $result_value->instance_id . '_settings' );
									if ( isset( $flexible_methods['custom_services'] ) && $flexible_methods['custom_services'] == 'yes' ) {
										$flexible_shipping_services = $flexible_methods['services'];
										$flexible_title             = 'Rest of the World' . ' -> ' . $value->title . ' -> ';
										foreach ( $flexible_shipping_services as $service_key => $service_value ) {
											if ( isset( $service_value['enabled'] ) && $service_value['enabled'] == true ) {
												$flexible_title          .= $service_value['name'];
												$shipping_default_zones[] = array(
													'shipping_default_zone_title' => $flexible_title,
													'shipping_default_zone_id' => $value->id . ':' . $result_value->instance_id . ':' . $service_key,
												);
											}
										}
									}

									if ( isset( $default_shipping_method->instance_settings['fallback'] ) && $default_shipping_method->instance_settings['fallback'] == 'yes' ) {
										$title                    = 'Rest of the World' . ' -> ' . $default_shipping_method->title;
										$id                       = $default_shipping_method->id . ':' . $result_value->instance_id . ':fallback';
										$shipping_default_zones[] = array(
											'shipping_default_zone_title' => $title,
											'shipping_default_zone_id' => $id,
										);
									}
								}

								if ( 'wbs' == $default_shipping_method->id && is_plugin_active( 'weight-based-shipping-for-woocommerce/plugin.php' ) ) {
									// Rest of the World shipping zone with  Weight based Shippng plugin by weightbasedshipping.com
									$wbs_settings = get_option( 'wbs_' . $result_value->instance_id . '_config' );
									if ( isset( $wbs_settings['enabled'] ) && $wbs_settings['enabled'] == 1 ) {
										foreach ( $wbs_settings['rules'] as $rk => $rv ) {
											if ( isset( $rv['meta']['enabled'] ) && $rv['meta']['enabled'] == 1 ) {
												$package_title = $rv['meta']['title'];
												$package_id    = self::orddd_get_shipping_package_id( $package_title );

												$title = 'Rest of the World' . ' -> ' . $value->title . ' -> ' . $package_title;
												$id    = $default_shipping_method->id . ':' . $result_value->instance_id . ':' . $package_id;

												$shipping_default_zones[] = array(
													'shipping_default_zone_title' => $title,
													'shipping_default_zone_id' => $id,
												);
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}

		// WooCommerce Tree Table Rate Shipping compatibility.
		if ( is_plugin_active( 'wc-tree-table-rate-shipping/wc-tree-table-rate-shipping.php' ) ) {
			$shipping_default_zones[] = ORDDD_Tree_Table_Rate::orddd_get_tree_table_shipping_zones( '', '', true );
		}

		// Fetch the shipping packages from Weight Based Shipping for WooCommerce plugin
		if ( is_plugin_active( 'weight-based-shipping-for-woocommerce/plugin.php' ) ) {
			$wbs_settings = get_option( 'wbs_config' );
			if ( isset( $wbs_settings['enabled'] ) && $wbs_settings['enabled'] == 1 ) {
				foreach ( $wbs_settings['rules'] as $rk => $rv ) {
					if ( isset( $rv['meta']['enabled'] ) && $rv['meta']['enabled'] == 1 ) {
						$package_title = $rv['meta']['title'];
						$package_id    = self::orddd_get_shipping_package_id( $package_title );

						$title = 'Weight Based Shipping -> ' . $package_title;
						$id    = 'wbs:' . $package_id;

						$shipping_default_zones[] = array(
							'shipping_default_zone_title' => $title,
							'shipping_default_zone_id'    => $id,
						);
					}
				}
			}
		}

		// Fetch the shipping methods from FedEx plugin from X-Adapter
		$wf_fedex_settings = get_option( 'woocommerce_wf_fedex_woocommerce_shipping_settings' );
		$fedex_services    = array();
		if ( $wf_fedex_settings != '' && $wf_fedex_settings != '[]' && $wf_fedex_settings != '{}' && $wf_fedex_settings != null ) {
			$fedex_services = $wf_fedex_settings['services'];
		}
		foreach ( $fedex_services as $fedex_services_key => $fedex_services_value ) {
			$fedex_services_enabled = $fedex_services_value['enabled'];
			if ( '1' == $fedex_services_enabled ) {
				$fedex_services_name = $fedex_services_value['name'];
				if ( '' == $fedex_services_name ) {
					$fedex_services_name = orddd_common::orddd_get_fedex_service_name( $fedex_services_key );
				}
				$title                    = $fedex_services_name;
				$id                       = 'wf_fedex_woocommerce_shipping:' . $fedex_services_key;
				$shipping_default_zones[] = array(
					'shipping_default_zone_title' => $title,
					'shipping_default_zone_id'    => $id,
				);
			}
		}

		// Fetch Shipping methods from WooCommerce Table Rate shipping plugin.
		$table_exists = $wpdb->get_results( "SHOW TABLES LIKE '" . $wpdb->prefix . "woocommerce_shipping_zone_shipping_methods'" );
		if ( is_array( $table_exists ) && count( $table_exists ) > 0 && is_plugin_active( 'woocommerce-table-rate-shipping/woocommerce-table-rate-shipping.php' ) ) {
			$shipping_zone_methods = $wpdb->get_results( 'SELECT * FROM `' . $wpdb->prefix . 'woocommerce_shipping_zone_shipping_methods`' );
			foreach ( $shipping_zone_methods as $shipping_key => $shipping_value ) {
				$option_settings = get_option( 'woocommerce_table_rate-' . $shipping_value->shipping_method_id . '_settings' );
				if ( isset( $option_settings['enabled'] ) && $option_settings['enabled'] != 'no' ) {
					if ( isset( $option_settings['calculation_type'] ) && $option_settings['calculation_type'] == '' ) {
						$shipping_zone_classes = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix . "woocommerce_shipping_table_rates` WHERE shipping_method_id = {$shipping_value->shipping_method_id} ORDER BY rate_order ASC" );
						foreach ( $shipping_zone_classes as $key => $value ) {
							$shipping_classes[] = array(
								'shipping_method_title' => $option_settings['title'],
								'shipping_method_id'    => $shipping_value->shipping_method_id,
								'rate_label'            => $value->rate_label,
								'rate_id'               => $value->rate_id,
							);
						}
					} else {
						$shipping_classes[] = array(
							'shipping_method_title' => $option_settings['title'],
							'shipping_method_id'    => $shipping_value->shipping_method_id,
							'rate_label'            => '',
							'rate_id'               => '',
						);
					}
				}
			}
		}
		$shipping_methods = array();

		// Fetch Shipping Methods from Woocommerce Advanced Shipping plugin.
		$methods = get_posts(
			array(
				'posts_per_page' => '-1',
				'post_type'      => 'was',
				'post_status'    => array( 'publish' ),
				'order'          => 'ASC',
			)
		);
		if ( is_array( $methods ) && count( $methods ) > 0 && is_plugin_active( 'woocommerce-advanced-shipping/woocommerce-advanced-shipping.php' ) ) {
			foreach ( $methods as $method ) {
				$method_details = get_post_meta( $method->ID, '_was_shipping_method', true );
				if ( isset( $method_details['shipping_title'] ) && $method_details['shipping_title'] != '' ) {
					$method_title = $method->post_title . ' -> ' . $method_details['shipping_title']; // Display the shipping method name along with title.
				} else {
					$method_title = $method->post_title;
				}
				$shipping_methods[] = array(
					'title'      => $method_title,
					'method_key' => $method->ID,
				);
			}
		}

		// Advanced Flat Rate Shipping Method WooCommerce by Multidots.
		$methods = get_posts(
			array(
				'posts_per_page' => '-1',
				'post_type'      => 'wc_afrsm',
				'post_status'    => array( 'publish' ),
				'order'          => 'ASC',
			)
		);

		if ( is_array( $methods ) && count( $methods ) > 0 && ( is_plugin_active( 'woo-extra-flat-rate/advanced-flat-rate-shipping-for-woocommerce.php' ) || is_plugin_active( 'advanced-flat-rate-shipping-for-woocommerce/advanced-flat-rate-shipping-for-woocommerce.php' ) ) ) {
			foreach ( $methods as $method ) {
				$method_title       = $method->post_title;
				$shipping_methods[] = array(
					'title'      => $method_title,
					'method_key' => 'advanced_flat_rate_shipping:' . $method->ID,
				);
			}
		}

		 // Fetch Shipping Classes
		$args_shipping_class = array(
			'hide_empty' => 0,
		);

		$default_shipping_classes = get_terms( 'product_shipping_class', $args_shipping_class );

		foreach ( $active_methods as $method_key => $method_name ) {
			$shipping_methods[] = array(
				'title'      => $method_name['title'],
				'method_key' => $method_key,
			);
		}

		foreach ( $shipping_default_zones as $zone_key => $zone_value ) {
			$shipping_methods[] = array(
				'title'      => $zone_value['shipping_default_zone_title'],
				'method_key' => $zone_value['shipping_default_zone_id'],
			);
		}

		foreach ( $default_shipping_classes as $class_key => $class_value ) {
			$shipping_methods[] = array(
				'title'      => $class_value->name,
				'method_key' => $class_value->slug,
			);
		}

		foreach ( $shipping_classes as $key => $value ) {
			if ( $value['rate_id'] != '' ) {
				$selected_value = 'table_rate-' . $value['shipping_method_id'] . ' : ' . $value['rate_id'];
				if ( $value['rate_label'] != '' ) {
					$label = $value['shipping_method_title'] . ' > ' . $value['rate_label'];
				} else {
					$label = $value['shipping_method_title'];
				}
			} else {
				$selected_value = 'table_rate-' . $value['shipping_method_id'];
				$label          = $value['shipping_method_title'];
			}

			if ( in_array( $selected_value, $shipping_methods_stored ) ) {
				echo '<option value="' . $selected_value . '" selected>' . __( $label, 'order-delivery-date' ) . '</option>';
			} else {
				echo '<option value="' . $selected_value . '">' . __( $label, 'order-delivery-date' ) . '</option>';
			}
		}

		 // Returns the other shipping methods for the default zones in WooCommerce. 
        // For example, shipping packages from the Advance Shipping packages for WooCommerce plugin.
        return apply_filters( 'orddd_custom_setting_shipping_methods', $shipping_methods );
	}

	/**
	 * Returs the shipping package if for the shipping packages added from the
	 * Weight based Shippng plugin by weightbasedshipping.com
	 *
	 * @param string $package_tile - Title of the shipping package
	 *
	 * @since 9.7
	 */
	public static function orddd_get_shipping_package_id( $package_title ) {
		$idParts   = array();
		$hash      = substr( md5( $package_title ), 0, 8 );
		$idParts[] = $hash;
		$slug      = strtolower( $package_title );
		$slug      = preg_replace( '/[^a-z0-9]+/', '_', $slug );
		$slug      = preg_replace( '/_+/', '_', $slug );
		$slug      = trim( $slug, '_' );
		if ( $slug !== '' ) {
			$idParts[] = $slug;
		}
		$package_id = join( '_', $idParts );
		return $package_id;
	}

	/**
	 * Callback for adding custom Appearance setting
	 */
	public static function orddd_shipping_based_appearance_section_callback() {}

	/**
	 * Callback for setting the delivery date field label on the checkout
	 *
	 * @param array $args Extra arguments containing label & class for the field
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_delivery_date_field_label_callback( $args ) {
		$row_id               = $date_field_label = '';
		$shipping_methods_arr = array();
		if ( ( isset( $_GET['action'] ) && $_GET['action'] == 'shipping_based' ) && ( isset( $_GET['mode'] ) && $_GET['mode'] == 'edit' ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
			}
		}

		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );

		if ( isset( $shipping_methods_arr['orddd_shipping_based_delivery_date_field_label'] ) ) {
			$date_field_label = $shipping_methods_arr['orddd_shipping_based_delivery_date_field_label'];
		}

		echo '<input type="text" name="orddd_shipping_based_settings_' . $option_key . '[orddd_shipping_based_delivery_date_field_label]" id="orddd_shipping_based_delivery_date_field_label" value="' . $date_field_label . '" maxlength="40"/>';

		$html = '<label for="orddd_shipping_based_delivery_date_field_label"> ' . $args[0] . '</label>';
		echo $html;
	}


	/**
	 * Callback for setting the timeslots field label on the checkout
	 *
	 * @param array $args Extra arguments containing label & class for the field
	 * @since 2.8.3
	 */
	public static function orddd_shipping_based_delivery_timeslot_field_label_callback( $args ) {
		$row_id               = $time_field_label = '';
		$shipping_methods_arr = array();
		if ( ( isset( $_GET['action'] ) && $_GET['action'] == 'shipping_based' ) && ( isset( $_GET['mode'] ) && $_GET['mode'] == 'edit' ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id               = $_GET['row_id'];
				$shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
			}
		}

		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );

		if ( isset( $shipping_methods_arr['orddd_shipping_based_delivery_timeslot_field_label'] ) ) {
			$time_field_label = $shipping_methods_arr['orddd_shipping_based_delivery_timeslot_field_label'];
		}

		echo '<input type="text" name="orddd_shipping_based_settings_' . $option_key . '[orddd_shipping_based_delivery_timeslot_field_label]" id="orddd_shipping_based_delivery_timeslot_field_label" value="' . $time_field_label . '" maxlength="40"/>';

		$html = '<label for="orddd_shipping_based_delivery_timeslot_field_label"> ' . $args[0] . '</label>';
		echo $html;
	}


	/**
	 * Callback for adding delivery charges based on number of days.
	 */
	public static function orddd_shipping_based_delivery_charges_based_on_days_section_callback() {
		echo __( '<em>If you want to create generic delivery charges for days except Same day & Next day, then you can set it here.</em>', 'order-delivery-date' );
	}

	/**
	 * Callback for adding delivery charges based on number of days.
	 */
	public static function orddd_shipping_based_delivery_charges_based_on_days_enable_callback( $args ) {
		$row_id                        = $delivery_charges_based_on_days_enable = '';
		$orddd_shipping_based_settings = array();
		if ( ( isset( $_GET['action'] ) && $_GET['action'] == 'shipping_based' ) && ( isset( $_GET['mode'] ) && $_GET['mode'] == 'edit' ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id                        = $_GET['row_id'];
				$orddd_shipping_based_settings = get_option( 'orddd_shipping_based_settings_' . $row_id );
			}
		}

		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );
		if ( isset( $orddd_shipping_based_settings['orddd_delivery_charges_based_on_days_enable'] ) && 'on' === $orddd_shipping_based_settings['orddd_delivery_charges_based_on_days_enable'] ) {
			$delivery_charges_based_on_days_enable = 'checked';
		}
		?>
		<input id="orddd_delivery_charges_based_on_days_enable" type="checkbox" name="orddd_shipping_based_settings_<?php echo $option_key; ?>[orddd_delivery_charges_based_on_days_enable]" <?php echo esc_attr( $delivery_charges_based_on_days_enable ); ?>/>
		<label for="orddd_delivery_charges_based_on_days_enable"><?php echo wp_kses_post( $args[0] ); ?></label>
		<p><br/><?php echo wp_kses_post( $args[1] ); ?><br/></p>
		<?php
	}

	/**
	 * Callback for adding delivery charges fields.
	 */
	public static function orddd_shipping_based_delivery_charges_based_on_days_callback( $args ) {
		$result_count = 3;
		$row_id       = $time_field_label = '';
		$number_value = '';
		$label_value  = '';
		$charge_value = '';
		$orddd_shipping_based_settings = array();

		if ( ( isset( $_GET['action'] ) && $_GET['action'] == 'shipping_based' ) && ( isset( $_GET['mode'] ) && $_GET['mode'] == 'edit' ) ) {
			if ( isset( $_GET['row_id'] ) ) {
				$row_id                                      = $_GET['row_id'];
				$orddd_shipping_based_settings = get_option( 'orddd_shipping_based_settings_' . $row_id );
			}
		}
		if ( isset( $orddd_shipping_based_settings['orddd_additional_settings_based_on_days'] ) && '' !== $orddd_shipping_based_settings['orddd_additional_settings_based_on_days'] && is_array( $orddd_shipping_based_settings['orddd_additional_settings_based_on_days'] ) ) {
			$result_count = count( $orddd_shipping_based_settings['orddd_additional_settings_based_on_days']['number'] );
		}
		$option_key = orddd_common::get_shipping_setting_option_key( $row_id );
		?>
		<div class="orddd_additional_settings_based_on_days_section">
			<?php for ( $i = 0; $i < $result_count; $i++ ) { ?>
				<?php
				if ( isset( $orddd_shipping_based_settings['orddd_additional_settings_based_on_days'] ) && '' !== $orddd_shipping_based_settings['orddd_additional_settings_based_on_days'] && is_array( $orddd_shipping_based_settings['orddd_additional_settings_based_on_days'] ) ) {
					if ( isset( $orddd_shipping_based_settings['orddd_additional_settings_based_on_days']['number'][ $i ] ) && '' !== $orddd_shipping_based_settings['orddd_additional_settings_based_on_days']['number'][ $i ] ) {
						$number_value = 'value="' . $orddd_shipping_based_settings['orddd_additional_settings_based_on_days']['number'][ $i ] . '"';
						$label_value  = 'value="' . $orddd_shipping_based_settings['orddd_additional_settings_based_on_days']['label'][ $i ] . '"';
						$charge_value = 'value="' . $orddd_shipping_based_settings['orddd_additional_settings_based_on_days']['charge'][ $i ] . '"';
					}else {
						$number_value = '';
						$label_value  = '';
						$charge_value = '';
					}
				} 
				?>
				<p class="orddd_additional_settings_based_on_days">
					<input <?php echo $number_value; ?> placeholder="<?php esc_attr_e('Number of days', 'order-delivery-date'); ?>" type="number" min="0" name="orddd_shipping_based_settings_<?php echo $option_key; ?>[orddd_additional_settings_based_on_days][number][]" />
					<input <?php echo $label_value; ?> placeholder="<?php esc_attr_e('Delivery charges label', 'order-delivery-date'); ?>" type="text" name="orddd_shipping_based_settings_<?php echo $option_key; ?>[orddd_additional_settings_based_on_days][label][]" />
					<input <?php echo $charge_value; ?> placeholder="<?php esc_attr_e('Delivery charges', 'order-delivery-date'); ?>" type="text" name="orddd_shipping_based_settings_<?php echo $option_key; ?>[orddd_additional_settings_based_on_days][charge][]" />
					<a class="orddd_additional_settings_remove" href="javascript:;" onclick="orddd_remove_additional_settings_of_days(this)"><i class="dashicons dashicons-trash"></i></a>
				</p>
			<?php } ?>
		</div>
		<p class="orddd_additional_settings_based_on_days_btn" data-count="<?php echo $result_count; ?>">
			<input type="button" class="button button-primary" onclick="orddd_add_new_additional_settings_based_on_days('orddd_shipping_based_settings_<?php echo $option_key; ?>');" value="<?php esc_attr_e('Add more', 'order-delivery-date'); ?>" /> 
		</p>
		<?php
	}


	/**
	 * Callback for activating or deactivating a custom delivery schedule
	 *
	 * @param int custom_setting_id - id of the custom delivery schedule to activate or deactivate
	 * @param string current_state  - activate or deactivate, values: on / off
	 * @since 9.6
	 */
	public static function orddd_toggle_custom_setting_status() {

		if ( isset( $_POST['custom_setting_id'] ) &&
			'' != $_POST['custom_setting_id'] ) {

			$custom_setting_id = sanitize_text_field( $_POST['custom_setting_id'] );
			$current_state     = sanitize_text_field( $_POST['current_state'] );

			if ( 'on' === $current_state ) {
				delete_option( 'orddd_shipping_settings_status_' . $custom_setting_id );
			} elseif ( 'off' === $current_state ) {
				update_option( 'orddd_shipping_settings_status_' . $custom_setting_id, 'inactive' );
			}
		}
		wp_die();
	}

	/**
	 * Ajax callback to add timeslots in bulk.
	 *
	 * @return void
	 */
	public static function orddd_get_time_slots_between_interval() {
		$duration_in_secs = wp_unslash( sanitize_text_field( $_POST['duration_in_secs'] ) );

		$frequency_in_secs = wp_unslash( sanitize_text_field( $_POST['frequency_in_secs'] ) );

		$time_starts_from = wp_unslash( sanitize_text_field( $_POST['time_starts_from'] ) );
		$time_ends_at 	  = wp_unslash( sanitize_text_field( $_POST['time_ends_at'] ) );
		
		if ( '' !== $time_starts_from ) {
			$array_of_time 	   = array();
			$start_time    	   = strtotime( $time_starts_from );
			$end_time      	   = strtotime( $time_ends_at );

			while ( $start_time <= $end_time ) {
				$from_hours = gmdate( "G:i", $start_time );
				$start_time += $duration_in_secs;

				if ( $start_time > $end_time ) {
					break;
				}
				$to_hours   = gmdate( "G:i", $start_time );
				$array_of_time[] = $from_hours . " - " . $to_hours;
				if ( $frequency_in_secs > 0 ) {
					$start_time += $frequency_in_secs;
				}
			}
		}		
		wp_send_json( $array_of_time );
		wp_die();
	}

	/**
	 * Ajax callback to clone the custom settings
	 *
	 * @return void
	 */
	public static function orddd_clone_custom_settings() {
		$custom_setting_id = isset( $_POST['setting_id'] ) && '' !== $_POST['setting_id'] ? $_POST['setting_id'] : '';
		$option_key 	   = get_option( 'orddd_shipping_based_settings_option_key' );
		$custom_settings   = orddd_custom_delivery_functions::orddd_get_delivery_schedule_settings_by_id( $custom_setting_id );

		$custom_settings['delivery_settings_based_on'] = array();
		if ( isset( $custom_settings['shipping_methods'] ) ) {
			$custom_settings['shipping_methods'] = array();
		}

		if ( isset( $custom_settings['product_categories'] ) ) {
			$custom_settings['product_categories'] = array();
		}

		if ( isset( $custom_settings['shipping_methods_for_categories'] ) ) {
			$custom_settings['shipping_methods_for_categories'] = array();
		}

		if ( isset( $custom_settings['orddd_lockout_date'] ) ) {
			$custom_settings['orddd_lockout_date'] = '';
		}

		if ( isset( $custom_settings['orddd_lockout_time_slot'] ) ) {
			$custom_settings['orddd_lockout_time_slot'] = '';
		}

		update_option( 'orddd_shipping_based_settings_' . $option_key, $custom_settings );
		update_option( 'orddd_shipping_based_settings_option_key', $option_key + 1 );

		if( get_option( 'orddd_shipping_based_settings_' . $option_key ) ) {
			wp_send_json('success');
		} else {
			wp_send_json('error');
		}

		wp_die();
	}
}

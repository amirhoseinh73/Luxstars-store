<?php
/**
 * Order Delivery Date Locations
 *
 * Adding Pickup Locations in admin and displaying them on frontend. 
 * 
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Pro-for-WooCommerce/Locations
 * @since 8.4
 * @category Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class orddd_locations {

	/**
	 * Default Constructor
	 * 
	 * @since 8.4
	 */
	public function __construct() {
	    add_action( 'admin_init', array( &$this, 'orddd_location_settings' ) );
		add_action( 'orddd_add_settings_tab', array( &$this, 'orddd_add_settings_tab' ), 1 );
		add_action( 'orddd_add_tab_content', array( &$this, 'orddd_add_tab_content' ) );

		add_action( 'wp_ajax_orddd_locations_save_changes', array( &$this, 'orddd_locations_save_changes' ) );

		add_action( 'orddd_after_custom_product_categories', array( &$this, 'orddd_after_custom_product_categories' ), 10, 1 );

        add_filter( 'orddd_shipping_settings_table_data', array( &$this, 'orddd_shipping_settings_table_data' ) );

        add_action( 'orddd_custom_delivery_settings', array( &$this, 'orddd_custom_delivery_settings' ), 10, 3 );     

        $orddd_shopping_cart_hook = orddd_common::orddd_get_shopping_cart_hook();
        add_action( $orddd_shopping_cart_hook,  array( &$this, 'orddd_locations_after_checkout_billing_form' ), 9 );  

        if( 'on' == get_option( 'orddd_delivery_date_on_cart_page' ) ) {
            add_action( 'woocommerce_cart_collaterals', array( &$this, 'orddd_locations_after_checkout_billing_form' ), 1 );
        }
        
        add_action( 'woocommerce_after_checkout_validation',     array( &$this, 'orddd_validate_pickup_locations' ), 10, 2 );
	}
	
    /**
     * Add mandatory location field settings. 
     * 
     * @hook admin_init
     * @since 8.7
     */
	
	public function orddd_location_settings() {
	    add_settings_section(
	       'orddd_location_settings_section',
	       '',
	       array( &$this, 'orddd_location_settings_callback' ),
	       'orddd_location_settings_page'
        );
	    
	    add_settings_field(
	       'orddd_pickup_location_mandatory',
	       __( 'Pickup Locations Mandatory?:', 'order-delivery-date' ),
	       array( &$this, 'orddd_pickup_location_mandatory_callback' ),
	       'orddd_location_settings_page',
	       'orddd_location_settings_section',
	       array( __( 'Selection of pickup location on the checkout page will become mandatory.', 'order-delivery-date' ) )
        );

        add_settings_field(
            'orddd_auto_populate_first_pickup_location',
            __( 'Auto-populate the first pickup location:', 'order-delivery-date' ),
            array( &$this, 'orddd_auto_populate_first_pickup_location_callback' ),
            'orddd_location_settings_page',
            'orddd_location_settings_section',
            array( __( 'The first pickup location will be auto selected on the checkout page.', 'order-delivery-date' ) )
         );
	    
	    register_setting(
	       'orddd_location_settings',
	       'orddd_pickup_location_mandatory'
        );

        register_setting(
            'orddd_location_settings',
            'orddd_auto_populate_first_pickup_location'
         );
	}
	
	/**
	 * Callback for Pickup Locations settings
	 * 
	 * @since 8.7
	 */
	public function orddd_location_settings_callback() {
	    
	}
	
	/**
	 * Callback for adding Pickup Location Mandatory setting
	 *
	 * @param array $args Extra arguments containing label & class for the field
	 * @since 8.7
	 */
	
	public static function orddd_pickup_location_mandatory_callback( $args ) {
	    $orddd_pickup_location_mandatory = "";
	    if ( get_option( 'orddd_pickup_location_mandatory' ) == 'on' ) {
	        $orddd_pickup_location_mandatory = "checked";
	    }
	    
	    echo '<input type="checkbox" name="orddd_pickup_location_mandatory" id="orddd_pickup_location_mandatory" class="day-checkbox" value="on" ' . $orddd_pickup_location_mandatory . ' />';
	
	    $html = '<label for="orddd_pickup_location_mandatory"> ' . $args[0] . '</label>';
	    echo $html;
    }
    
    /**
	 * Callback for adding Pickup Location Auto populate setting
	 *
	 * @param array $args Extra arguments containing label & class for the field
	 * @since 9.22.0
	 */
	
	public static function orddd_auto_populate_first_pickup_location_callback( $args ) {
	    $orddd_pickup_location_mandatory = "";
	    if ( get_option( 'orddd_auto_populate_first_pickup_location' ) == 'on' ) {
	        $orddd_pickup_location_mandatory = "checked";
	    }
	    
	    echo '<input type="checkbox" name="orddd_auto_populate_first_pickup_location" id="orddd_auto_populate_first_pickup_location" class="day-checkbox" value="on" ' . $orddd_pickup_location_mandatory . ' />';
	
	    $html = '<label for="orddd_auto_populate_first_pickup_location"> ' . $args[0] . '</label>';
	    echo $html;
	}
	
	/**
	 * Add Locations tab under Order Delivery Date -> Settings Menu. 
	 * 
	 * @hook orddd_add_settings_tab
	 * @since 8.4
	 */
	public function orddd_add_settings_tab() {
		if ( isset( $_GET[ 'action' ] ) ) {
			$action = sanitize_text_field( $_GET[ 'action' ] );
		} else {
		    $action = "general_settings";
		}
	    $active_locations = '';
		if( $action == 'orddd_locations' ) {
		    $active_locations = "nav-tab-active";
		}

	    ?>
        <a href="admin.php?page=order_delivery_date&action=orddd_locations" class="nav-tab <?php echo $active_locations; ?>"> <?php _e( 'Pickup Locations', 'order-delivery-date' ); ?> </a>
        <?php
	}

	/**
	 * Add Locations tab content under Order Delivery Date -> Settings Menu. 
	 * 
	 * @hook orddd_add_tab_content
	 * @since 8.4
	 */
	public function orddd_add_tab_content() {
		if ( isset( $_GET[ 'action' ] ) ) {
			$action = sanitize_text_field( $_GET[ 'action' ] );
		} else {
		    $action = "general_settings";
		}
    
		if( $action == 'orddd_locations' ) {
		    print( '<form method="post" action="options.php">' );
                settings_fields( "orddd_location_settings" );
                do_settings_sections( "orddd_location_settings_page" );
                submit_button ( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save_settings', true );
		    print('</form>');
		    include_once( 'includes/views/html-locations.php' );
		}
	}

	/**
	 * Save Locations.
	 * 
	 * @hook wp_ajax_orddd_locations_save_changes
	 * @since 8.4
	 */
	public function orddd_locations_save_changes() {
		$locations = get_option( 'orddd_locations' );

        if( '' === $locations ||
            '{}' === $locations ||
            '[]' === $locations ) {
            $locations = array();
        }
        
		$changes = array();
		if( isset( $_POST[ 'changes' ] ) ) {
        	$changes = $_POST[ 'changes' ];
        }
        foreach ( $changes as $row_id => $data ) {
            $row_id_arr = explode( "-", $row_id );
            if( isset( $row_id_arr[ 0 ] ) && 'new' == $row_id_arr[ 0 ] ) {
                $id = "orddd_location_" . $row_id_arr[ 1 ];
                $data[ 'row_id' ] = $id;
                $locations[ $id ]  = $data;
            } else if ( isset( $data[ 'deleted' ] ) ) {
                if ( isset( $data[ 'newRow' ] ) ) {
                    // So the user added and deleted a new row.
                    // That's fine, it's not in the database anyways. NEXT!
                    continue;
                }
                unset( $locations[ $row_id ] );
            } else {
                foreach( $data as $data_key => $data_value ) {
                    $locations[ $row_id ][ $data_key ] = $data_value;
                }
            }
        }

        update_option( 'orddd_locations', $locations );
        
        wp_send_json_success( array(
            'orddd_locations' => $locations,
        ) );
	}

	/**
	 * Add location option for adding custom settings. 
	 *
	 * @hook orddd_after_custom_product_categories
	 *
	 * @param string $option_key Custom settings key.
	 *
	 * @since 8.4
	 */
	public function orddd_after_custom_product_categories( $option_key ) {
        $location_enabled = '';
		$locations_stored = array();
		if ( ( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'shipping_based' ) && ( isset( $_GET[ 'mode' ] ) && $_GET[ 'mode' ] == 'edit' ) ) {
            if( isset( $_GET[ 'row_id' ] ) ) {
                $row_id = $_GET[ 'row_id' ];
                $shipping_methods_arr = get_option( 'orddd_shipping_based_settings_' . $row_id );
                
                if( isset( $shipping_methods_arr[ 'delivery_settings_based_on' ][ 0 ] ) && $shipping_methods_arr[ 'delivery_settings_based_on' ][ 0 ] == 'orddd_locations' ) {
                    $location_enabled = "checked";
                     if( isset( $shipping_methods_arr[ 'orddd_locations' ] ) ) {
	                    $locations_stored = $shipping_methods_arr[ 'orddd_locations' ];
	                } 
                }
            }
        }
           
        $pickup_locations = get_option( 'orddd_locations', true );
		?>
		<p>
            <label>
                <input type="radio" name="orddd_shipping_based_settings_<?php echo $option_key; ?>[delivery_settings_based_on][]" value="orddd_locations" id="orddd_delivery_settings_type" class="input-radio" <?php echo $location_enabled; ?>/><?php _e( 'Pickup Locations', 'order-delivery-date' );?>
            </label>
        </p>
        <div class="delivery_type_options delivery_type_orddd_locations">          
            <select class="orddd_shipping_methods" id="orddd_locations" name="orddd_shipping_based_settings_<?php echo $option_key; ?>[orddd_locations][]" multiple="multiple" placeholder="<?php _e( 'Choose Pickup Locations', 'order-delivery-date' );?>">
                <?php
                if( is_array( $pickup_locations ) && count( $pickup_locations ) > 0 ) {
	                foreach ( $pickup_locations as $key => $value ) {
	                    $address = self::orddd_get_formatted_address( $value, true );
	                    $location_id = '';
	                    if( isset( $value[ 'row_id' ] ) ) {
	                        $location_id = $value[ 'row_id' ];
	                    }

	                    if( in_array( esc_attr( $location_id ), $locations_stored ) ) {
	                        echo '<option value="' . esc_attr( $location_id ) . '" selected>';
	                    } else {
	                        echo '<option value="' . esc_attr( $location_id ) . '">';
	                    }
	                    echo $address;
	                    echo '</option>';
	                }
	            }
                ?>
            </select>
        </div>
        <?php
	}

    /**
     * Show selected Locations on Custom Delivery Settings tab. 
     *
     * @hook orddd_shipping_settings_table_data
     *
     * @array
     */
    public function orddd_shipping_settings_table_data( $shipping_settings ) {
        $shipping_method_str = '';
        $pickup_locations = get_option( 'orddd_locations', true );
        foreach( $shipping_settings as $key => $value ) {
            if( isset( $value->row_id ) ) { 
                $shipping_settings_arr = get_option( 'orddd_shipping_based_settings_' . $value->row_id );
                if ( isset( $shipping_settings_arr[ 'delivery_settings_based_on' ][ 0 ] ) && $shipping_settings_arr[ 'delivery_settings_based_on' ][ 0 ] == 'orddd_locations' ) {
                    $pickup_locations_stored = $shipping_settings_arr[ 'orddd_locations' ];
                    $shipping_method_str = "<b>Pickup Locations</b></br>";
                    if( is_array( $pickup_locations ) && count( $pickup_locations ) > 0 ) {
	                    foreach ( $pickup_locations as $pkey => $pvalue ) {
	                        $location_id = '';
	                        if( isset( $pvalue[ 'row_id' ] ) ) {
	                            $location_id = $pvalue[ 'row_id' ];
	                        }
	                        if( in_array( esc_attr( $location_id ), $pickup_locations_stored ) ) {
	                            $address = self::orddd_get_formatted_address( $pvalue, true );
	                            $shipping_method_str .= $address . '<br><br>';
	                        }
	                    }
	                }

                    if( isset( $shipping_settings_arr[ 'categories_for_pickup_locations' ] ) ) {
                        $shipping_method_str .= "<br><b>Product Categories</b><br>";
                        $categories_for_pickup = $shipping_settings_arr[ 'categories_for_pickup_locations' ];

                        $args = array(
                            'taxonomy'     => 'product_cat',
                            'hide_empty'   => 0
                        );
                        
                        $all_categories = get_categories( $args );
                        foreach( $categories_for_pickup as $pk => $pv ) {
                            foreach ( $all_categories as $id => $val ) {
                                if( $val->slug == $pv ) {
                                    $shipping_method_str .= $val->name. ', ';
                                }
                            }
                        }
                    }
                    $shipping_method_str = substr( $shipping_method_str, 0, -2 );
                    $shipping_settings[ $value->row_id ]->shipping_methods = $shipping_method_str;    
                }
            }        
        }
        return $shipping_settings;
    }

    /**
     * Pass custom delivery settings for locations. 
     *
     * @hook orddd_custom_delivery_settings
     *
     * @param array $shipping_settings_to_load All Custom delivery settings.  
     * @since 8.4
     */
    public function orddd_custom_delivery_settings( $shipping_settings, $shipping_settings_to_load, $custom_setting_id ) {
        $shipping_method_str = '';
    	if( isset( $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] ) && $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] == 'orddd_locations' ) {
            $categories_for_pickup_locations = self::orddd_get_categories_for_pickup_locations( $shipping_settings );

        	if( isset( $shipping_settings[ 'orddd_locations' ] ) ) {
				$shipping_methods = $shipping_settings[ 'orddd_locations' ];
                foreach( $shipping_methods as $skey => $sval ) {
                	$shipping_method_str .= $sval . ',';
                }
			}
			$shipping_method_str = substr( $shipping_method_str, 0, -1 );
			$shipping_settings_to_load[ 'orddd_locations' ]  = $shipping_method_str;
			$shipping_settings_to_load[ 'unique_settings_key' ] = 'custom_settings_' . $custom_setting_id;
            $shipping_settings_to_load[ 'categories_for_pickup_locations' ] = $categories_for_pickup_locations;
        }	
        return $shipping_settings_to_load;
    }
	/**
	 * Return the formatted delivery address
	 *
	 * @param array $address Delivery Address.
	 * @param bool $one_line Whether to display address in one line or not. 
	 * @since 8.4
	 */
	public static function orddd_get_formatted_address( $address, $one_line = false ) {
		// pass empty first_name/last_name otherwise we get a bunch of notices
		$formatted = WC()->countries->get_formatted_address( array_merge( array( 'first_name' => null, 'last_name' => null ), $address ) );
		if ( $one_line ) {
			$formatted = str_replace( array( '<br/>', '<br />', "\n" ), array( ', ', ', ', '' ), $formatted );
		} else {
			if ( isset( $address[ 'phone' ] ) && $address[ 'phone' ] ) {
				$formatted .= "<br/>\n" . $address[ 'phone' ];
			}
		}
		return $formatted;
	}

	/**
     * Location field on the checkout page. 
     *
     * @hook woocommerce_after_checkout_billing_form
     * @hook woocommerce_after_checkout_shipping_form
     * @hook woocommerce_before_order_notes
     * @hook woocommerce_after_order_notes
     * @hook woocommerce_cart_collaterals
     *
     * @param resource $checkout Checkout Page Object
     * @since 8.4
     */
    public function orddd_locations_after_checkout_billing_form( $checkout = "" ) {
        global $current_user;
        
        $result = array();
        $roles = array();
        if ( has_filter( 'orddd_disable_delivery_for_user_roles' ) ) {
            $roles = apply_filters( 'orddd_disable_delivery_for_user_roles', $roles );
        }
        
        $current_user_role = '';
        if( isset( $current_user->roles[0] ) ) {
            $current_user_role = $current_user->roles[0];
        }

        $is_delivery_enabled = orddd_common::orddd_is_delivery_enabled();
        if ( get_option( 'orddd_enable_delivery_date' ) == 'on' && $is_delivery_enabled == 'yes' && ( orddd_process::woo_product_has_delivery() === 'on' ) && !in_array( $current_user_role, $roles ) ) {
            $locations = get_option( 'orddd_locations', true );
            if( is_array( $locations ) && count( $locations ) > 0 ) {
            	$result[ 'select_location' ] = __( 'Select Location', 'order-delivery-date' );
                foreach ( $locations as $key => $value ) {
                    $address = orddd_locations::orddd_get_formatted_address( $value, true );
                    $location_id = '';
                    if( isset( $value[ 'row_id' ] ) ) {
                        $location_id = $value[ 'row_id' ];
                    }
                    $result[ $location_id ] = __( $address, "order-delivery-date" );
                }
                
                $validate_location_field = false;
                if ( get_option( 'orddd_pickup_location_mandatory' ) == 'on' ) {
                    $validate_location_field = true;
                }
               
                $locations_label = '' != get_option( 'orddd_location_field_label' ) ? get_option( 'orddd_location_field_label' ) : 'Pickup Location';
                if ( is_object( $checkout ) ) {
                    woocommerce_form_field( 'orddd_locations', array(
                                                'type'              => 'select',
                                                'label'             => __( $locations_label, 'order-delivery-date' ),
                                                'required'          => $validate_location_field,
                                                'options'           => $result,
                                                'class'             => array( 'form-row-wide' )
                    ),
                    $checkout->get_value( 'orddd_locations' ) );
                } else {
                    woocommerce_form_field( 'orddd_locations', array(
                                                'type'              => 'select',
                                                'label'             => __( $locations_label, 'order-delivery-date' ),
                                                'required'          => $validate_location_field,
                                                'options'           => $result,
                                                'class'             => array( 'form-row-wide' ),
                                                'custom_attributes' => array( 'style'=>'max-width:300px;' )
                    ) );
                }
            }
        }
    }
    
    /**
     * Validate the Pickup Location mandatory field
     * 
     * @hook woocommerce_after_checkout_validation
     * @since 8.7
     */
    
    public function orddd_validate_pickup_locations( $data, $errors ) {
    	global $current_user;
        $roles = array();
        if ( has_filter( 'orddd_disable_delivery_for_user_roles' ) ) {
            $roles = apply_filters( 'orddd_disable_delivery_for_user_roles', $roles );
        }
        
        $current_user_role = '';
        if( isset( $current_user->roles[0] ) ) {
            $current_user_role = $current_user->roles[0];
        }

    	$is_delivery_enabled = orddd_common::orddd_is_delivery_enabled();
    	$orddd_is_delivery_calendar_enabled_for_custom = orddd_common::orddd_is_delivery_calendar_enabled_for_custom_delivery();

    	if( $is_delivery_enabled == 'yes' && ( orddd_process::woo_product_has_delivery() === 'on' ) && !in_array( $current_user_role, $roles ) && ( 'delivery_calendar' == get_option( 'orddd_delivery_checkout_options' ) || 'yes' == $orddd_is_delivery_calendar_enabled_for_custom ) ) {
	        $locations = get_option( 'orddd_locations', true );
	        if( get_option( 'orddd_pickup_location_mandatory' ) == 'on' && is_array( $locations ) && count( $locations ) > 0 ) {
	            if( ( !isset( $_POST[ 'orddd_locations' ] ) || ( isset( $_POST[ 'orddd_locations' ] ) && ( $_POST[ 'orddd_locations' ] == '' || $_POST[ 'orddd_locations' ] == 'select_location' ) ) ) && isset( $_POST[ 'shipping_method' ][0] ) && strpos( $_POST[ 'shipping_method' ][0], 'local_pickup' ) !== false ) {
	                $message = '<strong>' . get_option( 'orddd_location_field_label' ) . '</strong>' . ' ' . __( 'is a required field.', 'order-delivery-date' );
	                $errors->add(
                        'required-field',
                        $message,
                        array(
                            'id' => 'orddd_locations'
                        )
                    );
	            } 
	        }
	    }
    }

    /**
	 * Return the selected product categories for the pickup location custom delivery settings added in the admin
	 *
	 * @param array $shipping_settings Custom settings added
	 * @return string Selected product category for the Custom delivery settings
	 * @since 9.25.0
	 */
	public static function orddd_get_categories_for_pickup_locations( $shipping_settings ) {
		$categories_for_pickup_locations = array();
		$categories_for_pickup_locations_str = ''; 
		if( isset( $shipping_settings[ 'categories_for_pickup_locations' ] ) ) {
            $categories_for_pickup_locations = $shipping_settings[ 'categories_for_pickup_locations' ];
            foreach( $categories_for_pickup_locations as $key => $value ) {
                $categories_for_pickup_locations_str .= $value . ',';
            }
            $categories_for_pickup_locations_str = substr( $categories_for_pickup_locations_str, 0, -1 );
        }
        return $categories_for_pickup_locations_str;
	}
}

$orddd_locations = new orddd_locations();
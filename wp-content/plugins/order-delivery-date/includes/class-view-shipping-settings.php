<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Display added Custom Delivery Settings in admin.
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Pro-for-WooCommerce/Admin/Settings/Custom-Delivery
 * @since 3.0
 */

class ORDDD_View_Shipping_Settings_Table extends WP_List_Table {

	/**
	 * URL of this page
	 *
	 * @var string
	 * @since 1.4.1
	 */
	public $base_url;
	
	/**
	 * Get things started
	 *
	 * @see WP_List_Table::__construct()
	 */
	public function __construct() {

		global $status, $page;
		// Set parent defaults
		parent::__construct( array(
    		    'singular' => __( 'custom_delivery_setting', 'order-delivery-date' ), //singular name of the listed records
	       	    'plural'   => __( 'custom_delivery_settings', 'order-delivery-date' ), //plural name of the listed records
				'ajax'      => false             			// Does this table support ajax?
		) );
		$this->process_bulk_action();
		$this->base_url = admin_url( 'admin.php?page=order_delivery_date&action=shipping_based' );
	}
	

	/**
	 * Add delete option in the bulk actions dropdown
	 * 
	 * @since 3.0
	 */
	public function get_bulk_actions() {
	    return array(
	        'orddd_delete' => __( 'Delete', 'order-delivery-date' )
	    );
	}
	
	/**
	 * Add the check box for the items to select 
	 * 
	 * @param string $item 
	 * @return string
	 * @since 3.0
	 **/
	function column_cb( $item ){
	    $row_id = '';
	    if( isset( $item->row_id ) && "" != $item->row_id ){
	        $row_id = $item->row_id;
	        return sprintf(
	            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
	            'custom_delivery_setting',
	            $row_id
	        );
	    }
	}
	
	/**
	 * Prepare items to display in the table
	 * 
	 * @since 3.0
	 */
	public function orddd_prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = array(); // No hidden columns
		$data     = $this->orddd_shipping_settings_data();
		$sortable = array();
		$status   = isset( $_GET['status'] ) ? $_GET['status'] : 'any';
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items = $data;
	}
	
	/**
	 * Return columns to add in the table
	 * 
	 * @return array $columns Columns to be displayed in the table
	 * @since 3.0
	 */
	public function get_columns() {
		$columns = array(
		        'cb'                 =>  '<input type="checkbox" />',
				'status'     		 => __( 'Status', 'order-delivery-date' ),
				'shipping_methods'   => __( 'Settings based on', 'order-delivery-date' ),
				'shipping_days'      => __( 'Delivery Settings', 'order-delivery-date' ),
		        'time_settings'      => __( 'Time Settings', 'order-delivery-date' ),
		        'same_day_settings'  => __( 'Same day Settings', 'order-delivery-date' ),
				'next_day_settings'  => __( 'Next day Settings', 'order-delivery-date' ),
				'time_slot_settings' => __( 'Time Slots and Maximum Order Deliveries per time slot', 'order-delivery-date' ),
		        'holidays'           => __( 'Holidays', 'order-delivery-date' ),
				'actions'  		     => __( 'Actions', 'order-delivery-date' )
		);
		return apply_filters( 'orddd_shipping_settings_table_columns', $columns );
	}
	
	/**
	 * Displays the data in the table
	 * 
     * @todo Check if we can use orddd_get_shipping_methods function in orddd-shipping-based-settings.php function 
     * to fetch all the shipping methods for the zones or global shipping methods. 
     *
	 * @return array $return_shipping_settings Settings for a shipping method
	 * @since 3.0
	 */
	
	public function orddd_shipping_settings_data() { 
		global $wpdb, $woocommerce, $orddd_weekdays;	
		
        $return_shipping_settings = array();
        $shipping_classes         = array();
        $shipping_settings        = array();
        $results                  = array();

		$currency_symbol          = get_woocommerce_currency_symbol();
        $results                  = orddd_common::orddd_get_shipping_settings();
        
		$i = 1;
		$shipping_based_count = 0;
		$express_id = '';
        $priority_id = '';

		// Default WooCommerce Shipping zones from verison 2.6
		$shipping_default_zones = array();
		if ( defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, "2.6", '>=' ) > 0 ) {
    		if( class_exists( 'WC_Shipping_Zones' ) ) {
    		    
                $shipping_zone_class = new WC_Shipping_Zones();
    		    
                $shipping_zones = array();
    		    if( method_exists ( $shipping_zone_class , 'get_zones' ) ) {
    		        $shipping_zones = $shipping_zone_class->get_zones();
    		    }

    		    foreach( $shipping_zones as $shipping_default_key => $shipping_default_value ) {
    		        if( isset ( $shipping_default_value[ 'shipping_methods' ] ) ) {
    		            foreach( $shipping_default_value[ 'shipping_methods' ] as $key => $value ) {
    		                if( 'table_rate' == $value->id ) {
                                // Custom delivery settings for WooCommerce Table Rate Shipping plugin by WooCommerce.
    		                    $table_rate_shipping_classes = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix . "woocommerce_shipping_table_rates` WHERE shipping_method_id = {$value->instance_id} ORDER BY rate_order ASC;" );
    		                    foreach( $table_rate_shipping_classes as $tkey => $tvalue ) {
    		                        $option_settings = get_option( "woocommerce_table_rate_" . $value->instance_id . "_settings" );
    		                        if( $option_settings[ 'calculation_type' ] == '' ) {
    		                            $title = $shipping_default_value[ 'zone_name' ] . " -> " . $value->title . " -> " . $tvalue->rate_label;
    		                            $id = $value->id . ":" . $value->instance_id . ":" . $tvalue->rate_id;
    		                        } else {
    		                            $title = $shipping_default_value[ 'zone_name' ] . " -> " . $value->title;
    		                            $id = $value->id . ":" . $value->instance_id;
    		                        }
    		                        $shipping_default_zones[] = array( "shipping_default_zone_title" => $title,
    		                            "shipping_default_zone_id" => $id
    		                        );
    		                    }
    		                } elseif ( 'usps' == $value->id ) {
                                // Custom Delivery Settings for WooCommerce USPS Shipping plugin by Automattic.
    		                	$usps_settings = $value ->instance_settings;
								$usps_title = $shipping_default_value[ 'zone_name' ] . " -> " . $value->title . " -> ";
                                $express_title = $usps_title . 'Priority Mail Express Flat Rate®';
                                $priority_title = $usps_title . 'Priority Mail Flat Rate®';
                                if ( 'yes' == $usps_settings[ 'enable_flat_rate_boxes' ] ) {
                                    $express_id = $shipping_default_value[ 'zone_id' ] . ':' . 'usps' . ":" . 'flat_rate_box_express';
                                    if ( isset( $usps_settings[ 'flat_rate_express_title' ] ) && '' != $usps_settings[ 'flat_rate_express_title' ] ) {
                                    	$express_title = $usps_title . $usps_settings[ 'flat_rate_express_title' ];
                                    }

                                    $priority_id = $shipping_default_value[ 'zone_id' ] . ':' . 'usps' . ":" . 'flat_rate_box_priority';
                                    if ( isset( $usps_settings[ 'flat_rate_priority_title' ] ) && '' != $usps_settings[ 'flat_rate_priority_title' ] ) {
                                    	$priority_title = $usps_title . $usps_settings[ 'flat_rate_priority_title' ];
                                    }               
                                } else if ( 'priority' == $usps_settings[ 'enable_flat_rate_boxes'] ) {
                                	$priority_id = $shipping_default_value[ 'zone_id' ] . ':' . 'usps' . ":" . 'flat_rate_box_priority';
                                    if ( isset( $usps_settings[ 'flat_rate_priority_title' ] ) && '' != $usps_settings[ 'flat_rate_priority_title' ] ) {
                                        $priority_title = $usps_title . $usps_settings[ 'flat_rate_priority_title' ];
                                    }
                                } else if ( 'express' == $usps_settings[ 'enable_flat_rate_boxes'] ) {
                                	$express_id = $shipping_default_value[ 'zone_id' ] . ':' . 'usps' . ":" . 'flat_rate_box_express';
                                    if ( isset( $usps_settings[ 'flat_rate_express_title' ] ) && '' != $usps_settings[ 'flat_rate_express_title' ] ) {
                                        $express_title = $usps_title . $usps_settings[ 'flat_rate_express_title' ];
                                    }

                                } 
                                if( '' != $express_id ) {
                                	$shipping_default_zones[] = array( "shipping_default_zone_title" => $express_title,
                                        "shipping_default_zone_id" => $express_id
                                    );	
                                }
                                
                                if( '' != $priority_id ) {
                                	$shipping_default_zones[] = array( "shipping_default_zone_title" => $priority_title,
                                        "shipping_default_zone_id" => $priority_id
                                    );   
                                }

                                if ( 'yes' == $usps_settings[ 'enable_standard_services' ] ) {
                                    $usps_services = $usps_settings[ 'services' ];
                                    foreach( $usps_services as $usps_skey => $usps_svalue ) {
                                    	$usps_service_name = $usps_svalue[ 'name'];
                                        if ( '' == $usps_service_name ) {
                                            $usps_service_name = orddd_common::orddd_get_shipping_service_name( $usps_skey );
                                        }
                                        $id = $shipping_default_value[ 'zone_id' ] . ':' . 'usps' . ':' . $usps_skey;
                                        $shipping_default_zones[] = array( "shipping_default_zone_title" => $usps_title . $usps_service_name,
                                            "shipping_default_zone_id" => $id
                                        );
                                    }
                                }
    		                } else if( 'fedex' == $value->id && is_plugin_active('woocommerce-shipping-fedex/woocommerce-shipping-fedex.php') ) {
                                // Custom delivery settings for WooCommerce Fedex Shipping plugin by WooCommerce

                                $fedex_instance = $value->instance_id;
                                $fedex_settings = $value->instance_settings;
                                $fedex_title = $shipping_default_value[ 'zone_name' ] . " -> " . $value->title . " -> ";
                                
                                $fedex_services = $fedex_settings[ 'services' ];
                                if( is_array( $fedex_services ) && count( $fedex_services ) > 0 ) {                               
                                    foreach ( $fedex_services as $fedex_services_key => $fedex_services_value ) {
                                        $fedex_services_enabled = $fedex_services_value[ 'enabled'];
                                        if( '1' == $fedex_services_enabled ) {
                                            $fedex_services_name = $fedex_services_value[ 'name'];
                                            if( '' == $fedex_services_name ) {
                                                $fedex_services_name = orddd_common::orddd_get_fedex_service_name( $fedex_services_key );
                                            }
                                            $fedex_id = 'fedex' . ':' . $fedex_instance .':' . $fedex_services_key;

                                            $shipping_default_zones[] = array( 
                                                "shipping_default_zone_title" => $fedex_title . $fedex_services_name,
                                                "shipping_default_zone_id" => $fedex_id,
                                            );
                                        }
                                    }
                                }
                            } else if( 'fedex' == $value->id ) {
                                // Custom delivery settings for WooCommerce FedEx Shipping Plugin with Print Label by Xadapter

                                $fedex_instance = $value->instance_id;
                                $fedex_settings = $value->instance_settings;
                                $fedex_title = $shipping_default_value[ 'zone_name' ] . " -> " . $value->title . " -> ";
                                
                                $fedex_services = $fedex_settings[ 'services' ];
                                if( is_array( $fedex_services ) && count( $fedex_services ) > 0 ) {                               
                                    foreach ( $fedex_services as $fedex_services_key => $fedex_services_value ) {
                                        $fedex_services_enabled = $fedex_services_value[ 'enabled'];
                                        if( '1' == $fedex_services_enabled ) {
                                            $fedex_services_name = $fedex_services_value[ 'name'];
                                            if( '' == $fedex_services_name ) {
                                                $fedex_services_name = orddd_common::orddd_get_fedex_service_name( $fedex_services_key );
                                            }
                                            $fedex_id = $shipping_default_value[ 'zone_id' ] . ':' . 'fedex' . ':' . $fedex_services_key;
                                            $shipping_default_zones[] = array( "shipping_default_zone_title" => $fedex_title . $fedex_services_name,
                                                "shipping_default_zone_id" => $fedex_id
                                            );
                                        }
                                    }
                                }
                            } else if( 'flexible_shipping' == $value->id ) {
                                // Custom Delivery Settings for Flexible Shipping plugin by WP Desk.
                                $flexible_methods = get_option( $value->shipping_methods_option );
                                
                                foreach ( $flexible_methods as $flexible_methods_key => $flexible_methods_value ) {
                                    $flexible_title = $shipping_default_value[ 'zone_name' ] . " -> " . $value->title . " -> ";
                                    if( 'yes' == $flexible_methods_value[ 'method_enabled' ] ) {

                                        $flexible_title .= $flexible_methods_value[ 'method_title' ];
                                        
                                        $shipping_default_zones[] = array( "shipping_default_zone_title" => $flexible_title,
                                        "shipping_default_zone_id" => $flexible_methods_value[ 'id_for_shipping' ]
                                        );    
                                    }
                                }
                            } else if( 'flexible_shipping_ups' == $value->id ) {
                                //Custom Delivery Setting for WooCommerce UPS Shipping – Live Rates and Access Points plugin by WP Desk.  
                                $flexible_methods = get_option( 'woocommerce_flexible_shipping_ups_' . $value->instance_id . '_settings' );
                                if( isset( $flexible_methods[ 'custom_services' ] ) && $flexible_methods[ 'custom_services' ] == 'yes' ) {
                                    $flexible_shipping_services = $flexible_methods[ 'services' ];
                                    $flexible_title = $shipping_default_value[ 'zone_name' ] . " -> " . $value->title . " -> ";
                                    foreach ( $flexible_shipping_services as $service_key => $service_value ) {
                                        if( isset( $service_value[ 'enabled' ] ) &&  $service_value[ 'enabled' ] == true ) {
                                            $flexible_title .= $service_value[ 'name' ];
                                            $shipping_default_zones[] = array( 
                                                "shipping_default_zone_title" => $flexible_title,
                                                "shipping_default_zone_id" => $value->id . ":" . $value->instance_id . ":" . $service_key
                                            );   
                                        }
                                    }
                                } 

                                if( isset( $flexible_methods[ 'fallback' ] ) && $flexible_methods[ 'fallback' ] == 'yes' ) {
                                    $flexible_title = $shipping_default_value[ 'zone_name' ] . " -> " . $value->title;
                                    $id = $value->id . ":" . $value->instance_id . ":fallback";
                                    $shipping_default_zones[] = array( "shipping_default_zone_title" => $flexible_title,
                                        "shipping_default_zone_id" => $id
                                    );
                                }
                            } else if( 'ups' == $value->id ) {
                                //Custom Delivery Settings for WooCommerce UPS Shipping by WooCommerce
                                $ups_instance = $value->instance_id;
                                $ups_settings = $value->instance_settings;
                                $ups_title = $shipping_default_value[ 'zone_name' ] . " -> " . $value->title . " -> ";

                                $ups_services = $ups_settings[ 'services' ];
                                foreach ( $ups_services as $ups_services_key => $ups_services_value ) {
                                    $ups_services_enabled = $ups_services_value[ 'enabled' ];
                                    if( '1' == $ups_services_enabled ) {
                                        $ups_services_name = $ups_services_value[ 'name' ];
                                        if( '' == $ups_services_name ) {
                                            $ups_services_name = orddd_common::orddd_get_ups_service_name( $ups_services_key );
                                        }
                                        $ups_id = 'ups' . ':' . $ups_instance . ':' . $ups_services_key;
                                        $shipping_default_zones[] = array( "shipping_default_zone_title" => $ups_title . $ups_services_name,
                                            "shipping_default_zone_id" => $ups_id
                                        );
                                    }
                                }
                            } else if( 'betrs_shipping' == $value->id ) {
                                //Custom Delivery Settings for Table rate shipping plugin by Bolder elements.
                                $betrs_instance = $value->instance_id;
                                $betrs_options_save_name = $value->id . '_options-' . $betrs_instance;
                                $betrs_shipping_options = get_option( $betrs_options_save_name );
                                
                                $betrs_settings = $betrs_shipping_options[ 'settings' ];
                                
                                foreach ( $betrs_settings as $betrs_settings_key => $betrs_settings_value ) {
                                    $betrs_title = $shipping_default_value[ 'zone_name' ] . " -> " . $value->title . " -> ";
                                    $betrs_title .= $betrs_settings_value[ 'title' ];
                                    if ( '' == $betrs_title ) {
                                        $betrs_title .= "Table Rate";
                                    }

                                    $betrs_id = $value->id . ':' . $betrs_instance . '-' . $betrs_settings_value[ 'option_id' ];
                                    $shipping_default_zones[] = array( "shipping_default_zone_title" => $betrs_title, "shipping_default_zone_id" => $betrs_id );
                                }
                            } else if( 'wbs' == $value->id && is_plugin_active( 'weight-based-shipping-for-woocommerce/plugin.php' ) ) {
                                // Custom Delivery Settings for Weight based Shippng plugin by weightbasedshipping.com
                                $wbs_settings = get_option( 'wbs_' . $value->instance_id . '_config' );
                                if( isset( $wbs_settings[ 'enabled' ] ) && $wbs_settings[ 'enabled' ] == 1 ) {
                                    foreach( $wbs_settings[ 'rules' ] as $rk => $rv ) {
                                        if( isset( $rv[ 'meta' ][ 'enabled' ] ) && $rv[ 'meta' ][ 'enabled' ] == 1 ) {
                                            $package_title = $rv[ 'meta' ][ 'title' ];
                                            $package_id = orddd_shipping_based_settings::orddd_get_shipping_package_id( $package_title );

                                            $title = $shipping_default_value[ 'zone_name' ] . " -> " . $value->title . " -> " . $package_title;
                                            $id = $value->id . ":" . $value->instance_id . ":" . $package_id;

                                            $shipping_default_zones[] = array( "shipping_default_zone_title" => $title,
                                                "shipping_default_zone_id" => $id
                                            );
                                        }
                                    }
                                }
                            }elseif ( 'canada_post' == $value->id ) {
								$settings = $value->custom_services;
								foreach( $settings as $service => $options ) {
									if( $options['enabled'] ) {
										$title = $shipping_default_value['zone_name'] . ' -> ' . $value->title . ' -> ' . $service;
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
                                //WooCommerce Default Shipping methods for shipping zones.
        		                $title = $shipping_default_value[ 'zone_name' ] . " -> " . $value->title;
        		                $id = $value->id . ":" . $value->instance_id;
        		                $shipping_default_zones[] = array( "shipping_default_zone_title" => $title,
        		                    "shipping_default_zone_id" => $id
        		                );
    		                }
    		            }
    		        }
    		    }
    		    
    		    $query = "SELECT instance_id, method_id FROM `" . $wpdb->prefix . "woocommerce_shipping_zone_methods` WHERE zone_id = 0";
    		    $results_default = $wpdb->get_results( $query );
    		    foreach( $results_default as $result_key => $result_value ) {
    		        $wc_shipping         = WC_Shipping::instance();
    		        $allowed_classes     = $wc_shipping->get_shipping_method_class_names();
    		        if ( ! empty( $results ) && in_array( $result_value->method_id, array_keys( $allowed_classes ) ) ) {
    		            if( isset( $allowed_classes[ $result_value->method_id ] ) ) {
    		                $class_name = $allowed_classes[ $result_value->method_id ];
    		                if ( is_object( $class_name ) ) {
    		                    $class_name = get_class( $class_name );
    		                }
    		                $default_shipping_method = new $class_name( $result_value->instance_id );
    		                if( $default_shipping_method != "" ) {
    		                    $title = "Rest of the World" . " -> " . $default_shipping_method->title;
    		                    $id = $default_shipping_method->id . ":" . $result_value->instance_id;
    		                    $shipping_default_zones[] = array( "shipping_default_zone_title" => $title,
    		                        "shipping_default_zone_id" => $id
    		                    );
                                if( 'fedex' == $default_shipping_method->id ) {
                                    // Rest of the World shipping zone with WooCommerce Fedex Shipping plugin by WooCommerce.
                                    $fedex_settings = get_option( 'woocommerce_fedex_' . $result_value->instance_id . '_settings' );
                                    $fedex_services = $fedex_settings[ 'services' ];     
                                    if( is_array( $fedex_services ) && count( $fedex_services ) > 0 ) {                     
                                        foreach ( $fedex_services as $fedex_services_key => $fedex_services_value ) {
                                            $fedex_services_enabled = $fedex_services_value[ 'enabled'];
                                            if( '1' == $fedex_services_enabled ) {
                                                $fedex_services_name = $fedex_services_value[ 'name'];
                                                if( '' == $fedex_services_name ) {
                                                    $fedex_services_name = orddd_common::orddd_get_fedex_service_name( $fedex_services_key );
                                                }
                                                $title = "Rest of the World" . " -> " . $default_shipping_method->title . " -> " . $fedex_services_name;
                                                $id = 0 . ':' . 'fedex' . ":" . $fedex_services_key;
                                                $shipping_default_zones[] = array( "shipping_default_zone_title" => $title,
                                                    "shipping_default_zone_id" => $id
                                                );
                                            }
                                        }
                                    }
                                }

                                if( 'table_rate' == $default_shipping_method->id ) {
                                    // Default Shipping methods for Rest of the World Shipping zone.
                                    $table_rate_shipping_classes = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix . "woocommerce_shipping_table_rates` WHERE shipping_method_id = {$default_shipping_method->instance_id} ORDER BY rate_order ASC;" );
                                    foreach( $table_rate_shipping_classes as $tkey => $tvalue ) {
                                        $option_settings = get_option( "woocommerce_table_rate_" . $default_shipping_method->instance_id . "_settings" );
                                        
                                        if( '' == $option_settings[ 'calculation_type' ] ) {
                                            $title = "Rest of the World" . " -> " . $default_shipping_method->title . " -> " . $tvalue->rate_label;
                                            $id = $default_shipping_method->id . ":" . $default_shipping_method->instance_id . ":" . $tvalue->rate_id;
                                        } else {
                                            $title = "Rest of the World" . " -> " . $default_shipping_method->title;
                                            $id = $default_shipping_method->id . ":" . $default_shipping_method->instance_id;
                                        }
                                        $shipping_default_zones[] = array( "shipping_default_zone_title" => $title,
                                            "shipping_default_zone_id" => $id
                                        );
                                    }
                                }

                                if( 'flexible_shipping' == $default_shipping_method->id ) {
                                    // Rest of the World shipping zone with Flexible Shipping plugin by WP Desk
                                    $flexible_methods = get_option( $default_shipping_method->shipping_methods_option );
                                    
                                    foreach ( $flexible_methods as $flexible_methods_key => $flexible_methods_value ) {
                                        $flexible_title = "Rest of the World" . " -> " . $default_shipping_method->title . " -> ";
                                        if( 'yes' == $flexible_methods_value[ 'method_enabled' ] ) {

                                            $flexible_title .= $flexible_methods_value[ 'method_title' ];
                                            
                                            $shipping_default_zones[] = array( "shipping_default_zone_title" => $flexible_title,
                                            "shipping_default_zone_id" => $flexible_methods_value[ 'id_for_shipping' ]
                                            );    
                                        }
                                    }
                                }

                                if( 'flexible_shipping_ups' == $default_shipping_method->id ) {
                                    // Rest of the World shipping zone with WooCommerce UPS Shipping – Live Rates and Access Points plugin by WP Desk
                                    $flexible_methods = get_option( 'woocommerce_flexible_shipping_ups_' . $result_value->instance_id . '_settings' );
                                    if( isset( $flexible_methods[ 'custom_services' ] ) && $flexible_methods[ 'custom_services' ] == 'yes' ) {
                                        $flexible_shipping_services = $flexible_methods[ 'services' ];
                                        $flexible_title = "Rest of the World" . " -> " . $value->title . " -> ";
                                        foreach ( $flexible_shipping_services as $service_key => $service_value ) {
                                            if( isset( $service_value[ 'enabled' ] ) &&  $service_value[ 'enabled' ] == true ) {
                                                $flexible_title .= $service_value[ 'name' ];
                                                $shipping_default_zones[] = array( 
                                                    "shipping_default_zone_title" => $flexible_title,
                                                    "shipping_default_zone_id" => $value->id . ":" . $result_value->instance_id . ":" . $service_key
                                                );   
                                            }
                                        }
                                    } 

                                    if( isset( $default_shipping_method->instance_settings[ 'fallback' ] ) && $default_shipping_method->instance_settings[ 'fallback' ] == 'yes' ) {
                                        $title = "Rest of the World" . " -> " . $default_shipping_method->title;
                                        $id = $default_shipping_method->id . ":" . $result_value->instance_id . ":fallback";
                                        $shipping_default_zones[] = array( "shipping_default_zone_title" => $title,
                                            "shipping_default_zone_id" => $id
                                        );
                                    }
                                }  

                                if( 'wbs' == $default_shipping_method->id && is_plugin_active( 'weight-based-shipping-for-woocommerce/plugin.php' ) ) {
                                    // Rest of the World shipping zone with  Weight based Shippng plugin by weightbasedshipping.com
                                    $wbs_settings = get_option( 'wbs_' . $result_value->instance_id . '_config' );
                                    if( isset( $wbs_settings[ 'enabled' ] ) && $wbs_settings[ 'enabled' ] == 1 ) {
                                        foreach( $wbs_settings[ 'rules' ] as $rk => $rv ) {
                                            if( isset( $rv[ 'meta' ][ 'enabled' ] ) && $rv[ 'meta' ][ 'enabled' ] == 1 ) {
                                                $package_title = $rv[ 'meta' ][ 'title' ];
                                                $package_id = orddd_shipping_based_settings::orddd_get_shipping_package_id( $package_title );
                                                
                                                $title = "Rest of the World" . " -> " . $value->title . " -> " . $package_title;
                                                $id = $default_shipping_method->id . ":" . $result_value->instance_id . ":" . $package_id;
                                                
                                                $shipping_default_zones[] = array( "shipping_default_zone_title" => $title,
                                                    "shipping_default_zone_id" => $id
                                                );
                                            }
                                        }
                                    }
                                }elseif ( 'canada_post' == $default_shipping_method->id ) {

									$settings = $result_value->custom_services;
									foreach( $settings as $service => $options ) {
										if( $options['enabled'] ) {
											$title = "Rest of the World" . ' -> ' . $result_value->title . ' -> ' . $service;
												$id                       = $default_shipping_method->id . ':' . $service;
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
		
        //Fetch the shipping packages from Weight Based Shipping for WooCommerce plugin
        if( is_plugin_active( 'weight-based-shipping-for-woocommerce/plugin.php' ) ) {
            $wbs_settings = get_option( 'wbs_config' );
            if( isset( $wbs_settings[ 'enabled' ] ) && $wbs_settings[ 'enabled' ] == 1 ) {
                foreach( $wbs_settings[ 'rules' ] as $rk => $rv ) {
                    if( isset( $rv[ 'meta' ][ 'enabled' ] ) && $rv[ 'meta' ][ 'enabled' ] == 1 ) {
                        $package_title =  $rv[ 'meta' ][ 'title' ];
                        $package_id = orddd_shipping_based_settings::orddd_get_shipping_package_id( $package_title );

                        $title = "Weight Based Shipping -> " . $package_title ;
                        $id = "wbs:" . $package_id;
                        
                        $shipping_default_zones[] = array( "shipping_default_zone_title" => $title,
                            "shipping_default_zone_id" => $id
                        );
                    }
                }
            }
		}
		
		// WooCommerce Tree Table Rate Shipping compatibility.
		if( is_plugin_active( 'wc-tree-table-rate-shipping/wc-tree-table-rate-shipping.php' ) ) {
			$shipping_default_zones[] = ORDDD_Tree_Table_Rate::orddd_get_tree_table_shipping_zones( '', '', true );
		}
        
        //Fetch the shipping methods from FedEx plugin from X-Adapter
        $wf_fedex_settings = get_option( 'woocommerce_wf_fedex_woocommerce_shipping_settings' );
        $fedex_services = array();
        if( $wf_fedex_settings != '' && $wf_fedex_settings != '[]' && $wf_fedex_settings != "{}" && $wf_fedex_settings != null ) {
            $fedex_services = $wf_fedex_settings[ 'services' ];                                       
        }
        foreach ( $fedex_services as $fedex_services_key => $fedex_services_value ) {
            $fedex_services_enabled = $fedex_services_value[ 'enabled'];
            if( '1' == $fedex_services_enabled ) {
                $fedex_services_name = $fedex_services_value[ 'name'];
                if( '' == $fedex_services_name ) {
                    $fedex_services_name = orddd_common::orddd_get_fedex_service_name( $fedex_services_key );
                }
                $title = $fedex_services_name;
                $id = 'wf_fedex_woocommerce_shipping:' . $fedex_services_key;
                $shipping_default_zones[] = array( "shipping_default_zone_title" => $title,
                    "shipping_default_zone_id" => $id
                );
            }
        }

		$table_exists = $wpdb->get_results( "SHOW TABLES LIKE '" . $wpdb->prefix . "woocommerce_shipping_zone_shipping_methods'" );
		if( is_array( $table_exists ) && count( $table_exists ) > 0 ) {
    		$shipping_zone_methods = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix . "woocommerce_shipping_zone_shipping_methods`" );
    		foreach( $shipping_zone_methods as $shipping_key => $shipping_value ) {
    		    $option_settings = get_option( "woocommerce_table_rate-" . $shipping_value->shipping_method_id . "_settings" );
    		    if( $option_settings[ 'calculation_type' ] == '' ) {
                    $shipping_zone_classes = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `" . $wpdb->prefix . "woocommerce_shipping_table_rates` WHERE shipping_method_id = %d ORDER BY rate_order ASC",  $shipping_value->shipping_method_id ) );
                    foreach( $shipping_zone_classes as $key => $value ) {
                        $shipping_classes[] = array( "shipping_method_title" => $option_settings[ 'title' ],
                            "shipping_method_id" => $shipping_value->shipping_method_id,
                            "rate_label" => $value->rate_label,
                            "rate_id" => $value->rate_id );
                    }
    		    } else {
    		        $shipping_classes[] = array( "shipping_method_title" => $option_settings[ 'title' ],
    		            "shipping_method_id" => $shipping_value->shipping_method_id,
    		            "rate_label" => '',
    		            "rate_id" => '' );
    		    }
    		}
		}
		
 		// Returns the other shipping methods for the default zones in WooCommerce. 
        // For example, shipping packages from the Advance Shipping packages for WooCommerce plugin.
        // 2 parameter in the apply_filter function the page on which this hook is used. So here view_settings passes.
		$shipping_default_zones = apply_filters( 'orddd_custom_setting_shipping_methods', $shipping_default_zones, 'view_settings' );
		
		foreach ( $results as $key => $value ) {
		    $shipping_settings = get_option( $value->option_name );
		    $row_id = substr( $value->option_name, strrpos( $value->option_name, "_" ) + 1 );
		    $return_shipping_settings[ $row_id ] = new stdClass();
		    $shipping_method_str = $shipping_days = '';
		    if( isset( $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] ) && $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] == 'shipping_methods' ) {
		        $shipping_methods = isset( $shipping_settings[ 'shipping_methods' ] ) ? $shipping_settings[ 'shipping_methods' ] : array();
		        $shipping_method_str = "<b>" . __( 'Shipping methods', 'order-delivery-date' ) . "</b></br>";
		        $active_shipping_methods = $woocommerce->shipping->load_shipping_methods();
		        $args = array(
		            'hide_empty'   => 0
		        );
		        
		        $default_shipping_classes = get_terms( 'product_shipping_class', $args );
		        foreach( $shipping_methods as $sk => $sv ) {
		            $shipping_class = 'no';
		            foreach ( $active_shipping_methods as $id => $active_shipping_method ) {
		                if( $sv == $id ) {
    		                if ( defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, "2.6", '>=' ) > 0 ) {
    		                    if ( isset( $active_shipping_method->id ) && false !== strstr( $active_shipping_method->id, "legacy" ) ) {
    		                        $title = $active_shipping_method->get_method_title() . " (Legacy)";
    		                        $shipping_method_str .= $title . ", ";
    		                    } else {
                                    $shipping_method_str .= $active_shipping_method->get_method_title() . ', ';
                                }
    		                } else {
    		                    $shipping_method_str .= $active_shipping_method->get_method_title() . ', ';
    		                } 
		                } else {
                            $shipping_class = 'yes';
                        }
		            }
		            
		            foreach( $shipping_default_zones as $zone_key => $zone_value ) {
		                if( $sv == $zone_value[ 'shipping_default_zone_id' ] ) {
		                    $shipping_method_str .= $zone_value[ 'shipping_default_zone_title' ] . ', ';
		                } else {
		                    $shipping_class = 'yes';
		                }
		            }
		            
		            foreach( $default_shipping_classes as $class_key => $class_value ) {
		                if( $class_value->slug == $sv ) {
		                    $shipping_method_str .= $class_value->name . ', ';
		                } else {
		                    $shipping_class = 'yes';
		                }
		            }
		            
		            if( $shipping_class == 'yes' ) {
		                foreach ( $shipping_classes as $id => $value ) {
		                    if( $value[ 'rate_id' ] != '' ) {
		                        $selected_value = "table_rate-" . $value[ 'shipping_method_id' ] . " : " . $value[ 'rate_id' ];
		                        if( $value[ 'rate_label'] != '' ) {
		                            $label = $value[ 'shipping_method_title' ] . " > " . $value[ 'rate_label' ];
		                        } else {
		                            $label = $value[ 'shipping_method_title' ];
		                        }
		                    } else {
		                        $selected_value = "table_rate-" . $value[ 'shipping_method_id' ];
		                        $label = $value[ 'shipping_method_title' ];
		                    }
		            
		                    if( $sv == $selected_value ) {
		                        $shipping_method_str .= $label . ', ';
		                    }
		                }
		            }
		            //Shipping Methods from Woocommerce Advanced Shipping plugin
		            $methods = get_posts( array( 'posts_per_page' => '-1', 'post_type' => 'was', 'post_status' => array( 'publish' ), 'order' => 'ASC' ) );
		            foreach ( $methods as $method ) {
		                if ( $method->ID == $sv ) {
        		            $method_details = get_post_meta( $sv, '_was_shipping_method', true );
        		            $shipping_method_str .= $method->post_title." -> ".$method_details[ 'shipping_title' ] . ', ';  // Display the shipping method name along with title.
		                }
					}		
					
					//Advanced Flat Rate Shipping Method WooCommerce by Multidots.
					$methods = get_posts( array( 'posts_per_page' => '-1', 'post_type' => 'wc_afrsm', 'post_status' => array( 'publish' ), 'order' => 'ASC' ) );
					foreach ( $methods as $method ) {
						if ( 'advanced_flat_rate_shipping:'.$method->ID == $sv ) {
							$shipping_method_str .= $method->post_title . ', ';  // Display the shipping method name along with title.
						}
					}
		        }
		        $shipping_method_str = substr( $shipping_method_str, 0, -2 );
		    } elseif ( isset( $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] ) && $shipping_settings[ 'delivery_settings_based_on' ][ 0 ] == 'product_categories' ) {
		        $product_categories = isset( $shipping_settings[ 'product_categories' ] ) ? $shipping_settings[ 'product_categories' ] : array();
		        $shipping_method_str = "<b>Product categories</b></br>";
		        $args = array(
		            'taxonomy'     => 'product_cat',
		            'hide_empty'   => 0
		        );
		        
		        $all_categories = get_categories( $args );
		        foreach( $product_categories as $pk => $pv ) {
		            foreach ( $all_categories as $id => $val ) {
		                if( $val->slug == $pv ) {
		                    $shipping_method_str .= $val->name. ', ';
		                }
		            }
		        }

		        $shipping_method_str = substr( $shipping_method_str, 0, -2 );

		        if( isset( $shipping_settings[ 'shipping_methods_for_categories' ] ) ) {
		        	$shipping_method_str .= "<br><br><b>Shipping Methods</b><br>";
		        	$shipping_methods_for_categories = $shipping_settings[ 'shipping_methods_for_categories' ];

		        	$args = array(
				            'hide_empty'   => 0
				        );
				        
			        $default_shipping_classes = get_terms( 'product_shipping_class', $args );
					$active_shipping_methods = $woocommerce->shipping->load_shipping_methods();
			        foreach( $shipping_methods_for_categories as $sk => $sv ) {
			            $shipping_class = 'no';
			            foreach ( $active_shipping_methods as $id => $active_shipping_method ) {
			                if( $sv == $id ) {
	    		                if ( defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, "2.6", '>=' ) > 0 ) {
	    		                    if ( isset( $active_shipping_method->id ) && false !== strstr( $active_shipping_method->id, "legacy" ) ) {
	    		                        $title = $active_shipping_method->title . " (Legacy)";
	    		                        $shipping_method_str .= $title . ", ";
	    		                    } else {
	                                    $shipping_method_str .= $active_shipping_method->title . ', ';
	                                }
	    		                } else {
	    		                    $shipping_method_str .= $active_shipping_method->title . ', ';
	    		                } 
			                } else {
	                            $shipping_class = 'yes';
	                        }
			            }
			            
			            foreach( $shipping_default_zones as $zone_key => $zone_value ) {
			                if( $sv == $zone_value[ 'shipping_default_zone_id' ] ) {
			                    $shipping_method_str .= $zone_value[ 'shipping_default_zone_title' ] . ', ';
			                } else {
			                    $shipping_class = 'yes';
			                }
			            }
			            
			            foreach( $default_shipping_classes as $class_key => $class_value ) {
			                if( $class_value->slug == $sv ) {
			                    $shipping_method_str .= $class_value->name . ', ';
			                } else {
			                    $shipping_class = 'yes';
			                }
			            }
			            
			            if( $shipping_class == 'yes' ) {
			                foreach ( $shipping_classes as $id => $value ) {
			                    if( $value[ 'rate_id' ] != '' ) {
			                        $selected_value = "table_rate-" . $value[ 'shipping_method_id' ] . " : " . $value[ 'rate_id' ];
			                        if( $value[ 'rate_label'] != '' ) {
			                            $label = $value[ 'shipping_method_title' ] . " > " . $value[ 'rate_label' ];
			                        } else {
			                            $label = $value[ 'shipping_method_title' ];
			                        }
			                    } else {
			                        $selected_value = "table_rate-" . $value[ 'shipping_method_id' ];
			                        $label = $value[ 'shipping_method_title' ];
			                    }
			            
			                    if( $sv == $selected_value ) {
			                        $shipping_method_str .= $label . ', ';
			                    }
			                }
			            }
			            //Shipping Methods from Woocommerce Advanced Shipping plugin
			            $methods = get_posts( array( 'posts_per_page' => '-1', 'post_type' => 'was', 'post_status' => array( 'publish' ), 'order' => 'ASC' ) );
			            foreach ( $methods as $method ) {
			                if ( $method->ID == $sv ) {
	        		            $method_details = get_post_meta( $sv, '_was_shipping_method', true );
	        		            $shipping_method_str .= $method->post_title." -> ".$method_details[ 'shipping_title' ] . ', ';// Display the shipping method name along with title.
			                }
			            }		            
			        }
			        $shipping_method_str = substr( $shipping_method_str, 0, -2 );
		        }
		    }
		    $return_shipping_settings[ $row_id ]->shipping_methods = $shipping_method_str;
		    
		    if( isset( $shipping_settings[ 'delivery_type' ] ) ) {
		        $delivery_type = $shipping_settings[ 'delivery_type' ];
		    } else {
		        $delivery_type = '';
		    }
		    
		    if( isset( $shipping_settings[ 'enable_shipping_based_delivery' ] ) && $shipping_settings[ 'enable_shipping_based_delivery' ] == 'on' ) {
		        $enable_shipping_based_delivery = $shipping_settings[ 'enable_shipping_based_delivery' ];
		    } else {
		        $enable_shipping_based_delivery = '';
		    }

		    $return_shipping_settings[ $row_id ]->enable_shipping_based_delivery = $enable_shipping_based_delivery;
		    
		    if( isset( $shipping_settings[ 'orddd_delivery_checkout_options' ] ) && $shipping_settings[ 'orddd_delivery_checkout_options' ] == 'text_block' ) { 
		    	$shipping_days .= "<b>" .__( 'Delivery Checkout option', 'order-delivery-date' ) . "</b>: " . __( 'Text block', 'order-delivery-date' );
		    	if( isset( $shipping_settings[ 'minimum_delivery_time' ] ) && $shipping_settings[ 'minimum_delivery_time' ] != '' ) {
		    		$shipping_days .= "<hr>";
                    $shipping_days .= '<b>' . __( 'Minimum Delivery time (in hours):', 'order-delivery-date' ) . ' </b>' . $shipping_settings[ 'minimum_delivery_time' ];
                }

                if( isset( $shipping_settings[ 'orddd_min_between_days' ] ) && $shipping_settings[ 'orddd_min_between_days' ] != '' && isset( $shipping_settings[ 'orddd_max_between_days' ] ) && $shipping_settings[ 'orddd_max_between_days' ] ) {
		    		$shipping_days .= "<hr>";
                    $shipping_days .= '<b>' . __( 'Between:', 'order-delivery-date' ) . ' </b>' . $shipping_settings[ 'orddd_min_between_days' ] . '-' . $shipping_settings[ 'orddd_max_between_days' ] . ' days' ;
                }

                $return_shipping_settings[ $row_id ]->shipping_days = $shipping_days;
		    } else {
		    	$shipping_days .= "<b>" .__( 'Delivery Checkout option', 'order-delivery-date' ) . "</b>: " . __( 'Delivery Calendar', 'order-delivery-date' );
		    	$shipping_days .= "<hr>";

                if( is_array( $delivery_type ) || 
                    (  !isset( $delivery_type[ 'weekdays' ] )
                    && !isset( $delivery_type[ 'specific_dates' ] ) ) ) {
	                $shipping_days .= "<b>" .__( 'Delivery Days/Dates', 'order-delivery-date' ) . "</b>";
	                $shipping_days .= '<table>';
	            }
			    
			    if( isset( $delivery_type[ 'weekdays' ] ) && $delivery_type[ 'weekdays' ] == 'on' ) {
			        if( isset( $shipping_settings[ 'weekdays' ] ) ) {
			            $weekdays_settings = $shipping_settings[ 'weekdays' ];
			        } else {
			            $weekdays_settings = array();
			        }
			        if( is_array( $weekdays_settings ) && count( $weekdays_settings ) > 0 ) {
			            foreach( $orddd_weekdays as $wk => $wv ) {
			                $weekday = $weekdays_settings[ $wk ];
			                if( isset( $weekday[ 'enable' ] ) && $weekday[ 'enable' ] == 'checked' ) {
			                    $shipping_days .= '<tr><td class="orddd_custom_view_padding">' . $wv . '</td>';
			                    if( isset( $weekday[ 'additional_charges' ] ) && $weekday[ 'additional_charges' ] != '' ) {
			                       $shipping_days .= '<td class="orddd_custom_view_padding">' . $currency_symbol . '' . $weekday[ 'additional_charges' ] . '</td>';
			                    } else {
			                       $shipping_days .= '<td class="orddd_custom_view_padding"></td>';
			                    }
			                    if( isset( $weekday[ 'delivery_charges_label' ] ) && $weekday[ 'delivery_charges_label' ] != '' ) {
			                        $shipping_days .= '<td class="orddd_custom_view_padding">' . $weekday[ 'delivery_charges_label' ] . '</td>';
			                    } else {
			                        $shipping_days .= '<td class="orddd_custom_view_padding"></td>';
			                    }
			                    $shipping_days .= '</tr>';
			                    
			                }
			            }
			        }
			    }
			    
			    if( isset( $delivery_type[ 'specific_dates' ] ) && $delivery_type[ 'specific_dates' ] == 'on' ) {
			        $specific_days_settings = explode( ',', $shipping_settings[ 'specific_dates' ] );
                    // Sort the specific dates array
                    usort( $specific_days_settings, array( 'orddd_common', 'orddd_sort_specific_dates' ) );

			        foreach( $specific_days_settings as $sk => $sv ) {
			            $sv_str = str_replace('}', '', $sv);
			            $sv_str = str_replace('{', '', $sv_str);
			            $specific_date_arr = explode( ':', $sv_str );
			            if( isset( $specific_date_arr[ 0 ] ) && $specific_date_arr[ 0 ] != '' ) {
			                $date = explode( "-",  $specific_date_arr[ 0 ] );
			                $date_to_display = date( "m-d-Y", gmmktime( 0, 0, 0, $date[0], $date[1], $date[2] ) );
			                $shipping_days .= '<tr><td class="orddd_custom_view_padding">' . $date_to_display . '</td>';
			                if( isset( $specific_date_arr[ 1 ] ) && $specific_date_arr[ 1 ] != '' ) {
			                    $shipping_days .= '<td class="orddd_custom_view_padding">' . $currency_symbol . '' . $specific_date_arr[ 1 ] . '</td>';
			                } else {
			                    $shipping_days .= '<td class="orddd_custom_view_padding"></td>';
			                }
			                if( isset( $specific_date_arr[ 2 ] ) && $specific_date_arr[ 2 ] != '' ) {
			                    $shipping_days .= '<td class="orddd_custom_view_padding">' . $specific_date_arr[ 2 ] . '</td>';
			                } else {
			                    $shipping_days .= '<td class="orddd_custom_view_padding"></td>';
			                }
			                $shipping_days .= '</tr>';
			            }
			        }
			    }
			    
			    if( is_array( $delivery_type ) || 
                    (  !isset( $delivery_type[ 'weekdays' ] )
                    && !isset( $delivery_type[ 'specific_dates' ] ) ) ) {
	                $shipping_days .= '</table>';
	                $shipping_days .= '<hr>';
	                if( isset( $shipping_settings[ 'date_lockout' ] ) && $shipping_settings[ 'date_lockout' ] != '' && $shipping_settings[ 'date_lockout' ] != '0' ) {
	                    $shipping_days .= "<b>" . __( 'Maximum Order Deliveries per day: ', 'order-delivery-date' ) . "</b>" . $shipping_settings[ 'date_lockout' ] . "<hr>";
	                } else if( isset( $shipping_settings[ 'date_lockout' ] ) ) {
	                    $shipping_days .= "<b>" . __( 'Maximum Order Deliveries per day: ', 'order-delivery-date' ) . "</b>" . __( 'Unlimited', 'order-delivery-date' ) . "<hr>";
	                }
			    
	                if( isset( $shipping_settings[ 'date_mandatory_field' ] ) && $shipping_settings[ 'date_mandatory_field' ] != '' ) {
	                    $shipping_days .= '<b>' . __( 'Mandatory Field: ', 'order-delivery-date' ) . '</b>Yes<hr>';
	                }
			    
	                if( isset( $shipping_settings[ 'minimum_delivery_time' ] ) && $shipping_settings[ 'minimum_delivery_time' ] != '' ) {
	                    $shipping_days .= '<b>' . __( 'Minimum Delivery time (in hours):', 'order-delivery-date' ) . ' </b>' . $shipping_settings[ 'minimum_delivery_time' ] . '<hr>';
	                }
			    
	                if( isset( $shipping_settings[ 'number_of_dates' ] ) && $shipping_settings[ 'number_of_dates' ] != '' ) {
	                    $shipping_days .= '<b>' . __( 'Number of dates to choose:', 'order-delivery-date' ) . '</b>' . $shipping_settings[ 'number_of_dates' ] . '<hr>';
	                }
			    }
			
		    	$return_shipping_settings[ $row_id ]->shipping_days = $shipping_days;
		    
			    $time_setting = '';
			    if( isset( $shipping_settings[ 'time_settings' ] ) ) {
			        $time_settings = $shipping_settings[ 'time_settings' ];
			        if( isset( $time_settings[ 'from_hours' ] ) && $time_settings[ 'from_hours' ] != 0 && isset( $time_settings[ 'to_hours' ] ) && $time_settings[ 'to_hours' ] != 0 ) {
			            $time_format_to_show = orddd_common::orddd_get_time_format(); 
			            $from_time = date( $time_format_to_show, strtotime ( $time_settings[ 'from_hours' ] . ':' . $time_settings[ 'from_mins' ] ) );
			            $to_time = date( $time_format_to_show, strtotime ( $time_settings[ 'to_hours' ] . ':' . $time_settings[ 'to_mins' ] ) );
			            $time_setting = $from_time . ' - ' . $to_time;
			        } 
			    }
			    
	            $return_shipping_settings[ $row_id ]->time_settings = $time_setting;
			    
	            $same_day_settings = '';
			    if( isset( $shipping_settings[ 'same_day' ] ) ) {
			        $same_day = $shipping_settings[ 'same_day' ];
			        
			        if( isset( $same_day[ 'after_hours' ] ) && $same_day[ 'after_hours' ] == '0' && isset( $same_day[ 'after_minutes' ] ) && $same_day[ 'after_minutes' ] == '00' ) {
			        } else {
			            $same_day_setting = $same_day[ 'after_hours' ] . ':' . $same_day[ 'after_minutes' ];
	                    $same_day_settings .= '<b>' . __( 'Cut-off:', 'order-delivery-date' ) . ' </b>'.$same_day_setting.'</br>';
			            if( isset( $same_day[ 'additional_charges' ] ) && $same_day[ 'additional_charges' ] != '' ) {
			                $same_day_settings .= '<b>' . __( 'Charges:', 'order-delivery-date' ) . ' </b>'.$currency_symbol.''.$same_day[ 'additional_charges' ];
			            } else {
			                $same_day_settings .= '';
			            }
			        }
			    }
			    $return_shipping_settings[ $row_id ]->same_day_settings = $same_day_settings;
	            
	            $next_day_settings = '';
			    if( isset( $shipping_settings[ 'next_day' ] ) ) {
			        $next_day = $shipping_settings[ 'next_day' ];
			        if( isset( $next_day[ 'after_hours' ] ) && $next_day[ 'after_hours' ] == 0 && isset( $next_day[ 'after_minutes' ] ) && $next_day[ 'after_minutes' ] == 00 ) {
			        } else {
			            $next_day_setting = $next_day[ 'after_hours' ] . ':' . $next_day[ 'after_minutes' ];
			            $next_day_settings .= '<b>' . __( 'Cut-off:', 'order-delivery-date' ) . ' </b>'.$next_day_setting.'</br>';
			            if( isset( $next_day[ 'additional_charges' ] ) && $next_day[ 'additional_charges' ] != '' ) {
			                $next_day_settings .= '<b>' . __( 'Charges:', 'order-delivery-date' ) . ' </b>'.$currency_symbol.''. $next_day[ 'additional_charges' ];
			            } else {
			                $next_day_settings .= '';
			            }
			        }
			    }
	            $return_shipping_settings[ $row_id ]->next_day_settings = $next_day_settings;
	                            
	            $time_slots_settings = '';
	            if( isset( $shipping_settings[ 'time_slots' ] ) && $shipping_settings[ 'time_slots' ] != '' ) {
			        $timeslot_settings = explode( '},', $shipping_settings[ 'time_slots' ] );
			        $time_slot_str = '';

                     // Sort the multidimensional array
                    usort( $timeslot_settings, array( 'orddd_common', 'orddd_custom_sort' ) );
			        foreach( $timeslot_settings as $hk => $hv ) {
			            $specific_dates = '';
			            if( $hv != '' ) {
			                $time_format_to_show = orddd_common::orddd_get_time_format(); 
			                $timeslot_values = orddd_common::get_timeslot_values( $hv );
	                        $weekdays = '';
	                        if( $timeslot_values[ 'delivery_days_selected' ] == 'weekdays' ) {
	                            $all_present = "No";
	                            foreach( $timeslot_values[ 'selected_days' ] as $key => $val ) { 
	                                foreach( $orddd_weekdays as $k => $v ) {
	                                    if( $k . "_custom_setting" == $val  ) {
	                                        $weekdays .= $v . ",";
	                                    } else if( "all" == $val ) {
	                                        if( $all_present == "No" ) {
	                                            $weekdays .= "All,";
	                                            $all_present = "Yes";
	                                        }
	                                    }
	                                }
	                            }
	                            $weekdays = substr( $weekdays, 0, strlen( $weekdays )-1 );
	                            $time_slots_settings .= '<b>' . __( 'Weekday:', 'order-delivery-date' ) . ' </b>' . $weekdays . '</br>';
	                        } else if( $timeslot_values[ 'delivery_days_selected' ] == 'specific_dates' ) {
	                            foreach( $timeslot_values[ 'selected_days' ] as $key => $val ) {
	                                $date = explode( "-", $val );
	                                if( isset( $date[0] ) && isset( $date[1] ) && isset( $date[2] ) ) {
	                                	$date_to_display = date( "m-d-Y", gmmktime( 0, 0, 0, $date[0], $date[1], $date[2] ) );	
	                                } else {
	                                	$date_to_display = '';
	                                }
	                                $specific_dates .= $date_to_display . ","; 
	                            }
	                            $specific_dates = substr( $specific_dates, 0, strlen( $specific_dates )-1 );
	                            $time_slots_settings .= '<b>' . __( 'Specific Dates:', 'order-delivery-date' ) . ' </b>' . $specific_dates . '</br>';
	                        }
	                        
	                        $time_slot_arr = explode( " - ", $timeslot_values[ 'time_slot' ] );
	                        $from_time = date( $time_format_to_show, strtotime( $time_slot_arr[ 0 ] ) );
	                        if( isset( $time_slot_arr[ 1 ] ) ) {
	                            $to_time = date( $time_format_to_show, strtotime( $time_slot_arr[ 1 ] ) );
	                            $time_slot = $from_time . " - " . $to_time;
	                        } else {
	                            $time_slot = $from_time;
	                        }
	                        
	                        $time_slots_settings .= '<b>' . __( 'Time Slot:', 'order-delivery-date' ) . ' </b>' . $time_slot . '</br>';
	                        $time_slots_settings .= '<b>' . __( 'Maximum Order Deliveries per time slot:', 'order-delivery-date' ) . ' </b>' . $timeslot_values[ 'lockout' ] . "</br>";
	                        if( $timeslot_values[ 'additional_charges' ] != "" ) {
	                            $time_slots_settings .= '<b>' . __( 'Additional Charges for time slot:', 'order-delivery-date' ) . ' </b>' . $currency_symbol . '' . $timeslot_values[ 'additional_charges' ] . "</br>";
	                        } 
	                        if( $timeslot_values[ 'additional_charges_label' ] != "" ) {
	                            $time_slots_settings .= '<b>' . __( 'Checkout label:', 'order-delivery-date' ) . ' </b>' . $timeslot_values[ 'additional_charges_label' ] . '<hr>';
	                        } else {
	                            $time_slots_settings .= '<hr>';
	                        }
			            }
			        }
			    }
			    if( isset( $shipping_settings[ 'timeslot_mandatory_field' ] ) && $shipping_settings[ 'timeslot_mandatory_field' ] != '' ) {
			        $time_slots_settings .= '<b>' . __( 'Mandatory Field:', 'order-delivery-date' ) . ' </b>Yes <hr>';
			    }

                if( isset( $shipping_settings[ 'timeslot_asap_option' ] ) && $shipping_settings[ 'timeslot_asap_option' ] != '' ) {
                    $time_slots_settings .= '<b>' . __( "Show 'As Soon As Possible' option:", 'order-delivery-date' ) . ' </b>Yes <hr>';
                }

			    $return_shipping_settings[ $row_id ]->time_slot_settings = $time_slots_settings;
			    
			    $holiday_str = '';
			    if( isset( $shipping_settings[ 'enable_global_holidays' ] ) && $shipping_settings[ 'enable_global_holidays' ] == 'checked' ) {
			        $holiday_str .=  '<b>Use Global Holidays:</b> Yes</br>';
			    }
			    
			    if( isset( $shipping_settings[ 'holidays' ] ) && $shipping_settings[ 'holidays' ] != '' ) {
			        $holiday_settings = explode( ',', $shipping_settings[ 'holidays' ] );
			        foreach( $holiday_settings as $hk => $hv ) {
			            $hv = str_replace( '}', '', $hv );
			            $hv = str_replace( '{', '', $hv );
			            $holiday_arr = explode( ':', $hv );
			            if( isset( $holiday_arr[ 1 ] ) && $holiday_arr[ 1 ] != '' ) {
			                $date = explode( "-", $holiday_arr[ 1 ] );
                            $date_to_display = date( "m-d-Y", gmmktime( 0, 0, 0, $date[0], $date[1], $date[2] ) );    
                            if( isset( $holiday_arr[2] ) && $holiday_arr[2] == 'on' ) {
                                $recurring_type = __( "Recurring", 'order-delivery-date' );
                            } else {
                                $recurring_type = __( "Current Year", 'order-delivery-date' );
                            }

			                $holiday_str .=  $date_to_display . '</br><b>' . __( "Type:", 'order-delivery-date' ) . "</b> " . $recurring_type . "<hr>";
			            }
			        }
			        $holiday_str = substr( $holiday_str, 0, -1 );
			    }
			    $return_shipping_settings[ $row_id ]->holidays = $holiday_str;
			}
		    $return_shipping_settings[ $row_id ]->row_id = $row_id;
		    $i++;
		    $shipping_based_count++;
        }
		return apply_filters( 'orddd_shipping_settings_table_data', $return_shipping_settings );
	}
	
	/**
	 * Add Edit and Delete link in each row of the table data
	 * 
	 * @param resource $shipping_settings
	 * @param string $column_name
	 * @return array
	 * @since 3.0
	 */
	public function column_default( $shipping_settings, $column_name ) {
		switch ( $column_name ) {
		    case 'status' :
		        if( $shipping_settings->enable_shipping_based_delivery == 'on' ) {
                    
                    $setting_str = 'orddd_shipping_settings_status_' . $shipping_settings->row_id;
                    $active = 'on';
                    if ( 'inactive' === get_option( $setting_str ) ) {
                        $active = 'off';
                    }
                    $active_text   = __( $active, 'order-delivery-date' ); 
                    $value =  '<button type="button" class="orddd-switch orddd-toggle-template-status" '
                    . 'orddd-custom-setting-id="' . $shipping_settings->row_id . '" '
                    . 'orddd-template-switch="' . $active . '">'
                    . $active_text . '</button>';
		        } else {
		            $value = '<span> - </span>';
		        }
		        break;
			case 'actions' :
	 			$value = '<a href=\'admin.php?page=order_delivery_date&action=shipping_based&mode=edit&row_id=' . $shipping_settings->row_id . '\' class=\'edit_shipping_setting\'><span class="dashicons dashicons-edit" title ="Edit"></a> | <a href="#" id="clone_setting" class="clone_setting" data-id = '.$shipping_settings->row_id .' ><span class="dashicons dashicons-admin-page" title="Clone"></span></a>';
				break;
			default:
				$value = isset( $shipping_settings->$column_name ) ? $shipping_settings->$column_name : '';
				break;
		}
		return apply_filters( 'bkap_booking_table_column_default', $value, $shipping_settings, $column_name );
	}	
}
?>
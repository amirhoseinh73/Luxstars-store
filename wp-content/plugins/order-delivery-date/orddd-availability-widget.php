<?php 
/**
 * Add Availability Widget in admin.
 *
 * @package Order-Delivery-Date-Pro-for-WooCommerce/Frontend/Widgets/Availability-Widget
 *
 * @author Tyche Softwares
 * @since 8.6
 */

/**
 * Creating the widget class
 * 
 * @since 8.6
 */

class orddd_availability_widget extends WP_Widget {

	/**
	 * Default Constructor
	 *
	 * @since 8.6
	 */
	public function __construct() {
		$this->widget_description = "";
		$this->widget_id          = 'orddd_availability_widget';
		$this->widget_name        = __( 'Delivery Date Availability Calendar', 'order-delivery-date' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Delivery Date Availability Calendar', 'order-delivery-date' ),
				'label' => __( 'Title', 'order-delivery-date' ),
			),
			'orddd_postcode_check' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Accept Postcodes', 'order-delivery-date' ),
			)
		);
		parent::__construct( 'orddd_availability_widget', $this->widget_name, $this->settings );
	}
 	
 	
	/**
	 * Adds the widget on the frontend.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database. 
	 *
	 * @since 8.6
	 */
	public function widget( $args, $instance ) { 
		global $pagename;
		if( !is_checkout() && 
		    ( ( 'on' == get_option( 'orddd_delivery_date_on_cart_page' ) && !is_cart() ) 
		        || 'on' != get_option( 'orddd_delivery_date_on_cart_page' ) 
		    ) && 
		    ( ( 'on' == get_option( 'orddd_shipping_multiple_address_compatibility' ) && $pagename != 'shipping-addresses' ) 
		        || 'on' != get_option( 'orddd_shipping_multiple_address_compatibility' ) 
	        )
	     ) {
			global $orddd_version;

			wp_enqueue_script( 'initialize-datepicker-functions-orddd' );
			wp_enqueue_script( 'orddd-availability-widget' );
			wp_enqueue_style( 'jquery-ui-style-orddd' );
			wp_enqueue_style( 'orddd-datepicker' );
			wp_enqueue_script( 'orddd_language' );
            
			echo $args[ 'before_widget' ];
			$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '' );
			if ( $title ) {
				echo $args[ 'before_title' ] . $title . $args[ 'after_title' ];
			}
			
			$admin_url = get_admin_url();
	        $admin_url_arr = explode( "://", $admin_url );
	        $home_url = get_home_url();
	        $home_url_arr = explode( "://", $home_url );
	        if( is_admin() ) {
	        	$ajax_url = $admin_url;
	        } else {
	        	if( $admin_url_arr[ 0 ] != $home_url_arr[ 0 ] ) {
	         	   $admin_url_arr[ 0 ] = $home_url_arr[ 0 ];
	            	$ajax_url = implode( "://", $admin_url_arr );
		        } else {
		            $ajax_url = $admin_url;
		        }	
	        }

		    echo orddd_common::load_hidden_fields();
            
		    $orddd_holiday_color = get_option( 'orddd_holiday_color' );
			$orddd_booked_dates_color = get_option( 'orddd_booked_dates_color' );
			$orddd_cut_off_time_color = get_option( 'orddd_cut_off_time_color' );
			$orddd_available_dates_color = get_option( 'orddd_available_dates_color' );

			echo '<style type="text/css">
				.holidays {
					background-color: ' . $orddd_holiday_color . ' !important;
				}

				.booked_dates {
					background-color: ' . $orddd_booked_dates_color . ' !important;
				}

				.cut_off_time_over {
					background-color: ' . $orddd_cut_off_time_color . ' !important;
				}
			</style>';
			
	        ?>
	        <input type="hidden" name="orddd_available_dates_color" id="orddd_available_dates_color" value="<?php echo $orddd_available_dates_color; ?>">
	        <input type="hidden" name="orddd_booked_dates_color" id="orddd_booked_dates_color" value="<?php echo $orddd_booked_dates_color; ?>">
		    <input type="hidden" name="orddd_admin_url" id="orddd_admin_url" value="<?php echo $ajax_url; ?>">
		    <div id="ajax_img" name="ajax_img"  style= "display:none;z-index: 1000;margin: 25px auto;position:absolute;padding-left: 95px;background-color: transparent;border-radius: 10px;filter: alpha(opacity=100);opacity: 1;-moz-opacity: 1;" > <img src="<?php echo plugins_url() . '/order-delivery-date/images/ajax-loader.gif' ?>"> </div>
			<div class='orddd_availability_widget'>
			<?php
				if( isset( $instance[ 'orddd_postcode_check' ] ) && $instance[ 'orddd_postcode_check' ] == 1 ) {
					?><p class='orddd_widget_postcode'>
						<input type='text' id='orddd_availability_postcode' placeholder="<?php _e( 'Enter Postcode', 'order-delivery-date' ); ?>"/>
					</p>
					<p class="orddd_show_availability_button">
						<a href="javascript:void(0)" style="font-size: 15px" class="orddd_show_availability"><?php _e( 'Show Availability', 'order-delivery-date' )?> &raquo;</a>
					</p>
					<div id="orddd_availability_calendar" class="availability_calendar" style="display:none"></div>
				<?php } else { ?>
				    <div id="orddd_availability_calendar" class="availability_calendar" ></div>
				<?php } ?>
				<br>
				<p class="orddd_current_postcode" style="display:none; margin:0 0 0 0;font-size: 15px;"></p>
				<p class="orddd_show_postcode_field" style="display:none">
					<a href="javascript:void(0)" style="font-size: 15px" class="orddd_show_postcode">&laquo; <?php _e( 'Change Postcode', 'order-delivery-date' )?> </a>
				</p>
			</div>
			<?php
			echo $args[ 'after_widget' ];	
		}
	}
		         
	/**
	 * Add settings in the widget on Appearance Widgets menu page in admin. 
	 * 
	 * @param array $instance Previously saved values from database.
	 *
	 * @since 8.6
	 */	
	public function form( $instance ) {
		if ( empty( $this->settings ) ) {
			return;
		}
		foreach ( $this->settings as $key => $setting ) {
			$class = isset( $setting['class'] ) ? $setting['class'] : '';
			$value = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting['std'];
			switch ( $setting['type'] ) {
				case 'text' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;
				case 'checkbox' :
				    $value 
					?>
					<p>						
						<input class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="checkbox" value="1" <?php checked( $value, 1 ); ?>/>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
					</p>
					<?php
				break;
				// Default: run an action
				default :
					do_action( 'woocommerce_widget_field_' . $setting['type'], $key, $value, $setting, $instance );
				break;
			}
		}
	}
     
	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 *
	 * @since 8.6
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		if ( empty( $this->settings ) ) {
			return $instance;
		}
		// Loop settings and get values to save.
		foreach ( $this->settings as $key => $setting ) {
			if ( ! isset( $setting['type'] ) ) {
				continue;
			}
			// Format the value based on settings type.
			switch ( $setting['type'] ) {
				case 'checkbox' :
					$instance[ $key ] = empty( $new_instance[ $key ] ) ? 0 : 1;
				break;
				default:
					$instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
				break;
			}
			/**
			 * Sanitize the value of a setting.
			 */
			$instance[ $key ] = apply_filters( 'woocommerce_widget_settings_sanitize_option', $instance[ $key ], $new_instance, $key, $setting );
		}
		return $instance;
	}
} // Class orddd_availability_widget ends here
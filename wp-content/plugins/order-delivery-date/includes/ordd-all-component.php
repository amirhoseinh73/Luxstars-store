<?php
/**
 * It will Add all the Boilerplate component when we activate the plugin.
 * @author  Tyche Softwares
 * @package Order-Delivery-Date-Pro-for-WooCommerce/Admin/Component
 * 
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
if ( ! class_exists( 'Ordd_All_Component' ) ) {
	/**
	 * It will Add all the Boilerplate component when we activate the plugin.
	 * 
	 */
	class Ordd_All_Component {
	    
		/**
		 * It will Add all the Boilerplate component when we activate the plugin.
		 */
		public function __construct() {

			$is_admin = is_admin();

			if ( true === $is_admin ) {

                require_once( "component/license-active-notice/ts-active-license-notice.php" );
                require_once( "component/WooCommerce-Check/ts-woo-active.php" );

                require_once( "component/tracking data/ts-tracking.php" );
                require_once( "component/deactivate-survey-popup/class-ts-deactivation.php" );

                require_once( "component/faq_support/ts-faq-support.php" );
                
                $ordd_plugin_name             = self::ts_get_plugin_name();
                $ordd_edd_license_option      = 'edd_sample_license_status_odd_woo';
                $ordd_license_path            = 'admin.php?page=edd_sample_license_page';
                $ordd_locale                  = self::ts_get_plugin_locale();
                $ordd_file_name               = 'order-delivery-date/order_delivery_date.php';
                $ordd_plugin_prefix           = 'orddd';
                $ordd_lite_plugin_prefix      = 'orddd_lite';
                $ordd_plugin_folder_name      = 'order-delivery-date/';
                $ordd_plugin_dir_name         = dirname ( untrailingslashit( plugin_dir_path ( __FILE__ ) ) )  . '/order_delivery_date.php' ;

                $ordd_blog_post_link           = 'https://www.tychesoftwares.com/order-delivery-date-usage-tracking/';

                $ordd_get_previous_version = get_option( 'orddd_db_version' );

                $ordd_plugins_page         = 'admin.php?page=order_delivery_date';
                $ordd_plugin_slug          = 'order_delivery_date';

                $ordd_settings_page        = 'admin.php?page=order_delivery_date&action=general_settings&section=additional_settings';
                $ordd_setting_add_on       = 'orddd_additional_settings_page';
                $ordd_setting_section      = 'orddd_additional_settings_section';
                $ordd_register_setting     = 'orddd_additional_settings';

                $ordd_plugin_url           = dirname ( untrailingslashit( plugins_url( '/', __FILE__ ) ) );

                $orddd_license_key_name     = 'edd_sample_license_key_odd_woo';

                new orddd_active_license_notice ( $ordd_plugin_name, $ordd_edd_license_option, $ordd_license_path, $ordd_locale );
				
				new orddd_ts_woo_active ( $ordd_plugin_name, $ordd_file_name, $ordd_locale );

                new orddd_ts_tracking ( $ordd_plugin_prefix, $ordd_plugin_name, $ordd_blog_post_link, $ordd_locale, $ordd_plugin_url, $ordd_settings_page, $ordd_setting_add_on, $ordd_setting_section, $ordd_register_setting );

                new orddd_ts_tracker ( $ordd_plugin_prefix, $ordd_plugin_name );

                $wcap_deativate = new orddd_ts_deactivate;
                $wcap_deativate->init ( $ordd_file_name, $ordd_plugin_name );

                $ts_pro_faq = self::ordd_get_faq ();
				new orddd_ts_faq_support( $ordd_plugin_name, $ordd_plugin_prefix, $ordd_plugins_page, $ordd_locale, $ordd_plugin_folder_name, $ordd_plugin_slug, $ts_pro_faq );

            }
        }
        
        /**
         * It will retrun the plguin name.
         * @return string $ts_plugin_name Name of the plugin
         */
		public static function ts_get_plugin_name () {
            $ordd_plugin_dir =  dirname ( dirname ( __FILE__ ) );
            $ordd_plugin_dir .= '/order_delivery_date.php';

            $ts_plugin_name = '';
            $plugin_data = get_file_data( $ordd_plugin_dir, array( 'name' => 'Plugin Name' ) );
            if ( ! empty( $plugin_data['name'] ) ) {
                $ts_plugin_name = $plugin_data[ 'name' ];
            }
            return $ts_plugin_name;
        }

        /**
         * It will retrun the Plugin text Domain
         * @return string $ts_plugin_domain Name of the Plugin domain
         */
        public static function ts_get_plugin_locale () {
            $ordd_plugin_dir =  dirname ( dirname ( __FILE__ ) );
            $ordd_plugin_dir .= '/order_delivery_date.php';

            $ts_plugin_domain = '';
            $plugin_data = get_file_data( $ordd_plugin_dir, array( 'domain' => 'Text Domain' ) );
            if ( ! empty( $plugin_data['domain'] ) ) {
                $ts_plugin_domain = $plugin_data[ 'domain' ];
            }
            return $ts_plugin_domain;
        }
		/**
         * It will contain all the FAQ which need to be display on the FAQ page.
         * @return array $ts_faq All questions and answers.
         * 
         */
        public static function ordd_get_faq () {

            //utm_source=userwebsite&utm_medium=link&utm_campaign=AbandonedCartProFAQTab
            $ts_faq = array ();

            $ts_faq = array(
                1 => array (
                        'question' => 'I need some lead preparation time before I can make a delivery. Can I set a minimum delivery period on my WooCommerce store?',
                        'answer'   => 'Yes, you can set a minimum delivery period in hours, which will be taken into consideration before showing the earliest available delivery date or time slot to your customers. This can be done under the <strong>“Minimum Delivery time (in hours)”</strong> field under the General Settings -> Date Settings tab in the Order Delivery Date on the admin side. Minutes will be accepted in the decimal format like for 30 Minutes you can use 0.50. <a href="https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/date-controls/set-up-minimum-delivery-preparation-time/?utm_source=userwebsite&utm_medium=link&utm_campaign=OrderDeliveryDateProFAQ" target="_blank" class="dashicons dashicons-external"></a>'
                    ), 
                2 => array (
                        'question' => 'The working days of my company are different than the working days of my shipping company. Can I add them differently?',
                        'answer'   => 'Yes, you can set your company’s working days and shipping company’s working days differently. You can set up this under Shipping Days section under General Settings -> Date Settings tab in the Order Delivery Date on the admin side.<a href="https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/setup-delivery-dates/?utm_source=userwebsite&utm_medium=link&utm_campaign=OrderDeliveryDateProFAQ" target="_blank" class="dashicons dashicons-external"></a>'
                    ),
                3 => array (
						'question' => 'Can I provide same day deliveries to my customers?',
						'answer'   => 'Yes. The Same day delivery feature enables you to get the deliveries for the same date. This is available in the Time Settings tab in the Order Delivery Date on the admin side. The current date would be available until the cut-off time set for the same date delivery is passed. Once the time is passed the date will be disabled and will have the label “Cut-Off time over”. Similarly, the Next day delivery feature works where the next day would be available for delivery.<a href="https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/setup-same-day-delivery-and-next-day-delivery/?utm_source=userwebsite&utm_medium=link&utm_campaign=OrderDeliveryDateProFAQ" target="_blank" class="dashicons dashicons-external"></a>'
                ),
                4 => array (
						'question' => 'Can I charge my customers if they want deliveries on certain days?',
						'answer'   => 'Yes, you can charge your customers by adding additional charges to the delivery days/dates as well as time slots from the plugin. Charges for the days can be added under Weekday Settings tab. For time slot, you can add charges while creating time slots.<a href="https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/additional-delivery-charges-for-delivery-dates-and-time/?utm_source=userwebsite&utm_medium=link&utm_campaign=OrderDeliveryDateProFAQ" target="_blank" class="dashicons dashicons-external"></a>'
                ),
                5 => array (
						'question' => 'Can the delivery date be changed for already placed orders?',
						'answer'   => 'Yes, the delivery date and time can be changed by the administrator as well as by the customers. The administrator can change it on WooCommerce -> Edit order page in the admin. And the customer can change it on the My Account page. To allow customers to edit the date, you need to enable <strong>“Allow Customers to edit Delivery Date & Time”</strong> checkbox under General Settings -> Additional Settings tab on the admin side.<a href="https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/ability-to-edit-dates-times-via-admin/?utm_source=userwebsite&utm_medium=link&utm_campaign=OrderDeliveryDateProFAQ" target="_blank" class="dashicons dashicons-external"></a>'
                ),
                6 => array (
						'question' => 'Can I limit the number of deliveries per day?',
						'answer'   => 'Yes, you can limit the number of deliveries per day. You need to set the number of deliveries in the “Maximum Order Deliveries per day (based on per order)” field under General Settings -> Date Settings tab in the Order Delivery Date on the admin side.<a href="https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/understanding-maximum-order-deliveries-setting/?utm_source=userwebsite&utm_medium=link&utm_campaign=OrderDeliveryDateProFAQ" target="_blank" class="dashicons dashicons-external"></a>'
                ),
                7 => array (
						'question' => 'Can I add different delivery schedules for different delivery zones?',
						'answer'   => 'Yes, you can add different delivery schedules for different shipping methods added for the default WooCommerce shipping zones.<br><br>Apart from shipping methods, you can also add different schedules for different product categories and default WooCommerce shipping classes.<a href="https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/custom-delivery-settings/?utm_source=userwebsite&utm_medium=link&utm_campaign=OrderDeliveryDateProFAQ" target="_blank" class="dashicons dashicons-external"></a>'
                ),
                8 => array (
						'question' => 'Can I export the deliveries to another calendar for easy access?',
						'answer'   => 'Yes, you can export your deliveries to the google calendar directly or manually by downloading ICS files. This can be done under Google Calendar Sync tab.<a href="https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/synchronise-delivery-date-time-with-google-calendar/?utm_source=userwebsite&utm_medium=link&utm_campaign=OrderDeliveryDateProFAQ" target="_blank" class="dashicons dashicons-external"></a>'
                ),
                9 => array (
						'question' => 'I don\'t want some time slots for particular of the dates or weekdays. Can I disable them?',
						'answer'   => 'Yes, disable time slots for certain days or dates. You can add the time slots which you want to disable under General Settings -> Time Slot -> Block a Time slots link. The time slot will not be shown on the checkout page for that particular day or date.<a href="https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/disabled-delivery-dates-and-time/block-a-time-slot/?utm_source=userwebsite&utm_medium=link&utm_campaign=OrderDeliveryDateProFAQ" target="_blank" class="dashicons dashicons-external"></a>'
                ),
                10 => array (
						'question' => 'Can I have a particular time range between which I can deliver products?',
						'answer'   => 'Yes, you can have your suitable time range. You can do this by selecting the time range in the General Settings -> Time settings tab in the Order Delivery date on the admin side. Firstly, you need to enable "Enable Delivery Time capture", then select the Delivery From Time and Delivery To Time from their respective drop boxes. In this manner, the time range will be created and time sliders will be displayed on the calendar with the set time range on the checkout page.'
                )    
            );

            return $ts_faq;
        }
	}
	$Ordd_All_Component = new Ordd_All_Component();
}

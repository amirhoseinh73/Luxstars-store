<?php 
/**
 * Order Delivery Date Pro for WooCommerce
 *
 * Handles email sending from the plugin.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Pro-for-WooCommerce/Class-ORDDD-Email-Manager
 * @since       5.7
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * ORDDD_Email_Manager Class
 *
 * @class ORDDD_Email_Manager
 */
class ORDDD_Email_Manager {

	/**
	 * Constructor sets up hooks to add the 
	 * email actions to WooCommerce emails.
	 * 
	 * @since 5.7
	 */
	public function __construct() {
		add_filter( 'woocommerce_email_classes', array( &$this, 'orddd_init_emails' ) );
		
		// Email Actions
		$email_actions = array(
            'orddd_admin_update_date',
			'orddd_email_delivery_reminder',
			'orddd_email_admin_delivery_reminder'
			
		 );

		foreach ( $email_actions as $action ) {
            add_action( $action, array( 'WC_Emails', 'send_transactional_email' ), 10, 10 );
		}
		
		add_filter( 'woocommerce_template_directory', array( $this, 'orddd_template_directory' ), 10, 2 );
		
	}
	
	/**
	 * Adds the Email class file to ensure the emails
	 * from the plugin are fired based on the settings.
	 * 
	 * @param array $emails - List of Emails already setup by WooCommerce
	 * @return array $emails - List of Emails with the ones from the plugin included.
	 *  
	 * @hook woocommerce_email_classes
	 * @since 5.7
	 */
	public function orddd_init_emails( $emails ) {
	    if ( ! isset( $emails[ 'ORDDD_Email_Update_Date' ] ) ) {
	        $emails[ 'ORDDD_Email_Update_Date' ] = require_once( 'emails/class-orddd-email-update-date.php' );
	    }

	    if ( ! isset( $emails[ 'ORDDD_Email_Delivery_Reminder' ] ) ) {
	        $emails[ 'ORDDD_Email_Delivery_Reminder' ] = require_once( 'emails/class-orddd-email-delivery-reminder.php' );
		}
		
		if ( ! isset( $emails[ 'ORDDD_Email_Admin_Delivery_Reminder' ] ) ) {
	        $emails[ 'ORDDD_Email_Admin_Delivery_Reminder' ] = require_once( 'emails/class-orddd-admin-delivery-reminder.php' );
	    }
	    return $emails;
	}
	
	/**
	 * Returns the directory name in which the template file is present.
	 * 
	 * @param string $directory - Directory Name in which the template is present.
	 * @param string $template - Email Template File Name
	 * @return string $directory - Directory Name in which the template is present. Modified when the template is for our plugin.
	 * 
	 * @hook woocommerce_template_directory
	 * @since 5.7
	 */
	public function orddd_template_directory( $directory, $template ) {
	    if ( false !== strpos( $template, 'order-' ) ) {
	        return 'order-delivery-date';
	    }
	
	    return $directory;
	}
	
	/**
	 * Adds a hook to fire the delivery date/time edit email notice.
	 * 
	 * @param integer $order_id - Order ID for which the Delivery Date/Time is edited.
	 * @param string $updated_by - States by whom are the details being updated. Valid Values: admin|customer 
	 * 
	 * @since 6.8
	 */
	public static function orddd_send_email_on_update( $order_id, $updated_by ) {
        WC_Emails::instance();
        do_action( 'orddd_admin_update_date_notification', $order_id, $updated_by );
	}
}// end of class
new ORDDD_Email_Manager();
?>

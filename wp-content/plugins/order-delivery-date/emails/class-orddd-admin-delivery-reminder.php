<?php

/**
 * Admin Delivery Reminder class.
 */
class ORDDD_Email_Admin_Delivery_Reminder extends WC_Email { 

    function __construct() {
        $this->id = 'orddd_delivery_reminder';
        $this->title = __( 'Admin Delivery Reminder', 'order-delivery-date' );
        $this->description = __( 'Delivery Reminder Emails for Admins', 'order-delivery-date' );
        $this->heading = __( 'Delivery Reminder for {tomorrow}', 'order-delivery-date' );
		$this->subject = __( '[{blogname}] You have {delivery_count} deliveries for {tomorrow}', 'order-delivery-date' );
        
        $this->template_html  = 'emails/admin-delivery-reminder.php';
        $this->template_plain = 'emails/plain/admin-delivery-reminder.php';
        
        // Call parent constructor
		parent::__construct();

		// Other settings
        $this->template_base = ORDDD_TEMPLATE_PATH;
		$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
        
    }


    function trigger( $subject = "", $message = "" ) {
	    $enabled = $this->is_enabled();
		$tomorrow = date('j M, Y', strtotime('tomorrow') ); 

		$tomorrow_orders = orddd_get_tomorrows_orders();
		$delivery_count = count( $tomorrow_orders );
		$this->find[]    = '{tomorrow}';
		$this->replace[] = $tomorrow;
		
		$this->find[]    = '{delivery_count}';
		$this->replace[] = $delivery_count;

        if ( ! $this->get_recipient() )
	        return;
	
		if( $subject !== "" || $message !== "" ){
			$this->heading              = str_replace( $this->find, $this->replace, $subject );
			$this->subject              = str_replace( $this->find, $this->replace, $subject );
			$this->message 				= str_replace( $this->find, $this->replace, $message );
		} else {
			$this->message = "";
			$this->subject = $this->get_subject();
		}

	    $this->send( $this->get_recipient(), $this->subject , stripslashes( $this->get_content() ), $this->get_headers(), $this->get_attachments() );
    }


    function get_content_html() {
	    ob_start();
	    wc_get_template( $this->template_html, array(
			'email_heading' => $this->get_heading(),
			'message'		=> $this->message,
			'email'			=> $this,
		    'sent_to_admin' => false,
		    'plain_text'    => false
		    ), '', $this->template_base );
	    return ob_get_clean();
    }
    
    function get_content_plain() {
	    ob_start();
	    wc_get_template( $this->template_plain, array(
			'email_heading' => $this->get_heading(),
			'message'		=> $this->message,
			'email'			=> $this,
		    'sent_to_admin' => false,
		    'plain_text'    => true
	    	), '', $this->template_base );
	    return ob_get_clean();
    }
    

    function init_form_fields() {
	    $this->form_fields = array(
	        'enabled' => array(
	            'title' 		=> __( 'Enable/Disable', 'order-delivery-date' ),
	            'type' 			=> 'checkbox',
	            'label' 		=> __( 'Enable this email notification', 'order-delivery-date' ),
	            'default' 		=> 'yes'
	        ),
	        'subject' => array(
	            'title' 		=> __( 'Subject', 'order-delivery-date' ),
	            'type' 			=> 'text',
	            'description' 	=> sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'order-delivery-date' ), $this->subject ),
	            'placeholder' 	=> '',
	            'default' 		=> ''
	        ),
	        'heading' => array(
	            'title' 		=> __( 'Email Heading', 'order-delivery-date' ),
	            'type' 			=> 'text',
	            'description' 	=> sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'order-delivery-date' ), $this->heading ),
	            'placeholder' 	=> '',
	            'default' 		=> ''
	        ),
	        'email_type' => array(
	            'title' 		=> __( 'Email type', 'order-delivery-date' ),
	            'type' 			=> 'select',
	            'description' 	=> __( 'Choose which format of email to send.', 'order-delivery-date' ),
	            'default' 		=> 'html',
	            'class'			=> 'email_type',
	            'options'		=> array(
	                'plain'		 	=> __( 'Plain text', 'order-delivery-date' ),
	                'html' 			=> __( 'HTML', 'order-delivery-date' ),
	                'multipart' 	=> __( 'Multipart', 'order-delivery-date' ),
	            )
	        )
	    );
	}
}

return new ORDDD_Email_Admin_Delivery_Reminder();
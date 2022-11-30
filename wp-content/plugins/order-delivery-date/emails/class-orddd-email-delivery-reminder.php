<?php

/**
 * Delivery reminder email to customers.
 */
class ORDDD_Email_Delivery_Reminder extends WC_Email {

    function __construct() {
        $this->id = 'orddd_delivery_reminder';
        $this->title = __( 'Delivery Reminder', 'order-delivery-date' );
        $this->description = __( 'Delivery Reminder Emails', 'order-delivery-date' );
        $this->heading = __( 'Delivery Reminder', 'order-delivery-date' );
		$this->subject = __( '[{blogname}] You have a delivery for your order {order_number}', 'order-delivery-date' );
        
        $this->template_html  = 'emails/customer-delivery-reminder.php';
        $this->template_plain = 'emails/plain/customer-delivery-reminder.php';
        
        // Call parent constructor
		parent::__construct();

		// Other settings
		$this->template_base = ORDDD_TEMPLATE_PATH;
    }


    function trigger( $order_id, $subject = "", $message = "" ) {
	    $enabled = $this->is_enabled();
        if ( $order_id && $enabled ) {
            $this->object = wc_get_order( $order_id );
        }

        $key = array_search( '{order_details}', $this->find );
        if ( false !== $key ) {
            unset( $this->find[ $key ] );
            unset( $this->replace[ $key ] );
        }

        ob_start();
		do_action( 'woocommerce_email_order_details', $this->object, false, false, $this );
		$order_details = ob_get_contents();
		ob_end_clean();

		$key = array_search( '{order_details}', $this->find );
        if ( false !== $key ) {
            unset( $this->find[ $key ] );
            unset( $this->replace[ $key ] );
        }

        $this->find[]    = '{order_details}';
        $this->replace[] = $order_details;

        if ( $order_id != '' ) {
        	
        	$key = array_search( '{order_date}', $this->find );
            if ( false !== $key ) {
                unset( $this->find[ $key ] );
                unset( $this->replace[ $key ] );
            }

            $this->find[]    = '{order_date}';
            $this->replace[] = date_i18n( wc_date_format(), strtotime( $this->object->get_date_completed() ) );


        	$key = array_search( '{order_number}', $this->find );
            if ( false !== $key ) {
                unset( $this->find[ $key ] );
                unset( $this->replace[ $key ] );
            }

            $this->find[]    = '{order_number}';
            $this->replace[] = $this->object->get_order_number();

            $this->recipient = $this->object->get_billing_email();

            $delivery_date = orddd_common::orddd_get_order_delivery_date( $order_id );
            $time_slot = orddd_common::orddd_get_order_timeslot( $order_id );

            $key = array_search( '{delivery_date}', $this->find );
            if ( false !== $key ) {
                unset( $this->find[ $key ] );
                unset( $this->replace[ $key ] );
            }

			$this->find[]    = '{delivery_date}';
            $this->replace[] = $delivery_date;

            $key = array_search( '{delivery_time}', $this->find );
            if ( false !== $key ) {
                unset( $this->find[ $key ] );
                unset( $this->replace[ $key ] );
            }

            $this->find[]    = '{delivery_time}';
            $this->replace[] = $time_slot;            
            
            $key = array_search( '{customer_name}', $this->find );
            if ( false !== $key ) {
                unset( $this->find[ $key ] );
                unset( $this->replace[ $key ] );
            }

            $this->find[]    = '{customer_name}';
			$this->replace[] = $this->object->get_billing_first_name() . " " . $this->object->get_billing_last_name();
			
			$key = array_search( '{customer_first_name}', $this->find );
            if ( false !== $key ) {
                unset( $this->find[ $key ] );
                unset( $this->replace[ $key ] );
            }

			$this->find[]    = '{customer_first_name}';
			$this->replace[] = $this->object->get_billing_first_name();

			$key = array_search( '{customer_last_name}', $this->find );
            if ( false !== $key ) {
                unset( $this->find[ $key ] );
                unset( $this->replace[ $key ] );
            }

			$this->find[]    = '{customer_last_name}';
			$this->replace[] = $this->object->get_billing_last_name();
        } 
        
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
		    'delivery' 		=> $this->object,
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
		    'delivery' 		=> $this->object,
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

return new ORDDD_Email_Delivery_Reminder();
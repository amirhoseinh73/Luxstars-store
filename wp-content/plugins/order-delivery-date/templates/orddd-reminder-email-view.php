<section class="heading">
    <div class="wrap">  
        <h2><?php _e( 'Send Reminder', 'order-delivery-date' ); ?></h2>
    </div>
</section>
<section class="orddd-automatic">
    <div class="wrap">
        <h2><?php _e( 'Automatic Reminders', 'order-delivery-date' ); ?></h2>
        <p><em>* The reminder emails will be sent for the orders that are either of 'Processing' Or 'Completed' status.</em></p>
        <div id="content">
            <form method="post" action="options.php">
                    <?php 
                        settings_errors();
                        settings_fields( "orddd_reminder_settings" );
                        do_settings_sections( "orddd_send_reminder_page" );
                        submit_button ( __( 'Save Settings', 'order-delivery-date' ), 'primary', 'save_reminder', true );
                    ?>
            </form>
        </div>
    </div>
</section>

<hr>

<section class="orddd-manual">
    <div class="wrap">
        <h2><?php _e( 'Manual Reminders', 'order-delivery-date' ); ?></h2>
        <p><?php echo sprintf( __( 'You may send an email notification to all customers who have a %sfuture%s delivery. This will use the default template specified under %sWooCommerce > Settings > Emails%s.', 'order-delivery-date' ), '<strong>', '</strong>', '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=email' ) ) . '">', '</a>' ); ?></p>
        <form method="POST">
            <table class="form-table">
                <tbody>
                    <tr valign="top" id="reminder_order_ids">
                        <th scope="row">
                            <label for="orddd_reminder_order_id"><?php _e( 'Order Ids', 'order-delivery-date' ); ?></label>
                        </th>
                        <td class="forminp">
                            <select id="orddd_reminder_order_id" name="orddd_reminder_order_id[]"  multiple="multiple" class="wc-enhanced-select" style="width:50%" >
                                <?php foreach( $order_ids as $id => $value ) { ?>
                                    <option value="<?php echo $value->ID ?>"><?php echo "#" . $value->ID ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>

                    <?php
                        $saved_subject = get_option( 'orddd_reminder_subject' );
                        if( isset( $saved_subject ) && '' != $saved_subject ) {
                            $email_subject = $saved_subject;
                        } else {
                            $email_subject = 'Delivery Reminder';
                        }
                    ?>

                    <tr valign="top">
                        <th scope="row">
                            <label for="orddd_reminder_subject"><?php _e( 'Subject', 'order-delivery-date' ); ?></label>
                        </th>
                        <td>
                            <input type="text" placeholder="<?php _e( 'Email subject', 'order-delivery-date' ); ?>" name="orddd_reminder_subject" id="orddd_reminder_subject" value="<?php echo $email_subject ?>" />
                        </td>
                    </tr>


                    <?php 
                        $saved_message = get_option( 'orddd_reminder_message' );
                        if( isset( $saved_message ) && '' != $saved_message ) {
                            $content = $saved_message;
                        } else {
                            $content = '
                                Hi {customer_first_name},

                                You have an upcoming delivery on {delivery_date} {delivery_time}. 

                                The details of your order are shown below. 
                                
                                {order_details}
                            ';
                        }
                            
                    ?>

                    <tr valign="top">
                        <th scope="row">
                            <label for="orddd_reminder_message"><?php _e( 'Message', 'order-delivery-date' ); ?></label>
                        </th>
                        <td>
                            <?php wp_editor( $content, 'orddd_reminder_message', array( 'textarea_name' => 'orddd_reminder_message' ) )?>
                            <span class="description"><?php _e( 'You can insert the following tags. They will be replaced dynamically' , 'order-delivery-date' ); ?>: <code>{order_date} {order_number} {customer_name} {customer_first_name} {customer_last_name} {delivery_date} {delivery_time} {order_details}</code></span>
                        </td>
                    </tr>
                
                    <tr valign="top">
                        <td>
                            <input type="submit" name="orddd_send_reminder" class="button-primary" value="<?php _e( 'Send Reminder', 'order-delivery-date' ); ?>" />
                            <?php wp_nonce_field( 'orddd_delivery_reminder' ); ?>
                        </td>

                        <td style="display:flex;">
                            <input type="button" id="orddd_save_message" name="orddd_save_message" class="button-primary" value="<?php _e( 'Save Draft', 'order-delivery-date' ); ?>" />
                            <div id="ajax_img" name="ajax_img" style="float:right; display:none;"> 
                                <img src="<?php echo plugins_url() . '/order-delivery-date/images/ajax-loader.gif'?>"> 
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    <div>
</section>
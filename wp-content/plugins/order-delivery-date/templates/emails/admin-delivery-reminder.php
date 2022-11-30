<?php
/**
 * Reminder Email for Admin
 */

do_action( 'woocommerce_email_header', $email_heading, $email );
$text_align = is_rtl() ? 'right' : 'left';
$tomorrow = date('j M, Y', strtotime('tomorrow') ); 
?>

<?php $pickup_locations = get_option( 'orddd_locations', true ); ?>
<?php $tomorrow_orders = orddd_get_tomorrows_orders(); ?>

<?php if( is_array( $tomorrow_orders) && count( $tomorrow_orders) > 0 ): ?>

    <p><?php _e( 'The details of your order for the date '.$tomorrow.' are shown below: ', 'order-delivery-date' ); ?></p>

    <div style="margin-bottom: 40px;">
        <table class="td" cellspacing="0" cellpadding="6"  width="100%" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
            <thead>
                <tr>
                    <th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Order', 'woocommerce' ); ?></th>
                    <th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Products', 'woocommerce' ); ?></th>
                    <th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Shipping Address', 'woocommerce' ); ?></th>
                    <th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Shipping Method', 'woocommerce' ); ?></th>
                    <th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Delivery Time', 'woocommerce' ); ?></th>
                    <?php if( is_array( $pickup_locations ) && count( $pickup_locations ) > 0 ) { ?>
                        <th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Pickup Location', 'woocommerce' ); ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php 
                    foreach( $tomorrow_orders as $key => $value ):
                        $the_order          = wc_get_order( $value->order_id );
                ?>
                        <tr>
                            <td class="td" scope="col"><a href="<?php echo $the_order->get_edit_order_url(); ?>"><?php echo $value->order_id ?></a></td>
                            <td  class="td" scope="col">
                                <?php 
                                    foreach( $value->product_name as $id => $data ) { ?>
                                        <?php echo $data['product'] . " x " . $data['quantity'] ?>
                                        <br>
                                <?php } ?>
                            </td>
                            <td class="td" scope="col"><?php echo $the_order->get_formatted_shipping_address($value->shipping_address) ?></td>
                            <td class="td" scope="col"><?php echo $value->shipping_method ?></td>
                            <td class="td" scope="col"><?php echo $value->delivery_time ?></td>
                            <?php if( is_array( $pickup_locations ) && count( $pickup_locations ) > 0 ) { ?>
                                <td class="td" scope="col"><?php echo $value->pickup_location ?></td>
                            <?php } ?>
                        </tr>

                    <?php endforeach; ?>

            </tbody>
            
        </table>
        
        <div style="margin-top:50px;">
            <p>To print the whole list of tomorrows orders - <a href="<?php echo admin_url().'admin.php?page=orddd_view_orders&download=orddd_data.print&eventType=order&orderType=wc-processing,wc-completed&start='.date('Y-m-d', strtotime('tomorrow') ).'&end='.date('Y-m-d', strtotime('tomorrow') ) ?>">Print</a></p>
        </div>
        
    </div>

<?php else: ?>
                
    <p><?php _e( 'There are no deliveries for tomorrow - '.$tomorrow.'.', 'order-delivery-date' ); ?></p>

<?php endif; ?>

<?php
do_action( 'woocommerce_email_footer', $email ); ?> 
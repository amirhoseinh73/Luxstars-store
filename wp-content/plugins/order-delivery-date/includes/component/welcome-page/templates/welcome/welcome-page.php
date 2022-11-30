<?php
/**
 * Welcome page on activate or updation of the plugin
 */
?>
<style>
    
    .wrap {
        font-size: 15px;
    }
</style>

<div class="wrap">
    
    <?php echo $get_welcome_header; ?>

    <div style="float:left;width: 80%;">
        <p style="margin-right:20px;font-size: 25px;"><?php
            if( 'yes' == get_option( 'orddd_pro_installed' ) ) {
                printf(
                    __( "Thank you for installing " . $plugin_name . "! As a first time user, welcome! You're well to accept deliveries with customer preferred delivery date." )
                );    
            } else {
                printf(
                    __( "Thank you for updating to the latest version of " . $plugin_name . "! Get ready to explore some exciting features in the recent updates." )
                );
            }
            
        ?>
        </p>
    </div>
    
    <div class="ts-badge"><img src="<?php echo $badge_url; ?>" style="width:150px;"/></div>
    
    <p>&nbsp;</p>
    <hr>

    <?php
    if( 'yes' == get_option( 'orddd_pro_installed' ) ) { ?>
        <div class="feature-section" style="height:375px">
            <h3><?php esc_html_e( "Get Started with " . $plugin_name, $plugin_context ); ?></h3>

            <div class="video feature-section-item" style="float:left;width:50%;">
                <img src="<?php echo $ts_dir_image_path . 'Enable_Delivery_Date.gif' ?>"
                     alt="<?php esc_attr_e( $plugin_name, $plugin_context ); ?>" style="">
            </div>

            <div class="content feature-section-item last-feature">
                <h3><?php esc_html_e( 'Enable Delivery Date Capture', $plugin_context ); ?></h3>
                <p style="font-size: 17px;"><?php esc_html_e( 'To start allowing customers to select their preferred delivery date, simply activate the Enable Delivery Date checkbox from under Order Delivery Date menu.', $plugin_context ); ?></p>
                <a href="admin.php?page=order_delivery_date" target="_blank" class="button-secondary">
                    <?php esc_html_e( 'Click Here to go to Order Delivery Date Settings page', $plugin_context ); ?>
                    <span class="dashicons dashicons-external"></span>
                </a>
            </div>
        </div>
    <?php } else { ?>
        <div class="feature-section" style="height:1000px">
            <h3><?php esc_html_e( "What's New?", $plugin_context ); ?></h3>

            <div class="feature-1" style="height: 570px;">
                <div class="video feature-section-item" style="float:left;width:50%;">
                    <img src="<?php echo $ts_dir_image_path . 'reminder-email.png' ?>"
                         alt="<?php esc_attr_e( $plugin_name, $plugin_context ); ?>" style="">
                </div>

                <div class="content feature-section-item last-feature">
                    <h3><?php esc_html_e( 'Customer Delivery Reminders', $plugin_context ); ?></h3>
                    <p style="font-size: 17px;"><?php esc_html_e( "You can now send a delivery reminder to the customers prior to some days. Reminders can be sent automatically before the X number of days set or manually for the deliverable orders.", $plugin_context ); ?></p>
                    <a href="admin.php?page=orddd_send_reminder_page" target="_blank" class="button-secondary">
                        <?php esc_html_e( 'Click Here to go to Order Delivery Date Send Reminder page', $plugin_context ); ?>
                        <span class="dashicons dashicons-external"></span>
                    </a>
                </div>
            </div>
            <div class="feature-1" style="">
                <div class="content feature-section-item last-feature" style="float:left;width:50%;" >
                    <h3><?php esc_html_e( 'Delivery Date Availability Calendar widget.' , $plugin_context ); ?></h3>
                    <p style="font-size: 17px;"><?php esc_html_e( "Availability of the Delivery Date & Time can now be shown to the customers before the checkout page using the 'Delivery Date Availability Calendar' widget.", $plugin_context ); ?></p>
                    <a href="widgets.php" target="_blank" class="button-secondary">
                        <?php esc_html_e( 'Click Here to go to Widgets page', $plugin_context ); ?>
                        <span class="dashicons dashicons-external"></span>
                    </a>
                </div>

                <div class="video feature-section-item" >
                    <img src="<?php echo $ts_dir_image_path . 'availability-calendar.png' ?>"
                         alt="<?php esc_attr_e( $plugin_name, $plugin_context ); ?>" style="">
                </div>
            </div>
        </div>
    <?php } ?>
    <!-- /.intro-section -->

    <p>&nbsp;</p>
    <hr>

    <h3>
        <?php if( 'yes' == get_option( 'orddd_pro_installed' ) ) { 
            esc_html_e( "Some exciting features of " . $plugin_name, $plugin_context ); 
        } else {
            esc_html_e( "Some other exciting features of " . $plugin_name, $plugin_context ); 
        }
        ?>
    </h3>

    <div class="content">
        <div class="feature-section clearfix introduction" style="height: 400px">
            <div class="video feature-section-item" style="float:left;width:50%;">
                <img src="<?php echo $ts_dir_image_path . 'Custom-Settings-small.gif'?>"
                     alt="<?php esc_attr_e( $plugin_name, $plugin_context ); ?>" style="width:500px;">
            </div>

            <div class="content feature-section-item last-feature" >
                <h3><?php esc_html_e( 'Create Custom Delivery Schedules', $plugin_context ); ?></h3>

                <p style="font-size: 17px;"><?php esc_html_e( 'The ability to set different delivery schedule for different WooCommerce shipping zones, shipping classes, product categories and pickup locations is very useful for the businesses like food packet deliveries, cake shops etc which deals with delivery in different shipping zones.', $plugin_context ); ?></p>

                <a href="https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/custom-delivery-settings/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDatePRoPlugin" target="_blank" class="button-secondary">
                    <?php esc_html_e( 'Learn More', $plugin_context ); ?>
                    <span class="dashicons dashicons-external"></span>
                </a>
            </div>
        </div>

        <div class="feature-section clearfix" style="height: 340px" >
            <div class="content feature-section-item" style="float:left;width:50%;">
                <h3><?php esc_html_e( 'Delivery Time along with Delivery Date', $plugin_context ); ?></h3>
                <p style="font-size: 17px;" ><?php esc_html_e( "The provision for allowing Delivery Time along with the Delivery Date on the checkout page makes the delivery more accurate. Delivering products on customer's preferred date and time improves your customers service.", $plugin_context ); ?></p>
                <a href="https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/setup-delivery-date-with-time/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateProPlugin" target="_blank" class="button-secondary">
                    <?php esc_html_e( 'Learn More', 'order-delivery-date-lite' ); ?>
                    <span class="dashicons dashicons-external"></span>
                </a>
            </div>

            <div class="content feature-section-item last-feature">
                <img src="<?php echo $ts_dir_image_path . 'Add_Time_Slot.gif'; ?>" 
                     alt="<?php esc_attr_e( $plugin_name, $plugin_context ); ?>" style="width:450px;">
            </div>
        </div>

        <div class="feature-section clearfix introduction" style="height: 275px" >
            <div class="video feature-section-item" style="float:left;width:50%">
                <img src="<?php echo $ts_dir_image_path . 'Google_Calendar_Sync.png'; ?>" alt="<?php esc_attr_e( $plugin_name, $plugin_context ); ?>" style="width:450px;">
            </div>

            <div class="content feature-section-item last-feature">
                <h3><?php esc_html_e( 'Synchronise Deliveries with Google Calendar', 'order-delivery-date-lite' ); ?></h3>
                <p style="font-size: 17px;"><?php esc_html_e( 'The ability to synchronise deliveries to the google calendar helps administrator or store manager to manage all the things in a single calendar.', $plugin_context ); ?></p>

                <a href="https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/synchronise-delivery-date-time-with-google-calendar/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateProPlugin" target="_blank" class="button-secondary">
                    <?php esc_html_e( 'Learn More', $plugin_context ); ?>
                    <span class="dashicons dashicons-external"></span>
                </a>
            </div>
        </div>

        <div class="feature-section clearfix" style="height: 500px" >
            <div class="content feature-section-item" style="float:left;width:50%;" >
                <h3><?php esc_html_e( 'Different delivery settings for each weekday', 'order-delivery-date-lite' ); ?></h3>
                <p style="font-size: 17px;"><?php esc_html_e( 'The Pro version of the plugin allows you to add different delivery settings like Same day cut-off time, Next Day cut-off time or Minimum Delivery Time for each weekday. It also allows you to add different delivery charges for different weekdays.', 'order-delivery-date-lite' ); ?></p>

                <a href="https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/weekday-settings/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateProPlugin" target="_blank" class="button-secondary">
                    <?php esc_html_e( 'Learn More', 'order-delivery-date-lite' ); ?>
                    <span class="dashicons dashicons-external"></span>
                </a>
            </div>

            <div class="content feature-section-item last-feature">
                <img src="<?php echo $ts_dir_image_path . 'weekday-settings.png'; ?>" alt="<?php esc_attr_e( 'Order Delivery Date for WooCommerce Lite', $plugin_context ); ?>" style="width:450px;">
            </div>
        </div>

        <div class="docs">
            <a href=https://www.tychesoftwares.com/docs/docs/order-delivery-date-pro-for-woocommerce/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateProPlugin" target="_blank" class="button-secondary">
                <?php esc_html_e( 'Documentation', $plugin_context ); ?>
                <span class="dashicons dashicons-external"></span>
            </a>

            <a href="https://www.tychesoftwares.com/woocommerce-order-delivery-date-delivery-time-plugin-changelog/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateProPlugin" target="_blank" class="button-secondary">
                <?php esc_html_e( 'Changelog', $plugin_context ); ?>
                <span class="dashicons dashicons-external"></span>
            </a>
        </div>
    </div>

    <div class="feature-section clearfix" >
        <div class="content feature-section-item" style="float:left;width:50%;" >
            <h3><?php esc_html_e( 'Getting to Know Tyche Softwares', 'woocommerce-ac' ); ?></h3>
            <ul class="ul-disc">
                <li><a href="https://tychesoftwares.com/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateProPlugin" target="_blank"><?php esc_html_e( 'Visit the Tyche Softwares Website', 'woocommerce-ac' ); ?></a></li>
                <li><a href="https://www.tychesoftwares.com/premium-woocommerce-plugins/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateProPlugin" target="_blank"><?php esc_html_e( 'View all Premium Plugins', 'woocommerce-ac' ); ?></a>
                <ul class="ul-disc">
                    <li><a href="https://www.tychesoftwares.com/store/premium-plugins/woocommerce-abandoned-cart-pro/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateProPlugin" target="_blank">Abandoned Cart Pro Plugin for WooCommerce</a></li>
                    <li><a href="https://www.tychesoftwares.com/store/premium-plugins/woocommerce-booking-plugin/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateProPlugin" target="_blank">Booking & Appointment Plugin for WooCommerce</a></li>
                    <li><a href="https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateProPlugin" target="_blank">Order Delivery Date for WooCommerce</a></li>
                    <li><a href="https://www.tychesoftwares.com/store/premium-plugins/product-delivery-date-pro-for-woocommerce/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateProPlugin" target="_blank">Product Delivery Date for WooCommerce</a></li>
                    <li><a href="https://www.tychesoftwares.com/store/premium-plugins/deposits-for-woocommerce/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateProPlugin" target="_blank">Deposits for WooCommerce</a></li>
                </ul>
                </li>
                <li><a href="https://tychesoftwares.com/about/?utm_source=wpaboutpage&utm_medium=link&utm_campaign=OrderDeliveryDateProPlugin" target="_blank"><?php esc_html_e( 'Meet the team', $plugin_context ); ?></a></li>
            </ul>

        </div>
        
        <div class="content feature-section-item">
            <h3><?php esc_html_e( 'Current Offers', $plugin_context ); ?></h3>
            <p>We do not have any offers going on right now</p>
        </div>
    </div>            
    <!-- /.feature-section -->
</div>
<?php

add_filter( 'woocommerce_locate_template', 'major_price_load_woocommerce_templates_from_plugin', 1, 3 );
function major_price_load_woocommerce_templates_from_plugin( $template, $template_name, $template_path ) {
     global $woocommerce;
     $_template = $template;
     
     if ( ! $template_path ) 
        $template_path = $woocommerce->template_url;

     $plugin_path  = untrailingslashit( plugin_dir_path( __FILE__ ) )  . '/template/woocommerce/';

    // Look within passed path within the theme - this is priority
    $template = locate_template(
    array(
      $template_path . $template_name,
      $template_name
    )
   );
   if( ! $template && file_exists( $plugin_path . $template_name ) )
    $template = $plugin_path . $template_name;
 
   if ( ! $template )
    $template = $_template;

   return $template;
}

function separate_price_number_with_comma( $price ) {
   if ( ! $price ) return 0;
   return number_format( $price, 0, "", "." );
}

add_action( "init", "user_is_wholesaler" );
function user_is_wholesaler() {
   if ( ! function_exists( "wp_get_current_user" ) ) {
      include( ABSPATH . "wp-includes/pluggable.php" );

      if ( ! function_exists( "wp_get_current_user" ) ) {
         return false;
      }
   }
   $userInfo = wp_get_current_user();
   if ( ! empty( $userInfo ) && ! empty( $userInfo->roles ) ) {
      $userRole = $userInfo->roles[ 0 ];
      if ( $userRole === "wholesaler" || $userRole === "administrator" ) return true;
   }

   return false;
}

function user_is_admin() {
   $userInfo = wp_get_current_user();
   if ( ! empty( $userInfo ) ) {
      $userRole = $userInfo->roles[ 0 ];
      if ( $userRole === "administrator" ) return true;
   }

   return false;
}

function returnResultAjaxJSON( $status, $message ) {
   return json_encode(
      array(
          "status" => $status,
          "message" => $message
      ),
      JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES
   );
}

function available_coupon_codes() {
   global $wpdb;

   // Get an array of all existing coupon codes
   $coupon_codes = $wpdb->get_col("SELECT post_name FROM $wpdb->posts WHERE post_type = 'shop_coupon' AND post_status = 'publish' ORDER BY post_name ASC");

   return $coupon_codes;
   // Display available coupon codes
   return implode(', ', $coupon_codes) ; // always use return in a shortcode
}
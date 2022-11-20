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
   $userInfo = userInfo();
   if ( ! empty( $userInfo ) && ! empty( $userInfo->roles ) ) {
      $userRole = $userInfo->roles[ 0 ];
      if ( $userRole === "wholesaler" || $userRole === "administrator" ) return true;
   }

   return false;
}

function user_is_admin() {
   $userInfo = userInfo();
   if ( ! empty( $userInfo ) ) {
      $userRole = $userInfo->roles[ 0 ];
      if ( $userRole === "administrator" ) return true;
   }

   return false;
}

function userInfo() {
   if ( ! function_exists( "wp_get_current_user" ) ) {
      include( ABSPATH . "wp-includes/pluggable.php" );

      if ( ! function_exists( "wp_get_current_user" ) ) {
         return null;
      }
   }
   return wp_get_current_user();
}
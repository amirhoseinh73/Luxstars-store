<?php

function randomColleagueCode( $length = 8 ) {
	return substr( str_shuffle( "ABCDEFHKLMNPRSTWYZ23456789" ), 0, $length );
}

add_filter( 'woocommerce_locate_template', 'wallet_load_woocommerce_templates_from_plugin', 1, 3 );
function wallet_load_woocommerce_templates_from_plugin( $template, $template_name, $template_path ) {
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

function getUserByMetaData( $key, $value ) {
   $args = array(
      'meta_query' => array(
         array(
            'key' => $key,
            'value' => $value,
            'compare' => '='
         )
      )
   );
   $members = get_users( $args );
   if ( ! isset( $members ) || empty( $members ) ) return new stdClass();

   return (object)$members[ 0 ];
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

   $coupon_codes = $wpdb->get_col("SELECT post_name FROM $wpdb->posts WHERE post_type = 'shop_coupon' AND post_status = 'publish' ORDER BY post_name ASC");

   return $coupon_codes;

   // $coupon_code = available_coupon_codes()[ 0 ];

   // $coupon = new WC_Coupon( $coupon_code );

   // $count = $coupon->get_usage_count();
   
   // $limit = $coupon->get_usage_limit_per_user();

   // WC()->cart->remove_coupon( $colleagueCode );
}
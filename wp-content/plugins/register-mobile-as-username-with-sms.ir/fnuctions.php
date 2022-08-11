<?php

function random_verficiraion_code( $length = 6 ) {
	return substr( str_shuffle( "1234567890" ), 0, $length );
}

add_filter( 'woocommerce_locate_template', 'woo_adon_plugin_template', 1, 3 );
function woo_adon_plugin_template( $template, $template_name, $template_path ) {
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

add_filter( 'woocommerce_checkout_fields' , 'bbloomer_change_address_input_type', 10, 1 );
 
function bbloomer_change_address_input_type( $fields ) {
$fields['billing']['billing_address_1']['type'] = 'textarea';
return $fields;
}
<?php

add_filter( 'woocommerce_locate_template', 'order_status_beautiful_load_woocommerce_templates_from_plugin', 1, 3 );
function order_status_beautiful_load_woocommerce_templates_from_plugin( $template, $template_name, $template_path ) {
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

add_action('wp_enqueue_scripts', 'amh_nj_load_script_order_status');
function amh_nj_load_script_order_status() {
    wp_enqueue_style( "amh-nj-order-status-beautiful-style", AMHNJ_ORDER_STATUS_PLUGIN_DIR_URL . "/assets/css/style.css" );
    wp_enqueue_style( "amh-nj-order-status-beautiful-fontawsome", AMHNJ_ORDER_STATUS_PLUGIN_DIR_URL . "/assets/fonts/fontawsome/all.min.css" );
}
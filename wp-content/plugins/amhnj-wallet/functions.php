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

if ( function_exists( "user_is_wholesaler" ) && ! user_is_wholesaler() ) {
   add_action( "woocommerce_before_checkout_form", "active_colleague_code" );
   add_action( "woocommerce_after_checkout_billing_form", "active_colleague_code_hidden" );
}

function active_colleague_code() {
   echo "<section class='row mx-0 my-4 woocommerce-info'>
      <div class='col-auto pr-0'><p class='text-dark fw-500'>در صورتی که کد معرف دارید، وارد کنید.</p></div>
      <div class='col-auto'><input id='amhnj_colleague_code_customer' type='text' class='form-control form-control-sm' /></div>
   </section>";
}

function active_colleague_code_hidden() {
   echo "<input id='amhnj_colleague_code_customer_hidden' name='amhnj_colleague_code_customer_hidden' type='hidden' value='' />";
   echo "<script type='text/javascript'>fillHiddenInputByShowedInput()</script>";
}

add_action( 'woocommerce_checkout_update_order_meta', 'submitColleagueCodeInOrder', 10, 2 );
function submitColleagueCodeInOrder( $order_id, $posted ) {
   if ( isset( $_POST[ "amhnj_colleague_code_customer_hidden" ] ) && ! empty( $_POST[ "amhnj_colleague_code_customer_hidden" ] ) ) {
      $order = wc_get_order( $order_id );
      $order->update_meta_data( 'colleague_code', $_POST[ "amhnj_colleague_code_customer_hidden" ] );
      $order->save();

      // wp_get_user
   }
}
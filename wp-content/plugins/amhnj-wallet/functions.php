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

function handleMenuItemWallet() {
   add_filter( 'woocommerce_account_menu_items', 'addMenuItemWallet', 99, 1 );

   function addMenuItemWallet( $items ) {
      $my_items = array(
         'wallet' => __( 'کیف پول', 'amhnj_wallet' ),
      );
   
      $my_items = array_slice( $items, 0, 1, true ) +
            array_slice( $items, 1, count( $items ) - 2, true ) +
            $my_items
            +
            array_slice( $items, count( $items ) - 2, count( $items ), true );
   
      return $my_items;
   }
   add_action( 'init', function() {
      add_rewrite_endpoint( 'wallet', EP_ROOT | EP_PAGES );
   } );

   add_action( 'woocommerce_account_wallet_endpoint', function() {
      wc_get_template_part('myaccount/wallet');
   });
}

handleMenuItemWallet();

function handleMenuItemColleague() {
   add_filter( 'woocommerce_account_menu_items', 'addMenuItemColleague', 99, 1 );

   function addMenuItemColleague( $items ) {
      $my_items = array(
         'colleague' => __( 'همکاری در فروش', 'amhnj_wallet' ),
      );
   
      $my_items = array_slice( $items, 0, 1, true ) +
            array_slice( $items, 1, count( $items ) - 2, true ) +
            $my_items
            +
            array_slice( $items, count( $items ) - 2, count( $items ), true );
   
      return $my_items;
   }

   add_action( 'init', function() {
      add_rewrite_endpoint( 'colleague', EP_ROOT | EP_PAGES );
   } );

   add_action( 'woocommerce_account_colleague_endpoint', function() {
      wc_get_template_part('myaccount/colleague');
   });
}

if ( function_exists( "user_is_wholesaler" ) ) {
   var_dump( user_is_wholesaler() );
   if ( user_is_wholesaler() ) {
      handleMenuItemColleague();
   }
}
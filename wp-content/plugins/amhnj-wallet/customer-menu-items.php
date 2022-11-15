<?php
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
      require_once AMHNJ_WALLET_PLUGIN_DIR_PATH . '/template/woocommerce/myaccount/wallet.php';
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
      require_once AMHNJ_WALLET_PLUGIN_DIR_PATH . '/template/woocommerce/myaccount/colleague.php';
   });
}

if ( function_exists( "user_is_wholesaler" ) ) {
   if ( user_is_wholesaler() ) {
      handleMenuItemColleague();
   }
}
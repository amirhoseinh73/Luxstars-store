<?php
/**
 * Order Delivery Date Pro for WooCommerce
 *
 * Lang file to define some strings used all over the plugin.
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Pro-for-WooCommerce/Lang
 * @since       2.6.2
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$orddd_lang = 'en';
$orddd_translations = array(
	'en' => array(
		'common.date-settings'     => "Date Settings", 
		'common.time-settings'     => "Time Settings",
		'common.holidays'		   => "Holidays",
		'common.appearance'		   => "Appearance",
		'common.delivery-dates'    => "Delivery Dates",
		'common.time-slot'		   => "Time Slots",
		'common.show_all_dates'	   => "Show all Delivery Dates"
	),
);
		
global $orddd_translations, $orddd_lang;

/**
 * Returns the label name of the setting accessed.
 *  
 * @param string $str - Array Key of the setting whose label is to be returned
 * @return string - Returns the label for the setting name.
 * @since 2.6.2
 */
function orddd_t( $str ) {
	global $orddd_translations, $orddd_lang;
	return $orddd_translations[ $orddd_lang ][ $str ];
}
?>
<?php
/**
 * Single Product Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

$retail_price = $product->get_price();
$retail_price = separate_price_number_with_comma( $retail_price );
$productPrice = $product->get_price_html();
if ( user_is_wholesaler() ) {
	$prices = major_prices();
	$_cash = separate_price_number_with_comma( $prices->_cash );
	$_45 = separate_price_number_with_comma( $prices->_45 );
	$_90 = separate_price_number_with_comma( $prices->_90 );

	$productPrice = "
	<span class='woocommerce-Price-amount amount'>
		<bdi>
			پرداخت نقدی:
			<abbr>$_cash</abbr>
			<span class='woocommerce-Price-currencySymbol'>تومان</span>
		</bdi>
	</span>
	<span class='woocommerce-Price-amount amount'>
		<bdi>
			پرداخت 45 روزه:
			<abbr>$_45</abbr>
			<span class='woocommerce-Price-currencySymbol'>تومان</span>
		</bdi>
	</span>
	<span class='woocommerce-Price-amount amount'>
		<bdi>
			پرداخت 90 روزه:
			<abbr>$_90</abbr>
			<span class='woocommerce-Price-currencySymbol'>تومان</span>
		</bdi>
	</span>
	";

	if ( user_is_admin() ) {
		$productPrice .= "
		<span class='woocommerce-Price-amount amount'>
			<bdi>
				قیمت فروش جزئی:
				<abbr>$retail_price</abbr>
				<span class='woocommerce-Price-currencySymbol'>تومان</span>
			</bdi>
		</span>";
		}
}
?>
<p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?>">
	<?= $productPrice; ?>
</p>

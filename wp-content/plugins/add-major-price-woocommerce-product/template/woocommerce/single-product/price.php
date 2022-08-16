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

$productPrice = $product->get_price_html();
$userInfo = wp_get_current_user();
if ( ! empty( $userInfo ) ) {
	$userRole = $userInfo->roles[ 0 ];
	if ( $userRole === "wholesaler" || $userRole === "administrator" ) {
		$prices = major_prices();
		$_cash = $prices->_cash;
		$_45 = $prices->_45;
		$_90 = $prices->_90;

		$productPrice = "
		<span class='woocommerce-Price-amount amount'>
			<bdi>
				پرداخت نقدی: $_cash
				<span class='woocommerce-Price-currencySymbol'>تومان</span>
			</bdi>
		</span>
		<span class='woocommerce-Price-amount amount'>
			<bdi>
				پرداخت 45 روزه: $_45
				<span class='woocommerce-Price-currencySymbol'>تومان</span>
			</bdi>
		</span>
		پرداخت 90 روزه:
		<span class='woocommerce-Price-amount amount'>
			<bdi>
				$_90
				<span class='woocommerce-Price-currencySymbol'>تومان</span>
			</bdi>
		</span>
		";

		if ( $userRole === "administrator" ) {
			add_filter( 'woocommerce_get_price_html', 'change_price_html', 100, 2 );
			function change_price_html( $price, $product ){
				return 'قیمت فروش جزئی' . $price;
			}
			$productPrice .= $product->get_price_html();
		}
	}
}
?>
<p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?>">
	<?= $productPrice; ?>
</p>

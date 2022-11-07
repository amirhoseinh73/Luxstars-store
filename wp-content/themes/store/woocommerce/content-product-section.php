<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<section class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-25 px-0">
    <section class="pro-card">
        <a href="<?= get_the_permalink(); ?>" class="blog-link-absolute"></a>
        <div class="pro-img">
            <?php if ($product->is_on_sale()):?>
                <img src="<?= get_template_directory_uri( "img/special.png" );?>" class="img-special-pro" alt="">
            <?php endif;?>
            <?php the_post_thumbnail('full', ['class' => '', 'alt' => get_the_title()]); ?>
        </div>
        <div class="pro-content">
            <div class="pro-title">
                <div class="row">
                    <div class="col-6 text-center my-auto pl-0">
                        <h1 class="font-13 fw500 text-boot-1 pr-2 mb-0"><?php the_title(); ?></h1>
                    </div>
                    <div class="col-6 my-auto pr-0">
                        <p class="font-14 fw600 pro-price mb-0 text-boot-3">
                            <?= $product->get_price_html(); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</section>
<?php
global $product;
?>
<section class="pro-card card-width-1">
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
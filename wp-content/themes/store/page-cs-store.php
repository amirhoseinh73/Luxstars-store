<?php /*Template Name: cs-store*/?>
<?php get_header(); ?>
<?php if (have_posts()) :
    while (have_posts()) :
        the_post(); ?>
        <header id="header" class="container-fluid position-relative">
            <div class="row">
                <?php get_template_part( "bread", "crumb-html" )?>
            </div>
        </header>
        <main class="page-content">
        <section class="mb-boot-45 mt-boot-22">
            <div class="container">
                <section class="pt-boot-45 pb-boot-45">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <h4 class="fw600 font-20 text-boot text-right tit-L-before">
                                    <?php the_title()?>
                                </h4>
                                <div class="row mt-boot-30">

                                    <?php
                                    $params_product = array('posts_per_page' => 6, 'post_type' => 'product');
                                    $last_products = new WP_Query($params_product);
                                    ?>
                                    <?php if ($last_products->have_posts()) : ?>
                                        <?php while ($last_products->have_posts()) :
                                            $last_products->the_post();
                                            $product = get_product(get_the_ID());
                                            ?>
                                            <blockquote class="pro-card card-width-1">
                                                <a href="<?php echo get_the_permalink(); ?>" class="blog-link-absolute"></a>
                                                <div class="pro-img">
                                                    <?php if ($product->is_on_sale()):?>
                                                        <img src="<?php echo get_template_directory_uri().'/img/special.png';?>" class="img-special-pro" alt="">
                                                    <?php endif;?>
                                                    <?php the_post_thumbnail('medium', ['class' => 'object-fit-cover', 'alt' => get_the_title()]); ?>
                                                </div>
                                                <div class="pro-content">
                                                    <div class="pro-title">
                                                        <div class="row">
                                                            <div class="col-12 my-auto pl-0 text-center">
                                                                <h1 class="font-13 fw500 text-39 pr-2 mb-0"><?php the_title(); ?></h1>
                                                            </div>
                                                            <div class="col-6 my-auto pr-0">
                                                                <p class="font-14 fw600 pro-price mb-0 text-boot-d">
                                                                    <?php
                                                                    echo $product->get_price_html();
                                                                    ?>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </blockquote>
                                        <?php endwhile; ?>
                                        <?php wp_reset_postdata(); ?>
                                    <?php else:  ?>
                                        <p>
                                            <?php _e( 'محصولی یافت نشد'); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </section>
    <?php endwhile; endif; ?>
    <section id="last_blog_index" class="pt-boot-45">
        <div class="container">
            <?php get_template_part('last-blog-title'); ?><?php get_template_part('last-blog'); ?>

            <?php get_template_part('last-blog-link'); ?>
        </div>
    </section>
    </main>
<?php get_footer(); ?>
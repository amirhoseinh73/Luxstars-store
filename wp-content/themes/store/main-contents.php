<main id="main_index">
    <section id="search_index" class="pt-boot-45 pb-boot-45">
        <div class="container">
            <div class="row align-items-end">
                <div class="col-12 mb-boot-12">
                    <h6 class="text-boot-4 fw600 font-20 tit-L-before">جستجوی پیشرفته محصول</h6>
                </div>
                <div class="col-12 my-auto text-md-right text-center">
                    <?php echo do_shortcode('[fibosearch]'); ?>
                </div>
            </div>
        </div>
    </section>
    <section id="service_index" class="mt-boot-45">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-6 col-lg-3 conts-parent mb-boot-15 px-2">
                    <div class="conts">
                        <a href="<?= home_url( "product-category/کاربرد/" ); ?>">
                            <div class="content-one img-thumbnail">
                                <span class="cont-icon-rotate"><i class="<?php echo get_theme_mod( 'service_1_icon', 'fas fa-cogs fa-3x'); ?>"></i></span>
                                <div class="caption p-2">
                                    <h1 class="font-19 fw500 text-center text-boot-4"><?php echo get_theme_mod( 'service_1_title', __('Title')); ?></h1>
                                    <p class="font-13 fw400 text-justify lh-18 text-secondary">
                                        <?php echo get_theme_mod( 'service_1_text', __('Text')); ?>
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3 conts-parent mb-boot-15 px-2">
                    <div class="conts">
                        <a href="<?= home_url( "product-category/جنس/" ); ?>">
                            <div class="content-one img-thumbnail">
                                <span class="cont-icon-rotate"><i class="<?php echo get_theme_mod( 'service_2_icon', 'fas fa-cogs fa-3x'); ?>"></i></span>
                                <div class="caption p-2">
                                    <h1 class="font-19 fw500 text-center text-boot-4"><?php echo get_theme_mod( 'service_2_title', __('Title')); ?></h1>
                                    <p class="font-13 fw400 text-justify lh-18 text-secondary">
                                        <?php echo get_theme_mod( 'service_2_text', __('Text')); ?>
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3 conts-parent mb-boot-15 px-2">
                    <div class="conts">
                        <a href="<?= home_url( "product-category/رنگ/" ); ?>">
                            <div class="content-one img-thumbnail">
                                <span class="cont-icon-rotate"><i class="<?php echo get_theme_mod( 'service_3_icon', 'fas fa-cogs fa-3x'); ?>"></i></span>
                                <div class="caption p-2">
                                    <h1 class="font-19 fw500 text-center text-boot-4"><?php echo get_theme_mod( 'service_3_title', __('Title')); ?></h1>
                                    <p class="font-13 fw400 text-justify lh-18 text-secondary">
                                        <?php echo get_theme_mod( 'service_3_text', __('Text')); ?>
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3 conts-parent mb-boot-15 px-2">
                    <div class="conts">
                        <a href="<?= home_url( "product-category/برند/" ); ?>">
                            <div class="content-one img-thumbnail">
                                <span class="cont-icon-rotate"><i class="<?php echo get_theme_mod( 'service_4_icon', 'fas fa-cogs fa-3x'); ?>"></i></span>
                                <div class="caption p-2">
                                    <h1 class="font-19 fw500 text-center text-boot-4"><?php echo get_theme_mod( 'service_4_title', __('Title')); ?></h1>
                                    <p class="font-13 fw400 text-justify lh-18 text-secondary">
                                        <?php echo get_theme_mod( 'service_4_text', __('Text')); ?>
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="last_products" class="pt-boot-22 mt-boot-30 pb-boot-45">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="fw600 font-20 text-boot text-right tit-L-before">
                        جدید ترین محصولات
                    </h1>
                    <p class="text-boot-4 font-15 fw400">لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ</p>
                    <div class="row mt-boot-30">
                        <?php
                        $params_product = array('posts_per_page' => 8, 'post_type' => 'product');
                        $last_products = new WP_Query($params_product);
                        if ($last_products->have_posts()) :
                            while ( $last_products->have_posts() ) :
                                $last_products->the_post();
                                $product = wc_get_product( get_the_ID() );
                                get_template_part( "./woocommerce/content", "product-section", $product );
                            endwhile;
                            wp_reset_postdata();
                        else:
                            echo "<p>"
                                . _e( 'محصولی یافت نشد') .
                            "</p>";
                        endif;
                        ?>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <?php if( is_user_logged_in() ) : ?>
        <section id="special_products" class="pt-boot-22 mt-boot-30 pb-boot-45">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1 class="fw600 font-20 text-boot text-right tit-L-before">
                            آخرین خرید های شما
                        </h1>
                        <p class="text-boot-4 font-15 fw400">لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ</p>
                        <div class="row mt-boot-30">
                        <?php
                            $params_product = array('posts_per_page' => 4, 'post_type' => 'product');
                            $last_products = new WP_Query($params_product);
                            if ($last_products->have_posts()) :
                                while ( $last_products->have_posts() ) :
                                    $last_products->the_post();
                                    $product = wc_get_product( get_the_ID() );
                                    get_template_part( "./woocommerce/content", "product-section", $product );
                                endwhile;
                                wp_reset_postdata();
                            else:
                                echo "<p>"
                                    . _e( 'محصولی یافت نشد') .
                                "</p>";
                            endif;
                        ?>
                        
                        </div>
                    </div>
                </div>

                <div class="row mt-boot-45">
                    <div class="col-12">
                        <h1 class="fw600 font-20 text-boot-5 text-right tit-L-before">
                            پیشنهاد های ویژه
                        </h1>
                        <p class="text-boot-4 font-15 fw400">لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ</p>
                        <div class="row mt-boot-30">

                        <?php
                            $params_product = array('posts_per_page' => 4, 'post_type' => 'product');
                            $last_products = new WP_Query($params_product);
                            if ($last_products->have_posts()) :
                                while ( $last_products->have_posts() ) :
                                    $last_products->the_post();
                                    $product = wc_get_product( get_the_ID() );
                                    get_template_part( "./woocommerce/content", "product-section", $product );
                                endwhile;
                                wp_reset_postdata();
                            else:
                                echo "<p>"
                                    . _e( 'محصولی یافت نشد') .
                                "</p>";
                            endif;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section id="advice_index" class="pt-boot-45 pb-boot-45">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-6 my-auto text-md-right text-center">
                    <h6 class="text-boot-4 font-27 fw500 position-relative">نیاز به مشاوره دارید...؟</h6>
                    <p class="font-14 text-boot-5 fw400">
                        شما با چالش هایی برای توسعه کسب و کار خود روبرو هستید.
                    </p>
                </div>
                <div class="col-12 col-md-6 my-md-auto mt-3 text-md-left text-center">
                    <a href="<?php echo home_url(); ?>/contact-us/"
                       class="rounded-pill btn-boot-3 d-inline-block px-3 py-2 fw400 font-14 position-relative amh_nj-hover-2">
                        درخواست مشاوره
                        <span class="amh_nj-hover-2__inner">
                          <span class="amh_nj-hover-2__blobs">
                            <span class="amh_nj-hover-2__blob"></span>
                            <span class="amh_nj-hover-2__blob"></span>
                            <span class="amh_nj-hover-2__blob"></span>
                            <span class="amh_nj-hover-2__blob"></span>
                          </span>
                        </span>
                        <span class="amh_nj-hover-2-svg">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1">
                            <defs>
                                <filter id="goo">
                                    <feGaussianBlur in="SourceGraphic" result="blur" stdDeviation="10"></feGaussianBlur>
                                    <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0 0 1 0 0 0 0 0 1 0 0 0 0 0 21 -7" result="goo"></feColorMatrix>
                                    <feBlend in2="goo" in="SourceGraphic" result="mix"></feBlend>
                                </filter>
                            </defs>
                        </svg>
                        </span>
                    </a>
                    <a class="bg-transparent border-boot border-color-3 rounded-pill text-boot-3 d-inline-block px-3 py-2 fw400 font-14 amh_nj-hover-2">
                        031-12345678
                        <span class="amh_nj-hover-2__inner">
                          <span class="amh_nj-hover-2__blobs">
                            <span class="amh_nj-hover-2__blob"></span>
                            <span class="amh_nj-hover-2__blob"></span>
                            <span class="amh_nj-hover-2__blob"></span>
                            <span class="amh_nj-hover-2__blob"></span>
                          </span>
                        </span>
                        <span class="amh_nj-hover-2-svg">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1">
                            <defs>
                                <filter id="goo">
                                    <feGaussianBlur in="SourceGraphic" result="blur" stdDeviation="10"></feGaussianBlur>
                                    <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0 0 1 0 0 0 0 0 1 0 0 0 0 0 21 -7" result="goo"></feColorMatrix>
                                    <feBlend in2="goo" in="SourceGraphic" result="mix"></feBlend>
                                </filter>
                            </defs>
                        </svg>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="about_index_2" class="mb-boot-30 mt-boot-30">
        <div class="container">
            <div class="row flex-row-reverse">
                <div class="col-12 col-sm-6 col-lg-4 my-auto img-about-shape-2-p">
                    <img src="<?php echo get_theme_mod( 'about_2_img', get_template_directory_uri().'/img/about.jpg'); ?>" class="img-fluid img-about-shape-2" alt="">
                </div>
                <div class="col-12 col-lg-8 my-auto">
                    <div class="about-contain">
                        <h3 class="text-boot-5 fw600 font-20 tit-L-before">
                            <?php echo get_theme_mod( 'about_2_title_1', __('Title')); ?>
                        </h3>
                        <p class="text-boot-4 font-15 fw400">
                            <?php echo get_theme_mod( 'about_2_title_2', __('Title')); ?>
                        </p>
                        <p class="text-boot-sec font-13 text-justify fw400 lh-2">
                            <?php echo get_theme_mod( 'about_2_text', __('Text')); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="last_blog_index" class="pt-boot-45">
        <div class="container">
            <?php get_template_part('last-blog-title'); ?>
            <?php get_template_part('last-blog'); ?>
            <?php get_template_part('last-blog-link'); ?>
        </div>
    </section>
	<section id="logos" class="bg-white pt-boot-22 pb-boot-30">
        <div class="container">
            <div class="row">
                <div class="col-12 my-auto text-center">
                    <h4 class="fw500 text-boot-5 font-27">
                        <?php echo get_theme_mod( 'company_title_1', __('Title')); ?>
                    </h4>
                    <p class="font-13 text-black-amh_nj fw400 text-boot-4">
                        <?php echo get_theme_mod( 'company_title_2', 'customers of company'); ?>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-12 pt-boot-25">
                    <!-- Swiper -->
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            <?php if (get_theme_mod('company_img_1_display', 'show') == 'show') : ?>
                            <div class="swiper-slide">
                                <img src="<?php echo get_theme_mod( 'company_img_1', get_template_directory_uri().'/img/logos1.png'); ?>"
                                     class="img-fluid d-block w-75 mx-auto" alt="">
                            </div>
                            <?php endif;?>
                            <?php if (get_theme_mod('company_img_2_display', 'show') == 'show') : ?>
                            <div class="swiper-slide">
                                <img src="<?php echo get_theme_mod( 'company_img_2', get_template_directory_uri().'/img/logos2.png'); ?>"
                                     class="img-fluid d-block w-75 mx-auto" alt="">
                            </div>
                            <?php endif;?>
                            <?php if (get_theme_mod('company_img_3_display', 'show') == 'show') : ?>
                            <div class="swiper-slide">
                                <img src="<?php echo get_theme_mod( 'company_img_3', get_template_directory_uri().'/img/logos3.png'); ?>"
                                     class="img-fluid d-block w-75 mx-auto" alt="">
                            </div>
                            <?php endif;?>
                            <?php if (get_theme_mod('company_img_4_display', 'show') == 'show') : ?>
                            <div class="swiper-slide">
                                <img src="<?php echo get_theme_mod( 'company_img_4', get_template_directory_uri().'/img/logos4.png'); ?>"
                                     class="img-fluid d-block w-75 mx-auto" alt="">
                            </div>
                            <?php endif;?>
                            <?php if (get_theme_mod('company_img_5_display', 'show') == 'show') : ?>
                            <div class="swiper-slide">
                                <img src="<?php echo get_theme_mod( 'company_img_5', get_template_directory_uri().'/img/logos5.png'); ?>"
                                     class="img-fluid d-block w-75 mx-auto" alt="">
                            </div>
                            <?php endif;?>
                            <?php if (get_theme_mod('company_img_6_display', 'show') == 'show') : ?>
                            <div class="swiper-slide">
                                <img src="<?php echo get_theme_mod( 'company_img_6', get_template_directory_uri().'/img/logos6.png'); ?>"
                                     class="img-fluid d-block w-75 mx-auto" alt="">
                            </div>
                            <?php endif;?>
                            <?php if (get_theme_mod('company_img_7_display', 'show') == 'show') : ?>
                            <div class="swiper-slide">
                                <img src="<?php echo get_theme_mod( 'company_img_7', get_template_directory_uri().'/img/logos7.png'); ?>"
                                     class="img-fluid d-block w-75 mx-auto" alt="">
                            </div>
                            <?php endif;?>
                            <?php if (get_theme_mod('company_img_8_display', 'show') == 'show') : ?>
                            <div class="swiper-slide">
                                <img src="<?php echo get_theme_mod( 'company_img_8', get_template_directory_uri().'/img/logos8.png'); ?>"
                                     class="img-fluid d-block w-75 mx-auto" alt="">
                            </div>
                            <?php endif;?>
                            <?php if (get_theme_mod('company_img_9_display', 'show') == 'show') : ?>
                            <div class="swiper-slide">
                                <img src="<?php echo get_theme_mod( 'company_img_9', get_template_directory_uri().'/img/logos9.png'); ?>"
                                     class="img-fluid d-block w-75 mx-auto" alt="">
                            </div>
                            <?php endif;?>
                            <?php if (get_theme_mod('company_img_10_display', 'show') == 'show') : ?>
                            <div class="swiper-slide">
                                <img src="<?php echo get_theme_mod( 'company_img_10', get_template_directory_uri().'/img/logos10.png'); ?>"
                                     class="img-fluid d-block w-75 mx-auto" alt="">
                            </div>
                            <?php endif;?>
                        </div>

                    </div>
                    <div id="nex_logos" class="swiper-button-next"></div>
                    <div id="prev_logos" class="swiper-button-prev"></div>
                </div>
            </div>
        </div>
    </section>
</main>
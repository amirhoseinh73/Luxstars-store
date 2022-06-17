<?php /* Template Name: contact us */ ?>
<?php get_header();?>
<?php
if (have_posts()) :
while (have_posts()) :
the_post();
?>
<header id="header" class="container-fluid position-relative">
    <div class="row">
        <div class="col-12 px-0">
            <img src="<?php echo get_template_directory_uri();?>/img/footer-img.jpg" class="img-header" alt="">
            <div class="header-caption">
                <h1 class="fw600"><?php echo get_the_title() ?></h1>
                <p class="fw400 font-15">
                    <?php the_content_rss('', true, '', '10'); ?>
                </p>
                <div class="row">
                    <div class="col-12">
                        <div class="amh_nj-breadcrumb">
                            <a class="font-14">
                                <i class="far fa-angle-double-left pl-1 pr-1 align-middle font-13"></i>
                            </a>
                            <a href="<?php echo home_url() ?>" class="amh_nj-breadcrumb-link font-13">خانه</a>
                            <i class="far fa-angle-left pl-1 align-middle font-15 fw300"></i>
                            <a class="amh_nj-breadcrumb-text font-12"><?php echo get_the_title() ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<main class="page-content">
    <section class="mb-boot-45 mt-boot-22">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-6 my-auto text-md-right text-center">
                    <h1 class="text-dark font-27 fw500 position-relative tit-L-before"> با ما تماس بگیرید</h1>
                </div>
            </div>
            <?php echo do_shortcode('[contact-form-7 id="5" title="contact form"]')?>
            <div class="row">
                <div class="col-12">
                    <div class="fw400 font-14 text-justify lh-2">
                       <?php the_content();?>
                    </div>
                </div>
            </div>

            <div id="contact_address" class="row mt-boot-5 flex-row-reverse">
                <div class="col-12 col-md-5 my-auto">
                    <?php
                    if (has_post_thumbnail()):
                        the_post_thumbnail('full', ['class' => 'img-fluid', 'alt' => get_the_title()]);
                    endif;
                    ?>
                </div>
                <div class="col-12 col-md-7 my-auto">
                    <div class="row mx-0 mx-md-n3">
                        <div class="col-12 px-0 py-5">
                            <div class="row mx-0">
                                <div class="col-1 my-auto">
                                    <embed src="<?php echo get_template_directory_uri();?>/img/svg-contact-3.svg" class="img-embed">
                                </div>
                                <div class="col-11 my-auto">
                                    <p>اصفهان، خیابان شمس آبادی، نبش گوچه جهان آرا، ساختمان آلا، طبقه دوم، واحد 204</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 px-0 col-sm-6">
                            <div class="row mx-0">
                                <div class="col-2 my-auto">
                                    <embed src="<?php echo get_template_directory_uri();?>/img/svg-contact-2.svg" class="img-embed">
                                </div>
                                <div class="col-10 my-auto">
                                    <p class="lh-25 text-uppercase">
                                        دپارتمان بازرگانی و فروش :
                                        <br>
                                        <?php echo get_theme_mod( 'footer_email_2', 'sale@amirhoseinhasani.ir'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 px-0 col-sm-6">
                            <div class="row mx-0">
                                <div class="col-2 my-auto">
                                    <embed src="<?php echo get_template_directory_uri();?>/img/svg-contact-1.svg" class="img-embed">
                                </div>
                                <div class="col-10 my-auto">
                                    <p class="lh-25">
                                        <?php echo get_theme_mod( 'footer_mobile', '09123456789'); ?>
                                        <br>
                                        <?php echo get_theme_mod( 'footer_tell', '031-31234567'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-boot-22">
                <div class="col-12">
                    <div class="h-b-300 ow-hd rounded-20">
                    <?= do_shortcode('[neshan-map id="1"]')?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="last_blog_index" class="pt-boot-45">
        <div class="container">
            <?php get_template_part('last-blog-title'); ?>
            <?php get_template_part('last-blog'); ?>
        </div>
    </section>
</main>
<?php
endwhile;
endif;
?>
<?php get_footer();?>
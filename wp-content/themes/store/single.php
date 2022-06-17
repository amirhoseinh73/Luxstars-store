<?php get_header(); ?>
<?php
if (have_posts()) :
    while (have_posts()) :
        the_post();

        gt_set_post_view();
        ?>
        <header id="header" class="container-fluid position-relative">
            <div class="row">
                <div class="col-12 px-0">
                    <?php the_post_thumbnail('full', ['class' => 'img-header', 'alt' => get_the_title()]) ?>
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
                                    <a href="<?php echo home_url() ?>/blog/" class="amh_nj-breadcrumb-link font-13">وبلاگ</a>
                                    <i class="far fa-angle-left pl-1 align-middle font-15 fw300"></i>
                                    <a class="amh_nj-breadcrumb-text font-12"><?php echo get_the_title() ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <main>
        <section class="mb-boot-45 mt-boot-22">
            <div class="container">
                <div class="row">
                    <div class="col-12 single-blog-titr">
                        <h1><?php echo get_the_title(); ?></h1>
                    </div>
                </div>
                <div class="row font-12 single-blog-opt-1 mx-0">
                    <div class="col-4 col-md-4 col-lg-2 px-0 my-auto text-lg-right text-center">
                        <span><?php echo get_the_date(); ?></span>
                    </div>
                    <div class="col-4 col-md-4 col-lg-2 my-auto text-lg-right text-center">
                        <span>توسط <?php echo get_author_name(); ?></span>
                    </div>
                    <div class="col-4 col-md-4 col-lg-2 my-auto text-lg-right text-center">
                        <span><?php echo gt_get_post_view(); ?> یازدید</span>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2 text-lg-right text-md-center text-right my-md-auto mt-3">
                        <span><?php echo get_comments_number(); ?> دیدکاه</span>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3 col-xl-2 px-0 my-md-auto mt-3 text-left mr-auto lh-2 text-lg-right text-center">
                        امتیاز
                        <?php echo do_shortcode('[ratemypost-result]'); ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <?php
                        if (has_post_thumbnail()):
                            the_post_thumbnail('full', ['class' => 'img-blog-single', 'alt' => get_the_title()]);
                        endif;
                        ?>
                    </div>
                    <div class="col-12 page-content">
                        <div class="lh-2 text-justify fw400 font-13 text-44">
                            <?php
                            the_content();
                            ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="single-blog-opt-2 font-12">
                            <div class="after2p d-inline-block text-44 fw500 font-14 ml-2">برچسب ها</div>
                            <div class="before-sharp text-right">
                                <a>
                                    <?php if (has_tag()) {
                                        echo get_the_tag_list('', '</a></div><div class="before-sharp text-right"><a>', '</a></div>');
                                    } else {
                                        echo '</a></div>';
                                    }

                                    ?>

                            </div>
                        </div>
                        <div class="col-12">
                            <div class="row single-blog-opt-3">
                                <div class="col-12 col-sm-4 col-md-4 text-center text-sm-right text-44 fw500 font-14 my-sm-auto mb-3">
                                    اشتراک گذاری مطلب:
                                </div>
                                <div class="col-12 col-sm-8 col-md-8 text-sm-left text-center my-auto">
                                    <a href="https://twitter.com/share?url=<?php echo get_the_permalink(); ?>&amp;text=Simple%20Share%20Buttons&amp;hashtags=simplesharebuttons"
                                       target="_blank" class="single-blog-social"><i class="fab fa-twitter"></i></a>
                                    <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo get_the_permalink(); ?>"
                                       target="_blank"
                                       class="single-blog-social"><i class="fab fa-linkedin"></i></a>
                                    <a href="http://www.facebook.com/sharer.php?u=<?php echo get_the_permalink(); ?>"
                                       target="_blank"
                                       class="single-blog-social"><i
                                                class="fab fa-facebook"></i></a>
                                    <a href="https://telegram.me/share?url=<?php echo get_the_permalink(); ?>"
                                       target="_blank"
                                       class="single-blog-social"><i
                                                class="fab fa-telegram"></i></a>
                                    <a href="https://instagram.com?url=<?php echo get_the_permalink(); ?>"
                                       target="_blank"
                                       class="single-blog-social"><i
                                                class="fab fa-instagram"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 text-center font-11">
                            <?php echo do_shortcode('[ratemypost]'); ?>
                        </div>
                    </div>
                    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery-3.4.1.min.js"></script>
                </div>
            </div>
        </section>
    <?php
    endwhile;
endif;
?>
    <section id="last_blog_index" class="pt-boot-45">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h5 class="text-boot font-27 fw500">بیشتر بخوانید</h5>
                    <p class="font-13 fw400 text-555">لورم ایپسوم متن ساختگی با تولید سادگی</p>
                </div>
            </div>
            <?php get_template_part('last-blog'); ?>
        </div>
    </section>
    <!--<section id="single_comments">
        <div class="container">
            <div class="row my-3">
                <div class="col-12 mx-auto">
                    <blockquote id="comments">
                        <?php /*comments_template(); */?>
                    </blockquote>
                </div>
            </div>
        </div>
    </section>-->
    </main>
<?php get_footer(); ?>
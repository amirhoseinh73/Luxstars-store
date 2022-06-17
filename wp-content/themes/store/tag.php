<?php
/**
 * A Simple tag Template
 */
?>
<?php get_header(); ?>

    <header id="header" class="container-fluid position-relative">
        <div class="row">
            <div class="col-12 px-0">
                <img src="<?php echo get_template_directory_uri().'/img/footer-img.jpg'; ?>" class="img-header"
                     alt="">
                <div class="header-caption">
                    <h1 class="fw600">برچسب ها</h1>
                    <p class="fw400 font-15">
                        لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم
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
                <div id="blog_cards" class="row">
                    <?php
                    if (have_posts()) : ?>
                    <?php
                    while (have_posts()) : the_post();
                        ?>
                        <blockquote class="blog-card card-width">
                            <a href="<?php echo get_the_permalink(); ?>" class="blog-link-absolute"></a>
                            <div class="blog-img">
                                <?php the_post_thumbnail('medium', ['class' => 'blog-card-img', 'alt' => get_the_title()]); ?>
                            </div>
                            <div class="blog-content">
                                <h6 class="font-13 fw500 text-right"><?php echo get_the_title(); ?></h6>
                                <p class="font-12 fw400">
                                    <?php echo the_content_rss('', true, '', '28'); ?>
                                </p>
                                <div class="row font-11 fw400">
                                    <div class="blog-check pl-1 pr-2">
                                        <div class="blog-check-icon">
                                            <i class="far fa-alarm-clock"></i>
                                        </div>
                                        <div class="blog-check-text">
                                            <?php echo get_the_date(); ?>
                                        </div>
                                    </div>
                                    <div class="blog-check pr-0">
                                        <div class="blog-check-icon">
                                            <i class="fas fa-eye"></i>
                                        </div>
                                        <div class="blog-check-text">
                                            <?php echo gt_get_post_view(); ?>
                                        </div>
                                    </div>
                                    <div class="blog-check">
                                        <div class="blog-check-text">
                                            <a href="<?php echo get_the_permalink(); ?>" class="hover-i-parent">
                                                بیشتر <i class="far fa-arrow-left hover-i"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </blockquote>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
                <nav>
                    <ul class="pagination justify-content-center">
                        <li class="page-item" aria-current="page">
                            <?php
                            echo paginate_links();
                            ?>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </section>
    </main>

<?php get_footer(); ?>
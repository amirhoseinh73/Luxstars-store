<?php
/**
 * A Simple Category Template
 */
?>
<?php get_header(); ?>

    <header id="header" class="container-fluid position-relative">
        <div class="row">
            <div class="col-12 px-0">
                <img src="<?php echo get_template_directory_uri().'/img/footer-img.jpg'; ?>" class="img-header"
                     alt="">
                <div class="header-caption">
                    <h1 class="fw600">
                        <?php
                        if ( is_tag() ) echo __( "Tags" );
                        elseif ( is_category() ) echo __( "Categories" );
                        ?>
                    </h1>
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
                                <a class="amh_nj-breadcrumb-text font-12"><?php echo single_term_title() ?></a>
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
                        <section class="blog-2-card card-width my-4">
                            <a href="<?php echo get_the_permalink(); ?>" class="blog-link-absolute"></a>
                            <div class="blog-2-img">
                                <?php
                                if (has_post_thumbnail()) {
                                    the_post_thumbnail('medium', ['class' => 'img-fluid d-block w-100 h-100 object-fit-cover',
                                        'alt' => get_the_title()]);
                                } else {
                                    echo '<img src="'.get_template_directory_uri().'/img/thumb.png" class="img-fluid d-block w-100 h-100 object-fit-cover" alt="'.get_the_title().'"/>';
                                }
                                ?>
                            </div>
                            <div class="blog-2-content">
                                <h6 class="font-13 fw500 text-right"><?php echo get_the_title(); ?></h6>
                                <p class="font-12 fw400">
                                    <?php the_content_rss('', true, '', '28'); ?>
                                </p>
                                <div class="row font-11 fw400 mt-boot-15">
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
                                            <a href="#" class="d-block font-9 position-relative btn-boot rounded-pill py-1 amh_nj-transition-l">
                                                ادامه مطلب
                                                <abbr class="hover-i">&#10095;</abbr>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
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
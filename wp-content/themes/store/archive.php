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
                    if (have_posts()) :
                    while (have_posts()) : 
                        the_post();
                        get_template_part( "last-blog-card" );
                    endwhile;
                    wp_reset_postdata(); ?>
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
<?php get_header(); ?>

<?php if (have_posts()) :
    while (have_posts()) :
        the_post(); ?>
        <header id="header" class="container-fluid position-relative">
            <div class="row">
                <div class="col-12 px-0">
                    <img src="<?php echo get_template_directory_uri() . '/img/footer-img.jpg'; ?>"
                         class="img-header" alt="">
                    <div class="header-caption">
                        <h1 class="fw600"><?php echo get_the_title() ?></h1>
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
        <main>
        <section class="mb-boot-45 mt-boot-45">
            <div class="container">
                <?php if (has_post_thumbnail()): ?>
                    <div class="row">
                        <div class="col-12 text-center my-4 h-b-400">
                            <?php the_post_thumbnail('full', ['class' => 'img-fluid object-fit-cover', 'alt' => get_the_title()]); ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-12 page-content">
                        <div class="fw400 font-14 text-justify lh-2">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php
    endwhile;
endif;
?>
    <section id="last_blog_index" class="pt-boot-45">
        <div class="container">
            <?php
            get_template_part('last-blog-title');
            get_template_part('last-blog');
            ?>
        </div>
    </section>
    </main>
<?php get_footer(); ?>
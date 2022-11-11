<?php get_header(); ?>

<?php if (have_posts()) :
    while (have_posts()) :
        the_post(); ?>
        <header id="header" class="container-fluid position-relative">
            <div class="row">
                <?php get_template_part( "bread", "crumb-html" )?>
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
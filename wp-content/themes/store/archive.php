<?php
/**
 * A Simple Category Template
 */
?>
<?php get_header(); ?>

    <header id="header" class="container-fluid position-relative">
        <div class="row">
            <?php get_template_part( "bread", "crumb-html" )?>
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
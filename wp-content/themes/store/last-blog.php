<div id="blog_cards" class="row mt-boot-25">
    <?php
    $last_blog = new WP_Query('post_type=post&posts_per_page=4&order=DESC&cat=1');
    while ($last_blog->have_posts()) :
        $last_blog->the_post();
        get_template_part( "last-blog-card" );
    endwhile;
    wp_reset_postdata();
    ?>
</div>
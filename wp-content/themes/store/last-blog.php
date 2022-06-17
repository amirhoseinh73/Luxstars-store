<div id="blog_cards" class="row mt-boot-25">
    <?php $last_blog = new WP_Query('post_type=post&posts_per_page=4&order=DESC&cat=1');?>
    <?php while ($last_blog->have_posts()) :
        $last_blog->the_post();
        ?>
        <blockquote class="blog-2-card card-width">
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
        </blockquote>
    <?php endwhile; ?>
    <?php wp_reset_postdata(); ?>
</div>
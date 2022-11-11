<div class="col-12 px-0">
    <div class="img-header-parent">
        <img src="<?php echo get_template_directory_uri().'/img/footer-img.jpg'; ?>" class="img-header" alt="">
    </div>
    <div class="header-caption">
        <h1 class="fw600 font-20"><?php
            // if ( is_archive() && ! empty( single_term_title() ) ) echo single_term_title();
            // elseif ( is_tag() ) echo __( "Tags" );
            // elseif ( is_category() ) echo __( "Categories" );
            // else echo get_the_title();
            wp_title( '', true, 'right' );
        ?></h1>
        <div class="fw400 font-13">
            <?//= term_description()?>
            <?//php the_content_rss("","","",10,"" )?>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="amh_nj-breadcrumb">
                    <a class="font-14">
                        <i class="far fa-angle-double-left pl-1 pr-1 align-middle font-13"></i>
                    </a>
                    <a href="<?php echo home_url() ?>" class="amh_nj-breadcrumb-link font-13">خانه</a>
                    <i class="far fa-angle-left pl-1 align-middle font-15 fw300"></i>
                    <a class="amh_nj-breadcrumb-text font-12"><?php
                        if ( is_archive() && ! empty( single_term_title() ) ) echo single_term_title();
                        else echo get_the_title();
                        ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
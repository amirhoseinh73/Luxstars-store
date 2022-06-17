<?php

// My custom comments output html
function better_comments( $comment, $args, $depth ) {

    // Get correct tag used for the comments
    if ( 'div' === $args['style'] ) {
        $tag       = 'div';
        $add_below = 'comment';
    } else {
        $tag       = 'section';
        $add_below = 'div-comment';
    } ?>

    <<?php echo $tag; ?> <?php comment_class( empty( $args['has_children'] ) ? '' : '' ); ?> id="comment-<?php comment_ID() ?>">

    <?php
    // Switch between different comment types
    switch ( $comment->comment_type ) :
        case 'pingback' :
        case 'trackback' : ?>
            <div class="pingback-entry"><span class="pingback-heading"><?php esc_html_e( 'Pingback:', 'textdomain' ); ?></span> <?php comment_author_link(); ?></div>
            <?php
            break;
        default :

            if ( 'div' != $args['style'] ) { ?>
                <blockquote id="div-comment-<?php comment_ID() ?>" class="comment-body">
            <?php } ?>
                        <div class="row p-3 text-justify border-b-grad">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-12 p-1 amh_nj-bg-answ">
                                        <?php
                                    if ( $args['avatar_size'] != 0 ) {
                                       // $avatar_size = ! empty( $args['avatar_size'] ) ? $args['avatar_size'] : 100; // set default avatar size
                                        echo get_avatar( $comment,'full','','',array('class'=>'img-amh_njcomment') );
                                    }
                                    ?>
                                        <div class="row">
                                        <?php printf( __( '<div class="col-6 px-0 py-0 my-auto"><h5 class="font-13 text-right mb-0 text-boot-sec fw300">
                                            
                                            <strong>
                                            %s
                                            </strong></h5></div>','textdomain'),get_comment_author_link()); ?>
                                            <div class="col-6 px-0 py-0 my-auto">
                                                <h6 class="font-11 text-left">
                                                    <time class="amh_nj-time-com fw300 font-11">
                                                    <?php
                                                        printf(
                                                                __( '%1$s', 'textdomain' ),
                                                                get_comment_date()
                                                            );
                                                        ?>
                                                    </time>
                                                </h6>
                                            </div>
                                        </div>
                                        <p class="font-12 amh_nj-com-message fw300">
                                        <?php echo get_comment_text(); ?>
                                            </p>
                                            <div class="comment-details">

                <?php
                // Display comment moderation text
                if ( $comment->comment_approved == '0' ) { ?>
                    <em class="comment-awaiting-moderation"><?php _e( 'دیدگاه شما در اتظار تایید است', 'textdomain' ); ?></em><br/><?php
                } ?>
                <div class="reply font-13 text-right px-3 amh_njanswer"><?php
                    // Display comment reply link
                    comment_reply_link( array_merge( $args, array(
                        'add_below' => $add_below,
                        'depth'     => $depth,
                        'max_depth' => $args['max_depth']
                    ) ) ); ?>
                </div>
            </div><!-- .comment-details -->
                                    </div>
                                </div>
                            </div>
                        </div>
            <?php
            if ( 'div' != $args['style'] ) { ?>
                </blockquote>
            <?php }
            // IMPORTANT: Note that we do NOT close the opening tag, WordPress does this for us
            break;
    endswitch; // End comment_type check.

}
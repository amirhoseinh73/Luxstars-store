<?php
// My custom comments output html
function amih_comments($comment, $args, $depth)
{
    // Get correct tag used for the comments
    if ('div' === $args['style']) {
        $tag = 'div ';
        $add_below = 'comment';
    } else {
        $tag = 'section ';
        $add_below = 'div-comment';
    } ?>
    <<?php echo $tag; ?><?php comment_class(empty($args['has_children']) ? '' : ''); ?> id="comment-<?php comment_ID() ?>">
    <?php
    // Switch between different comment types
    switch ($comment->comment_type) :
        case 'pingback' :
        case 'trackback' : ?>
            <div class="pingback-entry">
                <span class="pingback-heading"><?php esc_html_e('Pingback:', 'textdomain'); ?></span>
                <?php comment_author_link(); ?>
            </div>
            <?php
            break;
        default :
            if ('div' != $args['style']) { ?>
                <blockquote id="div-comment-<?php comment_ID() ?>" class="custom-comment-body">
            <?php } ?>
            <div class="row">
                <div class="col-12 custom-comment-answer">
                    <?php
                    if ($args['avatar_size'] != 0) {
                        // $avatar_size = ! empty( $args['avatar_size'] ) ? $args['avatar_size'] : 100; // set default avatar size
                        echo get_avatar($comment, 'full', '', '', array('class' => 'custom-comment-img'));
                    }
                    ?>
                    <div class="row">
                        <?php printf(__('<div class="col-6 my-auto custom-comment-title"><h5>
                                            %s
                                            </h5></div>', 'textdomain'), get_comment_author_link()); ?>
                        <div class="col-6 my-auto custom-comment-details">
                            <time>
                                <?php
                                printf(
                                    __('%1$s', 'textdomain'),
                                    get_comment_date()
                                );
                                ?>
                            </time>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-12 custom-comment-message">
                            <?php echo get_comment_text(); ?>
                        </p>
                    </div>
                    <div class="row">
                        <div class="col-12 custom-comment-details">
                            <?php
                            // Display comment moderation text
                            if ($comment->comment_approved == '0') { ?>
                                <em class="custom-comment-awaiting-moderation"><?php _e('دیدگاه شما در اتظار تایید است', 'textdomain'); ?></em>
                                <br/><?php
                            } ?>
                            <div class="reply custom-comment-answer"><?php
                                // Display comment reply link
                                comment_reply_link(array_merge($args, array(
                                    'add_below' => $add_below,
                                    'depth' => $depth,
                                    'max_depth' => $args['max_depth']
                                ))); ?>
                            </div>
                        </div><!-- .comment-details -->
                    </div>
                </div>
            </div>
            <?php
            if ('div' != $args['style']) { ?>
                </blockquote>
            <?php }
            // IMPORTANT: Note that we do NOT close the opening tag, WordPress does this for us
            break;
    endswitch; // End comment_type check.
}

function amih_comment_form($fields)
{
    $args = array();
    $commenter = wp_get_current_commenter();
    $user = wp_get_current_user();
    $user_identity = $user->exists() ? $user->display_name : '';

    $args = wp_parse_args($args);
    if (!isset($args['format'])) {
        $args['format'] = current_theme_supports('html5', 'comment-form') ? 'html5' : 'xhtml';
    }
    $req = get_option('require_name_email');
    $html_req = ($req ? " required='required'" : '');
    $html5 = 'html5' === $args['format'];
    //add placeholders and remove labels
    $fields = array(
        sprintf('<div class="row">'),
        'author' => sprintf(
            '<p class="col-12 col-sm-4 custom-comment-form-input comment-form-author">%s %s</p>',
            sprintf(
                '<label for="author">%s%s</label>',
                __('Name'),
                ($req ? ' <span class="required">*</span>' : '')
            ),
            sprintf(
                '<input class="form-control form-control-sm d-block" id="author" name="author" placeholder="' . __('Name') . '" type="text" value="%s" size="30" maxlength="245"%s />',
                esc_attr($commenter['comment_author']),
                $html_req
            )
        ),
        'email' => sprintf(
            '<p class="col-12 col-sm-4 custom-comment-form-input comment-form-email">%s %s</p>',
            sprintf(
                '<label for="email">%s%s</label>',
                __('Email'),
                ($req ? ' <span class="required">*</span>' : '')
            ),
            sprintf(
                '<input class="form-control form-control-sm d-block" id="email" name="email" %s value="%s" placeholder="' . __('Email') . '" size="30" maxlength="100" aria-describedby="email-notes"%s />',
                ($html5 ? 'type="email"' : 'type="text"'),
                esc_attr($commenter['comment_author_email']),
                $html_req
            )
        ),
        'url' => sprintf(
            '<p class="col-12 col-sm-4 custom-comment-form-input comment-form-url">%s %s</p>',
            sprintf(
                '<label for="url">%s</label>',
                __('Website')
            ),
            sprintf(
                '<input class="form-control form-control-sm d-block" id="url" name="url" %s value="%s" placeholder="' . __('Website') . '" size="30" maxlength="200" />',
                ($html5 ? 'type="url"' : 'type="text"'),
                esc_attr($commenter['comment_author_url'])
            )
        ),
        sprintf('</div>'),
    );
    unset($fields['comment']);
    $fields['comment'] = '<div class="row"><p class="col-12 custom-comment-form-input comment-form-message">
                        <label for="comment">' . _x('Comment', 'noun') . '*</label>
                        <textarea id="comment" name="comment" class="col-12" 
                        rows="6" maxlength="65525" placeholder="' . _x('Comment', 'noun') . '"></textarea>
                        </p></div>';
    return $fields;
}
function change_submit_button($submit_field) {
    $changed_submit = str_replace ('<input name="submit" type="submit" id="submit" class="submit" value="'.__( 'Post Comment' ).'" />', '<div class="row"><div class="col-12 col-sm-6 col-lg-4 col-xl-3 mr-auto"><span class="amih-hover-btn-span font-13 fw400">'.__( 'Post Comment' ).'</span><button name="submit" type="submit" id="submit" class="submit btn-boot-2 font-13 amih-hover-btn rounded-pill pb-boot-8 pt-boot-8">'.__( 'Post Comment' ).'</button></div></div>', $submit_field);
    return $changed_submit;
}
add_filter('comment_form_submit_field', 'change_submit_button');
add_filter( 'comment_form_defaults', function( $fields ) {
    $user          = wp_get_current_user();
    $user_identity = $user->exists() ? $user->display_name : '';
    $fields['logged_in_as'] = sprintf(
        __( '<p class="logged-in-as">%s</p>'),
        sprintf(
            __( '<a href="%1$s" aria-label="%2$s">Logged in as %3$s</a>. <a href="%4$s">Log out?</a>' ),
            get_edit_user_link(),
            esc_attr( sprintf( __( 'Logged in as %s. Edit your profile.' ), $user_identity ) ),
            $user_identity,
            wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ), $post_id ) )
        )
    );
    return $fields;
});
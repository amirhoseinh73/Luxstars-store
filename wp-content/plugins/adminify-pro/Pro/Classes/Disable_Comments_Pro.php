<?php

namespace WPAdminify\Pro\Classes;

use WPAdminify\Inc\Modules\DisableComments\DisableComments;
// no direct access allowed
if (!defined('ABSPATH'))  exit;

/**
 * Post Columns: Featured Image and ID
 * @package WP Adminify
 *
 * @author WP Adminify <support@wpadminify.com>
 */

class Disable_Comments_Pro extends DisableComments
{
    public function __construct()
    {
        parent::__construct();

        // Hide existing comments
        if (!empty($this->options['disable_comments_hide_existing'])) {
            add_filter('comments_array', '__return_empty_array', 10, 2);
        }

        // Disable Comments for Media Attachments
        add_filter('comments_open', [$this, 'jltwp_adminify_disable_comments_for_attachments'], 10, 2);
        add_filter('comment_text', [$this, 'disable_comments_text_comment_pseudo_links']);
        add_filter('get_comment_author_link', [$this, 'jltma_disable_comments_author_link_to_js'], 100, 3);

        if (!is_admin()) {
            add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        }
    }

    /**
     * Disable Comments for Attachments
     *
     * @param [type] $open
     * @param [type] $post_id
     *
     * @return void
     */
    public function jltwp_adminify_disable_comments_for_attachments($open, $post_id = null)
    {
        if (!empty($this->options['disable_comments_media'])) {
            $post = get_post($post_id);
            if ($post->post_type == 'attachment') {
                return false;
            }
        }
        return $open;
    }


    // Scripts for Comments
    public function enqueue_scripts()
    {
        if (!empty($this->options['disable_comments_replace_comment_link']) || !empty($this->options['disable_comments_replace_author_link'])) {
            wp_register_script('wp-adminify-disable-comments', $this->url . '/disable-comments-links.js',  ['jquery'], WP_ADMINIFY_VER, true);
            wp_enqueue_script('wp-adminify-disable-comments');
        }
    }


    public function disable_comments_text_comment_pseudo_links($comment_text)
    {
        if (!empty($this->options['disable_comments_replace_comment_link'])) {
            return $this->convert_to_pheudo($comment_text);
        }
    }



    /**
     * Convert author link to pseudo link
     *
     * @return string
     */

    public function jltma_disable_comments_author_link_to_js($return, $author, $comment_ID)
    {
        if (!empty($this->options['disable_comments_replace_author_link'])) {

            $url    = get_comment_author_url($comment_ID);
            $author = get_comment_author($comment_ID);

            if (empty($url) || 'http://' == $url) {
                $return = $author;
            } else {
                $return = '<span class="wp-adminify-author-link-to-data-uri" data-adminify-comment-uri="' . $url . '">' . $author . '</span>';
            }
        }
        return $return;
    }
}

<?php

namespace WPAdminify\Inc\Admin\Options;

use WPAdminify\Inc\Utils;
use WPAdminify\Inc\Admin\AdminSettingsModel;

if (!defined('ABSPATH')) {
    die;
} // Cannot access directly.

class Module_Post_Color extends AdminSettingsModel
{
    public function __construct()
    {
        $this->general_post_settings();
    }

    public function get_defaults()
    {
        return [
            'post_status_bg_colors' => [
                'publish' => '#DBE2F5',
                'pending' => '#FCE4EE',
                'future'  => '#E0F1ED',
                'private' => '#FCF3D2',
                'draft'   => '#EBE0F5',
                'trash'   => '#EFF4E1',
            ],
            'post_thumb_column'   => '',
            'post_page_id_column' => true,
            'taxonomy_id_column'  => true,
            'comment_id_column'   => true,
        ];
    }

    /**
     * Post Status colors
     */
    public function post_status_bg_colors(&$fields)
    {
        $fields[] = array(
            'type'    => 'subheading',
            'content'   => Utils::adminfiy_help_urls(
                __('Post Status Background Settings', WP_ADMINIFY_TD),
                'https://wpadminify.com/kb/post-status-background-color/',
                'https://www.youtube.com/playlist?list=PLqpMw0NsHXV-EKj9Xm1DMGa6FGniHHly8',
                'https://www.facebook.com/groups/jeweltheme',
                'https://wpadminify.com/support/'
            )
        );

        if (jltwp_adminify()->can_use_premium_code__premium_only()) {

            $fields[] = array(
                'id'        => 'post_status_bg_colors',
                'type'      => 'color_group',
                'title'     => __('Post Status Background Colors', WP_ADMINIFY_TD),
                'subtitle'  => __('Background Color by Post Status type', WP_ADMINIFY_TD),
                'options'   => array(
                    'publish' => __('Publish BG Color', WP_ADMINIFY_TD),
                    'pending' => __('Pending BG Color', WP_ADMINIFY_TD),
                    'future'  => __('Future BG Color', WP_ADMINIFY_TD),
                    'private' => __('Private BG Color', WP_ADMINIFY_TD),
                    'draft'   => __('Draft BG Color', WP_ADMINIFY_TD),
                    'trash'   => __('Trash BG Color', WP_ADMINIFY_TD),
                ),
                'default'    => $this->get_default_field('post_status_bg_colors'),
            );
        } else {
            $fields[] = array(
                'type'       => 'notice',
                'title'     => __('Post Status Background', WP_ADMINIFY_TD),
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro()
            );
        }
    }

    /**
     * Post Status Columns
     */

    public function post_status_columns(&$fields)
    {
        $fields[] = array(
            'type'    => 'subheading',
            'content' => __('Custom Columns', WP_ADMINIFY_TD),
        );

        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $fields[] = array(
                'id'         => 'post_thumb_column',
                'type'       => 'switcher',
                'title'      => __('Show Thumbnail Column', WP_ADMINIFY_TD),
                'subtitle'   => __('Display a thumbnail column before the title for post and page table lists.', WP_ADMINIFY_TD),
                'text_on'    => 'Show',
                'text_off'   => 'Hide',
                'text_width' => 100,
                'default'    => $this->get_default_field('post_thumb_column'),
            );
        } else {
            $fields[] = array(
                'type'       => 'notice',
                'title'      => __('Show Thumbnail Column', WP_ADMINIFY_TD),
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro()
            );
        }

        if (jltwp_adminify()->can_use_premium_code__premium_only()) {
            $fields[] = array(
                'id'         => 'post_page_id_column',
                'type'       => 'switcher',
                'title'      => __('Show Post/Page ID Column', WP_ADMINIFY_TD),
                'subtitle'   => __('Display a IDs column for post and page table lists.', WP_ADMINIFY_TD),
                'text_on'    => __('Show', WP_ADMINIFY_TD),
                'text_off'   => __('Hide', WP_ADMINIFY_TD),
                'text_width' => 100,
                'default'    => $this->get_default_field('post_page_id_column'),
            );
        } else {
            $fields[] = array(
                'type'       => 'notice',
                'title'      => __('Show Post/Page ID Column', WP_ADMINIFY_TD),
                'style'      => 'warning',
                'content'    => Utils::adminify_upgrade_pro()
            );
        }

        $fields[] = array(
            'id'         => 'taxonomy_id_column',
            'type'       => 'switcher',
            'title'      => __('Show "Taxonomy ID" Column', WP_ADMINIFY_TD),
            'subtitle'   => __('Taxonomy ID show on all possible types of taxonomies', WP_ADMINIFY_TD),
            'text_on'    => __('Show', WP_ADMINIFY_TD),
            'text_off'   => __('Hide', WP_ADMINIFY_TD),
            'text_width' => 100,
            'default'    => $this->get_default_field('taxonomy_id_column'),
        );
        $fields[] = array(
            'id'         => 'comment_id_column',
            'type'       => 'switcher',
            'title'      => __('Show "Comment ID" Column', WP_ADMINIFY_TD),
            'subtitle'   => __('Show Comment ID and Parent Comment ID Column', WP_ADMINIFY_TD),
            'text_on'    => 'Show',
            'text_off'   => 'Hide',
            'text_width' => 100,
            'default'    => $this->get_default_field('comment_id_column'),
        );
    }

    public function general_post_settings()
    {
        if (!class_exists('ADMINIFY')) {
            return;
        }

        $fields = [];
        $this->post_status_bg_colors($fields);
        $this->post_status_columns($fields);

        \ADMINIFY::createSection($this->prefix, array(
            'title'  => __('Post Status Colors', WP_ADMINIFY_TD),
            'parent' => 'module_settings',
            'icon'   => 'fas fa-paint-roller',
            'fields' => $fields
        ));
    }
}

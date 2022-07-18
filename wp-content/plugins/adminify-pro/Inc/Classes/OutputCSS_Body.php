<?php

namespace WPAdminify\Inc\Classes;

use WPAdminify\Inc\Utils;
use WPAdminify\Inc\Admin\AdminSettings;
use WPAdminify\Inc\Admin\AdminSettingsModel;

// no direct access allowed
if (!defined('ABSPATH'))  exit;
class OutputCSS_Body extends AdminSettingsModel
{
    public $url;
    public function __construct()
    {
        $this->options = (array) AdminSettings::get_instance()->get();
        add_action('admin_enqueue_scripts', [$this, 'jltwp_adminify_output_body_styles'], 9999);
    }

    public function jltwp_adminify_output_body_styles()
    {
        $jltwp_adminify_output_body_css = '';
        $admin_bg_type = !empty($this->options['admin_general_bg']) ? $this->options['admin_general_bg'] : 'color';

        // Background Types
        $admin_bg_color = !empty($this->options['admin_general_bg_color']) ? $this->options['admin_general_bg_color'] : '';

        // Background Types
        if ($admin_bg_type) {
            $jltwp_adminify_output_body_css .= 'html, body.wp-adminify{';

            // Background Type: Color
            if ($admin_bg_type == 'color') {
                if (!empty($admin_bg_color)) {
                    $jltwp_adminify_output_body_css .= 'background-color: ' . esc_attr($admin_bg_color) . ';';
                }
            }

            $jltwp_adminify_output_body_css .= '}';
        }

        // Admin Button Color
        $admin_button_bg_color = !empty($this->options['admin_general_button_color']['bg_color']) ? $this->options['admin_general_button_color']['bg_color'] : '#0347FF';
        $admin_button_hover_bg_color = !empty($this->options['admin_general_button_color']['hover_bg_color']) ? $this->options['admin_general_button_color']['hover_bg_color'] : '#fff';
        $admin_button_text_color = !empty($this->options['admin_general_button_color']['text_color']) ? $this->options['admin_general_button_color']['text_color'] : '#fff';
        $admin_button_hover_text_color = !empty($this->options['admin_general_button_color']['hover_text_color']) ? $this->options['admin_general_button_color']['hover_text_color'] : '#0347FF';
        $admin_button_border_color = !empty($this->options['admin_general_button_color']['border_color']) ? $this->options['admin_general_button_color']['border_color'] : '#0347FF';
        $admin_button_hover_border_color = !empty($this->options['admin_general_button_color']['hover_border_color']) ? $this->options['admin_general_button_color']['hover_border_color'] : '#0347FF';


        if ($admin_button_bg_color || $admin_button_text_color || $admin_button_border_color) {
            $jltwp_adminify_output_body_css .= '.wp-adminify .is-primary:not(.wp-editor-wrap button, .wp-editor-wrap button), .wp-adminify .is-secondary:not(.wp-editor-wrap button, .wp-editor-wrap button), .wp-adminify .button-primary:not(.wp-editor-wrap button, .wp-editor-wrap button), .wp-adminify .button-secondary:not(.wp-editor-wrap button, .wp-editor-wrap button), .wp-adminify #wpbody-content .page-title-action, .wp-adminify #wpbody-content input[type=submit]:not(.actions .button), .wp-adminify .adminify-options.wp-adminify-settings .adminify-reset-section, .wp-adminify .wp-adminify-dashboard-widgets .adminify-field button:not(.wp-editor-wrap button), .wp-adminify .adminify-section[data-section-id] .adminify-fieldset button:not(.wp-switch-editor, .wp-editor-wrap button, .adminify-content button), .wp-adminify .adminify-data-wrapper .adminify-cloneable-content button:not(.wp-editor-wrap button), .wp-adminify #wpbody-content .wp-picker-input-wrap .button.wp-picker-default, .wp-adminify.wp-adminify_page_adminify-sidebar-generator .sidebar-title a, .wp-adminify.wp-adminify_page_adminify-sidebar-generator .wp-adminify--popup-container_inner form .button.button-primary, .wp-adminify #wpadminify-admin-columns .columns.is-desktop button, .wp-adminify.appearance_page_custom-background #custom-background .form-table #choose-from-library-link, .wp-adminify.appearance_page_custom-background #custom-background .wp-picker-container .wp-picker-input-wrap .button, .wp-adminify.nav-menus-php #nav-menus-frame .menu-edit .button, .wp-adminify.nav-menus-php .appearance_page_custom-background .wp-upload-form button, .wp-adminify #widgets-left .widgets-chooser .widgets-chooser-sidebars button, .wp-adminify #wpbody-content .postbox-container #post_tag input.button, .wp-adminify #post-body-content .postarea button:not(.wp-switch-editor, .ed_button, .mce-widget button), .wp-adminify .-interface-skeleton__header .edit-post-header .edit-post-header__settings .editor-post-switch-to-draft, .wp-adminify #posts-filter .wp-list-table.widefat .inline-edit-row.inline-edit-save button, .wp-adminify #wpbody-content .metabox-holder .inside button:not(.button-link, .wp-editor-wrap button, .wp-editor-wrap button), .wp-adminify #wpbody-content .metabox-holder .inside .button:not(.wp-editor-wrap button), .wp-adminify #wpbody-content .metabox-holder .inside input[type=submit], .wp-adminify #wpbody-content .metabox-holder .postbox-header + .inside button:not(.button-link, .wp-editor-wrap button, .wp-editor-wrap button), .wp-adminify #wpbody-content .metabox-holder .postbox-header + .inside .button:not(.wp-editor-wrap button), .wp-adminify #wpbody-content .metabox-holder .postbox-header + .inside input[type=submit], .wp-adminify #major-publishing-actions input[type=submit], .wp-adminify .media-modal .media-frame-toolbar .media-toolbar button, .wp-adminify #post-body-content .image-editor button:not(.imgedit-help-toggle), .wp-adminify #post-body-content .image-editor input[type=button], .wp-adminify .wp-adminify-page-speed-wrapper .wp-adminify-analyze-button, .wp-adminify #wpbody-content #documentation #docs-lookup, .wp-adminify .wp-list-table.widefat.comments .inline-edit-row .comment-reply .reply-submit-buttons button, .wp-adminify.themes-php #wpbody-content .theme-browser .theme .theme-actions .button, .wp-adminify.themes-php #wpbody-content .theme-overlay .theme-actions .button, .wp-adminify.theme-install-php #wpbody-content .theme-browser .theme .theme-actions .button, .wp-adminify.theme-install-php #wpbody-content .theme-install-overlay .wp-full-overlay-header .button, .wp-adminify.theme-install-php #wpbody-content .filter-drawer .buttons .button, .wp-adminify .health-check-body .site-health-view-passed, .wp-adminify .health-check-body .site-health-copy-buttons button, .wp-adminify .privacy-settings-accordion-actions button, .wp-adminify #wpbody-content #createuser .wp-generate-pw, .wp-adminify #wpbody-content #createuser .wp-hide-pw, .wp-adminify #wpbody-content #your-profile .wp-generate-pw, .wp-adminify #wpbody-content #your-profile .wp-hide-pw, .wp-adminify .adminify-copy-btn, .wp-adminify .blocks-widgets-container .block-editor-inserter button:not(.is-pressed, .components-panel__body-toggle), .wp-adminify .popup--delete-history .button.button-primary, .wp-adminify--folder-widget .folder--header a, .wp-adminify.plugin-install-php #wpbody-content .plugin-card .plugin-card-top .action-links .plugin-action-buttons .button{';

            if (isset($admin_button_bg_color)) {
                $jltwp_adminify_output_body_css .= 'background-color: ' . esc_attr($admin_button_bg_color) . ' !important;';
            }
            if (isset($admin_button_text_color)) {
                $jltwp_adminify_output_body_css .= 'color: ' . esc_attr($admin_button_text_color) . ' !important;';
            }
            if (isset($admin_button_border_color)) {
                $jltwp_adminify_output_body_css .= 'border: 1px solid ' . esc_attr($admin_button_border_color) . ' !important;';
            }

            $jltwp_adminify_output_body_css .= '}';
        }

        // Button Hover
        if ($admin_button_hover_bg_color || $admin_button_hover_text_color || $admin_button_hover_border_color) {
            $jltwp_adminify_output_body_css .= '.wp-adminify .is-primary:not(.wp-editor-wrap button, .wp-editor-wrap button):hover, .wp-adminify .is-secondary:not(.wp-editor-wrap button, .wp-editor-wrap button):hover, .wp-adminify .button-primary:not(.wp-editor-wrap button, .wp-editor-wrap button):hover, .wp-adminify .button-secondary:not(.wp-editor-wrap button, .wp-editor-wrap button):hover, .wp-adminify #wpbody-content .page-title-action:hover, .wp-adminify #wpbody-content input[type=submit]:not(.actions .button):hover, .wp-adminify .adminify-options.wp-adminify-settings .adminify-reset-section:hover, .wp-adminify .wp-adminify-dashboard-widgets .adminify-field button:not(.wp-editor-wrap button):hover, .wp-adminify .adminify-section[data-section-id] .adminify-fieldset button:not(.wp-switch-editor, .wp-editor-wrap button, .adminify-content button):hover, .wp-adminify .adminify-data-wrapper .adminify-cloneable-content button:not(.wp-editor-wrap button):hover, .wp-adminify #wpbody-content .wp-picker-input-wrap .button.wp-picker-default:hover, .wp-adminify.wp-adminify_page_adminify-sidebar-generator .sidebar-title a:hover, .wp-adminify.wp-adminify_page_adminify-sidebar-generator .wp-adminify--popup-container_inner form .button.button-primary:hover, .wp-adminify #wpadminify-admin-columns .columns.is-desktop button:hover, .wp-adminify.appearance_page_custom-background #custom-background .form-table #choose-from-library-link:hover, .wp-adminify.appearance_page_custom-background #custom-background .wp-picker-container .wp-picker-input-wrap .button:hover, .wp-adminify.nav-menus-php #nav-menus-frame .menu-edit .button:hover, .wp-adminify.nav-menus-php .appearance_page_custom-background .wp-upload-form button:hover, .wp-adminify #widgets-left .widgets-chooser .widgets-chooser-sidebars button:hover, .wp-adminify #wpbody-content .postbox-container #post_tag input.button:hover, .wp-adminify #post-body-content .postarea button:not(.wp-switch-editor, .ed_button, .mce-widget button):hover, .wp-adminify .-interface-skeleton__header .edit-post-header .edit-post-header__settings .editor-post-switch-to-draft:hover, .wp-adminify #posts-filter .wp-list-table.widefat .inline-edit-row.inline-edit-save button:hover, .wp-adminify #wpbody-content .metabox-holder .inside button:not(.button-link, .wp-editor-wrap button, .wp-editor-wrap button):hover, .wp-adminify #wpbody-content .metabox-holder .inside .button:not(.wp-editor-wrap button):hover, .wp-adminify #wpbody-content .metabox-holder .inside input[type=submit]:hover, .wp-adminify #wpbody-content .metabox-holder .postbox-header + .inside button:not(.button-link, .wp-editor-wrap button, .wp-editor-wrap button):hover, .wp-adminify #wpbody-content .metabox-holder .postbox-header + .inside .button:not(.wp-editor-wrap button):hover, .wp-adminify #wpbody-content .metabox-holder .postbox-header + .inside input[type=submit]:hover, .wp-adminify #major-publishing-actions input[type=submit]:hover, .wp-adminify .media-modal .media-frame-toolbar .media-toolbar button:hover, .wp-adminify #post-body-content .image-editor button:not(.imgedit-help-toggle):hover, .wp-adminify #post-body-content .image-editor input[type=button]:hover, .wp-adminify .wp-adminify-page-speed-wrapper .wp-adminify-analyze-button:hover, .wp-adminify #wpbody-content #documentation #docs-lookup:hover, .wp-adminify .wp-list-table.widefat.comments .inline-edit-row .comment-reply .reply-submit-buttons button:hover, .wp-adminify.themes-php #wpbody-content .theme-browser .theme .theme-actions .button:hover, .wp-adminify.themes-php #wpbody-content .theme-overlay .theme-actions .button:hover, .wp-adminify.theme-install-php #wpbody-content .theme-browser .theme .theme-actions .button:hover, .wp-adminify.theme-install-php #wpbody-content .theme-install-overlay .wp-full-overlay-header .button:hover, .wp-adminify.theme-install-php #wpbody-content .filter-drawer .buttons .button:hover, .wp-adminify .health-check-body .site-health-view-passed:hover, .wp-adminify .health-check-body .site-health-copy-buttons button:hover, .wp-adminify .privacy-settings-accordion-actions button:hover, .wp-adminify #wpbody-content #createuser .wp-generate-pw:hover, .wp-adminify #wpbody-content #createuser .wp-hide-pw:hover, .wp-adminify #wpbody-content #your-profile .wp-generate-pw:hover, .wp-adminify #wpbody-content #your-profile .wp-hide-pw:hover, .wp-adminify .adminify-copy-btn:hover, .wp-adminify .blocks-widgets-container .block-editor-inserter button:not(.is-pressed, .components-panel__body-toggle):hover, .wp-adminify .popup--delete-history .button.button-primary:hover, .wp-adminify--folder-widget .folder--header a:hover, .wp-adminify.plugin-install-php #wpbody-content .plugin-card .plugin-card-top .action-links .plugin-action-buttons .button:hover{';

            if (isset($admin_button_hover_bg_color)) {
                $jltwp_adminify_output_body_css .= 'background-color: ' . esc_attr($admin_button_hover_bg_color) . ' !important;';
            }
            if (isset($admin_button_hover_text_color)) {
                $jltwp_adminify_output_body_css .= 'color: ' . esc_attr($admin_button_hover_text_color) . ' !important;';
            }
            if (isset($admin_button_hover_border_color)) {
                $jltwp_adminify_output_body_css .= 'border: 1px solid ' . esc_attr($admin_button_hover_border_color) . ' !important;';
            }

            $jltwp_adminify_output_body_css .= '}';
        }

        // Combine the values from above and minifiy them.
        $jltwp_adminify_output_body_css = preg_replace('#/\*.*?\*/#s', '', $jltwp_adminify_output_body_css);
        $jltwp_adminify_output_body_css = preg_replace('/\s*([{}|:;,])\s+/', '$1', $jltwp_adminify_output_body_css);
        $jltwp_adminify_output_body_css = preg_replace('/\s\s+(.*)/', '$1', $jltwp_adminify_output_body_css);

        if (!empty($this->options['admin_ui'])) {
            wp_add_inline_style('wp-adminify-admin', wp_strip_all_tags($jltwp_adminify_output_body_css));
        } else {
            wp_add_inline_style('wp-adminify-default-ui', wp_strip_all_tags($jltwp_adminify_output_body_css));
        }
    }
}

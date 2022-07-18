<?php
defined('ABSPATH') || die("Can't access directly");

/**
 * Login styles.
 *
 * @package WP Adminify
 *
 * @subpackage Login_Customizer
 */

$form_bg_color          = isset($this->options['login_form_bg_color']['background-color']) ? $this->options['login_form_bg_color']['background-color'] : '';
$form_width             = isset($this->options['login_form_height_width']) ? $this->options['login_form_height_width'] : '';

$fields_height             = isset($this->options['fields_height']) ? $this->options['fields_height'] : '50px';
$fields_horizontal_padding = isset($this->options['fields_horizontal_padding']) ? $this->options['fields_horizontal_padding'] : '10px';
$fields_border_width       = isset($this->options['fields_border_width']) ? $this->options['fields_border_width'] : '2px';
$fields_border_radius      = isset($this->options['fields_border_radius']) ? $this->options['fields_border_radius'] : '4px';
$fields_text_color         = isset($this->options['fields_text_color']) ? $this->options['fields_text_color'] : '';
$fields_text_color_focus   = isset($this->options['login_form_fields']['style_fields_color']['focus']) ? $this->options['login_form_fields']['style_fields_color']['focus'] : '';
$fields_border_color       = isset($this->options['fields_border_color']) ? $this->options['fields_border_color'] : '#dddddd';
$fields_border_color_focus = isset($this->options['fields_border_color_focus']) ? $this->options['fields_border_color_focus'] : '';

$labels_color = isset($this->options['labels_color']) ? $this->options['labels_color'] : '';

$fields_height_unit   = preg_replace('/\d+/', '', $fields_height);
$fields_height_number = str_ireplace($fields_height_unit, '', $fields_height);
$hide_pw_top          = ($fields_height_number / 2) - 20;
$hide_pw_top          = $hide_pw_top . $fields_height_unit;

$footer_link_color       = isset($this->options['footer_link_color']) ? $this->options['footer_link_color'] : '';
$footer_link_color_hover = isset($this->options['footer_link_color_hover']) ? $this->options['footer_link_color_hover'] : '';
?>

<?php
$adminify_bg_options = $this->options['jltwp_adminify_login_bg_color_opt'];

// Background Color Options
$adminify_bg_color      = isset($this->options['jltwp_adminify_login_bg_color']['background-color']) ? $this->options['jltwp_adminify_login_bg_color']['background-color'] : '#f1f1f1';
$adminify_bg_attachment = isset($this->options['jltwp_adminify_login_bg_color']['background-attachment']) ? $this->options['jltwp_adminify_login_bg_color']['background-attachment'] : '';
$adminify_bg_image      = isset($this->options['jltwp_adminify_login_bg_color']['background-image']) ? $this->options['jltwp_adminify_login_bg_color']['background-image'] : '';
$adminify_bg_pos        = isset($this->options['jltwp_adminify_login_bg_color']['background-position']) ? $this->options['jltwp_adminify_login_bg_color']['background-position'] : '';
$adminify_bg_repeat     = isset($this->options['jltwp_adminify_login_bg_color']['background-repeat']) ? $this->options['jltwp_adminify_login_bg_color']['background-repeat'] : '';
$adminify_bg_size       = isset($this->options['jltwp_adminify_login_bg_color']['background-size']) ? $this->options['jltwp_adminify_login_bg_color']['background-size'] : '';


if (jltwp_adminify()->can_use_premium_code__premium_only()) {
    // Background Gradient Options
    $adminify_gradient_bg_color          = isset($this->options['jltwp_adminify_login_gradient_bg']['background-color']) ? $this->options['jltwp_adminify_login_gradient_bg']['background-color'] : '';
    $adminify_gradient_bg_gradient_color = isset($this->options['jltwp_adminify_login_gradient_bg']['background-gradient-color']) ? $this->options['jltwp_adminify_login_gradient_bg']['background-gradient-color'] : '';
    $adminify_gradient_direction         = isset($this->options['jltwp_adminify_login_gradient_bg']['background-gradient-direction']) ? $this->options['jltwp_adminify_login_gradient_bg']['background-gradient-direction'] : '';
    $adminify_gradient_bg_attachment     = isset($this->options['jltwp_adminify_login_gradient_bg']['background-attachment']) ? $this->options['jltwp_adminify_login_gradient_bg']['background-attachment'] : '';
    $adminify_gradient_bg_image          = isset($this->options['jltwp_adminify_login_gradient_bg']['background-image']) ? $this->options['jltwp_adminify_login_gradient_bg']['background-image'] : '';
    $adminify_gradient_bg_pos            = isset($this->options['jltwp_adminify_login_gradient_bg']['background-position']) ? $this->options['jltwp_adminify_login_gradient_bg']['background-position'] : '';
    $adminify_gradient_bg_repeat         = isset($this->options['jltwp_adminify_login_gradient_bg']['background-repeat']) ? $this->options['jltwp_adminify_login_gradient_bg']['background-repeat'] : '';
    $adminify_gradient_bg_size           = isset($this->options['jltwp_adminify_login_gradient_bg']['background-size']) ? $this->options['jltwp_adminify_login_gradient_bg']['background-size'] : '';
    $adminify_gradient_bg_origin         = isset($this->options['jltwp_adminify_login_gradient_bg']['background-origin']) ? $this->options['jltwp_adminify_login_gradient_bg']['background-origin'] : '';
    $adminify_gradient_bg_clip           = isset($this->options['jltwp_adminify_login_gradient_bg']['background-clip']) ? $this->options['jltwp_adminify_login_gradient_bg']['background-clip'] : '';
    $adminify_gradient_bg_blend_mode     = isset($this->options['jltwp_adminify_login_gradient_bg']['background-blend-mode']) ? $this->options['jltwp_adminify_login_gradient_bg']['background-blend-mode'] : '';

?>

    body.login {

    <?php if ($adminify_bg_options == 'gradient') { ?>

        <?php if ($adminify_gradient_bg_color) { ?>
            background: <?php echo esc_attr($adminify_gradient_bg_color); ?>;
        <?php } ?>

        <?php if ($adminify_gradient_bg_image) { ?>
            background-image: url(<?php echo esc_attr($adminify_gradient_bg_image['url']); ?>);
        <?php } ?>

        <?php if ($adminify_gradient_bg_color) { ?>
            background-image: linear-gradient(<?php echo esc_attr($adminify_gradient_direction); ?>, <?php echo esc_attr($adminify_gradient_bg_color); ?> , <?php echo esc_attr($adminify_gradient_bg_gradient_color); ?>);
        <?php } ?>
        <?php if ($adminify_gradient_bg_attachment) { ?>
            background-attachment: <?php echo esc_attr($adminify_gradient_bg_attachment); ?>;
        <?php } ?>

        <?php if ($adminify_gradient_bg_pos) { ?>
            background-position: <?php echo esc_attr($adminify_gradient_bg_pos); ?>;
        <?php } ?>

        <?php if ($adminify_gradient_bg_repeat) { ?>
            background-repeat: <?php echo esc_attr($adminify_gradient_bg_repeat); ?>;
        <?php } ?>

        <?php if ($adminify_gradient_bg_size) { ?>
            background-size: <?php echo esc_attr($adminify_gradient_bg_size); ?>;
        <?php } ?>

        <?php if ($adminify_gradient_bg_origin) { ?>
            background-origin: <?php echo esc_attr($adminify_gradient_bg_origin); ?>;
        <?php } ?>

        <?php if ($adminify_gradient_bg_clip) { ?>
            background-clip: <?php echo esc_attr($adminify_gradient_bg_clip); ?>;
        <?php } ?>

        <?php if ($adminify_gradient_bg_blend_mode) { ?>
            background-blend-mode: <?php echo esc_attr($adminify_gradient_bg_blend_mode); ?>;
        <?php } ?>

<?php }
} ?>

<?php if ($adminify_bg_options == 'color') { ?>
    <?php if ($adminify_bg_color) { ?>
        background-color: <?php echo esc_attr($adminify_bg_color); ?>;
    <?php } ?>

    <?php if ($adminify_bg_attachment) { ?>
        background-attachment: <?php echo esc_attr($adminify_bg_attachment); ?>;
    <?php } ?>

    <?php if ($adminify_bg_image) { ?>
        background-image: url(<?php echo esc_attr($adminify_bg_image['url']); ?>);
    <?php } ?>

    <?php if ($adminify_bg_pos) { ?>
        background-position: <?php echo esc_attr($adminify_bg_pos); ?>;
    <?php } ?>

    <?php if ($adminify_bg_repeat) { ?>
        background-repeat: <?php echo esc_attr($adminify_bg_repeat); ?>;
    <?php } ?>

    <?php if ($adminify_bg_size) { ?>
        background-size: <?php echo esc_attr($adminify_bg_size); ?>;
    <?php } ?>
<?php } ?>

}



<?php
// Layouts , Columns and alignments
if (jltwp_adminify()->can_use_premium_code__premium_only()) {
    $alignment_login_bg      = isset($this->options['alignment_login_bg']) ? $this->options['alignment_login_bg'] : '';
    $alignment_login_bg_skew = isset($this->options['alignment_login_bg_skew']) ? $this->options['alignment_login_bg_skew'] : '';

    // Column Background Colors
    $adminify_column_bg_color              = isset($alignment_login_bg['background-color']) ? $alignment_login_bg['background-color'] : '';
    $adminify_column_bg_gradient_color     = isset($alignment_login_bg['background-gradient-color']) ? $alignment_login_bg['background-gradient-color'] : '';
    $adminify_column_bg_gradient_direction = isset($alignment_login_bg['background-gradient-direction']) ? $alignment_login_bg['background-gradient-direction'] : '';
    $adminify_column_bg_image              = isset($alignment_login_bg['background-image']) ? $alignment_login_bg['background-image'] : '';
    $adminify_column_bg_pos                = isset($alignment_login_bg['background-position']) ? $alignment_login_bg['background-position'] : '';
    $adminify_column_bg_repeat             = isset($alignment_login_bg['background-repeat']) ? $alignment_login_bg['background-repeat'] : '';
    $adminify_column_bg_size               = isset($alignment_login_bg['background-size']) ? $alignment_login_bg['background-size'] : '';
    $adminify_column_bg_blend_mode         = isset($alignment_login_bg['background-blend-mode']) ? $alignment_login_bg['background-blend-mode'] : '';
}


$alignment_login_width      = isset($this->options['alignment_login_width']) ? $this->options['alignment_login_width'] : '';
$alignment_login_column     = isset($this->options['alignment_login_column']) ? $this->options['alignment_login_column'] : '';
$alignment_login_horizontal = isset($this->options['alignment_login_horizontal']) ? $this->options['alignment_login_horizontal'] : '';
$alignment_login_vertical   = isset($this->options['alignment_login_vertical']) ? $this->options['alignment_login_vertical'] : '';


if (jltwp_adminify()->can_use_premium_code__premium_only()) {
    if ($alignment_login_width === 'fullwidth') { ?>
        body.wp-adminify-fullwidth .wp-adminify-form-container::after{
        <?php if ($alignment_login_bg_skew > 0) { ?>
            transform: skewX(<?php echo esc_attr($alignment_login_bg_skew); ?>deg);
        <?php } else { ?>
            transform: skewY(<?php echo esc_attr($alignment_login_bg_skew); ?>deg);
        <?php } ?>
        }
    <?php }

    if ($alignment_login_width === 'width_two_column') { ?>
        body.wp-adminify-half-screen .wp-adminify-form-container::after {
        <?php if ($adminify_column_bg_color) { ?>
            background-color: <?php echo esc_attr($adminify_column_bg_color); ?>;
        <?php } ?>

        <?php if ($adminify_column_bg_gradient_color) { ?>
            background-image: linear-gradient(<?php echo esc_attr($adminify_column_bg_gradient_direction); ?>, <?php echo esc_attr($adminify_column_bg_color); ?> , <?php echo esc_attr($adminify_column_bg_gradient_color); ?>);
        <?php } ?>

        <?php if (!empty($adminify_column_bg_image['url'])) { ?>
            background-image: url(<?php echo esc_attr($adminify_column_bg_image['url']); ?>) !important;
        <?php } ?>

        <?php if ($adminify_column_bg_pos) { ?>
            background-position: <?php echo esc_attr($adminify_column_bg_pos); ?>;
        <?php } ?>

        <?php if ($adminify_column_bg_repeat) { ?>
            background-repeat: <?php echo esc_attr($adminify_column_bg_repeat); ?>;
        <?php } ?>

        <?php if ($adminify_column_bg_size) { ?>
            background-size: <?php echo esc_attr($adminify_column_bg_size); ?>;
        <?php } ?>

        <?php if ($adminify_column_bg_blend_mode) { ?>
            background-blend-mode: <?php echo esc_attr($adminify_column_bg_blend_mode); ?>;
        <?php } ?>

        <?php
        if ($alignment_login_column === 'top' || $alignment_login_column === 'bottom') { ?>
            transform: skewY(<?php echo esc_attr($alignment_login_bg_skew); ?>deg);
        <?php } ?>
        <?php if ($alignment_login_column === 'right' || $alignment_login_column === 'left') { ?>
            transform: skewX(<?php echo esc_attr($alignment_login_bg_skew); ?>deg);
        <?php } ?>

        }
<?php }
} ?>


<?php
// Logo Styles
$logo_image     = isset($this->options['logo_image']) ? $this->options['logo_image'] : '';
$logo_image_url = isset($this->options['logo_image']['url']) ? $this->options['logo_image']['url'] : '';

$logo_settings          = isset($this->options['logo_settings']) ? $this->options['logo_settings'] : 'image-only';
$logo_image             = apply_filters('wp_adminify_login_logo', $logo_image);
$logo_height_width      = isset($this->options['login_title_style']['logo_heigh_width']) ? $this->options['login_title_style']['logo_heigh_width'] : '100%';
$logo_height_width_unit = isset($logo_height_width['unit']) ? $logo_height_width['unit'] : '';
$logo_height            = isset($logo_height_width['height']) ? $logo_height_width['height'] . $logo_height_width_unit : '';
$logo_width             = isset($logo_height_width['width']) ? $logo_height_width['width'] . $logo_height_width_unit : '';

if (jltwp_adminify()->can_use_premium_code__premium_only()) {
    $login_title_typography = isset($this->options['login_title_style']['login_title_typography']) ? $this->options['login_title_style']['login_title_typography'] : '';
    $logo_padding = !empty($this->options['login_title_style']['logo_padding']) ? $this->options['login_title_style']['logo_padding'] : '';
}

?>

.login h1 {
<?php if ($logo_height) { ?>
    height: <?php echo esc_attr($logo_height); ?>;
<?php } ?>
<?php if ($logo_width) { ?>
    width: <?php echo esc_attr($logo_width); ?>;
    <?php }

if (jltwp_adminify()->can_use_premium_code__premium_only()) {
    if ($logo_padding) {
        $logo_padding_top    = $logo_padding['top'] . $logo_padding['unit'];
        $logo_padding_right  = $logo_padding['right'] . $logo_padding['unit'];
        $logo_padding_bottom = $logo_padding['bottom'] . $logo_padding['unit'];
        $logo_padding_left   = $logo_padding['left'] . $logo_padding['unit'];
    ?>
        padding: <?php echo esc_attr($logo_padding_top) . ' ' . esc_attr($logo_padding_right) . ' ' . esc_attr($logo_padding_bottom) . ' ' . esc_attr($logo_padding_left); ?>;
<?php }
} ?>
}



<?php
if (jltwp_adminify()->can_use_premium_code__premium_only()) {
    if ($login_title_typography) {
        $logo_title_color           = $login_title_typography['color'];
        $logo_title_unit            = $login_title_typography['unit'];
        $logo_title_font_size       = $login_title_typography['font-size'] . $logo_title_unit;
        $logo_title_font_family     = $login_title_typography['font-family'];
        $logo_title_font_style      = $login_title_typography['font-style'];
        $logo_title_font_weight     = $login_title_typography['font-weight'];
        $logo_title_letter_spacing  = $login_title_typography['letter-spacing'] . $logo_title_unit;
        $logo_title_line_height     = $login_title_typography['line-height'] . $logo_title_unit;
        $logo_title_text_decoration = $login_title_typography['text-decoration'];
        $logo_title_text_transform  = $login_title_typography['text-transform'];
?>
        .wp-adminify-both-logo #logo-text{
        color : <?php echo esc_attr($logo_title_color); ?>;
        font-size : <?php echo esc_attr($logo_title_font_size); ?>;
        font-family : <?php echo esc_attr($logo_title_font_family); ?>;
        font-style : <?php echo esc_attr($logo_title_font_style); ?>;
        font-weight : <?php echo esc_attr($logo_title_font_weight); ?>;
        letter-spacing : <?php echo esc_attr($logo_title_letter_spacing); ?>;
        line-height : <?php echo esc_attr($logo_title_line_height); ?>;
        text-decoration: <?php echo esc_attr($logo_title_text_decoration); ?>;
        text-transform : <?php echo esc_attr($logo_title_text_transform); ?>;
        }
<?php }
} ?>



<?php if ($logo_settings == "text-only") { ?>
    .wp-adminify-text-logo:not(.wp-adminify-both-logo) h1 a {
    background-image: none !important;
    text-indent : unset;
    width : auto !important;
    height : auto !important;
    }
<?php } ?>

<?php if ($logo_settings == "image-only") { ?>
    .login h1 a {
    width : <?php echo esc_attr($logo_height); ?>;
    height : <?php echo esc_attr($logo_height); ?>;
    background-repeat : no-repeat;
    background-position: center bottom;
    background-size : auto <?php echo esc_attr($logo_height); ?>;
    }
<?php } ?>

<?php if ($logo_settings == "both") { ?>
    .wp-adminify-both-logo #login h1 a {
    width : 100%;
    height : 90px;
    background-repeat : no-repeat;
    background-position: top;
    background-size : auto <?php echo esc_attr($logo_height); ?>;
    text-indent : unset;
    display : flex;
    flex-wrap : wrap;
    align-items : flex-end;
    justify-content : center;
    overflow : visible;
    margin-bottom : 45px;
    }
    .wp-adminify-both-logo h1 a #logo-text {
    bottom : -30px;
    position: relative;
    }
<?php } ?>

<?php if ($logo_settings == "none") { ?>
    #login h1 a{
    display: none;
    }
<?php } ?>

<?php if ($logo_image_url) { ?>
    .login h1 a {
    background-image: url(<?php echo esc_url($logo_image_url); ?>) ;
    }
<?php } ?>

<?php if ($labels_color) : ?>
    #loginform label {
    color: <?php echo esc_attr($labels_color); ?>;
    }
<?php endif; ?>

<?php if ($form_width) : ?>
    #login {
    width : <?php echo esc_attr($form_width['width']); ?>;
    height: <?php echo esc_attr($form_width['height']); ?>;
    }
<?php endif; ?>



<?php
if (jltwp_adminify()->can_use_premium_code__premium_only()) {
    // Margin
    $form_margin        = isset($this->options['login_form_margin']) ? $this->options['login_form_margin'] : '';
    $form_margin_unit   = isset($form_margin['unit']) ? $form_margin['unit'] : 'px';
    $form_margin_top    = isset($form_margin['top']) ?  $form_margin['top'] . $form_margin_unit : '';
    $form_margin_right  = isset($form_margin['right']) ?  $form_margin['right'] . $form_margin_unit : '';
    $form_margin_bottom = isset($form_margin['bottom']) ?  $form_margin['bottom'] . $form_margin_unit : '';
    $form_margin_left   = isset($form_margin['left']) ?  $form_margin['left'] . $form_margin_unit : '';

    // Padding
    $form_padding       = isset($this->options['login_form_padding']) ? $this->options['login_form_padding'] : '';
    $form_padding_unit   = isset($form_padding['unit']) ? $form_padding['unit'] : 'px';
    $form_padding_top    = isset($form_padding['top']) ? $form_padding['top'] . $form_padding_unit : '';
    $form_padding_right  = isset($form_padding['right']) ? $form_padding['right'] . $form_padding_unit  : '';
    $form_padding_bottom = isset($form_padding['bottom']) ? $form_padding['bottom'] . $form_padding_unit  : '';
    $form_padding_left   = isset($form_padding['left']) ? $form_padding['left'] . $form_padding_unit  : '';
}

if (jltwp_adminify()->can_use_premium_code__premium_only()) {
    // Border Radius
    $form_border_radius = isset($this->options['login_form_border_radius']) ? $this->options['login_form_border_radius'] : '5px';
    $form_border_radius_unit   = isset($form_border_radius['unit']) ? $form_border_radius['unit'] : '';
    $form_border_radius_top    = isset($form_border_radius['top']) ? $form_border_radius['top'] . $form_border_radius_unit : '';
    $form_border_radius_right  = isset($form_border_radius['right']) ? $form_border_radius['right'] . $form_border_radius_unit : '';
    $form_border_radius_bottom = isset($form_border_radius['bottom']) ? $form_border_radius['bottom'] . $form_border_radius_unit : '';
    $form_border_radius_left   = isset($form_border_radius['left']) ? $form_border_radius['left'] . $form_border_radius_unit : '';
}


if (jltwp_adminify()->can_use_premium_code__premium_only()) {
    // Box Shadow
    $login_form_bs_color      = isset($this->options['login_form_box_shadow']['bs_color']) ? $this->options['login_form_box_shadow']['bs_color'] : '';
    $login_form_bs_hz         = isset($this->options['login_form_box_shadow']['bs_hz']) ? $this->options['login_form_box_shadow']['bs_hz'] : '';
    $login_form_bs_ver        = isset($this->options['login_form_box_shadow']['bs_ver']) ? $this->options['login_form_box_shadow']['bs_ver'] : '';
    $login_form_bs_blur       = isset($this->options['login_form_box_shadow']['bs_blur']) ? $this->options['login_form_box_shadow']['bs_blur'] : '';
    $login_form_bs_spread     = isset($this->options['login_form_box_shadow']['bs_spread']) ? $this->options['login_form_box_shadow']['bs_spread'] : '';
    $login_form_bs_spread_pos = isset($this->options['login_form_box_shadow']['bs_spread_pos']) ? $this->options['login_form_box_shadow']['bs_spread_pos'] : '';
}
$login_form_border  = isset($this->options['login_form_border']) ? $this->options['login_form_border'] : '';
$login_form_border_top    = isset($login_form_border['top']) ? $login_form_border['top'] : '';
$login_form_border_right  = isset($login_form_border['right']) ? $login_form_border['right'] : '';
$login_form_border_bottom = isset($login_form_border['bottom']) ? $login_form_border['bottom'] : '';
$login_form_border_left   = isset($login_form_border['left']) ? $login_form_border['left'] : '';
$login_form_border_style  = isset($login_form_border['style']) ? $login_form_border['style'] : '';
$login_form_border_color  = isset($login_form_border['color']) ? $login_form_border['color'] : '';
?>

<?php if (jltwp_adminify()->can_use_premium_code__premium_only()) { ?>
    .login form, #loginform {
    box-shadow : <?php echo esc_attr($login_form_bs_hz) . 'px ' . esc_attr($login_form_bs_ver) . 'px ' . esc_attr($login_form_bs_blur) . 'px ' . esc_attr($login_form_bs_spread) . 'px ' . esc_attr($login_form_bs_color) . ' ' . esc_attr($login_form_bs_spread_pos); ?>;
    border-width: <?php echo esc_attr($login_form_border_top); ?>px <?php echo esc_attr($login_form_border_right); ?>px <?php echo esc_attr($login_form_border_bottom); ?>px <?php echo esc_attr($login_form_border_left); ?>px;
<?php } ?>

<?php if ($login_form_border_style) { ?>
    border-style: <?php echo esc_attr($login_form_border_style); ?>;
<?php } ?>

<?php if ($login_form_border_color) { ?>
    border-color: <?php echo esc_attr($login_form_border_color); ?>;
<?php } ?>

<?php
if (jltwp_adminify()->can_use_premium_code__premium_only()) {
    if ($form_border_radius) { ?>
        border-radius: <?php echo esc_attr($form_border_radius_top) . ' ' . esc_attr($form_border_radius_right) . ' ' . esc_attr($form_border_radius_bottom) . ' ' . esc_attr($form_border_radius_left); ?>;
<?php }
} ?>

<?php if (isset($this->options['login_form_bg_type']) && $this->options['login_form_bg_type'] == "color") { ?>
    background-color: <?php echo esc_attr($form_bg_color); ?>;
    <?php } elseif (isset($this->options['login_form_bg_type']) && $this->options['login_form_bg_type'] == "gradient") {

    if (jltwp_adminify()->can_use_premium_code__premium_only()) {
        $form_bg_gradient_color = isset($this->options['login_form_bg_gradient']) ? $this->options['login_form_bg_gradient'] : '';
        if ($form_bg_gradient_color) { ?>
            background-image : linear-gradient(<?php echo esc_attr($form_bg_gradient_color['background-gradient-direction']); ?>, <?php echo esc_attr($form_bg_gradient_color['background-color']); ?> , <?php echo esc_attr($form_bg_gradient_color['background-gradient-color']); ?>);
<?php }
    }
} ?>

<?php if (jltwp_adminify()->can_use_premium_code__premium_only()) { ?>
    padding: <?php echo esc_attr($form_padding_top) . ' ' . esc_attr($form_padding_right) . ' ' . esc_attr($form_padding_bottom) . ' ' . esc_attr($form_padding_left); ?>;
    margin : <?php echo esc_attr($form_margin_top) . ' ' . esc_attr($form_margin_right) . ' ' . esc_attr($form_margin_bottom) . ' ' . esc_attr($form_margin_left); ?>;
<?php } ?>
}

<?php if (!empty($this->options['login_form_button_remember_me']) && $this->options['login_form_button_remember_me']) { ?>
    p.forgetmenot{
    display: none !important;
    }
<?php } ?>

<?php if (!empty($this->options['login_form_disable_lost_pass']) && $this->options['login_form_disable_lost_pass']) { ?>
    p#nav{
    display: none !important;
    }
<?php } ?>

<?php if (!empty($this->options['login_form_disable_back_to_site']) && $this->options['login_form_disable_back_to_site']) { ?>
    p#backtoblog{
    display: none !important;
    }
<?php } ?>


<?php
// Button Styles
$button_margin     = isset($this->options['login_form_button_settings']['button_margin']) ? $this->options['login_form_button_settings']['button_margin'] : '';
$button_padding    = isset($this->options['login_form_button_settings']['button_padding']) ? $this->options['login_form_button_settings']['button_padding'] : '15px';
$button_text_color = isset($this->options['login_form_button_settings']['button_text_color']) ? $this->options['login_form_button_settings']['button_text_color'] : '';

$button_bg_color = isset($this->options['login_form_button_settings']['button_bg']) ? $this->options['login_form_button_settings']['button_bg'] : '';
// $button_border        = isset($this->options['login_form_button_settings']['button_border_color']) ? $this->options['login_form_button_settings']['button_border_color'] : '';
$button_border        = isset($this->options['login_form_button_settings']['button_border']) ? $this->options['login_form_button_settings']['button_border'] : '';



// Hover
$button_bg_color_hover   = isset($this->options['login_form_button_settings']['button_bg_hover']) ? $this->options['login_form_button_settings']['button_bg_hover'] : '';
$button_text_color_hover = isset($this->options['login_form_button_settings']['button_text_hover']) ? $this->options['login_form_button_settings']['button_text_hover'] : '';
// $button_border_hover        = isset($this->options['login_form_button_settings']['button_border_color_hover']) ? $this->options['login_form_button_settings']['button_border_color_hover'] : '';
// $button_border_radius_hover = isset($this->options['login_form_button_settings']['button_border_radius_hover']) ? $this->options['login_form_button_settings']['button_border_radius_hover'] : '';





$login_btn_font_size = isset($this->options['button_font_size']) ? $this->options['button_font_size'] . 'px' : '';
?>

#loginform input#wp-submit{

<?php
if (jltwp_adminify()->can_use_premium_code__premium_only()) {
    $login_btn_unit   = isset($this->options['button_size']['unit']) ? $this->options['button_size']['unit'] : '';
    $login_btn_width  = isset($this->options['button_size']['width']) ? $this->options['button_size']['width'] . $login_btn_unit : '';
    $login_btn_height = isset($this->options['button_size']['height']) ? $this->options['button_size']['height'] . $login_btn_unit : '';

    if ($login_btn_height) { ?>
        height : <?php echo esc_attr($login_btn_height); ?>;
        line-height: <?php echo esc_attr($login_btn_height); ?>;
    <?php } ?>
    <?php if ($login_btn_width) { ?>
        width: <?php echo esc_attr($login_btn_width); ?>;
    <?php } ?>

<?php } ?>

<?php if ($login_btn_font_size) { ?>
    font-size: <?php echo esc_attr($login_btn_font_size); ?>;
<?php } ?>
<?php if ($button_bg_color) { ?>
    background-color: <?php echo esc_attr($button_bg_color); ?>;
<?php } ?>
<?php if ($button_text_color) { ?>
    color: <?php echo esc_attr($button_text_color); ?>;
<?php } ?>
<?php if ($button_border) { ?>
    border-color: <?php echo esc_attr($button_border['color']); ?>;
    border-style: <?php echo esc_attr($button_border['style']); ?>;
    border-width: <?php echo esc_attr($button_border['top']) . 'px ' . esc_attr($button_border['right']) . 'px ' . esc_attr($button_border['bottom']) . 'px ' . esc_attr($button_border['left']) . 'px'; ?>;
<?php } ?>

<?php
if (jltwp_adminify()->can_use_premium_code__premium_only()) {
    $button_border_radius = isset($this->options['login_form_button_settings']['button_border_radius']) ? $this->options['login_form_button_settings']['button_border_radius'] : '';
    if ($button_border_radius) {
        $btn_border_radius_top    = $button_border_radius['top'] . $button_border_radius['unit'];
        $btn_border_radius_right  = $button_border_radius['right'] . $button_border_radius['unit'];
        $btn_border_radius_bottom = $button_border_radius['bottom'] . $button_border_radius['unit'];
        $btn_border_radius_left   = $button_border_radius['left'] . $button_border_radius['unit'];
?>
        border-radius: <?php echo esc_attr($btn_border_radius_top) . ' ' . esc_attr($btn_border_radius_right) . ' ' . esc_attr($btn_border_radius_bottom) . ' ' . esc_attr($btn_border_radius_left); ?>;
<?php }
} ?>

<?php
if (jltwp_adminify()->can_use_premium_code__premium_only()) {
    $button_box_shadow    = isset($this->options['login_form_button_settings']['button_box_shadow']) ? $this->options['login_form_button_settings']['button_box_shadow'] : '';
    if ($button_box_shadow) {
        $btn_bs_color   = $button_box_shadow['bs_color'];
        $btn_bs_hz      = $button_box_shadow['bs_hz'];
        $btn_bs_ver     = $button_box_shadow['bs_ver'];
        $btn_bs_blur    = $button_box_shadow['bs_blur'];
        $btn_bs_spread  = $button_box_shadow['bs_spread'];
        $btn_spread_pos = $button_box_shadow['bs_spread_pos'];
?>
        box-shadow: <?php echo esc_attr($btn_bs_hz) . 'px ' . esc_attr($btn_bs_ver) . 'px ' . esc_attr($btn_bs_blur) . 'px ' . esc_attr($btn_bs_spread) . 'px ' . esc_attr($btn_bs_color) . ' ' . esc_attr($btn_spread_pos); ?>;
<?php }
} ?>

<?php
if (jltwp_adminify()->can_use_premium_code__premium_only()) {
    $button_text_shadow   = isset($this->options['login_form_button_settings']['button_text_shadow']) ? $this->options['login_form_button_settings']['button_text_shadow'] : '';
    if ($button_text_shadow) {
        $btn_ts_color = $button_text_shadow['ts_color'];
        $btn_ts_hz    = $button_text_shadow['ts_hz'];
        $btn_ts_ver   = $button_text_shadow['ts_ver'];
        $btn_ts_blur  = $button_text_shadow['ts_blur'];
?>
        -moz-text-shadow : <?php echo esc_attr($btn_ts_hz) . 'px ' . esc_attr($btn_ts_ver) . 'px ' . esc_attr($btn_ts_blur) . 'px ' . esc_attr($btn_ts_color); ?>;
        -webkit-text-shadow: <?php echo esc_attr($btn_ts_hz) . 'px ' . esc_attr($btn_ts_ver) . 'px ' . esc_attr($btn_ts_blur) . 'px ' . esc_attr($btn_ts_color); ?>;
        text-shadow : <?php echo esc_attr($btn_ts_hz) . 'px ' . esc_attr($btn_ts_ver) . 'px ' . esc_attr($btn_ts_blur) . 'px ' . esc_attr($btn_ts_color); ?>;
<?php }
} ?>


<?php if ($button_margin) {
    $btn_margin_top    = isset($button_margin['top']) ? $button_margin['top'] . $button_margin['unit'] : '';
    $btn_margin_right  = isset($button_margin['right']) ? $button_margin['right'] . $button_margin['unit'] : '';
    $btn_margin_bottom = isset($button_margin['bottom']) ? $button_margin['bottom'] . $button_margin['unit'] : '';
    $btn_margin_left   = isset($button_margin['unit']) ? $button_margin['unit'] . $button_margin['unit'] : '';
?>
    margin: <?php echo esc_attr($btn_margin_top) . ' ' . esc_attr($btn_margin_right) . ' ' . esc_attr($btn_margin_bottom) . ' ' . esc_attr($btn_margin_left); ?>;
<?php } ?>

<?php if ($button_padding) {
    $btn_padding_top    = isset($button_padding['top']) ? $button_padding['top'] . $button_padding['unit'] : '';
    $btn_padding_right  = isset($button_padding['right']) ? $button_padding['right'] . $button_padding['unit'] : '';
    $btn_padding_bottom = isset($button_padding['bottom']) ? $button_padding['bottom'] . $button_padding['unit'] : '';
    $btn_padding_left   = isset($button_padding['left']) ? $button_padding['left'] . $button_padding['unit'] : '';
?>
    padding: <?php echo esc_attr($btn_padding_top) . ' ' . esc_attr($btn_padding_right) . ' ' . esc_attr($btn_padding_bottom) . ' ' . esc_attr($btn_padding_left); ?>;
<?php } ?>


}



#loginform input#wp-submit:hover,#loginform input#wp-submit:focus {
<?php if ($button_bg_color_hover) : ?>
    background-color : <?php echo esc_attr($button_bg_color_hover); ?>;
<?php endif; ?>
<?php if ($button_text_color_hover) : ?>
    color : <?php echo esc_attr($button_text_color_hover); ?>;
<?php endif; ?>

<?php
//Turned off for future use
// if ($button_border_hover) {
?>
<!-- border-color: <?php  //echo esc_attr($button_border_hover['color']);
                    ?>;
border-style: <?php  //echo esc_attr($button_border_hover['style']);
                ?>;
border-width: <?php  //echo esc_attr($button_border_hover['top']) . 'px ' . esc_attr($button_border_hover['right']) . 'px ' . esc_attr($button_border_hover['bottom']) . 'px ' . esc_attr($button_border_hover['left']) . 'px';
                ?>; -->
<?php //}
?>

<?php
// Turned off for future use
// if ($button_border_radius_hover) {
//     $btn_border_radius_top_hover    = $button_border_radius_hover['top'] . $button_border_radius_hover['unit'];
//     $btn_border_radius_right_hover  = $button_border_radius_hover['right'] . $button_border_radius_hover['unit'];
//     $btn_border_radius_bottom_hover = $button_border_radius_hover['bottom'] . $button_border_radius_hover['unit'];
//     $btn_border_radius_left_hover   = $button_border_radius_hover['left'] . $button_border_radius_hover['unit'];
?>
<!-- border-radius: <?php  //echo esc_attr($btn_border_radius_top_hover) . ' ' . esc_attr($btn_border_radius_right_hover) . ' ' . esc_attr($btn_border_radius_bottom_hover) . ' ' . esc_attr($btn_border_radius_left_hover);
                    ?>; -->
<?php //}
?>


<?php
$button_box_shadow_hover  = isset($this->options['login_form_button_settings']['button_box_shadow_hover']) ? $this->options['login_form_button_settings']['button_box_shadow_hover'] : '';
if ($button_box_shadow_hover) {
    $btn_bs_color_hover   = $button_box_shadow_hover['bs_color'];
    $btn_bs_hz_hover      = $button_box_shadow_hover['bs_hz'];
    $btn_bs_ver_hover     = $button_box_shadow_hover['bs_ver'];
    $btn_bs_blur_hover    = $button_box_shadow_hover['bs_blur'];
    $btn_bs_spread_hover  = $button_box_shadow_hover['bs_spread'];
    $btn_spread_pos_hover = $button_box_shadow_hover['bs_spread_pos'];
?>
    box-shadow: <?php echo esc_attr($btn_bs_hz_hover) . 'px ' . esc_attr($btn_bs_ver_hover) . 'px ' . esc_attr($btn_bs_blur_hover) . 'px ' . esc_attr($btn_bs_spread_hover) . 'px ' . esc_attr($btn_bs_color_hover) . ' ' . esc_attr($btn_spread_pos_hover); ?>;
<?php } ?>

<?php
if (jltwp_adminify()->can_use_premium_code__premium_only()) {
    $button_text_shadow_hover = isset($this->options['login_form_button_settings']['button_text_shadow_hover']) ? $this->options['login_form_button_settings']['button_text_shadow_hover'] : '';
    if ($button_text_shadow_hover) {
        $btn_ts_color_hover = $button_text_shadow_hover['ts_hover'];
        $btn_ts_hz_hover    = $button_text_shadow_hover['ts_hz_hover'];
        $btn_ts_ver_hover   = $button_text_shadow_hover['ts_ver_hover'];
        $btn_ts_blur_hover  = $button_text_shadow_hover['ts_blur_hover'];
?>
        -moz-text-shadow : <?php echo esc_attr($btn_ts_hz_hover) . 'px ' . esc_attr($btn_ts_ver_hover) . 'px ' . esc_attr($btn_ts_blur_hover) . 'px ' . esc_attr($btn_ts_color_hover); ?>;
        -webkit-text-shadow: <?php echo esc_attr($btn_ts_hz_hover) . 'px ' . esc_attr($btn_ts_ver_hover) . 'px ' . esc_attr($btn_ts_blur_hover) . 'px ' . esc_attr($btn_ts_color_hover); ?>;
        text-shadow : <?php echo esc_attr($btn_ts_hz_hover) . 'px ' . esc_attr($btn_ts_ver_hover) . 'px ' . esc_attr($btn_ts_blur_hover) . 'px ' . esc_attr($btn_ts_color_hover); ?>;
<?php }
} ?>

}



.login input[type=text],
.login input[type=password] {
box-shadow : none;
outline : none;
transition : all 0.30s ease-in-out;
border-color : <?php echo esc_attr($fields_border_color); ?>;
border-radius : <?php echo esc_attr($fields_border_radius); ?>;
border-width : <?php echo esc_attr($fields_border_width); ?>;
padding : 0 <?php echo esc_attr($fields_horizontal_padding); ?>;
height : <?php echo esc_attr($fields_height); ?>;
<?php if ($fields_text_color) { ?>
    color : <?php echo esc_attr($fields_text_color); ?>;
<?php } ?>

<?php if (jltwp_adminify()->can_use_premium_code__premium_only()) {
    $fields_bg_color           = isset($this->options['login_form_fields']['style_fields_bg']['color']) ? $this->options['login_form_fields']['style_fields_bg']['color'] : '';
    if ($fields_bg_color) { ?>
        background-color : <?php echo esc_attr($fields_bg_color); ?>;
<?php }
} ?>

}

#loginform input[type=text] : focus,
#loginform input[type=email] : focus,
#loginform textarea : focus,
#loginform input[type=password]: focus {
<?php if ($fields_text_color_focus) { ?>
    color : <?php echo esc_attr($fields_text_color_focus); ?>;
<?php } ?>

<?php if (jltwp_adminify()->can_use_premium_code__premium_only()) {
    $fields_bg_color_focus     = isset($this->options['login_form_fields']['style_fields_bg']['focus']) ? $this->options['login_form_fields']['style_fields_bg']['focus'] : '';
    if ($fields_bg_color_focus) { ?>
        background : <?php echo esc_attr($fields_bg_color_focus); ?> !important;
<?php }
} ?>

<?php if ($fields_border_color_focus) { ?>
    border-color : <?php echo esc_attr($fields_border_color_focus); ?>;
<?php } ?>

}

.login .button.wp-hide-pw {
margin-top: <?php echo esc_attr($hide_pw_top); ?>;
}

.login #backtoblog,
.login #nav {
text-align: center;
}

<?php if ($footer_link_color) : ?>
    .login #nav a,
    .login #backtoblog a {
    color: <?php echo esc_attr($footer_link_color); ?>;
    }
<?php endif; ?>

<?php if ($footer_link_color_hover) : ?>
    .login #nav a : hover,
    .login #nav a : focus,
    .login #backtoblog a : hover,
    .login #backtoblog a : focus {
    color: <?php echo esc_attr($footer_link_color_hover); ?>;
    }
<?php endif; ?>



<?php
$jltwp_adminify_login_bg_type = $this->options['jltwp_adminify_login_bg_type'];
if ($jltwp_adminify_login_bg_type === 'video') { ?>
    .wp-adminify-background-wrapper {
    overflow: hidden;
    }
    .login-background,
    .login-overlay,
    .wp-adminify-background-wrapper {
    position: absolute;
    top : 0;
    right : 0;
    bottom : 0;
    left : 0;
    z-index : -1;
    }
<?php } ?>



<?php
/**
 * Login Form Fields Section
 */

if (jltwp_adminify()->can_use_premium_code__premium_only()) {
    // Form Field Margin
    $fields_margin        = isset($this->options['login_form_fields']['fields_margin']) ? $this->options['login_form_fields']['fields_margin'] : '';
    $fields_margin_unit   = isset($fields_margin['unit']) ? $fields_margin['unit'] : 'px';
    $fields_margin_top    = isset($fields_margin['top']) ?  $fields_margin['top'] . $fields_margin_unit : '';
    $fields_margin_right  = isset($fields_margin['right']) ?  $fields_margin['right'] . $fields_margin_unit : '';
    $fields_margin_bottom = isset($fields_margin['bottom']) ?  $fields_margin['bottom'] . $fields_margin_unit : '';
    $fields_margin_left   = isset($fields_margin['left']) ?  $fields_margin['left'] . $fields_margin_unit : '';

    // Form Field Padding
    $fields_padding        = isset($this->options['login_form_fields']['fields_padding']) ? $this->options['login_form_fields']['fields_padding'] : '';
    $fields_padding_unit   = isset($fields_padding['unit']) ? $fields_padding['unit'] : 'px';
    $fields_padding_top    = isset($fields_padding['top']) ?  $fields_padding['top'] . $fields_padding_unit : '';
    $fields_padding_right  = isset($fields_padding['right']) ?  $fields_padding['right'] . $fields_padding_unit : '';
    $fields_padding_bottom = isset($fields_padding['bottom']) ?  $fields_padding['bottom'] . $fields_padding_unit : '';
    $fields_padding_left   = isset($fields_padding['left']) ?  $fields_padding['left'] . $fields_padding_unit : '';

    // Box Shadow
    $login_fields_bs_color      = isset($this->options['login_form_fields']['fields_bs_color']) ? $this->options['login_form_fields']['fields_bs_color'] : '';
    $login_fields_bs_hz         = isset($this->options['login_form_fields']['fields_bs_hz']) ? $this->options['login_form_fields']['fields_bs_hz'] : '';
    $login_fields_bs_ver        = isset($this->options['login_form_fields']['fields_bs_ver']) ? $this->options['login_form_fields']['fields_bs_ver'] : '';
    $login_fields_bs_blur       = isset($this->options['login_form_fields']['fields_bs_blur']) ? $this->options['login_form_fields']['fields_bs_blur'] : '';
    $login_fields_bs_spread     = isset($this->options['login_form_fields']['fields_bs_spread']) ? $this->options['login_form_fields']['fields_bs_spread'] : '';
    $login_fields_bs_spread_pos = isset($this->options['login_form_fields']['fields_bs_spread_pos']) ? $this->options['login_form_fields']['fields_bs_spread_pos'] : '';

    if (jltwp_adminify()->can_use_premium_code__premium_only()) {
        // Border Radius
        $login_fields_border_radius = isset($this->options['login_form_fields']['style_border_radius']) ? $this->options['login_form_fields']['style_border_radius'] : '';
        $login_fields_border_radius_top    = !empty($login_fields_border_radius['top']) ? $login_fields_border_radius['top'] . $login_fields_border_radius['unit'] : '';
        $login_fields_border_radius_right  = !empty($login_fields_border_radius['right']) ? $login_fields_border_radius['right'] . $login_fields_border_radius['unit'] : '';
        $login_fields_border_radius_bottom = !empty($login_fields_border_radius['bottom']) ? $login_fields_border_radius['bottom'] . $login_fields_border_radius['unit'] : '';
        $login_fields_border_radius_left   = !empty($login_fields_border_radius['left']) ? $login_fields_border_radius['left'] . $login_fields_border_radius['unit'] : '';
    }

    // Form Fields Border
    $login_fields_border  = isset($this->options['login_form_fields']['style_border']) ? $this->options['login_form_fields']['style_border'] : '';
    $login_fields_border_top    = isset($login_fields_border['top']) ? $login_fields_border['top'] : '';
    $login_fields_border_right  = isset($login_fields_border['right']) ? $login_fields_border['right'] : '';
    $login_fields_border_bottom = isset($login_fields_border['bottom']) ? $login_fields_border['bottom'] : '';
    $login_fields_border_left   = isset($login_fields_border['left']) ? $login_fields_border['left'] : '';
    $login_fields_border_style  = isset($login_fields_border['style']) ? $login_fields_border['style'] : '';
    $login_fields_border_color  = isset($login_fields_border['color']) ? $login_fields_border['color'] : '';
?>

    #loginform input[type=text], #loginform input[type=password], #loginform input[type=email], #loginform textarea{
    margin : <?php echo esc_attr($fields_margin_top) . ' ' . esc_attr($fields_margin_right) . ' ' . esc_attr($fields_margin_bottom) . ' ' . esc_attr($fields_margin_left); ?>;
    padding : <?php echo esc_attr($fields_padding_top) . ' ' . esc_attr($fields_padding_right) . ' ' . esc_attr($fields_padding_bottom) . ' ' . esc_attr($fields_padding_left); ?>;
    box-shadow : <?php echo esc_attr($login_fields_bs_hz) . 'px ' . esc_attr($login_fields_bs_ver) . 'px ' . esc_attr($login_fields_bs_blur) . 'px ' . esc_attr($login_fields_bs_spread) . 'px ' . esc_attr($login_fields_bs_color) . ' ' . esc_attr($login_fields_bs_spread_pos); ?>;
    border-width: <?php echo esc_attr($login_form_border_top); ?>px <?php echo esc_attr($login_form_border_right); ?>px <?php echo esc_attr($login_form_border_bottom); ?>px <?php echo esc_attr($login_form_border_left); ?>px;
    border-radius: <?php echo esc_attr($login_fields_border_radius_top) . ' ' . esc_attr($login_fields_border_radius_right) . ' ' . esc_attr($login_fields_border_radius_bottom) . ' ' . esc_attr($login_fields_border_radius_left); ?>;

    border-width: <?php echo esc_attr($login_fields_border_top); ?>px <?php echo esc_attr($login_fields_border_right); ?>px <?php echo esc_attr($login_fields_border_bottom); ?>px <?php echo esc_attr($login_fields_border_left); ?>px;

    <?php if ($login_fields_border_style) { ?>
        border-style: <?php echo esc_attr($login_fields_border_style); ?>;
    <?php } ?>

    <?php if ($login_fields_border_color) { ?>
        border-color: <?php echo esc_attr($login_fields_border_color); ?>;
    <?php } ?>

    }
<?php } ?>


#wp-adminify-username, #wp-adminify-password, #wp-adminify-remember-me, #wp-adminify-lost-password, #backtoblog a{
<?php if (!empty($this->options['login_form_fields']['style_label_font_size'])) { ?>
    font-size: <?php echo esc_attr($this->options['login_form_fields']['style_label_font_size']); ?>px;
<?php } ?>
}

#loginform input[type=text], #loginform input[type=password], #loginform input[type=email], #loginform textarea{
<?php if (!empty($this->options['login_form_fields']['style_fields_height'])) { ?>
    height: <?php echo esc_attr($this->options['login_form_fields']['style_fields_height']); ?>px;
<?php } ?>

<?php if (!empty($this->options['login_form_fields']['style_fields_font_size'])) { ?>
    font-size: <?php echo esc_attr($this->options['login_form_fields']['style_fields_font_size']); ?>px;
<?php } ?>

}

#loginform label, #wp-adminify-lost-password, #backtoblog a{
<?php if (!empty($this->options['login_form_fields']['style_label_color'])) { ?>
    color: <?php echo esc_attr($this->options['login_form_fields']['style_label_color']); ?>;
<?php } ?>
}

<?php
defined('ABSPATH') or die('No Direct Access Sir!');

$jltwp_adminify_slides = esc_url($this->options['jltwp_adminify_login_bg_slideshow']);
$jltwp_adminify_slides_cleanup = preg_replace('#^https?://#', '', $jltwp_adminify_slides);
$jltwp_adminify_slide_ids = explode(',', $jltwp_adminify_slides_cleanup);

$slides = [];
foreach ($jltwp_adminify_slide_ids as $slide_item_id) {
    $image_url = wp_get_attachment_url($slide_item_id);
    $slides[]  = ['src' => $image_url];
}

if (empty($slides)) {
    return;
}

//Slideshow Effects
// fade,
// fade2,
// slideLeft,
// slideLeft2,
// slideRight,
// slideRight2,
// slideUp,
// slideUp2,
// slideDown,
// slideDown2,
// zoomIn,
// zoomIn2,
// zoomOut,
// zoomOut2,
// swirlLeft,
// swirlLeft2,
// swirlRight,
// swirlRight2,
// burn,
// burn2,
// blur,
// blur2,
// flash,
// flash2,


if (!empty($jltwp_adminify_slide_ids)) {
    ob_start();
?>


    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery(function() {
                jQuery('body').vegas({
                    slides: <?php echo json_encode($slides) ?>,
                    transition: 'fade',
                    delay: 5000,
                    timer: false,
                    overlay: '<?php echo WP_ADMINIFY_ASSETS . 'vendors/vegas/overlays/01.png'; ?>'
                });

            });
        });
    </script>
<?php
    $slideshow_html = ob_get_clean();
    echo $slideshow_html;
}

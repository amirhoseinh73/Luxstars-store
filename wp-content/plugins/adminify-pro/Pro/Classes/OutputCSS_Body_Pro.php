<?php

namespace WPAdminify\Pro\Classes;

use WPAdminify\Inc\Utils;
use WPAdminify\Inc\Classes\OutputCSS_Body;
// no direct access allowed
if (!defined('ABSPATH'))  exit;

class OutputCSS_Body_Pro extends OutputCSS_Body
{
    public $admin_bg_type;
    public function __construct()
    {
        parent::__construct();
        $this->options = get_option($this->prefix, []);
        $this->admin_bg_type = !empty($this->options['admin_general_bg']) ? $this->options['admin_general_bg'] : 'color';

        $this->jltwp_adminify_slideshow_bg();
        $this->jltwp_adminify_video_bg();

        add_action('admin_enqueue_scripts', [$this, 'jltwp_adminify_output_body_styles'], 99);
        // add_filter('admin_body_class', [$this, 'add_glassmorphism_body_classes']);

    }


    /**
     * Glass Effect Body Class
     *
     * @return void
     */
    public function add_glassmorphism_body_classes($classes)
    {
        $bodyclass = '';
        $admin_glass_effect = !empty($this->options['admin_glass_effect']) ? $this->options['admin_glass_effect'] : '';
        if ($admin_glass_effect) {
            $bodyclass .= ' adminify-glass-effect ';
        }
        return $classes . $bodyclass;
    }

    public function jltwp_adminify_slideshow_bg()
    {
        // $admin_glass_effect = !empty($this->options['admin_glass_effect']) ? $this->options['admin_glass_effect'] : '';
        if ($this->admin_bg_type == 'slideshow') {
            add_action('admin_footer', [$this, 'jltwp_adminify_add_slideshow_bg']);
        }
    }

    public function jltwp_adminify_video_bg()
    {
        if ($this->admin_bg_type === 'video') {
            add_action('admin_footer', [$this, 'jltwp_adminify_add_video_bg']);
        }
    }


    public function jltwp_adminify_add_video_bg()
    {
        $video_type = !empty($this->options['admin_general_bg_video_type']) ? $this->options['admin_general_bg_video_type'] : 'youtube';

        $video_yt = !empty($this->options['admin_general_bg_video_youtube']) ? $this->options['admin_general_bg_video_youtube'] : '';
        $video_local = !empty($this->options['admin_general_bg_video_self_hosted']['url']) ? $this->options['admin_general_bg_video_self_hosted']['url'] : '';

        $source = '';
        if ($video_yt) {
            $source = 'youtube';
        } elseif (!empty($video_local)) {
            $source = 'video/mp4';
        }

        if ($source) {
            $video_autoloop = !empty($this->options['admin_general_bg_video_loop']) ? $this->options['admin_general_bg_video_loop'] : '';
            $video_poster   =  !empty($this->options['admin_general_bg_video_poster']['url']) ? $this->options['admin_general_bg_video_poster']['url'] : '';

            ob_start();  ?>

            <script type="text/javascript">
                jQuery(document).ready(function() {
                    <?php
                    switch ($video_type) {
                        case 'youtube': ?>
                            var src = '<?php echo $video_yt; ?>';
                            var adminifyVideoBG = new vidim('body.wp-adminify', {
                                src: src,
                                type: 'YouTube',
                                <?php if (!empty($video_poster)) { ?>
                                    poster: '<?php echo esc_url($video_poster); ?>'
                                <?php } ?>,
                                quality: 'hd1080',
                                muted: true,
                                zIndex: 'initial',
                                loop: <?php echo $video_autoloop ? 'true' : 'false'; ?>,
                                startAt: src.length > 1 ? src[1] : '0',
                                showPosterBeforePlay: <?php echo $video_poster ? 'true' : 'false'; ?>
                            });

                        <?php
                            break;

                        case 'self_hosted':
                            $video_url = $video_local;
                            $video_url = $video_url; ?>
                            var adminifyVideoBG = new vidim('body.wp-adminify', {
                                src: [{
                                    type: 'video/mp4',
                                    src: '<?php echo esc_url($video_url); ?>',
                                }, ],
                                poster: '<?php echo esc_url($video_poster); ?>',
                                zIndex: 'initial',
                                showPosterBeforePlay: <?php echo $video_poster ? 'true' : 'false'; ?>,
                                loop: <?php echo $video_autoloop ? 'true' : 'false'; ?>
                            });
                    <?php
                            break;
                        default:
                            break;
                    } ?>
                });
            </script>
        <?php

            $video_html = ob_get_clean();
            echo $video_html;
        }
    }

    public function jltwp_adminify_add_slideshow_bg()
    {
        $jltwp_adminify_admin_bg_slides = !empty($this->options['admin_general_bg_slideshow']) ? $this->options['admin_general_bg_slideshow'] : '';

        $jltwp_adminify_admin_bg_slides_cleanup = preg_replace('#^https?://#', '', $jltwp_adminify_admin_bg_slides);
        $jltwp_adminify_admin_bg_slide_ids = explode(',', $jltwp_adminify_admin_bg_slides_cleanup);

        $slides = [];
        foreach ($jltwp_adminify_admin_bg_slide_ids as $slide_item_id) {
            $image_url = wp_get_attachment_url($slide_item_id);
            $slides[]  = ['src' => $image_url];
        }

        if (empty($slides)) {
            return;
        }

        ob_start();
        ?>
        <style>
            body.vegas-container>.vegas-overlay,
            body.vegas-container>.vegas-slide,
            body.vegas-container>.vegas-timer {
                z-index: initial;
            }
        </style>
        <script type="text/javascript">
            jQuery(document).ready(function() {
                jQuery(function() {
                    jQuery('body.wp-adminify').vegas({
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

    public function jltwp_adminify_output_body_styles()
    {
        $jltwp_adminify_output_body_css = '';

        $admin_google_font = !empty($this->options['admin_general_google_font']) ? $this->options['admin_general_google_font'] : '';

        // Background Types
        $admin_bg_color = !empty($this->options['admin_general_bg_color']) ? $this->options['admin_general_bg_color'] : '';
        $admin_bg_gradient = !empty($this->options['admin_general_bg_gradient']) ? $this->options['admin_general_bg_gradient'] : '';
        $admin_bg_image = !empty($this->options['admin_general_bg_image']['background-image']['url']) ? $this->options['admin_general_bg_image']['background-image']['url'] : '';

        // Typography Settings
        if (!empty($admin_google_font)) {
            $jltwp_adminify_output_body_css .= 'html, body.wp-adminify{';

            if (!empty($this->options['admin_general_google_font']['font-family'])) {
                $jltwp_adminify_output_body_css .= 'font-family: ' . $this->options['admin_general_google_font']['font-family'] . ';';
            }

            if (!empty($this->options['admin_general_google_font']['font-weight'])) {
                $jltwp_adminify_output_body_css .= 'font-weight: ' . $this->options['admin_general_google_font']['font-weight'] . ';';
            }

            if (!empty($this->options['admin_general_google_font']['font-style'])) {
                $jltwp_adminify_output_body_css .= 'font-style: ' . $this->options['admin_general_google_font']['font-style'] . ';';
            }

            if (!empty($this->options['admin_general_google_font']['font-size'])) {
                $jltwp_adminify_output_body_css .= 'font-size: ' . $this->options['admin_general_google_font']['font-size'] . 'px;';
            }

            if (!empty($this->options['admin_general_google_font']['line-height'])) {
                $jltwp_adminify_output_body_css .= 'line-height: ' . $this->options['admin_general_google_font']['line-height'] . 'px;';
            }

            if (!empty($this->options['admin_general_google_font']['color'])) {
                $jltwp_adminify_output_body_css .= 'color: ' . $this->options['admin_general_google_font']['color'] . ';';
            }

            $jltwp_adminify_output_body_css .= '}';
        }

        // Background Types
        if (!empty($this->admin_bg_type)) {
            $jltwp_adminify_output_body_css .= 'html, body.wp-adminify{';

            // Background Type: Gradient
            if ($this->admin_bg_type == 'gradient') {
                if (!empty($admin_bg_gradient)) {
                    $jltwp_adminify_output_body_css .= 'background-image : linear-gradient(' . esc_attr($admin_bg_gradient['background-gradient-direction']) . ', ' . esc_attr($admin_bg_gradient['background-color']) . ' , ' . esc_attr($admin_bg_gradient['background-gradient-color']) . ')';
                }
            }

            // Background Type: Image
            if ($this->admin_bg_type == 'image') {
                if (!empty($admin_bg_image)) {
                    $jltwp_adminify_output_body_css .= 'background-image: url(' . esc_attr($admin_bg_image) . ');';
                    $jltwp_adminify_output_body_css .= 'background-repeat: no-repeat;';
                    $jltwp_adminify_output_body_css .= 'background-position: center center;';
                    $jltwp_adminify_output_body_css .= 'background-size: cover;';
                }
            }

            $jltwp_adminify_output_body_css .= '}';
        }


        // Combine the values from above and minifiy them.
        $jltwp_adminify_output_body_css = preg_replace('#/\*.*?\*/#s', '', $jltwp_adminify_output_body_css);
        $jltwp_adminify_output_body_css = preg_replace('/\s*([{}|:;,])\s+/', '$1', $jltwp_adminify_output_body_css);
        $jltwp_adminify_output_body_css = preg_replace('/\s\s+(.*)/', '$1', $jltwp_adminify_output_body_css);

        wp_add_inline_style('wp-adminify-admin', wp_strip_all_tags($jltwp_adminify_output_body_css));

        // Slideshow Scripts
        if ($this->admin_bg_type == 'slideshow') {
            wp_enqueue_style('wp-adminify-vegas', WP_ADMINIFY_ASSETS . 'vendors/vegas/vegas.min.css');
            wp_enqueue_script('wp-adminify-vegas', WP_ADMINIFY_ASSETS . 'vendors/vegas/vegas.min.js', ['jquery'], WP_ADMINIFY_VER, true);
        }

        // Video Scripts
        if ($this->admin_bg_type == 'video') {
            wp_enqueue_script('wp-adminify-vidim', WP_ADMINIFY_ASSETS . 'vendors/vidim/vidim.min.js', ['jquery'], WP_ADMINIFY_VER, true);
        }
    }
}

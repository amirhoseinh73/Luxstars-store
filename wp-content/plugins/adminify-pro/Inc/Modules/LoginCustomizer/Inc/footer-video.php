<?php
defined('ABSPATH') or die('No Direct Access Sir!');

$video_yt = esc_url($this->options['jltwp_adminify_login_bg_video_youtube']);
$video_local = esc_attr($this->options['jltwp_adminify_login_bg_video_self_hosted']['url']);

$source = !empty($video_yt) ? empty($video_local) ? null : 'video/mp4' : 'YouTube';

if ($source) {
    $video_autoloop = $this->options['jltwp_adminify_login_bg_video_loop'];
    $video_poster   = $this->options['jltwp_adminify_login_bg_video_poster']['url'];

    if (!empty($video_poster)) {
        $video_poster = $video_poster;
    }

    ob_start();  ?>
    <script type='text/javascript' src='<?php echo WP_ADMINIFY_ASSETS . 'vendors/vidim/vidim.min.js'; ?>?ver=<?php echo WP_ADMINIFY_VER; ?>'></script>
    <script>
        <?php
        switch ($source) {
            case 'YouTube': ?>
                var src = '<?php echo $video_yt; ?>';
                var adminifyVideoBG = new vidim('.login-background', {
                    src: src,
                    type: 'YouTube',
                    poster: '<?php echo esc_url($video_poster); ?>',
                    quality: 'hd1080',
                    muted: true,
                    loop: <?php echo $video_autoloop ? 'true' : 'false'; ?>,
                    startAt: src.length > 1 ? src[1] : '0',
                    showPosterBeforePlay: <?php echo $video_poster ? 'true' : 'false'; ?>
                });

            <?php
                break;

            case 'video/mp4':
                $video_url = $video_local;
                $video_url = $video_url; ?>
                var adminifyVideoBG = new vidim('.login-background', {
                    src: [{
                        type: 'video/mp4',
                        src: '<?php echo esc_url($video_url); ?>',
                    }, ],
                    poster: '<?php echo esc_url($video_poster); ?>',
                    showPosterBeforePlay: <?php echo $video_poster ? 'true' : 'false'; ?>,
                    loop: <?php echo $video_autoloop ? 'true' : 'false'; ?>
                });
        <?php
                break;
            default:
                break;
        } ?>
    </script>
<?php

    $video_html = ob_get_clean();
    echo $video_html;
}

<?php if (!defined('ABSPATH')) die('No direct access allowed'); 
	global $WOOF;
	$btn_url = '';
	if (isset($WOOF->settings['woof_auto_hide_button_img']) AND ! empty($WOOF->settings['woof_auto_hide_button_img'])) {
		$btn_url = $WOOF->settings['woof_auto_hide_button_img'];		
	}

	if(isset($img_url) && $img_url){
		$btn_url = $img_url;
	}
	$style = '';
        
            if ($btn_url != 'none' && $btn_url) {

                $style = "background-image: url('$btn_url');" ;
            } elseif($btn_url=='none'){
                $style = "background-image: none ;" ;
            }

		
        //***
        $woof_auto_hide_button_txt = '';
        if (isset($WOOF->settings['woof_auto_hide_button_txt'])) {
            $woof_auto_hide_button_txt = WOOF_HELPER::wpml_translate(null, $WOOF->settings['woof_auto_hide_button_txt']);
        }
        ?>
		<style type="text/css">
			.woof_show_auto_form.woof_btn_default  {
				display: none !important;
			}
		</style>
        <a href="javascript:void(0);" <?php echo ($style)? 'style="' . $style . '"' :"";?> class="woof_show_auto_form woof_btn <?php if ($btn_url == 'none') echo 'woof_show_auto_form_txt'; ?>">
			<?php echo __($woof_auto_hide_button_txt) ?>
		</a>


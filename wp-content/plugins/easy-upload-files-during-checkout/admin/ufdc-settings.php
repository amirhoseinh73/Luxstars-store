<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 



if(
	(isset($_GET['page']) && $_GET['page']=='easy_ufdc')
	||
	( isset( $_POST['ufdc_fields_submitted'] ) && $_POST['ufdc_fields_submitted'] == 'submitted' )
){
	
function easy_ufdc_page() {

// Check the user capabilities

	global $easy_ufdc_error_default, $easy_ufdc_success_default, $easy_ufdc_error;

	if ( !current_user_can( 'administrator' ) ) {

		wp_die( __( 'You do not have sufficient permissions to access this page.','easy-upload-files-during-checkout' ) );

	}

	

// Save the field values

	if ( isset( $_POST['ufdc_fields_submitted'] ) && $_POST['ufdc_fields_submitted'] == 'submitted' ) {
		
		
		if ( 
			! isset( $_POST['eufdc_noce_action_field'] ) 
			|| ! wp_verify_nonce( $_POST['eufdc_noce_action_field'], 'eufdc_noce_action' ) 
		) {
		
		   _e('Sorry, your nonce did not verify.','easy-upload-files-during-checkout');
		   exit;
		
		} else {
				
	
			delete_option('easy_ufdc_use_style');
	
			$_POST['eufdc_email'] = (isset($_POST['eufdc_email'])?sanitize_eufdc_data($_POST['eufdc_email']):false);
			
			$_POST['eufdc_email_link'] = (isset($_POST['eufdc_email_link'])?sanitize_eufdc_data($_POST['eufdc_email_link']):false);
			
			
	
			$_POST['eufdc_billing_off'] = (isset($_POST['eufdc_billing_off'])?sanitize_eufdc_data($_POST['eufdc_billing_off']):false);
	
			$_POST['eufdc_shipping_off'] = (isset($_POST['eufdc_shipping_off'])?sanitize_eufdc_data($_POST['eufdc_shipping_off']):false);
	
			$_POST['eufdc_order_comments_off'] = (isset($_POST['eufdc_order_comments_off'])?sanitize_eufdc_data($_POST['eufdc_order_comments_off']):false);
			
			$_POST['eufdc_secure_links'] = (isset($_POST['eufdc_secure_links'])?sanitize_eufdc_data($_POST['eufdc_secure_links']):false);
			
			$_POST['eufdc_secure_upload'] = (isset($_POST['eufdc_secure_upload'])?sanitize_eufdc_data($_POST['eufdc_secure_upload']):false);
			
			$_POST['eufdc_server_side_check'] = (isset($_POST['eufdc_server_side_check'])?sanitize_eufdc_data($_POST['eufdc_server_side_check']):false);
			
			$_POST['eufdc_img_thumbnails'] = (isset($_POST['eufdc_img_thumbnails'])?sanitize_eufdc_data($_POST['eufdc_img_thumbnails']):false);
			
			
	
			$_POST['easy_ufdc_req'] = (array_key_exists('easy_ufdc_req', $_POST)?1:0);

			$_POST['easy_ufdc_multiple'] = (array_key_exists('easy_ufdc_multiple', $_POST)?1:0);

			$_POST['easy_ufdc_page_checkout_refresh'] = (array_key_exists('easy_ufdc_page_checkout_refresh', $_POST)?1:0);
			$_POST['eufdc_items_attachments'] = (array_key_exists('eufdc_items_attachments', $_POST)?1:0);
			
			$_POST['easy_ufdc_auto_sync'] = (array_key_exists('easy_ufdc_auto_sync', $_POST)?1:0);

			$_POST['eufdc_input_text_field'] = (array_key_exists('eufdc_input_text_field', $_POST)?1:0);
			$_POST['eufdc_input_text_label'] = (array_key_exists('eufdc_input_text_label', $_POST) ? sanitize_eufdc_data($_POST['eufdc_input_text_label']): '');

	
			foreach ( $_POST as $key => $value ) {
	

	
				if ( get_option( $key ) != $value ) {
	
					update_option( $key, sanitize_eufdc_data($value) );
	
				} else {
	
					add_option( $key, sanitize_eufdc_data($value), '', 'no' );
	
				}
	
			}
		}

	}
	

global $easy_ufdc_page, $ufdc_custom, $eufdc_data, $ufdc_premium_link, $wufdc_dir, $wufdc_dir_url;
global $default_upload_dir, $wc_ufdc_upload_dir;
$wc_ufdc_upload_dir = stripslashes(get_option( 'woocommerce_ufdc_upload_dir', $default_upload_dir['basedir']));
$wc_ufdc_upload_dir = str_replace('\\', '/', $wc_ufdc_upload_dir);



$easy_ufdc_page = get_option( 'easy_ufdc_page' );

if(isset($_GET['eufdc_debug'])){
	if(isset($_GET['clear_eufdc_debug'])){
		update_option('eufdc_debug', array());
	}
	
	
	$args = array( 'posts_per_page' => 2, 'offset'=>0, 'post_type'=>array('attachment', 'attachment_order'), 'post_status'=>'inherit', 'order_by'=>'ID', 'sort_order'=>'DESC' );
	$myposts = get_posts( $args );
	
	exit;
}


?>



<div class="wrap eufdc_settings_div <?php echo esc_attr($ufdc_custom?'eufdc_pro_activated':''); ?>">

	<div id="icon-options-general" class="icon32"></div>
 
	<h2><?php echo esc_html($eufdc_data['Name'].' ('.$eufdc_data['Version'].($ufdc_custom?') '.__('Pro' , 'easy-upload-files-during-checkout').'':')')); ?></h2>    
    

    <h2 class="nav-tab-wrapper">
        <a class="nav-tab nav-tab-active"><?php _e("Settings",'easy-upload-files-during-checkout'); ?> <i class="fas fa-cogs"></i></a>
        <a class="nav-tab"><?php _e("Compatibility",'easy-upload-files-during-checkout'); ?> <i class="fas fa-compass"></i></a>
		
        <a class="nav-tab"><?php if($ufdc_custom): ?><?php _e("Optional",'easy-upload-files-during-checkout'); ?> <i class="far fa-check-square"></i><?php else: ?><?php _e("Premium Feature",'easy-upload-files-during-checkout'); ?><?php endif; ?></a>
       
        
        <a class="nav-tab"><?php _e("Developer Mode",'easy-upload-files-during-checkout'); ?> <i class="fas fa-code"></i></a>
        <a class="nav-tab"><?php _e("Orphan Files",'easy-upload-files-during-checkout'); ?> <i class="fas fa-unlink"></i></a>
        <a class="nav-tab float-end" data-tab="help" data-type="free"><i class="far fa-question-circle"></i>&nbsp;<?php _e("Help", 'easy-upload-files-during-checkout'); ?></a>
    </h2>          

	<?php if ( isset( $_POST['ufdc_fields_submitted'] ) && $_POST['ufdc_fields_submitted'] == 'submitted' ) { ?>

	<div id="message" class="updated fade"><p><strong><?php _e( 'Your settings have been saved.','easy-upload-files-during-checkout'); ?></strong></p></div>

	<?php } ?>


	<div class="postbox" style="float:left; width:100%;">
 		
		<form method="post" action="" id="ufdc_settings" class="nav-tab-content">
        
        	<input type="hidden" name="eufdc_tn" value="<?php echo isset($_GET['t'])?esc_attr($_GET['t']):'0'; ?>" />
            
			<?php wp_nonce_field( 'eufdc_noce_action', 'eufdc_noce_action_field' ); ?>
			<input type="hidden" name="ufdc_fields_submitted" value="submitted">

			
            
			<div id="poststuff">

				
                
				<div style="float:left; width:100%;">

					

						<div class="inside ufdc-settings">

							<table class="form-table">

                                                        

    							<tr>

    								<th>

    									<label for="easy_ufdc_page"><?php _e( 'Display on','easy-upload-files-during-checkout');?>:</label>

    								</th>

    								<td class="easy_ufdc_page_to_implement_div">

                                        <ul class="easy_ufdc_page_to_implement">
                                        
                                            <li><input type="radio" name="easy_ufdc_page" id="easy_ufdc_page_register" value="register" <?php checked($easy_ufdc_page=='register'); ?> />

                                            <label for="easy_ufdc_page_register">&nbsp;<?php echo __("Registration Page",'easy-upload-files-during-checkout').' '.__("(Beta)",'easy-upload-files-during-checkout'); ?></label>

                                            </li>                                       

                                            <li><input type="radio" name="easy_ufdc_page" id="easy_ufdc_page_cart" value="cart" <?php checked($easy_ufdc_page=='cart' || !$easy_ufdc_page); ?> />

                                            <label for="easy_ufdc_page_cart">&nbsp;<?php _e("Cart Page",'easy-upload-files-during-checkout'); ?></label>

                                            </li>
                                        
                                        
											<li class="eufdc_premium"><input type="radio" name="easy_ufdc_page" id="easy_ufdc_page_product" value="product" <?php if($easy_ufdc_page=='product') { echo 'checked="checked"'; } ?> />

                                            <label for="easy_ufdc_page_product">&nbsp;<?php _e("Product Page",'easy-upload-files-during-checkout'); ?></label>
                                            
                                            <?php $eufdc_product_page_positions = get_option( 'eufdc_product_page_positions', '' ); ?>
                                            <select name="eufdc_product_page_positions" title="<?php _e("Product Page",'easy-upload-files-during-checkout'); ?>">
                                            	<option value="" <?php selected($eufdc_product_page_positions==''); ?>><?php _e('Default','easy-upload-files-during-checkout'); ?></option>
                                            	<option disabled="disabled" value="woocommerce_single_product_summary" <?php selected($eufdc_product_page_positions=='woocommerce_single_product_summary'); ?>>woocommerce_single_product_summary</option>
                                                <option  value="woocommerce_before_add_to_cart_form" <?php selected($eufdc_product_page_positions=='woocommerce_before_add_to_cart_form'); ?>>woocommerce_before_add_to_cart_form</option>
                                                <option disabled="disabled" value="woocommerce_before_variations_form" <?php selected($eufdc_product_page_positions=='woocommerce_before_variations_form'); ?>>woocommerce_before_variations_form</option>
                                                <option value="woocommerce_before_add_to_cart_button" <?php selected($eufdc_product_page_positions=='woocommerce_before_add_to_cart_button'); ?>>woocommerce_before_add_to_cart_button</option>
                                                <option value="woocommerce_before_single_variation" <?php selected($eufdc_product_page_positions=='woocommerce_before_single_variation'); ?>>woocommerce_before_single_variation</option>
                                                <option value="woocommerce_single_variation" <?php selected($eufdc_product_page_positions=='woocommerce_single_variation'); ?>>woocommerce_single_variation</option>
                                                <option value="woocommerce_after_single_variation" <?php selected($eufdc_product_page_positions=='woocommerce_after_single_variation'); ?>>woocommerce_after_single_variation</option>
                                                <option value="woocommerce_after_add_to_cart_button" <?php selected($eufdc_product_page_positions=='woocommerce_after_add_to_cart_button'); ?>>woocommerce_after_add_to_cart_button</option>
                                                <option disabled="disabled" value="woocommerce_after_variations_form" <?php selected($eufdc_product_page_positions=='woocommerce_after_variations_form'); ?>>woocommerce_after_variations_form</option>
                                                <option disabled="disabled" value="woocommerce_after_add_to_cart_form" <?php selected($eufdc_product_page_positions=='woocommerce_after_add_to_cart_form'); ?>>woocommerce_after_add_to_cart_form</option>
                                                <option disabled="disabled" value="woocommerce_product_meta_start" <?php selected($eufdc_product_page_positions=='woocommerce_product_meta_start'); ?>>woocommerce_product_meta_start</option>
                                                <option disabled="disabled" value="woocommerce_product_meta_end" <?php selected($eufdc_product_page_positions=='woocommerce_product_meta_end'); ?>>woocommerce_product_meta_end</option>
                                                <option disabled="disabled" value="woocommerce_share" <?php selected($eufdc_product_page_positions=='woocommerce_share'); ?>>woocommerce_share</option>
                                            </select><br /><br />
                            
                                            <a href="https://ibulb.wordpress.com/2019/02/21/woocommerce-single-product-page-visual-hook-guide/" target="_blank" class="float-right mr-1"><small><?php _e('Visual Hook Guide', 'easy-upload-files-during-checkout'); ?></small></a>
                                            <br />
                                            
                                            <div class="iframe_div">
                                            <iframe src="https://www.youtube.com/embed/I-TX7rr8JQQ" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                            <code>$('form.eufdc_form_copy').insertAfter('YOUR CSS SELECTOR IN YOUR FILES').show();</code>
                                            </div>

                                            </li> 
                                            



                                            <li style="display:none"><input type="radio" name="easy_ufdc_page" id="easy_ufdc_page_checkout" value="checkout" <?php if($easy_ufdc_page=='checkout'){ echo 'checked="checked"'; } ?> />

                                            <label for="easy_ufdc_page_checkout">&nbsp;<?php _e("Checkout Page",'easy-upload-files-during-checkout'); ?></label>

                                             </li>
                                             
    										 <li style="display:none"><input type="radio" name="easy_ufdc_page" id="easy_ufdc_page_checkout_notes" value="checkout_notes" <?php checked($easy_ufdc_page=='checkout_notes'); ?> />

                                            <label for="easy_ufdc_page_checkout_notes">&nbsp;<?php echo __("Checkout Page ",'easy-upload-files-during-checkout').' > '.__("After Notes",'easy-upload-files-during-checkout'); ?></label>

                                             </li>
                                             
    										 <li class="eufdc_premium"><input type="radio" name="easy_ufdc_page" id="easy_ufdc_page_checkout_above" value="checkout_above" <?php checked($easy_ufdc_page=='checkout_above'); ?> />

                                            <label for="easy_ufdc_page_checkout_above">&nbsp;<?php echo __("Checkout Page ",'easy-upload-files-during-checkout').' > '.__("Top of the Page",'easy-upload-files-during-checkout'); ?><?php echo($ufdc_custom?' / '.__("(Beta)",'easy-upload-files-during-checkout'):''); ?></label>

                                             </li>   
                                             
                                             <li class="eufdc_premium"><input type="radio" name="easy_ufdc_page" id="easy_ufdc_page_checkout_above_content" value="checkout_above_content" <?php checked($easy_ufdc_page=='checkout_above_content'); ?> />

                                            <label for="easy_ufdc_page_checkout_above_content">&nbsp;<?php echo __("Checkout Page ",'easy-upload-files-during-checkout').' > '.__("Top of the Content",'easy-upload-files-during-checkout'); ?><?php echo($ufdc_custom?' / '.__("(Beta)",'easy-upload-files-during-checkout'):''); ?></label>

                                             </li>                                             

                                            <li class="eufdc_premium"><input type="radio" name="easy_ufdc_page" id="easy_ufdc_page_thank_you" value="thank_you" <?php checked($easy_ufdc_page=='thank_you'); ?> />

                                                <label for="easy_ufdc_page_thank_you">&nbsp;<?php _e("Thank You Page",'easy-upload-files-during-checkout'); ?><?php echo ($ufdc_custom?' / '.__("(Beta)",'easy-upload-files-during-checkout'):''); ?></label>

                                            </li>

                                            <li class="eufdc_premium"><input type="radio" name="easy_ufdc_page" id="easy_ufdc_page_customer_order" value="customer_order" <?php checked($easy_ufdc_page=='customer_order'); ?> />

                                                <label for="easy_ufdc_page_customer_order">&nbsp;<?php _e("Customer Order View Page",'easy-upload-files-during-checkout'); ?><?php echo ($ufdc_custom?' / '.__("(Beta)",'easy-upload-files-during-checkout'):''); ?></label>

                                            </li>
                                            
                                            
                                            <li class="eufdc_premium"><input type="radio" name="easy_ufdc_page" id="easy_ufdc_shortcode" value="shortcode" <?php checked($easy_ufdc_page=='shortcode'); ?> />

                                                <label for="easy_ufdc_shortcode">&nbsp;<strong><?php _e("Shortcode",'easy-upload-files-during-checkout'); ?>:</strong> [EUFDC_UPLOAD_FORM target="css selectors"]</label>
                                                <a href="https://www.youtube.com/embed/JVYiwN7J7FQ" target="_blank"><i class="fab fa-youtube"></i></a>

                                            </li>

                                        </ul>
                                        
                                        

    								</td>

    							</tr>
                                
                                

                                



                                



								<tr>

    								<th>

    									<label for="easy_ufdc_allowed_file_types"><?php _e( 'Allowed file types','easy-upload-files-during-checkout'); ?>:</label>

    								</th>

    								<td>
<?php
$mime = array_keys(get_allowed_mime_types());
$allowed_mime = array();
if(!empty($mime)){
	foreach($mime as $types){
		$types = explode('|', $types);
		$allowed_mime = array_merge($allowed_mime, $types);
	}
}

?>
<script type="text/javascript" language="javascript">

var contains = function(needle) {
    // Per spec, the way to identify NaN is that it is not equal to itself
    var findNaN = needle !== needle;
    var indexOf;

    if(!findNaN && typeof Array.prototype.indexOf === 'function') {
        indexOf = Array.prototype.indexOf;
    } else {
        indexOf = function(needle) {
            var i = -1, index = -1;

            for(i = 0; i < this.length; i++) {
                var item = this[i];

                if((findNaN && item !== item) || item === needle) {
                    index = i;
                    break;
                }
            }

            return index;
        };
    }

    return indexOf.call(this, needle) > -1;
};
var allowed_mime = [];
<?php 
	if(!empty($allowed_mime)){
		foreach($allowed_mime as $i=>$mime){
?>
allowed_mime[<?php echo esc_attr($i); ?>] = '<?php echo esc_attr($mime); ?>';
<?php			
		}
	}
	
?>	
jQuery(document).ready(function($){
	<?php if(isset($_POST['eufdc_tn'])): ?>
		$('.eufdc_settings_div .nav-tab-wrapper .nav-tab:nth-child(<?php echo esc_attr($_POST['eufdc_tn'])+1; ?>)').click();		
	<?php endif; ?>

	if(eufdc_obj.eufdc_tab>0){
		$('.eufdc_settings_div .nav-tab-wrapper a.nav-tab').eq(eufdc_obj.eufdc_tab).click();
	}
			
	$('body').on('blur', 'input[name="easy_ufdc_allowed_file_types"]', function(){
		var str = $.trim($(this).val());
		//alert(str);
		if(str!=''){
			
			var chars = str.split(',');
			var all_good = true;
			$.each(chars, function(i, v){
				//console.log(v);
				if(all_good){
					all_good = contains.call(allowed_mime, v);					
				}
				
			});
			//console.log(all_good);
			if(all_good)
			$('#ufdc_settings pre').fadeOut();
			else
			$('#ufdc_settings pre').fadeIn();
		}
	});
	
	$('input[name="easy_ufdc_allowed_file_types"]:visible').eq(0).trigger('blur');
});

</script>
<input type="text" class="form-control w-50 regular-text" name="easy_ufdc_allowed_file_types" value="<?php if(!get_option( 'easy_ufdc_allowed_file_types' )) { echo 'doc,txt'; } else { echo stripslashes(get_option( 'easy_ufdc_allowed_file_types' )); }?>"/><br />



    									<span class="description"><?php

    										echo __( 'Specify which file types are allowed for uploading, seperate by commas.','easy-upload-files-during-checkout');

    									?></span>
                                        <?php if(!defined('ALLOW_UNFILTERED_UPLOADS') || (defined('ALLOW_UNFILTERED_UPLOADS') && !ALLOW_UNFILTERED_UPLOADS)){ ?>
                                        <pre>Add the following line in your wp-config.php <br /><br /><strong>define( 'ALLOW_UNFILTERED_UPLOADS', true );</strong><br /><br />if you are allowing some files which are not<br />supported by <a href="https://codex.wordpress.org/Uploading_Files" target="_blank">WordPress by default</a>.<br /></pre>
                                        <?php } ?>

    								</td>

    							</tr>



                                


								<tr>

    								<th>

    									<label for="easy_ufdc_max_uploadsize"><?php _e( 'Maximum upload size','easy-upload-files-during-checkout'); ?>:</label>

    								</th>

    								<td>

    									<input type="text" class="form-control w-50 short" name="easy_ufdc_max_uploadsize" value="<?php if(!get_option( 'easy_ufdc_max_uploadsize' )) { echo ini_get('upload_max_filesize'); } else { echo stripslashes(get_option( 'easy_ufdc_max_uploadsize' )); }?>"/><br />



    									<span class="description"><?php

    										echo __( 'Specify maximum upload size for all files in MegaBytes.','easy-upload-files-during-checkout').' '.__('Cannot exceed max PHP upload size.','easy-upload-files-during-checkout').'<br>';

											echo __( 'Note: recommended max upload size below 8MB.','easy-upload-files-during-checkout');
											echo '<br />';
											echo 'upload_max_filesize '.__('is').' '.ini_get('upload_max_filesize').' '.__('and').' post_max_size '.__('is').' '.ini_get('post_max_size').' '.__('on your server','easy-upload-files-during-checkout');

    									?></span>

    								</td>

    							</tr>
								
                                <tr class="igs">

                            		<th>

                                		<label for="wc_ufdc_upload_dir"><?php _e( 'Uploads Directory','easy-upload-files-during-checkout'); ?>:<br /></label>

                            		</th>

                            		<td class="eufdc_premium">

<?php
	
	$order_id_based = $default_upload_dir['basedir'].'/wc-orders/ORDER_ID';										
										
										
?>

<ul class="eufdc-wp-paths">
    <li>
    <?php _e('Default','easy-upload-files-during-checkout'); ?>:
        <ul>
        
            <li class="<?php echo $wc_ufdc_upload_dir==$default_upload_dir['basedir']?'active':'';?> pr-4"><?php echo esc_html($default_upload_dir['basedir']); ?> - <a href="https://www.youtube.com/embed/Q_S1FwCIvOg" target="_blank"><?php _e('How it works?','easy-upload-files-during-checkout'); ?></a></li>
            </ul>                                            
            </li>
            <li>
            <?php _e('WooCommerce','easy-upload-files-during-checkout'); ?>: 
            <ul>
            <li class="<?php echo $wc_ufdc_upload_dir==$order_id_based?'active':'';?>"><?php echo esc_attr($order_id_based); ?> - <a href="https://www.youtube.com/embed/Z2Vxug5EM0Q" target="_blank"><?php _e('How it works?','easy-upload-files-during-checkout'); ?></a></li>
        </ul>
    </li>
</ul>

<div class="eufdc-wp-paths-div">

                                        <input name="woocommerce_ufdc_upload_dir" class="form-control w-75 d-inline mb-3"  type="text" id="wc_ufdc_upload_dir" data-value="<?php echo esc_attr($wc_ufdc_upload_dir);?>" value="<?php echo esc_attr($wc_ufdc_upload_dir);?>" />

                                        <div class="dropdown eufdc_download_section" style="float: right">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" id="eufdc_backup_btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <?php _e('Download', 'easy-upload-files-during-checkout') ?>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="eufdc_backup_btn">
                                            <a class="dropdown-item eufdc_amazon_item"><img src="<?php echo esc_url($wufdc_dir_url); ?>img/amazon.jpg" width="90px" height="35px" /></a>

                                        <?php if($ufdc_custom){ if(class_exists('ZipArchive')){ ?>

                                            <a class="dropdown-item" href="admin.php?page=easy_ufdc&eufdc_zipper" title="<?php _e('Download complete directory archive','easy-upload-files-during-checkout'); ?>">
                                                <img src="<?php echo esc_url($wufdc_dir_url); ?>img/disk.png" width="35px" height="35px" />
                                            </a>
                                        <?php } }else{ ?>
                                            <a class="dropdown-item" title="<?php _e('Download complete directory archive is a premium feature','easy-upload-files-during-checkout'); ?>">
                                                <?php _e('Download','easy-upload-files-during-checkout'); ?>
                                            </a><?php } ?>

                                            </div>
                                        </div>
                                       <br />

                                        <div class="w-100 h-25 border mb-3 p-2 eufdc_amazon_form_wrapper eufdc_download_section" style="display: none">

                                            <?php

                                                $eufdc_amazon_credential = get_option('eufdc_amazon_credential', array());

                                                $amazon_key = '';
                                                $amazon_secret = '';
                                                $connected_display = 'none';
                                                $sync_disabled = '';
                                                $amazon_key = isset($eufdc_amazon_credential['key'])?$eufdc_amazon_credential['key']:'';
                                                $amazon_secret = isset($eufdc_amazon_credential['secret'])?$eufdc_amazon_credential['secret']:'';

                                                if(!empty($eufdc_amazon_credential) && $eufdc_amazon_credential['status'] == 'connected' && $ufdc_custom){


                                                    $connected_display = 'inline';
                                                    $sync_disabled = '';

                                                }

                                            ?>


                                            <div class="h6 mb-3">
                                                <?php _e('Enter Amazon credential to sync data on Amazon S3' , 'easy-upload-files-during-checkout') ?>:
                                                <img src="<?php echo esc_url($wufdc_dir_url); ?>img/amazon.jpg" width="90px" height="35px" style="float: right" />
                                            </div>

                                            <div class="eufdc_amazon_form">

                                                <div class="form-group">
                                                    <label for="eufdc_amazon_key"><?php _e("Amazon S3 Key", "") ?></label>
                                                    <input type="text" class="form-control eufdc_amazon_key" id="eufdc_amazon_key" name="eufdc_amazon_key" value="<?php echo esc_attr($amazon_key); ?>">
                                                </div>

                                                <div class="form-group">
                                                    <label for="eufdc_amazon_secret"><?php _e("Amazon S3 Secret", "") ?></label>
                                                    <input type="text" class="form-control eufdc_amazon_secret" id="eufdc_amazon_secret" name="eufdc_amazon_secret" value="<?php echo esc_attr($amazon_secret); ?>">
                                                </div>

                                                <div class="custom-control custom-switch mb-2">
                                                    <input type="checkbox" class="custom-control-input" id="eufdc_sync_zip" >
                                                    <label class="custom-control-label" for="eufdc_sync_zip"><?php _e('Sync as zip file', '') ?></label>
                                                </div>
                                                
                                                <div class="custom-control custom-switch mb-2">
                                                
                                                    <input type="checkbox" name="easy_ufdc_auto_sync" value="yes" class="custom-control-input" id="easy_ufdc_auto_sync" <?php checked(get_option('easy_ufdc_auto_sync', '')==1); ?> >
                                                    <label class="custom-control-label" title="<?php _e("Sync new added files to amazon automatically.",'easy-upload-files-during-checkout'); ?>" for="easy_ufdc_auto_sync"><?php _e('Auto Sync'); ?></label>
                                                    
                                                </div>


                                                <a type="submit" class="btn btn-success eufdc_connect_amazon text-white"><?php _e('Connect', 'easy-upload-files-during-checkout') ?></a>
                                                <a type="submit" class="btn btn-primary eufdc_sync_amazon text-white" <?php echo esc_attr($sync_disabled); ?>><?php _e('Sync now', 'easy-upload-files-during-checkout') ?></a>
                                                <span class="loading ml-2"><img src="<?php echo esc_url($wufdc_dir_url).'img/animation/'; ?>default.gif" width="25px" height="25px"/>  </span>
                                                <span class="connected" style="display: <?php echo esc_attr($connected_display); ?>"></span>
                                                <div class="my-2"><a href="https://aws.amazon.com/console/" target="_blank"><?php _e('How to setup an Amazon account', 'easy-upload-files-during-checkout') ?>?</a> </div>
                                                <div class="eufdc_amazon_alert"></div>


                                            </div>

                                        </div>

                                       
                                       
<?php 
	if($wc_ufdc_upload_dir!=''){
		
		$wc_ufdc_upload_dir = explode('/', $wc_ufdc_upload_dir);
		if(!empty($wc_ufdc_upload_dir)){

			$nodes = $wc_ufdc_upload_dir;
		
?>	
<ul class="ufdc_upload_dir_nodes eufdc_download_section">
<?php
			$nodes_arr = array();
			for($d=0; $d<=count($nodes); $d++){
				
				

				$node_dir = '';
				for($di=0; $di<$d; $di++){
					$node_dir .= $nodes[$di].'/';
				}
				
				if($node_dir){
					$valid_status = is_dir($node_dir);
					$writable_status = is_writable($node_dir);
					$node_dir = str_replace(array('ORDER_ID'), array(''), $node_dir);
					$node_dir = str_replace(array('//'), array('/'), $node_dir);
					if(!in_array($node_dir, $nodes_arr)){
						$nodes_arr[] = $node_dir;
?>			
<li class="<?php echo ($valid_status?'valid_node':'invalid_node'); ?> <?php echo ($writable_status?'writable':'not_writable'); ?> <?php echo $wc_ufdc_upload_dir==$node_dir?'active':'';?>">
<?php 					
					echo esc_url($node_dir);
					
					
					
					echo '<div class="eufdc_legends">';
					echo '<span>'.esc_html($valid_status?'Valid Directory':'Invalid Directory').'</span>';
					echo '&nbsp;|&nbsp;<span class="writable_status">'.esc_html($writable_status?'':'Not').' Writable</span>';
					echo '</div>';

 ?>
</li>
<?php
					}
				}
			}
?>		
</ul>
<?php 
		}
	}
?>	

</div>
                                	</td>

                                </tr>


                                <tr>
                                <th>
                                </th>
                                <td>
                                	
                                	<img class="eufdc-dimensions" src="<?php echo esc_url($wufdc_dir_url); ?>img/dimensions.png" />
                                </td>
                                </tr>              

                                <tr class="eufdc-dimensions-section d-none">
                        
                            		<th>

                                		<label for="woocommerce_ufdc_max_wh"><?php _e( 'Dimensions Check','easy-upload-files-during-checkout'); ?>:<br />

                                            <small>*<?php _e("For Images Only",'easy-upload-files-during-checkout');?></small>

                                        </label>

                            		</th>

                            		<td class="eufdc_premium">


                                        <span class="min_max"><?php _e("Min Width",'easy-upload-files-during-checkout');?>:</span>
                                        <input type="number" min="0" name="woocommerce_ufdc_min_w" class="short min_max mb-2" value="<?php if(!get_option( 'woocommerce_ufdc_min_w' )) { echo ''; } else { echo stripslashes(get_option( 'woocommerce_ufdc_min_w' )); }?>"/>&nbsp;

                                        <span class="min_max"><?php _e("Max Width",'easy-upload-files-during-checkout');?>:</span>
                                        <input type="number" min="0" name="woocommerce_ufdc_max_w" class="short min_max mb-2" value="<?php if(!get_option( 'woocommerce_ufdc_max_w' )) { echo ''; } else { echo stripslashes(get_option( 'woocommerce_ufdc_max_w' )); }?>"/><br/>

                                        <span class="min_max"><?php _e("Min Height",'easy-upload-files-during-checkout');?>:</span>
                                        <input type="number" min="0" name="woocommerce_ufdc_min_h" class="short min_max mb-2" value="<?php if(!get_option( 'woocommerce_ufdc_min_h' )) { echo ''; } else { echo stripslashes(get_option( 'woocommerce_ufdc_min_h' )); }?>"/>&nbsp;

                                        <span class="min_max"><?php _e("Max Height",'easy-upload-files-during-checkout');?>:</span>
                                        <input type="number" min="0" name="woocommerce_ufdc_max_h" class="short min_max mb-2" value="<?php if(!get_option( 'woocommerce_ufdc_max_h' )) { echo ''; } else { echo stripslashes(get_option( 'woocommerce_ufdc_max_h' )); }?>"/><br />


                                        <span class="description"><?php

                                        echo __( 'Leave empty for no restrictions.','easy-upload-files-during-checkout');

                                        ?></span>                                       

                                	</td>

                                </tr>	
                                
   									              
                                
                                
                                <tr>
                                	<th>

                                		<label for="woocommerce_ufdc_upload_anim"><?php _e( 'Loading Animation','easy-upload-files-during-checkout'); ?>:<br /></label>

                            		</th>
									<td class="eufdc_premium">
<?php
//update_option('eufdc_animations', false);
	if(!get_option('eufdc_animations', false)){
		
		$animations = 'https://plugins.svn.wordpress.org/easy-upload-files-during-checkout/assets/animation/';
		$response = wp_remote_get( $animations );
		if ( is_array( $response ) && class_exists('DOMDocument')) {
		  $header = $response['headers']; // array of http header lines
		  $body = $response['body']; // use the content
			  
			$doc = new DOMDocument();
			$doc->loadHTML($body);
			
			$links = array();
			foreach($doc->getElementsByTagName('a') as $elem) {
				if($elem->hasAttribute('href') && preg_match('/.*\.gif$/i', $elem->getAttribute('href'))) {
					$links []= $elem->getAttribute('href');
				}
			}  
			
			if(!empty($links)){
				foreach($links as $link){
					@copy($animations.$link, $wufdc_dir.'img/animation/'.$link);
				}
				update_option('eufdc_animations', true);
			}
		}		
	}
	
	$selected_anim = $wufdc_dir.'img/animation/'.get_option( 'woocommerce_ufdc_upload_anim', 'default.gif');
	
	if(!file_exists($selected_anim))
	$selected_anim = $wufdc_dir.'img/animation/default.gif';
	
	$selected_anim = str_replace($wufdc_dir, $wufdc_dir_url, $selected_anim);
?>                                    
                                    <a class="woocommerce_ufdc_upload_anim">
                                    <img src="<?php echo esc_url($selected_anim); ?>" />
                                    <b><?php _e('Change', 'easy-upload-files-during-checkout'); ?></b>
                                    </a>
                                    <?php 
										
										
								
										$anims = glob($wufdc_dir."img/animation/*.gif");
										
										//echo $wufdc_dir_url;
										if(!empty($anims)){
											
											if(count($anims)==1){
												update_option('eufdc_animations', false);
											}
?>
<ul class="eufdc_anims">
<?php											
											echo '';
											foreach ($anims as $filename) {
?>
<li data-name="<?php echo esc_attr(basename($filename)); ?>" <?php echo (get_option( 'woocommerce_ufdc_upload_anim', '')==basename($filename)?'class="selected"':''); ?>><img src="<?php echo esc_url(str_replace($wufdc_dir, $wufdc_dir_url, $filename)); ?>" /></li>
<?php												
											} 
?>
</ul>
<?php											
											echo '';
										}
									?>
										
                                        
                                  		
                                        <input name="woocommerce_ufdc_upload_anim" type="hidden" id="woocommerce_ufdc_upload_anim" value="<?php if(!get_option( 'woocommerce_ufdc_upload_anim' )) { echo 'default.gif'; } else { echo get_option( 'woocommerce_ufdc_upload_anim' ); }?>" />
                                       
                                       

                                	</td>                                    
                                </tr>                  													

                            
								<tr>

									<td  style="padding:0">


                                    </td>

                                    <td  style="padding:0">

                                        <p class="submit" style="float:left"><input type="submit" name="Submit" class="button-primary" value="<?php _e( 'Save Changes','easy-upload-files-during-checkout'); ?>" /></p>

                                    </td>

								</tr>

							</table>

                            <div class="optional">

                            <h3><?php _e("Optional",'easy-upload-files-during-checkout'); ?></h3>

                            	<fieldset>

                                	<ul>
									<?php echo (!$ufdc_custom?'<li><a style="color:yellow; float:right; font-weight:normal;" href="'.esc_url($ufdc_premium_link).'" target="_blank">'. __("Go Premium",'easy-upload-files-during-checkout') . '</a></li>':''); ?>
                                    <li>

                                	<input id="eufdc_email" name="eufdc_email" type="checkbox" value="1" <?php checked(get_option('eufdc_email', 0)); ?> /><label for="eufdc_email"><?php _e("Send Attachments in Email",'easy-upload-files-during-checkout'); ?></label>

                                    </li>
                                    
                                     <li class="eufdc_email" <?php echo (get_option('eufdc_email', 0)?'':'style="display:none"'); ?>>

                                	<input <?php disabled(!$ufdc_custom); ?> id="eufdc_email_link" name="eufdc_email_link" type="checkbox" value="1" <?php checked(get_option('eufdc_email_link', 0)); ?> /><label for="eufdc_email_link"><?php _e("Display File URLs Instead of Titles",'easy-upload-files-during-checkout'); ?></label>

                                    </li>

                                    <li <?php echo (get_option('eufdc_billing_off', 0)?'class="selected"':''); ?>>

                                	<input class="eufdc_checkout_options" id="eufdc_billing_off" name="eufdc_billing_off" type="checkbox" value="1" <?php checked(get_option('eufdc_billing_off', 0)); ?> /><label for="eufdc_billing_off"><?php _e('Billing Details','easy-upload-files-during-checkout'); ?> <strong><?php _e('On','easy-upload-files-during-checkout'); ?></strong>/<strong><?php _e('Off','easy-upload-files-during-checkout'); ?></strong></label>

                                    </li>

                                    <li <?php echo (get_option('eufdc_shipping_off', 0)?'class="selected"':''); ?>>

                                	<input class="eufdc_checkout_options" id="eufdc_shipping_off" name="eufdc_shipping_off" type="checkbox" value="1" <?php checked(get_option('eufdc_shipping_off', 0)); ?> /><label for="eufdc_shipping_off"><?php _e('Shipping Details','easy-upload-files-during-checkout'); ?> <strong><?php _e('On','easy-upload-files-during-checkout'); ?></strong>/<strong><?php _e('Off','easy-upload-files-during-checkout'); ?></strong></label>

                                    </li>

                                    <li <?php echo (get_option('eufdc_order_comments_off', 0)?'class="selected"':''); ?>>

                                	<input class="eufdc_checkout_options" id="eufdc_order_comments_off" name="eufdc_order_comments_off" type="checkbox" value="1" <?php checked(get_option('eufdc_order_comments_off', 0)); ?> /><label for="eufdc_order_comments_off"><?php _e("Order Comments",'easy-upload-files-during-checkout'); ?> <strong><?php _e('On','easy-upload-files-during-checkout'); ?></strong>/<strong><?php _e('Off','easy-upload-files-during-checkout'); ?></strong></label>

                                    </li>
                                    
                                    
                                      <li <?php echo (get_option('eufdc_secure_links', 0)?'class="selected"':''); ?>>

                                	<input class="eufdc_checkout_options" id="eufdc_secure_links" name="eufdc_secure_links" type="checkbox" value="1" <?php checked(get_option('eufdc_secure_links', 0)); ?> /><label for="eufdc_secure_links"><?php _e("Secure File Links",'easy-upload-files-during-checkout'); ?> <strong><?php _e('Off','easy-upload-files-during-checkout'); ?></strong>/<strong><?php _e('On','easy-upload-files-during-checkout'); ?></strong></label>

                                    </li>
                                    
                                    
									<li <?php echo (get_option('eufdc_secure_upload', 0)?'class="selected"':''); ?>>

                                	<input <?php disabled($easy_ufdc_page=='register'); ?> class="eufdc_checkout_options" id="eufdc_secure_upload" name="eufdc_secure_upload" type="checkbox" value="1" <?php checked(get_option('eufdc_secure_upload', 0)); ?> /><label for="eufdc_secure_upload"><?php _e("Upload Files After Login/Register",'easy-upload-files-during-checkout'); ?> <strong><?php _e('Off','easy-upload-files-during-checkout'); ?></strong>/<strong><?php _e('On','easy-upload-files-during-checkout'); ?></strong></label>

                                    </li>   
                                    <?php if(class_exists('finfo') || function_exists('mime_content_type')): ?>
                                    <li <?php echo (get_option('eufdc_server_side_check', 0)?'class="selected"':''); ?>>

                                        <input class="eufdc_checkout_options" id="eufdc_server_side_check" name="eufdc_server_side_check" type="checkbox" value="1" <?php echo checked(get_option('eufdc_server_side_check', 0)); ?> /><label for="eufdc_server_side_check"><?php _e("Check file content type on server side",'easy-upload-files-during-checkout'); ?> <strong><?php _e('Off','easy-upload-files-during-checkout'); ?></strong>/<strong><?php _e('On','easy-upload-files-during-checkout'); ?></strong></label>

                                    </li>
                                    <?php endif; ?>
                                    
                                    <li class="eufdc_premium"></li>
                                    

									<li <?php echo (get_option('eufdc_img_thumbnails', 0)?'class="selected"':''); ?>>

                                	<input <?php disabled(!$ufdc_custom); ?> class="eufdc_checkout_options" id="eufdc_img_thumbnails" name="eufdc_img_thumbnails" type="checkbox" value="1" <?php checked(get_option('eufdc_img_thumbnails', 0)); ?> /><label for="eufdc_img_thumbnails"><?php _e("File Thumbnails/Icons with Filename",'easy-upload-files-during-checkout'); ?> <strong><?php _e('Off','easy-upload-files-during-checkout'); ?></strong>/<strong><?php _e('On','easy-upload-files-during-checkout'); ?></strong></label>

                                    </li>   
                                                               

                                                                  

                                    </ul>

                                </fieldset>
        <?php

            $show_items_attachments = (get_option('eufdc_items_attachments') == '1');

        ?>

        <div class="table_wrapper product_page_settings <?php echo ($easy_ufdc_page == 'product') ? '' : 'd-none' ?>">
            <table>
                <tr class="">
                    <th>
                        <label title="<?php _e("Show attachments with cart and order items",'easy-upload-files-during-checkout'); ?>" for="eufdc_items_attachments"><?php _e("Display Attachments Under Product Title",'easy-upload-files-during-checkout'); ?>:</label>
                    </th>
                    <td><ul>
                            <li>

                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="eufdc_items_attachments" value="yes" class="custom-control-input" id="eufdc_items_attachments" <?php echo checked($show_items_attachments); ?> >
                                    <label class="custom-control-label" for="eufdc_items_attachments"></label>
                                </div>

                            </li>
                        </ul></td>
                </tr>
            </table>
        </div>                                
                                <div class="table_wrapper">
                                <table>
								<tr class="easy_ufdc_page_based_options <?php echo (!in_array($easy_ufdc_page, array('thank_you', 'customer_order'))?'':'hides'); ?>">
                                <th>
                                <label title="<?php _e("Refresh Page After Every Upload: Use this option if any third-party JavaScript isn't working after uploading the files.",'easy-upload-files-during-checkout'); ?>" for="easy_ufdc_page_checkout_refresh"><?php _e('Refresh Page'); ?>:</label>
                                </th>
                                <td><ul>
                                        <li>

                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" name="easy_ufdc_page_checkout_refresh" value="yes" class="custom-control-input" id="easy_ufdc_page_checkout_refresh" <?php checked(get_option('easy_ufdc_page_checkout_refresh', '1')); ?> >
                                                <label class="custom-control-label" for="easy_ufdc_page_checkout_refresh"></label>
                                            </div>

                                        </li>
                                        </ul></td>
                                </tr>
                                <tr>

    								<th>

    									<label for="easy_ufdc_caption"><?php _e( 'Instructions (for users)','easy-upload-files-during-checkout'); ?>:</label>

    								</th>
								
    								<td>

                                        <textarea id="easy_ufdc_caption" style="width:100%; height:60px" name="easy_ufdc_caption" placeholder="<?php echo esc_attr($easy_ufdc_error_default); ?>"><?php echo esc_textarea(get_option( 'easy_ufdc_caption' )?stripslashes(get_option( 'easy_ufdc_caption' )):''); ?></textarea>

    								</td>

    							</tr>    
                                
								<tr>

    								<th>

    									<label for="easy_ufdc_success"><?php _e( 'Success Message','easy-upload-files-during-checkout'); ?>:</label>

    								</th>

    								<td>

                                        <textarea id="easy_ufdc_success" style="width:100%; height:60px" name="easy_ufdc_success" placeholder="<?php echo esc_attr($easy_ufdc_success_default); ?>"><?php echo esc_textarea(stripslashes(get_option( 'easy_ufdc_success' ))); ?></textarea>

    								</td>

    							</tr>

                                    <tr>

                                        <th valign="top">

                                            <label for="easy_ufdc_multiple_switch"><?php _e( 'Multiple files?','easy-upload-files-during-checkout'); ?></label>

                                        </th>



                                        <td>

                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" name="easy_ufdc_multiple" class="custom-control-input" id="easy_ufdc_multiple_switch" <?php echo checked(get_option( 'easy_ufdc_multiple' )); ?> >
                                                <label class="custom-control-label" for="easy_ufdc_multiple_switch"></label>
                                            </div>

                                            <div class="eufdc_multiple_wrapper text-white mt-3">

                                                <input type="number" min="1" name="easy_ufdc_limit" class="regular-text form-control" value="<?php if(!get_option( 'easy_ufdc_limit' )) { echo '1'; } else { echo stripslashes(get_option( 'easy_ufdc_limit' )); }?>"/><br />

                                                <span class="description"><?php

                                                    echo __( 'Specify number of files allowed to upload (numbers only).','easy-upload-files-during-checkout');

                                                    ?></span>


                                                <?php if($ufdc_custom): ?>

                                                    <div class="prog_wrapper">

                                                        <span class="prog_description"><?php

                                                            echo __( 'Display all browse buttons at once or one after one?','easy-upload-files-during-checkout');

                                                            ?>
                                                        </span>

                                                        <ul>
                                                            <li>

                                                                <label for="easy_ufdc_prog_yes"><input type="radio" name="easy_ufdc_prog" id="easy_ufdc_prog_yes"  value="1" <?php if(get_option( 'easy_ufdc_prog' ) && get_option( 'easy_ufdc_prog' )==1) { echo 'checked="checked"'; } ?> /><?php _e("At once",'easy-upload-files-during-checkout');?></label>
                                                            </li>
                                                            <li>
                                                                <label for="easy_ufdc_prog_no"><input type="radio" name="easy_ufdc_prog" id="easy_ufdc_prog_no" value="0" <?php if(!get_option( 'easy_ufdc_prog' ) || get_option( 'easy_ufdc_prog' )!=1) { echo 'checked="checked"'; } ?> /><?php _e("One at a time",'easy-upload-files-during-checkout');?></label>
                                                            </li>
                                                        </ul>
                                                    </div>

                                                <?php endif; ?>
                                        </div>

                                        </td>



                                    </tr>
                                
                                
									<tr valign="top">

    								<th>

    									<label for="easy_ufdc_req"><?php _e( 'Make upload field required?','easy-upload-files-during-checkout'); ?></label>

    								</th>



    								<td>

                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="easy_ufdc_req" class="custom-control-input" id="easy_ufdc_req" <?php echo checked(get_option( 'easy_ufdc_req' )); ?> >
                                            <label class="custom-control-label" for="easy_ufdc_req"></label>
                                        </div>
<!--eufdc_premium-->
                                        <div class=" easy_ufdc_required_wrapper text-white mt-3">

                                            <input type="number" min="0" id="easy_ufdc_required_limit" name="easy_ufdc_required_limit" class="regular-text form-control mb-1" value="<?php if(!get_option( 'easy_ufdc_required_limit' )) { echo '0'; } else { echo stripslashes(get_option( 'easy_ufdc_required_limit' )); }?>"/>

                                            <div class="description eufdc_error  d-none text-warning mb-3"><?php

												echo __( '*Required files should be less than or equal to multiple files.','easy-upload-files-during-checkout'); ?>

                                            </div>

                                            <span class="description"><?php

                                                echo __( 'Specify number of files required to upload (numbers only).','easy-upload-files-during-checkout');?>

                                        </span>

                                        </div>


       									<span class="description">&nbsp;</span>

    								</td>

    							</tr>
                                
                                <tr class="easy_ufdc_req <?php echo esc_attr(get_option( 'easy_ufdc_req' )?'':'d-none'); ?>">

    								<th>

    									<label for="easy_ufdc_error"><?php _e( 'Error Message','easy-upload-files-during-checkout'); ?>:</label>

    								</th>

    								<td>

                                        <textarea id="easy_ufdc_error" style="width:100%; height:60px" name="easy_ufdc_error" placeholder="<?php echo esc_attr($easy_ufdc_error_default); ?>"><?php echo esc_html($easy_ufdc_error); ?></textarea>

    								</td>
                                    
                                    <tr valign="top" class="eufdc_input_field">

                                        <th>

                                            <label for="eufdc_input_text_field"><?php _e( 'File Description?','easy-upload-files-during-checkout'); ?></label>

                                        </th>



                                        <td>

                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" name="eufdc_input_text_field" class="custom-control-input" id="eufdc_input_text_field" <?php echo checked(get_option( 'eufdc_input_text_field' )); ?> >
                                                <label class="custom-control-label" for="eufdc_input_text_field"></label>
                                            </div>
<!--eufdc_premium-->


                                        </td>

                                    </tr>

                                    <tr valign="top" class="eufdc_input_field_label">

                                        <th>

                                            <label for="eufdc_input_text_label"><?php _e( 'File Description Label:','easy-upload-files-during-checkout'); ?></label>

                                        </th>



                                        <td>

                                            <?php

                                                $eufdc_input_text_label = get_option('eufdc_input_text_label', __("File Description", 'easy-upload-files-during-checkout'));

                                            ?>

                                            <div class="form-group">
                                                <input type="text" id="eufdc_input_text_label" name="eufdc_input_text_label" class="regular-text form-control mb-1" value="<?php echo esc_attr($eufdc_input_text_label); ?>">
                                            </div>                                         
<!--eufdc_premium-->


                                        </td>

                                    </tr>
                                
                                </table>
                                </div>

                            </div>

						</div>

					</div>

				

			</div>

		</form>


 <div class="nav-tab-content hide p-3">

            <div class="row mt-3">
                <div class="col-md-12">

                    <div class="h5">
                        <?php _e("This plugin is compatible with following plugins", "easy-upload-files-during-checkout"); ?>:
                    </div>
                    
                    <ul>
                    	<li>
                        	<a href="https://www.salesforce.org/" target="_blank">Salesforce - CRM Software: Cloud Computing Solutions For Every Business</a>
                        </li>
                    	<li>
                        	<a href="https://wordpress.org/plugins/custom-order-numbers-for-woocommerce/" target="_blank">Custom Order Numbers for WooCommerce</a>
                        </li>
                    </ul>

                </div>
            </div>

        </div>   

<?php //if($ufdc_custom){ ?>
		<form method="post" action="" id="optional_settings" class="nav-tab-content hide">
        <input type="hidden" name="eufdc_tn" value="<?php echo isset($_GET['t'])?esc_attr($_GET['t']):'0'; ?>" />
        
        <input type="hidden" name="ufdc_optional_fields_submitted" value="submitted">
                
        <?php wp_nonce_field( 'eufdc_optional_noce_action', 'eufdc_optional_noce_action_field' ); ?>
        
<?php
	$products = ((function_exists('eufdc_get_products') && isset($_GET['t']) && $_GET['t']==2)?eufdc_get_products():array());
	
	if(empty($products)){
?>        
<div class="alert alert-info" role="alert">
 <?php _e("Please refresh the page to load list of product.", "easy-upload-files-during-checkout") ?> <a class="btn-sm btn-danger text-decoration-none eufdc-refresh" role="button"><?php _e("Click here to refresh", "easy-upload-files-during-checkout") ?></a>
</div>             
<?php
	}
?>	
        <?php if(!$ufdc_custom): ?><br />
        <div class="wc_os_notes"><?php echo __('You may select products to enable upload feature. This feature is available in Premium Version only.', 'easy-upload-files-during-checkout').' '.__('By default all products are considered enabled.', 'easy-upload-files-during-checkout'); ?></div><br />
        <?php endif; ?>
        
        
        <?php
            
            
            if(!empty($products)){
				
        ?>
        <br />
        <div class="wc_os_notes"><?php echo __('Select from the following products to enable selected products only.', 'easy-upload-files-during-checkout').' '.__('By default all products are considered enabled.', 'easy-upload-files-during-checkout'); ?></div>
        
        
        <table border="0">
        <thead>
        <th></th>
        <th><?php _e('Product Names', 'easy-upload-files-during-checkout'); ?></th>
        <th><?php _e('Actions', 'easy-upload-files-during-checkout'); ?></th>
        </thead>
        <tbody>
        <?php	
                
				$eufdc_optional = get_option('eufdc_optional', array());
				
				
                foreach($products as $prod){
                $product = wc_get_product($prod->ID);
                
                $ticked = (array_key_exists('allowed_products', $eufdc_optional) && is_array($eufdc_optional['allowed_products']) && in_array($prod->ID, $eufdc_optional['allowed_products']));
        
        ?>
        
        <tr>
        <td><input id="wip-<?php echo esc_attr($prod->ID); ?>" <?php checked($ticked); ?> type="checkbox" name="eufdc_optional[allowed_products][]" value="<?php echo esc_attr($prod->ID); ?>" /></td>
        <td><label for="wip-<?php echo esc_attr($prod->ID); ?>"><?php echo esc_html($prod->post_title.' '.($product->get_price()?get_woocommerce_currency_symbol().$product->get_price():'')); ?></label>
        </td>
        <td><a href="<?php echo esc_url(get_edit_post_link($prod->ID)); ?>" target="_blank"><?php _e('Edit'); ?></a> - <a href="<?php echo esc_url(get_permalink($prod->ID)); ?>" target="_blank"><?php _e('View'); ?></a>
        </td>
        </tr>
        <?php
                }
        ?>
        </tbody>
        </table>
        <input type="hidden" name="eufdc_optional[allowed_products][]" value="0" />
        
        <p class="submit"><input type="submit" value="<?php _e('Save Changes','easy-upload-files-during-checkout'); ?>" class="button button-primary" id="eufdc-optional-submit" name="eufdc-optional-submit"></p>
        
        <?php    
            }
        ?>		
        
        <?php //pree(eufdc_custom_upload_dir()); ?>
        
        </form>



		<form method="post" action="" id="optional_settings" class="nav-tab-content hide">
        <input type="hidden" name="eufdc_tn" value="<?php echo isset($_GET['t'])?esc_attr($_GET['t']):'0'; ?>" />
        
             
        
        <code>
        
        //<?php _e('USE THE FOLLOWING STRING IN Salesforce META VALUE AGAINST ANY TYPE OF FIELD','easy-upload-files-during-checkout'); ?><br /><br />
        
        ORDER-ATTACHMENTS:  {_order_id}
        <br /><br />
		<?php _e('DEBUG WITH THIS:','easy-upload-files-during-checkout'); ?> <a href="<?php echo $debug_url = esc_url(admin_url().'?debug-salesforce'); ?>" target="_blank"><?php echo esc_html($debug_url); ?></a>
<br />
<br />

        //<?php _e('USE THE FOLLOWING FUNCTION TO GET ORDER ATTACHMENTS WITH DESCRIPTION','easy-upload-files-during-checkout'); ?><br /><br />
        
        &lt;?php <br /><br />
        
        if(function_exists('eufdc_get_order_attachments')){<br />
        	$get_order_attachments = eufdc_get_order_attachments(ORDER_ID);<br />
        }<br /><br />
        
        ?&gt;</code>

<hr>

            <div class="h5 mb-3">
                <?php _e("Action Hooks", "easy-upload-files-during-checkout"); ?><a href="https://plugins.svn.wordpress.org/easy-upload-files-during-checkout/assets/scripts/sample.php" target="_blank" style="float:right"><?php _e("Sample.php", "easy-upload-files-during-checkout"); ?></a>
            </div>

            <code>
                //<?php _e('USE THE FOLLOWING ACTION HOOK TO GET THE LINKS OF ALL ATTACHMENTS AFTER SUCCESSFUL ORDER CREATION', 'easy-upload-files-during-checkout'); ?>



                <br /><br />
                add_action('eufdc_order_attachments', 'eufdc_order_attachments_callback', 10, 2);<?php echo (str_repeat('<br />', 2)); ?>

                if(!function_exists('eufdc_order_attachments_callback')){
                <br /><br />
                  &nbsp;   function eufdc_order_attachments_callback($order_id, $attachement_links){
                <br />
                &nbsp;&nbsp; //<?php _e("Add your code here", "easy-upload-files-during-checkout") ?>
                <br /><br />
                $order = new WC_Order($order_id);<br /><br />
                $order_number = $order->get_order_number();<br />    <br />           
                &nbsp;  }
                <br />
                }



            </code>        
        
        </form>
        
        <form method="post" action="" id="cleanup_settings" class="nav-tab-content hide">
        <input type="hidden" name="eufdc_tn" value="<?php echo isset($_GET['t'])?esc_attr($_GET['t']):'0'; ?>" />

<div class="alert alert-warning" role="alert">
 <?php _e("Did you notice when customers upload files and they are stored in the uploads directory, but what to do if a customer does not complete the order?", "easy-upload-files-during-checkout") ?>
</div>       

<div class="mt-4 mb-4">
    
        
    <div class="btn-group float-start mb-4" role="group">
    
      <input type="checkbox" class="btn-check" checked="checked" id="orphan-statistics" autocomplete="off">
      <label class="btn btn-outline-primary" for="orphan-statistics"><?php _e("Fetch Statistics", "easy-upload-files-during-checkout") ?></label>
    
      <input type="checkbox" class="btn-check" id="orphan-files" autocomplete="off">
      <label class="btn btn-outline-primary" for="orphan-files"><?php _e("Fetch Orphan Files", "easy-upload-files-during-checkout") ?></label>
    </div>    

    <div class="eufdc-orphan-delete">
	    <a class="btn-sm btn-danger text-decoration-none eufdc-orphan-cleaner float-end" role="button"><?php _e("Click here to delete orphan Files", "easy-upload-files-during-checkout") ?> <i class="fas fa-trash-alt"></i></a>
    </div>    
    
    <div class="eufdc-orphan-actions">
	    <a class="btn-lg btn-success text-decoration-none eufdc-scanner" role="button"><?php _e("Click here to scan the Database", "easy-upload-files-during-checkout") ?> <i class="fas fa-database"></i></a>
    </div>
    

    <img class="eufdc-statistics-loading" src="<?php echo $wufdc_dir_url.'img/animation/default.gif'; ?>" />

</div>

<ul class="list-group orphan_statistics">
</ul>             
<ul class="list-group orphan_files">
</ul>        
        
        </form>
        
        <div class="nav-tab-content container-fluid hide" data-content="help">

        <div class="row mt-3 eufdc_help_section">
        
        	<ul class="position-relative">
            	<li><a class="btn btn-sm btn-info" href="https://wordpress.org/support/plugin/easy-upload-files-during-checkout/" target="_blank"><?php _e('Open a Ticket on Support Forums', 'easy-upload-files-during-checkout'); ?></a></li>
                <li><a class="btn btn-sm btn-warning" href="http://demo.androidbubble.com/contact/" target="_blank"><?php _e('Contact Developer', 'easy-upload-files-during-checkout'); ?></a><i class="fas fa-headset"></i></li>
                <li><iframe width="560" height="315" src="https://www.youtube.com/embed/5uFQX7G7pn4" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></li>
			</ul>                
        </div>

    </div>
        
</div>
</div>
<?php //}
}
}
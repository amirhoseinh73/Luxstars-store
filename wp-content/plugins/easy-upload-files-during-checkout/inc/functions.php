<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 


	
	function sanitize_eufdc_data( $input ) {
		if(is_array($input)){		
			$new_input = array();	
			foreach ( $input as $key => $val ) {
				$new_input[ $key ] = (is_array($val)?sanitize_eufdc_data($val):sanitize_text_field( $val ));
			}			
		}else{
			$new_input = sanitize_text_field($input);			
			if(stripos($new_input, '@') && is_email($new_input)){
				$new_input = sanitize_email($new_input);
			}
			if(stripos($new_input, 'http') || wp_http_validate_url($new_input)){
				$new_input = sanitize_url($new_input);
			}			
		}	
		return $new_input;
	}	
		


	global $easy_ufdc_page, $wufdc_dir, $wufdc_pro_file, $ufdc_custom;

	
	//echo include_once($wufdc_pro_file);exit;
	
	function wufdc_admin_enqueue_script(){
        global $ufdc_custom, $easy_ufdc_page, $eufdc_items_attachments, $current_screen;
	    $pages = array(
	            'easy_ufdc',
        );

        $translation_array = array(
			'current_screen' => $current_screen,
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'is_pro' => $ufdc_custom,
			'this_url' => admin_url( 'admin.php?page=easy_ufdc' ),
			'eufdc_tab' => (isset($_GET['t'])?sanitize_eufdc_data($_GET['t']):'0'),
			'easy_ufdc_page' => $easy_ufdc_page,
			'product_attachments' => ($easy_ufdc_page=='product' && $eufdc_items_attachments?true:false),
            'translation' => array(

                    'status' => __('Status', 'easy-upload-files-during-checkout'),
                    'error_code' => __('Error Code', 'easy-upload-files-during-checkout'),                    
					'error_message' => __('Error Message', 'easy-upload-files-during-checkout'),
                    'error_type' => __('Error Type', 'easy-upload-files-during-checkout'),
                    'view_file' => __('View File', 'easy-upload-files-during-checkout'),
                    'success' => __('Success', 'easy-upload-files-during-checkout'),
                    'key_required' => __('Amazon Key & Secret required.', 'easy-upload-files-during-checkout'),
                    'zip_file' => __('Zip file', 'easy-upload-files-during-checkout'),
                    'out_of' => __('Out of', 'easy-upload-files-during-checkout'),
                    'successfully' => __('synced successfully.', 'easy-upload-files-during-checkout'),
                    'premium' => __('This is a premium feature.', 'easy-upload-files-during-checkout'),
            ),
			'does_not_exist' => __('This file has already been deleted from server but footprints found in order meta data.', 'easy-upload-files-during-checkout'),
			'orphan_files_msg' => __('This file is an Orphan one, it was uploaded but never linked to any successful order.', 'easy-upload-files-during-checkout'),

        );
		//pre($translation_array);
	    if(isset($_GET['page']) && in_array($_GET['page'], $pages)){
	
			wp_enqueue_script( 'wufdc-popper', plugin_dir_url( dirname(__FILE__) ) . 'js/popper.min.js' );
			wp_enqueue_script( 'wufdc-bootstrap', plugin_dir_url( dirname(__FILE__) ) . 'js/bootstrap.min.js' );
			wp_enqueue_style( 'wufdc-bootstrap', plugins_url('css/bootstrap.min.css', dirname(__FILE__)), array());
			wp_enqueue_script( 'wufdc-fontawesome', plugin_dir_url( dirname(__FILE__) ) . 'js/fontawesome.min.js' );
			wp_enqueue_style( 'wufdc-fontawesome', plugins_url('css/fontawesome.min.css', dirname(__FILE__)), array());

	    }
        wp_enqueue_script( 'wufdc_scripts', plugin_dir_url( dirname(__FILE__) ) . 'js/admin_scripts.js', array('jquery'), time(), true );
		wp_enqueue_style( 'wufdc-style', plugins_url('css/admin.css?t='.date('Yhmi'), dirname(__FILE__)), array(), date('Yhmi'));

        wp_localize_script( 'wufdc_scripts', 'eufdc_obj', $translation_array );



    }
	
	
	/*if(!function_exists('wc_add_notice')){
		function wc_add_notice($error, $domain){
			
			return $error;
			
		}	 
	} 	*/
	if(!function_exists('eufdc_pre')){
	function eufdc_pre($data){
			if(isset($_GET['debug'])){
				eufdc_pree($data);
			}
		}	 
	} 	

	if(!function_exists('eufdc_pree')){
		function eufdc_pree($data){
				echo '<pre>';
				print_r($data);
				echo '</pre>';	
		}	 
	}

    if($ufdc_custom){
        include_once($wufdc_pro_file);
    }

    include_once ('functions-inner.php');


    if(!function_exists('eufdc_get_parent_post_id')){
        function eufdc_get_parent_post_id($force_process=false){
            global $wpdb, $woocommerce, $eufdc_default_parent_post_id, $is_view_order;
  
  
            if(!is_admin() || $force_process){

				$wc = (array)$woocommerce->session;



                $wc = array_values($wc);

                //pre($wc);pre(eufdc_get_the_user_ip());
               $parent_post_id = isset($wc[1])?$wc[1]:'';
			   //pre($eufdc_default_parent_post_id);
			   //$parent_post_id = eufdc_get_the_user_ip();

	
				$cart_contents = (isset($woocommerce->cart) && is_object($woocommerce->cart) && isset($woocommerce->cart->cart_contents) && is_array($woocommerce->cart->cart_contents))?$woocommerce->cart->cart_contents:array();

				if(!empty($cart_contents) && isset($wc[1]) && $eufdc_default_parent_post_id != ''){
					
					$post_parent_query = 'UPDATE '.$wpdb->posts.' SET post_parent="'.$parent_post_id.'" WHERE post_parent="'.$eufdc_default_parent_post_id.'" AND post_parent!=0';
					//pre($post_parent_query);
				    $result = $wpdb->query($post_parent_query);


                }else{

					$parent_post_id = $eufdc_default_parent_post_id;

				}
			}else{
				
				$parent_post_id = 0;
				
			}
			//pre($parent_post_id);
			
			return $parent_post_id;
			
		}
	}
		
	if(!function_exists('get_eufdc_uploaded_files')){
		function get_eufdc_uploaded_files($author_id=0, $postid=0){
			
			
			$files = array();			
			$uploaded = array();
			


			$args = array( 'posts_per_page' => -1, 'offset'=>0, 'post_type'=>array('attachment', 'attachment_order'), 'post_status'=>'inherit', 'post_parent'=>($postid?'':eufdc_get_parent_post_id()), 'order_by'=>'title', 'order'=>'ASC' );
			
			
			
			if($postid){
				$args['include'] = array($postid);
				//unset($args['post_parent']);
			}
			if($author_id){
				$args['author'] = $author_id;
			}					
			
			$files_arr = get_posts( $args );
			
			if(empty($files_arr)){
				$files_arr = eufdc_get_uploaded_temp_files();
			}
			
			if(!empty($files_arr)){
				foreach($files_arr as $fi=>$f_arr){
					$f_arr->eufc_index = get_post_meta($f_arr->ID, 'eufdc_index', true);
					$files[$fi] = $f_arr;
				}
			}
			
			
			return $files;
		}
	}
	
	function eufdc_get_uploaded_temp_files($eufdc_parent_post_id = 0, $product_id = ''){
		global $wpdb;
		$eufdc_parent_post_id = $eufdc_parent_post_id?$eufdc_parent_post_id:eufdc_get_parent_post_id();
		if($product_id){
			$meta_query = "SELECT * FROM ".$wpdb->prefix."posts p, ".$wpdb->prefix."postmeta pm WHERE p.ID=pm.post_id AND pm.meta_key='eufdc_product_id' AND pm.meta_value='$product_id'AND  p.post_status='inherit' AND p.post_type IN ('attachment', 'attachment_order') AND p.post_parent='$eufdc_parent_post_id' ORDER BY p.post_title ASC";
			
			$return = $wpdb->get_results($meta_query);
			
		}else{
			$return = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."posts WHERE post_status='inherit' AND post_type IN ('attachment', 'attachment_order') AND post_parent='$eufdc_parent_post_id' ORDER BY post_title ASC");
		}
		return $return;
	}	
	if(!(function_exists('eufdc_get_files_li_html'))){
	
		function eufdc_get_files_li_html($files){
		
		
			$uploaded = array();
			
			
			if(!empty($files)){
			
				foreach($files as $my_files){
					$eufdc_index = get_post_meta($my_files->ID, 'eufdc_index', true);
					$eufdc_product_id = get_post_meta($my_files->ID, 'eufdc_product_id', true);
					$eufdc_variation_id = get_post_meta($my_files->ID, 'eufdc_variation_id', true);
					$eufdc_product_id = ($eufdc_variation_id?$eufdc_variation_id:$eufdc_product_id);
					
					$delete_url = get_permalink();
					$delete_url = strpos($delete_url, '?')?$delete_url.'&':$delete_url.'?';

					$url = str_replace( ABSPATH, home_url('/'), wp_get_attachment_url($my_files->ID));
					
					$url = eufdc_secure_download_link($my_files->ID, $eufdc_index, $url);
					$url = eufdc_secure_download_link($my_files->ID, 0, $url);
					
                    $file_caption_input = eufdc_get_file_caption_html($my_files);
                    $eufdc_dashicon_collapse = $file_caption_input ? eufdc_dashicon_collapse() : '';

                    $file_list_item = '<li class="ready" data-product="'.esc_attr($eufdc_product_id).'" data-id="'.esc_attr($my_files->ID).'" eufdc_index="'.esc_attr($eufdc_index).'"><a href="'.esc_url($url).'" target="_blank">'.esc_html($my_files->post_title).'</a> '.$eufdc_dashicon_collapse.' <a class="delete" href="'.esc_url($delete_url.'eufdc-delete='.$my_files->ID).'"><small>'.__('Delete','easy-upload-files-during-checkout').'</small></a>'.$file_caption_input.'</li>';
					$uploaded[] = $file_list_item;					
				}
			}
			
			
			return $uploaded;
		
		
		}
	
	
	}
	if(!function_exists('add_file_to_upcoming_order')){
		function add_file_to_upcoming_order($inside=false){
			
			
			$files = get_eufdc_uploaded_files();	
			//$uploaded = array();
			$uploaded = eufdc_get_files_li_html($files);
			


	
		
			
	$easy_ufdc_caption = stripslashes(get_option( 'easy_ufdc_caption' ));	
	
?>
		<div id="wufdc_div">
<?php if($easy_ufdc_caption): ?>
			<h6><?php echo esc_html($easy_ufdc_caption); ?></h6><br />
<?php endif; ?>			
            
            

<ul>
		<?php 
		
		if(!empty($uploaded)){ echo wp_kses_post(implode('', $uploaded));/*escaped already inside the funciton*/  }else{ ?>

        

        <li><input type="file" name="file_during_checkout" /></li>
<?php
		}
?>        
</ul>   
    
</div>
            
            <div class="wufdc_legends <?php echo esc_attr($inside?'i_s':'o_s'); ?>">
            <div class="afs eufdc-msg eufdc-warning"><?php _e('Allowed file size','easy-upload-files-during-checkout'); ?>: <?php echo esc_html(eufdc_add_space_after_comma(get_option( 'easy_ufdc_max_uploadsize' ))); ?></div>
            <div class="aft eufdc-msg eufdc-warning"><?php _e('Allowed files','easy-upload-files-during-checkout'); ?>: <?php echo esc_html(get_option( 'easy_ufdc_allowed_file_types' )); ?></div>
            <div class="eufdc-gerr eufdc-error"></div>
            
            </div>
            <?php //if(!$inside): ?>
            
	  		<script type="text/javascript">jQuery(document).ready(function(){ 
				var eufdc_init = setInterval(function(){
					if((typeof eufdc_in_action=='undefined' || !eufdc_in_action) && typeof layered_js2!='undefined'){ clearInterval(eufdc_init); layered_js2(jQuery);	}else{ 
					//console.log(typeof eufdc_in_action); 
					} 
				}, 1000);
			});
            </script>
			 <?php //endif; ?>
		<?php	
		}
	}

	if(!function_exists('easy_ufdc_admin_menu')){
		function easy_ufdc_admin_menu() {
			$page = add_submenu_page('woocommerce', 'Upload Files During Checkout', 'Easy Upload Files', 'manage_woocommerce', 'easy_ufdc', 'easy_ufdc_page' );
		}	
	}

	function wufdc_enqueue_style() {
		wp_enqueue_style(
			'wufdc-style',
			plugins_url('css/style.css?t='.time(), dirname(__FILE__)),
			array(),
			date('Ymdhi')
		);
	}
	
	function wufdc_validate_extra_register_fields( $username, $email, $validation_errors ) {
	
		//if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
		//	 $validation_errors->add( 'billing_last_name_error', __( '<strong>Error</strong>: Last name is required!.', 'woocommerce' ) );
		//}
		return $validation_errors;
	}
	add_action( 'woocommerce_register_post', 'wufdc_validate_extra_register_fields', 10, 3 );	
	
	function wufdc_save_extra_register_fields( $customer_id ) {
		
			
		if ( isset( $_POST['eufdc_files_register'] ) ) {

		    $register_files = sanitize_eufdc_data($_POST['eufdc_files_register']);

			if(!empty($register_files)){

                update_user_meta( $customer_id, '_eufdc_files_register', $register_files );

                foreach ($register_files as $file_id){

                    $args = array(
                        'ID' => $file_id,
                        'post_author' => $customer_id,
                        'post_parent' => $customer_id,
                    );

                    update_post_meta($file_id, '_eufdc_attached_user', $customer_id);

                    wp_update_post($args);

                }

            }

		}
	}
	
	add_action( 'woocommerce_created_customer', 'wufdc_save_extra_register_fields' );
	
	function wufdc_enqueue_script() {
		global $woocommerce, $easy_ufdc_req, $easy_ufdc_error, $easy_ufdc_success, $easy_ufdc_page, $ufdc_custom, $easy_ufdc_page_checkout_refresh, $post, $easy_ufdc_multiple, $wp, $eufdc_items_attachments, $eufdc_localize_arr, $easy_ufdc_max_uploadsize;

		$cart_url = (function_exists('wc_get_cart_url')?wc_get_cart_url():$woocommerce->cart->get_cart_url());
		$checkout_url = (function_exists('wc_get_checkout_url')?wc_get_checkout_url():$woocommerce->cart->get_checkout_url());
		// Register the script


		// Localize the script with new data
		
		
		$doctypes = explode( ',', get_option( 'easy_ufdc_allowed_file_types' ) );
		$doctypes=array_map('trim',$doctypes);

		$REQUEST_URI = $_SERVER['REQUEST_URI'];
		$is_checkout = strpos($checkout_url, $REQUEST_URI);
		$is_cart = strpos($cart_url, $REQUEST_URI);
		
		$ufdc_limit = get_option( 'easy_ufdc_limit' );
        $required_limit = get_option( 'easy_ufdc_required_limit' );
		$required_limit = (!$ufdc_custom && $required_limit > 1 ? 1 : $required_limit);
        
        $ufdc_file_str = $required_limit > 1 ? 'files' : 'file';
		//pre($ufdc_limit);
								

		$translation_array = array(
			'cart_url' => $cart_url,
			'checkout_url' => $checkout_url,
			'is_cart' => (is_cart()),// || $is_cart),
			'is_checkout' => (is_checkout()),// || $is_checkout),			
			'is_product' => (is_product()),
			'is_view_order' => stripos(home_url( $wp->request ), 'view-order')!='',
			'is_thank_you' => stripos(home_url( $wp->request ), 'order-received')!='',
			'is_account_page' => is_account_page(),
			'product_url' => (is_product()?get_permalink():''),
			'this_url' => get_permalink(),
			'max_uploadsize' => $easy_ufdc_max_uploadsize,
			'max_uploadsize_bytes' => ($easy_ufdc_max_uploadsize*1024*1024),
			'allowed_filetypes' => implode(',', $doctypes),
			'url' => plugin_dir_url( dirname(__FILE__) ),
			'easy_ufdc_req' => $easy_ufdc_req,
			'required_limit' => $required_limit,
			'progress_text' => __('Uploading your files, please wait for the system to continue.','easy-upload-files-during-checkout'),
			'progress_error' => '<span class="green">'.__('Uploaded your files, please wait for the system to continue.','easy-upload-files-during-checkout').'</span>',
			'error_default' => $easy_ufdc_error,
			'error_default_checkout' => $easy_ufdc_error.' <a href="'.$cart_url.'">'.__('Click here to upload','easy-upload-files-during-checkout').'</a>',
			'error_valid_extension' => __('Please choose a valid file to upload!', 'easy-upload-files-during-checkout'),
			'proceed_to_checkout' => __('Proceed to Checkout', 'easy-upload-files-during-checkout'),
			'easy_ufdc_page' => $easy_ufdc_page,
			'product_attachments' => ($easy_ufdc_page=='product' && $eufdc_items_attachments?true:false),
			'is_custom' => $ufdc_custom,
			'uploaded_files' => count(get_eufdc_uploaded_files()),
			'eufdc_limit' => $ufdc_custom?$ufdc_limit:1,            
            'ajax_url' => admin_url( 'admin-ajax.php' ),
			'checkout_refresh' => $easy_ufdc_page_checkout_refresh,
			'upload_anim' => ($ufdc_custom?get_option( 'woocommerce_ufdc_upload_anim', 'default.gif'):'default.gif'),
			'easy_ufdc_prog' => ($ufdc_custom?get_option( 'easy_ufdc_prog', '0'):'0'),
			'eufdc_secure_upload' => get_option('eufdc_secure_upload', 0)?'Y':'N',
			'eufdc_secure_upload_error' => __('Sorry, you need to login/register to upload files.','easy-upload-files-during-checkout'),
			'is_user_logged_in' => is_user_logged_in()?'Y':'N',
			'eufdc_restricted' => (function_exists('eufdc_restriction_status')?!eufdc_restriction_status():''),
			'success_message' => $easy_ufdc_success,			
			'easy_ufdc_multiple' => $easy_ufdc_multiple,
			'eufdc_nonce' => wp_create_nonce('eufdc_nonce_action'),
			'eufdc_upload_element' => '<li><input type="file" name="file_during_checkout[]"></li>',
			'eufdc_upload_element_single' => '<li><input type="file" name="file_during_checkout"></li>',
            'eufdc_server_side_check' => get_option('eufdc_server_side_check', false),
			'easy_ufdc_caption' => stripslashes(get_option( 'easy_ufdc_caption' ))
		);
		
		//pre($translation_array);
		
		$translation_array['is_woocommerce'] = (is_woocommerce() || $translation_array['is_cart'] || $translation_array['is_checkout'] || $translation_array['is_account_page']);
		
		if($ufdc_custom && function_exists('get_eufdc_min_max_dimensions')){

			$eufdc_min_max_dimensions = get_eufdc_min_max_dimensions();

            if(is_single() && is_product()){

                $product_id = $post->ID;
                $eufdc_min_max_dimensions = get_eufdc_min_max_dimensions($product_id);
				
				$eufdc_max_files = get_post_meta($product_id, 'eufdc_max_files', true);
				$eufdc_required_files = get_post_meta($product_id, 'eufdc_required_files', true);
				
				$translation_array['eufdc_limit'] = $eufdc_max_files?$eufdc_max_files:$translation_array['eufdc_limit'];
				$translation_array['required_limit'] = $eufdc_required_files?$eufdc_required_files:$translation_array['required_limit'];


            }

            $translation_array['min_max_dimensions'] = $eufdc_min_max_dimensions;
				

		}



		//pre($translation_array);
		
        wp_register_script( 'eufdc', plugins_url('js/scripts.js', dirname(__FILE__)), array('jquery'), time(), true);
		wp_localize_script( 'eufdc', 'eufdc_obj', $translation_array );
		// Enqueued script with localized data.
		
		$eufdc_localize_arr = $translation_array;
		
		if(!is_admin()){
			wp_enqueue_script( 'jquery' );
			if(!is_user_logged_in()){

                wp_enqueue_style( 'dashicons' );

            }
		}		
		wp_enqueue_script( 'eufdc' );			

		/*wp_enqueue_script(
			'eufdc',
			plugins_url('js/scripts.js', __FILE__),
			array('jquery')
		);*/	

	}


	if(!function_exists('ufdc_file_during_checkout')){
		function ufdc_file_during_checkout(){
		
			
			$ret = (isset($_FILES['file_during_checkout']['name']) && $_FILES['file_during_checkout']['error']==0)?true:false;

			return $ret;
		}
	}
	
	function ufdc_custom_init(){
		if(isset($_GET['eufdc-delete']) && is_numeric($_GET['eufdc-delete'])){


			global $ufdc_limit;

			
			$postid = isset($_GET['eufdc-delete'])?sanitize_eufdc_data($_GET['eufdc-delete']):0;


			
			if($postid){
			
				$user_id = 0;
				if(is_user_logged_in()){
					$current_user = wp_get_current_user();
					$user_id = $current_user->ID;
				}
				
				$files = get_eufdc_uploaded_files($user_id, $postid);

               
			
				
				if(!empty($files)){
	
					$force_delete = true;
					wp_delete_post( $postid, $force_delete );
					wp_delete_attachment( $postid, $force_delete );
				
				}
			
			}
			
		}
	}

	if(!function_exists('ufdc_custom_file_upload')){
	 	function ufdc_custom_file_upload() {
			
			
			
			if(is_admin())
			return;

			if(isset(WC()->session) && empty(WC()->session)){

				WC()->session = new WC_Session_Handler();
				WC()->session->init();
			
			}			
			global $easy_ufdc_page, $easy_ufdc_req, $easy_ufdc_error;

			
			
			
			if(isset($_SERVER['HTTP_REFERER'])) {
				$same_page = (stristr($_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_URI']));
			}else{
				$same_page = false;
			}			
			//exit;
			if($same_page && !empty($_POST) && $easy_ufdc_req && !ufdc_file_during_checkout()){	
				
				switch($easy_ufdc_page){			
					case 'checkout':
					case 'checkout_notes':
						if(strpos($_SERVER['REQUEST_URI'], '/checkout')>0){
							wc_add_notice( $easy_ufdc_error, 'error' );				
						}
					break;
					
					case 'checkout_above':
					case 'checkout_above_content':					
					case 'cart':
					case '':
						if(strpos($_SERVER['REQUEST_URI'], '/cart')>0){
							wc_add_notice( $easy_ufdc_error, 'error' );					
						}
					break;
				}
			}
		}
	}


	if(!function_exists('ufdc_easy_ufdc_req')){	
		function ufdc_easy_ufdc_req(){
			global $woocommerce;
			
			$action_url = '';
			
			if(is_checkout()){
				$action_url = (function_exists('wc_get_checkout_url')?wc_get_checkout_url():$woocommerce->cart->get_checkout_url());
			}elseif(is_cart()){
				$action_url = (function_exists('wc_get_cart_url')?wc_get_cart_url():$woocommerce->cart->get_cart_url());
			}
?>
		<script type="text/javascript" language="javascript">
			jQuery(document).ready(function($){
				var eufdc_elem = 'input[name^="file_during_checkout"]:visible';
				if($(eufdc_elem).length>0){
					$('body').on('change', eufdc_elem, function(){
						$('form[method="post"]').attr('action', '<?php echo esc_url($action_url); ?>');
					});
					
					
					
					var act = setInterval(function(){
						$.each($(eufdc_elem).closest('form'), function(){
							var attr = $(this).attr('action');
							if (typeof attr !== typeof undefined && attr !== false) {
								$(this).removeAttr('action');
								clearInterval(act);
							}
						});
					}, 1000);					
				}
			});
		</script>
<?php			
		}
	}	

	if(!function_exists('file_during_checkout')){
		function file_during_checkout(){



			global $wpdb, $easy_ufdc_page, $woocommerce, $easy_ufdc_req, $easy_ufdc_error, $easy_ufdc_max_uploadsize, $default_upload_dir;
            
            

			$REQUEST_URI = $_SERVER['REQUEST_URI'];
			if(
				!empty($_FILES) 
			&& 
				!is_admin() 
			&&
				(
					(
						$easy_ufdc_req 
						&& 
						array_key_exists('file_during_checkout', $_FILES)
						)
						||
						array_key_exists('file_during_checkout', $_FILES)
					)
			){	
			
				$eufdc_parent_post_id = eufdc_get_parent_post_id();

				
				
				$cart_url = (function_exists('wc_get_cart_url')?wc_get_cart_url():$woocommerce->cart->get_cart_url());
				$checkout_url = (function_exists('wc_get_checkout_url')?wc_get_checkout_url():$woocommerce->cart->get_checkout_url());				
				
				$is_checkout = strpos($checkout_url, $REQUEST_URI);
				$is_cart = strpos($cart_url, $REQUEST_URI);
				
				if(is_account_page() || $is_checkout || $is_cart){
					switch($easy_ufdc_page){
						case 'checkout':		
						case 'checkout_notes':										
							$redir = $checkout_url;
						break;
						case 'checkout_above':
						case 'checkout_above_content':						
						case 'cart':
						default:					
							$redir = $cart_url;
						break;
						case 'register':
							$redir = get_permalink( get_option('woocommerce_myaccount_page_id') );
						break;
					}		
					

	
					if($easy_ufdc_req && !ufdc_file_during_checkout() && !wp_doing_ajax()){
						wc_add_notice(sprintf( $easy_ufdc_error, $ext ), 'error');
						wp_redirect($redir);
						exit;
					}elseif(ufdc_file_during_checkout()){
						$uploadedfile = $_FILES['file_during_checkout'];
						$file_during_checkout = $uploadedfile['name'];
						$file_during_checkout = explode('.', $file_during_checkout);
						$ext = end($file_during_checkout);
						$doctypes = explode( ',', get_option( 'easy_ufdc_allowed_file_types' ) );
						$doctypes = array_map('trim',$doctypes);
						$fi = 0;
						
						
						
						if(!empty($ext) && !in_array($ext, $doctypes) && !wp_doing_ajax()){
							wc_add_notice(sprintf( __( 'The').' %s '.__('file type is not allowed.','easy-upload-files-during-checkout'), $ext ), 'error');
							wp_redirect($redir);
							exit;
						}
	
						if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
						$uploadedfile = $_FILES['file_during_checkout'];

						$upload_overrides = array( 'test_form' => false );
						$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
						$ufdc_error = (isset($movefile['error']) || $uploadedfile['error']>0);
						
						
						if($ufdc_error){
							$wc_session = WC()->session;
							$wc_session->set('eufdc_file_type_error', $movefile['error']);
						}
						
						if ($ufdc_error && $uploadedfile['error']!=4) {
							$size_in_mb = round((($uploadedfile['size']/1024)/1024),2);
							$size_is_valid = ($size_in_mb<=$easy_ufdc_max_uploadsize);
							
							if(($size_is_valid && in_array($uploadedfile['error'], array(1)))){
								
								$upload_dir = $default_upload_dir;
								$upload_dir['basedir'] = rtrim($upload_dir['basedir'], '/') . '/';

								$target_file = ($upload_dir['basedir'].''.$uploadedfile['name']);
								
								$target_file_status = @move_uploaded_file($uploadedfile['tmp_name'], $target_file);
								
								$ufdc_error = !$target_file_status;
								
								if($target_file_status){
							
							
									$movefile['file'] = $target_file;
									$movefile['type'] = $uploadedfile['type'];
									$movefile['url'] = str_replace( ABSPATH, home_url('/'),  $movefile['file']);
									
									
								}
								
							}
							
							
			
						}
						
						if(isset($movefile['url'])){
							$movefile['url'] = str_replace(array('//'), array('/'), $movefile['url']);
							$movefile['url'] = str_replace(array('http:/', 'https:/'), array('http://', 'https://'), $movefile['url']);
						}
						

						if(!empty($movefile) && !$ufdc_error){				

							$myposts = $wpdb->get_results("
								SELECT 
									$wpdb->posts.* 
								FROM 
									$wpdb->posts                                     				
								WHERE 
									$wpdb->posts.post_parent = '".$eufdc_parent_post_id."'  
								AND 
									$wpdb->posts.post_type IN ('attachment', 'attachment_order')
								AND 
									(($wpdb->posts.post_status = 'inherit')) 
								ORDER BY 
									$wpdb->posts.post_date 
								DESC 
									LIMIT 0, 1"
								);
					
							if(!empty($myposts)){
								foreach($myposts as $post){
									wp_delete_post( $post->ID, true );
								}
							}

							// $filename should be the path to a file in the upload directory.
							$filename = $movefile['file'];
						
	
							// Check the type of tile. We'll use this as the 'post_mime_type'.
							$filetype = wp_check_filetype( basename( $filename ), null );
	
							// Get the path to the upload directory.
							$wp_upload_dir = wp_upload_dir();
							
							$movefile['url'] = str_replace(array('//'), array('/'), $movefile['url']);
							$movefile['url'] = str_replace(array('http:/', 'https:/'), array('http://', 'https://'), $movefile['url']);

	
							// Prepare an array of post data for the attachment.
							$attachment = array(
								'guid'           => $movefile['url'], 
								'post_mime_type' => $filetype['type'],
								'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
								'post_content'   => '',
								'post_status'    => 'inherit'
							);

							// Insert the attachment.
							$attach_id = wp_insert_attachment( $attachment, $filename, $eufdc_parent_post_id );
							add_filter( 'intermediate_image_sizes', '__return_empty_array' );
							// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
							//require_once( ABSPATH . 'wp-admin/includes/image.php' );
	
							// Generate the metadata for the attachment, and update the database record.
							//$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
							//wp_update_attachment_metadata( $attach_id, $attach_data );
														
							$files = get_eufdc_uploaded_files();
							
							
							if(count($files)>0){
							
								$uploaded = eufdc_get_files_li_html($files);
								
								$temp_files = (is_array($_FILES['file_during_checkout']['tmp_name']) ? array_filter($_FILES['file_during_checkout']['tmp_name']) : $_FILES['file_during_checkout']['tmp_name']);
								
								if((array_key_exists('file_during_checkout', $_FILES) && $temp_files) || array_key_exists('file_during_checkout', $_POST)){
									echo wp_kses_post(implode('', $uploaded));//escaped already inside the funciton
									exit;
								}
							
							}							
							
						}else{
                            $uploaded = eufdc_get_files_li_html(array());
                            echo wp_kses_post(implode('', $uploaded)); //escaped already inside the funciton
                            exit;
                        }
						
						
					}elseif($is_cart && !wp_doing_ajax()){
							wp_redirect($checkout_url);
							exit;
					}
				}
			}
					
			
		}
	}

	function pre_wc_checkout_order_processed($post_id){
		$post = get_post($post_id); 
		
		if(is_object($post) && $post->post_type=='shop_order'){		
			wc_checkout_order_processed($post_id);
		}
	}

	if(!function_exists('wc_checkout_order_processed')){
		function wc_checkout_order_processed($order_id){
			
			global $wc_ufdc_upload_dir;
			
		
			$eufdc_get_parent_post_id = eufdc_get_parent_post_id();
				
			if($eufdc_get_parent_post_id!='' && is_numeric($eufdc_get_parent_post_id) && $eufdc_get_parent_post_id>0){
			
				$args = array( 'posts_per_page' => 1, 'offset'=>0, 'post_type'=>array('attachment', 'attachment_order'), 'post_status'=>'inherit', 'post_parent'=>$eufdc_get_parent_post_id );
				$myposts = get_posts( $args );	
				
				
	
				if(!empty($myposts)){
					$my_post = (array)current($myposts);
					$my_post['post_type'] = 'attachment_order';
					$my_post['post_parent'] = $order_id;
					
					wp_update_post( $my_post );	
					$key = 1;
					update_post_meta( $order_id, '_woo_ufdc_uploaded_file_name_' . $key, sanitize_eufdc_data($my_post['post_title']) );
					update_post_meta( $order_id, '_woo_ufdc_uploaded_file_path_' . $key, sanitize_eufdc_data(str_replace(get_bloginfo('siteurl').'/', '', $my_post['guid'])) );
					update_post_meta( $order_id, '_woo_ufdc_uploaded_product_name_' . $key, 'Order ID: '.$order_id );
					if(function_exists('eufdc_do_action_after_files_attached')){ eufdc_do_action_after_files_attached($order_id); }
				}	
				
			}
		}
	}

	if(!function_exists('init_sessions')){
		function init_sessions(){
			if (!session_id()){
				ob_start();
				@session_start();
			}
		}
	}

	if(!function_exists('easy_ufdc_add_box_for_files')){
		function easy_ufdc_add_box_for_files() {
			add_meta_box( 'easy-ufdc-box-order-detail', __( 'Attached Files','easy-upload-files-during-checkout'), 'easy_ufdc_box_order_detail', 'shop_order', 'side', 'high' );
		}
	}

	if(!function_exists('add_order_attachments')){
		function add_order_attachments(  $order, $sent_to_admin, $plain_text=false, $email='' ) {
	
			if (get_option('eufdc_email', 0) && in_array($order->post_status, array('wc-on-hold', 'wc-processing', 'wc-pending', 'wc-completed'))) {
				global $woocommerce;
				$post = $order->post;
				$ret = '<h3>'. __("Attachments",'easy-upload-files-during-checkout') . '</h3>';
				$ret .= '<ul style="padding:0; margin:0; list-style:decimal outside;">';
				$i=1;
				$j=1;
				$upload_count=0;		
				$max_upload_count=1;
				while ($i <= $max_upload_count) {
					$name = get_post_meta( $post->ID, '_woo_ufdc_uploaded_file_name_' . $j, true );	
					$uploaded_file_path = get_post_meta( $post->ID, '_woo_ufdc_uploaded_file_path_' . $j, true );	
					$uploaded_file_path = str_replace(array('http:', 'https:'), '', $uploaded_file_path);
					$url = str_replace( ABSPATH, home_url('/'),  $uploaded_file_path);
					$url = (strpos($url, home_url('/'))?$url:home_url('/').$url);
					
					$forproduct = get_post_meta( $post->ID, '_woo_ufdc_uploaded_product_name_' . $j, true );
	
					if( !empty( $url ) && !empty( $name ) ) {
						$url = eufdc_secure_download_link($post->ID, $j, $url);
						$ret .= '<li>';
						$ret .= str_replace(array('_URL', '_NAME'), array($url, $name), ($sent_to_admin?'<a href="_URL" target="_blank">':'').'_NAME'.($sent_to_admin?'</a>':'') );
						$ret .= '</li>';
						$upload_count++;
					} else {
						//silence is golden
					}
					$i++;
					$j++;
				}
				$ret .= '</ul><br /><br /><br />';
				echo esc_html($upload_count?$ret:'');
				//exit;
			}
		}
	}

	//add_filter('woocommerce_email_recipient_customer_processing_order', 'wc_cc_store_email', 1, 2);

	add_action( 'woocommerce_email_before_order_table', 'add_order_attachments', 10, 2 );

	if(!function_exists('easy_ufdc_box_order_detail')){
		function easy_ufdc_box_order_detail($order) {
			
			$ret = '';
			$upload_count_outer = 0;
			if($order instanceof WP_Post && $order->ID){
				$order = wc_get_order($order->ID);
			}			
			$order_id = $order->get_id();
			
			$j=1;
			/* per product een formulier met gegevens */
			foreach ( $order->get_items() as $order_item ) {				
				$max_upload_count=1;
				//$max_upload_count=get_max_upload_count_plus($order,$order_item['product_id']);

			
				$forproduct = $order_item['name'];
				

				/* Controle of er al een bestand is geupload */

				$i=1;
				$upload_count=0;
				$retu = '';
				while ($i <= $max_upload_count) {
				
					$retf = '';
					$name = get_post_meta( $order_id, '_woo_ufdc_uploaded_file_name_' . $j, true );					
					$uploaded_file_path = get_post_meta( $order_id, '_woo_ufdc_uploaded_file_path_' . $j, true );		

					$order_id_based = stripos($uploaded_file_path, 'wc-orders');
					if($order_id_based!=''){
						$uploaded_file_path = str_replace(array('wc-orders/'), array('wc-orders/'.$order_id.'/'), $uploaded_file_path);										
					}					
					
					
					$uploaded_file_path = str_replace(array('http:', 'https:'), '', $uploaded_file_path);
					
                    $attachment = eufdc_get_file_by_path($order_id, $uploaded_file_path);

                    $file_caption_input = eufdc_get_file_caption_html($attachment, 'view');
                    $eufdc_dashicon_collapse = $file_caption_input ? eufdc_dashicon_collapse() : '';
					
					
					$url = str_replace( ABSPATH, home_url('/'),  $uploaded_file_path);
					//$url = (strpos($url, home_url('/'))?$url:home_url('/').$url); //16/09/2020
					$url = (substr($url, 0, 1)!='/'?'/'.$url:$url);
					
					$forproduct = get_post_meta( $order_id, '_woo_ufdc_uploaded_product_name_' . $j, true );
					if( !empty( $url ) && !empty( $name ) ) {
						$url = eufdc_secure_download_link($order_id, $j, $url);
						
						$retf .= sprintf( '<a href="%s" target="_blank">%s</a>', esc_url($url), $name );
						$upload_count++;
						$upload_count_outer++;
					} else {
						//silence is golden
					}
				$i++;
				$j++;
					if($retf){
						$retu .= '<li>'.$retf."$eufdc_dashicon_collapse $file_caption_input</li>";
					}
					
				}
				if($retu){
					$ret .= '<strong>';
					$ret .= sprintf( __('File for product','easy-upload-files-during-checkout').': "%s"', $forproduct);
					$ret .= '</strong><br>';
					$ret .= '<ul>'.$retu.'</ul>';
				}
			//}
			}
			
			echo $upload_count_outer?wp_kses_post($ret):'';
		}
	}

	function get_max_upload_count_plus($order,$order_item=0) {
		$max_upload_count=0;
		//product specifiek
		if( 
			(
				(
					is_array( get_option( 'easy_umf_status' )
				) 
				&& 
				in_array( $order->status, get_option( 'easy_umf_status' ) ) ) 
			) 
				|| 
				$order->status == get_option( 'easy_umf_status' ) 
		){
			if($order_item!=0) {
				$product = easy_umf_get_product($order_item);
				if( easy_umf_get_product_meta($product,'woo_umf_enable') == 1) {
					$max_upload_count=1;
				}
			} else {
			// order totaal
			foreach ( $order->get_items() as $order_item ) {
				$product = easy_umf_get_product($order_item['product_id']);
				$limit=1;
				if( easy_umf_get_product_meta($product,'woo_umf_enable') == 1 && $limit > 0 ) {
					$max_upload_count+=$limit;
				}
			}
			}
		}
		return $max_upload_count;
	}

	function ufdc_plugin_links($links) { 
		global $ufdc_premium_link, $ufdc_custom;
		$settings_link = '<a href="admin.php?page=easy_ufdc">' . __("Settings",'easy-upload-files-during-checkout') . '</a>';
		if($ufdc_custom){
			array_unshift($links, $settings_link); 
		}else{
			$ufdc_premium_link = '<a href="'.esc_url($ufdc_premium_link).'" title="' . __('Go Premium','easy-upload-files-during-checkout') . '" target="_blank">' . __("Go Premium",'easy-upload-files-during-checkout') . '</a>'; 
			array_unshift($links, $settings_link, $ufdc_premium_link); 
		}
		return $links; 
	}
	
	function eufdc_header_scripts(){
		global $ufdc_custom;
?>
	<style type="text/css">
	<?php
		if(get_option('eufdc_shipping_off', 0)){
?>
			.woocommerce-shipping-fields{
				display:none;	
			}
<?php			
		}
		if(get_option('eufdc_billing_off', 0)){
?>
			.woocommerce-billing-fields{
				display:none;	
			}
<?php			
		}
		if(get_option('eufdc_order_comments_off', 0)){
?>
		.woocommerce-additional-fields{
			display:none;
		}
<?php	
		}				
	?>
	<?php if($ufdc_custom): ?>
	
		<?php if(!get_option( 'easy_ufdc_prog', '0')): ?>
		div#wufdc_div ul li:not(.ready){
			display:none;
		}
		div#wufdc_div ul li:first-child,
		div#wufdc_div ul li.ready,
		div#wufdc_div ul li.ready + li:not(.ready){
			display:block;
		}
		<?php endif; ?>
	<?php endif; ?>
	</style>
<?php		
	}
	
	add_action('wp_head', 'eufdc_header_scripts');
	
	add_filter( 'woocommerce_checkout_fields' , 'eufdc_override_checkout_fields' );
	
	function eufdc_override_checkout_fields( $fields ) {
	
		if(get_option('eufdc_shipping_off', 0)){
			unset($fields['shipping']['shipping_first_name']);
			unset($fields['shipping']['shipping_last_name']);
			unset($fields['shipping']['shipping_company']);
			unset($fields['shipping']['shipping_address_1']);
			unset($fields['shipping']['shipping_address_2']);
			unset($fields['shipping']['shipping_city']);
			unset($fields['shipping']['shipping_postcode']);
			unset($fields['shipping']['shipping_country']);
			unset($fields['shipping']['shipping_state']);
			unset($fields['shipping']['shipping_phone']);	
			unset($fields['shipping']['shipping_email']);	
		}
		
		if(get_option('eufdc_billing_off', 0)){
			unset($fields['billing']['billing_first_name']);
			unset($fields['billing']['billing_last_name']);
			unset($fields['billing']['billing_company']);
			unset($fields['billing']['billing_address_1']);
			unset($fields['billing']['billing_address_2']);
			unset($fields['billing']['billing_city']);
			unset($fields['billing']['billing_postcode']);
			unset($fields['billing']['billing_country']);
			unset($fields['billing']['billing_state']);
			unset($fields['billing']['billing_phone']);	
			unset($fields['billing']['billing_email']);
		}
		
		if(get_option('eufdc_order_comments_off', 0))
		unset($fields['order']['order_comments']);
		
		return $fields;
	}
	
	function eufdc_custom_notes_on_single_order_page($order){
		
       	$note = easy_ufdc_box_order_detail($order);
		
		if(trim($note)){
			$order->add_order_note( $note );
		}

    }

    add_action( 'woocommerce_order_details_after_order_table', 'eufdc_custom_notes_on_single_order_page',10,1 );	
	
	function eufdc_secure_download_link($post_id, $j, $url){
		
		if(!is_admin() && get_option('eufdc_secure_links', 0)){
			$url = home_url('/?eufdc='.base64_encode($post_id.'.'.$j));		
		}
		return $url;
	}
	
	function eufdc_translate_file_link(){
		$eufdc = sanitize_eufdc_data($_GET['eufdc']);
		$eufdc = ($eufdc!=''?base64_decode($eufdc):'');
		
		$eufdc = explode('.', $eufdc);
		if(count($eufdc)==2){
			list($post_id, $j) = $eufdc;
			
			$theFile = '';
			
			if($j==0){
				global $wpdb, $woocommerce;
				
				$files = get_eufdc_uploaded_files(0, $post_id);
				
				if(!empty($files) && !wp_doing_ajax()){
					$files = current($files);
					$theFile = str_replace(home_url('/'), ABSPATH, $files->guid);
					wp_redirect($files->guid);exit;
				}

				
			}else{
				$upload_dir   = wp_upload_dir();
				$basedir = $upload_dir['basedir'];
				
				$_woo_ufdc_uploaded_file_path = get_post_meta( $post_id, '_woo_ufdc_uploaded_file_path_' . $j, true );
				
				$theFile = ABSPATH.($_woo_ufdc_uploaded_file_path?$_woo_ufdc_uploaded_file_path:get_post_meta($post_id, '_wp_attached_file', true));				
				$theFile = str_replace('//', '/', $theFile);
				$theFile = str_replace(ABSPATH.'wc-orders', $basedir.'/wc-orders', $theFile);
				
				if(!file_exists($theFile)){
					//$theFile = str_replace(ABSPATH, home_url('/'), $theFile);
					
					//wp_redirect($theFile);exit;
					$files = get_eufdc_uploaded_files(0, $post_id);
					if(!empty($files) && !wp_doing_ajax()){
						$files = current($files);
						$theFile = str_replace(home_url('/'), ABSPATH, $files->guid);
						wp_redirect($files->guid);exit;
					}
					
				}
				
			}
			if($theFile!='' && file_exists($theFile)){
				
				
							
				$file_url  = stripslashes( trim( $theFile ) );
				//get filename
				$file_name = basename( $theFile );
				//get fileextension
				
				$file_extension = pathinfo($file_name);
				//security check
				$fileName = strtolower($file_url);
				
				$whitelist = apply_filters( "ibenic_allowed_file_types", array('png', 'gif', 'tiff', 'jpeg', 'jpg','bmp','svg') );
				
				$file_name_array = ($fileName!=''?explode('.', $fileName):array());
				$file_name_ext = (is_array($file_name_array)?end($file_name_array):'');
				
				if(!in_array($file_name_ext, $whitelist)){				
				  	exit('Invalid file!');
				}
				if(strpos( $fileName , '.php' ) == true){
				   die("Invalid file!");
				}
				
				$file_new_name = $file_name;
				$content_type = "";
				//check filetype
				switch( $file_extension['extension'] ) {
					case "png": 
					  $content_type="image/png"; 
					  break;
					case "gif": 
					  $content_type="image/gif"; 
					  break;
					case "tiff": 
					  $content_type="image/tiff"; 
					  break;
					case "jpeg":
					case "jpg": 
					  $content_type="image/jpg"; 
					  break;
					default: 
					  $content_type="application/force-download";
				}
				
				$content_type = apply_filters( "ibenic_content_type", $content_type, $file_extension['extension'] );
				
				
				
				header("Expires: 0");
				header("Cache-Control: no-cache, no-store, must-revalidate"); 
				header('Cache-Control: pre-check=0, post-check=0, max-age=0', false); 
				header("Pragma: no-cache");	
				header("Content-type: {$content_type}");
				header("Content-Disposition:attachment; filename={$file_new_name}");
				header("Content-Type: application/force-download");
				
				readfile("{$file_url}");
				exit();				
			}
		}
	}
	
	if(isset($_GET['eufdc'])){
		
		add_action('wp', 'eufdc_translate_file_link');		
	}
	function eufdc_show_user_register_files(WP_User $user) {

	    global $ufdc_custom, $wufdc_dir_url;
	    $user_id = $user->ID;

	    $attached_files = get_user_meta($user_id, '_eufdc_files_register', true);

	    $eufdc_thumbnail = get_option('eufdc_img_thumbnails', false) && $ufdc_custom;
        $ext_thumb = $wufdc_dir_url.'/pro/filetype-icons/';
        $img_extensions = array(
                'jpg', 'jpeg', 'png', 'gif'
        );



	    if(empty($attached_files)){
	        return;
        }

        $args = array(

                'numberposts' => -1,
                'post_type' => array('attachment', 'attachment_order'),
                'include' => $attached_files,
        );

	    $attachments = get_posts($args);




        ?>
        <h2><?php _e('Files Attached', 'easy-upload-files-during-checkout') ?></h2>
        <table class="form-table">
            <tr>
                <th><label><?php _e('Files Attached', 'easy-upload-files-during-checkout') ?></label></th>
                <td>

                    <ul>

                    <?php

                        foreach ($attachments as $index => $attachment){

                            $guid = $attachment->guid;
                            $file_name = basename($guid);
                            $ext = pathinfo($file_name, PATHINFO_EXTENSION);

                            $ext_thumb_img = in_array($ext, $img_extensions) ? $guid : $ext_thumb.$ext.'.png';

                            $file_caption = eufdc_get_file_caption_html($attachment, 'view');
                            $eufdc_dashicon_collapse = $file_caption ? eufdc_dashicon_collapse() : '';


                            $thumb_size = in_array($ext, $img_extensions) ? 'width: 50px; border : 1px solid lightgray; padding : 2px;' : 'width : 20px';
                            $ext_img = $eufdc_thumbnail ? "<img src='".esc_url($ext_thumb_img)."' alt='' style='".esc_attr($thumb_size)."'>" : '';

                            
							echo "<li><a href='".esc_url($guid)."' target='_blank'>$ext_img <span style='display: inline-block; margin-bottom: 10px'>".esc_html($file_name)."</span></a>$eufdc_dashicon_collapse $file_caption </li>";

                        }

                    ?>

                    </ul>

                </td>
            </tr>
        </table>
        <?php
    }

	add_action('show_user_profile', 'eufdc_show_user_register_files');
	add_action('edit_user_profile', 'eufdc_show_user_register_files');
	
    if(!function_exists('eufdc_get_order_attachments')){

        function eufdc_get_order_attachments($order_id, $order_id_force=false){

            $attachments = array();

            if(!is_int($order_id)){

                return $attachments;
            }

            $post_args = array(

                'numberposts' => -1,
                'post_status' => 'any',
                'post_type' => 'attachment_order',
                'post_parent' => $order_id,

            );

            $attachment_posts = get_posts($post_args);

            if(!empty($attachment_posts)){

                foreach($attachment_posts as $attachment_index => $attachment){


                    $attachment_data = array();
					$order_id_based_guid = ($order_id_force?eufdc_order_id_based_item($order_id, $attachment):$attachment->guid);
                    $attachment_data['link'] = $order_id_based_guid;//$attachment->guid;
					$attachment_data['title'] = $attachment->post_title;
                    $attachment_data['caption'] = $attachment->post_content;

                    $attachments[$attachment->ID] = $attachment_data;
					
					
                }

            }


            return $attachments;

        }

    }

    if(!function_exists('eufdc_get_file_item_by_path')){
        function eufdc_get_file_by_path($order_id, $path){

            $order_attachments = eufdc_get_order_attachments($order_id);

            $match_id = false;
            if(!empty($order_attachments)){


                foreach($order_attachments as $attachment_id => $attachment_data){

                    $path_without_home = str_replace(home_url().'/', '', $attachment_data['link']);

                    if($path_without_home == $path){

                        $match_id = $attachment_id;

                        break;
                    }

                }
            }

            $match_attachment = false;
            if($match_id){

                $match_attachment = get_post($match_id);
            }

            return $match_attachment;

        }
    }



    if(!function_exists('eufdc_get_file_caption_html')){
        function eufdc_get_file_caption_html($file, $context = 'edit'){
            global $eufdc_input_text_label;
            $file_caption_input = '';
            $file_content = ((is_object($file) && isset($file->post_content))?trim($file->post_content):'');


            if($context == 'edit'){

                if(get_option('eufdc_input_text_field')){

                    $file_caption_input .= "<div class='eufdc_file_caption_wrapper'>";
//                    $file_caption_input .= "<label for='file_during_checkout_$file->ID'>$eufdc_input_text_label</label>";
                    $file_caption_input .= "<textarea id='file_during_checkout_$file->ID' class='eufdc_input_caption' placeholder='".esc_attr($eufdc_input_text_label)."' rows='1'>".esc_textarea($file_content)."</textarea>";
                    $file_caption_input .= "</div>";

                }


            }elseif($context == 'view' && $file_content){

                $file_caption_input .= "<div class='eufdc_file_caption_wrapper'>";
//                $file_caption_input .= "<div><strong>$eufdc_input_text_label</strong></div>";
                $file_caption_input .= "<p class='eufdc_info_caption'>".esc_html($file_content)."</p>";
                $file_caption_input .= "</div>";

            }


            return $file_caption_input;
        }
    }

    add_action('wp_ajax_nopriv_eufdc_update_file_caption', 'eufdc_update_file_caption_ajax');
    add_action('wp_ajax_eufdc_update_file_caption', 'eufdc_update_file_caption_ajax');

    if(!function_exists('eufdc_update_file_caption_ajax')){


        function eufdc_update_file_caption_ajax(){

            $result = array(
                'status' => false,
            );


            if(!empty($_POST) && isset($_POST['eufdc_file_id'])){


                if(!isset($_POST['eufdc_nonce']) || !wp_verify_nonce($_POST['eufdc_nonce'], 'eufdc_nonce_action')){

                    wp_die(__('Sorry, your nonce did not verify.', 'easy-upload-files-during-checkout'));

                }else{

                    //your code here


                    $file_id = sanitize_eufdc_data($_POST['eufdc_file_id']);

                    $file_caption = isset($_POST['eufdc_file_caption']) ? sanitize_eufdc_data($_POST['eufdc_file_caption']) : '';


                    if($file_id){

                        $file_args = array(
                            'ID'           => $file_id,
                            'post_content' => $file_caption,
                        );



                        $update_status = wp_update_post($file_args);

                        $result['status'] = $update_status > 0;

                    }

                }

            }

            wp_send_json($result);

        }
    }

    if(!function_exists('eufdc_dashicon_collapse')){
        function eufdc_dashicon_collapse(){

            global $wufdc_dir_url;

            $img_url = $wufdc_dir_url.'img/animation/default.gif';

            $title_show = __('Show caption', 'easy-upload-files-during-checkout');
            $title_hide = __('Hide caption', 'easy-upload-files-during-checkout');


            $ret = ' &nbsp; &nbsp; <span class="dashicons dashicons-edit-large eufdc-dashicons eufdc-dash-show" title="'.esc_attr($title_show).'"></span>
            <span class="dashicons dashicons-edit-large eufdc-dashicons eufdc-dash-hide" title="'.esc_attr($title_hide).'"></span>';

            $ret .= "<span class='eufdc_loading'><span class='loading_img'><img src='$img_url' alt=''></span><span class='dashicons dashicons-thumbs-up loading_complete'></span></span>";

            return $ret;

        }
    }

    if(!function_exists('eufdc_enqueue_common_scripts')){

        function eufdc_enqueue_common_scripts(){

            wp_enqueue_script( 'wufdc-common-js', plugin_dir_url( dirname(__FILE__) ) . 'js/common.js' );
            wp_enqueue_style( 'wufdc-common-css', plugins_url('css/common.css', dirname(__FILE__)), array(), date('Yhmi'));

        }

    }	
	
	if(!function_exists('eufdc_get_content_type_from_ext')){
		function eufdc_get_content_type_from_ext($ext){
			$mime_types = wp_get_mime_types();
			$mime_types['eps'] = array('application/postscript', 'image/x-eps');
            $mime_types['ai'] = array('application/postscript');
			//pree($mime_types);
			$content_type = '';
			if(!empty($mime_types)){
				foreach($mime_types as $extensions => $c_type){
					$extensions = explode('|', $extensions);
					$extensions = array_map('trim', $extensions);
					if(in_array(trim($ext), $extensions)){
						$content_type = $c_type;
						break;
					}
				}
			}
	
			return $content_type;
	
		}
    }

    if(!function_exists('eufdc_get_allowed_file_types')){
        function eufdc_get_allowed_file_types(){

            $doctypes = explode( ',', get_option( 'easy_ufdc_allowed_file_types' ) );
            $doctypes=array_map('trim',$doctypes);
            $doc_types_content = array();
            if(!empty($doctypes)){
                foreach($doctypes as $type){
                    $doc_types_content[$type] = eufdc_get_content_type_from_ext($type);
                }
            }

            return $doc_types_content;

        }
    }
	
	

    add_filter('wp_handle_upload_prefilter', 'eufdc_check_file_type_before_upload');
    if(!function_exists('eufdc_check_file_type_before_upload')){
        function eufdc_check_file_type_before_upload($file){
			
			//pree($file);exit;
			
            if(is_admin()){
                return $file;
            }
			if(!isset(WC()->session) || (isset(WC()->session) && !empty(WC()->session))){
            	
				WC()->session = new WC_Session_Handler();
				WC()->session->init();		
						
			}

            $compare_content = get_option('eufdc_server_side_check', 0);

            if(!$compare_content && (!function_exists('mime_content_type') || !class_exists('finfo'))){
                return $file;
            }

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $allowed_file_types = eufdc_get_allowed_file_types();
            $allowed_ext = array_keys($allowed_file_types);
            $allowed_ext_string = implode(', ', $allowed_ext);
            $ext_file_type = eufdc_get_content_type_from_ext($ext);
			$ext_file_type = (is_array($ext_file_type)?$ext_file_type:array($ext_file_type));
			//pree($ext);exit;
            $uploaded_content_type = function_exists('mime_content_type')?mime_content_type($file['tmp_name']):eufdc_mime_content_type($file['tmp_name']);

            $error_string = false;

            if(!array_key_exists($ext, $allowed_file_types)){

                $file['error'] = 1;
                $error_string = __('Allowed files:', 'easy-upload-files-during-checkout').' '.esc_html($allowed_ext_string);
            }elseif(!in_array($uploaded_content_type, $ext_file_type)){
                $file['error'] = 1;
                $error_string = __('File type did not match with the uploaded file extension.', 'easy-upload-files-during-checkout');
            }
			//pree($uploaded_content_type);pree($ext_file_type);
			//pree($error_string);exit;
			
			if(is_object(WC()->session) && $error_string){
				$wc_session = WC()->session;
            	$wc_session->set('eufdc_file_type_error', $error_string);
			}


            return $file;
        }
    }

    add_action('wp_ajax_nopriv_eufdc_get_file_upload_error', 'eufdc_get_file_upload_error');
    add_action('wp_ajax_eufdc_get_file_upload_error', 'eufdc_get_file_upload_error');

    function eufdc_get_file_upload_error(){

        $wc_session = WC()->session;
        $eufdc_file_error = $wc_session->get('eufdc_file_type_error', false);
        $result = array(
            'is_error' => $eufdc_file_error != false,
            'error_message' => $eufdc_file_error,
        );
        $wc_session->__unset('eufdc_file_type_error');
        wp_send_json($result);

    }
	function eufdc_mime_content_type($filename) {
		$result = new finfo();		
		return $result->file($filename, FILEINFO_MIME_TYPE);
		
	}
	add_action('wp_ajax_eufdc_delete_orphan_files', 'eufdc_delete_orphan_files');
	function eufdc_delete_orphan_files(){
		global $wpdb;
		
		$results = eufdc_get_orphan_files();
		$deleted = array();
		if(!empty($results)){
			foreach($results as $result){
				if(!$result->order_id){
					$deleted[] = $result->attachment_id;
					wp_delete_attachment($result->attachment_id);
					wp_delete_post($result->attachment_id);
				}
			}
		}
		
		echo json_encode(array('total'=>count($results), 'deleted'=>count($deleted)));exit;
	}
	
	function eufdc_get_orphan_files(){
		global $wpdb;
		$results = $wpdb->get_results("
			
			SELECT 
					p.ID AS attachment_id, 
					p.post_title, 
					o.ID AS order_id 
				FROM `".$wpdb->prefix."posts` p 
				
					LEFT JOIN `".$wpdb->prefix."posts` o 
					
						ON 
							(p.post_parent=o.ID AND o.post_type='shop_order') 
							
					INNER JOIN `".$wpdb->prefix."postmeta` pm 
					
						ON 
						
							(pm.post_id=p.ID AND pm.meta_key IN ('eufdc_index', 'eufdc_product_id', 'eufdc_variation_id')) 
				
				WHERE 
					
					p.post_type IN ('attachment', 'attachment_order') AND p.post_parent!='' 
				
				GROUP BY p.ID
		");
		
		return $results;
	}
	
	add_action('wp_ajax_eufdc_get_orphan_files_statistics', 'eufdc_get_orphan_files_statistics');
	function eufdc_get_orphan_files_statistics(){
		
		
		$statistics = sanitize_eufdc_data($_POST['statistics']);
		$statistics = ($statistics=='true');
		$orphan = sanitize_eufdc_data($_POST['orphan']);
		$orphan = ($orphan=='true');
		
		$ret = array('statistics'=>array(
				'total'=>array('title'=>__('Total', 'easy-upload-files-during-checkout'), 'value'=>0),
				'deleted'=>array('title'=>__('Do not exist', 'easy-upload-files-during-checkout'), 'value'=>0),
				'orphan'=>array('title'=>__('Orphan', 'easy-upload-files-during-checkout'), 'value'=>0),
				'linked'=>array('title'=>__('Linked', 'easy-upload-files-during-checkout'), 'value'=>0),
			), 'orphan_files'=>array());
		
		if($statistics || $orphan){
			
			
			$results = eufdc_get_orphan_files();
			
			if(!empty($results)){
				$ret['statistics']['total']['value'] = count($results);
				foreach($results as $result){
					if($result->order_id){
						if($statistics){
							$ret['statistics']['linked']['value'] += 1;
						}
					}else{
						if($statistics){
							$ret['statistics']['orphan']['value'] += 1;
						}
						
						if($orphan){
							$url = wp_get_attachment_image_src($result->attachment_id);		
							$url_link = ((is_array($url) && !empty($url))?$url[0]:'');
							
							$ret['orphan_files'][] = array('title'=>$result->post_title, 'url'=>$url_link);
							
							if(!$url_link){
								$ret['statistics']['deleted']['value'] += 1;
								$ret['statistics']['orphan']['value'] -= 1;
							}
						}
					}
				}
			}
		}
		echo json_encode($ret);exit;
	}
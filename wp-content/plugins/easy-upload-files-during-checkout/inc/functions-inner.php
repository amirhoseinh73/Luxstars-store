<?php
	
	if(!function_exists('eufdc_connect_to_amazon')){
		function eufdc_connect_to_amazon(){
			global  $ufdc_custom;
	
			if(isset($_POST['amazon_key']) && isset($_POST['amazon_secret'])){
	
				$eufdc_key = sanitize_eufdc_data(trim($_POST['amazon_key']));
				$eufdc_secret = sanitize_eufdc_data(trim($_POST['amazon_secret']));
	
				$eufdc_amazon_credential = array(
	
					'status' => 'not connected',
					'key' => $eufdc_key,
					'secret' => $eufdc_secret
	
				);
	
				if($ufdc_custom) {
	
					$s3 = eufdc_setup_s3client($eufdc_key, $eufdc_secret);
					$return_array = eufdc_verify_amazon_credential($eufdc_key, $eufdc_secret);
	
				}else{
	
					$return_array['status'] = 'not connected';
					$return_array['error_code'] = __('Not Priemium', 'easy-upload-files-during-checkout');
					$return_array['error_message'] = __('Your Credentials are saved, but you could not connect to Amazon.', 'easy-upload-files-during-checkout').' '.__('This is a premium feature.', 'easy-upload-files-during-checkout');
					
					$return_array['error_type'] = __('Client', 'easy-upload-files-during-checkout');;
	
				}
	
	
	
				if($return_array['status'] == 'connected'){
	
					$eufdc_amazon_credential['status'] = 'connected';
					$eufdc_amazon_credential['key'] = $eufdc_key;
					$eufdc_amazon_credential['secret'] = $eufdc_secret;
	
					update_option('eufdc_amazon_credential', $eufdc_amazon_credential);
					eufdc_initial_setup();
					$eufdc_bucket = eufdc_create_bucket();
	
					$eufdc_amazon_credential['bucket'] = $eufdc_bucket != false ? $eufdc_bucket: '';
	
				}
	
				update_option('eufdc_amazon_credential', $eufdc_amazon_credential);
				echo  json_encode($return_array);
			}
	
			wp_die();
		}
	}
	add_action('wp_ajax_eufdc_connect_to_amazon', 'eufdc_connect_to_amazon');
	
	if(!function_exists('eufdc_add_space_after_comma')){
	
		function eufdc_add_space_after_comma($string){
	
			if($string && is_string($string)){
	
				$string = str_replace(' ', '|||', $string);
				$string = str_replace(',', ', ', $string);
				$string = str_replace('|||', '', $string);
	
				return $string;
	
			}else{
	
				return $string;
			}
	
		}
	}


	//apply_filters('woo_salesforce_crmperks_post_data',$data,$feed['id']);
	
	if(isset($_GET['debug-salesforce'])){
		add_action('admin_init', function(){
			$upload_dir   = wp_upload_dir();
			$data = file_get_contents($upload_dir['basedir'].'/salesforce-data.html');
			$data = unserialize($data);
			
			if(function_exists('eufdc_get_order_attachments')){
				$data = eufdc_salesforce_crmperks_post_data($data);
				
				
				$data = file_get_contents($upload_dir['basedir'].'/salesforce-attachments.html');
				$data = unserialize($data);
				
				
			}else{

				echo __('PHP function','easy-upload-files-during-checkout').' eufdc_get_order_attachments '.__('is missing.', 'easy-upload-files-during-checkout').' '.__('Please contact Plugin Author.','easy-upload-files-during-checkout');
			}
			
			exit;
		});
		
	}
	
	function eufdc_salesforce_crmperks_post_data( $data ) {
	
	
		
		
		//if(is_admin()){
			//global $post;
		//}else{
			$post = array();
		//}
		$upload_dir   = wp_upload_dir();
		$f = fopen($upload_dir['basedir'].'/salesforce-data.html', 'w');
		fwrite($f, serialize($data));
		fclose($f);
		
		$order_id = 0;
		if(empty($post)){
			if(!empty($data)){
				
				foreach($data as $key=>$dataset){
					
					
					$dataset['value'] = str_replace(' ', '', trim($dataset['value'])); 
	
	
					if(substr($dataset['value'], 0, strlen('ORDER-ATTACHMENTS:'))=='ORDER-ATTACHMENTS:'){
						$order_id = trim(str_replace('ORDER-ATTACHMENTS:', '', $dataset['value']));
			
			
						if(is_numeric($order_id)){
							$data[$key]['value']='[ORDER-ATTACHMENTS]';
						
						
						}
						//exit;
					}
				}
			}
		}else{
			if(!isset($post->ID) || (isset($post->ID) && !is_numeric($post->ID))){ return $data; }
			$order_id = $post->ID;
		}
		
		if(!is_numeric($order_id) || $order_id==0){ return $data; }
			
		
		$order = wc_get_order($order_id);
		if(empty($order)){ return $data; }
		
		$order_attachments_str = '';
		if(function_exists('eufdc_get_order_attachments')){
			$get_order_attachments = eufdc_get_order_attachments($order->get_id(), true);
			
			$f = fopen($upload_dir['basedir'].'/salesforce-attachments.html', 'w');
			fwrite($f, serialize($get_order_attachments));
			fclose($f);
			
			if(!empty($get_order_attachments)){
				$c = 0;
				foreach($get_order_attachments as $order_attachments){ $c++;
					$order_attachments['caption'] = ($order_attachments['caption']?'('.$order_attachments['caption'].')':'');
					$order_attachments_str .= (count($get_order_attachments)>1?'<b>'.$c.'.</b> ':'').$order_attachments['link'].' '.$order_attachments['caption'].'<br /><br />';
				}
			}
		}
		
		if(!empty($data)){
			foreach($data as $key=>$dataset){				
				$data[$key]['value'] = str_replace('[ORDER-ATTACHMENTS]', $order_attachments_str, $dataset['value']);
			}
		}
		
		return $data;
	}
	add_filter( 'woo_salesforce_crmperks_post_data', 'eufdc_salesforce_crmperks_post_data' );



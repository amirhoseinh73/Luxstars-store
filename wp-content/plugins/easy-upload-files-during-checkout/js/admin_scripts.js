// JavaScript Document
	
	
	
	jQuery(document).ready(function($){
		
		$('.eufdc-refresh').on('click', function(){
			document.location.reload();
		});
		
		if($('.eufdc-product-attachment').length>0 && eufdc_obj.product_attachments=='true'){
			var c = 0;
			$.each($('.eufdc-product-attachment'), function(){ c++;
				$(this).parents().eq(1).addClass('variation-eufdc-attachments-'+c);
			});
			
		}
		setTimeout(function(){ $('table.woocommerce_order_items #order_line_items table.display_meta').fadeIn(1000); }, 500);
			
		$('.eufdc_settings_div .nav-tab-wrapper a.nav-tab').click(function(){
			$(this).siblings().removeClass('nav-tab-active');
			$(this).addClass('nav-tab-active');
			$('.nav-tab-content').hide();
			$('.nav-tab-content').eq($(this).index()).show();
			window.history.replaceState('', '', eufdc_obj.this_url+'&t='+$(this).index());
			eufdc_obj.eufdc_tab = $(this).index();
			$('form input[name="eufdc_tn"]').val($(this).index());
		});
		
		$('.eufdc_settings_div div.optional input[type="checkbox"]').click(function(){
			var obj_str = '.'+$(this).attr('id');
			if($(obj_str).length>0){
				if($(this).is(':checked')){
					$(obj_str).show();
				}else{
					$(obj_str).hide();
				}
			}
		});
				
		$('input[name="easy_ufdc_page"]').on('click', function(){
			switch($(this).val()){
				case 'register':
					$('#eufdc_secure_upload').prop({'checked':false, 'disabled':true});
				break;
				default:
					$('#eufdc_secure_upload').prop({'disabled':false});
				break;				
			}
			$('select[name="eufdc_product_page_positions"]').trigger('change');
		});
		
		$('select[name="eufdc_product_page_positions"]').on('change', function(){
			if($(this).val()=='woocommerce_before_add_to_cart_form' && $('#easy_ufdc_page_product').is(':checked')){
				$('.easy_ufdc_page_to_implement_div .iframe_div').show();
			}else{
				$('.easy_ufdc_page_to_implement_div .iframe_div').hide();
			}
		});
		setTimeout(function(){
			//$('select[name="eufdc_product_page_positions"]').trigger('change');
			$('input[name="easy_ufdc_page"]').trigger('change');
		}, 1000);
		
		$('.woocommerce_ufdc_upload_anim').on('click', function(){
			$('.eufdc_anims').show();
			$(this).hide();
		});
		
		$('.eufdc_anims li').on('click', function(){
			$('.eufdc_anims li.selected').removeClass('selected');
			$(this).addClass('selected');
			$('input[name="woocommerce_ufdc_upload_anim"]').val($(this).data('name'));
		});
	

		
		$('.eufdc-wp-paths').on('click', function(){
			$('.eufdc-wp-paths-div').slideToggle();
		});
		
		$('.eufdc-dimensions').on('click', function(){
			console.log($(this));
			$('.eufdc-dimensions-section').toggleClass('d-none');
		});
		
		$('.eufdc_checkout_options').on('click', function(){
			if($(this).is(':checked')){
				$(this).parent().addClass('selected');
			}else{
				$(this).parent().removeClass('selected');
			}
		});
		
		
		
		$('.eufdc_settings_div a.nav-tab').click(function(){
			$(this).siblings().removeClass('nav-tab-active');
			$(this).addClass('nav-tab-active');
			$('.nav-tab-content').hide();
			$('.nav-tab-content').eq($(this).index()).show();
		});

		$('.dropdown-toggle').on('click', function () {

			var id = $(this).attr('id');

			$('.dropdown-menu[aria-labelledby="'+id+'"]').toggle();

		});

		$('.eufdc_amazon_item').on('click', function(e){
			$('.eufdc_amazon_form_wrapper').toggle('slow');
			$('#eufdc_backup_btn').click();
		});

		$('.eufdc_connect_amazon').on('click', function(e){
			e.preventDefault();


			var amazon_key =  $('.eufdc_amazon_key').val();
			var amazon_secret =  $('.eufdc_amazon_secret').val()



			var data = {

				action:'eufdc_connect_to_amazon',
				amazon_key: amazon_key,
				amazon_secret: amazon_secret

			}

			if(amazon_key.length > 0 && amazon_secret.length > 0) {

				$('.loading').show();
				$('.connected').hide();
				$('.eufdc_amazon_alert').html('');

				jQuery.post(eufdc_obj.ajax_url, data, function (resp) {

					// console.log(resp);
					resp = JSON.parse(resp);
					if (resp.status == 'connected') {
						$('.connected').show();
						$('.eufdc_sync_amazon').prop('disabled', false);

					} else {
						var alert = `<div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">

                                                    <strong>` + eufdc_obj.translation.status + `: </strong>` + resp.status + `<br>
                                                    <strong>` + eufdc_obj.translation.error_code + `: </strong>` + resp.error_code + `<br>
                                                    <strong>` + eufdc_obj.translation.error_message + `: </strong>` + resp.error_message + `<br>
                                                    <strong>` + eufdc_obj.translation.error_type + `: </strong>` + resp.error_type + `<br>

                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>`;

						$('.eufdc_amazon_alert').html(alert);
						$('.eufdc_sync_amazon').prop('disabled', true);

					}

					$('.loading').hide();
				});
			}else{
				alert(eufdc_obj.translation.key_required);
			}

		});




		$('.eufdc-orphan-cleaner').on('click', function(e){
			e.preventDefault();
			
			var data = {
				'action':'eufdc_delete_orphan_files',
			}
			
			$('#cleanup_settings .list-group.orphan_statistics').html('');
			$('#cleanup_settings .list-group.orphan_files').html('');
			
			$('.eufdc-statistics-loading').show();
			$(this).hide();

			$.post(eufdc_obj.ajax_url, data, function(resp){
				$('.eufdc-statistics-loading').hide();
				$('#cleanup_settings .list-group.orphan_statistics').html('');
				$('#cleanup_settings .list-group.orphan_files').html('');			
				$('.eufdc-scanner').click();	
			});
			
		});
				
		$('.eufdc-scanner').on('click', function(e){
			e.preventDefault();
			
			var data = {
				'action':'eufdc_get_orphan_files_statistics',
				'statistics':$('#orphan-statistics').is(':checked'),
				'orphan':$('#orphan-files').is(':checked'),
			}
			
			$('#cleanup_settings .list-group.orphan_statistics').html('');
			$('#cleanup_settings .list-group.orphan_files').html('');
			
			$('.eufdc-statistics-loading').show();
			$('.eufdc-orphan-delete').hide();

			$.post(eufdc_obj.ajax_url, data, function(resp){

				resp = JSON.parse(resp);

				$.each(resp.statistics, function(i, v){
					
					$('#cleanup_settings .list-group.orphan_statistics').append('<li class="list-group-item">'+v.title+': '+v.value+'</li>');
				});
				
				$.each(resp.orphan_files, function(i, v){
					var tooltip = (v.url?eufdc_obj.orphan_files_msg:eufdc_obj.does_not_exist);
					
					$('#cleanup_settings .list-group.orphan_files').append('<li class="list-group-item"><a title="'+tooltip+'" href="'+v.url+'" target="url">'+v.title+' - '+(v.url?v.url:'<i class="fas fa-ban"></i>')+'</a></li>');
				});
				
				$('.eufdc-statistics-loading').hide();
				$('.eufdc-orphan-delete').show();
			});
			
		});
				
		$('.eufdc_sync_amazon').on('click', function(e){
			e.preventDefault();
			if(eufdc_obj.is_pro == '1'){
			$('.loading').show();
			$('.connected').hide();
			$('.eufdc_amazon_alert').html('');
			var eufdc_sync_zip = $('#eufdc_sync_zip');
			var eufdc_sync_zip_checked = 'off';

			if(eufdc_sync_zip.prop('checked') == true){
				eufdc_sync_zip_checked = 'on';
			}



			var data = {

				'action':'eufdc_sync_amazon_ajax',
				'eufdc_sync_amazon':'',
				'eufdc_sync_zip': eufdc_sync_zip_checked,
			}

			jQuery.post(eufdc_obj.ajax_url, data, function(resp){

				// console.log(resp);
				resp = JSON.parse(resp);
				var message = '';
				if(eufdc_sync_zip_checked == 'on'){

					message = resp.total_uploaded+` `+ eufdc_obj.translation.zip_file +` ` +eufdc_obj.translation.successfully;

				}else{

					message = resp.total_uploaded+` `+ eufdc_obj.translation.out_of +` `+resp.total_files + ` ` + eufdc_obj.translation.successfully ;

				}

				if(resp.status == 'uploaded'){

					$('.loading').hide();
					$('.connected').show();
					var alert = `<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">

                                                    <strong>`+eufdc_obj.translation.success+`: </strong>`+message+`
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>`;


				}else{

					var alert = `<div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">

                                                    <strong>`+resp.status+`: </strong>`+resp.error_message+`</a>
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>`;

				}

				$('.loading').hide();
				$('.connected').show();
				$('.eufdc_amazon_alert').html(alert);

			});
			}else{

				alert(eufdc_obj.translation.premium);
			}
		});

		$('#easy_ufdc_req').change(function(){

			if($(this).is(':checked')){

				$(this).val(1);
				$('.easy_ufdc_req').removeClass('d-none');
				$('.easy_ufdc_required_wrapper').show();


			}else{

				$(this).val(0);
				$('.easy_ufdc_req').addClass('d-none');
				$('.easy_ufdc_required_wrapper').hide();


			}


		});

		$('#easy_ufdc_multiple_switch').on('change', function(){

				if($(this).prop('checked')){
					$('.eufdc_multiple_wrapper').slideDown();
				}else{

					$('.eufdc_multiple_wrapper').slideUp();

				}

		});

		$('#easy_ufdc_multiple_switch').trigger('change');
		$('#easy_ufdc_req').trigger('change');

		$('#easy_ufdc_required_limit').on('change', function () {

			var easy_ufdc_limit = $('input[name="easy_ufdc_limit"]').val();


			if(parseInt($(this).val()) <= parseInt(easy_ufdc_limit)){

				$(this).next().addClass('d-none');
				$('.eufdc_settings_save_changes').prop('disabled', false);

			}else{

				$(this).next().removeClass('d-none');

				$(this).val(easy_ufdc_limit);
				$('.eufdc_settings_save_changes').prop('disabled', true);

			}

		});
		
		

		$('#easy_ufdc_required_limit').trigger('change');

		$('input[name="easy_ufdc_limit"]').on('change', function () {
			$('#easy_ufdc_required_limit').prop('max', $(this).val());
			$('#easy_ufdc_required_limit').trigger('change');

		});


		$('#wc_ufdc_upload_dir').on('keyup', function(){
			
			if($(this).data('value')==$(this).val()){
				$('.dropdown.eufdc_download_section').show();
				$('.ufdc_upload_dir_nodes').show();
				$(this).removeClass('w-100').addClass('w-75');
			}else{
				$('.eufdc_download_section').hide();
				$(this).removeClass('w-75').addClass('w-100');
			}
		});

		$('#easy_ufdc_multiple_switch').on('change', function(){

			if($(this).prop('checked')){

				$('#easy_ufdc_required_limit').prop('max', $('input[name="easy_ufdc_limit"]').val());


			}else{

				$('#easy_ufdc_required_limit').prop('max', 1);

				if($('#easy_ufdc_required_limit').val() > 0){

					$('#easy_ufdc_required_limit').val(1);

				}



			}

		});

		$('#easy_ufdc_multiple_switch').change();
		
		$('.eufdc_input_field td input[type="checkbox"]').on('change', function(){

            var this_checkbox = $(this);
            var label_tr = $('.eufdc_input_field_label');
            if(this_checkbox.prop('checked')){

                label_tr.show();

            }else{

                label_tr.hide();

            }

		});

		$('.eufdc_input_field td input[type="checkbox"]').change();		


		setTimeout(function(){
		
			$('input[name="easy_ufdc_page"]').on('change', function(){
		
		
				if($(this).val() == 'product'){
		
					$('.eufdc_settings_div .table_wrapper.product_page_settings').removeClass('d-none');
		
				}else{
		
					$('.eufdc_settings_div .table_wrapper.product_page_settings').addClass('d-none');
		
				}
			});
		
		}, 1100);
	});
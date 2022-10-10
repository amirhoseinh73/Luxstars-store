var eufdc_pro = (eufdc_obj.is_custom=='1');
var eufdc_file_type_error = false;
var eufdc_cart_input_updated = false;
var eufdc_target_element = '';
var eufdc_target_element_set = true;
var eufdc_shortcode_intv;

function eufdc_target_element_handling($){		
	if(eufdc_target_element!='' && typeof $(eufdc_target_element)=='object' && $(eufdc_target_element).length>0){
		if(eufdc_target_element_set){
			$(eufdc_target_element).show();
		}else{
			$(eufdc_target_element).hide();
		}
	}
}
function eufdc_check_required_files(e){
	$ = jQuery;

	if(eufdc_obj.easy_ufdc_req=="1" && eufdc_obj.uploaded_files<eufdc_obj.required_limit){
	
		if(e!=''){
			e.preventDefault();
		}
		
		if($('.eufdc-gerr').length>0){
			$('.eufdc-gerr').html(eufdc_obj.error_default).show();
			eufdc_target_element_set = false;
		
		
			$([document.documentElement, document.body]).animate({
				scrollTop: $('#wufdc_div').offset().top-100
			}, 500);
			
			
			
			if(eufdc_obj.is_custom!='1'){				
				
			}else{
				if($('#wufdc_div > ul').find('input[type="file"]').length==0){
					$('#wufdc_div > ul').append(eufdc_obj.eufdc_upload_element);
				}
			}
		
		}
	}
	
}	
jQuery(document).ready(function($) {

	
	if(eufdc_obj.easy_ufdc_page=='cart'){
		
		var eufdc_checkout_check = setInterval(function(){
			
			if(eufdc_obj.is_checkout){
				
				if(!(eufdc_obj.easy_ufdc_req !='1' || eufdc_obj.required_limit == 0 || eufdc_obj.uploaded_files > 0)){
					
					if(!(eufdc_obj.easy_ufdc_req !='1' || eufdc_obj.uploaded_files >= eufdc_obj.required_limit)){
						
						if($('.eufdc-gerr.eufdc-error').length==0){
							$('<div class="eufdc-gerr eufdc-error" style="display: block;">'+eufdc_obj.error_default_checkout+'</div>').insertBefore($('#place_order'));
						}						
						$('#place_order').prop('disabled', true);
						
					}else{
						
						$('.eufdc-gerr.eufdc-error').remove();
						$('#place_order').prop('disabled', false);
						clearInterval(eufdc_checkout_check);
					}
					
				}else{
					
					$('.eufdc-gerr.eufdc-error').remove();
					$('#place_order').prop('disabled', false);
					clearInterval(eufdc_checkout_check);
				}
				
			}else{
				
				clearInterval(eufdc_checkout_check);
				
			}
			
		}, 1000);	
		
	}
	
	if($('ul.wc-item-meta li .eufdc-product-attachment').length>0 && eufdc_obj.product_attachments=='true'){
		var c = 0;
		$.each($('.eufdc-product-attachment'), function(){ c++;
			$(this).parents().eq(0).addClass('variation-eufdc-attachments-'+c);
		});
		
	}
	setTimeout(function(){ $('ul.wc-item-meta').fadeIn(1000); }, 500);
	
	$('body').on('click', '#wufdc_div > ul > li a.delete', function(e){
		e.preventDefault();
		var liObj = $(this).parent();
		
		liObj.remove();
		$.blockUI({message:''});
		$.ajax($(this).attr('href')).done(function( data ) {
			eufdc_check_required_files('');			
			$.unblockUI();
		});
		eufdc_obj.uploaded_files = $('#wufdc_div > ul > li.ready').length;
		if($('#wufdc_div > ul > li').length==0){
			$('#wufdc_div > ul').append(eufdc_obj.eufdc_upload_element);
		}
		
	});
	
	

	if(eufdc_obj.is_checkout){

		$('body').on('click', 'input[type="submit"]:visible', function(){

		});
	
	
		$('body').on('click', '.eufdc_me button[name="woocommerce_checkout_place_order"]', function(){			
			$('.eufdc_me').removeClass('eufdc_me');
		});
		
	
	
	}
	
	if(eufdc_pro && eufdc_obj.is_product){
	
	
	}
	
	if(eufdc_obj.is_product && (document.URL.indexOf('add-to-cart')>=0 || document.URL.indexOf('eufdc-delete')>=0)){
		window.location.assign(eufdc_obj.this_url);
	}

	$('body').on('change', '.eufdc_file_caption_wrapper textarea', function(e){
		
		if(!$(this).is(':visible')){return;}

        var loading_img = $(this).parents('li:first').find('.eufdc_loading span.loading_img');
        var loading_complete = $(this).parents('li:first').find('.eufdc_loading span.loading_complete');
        loading_complete.hide();
        loading_img.css('display', 'inline-block');
        var request_data = {

            action : 'eufdc_update_file_caption',
            eufdc_file_id : $(this).parents('li:first').data('id'),
            eufdc_file_caption : $(this).val(),
            eufdc_nonce : eufdc_obj.eufdc_nonce,

        };


        $.post(eufdc_obj.ajax_url, request_data, function(response){

            loading_img.hide();
            loading_complete.css('display', 'inline-block');

            setTimeout(function(){

                loading_complete.hide();

            }, 3000);



        });



	});

});

var eufdc_in_action = false;

(function($) {
  $.fn.savy = function(order,fn) {
    const sv = "savy-";
    if (order == "load") {
      $(this).each(function() {
        if ($(this).is(":radio")) {
          if(localStorage.getItem(sv+$(this).attr("name"))){
            if (localStorage.getItem(sv+$(this).attr("name")) == this.id) {
              this.checked = true;
            }else{
              this.checked = false
            }
          }
          $(this).change(function() {
            localStorage.setItem(sv+$(this).attr("name"), this.id);
          });
        }else if($(this).is(":checkbox")){
          if(localStorage.getItem(sv+this.id)){
            this.checked = (localStorage.getItem(sv+this.id) == "1" ? true : false);
          }
          $(this).change(function() {
            localStorage.setItem(sv+this.id, (this.checked ? "1" : "0"));
          });
        }else if($(this).is("input") || $(this).is("textarea")) {
          if(localStorage.getItem(sv+this.id)){
            this.value = localStorage.getItem(sv+this.id);
          }
          $(this).keyup(function() {
            localStorage.setItem(sv+this.id, this.value);
          });
        }else if($(this).is("select")) {
          if ($(this).is("[multiple]")) {
            if(localStorage.getItem(sv+this.id)){
              $(this).val(localStorage.getItem(sv+this.id).split(","));
            }
            $(this).change(function() {
              localStorage.setItem(sv+this.id, $(this).val());
            });
          }else{
            if(localStorage.getItem(sv+this.id)){
              $(this).val(localStorage.getItem("savy-"+this.id));
            }
            $(this).change(function() {
              localStorage.setItem(sv+this.id, $(this).val());
            });
          }
        }
      });
      if ($.isFunction(fn)){fn();}
    }else if (order == "destroy") {
      $(this).each(function() {
        if(localStorage.getItem(sv+this.id)){
          localStorage.removeItem(sv+this.id)
        }
      });
      if ($.isFunction(fn)){fn();}
    }else{
      console.error("savy action not defined please use $('.classname').savy('load') to trigger savy to save all inputs")
    }
  };
})(jQuery);


function layered_js2($){


	
	//if(!eufdc_obj.is_woocommerce=='1'){
	if(!eufdc_check_custom_product() && !eufdc_obj.is_woocommerce=='1'){
		return;
	}



	eufdc_in_action = true; 
	
	var eufdc_proceed = true;
	var eufdc_locked = false;
	var eufdc_file_upload = false;
	
	$('.woocommerce form.cart-form, .woocommerce form.woocommerce-cart-form, .woocommerce .woocommerce-checkout:not(.eufdc-out), .eufdc_form_copy, .woocommerce-form-register').attr('enctype','multipart/form-data');

	$('.woocommerce form.cart-form, .woocommerce form.woocommerce-cart-form, .woocommerce .woocommerce-checkout:not(.eufdc-out), .eufdc_form_copy, .woocommerce-form-register').attr('encoding', 'multipart/form-data');


	var inpi = setInterval(function(){
		var obi = $('.woocommerce form.woocommerce-checkout:not(.eufdc-out) :input, .woocommerce.single-product form.eufdc_form :input, .single-product form.cart :input, .eufdc_form_copy :input, .woocommerce-form-register :input');
		if(obi.length>0){
			obi.not('input[name^="file_during_checkout"], input[type="submit"], input[name="wufdc_uploading"], .a-save-ignore').addClass('a-save');
			$('.a-save').savy('load');
			$('.a-save').trigger('change');
			clearInterval(inpi);
		}
	}, 100);

	var btn_co_val = ($('a.checkout-button').length>0?$('a.checkout-button').text():eufdc_obj.proceed_to_checkout);

	if(
		(
			eufdc_obj.is_cart=='1' && $('.woocommerce form.woocommerce-cart-form').find('input[name^="file_during_checkout"]:visible').length>0
			||
			eufdc_obj.is_cart=='1' && $('.woocommerce form.cart-form').find('input[name^="file_during_checkout"]:visible').length>0
		)
		||
		eufdc_obj.is_checkout=='1' && $('.woocommerce form.woocommerce-checkout:not(.eufdc-out)').find('input[name^="file_during_checkout"]:visible').length>0
	){

		$('.woocommerce form.cart-form, .woocommerce form.woocommerce-cart-form, .woocommerce form.woocommerce-checkout:not(.eufdc-out)').attr('action', eufdc_obj.checkout_url);

		if($('.wc-proceed-to-checkout input[type="submit"]').length==0 && eufdc_obj.is_cart=='1'){

		}

	}
	
	
	// CHECKOUT ONLY
	if(eufdc_obj.is_checkout=='1' && $('.woocommerce form.woocommerce-checkout:not(.eufdc-out)').length>0){
		if(eufdc_obj.checkout_refresh=='1'){
			$('.woocommerce form.woocommerce-checkout:not(.eufdc-out)').addClass('eufdc_me').css({'visibility':'visible'}).delay(100).fadeIn();
			
		}
		
		$('<span class="temp_checkout"></span>').insertAfter('.woocommerce form.woocommerce-checkout:not(.eufdc-out)');

		if(eufdc_obj.easy_ufdc_req=="1" && ((eufdc_obj.required_limit=='' && eufdc_obj.uploaded_files<1) || (eufdc_obj.required_limit!='' && eufdc_obj.uploaded_files<eufdc_obj.required_limit))){
			var target_field = $('input[name^="file_during_checkout"]').eq(0);
			target_field.closest('form').unbind( "submit" );
			
			
			
			
		}
		
		
	}	
			
	
	
			
	
	if(eufdc_obj.is_checkout=='1' && (eufdc_obj.easy_ufdc_page=='checkout_above' || eufdc_obj.easy_ufdc_page=='checkout_above_content')){
	
	
		$('body').on('click', 'form.checkout button[type="submit"], form.checkout input[type="submit"]', function(e){
		
			eufdc_check_required_files(e);
		
		});
	
	}	
	// CART ONLY
	if(eufdc_obj.is_cart=='1' && eufdc_obj.easy_ufdc_page=='cart' && $('.woocommerce .woocommerce-cart-form').length>0){		
		
			$('body').on('click', '.checkout-button', function(e){
				
				eufdc_check_required_files(e);
			});			
		
	}

		
	var allowed_filetypes = eufdc_obj.allowed_filetypes.split(',');
	
	$('body').on('change', 'input[name^="file_during_checkout"]:visible', function(){
		
		var t = 0;
		eufdc_proceed = true;

		if($('.woocommerce form.cart').length>0)
		$('.woocommerce form.cart').addClass('eufdc_form');
		
		if($('.woocommerce form.woocommerce-form-register').length>0)
		$('.woocommerce form.woocommerce-form-register').addClass('eufdc_form');		

		if($('.eufdc_form_copy').length>0)
		$('.eufdc_form_copy').addClass('eufdc_form');


		$('.wufdc_legends .eufdc-msg.eufdc-warning').hide();



		$('.wufdc_legends small').hide();

		$(this).closest('form').addClass('uploading');
		
		$('input[name^="file_during_checkout"]:visible').each(function(){
			var file_obj = $(this);
			file_obj.parent().find('small').remove();
			file_obj.removeClass('eufdc-wf');
			file_obj.removeClass('eufdc-bf');
			file_obj.removeClass('eufdc-fd');


			if(file_obj.val()!=''){

				var ext = file_obj.val().split('.').pop().toLowerCase();
				var size_mb = (((this.files[0].size/1024)/1024));
				size_mb = (size_mb>=1?(size_mb.toFixed(2)+'MB'):(size_mb*1024).toFixed(2)+'KB');

				$('<small>'+size_mb+'</small>').insertAfter(file_obj);
				file_obj.parent().removeClass('ignore');

				if($.inArray(ext, allowed_filetypes) == -1) {
					file_obj.addClass('eufdc-wf');
					$('.wufdc_legends .aft').show();

					if(eufdc_proceed){ eufdc_proceed = false; }

				}else{

				}

				if(this.files[0].size<=eufdc_obj.max_uploadsize_bytes){

				}else{
					file_obj.parent().find('small').addClass('eufdc-bf');
					file_obj.addClass('eufdc-bf');
					$('.wufdc_legends .afs').show();

					if(eufdc_proceed){ eufdc_proceed = false; }

				}


				if(eufdc_proceed && eufdc_pro){

					var img = new Image();
					img.src = window.URL.createObjectURL( this.files[0] );
					img.onload = function() {
						var width = img.naturalWidth, height = img.naturalHeight;

						window.URL.revokeObjectURL( img.src );

						var all_good = true;

						if(eufdc_obj.min_max_dimensions.minimum.width!='' && width < eufdc_obj.min_max_dimensions.minimum.width){
							all_good = false;
						}
						if(eufdc_obj.min_max_dimensions.minimum.height!='' && height < eufdc_obj.min_max_dimensions.minimum.height ) {
							all_good = false;
						}
						if(eufdc_obj.min_max_dimensions.maximum.width!='' && width > eufdc_obj.min_max_dimensions.maximum.width ) {
							all_good = false;
						}
						if(eufdc_obj.min_max_dimensions.maximum.height!='' && height > eufdc_obj.min_max_dimensions.maximum.height ) {
							all_good = false;
						}

						if(!all_good){
							file_obj.parent().find('small').addClass('eufdc-fd');
							file_obj.addClass('eufdc-fd');
							$('.wufdc_legends .afd').show();

							eufdc_proceed = false;
						}

					}
				}

				if(!eufdc_proceed){file_obj.parent().addClass('ignore');}


				t++;
			}


			if(t>0){
				$('.wc-proceed-to-checkout .checkout-button').val(btn_co_val);
			}else{
				$('.wc-proceed-to-checkout .checkout-button').val(btn_co_val);
			}
			
		});

	});


	$('input[name^="file_during_checkout"]:visible').eq(0).trigger('change');
	$('input[name^="file_during_checkout"]:visible').eq(0).closest('form').removeClass('uploading');
	

	function reload_wufdc_div(){
		
		if($('#wufdc_div > ul').length>0){
			$('#wufdc_div > ul > *:not(li)').remove();
		}

		
		

		if((eufdc_obj.easy_ufdc_page=='register' && eufdc_obj.is_account_page) || eufdc_check_custom_product() || eufdc_obj.is_product=='1' || eufdc_obj.is_cart=='1' || eufdc_obj.is_checkout=='1' || eufdc_obj.is_view_order){
				

				eufdc_obj.uploaded_files++;
				if(eufdc_obj.uploaded_files<eufdc_obj.eufdc_limit){
					var eufdc_left = eufdc_obj.eufdc_limit-eufdc_obj.uploaded_files;
					eufdc_left = eufdc_obj.easy_ufdc_multiple=='1'?eufdc_left:(eufdc_obj.uploaded_files==0?1:0);
					
					for(var i=1; i<=eufdc_left; i++){
						$('#wufdc_div > ul').append(eufdc_obj.eufdc_upload_element);
					}
					
					switch(eufdc_obj.easy_ufdc_page){
						default:
							$.each($('#wufdc_div > ul > li a.delete'), function(){
								var href = $(this).attr('href');
								if(href.substr(0,1)=='?'){
									if(eufdc_obj.product_url.includes('?')){
										href = href.replace('?','&');
									}
									$(this).attr('href', eufdc_obj.this_url+href);
								}
							});
						break;
					}												
				}else{

				}		
				$('.wufdc-progress-text, .wufdc-progress-div').fadeOut();				
				if(!eufdc_file_type_error){
					$('form .eufdc-msg.eufdc-success').fadeIn().delay(10000).fadeOut();
				}
				
				if(eufdc_obj.checkout_refresh=='1' || eufdc_obj.easy_ufdc_page=='thank_you' || eufdc_obj.easy_ufdc_page=='customer_order'){
					window.location.assign(document.URL);
				}else{					
					if(!eufdc_file_type_error && $('.eufdc-gerr').length>0)
					$('.eufdc-gerr').html('').hide();					
				}
				
		}else{


			var data = {'action': 'file_to_upcoming_order'};
			
			$.post(eufdc_obj.ajax_url, data, function(response) {		
				
				$( "div#wufdc_div" ).replaceWith( response );
				$('.a-save').savy('load');
				

			});		
		}
		
		
	}	
	function reload_progress_bar(){
		if($('input[name^="file_during_checkout"]:visible').length>0){
			if($('div#wufdc_div div.wufdc-progress-div').length==0){
				$('div#wufdc_div').append('<div class="wufdc-progress-text">'+eufdc_obj.progress_text+'</div><div class="wufdc-progress-div"><div class="wufdc-progress-bar"></div></div><div class="wufdc-targetLayer"></div><div class="wufdc-loader-icon"><img src="'+eufdc_obj.url+'img/animation/'+eufdc_obj.upload_anim+'" class="eufdc_loader" /></div><input type="hidden" name="wufdc_uploading" /><div class="eufdc-msg eufdc-success">'+eufdc_obj.success_message+'</div>');
			}
		}
	}
	
	reload_progress_bar();
	

	function required_file_msg(add_new){

		$('.eufdc_register_required_file').remove();

		if(add_new){
			var require_element = "<div class='eufdc_register_required_file eufdc-msg eufdc-warning' style='display: block'>"+eufdc_obj.error_default+"</div>";
			$('.woocommerce-form-register__submit').before(require_element);
		}

	}	
	
	$('body').on('change', 'form.woocommerce-cart-form input.qty, form.woocommerce-cart-form input#coupon_code', function(){
		eufdc_cart_input_updated = true;
	});
	
	$('body').on('submit', '.woocommerce form.cart-form, form.woocommerce-cart-form, form.woocommerce-checkout:not(.eufdc-out), form.cart.eufdc_form, .woocommerce form.eufdc_form:visible, form.eufdc_form_copy, .woocommerce form.woocommerce-form-register', function(e) {
		
		if(eufdc_obj.easy_ufdc_page=='cart'){
			if(eufdc_obj.uploaded_files==eufdc_obj.easy_ufdc_req && eufdc_cart_input_updated && $('#wufdc_div input[name="file_during_checkout"]:visible').length==0){				
				return;
			}
		}

		required_file_msg(false);

		if(!eufdc_file_upload && eufdc_obj.easy_ufdc_page=='register' && eufdc_obj.is_account_page){
			if(eufdc_obj.easy_ufdc_req !='1' || eufdc_obj.required_limit == 0 || eufdc_obj.uploaded_files > 0){
				var uploadad_li = $('#wufdc_div ul li[eufdc_index]');				

				if(eufdc_obj.easy_ufdc_req !='1' || eufdc_obj.uploaded_files >= eufdc_obj.required_limit){
					$.each(uploadad_li, function(){
	
						var file_id = $(this).data('id');
	
						$(this).append("<input type='hidden' name='eufdc_files_register[]' value='"+file_id+"' />");
					});

					return true;


				}else{
					required_file_msg(true);
					return false;
				}

			}else{
				required_file_msg(true);

				return false;


			}
		}

		



		eufdc_file_upload = false;
				
		var form_object = $(this);

		eufdc_obj.required_limit =  eufdc_obj.required_limit*1;

		var eufdc_files = $('div#wufdc_div li.ready').length;
		var eufdc_uploading = false;
		$('input[name^="file_during_checkout"]:visible').each(function(){

			if($(this).val()!='' && !eufdc_uploading){	 
				eufdc_uploading=true;
				eufdc_files = (eufdc_files==0?1:eufdc_files);
			}
		});
		

		
		if((eufdc_obj.is_product=='1' || eufdc_check_custom_product()) && !eufdc_uploading){
			
			var eufdc_add_to_cart = (eufdc_obj.easy_ufdc_req!='1' || (eufdc_obj.easy_ufdc_req=='1' && (eufdc_obj.required_limit=='' || (eufdc_obj.required_limit!='' && eufdc_files>=eufdc_obj.required_limit))));

			if(eufdc_add_to_cart){

				return true;

			}else{
				if($('.eufdc-gerr').length>0){
					$('.eufdc-gerr').html(eufdc_obj.error_default).show();	
					return false;			
				}
			}
		}

	 	e.preventDefault();
		reload_progress_bar();
		
		
		
		if(eufdc_files && eufdc_proceed) {

			//30-08-2018: snippet added to manage checkout page errors on form submit - START
			if(eufdc_obj.is_checkout=='1'){
				var woo_err = setInterval(function(){
					if($('.woocommerce-error').length>0){
						$('.woocommerce-error').remove();
						clearInterval(woo_err);
						$('html, body').animate({
							scrollTop: $("#wufdc_div").offset().top
						}, 2000);
					}
				}, 10);
			}		
			//30-08-2018: snippet added to manage checkout page errors on form submit - END
			
			$('#loader-icon').show();
			$(this).ajaxSubmit({		
				target: '#wufdc_div > ul',
				beforeSubmit: function(f) {
					$('div.wufdc_legends  .eufdc-gerr.eufdc-error').text('').hide();
					
					if(eufdc_obj.easy_ufdc_page=='register' && eufdc_obj.is_account_page == '1'){

						f.push({name : 'file_during_checkout', value : true});

						$.each(f, function(key, value){


							if( value != undefined && value.name == 'email'){

								f.splice(key, 1)
							}
						});

					}					
					
					$('form.eufdc_form .eufdc-msg.eufdc-success').hide();
					$(".wufdc-progress-bar").width('0%');
					$('div#wufdc_div .wufdc-progress-div, div#wufdc_div .wufdc-progress-text').fadeIn();
					$('div#wufdc_div li.ignore').remove();
					$('div#wufdc_div .wufdc-progress-div').removeClass('green');
					$('div#wufdc_div .wufdc-progress-text').html(eufdc_obj.progress_text);
					$('div#wufdc_div .wufdc-loader-icon').show();
					$('div#wufdc_div .in-progress').attr('disabled', 'disabled');
					$('.wc-proceed-to-checkout input[type="submit"]').attr('disabled', 'disabled');
					$('form.woocommerce-checkout:not(.eufdc-out) input[type="submit"]').attr('disabled', 'disabled');
				},
				uploadProgress: function (event, position, total, percentComplete){
					$(".wufdc-progress-bar").width(percentComplete + '%');
					$(".wufdc-progress-bar").html('<div class="wufdc-progress-status">' + percentComplete +' %</div>');


					if(percentComplete>95){
						$('.wc-proceed-to-checkout input[type="submit"]').removeAttr('disabled');
						$('form.woocommerce-checkout:not(.eufdc-out) input[type="submit"]').removeAttr('disabled');
						$('form.uploading').removeClass('uploading');
					}

				},
				success:function (){

					$('.wufdc-loader-icon').hide();
					
					$(this).removeClass('eufdc-in-progress');

					if(eufdc_obj.eufdc_server_side_check){

						var data = {
							action : 'eufdc_get_file_upload_error'
						}
						//resp = $.parseJSON(response);

						var eufdc_error_msg = '';
						$.post(eufdc_obj.ajax_url,data, function(resp, code){
							
							if(code == 'success'){
								eufdc_file_type_error = resp.is_error;
								eufdc_error_msg = resp.error_message;
								if(eufdc_file_type_error){
									$('div.wufdc_legends  .eufdc-gerr.eufdc-error').text(eufdc_error_msg).show();

									if(eufdc_obj.is_custom!='1'){

										$('div#wufdc_div .wufdc-progress-text').hide();
										$('div#wufdc_div .wufdc-progress-div').hide('green');
										$('div#wufdc_div .in-progress').parent().remove();
										
										$('#wufdc_div > ul').html(eufdc_obj.eufdc_upload_element_single);

									}

								}
							}
						});

					}

					$.each(form_object.find(':input[type="hidden"]'), function(){
						var aname = $(this).attr('name');
						$(this).attr('name', aname.replace('eufdch-', ''));
					});


					if(eufdc_obj.is_cart=='1' || eufdc_obj.is_checkout=='1' ||  (eufdc_pro && eufdc_check_custom_product()) ||(eufdc_pro && eufdc_obj.is_product=='1') || (eufdc_pro && eufdc_obj.is_view_order) || eufdc_obj.is_account_page == '1'){	

						$('.woocommerce form.cart-form,.woocommerce form.woocommerce-cart-form, .woocommerce form.woocommerce-checkout:not(.eufdc-out)').attr('action', eufdc_obj.cart_url);

						setTimeout(function(){

							$('div#wufdc_div .wufdc-loader-icon').fadeOut();
							if(eufdc_obj.is_custom!='1' && eufdc_file_type_error){
								return;
							}
							
							switch($.trim($('.wufdc-targetLayer').html())){

								case 'all-done':
									reload_wufdc_div();
								break;
								case 'uploaded':
									reload_wufdc_div();
								break;
								default:

									if(eufdc_obj.is_custom!='1'){

										if(eufdc_obj.is_cart){

											$('div#wufdc_div .wufdc-loader-icon').show();

											setTimeout(function(){
												
											}, 3000);

										}else{

											window.location.assign(document.URL);

										}


									}else{

										$('div#wufdc_div .wufdc-progress-text').html(eufdc_obj.progress_error);
										$('div#wufdc_div .wufdc-progress-div').addClass('green');
										$('div#wufdc_div .in-progress').parent().remove();

											if(eufdc_obj.is_account_page == '1' || eufdc_obj.is_checkout=='1' || (eufdc_pro && eufdc_check_custom_product()) || (eufdc_pro && eufdc_obj.is_product=='1') || (eufdc_pro && eufdc_obj.is_cart=='1') || (eufdc_pro && eufdc_obj.is_view_order=='1')){
											reload_wufdc_div();



										}
									}

								break;

							}
							
							if(eufdc_obj.checkout_refresh==true){	
								$('div#wufdc_div').fadeOut();
								window.location.assign(document.URL);
							}

						}, 1000);

					}
				},
				resetForm: true
			});
			return false; 
		}
		
		if(eufdc_obj.easy_ufdc_req!='1'){

			if(eufdc_obj.is_cart){
				
			}
			
		}

		if(eufdc_obj.easy_ufdc_req=='1' && $('div#wufdc_div').is(':visible')){
			
			var gprompt = false;
			
			switch(eufdc_obj.easy_ufdc_page){
				case 'cart':
				case 'checkout_above':
				case 'checkout_above_content':				
					gprompt = true;
				break;
				
				case 'checkout':
				case 'checkout_notes':				
					gprompt = true;
				break;	
				
				case 'product':
					gprompt = true;
				break;				
								
				default:
					
				break;
			}
			if(!gprompt && (eufdc_target_element!='' && !eufdc_target_element_set)){
				gprompt = true;
			}
			
			if((eufdc_obj.is_cart || eufdc_obj.is_checkout || eufdc_obj.is_product) && gprompt){
				
				if(!eufdc_files){
					

					var required_error = $('div#wufdc_div li.ready').length==0 || (eufdc_obj.required_limit>0 && $('div#wufdc_div li.ready').length<eufdc_obj.required_limit);

					if(required_error){

						if($('.eufdc-gerr').length>0)
						$('.eufdc-gerr').html(eufdc_obj.error_default).show();
						
					}else if(eufdc_obj.is_cart && $('div#wufdc_div li.ready').length>=1 && eufdc_pro){

					}else if(eufdc_obj.is_product && $('div#wufdc_div li.ready').length>=1 && eufdc_pro){

					}
				}else{
					switch(eufdc_obj.easy_ufdc_page){
						case 'checkout':
						case 'checkout_notes':						
						
							if($('div#wufdc_div li.ready').length==0){
								
								if($('.eufdc-gerr').length>0)
								$('.eufdc-gerr').html(eufdc_obj.error_valid_extension).show();
								
							}
						break;						
					}
				}
			}		
			
		}else{
			
		}
		
		


	});
	

	
	$('body').on('change', 'input[name^="file_during_checkout"]:visible', function (){

		if(eufdc_obj.eufdc_secure_upload=='Y' && eufdc_obj.is_user_logged_in!='Y'){

			if($('.eufdc-gerr').length>0)
			$('.eufdc-gerr').html(eufdc_obj.eufdc_secure_upload_error).show();

			return;
		}

		var form_object = $(this).closest('form');


		setTimeout(function(){
				
			if(eufdc_pro && (eufdc_check_custom_product() || eufdc_obj.is_product=='1' || eufdc_obj.easy_ufdc_page=='thank_you' || eufdc_obj.easy_ufdc_page=='shortcode' || eufdc_obj.easy_ufdc_page=='customer_order' || (eufdc_obj.easy_ufdc_page=='checkout_above' || eufdc_obj.easy_ufdc_page=='checkout_above_content'))){	
				if(eufdc_proceed){

					$(this).addClass('in-progress');


					form_object.addClass('eufdc_form');
					$.each(form_object.find(':input[type="hidden"]'), function(){
						var aname = $(this).attr('name');
						$(this).attr('name', 'eufdch-'+aname);
					});
					form_object.submit();

					eufdc_locked = true;
				}else{

				}
			}
			if(eufdc_obj.is_cart=='1'){
				if(eufdc_proceed){
					$(this).addClass('in-progress');
					$('.woocommerce form.cart-form,.woocommerce form.woocommerce-cart-form').submit();
					eufdc_locked = true;
				}
			}
			if(eufdc_obj.easy_ufdc_page=='register' && eufdc_obj.is_account_page=='1'){

				if(eufdc_proceed){

					eufdc_file_upload = true;
					$(this).addClass('in-progress');
					$('.woocommerce-form-register').submit();
					eufdc_locked = true;
				}
			}
			if(eufdc_obj.is_checkout=='1'){
				if(eufdc_proceed){

					$(this).addClass('in-progress');
					if(eufdc_obj.is_checkout=='1' && eufdc_obj.checkout_refresh=='1'){

					}
					$('.woocommerce form.woocommerce-checkout:not(.eufdc-out)').submit();

					eufdc_locked = true;
				}
			}
		}, 1000);
	});

	
}




function eufdc_url_reset($) {
	
	if($('div#wufdc_div').length==0){
		
		var url = '';
		
		if(eufdc_obj.is_cart=='1'){	
			url = eufdc_obj.cart_url;
		}else if(eufdc_obj.is_checkout=='1'){
			url = eufdc_obj.checkout_url;
		}
		
		if (typeof (history.pushState) != "undefined") {
			var obj = { Title: '', Url: url };
			history.pushState(obj, '', obj.Url);
		} else {
			
		}
	
	}
	
}

function layered_js($){

				

	jQuery('.woocommerce form').attr('enctype','multipart/form-data');

	jQuery('.woocommerce form').attr('encoding', 'multipart/form-data');

	

	if(jQuery('.woocommerce-cart .woocommerce form').find('input[name^="file_during_checkout"]:visible').length>0){

		jQuery('.woocommerce-cart .woocommerce form').attr('action', eufdc_obj.checkout_url);
		
	}

	

	if(jQuery('.woocommerce form').hasClass('checkout')){

		

		jQuery('<span class="temp_checkout"></span>').insertAfter('.woocommerce form.checkout');


		jQuery( ".woocommerce form.checkout").unbind( "submit" );

	}	

	

}


function eufdc_check_custom_product(){
	var $ = jQuery;
	var eufdc_page = eufdc_obj.easy_ufdc_page;
	var is_product = eufdc_obj.is_product;
	var eufdc_div = $('#wufdc_div');
	var cart_form = eufdc_div.closest('form.cart');
	var single_product = eufdc_div.closest('div.single-product');
	var single_product_id = single_product.find('div[id^="product-"]');


	return (

		eufdc_page == 'product'
		&& is_product != '1'
		&& eufdc_div.length > 0
		&& cart_form.length > 0
		&& single_product.length > 0
		&& single_product_id.length > 0
	);

}

(function(e){"use strict";if(typeof define==="function"&&define.amd){define(["jquery"],e)}else{e(typeof jQuery!="undefined"?jQuery:window.Zepto)}})(function(e){"use strict";function r(t){var n=t.data;if(!t.isDefaultPrevented()){t.preventDefault();e(t.target).ajaxSubmit(n)}}function i(t){var n=t.target;var r=e(n);if(!r.is("[type=submit],[type=image]")){var i=r.closest("[type=submit]");if(i.length===0){return}n=i[0]}var s=this;s.clk=n;if(n.type=="image"){if(t.offsetX!==undefined){s.clk_x=t.offsetX;s.clk_y=t.offsetY}else if(typeof e.fn.offset=="function"){var o=r.offset();s.clk_x=t.pageX-o.left;s.clk_y=t.pageY-o.top}else{s.clk_x=t.pageX-n.offsetLeft;s.clk_y=t.pageY-n.offsetTop}}setTimeout(function(){s.clk=s.clk_x=s.clk_y=null},100)}function s(){if(!e.fn.ajaxSubmit.debug){return}var t="[jquery.form] "+Array.prototype.join.call(arguments,"");if(window.console&&window.console.log){window.console.log(t)}else if(window.opera&&window.opera.postError){window.opera.postError(t)}}var t={};t.fileapi=e("<input type='file'/>").get(0).files!==undefined;t.formdata=window.FormData!==undefined;var n=!!e.fn.prop;e.fn.attr2=function(){if(!n){return this.attr.apply(this,arguments)}var e=this.prop.apply(this,arguments);if(e&&e.jquery||typeof e==="string"){return e}return this.attr.apply(this,arguments)};e.fn.ajaxSubmit=function(r){function k(t){var n=e.param(t,r.traditional).split("&");var i=n.length;var s=[];var o,u;for(o=0;o<i;o++){n[o]=n[o].replace(/\+/g," ");u=n[o].split("=");s.push([decodeURIComponent(u[0]),decodeURIComponent(u[1])])}return s}function L(t){var n=new FormData;for(var s=0;s<t.length;s++){n.append(t[s].name,t[s].value)}if(r.extraData){var o=k(r.extraData);for(s=0;s<o.length;s++){if(o[s]){n.append(o[s][0],o[s][1])}}}r.data=null;var u=e.extend(true,{},e.ajaxSettings,r,{contentType:false,processData:false,cache:false,type:i||"POST"});if(r.uploadProgress){u.xhr=function(){var t=e.ajaxSettings.xhr();if(t.upload){t.upload.addEventListener("progress",function(e){var t=0;var n=e.loaded||e.position;var i=e.total;if(e.lengthComputable){t=Math.ceil(n/i*100)}r.uploadProgress(e,n,i,t)},false)}return t}}u.data=null;var a=u.beforeSend;u.beforeSend=function(e,t){if(r.formData){t.data=r.formData}else{t.data=n}if(a){a.call(this,e,t)}};return e.ajax(u)}function A(t){function T(e){var t=null;try{if(e.contentWindow){t=e.contentWindow.document}}catch(n){s("cannot get iframe.contentWindow document: "+n)}if(t){return t}try{t=e.contentDocument?e.contentDocument:e.document}catch(n){s("cannot get iframe.contentDocument: "+n);t=e.document}return t}function k(){function f(){try{var e=T(v).readyState;s("state = "+e);if(e&&e.toLowerCase()=="uninitialized"){setTimeout(f,50)}}catch(t){s("Server abort: ",t," (",t.name,")");_(x);if(w){clearTimeout(w)}w=undefined}}var t=a.attr2("target"),n=a.attr2("action"),r="multipart/form-data",u=a.attr("enctype")||a.attr("encoding")||r;o.setAttribute("target",p);if(!i||/post/i.test(i)){o.setAttribute("method","POST")}if(n!=l.url){o.setAttribute("action",l.url)}if(!l.skipEncodingOverride&&(!i||/post/i.test(i))){a.attr({encoding:"multipart/form-data",enctype:"multipart/form-data"})}if(l.timeout){w=setTimeout(function(){b=true;_(S)},l.timeout)}var c=[];try{if(l.extraData){for(var h in l.extraData){if(l.extraData.hasOwnProperty(h)){if(e.isPlainObject(l.extraData[h])&&l.extraData[h].hasOwnProperty("name")&&l.extraData[h].hasOwnProperty("value")){c.push(e('<input type="hidden" name="'+l.extraData[h].name+'">').val(l.extraData[h].value).appendTo(o)[0])}else{c.push(e('<input type="hidden" name="'+h+'">').val(l.extraData[h]).appendTo(o)[0])}}}}if(!l.iframeTarget){d.appendTo("body")}if(v.attachEvent){v.attachEvent("onload",_)}else{v.addEventListener("load",_,false)}setTimeout(f,15);try{o.submit()}catch(m){var g=document.createElement("form").submit;g.apply(o)}}finally{o.setAttribute("action",n);o.setAttribute("enctype",u);if(t){o.setAttribute("target",t)}else{a.removeAttr("target")}e(c).remove()}}function _(t){if(m.aborted||M){return}A=T(v);if(!A){s("cannot access response document");t=x}if(t===S&&m){m.abort("timeout");E.reject(m,"timeout");return}else if(t==x&&m){m.abort("server abort");E.reject(m,"error","server abort");return}if(!A||A.location.href==l.iframeSrc){if(!b){return}}if(v.detachEvent){v.detachEvent("onload",_)}else{v.removeEventListener("load",_,false)}var n="success",r;try{if(b){throw"timeout"}var i=l.dataType=="xml"||A.XMLDocument||e.isXMLDoc(A);s("isXml="+i);if(!i&&window.opera&&(A.body===null||!A.body.innerHTML)){if(--O){s("requeing onLoad callback, DOM not available");setTimeout(_,250);return}}var o=A.body?A.body:A.documentElement;m.responseText=o?o.innerHTML:null;m.responseXML=A.XMLDocument?A.XMLDocument:A;if(i){l.dataType="xml"}m.getResponseHeader=function(e){var t={"content-type":l.dataType};return t[e.toLowerCase()]};if(o){m.status=Number(o.getAttribute("status"))||m.status;m.statusText=o.getAttribute("statusText")||m.statusText}var u=(l.dataType||"").toLowerCase();var a=/(json|script|text)/.test(u);if(a||l.textarea){var f=A.getElementsByTagName("textarea")[0];if(f){m.responseText=f.value;m.status=Number(f.getAttribute("status"))||m.status;m.statusText=f.getAttribute("statusText")||m.statusText}else if(a){var c=A.getElementsByTagName("pre")[0];var p=A.getElementsByTagName("body")[0];if(c){m.responseText=c.textContent?c.textContent:c.innerText}else if(p){m.responseText=p.textContent?p.textContent:p.innerText}}}else if(u=="xml"&&!m.responseXML&&m.responseText){m.responseXML=D(m.responseText)}try{L=H(m,u,l)}catch(g){n="parsererror";m.error=r=g||n}}catch(g){s("error caught: ",g);n="error";m.error=r=g||n}if(m.aborted){s("upload aborted");n=null}if(m.status){n=m.status>=200&&m.status<300||m.status===304?"success":"error"}if(n==="success"){if(l.success){l.success.call(l.context,L,"success",m)}E.resolve(m.responseText,"success",m);if(h){e.event.trigger("ajaxSuccess",[m,l])}}else if(n){if(r===undefined){r=m.statusText}if(l.error){l.error.call(l.context,m,n,r)}E.reject(m,"error",r);if(h){e.event.trigger("ajaxError",[m,l,r])}}if(h){e.event.trigger("ajaxComplete",[m,l])}if(h&&!--e.active){e.event.trigger("ajaxStop")}if(l.complete){l.complete.call(l.context,m,n)}M=true;if(l.timeout){clearTimeout(w)}setTimeout(function(){if(!l.iframeTarget){d.remove()}else{d.attr("src",l.iframeSrc)}m.responseXML=null},100)}var o=a[0],u,f,l,h,p,d,v,m,g,y,b,w;var E=e.Deferred();E.abort=function(e){m.abort(e)};if(t){for(f=0;f<c.length;f++){u=e(c[f]);if(n){u.prop("disabled",false)}else{u.removeAttr("disabled")}}}l=e.extend(true,{},e.ajaxSettings,r);l.context=l.context||l;p="jqFormIO"+(new Date).getTime();if(l.iframeTarget){d=e(l.iframeTarget);y=d.attr2("name");if(!y){d.attr2("name",p)}else{p=y}}else{d=e('<iframe name="'+p+'" src="'+l.iframeSrc+'" />');d.css({position:"absolute",top:"-1000px",left:"-1000px"})}v=d[0];m={aborted:0,responseText:null,responseXML:null,status:0,statusText:"n/a",getAllResponseHeaders:function(){},getResponseHeader:function(){},setRequestHeader:function(){},abort:function(t){var n=t==="timeout"?"timeout":"aborted";s("aborting upload... "+n);this.aborted=1;try{if(v.contentWindow.document.execCommand){v.contentWindow.document.execCommand("Stop")}}catch(r){}d.attr("src",l.iframeSrc);m.error=n;if(l.error){l.error.call(l.context,m,n,t)}if(h){e.event.trigger("ajaxError",[m,l,n])}if(l.complete){l.complete.call(l.context,m,n)}}};h=l.global;if(h&&0===e.active++){e.event.trigger("ajaxStart")}if(h){e.event.trigger("ajaxSend",[m,l])}if(l.beforeSend&&l.beforeSend.call(l.context,m,l)===false){if(l.global){e.active--}E.reject();return E}if(m.aborted){E.reject();return E}g=o.clk;if(g){y=g.name;if(y&&!g.disabled){l.extraData=l.extraData||{};l.extraData[y]=g.value;if(g.type=="image"){l.extraData[y+".x"]=o.clk_x;l.extraData[y+".y"]=o.clk_y}}}var S=1;var x=2;var N=e("meta[name=csrf-token]").attr("content");var C=e("meta[name=csrf-param]").attr("content");if(C&&N){l.extraData=l.extraData||{};l.extraData[C]=N}if(l.forceSync){k()}else{setTimeout(k,10)}var L,A,O=50,M;var D=e.parseXML||function(e,t){if(window.ActiveXObject){t=new ActiveXObject("Microsoft.XMLDOM");t.async="true";t.loadXML(e)}else{t=(new DOMParser).parseFromString(e,"text/xml")}return t&&t.documentElement&&t.documentElement.nodeName!="parsererror"?t:null};var P=e.parseJSON||function(e){return window["eval"]("("+e+")")};var H=function(t,n,r){var i=t.getResponseHeader("content-type")||"",s=n==="xml"||!n&&i.indexOf("xml")>=0,o=s?t.responseXML:t.responseText;if(s&&o.documentElement.nodeName==="parsererror"){if(e.error){e.error("parsererror")}}if(r&&r.dataFilter){o=r.dataFilter(o,n)}if(typeof o==="string"){if(n==="json"||!n&&i.indexOf("json")>=0){o=P(o)}else if(n==="script"||!n&&i.indexOf("javascript")>=0){e.globalEval(o)}}return o};return E}if(!this.length){s("ajaxSubmit: skipping submit process - no element selected");return this}var i,o,u,a=this;if(typeof r=="function"){r={success:r}}else if(r===undefined){r={}}i=r.type||this.attr2("method");o=r.url||this.attr2("action");u=typeof o==="string"?e.trim(o):"";u=u||window.location.href||"";if(u){u=(u.match(/^([^#]+)/)||[])[1]}r=e.extend(true,{url:u,success:e.ajaxSettings.success,type:i||e.ajaxSettings.type,iframeSrc:/^https/i.test(window.location.href||"")?"javascript:false":"about:blank"},r);var f={};this.trigger("form-pre-serialize",[this,r,f]);if(f.veto){s("ajaxSubmit: submit vetoed via form-pre-serialize trigger");return this}if(r.beforeSerialize&&r.beforeSerialize(this,r)===false){s("ajaxSubmit: submit aborted via beforeSerialize callback");return this}var l=r.traditional;if(l===undefined){l=e.ajaxSettings.traditional}var c=[];var h,p=this.formToArray(r.semantic,c);if(r.data){r.extraData=r.data;h=e.param(r.data,l)}if(r.beforeSubmit&&r.beforeSubmit(p,this,r)===false){s("ajaxSubmit: submit aborted via beforeSubmit callback");return this}this.trigger("form-submit-validate",[p,this,r,f]);if(f.veto){s("ajaxSubmit: submit vetoed via form-submit-validate trigger");return this}var d=e.param(p,l);if(h){d=d?d+"&"+h:h}if(r.type.toUpperCase()=="GET"){r.url+=(r.url.indexOf("?")>=0?"&":"?")+d;r.data=null}else{r.data=d}var v=[];if(r.resetForm){v.push(function(){a.resetForm()})}if(r.clearForm){v.push(function(){a.clearForm(r.includeHidden)})}if(!r.dataType&&r.target){var m=r.success||function(){};v.push(function(t){var n=r.replaceTarget?"replaceWith":"html";e(r.target)[n](t).each(m,arguments)})}else if(r.success){v.push(r.success)}r.success=function(e,t,n){var i=r.context||this;for(var s=0,o=v.length;s<o;s++){v[s].apply(i,[e,t,n||a,a])}};if(r.error){var g=r.error;r.error=function(e,t,n){var i=r.context||this;g.apply(i,[e,t,n,a])}}if(r.complete){var y=r.complete;r.complete=function(e,t){var n=r.context||this;y.apply(n,[e,t,a])}}var b=e("input[type=file]:enabled",this).filter(function(){return e(this).val()!==""});var w=b.length>0;var E="multipart/form-data";var S=a.attr("enctype")==E||a.attr("encoding")==E;var x=t.fileapi&&t.formdata;s("fileAPI :"+x);var T=(w||S)&&!x;var N;if(r.iframe!==false&&(r.iframe||T)){if(r.closeKeepAlive){e.get(r.closeKeepAlive,function(){N=A(p)})}else{N=A(p)}}else if((w||S)&&x){N=L(p)}else{N=e.ajax(r)}a.removeData("jqxhr").data("jqxhr",N);for(var C=0;C<c.length;C++){c[C]=null}this.trigger("form-submit-notify",[this,r]);return this};e.fn.ajaxForm=function(t){t=t||{};t.delegation=t.delegation&&e.isFunction(e.fn.on);if(!t.delegation&&this.length===0){var n={s:this.selector,c:this.context};if(!e.isReady&&n.s){s("DOM not ready, queuing ajaxForm");e(function(){e(n.s,n.c).ajaxForm(t)});return this}s("terminating; zero elements found by selector"+(e.isReady?"":" (DOM not ready)"));return this}if(t.delegation){e(document).off("submit.form-plugin",this.selector,r).off("click.form-plugin",this.selector,i).on("submit.form-plugin",this.selector,t,r).on("click.form-plugin",this.selector,t,i);return this}return this.ajaxFormUnbind().bind("submit.form-plugin",t,r).bind("click.form-plugin",t,i)};e.fn.ajaxFormUnbind=function(){return this.unbind("submit.form-plugin click.form-plugin")};e.fn.formToArray=function(n,r){var i=[];if(this.length===0){return i}var s=this[0];var o=this.attr("id");var u=n?s.getElementsByTagName("*"):s.elements;var a;if(u&&!/MSIE [678]/.test(navigator.userAgent)){u=e(u).get()}if(o){a=e(':input[form="'+o+'"]').get();if(a.length){u=(u||[]).concat(a)}}if(!u||!u.length){return i}var f,l,c,h,p,d,v;for(f=0,d=u.length;f<d;f++){p=u[f];c=p.name;if(!c||p.disabled){continue}if(n&&s.clk&&p.type=="image"){if(s.clk==p){i.push({name:c,value:e(p).val(),type:p.type});i.push({name:c+".x",value:s.clk_x},{name:c+".y",value:s.clk_y})}continue}h=e.fieldValue(p,true);if(h&&h.constructor==Array){if(r){r.push(p)}for(l=0,v=h.length;l<v;l++){i.push({name:c,value:h[l]})}}else if(t.fileapi&&p.type=="file"){if(r){r.push(p)}var m=p.files;if(m.length){for(l=0;l<m.length;l++){i.push({name:c,value:m[l],type:p.type})}}else{i.push({name:c,value:"",type:p.type})}}else if(h!==null&&typeof h!="undefined"){if(r){r.push(p)}i.push({name:c,value:h,type:p.type,required:p.required})}}if(!n&&s.clk){var g=e(s.clk),y=g[0];c=y.name;if(c&&!y.disabled&&y.type=="image"){i.push({name:c,value:g.val()});i.push({name:c+".x",value:s.clk_x},{name:c+".y",value:s.clk_y})}}return i};e.fn.formSerialize=function(t){return e.param(this.formToArray(t))};e.fn.fieldSerialize=function(t){var n=[];this.each(function(){var r=this.name;if(!r){return}var i=e.fieldValue(this,t);if(i&&i.constructor==Array){for(var s=0,o=i.length;s<o;s++){n.push({name:r,value:i[s]})}}else if(i!==null&&typeof i!="undefined"){n.push({name:this.name,value:i})}});return e.param(n)};e.fn.fieldValue=function(t){for(var n=[],r=0,i=this.length;r<i;r++){var s=this[r];var o=e.fieldValue(s,t);if(o===null||typeof o=="undefined"||o.constructor==Array&&!o.length){continue}if(o.constructor==Array){e.merge(n,o)}else{n.push(o)}}return n};e.fieldValue=function(t,n){var r=t.name,i=t.type,s=t.tagName.toLowerCase();if(n===undefined){n=true}if(n&&(!r||t.disabled||i=="reset"||i=="button"||(i=="checkbox"||i=="radio")&&!t.checked||(i=="submit"||i=="image")&&t.form&&t.form.clk!=t||s=="select"&&t.selectedIndex==-1)){return null}if(s=="select"){var o=t.selectedIndex;if(o<0){return null}var u=[],a=t.options;var f=i=="select-one";var l=f?o+1:a.length;for(var c=f?o:0;c<l;c++){var h=a[c];if(h.selected){var p=h.value;if(!p){p=h.attributes&&h.attributes.value&&!h.attributes.value.specified?h.text:h.value}if(f){return p}u.push(p)}}return u}return e(t).val()};e.fn.clearForm=function(t){return this.each(function(){e("input,select,textarea",this).clearFields(t)})};e.fn.clearFields=e.fn.clearInputs=function(t){var n=/^(?:color|date|datetime|email|month|number|password|range|search|tel|text|time|url|week)$/i;return this.each(function(){var r=this.type,i=this.tagName.toLowerCase();if(n.test(r)||i=="textarea"){this.value=""}else if(r=="checkbox"||r=="radio"){this.checked=false}else if(i=="select"){this.selectedIndex=-1}else if(r=="file"){if(/MSIE/.test(navigator.userAgent)){e(this).replaceWith(e(this).clone(true))}else{e(this).val("")}}else if(t){if(t===true&&/hidden/.test(r)||typeof t=="string"&&e(this).is(t)){this.value=""}}})};e.fn.resetForm=function(){return this.each(function(){if(typeof this.reset=="function"||typeof this.reset=="object"&&!this.reset.nodeType){this.reset()}})};e.fn.enable=function(e){if(e===undefined){e=true}return this.each(function(){this.disabled=!e})};e.fn.selected=function(t){if(t===undefined){t=true}return this.each(function(){var n=this.type;if(n=="checkbox"||n=="radio"){this.checked=t}else if(this.tagName.toLowerCase()=="option"){var r=e(this).parent("select");if(t&&r[0]&&r[0].type=="select-one"){r.find("option").selected(false)}this.selected=t}})};e.fn.ajaxSubmit.debug=false})


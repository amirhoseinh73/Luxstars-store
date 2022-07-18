/**
 * Scripts within customizer preview window.
 *
 * Used global objects:
 * - jQuery
 * - wp
 * - WPAdminifyLoginCustomizer
 */
(function ($, api) {
	var events = {},
		state = {};


	wp.customize.bind('preview-ready', function () {

		listen();

		// When the previewer is active, the "active" event has been triggered (on load)
		wp.customize.preview.bind( 'active', function() {
			var $loginform	= $( '#loginform');
			$loginform.append( '<span class="customize-partial--loginform customize-partial-edit-shortcut"><button class="login-designer-event-button customize-partial-edit-shortcut-button" data-wp-adminify-preview-event="wp-adminify-edit-loginform"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13.89 3.39l2.71 2.72c.46.46.42 1.24.03 1.64l-8.01 8.02-5.56 1.16 1.16-5.58s7.6-7.63 7.99-8.03c.39-.39 1.22-.39 1.68.07zm-2.73 2.79l-5.59 5.61 1.11 1.11 5.54-5.65zm-2.97 8.23l5.58-5.6-1.07-1.08-5.59 5.6z"></path></svg></button></span>' );
		});

		wp.customize.preview.bind( 'change-form', function( form ) {
			if ( 'register' == form ) {
				$('.show-only_login').hide();
				$('.show-only_lostpassword').hide();
				$('.show-only_register').show();
			}else if( 'lostpassword' == form ){
				$('.show-only_login').hide();
				$('.show-only_register').hide();
				$('.show-only_lostpassword').show();
			}else{
				$('.show-only_register').hide();
				$('.show-only_lostpassword').hide();
				$('.show-only_login').show();
			}
		} );
	});

	var change_theme;
	function listen() {
		// if (!WPAdminifyLoginCustomizer.isProActive) events.previewerBinding();
		events.logoFieldsChange();
		events.bgFieldsChange();
		events.templateFieldsChange();
		// if (!WPAdminifyLoginCustomizer.isProActive) events.templateFieldsChange();
		events.layoutFieldsChange();
		events.formButtonChanges();
		events.formFieldsChange();
		events.formBackground();
		events.brandingConfig();
		events.errorMessages();
		events.footerFieldsChange();
		events.focusSection();
		events.othersFields();
		events.formAlignments();
	}


	function jltwp_adminify_find( finder = '.wp-adminify-customize' ) {
		var customizer_finder = $('#customize-preview iframe').contents().find( finder );
		return customizer_finder;
	}

	events.templateFieldsChange = function () {
		wp.customize('jltwp_adminify_login[templates]', function (value) {
			value.bind(function (to) {
				switch (to) {
					case 'template-05':
						$( 'body' ).addClass( 'wp-adminify-half-screen jltwp-adminify-login-right' );
						break;

					case 'template-11':
						$( 'body' ).addClass( 'wp-adminify-half-screen jltwp-adminify-login-right' );
						break;

					case 'template-12':
						$( 'body' ).addClass( 'wp-adminify-half-screen jltwp-adminify-login-right' );
						break;

					case 'template-13':
						$( 'body' ).addClass( 'wp-adminify-half-screen jltwp-adminify-login-right' );
						break;

					default:
						 $( 'body' ).removeClass( 'wp-adminify-half-screen jltwp-adminify-login-right' );
				}

				// Pre-defined Templates Selction
				$.ajax({
					url : WPAdminifyLoginCustomizer.ajaxurl,
					type: 'POST',
					data: {
						action   : 'jltwp_adminify_adminify_presets',
						template_id : to,
						security : WPAdminifyLoginCustomizer.preset_nonce
					},
					beforeSend: function() {
						$('.login').append('<div class="wp-adminify-login-preloader" style="position: fixed;top: 0;left: 0; height: 100%; width: 100%; background: rgba(255,255, 255, .5) url(' + WPAdminifyLoginCustomizer.preset_loader + ') no-repeat center center; z-index: 9999999;"></div>');
					},
					success: function(response) {
						// if(to!=="template-01"){
							$('.wp-adminify-style-wp').remove();
						// }
						$('head').append(response);
						$('.wp-adminify-login-preloader').remove();
					}
				});

			});
		});


	}


	events.othersFields = function(){
		// Hide Remember Me
		wp.customize( 'jltwp_adminify_login[login_form_button_remember_me]', function( value ) {
			value.bind( function( to ) {
				if(to == true){
					$( 'p.forgetmenot' ).css({ display: 'none' });
				}else{
					$( 'p.forgetmenot' ).css({ display: 'block' });
				}
			} );
		} );

		// Disable Lost Password
		wp.customize( 'jltwp_adminify_login[login_form_disable_lost_pass]', function( value ) {
			value.bind( function( to ) {
				if(to == true){
					$( 'p#nav' ).css({ display: 'none' });
				}else{
					$( 'p#nav' ).css({ display: 'block' });
				}
			} );
		} );

		// Hide Back to Site
		wp.customize( 'jltwp_adminify_login[login_form_disable_back_to_site]', function( value ) {
			value.bind( function( to ) {
				if(to == true){
					$( 'p#backtoblog' ).css({ display: 'none' });
				}else{
					$( 'p#backtoblog' ).css({ display: 'block' });
				}
			} );
		} );
	}


	events.focusSection = function(){
		$( '.wp-adminify-preview-event' ).click( function() {
			wp.customize.preview.send( 'wp-adminify-focus-section', $( this ).data( 'section' ) );
		} );
	}

	events.previewerBinding = function () {
		wp.customize.preview.bind('pro_notice', function (action) {
			if (action === 'show') {
				showProNotice();
			} else {
				hideProNotice();
			}
		});
	};

	events.logoFieldsChange = function () {
		// var line = this.settings[ option ].attribute + ':';

        // if ( '' === this.settings[ option ].value && 'custom-logo' !== option ) {
        //   return '';
        // }
		const logo_body_class = wp.customize('jltwp_adminify_login[logo_settings]').get();
		if(logo_body_class=="text-only"){
			$( 'body' ).addClass( 'wp-adminify-text-logo' );
		} else if( logo_body_class=="both" ){
			$( 'body' ).addClass( 'wp-adminify-both-logo' );
		} else if( logo_body_class=="none" ){
			$( '#login h1 a' ).hide();
		}

		// logo Link
		wp.customize( 'jltwp_adminify_login[logo_login_url]', function( value ) {
			value.bind( function( to ) {
				$( '#wp-adminify-logo-login-link' ).attr( 'href', to );
			} );
		} );

		// Login page Title
		wp.customize( 'jltwp_adminify_login[login_page_title]', function( value ) {
			value.bind( function( to ) {
				$( '#wp-adminify-logo-login-link' ).attr( 'title', to );
			} );
		} );

		// Login Text Typography
		wp.customize( 'jltwp_adminify_login[login_title_style]', function( value ) {
			value.bind( function( to ) {
				for (const label of Object.keys(to)) {
					switch (label) {
						case 'login_title_typography':
							const login_fnt_color     = to['login_title_typography']['color'],
							      login_fnt_unit      = to['login_title_typography']['unit'],
							      login_fnt_size      = to['login_title_typography']['font-size'] + login_fnt_unit,
							      login_fnt_family    = to['login_title_typography']['font-family'],
							      login_fnt_style     = to['login_title_typography']['font-style'],
							      login_fnt_weight    = to['login_title_typography']['font-weight'],
							      login_fnt_ltr_space = to['login_title_typography']['letter-spacing'] + login_fnt_unit,
							      login_fnt_ln_height = to['login_title_typography']['line-height'] + login_fnt_unit,
							      login_fnt_txt_decor = to['login_title_typography']['text-decoration'],
							      login_fnt_txt_trans = to['login_title_typography']['text-transform'];
							$( '.wp-adminify-both-logo #logo-text' ).css({
									color: login_fnt_color,
									fontSize: login_fnt_size,
									fontFamily: login_fnt_family,
									fontStyle: login_fnt_style,
									fontWeight: login_fnt_weight,
									letterSpacing: login_fnt_ltr_space,
									lineHeight: login_fnt_ln_height,
									textDecoration: login_fnt_txt_decor,
									textTransform: login_fnt_txt_trans
							});
							break;

						case 'logo_heigh_width':
							$('#login h1').css({ width: to['logo_heigh_width']['width'] + to['logo_heigh_width']['unit'], height: to['logo_heigh_width']['height'] + to['logo_heigh_width']['unit'] })
							break;

						case 'logo_padding':
							const log_padding_top    = to['logo_padding']['top'] + to['logo_padding']['unit'],
							      log_padding_right  = to['logo_padding']['right'] + to['logo_padding']['unit'],
							      log_padding_bottom = to['logo_padding']['bottom'] + to['logo_padding']['unit'],
							      log_padding_left   = to['logo_padding']['left'] + to['logo_padding']['unit'];

							$('#login h1').css({padding: log_padding_top + ' ' + log_padding_right + ' ' + log_padding_bottom + ' ' + log_padding_left })
							break;
					}
				}
			} );
		} );


		// $("div.myclass").hover(function() {
		//   $(this).css("background-color","red")
		// });

		wp.customize('jltwp_adminify_login[logo_settings]', function (value) {
			value.bind(function (to) {

				switch (to) {
					case 'text-only':
						// var text_class_exists = $('body').hasClass('logo_body_class');
						const logo_text_content = wp.customize('jltwp_adminify_login[logo_text]').get();
						$( 'body' ).removeClass( 'wp-adminify-both-logo' ).addClass( 'wp-adminify-text-logo' );
						$( '#logo-text' ).text(logo_text_content).show();

						// Logo Text
						wp.customize( 'jltwp_adminify_login[logo_text]', function( txtvalue ) {
							txtvalue.bind( function( txtto ) {
								$( '#logo-text' ).text( txtto );
							} );
						} );
						break;

					case 'image-only':
						$( 'body' ).removeClass( 'wp-adminify-text-logo' );
						$( '.login h1 a' ).show();
						$( '#logo-text' ).hide();

						wp.customize('jltwp_adminify_login[logo_image]', function (imgvalue) {
							imgvalue.bind(function (imgto) {
								if(imgto['url'] != ''){
									$('.login h1 a').css({ backgroundImage: 'url(' + imgto['url'] +')' });
								}else{
									$('.login h1 a').css({ backgroundImage: 'url(wp-admin/images/wordpress-logo.svg)' });
								}
							});
						});
						break;

					case 'both':
						$( 'body' ).removeClass( 'wp-adminify-text-logo' ).addClass( 'wp-adminify-both-logo' );
						const both_logo_text = wp.customize('jltwp_adminify_login[logo_text]').get();

						$( '.login h1 a' ).show();
						$('.wp-adminify-both-logo h1 a #logo-text').css('display', 'block');
						$( '#logo-text' ).text(both_logo_text).show();
						break;

					case 'none':
							$( '#login h1 a' ).hide();
						break;
				}
			});
		});


		// wp.customize( 'jltwp_adminify_login[logo_text]', function( txtvalue ) {
		// 	txtvalue.bind( function( txtto ) {
		// 		$( 'body' ).removeClass( 'wp-adminify-both-logo' ).addClass( 'wp-adminify-text-logo' );
		// 		$( '#logo-text' ).text( txtto );
		// 	} );
		// } );

		// Image Change
		// wp.customize('jltwp_adminify_login[logo_image]', function (imgvalue) {
		// 	imgvalue.bind(function (imgto) {
		// 		// $( 'body' ).removeClass( 'wp-adminify-text-logo' ).addClass( 'wp-adminify-both-logo' );
		// 		if(imgto['url'] != ''){
		// 			$('.login h1 a').css({ backgroundImage: 'url(' + imgto['url'] +')' });
		// 		}else{
		// 			$('.login h1 a').css({ backgroundImage: 'url(wp-admin/images/wordpress-logo.svg)' });
		// 		}
		// 	});
		// });

		// wp.customize('jltwp_adminify_login[logo_login_url]', function (value) {
		// 	value.bind(function (to) {

		// 		console.log('logo url', val);

		// 		val = val.replace('{home_url}', WPAdminifyLoginCustomizer.homeUrl);

		// 		document.querySelector('.wp-adminify-logo-login-link').href = val;

		// 	});
		// });

		// wp.customize('jltwp_adminify_login[logo_title]', function (setting) {
		// 	setting.bind(function (val) {

		// 		console.log('logo title', val);

		// 		document.querySelector('.wp-adminify-logo-login-link').innerHTML = val;

		// 	});
		// });

		wp.customize('jltwp_adminify_login[logo_height]', function (setting) {
			setting.bind(function (val) {
				document.querySelector('[data-listen-value="jltwp_adminify_login[logo_height]"]').innerHTML = '.login h1 a {background-size: auto ' + val + ';}';
			});
		});
	};


	events.bgFieldsChange = function () {
		// var backgroundType = wp.customize('jltwp_adminify_login[jltwp_adminify_login_bg_color_opt]').get();

		// Color Background Options
		wp.customize('jltwp_adminify_login[jltwp_adminify_login_bg_color]', function (value) {

			value.bind( function (to) {
				const login_page_bg        = to['background-color'] ? to['background-color'] : '#f1f1f1',
					login_page_bg_attach = to['background-attachment'],
					login_page_image     = to['background-image'],
					login_page_bg_pos    = to['background-position'],
					login_page_bg_repeat = to['background-repeat'],
					login_page_bg_size   = to['background-size'];

				for (const label of Object.keys(to)) {
					switch (label) {
						case 'background-color':
							$('body.login').css({backgroundColor: login_page_bg});
							break;
						case 'background-attachment':
							$('body.login').css({backgroundAttachment: login_page_bg_attach})
							break;
						case 'background-image':
							$('body.login').css({ backgroundImage: 'url(' + login_page_image['url'] +')' });
							break;
						case 'background-position':
							$('body.login').css({ backgroundPosition: login_page_bg_pos });
							break;
						case 'background-repeat':
							$('body.login').css({ backgroundRepeat: login_page_bg_repeat });
							break;
						case 'background-size':
							$('body.login').css({ backgroundSize: login_page_bg_size });
							break;
					}
				}

			});
		});

		// Gradient Background Options
		wp.customize('jltwp_adminify_login[jltwp_adminify_login_gradient_bg]', function (value) {

			value.bind( function (to) {

				const login_page_gradient_bg_color           = to['background-color'],
					login_page_gradient_gradient_color     = to['background-gradient-color'],
					login_page_gradient_gradient_direction = to['background-gradient-direction'],
					login_page_gradient_bg_attach          = to['background-attachment'],
					login_page_gradient_image              = to['background-image'],
					login_page_gradient_bg_pos             = to['background-position'],
					login_page_gradient_bg_repeat          = to['background-repeat'],
					login_page_gradient_bg_size            = to['background-size'],
					login_page_gradient_bg_origin          = to['background-origin'],
					login_page_gradient_bg_clip            = to['background-clip'],
					login_page_gradient_bg_blend_mode      = to['background-blend-mode'];

				for (const label of Object.keys(to)) {
					switch (label) {

						case 'background-color':
							$('body.login').css({background: login_page_gradient_bg_color});
							break;
						case 'background-image':
							$('body.login').css({ backgroundImage: 'url(' + login_page_gradient_image['url'] +')' });
							break;
						case 'background-gradient-color':
							$('body.login').css({backgroundImage: 'linear-gradient(' + login_page_gradient_gradient_direction + ',' + login_page_gradient_bg_color + ',' + login_page_gradient_gradient_color + ')' })
							break;
						case 'background-attachment':
							$('body.login').css({backgroundAttachment: login_page_gradient_bg_attach})
							break;
						case 'background-position':
							$('body.login').css({ backgroundPosition: login_page_gradient_bg_pos });
							break;
						case 'background-repeat':
							$('body.login').css({ backgroundRepeat: login_page_gradient_bg_repeat });
							break;
						case 'background-size':
							$('body.login').css({ backgroundSize: login_page_gradient_bg_size });
							break;
						case 'background-origin':
							$('body.login').css({ backgroundOrigin: login_page_gradient_bg_origin });
							break;
						case 'background-clip':
							$('body.login').css({ backgroundClip: login_page_gradient_bg_clip });
							break;
						case 'background-blend-mode':
							$('body.login').css({ backgroundBlendMode: login_page_gradient_bg_blend_mode });
							break;
					}
				}

			});
		});
	};






	events.layoutFieldsChange = function () {
		wp.customize('jltwp_adminify_login[form_position]', function (setting) {
			var formPositionStyleTag = document.querySelector('[data-listen-value="jltwp_adminify_login[form_position]"]');
			var formWidthStyleTag = document.querySelector('[data-listen-value="jltwp_adminify_login[form_width]"]');
			var formHorizontalPaddingStyleTag = document.querySelector('[data-listen-value="jltwp_adminify_login[form_horizontal_padding]"]');
			var formBorderWidthStyleTag = document.querySelector('[data-listen-value="jltwp_adminify_login[form_border_width]"]');

			setting.bind(function (val) {
				var formWidth = wp.customize('jltwp_adminify_login[form_width]').get();
				var formHorizontalPadding = wp.customize('jltwp_adminify_login[form_horizontal_padding]').get();
				var formBorderWidth = wp.customize('jltwp_adminify_login[form_border_width]').get();

				formWidth = formWidth ? formWidth : '320px';
				formHorizontalPadding = formHorizontalPadding ? formHorizontalPadding : '24px';
				formBorderWidth = formBorderWidth ? formBorderWidth : '2px';

				if (val === 'default') {
					formWidthStyleTag.innerHTML = formWidthStyleTag.innerHTML.replace('#loginform {max-width:', '#login {width:');

					formPositionStyleTag.innerHTML = '#login {margin-left: auto; margin-right: auto; width: ' + formWidth + '; min-height: 0; background-color: transparent;} #loginform {min-width: 0; max-width: none;}';

					formHorizontalPaddingStyleTag.innerHTML = '#loginform {padding-left: ' + formHorizontalPadding + '; padding-right: ' + formHorizontalPadding + ';}';

					formBorderWidthStyleTag.innerHTML = '#loginform {border-width: ' + formBorderWidth + ';}';

				}

			});
		});

		wp.customize('jltwp_adminify_login[form_bg_color]', function (setting) {
			setting.bind(function (val) {
				var formPosition = wp.customize('jltwp_adminify_login[form_position]').get();
				var content = '';

				val = val ? val : '#ffffff';
				formPosition = formPosition ? formPosition : 'default';

				if (formPosition === 'default') {
					content = '#login {background-color: transparent;} #loginform {background-color: ' + val + ';}';
				} else {
					content = '#login {background-color: ' + val + ';} #loginform {background-color: ' + val + ';}';
				}

				document.querySelector('[data-listen-value="jltwp_adminify_login[form_bg_color]"]').innerHTML = content;
			});
		});

		wp.customize('jltwp_adminify_login[form_width]', function (setting) {
			setting.bind(function (val) {
				var formPosition = wp.customize('jltwp_adminify_login[form_position]').get();
				var content = '';

				formPosition = formPosition ? formPosition : 'default';

				if (formPosition === 'default') {
					content = '#login {width: ' + val + ';}';
				} else {
					content = '#loginform {max-width: ' + val + ';}';
				}

				document.querySelector('[data-listen-value="jltwp_adminify_login[form_width]"]').innerHTML = content;
			});
		});

		wp.customize('jltwp_adminify_login[form_top_padding]', function (setting) {
			setting.bind(function (val) {
				var content = '#loginform {padding-top: ' + val + ';}';

				document.querySelector('[data-listen-value="jltwp_adminify_login[form_top_padding]"]').innerHTML = content;
			});
		});

		wp.customize('jltwp_adminify_login[form_bottom_padding]', function (setting) {
			setting.bind(function (val) {
				var content = '#loginform {padding-bottom: ' + val + ';}';

				document.querySelector('[data-listen-value="jltwp_adminify_login[form_bottom_padding]"]').innerHTML = content;
			});
		});

		wp.customize('jltwp_adminify_login[form_horizontal_padding]', function (setting) {
			setting.bind(function (val) {
				var content = val ? '#loginform {padding-left: ' + val + '; padding-right: ' + val + ';}' : '';

				document.querySelector('[data-listen-value="jltwp_adminify_login[form_horizontal_padding]"]').innerHTML = content;
			});
		});

		wp.customize('jltwp_adminify_login[form_border_width]', function (setting) {
			setting.bind(function (val) {
				var content = val ? '#loginform {border-width: ' + val + ';}' : '';

				document.querySelector('[data-listen-value="jltwp_adminify_login[form_border_width]"]').innerHTML = content;
			});
		});

		wp.customize('jltwp_adminify_login[form_border_color]', function (setting) {
			setting.bind(function (val) {
				val = val ? val : '#dddddd';

				document.querySelector('[data-listen-value="jltwp_adminify_login[form_border_color]"]').innerHTML = '#loginform {border-color: ' + val + ';}';
			});
		});

		wp.customize('jltwp_adminify_login[form_border_radius]', function (setting) {
			setting.bind(function (val) {
				var content = val ? '#loginform {border-radius: ' + val + ';}' : '';

				document.querySelector('[data-listen-value="jltwp_adminify_login[form_border_radius]"]').innerHTML = content;
			});
		});
	};

	events.formFieldsChange = function () {

		// Form Field Settings
		wp.customize( 'jltwp_adminify_login[login_form_fields]', function( value ) {

			value.bind( function( to ) {

				const labels = {};
				for (const label of Object.keys(to)) {
					if( label.startsWith('label_') ){
						const _id = label.replaceAll('label_', 'wp_adminify_');
						const id = _id.replaceAll('_','-');
						$('#' + id ).text(to[label]);
					} else{
						switch (label) {
							// case 'jltwp_adminify_login_form_fields_border_radius':
							// 	const radius = to['jltwp_adminify_login_form_fields_border_radius'];
							// 	$('#loginform input[type=text], #loginform input[type=password]').css({'border-top-right-radius': +radius.top });
							// 	break;
							case 'fields_user_placeholder':
								$( '#loginform #user_login' ).attr( 'placeholder', to['fields_user_placeholder'] );
								break;
							case 'fields_pass_placeholder':
								$( '#loginform #user_pass' ).attr( 'placeholder', to['fields_pass_placeholder'] );
								break;

							case 'style_label_font_size':
								$( '#wp-adminify-username, #wp-adminify-password, #wp-adminify-remember-me, #wp-adminify-lost-password, #backtoblog a' ).css( 'font-size', to['style_label_font_size'] + 'px' );
								break;

							case 'style_fields_height':
								$( '#loginform input[type=text], #loginform input[type=password], #loginform input[type=email], #loginform textarea' ).css( 'height', to['style_fields_height'] + 'px' );
								break;

							case 'style_fields_font_size':
								$( '#loginform input[type=text], #loginform input[type=password], #loginform input[type=email], #loginform textarea' ).css( 'font-size', to['style_fields_font_size'] + 'px' );
								break;

							case 'style_fields_bg':
								$( '#loginform input[type=text], #loginform input[type=password], #loginform input[type=email], #loginform textarea' ).css( 'background', to['style_fields_bg']['color'] );
								$( '#loginform input[type=text]:focus, #loginform input[type=email]:focus, #loginform textarea:focus, #loginform input[type=password]:focus' ).css({ background: to['style_fields_bg']['focus'] + ' !important '});
								break;

							case 'style_label_color':
								$('#loginform label, #wp-adminify-lost-password, #backtoblog a').css('color', to['style_label_color']);
								break;

							case 'style_fields_color':
								$('#loginform input[type=text], #loginform input[type=email], #loginform textarea, #loginform input[type=password]').css('color', to['style_fields_color']['color']);
								$('#loginform input[type=text]::placeholder, #loginform input[type=email]::placeholder, #loginform textarea::placeholder, #loginform input[type=password]::placeholder').css('color', to['style_fields_color']['color']);
								$('#loginform input[type=text]:focus, #loginform input[type=email]:focus, #loginform textarea:focus, #loginform input[type=password]:focus').css('color', to['style_fields_color']['focus']);
								break;

							case 'style_border':
								$('#loginform input[type=text], #loginform input[type=password], #loginform input[type=email], #loginform textarea').css('border-color', to['style_border']['color']);
								$('#loginform input[type=text], #loginform input[type=password], #loginform input[type=email], #loginform textarea').css('border-style', to['style_border']['style']);
								$('#loginform input[type=text], #loginform input[type=password], #loginform input[type=email], #loginform textarea').css('border-width', to['style_border']['top'] + 'px ' + to['style_border']['right'] + 'px ' + to['style_border']['bottom'] + 'px ' + to['style_border']['left'] + 'px ' );
								break;

							case 'style_border_radius':
								const b_r        = to['style_border_radius'],
								      b_r_top    = b_r['top'] + b_r['unit'],
								      b_r_right  = b_r['right'] + b_r['unit'],
								      b_r_bottom = b_r['bottom'] + b_r['unit'],
								      b_r_left   = b_r['left'] + b_r['unit'];
								$('#loginform input[type=text], #loginform input[type=password], #loginform input[type=email], #loginform textarea').css('border-radius', b_r_top + ' ' + b_r_right  + ' ' + b_r_bottom + ' ' + b_r_left );
								break;

							case 'fields_margin':
								const fl_margin   = to['fields_margin'],
								      fl_m_top    = fl_margin['top'] + fl_margin['unit'],
								      fl_m_right  = fl_margin['right'] + fl_margin['unit'],
								      fl_m_bottom = fl_margin['bottom'] + fl_margin['unit'],
								      fl_m_left   = fl_margin['left'] + fl_margin['unit'];

								$('#loginform input[type=text], #loginform input[type=password], #loginform input[type=email], #loginform textarea').css( { marginTop : fl_m_top, marginRight : fl_m_right, marginBottom : fl_m_bottom, marginLeft : fl_m_left } );
								break;

							case 'fields_padding':
								const fl_padding   = to['fields_padding'],
								      fl_p_top    = fl_padding['top'] + fl_padding['unit'],
								      fl_p_right  = fl_padding['right'] + fl_padding['unit'],
								      fl_p_bottom = fl_padding['bottom'] + fl_padding['unit'],
								      fl_p_left   = fl_padding['left'] + fl_padding['unit'];
								$('#loginform input[type=text], #loginform input[type=password], #loginform input[type=email], #loginform textarea').css( { paddingTop : fl_p_top, paddingRight : fl_p_right, paddingBottom : fl_p_bottom, paddingLeft : fl_p_left } );
								break;

							case 'fields_bs_color':
								const fl_bs_color = to['fields_bs_color'],
								      fl_bs_hz = to['fields_bs_hz'] + 'px',
								      fl_bs_ver = to['fields_bs_ver'] + 'px',
								      fl_bs_blur = to['fields_bs_blur'] + 'px',
								      fl_bs_spread = to['fields_bs_spread'] + 'px',
								      fl_bs_spread_pos = to['fields_bs_spread_pos'];

								$('#loginform input[type=text], #loginform input[type=password], #loginform input[type=email], #loginform textarea').css({ boxShadow: fl_bs_hz + ' ' + fl_bs_ver + ' ' + fl_bs_blur + ' ' + fl_bs_spread + ' ' + fl_bs_color + ' ' + fl_bs_spread_pos });
								break;

							case 'input_login':
								$( '#loginform input[name="wp-submit"]' ).val( to['input_login'] );
								break;
						}

					}
				}
			} );
		} );



		// Only Working Fires till now
		// console.log('Color Fired to', wp.customize( 'jltwp_adminify_login[credits_text_color]') );
		// wp.customize( 'jltwp_adminify_login[credits_text_color]', function( value ) {
		// 	console.log('Color Fired', value);
		// 	value.bind( function( to ) {
		// 		$( '#liton' ).text( to );
		// 		console.log('Color Fired to', to);
		// 	} );
		// } );


	};

	// Login Form Background Settings
	events.formBackground = function () {

		// Background Color
		wp.customize( 'jltwp_adminify_login[login_form_bg_color]', function( value ) {
			value.bind( function( to ) {
				if( ! to ) {
					return;
				}

				const form_bg = to['background-color'],
					form_bg_attach = to['background-attachment'],
					form_image = to['background-image'],
					form_bg_pos = to['background-position'],
					form_bg_repeat = to['background-repeat'],
					form_bg_size = to['background-size'];

				for (const label of Object.keys(to)) {

					switch (label) {
						case 'background-color':
							$('#loginform').css({backgroundColor: form_bg})
							break;

						case 'background-attachment':
							$('#loginform').css({backgroundAttachment: form_bg_attach})
							break;

						case 'background-image':
							$('#loginform').css({ backgroundImage: 'url(' + form_image['url'] +')' });
							break;

						case 'background-position':
							$('#loginform').css({ backgroundPosition: form_bg_pos });
							break;

						case 'background-repeat':
							$('#loginform').css({ backgroundRepeat: form_bg_repeat });
							break;

						case 'background-size':
							$('#loginform').css({ backgroundSize: form_bg_size });
							break;

					}
				}

			} );
		} );

		// Gradient Background Color
		wp.customize( 'jltwp_adminify_login[login_form_bg_gradient]', function( value ) {
			value.bind( function( to ) {
				if( ! to ) return;
				const form_bg          = to['background-color'],
				      form_bg_gr_color = to['background-gradient-color'],
				      form_bg_gr_dir   = to['background-gradient-direction'];
					$('#loginform').css({backgroundImage: 'linear-gradient(' + form_bg_gr_dir + ',' + form_bg + ',' + form_bg_gr_color + ')' })
			} );
		} );

		// Login Form Width and Height
		wp.customize( 'jltwp_adminify_login[login_form_height_width]', function( value ) {
			value.bind( function( to ) {
				if( ! to ) return;
					$('#loginform').css({width: to['width'], height: to['height'] })
			} );
		} );

		// Login Form Margin
		wp.customize( 'jltwp_adminify_login[login_form_margin]', function( value ) {
			value.bind( function( to ) {
			const margin_top    = to['top'] + to['unit'],
			      margin_right  = to['right'] + to['unit'],
			      margin_bottom = to['bottom'] + to['unit'],
			      margin_left   = to['left'] + to['unit'];

				$('#loginform').css({margin: margin_top + ' ' + margin_right + ' ' + margin_bottom + ' ' + margin_left })

			} );
		} );

		// Login Form Padding
		wp.customize( 'jltwp_adminify_login[login_form_padding]', function( value ) {
			value.bind( function( to ) {
			const padding_top    = to['top'] + to['unit'],
			      padding_right  = to['right'] + to['unit'],
			      padding_bottom = to['bottom'] + to['unit'],
			      padding_left   = to['left'] + to['unit'];

				$('#loginform').css({padding: padding_top + ' ' + padding_right + ' ' + padding_bottom + ' ' + padding_left })

			} );
		} );

		// Login Form Border Radius
		wp.customize( 'jltwp_adminify_login[login_form_border_radius]', function( value ) {
			value.bind( function( to ) {
			const border_radius_top    = to['top'] + to['unit'],
			      border_radius_right  = to['right'] + to['unit'],
			      border_radius_bottom = to['bottom'] + to['unit'],
			      border_radius_left   = to['left'] + to['unit'];

				$('#loginform').css({borderRadius: border_radius_top + ' ' + border_radius_right + ' ' + border_radius_bottom + ' ' + border_radius_left })
			} );
		} );

		// Login Form Border
		wp.customize( 'jltwp_adminify_login[login_form_border]', function( value ) {
			value.bind( function( to ) {
			const border_top    = to['top'],
			      border_right  = to['right'],
			      border_bottom = to['bottom'],
			      border_left   = to['left'];
				$('#loginform').css({borderWidth: border_top + 'px ' + border_right + 'px ' + border_bottom + 'px ' + border_left + 'px'})
				$('#loginform').css({borderStyle: to['style']})
				$('#loginform').css({borderColor: to['color']})
			} );
		} );

		// Login Form Box Shadow
		wp.customize( 'jltwp_adminify_login[login_form_box_shadow]', function( value ) {
			value.bind( function( to ) {
				const login_frm_bs_color       = to['bs_color'],
				      login_form_bs_hz         = to['bs_hz'] + 'px',
				      login_form_bs_ver        = to['bs_ver'] + 'px',
				      login_form_bs_blur       = to['bs_blur'] + 'px',
				      login_form_bs_spread     = to['bs_spread'] + 'px',
				      login_form_bs_spread_pos = to['bs_spread_pos'];

				$('#loginform').css({ boxShadow: login_form_bs_hz + ' ' + login_form_bs_ver + ' ' + login_form_bs_blur + ' ' + login_form_bs_spread + ' ' + login_frm_bs_color + ' ' + login_form_bs_spread_pos });
			} );
		} );

	};


	// Credits Settings
	events.brandingConfig = function () {
		wp.customize( 'jltwp_adminify_login[jltwp_adminify_credits]', function( value ) {
			value.bind( function( to ) {
				if (to !=='1' ) {
					$( '.wp-adminify-badge' ).addClass( 'is-hidden' );
				} else {
					$( '.wp-adminify-badge' ).removeClass( 'is-hidden' );
				}
			} );
		} );

		wp.customize( 'jltwp_adminify_login[credits_text_color]', function( value ) {
			value.bind( function( to ) {
				$( '.wp-adminify-badge__text' ).css( 'color', to );
			} );
		} );

		wp.customize( 'jltwp_adminify_login[credits_logo_position]', function( value ) {
			value.bind( function( to ) {
				var credit_position = to['background-position'],
					credit_position = credit_position.replace(/\s+/g, '-').toLowerCase();
				$( '.wp-adminify-badge' ).attr( 'class', 'wp-adminify-badge' );
				$( '.wp-adminify-badge' ).addClass( credit_position );
			} );
		} );
	};

  /**
   * WordPress Login Page Error Messages.
   */
	events.errorMessages = function () {
		wp.customize( 'jltwp_adminify_login[login_error_messages]', function( value ) {
		value.bind( function( to ) {
			for (const label of Object.keys(to)) {
				switch (label) {
					case 'error_incorrect_username':
						if(to['error_incorrect_username']==''){
							$('#login_error').html('');
							$('#login_error').css( 'display', 'none' );
						}else{
							$('#login_error').html(to['error_incorrect_username']);
							$('#login_error').css( 'display', 'block' );
						}
					break;

					case 'error_empty_username':
						if(to['error_empty_username']==''){
							$('#login_error').html('');
							$('#login_error').css( 'display', 'none' );
						}else{
							$('#login_error').html(to['error_empty_username']);
							$('#login_error').css( 'display', 'block' );
						}
					break;

					case 'error_exists_username':
						if(to['error_exists_username']==''){
							$('#login_error').html('');
							$('#login_error').css( 'display', 'none' );
						}else{
							$('#login_error').html(to['error_exists_username']);
							$('#login_error').css( 'display', 'block' );
						}
					break;

					case 'error_incorrect_password':
						if(to['error_incorrect_password']==''){
							$('#login_error').html('');
							$('#login_error').css( 'display', 'none' );
						}else{
							$('#login_error').html(to['error_incorrect_password']);
							$('#login_error').css( 'display', 'block' );
						}
					break;

					case 'error_empty_password':
						if(to['error_empty_password']==''){
							$('#login_error').html('');
							$('#login_error').css( 'display', 'none' );
						}else{
							$('#login_error').html(to['error_empty_password']);
							$('#login_error').css( 'display', 'block' );
						}
					break;

					case 'error_forget_password':
						if(to['error_forget_password']==''){
							$('#login_error').html('');
							$('#login_error').css( 'display', 'none' );
						}else{
							$('#login_error').html(to['error_forget_password']);
							$('#login_error').css( 'display', 'block' );
						}
					break;

					case 'error_incorrect_email':
						if(to['error_incorrect_email']==''){
							$('#login_error').html('');
							$('#login_error').css( 'display', 'none' );
						}else{
							$('#login_error').html(to['error_incorrect_email']);
							$('#login_error').css( 'display', 'block' );
						}
					break;

					case 'error_empty_email':
						if(to['error_empty_email']==''){
							$('#login_error').html('');
							$('#login_error').css( 'display', 'none' );
						}else{
							$('#login_error').html(to['error_empty_email']);
							$('#login_error').css( 'display', 'block' );
						}
					break;

					case 'error_exists_email':
						if(to['error_exists_email']==''){
							$('#login_error').html('');
							$('#login_error').css( 'display', 'none' );
						}else{
							$('#login_error').html(to['error_exists_email']);
							$('#login_error').css( 'display', 'block' );
						}
					break;

				}
			}
		} );
		} );
	};


	// Button Changes
	events.formButtonChanges = function () {

		// Height & Width
		wp.customize( 'jltwp_adminify_login[button_size]', function( value ) {
			value.bind( function( to ) {
				const btn_width = to['width'] + to['unit'],
					btn_height = to['height'] + to['unit'];
				$('#wp-submit').css( {width:btn_width, height:btn_height, lineHeight: btn_height })
			} );
		} );

		// Font Size
		wp.customize( 'jltwp_adminify_login[button_font_size]', function( value ) {
			value.bind( function( to ) {
				$('#wp-submit').css( {fontSize:to+'px'})
			} );
		} );

		// Button Style Settings
		wp.customize( 'jltwp_adminify_login[login_form_button_settings]', function( value ) {
			value.bind( function( to ) {

				for (const label of Object.keys(to)) {
					switch (label) {
						case 'button_bg':
							$('#wp-submit').css( {background:to['button_bg']})
							break;
						case 'button_text_color':
							$('#wp-submit').css( {color:to['button_text_color']})
							break;
						// case 'button_border_color':
						// 	const btn_border_color  = to['button_border_color']['color'],
						// 	      btn_border_style  = to['button_border_color']['style'],
						// 	      btn_border_top    = to['button_border_color']['top'],
						// 	      btn_border_right  = to['button_border_color']['right'],
						// 	      btn_border_bottom = to['button_border_color']['bottom'],
						// 	      btn_border_left   = to['button_border_color']['left'];

						// 	$('#wp-submit').css( {borderColor:btn_border_color, borderStyle:btn_border_style, borderWidth: btn_border_top +'px ' + btn_border_right +'px ' + btn_border_bottom +'px ' + btn_border_left +'px' })
						// 	break;
						case 'button_border':
							const btn_border_color  = to['button_border']['color'],
							      btn_border_style  = to['button_border']['style'],
							      btn_border_top    = to['button_border']['top'],
							      btn_border_right  = to['button_border']['right'],
							      btn_border_bottom = to['button_border']['bottom'],
							      btn_border_left   = to['button_border']['left'];

							$('#wp-submit').css( {borderColor:btn_border_color, borderStyle:btn_border_style, borderWidth: btn_border_top +'px ' + btn_border_right +'px ' + btn_border_bottom +'px ' + btn_border_left +'px' })
							break;
						// case 'button_border_radius':
						// 	const btn_border_radius_top    = to['button_border_radius']['top'] + to['button_border_radius']['unit'],
						// 	      btn_border_radius_right  = to['button_border_radius']['right'] + to['button_border_radius']['unit'],
						// 	      btn_border_radius_bottom = to['button_border_radius']['bottom'] + to['button_border_radius']['unit'],
						// 	      btn_border_radius_left   = to['button_border_radius']['left'] + to['button_border_radius']['unit']

						// 	$('#wp-submit').css( {borderRadius: btn_border_radius_top +' ' + btn_border_radius_right +' ' + btn_border_radius_bottom +' ' + btn_border_radius_left })
						// 	break;
						case 'button_border_radius':
							const btn_border_radius_top    = to['button_border_radius']['top'] + to['button_border_radius']['unit'],
							      btn_border_radius_right  = to['button_border_radius']['right'] + to['button_border_radius']['unit'],
							      btn_border_radius_bottom = to['button_border_radius']['bottom'] + to['button_border_radius']['unit'],
							      btn_border_radius_left   = to['button_border_radius']['left'] + to['button_border_radius']['unit']

							$('#wp-submit').css( {borderRadius: btn_border_radius_top +' ' + btn_border_radius_right +' ' + btn_border_radius_bottom +' ' + btn_border_radius_left })
							break;
						case 'button_text_shadow':
							const btn_text_shadow = to['button_text_shadow'],
							      btn_ts_color   = btn_text_shadow['ts_color'],
							      btn_ts_hz      = btn_text_shadow['ts_hz'] + 'px',
							      btn_ts_ver     = btn_text_shadow['ts_ver'] + 'px',
							      btn_ts_blur    = btn_text_shadow['ts_ver'] + 'px';
							$('#wp-submit').css({ textShadow: btn_ts_hz + ' ' + btn_ts_ver + ' ' + btn_ts_blur + ' ' + btn_ts_color });
							break;
						case 'button_box_shadow':
							const btn_box_shadow = to['button_box_shadow'],
							      btn_bs_color   = btn_box_shadow['bs_color'],
							      btn_bs_hz      = btn_box_shadow['bs_hz'] + 'px',
							      btn_bs_ver     = btn_box_shadow['bs_ver'] + 'px',
							      btn_bs_blur    = btn_box_shadow['bs_blur'] + 'px',
							      btn_bs_spread  = btn_box_shadow['bs_spread'] + 'px',
							      btn_spread_pos = btn_box_shadow['bs_spread_pos'];

							$('#wp-submit').css({ boxShadow: btn_bs_hz + ' ' + btn_bs_ver + ' ' + btn_bs_blur + ' ' + btn_bs_spread + ' ' + btn_bs_color + ' ' + btn_spread_pos });
							break;
						case 'button_margin':
							const btn_margin        = to['button_margin'],
							      btn_margin_top    = btn_margin['top'] + btn_margin['unit'],
							      btn_margin_right  = btn_margin['right'] + btn_margin['unit'],
							      btn_margin_bottom = btn_margin['bottom'] + btn_margin['unit'],
							      btn_margin_left   = btn_margin['left'] + btn_margin['unit'];

							$('#wp-submit').css({margin: btn_margin_top + ' ' + btn_margin_right + ' ' + btn_margin_bottom + ' ' + btn_margin_left })
							break;
						case 'button_padding':
							const btn_padding        = to['button_padding'],
							      btn_padding_top    = btn_padding['top'] + btn_padding['unit'],
							      btn_padding_right  = btn_padding['right'] + btn_padding['unit'],
							      btn_padding_bottom = btn_padding['bottom'] + btn_padding['unit'],
							      btn_padding_left   = btn_padding['left'] + btn_padding['unit'];

							$('#wp-submit').css({padding: btn_padding_top + ' ' + btn_padding_right + ' ' + btn_padding_bottom + ' ' + btn_padding_left })
							break;

						//Button Hover Conditions
						case 'button_bg_hover':
							$('#loginform input#wp-submit:hover,#loginform input#wp-submit:focus').css( 'background-color', to['button_bg_hover'])
							break;
						case 'button_text_hover':
							$('#loginform input#wp-submit:hover,#loginform input#wp-submit:focus').css( 'color', to['button_text_hover'])
							break;
						// Turned off for future use
						// case 'button_border_color_hover':
						// 	const btn_border_color_hover  = to['button_border_color_hover']['color'],
						// 	      btn_border_style_hover  = to['button_border_color_hover']['style'],
						// 	      btn_border_top_hover    = to['button_border_color_hover']['top'],
						// 	      btn_border_right_hover  = to['button_border_color_hover']['right'],
						// 	      btn_border_bottom_hover = to['button_border_color_hover']['bottom'],
						// 	      btn_border_left_hover   = to['button_border_color_hover']['left'];

						// 	$('#loginform input#wp-submit:hover,#loginform input#wp-submit:focus').css( {borderColor:btn_border_color_hover, borderStyle:btn_border_style_hover, borderWidth: btn_border_top_hover +'px ' + btn_border_right_hover +'px ' + btn_border_bottom_hover +'px ' + btn_border_left_hover +'px' })
						// 	break;

						// case 'button_border_radius_hover':
						// 	const btn_border_radius_top_hover    = to['button_border_radius_hover']['top'] + to['button_border_radius_hover']['unit'],
						// 	      btn_border_radius_right_hover  = to['button_border_radius_hover']['right'] + to['button_border_radius_hover']['unit'],
						// 	      btn_border_radius_bottom_hover = to['button_border_radius_hover']['bottom'] + to['button_border_radius_hover']['unit'],
						// 	      btn_border_radius_left_hover   = to['button_border_radius_hover']['left'] + to['button_border_radius_hover']['unit']

						// 	$('#loginform input#wp-submit:hover,#loginform input#wp-submit:focus').css( {borderRadius: btn_border_radius_top_hover +' ' + btn_border_radius_right_hover +' ' + btn_border_radius_bottom_hover +' ' + btn_border_radius_left_hover })
						// 	break;

						case 'button_text_shadow_hover':
							const btn_text_shadow_hover = to['button_text_shadow_hover'],
							      btn_ts_color_hover   = btn_text_shadow_hover['ts_hover'],
							      btn_ts_hz_hover      = btn_text_shadow_hover['ts_hz_hover'] + 'px',
							      btn_ts_ver_hover     = btn_text_shadow_hover['ts_ver_hover'] + 'px',
							      btn_ts_blur_hover    = btn_text_shadow_hover['ts_blur_hover'] + 'px';
							$('#loginform input#wp-submit:hover,#loginform input#wp-submit:focus').css({ textShadow: btn_ts_hz_hover + ' ' + btn_ts_ver_hover + ' ' + btn_ts_blur_hover + ' ' + btn_ts_color_hover });
							break;
						//Turned off for Future use
						// case 'button_box_shadow_hover':
						// 	const btn_box_shadow_hover = to['button_box_shadow_hover'],
						// 	      btn_bs_color_hover   = btn_box_shadow_hover['bs_color'],
						// 	      btn_bs_hz_hover      = btn_box_shadow_hover['bs_hz'] + 'px',
						// 	      btn_bs_ver_hover     = btn_box_shadow_hover['bs_ver'] + 'px',
						// 	      btn_bs_blur_hover    = btn_box_shadow_hover['bs_blur'] + 'px',
						// 	      btn_bs_spread_hover  = btn_box_shadow_hover['bs_spread'] + 'px',
						// 	      btn_spread_pos_hover = btn_box_shadow_hover['bs_spread_pos'];

						// 	$('#loginform input#wp-submit:hover,#loginform input#wp-submit:focus').css({ boxShadow: btn_bs_hz_hover + ' ' + btn_bs_ver_hover + ' ' + btn_bs_blur_hover + ' ' + btn_bs_spread_hover + ' ' + btn_bs_color_hover + ' ' + btn_spread_pos_hover });
						// 	break;
					}
				}

			} );
		} );

	}


	events.footerFieldsChange = function () {
		wp.customize('jltwp_adminify_login[footer_link_color]', function (setting) {
			setting.bind(function (val) {
				val = val ? val : '#555d66';

				document.querySelector('[data-listen-value="jltwp_adminify_login[footer_link_color]"]').innerHTML = '.login #nav a, .login #backtoblog a {color: ' + val + ';}';
			});
		});

		wp.customize('jltwp_adminify_login[footer_link_color_hover]', function (setting) {
			setting.bind(function (val) {
				val = val ? val : '#00a0d2';

				document.querySelector('[data-listen-value="jltwp_adminify_login[footer_link_color_hover]"]').innerHTML = '.login #nav a:hover, .login #nav a:focus, .login #backtoblog a:hover, .login #backtoblog a:focus {color: ' + val + ';}';
			});
		});
	};



	events.formAlignments = function(){

		// Gradient Background Options
		wp.customize('jltwp_adminify_login[alignment_login_bg]', function (value) {

			value.bind( function (to) {

				const alignment_login_bg_color           = to['background-color'],
				      alignment_login_gradient_color     = to['background-gradient-color'],
				      alignment_login_gradient_direction = to['background-gradient-direction'],
				      alignment_login_image              = to['background-image'],
				      alignment_login_pos                = to['background-position'],
				      alignment_login_repeat             = to['background-repeat'],
				      alignment_login_size               = to['background-size'],
				      alignment_login_blend_mode         = to['background-blend-mode'];

				for (const label of Object.keys(to)) {
					switch (label) {
						case 'background-color':
							if(alignment_login_bg_color !=''){
								$('#wp-adminify-half-bg-color').remove();
								$('head').append( '<style id="wp-adminify-half-bg-color">body.wp-adminify-half-screen .wp-adminify-form-container::after {background: ' + alignment_login_bg_color + '}' );
							}
							break;
						case 'background-gradient-color':
							if(alignment_login_gradient_color !=''){
								$('#wp-adminify-half-bg-graident-bg').remove();
								$('head').append( '<style id="wp-adminify-half-bg-graident-bg">body.wp-adminify-half-screen .wp-adminify-form-container::after {background-image: linear-gradient(' + alignment_login_gradient_direction + ',' + alignment_login_bg_color + ',' + alignment_login_gradient_color + ')}' );
							}
							break;
						case 'background-image':
							if(alignment_login_image['url'] !=''){
								$('#wp-adminify-half-bg-image').remove();
								$('head').append( '<style id="wp-adminify-half-bg-image">body.wp-adminify-half-screen .wp-adminify-form-container::after {background-image: url(' + alignment_login_image['url'] +')' );
							}
							break;
						case 'background-position':
							$('#wp-adminify-half-bg-graident-pos').remove();
							$('head').append( '<style id="wp-adminify-half-bg-graident-pos">body.wp-adminify-half-screen .wp-adminify-form-container::after {background-position: ' + alignment_login_pos +'}' );
							break;
						case 'background-repeat':
							$('#wp-adminify-half-bg-repeat').remove();
							$('head').append( '<style id="wp-adminify-half-bg-repeat">body.wp-adminify-half-screen .wp-adminify-form-container::after{ background-repeat: ' + alignment_login_repeat +'}' );
							break;
						case 'background-size':
							$('#wp-adminify-half-bg-size').remove();
							$('head').append( '<style id="wp-adminify-half-bg-size">body.wp-adminify-half-screen .wp-adminify-form-container::after{ background-size: ' + alignment_login_size +'}' );
							break;
						case 'background-blend-mode':
							$('#wp-adminify-half-bg-blend-mode').remove();
							$('head').append( '<style id="wp-adminify-half-bg-blend-mode">body.wp-adminify-half-screen .wp-adminify-form-container::after{ background-blend-mode: ' + alignment_login_blend_mode +'}' );
							break;
					}
				}

			});
		});


		/* Skew Background */
		wp.customize( 'jltwp_adminify_login[alignment_login_bg_skew]', function( value ) {
			value.bind( function( to ) {
				const align_layout_width = wp.customize('jltwp_adminify_login[alignment_login_width]').get(),
					align_column_skew = wp.customize('jltwp_adminify_login[alignment_login_column]').get();
				if( align_layout_width === "fullwidth"){
					$('#wp-adminify-fullwidth-skew').remove();
					if(to>0){
						$('head').append( '<style id="wp-adminify-fullwidth-skew">body.wp-adminify-fullwidth .wp-adminify-form-container::after {transform: skewX(' + to + 'deg)}' );
					} else{
						$('head').append( '<style id="wp-adminify-fullwidth-skew">body.wp-adminify-fullwidth .wp-adminify-form-container::after {transform: skewY(' + to + 'deg)}' );
					}
				}else{
					$('#wp-adminify-half-bg-skew').remove();
					if(align_column_skew === 'top' || align_column_skew === 'bottom'){
						$('head').append( '<style id="wp-adminify-half-bg-skew">body.wp-adminify-half-screen .wp-adminify-form-container::after {transform: skewY(' + to + 'deg)}' );
					} else if(align_column_skew === 'right' || align_column_skew === 'left'){
						$('head').append( '<style id="wp-adminify-half-bg-skew">body.wp-adminify-half-screen .wp-adminify-form-container::after {transform: skewX(' + to + 'deg)}' );
					}
				}
			} );
		} );

		/* Layout Widths */
		wp.customize( 'jltwp_adminify_login[alignment_login_width]', function( value ) {
			value.bind( function( to ) {
				const layout_column_skey = wp.customize('jltwp_adminify_login[alignment_login_column]').get();

				if ( 'fullwidth' === to ) {
					if( layout_column_skey != 0){
						$( 'body' ).removeClass( 'wp-adminify-half-screen' );
						$( 'body' ).addClass( 'wp-adminify-fullwidth' );
					}

				} else if ( 'width_two_column' === to ) {
					$( 'body' ).addClass( 'wp-adminify-half-screen' );
				} else {
					$( 'body' ).removeClass( 'wp-adminify-half-screen' );
				}
				} );

		} );

		/* Column Alignments */
		wp.customize( 'jltwp_adminify_login[alignment_login_column]', function( value ) {
			value.bind( function( to ) {
			$( 'body' ).removeClass( 'jltwp-adminify-login-top jltwp-adminify-login-right jltwp-adminify-login-bottom jltwp-adminify-login-left' ).addClass( 'jltwp-adminify-login-' + to );
			} );
		} );

		/* Horizontal Column Alignments */
		wp.customize( 'jltwp_adminify_login[alignment_login_horizontal]', function( value ) {
			value.bind( function( to ) {
			$( 'body' ).removeClass( 'wp-adminify-horizontal-align-center_center wp-adminify-horizontal-align-left_center wp-adminify-horizontal-align-right_center' ).addClass( 'wp-adminify-horizontal-align-' + to );
			} );
		} );

		/* Vertical Column Alignments */
		wp.customize( 'jltwp_adminify_login[alignment_login_vertical]', function( value ) {
			value.bind( function( to ) {
			$( 'body' ).removeClass( 'wp-adminify-vertical-align-center_top wp-adminify-vertical-align-center_center wp-adminify-vertical-align-center_bottom' ).addClass( 'wp-adminify-vertical-align-' + to );
			} );
		} );
	}

	function showProNotice(autoHide) {
		var notice = document.querySelector('.wp-adminify-pro-login-customizer-notice');
		if (!notice) return;

		notice.classList.add('is-shown');

		if (autoHide) setTimeout(hideProNotice, 3000);
	}

	function hideProNotice() {
		var notice = document.querySelector('.wp-adminify-pro-login-customizer-notice');
		if (!notice) return;

		notice.classList.remove('is-shown');
	}

})(jQuery);

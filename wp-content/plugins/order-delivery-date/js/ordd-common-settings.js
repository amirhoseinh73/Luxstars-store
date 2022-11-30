/**
 * Events added to perform UI changes in the admin
 * 
 * @namespace orddd_admin_js
 * @since 7.5
 */

jQuery( function( $ ) {	
	var orddd_weekdays = JSON.parse( localizeStrings.orddd_weekdays );
	var formats = [ "mm-dd-yy", "d.m.y", "d M, yy","MM d, yy" ];

	var settings = {

		init: function() {
			// Add Color Picker to all inputs that have 'color-field' class
			jQuery( '.cpa-color-picker' ).wpColorPicker();
			jQuery( ".orddd_weekdays" ).select2();
			jQuery( ".orddd_shipping_days" ).select2();

			jQuery.datepicker.setDefaults( jQuery.datepicker.regional[ "en-GB" ] );
			var formats = [ "mm-dd-yy", "d.m.y", "d M, yy","MM d, yy" ];

			/** Date settings */
			if ( jQuery( "input[type=radio][id=\"orddd_delivery_checkout_options\"][value=\"delivery_calendar\"]" ).is(":checked") ) {
				i = 0;
				jQuery( ".form-table"  ).each( function() {
					if( i == 0 ) {
						k = 0;
						var row = jQuery( this ).find( "tr" );
						if( row.length == 10 ) {
							jQuery.each( row , function() {
								if( k == 9 ) {
									jQuery( this ).fadeOut();
								} else {
									jQuery( this ).fadeIn();    
								}
								k++ 
							});
						} else {
							jQuery.each( row , function() {
								if( k == 11 ) {
									jQuery( this ).fadeOut();
								} else {
									jQuery( this ).fadeIn();    
								}
								k++ 
							});
						}
					} else {
						jQuery( this ).fadeIn();
					} 
					i++;
				}); 
			} else if ( jQuery( "input[type=radio][id=\"orddd_delivery_checkout_options\"][value=\"text_block\"]" ).is(":checked") ) {
				i = 0;
				jQuery(".form-table").each( function() {
					if( i == 0 ) {
						k = 0;
						var row = jQuery( this ).find( "tr" );
						if( row.length == 10 ) {
							jQuery.each( row , function() {
		
								if( k == 1 || k == 0 || k == 4 ) {
									// the field needs to be shown so we do nothing
								} else if( k == 9 ) {
									jQuery( this ).fadeIn();    
								} else {
									jQuery( this ).fadeOut();
								}
								k++ 
							});
						} else {
							jQuery.each( row , function() {
		
								if( k == 1 || k == 0 || k == 4 || k == 7 || k == 9 || k == 10 ) {
									// the field needs to be shown so we do nothing
								} else if( k == 11 ) {
									jQuery( this ).fadeIn();    
								} else {
									jQuery( this ).fadeOut();
								}
								k++ 
							});
						}
					}
					i++;
				});
			}

			/** Time Slots */
			if( 'on' !== jQuery('#specific_dates_enabled').val() ) {
				jQuery( "input[type=radio][id=\"orddd_time_slot_for_delivery_days\"][value=\"specific_dates\"]" ).attr( "disabled", "disabled" );
				jQuery( "input[type=radio][id=\"orddd_bulk_time_slot_for_delivery_days\"][value=\"specific_dates\"]" ).attr( "disabled", "disabled" );
				jQuery( '#orddd_select_delivery_dates' ).attr( "disabled", "disabled" );
				jQuery( '#orddd_select_delivery_dates_bulk' ).attr( "disabled", "disabled" );
			}

			if ( jQuery( "input[type=radio][id=\"orddd_time_slot_for_delivery_days\"][value=\"weekdays\"]" ).is(":checked") ) {
				jQuery( '.time_slot_options' ).slideUp();
				jQuery( '.time_slot_for_weekdays' ).slideDown();
			} else {
				jQuery( '.time_slot_options' ).slideDown();
				jQuery( '.time_slot_for_weekdays' ).slideUp();
			}
			jQuery( '.orddd_time_slot_for_weekdays' ).select2();
			jQuery( '.orddd_time_slot_for_weekdays' ).css({'width': '300px' });
			jQuery( "input[type=radio][id=\"orddd_time_slot_for_delivery_days\"]" ).on( 'change', function() {
				if ( jQuery( this ).is(':checked') ) {
					var value = jQuery( this ).val();
					jQuery( '.time_slot_options' ).slideUp();
					jQuery( '.time_slot_for_' + value ).slideDown();
				}
			});

			// Bulk Time Slots
			if ( jQuery( "input[type=radio][id=\"orddd_bulk_time_slot_for_delivery_days\"][value=\"weekdays\"]" ).is(":checked") ) {
				jQuery( '.time_slot_options_bulk' ).slideUp();
				jQuery( '.time_slot_for_bulk_weekdays' ).slideDown();
			} else {
				jQuery( '.time_slot_options_bulk' ).slideDown();
				jQuery( '.time_slot_for_bulk_weekdays' ).slideUp();
			}
			jQuery( '.orddd_bulk_time_slot_for_delivery_days' ).select2();
			jQuery( '.orddd_bulk_time_slot_for_delivery_days' ).css({'width': '300px' });
			jQuery( "input[type=radio][id=\"orddd_bulk_time_slot_for_delivery_days\"]" ).on( 'change', function() {
				if ( jQuery( this ).is(':checked') ) {
					var value = jQuery( this ).val();
					jQuery( '.time_slot_options_bulk' ).slideUp();
					jQuery( '.time_slot_for_bulk_' + value ).slideDown();
				}
			});

			/** Block time slots */
			jQuery( '.orddd_selected_time_slots_to_be_disabled' ).select2();
			jQuery( '.orddd_selected_time_slots_to_be_disabled' ).css({'width': '300px' });
			jQuery( '.orddd_disable_time_slot_for_weekdays' ).select2();
			jQuery( '.orddd_disable_time_slot_for_weekdays' ).css({'width': '300px' });

			if ( jQuery( "input[type=radio][id=\"orddd_disable_time_slot_for_delivery_days\"][value=\"weekdays\"]" ).is(":checked") ) {
				jQuery( '.disable_time_slot_options' ).slideUp();
				jQuery( '.disable_time_slot_for_weekdays' ).slideDown();
			} else {
				jQuery( '.disable_time_slot_options' ).slideDown();
				jQuery( '.disable_time_slot_for_weekdays' ).slideUp();
			}

			jQuery( "#disable_time_slot_for_dates" ).datepick(
				{ dateFormat: formats[0], multiSelect: 999, monthsToShow: 1, showTrigger: "#calImg" } 
			);

			/** Business days settings */
			$('#orddd_business_opening_time, #orddd_business_closing_time').timepicker({ 
				'scrollDefault': 'now', 
				'timeFormat' : 'h:i A',
				'step'	: 15,
				'listWidth' : 1,
			});

			/** Time Settings */
			from_value = $( '#orddd_delivery_from_hours' ).val();
			to_value = $( '#orddd_delivery_to_hours' ).val();
			for( i = from_value - 1; i > 0; i-- ) {
				$( '#orddd_delivery_to_hours option[value="'+i+'"]' ).attr( 'disabled', true );		
			}

			/** Appearance settings */
			if( 'yes' === $('#orddd_delivery_dates_in_dropdown').val() ) {
				$('#start_of_week').closest('tr').hide();
				$('#orddd_number_of_months').closest('tr').hide();
				$('#switcher').closest('tr').hide();
				$('#orddd_calendar_display_mode').closest('tr').hide();
			} else {
				$('#start_of_week').closest('tr').show();
				$('#orddd_number_of_months').closest('tr').show();
				$('#switcher').closest('tr').show();
				$('#orddd_calendar_display_mode').closest('tr').show();
			}

			/** Switch the theme in the appearance settings */
			if( jQuery( "#switcher" ).length > 0 ) {
				var calendar_themes = JSON.parse( jQuery( "#orddd_calendar_themes" ).val() );
				jQuery( "#switcher" ).themeswitcher( {
					imgpath: jQuery( "#orddd_image_path" ).val(),
					loadTheme: jQuery( "#orddd_calendar_theme_name" ).val(),
					cookieName: "orddd-jquery-ui-theme",
					onclose: function() {
						jQuery( "#orddd_calendar_theme" ).val( jQuery.cookie( "orddd-jquery-ui-theme" ) );
						for ( const property in calendar_themes ) {
							if( jQuery.cookie( "orddd-jquery-ui-theme" ) == property ) {
								jQuery( "input#orddd_calendar_theme_name" ).val( calendar_themes[property] );
							}
						}
						jQuery( "<link/>", {
							rel: "stylesheet",
							type: "text/css",
							href: jQuery( "#orddd_css_path" ).val()
						}).appendTo( "head" );
					},
					
				});
			}

			/** Change preview datepicker theme on change of theme setting */
			jQuery.datepicker.setDefaults( jQuery.datepicker.regional[ "" ] );
			jQuery( "#datepicker" ).datepicker( jQuery.datepicker.regional[ jQuery( "#orddd_language_selected" ).val() ] );
			jQuery("#datepicker").datepicker("option", "firstDay",jQuery( "#start_of_week" ).val() );

			/** Specific dates settings */
			jQuery( "#orddd_delivery_date_1" ).width( "100px" );
			jQuery( "#orddd_delivery_date_1" ).val("").datepicker( {constrainInput: true, dateFormat: formats[0], minDate: new Date(), firstDay: jQuery( "#start_of_week" ).val() } ); 
			jQuery( "#orddd_delivery_date_2" ).val("").datepicker( {constrainInput: true, dateFormat: formats[0], minDate: new Date(), firstDay: jQuery( "#start_of_week" ).val() } ); 
			jQuery( "#orddd_delivery_date_3" ).val("").datepicker( {constrainInput: true, dateFormat: formats[0], minDate: new Date(), firstDay: jQuery( "#start_of_week" ).val() } ); 

			/** Holidays settings */
			jQuery( "#orddd_holiday_from_date" ).val( "" ).datepicker( {
				constrainInput: true,
				dateFormat: formats[0],
				onSelect: function( selectedDate,inst ) {
					var monthValue = inst.selectedMonth+1;
					var dayValue = inst.selectedDay;
					var yearValue = inst.selectedYear;
					var current_dt = dayValue + "-" + monthValue + "-" + yearValue;
					var to_date = jQuery("#orddd_holiday_to_date").val();
					if ( to_date == "") {    
						var split = current_dt.split("-");
						split[1] = split[1] - 1;		
						var minDate = new Date(split[2],split[1],split[0]);
						jQuery("#orddd_holiday_to_date").datepicker("setDate",minDate);
					}
				},
				firstDay: jQuery("#orddd_start_of_week").val(),
			} );
	
			jQuery( "#orddd_holiday_to_date" ).val( "" ).datepicker( {
				constrainInput: true,
				dateFormat: formats[0],
				firstDay: jQuery("#orddd_start_of_week").val(),
			} );

			/** Additional Settings */
			//Show on Orders Listing Page: setting toggle.
			if ( jQuery( "#orddd_show_column_on_orders_page_check" ).is(':checked') ) {
				jQuery( '#orddd_enable_default_sorting_of_column' ).fadeIn();
				jQuery( 'label[ for=\"orddd_enable_default_sorting_of_column\" ]' ).fadeIn();
			} else {
				jQuery( '#orddd_enable_default_sorting_of_column' ).fadeOut();
				jQuery( 'label[ for=\"orddd_enable_default_sorting_of_column\" ]' ).fadeOut();
			}

			// Allow Customers to edit Delivery Date settings toggle.
			if ( jQuery( "#orddd_allow_customers_to_edit_date" ).is(':checked') ) {
				jQuery( '#orddd_send_email_to_admin_when_date_updated' ).fadeIn();
				jQuery( 'label[ for=\"orddd_send_email_to_admin_when_date_updated\" ]' ).fadeIn();

				jQuery( '#orddd_disable_edit_after_cutoff' ).fadeIn();
				jQuery( 'label[ for=\"orddd_disable_edit_after_cutoff\" ]' ).fadeIn();
			} else {
				jQuery( '#orddd_send_email_to_admin_when_date_updated' ).fadeOut();
				jQuery( 'label[ for=\"orddd_send_email_to_admin_when_date_updated\" ]' ).fadeOut();

				jQuery( '#orddd_disable_edit_after_cutoff' ).fadeOut();
				jQuery( 'label[ for=\"orddd_disable_edit_after_cutoff\" ]' ).fadeOut();
			}

			/** Calendar Sync Settings */
			var isChecked = jQuery( "#orddd_calendar_sync_integration_mode:checked" ).val();
			if( isChecked == "directly" ) {
			i = 0;
			jQuery( ".form-table" ).each( function() {
					if( i == 2 ) {
						k = 0;
						var row = jQuery( this ).find( "tr" );
						jQuery.each( row , function() {
							if( k == 7 ) {
								jQuery( this ).fadeOut();
							} else {
								jQuery( this ).fadeIn();
							}
							k++;
						});
					} else {
						jQuery( this ).fadeIn();
					}
					i++;
				} );
			} else if( isChecked == "manually" ) {
				i = 0;
				jQuery( ".form-table" ).each( function() {
					if( i == 2 ) {
						k = 0;
						var row = jQuery( this ).find( "tr" );
						jQuery.each( row , function() {
							if( k != 7 && k != 0 ) {
								jQuery( this ).fadeOut();
							} else {
								jQuery( this ).fadeIn();
							}
							k++;
						});
					} else {
						jQuery( this ).fadeIn();
					}
					i++;
				});
			} else if( isChecked == "disabled" ) {
				i = 0;
				jQuery( ".form-table" ).each( function() {
					if( i == 2 ) {
						k = 0;
						var row = jQuery( this ).find( "tr" );
						jQuery.each( row , function() {
							if( k != 0 ) {
								jQuery( this ).fadeOut();
							} else {
								jQuery( this ).fadeIn();
							}
							k++;
						});
					} else {
						jQuery( this ).fadeIn();
					}
					i++;
				});
			}
			
			/** Custom settings */
			jQuery( '.orddd_shipping_methods_for_categories' ).select2();
			jQuery( '.orddd_categories_for_pickup_locations' ).select2();

			jQuery( '.orddd_shipping_methods' ).select2();
			jQuery( '.orddd_shipping_methods' ).css({'width': '300px' });

			jQuery( '.orddd_shipping_based_time_slot_for_weekdays' ).css({'width': '300px' });
			jQuery( '.orddd_shipping_based_select_delivery_dates' ).css({'width': '300px' });

			if( typeof jQuery( "#is_shipping_based_page" ).val() != "undefined" && jQuery( "#is_shipping_based_page" ).val() != '' ) {
				if ( 'product_categories' == jQuery( "input[type=radio][id=\"orddd_delivery_settings_type\"]:checked" ).val() ) {
					jQuery( '.delivery_type_options' ).slideUp();
					jQuery( '.delivery_type_product_categories' ).slideDown();
					jQuery('#orddd_shipping_methods_for_categories').closest('tr').fadeIn();
					jQuery('#orddd_categories_for_pickup_locations').closest('tr').hide();
				} else if( 'orddd_locations' == jQuery( "input[type=radio][id=\"orddd_delivery_settings_type\"]:checked" ).val() ) { 
					jQuery( '.delivery_type_options' ).slideUp();
					jQuery( '.delivery_type_orddd_locations' ).slideDown();
					jQuery('#orddd_categories_for_pickup_locations').closest('tr').fadeIn();
					jQuery('#orddd_shipping_methods_for_categories').closest('tr').fadeOut();
				} else {
					jQuery( '.delivery_type_options' ).slideDown();
					jQuery( '.delivery_type_product_categories' ).slideUp();
					jQuery( '.delivery_type_orddd_locations' ).slideUp();
					jQuery('#orddd_shipping_methods_for_categories').closest('tr').fadeOut();
					jQuery('#orddd_categories_for_pickup_locations').closest('tr').fadeOut();
				}

				var isChecked = jQuery( "#orddd_enable_shipping_based_delivery_date" ).is( ":checked" );
				var checked   = jQuery( "#weekday_delivery" ).val();
				var isEdit    = jQuery( "#is_edit" ).val()
				if( isChecked == true ) {
				   i = 0;
				   jQuery(".form-table").each( function() {
						if( i == 1 ) {
							k = 0;
							var row = jQuery( this ).find( "tr" );
							jQuery.each( row , function() {
								jQuery( this ).fadeIn();
								k++;
							});
						} else {
							jQuery( this ).fadeIn();
						} 
						i++;
					} ); 
					jQuery( "h2" ).show();
					jQuery( "em" ).show();
					jQuery( "#orddd_individual" ).fadeIn();
					jQuery( "#orddd_bulk" ).fadeIn();
					if( isEdit == "yes" ) {
						if( checked == "checked" ) {
							jQuery( "#orddd_shipping_delivery_type_weekdays" ).attr( "checked", "checked" );
						} else {
							jQuery( "#orddd_shipping_delivery_type_weekdays" ).removeAttr( "checked" );
						}
					} else {
						jQuery( "#orddd_shipping_delivery_type_weekdays" ).attr( "checked", "checked" );
					}
				} else {
					i = 0;
					jQuery(".form-table").each( function() {
						if( i == 0 ) {
							// the field needs to be shown so we do nothing
						} else if( i == 1 ) {
							k = 0;
							var row = jQuery( this ).find( "tr" );
							jQuery.each( row , function() {
								if( k == 1 || k == 0 || k == 2 ) {
									// the field needs to be shown so we do nothing
								} else {
									jQuery( this ).fadeOut();
								}
								
								if( k == 0 && jQuery("input[type=radio][id=\"orddd_delivery_settings_type\"][value=\"orddd_locations\"]").is(":checked") ) {
									jQuery( this ).fadeOut();
								} else if ( k == 1 && jQuery("input[type=radio][id=\"orddd_delivery_settings_type\"][value=\"product_categories\"]").is(":checked") ) {
									jQuery( this ).fadeOut();
								}
								k++;
							});
						} else {
							jQuery( this ).fadeOut();
						}
						i++;
					});
						
					j = 0;
					jQuery( "h2" ).each( function() {
						if( j == 0 || j == 1 || j == 2 ) {
							// the field needs to be shown so we do nothing
						} else {
							jQuery( this ).fadeOut();
						}
						j++;
					} );
					jQuery( "em" ).hide();
					jQuery( "#orddd_individual" ).fadeOut();
					jQuery( "#orddd_bulk" ).fadeOut();
					if( isEdit == "yes" ) {
						if( checked == "checked" ) {
							jQuery( "#orddd_shipping_delivery_type_weekdays" ).attr( "checked", "checked" );
						} else {
							jQuery( "#orddd_shipping_delivery_type_weekdays" ).removeAttr( "checked" );
						}
					} else {
						jQuery( "#orddd_shipping_delivery_type_weekdays" ).removeAttr( "checked" );
					}
				}
			}

			jQuery( "#orddd_shipping_based_holiday_from_date" ).width( "160px" );
			jQuery( "#orddd_shipping_based_holiday_to_date" ).width( "160px" );

			jQuery( '.orddd_shipping_based_time_slot_for_weekdays' ).select2();
			jQuery( '.orddd_shipping_based_select_delivery_dates' ).select2();

			jQuery( "#orddd_shipping_based_holiday_from_date" ).val("").datepicker( {
				constrainInput: true,
				dateFormat: formats[0],
				onSelect: function( selectedDate,inst ) {
					var monthValue = inst.selectedMonth+1;
					var dayValue = inst.selectedDay;
					var yearValue = inst.selectedYear;
					var current_dt = dayValue + "-" + monthValue + "-" + yearValue;
					var to_date = jQuery( "#orddd_shipping_based_holiday_to_date" ).val();
					if ( to_date == "" ) {
						var split = current_dt.split( "-" );
						split[1] = split[1] - 1;
						var minDate = new Date( split[2], split[1], split[0] );
						jQuery( "#orddd_shipping_based_holiday_to_date" ).datepicker( "setDate",minDate );
					}
				},
				firstDay: jQuery("input[name='orddd_holiday_start_day']").val()
			} );
				
			jQuery( "#orddd_shipping_based_holiday_to_date" ).val("").datepicker( {
				constrainInput: true,
				dateFormat: formats[0],
				firstDay: jQuery("input[name='orddd_holiday_start_day']").val()
			} );

			/** */
			$( document ).on( 
				'change', 
				'#orddd_delivery_date_1, #orddd_delivery_date_2, #orddd_delivery_date_3, #orddd_delivery_date', 
				settings.select_specific_dates
			);

			$( document ).on( 
				'change',
				'#orddd_delivery_dates_in_dropdown',
				settings.delivery_dates_in_dropdown
			);

			$( "input[type=radio][id=\"orddd_disable_time_slot_for_delivery_days\"]" ).on( 
				'change', 
				function() {
					if ( jQuery( this ).is(':checked') ) {
						var value = jQuery( this ).val();
						jQuery( '.disable_time_slot_options' ).slideUp();
						jQuery( '.disable_time_slot_for_' + value ).slideDown();
					}
				}
			);

			jQuery( "input[type=radio][id=\"orddd_delivery_checkout_options\"]" ).on( 
				'change',
				settings.select_checkout_option
			);

			jQuery( "input[type=radio][id=orddd_calendar_sync_integration_mode]" ).on(
				'change',
				settings.calendar_integration_mode
			);

			$( '#orddd_delivery_from_hours' ).on( 
				'select change',
				settings.select_time_range
			);

			/** Change preview datepicker when First day of week setting is changed */
			jQuery( '#start_of_week' ).on(
				'change', 
				function() {
					jQuery("#datepicker").datepicker("option", "firstDay", jQuery(this).val());
				}
			);

			jQuery( "#localisation_select" ).on( 
				'change', 
				function() {
					jQuery( "#datepicker" ).datepicker( "option", jQuery.datepicker.regional[ jQuery( this ).val() ] );
			});

			jQuery( "#orddd_show_column_on_orders_page_check" ).on( 
				'change',
				settings.enable_column_on_orders_page
			);

			jQuery( "#orddd_allow_customers_to_edit_date" ).on( 
				'change',
				settings.enable_edit_date_setting
			);

			$( '.clone_setting' ).on( 
				'click',
				settings.clone_custom_settings
			);

			$( document ).on( 
				'click', 
				'.orddd-info_trigger', 
				settings.toggle_target 
			);

			jQuery( document ).on( 
				'click', 
				'#test_connection', 
				function( e ) {
					e.preventDefault();    
					var data = {
							gcal_api_test_result: '',
							gcal_api_pre_test: '',
							gcal_api_test: 1,
							action: 'display_nag'
						};
					jQuery( '#test_connection_ajax_loader' ).show();
					jQuery.post( localizeStrings.ajax_url, data, function( response ) {
						jQuery( '#test_connection_message' ).html( response );
						jQuery( '#test_connection_ajax_loader' ).hide();
					});
				}
			);

			jQuery( document ).on( 
				'click', 
				'.orddd_ics_feed-info_trigger', 
				settings.orddd_ics_feed_toggle_target 
			);

			jQuery( '#add_new_ics_feed' ).on( 
				'click', 
				settings.add_new_ics_feed
			);

			jQuery( document ).on( 
				'click', 
				'#save_ics_url',
				settings.save_ics_url
			);
		
			jQuery( document ).on( 
				'click', 
				'input[type=\'button\'][name=\'delete_ics_feed\']',
				settings.delete_ics_feed
			);
		
			jQuery( document ).on( 
				'click', 
				'input[type=\'button\'][name=\'import_ics\']',
				settings.import_ics
			);

			jQuery( "#orddd_enable_shipping_based_delivery_date" ).on(
				'change',
				settings.enable_shipping_based_delivery
			);

			jQuery( "table#orddd_holidays_list" ).on( 
				"click", 
				"a.confirmation_holidays",
				settings.delete_holidays
			);

			jQuery( "#save_holidays" ).on( 
				'click',
				settings.save_holidays
			);

			jQuery( "input[type=radio][id=\"orddd_delivery_checkout_options\"]" ).on( 
				'change',
				settings.custom_checkout_options
			);
	
			jQuery("#orddd_shipping_delivery_type_weekdays").on(
				'change', 
				function() {
					var isChecked = jQuery("#orddd_shipping_delivery_type_weekdays").is(":checked");
					if( isChecked == true ) {
						jQuery("#weekdays_fieldset").removeAttr("disabled");  
						jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days\"][value=\"weekdays\"]" ).removeAttr("disabled"); 
						jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days_bulk\"][value=\"weekdays\"]" ).removeAttr("disabled"); 
					} else {
						jQuery("#weekdays_fieldset").attr("disabled","disabled");
						jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days\"][value=\"weekdays\"]" ).attr( "disabled", "disabled" );         
						jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days_bulk\"][value=\"weekdays\"]" ).attr( "disabled", "disabled" );     
					}
				}
			);

			jQuery("#orddd_weekdays_table :checkbox").on( 
				"change",
				settings.select_weekdays_custom
			);

			jQuery( "#orddd_shipping_delivery_type_specific_days" ).on( 
				'change', 
				function() {
					var isChecked = jQuery( "#orddd_shipping_delivery_type_specific_days" ).is( ":checked" );
					if( isChecked  == true ) {
						jQuery( "#orddd_delivery_date" ).removeAttr( "disabled" );  
						jQuery( "#save_specific_date" ).removeAttr( "disabled" );
						jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days\"][value=\"specific_dates\"]" ).attr( "disabled", "disabled" );              
						jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days_bulk\"][value=\"specific_dates\"]" ).attr( "disabled", "disabled" );              
					} else {
						jQuery( "#orddd_delivery_date" ).attr( "disabled", "disabled" );  
						jQuery( "#save_specific_date" ).attr( "disabled", "disabled" );  
						jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days\"][value=\"specific_dates\"]" ).attr( "disabled", "disabled" );              
						jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days_bulk\"][value=\"specific_dates\"]" ).attr( "disabled", "disabled" );              
					}
				}
			);

			jQuery( "table#orddd_specific_date_list" ).on( 
				"click", 
				"a.confirmation_specific_date",
				settings.delete_specific_dates
			);

			jQuery( "#save_specific_date" ).on( 
				'click',
				settings.save_specific_date
			)
		},

		/** Specific dates settings */
		select_specific_dates: function() {
			var ordd_id    = $(this).attr( "id" );
			var ordd_value = this.value.length; 
	
			if ( "orddd_delivery_date_1" == ordd_id && ordd_value === 0 ) {
				$( "#additional_charges_1" ).prop( "disabled", true );
				$( "#specific_charges_label_1" ).prop( "disabled", true );
			} else if ( "orddd_delivery_date_2" == ordd_id && ordd_value === 0 ) {
				$( "#additional_charges_2" ).prop( "disabled", true );
				$( "#specific_charges_label_2" ).prop( "disabled", true );
			} else if ( "orddd_delivery_date_3" == ordd_id && ordd_value === 0 ) {
				$( "#additional_charges_3" ).prop( "disabled", true );
				$( "#specific_charges_label_3" ).prop( "disabled", true );
			} else if( "orddd_delivery_date" == ordd_id && ordd_value === 0 ) {
				$( "#additional_charges" ).prop( "disabled", true );
				$( "#specific_charges_label" ).prop( "disabled", true );
			} else if ( "orddd_delivery_date_1" == ordd_id && ordd_value > 0 ) {
				$( "#additional_charges_1" ).prop( "disabled", false );
				$( "#specific_charges_label_1" ).prop( "disabled", false );
			} else if ( "orddd_delivery_date_2" == ordd_id && ordd_value > 0 ) {
				$( "#additional_charges_2" ).prop( "disabled", false );
				$( "#specific_charges_label_2" ).prop( "disabled", false );
			} else if( "orddd_delivery_date_3" == ordd_id && ordd_value > 0 ) {
				$( "#additional_charges_3" ).prop( "disabled", false );
				$( "#specific_charges_label_3" ).prop( "disabled", false );
			} else if( "orddd_delivery_date" == ordd_id && ordd_value > 0 ) {
				$( "#additional_charges" ).prop( "disabled", false );
				$( "#specific_charges_label" ).prop( "disabled", false );
			}
		},

		/** Delivery Dates in dropdown setting */
		delivery_dates_in_dropdown: function() {
			if( 'yes' === $(this).val() ) {
				$('#start_of_week').closest('tr').hide();
				$('#orddd_number_of_months').closest('tr').hide();
				$('#switcher').closest('tr').hide();
				$('#orddd_calendar_display_mode').closest('tr').hide();
			} else {
				$('#start_of_week').closest('tr').show();
				$('#orddd_number_of_months').closest('tr').show();
				$('#switcher').closest('tr').show();
				$('#orddd_calendar_display_mode').closest('tr').show();
			}
		},

		select_checkout_option: function() {
			if ( jQuery( this ).is(':checked') ) {
				var value = jQuery( this ).val();
				if( value == 'delivery_calendar' ) {
					i = 0;
					jQuery( ".form-table"  ).each( function() {
						if( i == 0 ) {
							k = 0;
							var row = jQuery( this ).find( "tr" );
							if( row.length == 10 ) {
								jQuery.each( row , function() {
									if( k == 9 ) {
										jQuery( this ).fadeOut();
									} else {
										jQuery( this ).fadeIn();    
									}
									k++ 
								});
							} else {
								jQuery.each( row , function() {
									if( k == 11 ) {
										jQuery( this ).fadeOut();
									} else {
										jQuery( this ).fadeIn();    
									}
									k++;
								});
							}
						} else {
							jQuery( this ).fadeIn();
						} 
						i++;
					}); 
				} else if( value == 'text_block' ) {
					i = 0;
					jQuery(".form-table").each( function() {
						if( i == 0 ) {
							k = 0;
							var row = jQuery( this ).find( "tr" );
							if( row.length == 10 ) {
								jQuery.each( row , function() {
	
									if( k == 1 || k == 0 || k == 4 ) {
										// the field needs to be shown so we do nothing
									} else if( k == 9 ) {
										jQuery( this ).fadeIn();    
									} else {
										jQuery( this ).fadeOut();
									}
									k++ 
								});
							} else {
								jQuery.each( row , function() {
									if( k == 1 || k == 0 || k == 4 || k == 7 || k == 9 || k == 10 ) {
										// the field needs to be shown so we do nothing
									} else if( k == 11 ) {
										jQuery( this ).fadeIn();    
									} else {
										jQuery( this ).fadeOut();
									}
									k++ 
								});
							}
						}
						i++;
					});
				}
			}
		},

		select_time_range: function() {
			from_value = $( '#orddd_delivery_from_hours' ).val();
			to_value = $( '#orddd_delivery_to_hours' ).val();
			for( i = from_value - 1; i >= 0; i-- ) {
				if( i != 0 ) {
					$( '#orddd_delivery_to_hours option[value="'+i+'"]' ).attr( 'disabled', true );		
				}
				$( '#orddd_delivery_to_hours' ).val( from_value );
			}
	
			for( j = from_value ; j < 24 ; j++ ) {
				$( '#orddd_delivery_to_hours option[value="'+j+'"]' ).attr( 'disabled', false );		
			}
		},

		enable_column_on_orders_page: function() {
			if ( jQuery( this ).is(':checked') ) {
				jQuery( '#orddd_enable_default_sorting_of_column' ).fadeIn();
				jQuery( 'label[ for=\"orddd_enable_default_sorting_of_column\" ]' ).fadeIn();
			} else {
				jQuery( '#orddd_enable_default_sorting_of_column' ).fadeOut();
				jQuery( 'label[ for=\"orddd_enable_default_sorting_of_column\" ]' ).fadeOut();
			}
		},

		enable_edit_date_setting: function() {
			if ( jQuery( this ).is(':checked') ) {
				jQuery( '#orddd_send_email_to_admin_when_date_updated' ).fadeIn();
				jQuery( 'label[ for=\"orddd_send_email_to_admin_when_date_updated\" ]' ).fadeIn();
	
				jQuery( '#orddd_disable_edit_after_cutoff' ).fadeIn();
				jQuery( 'label[ for=\"orddd_disable_edit_after_cutoff\" ]' ).fadeIn();
			} else {
				jQuery( '#orddd_send_email_to_admin_when_date_updated' ).fadeOut();
				jQuery( 'label[ for=\"orddd_send_email_to_admin_when_date_updated\" ]' ).fadeOut();
	
				jQuery( '#orddd_disable_edit_after_cutoff' ).fadeOut();
				jQuery( 'label[ for=\"orddd_disable_edit_after_cutoff\" ]' ).fadeOut();
			}
		},

		calendar_integration_mode: function() {
			var isChecked = jQuery( this ).val();
			if( isChecked == "directly" ) {
				i = 0;
				jQuery( ".form-table" ).each( function() {
					if( i == 2 ) {
						k = 0;
						var row = jQuery( this ).find( "tr" );
						jQuery.each( row , function() {
							if( k == 7 ) {
								jQuery( this ).fadeOut();
							} else {
								jQuery( this ).fadeIn();
							}
							k++;
						});
					} else {
						jQuery( this ).fadeIn();
					}
					i++;
				} );
			} else if( isChecked == "manually" ) {
				i = 0;
				jQuery( ".form-table" ).each( function() {
					if( i == 2 ) {
						k = 0;
						var row = jQuery( this ).find( "tr" );
						jQuery.each( row , function() {
							if( k != 7 && k != 0 ) {
								jQuery( this ).fadeOut();
							} else {
								jQuery( this ).fadeIn();
							}
							k++;
						});
					} else {
						jQuery( this ).fadeIn();
					}
					i++;
				});
			} else if( isChecked == "disabled" ) {
				i = 0;
				jQuery( ".form-table" ).each( function() {
					if( i == 2 ) {
						k = 0;
						var row = jQuery( this ).find( "tr" );
						jQuery.each( row , function() {
							if( k != 0 ) {
								jQuery( this ).fadeOut();
							} else {
								jQuery( this ).fadeIn();
							}
							k++;
						});
					} else {
						jQuery( this ).fadeIn();
					}
					i++;
				});
			}
		},

		clone_custom_settings: function( e ) {
			e.preventDefault();
			var setting_id = $(this).data('id');
			var data = {
				setting_id : setting_id,
				action: 'orddd_clone_custom_settings'
			}
	
			jQuery.post( localizeStrings.ajax_url, data, function( response ) {
				if( 'success' == response ) {
					location.reload();
				}
			});
		},

		toggle_target: function( e ) {
			if ( e && e.preventDefault ) { 
				e.preventDefault();
			}
			if ( e && e.stopPropagation ) {
				e.stopPropagation();
			}
			var target = jQuery(".orddd-info_target.api-instructions" );
			if ( !target.length ) {
				return false;
			}
		
			if ( target.is( ":visible" ) ) {
				target.hide( "fast" );
			} else {
				target.show( "fast" );
			}
		
			return false;
		},

		orddd_ics_feed_toggle_target: function( e ) {
			if ( e && e.preventDefault ) { 
				e.preventDefault();
			}
			if ( e && e.stopPropagation ) {
				e.stopPropagation();
			}
			var target = jQuery( ".orddd_ics_feed-info_target.api-instructions" );
			if ( !target.length ) {
				return false;
			}
		
			if ( target.is( ":visible" ) ) {
				target.hide( "fast" );
			} else {
				target.show( "fast" );
			}
		
			return false;
		},

		add_new_ics_feed: function() {
			var rowCount = jQuery( '#orddd_ics_url_list tr' ).length;
			jQuery( '#orddd_ics_url_list' ).append( '<tr id=\'' + rowCount + '\'><td class=\'ics_feed_url\'><input type=\'text\' id=\'orddd_ics_fee_url_' + rowCount + '\' size=\'60\' ></td><td class=\'ics_feed_url\'><input type=\'button\' value=\'Save\' id=\'save_ics_url\' class=\'save_button\' name=\'' + rowCount + '\'></td><tdclass=\'ics_feed_url\'><input  type=\'button\' class=\'save_button\' id=\'' + rowCount + '\'name=\'import_ics\' value=\'Import Events\' disabled=\'disabled\'></td><td class=\'ics_feed_url\'><input type=\'button\' class=\'save_button\' id=\'' + rowCount + '\' value=\'Delete\' disabled=\'disabled\' name=\'delete_ics_feed\' ></td><td class=\'ics_feed_url\'><div id=\'import_event_message\'><img src=\'' +localizeStrings.plugins_url + '/order-delivery-date/images/ajax-loader.gif\'></div><div id=\'success_message\' ></div></td></tr>' );
		},

		save_ics_url: function() {
			var key = jQuery( this ).attr( 'name' );
			var data = {
				ics_url: jQuery( '#orddd_ics_fee_url_' + key ).val(),
				action: 'save_ics_url_feed'
			};
			jQuery.post( localizeStrings.ajax_url, data, function( response ) {
				if( response == 'yes' ) {
					jQuery( 'input[name=\'' + key + '\']' ).attr( 'disabled','disabled' );
					jQuery( 'input[id=\'' + key + '\']' ).removeAttr( 'disabled' );
				} 
			});
		},

		delete_ics_feed: function() {
			var key = jQuery( this ).attr( 'id' );
			var data = {
				ics_feed_key: key,
				action: 'delete_ics_url_feed'
			};
			jQuery.post( localizeStrings.ajax_url, data, function( response ) {
				if( response == 'yes' ) {
					jQuery( 'table#orddd_ics_url_list tr#' + key ).remove();
				} 
			});
		},

		import_ics: function() {
			jQuery( '#import_event_message' ).show();
			var key = jQuery( this ).attr( 'id' );
			var data = {
				ics_feed_key: key,
				action: 'orddd_setup_import_events'
			};
			jQuery.post( localizeStrings.ajax_url, data, function( response ) {
				jQuery( '#import_event_message' ).hide();
				jQuery( '#success_message' ).html( response );  
				jQuery( '#success_message' ).fadeIn();
				setTimeout( function() {
					jQuery( '#success_message' ).fadeOut();
				},3000 );
			});
		},

		enable_shipping_based_delivery: function() {
			var isChecked = jQuery( "#orddd_enable_shipping_based_delivery_date" ).is( ":checked" );
			var isEdit = jQuery( "#is_edit" ).val();
			var checked = jQuery( "#weekday_delivery" ).val();

			if( isChecked == true ) {
				i = 0;
				jQuery(".form-table").each( function() {
					if( i == 1 ) {
						k = 0;
						var row = jQuery( this ).find( "tr" );
						jQuery.each( row , function() {
							jQuery( this ).fadeIn();
							
							if( k == 0 && jQuery("input[type=radio][id=\"orddd_delivery_settings_type\"][value=\"orddd_locations\"]").is(":checked") ) {
								jQuery( this ).fadeOut();
							} else if ( k == 1 && jQuery("input[type=radio][id=\"orddd_delivery_settings_type\"][value=\"product_categories\"]").is(":checked") ) {
								jQuery( this ).fadeOut();
							} else if( ( k ==0 || k == 1 ) && jQuery("input[type=radio][id=\"orddd_delivery_settings_type\"][value=\"shipping_methods\"]").is(":checked") ) {
								jQuery( this ).fadeOut();
							}
						   k++;
						});
					} else {
						jQuery( this ).fadeIn();
					} 
					i++;
				} ); 
				jQuery( "h2" ).show();
				jQuery( "em" ).show();
				jQuery( "#orddd_individual" ).show();
				jQuery( "#orddd_bulk" ).show();
				if( isEdit == "yes" ) {
					if( checked == "checked" ) {
						jQuery( "#orddd_shipping_delivery_type_weekdays" ).attr( "checked", "checked" );
					} else {
						jQuery( "#orddd_shipping_delivery_type_weekdays" ).removeAttr( "checked" );
					}
				} else {
					jQuery( "#orddd_shipping_delivery_type_weekdays" ).attr( "checked", "checked" );
				}
			} else {
			   i = 0;
				jQuery(".form-table").each( function() {
					if( i == 0 ) {
						// the field needs to be shown so we do nothing
					} else if( i == 1 ) {
						k = 0;
						var row = jQuery( this ).find( "tr" );
						jQuery.each( row , function() {
							if( k == 0 && jQuery("input[type=radio][id=\"orddd_delivery_settings_type\"][value=\"orddd_locations\"]").is(":checked") ) {
								jQuery( this ).fadeOut();
							} else if ( k == 1 && jQuery("input[type=radio][id=\"orddd_delivery_settings_type\"][value=\"product_categories\"]").is(":checked") ) {
								jQuery( this ).fadeOut();
							} else if( k == 2 ) {
							   // the field needs to be shown so we do nothing
						   } else {
								jQuery( this ).fadeOut();
						   }
						   k++ 
						});
					} else {
						jQuery( this ).fadeOut();
					}
					i++;
				});
				
				j = 0;
				jQuery( "h2" ).each( function() {
					if( j == 0 || j == 1 || j == 2 ) {
					} else {
						jQuery( this ).fadeOut();
					}
					j++;
				});
				jQuery( "em" ).hide();
				jQuery( "#orddd_individual" ).hide();
				jQuery( "#orddd_bulk" ).hide();
				if( isEdit == "yes" ) {
					if( checked == "checked" ) {
						jQuery( "#orddd_shipping_delivery_type_weekdays" ).attr( "checked", "checked" );
					} else {
						jQuery( "#orddd_shipping_delivery_type_weekdays" ).removeAttr( "checked" );
					}
				} else {
					jQuery( "#orddd_shipping_delivery_type_weekdays" ).removeAttr( "checked" );
				}
			}
		},

		delete_holidays: function() {
			var holidays_hidden = jQuery( "#orddd_holiday_hidden" ).val();
            var holiday_name = jQuery( "table#orddd_holidays_list tr#"+ this.id + " td#orddd_holiday_name" ).html();
            var holiday_date = jQuery( "table#orddd_holidays_list tr#"+ this.id + " td#orddd_holiday_date" ).html();
            var recurring_type_text = jQuery( "table#orddd_holidays_list tr#"+ this.id + " td#orddd_allow_recurring_type" ).html();
            if( recurring_type_text == localizeStrings.holidayrecurringText ) {
            	var recurring_type = 'on';
            } else {
            	var recurring_type = '';
            }
            var split_date = holiday_date.split( "-" );            
            var dt = new Date ( split_date[ 0 ] + "/" + split_date[ 1 ] + "/" + split_date[ 2 ] );
            var date = ( dt.getMonth() + 1 ) + "-" + dt.getDate() + "-" + dt.getFullYear();    
            var substring = "{" + holiday_name + ":" + date + ":" + recurring_type + "},";
            var updatedString = holidays_hidden.replace( substring, "" );
            jQuery( "#orddd_holiday_hidden" ).val( updatedString );
            jQuery( "table#orddd_holidays_list tr#"+ this.id ).remove();
		},

		save_holidays: function() {
			var holidays_row_arr = [];
			var holidays = [];
			
			var row = jQuery( "#orddd_holiday_hidden" ).val();
			if( row != "" ) {
				holidays_row_arr = row.split(",");
				for( i = 0; i < holidays_row_arr.length; i++ ) {
					if( holidays_row_arr[ i ] != "" ) {
						var string = holidays_row_arr[ i ].replace( "{", "" );
						string = string.replace( "}", "" );
						var string_arr = string.split( ":" );
						holidays.push( string_arr[ 1 ] );
					}
				}
			}
					
			var split_from_date = jQuery( "#orddd_shipping_based_holiday_from_date" ).val().split( "-" );
			split_from_date[0] = split_from_date[0] - 1;
			var from_date = new Date( split_from_date[2], split_from_date[0], split_from_date[1] );
			
			var split_to_date = jQuery( "#orddd_shipping_based_holiday_to_date" ).val().split( "-" );
			split_to_date[0] = split_to_date[0] - 1;
			var to_date = new Date( split_to_date[2], split_to_date[0], split_to_date[1] );
					
			var timediff = ( ( to_date.getTime() - from_date.getTime() ) / ( 1000 * 60 * 60 * 24 ) ) + 1;
			var date = jQuery( "#orddd_shipping_based_holiday_from_date" ).val();
			for ( i = 1; i <= timediff; i++ ) {
				if( from_date <= to_date ) {
					hidden_date = ( from_date.getMonth() + 1 ) + "-" + from_date.getDate() + "-" + from_date.getFullYear();
					if( jQuery.inArray( hidden_date, holidays ) == -1 ) {  
						var rowCount = jQuery( "#orddd_holidays_list tr" ).length;
						if( rowCount == 0 ) {
							jQuery( "#orddd_holidays_list" ).append( "<tr class=\"orddd_common_list_tr\"><th class=\"orddd_holidays_list\"> " + localizeStrings.holidaynameText + "</th><th class=\"orddd_holidays_list\">" + localizeStrings.holidaydateText + "</th><th class=\"orddd_holidays_list\">" + localizeStrings.holidaytypeText + "</th><th class=\"orddd_holidays_list\">" + localizeStrings.holidayactionText + "</th></tr>" );
							var rowCount = 1;
						}

						rowCount = rowCount - 1;
						if( from_date.getDate() < 10 ){ 
							dd = "0" + from_date.getDate();
						} else {
							dd = from_date.getDate();
						}

						if( ( from_date.getMonth() + 1 ) < 10 ){ 
							mm = "0" + ( from_date.getMonth() + 1 );
						} else {
							mm = ( from_date.getMonth() + 1 );
						}

						date =  mm + "-" + dd + "-" + from_date.getFullYear();

						var recurring_type_text = localizeStrings.holidaycurrentText;
						var recurring_type = '';
						var isChecked = jQuery( "#orddd_shipping_based_allow_recurring_holiday" ).is( ":checked" );
						
						if( isChecked == true ) {
							recurring_type_text = localizeStrings.holidayrecurringText;
							recurring_type = 'on';
						}

						jQuery( "#orddd_holidays_list tr:last" ).after( "<tr class=\"orddd_common_list_tr\" id=\"orddd_delete_holidays_" + rowCount + "\"><td class=\"orddd_holidays_list\" id=\"orddd_holiday_name\">" + jQuery("#orddd_shipping_based_holiday_name").val() + "</td><td class=\"orddd_holidays_list\" id=\"orddd_holiday_date\">" + date +"</td><td class=\"orddd_holidays_list\" id=\"orddd_allow_recurring_type\">" + recurring_type_text +"</td><td class=\"orddd_holidays_list\"><a href=\"javascript:void(0)\" class=\"confirmation_holidays\" id=\"orddd_delete_holidays_" + rowCount + "\">" + localizeStrings.holidaydeleteText + "</a></td></tr>" );

						row += "{" + jQuery( "#orddd_shipping_based_holiday_name" ).val() + ":" + hidden_date + ":" + recurring_type + "},";
					}

					from_date.setDate( from_date.getDate() + 1 );
				}
			}

			jQuery( "#orddd_holiday_hidden" ).val( row );
			jQuery( "#orddd_shipping_based_holiday_from_date" ).datepicker( "setDate", "" );
			jQuery( "#orddd_shipping_based_holiday_to_date" ).datepicker( "setDate", "" );
			jQuery( "#orddd_shipping_based_holiday_name" ).val( "" );
			jQuery( "#orddd_shipping_based_allow_recurring_holiday" ).prop( "checked", false );
		},

		custom_checkout_options: function() {
			if ( typeof jQuery( "#is_shipping_based_page" ).val() != "undefined" && jQuery( "#is_shipping_based_page" ).val() != '' && jQuery( this ).is(':checked') ) {
				var value = jQuery( this ).val();
				var isChecked = jQuery( "#orddd_enable_shipping_based_delivery_date" ).is( ":checked" );

				if( value == 'delivery_calendar' ) {
					var i = 0;
					jQuery( ".form-table"  ).each( function() {
						if( i == 0 ) {
						} else if( i == 1 ) {
							var k = 0;
							var row = jQuery( this ).find( "tr" );
							jQuery.each( row , function() {
								if( k == ( row.length - 1 ) ) {
									jQuery( this ).fadeOut();
								} else {
									if( isChecked == true ) {
										jQuery( this ).fadeIn();    
									}	

									if( k == 0 && jQuery('input[type=radio][id="orddd_delivery_settings_type"][value="orddd_locations"]').is(':checked') ) {
										jQuery( this ).fadeOut();
									} else if ( k == 1 && jQuery('input[type=radio][id="orddd_delivery_settings_type"][value="product_categories"]').is(':checked') ) {
										jQuery( this ).fadeOut();
									} else if ( ( k == 0 || k == 1 ) && jQuery('input[type=radio][id="orddd_delivery_settings_type"][value="shipping_methods"]').is(':checked') ) {
										jQuery( this ).fadeOut();
									}
								}

								k++;
							});
						} else {
							if( isChecked == true ) {
								jQuery( this ).fadeIn();
							}
						} 
						i++;
					}); 
					var j = 0;
					jQuery( "h2" ).each( function() {
						if( isChecked == true ) {
							jQuery( this ).fadeIn();
						}
						j++;
					});
					jQuery( ".form-table"  ).show();
					jQuery( ".form-table"  ).find('tr').show();
					jQuery( "#content form h2"  ).show();
					jQuery( '#orddd_individual' ).show();
					jQuery( '#orddd_bulk' ).show();
				} else if( value == 'text_block' ) {
					var i = 0;
					jQuery( ".form-table" ).each( function() {
						if( i == 0 ) {
						} else if( i == 1 ) {
							var k = 0;
							var row = jQuery( this ).find( "tr" );
							jQuery.each( row , function() {
								if( jQuery('input[type=radio][id="orddd_delivery_settings_type"][value="orddd_locations"]').is(':checked') && k == 1 ) {
									jQuery( this ).fadeIn();
								} else if ( jQuery('input[type=radio][id="orddd_delivery_settings_type"][value="product_categories"]').is(':checked') && k == 0 ) {
									jQuery( this ).fadeIn();
								} else if( k == 2 || k == 3 || k == ( row.length - 1 ) || k == ( row.length - 6 ) ) {
									if( isChecked == true ) {
										jQuery( this ).fadeIn();    
									}
									// the field needs to be shown so we do nothing
								} else {
									jQuery( this ).fadeOut();
								}
								k++;
							});
						} else {
							jQuery( this ).fadeOut();
						}
						i++;
					});

					var j = 0;  
					jQuery( "h2" ).each( function() {
						if( j == 0 || j == 1 || j == 2 ) {
						} else {
							jQuery( this ).fadeOut();
						}
						j++;
					});
					jQuery( '#orddd_individual' ).hide();
					jQuery( '#orddd_bulk' ).hide();
				}
			}
		},

		select_weekdays_custom: function() {
			var isChecked = jQuery( "#" + this.id ).is(":checked");
			if( isChecked == false ) {
				jQuery( "#orddd_shipping_based_time_slot_for_weekdays option[value=" + this.id + "]").remove();    
				jQuery( "#orddd_shipping_based_time_slot_for_weekdays_bulk option[value=" + this.id + "]").remove();    
			} else {
				var rowCount = jQuery( "#orddd_shipping_based_time_slot_for_weekdays option[value=" + this.id + "]" ).length;
				if( rowCount == 0 ) {
					for ( var i = 0; i <= 6; i++ ) {
						if( this.id == "orddd_weekday_" + i + "_custom_setting" ) {
							if ( 6 == i ) {
								jQuery( "#orddd_shipping_based_time_slot_for_weekdays option" ).eq( i ).after( jQuery("<option></option>").val( this.id ).html( orddd_weekdays[ 'orddd_weekday_' + i ] ) );
								
								jQuery( "#orddd_shipping_based_time_slot_for_weekdays_bulk option" ).eq( i ).after( jQuery("<option></option>").val( this.id ).html( orddd_weekdays[ 'orddd_weekday_' + i ] ) );
							} else {
								jQuery( "#orddd_shipping_based_time_slot_for_weekdays option" ).eq( i + 1 ).before( jQuery("<option></option>").val( this.id ).html( orddd_weekdays[ 'orddd_weekday_' + i ] ) ); 
								
								jQuery( "#orddd_shipping_based_time_slot_for_weekdays_bulk option" ).eq( i + 1 ).before( jQuery("<option></option>").val( this.id ).html( orddd_weekdays[ 'orddd_weekday_' + i ] ) );
							}
						}
					}
				}
			}
		},

		delete_specific_dates: function() {
			var specific_dates_hidden = jQuery( "#orddd_specific_date_hidden" ).val();
			var split_array = specific_dates_hidden.split( "," );
			var specific_date = jQuery( "table#orddd_specific_date_list tr#" + this.id + " td#orddd_specific_date" ).html();
			var split_date = specific_date.split( "-" );
			var dt = new Date ( split_date[ 0 ] + "/" + split_date[ 1 ] + "/" + split_date[ 2 ] );
			var date = ( dt.getMonth() + 1 ) + "-" + dt.getDate() + "-" + dt.getFullYear();
			var updatedString = "";
			for( i=0; i < ( split_array.length - 1 ); i++ ) {
				if( split_array[i].indexOf( date ) == -1 ) {
					updatedString = updatedString + split_array[i] + ",";
				}
			}
			jQuery( "#orddd_specific_date_hidden" ).val( updatedString );
			jQuery( "table#orddd_specific_date_list tr#"+ this.id ).remove();
			var rowCount = jQuery( "#orddd_specific_date_list tr" ).length;
			if( rowCount == 1 ) {
				jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days\"][value=\"specific_dates\"]" ).attr( "disabled", "disabled" );              
				jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days_bulk\"][value=\"specific_dates\"]" ).attr( "disabled", "disabled" );              
			} else {
				jQuery( "#orddd_shipping_based_select_delivery_dates option[value=" + date + "]").remove(); 
				jQuery( "#orddd_shipping_based_select_delivery_dates_bulk option[value=" + date + "]").remove(); 
			}
		},

		save_specific_date: function() {
			var row_arr = [];
			var specific_dates = [];
			if( jQuery( "#orddd_delivery_date" ).val() != "" ) {
				var split = jQuery( "#orddd_delivery_date" ).val().split( "-" );
				split[0] = split[0] - 1;
				var dt = new Date( split[2], split[0], split[1] );
				var date = ( dt.getMonth() + 1 ) + "-" + dt.getDate() + "-" + dt.getFullYear();
				var row = jQuery( "#orddd_specific_date_hidden" ).val();
				if( row != "" ) {
					row_arr = row.split(",");
					for( i = 0; i < row_arr.length; i++ ) {
						if( row_arr[ i ] != "" ) {
							var string = row_arr[ i ].replace( "{", "" );
							string = string.replace( "}", "" );
							var string_arr = string.split( ":" );
							specific_dates.push( string_arr[ 0 ] );
						}
					}
				}
				if( jQuery.inArray( date, specific_dates ) == -1 ) {
					var rowCount = jQuery( "#orddd_specific_date_list tr" ).length;
					if( rowCount == 0 ) {
						jQuery( "#orddd_specific_date_list" ).append( "<tr class=\"orddd_common_list_tr\"><th class=\"orddd_specific_date_list\">"+ localizeStrings.specificDateName + "</th><th class=\"orddd_specific_date_list\">" + localizeStrings.specificChargesText + "</th><th class=\"orddd_specific_date_list\">" + localizeStrings.specificChargesLabel + "</th><th class=\"orddd_specific_date_list\">" + localizeStrings.holidayactionText + "</th></tr>" );
						jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days\"][value=\"specific_dates\"]" ).attr( "disabled", "disabled" );              
						jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days_bulk\"][value=\"specific_dates\"]" ).attr( "disabled", "disabled" );              
						var rowCount = 1;
					}
					if( rowCount >= 1 ) {
						jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days\"][value=\"specific_dates\"]" ).removeAttr( "disabled" );
						jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days_bulk\"][value=\"specific_dates\"]" ).removeAttr( "disabled" );
					}
					rowCount = rowCount - 1;
					if( jQuery("#additional_charges").val() != "" ) {
						jQuery( "#orddd_specific_date_list tr:last" ).after( "<tr class=\"orddd_common_list_tr\" id=\"orddd_delete_specific_dates_" + rowCount + "\"><td class=\"orddd_specific_date_list\" id=\"orddd_specific_date\">" + jQuery( "#orddd_delivery_date" ).val() + "</td><td class=\"orddd_specific_date_list\" id=\"orddd_additional_charges\">" + localizeStrings.currency + jQuery("#additional_charges").val() + "</td><td class=\"orddd_specific_date_list\" id=\"orddd_specific_charges_label\">" + jQuery("#specific_charges_label").val() + "</td><td class=\"orddd_specific_date_list\" id=\"orddd_max_orders_specific\">" + jQuery( "#orddd_max_orders_specific" ).val() + "</td><td class=\"orddd_specific_date_list\"><a href=\"javascript:void(0)\" class=\"confirmation_specific_date\" id=\"orddd_delete_specific_dates_" + rowCount + "\">" + localizeStrings.holidaydeleteText + "</a></td></tr>" );
					} else {
						jQuery( "#orddd_specific_date_list tr:last" ).after( "<tr class=\"orddd_common_list_tr\" id=\"orddd_delete_specific_dates_" + rowCount + "\"><td class=\"orddd_specific_date_list\" id=\"orddd_specific_date\">" + jQuery( "#orddd_delivery_date" ).val() + "</td><td class=\"orddd_specific_date_list\" id=\"orddd_additional_charges\"></td><td class=\"orddd_specific_date_list\" id=\"orddd_specific_charges_label\">" + jQuery("#specific_charges_label").val() + "</td><td class=\"orddd_specific_date_list\" id=\"orddd_max_orders_specific\">"+jQuery( "#orddd_max_orders_specific" ).val()+"</td><td class=\"orddd_specific_date_list\"><a href=\"javascript:void(0)\" class=\"confirmation_specific_date\" id=\"orddd_delete_specific_dates_" + rowCount + "\">" + localizeStrings.holidaydeleteText + "</a></td></tr>" );
					}
				
					row += "{" + date + ":" + jQuery( "#additional_charges" ).val() + ":" + jQuery( "#specific_charges_label" ).val() + ":" + jQuery( "#orddd_max_orders_specific" ).val() + "},";
					jQuery( "#orddd_specific_date_hidden" ).val( row );
					jQuery( "#orddd_shipping_based_select_delivery_dates" ).append( jQuery("<option></option>").val(date).html( jQuery( "#orddd_delivery_date" ).val() ) );
					jQuery( "#orddd_shipping_based_select_delivery_dates_bulk" ).append( jQuery("<option></option>").val(date).html( jQuery( "#orddd_delivery_date" ).val() ) );
				}
				jQuery( "#orddd_delivery_date" ).datepicker( "setDate", "" );
				jQuery( "#additional_charges" ).val( "" );
				jQuery( "#specific_charges_label" ).val( "" );
				jQuery( "#orddd_max_orders_specific" ).val("");
			}
		}
	}
	settings.init();

	if( typeof jQuery( "#is_shipping_based_page" ).val() != "undefined" && jQuery( "#is_shipping_based_page" ).val() != '' ) {
	    jQuery( '.orddd_shipping_methods' ).select2();
	    jQuery( '.orddd_shipping_methods' ).css({'width': '300px' });
	    jQuery( "input[type=radio][id=\"orddd_delivery_settings_type\"]" ).on( 'change', function() {
			if ( jQuery( this ).is(':checked') ) {
				var value = jQuery( this ).val();
				jQuery( '.delivery_type_options' ).slideUp();
				jQuery( '.delivery_type_' + value ).slideDown();
	            if( value == 'product_categories' ) {
					jQuery('#orddd_shipping_methods_for_categories').closest('tr').fadeIn();
					jQuery('#orddd_categories_for_pickup_locations').closest('tr').fadeOut();
	            } else if( value == 'orddd_locations' ) {
					jQuery('#orddd_shipping_methods_for_categories').closest('tr').fadeOut();
					jQuery('#orddd_categories_for_pickup_locations').closest('tr').fadeIn();
				} else {
	                i = 0;
					jQuery('#orddd_shipping_methods_for_categories').closest('tr').fadeOut();
					jQuery('#orddd_categories_for_pickup_locations').closest('tr').fadeOut();
	            }
			}
		});

		if ( 'delivery_calendar' == jQuery( "input[type=radio][id=\"orddd_delivery_checkout_options\"]:checked" ).val() ) {
			var i = 0;
			var isChecked = jQuery( "#orddd_enable_shipping_based_delivery_date" ).is( ":checked" );
			jQuery( ".form-table"  ).each( function() {
				if( i == 0 ) {
				} else if( i == 1 ) {
					var k = 0;
					var row = jQuery( this ).find( "tr" );
					jQuery.each( row , function() {
						if( k == ( row.length - 1 ) ) {
							jQuery( this ).fadeOut();
						} else {
							if( isChecked == true ) {
								jQuery( this ).fadeIn();    
							}
						}
						
						if( 'orddd_locations' == jQuery('input[type=radio][id="orddd_delivery_settings_type"]:checked').val() && k == 0 ) {
							jQuery( this ).fadeOut();
						} else if ( 'product_categories' == jQuery('input[type=radio][id="orddd_delivery_settings_type"]:checked').val() && k == 1 ) {
							jQuery( this ).fadeOut();
						} else if ( ( k == 0 || k == 1 ) && jQuery('input[type=radio][id="orddd_delivery_settings_type"][value="shipping_methods"]').is(':checked') ) {
							jQuery( this ).fadeOut();
						}
						k++;
					});
				} else {
					if( isChecked == true ) {
						jQuery( this ).fadeIn();
					}
				} 
				i++;
			}); 
			var j = 0;
			jQuery( "h2" ).each( function() {
				if( isChecked == true ) {
					jQuery( this ).fadeIn();
				}
				j++;
			}); 
			jQuery( '#orddd_individual' ).show();
			jQuery( '#orddd_bulk' ).show();
		} else if ( 'text_block' == jQuery( "input[type=radio][id=\"orddd_delivery_checkout_options\"]:checked" ).val() ) {
			var i = 0;
			var isChecked = jQuery( "#orddd_enable_shipping_based_delivery_date" ).is( ":checked" );
	
			jQuery( ".form-table" ).each( function() {
				if( i == 0 ) {
				} else if( i == 1 ) {
					var k = 0;
					var row = jQuery( this ).find( "tr" );
					jQuery.each( row , function() {
						if( k == 2 || k == 3 || k == ( row.length - 1 ) || k == ( row.length - 6 ) ) {
							// the field needs to be shown so we do nothing
							if( isChecked == true ) {
								jQuery( this ).fadeIn();    
							}
						} else {
							jQuery( this ).fadeOut();
						}
	
						if( jQuery('input[type=radio][id="orddd_delivery_settings_type"][value="orddd_locations"]').is(':checked') && k == 0 ) {
							jQuery( this ).fadeOut();
						} else if ( jQuery('input[type=radio][id="orddd_delivery_settings_type"][value="product_categories"]').is(':checked') && k == 1 ) {
							jQuery( this ).fadeOut();
						}
						k++;
					});
				} else {
					jQuery( this ).fadeOut();
				}
				i++;
			});
	
			var j = 0;  
			jQuery( "h2" ).each( function() {
				if( j == 0 || j == 1 || j == 2 ) {
				} else {
					jQuery( this ).fadeOut();
				}
				j++;
			});
			jQuery( '#orddd_individual' ).hide();
			jQuery( '#orddd_bulk' ).hide();
		}
	}

	var isChecked = jQuery( "#orddd_shipping_delivery_type_weekdays"  ).is( ":checked" );
    if( isChecked == true ) {
        jQuery( "#weekdays_fieldset" ).removeAttr( "disabled" );  
		jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days\"][value=\"weekdays\"]" ).removeAttr("disabled");  
		jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days_bulk\"][value=\"weekdays\"]" ).removeAttr("disabled");               
    } else {
         jQuery( "#weekdays_fieldset" ).attr( "disabled", "disabled" );  
		jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days\"][value=\"weekdays\"]" ).attr( "disabled", "disabled" );      
		jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days_bulk\"][value=\"weekdays\"]" ).attr( "disabled", "disabled" );              
    }

	for ( var i = 0; i <= 6; i++ ) {
		var weekday_enabled = jQuery( "#orddd_weekday_" + i + "_custom_setting" ).is(":checked");
		if( weekday_enabled === true ) {
			jQuery( "#orddd_shipping_based_time_slot_for_weekdays" ).append( jQuery("<option></option>").val( "orddd_weekday_" + i + "_custom_setting" ).html( orddd_weekdays['orddd_weekday_' + i ] ) );    
			jQuery( "#orddd_shipping_based_time_slot_for_weekdays_bulk" ).append( jQuery("<option></option>").val("orddd_weekday_" + i + "_custom_setting" ).html( orddd_weekdays['orddd_weekday_' + i ] ) );  
		}
	}

	jQuery( "#orddd_delivery_date" ).width( "100px" );
	jQuery( "#orddd_delivery_date" ).val( "" ).datepicker( {constrainInput: true, dateFormat: formats[0],minDate: new Date(), firstDay: jQuery( '#start_of_week' ).val() } );
	var isChecked = jQuery( "#orddd_shipping_delivery_type_specific_days" ).is( ":checked" );
	if( isChecked == true ) {
	    jQuery( "#orddd_delivery_date" ).removeAttr( "disabled" );  
	    jQuery( "#save_specific_date" ).removeAttr( "disabled" );
	    jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days\"][value=\"specific_dates\"]" ).removeAttr( "disabled" );
	    jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days_bulk\"][value=\"specific_dates\"]" ).removeAttr( "disabled" );
	    var isWeekdayChecked = jQuery("#orddd_shipping_delivery_type_weekdays").is(":checked");
	    if( isWeekdayChecked == false ) {
			jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days\"][value=\"specific_dates\"]" ).attr( "checked", true);
	        jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days_bulk\"][value=\"specific_dates\"]" ).attr( "checked", true);
	    }
	} else {
	    jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days\"][value=\"specific_dates\"]" ).attr( "disabled", "disabled" );              
	    jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days_bulk\"][value=\"specific_dates\"]" ).attr( "disabled", "disabled" );              
	}

	if ( jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days\"][value=\"weekdays\"]" ).is(":checked") ) {
		jQuery( '.shipping_based_time_slot_options' ).slideUp();
		jQuery( '.shipping_based_time_slot_for_weekdays' ).slideDown();
	} else {
		jQuery( '.shipping_based_time_slot_options' ).slideDown();
		jQuery( '.shipping_based_time_slot_for_weekdays' ).slideUp();
	}
	
	jQuery( '.orddd_shipping_based_time_slot_for_weekdays' ).select2();
	jQuery( '.orddd_shipping_based_time_slot_for_weekdays' ).css({'width': '300px' });

	jQuery( '.orddd_shipping_based_select_delivery_dates' ).select2();
	jQuery( '.orddd_shipping_based_select_delivery_dates' ).css({'width': '300px' });

	jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days\"]" ).on( 'change', function() {
		if ( jQuery( this ).is(':checked') ) {
			var value = jQuery( this ).val();
			jQuery( '.shipping_based_time_slot_options' ).slideUp();
			jQuery( '.shipping_based_time_slot_for_' + value ).slideDown();
		}
	});

	if ( jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days_bulk\"][value=\"weekdays\"]" ).is(":checked") ) {
		jQuery( '.shipping_based_time_slot_options_bulk' ).slideUp();
		jQuery( '.shipping_based_time_slot_for_bulk_weekdays' ).slideDown();
	} else {
		jQuery( '.shipping_based_time_slot_options_bulk' ).slideDown();
		jQuery( '.shipping_based_time_slot_for_bulk_weekdays' ).slideUp();
	}
	
	jQuery( '.orddd_shipping_based_time_slot_for_weekdays' ).css({'width': '300px' });
	jQuery( '.orddd_shipping_based_select_delivery_dates' ).css({'width': '300px' });

	jQuery( "input[type=radio][id=\"orddd_shipping_based_time_slot_for_delivery_days_bulk\"]" ).on( 'change', function() {
		if ( jQuery( this ).is(':checked') ) {
			var value = jQuery( this ).val();
			jQuery( '.shipping_based_time_slot_options_bulk' ).slideUp();
			jQuery( '.shipping_based_time_slot_for_bulk_' + value ).slideDown();
		}
	});

	jQuery( '#save_timeslots' ).click( function() {
		var time_slot_hidden = jQuery( '#orddd_time_slot_hidden' ).val();
		if( time_slot_hidden != '' ) {
			var hidden_arr = time_slot_hidden.split( '},' );	
		} else {
			var hidden_arr = [];
		}

		let from_time_array = [];
		let to_time_array 	= [];
		var timeslot_array  = [];
		var delivery_days   = '';
		var duration		= '';
		var frequency		= '';
		var is_bulk 		= jQuery( '#orddd_individual_or_bulk' ).val();
		var selected_days	= '';
		var lockout     	= '';
		var charges 		= '';
		var charges_label   = '';
		if( 'bulk' === is_bulk ) {
			delivery_days  = jQuery('input[name=orddd_shipping_based_time_slot_for_delivery_days_bulk]:checked' ).val();
			duration 	   = jQuery('#orddd_shipping_based_time_slot_duration').val();
			frequency 	   = jQuery('#orddd_shipping_based_time_slot_interval').val();
			selected_days  = jQuery( '#orddd_shipping_based_time_slot_for_weekdays_bulk' ).val();
			selected_dates = jQuery( '#orddd_shipping_based_select_delivery_dates_bulk' ).val();
			lockout 	   = jQuery( '#orddd_shipping_based_time_slot_lockout_bulk' ).val();
			charges 	   = jQuery( '#orddd_shipping_based_time_slot_additional_charges_bulk' ).val();
			charges_label  = jQuery( '#orddd_shipping_based_time_slot_additional_charges_label_bulk' ).val();

			var duration_in_secs  = duration * 60;
			var frequency_in_secs = frequency * 60;
			var time_starts_from  = jQuery("#orddd_shipping_based_time_slot_starts_from").val();
			var time_ends_at 	  = jQuery("#orddd_shipping_based_time_slot_ends_at").val();

			if( 0 == duration ) {
				jQuery( '#orddd_time_slot_list' ).before( '<div id=\'cdts-days-error-msg\' class=\'error settings-error notice is-dismissible\' style=\' width:50%;position:absolute;margin-left:50px;\'>Please Set the Time Slot Duration to be Greater than 0.</div>' );
				setTimeout( function() {
					jQuery( '#cdts-days-error-msg' ).fadeOut();
				}, 3000 );
			} else if( '' !== time_starts_from ) {
				var data = {
					time_starts_from: time_starts_from,
					time_ends_at: time_ends_at,
					duration_in_secs: duration_in_secs,
					frequency_in_secs: frequency_in_secs,
					action: 'orddd_get_time_slots_between_interval'
				}
			
				jQuery.post( localizeStrings.ajax_url, data, function( response ) {
					response.forEach( from_time => {
						timeslot_array.push( from_time );
					});
		
					orddd_add_timeslots( timeslot_array, delivery_days, hidden_arr, selected_days, selected_dates, lockout, charges, charges_label );
				});
			}
		} else {
			delivery_days   = jQuery('input[name=orddd_shipping_based_time_slot_for_delivery_days]:checked' ).val();
			selected_days   = jQuery( '#orddd_shipping_based_time_slot_for_weekdays' ).val();
			selected_dates  = jQuery( '#orddd_shipping_based_select_delivery_dates' ).val();
			lockout 	    = jQuery( '#orddd_shipping_based_time_slot_lockout' ).val();
			charges 	    = jQuery( '#orddd_shipping_based_time_slot_additional_charges' ).val();
			charges_label   = jQuery( '#orddd_shipping_based_time_slot_additional_charges_label' ).val();
			from_time_array = jQuery( 'input[name="orddd_shipping_based_time_from_hours[]"]' ).map( function(){
								return $(this).val();
							}).get();

			to_time_array = jQuery( 'input[name="orddd_shipping_based_time_to_hours[]"]' ).map( function(){
									return $(this).val();
								})
							.get();

			from_time_array.forEach( ( from_time, index ) => {
				if( '' === to_time_array[index] ) {
					timeslot_array.push( from_time );
				} else {
					timeslot_array.push( from_time + " - " + to_time_array[index] );
				}
			});

			orddd_add_timeslots( timeslot_array, delivery_days, hidden_arr, selected_days, selected_dates, lockout, charges, charges_label );
		}
	});

	// Delete time slots

	jQuery( 'table#orddd_time_slot_list' ).on( 'click', 'a.confirmation_time_slot', function() {
		var orddd_weekdays = jQuery.parseJSON( localizeStrings.orddd_weekdays );

		var time_slot_hidden = jQuery( '#orddd_time_slot_hidden' ).val();
		var time_slot = jQuery( 'table#orddd_time_slot_list tr#'+ this.id + ' td#orddd_time_slot' ).html();
		var date_str = jQuery( 'table#orddd_time_slot_list tr#'+ this.id + ' td#orddd_delivery_day' ).html();
		if( date_str.indexOf( '-' ) !== -1 ) {
			var delivery_day_type = 'specific_dates';
		} else {
			var delivery_day_type = 'weekdays'; 
		}
		if( delivery_day_type == 'weekdays' ) { 
			var orddd_weekdays_js = {};
			jQuery.each( orddd_weekdays, function( key, value ) {
				orddd_weekdays_js[ `${key}_custom_setting` ] = value;
			});
			jQuery.each( orddd_weekdays_js, function( key, name ) {
				if ( name == date_str ) {
					date_str = key;
				} else if( 'All' == date_str ) {
					date_str = 'all';
				}
			});
		} else if( delivery_day_type == 'specific_dates' ) {
			var specific_date = date_str.split( '-' );
			var new_specific_date = new Date( specific_date[ 0 ] + '/' + specific_date[ 1 ] + '/' + specific_date[ 2 ] );
			date_str = ( new_specific_date.getMonth()+1 ) + '-' + new_specific_date.getDate() + '-' + new_specific_date.getFullYear(); 
		} 
	
		var hidden_arr = time_slot_hidden.split( '},' );
		var substring = '';
		for( i = 0; i < hidden_arr.length; i++ ) {
			if( hidden_arr[ i ] != '' ) {
				var date_hidden_arr = hidden_arr[ i ].split( ':' );
				if( date_hidden_arr.length == '7' ) {
					var time_slot_str = date_hidden_arr[ 2 ] + ':' + date_hidden_arr[ 3 ];
				} else {
					var time_slot_str = date_hidden_arr[ 2 ] + ':' + date_hidden_arr[ 3 ] + ':' + date_hidden_arr[ 4 ];
				}    
				var array = date_hidden_arr[ 1 ].split( ',' );
				for( j = 0; j < array.length; j++ ) {
					if( date_str == array[ j ].trim() && time_slot == time_slot_str ) {
						array.splice( j, 1 );
					}
				}
				var array_str = array.join( ',' );
				if( array_str != '' ) {
					if( date_hidden_arr.length == '7' ) {
						hidden_arr[ i ] = date_hidden_arr[ 0 ] + ':' + array_str + ':' + time_slot_str + ':' + date_hidden_arr[ 4 ] + ':' + date_hidden_arr[ 5 ] + ':' + date_hidden_arr[ 6 ];
					} else {
						hidden_arr[ i ] = date_hidden_arr[ 0 ] + ':' + array_str + ':' + time_slot_str + ':' + date_hidden_arr[ 5 ] + ':' + date_hidden_arr[ 6 ] + ':' + date_hidden_arr[ 7 ];
					}
				} else {
					hidden_arr.splice( i, 1 );                           
				}
			}
		}
		jQuery( '#orddd_time_slot_hidden' ).val(  hidden_arr.join( '},' ) );
		jQuery( 'table#orddd_time_slot_list tr#'+ this.id ).remove();
	});


	jQuery( 'table#orddd_time_slot_list' ).on( 'click', 'a.edit_time_slot', function(e) {
		e.preventDefault();
		var orddd_weekdays = jQuery.parseJSON( localizeStrings.orddd_weekdays );

		var currentTD = $(this).closest('tr').find('td');
		var rowIndex  = $(this).parents("tr").index();
		rowIndex	  = rowIndex - 1;

		$.each(currentTD, function( index, value ) {
			var html = $(this).html();
			$(this).data( 'oldValue', html );
			if( 0 !== index && 5 !== index ) {
				var input = '';
				if( 2 == index ) {
					input = $(`<input name="orddd_edit_max_deliveries_${rowIndex}" id="orddd_edit_max_deliveries_${rowIndex}" type="number" value="${html}" />`);
					input.val(html);
				} else if( 3 == index ) {
					var charges_value = html.length > 0 ? parseFloat( html.substring(1) ) : '';
					input = $(`<input name="orddd_edit_charges_${rowIndex}" id="orddd_edit_charges_${rowIndex}" type="text"  value="${charges_value}"/>`);
					input.val(charges_value);
				} else if( 4 == index ) {
					input = $(`<input name="orddd_edit_charges_label_${rowIndex}" id="orddd_edit_charges_label_${rowIndex}" type="text" value="${html}"/>`);
					input.val(html);
				} else if( 1 == index ) {
					var time_array = html.split( ' - ' );
					var from_time  = time_array[0];
					var to_time    = '';
					if( undefined !== time_array[1] ) {
						to_time = time_array[1];
					}
					input = $( `
						<input type="text" name="orddd_edit_time_from_hours_${rowIndex}" id="orddd_edit_time_from_hours_${rowIndex}" class="orddd_time_slot" value="${from_time}" data-value="${from_time}"/>
						To
						<input type="text" name="orddd_edit_time_to_hours_${rowIndex}" id="orddd_edit_time_to_hours_${rowIndex}" class="orddd_time_slot" value="${to_time}" data-value="${to_time}"/>
					`);
				}
				$(this).html(input);
			}
			
			if( 5 == index ) {
				input = $( `
					<a href="#" class="orddd_custom_update_time">Update</a>
					<a href="#" class="orddd_custom_cancel">Cancel</a>
				`);

				$(this).html(input);
			}

			if( 0 == index ) {
				var weekday = $(this)
								.clone()	//clone the element
								.children()	//select all the children
								.remove()	//remove all the children
								.end()	//again go back to selected element
								.text();
				var selected_weekday = '';
				var weekday_or_date = '';
				if( weekday.indexOf( '-' ) !== -1 ) {
					var specific_date = weekday.split( '-' );
					var new_specific_date = new Date( specific_date[ 0 ] + '/' + specific_date[ 1 ] + '/' + specific_date[ 2 ] );
					weekday = ( new_specific_date.getMonth()+1 ) + '-' + new_specific_date.getDate() + '-' + new_specific_date.getFullYear(); 
					selected_weekday = weekday;
					weekday_or_date = 'specific_dates';
				}
				jQuery.each( orddd_weekdays, function( key, day ) {
					if( day == weekday ) {
						selected_weekday = `${key}_custom_setting`;
						weekday_or_date = 'weekdays';
					}
				});
				input = $( `
					<input type="hidden" name="orddd_edit_weekday" id="orddd_edit_weekday_${rowIndex}" value="${selected_weekday}">
					<input type="hidden" name="orddd_edit_weekday_or_date" id="orddd_edit_weekday_or_date${rowIndex}" value="${weekday_or_date}">
				`);

				$(this).append(input);
			}
		});
	});

	jQuery( 'table#orddd_time_slot_list' ).on( 'click', '.orddd_custom_cancel', function(e) {
		e.preventDefault();
		var currentTD = $(this).closest('tr').find('td');
		var rowIndex  = $(this).parents("tr").index();
		rowIndex	  = rowIndex - 1;
		//enable the current row
		$.each(currentTD, function( index, value ) {
			var html = $(this).data('oldValue');
		
			if ( 5 === index ) {
				input = $(`<a href="javascript:void(0)" id="orddd_edit_time_slot_${rowIndex}" class="edit_time_slot">Edit</a> | 
				<a href="javascript:void(0)"  class="confirmation_time_slot" id="orddd_delete_time_slot_${rowIndex}">Delete</a>`);
				$(this).html(input);
			} else {
				$(this).html(html);
			}
		});
	});

	$( 'table#orddd_time_slot_list' ).on( 'click', '.orddd_custom_update_time', function(e) {
		e.preventDefault();
		var orddd_weekdays = jQuery.parseJSON( localizeStrings.orddd_weekdays );
		var currentTD = jQuery(this).closest('tr').find('td');
		var rowIndex  = jQuery(this).parents("tr").index();
		rowIndex	  = rowIndex - 1;

		var time_slot_hidden = jQuery( '#orddd_time_slot_hidden' ).val();

		var date_str 								 = jQuery( `#orddd_edit_weekday_${rowIndex}` ).val();
		var orddd_time_from_hours 					 = jQuery(`#orddd_edit_time_from_hours_${rowIndex}`).val();
		var	from_time_old 							 = jQuery(`#orddd_edit_time_from_hours_${rowIndex}`).data('value');
		var	orddd_time_to_hours 					 = jQuery(`#orddd_edit_time_to_hours_${rowIndex}`).val();
		var	to_time_old 						 	 = jQuery(`#orddd_edit_time_to_hours_${rowIndex}`).data('value');
		var	orddd_time_slot_lockout 				 = jQuery(`#orddd_edit_max_deliveries_${rowIndex}`).val();
		var	orddd_time_slot_additional_charges 		 = jQuery(`#orddd_edit_charges_${rowIndex}`).val();
		var	orddd_time_slot_additional_charges_label = jQuery(`#orddd_edit_charges_label_${rowIndex}`).val();
		var time_slot 								 = from_time_old + ' - ' + to_time_old;

		if( '' == to_time_old ) {
			time_slot = from_time_old;
		}

		if( date_str.indexOf( '-' ) !== -1 ) {
			var delivery_day_type = 'specific_dates';
		} else {
			var delivery_day_type = 'weekdays'; 
		}
		if( delivery_day_type == 'weekdays' ) { 
			var orddd_weekdays_js = {};
			jQuery.each( orddd_weekdays, function( key, value ) {
				orddd_weekdays_js[ `${key}_custom_setting` ] = value;
			});
			jQuery.each( orddd_weekdays_js, function( key, name ) {
				if ( name == date_str ) {
					date_str = key;
				} else if( 'All' == date_str ) {
					date_str = 'all';
				}
			});
		} else if( delivery_day_type == 'specific_dates' ) {
			var specific_date = date_str.split( '-' );
			var new_specific_date = new Date( specific_date[ 0 ] + '/' + specific_date[ 1 ] + '/' + specific_date[ 2 ] );
			date_str = ( new_specific_date.getMonth()+1 ) + '-' + new_specific_date.getDate() + '-' + new_specific_date.getFullYear(); 
		} 

		var hidden_arr = time_slot_hidden.split( '},' );
		hidden_arr = orddd_get_separate_values( hidden_arr );
		for( i = 0; i < hidden_arr.length; i++ ) {
			if( hidden_arr[ i ] != '' ) {
				var date_hidden_arr = hidden_arr[ i ].split( ':' );

				if( date_hidden_arr.length == '7' ) {
					var time_slot_str = date_hidden_arr[ 2 ] + ':' + date_hidden_arr[ 3 ];
				} else {
					var time_slot_str = date_hidden_arr[ 2 ] + ':' + date_hidden_arr[ 3 ] + ':' + date_hidden_arr[ 4 ];
				}    
				var array = date_hidden_arr[ 1 ].split( ',' );
				for( j = 0; j < array.length; j++ ) {
					if( date_str == array[ j ].trim() && time_slot == time_slot_str ) {
						time_slot_str = orddd_time_from_hours + ' - ' + orddd_time_to_hours;
						if( '' == orddd_time_to_hours ) {
							time_slot_str = orddd_time_from_hours;
						}
						if( date_hidden_arr.length == '7' ) {
							date_hidden_arr[ 4 ] = orddd_time_slot_lockout;
							date_hidden_arr[ 5 ] = orddd_time_slot_additional_charges;
							date_hidden_arr[ 6 ] = orddd_time_slot_additional_charges_label;
						} else {
							date_hidden_arr[ 5 ] = orddd_time_slot_lockout;
							date_hidden_arr[ 6 ] = orddd_time_slot_additional_charges;
							date_hidden_arr[ 7 ] = orddd_time_slot_additional_charges_label;
						}
					}
				}
				var array_str = array.join( ',' );
				if( array_str != '' ) {
					if( date_hidden_arr.length == '7' ) {
						hidden_arr[ i ] = date_hidden_arr[ 0 ] + ':' + array_str + ':' + time_slot_str + ':' + date_hidden_arr[ 4 ] + ':' + date_hidden_arr[ 5 ] + ':' + date_hidden_arr[ 6 ];
					} else {
						hidden_arr[ i ] = date_hidden_arr[ 0 ] + ':' + array_str + ':' + time_slot_str + ':' + date_hidden_arr[ 5 ] + ':' + date_hidden_arr[ 6 ] + ':' + date_hidden_arr[ 7 ];
					}
				}
			}
		}
		jQuery( '#orddd_time_slot_hidden' ).val(  hidden_arr.join( '},' ) );

		$.each(currentTD, function( index, value ) {
			var html = $(this).data('oldValue');
			switch( index ) {
				case 1:
					var from_time = $(`#orddd_edit_time_from_hours_${rowIndex}`).val();
					var to_time = $(`#orddd_edit_time_to_hours_${rowIndex}`).val();
					var time = from_time + " - " + to_time;
					if( '' == to_time ) {
						time = from_time;
					}
					$(this).html(time);
					break;
				case 2:
					$(this).html( $(`#orddd_edit_max_deliveries_${rowIndex}`).val() );
					break;
				case 3:
					var charges_value = $(`#orddd_edit_charges_${rowIndex}`).val();
					var charges_str = charges_value.length > 0 ? timeslotStrings.currency + charges_value : charges_value;
					$(this).html( charges_str );
					break;
				case 4:
					$(this).html( $(`#orddd_edit_charges_label_${rowIndex}`).val() );
					break;
				case 5:
					input = $(`<a href="#" id="orddd_edit_time_slot_${rowIndex}" class="edit_time_slot">Edit</a> | 
					<a href="javascript:void(0)"  class="confirmation_time_slot" id="orddd_delete_time_slot_${rowIndex}">Delete</a>`);
					$(this).html(input);
					break;
			}
		});

	});

});

function orddd_add_timeslots( timeslot_array, delivery_days, hidden_arr, selected_days, selected_dates, lockout, charges, charges_label ) {
	var orddd_weekdays = jQuery.parseJSON( localizeStrings.orddd_weekdays );

	if( timeslot_array.length > 0 ) {	
		timeslot_array.forEach( ( timeslot, index ) => {
			var time_slot = timeslot;
			var added_timeslots = [];
			var weekdays = [];
			for( i = 0; i < hidden_arr.length; i++ ) {
				if( hidden_arr[ i ] != '' ) {
					var date_hidden_arr = hidden_arr[ i ].split( ':' );
					if( date_hidden_arr.length == '7' ) {
						var time_slot_str = date_hidden_arr[ 2 ] + ':' + date_hidden_arr[ 3 ];
					} else {
						var time_slot_str = date_hidden_arr[ 2 ] + ':' + date_hidden_arr[ 3 ] + ':' + date_hidden_arr[ 4 ];
					}    
					added_timeslots.push( time_slot_str );
					weekdays[time_slot_str] = date_hidden_arr[ 1 ];
				}
			}


			var timeslot_present = 'no';
			var rowCount 	   	 = jQuery( '#orddd_time_slot_list tr').length;

			if( delivery_days == 'weekdays' ) {
				var dd = selected_days;
				if( dd != '' && dd != null ) {
					var orddd_weekdays_js = {};

					jQuery.each( orddd_weekdays, function( key, value ) {
						orddd_weekdays_js[ `${key}_custom_setting` ] = value;
					});
					if( rowCount == 0 ) {
						jQuery( '#orddd_time_slot_list' ).append( `
						<tr class=\'orddd_common_list_tr\'>
							<th class=\'orddd_holiday_list\'>${timeslotStrings.timeslotDayText}</th>
							<th class=\'orddd_holiday_list\'>${timeslotStrings.timeslotText}</th>
							<th class=\'orddd_holiday_list\'>${timeslotStrings.timeslotLockoutText}</th>
							<th class=\'orddd_holiday_list\'>${timeslotStrings.timeslotChargesText}</th>
							<th class=\'orddd_holiday_list\'>${timeslotStrings.timeslotChargesLabelText}</th>
							<th class=\'orddd_holiday_list\'>${timeslotStrings.timeslotActionsText}</th>
						</tr>` );
						var rowCount = 1;
					}

					rowCount = rowCount - 1;
					for( i = 0; i < dd.length; i++ ) {
						if( dd[ i ] == 'all' ) {
							for( k = 0; k < hidden_arr.length; k++ ) {
								if( hidden_arr[ k ] != '' ) {
									var date_hidden_arr = hidden_arr[ k ].split( ':' );
									if( date_hidden_arr.length == '7' ) {
										var time_slot_str = date_hidden_arr[ 2 ] + ':' + date_hidden_arr[ 3 ];
									} else {
										var time_slot_str = date_hidden_arr[ 2 ] + ':' + date_hidden_arr[ 3 ] + ':' + date_hidden_arr[ 4 ];
									}    
									if( time_slot == time_slot_str && date_hidden_arr[ 1 ] == 'all' ) {
										timeslot_present = 'yes';
									}
								}
							}

							if( timeslot_present == 'no' ) {
								if( '' !== charges ) {

									//Add all the individual enabled weekdays if 'all' is selected.
									jQuery.each( orddd_weekdays_js, function(key, value ) {
										var isChecked = jQuery( '#'+key).is(':checked');
										
										if( isChecked == true ) {
											jQuery( '#orddd_time_slot_list tr:last').after(`
											<tr class=\'orddd_common_list_tr\' id=\'orddd_delete_time_slot_${rowCount}\'>
												<td class=\'orddd_holiday_list\' id=\'orddd_delivery_day\'>${value}</td>
												<td class=\'orddd_holiday_list\' id=\'orddd_time_slot\'>${time_slot}</td>
												<td class=\'orddd_holiday_list\' id=\'orddd_time_slot_lockout\'>${lockout}</td>
												<td class=\'orddd_holiday_list\' id=\'orddd_time_slot_additional_charges\' >${timeslotStrings.currency}${charges}</td>
												<td class=\'orddd_holiday_list\' id=\'orddd_time_slot_additional_charges_label\'>${charges_label}</td>
												<td class=\'orddd_holiday_list\'>
													<a href=\'javascript:void(0)\' class=\'edit_time_slot\' id=\'orddd_edit_time_slot_${rowCount}\'>Edit</a> | 
													<a href=\'javascript:void(0)\' class=\'confirmation_time_slot\' id=\'orddd_delete_time_slot_${rowCount}\'>Delete</a></td>
											</tr>` );
											rowCount = rowCount + 1;
										}
									}); 
									
								} else {
									jQuery.each( orddd_weekdays_js, function(key, value ) {
										var isChecked = jQuery( '#'+key).is(':checked');

										if( isChecked == true ) {
											jQuery( '#orddd_time_slot_list tr:last').after(`
											<tr class=\'orddd_common_list_tr\' id=\'orddd_delete_time_slot_${rowCount}'\'><td class=\'orddd_holiday_list\' id=\'orddd_delivery_day\'>${value}</td>
												<td class=\'orddd_holiday_list\' id=\'orddd_time_slot\'>${time_slot}</td>
												<td class=\'orddd_holiday_list\' id=\'orddd_time_slot_lockout\'>${lockout}</td>
												<td class=\'orddd_holiday_list\' id=\'orddd_time_slot_additional_charges\' ></td>
												<td class=\'orddd_holiday_list\' id=\'orddd_time_slot_additional_charges_label\' >${charges_label}</td>
												<td class=\'orddd_holiday_list\'>
												<a href=\'javascript:void(0)\' class=\'edit_time_slot\' id=\'orddd_edit_time_slot_${rowCount}\'>Edit</a> |
												<a href=\'javascript:void(0)\' class=\'confirmation_time_slot\' id=\'orddd_delete_time_slot_${rowCount}\'>Delete</a></td>
											</tr>` );
											rowCount = rowCount + 1;
										}
									});
									
								}
							}
						} else {    
							var weekday_value = dd[ i ];
							for( j = 0; j < hidden_arr.length; j++ ) {
								if( hidden_arr[ j ] != '' ) {
									var date_hidden_arr = hidden_arr[ j ].split( ':' );
									if( date_hidden_arr.length == '7' ) {
										var time_slot_str = date_hidden_arr[ 2 ] + ':' + date_hidden_arr[ 3 ];
									} else {
										var time_slot_str = date_hidden_arr[ 2 ] + ':' + date_hidden_arr[ 3 ] + ':' + date_hidden_arr[ 4 ];
									}    
									
									if( time_slot == time_slot_str && date_hidden_arr[ 1 ].indexOf( weekday_value ) != -1 ) { 
										timeslot_present = 'yes';
									}
								}
							}	
							
							if( timeslot_present == 'no' ) {
								if( charges != '' ) {
									jQuery( '#orddd_time_slot_list tr:last').after(`
									<tr class=\'orddd_common_list_tr\' id=\'orddd_delete_time_slot_${rowCount}\'>
										<td class=\'orddd_holiday_list\' id=\'orddd_delivery_day\'>${orddd_weekdays_js[ weekday_value ]}</td>
										<td class=\'orddd_holiday_list\' id=\'orddd_time_slot\'>${time_slot}</td>
										<td class=\'orddd_holiday_list\' id=\'orddd_time_slot_lockout\'>${lockout}</td>
										<td class=\'orddd_holiday_list\' id=\'orddd_time_slot_additional_charges\' >${timeslotStrings.currency}${charges}</td>
										<td class=\'orddd_holiday_list\' id=\'orddd_time_slot_additional_charges_label\' >${charges_label}</td>
										<td class=\'orddd_holiday_list\'>
											<a href=\'javascript:void(0)\' class=\'edit_time_slot\' id=\'orddd_edit_time_slot_${rowCount}\'>Edit</a> |
											<a href=\'javascript:void(0)\' class=\'confirmation_time_slot\' id=\'orddd_delete_time_slot_${rowCount}\'>Delete</a>
										</td>
									</tr>` );
								} else {
									jQuery( '#orddd_time_slot_list tr:last').after(`
									<tr class=\'orddd_common_list_tr\' id=\'orddd_delete_time_slot_${rowCount}\'>
										<td class=\'orddd_holiday_list\' id=\'orddd_delivery_day\'>${orddd_weekdays_js[ weekday_value ]}</td>
										<td class=\'orddd_holiday_list\' id=\'orddd_time_slot\'>${time_slot}</td>
										<td class=\'orddd_holiday_list\' id=\'orddd_time_slot_lockout\'>${lockout}</td>
										<td class=\'orddd_holiday_list\' id=\'orddd_time_slot_additional_charges\' ></td>
										<td class=\'orddd_holiday_list\' id=\'orddd_time_slot_additional_charges_label\' > ${charges_label} </td>
										<td class=\'orddd_holiday_list\'>
											<a href=\'javascript:void(0)\' class=\'edit_time_slot\' id=\'orddd_edit_time_slot_${rowCount}\'>Edit</a> |
											<a href=\'javascript:void(0)\' class=\'confirmation_time_slot\' id=\'orddd_delete_time_slot_${rowCount}\'>Delete</a>
										</td>
									</tr>` );
								}
							}
							rowCount = rowCount + 1;
							
						}
					}
					jQuery( '#cdts-days-error-msg' ).hide();
				} else { 
					jQuery( '#orddd_time_slot_list' ).before( '<div id=\'cdts-duration-error-msg\' class=\'error settings-error notice is-dismissible\' style=\' width:50%;position:absolute;margin-left:50px;\'>Please Select Delivery Days/Dates for the Time slot </div>' );
					setTimeout( function() {
						jQuery( '#cdts-duration-error-msg' ).fadeOut();
					}, 3000 );
				}      
			} else if( delivery_days == 'specific_dates' ) {
				var dd = selected_dates;
				if( dd != '' && dd != null ) {
					
					if( rowCount == 0 ) {
						jQuery( '#orddd_time_slot_list' ).append( `
						<tr class=\'orddd_common_list_tr\'>
							<th class=\'orddd_holiday_list\'>${timeslotStrings.timeslotDayText}</th>
							<th class=\'orddd_holiday_list\'>${timeslotStrings.timeslotText}</th>
							<th class=\'orddd_holiday_list\'>${timeslotStrings.timeslotLockoutText}</th>
							<th class=\'orddd_holiday_list\'>${timeslotStrings.timeslotChargesText}</th>
							<th class=\'orddd_holiday_list\'>${timeslotStrings.timeslotChargesLabelText}</th>
							<th class=\'orddd_holiday_list\'>${timeslotStrings.timeslotActionsText}</th>
						</tr>` );
						var rowCount = 1;
					}

					rowCount = rowCount - 1;
					for( i = 0; i < dd.length; i++ ) {
						var split_to_date = dd[ i ].split( '-' );
						split_to_date[0] = split_to_date[0] - 1;
						var date = new Date( split_to_date[2], split_to_date[0], split_to_date[1] );
						if( date.getDate() < 10 ) {
						   var dd_str = '0' + date.getDate();
						} else {
						   var dd_str = date.getDate();
						}
			
						if( ( date.getMonth() + 1 ) < 10 ) {
						   var mm = '0' + ( date.getMonth() + 1 );
						} else {
						   var mm = ( date.getMonth() + 1 );
						}

						var date_str = mm + '-' + dd_str + '-' + date.getFullYear();
						for( k = 0; k < hidden_arr.length; k++ ) {
							if( hidden_arr[ k ] != '' ) {
								var date_hidden_arr = hidden_arr[ k ].split( ':' );
								if( date_hidden_arr.length == '7' ) {
									var time_slot_str = date_hidden_arr[ 2 ] + ':' + date_hidden_arr[ 3 ];
								} else {
									var time_slot_str = date_hidden_arr[ 2 ] + ':' + date_hidden_arr[ 3 ] + ':' + date_hidden_arr[ 4 ];
								}    
								
								if( time_slot == time_slot_str && date_str == date_hidden_arr[ 1 ] ) {
									timeslot_present = 'yes';
								}
							}
						}

						if( timeslot_present == 'no' ) {
							if( charges != '' ) {
								jQuery( '#orddd_time_slot_list tr:last').after(`
								<tr class=\'orddd_common_list_tr\' id=\'orddd_delete_time_slot_${rowCount}\'>
									<td class=\'orddd_holiday_list\' id=\'orddd_delivery_day\'>${date_str}</td>
									<td class=\'orddd_holiday_list\' id=\'orddd_time_slot\'>${time_slot}</td>
									<td class=\'orddd_holiday_list\' id=\'orddd_time_slot_lockout\'>${lockout}</td>
									<td class=\'orddd_holiday_list\' id=\'orddd_time_slot_additional_charges\' >${timeslotStrings.currency}${charges}</td>
									<td class=\'orddd_holiday_list\' id=\'orddd_time_slot_additional_charges_label\' >${charges_label}</td>
									<td class=\'orddd_holiday_list\'>
										<a href=\'javascript:void(0)\' class=\'edit_time_slot\' id=\'orddd_edit_time_slot_${rowCount}\'>Edit</a> |
										<a href=\'javascript:void(0)\' class=\'confirmation_time_slot\' id=\'orddd_delete_time_slot_${rowCount}\'>Delete</a>
									</td>
								</tr>` );
							} else {
								jQuery( '#orddd_time_slot_list tr:last').after(`
								<tr class=\'orddd_common_list_tr\' id=\'orddd_delete_time_slot_${rowCount}\'>
									<td class=\'orddd_holiday_list\' id=\'orddd_delivery_day\'>${date_str}</td>
									<td class=\'orddd_holiday_list\' id=\'orddd_time_slot\'>${time_slot}</td>
									<td class=\'orddd_holiday_list\' id=\'orddd_time_slot_lockout\'>${lockout}</td>
									<td class=\'orddd_holiday_list\' id=\'orddd_time_slot_additional_charges\' ></td>
									<td class=\'orddd_holiday_list\' id=\'orddd_time_slot_additional_charges_label\' >${charges_label}</td>
									<td class=\'orddd_holiday_list\'>
										<a href=\'javascript:void(0)\' class=\'edit_time_slot\' id=\'orddd_edit_time_slot_${rowCount}\'>Edit</a> |
										<a href=\'javascript:void(0)\' class=\'confirmation_time_slot\' id=\'orddd_delete_time_slot_${rowCount} \'>Delete</a>
									</td>
								</tr>` );
							}
						}
						rowCount = rowCount + 1;
					}
					jQuery( '#cdts-dates-error-msg' ).hide();
				} else { 
					jQuery( '#orddd_time_slot_list' ).before( '<div id=\'cdts-dates-error-msg\' class=\'error settings-error notice is-dismissible\' style=\' width:50%;position:absolute;margin-left:50px; \'>Please Select Delivery Days/Dates for the Time slot </div>' );
					setTimeout( function() {
						jQuery( '#cdts-dates-error-msg' ).fadeOut();
					},3000 );
				} 

			} else {
				var dd = [];
			}

			if( dd != '' && dd != null && timeslot_present == 'no' ) {
				var row = jQuery( '#orddd_time_slot_hidden' ).val();

				if( jQuery.inArray( 'all', dd ) != -1 ) {
					jQuery.each( orddd_weekdays_js, function(key, value ) {
						var isChecked = jQuery( '#'+key).is(':checked');
						if( isChecked == true ) {
							row += '{' + delivery_days + ':' + key + ':' + time_slot + ':' + lockout + ':' + charges + ':' + charges_label + '},';
							jQuery( '#orddd_time_slot_hidden' ).val( row );
						}
					});
				}else{
					row += '{' + delivery_days + ':' + dd + ':' + time_slot + ':' + lockout + ':' + charges + ':' + charges_label + '},';

				}
				jQuery( '#orddd_time_slot_hidden' ).val( row );
				
			}
			jQuery( '#orddd_shipping_based_time_from_hours').val('');
			jQuery( `#orddd_shipping_based_time_from_hours_${index + 1}`).val('');
			jQuery( '#orddd_shipping_based_time_to_hours').val('');
			jQuery( `#orddd_shipping_based_time_to_hours_${index + 1}`).val('');

		});
		
		jQuery( '#orddd_shipping_based_time_slot_lockout' ).val( '' );
		jQuery( '#orddd_shipping_based_time_slot_lockout_bulk' ).val( '' );
		jQuery( '#orddd_shipping_based_time_slot_for_weekdays' ).val('').trigger('change');
		jQuery( '#orddd_shipping_based_time_slot_for_weekdays_bulk' ).val('').trigger('change');
		jQuery( '#orddd_shipping_based_select_delivery_dates' ).val('').trigger('change');
		jQuery( '#orddd_shipping_based_select_delivery_dates_bulk' ).val('').trigger('change');
		jQuery( '#orddd_shipping_based_time_slot_additional_charges_label' ).val( '' );
		jQuery( '#orddd_shipping_based_time_slot_additional_charges' ).val( '' ); 
		jQuery( '#orddd_shipping_based_time_slot_additional_charges_bulk' ).val( '' );
		jQuery( '#orddd_shipping_based_time_slot_additional_charges_label_bulk' ).val( '' );
		jQuery( '#orddd_shipping_based_time_slot_duration' ).val( '' );
		jQuery( '#orddd_shipping_based_time_slot_interval' ).val( '' );
		jQuery( '#orddd_shipping_based_time_slot_starts_from' ).val( '' );
		jQuery( '#orddd_shipping_based_time_slot_ends_at' ).val( '' );
	}
}

function orddd_get_separate_values( hidden_arr ) {
	var hidden_arr_updated = [];
	hidden_arr = hidden_arr.filter(Boolean);
	for( i = 0; i < hidden_arr.length; i++ ) {
		if( hidden_arr[ i ] != '' ) {
			var date_hidden_arr = hidden_arr[ i ].split( ':' );
			if( date_hidden_arr.length == '7' ) {
				var time_slot_str = date_hidden_arr[ 2 ] + ':' + date_hidden_arr[ 3 ];
			} else {
				var time_slot_str = date_hidden_arr[ 2 ] + ':' + date_hidden_arr[ 3 ] + ':' + date_hidden_arr[ 4 ];
			}    
			var array = date_hidden_arr[ 1 ].split( ',' );
			if( array.length > 1 ) {
				for( j = 0; j < array.length; j++ ) {
					if( date_hidden_arr.length == '7' ) {
						var str = date_hidden_arr[ 0 ] + ':' + array[ j ] + ':' + time_slot_str + ':' + date_hidden_arr[ 4 ] + ':' + date_hidden_arr[ 5 ] + ':' + date_hidden_arr[ 6 ];
					} else {
						var str = date_hidden_arr[ 0 ] + ':' + array[ j ] + ':' + time_slot_str + ':' + date_hidden_arr[ 5 ] + ':' + date_hidden_arr[ 6 ] + ':' + date_hidden_arr[ 7 ];
					}
					hidden_arr_updated.push( str );
				}
				
				hidden_arr.splice( i, 1 );
				hidden_arr.splice( i, 0, ...hidden_arr_updated );
				i = i + hidden_arr_updated.length;
			}				
		}
	}
	return hidden_arr;
}
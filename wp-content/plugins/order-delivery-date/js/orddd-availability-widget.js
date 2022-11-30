/**
 * Events added for the availability calendar of the widget.
 * 
 * @namespace orddd_availability_widget
 * @since 8.6
 */

jQuery( function( $ ) {	
	$( document ).ready( function() {
		var orddd_available_dates_color = $( "#orddd_available_dates_color" ).val() + '59';
		var orddd_booked_dates_color    = $( "#orddd_booked_dates_color" ).val() + '59';

		var option_str = get_datepicker_options();
		$( '.availability_calendar' ).datepicker( option_str );

		$( '.undefined' ).addClass( "ui-datepicker-unselectable" );
		$( 'a.ui-state-default' ).replaceWith(function(){
		    return $( "<span class='ui-state-default'/>" ).append( $(this).contents());
		});
		
		$( ".partially-booked" ).children().attr( 'style', 'background: linear-gradient(to bottom right, ' + orddd_booked_dates_color + ' 0%, ' + orddd_booked_dates_color + ' 50%, ' + orddd_available_dates_color + ' 50%, ' + orddd_available_dates_color + ' 100%);' );
		$( ".available-deliveries" ).children().attr( 'style', 'background: ' + orddd_available_dates_color + ' !important;' );

		$(document).on('click', '.ui-datepicker-next, .ui-datepicker-prev', function () {
			$( '.undefined' ).addClass( "ui-datepicker-unselectable" );
			$( 'a.ui-state-default' ).replaceWith(function(){
			    return $( "<span class='ui-state-default'/>" ).append( $(this).contents());
			});			
			$( ".partially-booked" ).children().attr( 'style', 'background: linear-gradient(to bottom right, ' + orddd_booked_dates_color + ' 0%, ' + orddd_booked_dates_color + ' 50%, ' + orddd_available_dates_color + ' 50%, ' + orddd_available_dates_color + ' 100%);' );
			$( ".available-deliveries" ).children().attr( 'style', 'background: ' + orddd_available_dates_color + ' !important;' );
		});

		
		$( ".orddd_show_availability" ).on( "click", function() {
			$( ".orddd_availability_widget" ).attr( "style", "opacity: 0.3" );
			$( "#ajax_img" ).show();
			var postcode = $( "#orddd_availability_postcode" ).val();
			localStorage.setItem( "orddd_availability_postcode", postcode );
			var data = {
				billing_postcode: postcode,
				action: "orddd_show_availability_calendar"
			};

			jQuery.ajax({
                type:		'POST',
                url:		orddd_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'orddd_show_availability_calendar' ),
                data:		data,
                success:	function( response ) {
			    	var response_arr = response.split( '&' );
                    orddd_params.orddd_shipping_id = response_arr [0];
					orddd_params.orddd_partially_booked_dates = response_arr[1];
					orddd_params.orddd_available_deliveries = response_arr[2];
					load_delivery_date();
					$( ".orddd_availability_widget" ).attr( "style", "opacity: 1" );
					$( ".orddd_widget_postcode" ).hide();
					var current_postcode = availability_widget.current_postcode + postcode;
					$( ".orddd_current_postcode" ).html( current_postcode );
					$( ".orddd_show_availability_button" ).hide();
					$( ".orddd_show_postcode_field" ).show();
					$( ".orddd_current_postcode" ).show();
					$( ".availability_calendar" ).show();
					$( '.undefined' ).addClass( "ui-datepicker-unselectable" );
					$( 'a.ui-state-default' ).replaceWith(function(){
						return $( "<span class='ui-state-default'/>" ).append( $(this).contents());
					});			
					$( ".partially-booked" ).children().attr( 'style', 'background: linear-gradient(to bottom right, ' + orddd_booked_dates_color + ' 0%, ' + orddd_booked_dates_color + ' 50%, ' + orddd_available_dates_color + ' 50%, ' + orddd_available_dates_color + ' 100%);' );
					$( ".available-deliveries" ).children().attr( 'style', 'background: ' + orddd_available_dates_color + ' !important;' );
					$( "#ajax_img" ).hide();
                }
            });
		});

		$( ".orddd_show_postcode" ).on( "click", function() {
			$( ".orddd_current_postcode" ).hide();
			$( ".orddd_current_postcode" ).html( "" );
			$( ".orddd_availability_widget" ).attr( "style", "opacity: 1" );
			$( ".orddd_widget_postcode" ).show();
			$( ".orddd_show_availability_button" ).show();
			$( ".orddd_show_postcode_field" ).hide();
			$( ".availability_calendar" ).hide();
		});
	});	
});
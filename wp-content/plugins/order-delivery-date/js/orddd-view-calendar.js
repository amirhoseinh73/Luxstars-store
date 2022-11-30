/**
 * File for configuring Full Calendar
 *
 * @namespace orddd_delivery_calendar
 * @since 2.8.7
 */
jQuery(document).ready( function() {
    
    // Prevent the right click event on the Print button click on Delivery Calendar Page. 
    jQuery( "#orddd_print_orders" ).on("contextmenu", function(e) {
        e.preventDefault();
    });   

    // The auxclick event is fired at an Element when a non-primary pointing device button (any mouse button other than the primary—usually leftmost—button) 
    // has been pressed and released both within the same element. This is added for the mouse roller click.
    // This event is not supported on the Safari browser and IE. 
    jQuery( "#orddd_print_orders" ).on("auxclick", function(e) {
		e.preventDefault();
    }); 

	jQuery( "#orddd_admin_add_to_calendar_delivery" ).on( 'click', function() {
		var orders_to_export = jQuery( "#orders_to_export" ).val();
		jQuery( "#orddd_update_event_message" ).html( "Total orders to export " +  orders_to_export + " ... " );
		var data = {
			action: "orddd_admin_delivery_calendar_events"
		};
		jQuery.post( jsArgs.ajaxurl, data, function( response ) {
			jQuery( "#orddd_update_event_message" ).html( "<p>All events are added to the Google calendar. <br> You can re-export the events to your calendar if you wish to add the events again with new settings.</p>" );
		});
	}); 

	jQuery( "#orddd_admin_export_to_calendar" ).on( 'click', function() {
		jQuery( "#orddd_update_event_message" ).html( "Re-exporting Orders ... " );
		var data = {
			action: "orddd_export_orders_again"
		};
		jQuery.post( jsArgs.ajaxurl, data, function( response ) {
			jQuery( "#orddd_update_event_message" ).html( "<p>All events are re-exported to the Google calendar.</p>" );
		});
	});
   
    jQuery( '.orddd_filter_delivery_calendar' ).select2({ minimumResultsForSearch: -1, width: '90%' });
    
    jQuery( '.orddd_filter_by_order_status' ).select2();
    jQuery( '.orddd_filter_by_order_status' ).select2({ width: '90%', closeOnSelect: false });
    
    jQuery( '.orddd_filter_by_order_shipping' ).select2();
    jQuery( '.orddd_filter_by_order_shipping' ).select2({ width: '90%', closeOnSelect: false, allowClear: true });
	
});


document.addEventListener('DOMContentLoaded', function() {
	var date = new Date();
	var d = date.getDate(); var m = date.getMonth(); var y = date.getFullYear();
	var load_previous_view_url = orddd.pluginurl;

	// Fields with id prev_order_status and prev_event_type are hidden fields.

	// Check for the selected status and pre populate that statuses in the dropdown
	// when the admin visits again. By default, Processing, Completed, On-hold and Pending payment will come.
	var prev_order_status = document.getElementById('prev_order_status').value;
	if ( localStorage.getItem( "delivery_calendar_last_order_statuses" ) != null ) {
		// Remove all selected options from the dropdown.
		jQuery( ".orddd_filter_by_order_status option:selected" ).removeAttr( "selected" );

		//Reselect the previously selected options in the dropdown.
		prev_order_status = localStorage.getItem( "delivery_calendar_last_order_statuses" );
		prev_order_status_arr = prev_order_status.split( "," );
		for( i = 0; i < prev_order_status_arr.length; i++ ) {
			jQuery( ".orddd_filter_by_order_status option[value='" + prev_order_status_arr[i] + "']").prop( 'selected', true );
		}

		//Set the previous order status as the previously selected status.
		jQuery( "#prev_order_status" ).val( prev_order_status );
	}

	// Check for the selected shipping method and pre populate that shipping method in the dropdown
	var prev_order_shipping = document.getElementById('prev_order_shipping').value;
	if ( localStorage.getItem( "delivery_calendar_last_order_shipping" ) != null ) {
		// Remove all selected options from the shipping method dropdown.
		jQuery( ".orddd_filter_by_order_shipping option:selected" ).removeAttr( "selected" );

		//Reselect the previously selected options in the dropdown.
		prev_order_shipping = localStorage.getItem( "delivery_calendar_last_order_shipping" );
		prev_order_shipping_arr = prev_order_shipping.split( "," );
		for( i = 0; i < prev_order_shipping_arr.length; i++ ) {
			jQuery( ".orddd_filter_by_order_shipping option[value='" + prev_order_shipping_arr[i] + "']").prop( 'selected', true );
		}

		//Set the previous shipping method as the previously selected shipping method.
		jQuery( "#prev_order_shipping" ).val( prev_order_shipping );
	}

	//Fetch the last view of the Delivery Calendar from the Local Storage.
	if ( localStorage.getItem( "delivery_calendar_last_view" ) == 'order' ) {

		// Pass the order type as the previous order statuses and event type as order to fetch events by default on page load.
		// Pass the previous order statuses for which the events should be displayed in the Calendar. 
		// This is required as when the default type is Orders and when an admin changes the order statuses,
		// the url for refetching events does not have statues and hence it displays the default events and not 
		// with the selected statuses.
		load_previous_view_url = orddd.pluginurl + '&eventType=order' + '&orderType=' + prev_order_status + '&orderType=' + prev_order_status;
		
		//Set the filter value to Orders.
		jQuery( '.orddd_filter_delivery_calendar' ).val( 'order' );

		//Set the previous event type as orders.
		jQuery( "#prev_event_type" ).val( "order" );
	} else {
		// Pass the order type as the previous order statuses and event type as order to fetch events by default on page load.
		load_previous_view_url = orddd.pluginurl + '&eventType=product' + '&orderType=' + prev_order_status;
		
		//Set the filter value to product.
		jQuery( '.orddd_filter_delivery_calendar' ).val( 'product' );

		//Set the previous event type as products.
		jQuery( "#prev_event_type" ).val( "product" );
	}

	if ( '' !== prev_order_shipping ) {
		load_previous_view_url = orddd.pluginurl + '&eventType=product' + '&orderType=' + prev_order_status + '&orderShipping=' + prev_order_shipping;
	}
	var calendarEl = document.getElementById('calendar');
	var calendar = new FullCalendar.Calendar(calendarEl, {
		headerToolbar: {
			left: 'prev,next today',
			center: 'title',
			right: 'dayGridMonth,timeGridWeek,timeGridDay'
		},
	  selectable: false,
	  events: load_previous_view_url,
	  navLinks: true,
	  locale: jsArgs.calendar_language,
	  dayMaxEvents: true,
	  eventDidMount: function( info ) {
		var event_data = { 
			action : 'orddd_order_calendar_content', 
			event_product_id: info.event.extendedProps.event_product_id,
			product_name: info.event.extendedProps.product_name,
			event_product_qty: info.event.extendedProps.event_product_qty,
			order_id : info.event.id,
			event_value : info.event.extendedProps.value,
			event_date : info.event.extendedProps.delivery_date, 
			event_timeslot: info.event.extendedProps.time_slot,
			event_type : info.event.extendedProps.eventtype
		};
		jQuery(info.el).qtip({
			content:{
				text : 'Loading...',
				button: 'Close', // It will disply Close button on the tool tip.
				ajax : {
					url : orddd.ajaxurl,
					type : "POST",
					data : event_data
				}
			},
			show: {
                event: 'click', // Show tooltip only on click of the vent
                solo: true // Disply only One tool tip at time, hide other all tool tip
            },
			position: {
				my: 'bottom right', // this is for the botton v shape icon position.
				at: 'top right' // this is for the content box position
			},
			hide: 'unfocus', //this is used to keep the hover effect untill click outside on calender. For clickingthe order number
			style: {
				classes: 'qtip-light qtip-shadow'
			}
		});
	  },

	  dayCellDidMount: function( args ) {
		var holidays = eval( '[' + jsArgs.orddd_holidays + ']' );
		var date_obj = args.date;
		var m = date_obj.getMonth(), d = date_obj.getDate(), y = date_obj.getFullYear();

		if( jQuery.inArray( ( m+1 ) + '-' + d + '-' + y, holidays ) != -1 || jQuery.inArray( ( m+1 ) + '-' + d,holidays ) != -1 ) {
			args.el.style.background = jsArgs.orddd_holiday_color;
		}
	  },
	  loading: function(bool) {
		if( bool == true ) {
			jQuery( "#orddd_events_loader" ).show();
		} else if( bool == false ) {
			jQuery( "#orddd_events_loader" ).hide();
		}
	  },
	  datesSet: function( dateInfo ) {
			jQuery( '#orddd_print_orders, #orddd_csv_orders' ).attr( 'href', function() {
				var start_date_obj = dateInfo.view.currentStart;
				var start_date = moment( start_date_obj ).format( 'YYYY-MM-DD' );

				var end_date_obj = dateInfo.view.currentEnd;
				var end_date = moment( end_date_obj ).subtract( '1', 'days' ).format( 'YYYY-MM-DD' );
				jQuery(this).data( 'start', start_date );
				jQuery(this).data( 'end', end_date );
				var orddd_this_href = this.href;
				if ( orddd_this_href.includes( "orddd_data.print" ) ) {
					orddd_this_href = `${jsArgs.admin_url}/admin.php?page=orddd_view_orders&download=orddd_data.print`;
				} else {
					orddd_this_href = `${jsArgs.admin_url}/admin.php?page=orddd_view_orders&download=orddd_data.csv`;
				}

				return orddd_this_href + '&eventType=' + jQuery( ".orddd_filter_delivery_calendar" ).val() + '&orderType=' + jQuery( ".orddd_filter_by_order_status" ).val() + '&orderShipping=' + jQuery( ".orddd_filter_by_order_shipping" ).val() +'&start=' + start_date + "&end=" + end_date;
			});
	  	}
	});

	calendar.render();
	jQuery( '.orddd_filter_delivery_calendar' ).on( 'change', function(){
		var prev_order_status = jQuery( "#prev_order_status" ).val();
		var prev_order_shipping = jQuery( "#prev_order_shipping" ).val();
		var eventSource = calendar.getEventSources();
		
        if ( jQuery( this ).val() == 'order') {
            jQuery( "#prev_event_type" ).val( "order" );
    		localStorage.setItem( "delivery_calendar_last_view", jQuery( this ).val() );
        } else {
        	jQuery( "#prev_event_type" ).val( "product" );
    		localStorage.setItem( "delivery_calendar_last_view", 'product' );
        }

		// Changes the href value of CSV and print buttons.
		orddd_set_buttons_href_value();
    });

	jQuery( '.orddd_filter_by_order_status' ).on( 'change', function() {
		var order_status = jQuery( this ).val();
    	jQuery( "#prev_order_status" ).val( order_status );

    	// Store the selected statuses in the local storage.
    	localStorage.setItem( "delivery_calendar_last_order_statuses", order_status );

		// Changes the href value of CSV and print buttons.
		orddd_set_buttons_href_value();
	});

	jQuery( '.orddd_filter_by_order_shipping' ).on( 'change', function() {
		var order_shipping = jQuery( this ).val();
    	jQuery( "#prev_order_shipping" ).val( order_shipping );

    	// Store the selected shipping in the local storage.
    	localStorage.setItem( "delivery_calendar_last_order_shipping", order_shipping );

		// Changes the href value of CSV and print buttons.
		orddd_set_buttons_href_value();
	});

	jQuery( '#orddd_filter_calendar_data' ).on( 'click', function() {
    	var order_shipping = jQuery( '.orddd_filter_by_order_shipping' ).val();
		var event_type = jQuery( "#prev_event_type" ).val();
		var order_status = jQuery( '.orddd_filter_by_order_status' ).val();
		var eventSource = calendar.getEventSources();
		eventSource[0].remove();
		calendar.addEventSource( orddd.pluginurl + "&eventType=" + event_type + "&orderType=" + order_status + "&orderShipping=" + order_shipping );
		calendar.refetchEvents();
	});
});

function orddd_set_buttons_href_value() {
	jQuery( '#orddd_print_orders, #orddd_csv_orders' ).attr( 'href', function() {
		var start_date = jQuery(this).data('start');
		var end_date = jQuery(this).data('end');
		var orddd_this_href = this.href;
		if ( orddd_this_href.includes( "orddd_data.print" ) ) {
			orddd_this_href = `${jsArgs.admin_url}/admin.php?page=orddd_view_orders&download=orddd_data.print`;
		} else {
			orddd_this_href = `${jsArgs.admin_url}/admin.php?page=orddd_view_orders&download=orddd_data.csv`;
		}

		return orddd_this_href + '&eventType=' + jQuery( ".orddd_filter_delivery_calendar" ).val() + '&orderType=' + jQuery( ".orddd_filter_by_order_status" ).val() + '&orderShipping=' + jQuery( ".orddd_filter_by_order_shipping" ).val() +'&start=' + start_date + "&end=" + end_date;
	});
}
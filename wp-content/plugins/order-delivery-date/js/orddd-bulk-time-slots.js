jQuery(document).ready( function( $ ) {

	var time_format = 'H:i';
	if( '1' == timeslotStrings.time_format ) {
		time_format = 'h:i A';
	}

    $('#orddd_time_slot_ends_at').timepicker({ 
		'scrollDefault': 'now', 
		'timeFormat' : time_format,
		'step'	: 15,
		'listWidth' : 1,
	});

	$('#orddd_time_slot_starts_from').timepicker({ 
		'scrollDefault': 'now', 
		'timeFormat' : time_format,
		'step'	: 15,
		'listWidth' : 1,
	});

	$('#orddd_time_from_hours, #orddd_time_to_hours').timepicker({ 
		'scrollDefault': 'now', 
		'timeFormat' : time_format,
		'step'	: 15,
		'listWidth' : 1,
	});

	$('#orddd_shipping_based_time_from_hours, #orddd_shipping_based_time_to_hours, #orddd_shipping_based_time_slot_starts_from,#orddd_shipping_based_time_slot_ends_at').timepicker({ 
		'scrollDefault': 'now', 
		'timeFormat' : 'H:i',
		'step'	: 15,
		'listWidth' : 1,
	});

	$( 'body' ).on( 'focus', '.time_slot .orddd_time_slot', function() {
		$(this).timepicker({ 
			'scrollDefault': 'now', 
			'timeFormat' : time_format,
			'step'	: 15,
			'listWidth' : 1,
		});
	});

	$( 'body' ).on( 'focus', '#orddd_time_slot .orddd_time_slot', function() {
		$(this).timepicker({ 
			'scrollDefault': 'now', 
			'timeFormat' : 'H:i',
			'step'	: 15,
			'listWidth' : 1,
		});
	});

	$( '#orddd_individual_time_slot_page' ).hide();
	$( '#orddd_bulk_time_slot_page' ).hide();
	$( '#orddd_individual' ).on( 'click', function(e) {
		e.preventDefault();
		$( '#orddd_individual_time_slot_page' ).show();
		$( '#orddd_bulk_time_slot_page' ).hide();
		$( '#orddd_individual_or_bulk' ).val('individual');
	});

	$( '#orddd_bulk' ).on( 'click', function(e) {
		e.preventDefault();
		$( '#orddd_individual_time_slot_page' ).hide();
		$( '#orddd_bulk_time_slot_page' ).show();
		$( '#orddd_individual_or_bulk' ).val('bulk');
	});
	var count = 0;
	
	$( '#add_another_slot' ).on( 'click', function(e) {
		e.preventDefault();
		if( count < 0 ){
			count = 0;
		}
		count++;

		$( '.add-timeslot' ).parent().append( `
			<section class="add-timeslot">
				<input type="text" name="orddd_time_from_hours[]" id="orddd_time_from_hours_${count}" value=""/> To
				<input type="text" name="orddd_time_to_hours[]" id="orddd_time_to_hours_${count}" value=""/> 
				<a href="#" class="remove_slot" role="button">Remove</a>
			</section> 
		` );
		$('#orddd_time_from_hours_' + count ).timepicker({ 
			'scrollDefault': 'now', 
			'timeFormat' : 'H:i',
			'step'	: 15,
			'listWidth' : 1,
		});
	
		$('#orddd_time_to_hours_' + count ).timepicker({ 
			'scrollDefault': 'now', 
			'timeFormat' : 'H:i',
			'step'	: 15,
			'listWidth' : 1,
		});
	});

	$( 'body' ).on( 'click', 'a.remove_slot', function(e) {
		count--;
		$(this).parent().remove();
		e.preventDefault();
	});

	//For custom settings 
	$( '#custom_add_another_slot' ).on( 'click', function(e) {
		e.preventDefault();
		if( count < 0 ){
			count = 0;
		}
		count++;

		$( '.custom-add-timeslot' ).parent().append( `
			<section class="add-timeslot">
				<input type="text" name="orddd_shipping_based_time_from_hours[]" id="orddd_shipping_based_time_from_hours_${count}" value=""/> To
				<input type="text" name="orddd_shipping_based_time_to_hours[]" id="orddd_shipping_based_time_to_hours_${count}" value=""/> 
				<a href="#" class="remove_slot" role="button">Remove</a>
			</section> 
		` );
		$('#orddd_shipping_based_time_from_hours_' + count ).timepicker({ 
			'scrollDefault': 'now', 
			'timeFormat' : 'H:i',
			'step'	: 15,
			'listWidth' : 1,
		});
	
		$('#orddd_shipping_based_time_to_hours_' + count ).timepicker({ 
			'scrollDefault': 'now', 
			'timeFormat' : 'H:i',
			'step'	: 15,
			'listWidth' : 1,
		});
	});


	$( 'body' ).on('click', '.edit_timeslot', function(e) {
		e.preventDefault();
		var currentTD = $(this).parents('tr').find('td');
		var rowIndex  = $(this).parents("tr").index();
		var orddd_weekdays = jQuery.parseJSON( localizeStrings.orddd_weekdays );
	
		//enable the current row
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
					<a href="#" class="orddd_update_time">Update</a> | 
					<a href="#" class="orddd_cancel">Cancel</a>
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
						selected_weekday = key;
						weekday_or_date = 'weekdays';
					}
				});

				if( 'All' == weekday ) {
					selected_weekday = weekday;
					weekday_or_date = 'weekdays';
				}
        
				input = $( `
					<input type="hidden" name="orddd_edit_weekday" id="orddd_edit_weekday_${rowIndex}" value="${selected_weekday}">
					<input type="hidden" name="orddd_edit_weekday_or_date" id="orddd_edit_weekday_or_date${rowIndex}" value="${weekday_or_date}">
				`);

				$(this).append(input);
			}
		});
	});

	$( 'body' ).on( 'click', '.orddd_cancel', function(e) {
		e.preventDefault();
		var currentTD = $(this).parents('tr').find('td');
		//enable the current row
		$.each(currentTD, function( index, value ) {
			var html = $(this).data('oldValue');

			if( 5 === index ) {
				input = $('<a href="#" class="edit_timeslot"><span class="dashicons dashicons-edit"></span></a>');
				$(this).html(input);
			}else {
				$(this).html(html);
			}
		});
	});

	$( 'body' ).on( 'click', '.orddd_update_time', function(e) {
		e.preventDefault();
		var currentTD = $(this).parents('tr').find('td');
		var rowIndex  = $(this).parents("tr").index();

		var data = {
			weekday: $(`#orddd_edit_weekday_${rowIndex}`).val(),
			time_slot_for: $(`#orddd_edit_weekday_or_date${rowIndex}`).val(),
			orddd_time_from_hours: $(`#orddd_edit_time_from_hours_${rowIndex}`).val(),
			from_time_old: $(`#orddd_edit_time_from_hours_${rowIndex}`).data('value'),
			orddd_time_to_hours: $(`#orddd_edit_time_to_hours_${rowIndex}`).val(),
			to_time_old: $(`#orddd_edit_time_to_hours_${rowIndex}`).data('value'),
			orddd_time_slot_lockout: $(`#orddd_edit_max_deliveries_${rowIndex}`).val(),
			orddd_time_slot_additional_charges: $(`#orddd_edit_charges_${rowIndex}`).val(),
			orddd_time_slot_additional_charges_label: $(`#orddd_edit_charges_label_${rowIndex}`).val(),
			orddd_mode: 'edit',
			action: 'orddd_edit_time_slot'
		}

		jQuery.post( localizeStrings.ajax_url, data, function( response ) {
			if( response == 'success' ) {
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
							input = $('<a href="#" class="edit_timeslot"><span class="dashicons dashicons-edit"></span></a>');
							$(this).html(input);
					}
				});
			}
		});
	})
});


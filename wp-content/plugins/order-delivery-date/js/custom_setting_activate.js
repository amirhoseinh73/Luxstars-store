/**
 * This function will toggle the status of the custom delivery setting.
 * 
 * @function orddd_toggle_custom_setting_status
 * @since 9.6
 */
jQuery(function( $ ) {

	$('.orddd-switch.orddd-toggle-template-status').click(function(){

		var $switch, state, new_state;

		$switch = $(this);

		if ( $switch.is('.wcal-loading') )
			return;

		state = $switch.attr( 'orddd-template-switch' );
		new_state = state === 'on' ? 'off' : 'on';

		$switch.addClass('wcal-loading');
		$switch.attr( 'orddd-template-switch', new_state );

		$.post( ajaxurl, {
			action           : 'orddd_toggle_custom_setting_status',
			custom_setting_id: $switch.attr( 'orddd-custom-setting-id' ),
			current_state    : new_state
		}, function( wcal_template_response ) {
			if ( wcal_template_response.indexOf('wcal-template-updated') > -1){
				var wcal_template_response_array = wcal_template_response.split ( ':' );

				var wcal_deactivate_ids = wcal_template_response_array[1];
				var wcal_split_all_ids  = wcal_deactivate_ids.split ( ',' );

				for (i = 0; i < wcal_split_all_ids.length; i++) { 
					var selelcted_id = wcal_split_all_ids[i];
				
					var $list = document.querySelector('[orddd-custom-setting-id="'+ selelcted_id+'"]');
					$($list).attr('orddd-template-switch','off');
				}
				
			}
			$switch.removeClass('wcal-loading');
		});
	});
});
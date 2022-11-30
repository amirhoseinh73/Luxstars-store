/**
 * Functions for managing scripts of additional delivery charges.
 *
 * @since 9.25.0
 */
 jQuery(document).ready( function(){

 });

 function orddd_add_new_additional_settings_based_on_days( custom_settings = '' ) {
    total_row_count = jQuery('.orddd_additional_settings_based_on_days_btn').data('count');
    if ( ! total_row_count || total_row_count <= 0 ) {
        total_row_count = 0;
    }
    if ( '' !== custom_settings ) {
        field_name = custom_settings + '[orddd_additional_settings_based_on_days]';
    } else {
        field_name = 'orddd_additional_settings_based_on_days';
    }
    var total_row_count = parseFloat( total_row_count ) + 1;
    var output = '<p class="orddd_additional_settings_based_on_days"><input placeholder="Number of days" type="number" min="0" name="' + field_name + '[number][]" /> <input placeholder="Delivery charges label" type="text" name="' + field_name + '[label][]" /> <input placeholder="Delivery charges" type="text" name="' + field_name + '[charge][]" /> <a class="orddd_additional_settings_remove" href="javascript:;" onclick="orddd_remove_additional_settings_of_days(this)"><i class="dashicons dashicons-trash"></i></a></p>';
    jQuery('.orddd_additional_settings_based_on_days_section').append( output );
    jQuery('.orddd_additional_settings_based_on_days_btn').data( 'count', total_row_count );
 }

 function orddd_remove_additional_settings_of_days( elem ) {
    jQuery( elem ).parent('p').remove();
    new_counter = jQuery('.orddd_additional_settings_based_on_days').length;
    jQuery('.orddd_additional_settings_based_on_days_btn').data( 'count', new_counter );
 }
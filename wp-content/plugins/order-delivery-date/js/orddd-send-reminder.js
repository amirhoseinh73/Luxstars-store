/**
 * Manual reminder email from admin.
 */
jQuery( document ).ready( function ($) {
    $( '#orddd_save_message' ).on( "click", function() {
        let content = '';

        if($('#wp-orddd_reminder_message-wrap').hasClass('html-active')){ 
            content = $('#orddd_reminder_message').val(); 
        }else if( $('#wp-orddd_reminder_message-wrap').hasClass('tmce-active') ) {
            var activeEditor = tinyMCE.get('orddd_reminder_message');
            if(activeEditor!==null){ 
             content = activeEditor.getContent();
            }
        }

        $( "#ajax_img" ).show();
        var data = {
            subject : $( '#orddd_reminder_subject' ).val(),
            message :content,
            action: 'orddd_save_reminder_message'
        };

        $.post( orddd_reminder_params.ajax_url, data, function(response) {
            if( response !== false ) {
                $( "#ajax_img" ).hide();
                $( '.wrap form' ).after( '<div class="notice notice-success"><p>Message draft saved</p></div>' );
                $( '.notice-success' ).fadeOut( 5000 );
            }
        });
    });
});
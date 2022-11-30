jQuery( document ).ready( function() {

    /** Remove storage values on Order Received page. */
    localStorage.removeItem( "orddd_storage_next_time" );
    localStorage.removeItem( "e_deliverydate_session" );
    localStorage.removeItem( "h_deliverydate_session" );
    localStorage.removeItem( "orddd_time_slot" );
    localStorage.removeItem( "orddd_availability_postcode" );

    /** Close the dropdown if the user clicks outside of it */
    window.onclick = function(event) {
        if ( !event.target.matches( '.dropbtn' ) ) {
            var dropdowns = document.getElementsByClassName( "add_to_calendar-content" );
            var i;
            for ( i = 0; i < dropdowns.length; i++ ) {
                var openDropdown = dropdowns[i];
                if ( openDropdown.classList.contains( 'show' ) ) {
                    openDropdown.classList.remove( 'show' );
                }
            }
        } 
    }

    /** When the user clicks on the button, toggle between hiding and showing the dropdown content */
    jQuery( '#calendar-toggle' ).on( 'click', function() {
        document.getElementById( "add_to_calendar_menu" ).classList.toggle( "show" );
    });

});


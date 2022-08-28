function doc_ready( fn ) {
    if ( document.readyState === "complete" || document.readyState === "interactive" )
        setTimeout( fn, 1 );
    else
        document.addEventListener( "DOMContentLoaded", fn );
}

function fill_email_by_username() {
    const form = document.getElementById( "createuser" )
    if ( ! form ) return

    const email = document.getElementById( "email" )
    if ( ! email ) return

    // email.setAttribute( "disabled", true )
    // email.setAttribute( "readonly", true )|

    email.closest( "tr" ).style.display = "none"

    const user_login = document.getElementById( "user_login" )
    if ( ! user_login ) return

    user_login.addEventListener( "keyup", function() {
        email.value = this.value + "@luxstars.ir";
    } );
}

function hideEmailColumnFromUserTable() {
    const tableBody = document.getElementById( "the-list" );
    if ( ! tableBody ) return

    const emailColumn = tableBody.querySelectorAll( ".email.column-email" )
    if ( ! emailColumn || ! emailColumn.length ) return

    const email = document.getElementById( "email" )
    if ( ! email ) return

    emailColumn.forEach( elem => elem.style.display = "none" )
    email.style.display = "none"
}

doc_ready( () => {
    fill_email_by_username()
    // hideEmailColumnFromUserTable()
} );
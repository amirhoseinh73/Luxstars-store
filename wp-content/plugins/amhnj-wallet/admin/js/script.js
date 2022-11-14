function doc_ready( fn ) {
    if ( document.readyState === "complete" || document.readyState === "interactive" )
        setTimeout( fn, 1 );
    else
        document.addEventListener( "DOMContentLoaded", fn );
}

doc_ready( () => {
} );
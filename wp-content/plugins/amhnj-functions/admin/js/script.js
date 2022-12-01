function doc_ready( fn ) {
    if ( document.readyState === "complete" || document.readyState === "interactive" )
        setTimeout( fn, 1 );
    else
        document.addEventListener( "DOMContentLoaded", fn );
}

doc_ready( () => {
    hideOrderLicenseError()
} );

function hideOrderLicenseError() {
    const a = document.querySelector( ".notice-error a[href*='edd_sample_license_page']" )
    if ( ! a ) return

    const div = a.closest( "div.notice-error" )
    if ( ! div ) return
    div.style.display = "none"
}
jQuery(document).ready( () => {
    
    jQuery('body').on( 'added_to_cart', function(){
        append_banner()
    });

    const is_add_to_cart_with_form = document.querySelector( ".woocommerce-message" )
    if ( ! is_add_to_cart_with_form ) return

    const makeSure = is_add_to_cart_with_form.querySelector( ".button.wc-forward" )
    if ( ! makeSure ) return
    append_banner()
} )

function append_banner() {
    jQuery( "body" ).append( `<section class="alert-payment position-fixed alert alert-info" style="top:4rem;left:50%;transform:translateX(-50%);z-index:50000">
        در سبد خرید خود میتوانید نوع پرداخت خود را نقدی، 45 و یا 90 روزه انتخاب نمایید!
    </section>` );
    
    setTimeout( () => {
        const alert = jQuery( ".alert-payment" )
        if ( ! alert ) return
        
        alert.fadeOut(500);
    }, 5000 )
}
function doc_ready( fn ) {
    if ( document.readyState === "complete" || document.readyState === "interactive" )
        setTimeout( fn, 1 );
    else
        document.addEventListener( "DOMContentLoaded", fn );
}

doc_ready( () => {
    changeCalcCartTotal()
} );

function changeCalcCartTotal() {
    const radioBoxes = document.querySelectorAll( "[name='wholesaler_payment_way']" );
    if ( ! radioBoxes || ! radioBoxes.length ) return
    radioBoxes.forEach( radio => {
        if ( radio.checked ) {
            calcCart.call(radio)
            addGetParamToSubmitButton()
        }
        radio.addEventListener( "change", calcCart );
    } );
}

function calcCart() {
    if ( ! this ) return;
    const which = this.value;
    const valueOfTotalPayment = document.getElementById( "wholesaler_cart_value_payment_" + which ).value;

    const subTotal = document.getElementById( "wholesaler_subtotal_cart" );
    const total = document.getElementById( "wholesaler_total_cart" );

    total.innerHTML = valueOfTotalPayment;
    subTotal.innerHTML = valueOfTotalPayment;
    addGetParamToSubmitButton();
}

function addGetParamToSubmitButton() {
    const button = document.getElementById( "cart_to_checkout_submit" );
    if ( ! button ) return
    const radioBox = document.querySelector( "[name='wholesaler_payment_way']:checked" );
    if ( ! radioBox ) return

    const href = button.href.split( "?" )[ 0 ];
    button.href = href + "?pay_way=" + radioBox.value
}
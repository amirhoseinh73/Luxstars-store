function fillHiddenInputByShowedInput() {
    const input = document.getElementById( "amhnj_colleague_code_customer" )
    const hidden = document.getElementById( "amhnj_colleague_code_customer_hidden" )

    if ( ! input || ! hidden ) return

    input.addEventListener( "keyup", filleInHidden )

    function filleInHidden() {
        hidden.value = this.value
    }
}
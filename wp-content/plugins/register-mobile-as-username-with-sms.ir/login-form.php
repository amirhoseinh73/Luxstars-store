<?php

require_once AMHNJ_REGISTER_PLUGIN_DIR_PATH . "sms.ir-send.php";

function login_form() {
    global $form_error_login;
    if ( isset( $_POST['amhnj_login_customer'] ) && isset( $_POST['username'] ) && ! empty( $_POST['username'] ) && count( $form_error_login->get_error_messages() ) < 1 ) :
        echo '<form class="woocommerce-form woocommerce-form-login login" method="post">

            ' . do_action( 'woocommerce_login_form_start' ) . '

            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide d-none">
                <label for="username">' . __( 'Username or email address', 'woocommerce' ) . ' &nbsp;<span class="required">*</span></label>
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text"
                name="username" id="username" autocomplete="username"
                value="' . ( ( ! empty( $_POST['username'] ) ) ? __( wp_unslash( $_POST['username'] ) ) : '' ) . '" />
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="password">کد تایید پیامک شده را وارد نمایید&nbsp;<span class="required">*</span></label>
                <input class="woocommerce-Input woocommerce-Input--text input-text" type="text" name="password"
                id="password" autocomplete="current-password" />
            </p>

            ' . do_action( 'woocommerce_login_form' ) . '

            <p class="form-row">
                ' . wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ) . '
                <button type="submit" class="woocommerce-button button woocommerce-form-login__submit"
                name="login" value="' . __( 'Log in', 'woocommerce' ) . '">' . __( 'Log in', 'woocommerce' ) . '</button>
            </p>

            ' . do_action( 'woocommerce_login_form_end' ) . '

        </form>';
    else :
    echo '<form action="' . get_permalink() . '" method="post"
            class="woocommerce-form woocommerce-form-register register">
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="username">' . __( 'Phone Number', 'woocommerce' ) . ' &nbsp;<span class="required">*</span></label>
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text"
                    name="username" id="username" autocomplete="username"
                    value="' . ( ( ! empty( $_POST['username'] ) ) ? __( wp_unslash( $_POST['username'] ) ) : '' ) . ' " />
                </p>
                <p class="woocommerce-form-row form-row">
                    <button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit"
                    name="amhnj_login_customer">
                        ' . __( 'Log in', 'woocommerce' ) . '
                    </button>
                </p>
            </form>';
    endif;
}

function validate_login( $phone_number ) {
 
    // Make the WP_Error object global    
    global $form_error_login;
     
    // If any field is left empty, add the error message to the error object
    if ( empty( $phone_number ) || ! is_numeric( $phone_number ) || strlen( $phone_number ) !== 11 || $phone_number[ 0 ] !== "0" || $phone_number[ 1 ] !== "9" ) {
        $form_error_login->add( 'registration-error-invalid-username', __( 'شماره تلفن وارد شده صحیح نیست!', 'woocommerce' ) );
    }

    if ( ! username_exists( $phone_number ) ) {
        $form_error_login->add( 'registration-error-username-exists', __( 'با این شماره قبلا ثبت نامی انجام نشده است! لطفا با پشتیبان سایت تماس بگیرید و یا از بخش ثبت نام اقدام نمایید', 'woocommerce' ) );
    }

}

function send_sms_login( $phone_number, $password ) {
    global $form_error_login;
     
    if ( 1 > count( $form_error_login->get_error_messages() ) ) {
             
        send_sms_ir( $phone_number, "VerificationCode", $password );
 
    }
}

function change_password( $phone_number, $password ) {
    global $form_error_login;

    if ( 1 <= count( $form_error_login->get_error_messages() ) ) return;

    $user = get_user_by( "login", $phone_number );
    
    // do_action( 'password_reset', $user, $password );

    wp_set_password( $password, $user->ID );
}

function do_login_form() {
    global $form_error_login, $phone_number;
    $form_error_login = new WP_Error;

    if ( isset( $_POST['amhnj_login_customer'] ) ) {
        // Get the form data
        $phone_number = trim( $_POST['username'] );

        // validate the user form input
        validate_login( $phone_number );

        $random_code = random_verficiraion_code();
        // send the sms
        send_sms_login( $phone_number, $random_code );

        change_password( $phone_number, $random_code );
    }

    // if $form_error_login is WordPress Error, loop through the error object
    // and echo the error
    if ( is_wp_error( $form_error_login ) ) {
        foreach ( $form_error_login->get_error_messages() as $error ) {
            echo "<p class='woocommerce-error'>$error</p>";
        }
    }
     
    // display the contact form
    login_form();
 
}
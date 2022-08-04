<?php

require_once AMHNJ_REGISTER_PLUGIN_DIR_PATH . "sms.ir-send.php";

function register_form() {
    global $phone_number, $form_error;
    if ( isset( $_POST['username'] ) && ! empty( $_POST['username'] ) && count( $form_error->get_error_messages() ) < 1 ) :
        echo '<form class="woocommerce-form woocommerce-form-login login" method="post">

            ' . do_action( 'woocommerce_login_form_start' ) . '

            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide d-none">
                <label for="username">' . __( 'Username or email address', 'woocommerce' ) . ' &nbsp;<span class="required">*</span></label>
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text"
                name="username" id="username" autocomplete="username"
                value=" ' . ( ( ! empty( $_POST['username'] ) ) ? __( wp_unslash( $_POST['username'] ) ) : '' ) . ' " />
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
            <label for="reg_username">
            ' . __( 'Phone Number', 'woocommerce' ) . '&nbsp;<span class="required">*</span>
            </label>
            <input type="tel" class="woocommerce-Input woocommerce-Input--text input-text"
            name="username" id="reg_username" autocomplete="username"
            value="' . ( ( isset( $_POST['username'] ) && ! empty( $_POST['username'] ) ) ? __( wp_unslash( $phone_number ) ) : '' ) . '" />
        </p>
        <p class="woocommerce-form-row form-row">
            <button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit"
            name="amhnj_create_new_customer" value="' . __( 'Register', 'woocommerce' ) . '">
                ' . __( 'Register', 'woocommerce' ) . '
            </button>
        </p>
    </form>';

    endif;
}

function validate_form( $phone_number ) {
 
    // Make the WP_Error object global    
    global $form_error;
     
    // instantiate the class
    $form_error = new WP_Error;
     
    // If any field is left empty, add the error message to the error object
    if ( empty( $phone_number ) || ! is_numeric( $phone_number ) || strlen( $phone_number ) !== 11 || $phone_number[ 0 ] !== "0" || $phone_number[ 1 ] !== "9" ) {
        $form_error->add( 'registration-error-invalid-username', __( 'شماره تلفن وارد شده صحیح نیست!', 'woocommerce' ) );
    }

    if ( username_exists( $phone_number ) ) {
        $form_error->add( 'registration-error-username-exists', __( 'شماره وارد شده قبلا ثبت شده است!', 'woocommerce' ) );
    }

}

function send_sms( $phone_number, $password ) {
    global $form_error;
     
    if ( 1 > count( $form_error->get_error_messages() ) ) {
             
        send_sms_ir( $phone_number, "VerificationCode", $password );
 
    }
}

function register_customer( $phone_number, $password ) {
    global $form_error;

    if ( 1 <= count( $form_error->get_error_messages() ) ) return;

    $email = "$phone_number@luxstars.ir";

    if ( username_exists( $phone_number ) || email_exists( $email ) ) {
        return $form_error->add( 'registration-error-username-exists', __( 'شماره وارد شده قبلا ثبت شده است!', 'woocommerce' ) );
    }

    $userData = array(
        'user_login' => $phone_number,
        'user_pass'  => $password,
        'user_email' => $email,
        'role'       => "customer",
    );

    $customer_id = wp_insert_user( $userData );

    if ( is_wp_error( $customer_id ) ) {
        return $form_error->add( $customer_id );
    }

    do_action( 'woocommerce_created_customer', $customer_id, $userData, $password );

    return $customer_id;
}

function do_register_form() {
    global $form_error, $phone_number;
    if ( isset( $_POST['amhnj_create_new_customer'] ) ) {
        // Get the form data
        $phone_number = $_POST['username'];

        // validate the user form input
        validate_form( $phone_number );

        $random_code = random_verficiraion_code();
        // send the sms
        send_sms( $phone_number, $random_code );

        register_customer( $phone_number, $random_code );
    }

    // if $form_error is WordPress Error, loop through the error object
    // and echo the error
    if ( is_wp_error( $form_error ) ) {
        foreach ( $form_error->get_error_messages() as $error ) {
            echo "<p class='woocommerce-error'>$error</p>";
        }
    }
     
    // display the contact form
    register_form();
 
}
<style media="screen" class="wp-adminify-style-wp">
    body.wp-adminify-login-customizer.login {
        position: relative;
        z-index: 0;
        overflow: hidden;
    }

    body.wp-adminify-login-customizer.login:before {
        content: '';
        background: url('https://wpadminify.com/wp-content/uploads/2021/06/login-bg-16.png');
        background-position: right !important;
        background-size: cover !important;
        background-repeat: no-repeat !important;
        height: 120%;
        width: 100%;
        right: 0;
        top: 0;
        position: absolute;
        z-index: -1;
        transform: rotate(7deg) scaleX(1.5) translate(0%, -11.4%);
    }

    body.wp-adminify-login-customizer #login a.wp-adminify-logo-login-link:focus {
        border: 0;
        box-shadow: none;
        outline: 0;
    }

    body.wp-adminify-login-customizer.login #login {
        background-color: #fff;
        border: 0;
        border-radius: 8px;
        box-shadow: 0px 2px 54px rgba(20, 20, 42, 0.1);
        padding: 20px 0;
    }
    body.wp-adminify-login-customizer #login h1 a {
        margin-bottom: 0;
    }

    body.wp-adminify-login-customizer #loginform {
        border: 0;
        box-shadow: none;
        margin-top: 0;
        padding-bottom: 25px;
    }
    

    body.wp-adminify-login-customizer #loginform label {
        color: #4E4B66;
    }

    body.wp-adminify-login-customizer #loginform input,
    body.wp-adminify-login-customizer #loginform textarea,
    body.wp-adminify-login-customizer #loginform select {
        background: #F1F1F3;
        border: 0;
        border-radius: 6px;
        box-shadow: none;
        color: #4E4B66;
        font-size: 14px;
        line-height: 16px;
        height: 36px;
    }

    body.wp-adminify-login-customizer #loginform textarea {
        height: auto;
    }

    body.wp-adminify-login-customizer #loginform input[type="submit"] {
        background-color: #0347FF;
        color: #fff;
        float: none;
        margin-top: 15px;
        padding: 0 15px;
        width: 100%;
    }

    body.wp-adminify-login-customizer #loginform input[type="checkbox"],
    body.wp-adminify-login-customizer #loginform input[type="radio"] {
        height: 14px;
        width: 14px;
        border-radius: 4px;
        min-width: inherit;
    }

    body.wp-adminify-login-customizer #loginform .button.wp-hide-pw {
        background-color: transparent;
        border: 0;
        box-shadow: none;
        color: #4E4B66;
        font-size: 16px;
        height: 36px;
        margin-top: 0;
    }

    body.wp-adminify-login-customizer #login #wp-adminify-lost-password {
        color: #0347FF;
        font-size: 14px;
        font-weight: 700;
    }

    body.wp-adminify-login-customizer #login #backtoblog a {
        background: #FFF;
        box-shadow: 0px 2px 35px rgba(78, 75, 102, 0.05);
        border-radius: 6px;
        color: #4E4B66;
        display: inline-block;
        font-size: 13px;
        line-height: 20px;
        left: 30px;
        top: 30px;
        padding: 8px 10px;
        position: fixed;
    }
    body.wp-adminify-login-customizer #login #nav {
        margin-top: 0;
    }
</style>

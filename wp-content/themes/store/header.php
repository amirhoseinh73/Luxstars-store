<?php
session_start();
if ( is_user_logged_in() && isset( $_SESSION[ "is_checkout_not_logged_in" ] ) && $_SESSION[ "is_checkout_not_logged_in" ] === true ) {
	$_SESSION[ "is_checkout_not_logged_in" ] = false;

	wp_redirect( wc_get_checkout_url() );
	exit;
}
?>

<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="<?php echo get_theme_mod( 'favicon', get_template_directory_uri().'/img/logo-d.png' ); ?>">
    <meta name="theme-color" content="#FCA311"/>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<nav id="main_nav" class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div id="nav_menu" class="navbar navbar-expand-lg navbar-dark">
                <div class="container-fluid position-relative">
                    <button class="navbar-toggler border-0" type="button" onclick="openNav()">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="menu_items">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'header_1',
                            'menu_class' => 'navbar-nav px-0',
                            'depth' => 4,
                            'container' => false,
                            'menu_id' => "header_menu_1_1"
                        ));
                        ?>
                    </div>
                    <div class="collapse navbar-collapse" id="menu_items1">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'header_2',
                            'menu_class' => 'navbar-nav px-0 header-second-menu',
                            'depth' => 2,
                            'container' => false,
                            'menu_id' => "header_menu_2_1"
                        ));
                        ?>
                    </div>
                    <a id="logo_lg" class="nav-logo d-none d-lg-block" href="<?php echo home_url(); ?>">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" class="img-fluid" alt="">
                    </a>
                    <a id="logo_sm" class="nav-logo d-block d-lg-none" href="<?php echo home_url(); ?>">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/logo-mobile.png" class="img-fluid" alt="">
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
<div id="mySidenav" class="sidenav">
    <a href="javascript:void(0)" class="close-btn" onclick="closeNav()">&times;</a>
    <div class="row mx-0">
        <div class="col-auto text-right">
            <a class="logo-sidenav" href="<?php echo home_url(); ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" class="img-fluid" alt="">
            </a>
        </div>
        <div class="col-12 pr-2">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'header_2',
                'menu_class' => 'navbar-nav px-0 flex-row header-second-menu mb-4 justify-content-end',
                'depth' => 2,
                'container' => false,
                'menu_id' => "header_menu_2_2"
            ));
            wp_nav_menu(array(
                'theme_location' => 'header_1',
                'menu_class' => 'navbar-nav px-0',
                'depth' => 4,
                'container' => false,
                'menu_id' => "header_menu_1_2"
            ));
            ?>
        </div>
    </div>
</div>
<div id="fix_hover_body_nav_menu" class="fix-hover-body-nav-menu" onclick="closeNav()"></div>
<body>
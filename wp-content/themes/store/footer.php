<section id="social" class="pt-boot-18 pb-boot-18 bg-boot-2">
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-6 my-auto text-right text-color-5">
                <h4 class="fw400 font-17 mb-0">ما را در شبکه های اجتماعی دنبال کنید:</h4>
            </div>
            <div class="col-12 col-sm-6 my-auto text-left justify-content-sm-end">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'menu_social',
                    'menu_class' => 'social-footer justify-content-sm-start',
                    'depth' => 1,
                ));
                ?>
            </div>
        </div>
    </div>
</section>
<footer id="footer_parent">
    <div class="border-footer"></div>
    <div class="container pt-boot-15">
        <div id="footer" class="pt-1">
            <div class="row">
                <div id="footer_text" class="col-12 mb-3 border-bottom">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="text-dark fw600 mb-boot-22 pr-2">
                                <?php echo get_theme_mod( 'footer_title', 'History'); ?>
                            </h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <img src="<?php echo get_theme_mod( 'footer_logo', get_template_directory_uri().'/img/logo-d.png'); ?>"
                                 class="img-fluid float-right ml-4 mb-boot-2" width="100" alt="">
                            <p class="font-13 fw400 text-dark lh-2 text-justify pt-boot-8">
                                <?php echo get_theme_mod( 'footer_text', __('Text')); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div id="footer_contact" class="col-12 col-lg-7 mb-3">
                    <h4 class="text-dark fw600 mb-boot-22">
                        راه های ارتباطی
                    </h4>
                    <div class="row footer-contact">
                        <div class="col-12">
                            <i class="float-right ml-2 mb-boot-2 fas fa-map-marker-alt font-18 align-middle"></i>
                            <p class="mb-0 fw500 font-14">
                                <a target="_blank" class="text-dark" href="https://maps.google.com/?q=<?php echo get_theme_mod( 'footer_address', 'Address'); ?>"><?php echo get_theme_mod( 'footer_address', 'Address'); ?></a>
                            </p>
                        </div>
                    </div>
                    <div class="row footer-contact">
                        <div class="col-12 col-sm-6 mt-boot-8">
                            <i class="float-right ml-2 mb-boot-2 fas fa-phone font-18 align-middle"></i>
                            <p class="mb-0 fw500 font-14 lh-25">
                                <a class="text-dark" href="tel:<?php echo get_theme_mod( 'footer_tell', '031-31234567'); ?>"><?php echo get_theme_mod( 'footer_tell', '031-31234567'); ?></a>
                            </p>
                        </div>
                        <div class="col-12 col-sm-6 mt-boot-8">
                            <i class="float-right ml-2 mb-boot-2 fas fa-mobile-alt font-20 align-middle"></i>
                            <p class="mb-0 fw500 font-14 lh-25">
                                <a class="text-dark" href="tel:<?php echo get_theme_mod( 'footer_mobile', '09123456789'); ?>"><?php echo get_theme_mod( 'footer_mobile', '09123456789'); ?></a>
                            </p>
                        </div>
                        <div class="col-12 col-sm-6 mt-boot-8">
                            <i class="float-right ml-2 mb-boot-2 fas fa-envelope-open font-20 align-middle"></i>
                            <p class="mb-0 fw500 font-14 lh-25 text-uppercase">
                                <a class="text-dark" href="mailto:<?php echo get_theme_mod( 'footer_email_1', 'info@amirhoseinhasani.ir'); ?>"><?php echo get_theme_mod( 'footer_email_1', 'info@amirhoseinhasani.ir'); ?></a>
                            </p>
                        </div>
                        <div class="col-12 col-sm-6 mt-boot-8">
                            <i class="float-right ml-2 mb-boot-2 fas fa-envelope font-20 align-middle"></i>
                            <p class="mb-0 fw500 font-14 lh-25 text-uppercase">
                                <a class="text-dark" href="mailto:<?php echo get_theme_mod( 'footer_email_2', 'sale@amirhoseinhasani.ir'); ?>"><?php echo get_theme_mod( 'footer_email_2', 'sale@amirhoseinhasani.ir'); ?></a>
                            </p>
                        </div>
                    </div>
                </div>
                <div id="footer_links" class="col-12 col-lg-5 mb-3 mx-auto">
                    <h4 class="text-dark fw600 mb-boot-22">
                        دسترسی سریع
                    </h4>
                    <div class="row">
                        <div class="col-12">
                            <?php
                            wp_nav_menu(array(
                                'theme_location' => 'menu_1_footer',
                                'menu_class' => 'font-14 fw400 lh-25 hover-li-bef',
                            ));
                            ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div id="bottom_footer" class="pt-boot-8 pb-boot-8">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-6 text-md-right text-center my-auto">
                    <label class="font-11 text-color-5">
                        <i class="far fa-copyright pl-1 align-middle text-dark"></i>
                        1399-<?= wp_date('Y')?>
                        کلیه حقوق مادی و معنوی این تارنما متعلق به شرکت لوکس استار می باشد.
                    </label>
                </div>
                <div class="col-12 col-md-6 text-md-left text-center my-auto o-25">
                    <label class="font-11 text-color-5" style="direction:ltr;font-family: arial,sans-serif">
                        <i class="far fa-copyright pl-1 align-middle text-dark"></i>
                        1401 Designed By
                        <a class="text-boot-3" target="_blank" href="https://www.instagram.com/amirhoseinh73/">
                            Amirhosein Hasani
                        </a>
                    </label>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
<!-- <script src="<?php echo get_template_directory_uri(); ?>/js/jquery-3.4.1.min.js"></script> -->
<script src="<?php echo get_template_directory_uri(); ?>/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/swiper.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/amh_nj.js"></script>
</body>
</html>
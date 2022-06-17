<?php get_header();?>
    <main class="content-area background-404">
        <div class="p-5">
            <article class="page-content">
                <section id="main_content" class="row justify-content-center justify-content-xl-end">
                    <div class="col-12 col-sm-10 col-lg-9 col-xl-3">
                        <p class="text-boot-d fw500 text-center font-15 lh-25 mt-boot-12 mb-boot-12 d-none d-sm-block">
                            به نظر میرسد اشتباهی پیش آمده، لطفا دوباره به خانه برگردید و صفحه مورد نظرتان را انتخاب کنید.
                        </p>
                        <div class="row py-3 justify-content-center">
                            <div class="col-5 col-sm-4 col-xl-8 mb-boot-25">
                                <a href="<?php echo home_url(); ?>"
                                   class="btn-boot-grad-l text-center btn-block font-13 amih-hover-btn rounded-pill pb-boot-8 pt-boot-8">
                                    خانه
                                </a>
                            </div>
                            <div class="col-5 col-sm-4 col-xl-8">
                                <a href="<?php echo home_url() . '/contact-us/'; ?>"
                                   class="btn-boot-grad text-center btn-block font-13 amih-hover-btn rounded-pill pb-boot-8 pt-boot-8">
                                    تماس با ما
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
            </article>
        </div>
    </main>
<?php get_footer(); ?>
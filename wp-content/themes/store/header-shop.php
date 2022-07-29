<?php get_header(); ?>
    <header id="header" class="container-fluid position-relative">
        <div class="row">
            <div class="col-12 px-0">
                <img src="<?php echo get_template_directory_uri().'/img/footer-img.jpg'; ?>" class="img-header" alt="">
                <div class="header-caption">
                    <h1 class="fw600"><?= get_the_title()?></h1>
                    <p class="fw400 font-15">
                        لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت ...
                    </p>
                    <div class="row">
                        <div class="col-12">
                            <div class="amh_nj-breadcrumb">
                                <a class="font-14">
                                    <i class="far fa-angle-double-left pl-1 pr-1 align-middle font-13"></i>
                                </a>
                                <a href="<?php echo home_url() ?>" class="amh_nj-breadcrumb-link font-13">خانه</a>
                                <i class="far fa-angle-left pl-1 align-middle font-15 fw300"></i>
                                <a class="amh_nj-breadcrumb-text font-12"><?= get_the_title()?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main class="page-content mb-boot-45 mt-boot-22">
        <div class="container">
            <div class="row">
                <section class="col-12">
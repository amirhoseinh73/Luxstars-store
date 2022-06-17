<?php $last_blog = new WP_Query('post_type=post&order=DESC&cat=1');
if ($last_blog->post_count > 4) {
    ?>
    <div class="row">
        <div class="col-12 text-center">
            <a href="<?php echo home_url(); ?>/blog/"
               class="btn-boot-grad-l px-3 py-2 rounded-pill font-14 fw400 mt-boot-25 mb-boot-30 amh_nj-hover-2 d-inline-block">
                مطالب بیشتر
                <span class="amh_nj-hover-2__inner">
              <span class="amh_nj-hover-2__blobs">
                <span class="amh_nj-hover-2__blob"></span>
                <span class="amh_nj-hover-2__blob"></span>
                <span class="amh_nj-hover-2__blob"></span>
                <span class="amh_nj-hover-2__blob"></span>
              </span>
            </span>
                <span class="amh_nj-hover-2-svg">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1">
                <defs>
                    <filter id="goo">
                        <feGaussianBlur in="SourceGraphic" result="blur"
                                        stdDeviation="10"></feGaussianBlur>
                        <feColorMatrix in="blur" mode="matrix"
                                       values="1 0 0 0 0 0 1 0 0 0 0 0 1 0 0 0 0 0 21 -7"
                                       result="goo"></feColorMatrix>
                        <feBlend in2="goo" in="SourceGraphic" result="mix"></feBlend>
                    </filter>
                </defs>
            </svg>
            </span>
            </a>
        </div>
    </div>
    <?php
}
?>
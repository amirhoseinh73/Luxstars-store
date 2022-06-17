swiper = new Swiper('#slide_show .swiper-container', {
    effect: 'fade',
    loop: true,
    autoplay: {
        delay: 5000,
        disableOnInteraction: false,
    }
});


swiper = new Swiper('#logos .swiper-container', {
    centeredSlides: true,
    slidesPerView: 2,
    spaceBetween: 15,
    loop: true,
    freeMode: true,
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    navigation: {
        nextEl: '#nex_logos',
        prevEl: '#prev_logos',
    },
    breakpoints: {
        300: {
            slidesPerView: 2,
            spaceBetween: 15,
        },
        576: {
            slidesPerView: 3,
            spaceBetween: 25,
        },
        768: {
            slidesPerView: 4,
            spaceBetween: 35,
        },
        992: {
            slidesPerView: 5,
            spaceBetween: 45,
        },
    }
});

$(function () {
    $(document).scroll(function () {
        var nav = $("#nav_menu");
        var logo = $("#logo_lg");
        var height = parseInt($("#header").height()) - 300;
        nav.toggleClass('scrolled', $(this).scrollTop() > height);
        logo.toggleClass('scrolled-logo', $(this).scrollTop() > height);
    });
    woocomeerce_gallery();
});

function openNav() {
    document.getElementById("mySidenav").style.transform = "scaleX(1)";
    document.getElementById("fix_hover_body_nav_menu").style.display = "block";
}

function closeNav() {
    document.getElementById("mySidenav").style.transform = "scaleX(0)";
    document.getElementById("fix_hover_body_nav_menu").style.display = "none";
}

function woocomeerce_gallery() {

    $('.woocommerce div.product div.images .woocommerce-product-gallery__image:nth-child(n+2) a').off('click').on('click', function (e) {
        e.preventDefault();
        let main = $(this).parents('figure').children().eq(0);
        let src = $(this).find('img').attr('data-large_image');
        main.find('img').attr('src', src);
        main.find('img').attr('data-src', src);
        main.find('img').attr('srcset', src);
        main.find('img').attr('data-large_image', src);
    });
    $('.woocommerce div.product div.images .woocommerce-product-gallery__image:nth-child(1) a').off('click').on('click', function (e) {
        e.preventDefault();
    });
    let flag = false;
    let position = {x:0, y:0};
    let imageWidth = 0;
    let frame = {x: 0, y: 0};
    let image;
    $('.woocommerce div.product div.images .woocommerce-product-gallery__image:nth-child(1) a img').off('mousedown').on('mousedown', function (e) {
        e.preventDefault();
        flag = true;
        position.x = e.clientX;
        position.y = e.clientY;
        imageWidth = $(this).outerWidth();
        image = this;
    }).off('mousemove').on('mousemove', function (e) {
        if (!flag) return;
        move_image(e);
    }).off('mouseup').on('mouseup', function (e) {
        e.preventDefault();
        stop_image();
    });
    $('body').on('mouseup', function () {
        stop_image();
    }).on('mousemove', function (e) {
        if (!flag) return;
        move_image(e);
    });

    function move_image(e) {
        if (e.clientX > position.x + 20) {
            frame.x++;
        } else if (e.clientX < position.x - 20) {
            frame.x--;
        }
        if (e.clientY > position.y + 20) {
            frame.y++;
        } else if (e.clientY < position.y - 20) {
            frame.y--;
        }
        $(image).css({
            objectPosition: frame.x * 3 + 'px ' + frame.y * 3 + 'px'
        });
    }

    function stop_image() {
        flag = false;
        position = {x:0, y:0};
        imageWidth = 0;
        frame = {x: 0, y: 0};
        setTimeout(function () {
            $(image).css({
                objectPosition: 0 + 'px ' + 0 + 'px'
            });
        },500);
    }
}
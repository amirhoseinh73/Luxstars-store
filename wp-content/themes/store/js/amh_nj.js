const swiper1 = new Swiper('#slide_show .swiper-container', {
    effect: 'fade',
    loop: true,
    autoplay: {
        delay: 5000,
        disableOnInteraction: false,
    }
});


const swiper2 = new Swiper('#logos .swiper-container', {
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

//document ready
jQuery(function () {
    jQuery(document).scroll(function () {
        var nav = jQuery("#nav_menu");
        var logo = jQuery("#logo_lg");
        var height = parseInt(jQuery("#header").height()) - 300;
        nav.toggleClass('scrolled', jQuery(this).scrollTop() > height);
        logo.toggleClass('scrolled-logo', jQuery(this).scrollTop() > height);
    });
    woocomeerce_gallery();
    fiboSearchClasses();

    setTimeout( () => {
        woofFilters();
        woofFilters2();
    }, 500 );
    
    onDomChange( function() {
        setTimeout( () => {
            woofFilters2();
        }, 500 );
    } );

    // nav_menu_submenu_hover();

    changeQuantityWithButton()

    jQuery('body').on( 'updated_wc_div', function(){
        if ( typeof changeQuantityWithButton === "function" ) changeQuantityWithButton()
    });

    collapseWoofFilter();

});

function openNav() {
    document.getElementById("mySidenav").style.transform = "scaleX(1)";
    document.getElementById("mySidenav").style.right = "0";
    document.getElementById("fix_hover_body_nav_menu").style.display = "block";
}

function closeNav() {
    document.getElementById("mySidenav").style.transform = "scaleX(0)";
    document.getElementById("mySidenav").style.right = "-100%";
    document.getElementById("fix_hover_body_nav_menu").style.display = "none";
}

function woocomeerce_gallery() {

    jQuery('.woocommerce div.product div.images .woocommerce-product-gallery__image:nth-child(n+2) a').off('click').on('click', function (e) {
        e.preventDefault();
        let main = jQuery(this).parents('figure').children().eq(0);
        let src = jQuery(this).find('img').attr('data-large_image');
        main.find('img').attr('src', src);
        main.find('img').attr('data-src', src);
        main.find('img').attr('srcset', src);
        main.find('img').attr('data-large_image', src);
    });
    jQuery('.woocommerce div.product div.images .woocommerce-product-gallery__image:nth-child(1) a').off('click').on('click', function (e) {
        e.preventDefault();
    });
    let flag = false;
    let position = {x:0, y:0};
    let imageWidth = 0;
    let frame = {x: 0, y: 0};
    let image;
    jQuery('.woocommerce div.product div.images .woocommerce-product-gallery__image:nth-child(1) a img').off('mousedown').on('mousedown', function (e) {
        e.preventDefault();
        flag = true;
        position.x = e.clientX;
        position.y = e.clientY;
        imageWidth = jQuery(this).outerWidth();
        image = this;
    }).off('mousemove').on('mousemove', function (e) {
        if (!flag) return;
        move_image(e);
    }).off('mouseup').on('mouseup', function (e) {
        e.preventDefault();
        stop_image();
    });
    jQuery('body').on('mouseup', function () {
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
        jQuery(image).css({
            objectPosition: frame.x * 3 + 'px ' + frame.y * 3 + 'px'
        });
    }

    function stop_image() {
        flag = false;
        position = {x:0, y:0};
        imageWidth = 0;
        frame = {x: 0, y: 0};
        setTimeout(function () {
            jQuery(image).css({
                objectPosition: 0 + 'px ' + 0 + 'px'
            });
        },500);
    }
}

function fiboSearchClasses() {
    const form = document.querySelector( ".dgwt-wcas-search-form" );
    if ( ! form ) return;

    const input = form.querySelector( "input[type='search']" );
    input.classList.add( "form-control" );
    input.classList.add( "bg-boot-1" );
    input.classList.add( "rounded-pill" );
    input.classList.add( "text-right" );

    const button = form.querySelector( ".dgwt-wcas-search-submit" );
    button.classList.add( ...[
    "rounded-pill",
    "btn-boot-3",
    "btn-block",
    "amh_nj-hover-2",
    "btn-search"
    ] );
    button.classList.remove( "dgwt-wcas-search-submit" );
    button.insertAdjacentHTML( "beforeend", hoverButtonHTML() );
}

function hoverButtonHTML() {
    return `
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
                <feGaussianBlur in="SourceGraphic" result="blur" stdDeviation="10"></feGaussianBlur>
                <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0 0 1 0 0 0 0 0 1 0 0 0 0 0 21 -7" result="goo"></feColorMatrix>
                <feBlend in2="goo" in="SourceGraphic" result="mix"></feBlend>
            </filter>
        </defs>
    </svg>
    </span>
    `;
}

function woofFilters() {
    const titles = document.querySelectorAll( ".woof_sid_auto_shortcode .woof_container_inner > h4" );
    
    const text = "محصول ";

    if ( ! titles || ! titles.length ) return;

    titles.forEach( title =>  {
        const current_title = title.innerHTML.trim();
        if ( ! current_title.includes( text ) ) return
        title.innerHTML = current_title.replace( text, "" ) + " " + text;
    })
}

function woofFilters2() {
    const titles = document.querySelectorAll( ".woof_products_top_panel_ul > li" );
    
    const text = "محصول ";

    if ( ! titles || ! titles.length ) return;

    titles.forEach( title =>  {
        const current_title = title.innerHTML.trim();
        if ( ! current_title.includes( text ) ) return
        title.innerHTML = current_title.replace( text, "" );
    });

    const text_2 = "Clear All";
    const text_3 = "price range";

    titles.forEach( title => {
        const current_title = title.innerHTML.trim();
        if ( ! current_title.includes( text_3 ) ) return
        title.innerHTML = current_title.replace( text_3, "فیلتر قیمت" );
    } );

    titles.forEach( title => {
        const current_title = title.innerHTML.trim();
        if ( ! current_title.includes( text_2 ) ) return
        title.innerHTML = current_title.replace( text_2, "حذف همه" );
    } );
 }

(function (window) {
    var last = +new Date();
    var delay = 100; // default delay

    // Manage event queue
    var stack = [];

    function callback() {
        var now = +new Date();
        if (now - last > delay) {
            for (var i = 0; i < stack.length; i++) {
                stack[i]();
            }
            last = now;
        }
    }

    // Public interface
    var onDomChange = function (fn, newdelay) {
        if (newdelay) delay = newdelay;
        stack.push(fn);
    };

    // Naive approach for compatibility
    function naive() {

        var last = document.getElementsByTagName('*');
        var lastlen = last.length;
        var timer = setTimeout(function check() {

            // get current state of the document
            var current = document.getElementsByTagName('*');
            var len = current.length;

            // if the length is different
            // it's fairly obvious
            if (len != lastlen) {
                // just make sure the loop finishes early
                last = [];
            }

            // go check every element in order
            for (var i = 0; i < len; i++) {
                if (current[i] !== last[i]) {
                    callback();
                    last = current;
                    lastlen = len;
                    break;
                }
            }

            // over, and over, and over again
            setTimeout(check, delay);

        }, delay);
    }

    //
    //  Check for mutation events support
    //

    var support = {};

    var el = document.documentElement;
    var remain = 3;

    // callback for the tests
    function decide() {
        if (support.DOMNodeInserted) {
            window.addEventListener("DOMContentLoaded", function () {
                if (support.DOMSubtreeModified) { // for FF 3+, Chrome
                    el.addEventListener('DOMSubtreeModified', callback, false);
                } else { // for FF 2, Safari, Opera 9.6+
                    el.addEventListener('DOMNodeInserted', callback, false);
                    el.addEventListener('DOMNodeRemoved', callback, false);
                }
            }, false);
        } else if (document.onpropertychange) { // for IE 5.5+
            document.onpropertychange = callback;
        } else { // fallback
            naive();
        }
    }

    // checks a particular event
    function test(event) {
        el.addEventListener(event, function fn() {
            support[event] = true;
            el.removeEventListener(event, fn, false);
            if (--remain === 0) decide();
        }, false);
    }

    // attach test events
    if (window.addEventListener) {
        test('DOMSubtreeModified');
        test('DOMNodeInserted');
        test('DOMNodeRemoved');
    } else {
        decide();
    }

    // do the dummy test
    var dummy = document.createElement("div");
    el.appendChild(dummy);
    el.removeChild(dummy);

    // expose
    window.onDomChange = onDomChange;
})(window);

function nav_menu_submenu_hover() {
    const all_li = document.querySelectorAll( "li.menu-item" );
    if ( ! all_li ) return

    all_li.forEach( li => {
        li.addEventListener( "mouseenter", function() {
            const ul = this.closest( "ul.sub-menu" )
            if ( ! ul ) return
            const li = ul.closest( "li.menu-item-has-children" )
            if ( ! li ) return

            li.classList.add( "active-parent" );
        } )
        li.addEventListener( "mouseleave", function() {
            this.classList.remove( "active-parent" );
        } )
    } );

    const all_ul_sub_menu = document.querySelectorAll( "ul.sub-menu" );
    if ( ! all_ul_sub_menu ) return

    all_ul_sub_menu.forEach( ul => {
        ul.addEventListener( "mouseenter", function() {
            const li = this.closest( "li.menu-item-has-children" )
            if ( ! li ) return

            li.classList.add( "active-parent" );
        } )
    } );
}

function changeQuantityWithButton() {
    const up = document.querySelectorAll( ".btn-cart-quantity-up" )
    const down = document.querySelectorAll( ".btn-cart-quantity-down" )

    if( ! up || ! down || up.length < 1 || down.length < 1 ) return

    down.forEach( btn => {
        const checkActivateEvent = btn.getAttribute( "data-quantity-event" )
        if ( checkActivateEvent ) return
        
        btn.addEventListener( "click", updateInputQuantityDown )
        btn.setAttribute( "data-quantity-event", "true" )
    } )

    up.forEach( btn => {
        const checkActivateEvent = btn.getAttribute( "data-quantity-event" )
        if ( checkActivateEvent ) return

        btn.addEventListener( "click", updateInputQuantityUp )
        btn.setAttribute( "data-quantity-event", "true" )
    } )

    function updateInputQuantityDown( e ) {
        updateInputQuantity.call( e.target, "down" )
    }

    function updateInputQuantityUp( e ) {
        updateInputQuantity.call( e.target, "up" )
    }

    function updateInputQuantity( which = "down" ) {
        const parent = this.closest( ".quantity-parent-icon" )
        if ( ! parent ) return
    
        const input = parent.querySelector( "input[type='number']" )
        if ( ! input ) return
    
        let value = +input.value
        let max = input.max
        let min = input.min
    
        if ( ! min ) min = 0
        if ( ! max ) max = 1000
    
        if ( which === "down" ) {
            if ( value <= min ) return
    
            input.value = --value
        }
    
        if ( which === "up" ) {
            if ( value >= max ) return
    
            input.value = ++value
        }
    
        const submitCart = document.querySelector( "[name='update_cart']" )
        if ( ! submitCart ) return
        submitCart.removeAttribute( "disabled" )
        submitCart.removeAttribute( "aria-disabled" )
    }
}

function collapseWoofFilter() {
    const filterBtn = document.querySelector( ".woof_sid_auto_shortcode.woof > div:first-of-type" )

    if ( ! filterBtn ) return

    filterBtn.addEventListener( "click", function() {
        const section = document.querySelector( ".woof_sid_auto_shortcode.woof .woof_redraw_zone" )

        if ( ! section ) return

        section.classList.toggle( "show" )
    } )
} 
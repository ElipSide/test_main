// меню гамбургера
$(function() {
    const btnMenu = document.querySelector('.js-hamb');
    const menu = document.querySelector('.b-header__top-nav nav');
    const toggleMenu = function() {
        menu.classList.toggle('active');
    }

    btnMenu.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleMenu();
    });

    document.addEventListener('click', function(e) {
        const target = e.target;
        const its_menu = target == menu || menu.contains(target);
        const its_btnMenu = target == btnMenu;
        const menu_is_active = menu.classList.contains('active');

        if (!its_menu && !its_btnMenu && menu_is_active) {
            toggleMenu();
        }
    });
});

//слайдер
$(function() {
    $('.js-slider').slick({
        infinite: false,
        dots: true,
        arrows: false,
        speed: 300,
        slidesToShow: 1,
        slidesToScroll: 1,
        adaptiveHeight: true
    });
});

//галерея в модальном окне
$(function() {
    $('.popup-gallery').magnificPopup({
        delegate: 'a',
        type: 'image',
        tLoading: 'Загрузка изображения #%curr%...',
        mainClass: 'mfp-img-mobile',
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0,1] // Will preload 0 - before current, and 1 after the current image
        }
    });
});

//кастомный скролл
$(function() {
    var params = {
        // объект параметров плагина
        setHeight: 900
    };

    // Инициализируем при загрузки DOM
    initScrollbar($('.js-materials-list'), params);

    // Инициализируем/разгрушаем по изменению окна браузера
    $(window).on('resize', function() {
        initScrollbar($('.js-materials-list'), params);
    });

    function initScrollbar($selector, options) {
        // Если ширина окна меньше чем 992 px
        if ($(window).width() < 991) {
            // Если на этом селекторе уже был инициализирован плагин, то разрушим его
            // Если нет, то ничего не делаем
            if ($selector.data('mCS')) $selector.mCustomScrollbar('destroy');
        } else {
            // Если ширина окна больше 992 px, То инициализируем плагин
            $selector.mCustomScrollbar(options || {});
        }
    }
});

// взрываем модальное окно
$(function() {
    var startWindowScroll = 0;
    $('.js-modal').magnificPopup({
        type: 'inline',
        midClick: true,
        mainClass: 'b-modal-default',
        removalDelay: 300,
        fixedContentPos: true,
        fixedBgPos: true,
        overflowY: 'auto',
        closeBtnInside: true,
        callbacks: {
            beforeOpen: function () {
                startWindowScroll = $(window).scrollTop();
            },
            open: function () {
                if ($('.mfp-content').height() < $(window).height()) {
                    $('body').on('touchmove', function (e) {
                        e.preventDefault();
                    });
                }
            },
            close: function () {
                $(window).scrollTop(startWindowScroll);
                $('body').off('touchmove');
            }
        }
    });
});

// маскa
$(function() {
    $('[type="tel"]').inputmask({
        "mask": "+7 (999) 999-99-99",
        "clearIncomplete": true,
        autoUnmask: true,
        removeMaskOnSubmit: false,
        showMaskOnHover: false,
    });
});

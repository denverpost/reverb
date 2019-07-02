jQuery(function($) {

    var mySwiper = new Swiper ('.swiper-container', {
        // Optional parameters
        direction: 'horizontal',

        // Navigation arrows
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },

    })

    AOS.init({
        duration: 400,
        easing: 'ease-in-out-back'
    });





});


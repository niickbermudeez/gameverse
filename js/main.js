var swiper = new Swiper(".swiper-container", {
    effect: "coverflow",
    grabCursor: true,
    centeredSlides: true,
    speed: 600,
    preventClicksPropagation: true,
    slidesPerView: "auto",
    loop:true,
    coverflowEffect: {
        rotate: 0,
        stretch: 80,
        depth: 200,
        modifier: 1,
        slideShadows: false,
    },
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    on: {
        click: function (swiper, event) {
            swiper.slideTo(swiper.clickedIndex);
        },
    },
});

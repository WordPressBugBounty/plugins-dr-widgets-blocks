function initializeRecipePostsCarouselThree( $scope, $ ) {
	const swiperNavBtnHeight = $scope.find(".dr-swiper-next").outerHeight();
	const swiperNav = $scope.find(".dr-swiper-navigation");
	swiperNav.css("--nav-height", swiperNavBtnHeight + "px");

    // swiper

	let sliderContainer = document.querySelectorAll(
		".dr-recipe-carousel-3 .swiper"
	);
	if (sliderContainer.length > 0) {

		sliderContainer.forEach((slider) => {
			if(!slider.swiper){
				let carouselID = slider.dataset.id;
				let options = Object.assign(
					{
						navigation: {
							nextEl:
								`.dr-recipe-carousel-3 #dr_swiper-next-${carouselID}.dr-swiper-next`,
							prevEl:
								`.dr-recipe-carousel-3 #dr_swiper-prev-${carouselID}.dr-swiper-prev`,
						},
						pagination: {
							el: `.slider-${carouselID}-pagination`,
							clickable: true,
						},
					},
					slider.dataset.swiper ? JSON.parse(slider.dataset.swiper) : {}
				);
				new Swiper(slider, options);
			}
		});
	}
}

jQuery(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
        "frontend/element_ready/dr-recipe-posts-carousel-three.default",
        initializeRecipePostsCarouselThree
    );
});

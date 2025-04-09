function initializePostsSliders( $scope, $ ) {
    
    // swiper
	let sliderContainer = document.querySelectorAll(
		".dr-widgetBlock_recipe-carousel .swiper"
	);
	if (sliderContainer.length > 0) {
		sliderContainer.forEach((slider) => {
			let carouselID = slider.dataset.id;
			let options = Object.assign(
				{
					navigation: {
						nextEl:
							`.dr-widgetBlock_recipe-carousel #dr_swiper-next-${carouselID}.dr_swiper-next`,
						prevEl:
							`.dr-widgetBlock_recipe-carousel #dr_swiper-prev-${carouselID}.dr_swiper-prev`,
					},
				},
				slider.dataset.swiper ? JSON.parse(slider.dataset.swiper) : {}
			);
			new Swiper(slider, options);
		});
	}
}

jQuery(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
        "frontend/element_ready/dr-recipe-posts-carousel.default",
        initializePostsSliders
    );
});

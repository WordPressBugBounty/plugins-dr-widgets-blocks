(() => {
	// siblings
	const siblings = (el) => {
		let parent = el.parentNode;
		let childrens = parent.children;
		let r = [];
		for (let i = 0; i < childrens.length; i++) {
			r.push(childrens[i]);
		}
		return r;
	};

	// swiper
	let tabSliderContainer = document.querySelectorAll(
		".dr_tab .dr_recipe-slider"
	);
	if (tabSliderContainer.length > 0) {
		tabSliderContainer.forEach((slider) => {
			let carouselID = slider.dataset.id;
			new Swiper(slider, {
				navigation: {
					nextEl:
						`.dr_tab #dr_swiper-next-${carouselID}.dr_swiper-next`,
					prevEl:
						`.dr_tab #dr_swiper-prev-${carouselID}.dr_swiper-prev`,
				},
			});
		});
	}

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

	// tabs
	const showTabContent = (el) => {
		let targetTabContent = el.getAttribute("aria-controls");
		targetTabContent = document.getElementById(targetTabContent);
		let s = siblings(targetTabContent);
		s.forEach((item) => {
			item.classList.remove("dr_active");
		});
		targetTabContent.classList.add("dr_active");
	};

	let drTabsTitle = document.querySelectorAll(".dr_tab-title");
	drTabsTitle.forEach((title) => {
		title.onclick = (e) => {
			let currentTab = e.target;
			let s = currentTab.closest(".dr_tab-nav").querySelectorAll("li");
			s.forEach((item) => {
				item.querySelector(".dr_tab-title").classList.remove(
					"dr_active"
				);
			});
			currentTab.classList.add("dr_active");
			showTabContent(currentTab);
		};
	});

	// overflow menu
	function calcWidth() {
		var tabs = document.querySelectorAll(".dr_tab-nav");
		if (tabs) {
			tabs.forEach(function (tab) {
				var navContainer = tab.querySelector(".dr_tab-nav-container");
				var navigation = tab.querySelector(".dr_tab-navigation");
				var navWidth = 0;
				var more = tab.querySelector(".dr_tab-dropdown");
				var morMenu = more.querySelector("ul");
				var moreMenuItems = morMenu.querySelectorAll("li");
				var navItems = navigation.children;
				navItems = Array.from(navItems).filter(function (li) {
					return !li.classList.contains("dr_tab-dropdown");
				});
				navItems.forEach(function (navItem) {
					navWidth += navItem.offsetWidth;
				});

				var availabeSpace = navContainer.clientWidth;
				if (navWidth + 200 > availabeSpace) {
					var lastItem = navItems[navItems.length - 1];
					var lastOuterWidth = lastItem.offsetWidth;
					lastItem.setAttribute("data-width", lastOuterWidth);
					morMenu.prepend(lastItem);
				} else {
					var firstMoreItem = moreMenuItems[0];
					if (firstMoreItem) {
						navigation.append(firstMoreItem);
					}
				}

				var moreTotal = more.querySelectorAll("li").length;
				if (moreTotal > 0) {
					more.classList.remove("hidden");
				} else {
					more.classList.add("hidden");
				}
			});
		}
	}

	function onLoadCalc() {
		var tabs = document.querySelectorAll(".dr_tab-nav");
		if (tabs) {
			tabs.forEach(function (tab) {
				var navContainer = tab.querySelector(".dr_tab-nav-container");
				var navigation = tab.querySelector(".dr_tab-navigation");
				var navWidth = 0;
				var more = tab.querySelector(".dr_tab-dropdown");
				var morMenu = more.querySelector("ul");
				var moreMenuItems = morMenu.querySelectorAll("li");
				var navItems = navigation.children;
				navItems = Array.from(navItems).filter(function (li) {
					return !li.classList.contains("dr_tab-dropdown");
				});
				navItems.forEach(function (navItem) {
					navWidth += navItem.offsetWidth;
					var availabeSpace = navContainer.clientWidth;
					if (navWidth + 150 > availabeSpace) {
						morMenu.append(navItem);
					} else {
						var firstMoreItem = moreMenuItems[0];
						if (firstMoreItem) {
							navigation.insertBefore(firstMoreItem, more);
						}
					}
				});

				var moreTotal = more.querySelectorAll("li").length;
				if (moreTotal > 0) {
					more.classList.remove("hidden");
				} else {
					more.classList.add("hidden");
				}
			});
		}
	}

	onLoadCalc();
	window.addEventListener("resize", calcWidth);
})();

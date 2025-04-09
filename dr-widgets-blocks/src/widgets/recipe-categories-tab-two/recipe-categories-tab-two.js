function initializeRecipeTabTwo($scope, $) {
    // siblings
    const siblings = (el) => {
        let parent = el.parentNode;
        let childrens = parent.children;
        let r = []
        for (let i = 0; i < childrens.length; i++) {
            r.push(childrens[i]);
        }
        return r;
    }

    // swiper
    const initSwiperForTab = (tabpanel) => {
        let slider = tabpanel.querySelector('.dr_recipe-slider-tab-2');
        if (slider && !slider.swiper) {
            let carouselID = slider.dataset.id;
            // Parse the swiperOptions
            let swiperOptions;
            try {
                swiperOptions = JSON.parse(slider.dataset.swiperOptions);
            } catch (error) {
                console.error("Invalid JSON in data-swiper-options:", error);
                swiperOptions = {}; // Fallback or handle the error as needed
            }
            swiperOptions.navigation = {
                nextEl:
                    `.dr_tab #dr_swiper-next-${carouselID}.dr-swiper-next`,
                prevEl:
                    `.dr_tab #dr_swiper-prev-${carouselID}.dr-swiper-prev`,
            }
			new Swiper( slider, swiperOptions );
        }
    }

    // destroy swiper
    const destroySwiperForTab = (tabpanel) => {
        let swiperContainer = tabpanel.querySelector('.dr_recipe-slider-tab-2');
        if (swiperContainer && swiperContainer.swiper) {
            swiperContainer.swiper.destroy(true, true);
            swiperContainer.swiper = null;
        }
    }

    let tabSliderContainer = document.querySelectorAll(
		".dr_tab-content.dr_active"
	);

	if (tabSliderContainer) {
        tabSliderContainer.forEach((recipeTab) => {
            initSwiperForTab(recipeTab);
        });
	}

    // tabs
    const showTabContent = (el) => {
        let targetTabContent = el.getAttribute("aria-controls");
        targetTabContent = document.getElementById(targetTabContent);
        let s = siblings(targetTabContent);
        s.forEach(item => {
            item.classList.remove("dr_active");
            destroySwiperForTab(item)
        })
        targetTabContent.classList.add("dr_active");
        initSwiperForTab(targetTabContent)
    };

    let drTabsTitle = document.querySelectorAll(".dr_tab-title");
    drTabsTitle.forEach((title) => {
        title.onclick = (e) => {
            let currentTab = e.target;
            let s = currentTab.closest('.dr_tab-nav').querySelectorAll('li');
            s.forEach(item => {
                item.querySelector('.dr_tab-title').classList.remove("dr_active");
            })
            currentTab.classList.add("dr_active");
            showTabContent(currentTab)
        };
    });

     // overflow menu
     function calcWidth() {
        var tabs = document.querySelectorAll('.dr_tab-nav');
        if (tabs) {
            tabs.forEach(function (tab) {
                var navContainer = tab.querySelector('.dr_tab-nav-container');
                var navigation = tab.querySelector('.dr_tab-navigation');
                var navWidth = 0;
                var more = tab.querySelector('.dr_tab-dropdown');
                var morMenu = more.querySelector('ul');
                var moreMenuItems = morMenu.querySelectorAll('li');
                var navItems = navigation.children
                const itemGap = window.getComputedStyle(navigation).getPropertyValue('--nav-gap').slice(0, -2);

                navItems = Array.from(navItems).filter(function (li) {
                    return !li.classList.contains('dr_tab-dropdown')
                })
                navItems.forEach(function (navItem) {
                    navWidth += navItem.offsetWidth + itemGap / 2;
                })

                var availabeSpace = navContainer.clientWidth;
                if (navWidth + 200 > availabeSpace) {
                    var lastItem = navItems[navItems.length - 1];
                    var lastOuterWidth = lastItem.offsetWidth;
                    lastItem.setAttribute('data-width', lastOuterWidth);
                    morMenu.prepend(lastItem);
                } else {
                    var firstMoreItem = moreMenuItems[0];
                    if (firstMoreItem) {
                        navigation.append(firstMoreItem);
                    }
                }

                var moreTotal = more.querySelectorAll('li').length;
                if (moreTotal > 0) {
                    more.classList.remove('hidden');
                } else {
                    more.classList.add('hidden');
                }
            })
        }
    }

    function onLoadCalc() {
        var tabs = document.querySelectorAll('.dr_tab-nav');
        if (tabs) {
            tabs.forEach(function (tab) {
                var navContainer = tab.querySelector('.dr_tab-nav-container');
                var navigation = tab.querySelector('.dr_tab-navigation');
                var navWidth = 0;
                var more = tab.querySelector('.dr_tab-dropdown');
                var morMenu = more.querySelector('ul');
                var moreMenuItems = morMenu.querySelectorAll('li');
                var navItems = navigation.children
                const itemGap = window.getComputedStyle(navigation).getPropertyValue('--nav-gap').slice(0, -2);
                navItems = Array.from(navItems).filter(function (li) {
                    return !li.classList.contains('dr_tab-dropdown')
                })

                navItems.forEach(function (navItem) {
                    navWidth += navItem.offsetWidth + itemGap / 2;
                    var availabeSpace = navContainer.clientWidth;
                    if (navWidth + 150 > availabeSpace) {
                        morMenu.append(navItem);
                    }  else {
                        var firstMoreItem = moreMenuItems[0];
                        if (firstMoreItem) {
                            if (navigation.contains(more)) {
                                navigation.insertBefore(firstMoreItem, more);
                            }
                        }
                    }
                })

                var moreTotal = more.querySelectorAll('li').length;
                if (moreTotal > 0) {
                    more.classList.remove('hidden');
                } else {
                    more.classList.add('hidden');
                }
            })
        }
    }

    onLoadCalc();
    window.addEventListener('resize', calcWidth);
}

jQuery(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
        "frontend/element_ready/dr-recipe-categories-tab-two.default",
        initializeRecipeTabTwo
    );
});


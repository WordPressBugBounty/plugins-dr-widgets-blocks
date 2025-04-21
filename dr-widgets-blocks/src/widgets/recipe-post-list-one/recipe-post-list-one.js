"use strict";

(function ($) {
    jQuery(window).on('elementor/frontend/init', () => {
        class PaginationHandler extends elementorModules.frontend.handlers.Base {
            onInit() {
                this.initPagination();
            }

            initPagination() {
                let containerID = document.querySelectorAll('.elementor-widget-dr-recipe-post-list-one');
                containerID.forEach((container) => {
                    let $container = $(container);
                    let dataID     = $container.attr('data-id');
                    let elementID  = $container.find('.dr-widget-wrapper').attr('id');

                    if (dataID === elementID) {
                        let dataSettings   = JSON.parse($container.attr('data-settings'));
                        let paginationType = dataSettings['paginationType'] ?? 'number';
                        let taxonomy       = dataSettings['taxonomy'] ?? '';
                        let terms          = taxonomy ? dataSettings[`${taxonomy}_term_id`] : '';
                        let maxPages       = parseInt(dataSettings['paged'] ?? 1);

                        // If a .dr-max-pages element is found later, we’ll update this value
                        const $maxPagesEl = $container.find('.dr-max-pages');
                        if ($maxPagesEl.length) {
                            const pagesFromDom = parseInt($maxPagesEl.data('max-pages'));
                            if (!isNaN(pagesFromDom)) {
                                maxPages = pagesFromDom;
                            }
                        }

                        if (paginationType === 'number') {
                            let pageNumbersSelector = '#' + elementID + ' .page-numbers';
                            if ($(pageNumbersSelector).length > 0) {
                                $(document).on('click', pageNumbersSelector, (e) => {
                                    e.preventDefault();
                                    let $link = $(e.target).closest('a');
                                    let hrefValue = $link.attr('href');
                                    if (hrefValue) {
                                        const match = hrefValue.match(/[?&]paged=(\d+)/);
                                        let pagedValue = match && match[1] ? parseInt(match[1]) : 1;
                                        this.fetchRecipes(dataSettings, taxonomy, terms, pagedValue, elementID, false);
                                    }
                                });
                            }
                        }
                        // Handle Load More pagination  
                        if (paginationType === 'loadMore') {
                            let loadMoreBtnSelector = '#' + elementID + ' .dr-widget-pagination__btn';

                            $(document).on('click', loadMoreBtnSelector, (e) => {
                                e.preventDefault();

                                const $btn = $(e.currentTarget); 
                                if ($btn.prop('disabled')) return;

                                let currentPage = parseInt($container.attr('data-current-page') ?? 1);

                                this.fetchRecipes(dataSettings, taxonomy, terms, currentPage + 1, elementID, true, () => {
                                    currentPage++;
                                    $container.attr('data-current-page', currentPage);
                                    if (currentPage >= maxPages) {
                                        $btn
                                            .text('No More Recipes')
                                            .addClass('dr-btn-disabled') // optional class
                                            .prop('disabled', true)
                                            .css({
                                                'pointer-events': 'none',
                                                'opacity': '0.6',
                                                'cursor': 'not-allowed'
                                            });
                                    }
                                });
                            });
                        }
                    }
                });
            }

            fetchRecipes(dataSettings, taxonomy, terms, pagedValue, elementID, append, callback = () => {}) {
                // Store frequently used jQuery selectors
                const $container      = $('#' + elementID);
                const $spinner        = $container.find('.dr-widget-pagination__spinner');
                const $recipePostList = $container.find('.dr-widgetBlock_row');

                // Add loading states                
                $container.addClass('animate-pulse');
                $spinner.addClass('animate-spin').prop('disabled', true);
                $.ajax({
                    url: delicious_recipes.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'dr_widgets_blocks_recipe_post_list_one',
                        nonce: recipePostListOne.nonce,
                        paged: pagedValue,
                        layout: dataSettings['layout'],
                        postsPerPage: dataSettings['postsPerPage'],
                        exclude: dataSettings['exclude'] ?? '',
                        orderby: dataSettings['orderby'],
                        order: dataSettings['order'],
                        offset: dataSettings['offset'],
                        filterBy: dataSettings['filterBy'],
                        taxonomy: taxonomy,
                        terms: terms ?? '',
                        all_taxonomy: dataSettings['all_taxonomy'] ?? 'no',
                        all_term_id: dataSettings['all_term_id'] ?? [],
                        showPagination: dataSettings['showPagination'],
                        paginationType: dataSettings['paginationType'],
                        imageSize: dataSettings['imageSize'],
                        imageCustomSize: dataSettings['imageCustomSize'],
                        showTitle: dataSettings['showTitle'],
                        headingTag: dataSettings['headingTag'],
                        showTotalTime: dataSettings['showTotalTime'],
                        showDifficulty: dataSettings['showDifficulty'],
                        showRecipeKeys: dataSettings['showRecipeKeys'],
                        showExcerpt: dataSettings['showExcerpt'],
                        showRating: dataSettings['showRating'] ?? 'no',
                        showComment: dataSettings['showComment'] ?? 'no',
                        showCategory: dataSettings['showCategory'],
                        excerptLength: dataSettings['excerptLength'],
                        imageAlignment: dataSettings['imageAlignment'] ?? 'left',
                        separator: dataSettings['separator'] ?? 'dot',
                        showBookmark: dataSettings['showBookmark'] ?? 'no',
                        prevText: dataSettings['prevText'] ?? 'Previous',
                        nextText: dataSettings['nextText'] ?? 'Next',
                        loadText: dataSettings['loadText'] ?? 'Load More',
                    },
                    success: function (response) {
                        // Remove loading states                        
                        $container.removeClass('animate-pulse');
                        $spinner.removeClass('animate-spin');
                        if (response.success ?? true) {
                            if (append) {
                                // Create a temporary div to parse the response HTML                                
                                const tempDiv = document.createElement('div');
                                tempDiv.innerHTML = response;
                                // Find all .dr-post-list__recipe elements
                                const columns = tempDiv.querySelectorAll('.dr-post-list__recipe');
                                // Append only the .dr-post-list__recipe elements
                                columns.forEach(column => {
                                    $recipePostList.append(column);
                                });
                            } else {
                                $container.html(response);
                            }
                            // Replace the current content with new recipes
                            callback(); // Call the callback after success
                        } 
                    },
                    error: function (error) {
                        // Remove the loading class
                        $container.removeClass('animate-pulse');
                        $spinner.removeClass('animate-spin');
                    }
                });
            }
        }

        elementorFrontend.elementsHandler.attachHandler('dr-recipe-post-list-one', PaginationHandler);
    });
})(jQuery);
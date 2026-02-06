(function ($) {

    "use strict";

    let elementIndex = null;
    let currentAjaxRequest;

    function addLibraryButton(elementorPreview) {
        const libraryButton = '<div id="delisho-cw-layout-btn" class="elementor-add-section-area-button transform-scale">' + delishoAdmin.btnIcon + '</div>';
        const elementorAddSection = $("#tmpl-elementor-add-section");
        const elementorAddSectionText = elementorAddSection.text();
        const updatedText = elementorAddSectionText.replace('<div class="elementor-add-section-drag-title', libraryButton + '<div class="elementor-add-section-drag-title');
        elementorAddSection.text(updatedText);

        $(elementorPreview).on('click', '.elementor-editor-element-settings .elementor-editor-element-add', function () {
            const modelID = $(this).closest('.elementor-element').data('model-cid');

            // Find element index when user tries to append new element between sections
            if (window.elementor.elements.models.length) {
                $.each(window.elementor.elements.models, function (index, model) {
                    if (modelID === model.cid) {
                        elementIndex = index;
                    }
                });
            }
        });
    }

    function getTemplatesModal(elementorPreview) {

        // Popup
        elementorPreview.on('click', '#delisho-cw-layout-btn', function () {

            const body = elementorPreview.find('body');
            const html = elementorPreview.find('html');

            if (elementorPreview.find('.cw-template-modal').length == 0) {

                body.append(`
                <div class="cw-template-modal-overlay">
                    <div class="cw-template-modal">
                        <div class="cw-header">
                            <div class="cw-header-left">
                                <span class="cw-header-title">
                                    <svg width="143" height="49" viewBox="0 0 143 49" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M18.9764 0H9.98931C4.47236 0 0 4.47237 0 9.98931V18.3724C0 20.3679 1.54454 22.0688 3.52201 22.3359C6.23195 22.7018 9.66645 23.1656 12.6396 23.1688C12.6588 23.1688 12.6767 23.1592 12.6879 23.1436C12.6942 23.1348 12.7006 23.1259 12.7056 23.1183C12.8724 22.8761 13.105 22.7298 13.3628 22.7298C13.3779 22.7298 13.3931 22.731 13.4082 22.7323C13.4234 22.7335 13.4386 22.7348 13.4537 22.7348C13.4945 22.7348 13.5358 22.7248 13.5634 22.6948C13.7502 22.4923 13.9808 22.3646 14.232 22.3481C14.2691 22.3456 14.3063 22.3488 14.3434 22.3513C14.3624 22.3525 14.3813 22.3538 14.399 22.3563C14.4142 22.3588 14.4281 22.3614 14.442 22.3639C14.4673 22.3685 14.4923 22.3552 14.5032 22.3319C14.9007 21.4798 15.5958 20.7166 16.5018 20.0954C17.9677 19.0912 19.9998 18.4704 22.2391 18.4704C24.4763 18.4704 26.5066 19.09 27.9722 20.0926C27.9748 20.0944 27.9764 20.0973 27.9764 20.1005C27.9764 20.1036 27.9779 20.1066 27.9806 20.1084C29.4541 21.1124 30.3622 22.5041 30.3622 24.0419C30.3622 25.5811 29.4473 26.974 27.9764 27.9782C26.5104 28.9825 24.4784 29.6032 22.2391 29.6032C19.9998 29.6032 17.9728 28.9825 16.5018 27.9782C15.589 27.3573 14.8902 26.5874 14.4942 25.7226C14.4887 25.7107 14.4758 25.7041 14.463 25.7072C14.421 25.7174 14.3775 25.7211 14.3342 25.7221C14.3193 25.7224 14.304 25.7224 14.2878 25.7224C14.0136 25.7224 13.7615 25.59 13.5604 25.3705C13.5347 25.3425 13.4921 25.3295 13.4543 25.3337C13.4514 25.3341 13.4523 25.3341 13.4495 25.3346C13.4251 25.3389 13.3965 25.3389 13.3678 25.3389C13.1237 25.3389 12.9023 25.2031 12.7378 24.9873C12.7193 24.963 12.6997 24.9393 12.6837 24.9132C12.6786 24.9048 12.6694 24.8998 12.6595 24.8998C9.64853 24.9016 6.26988 25.4024 3.58693 25.8001C1.57335 26.0985 0 27.842 0 29.8776V38.0843C0 43.6012 4.47237 48.0736 9.98931 48.0736H18.9764C32.2516 48.0736 43.0132 37.3119 43.0132 24.0368C43.0132 10.7616 32.2516 0 18.9764 0Z" fill="#F06432"/>
                                        <path d="M135.133 21.3203C138.945 21.3203 142.011 24.4116 142.011 28.2243C142.011 32.0111 138.945 35.1024 135.133 35.1024C131.32 35.1024 128.255 32.0111 128.255 28.2243C128.255 24.4116 131.32 21.3203 135.133 21.3203ZM138.611 28.2243C138.611 24.4632 137.065 21.4491 135.133 21.4491C133.201 21.4491 131.629 24.4632 131.629 28.2243C131.629 31.9596 133.201 34.9994 135.133 34.9994C137.065 34.9994 138.611 31.9596 138.611 28.2243Z" fill="#292929"/>
                                        <path d="M113.997 31.3668C113.997 27.4769 113.997 23.5612 113.997 19.6713C113.997 19.1561 113.842 17.6619 112.425 17.0952C114.048 16.142 115.671 15.2146 117.32 14.2615C117.32 17.5589 117.32 20.8563 117.32 24.1537C118.968 23.1233 120.823 21.8094 122.755 21.7837C124.352 21.7579 125.898 22.608 125.898 25.1326C125.898 27.1935 125.898 29.2801 125.898 31.3668C125.898 32.7321 125.975 34.4581 127.701 34.6384H120.772C122.472 34.4581 122.575 32.7321 122.575 31.3668C122.575 29.3574 122.575 27.3481 122.575 25.3645C122.601 23.9734 122.085 22.4792 120.437 22.7368C119.406 22.8914 118.196 23.7415 117.32 24.2825V31.3668C117.32 32.7321 117.397 34.4581 119.123 34.6384H112.193C113.893 34.4581 113.997 32.7321 113.997 31.3668Z" fill="#292929"/>
                                        <path d="M104.685 21.3459C106.411 21.2171 108.06 21.8869 109.425 22.8916L107.596 25.9829C107.777 22.8916 106.411 21.6035 105.072 21.552C104.041 21.5262 103.037 22.1703 102.702 23.6644C102.367 28.0695 110.559 25.751 110.894 30.6972C111.126 33.8658 108.137 35.0765 105.432 35.1023C103.526 35.1023 101.208 34.4067 99.765 33.3248L101.465 29.4091C101.594 32.011 102.856 34.8189 105.587 34.8704C108.137 34.8962 109.554 31.8049 106.901 30.3365C104.608 29.0742 100.383 29.2803 100.28 25.5192C100.332 22.9431 102.367 21.5262 104.685 21.3459Z" fill="#292929"/>
                                        <path d="M96.6063 21.7839V31.367C96.6063 32.7323 96.6836 34.4583 98.4095 34.6386H91.4541C93.1801 34.4583 93.2831 32.7323 93.2831 31.367V27.1937C93.2831 26.6785 93.1285 25.1843 91.7117 24.6176C93.3346 23.6644 94.9576 22.737 96.6063 21.7839ZM88.8265 14.4677C89.4447 14.0813 90.1918 13.5919 90.8616 13.3343C95.9622 11.4022 98.8217 17.6621 96.2971 20.7277C95.9622 21.2171 95.4213 21.5263 94.803 21.5263C93.7983 21.5263 92.974 20.7019 92.974 19.6972C92.974 18.6668 93.7983 17.8682 94.803 17.8682C95.8077 17.8682 96.632 18.6668 96.632 19.6972C96.632 19.7745 96.632 19.826 96.632 19.9033C97.5337 17.997 96.9927 15.4982 95.4213 14.1329C94.3393 13.1797 92.7679 12.716 90.9389 13.4373C90.3464 13.6692 89.7281 14.0556 89.1871 14.3647V31.367C89.1871 32.7323 89.2644 34.4583 90.9904 34.6386H84.0607C85.7609 34.4583 85.8639 32.7323 85.8639 31.367C85.8639 27.477 85.8639 23.5614 85.8639 19.6715C85.8639 19.1562 85.7094 17.6621 84.2925 17.0954C85.2972 16.5029 88.8265 14.4677 88.8265 14.4677Z" fill="#292929"/>
                                        <path d="M77.6447 35.102C68.9374 35.0762 68.448 21.2168 77.6447 21.3456C81.5346 21.3714 83.2863 24.3597 83.5439 27.5798C80.4011 27.5798 77.3098 27.5798 74.1669 27.5798C74.2442 30.2074 75.455 32.6805 78.2887 32.8608C80.3753 33.0154 82.6938 31.8046 83.6727 29.9756C83.209 33.2214 80.5299 35.1278 77.6447 35.102ZM74.1669 26.4034H80.0662C80.195 24.497 79.7571 20.985 77.2067 21.4744C75.3262 21.8351 74.1154 23.6212 74.1669 26.4034Z" fill="#292929"/>
                                        <path d="M56.4746 14.4418C56.4746 19.053 56.4746 26.7556 56.4746 31.3668V34.4839C68.402 34.1747 68.402 14.7252 56.4746 14.4418ZM53.9501 34.6642H51.0133C52.7135 34.4581 52.9196 32.7321 52.9196 31.3668C52.9196 26.7556 52.9196 22.1443 52.9196 17.5589C52.9196 16.1935 52.7135 14.4676 51.0133 14.2615H60.0812C72.5495 14.2615 72.5495 34.6642 60.0812 34.6642H53.9501Z" fill="#292929"/>
                                    </svg>
                                </span>
                                <button class="cw-header-back-btn transform-scale">
                                    <i class="eicon-arrow-left" aria-hidden="true"></i>
                                    <span>${delishoAdmin.templatesText.back}</span>
                                </button>
                            </div>
                            ${tabSwitcher()}
                            <div class="cw-header-right">
                                <button class="cw-header-insert-temp cw-insert-temp transform-scale" data-slug-id="">${delishoAdmin.templatesText.import}</button>
                                <span class="divider"></span>
                                <div class="cw-close"><i class="eicon-close"></i></div>
                            </div>
                        </div>
                        <div class="cw-template-modal-body">
                            <div class="cw-content-wrap">
                            </div>
                            <div class="cw-preview-wrap"></div>
                        </div>
                    </div>
                </div>
                `);
            }
            //Show Overlay
            html.css('overflow', 'hidden');
            elementorPreview.find('.cw-template-modal-overlay').show();
            //Close Overlay
            elementorPreview.find('.cw-close').on('click', function () {
                html.css('overflow', 'auto');
                elementorPreview.find('.cw-template-modal-overlay').fadeOut('fast', function () {
                    elementorPreview.find('.cw-template-modal-overlay').remove();
                });
            });

            displayTemplates(elementorPreview)
        });
    }

    function displayTemplates(elementorPreview) {

        const contentWrap = elementorPreview.find('.cw-content-wrap');
        const templateModalLoader = elementorPreview.find('.cw-template-modal-loader-wrapper');

        contentWrap.html(loadingElementorPreview());

        if (currentAjaxRequest) {
            currentAjaxRequest.abort();
        }

        // AJAX Data
        let data = {
            action: 'render_templates_designs'
        };

        currentAjaxRequest = $.post(delishoAdmin.ajaxURL, data)
            .done(function (response) {
                contentWrap.html(response);
                toggleLayout(elementorPreview);
                importTemplates(elementorPreview);
                runFilter(elementorPreview);
                templatePreview(elementorPreview);
                templateModalLoader.remove();
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                // Check if the request was aborted
                if (jqXHR.statusText === 'abort') {
                    console.log('AJAX request aborted');
                    return; // No need to further handle this case
                }

                templateModalLoader.remove();
                contentWrap.html('<div class="cw-error">Error: ' + errorThrown + '</div>');
                console.error("AJAX request failed:", textStatus, errorThrown);
            });
    }

    // toggle layout
    function toggleLayout(elementorPreview) {

        const layoutTwo = elementorPreview.find('.cw-layout-two');
        const layoutThree = elementorPreview.find('.cw-layout-three');
        const designList = elementorPreview.find('.cw-pattern-library__design-list');
        const layoutBlock = elementorPreview.find('.cw-layout-block');
        const layoutPage = elementorPreview.find('.cw-layout-page');

        elementorPreview.on('click', '.cw-layout-two', function () {
            designList.addClass('column2');
            $(this).addClass('active');
            layoutThree.removeClass('active');
        });

        elementorPreview.on('click', '.cw-layout-three', function () {
            designList.removeClass('column2');
            $(this).addClass('active');
            layoutTwo.removeClass('active');
        });

        elementorPreview.on('click', '.cw-layout-block', function () {
            layoutPage.removeClass('active');
            $(this).addClass('active');
        });

        elementorPreview.on('click', '.cw-layout-page', function () {
            layoutBlock.removeClass('active');
            $(this).addClass('active');
        });
    }

    function runFilter(elementorPreview) {

        let filters = {
            search: '',
            category: '',
            plan: ''
        }
        const demosList = Array.from(elementorPreview.find('.cw-pattern-library__design-item'));

        let debouncedSearch = debounce(function (event) {
            filters = { ...filters, search: $(event.target).val().toLowerCase() }
            filter()
        }, 300)

        elementorPreview.on('keyup', '#cw-search-control', debouncedSearch)

        elementorPreview.on('click', '.cw-cat-item', function () {
            elementorPreview.find('.cw-cat-item').removeClass('tab-active');
            $(this).addClass('tab-active');
            filters = { ...filters, category: $(this).attr('data-filter') }
            filter()
        })

        elementorPreview.on('change', '.demo-list_dropdown', function () {
            filters = { ...filters, plan: $(this).val() }
            filter()
        })

        function filter() {
            let filteredDemos = demosList;

            if (filters.search) {
                filteredDemos = filteredDemos.filter(demo => {
                    let demoData = $(demo).data();
                    let demoTitle = demoData.filterName.toLowerCase();
                    return demoTitle.indexOf(filters.search) > -1;
                })
            }

            if (filters.category && filters.category !== 'all') {
                filteredDemos = filteredDemos.filter(demo => {
                    let demoData = $(demo).data();
                    let demoCategory = demoData.filter.split(' ');
                    return demoCategory.includes(filters.category);
                })
            }

            if (filters.plan && filters.plan !== 'all') {
                filteredDemos = filteredDemos.filter(demo => {
                    let demoData = $(demo).data();
                    let demoPlan = demoData.filterPlan === 1 ? 'pro' : 'free';
                    console.log(demoData.filterPlan);
                    console.log('demoPlan:', demoPlan, 'filters.plan:', filters.plan);
                    return demoPlan === filters.plan;
                })
            }

            demosList.forEach(demo => {
                $(demo).hide()
            })

            filteredDemos.forEach(demo => {
                $(demo).show()
            })

            if(filteredDemos.length === 0) {
                elementorPreview.find('.cw-no-results')?.remove();

                elementorPreview.find('.cw-pattern-library__design-list').append(noResultPreview());
            } else {
                elementorPreview.find('.cw-no-results')?.remove();

            }
        }

    }

    async function fetchRequiredPlugins() {
        let response = await fetch(`https://wpdeliciousdemo.com/widgets-blocks-demo/wp-json/delisho-elementor-templates/v1/patterns/`);
        let designApi = await response.json();
        const requiredPlugins = {};
        const isPro = {};
        for (let dataContent of designApi) {
            if (dataContent.meta && dataContent.meta.required_plugins) {
                requiredPlugins[dataContent.id] = dataContent.meta.required_plugins;
            }
            if (dataContent.meta) {
                isPro[dataContent.id] =  '1' === dataContent.meta.freevspro ? true : false;
            }
        }
        return [requiredPlugins, isPro];
    }

    function importTemplates(elementorPreview) {
        elementorPreview.find('.cw-insert-temp').on('click', function () {
            let templateID = $(this).attr('data-slug-id');

            const modal = elementorPreview.find('.cw-template-modal');

            let patternsData = fetchRequiredPlugins();

            patternsData.then(data => {

                let pluginsLists = data[0][templateID]?.filter(requiredPlugin =>
                    !delishoAdmin.activePlugin.some(activePlugin => activePlugin.slug === requiredPlugin.slug)
                );

                let isPro = data[1][templateID];
                let isLicenseActive = delishoAdmin.isLicenseActive;

                if (pluginsLists && pluginsLists.length > 0) {
                    modal.append(`
                    <dialog class="cw-plugins-dialog">
                        <div class="cw-plugins-dialog-wrapper">
                            <div class="header">
                                <h2>Required Plugins</h2>
                                <button id="cw-plugins-dialog-close" class="cw-plugins-dialog-close" aria-label="close" type="button">
                                    <i class="eicon-close" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="cw-plugins-dialog-content">
                                <p> To use this template, please install and activate the required plugins. Once completed, you can return to this page to proceed. </p> 
                                    <ol class="plugins-list">
                                        ${pluginsLists.map(plugin => {
                                            if('delisho-pro' === plugin.slug){
                                                return `<li>
                                                ${plugin.name} - <a class="plugins-link" href="https://wpdelicious.com/delisho/?utm_source=delisho&utm_medium=elementor_button&utm_campaign=upgrade_delisho#pricing" target="_blank"> Get it here</a>
                                                </li>`
                                            } else {
                                                return `<li>
                                                ${plugin.name} - <a class="plugins-link" href="${delishoAdmin.url}/wp-admin/plugin-install.php?s=${plugin.slug}&tab=search&type=term" target="_blank">Install here</a>
                                                </li>`
                                            }
                                        }).join('')} 
                                    </ol>
                                    <span>
                                        After activating plugins, make sure to <a class="reload-page" href="javascript:void(0)"}>Reload</a> the page.
                                    </span>
                                </p>
                            </div>
                        </div>
                    </dialog>
                    `);
                    // Close functionality
                    modal.on('click', '#cw-plugins-dialog-close', function () {
                        modal.find('.cw-plugins-dialog').remove();
                    });
                    modal.on('click', 'a[target="_blank"]', function () {
                        window.open(this.href, '_blank');
                    });
                    modal.on('click', '.reload-page', function () {
                        location.reload();
                    });
                    return;
                } else if(isPro && !isLicenseActive){
                    modal.append(`
                        <dialog class="cw-plugins-dialog">
                            <div class="cw-plugins-dialog-wrapper">
                                <div class="header">
                                    <h2>Activate Your License</h2>
                                    <button id="cw-plugins-dialog-close" class="cw-plugins-dialog-close" aria-label="close" type="button">
                                        <i class="eicon-close" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <div class="cw-plugins-dialog-content">
                                    Please activate your license for Delisho Pro to use this template. Click here to <a target="_blank" class="plugins-link" href="${delishoAdmin.url}/wp-admin/admin.php?page=dr-widgets-blocks#license"> activate the license key</a>.
                                </div>
                            </div>
                        </dialog>
                        `);
                    modal.on('click', '#cw-plugins-dialog-close', function () {
                        modal.find('.cw-plugins-dialog').remove();
                    });
                    modal.on('click', 'a[target="_blank"]', function () {
                        window.open(this.href, '_blank');
                    });
                    return;
                } else {

                    $(this).attr("disabled", true);

                    let contentURL = `https://wpdeliciousdemo.com/widgets-blocks-demo/wp-json/delisho-elementor-templates/v1/patterns/${templateID}`;

                    $.ajax({
                        url: delishoAdmin.ajaxURL,
                        type: 'POST',
                        data: {
                            action: 'process_data_for_import',
                            nonce: delishoAdmin.nonce,
                            apiURL: contentURL
                        },
                        beforeSend: function () {
                            console.groupCollapsed('Inserting Demo.');
                            elementorPreview.find('.cw-template-modal-body').append(loadingElementorPreview(`${delishoAdmin.templatesText.stay}`));
                        },
                    })
                        .fail(function (jqXHR) {
                            let errorMessage = jqXHR.statusText;
                            console.log(errorMessage);
                            elementorPreview.find('.cw-template-modal-loader-wrapper').remove();
                        })
                        .done(function (response) {

                            let contentData = response.data;

                            // Import elementor templates and enable update button
                            window.elementor.getPreviewView().addChildModel(contentData, { at: elementIndex });
                            window.elementor.panel.$el.find('#elementor-panel-footer-saver-publish button').removeClass('elementor-disabled');
                            window.elementor.panel.$el.find('#elementor-panel-footer-saver-options button').removeClass('elementor-disabled');

                            // Reset Element index if it has been updated
                            elementIndex = null;

                            elementorPreview.find('.cw-template-modal-loader-wrapper').remove();

                            // Close Library
                            elementorPreview.find('.cw-close').trigger('click');
                        });
                }
            });
        });

    }

    function templatePreview(elementorPreview) {
        const previewWrap = elementorPreview.find('.cw-preview-wrap');
        elementorPreview.on('click', '.cw-pattern-library__design-preview-btn', function () {

            const dataSlugId = $(this).attr('data-slug-id');
            elementorPreview.find('.cw-header .cw-insert-temp').attr('data-slug-id', dataSlugId);

            const templateURL = $(this).attr('data-preview-url');

            elementorPreview.find('.cw-content-wrap').hide();

            elementorPreview.find('.cw-template-modal').addClass('preview-active');
            previewWrap.css("--cw-header-height", elementorPreview.find('.cw-header').outerHeight() + 'px');
            previewWrap.append(loadingElementorPreview());
            previewWrap.append(templateIframe(templateURL));
            previewWrap.find('#cw-template-preview').on('load', function () {
                previewWrap.find('.cw-template-modal-loader-wrapper').remove();
            });

        })

        switchPreviewTab(elementorPreview);

        getBackToTemplates(elementorPreview);
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        }
    }

    function getBackToTemplates(elementorPreview) {
        elementorPreview.on('click', '.cw-header-back-btn', function () {
            elementorPreview.find('.cw-content-wrap').show();
            elementorPreview.find('.cw-template-modal').removeClass('preview-active');
            elementorPreview.find('.cw-preview-wrap').empty();
            elementorPreview.find('.cw-template-modal-loader-wrapper').remove();
        });
    }

    // switch preview tab
    function switchPreviewTab(elementorPreview) {
        elementorPreview.on('change', 'input[name="template-preivew-group"]', function () {
            elementorPreview.find('.cw-preview-wrap').css('--iframe-width', $(this).val());
        });
    }

    // No Result Preivew
    function noResultPreview() {
        return (`
            <div class="cw-no-results">
                <div class="cw-no-results-title">No results found</div>
                <div class="cw-no-results-message">Please make sure your search is spelled correctly or try a different words.</div>
            </div>
        `)
    }

    // Loading Elementor Preview
    function loadingElementorPreview($content = null) {
        return (`
            <div class="cw-template-modal-loader-wrapper">
                <div class="cw-template-modal-loader">
                    <div class="cw-template-modal-loader-boxes">
                        <div class="cw-template-modal-loader-box"></div>
                        <div class="cw-template-modal-loader-box"></div>
                        <div class="cw-template-modal-loader-box"></div>
                        <div class="cw-template-modal-loader-box"></div>
                    </div>
                </div>
                <div class="cw-template-modal-loading-title">${$content ? $content : delishoAdmin.templatesText.loading}</div>
            </div>
        `);
    }

    // template iframe
    function templateIframe(templateURL) {
        return (`
                    <iframe id="cw-template-preview" loading="lazy" allow="fullscreen" style="border-style: none;width: 100%; height: 100%; transition: 0.3s; border-radius: 8px"  src=${templateURL} >
                    </iframe>
                `)
    }

    // tab switcher
    function tabSwitcher() {
        return (`
        <div class="controls-container">
            <div class="controls ready">
                <div class="segment transform-scale ">
                    <input type="radio" id="Desktop" name="template-preivew-group" value="100%" checked="">
                    <label for="Desktop"><svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.16666 17.5H13.8333M10.5 14.1667V17.5M6.16666 14.1667H14.8333C16.2335 14.1667 16.9335 14.1667 17.4683 13.8942C17.9387 13.6545 18.3212 13.272 18.5608 12.8016C18.8333 12.2669 18.8333 11.5668 18.8333 10.1667V6.5C18.8333 5.09987 18.8333 4.3998 18.5608 3.86502C18.3212 3.39462 17.9387 3.01217 17.4683 2.77248C16.9335 2.5 16.2335 2.5 14.8333 2.5H6.16666C4.76652 2.5 4.06646 2.5 3.53168 2.77248C3.06127 3.01217 2.67882 3.39462 2.43914 3.86502C2.16666 4.3998 2.16666 5.09987 2.16666 6.5V10.1667C2.16666 11.5668 2.16666 12.2669 2.43914 12.8016C2.67882 13.272 3.06127 13.6545 3.53168 13.8942C4.06646 14.1667 4.76652 14.1667 6.16666 14.1667Z" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"></path></svg></label>
                </div>
                <div class="segment transform-scale ">
                    <input type="radio" id="Tablet" name="template-preivew-group" value="768px">
                    <label for="Tablet"><svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.5 14.5834H10.5083M6.50001 18.3334H14.5C15.4334 18.3334 15.9001 18.3334 16.2567 18.1517C16.5703 17.9919 16.8252 17.7369 16.985 17.4233C17.1667 17.0668 17.1667 16.6001 17.1667 15.6667V4.33335C17.1667 3.39993 17.1667 2.93322 16.985 2.5767C16.8252 2.2631 16.5703 2.00813 16.2567 1.84834C15.9001 1.66669 15.4334 1.66669 14.5 1.66669H6.50001C5.56659 1.66669 5.09988 1.66669 4.74336 1.84834C4.42976 2.00813 4.17479 2.2631 4.015 2.5767C3.83334 2.93322 3.83334 3.39993 3.83334 4.33335V15.6667C3.83334 16.6001 3.83334 17.0668 4.015 17.4233C4.17479 17.7369 4.42976 17.9919 4.74336 18.1517C5.09988 18.3334 5.56659 18.3334 6.50001 18.3334ZM10.9167 14.5834C10.9167 14.8135 10.7301 15 10.5 15C10.2699 15 10.0833 14.8135 10.0833 14.5834C10.0833 14.3532 10.2699 14.1667 10.5 14.1667C10.7301 14.1667 10.9167 14.3532 10.9167 14.5834Z" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"></path></svg></label>
                </div>
                <div class="segment transform-scale ">
                    <input type="radio" id="Mobile" name="template-preivew-group" value="500px">
                    <label for="Mobile"><svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.5 14.4H10.5071M7.78571 18H13.2143C14.0144 18 14.4144 18 14.72 17.8256C14.9888 17.6722 15.2073 17.4274 15.3443 17.1264C15.5 16.7841 15.5 16.3361 15.5 15.44V4.56C15.5 3.66392 15.5 3.21587 15.3443 2.87362C15.2073 2.57256 14.9888 2.32779 14.72 2.17439C14.4144 2 14.0144 2 13.2143 2H7.78571C6.98564 2 6.5856 2 6.28001 2.17439C6.01121 2.32779 5.79267 2.57256 5.6557 2.87362C5.5 3.21587 5.5 3.66392 5.5 4.56V15.44C5.5 16.3361 5.5 16.7841 5.6557 17.1264C5.79267 17.4274 6.01121 17.6722 6.28001 17.8256C6.5856 18 6.98564 18 7.78571 18ZM10.8571 14.4C10.8571 14.6209 10.6972 14.8 10.5 14.8C10.3028 14.8 10.1429 14.6209 10.1429 14.4C10.1429 14.1791 10.3028 14 10.5 14C10.6972 14 10.8571 14.1791 10.8571 14.4Z" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"></path></svg></label>
                </div>
            </div>
        </div>
        `)
    }

    function implementTemplatesImport() {
        const elementorPreview = window.elementor.$previewContents;
        addLibraryButton(elementorPreview);
        getTemplatesModal(elementorPreview);
    }


    function init() {
        if (!window.elementor) return;
        window.elementor.on('preview:loaded', implementTemplatesImport);
    }

    $(window).on('elementor:init', init);

}(jQuery));

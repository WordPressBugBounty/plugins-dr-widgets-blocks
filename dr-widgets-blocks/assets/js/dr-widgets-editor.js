(function ($) {
    $(document).on('drwb_select2_init', function (event, obj) {
        var ID = '#elementor-control-default-' + obj.data._cid;
        setTimeout(function () {
            var IDSelect2 = $(ID).select2({});

            //Manual Sorting : Select2 drag and drop : starts
            setTimeout(function (){
                IDSelect2.next().children().children().children().sortable({
                    containment: 'parent',
                    stop: function(event, ui) {
                        ui.item.parent().children('[title]').each(function() {
                            var title = $(this).attr('title');
                            var original = $('option:contains(' + title + ')', IDSelect2).first();
                            original.detach();
                            IDSelect2.append(original)
                        });
                        IDSelect2.change();
                    }
                });

                $(ID).on("select2:select", function(evt) {
                    var element = evt.params.data.element;
                    var $element = $(element);

                    $element.detach();
                    $(this).append($element);
                    $(this).trigger("change");
                });
            },200);
            //Manual Sorting : Select2 drag and drop : ends

        }, 100);

    });
}(jQuery));
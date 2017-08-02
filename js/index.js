jQuery(document).ready(function () {


    jQuery('#save_order').click(function () {
        jsonObj = [];
        var counter = 0;
        jQuery("#inner .gravity").each(function () {
            counter++;
            var index1 = jQuery(this).index();
            jQuery(this).attr('data-order', index1);

            var key1 = jQuery(this).data('pid');
            var value1 = index1;

            item = {};
            item ["ID"] = key1;
            item ["menu_order"] = value1;
            jsonObj.push(item);

        });


        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {'action': 'wc_order', 'obj': jsonObj},
            success: function (data) {

                if (data) {
                    var json = jQuery.parseJSON(data);
                    var msgholder = jQuery('#after-order-saved');
                    if (json.error == false) {
                        msgholder.removeClass('error');
                    } else {
                        msgholder.addClass('error');
                    }
                    msgholder.text(json.msg);
                }

            }
        });

    });


    jQuery('#inner').isotope({
        itemSelector: '.gravity'
    });
    var list = jQuery('#inner');
    list.sortable({

        update: function (event, ui) {
        },

        cursor: 'move',
        start: function (event, ui) {
            ui.item.addClass('grabbing moving').removeClass('gravity');
            ui.placeholder
                    .addClass('starting')
                    .removeClass('moving')
                    .css({
                        top: ui.originalPosition.top,
                        left: ui.originalPosition.left
                    });
            list.isotope('reloadItems');
        },
        change: function (event, ui) {
            ui.placeholder.removeClass('starting');
            list
                    .isotope('reloadItems')
                    .isotope({
                        sortBy: 'original-order',
                        transformsEnabled: false
                    });
        },
        beforeStop: function (event, ui) {

            ui.placeholder.after(ui.item);
        },
        stop: function (event, ui) {

            ui.item.removeClass('grabbing').addClass('gravity');
            list
                    .isotope('reloadItems')
                    .isotope({
                        sortBy: 'original-order',
                        transformsEnabled: false
                    });
        }
    });


});

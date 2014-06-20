function buildOptimalCheckedList(api, item) {
    if (api.isChecked(item) & !api.isTristate(item)) {
        addGeoItem(api.getId(item));
    }

    if (api.isTristate(item)) {
        var currentItem = api.first(item);
        while (api.hasNext(currentItem)) {
            buildOptimalCheckedList(api, currentItem);
            currentItem = api.next(currentItem);
        }
        buildOptimalCheckedList(api, currentItem);
    }
}

function addGeoItem(geoCode) {
    $("#ad_settings_form").append("<input type='hidden' id='" + geoCode + "' name='location[]' value='" + geoCode + "'/>");
}

$(function () {
    $('#geo_tree').aciTree({
        ajax: {
            url: '/geo/get-list',
            data: {
                ad: $("#ad_settings_form #id").val()
            }
        },
        checkbox: true
    });

    $('#geo_tree').on('acitree', function(event, api, item, eventName, options) {
        if (eventName == 'checked' || eventName == 'unchecked'){
            $("[id^='1']").remove();
            var currentItem = item;
            if (api.hasParent(item)) {
                currentItem = api.topParent(item);
            }
            buildOptimalCheckedList(api, currentItem);
        }
    });
})
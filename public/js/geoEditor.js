$(function() {
    var geoEdit = new GeoEdit();
    geoEdit.init();
});

function GeoEdit() {
    var api;
    var item;

    function init() {
        var options = {
            ajax: {
                url: '/geo/get-edit-list'
            }
        };
        $("#geo_form").hide();
        $('#geo_tree').aciTree(options);
        api = $('#geo_tree').aciTree('api');

        $('#geo_form').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: $('#geo_form').attr("action"),
                data: $('#geo_form').serialize(),
                method: "post",
                dataType: "json",
                success: function (res) {
                    if (res.success == true) {
                        refreshTree();
                        $('#geo_form').hide();
                    }
                }
            });
            return false;
        });

        $('#generate_files').click(function () {
            $.ajax({
                url: "/geo/generate",
                success: function() {
                    alert("Done!!!");
                }
            });
        });

        $.contextMenu({
            selector: '.aciTreeLine',
            build: function(element) {
                item = api.itemFrom(element);

                var menu = {
                    append: {
                        name: 'Append',
                        callback: append
                    },
                    edit: {
                        name: 'Edit',
                        callback: edit
                    },
                    remove: {
                        name: 'Remove',
                        callback: remove
                    }
                };
                return {
                    autoHide: true,
                    items: menu
                };
            }
        });
    }

    function refreshTree() {
        api.unload(null, {
            success: function () {
                this.ajaxLoad(null);
            }
        });
    }

    function append() {
        var parentCode = api.getId(item);
        var data = new Object({
            parent: parentCode
        });
        fillDialog(data);
    }

    function edit() {
        var currentCode = api.getId(item);
        $.ajax({
            url: "/geo/get-edit",
            data: {
                code: currentCode
            },
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    var data = new Object({
                        code: currentCode,
                        native: res.data.locale.NATIVE,
                        inter: res.data.locale.US
                    });
                    fillDialog(data);
                }
            }
        });
    }

    function remove() {
        var currentCode = api.getId(item);
        $.ajax({
            url: "/geo/remove",
            success: function(res) {
                if (res.success) {
                    refreshTree();
                }
            },
            data: {
                code: currentCode
            },
            dataType: "json"
        });
    }

    function fillDialog(data) {
        var inputsList = $("#geo_form :input");
        for (var i=0; i<inputsList.length; i++) {
            var fieldName = $(inputsList[i]).attr("name");
            if (fieldName == "submit") {
                continue;
            }
            if (fieldName) {
                if (data[fieldName]) {
                    $(inputsList[i]).val(data[fieldName]);
                } else {
                    $(inputsList[i]).val("");
                }
            }
        }
        $("#geo_form").show();
    }

    return {
        init: init
    }
}
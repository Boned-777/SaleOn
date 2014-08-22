$(function() {
    var geoEdit = new GeoEdit();
    geoEdit.init();

        // node context menu

//
//        // node tooltips
//        $('#tree').tooltip({
//            items: '.aciTreeLi',
//            track: true,
//            position: {
//                my: 'left+80 top+40'
//            },
//            content: function() {
//                var api = $('#tree').aciTree('api');
//                var item = api.itemFrom(this);
//                var info = '';
//                info += 'getId: [' + api.getId(item) + ']\n';
//                info += 'getLabel: `' + api.getLabel(item) + '`\n';
//                info += 'level: ' + api.level(item) + '\n';
//                info += 'getIndex: ' + api.getIndex(item) + '\n';
//                info += 'isLeaf: ' + api.isLeaf(item) + '\n';
//                info += 'isInode: ' + api.isInode(item) + ' (open: ' + api.isOpen(item) + ')\n';
//                info += 'wasLoad: ' + api.wasLoad(item) + '\n';
//                info += 'hasParent: ' + api.hasParent(item) + '\n';
//                info += 'parent: [' + api.getId(api.parent(item)) + ']\n';
//                info += 'path: [root] ';
//                api.path(item).each(function() {
//                    info += '[' + api.getId($(this)) + '] ';
//                });
//                info += '\n';
//                info += 'hasSiblings: ' + api.hasSiblings(item) + ' count: #' + api.siblings(item).length + '\n';
//                info += 'hasPrev: ' + api.hasPrev(item) + ' [' + api.getId(api.prev(item)) + ']\n';
//                info += 'hasNext: ' + api.hasNext(item) + ' [' + api.getId(api.next(item)) + ']\n';
//                var children = api.children(item);
//                info += 'hasChildren: ' + api.hasChildren(item) + ' count: #' + children.length +
//                    ' inodes: #' + api.inodes(children).length + ' (open: #' + api.inodes(children, true).length +
//                    ' closed: #' + api.inodes(children, false).length + ') leaves: #' + api.leaves(children).length + '\n';
//                info += 'first [' + api.getId(api.first(item)) + '] isFirst: ' + api.isFirst(item) + '\n';
//                info += 'last [' + api.getId(api.last(item)) + '] isLast: ' + api.isLast(item) + '\n';
//                info += 'children: #' + children.length + '\n';
//                return info.replace(/\n/g, '<br>');
//            }
//        });
//
//        if (JSON) {
//            // load JSON
//            $('#load').click(function() {
//                var api = $('#tree').aciTree('api');
//                try {
//                    var jsonData = JSON.parse($(this).parents('div:first').find('textarea').val());
//                    api.unload(null, {
//                        success: function() {
//                            this.loadFrom(null, {
//                                itemData: jsonData
//                            });
//                        }
//                    });
//                } catch (e) {
//                    alert('Please fill in a valid JSON compatible with aciTree');
//                }
//            });
//            // save JSON
//            $('#save').click(function() {
//                var api = $('#tree').aciTree('api');
//                var jsonText = JSON.stringify(api.serialize(null, {
//                }));
//                $(this).parents('div:first').find('textarea').val(jsonText);
//            });
//        } else {
//            alert('Native JSON is not supported by your browser');
//        }

    });

function GeoEdit() {
    var api;
    var item;

    function init() {
        var options = {
            ajax: {
                url: '/geo/get-list'
            },
            radio: true,
            checkbox: true
        };
        $('#geo_tree').aciTree(options);
        api = $('#geo_tree').aciTree('api');

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
                    }

                };
                return {
                    autoHide: true,
                    items: menu
                };
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
        var currentName = api.getLabel(item);
        var data = new Object({
            code: currentCode,
            native: currentName
        });
        fillDialog(data);
    }

    function remove() {
        var currentCode = api.getId(item);
        $.ajax({
            url: "/geo/remove",
            success: function(res) {
                console.log(res);
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
            if (fieldName) {
                if (data[fieldName]) {
                    $(inputsList[i]).val(data[fieldName]);
                } else {
                    $(inputsList[i]).val("");
                }
            }
        }
    }

    return {
        init: init
    }
}
$(function () {
    if (!$("#geo").val()) {
        getGeo($("#country").val(), "region");
        getGeo("1.", "district");
    }

    $("#country").change(function() {
        getGeo($("#country").val(), "region");
        $("#geo").val($("#country").val());
    });
    $("#region").change(function() {
        getGeo($("#region").val(), "district");
        $("#geo").val($("#region").val());
    });
    $("#district").change(function() {
        $("#geo").val($("#district").val());
    });

    $("#brand_name").click(function() {
        $("#brand_name").val("");
        $("#brand").val("0");
    });

    $("#product_name").click(function() {
        $("#product_name").val("");
        $("#product").val("0");
    });
});

function getGeo(term, target) {
    $.ajax({
        url: "/geo/get",
        data: {
            term: term
        },
        success: function (res) {
            $("#" + target).html("");
            console.log(res.length);
            if (res.length < 2) {
                $("#" + target + "-label").hide();
                $("#" + target + "-element").hide();
            } else{
                $("#" + target + "-label").show();
                $("#" + target + "-element").show();
            }
            $.each(res, function (id, data) {
                $("#" + target).append(drowSelectOptions(data));
            });
        },
        dataType: "json"
    })
}

function drowSelectOptions(data) {
    return $('<option value="' + data["value"] + '">' + data["option"] + '</option>');
}
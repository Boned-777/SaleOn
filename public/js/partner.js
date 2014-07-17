$(function () {
    $("#new_address").geocomplete();
    $(".add_address").click(function () {
        addAddress();
    })
})

function addAddress() {
    var address = $("#new_address").val();
    if (address) {
        $.ajax({
            url: "/partner/add-address",
            data: {
                val: address
            },
            type: "post",
            success: function () {
                document.location.reload();
            }
        });
    }
}

function removePartnerAddress(id) {
    $.ajax({
        url: "/partner/remove-address",
        data: {
            addr: id
        },
        type: "post",
        success: function () {
            document.location.reload();
        }
    });
}
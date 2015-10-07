$(function () {
  $("#new_address").geocomplete();
  $(".add_address").click(function () {
      addAddress();
  });

  $("#phone").add("#phone1").add("#phone2").mask("(999) 999-9999");
  $("#address").geocomplete();

  var clearAutocompleter = function (select, hiddenInput) {
    select.val("");
    hiddenInput.val("0");
  }
  var brandName = $("#brand_name");
  brandName.click(function() {
    clearAutocompleter(brandName, $("#brand"));
  });
  brandName.focus(function() {
    clearAutocompleter(brandName, $("#brand"));
  });
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
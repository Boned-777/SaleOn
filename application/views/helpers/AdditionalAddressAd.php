<script type="text/javascript">
    function addAddress(addrId) {
        var newAddress = $("<input type='hidden' name='additional_address[]' id='additional_addr_" + addrId + "' value='" + addrId + "'/>");
        $("form#ad_contacts_form").append(newAddress);
    }

    function removeAddress(addrId) {
        $("form#ad_contacts_form #additional_addr_" + addrId).remove();
    }

    $(function() {
        $(".additional_address input").change(function() {
            if (this.checked) {
                addAddress($(this).val());
            } else {
                removeAddress($(this).val());
            }
        });
        $("#new_address").geocomplete();
        $(".add_address").click(function () {
            addParnerAddress();
        })
    });

    function addParnerAddress() {
        var address = $("#new_address").val();
        if (address) {
            $.ajax({
                url: "/partner/add-address",
                data: {
                    val: address
                },
                type: "post",
                success: function (res) {
                    $(".additional_address ul").append('<li><input type="checkbox" value="' + res.id + '" checked="checked" />&nbsp;' + res.name + '</li>');
                    addAddress(res.id);
                },
                dataType: "json"
            });
        }
    }
</script>

<?php
class Zend_View_Helper_AdditionalAddressAd extends Zend_View_Helper_Abstract
{
	public function AdditionalAddressAd ($addresesList = null) {
        global $translate;
        echo '<div class="additional_address">';
        echo '<div>';
        echo '<ul style="list-style: none;">';
        foreach ($addresesList->list as $item) {
            echo '<li><input type="checkbox" value="' . $item["id"] . '"' . ($item["checked"]?'checked="checked"':''). '/>&nbsp;' . $item["name"] . '</li>';
        }
        echo '</ul>';
        echo '</div>';

        echo '<div class="input-append" style="margin-left: 18px; margin-top: 10px;">';
        echo '<input id="new_address" class="form-control" type="text" style="width: 330px;"/>';
        echo '<span class="add_address add-on">' . $translate->getAdapter()->translate("add") . '</span>';
        echo '</div>';
        echo '</div>';
    }
}
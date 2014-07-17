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
    });
</script>

<?php
class Zend_View_Helper_AdditionalAddressAd extends Zend_View_Helper_Abstract
{
	public function AdditionalAddressAd ($addresesList = null) {
        global $translate;
        echo '<div class="additional_address">';
        echo '<div>';
        echo '<ul>';
        foreach ($addresesList->list as $item) {
            echo '<li><input type="checkbox" value="' . $item["id"] . '"' . ($item["checked"]?'checked="checked"':''). '/>&nbsp;' . $item["name"] . '</li>';
        }
        echo '</ul>';
        echo '</div>';

//        echo '<div class="input-append">';
//        echo '<input id="new_address" class="form-control" type="text" style="width: 450px;"/>';
//        echo '<span class="add_address add-on">' . $translate->getAdapter()->translate("add") . '</span>';
//        echo '</div>';
        echo '</div>';
    }
}
<?php
class Zend_View_Helper_AdditionalAddressPartner extends Zend_View_Helper_Abstract
{
    /**
     * @param Application_Model_PartnerAddressCollection|null $addresesList
     */
    public function AdditionalAddressPartner ($addresesList = null) {
        global $translate;

        echo '<div class="additional_address">';
            echo '<div>';
            echo '<ul style="list-style: none">';
            foreach ($addresesList->list as $item) {
                echo '<li>' . $item["name"] . '&nbsp;<a href="#" onclick="removePartnerAddress('.$item["id"].')">' . $translate->getAdapter()->translate("remove") . '</a></li>';
            }
            echo '</ul>';
            echo '</div>';

            echo '<div class="input-append" style="margin-left:18px; margin-top: 10px;">';
                echo '<input id="new_address" class="form-control" type="text" style="width: 330px;"/>';
                echo '<span class="add_address add-on">' . $translate->getAdapter()->translate("add") . '</span>';
            echo '</div>';
        echo '</div>';
    }
}
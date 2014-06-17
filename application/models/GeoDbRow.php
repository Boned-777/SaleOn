<?php
class Application_Model_GeoDbRow extends Zend_Db_Table_Row {

    public function toArray($checked = false) {
        global $translate;

        $data = array(
            "id" => $this->code,
            "label" => $translate->getAdapter()->translate($this->name),
            "inode" => false,
            "open" => false,
            "checkbox" => true,
            "checked" => $checked,
            "branch" => null
        );
        return $data;
    }
}
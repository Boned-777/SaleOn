<?php
class Application_Model_GeoDbRow extends Zend_Db_Table_Row {

    public function toArray($checked=false, $editMode=false) {
        global $translate;
        $displayName = $this->name;
        if ($editMode) {
            if(!empty($this->locale)) {
                $displayName = unserialize($this->locale);
                $displayName = $displayName["NATIVE"];
            }
        } else {
            $displayName = $translate->getAdapter()->translate($this->name);
        }


        $data = array(
            "id" => $this->code,
            "label" => $displayName,
            "inode" => false,
            "open" => false,
            "checkbox" => true,
            "checked" => $checked,
            "branch" => null
        );
        return $data;
    }

    public function toItemArray() {
        $data = array(
            "id" => $this->id,
            "code" => $this->code,
            "name" => $this->name,
            "locale" => empty($this->locale) ? array("NATIVE" => $this->name, "US" => $this->name) : unserialize($this->locale),
        );
        return $data;
    }
}
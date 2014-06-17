<?php

class Application_Model_DbTable_AdLocation extends Zend_Db_Table_Abstract
{
    protected $_name = 'AdLocation';

    static function prepareWhereStatement($geoCodeString, $tableAlias = null) {
        $tableAlias = is_null($tableAlias)?'AdLocation':$tableAlias;
        $geo = explode("-", $geoCodeString);
        $geoList = array();
        $geoCode = "";
        foreach ($geo as $geoItem) {
            $geoCode .= (!empty($geoCode)?"-":"") . $geoItem;
            $geoList[] = $tableAlias.'.location LIKE "'.$geoCode.'"';
        }
        $geoList[] = $tableAlias.'.location LIKE "'.$geoCode.'-%"';
        return $geoStmt = implode(" OR ", $geoList);
    }
}


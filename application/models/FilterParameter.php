<?php
class Application_Model_FilterParameter {

    /** Preparing filter params
     *
     * @param array $paramsNeedle - list of parameters to be processed
     * @return array||null
     */
    static function prepare($paramsNeedle=array()) {
        $params = null;
        foreach ($paramsNeedle as $param) {
            $request = new Zend_Controller_Request_Http();
            $coockie = $request->getCookie($param);
            if ($coockie && $coockie != "all") {
                $filterClass = "Application_Model_" . ucfirst($param);
                $filter = new $filterClass();
                $filterValue = $filter->getByAlias($coockie);
                if ($filterValue) {
                    $params[$param] = $filterValue;
                }
            }
        }
        return $params;
    }
}
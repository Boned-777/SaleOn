<?php
class Application_Model_FilterParameter {

    /** Preparing filter params
     *
     * @param array $paramsNeedle - list of parameters to be processed
     * @return array||null
     */
    static function prepare($paramsNeedle=array()) {
        $params = null;
        $request = new Zend_Controller_Request_Http();
        foreach ($paramsNeedle as $param) {
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

        $params["sort"] = $request->getCookie("sort");

        return $params;
    }

    /** Preparing filter params for request
     *
     * @param array $paramsNeedle - list of parameters to be processed
     * @return array||null
     */
    static function prepareFromRequest($paramsNeedle=array()) {
        $params = null;
        $request = new Zend_Controller_Request_Http();
        foreach ($paramsNeedle as $param) {
            $coockie = $request->getParam($param, "all");
            if ($coockie && $coockie != "all") {
                $filterClass = "Application_Model_" . ucfirst($param);
                $filter = new $filterClass();
                $filterValue = $filter->getByAlias($coockie);
                if ($filterValue) {
                    $params[$param] = $filterValue;
                }
            }
        }

        $params["sort"] = $request->getCookie("sort");

        return $params;
    }
}
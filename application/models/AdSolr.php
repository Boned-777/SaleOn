<?php
class Application_Model_AdSolr {
    protected $_client;

    protected $_query;

    function __construct() {
        $this->initClient();
    }

    public function initClient() {
        $this->_client = new Solarium\Client(Zend_Registry::get('config')->solr->toArray());
    }

    public function updateAllSolrData() {
        $this->clearAllSolrData();

        $ad = new Application_Model_Ad();
        $list = $ad->getRegularList();

        $update = $this->getClient()->createUpdate();

        foreach($list as $item) {
            $update->addDocument($item->createSolrDocument());
        }

        $update->addCommit();
        $result = $this->getClient()->update($update);

        return $result->getStatus();


    }

    public function getAds($params=null) {
        $query = $this->getQuery();
        $query->setRows(9999);
        $query->setFields(array(
            "post_id",
            "post_full_url",
            "brand_name",
            "name",
            "photoimg",
            "description",
            //"favorites_link",
            //"is_favorite",
            "days",
            "seo_name"
        ));
        $this->applyParams($params);

        $resultSet = $this->getClient()->execute($query);

        return $resultSet;
    }

    public function getAdsCount($temp, $params=null) {
        $params["geo"] = $temp;

        $query = $this->getQuery();
        $query->setFields(array(
            "post_id"
        ));
        $this->applyParams($params);

        $resultSet = $this->getClient()->execute($query);

        return $resultSet->getNumFound();
    }

    public function getFacets($facetField, $params=null) {
        $query = $this->getQuery();
        $facetSet = $query->getFacetSet();
        $facetSet->createFacetField($facetField)->setField($facetField);
        if ($facetField == "geo") {
            if (!isset($params["geo"])) {
                $params["geo"] = "1";
            }
            $geoFacetField = "geoLvl_" . (((int)substr_count($params["geo"], "-")) + 1);
            $facetSet->createFacetField($geoFacetField)->setField($geoFacetField);
        }

        $this->applyParams($params);

        $resultSet = $this->getClient()->execute($query);

        $facetResult = $resultSet->getFacetSet()->getFacet($facetField);
        $result = $facetResult->getValues();

        if ($facetField == "geo") {
            $geoParts = explode("-", $params["geo"]);
            $geoPartsCount = count($geoParts);

            $totalOriginGeo = 0;
            $tmp = "";
            for ($i=0; $i<$geoPartsCount; $i++) {
                $tmp .= $geoParts[$i];
                $totalOriginGeo += $result[$tmp];
                $tmp .= "-";
            }

            $result = $resultSet->getFacetSet()->getFacet($geoFacetField)->getValues();
            $result["origin"] = $totalOriginGeo;
        }
        return $result;
    }

    public function getGeoFacets($facetField, $params=null) {
        $query = $this->getQuery();
        $facetSet = $query->getFacetSet();
        $facetSet->createFacetField($facetField)->setField($facetField);
    }

    public function applyParams($params=null) {
        foreach((array)$params as $key=>$value) {
            switch ($key) {
                case 'sort':
                    if ($value == "new")
                    $this->getQuery()->addSort("public_dt", "DESC");
                    break;
                case 'favorites_list' :
                    break;

                case 'geo' :
                    $geoArr = explode("-", $value);
                    $tmp = "";
                    foreach ($geoArr as $val) {
                        $tmp .= $val;
                        $geoList[] = $tmp;
                        $tmp .= "-";
                    }
                    $geoList[] = $tmp . "*";
                    $value = "(" . implode(" OR ", $geoList) . ")";

                default:
                    $this->getQuery()->createFilterQuery($key)->setQuery($key.':'.$value);
            }
        }
    }

    public function getClient() {
        return $this->_client;
    }

    public function getQuery() {
        if (empty($this->_query)) {
            $this->_query = $this->getClient()->createQuery(Solarium\Client::QUERY_SELECT);
        }
        return $this->_query;
    }

    protected function clearAllSolrData() {
        $update = $this->getClient()->createUpdate();
        $update->addDeleteQuery('post_id:*');
        $update->addCommit();
        $result = $this->getClient()->update($update);


        return $result->getStatus();
    }
}
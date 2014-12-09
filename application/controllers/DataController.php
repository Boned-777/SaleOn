<?php

class DataController extends Zend_Controller_Action
{

    public function indexAction()
    {
        exit;
    }

    public function updateAction() {
        $s = new Application_Model_AdSolr();
        $s->updateAllSolrData();
        exit;
    }
}
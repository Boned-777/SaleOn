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

    public function dataAction() {
        $a = new Application_Model_Ad();
        $data = $a->getRegularList();

        $res = array();
        foreach ($data as $val) {
            $res[] = $val-> toListArray(null);
        }

        $this->_helper->json(array(
            "list" => $res,
            "options" => array (
                "days_left_text" => "Залишилося днів"
            ),
            "translation" => "<img src='/img/no-image-gray-ua.png' class='img-polaroid'>",
            "tr_title" => "Додати акцію"
        ));
    }
}
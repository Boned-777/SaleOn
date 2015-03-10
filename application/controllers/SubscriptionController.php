<?php
class SubscriptionController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $brands = new Application_Model_Brand();
        //$brands->getAll();

        Zend_Debug::dump($brands->getAll()); die();

        $this->view->brands = $brands;
    }

    public function addAction()
    {

    }

    public function removeAction()
    {

    }

}

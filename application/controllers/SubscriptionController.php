<?php

class SubscriptionController extends Zend_Controller_Action
{
    var $user;

    public function init()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->getIdentity()->role != Application_Model_User::USER) {
            $this->_helper->redirector('index', 'auth');
        }
        $this->user = $auth->getIdentity();
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        $form = new Application_Form_Subscription();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $item = new Application_Model_Subscription();
                $data = array_merge(
                    $request->getPost(),
                    array("user_id" => $this->user->id)
                );
                if ($item->create($data)) {
                    $this->_helper->json(array("success" => true));
                } else {
                    $this->_helper->json(array("success" => false));
                }
            } else {
                $this->_helper->json(array("success" => false));
            }
        }

        $this->view->subscriptionForm = $form;
    }

    public function managerAction () {
        $request = $this->getRequest();
        $item = new Application_Model_Subscription();

        if ($this->getRequest()->isPost()) {
            $formData = $request->getPost();

            $subscriptionItems = $item->getByUserId($this->user->id, true);
            foreach ($subscriptionItems as $subscriptionItem) {
                if (!isset($formData["brand"][$subscriptionItem->brand_id])) {
                    $subscriptionItem->delete();
                }
            }

            $this->_helper->json(array("success" => true));
        }

        $subscriptionItems = $item->getByUserId($this->user->id);
        $this->view->items = $subscriptionItems;
    }

    public function listAction () {
        $item = new Application_Model_Subscription();
        if (empty($this->user->id)) {
            $this->_helper->json(array("success" => false));
        }
        $subscriptionItems = $item->getByUserId($this->user->id);

        $this->_helper->json(array(
            "success" => true,
            "list" => $subscriptionItems->toArray()
        ));
    }

    public function sendAuction() {
        $subscription = new Application_Model_Subscription();
        $subscription->send();
    }

}
<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			
		} else {
			//$this->_redirect("/auth");
		}
    }

    public function indexAction()
    {
        // action body
    }

    public function favoritesAction()
    {
        // action body
    }

    public function newsAction()
    {
        // action body
    }

    public function contactsAction()
    {
        {
// action body
// Create form instance
            $form = new Application_Form_Contact();

            /**
             * Get request
             */
            $request = $this->getRequest();
            $post = $request->getPost(); // This contains the POST params

            /**
             * Check if form was sent
             */
            if ($request->isPost()) {
                /**
                 * Check if form is valid
                 */
                if ($form->isValid($post)) {
// build message
                    $message = 'From: ' . $post['name'] . chr(10) . 'Email: ' . $post['email'] . chr(10) . 'Message: ' . $post['message'];
// send mail
                    mail('boss@elogic.com.ua', 'contact: ' . $post['subject'], $message);
                }
            }

// give form to view (needed in index.phtml file)
            $this->view->form = $form;

        }
    }

}
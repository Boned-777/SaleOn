<?php
class ContactController extends Zend_Controller_Action
{

public function init()
{
/* Initialize action controller here */
//$form  = new Application_Form_Contact();
//$this->view->form = $form;

}

public function indexAction()
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
mail('boss@ukr.net', 'contact: ' . $post['subject'], $message);
}
}

// give form to view (needed in index.phtml file)
$this->view->form = $form;

}
}
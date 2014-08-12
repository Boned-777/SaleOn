<?php
require_once APPLICATION_PATH.'/../library/Zend/Mail.php';
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
            global $translate;

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
echo'<div id="ok">'.$translate->getAdapter()->translate("contact_ok").'</div>';
                    $message = 'От: ' . $post['name'] . chr(10) . 'Email: ' . $post['email'] . chr(10) . 'Сообщение: ' . $post['message'];
// send mail
                    $mail = new Zend_Mail();
                    mail('boned@ukr.net', 'Feedback Form WantLook: ' . $post['subject'], $message);
                }
            }

// give form to view (needed in index.phtml file)
            $this->view->form = $form;

        }
    }

}
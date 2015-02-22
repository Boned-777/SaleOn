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
        global $translate;
        {

            $layout = Zend_Layout::getMvcInstance();
            $view = $layout->getView();
            $form = new Application_Form_Contact();

            $request = $this->getRequest();

            if ($request->isPost()) {
                $data = $request->getPost();

                if ($form->isValid($data)) {
                    $message = "<h2>" . $data['name'] . "</h2><p>Email:&nbsp;" . $data['email'] . "</p><p>" . $data['message'] . "</p>";

                    $email = new Application_Model_MandrillAdapter();
                    $sendRes = $email->sendHTML('Новое сообщение через форму связи', $message,
                        array(
                            "saleoninfo@gmail.com" => "SaleON Admin"
                        ),
                        array(
                            'headers' => array(
                                'Reply-To' => $data['email']
                            ),
                            'important' => true
                        )
                    );
                    if ($sendRes !== false) {
                        $view->successMessage = $translate->getAdapter()->translate("contact_ok");
                    }
                    return true;
                }
                $view->errorMessage = $translate->getAdapter()->translate("error");;
            }
            $this->view->form = $form;
        }
    }

}
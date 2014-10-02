<?php
require_once APPLICATION_PATH.'/../library/Google/Google_Client.php';
require_once APPLICATION_PATH.'/../library/Google/contrib/Google_PlusService.php';
require_once APPLICATION_PATH.'/../library/Google/contrib/Google_Oauth2Service.php';

class AuthController extends Zend_Controller_Action
{

	public function init()
	{

	}

	public function indexAction()
	{
        $form = new Application_Form_Login();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $vars = $request->getPost();
            if ($form->isValid($vars)) {
                $data = array(
                    "username" => $vars["username"],
                    "password" => $vars["password"]
                );
                $authObj = new Application_Model_Auth();
                $user = $authObj->process(
                    $vars["username"],
                    $vars["password"]
                );
                if ($user) {
                    if ($user->role == Application_Model_User::PARTNER) {
                        $this->_helper->redirector('profile', 'partner');
                    } elseif ($user->role == Application_Model_User::ADMIN) {
                        $this->redirect("/admin/ready");
                    } else {
                        $this->_helper->redirector('index', 'index');
                    }
                } else {
                    $this->view->errorStatus = TRUE;
                }
                $form->populate($data);
            }
        } else {
            $this->_helper->redirector('new', 'user');
        }
        $this->view->form = $form;
	}

    public function authAction() {
        $this->config = Zend_Registry::get('sauthConf');
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $this->getResponse()->setRedirect($this->view->siteDir);
        }
        $adapterName = $this->getRequest()->getParam('by') ? $this->getRequest()->getParam('by') : 'vkontakte';
        $adapterClass = 'SAuth_Adapter_' . ucfirst($adapterName);
        $adapter = new $adapterClass($this->config[$adapterName]);
        $result = $adapter->authenticate();
        if ($result->getIdentity() !== false) {
            $authData = $result->getIdentity();
            $socialUserId = null;
            switch ($adapterName) {
                case "vkontakte":
                    $socialUserId = $authData["uid"];
                    break;
                case "twitter" :
                    $socialUserId = $authData["user_id"];
                    break;

                default:
                    $socialUserId = $authData["id"];
            }
            if (!is_null($socialUserId)) {
                $user = new Application_Model_User();
                if (!$user->getByUsername($socialUserId)) {
                    $user->create(array(
                        "username" => $socialUserId,
                        "password" => uniqid().uniqid().uniqid(),
                        "role" => Application_Model_User::USER
                    ));
                    $user->getByUsername($socialUserId);
                }
                $auth->getStorage()->write($user->getAuthObject());
                $this->_helper->redirector('index', 'index');
            } else {
                $auth->clearIdentity();
            }
        } else {
            $this->view->auth = false;
            $this->view->errors = $result->getMessages();
        }
    }

    public function userAction()
    {
        $form = new Application_Form_Login();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $vars = $request->getPost();
            if ($form->isValid($vars)) {
                $data = array(
                    "username" => $vars["username"],
                    "password" => $vars["password"]
                );
                $authObj = new Application_Model_Auth();
                $user = $authObj->process(
                    $vars["username"],
                    $vars["password"]
                );
                if ($user) {
                    if ($user->role == "PARTNER")
                        $this->_helper->redirector('profile', 'partner');
                    elseif ($user->role == "ADMIN")
                        $this->_helper->redirector('ready', 'admin');
                    else
                        $this->_helper->redirector('index', 'index');
                } else {
                    $this->view->errorStatus = TRUE;
                }
                $form->populate($data);
            }
        }

        $this->view->form = $form;
    }

	public function logoutAction()
	{
		Zend_Auth::getInstance()->clearIdentity();
		$this->_helper->redirector('index', 'index');
	}

    public function googleAuthAction()
    {
        $client = new Google_Client();
        $client->setApplicationName("WantLook");
        $client->setUseObjects(true);
        $oauth2 = new Google_Oauth2Service($client);

        if (isset($_SESSION['token'])) {
            $client->setAccessToken($_SESSION['token']);
        }

        if ($client->getAccessToken()) {
            $auth = Zend_Auth::getInstance();
            if ($auth->hasIdentity()) {
                $this->getResponse()->setRedirect($this->view->siteDir);
            }
            $user = $oauth2->userinfo->get();
            $systemUser = new Application_Model_User();
            $socialUserId = $user->id;
            Zend_Debug::dump($user); die();
            if (!is_null($socialUserId)) {
                $systemUser->getBySocial($socialUserId, "google");
                $auth->getStorage()->write($systemUser->toObject());
                $this->_helper->redirector('index', 'index');
            } else {
                $auth->clearIdentity();
            }

            $_SESSION['token'] = $client->getAccessToken();
        }
    }
}




<?php
require_once APPLICATION_PATH.'/../library/Google/Google_Client.php';
require_once APPLICATION_PATH.'/../library/Google/contrib/Google_PlusService.php';
require_once APPLICATION_PATH.'/../library/Google/contrib/Google_Oauth2Service.php';

class TestController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $client = new Google_Client();
        $client->setApplicationName("WantLook");
        //echo "http://".$_SERVER['HTTP_HOST']."/test/auth";
        $client->setRedirectUri('http://localhost/test/auth');
        $plus = new Google_PlusService($client);

        if ($client->getAccessToken()) {
            $client->setUseBatch(true);

            $batch = new Google_BatchRequest();
            $batch->add($plus->people->get('me'), 'key1');
            $batch->add($plus->people->get('me'), 'key2');
            $result = $batch->execute();

            // The access token may have been updated lazily.
            $_SESSION['token'] = $client->getAccessToken();
        } else {
            $authUrl = $client->createAuthUrl();
            print "<a class='login' href='$authUrl'>Connect Me!</a>";
        }
	}
    public function authAction()
    {
        $client = new Google_Client();
        $client->setApplicationName("WantLook");
        $client->setUseObjects(true);
        $client->setRedirectUri("http://localhost/test/auth");
        $oauth2 = new Google_Oauth2Service($client);

        if (isset($_SESSION['token'])) {
            $client->setAccessToken($_SESSION['token']);
        }

        if (isset($_GET['code'])) {
            $client->authenticate($_GET['code']);
            $_SESSION['token'] = $client->getAccessToken();
            $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
            header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
            return;
        }

        if (isset($_GET['code'])) {
            $client->authenticate();
            $_SESSION['token'] = $client->getAccessToken();
            $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
            echo filter_var($redirect, FILTER_SANITIZE_URL);
        }
        if ($client->getAccessToken()) {
            $auth = Zend_Auth::getInstance();
            if ($auth->hasIdentity()) {
                $this->getResponse()->setRedirect($this->view->siteDir);
            }
            $user = $oauth2->userinfo->get();
            $systemUser = new Application_Model_User();
            $socialUserId = $user->id;
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

    public function aAction()
    {
        $auth = Zend_Auth::getInstance();
        Zend_Debug::dump($auth->getIdentity()); die();
    }
}


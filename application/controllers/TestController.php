<?php
require_once APPLICATION_PATH.'/../library/Google/Google_Client.php';
require_once APPLICATION_PATH.'/../library/Google/contrib/Google_PlusService.php';

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
        $plus = new Google_PlusService($client);

        if (isset($_GET['code'])) {
            $client->authenticate();
            $_SESSION['token'] = $client->getAccessToken();
            $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
            echo filter_var($redirect, FILTER_SANITIZE_URL);
        }
        Zend_Debug::dump($client); die();
    }
}


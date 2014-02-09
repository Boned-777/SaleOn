<?php

class TestController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $mailhost= '127.0.0.1';
		$mailconfig = array(
			'port'    =>  '25',
		);

		$transport = new Zend_Mail_Transport_Smtp ($mailhost, $mailconfig);
		Zend_Mail::setDefaultTransport($transport);

		$text = "We have created an account for you in the.\nLog in using the details below:\n\n".
		"Email: " . "username" . "\n".
		"Password: " . "real_password";

		//echo "<h1>This is temporary solution!</h1> Message is: <br/>" . $text; exit;

		$mail = new Zend_Mail();
		$mail->setBodyText($text);
		$mail->setFrom('admin@test.com', 'test');
		$mail->addTo("akgals@mail.ru", '');
		$mail->setSubject('New account for you');
		$mail->send();
	}
	public function favoritesAction()
    {
        // action body
    }
}


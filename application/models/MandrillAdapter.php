<?php
class Application_Model_MandrillAdapter {
    protected $mandrill;
    protected $emailBody;
    protected $pattern;

    public $error;

    function __construct() {
        $this->mandrill = new Mandrill(Zend_Registry::get('config')->mandrill->key);
        $this->pattern = array(
            'from_email' => 'no-reply@saleon.info',
            'from_name' => 'SaleON Notification',
        );
    }

    /**
     * Build and send TEXT message
     *
     * @param string $subject
     * @param string $content
     * @param array $to        array( "email" => "name" )
     * @param array $options
     * @return bool
     */
    public function sendText($subject, $content, $to, $options=array()) {
        $message = array(
            "text" => $content
        );

        $email = $this->buildEmail($subject, $message, $to, $options);

        return $this->send($email);
    }

    /**
     * Build and send HTML message
     *
     * @param string $subject
     * @param string $content
     * @param array $to        array( "email" => "name" )
     * @param array $options
     * @return bool
     */
    public function sendHTML($subject, $content, $to, $options=array()) {
        $message = array(
            "html" => $content
        );

        $email = $this->buildEmail($subject, $message, $to, $options);

        return $this->send($email);
    }


    protected function buildEmail($subject, $content, $to, $options=array()) {
        $email = $content;

        $email = array_merge($email, $this->pattern);
        $email = array_merge($email, array(
            'subject' => $subject,
            'important' => false,
            'tags' => array('notification'),
        ));

        $receiversList = array();

        foreach ($to as $userEmail => $name) {
            $receiversList[] = array(
                'email' => $userEmail,
                'name' => $name,
                'type' => "to"
            );
        }
        $email["to"] = $receiversList;
        return $this->emailBody = $email;
    }

    public function send($message) {
        try {
            $result = $this->mandrill->messages->send($message);
        } catch (Mandrill_Error $e) {
            $this->error = $e;
            return false;
        }

        return $result;
    }


}
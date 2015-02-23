<?php
class Application_Form_Contact extends Zend_Form
{

    public function init()
    {
        global $translate;
        $this->setTranslator($translate);
        $this->setmethod('post');
        $this->setName('contact-form');

        $this->addElement('text', 'name', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("contact_name"),
            'required' => true,
        ));

        $this->addElement('text', 'email', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("contact_email"),
            'required' => true,
            'validators' => array('EmailAddress'),
        ));

        $this->addElement('textarea', 'message', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("contact_text"),
            'required' => true,
            'validators' => array( array('validator' => 'StringLength', 'options' => array(0, 1000) )
            )));

//       $this->addElement('captcha', 'captcha', array(
//            'class' => "input-block-level",
//            'label'      => $translate->getAdapter()->translate("capcha"),
//            'required'   => true,
//            'captcha'    => array('captcha' => 'Figlet','wordLen' => 5,'timeout' => 300 )
//        ));

        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => $translate->getAdapter()->translate("contact_send"),
        ));

        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
    }
}
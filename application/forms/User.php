<?php

class Application_Form_User extends Zend_Form
{

    public function init()
    {
        global $translate;

        $this->addElement('text', 'username', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("email"),
            'validators' => array(
                array('EmailAddress', true),
            ),
            'required' => true,
        ));

        $this->addElement('password', 'password', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("password"),
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
            'required' => true,
        ));

        $this->addElement('password', 'confirm_password', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("confirm_password"),
            'validators' => array(
                array('StringLength', false, array(0, 50)),
                array('Identical', true, array('password'))
            ),
            'required' => true,
        ));

        $this->addElement('submit', 'login', array(
            //'class' => 'btn btn-large btn-primary',
            'required' => false,
            'ignore' => true,
            'label' => $translate->getAdapter()->translate("register"),
        ));
    }


}


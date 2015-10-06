<?php

class Application_Form_User extends Zend_Form
{

    public function init()
    {
        global $translate;

        $this->addElement('text', 'username', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'class' => "form-control",
            'label' => $translate->getAdapter()->translate("email"),
            'validators' => array(
                array('EmailAddress', true),
            ),
            'required' => true,
        ));

        $this->addElement('password', 'password', array(
            'class' => "form-control",
            'label' => $translate->getAdapter()->translate("password"),
            'validators'    => array(
                array(
                    'validator' =>  'StringLength',
                    'options'   => array(
                        'min' => 6,
                        'messages'  =>  array(
                            Zend_Validate_StringLength::TOO_SHORT => $translate->getAdapter()->translate("error_password_too_short"),
                        )
                    )
                ),
            ),
            'required' => true,
        ));

        $this->addElement('password', 'confirm_password', array(
            'class' => "form-control",
            'label' => $translate->getAdapter()->translate("confirm_password"),
            'validators' => array(
                array('StringLength', false, array(0, 50)),
                array(
                    'validator' =>  'Identical',
                    'options'   => array('password', 'messages'  =>  array(
                            Zend_Validate_Identical::NOT_SAME => $translate->getAdapter()->translate("error_confirm_password"),
                        )
                    )
                ),
            ),
            'required' => true,
        ));

        $this->addElement('submit', 'login', array(
            'class' => 'btn btn-primary',
            'required' => false,
            'ignore' => true,
            'label' => $translate->getAdapter()->translate("register"),
        ));
    }


}


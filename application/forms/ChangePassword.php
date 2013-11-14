<?php

class Application_Form_ChangePassword extends Zend_Form
{

    public function init()
    {
        global $translate;

        $this->addElement('hidden', 'id');
        $this->getElement("id")->setDecorators(array('ViewHelper'));

        $this->addElement('hidden', 'recovery');
        $this->getElement("recovery")->setDecorators(array('ViewHelper'));

        $this->addElement('hidden', 'form');
        $this->getElement("form")->setDecorators(array('ViewHelper'));
        $this->getElement("form")->setValue("change_password");

        $this->setName("change_password");
		$this->setMethod('post');

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
            'label' => $translate->getAdapter()->translate("change_password"),
        ));
    }
	
}


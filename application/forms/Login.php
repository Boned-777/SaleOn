<?php

class Application_Form_Login extends Zend_Form
{

    public function init()
	{
        global $translate;

		$this->setName("login");
		$this->setMethod('post');

        $this->addElement('hidden', 'id');
        $this->getElement("id")->setDecorators(array('ViewHelper'));

        $this->addElement('text', 'username', array(
			'filters' => array('StringTrim', 'StringToLower'),
			'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("email"),
			'validators' => array(
				array('StringLength', false, array(0, 50)),
			),
			'required' => true,
		));
		
		$this->addElement('password', 'password', array(
			'filters' => array('StringTrim'),
			'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("password"),
			'validators' => array(
			array('StringLength', false, array(0, 50)),
			),
			'required' => true,
		));

        $recoveryBtn = new Custom_Form_Element_Universal('back');
        $recoveryBtn->setValue('<a href="/user/recovery">' . $translate->getAdapter()->translate("password_recovery_btn_caption") . '</a>');
        $recoveryBtn->removeDecorator("Label");
        $this->addElement($recoveryBtn);
		
		$this->addElement('submit', 'login', array(
			//'class' => 'btn btn-large btn-primary',
			'required' => false,
			'ignore' => true,
			'label' => $translate->getAdapter()->translate("sign_in"),
		));
	}
}


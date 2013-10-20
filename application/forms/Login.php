<?php

class Application_Form_Login extends Zend_Form
{

    public function init()
	{
		$this->setName("login");
		$this->setMethod('post');

        $this->addElement('hidden', 'id');
        $this->getElement("id")->setDecorators(array('ViewHelper'));

        $this->addElement('text', 'username', array(
			'filters' => array('StringTrim', 'StringToLower'),
			'class' => "input-block-level",
			'placeholder' => "Email address",
            'label' => 'Email',
			'validators' => array(
				array('StringLength', false, array(0, 50)),
			),
			'required' => true,
		));
		
		$this->addElement('password', 'password', array(
			'filters' => array('StringTrim'),
			'class' => "input-block-level",
			'placeholder' => "Password",
            'label' => 'Password',
			'validators' => array(
			array('StringLength', false, array(0, 50)),
			),
			'required' => true,
		));
		
		$this->addElement('submit', 'login', array(
			//'class' => 'btn btn-large btn-primary',
			'required' => false,
			'ignore' => true,
			'label' => 'Sign in',
		));
	}
}


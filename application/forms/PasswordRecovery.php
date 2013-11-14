<?php

class Application_Form_PasswordRecovery extends Zend_Form
{

    public function init()
	{
        global $translate;

		$this->setName("password_recovery");
		$this->setMethod('post');

        $this->addElement('text', 'username', array(
			'filters' => array('StringTrim', 'StringToLower'),
			'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("email"),
			'validators' => array(
				array('StringLength', false, array(0, 50)),
			),
			'required' => true,
		));
		
		$this->addElement('submit', 'recover', array(
			'required' => false,
			'ignore' => true,
			'label' => $translate->getAdapter()->translate("recover"),
		));
	}
}


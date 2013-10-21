<?php

class Application_Form_AddUser extends Zend_Form
{

    public function init()
    {
        $this->addElement('hidden', 'id');
        $this->getElement("id")->setDecorators(array('ViewHelper'));

        $this->setName("add_user");
		$this->setMethod('post');
		
		$this->addElement('text', 'name', array(
			'class' => "input-block-level",
			'validators' => array(
				array('StringLength', false, array(0, 50)),
			),
			//'required' => true
		));
		$this->addElement('text', 'email', array(
			'filters' => array('StringTrim', 'StringToLower'),
			'class' => "input-block-level",
			'validators' => array(
				array('EmailAddress', true),
			),
			'required' => true,
		));
		
		$this->addElement('submit', 'submit', array(
			'class' => 'btn btn-primary',
			'required' => false,
			'ignore' => true,
			'label' => 'Create',
		));
    }
	
}


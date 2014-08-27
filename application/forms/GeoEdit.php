<?php

class Application_Form_GeoEdit extends Zend_Form
{

    public function init()
    {
        global $translate;

        $this->setName("geo_form");
        $this->setAttrib("id", "geo_form");
        $this->setMethod('post');

        $this->addElement('hidden', 'id');
        $this->getElement("id")->setDecorators(array('ViewHelper'));

        $this->addElement('hidden', 'code');
        $this->getElement("code")->setDecorators(array('ViewHelper'));

        $this->addElement('hidden', 'parent');
        $this->getElement("parent")->setDecorators(array('ViewHelper'));

		$this->addElement('text', 'native', array(
			'class' => "input-block-level",
			'validators' => array(
				array('StringLength', false, array(0, 50)),
			),
            'label' => "native",
			'required' => true
		));

        $this->addElement('text', 'inter', array(
            'class' => "input-block-level",
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
            'label' => "inter",
            'required' => true
        ));
		
		$this->addElement('submit', 'submit', array(
			'class' => 'btn btn-primary',
			'required' => false,
			'ignore' => true,
			'label' => 'Create',
		));
    }
	
}


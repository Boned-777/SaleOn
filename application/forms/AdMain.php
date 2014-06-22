<?php

class Application_Form_AdMain extends Zend_Form
{
    public $isReady;

    public function __construct($options = null)
    {
        $this->isReady = $options["isReady"]?true:false;
        parent::__construct($options);
    }

    public function init()
    {
        global $translate;
        $this->setTranslator($translate);

        $this->addElement('hidden', 'id');
        $this->getElement("id")->setDecorators(array('ViewHelper'));

        $this->addElement('hidden', 'form');
        $this->getElement("form")->setValue("AdMain");
        $this->getElement("form")->setDecorators(array('ViewHelper'));

        $this->addElement('text', 'name', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("name") . ' *',
            'validators' => array(
                array('StringLength', false, array(0, 255)),
            ),
            'required' => true,
        ));
        $this->
        $this->addElement('textarea', 'description', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("description") . ' *',
            'max_length' => 250,
            'validators'    => array(
                array(
                    'validator' =>  'StringLength',
                    'options'   => array(
                        'encoding' => 'UTF-8',
                        'max' => 300,
                        'messages'  =>  array(
                               Zend_Validate_StringLength::TOO_LONG => $translate->getAdapter()->translate("too_long"),
                        )
                    )
                ),
            ),
            'required' => true,
        ));

        $this->addElement('textarea', 'full_description', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("full_description"),
            'max_length' => 10000,
            'validators'    => array(
                array(
                    'validator' =>  'StringLength',
                    'options'   => array(
                        'encoding' => 'UTF-8',
                        'max' => 10050,
                        'messages'  =>  array(
                            Zend_Validate_StringLength::TOO_LONG => $translate->getAdapter()->translate("too_long"),
                        )
                    )
                ),
            ),
        ));

        $this->addElement('submit', 'login', array(
            //'class' => 'btn btn-large btn-primary',
            'required' => false,
            'ignore' => true,
            'label' => $translate->getAdapter()->translate($this->isReady?"finish":"save_and_next")
        ));
    }
}


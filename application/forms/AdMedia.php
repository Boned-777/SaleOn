<?php

class Application_Form_AdMedia extends Zend_Form
{
    public $isReady;

    public function __construct($options = null)
    {
        $this->isReady = $options["isReady"]?true:false;
        parent::__construct($options);
        $this->setName('media');
        $this->setAttrib('enctype', 'multipart/form-data');
        $this->setMethod("post");
    }

    public function init()
    {
        global $translate;

        $this->addElement('hidden', 'id');
        $this->getElement("id")->setDecorators(array('ViewHelper'));

        $this->addElement('hidden', 'form');
        $this->getElement("form")->setValue("AdMedia");
        $this->getElement("form")->setDecorators(array('ViewHelper'));

        $this->addElement('file', 'image_file', array(
            'class' => "bottom-offset",
            'label' => $translate->getAdapter()->translate("image")
        ));

        $this->addElement('file', 'banner_file', array(
            'class' => "bottom-offset",
            'label' => $translate->getAdapter()->translate("banner"),
        ));

        $this->addElement('textarea', 'video', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("video"),
        ));

        $this->addElement('submit', 'submit', array(
            //'class' => 'btn btn-primary',
            'required' => false,
            'ignore' => true,
            'label' => $translate->getAdapter()->translate($this->isReady?"finish":"save_and_next")
        ));
    }
}


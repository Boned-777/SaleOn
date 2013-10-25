<?php

class Application_Form_AdMedia extends Zend_Form
{
    public function __construct($options = null)
    {
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
            'label' => $translate->getAdapter()->translate("image"),
        ));

        $this->addElement('file', 'banner_file', array(
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
            'label' => $translate->getAdapter()->translate("finish"),
        ));
    }
}


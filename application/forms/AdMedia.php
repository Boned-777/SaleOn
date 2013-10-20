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
        $this->addElement('hidden', 'id');
        $this->getElement("id")->setDecorators(array('ViewHelper'));

        $this->addElement('hidden', 'form');
        $this->getElement("form")->setValue("AdMedia");
        $this->getElement("form")->setDecorators(array('ViewHelper'));

        $this->addElement('textarea', 'video', array(
            'class' => "input-block-level",
            'placeholder' => "Code of video",
            'label' => "Video",
        ));

        $this->addElement('file', 'banner_file', array(
            'class' => "input-block-level",
            'label' => "Banner",
        ));
        
        $doc_file = new Zend_Form_Element_File("img");
        $doc_file->setLabel("Image");
        $this->addElement($doc_file);

        $this->addElement('submit', 'submit', array(
            //'class' => 'btn btn-primary',
            'required' => false,
            'ignore' => true,
            'label' => 'Submit',
        ));
    }
}


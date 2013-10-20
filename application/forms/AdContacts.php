<?php

class Application_Form_AdContacts extends Zend_Form
{

    public function init()
    {

        $this->addElement('hidden', 'id');
        $this->getElement("id")->setDecorators(array('ViewHelper'));

        $this->addElement('hidden', 'form');
        $this->getElement("form")->setValue("AdContacts");
        $this->getElement("form")->setDecorators(array('ViewHelper'));

        $this->addElement('text', 'address', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'class' => "input-block-level",
            'placeholder' => "Address",
            'label' => "Address",
            'validators' => array(
                array('StringLength', false, array(0, 255)),
            ),
            //'required' => true,
        ));

        $this->addElement('text', 'phone', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'class' => "input-block-level",
            'placeholder' => "Phone",
            'label' => "Phone",
            'validators' => array(
                array('StringLength', false, array(0, 45)),
            ),
            //'required' => true,
        ));

        $this->addElement('text', 'fax', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'class' => "input-block-level",
            'placeholder' => "Fax",
            'label' => "Fax",
            'validators' => array(
                array('StringLength', false, array(0, 45)),
            ),
            //'required' => true,
        ));

        $this->addElement('text', 'url', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'class' => "input-block-level",
            'placeholder' => "URL",
            'label' => "URL",
            'validators' => array(
                array('StringLength', false, array(0, 255)),
            ),
            //'required' => true,
        ));

        $this->addElement('submit', 'login', array(
            //'class' => 'btn btn-large btn-primary',
            'required' => false,
            'ignore' => true,
            'label' => 'Submit',
        ));
    }
}


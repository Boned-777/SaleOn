<?php

class Application_Form_Partner extends Zend_Form
{

    public function init()
    {
        $this->addElement('hidden', 'id');
        $this->getElement("id")->setDecorators(array('ViewHelper'));

        $this->addElement('text', 'enterprise', array(
            'class' => "input-block-level",
            'placeholder' => "Enterprise",
            'label' => "Enterprise",
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
            'required' => true,
        ));

        $this->addElement('text', 'brand', array(
            'class' => "input-block-level",
            'placeholder' => "Brand",
            'label' => "Brand",
            'validators' => array(
                array('StringLength', false, array(0, 50))
            ),
            'required' => true,
        ));

        $this->addElement('text', 'phone', array(
            'class' => "input-block-level",
            'placeholder' => "Phone",
            'label' => "Phone",
            'validators' => array(
                array('StringLength', false, array(0, 14))
            ),
            'required' => true,
        ));

        $this->addElement('text', 'web', array(
            'class' => "input-block-level",
            'placeholder' => "Web",
            'label' => "Web",
            'validators' => array(
                array('StringLength', false, array(0, 100))
            ),
            'required' => true,
        ));

        $this->addElement('textarea', 'address', array(
            'class' => "input-block-level",
            'placeholder' => "Address",
            'label' => "Address",
//            'validators' => array(
//                array('StringLength', false, array(0, 100))
//            ),
            'required' => true,
        ));

        $this->addElement('submit', 'login', array(
            //'class' => 'btn btn-large btn-primary',
            'required' => false,
            'ignore' => true,
            'label' => 'Update',
        ));
    }


}


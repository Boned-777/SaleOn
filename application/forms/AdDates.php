<?php

class Application_Form_AdDates extends Zend_Form
{

    public function init()
    {
        global $translate;

        $this->addElement('hidden', 'id');
        $this->getElement("id")->setDecorators(array('ViewHelper'));

        $this->addElement('hidden', 'form');
        $this->getElement("form")->setValue("AdDates");
        $this->getElement("form")->setDecorators(array('ViewHelper'));

        $this->addElement('text', 'public_dt', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("public_date"),
            'validators' => array(
                array('StringLength', false, array(0, 255)),
            ),
            'required' => true,
        ));

        $this->addElement('text', 'start_dt', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("start_date"),
            'validators' => array(
                array('StringLength', false, array(0, 45)),
            ),
            'required' => true,
        ));

        $this->addElement('text', 'end_dt', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("end_date"),
            'validators' => array(
                array('StringLength', false, array(0, 45)),
            ),
            'required' => true,
        ));

        $this->addElement('submit', 'login', array(
            //'class' => 'btn btn-large btn-primary',
            'required' => false,
            'ignore' => true,
            'label' => $translate->getAdapter()->translate("save_and_next"),
        ));
    }
}


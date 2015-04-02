<?php

class Application_Form_BrandOwner extends Zend_Form
{

    public function init()
    {
        global $translate;

        $this->setAction('/brands/edit-owner');

        $this->addElement('hidden', 'id');
        $this->getElement("id")->setDecorators(array('ViewHelper'));

        $this->addElement('hidden', 'brand_id');
        $this->getElement("brand_id")->setDecorators(array('ViewHelper'));


        $this->addElement('text', 'owner_email', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("email"),
            'validators' => array(
                array('EmailAddress')
            ),
//            'required' => true,
        ));

        $this->addElement('text', 'address', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("address"),
            'disabled' => true,
//            'validators' => array(
//                array('StringLength', false, array(0, 100))
//            ),
//            'required' => true,
        ));

        $this->addElement('text', 'phone', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("phone"),
            'validators' => array(
                array('StringLength', false, array(0, 14))
            ),
            'disabled' => true,
//            'required' => true,
        ));

        $this->addElement('text', 'phone1', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("additional_phone"),
            'validators' => array(
                array('StringLength', false, array(0, 14))
            ),
            'disabled' => true,
//            'required' => true,
        ));

        $this->addElement('text', 'web', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("url"),
            'validators' => array(
                array('StringLength', false, array(0, 100)),
                new Custom_Form_Validator_Url()
            ),
            'disabled' => true,
//            'required' => true,
        ));

        $this->addElement('submit', 'brand_owner_save', array(
            //'class' => 'btn btn-large btn-primary',
            'required' => false,
            'ignore' => true,
            'label' => $translate->getAdapter()->translate("update"),
        ));
    }

}


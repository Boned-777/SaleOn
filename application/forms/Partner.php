<?php

class Application_Form_Partner extends Zend_Form
{

    public function init()
    {
        global $translate;

        $this->addElement('hidden', 'id');
        $this->getElement("id")->setDecorators(array('ViewHelper'));

        $this->addElement('text', 'enterprise', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("enterprise"),
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
//            'required' => true,
        ));

        $this->addElement('hidden', 'brand');
        $this->getElement("brand")->setDecorators(array('ViewHelper'));

        $brand = new ZendX_JQuery_Form_Element_AutoComplete('brand_name', array(
            'class' => "input-block-level",
        ));
        $brand->setLabel($translate->getAdapter()->translate("brand"));
        $brand->setJQueryParam('source', '/brands/autocomp');
        $brand->setJQueryParam('select', new Zend_Json_Expr(
            'function (e, data) {
                console.log(data);
                $("#brand_name").val(data.item.label);
                $("#brand").val(data.item.value);
                return false;
            }'));
        $this->addElement($brand);

        $this->addElement('text', 'address', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("address"),
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
//            'required' => true,
        ));

        $this->addElement('text', 'phone1', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("additional_phone"),
            'validators' => array(
                array('StringLength', false, array(0, 14))
            ),
//            'required' => true,
        ));

        $this->addElement('text', 'phone2', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("additional_phone"),
            'validators' => array(
                array('StringLength', false, array(0, 14))
            ),
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
//            'required' => true,
        ));

        $this->addElement('text', 'email', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("email"),
            'validators' => array(
                array('EmailAddress')
            ),
//            'required' => true,
        ));

        $this->addElement('submit', 'login', array(
            //'class' => 'btn btn-large btn-primary',
            'required' => false,
            'ignore' => true,
            'label' => $translate->getAdapter()->translate("update"),
        ));
    }


}


<?php

class Application_Form_AdSettings extends Zend_Form
{

    public function init()
    {
        global $translate;

        $this->addElement('hidden', 'id');
        $this->getElement("id")->setDecorators(array('ViewHelper'));

        $this->addElement('hidden', 'form');
        $this->getElement("form")->setValue("AdSettings");
        $this->getElement("form")->setDecorators(array('ViewHelper'));

        $geo = new Application_Model_Geo();
        $this->addElement('select', 'country', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("country"),
            'multiOptions' => $geo->getAll("_.")
        ));

        $this->addElement('select', 'region', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("region"),
            'multiOptions' => $geo->getAll("1._.")
        ));

        $this->addElement('select', 'district', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("city"),
        ));

        $categories = new Application_Model_Category();
        $this->addElement('select', 'category', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("category"),
            'multiOptions' => $categories->getAll()
        ));
        $brand = new ZendX_JQuery_Form_Element_AutoComplete('brand');
        $brand->setLabel($translate->getAdapter()->translate("brand"));
        $brand->setJQueryParam('source', '/brands/autocomp');
        $this->addElement($brand);

        $this->addElement('submit', 'login', array(
            //'class' => 'btn btn-large btn-primary',
            'required' => false,
            'ignore' => true,
            'label' => $translate->getAdapter()->translate("save_and_next"),
        ));
    }
}


<?php

class Application_Form_AdSettings extends Zend_Form
{

    public function init()
    {
        $this->addElement('hidden', 'id');
        $this->getElement("id")->setDecorators(array('ViewHelper'));

        $this->addElement('hidden', 'form');
        $this->getElement("form")->setValue("AdSettings");
        $this->getElement("form")->setDecorators(array('ViewHelper'));

        $geo = new Application_Model_Geo();
        $this->addElement('select', 'country', array(
            'class' => "input-block-level",
            'label' => "Country",
            'multiOptions' => $geo->getAll("_.")
        ));

        $this->addElement('select', 'region', array(
            'class' => "input-block-level",
            'label' => "Region",
            'multiOptions' => $geo->getAll("1._.")
        ));

        $this->addElement('select', 'district', array(
            'class' => "input-block-level",
            'label' => "City"
        ));

        $categories = new Application_Model_Category();
        $this->addElement('select', 'category', array(
            'class' => "input-block-level",
            'label' => "Category",
            'multiOptions' => $categories->getAll()
        ));

        $this->addElement('select', 'brand', array(
            'class' => "input-block-level",
            'label' => "Brand"
        ));

        $this->addElement('submit', 'login', array(
            //'class' => 'btn btn-large btn-primary',
            'required' => false,
            'ignore' => true,
            'label' => 'Submit',
        ));
    }
}


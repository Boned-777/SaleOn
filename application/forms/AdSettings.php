<?php
class Application_Form_AdSettings extends Zend_Form
{
    public $isReady;

    public function __construct($options = null)
    {
        $this->isReady = $options["isReady"]?true:false;
        parent::__construct($options);
    }

    public function init()
    {
        global $translate;

        $this->addElement('hidden', 'id');
        $this->getElement("id")->setDecorators(array('ViewHelper'));

        $this->addElement('hidden', 'form');
        $this->getElement("form")->setValue("AdSettings");
        $this->getElement("form")->setDecorators(array('ViewHelper'));

        $categories = new Application_Model_Category();
        $this->addElement('select', 'category', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("category"),
            'multiOptions' => $categories->getAll()
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
            $("#brand_name").val(data.item.label);
            $("#brand").val(data.item.value);
            return false;
        }'));
        $this->addElement($brand);

        $this->addElement('hidden', 'product');
        $this->getElement("product")->setDecorators(array('ViewHelper'));

        $product = new ZendX_JQuery_Form_Element_AutoComplete('product_name', array(
            'class' => "input-block-level",
        ));
        $product->setLabel($translate->getAdapter()->translate("product"));
        $product->setJQueryParam('source', '/products/autocomp');
        $product->setJQueryParam('select', new Zend_Json_Expr(
            'function (e, data) {
                $("#product_name").val(data.item.label);
                $("#product").val(data.item.value);
                return false;
            }'));
        $this->addElement($product);

        $this->addElement('hidden', 'geo');
        $this->getElement("geo")->setDecorators(array('ViewHelper'));

        $geo = new Application_Model_Geo();
        $this->addElement('select', 'country', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("country"),
            'multiOptions' => $geo->getAll("")
        ));

        $this->addElement('select', 'region', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("region"),
            //'multiOptions' => $geo->getAll("1")
        ));

        $this->addElement('select', 'district', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("city"),
        ));

        $this->addElement('submit', 'login', array(
            //'class' => 'btn btn-large btn-primary',
            'required' => false,
            'ignore' => true,
            'label' => $translate->getAdapter()->translate($this->isReady?"finish":"save_and_next")
        ));
    }
}


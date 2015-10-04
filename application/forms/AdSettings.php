<?php
class Application_Form_AdSettings extends Zend_Form
{
    public $isReady;

    public function __construct($options = null)
    {
        $this->isReady = $options["isReady"]?true:false;
        parent::__construct($options);
        $this->setAttrib("id", "ad_settings_form");
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
            //'class' => "form-control",
            'label' => $translate->getAdapter()->translate("category") . ' *',
            'multiOptions' => $categories->getAll()
        ));

        $this->addElement('hidden', 'brand');
        $this->getElement("brand")->setDecorators(array('ViewHelper'));

        $brand = new ZendX_JQuery_Form_Element_AutoComplete('brand_name', array(
            'class' => "form-control"
        ));
        $brand->setLabel($translate->getAdapter()->translate("brand") . ' *');
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
            'class' => "form-control"
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

        $this->addElement('submit', 'settings_submit', array(
            'class' => 'btn btn-primary',
            'required' => false,
            'ignore' => true,
            'label' => $translate->getAdapter()->translate($this->isReady?"finish":"save_and_next")
        ));
    }

    public function prepareGeo($geo="1-0-0") {
//        $geoItem = new Application_Model_Geo();
//        $geo = $geo?$geo:"1-0-0";
//        $geoVals = explode("-", $geo);
//        $geoVals = array_merge($geoVals, array(0,0,0));
//
//        $this->getElement("country")->setValue($geoVals[0]);
//
//        $this->getElement("region")->setMultiOptions($geoItem->getAll($geoVals[0]));
//        $this->getElement("region")->setValue($geoVals[0].'-'.$geoVals[1]);
//
//        $this->getElement("district")->setMultiOptions($geoItem->getAll($geoVals[0].'-'.$geoVals[1]));
//        $this->getElement("district")->setValue($geoVals[0].'-'.$geoVals[1].'-'.$geoVals[2]);
    }

    public function isValid($data) {
        global $translate;

        $parentRes = parent::isValid($data);

        $customRes = true;
        if (empty($data["brand_name"])) {
            $this->getElement("brand_name")->addError($translate->getAdapter()->translate("isEmpty"));
            $customRes = false;
        }

        return $parentRes&&$customRes;
    }

    public function processData($formData) {
        $itemData = array();
        if ((!empty($formData["brand_name"])) && (!$formData["brand"])) {
            $formData["brand_name"] = str_replace("\"", "", $formData["brand_name"]);
            $formData["brand_name"] = ucfirst($formData["brand_name"]);
            $brand = new Application_Model_DbTable_Brand();
            $brand_res = $brand->save(array(
                "name" => $formData["brand_name"]
            ));
            if ($brand_res) {
                $itemData["brand_name"] = $formData["brand_name"];
                $itemData["brand"] = $brand_res;
            }
        }

        if ((!empty($formData["product_name"])) && (!$formData["product"])) {
            $formData["product_name"] = str_replace("\"", "", $formData["product_name"]);
            $formData["product_name"] = ucfirst($formData["product_name"]);
            $product = new Application_Model_DbTable_Product();
            $product_res = $product->save(array(
                "name" => $formData["product_name"]
            ));
            if ($product_res) {
                $itemData["product_name"] = $formData["product_name"];
                $itemData["product"] = $product_res;
            }
        }
        return $itemData;
    }
}


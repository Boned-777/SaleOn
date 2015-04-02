<?php
class Application_Form_Brand extends Zend_Form
{
    public function init()
    {
        global $translate;

        $this->addElement('hidden', 'id');
        $this->getElement("id")->setDecorators(array('ViewHelper'));

        $brand = new ZendX_JQuery_Form_Element_AutoComplete('name', array(
            'class' => "input-block-level",
        ));
        $brand->setLabel($translate->getAdapter()->translate("name") . ' *');
        $brand->setJQueryParam('source', '/brands/autocomp');
        $brand->setJQueryParam('select', new Zend_Json_Expr(
            'function (e, data) {
                $("#name").val(data.item.label);
                $("#id").val(data.item.value);
                return false;
            }'));
        $this->addElement($brand);

        $this->addElement('select', 'status', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("status"),
            'multiOptions' => array(
                Application_Model_Brand::NEW_BRAND => Application_Model_Brand::NEW_BRAND,
                Application_Model_Brand::ACTIVE => Application_Model_Brand::ACTIVE,
                Application_Model_Brand::INACTIVE => Application_Model_Brand::INACTIVE,
            )
        ));

        $this->addElement('textarea', 'description', array(
            'class' => "input-block-level",
            'label' => $translate->getAdapter()->translate("description")
        ));

        $this->addElement('submit', 'brand_submit', array(
            //'class' => 'btn btn-large btn-primary',
            'required' => false,
            'ignore' => true,
            'label' => $translate->getAdapter()->translate("save")
        ));
    }
}


<?php

class Application_Form_Subscription extends Zend_Form
{

    public function init()
    {
        global $translate;

        $scriptElement = new Custom_Form_Element_Universal("script");
        $scriptElement->setValue("
            <script>
            $( document ).ready(function() {
                $('#description-label').hide();
                $('#description-element').hide();
            });
            </script>
        ");
        $scriptElement->removeDecorator("Label");

        $this->addElement($scriptElement);

        $this->addElement('hidden', 'brand_id');
        $this->getElement("brand_id")->setDecorators(array('ViewHelper'));

        $brand = new ZendX_JQuery_Form_Element_AutoComplete('brand_name', array(
            'class' => "input-block-level",
        ));
        $brand->setLabel($translate->getAdapter()->translate("subscription_brand"));
        $brand->setJQueryParam('source', '/brands/autocomp');
        $brand->setJQueryParam('response', new Zend_Json_Expr(
            'function (e, data) {
                $("#brand_id").val("");
                if (data.content.length) {
                    $("#description-label").hide();
                    $("#description-element").hide();
                } else {
                    $("#description-label").show();
                    $("#description-element").show();
                }
                return false;
            }'));
        $brand->setJQueryParam('select', new Zend_Json_Expr(
            'function (e, data) {
                $("#brand_name").val(data.item.label);
                $("#brand_id").val(data.item.value);
                return false;
            }'));
        $this->addElement($brand);

        $this->addElement('textarea', 'description', array(
            'class' => "input-block-level hide",
            'label' => $translate->getAdapter()->translate("subscription_details"),
            'validators' => array(
                array('StringLength', false, array(0, 500))
            ),
            //'required' => true,
        ));

        $this->addElement('submit', "subscription_submit", array(
            //'class' => 'btn btn-large btn-primary',
            'required' => false,
            'ignore' => true,
            'label' => $translate->getAdapter()->translate("update"),
        ));
    }

}


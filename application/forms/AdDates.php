<?php

class Application_Form_AdDates extends Zend_Form
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

        //Validators
        $dateCompareValidatorsMessages = array(
            Custom_Form_Validator_DateCompare::NOT_ACTUAL => $translate->getAdapter()->translate("date_compare_not_actual"),
            Custom_Form_Validator_DateCompare::NOT_LATER => $translate->getAdapter()->translate("date_compare_not_later")
        );

        $publicDTValidatorFalse = new Custom_Form_Validator_DateCompare("public_dt", false);
        $publicDTValidatorTrue = new Custom_Form_Validator_DateCompare("public_dt", true);

        //Elements
        $this->addElement('hidden', 'id');
        $this->getElement("id")->setDecorators(array('ViewHelper'));

        $this->addElement('hidden', 'form');
        $this->getElement("form")->setValue("AdDates");
        $this->getElement("form")->setDecorators(array('ViewHelper'));

        $this->addElement('text', 'public_dt', array(
            'readonly' => "readonly",
            'class' => "input-block-level-date",
            'label' => $translate->getAdapter()->translate("public_date") . ' *',
            'required' => true,

        ));

        $public_dt = $this->getElement("public_dt");
        //$public_dt->addValidator($publicDTValidatorFalse);

        $this->addElement('text', 'end_dt', array(
            'readonly' => "readonly",
            'class' => "input-block-level-date",
            'label' => $translate->getAdapter()->translate("end_date") . ' *',
            'required' => true,
        ));

        $end_dt = $this->getElement("end_dt");
        $end_dt->addValidator($publicDTValidatorTrue);

        $this->addElement('submit', 'login', array(
            //'class' => 'btn btn-large btn-primary',
            'required' => false,
            'ignore' => true,
            'label' => $translate->getAdapter()->translate($this->isReady?"finish":"save_and_next")
        ));
    }
}


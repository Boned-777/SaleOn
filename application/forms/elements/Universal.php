<?php
class Custom_Form_Element_Universal extends Zend_Form_Element_Xhtml
{
    public $helper = 'formNote';
	
	public function isValid($value, $context = null) {
        return true;
    }
}
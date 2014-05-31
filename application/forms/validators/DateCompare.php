<?php

/** @see Zend_Validate_Abstract */
require_once 'Zend/Validate/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Custom_Form_Validator_DateCompare extends Zend_Validate_Abstract
{
    /**
     * Error codes
     * @const string
     */
    const NOT_ACTUAL    = 'notActual';
    const MISSING_TOKEN = 'missingToken';
    const NOT_LATER     = 'notLater';
    const NOT_EARLIER   = 'notEarlier';
    const NOT_BETWEEN   = 'notBetween';

    /**
     * Error messages
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_ACTUAL     => "The date '%value%' does not actual",
        self::NOT_LATER      => "The date '%value%' is not later than the required",
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'token' => '_tokenString'
    );

    /**
     * Original token against which to validate
     * @var string
     */
    protected $_tokenString;
    protected $_token;
    protected $_compare;

    /**
     * Sets validator options
     *
     * @param  mixed $token
     * @param  mixed $compare
     * @return void
     */
    public function __construct($token = null, $compare = null)
    {
        if (null !== $token) {
            $this->setToken($token);
            $this->setCompare($compare);
        }
    }

    /**
     * Set token against which to compare
     *
     * @param  mixed $token
     * @return Zend_Validate_Identical
     */
    public function setToken($token)
    {
        $this->_tokenString = (string) $token;
        $this->_token       = $token;
        return $this;
    }

    /**
     * Retrieve token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * Set compare against which to compare
     *
     * @param  mixed $compare
     * @return Zend_Validate_Identical
     */
    public function setCompare($compare)
    {
        $this->_compareString = (string) $compare;
        $this->_compare       = $compare;
        return $this;
    }

    /**
     * Retrieve compare
     *
     * @return string
     */
    public function getCompare()
    {
        return $this->_compare;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if a token has been set and the provided value
     * matches that token.
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->_setValue((string) $value);
        $token = $this->getToken();

        if ($token === null) {
            $this->_error(self::MISSING_TOKEN);
            return false;
        }
        try {
            $date1 = new Zend_Date($value);
            $date2 = new Zend_Date($context[$token]);
        } catch (Exception $e) {
            $this->_error(self::NOT_LATER);
            return false;
        }



        // Not Later
        if ($this->getCompare()) {
            if ($date1->compare($date2) <= 0 || ($date1==$date2)) {
                $this->_error(self::NOT_LATER);
                return false;
            }
        } else {
            if ($date1->compare($date2) > 0) {
                $this->_error(self::NOT_EARLIER);
                return false;
            }
        }

        $today = new Zend_Date();
        if ($date1->compare($today) <= 0) {
            $this->_error(self::NOT_ACTUAL);
            return true;
        }

        // Date is valid
        return true;
    }
}
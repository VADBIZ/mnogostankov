<?php
namespace Braintree;

/**
 * Braintree UnknownPaymentMethod module
 *
 * @package    Braintree
 * @category   Resources
 */

/**
 * Manages Braintree UnknownPaymentMethod
 *
 * <b>== More information ==</b>
 *
 *
 * @package    Braintree
 * @category   Resources
 *
 * @property-read string $token
 * @property-read string $imageUrl
 */
class UnknownPaymentMethod extends Base
{


    /**
     *  factory method: returns an instance of UnknownPaymentMethod
     *  to the requesting method, with populated properties
     *
     * @ignore
     * @return UnknownPaymentMethod
     */
    public static function factory($attributes)
    {
        $instance = new self();
        $values = array_values($attributes);
        $instance->_initialize(array_shift($values));
        return $instance;
    }

    /* instance methods */

    /**
     * sets instance properties from an array of values
     *
     * @access protected
     * @param array $unknownPaymentMethodAttribs array of unknownPaymentMethod data
     * @return void
     */
    protected function _initialize($unknownPaymentMethodAttribs)
    {
        // set the attributes
        $this->imageUrl = 'https://assets.braintreegateway.com/payment_method_logo/unknown.png';
        $this->_attributes = $unknownPaymentMethodAttribs;
    }

    /**
     * returns false if default is null or false
     *
     * @return boolean
     */
    public function isDefault()
    {
        return $this->default;
    }

}
class_alias('Braintree\UnknownPaymentMethod', 'Braintree_UnknownPaymentMethod');

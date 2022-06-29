<?php
namespace Braintree;

/**
 * Braintree OAuthCredentials module
 *
 * @package    Braintree
 * @category   Resources
 */
class OAuthCredentials extends Base
{
    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);
        return $instance;
    }

    protected function _initialize($attribs)
    {
        $this->_attributes = $attribs;
    }

    /**
     * returns a string representation of the access token
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . '[' .
                Util::attributesToString($this->_attributes) .']';
    }
}
class_alias('Braintree\OAuthCredentials', 'Braintree_OAuthCredentials');

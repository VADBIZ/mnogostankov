<?php
namespace Braintree\MerchantAccount;

use Braintree\Base;

class BusinessDetails extends Base
{
    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);
        return $instance;
    }

    protected function _initialize($businessAttribs)
    {
        $this->_attributes = $businessAttribs;
        if (isset($businessAttribs['address'])) {
            $this->_set('addressDetails', new AddressDetails($businessAttribs['address']));
        }
    }
}
class_alias('Braintree\MerchantAccount\BusinessDetails', 'Braintree_MerchantAccount_BusinessDetails');

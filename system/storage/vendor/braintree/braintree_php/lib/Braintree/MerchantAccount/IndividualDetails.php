<?php
namespace Braintree\MerchantAccount;

use Braintree\Base;

class IndividualDetails extends Base
{
    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);
        return $instance;
    }

    protected function _initialize($individualAttribs)
    {
        $this->_attributes = $individualAttribs;
        if (isset($individualAttribs['address'])) {
            $this->_set('addressDetails', new AddressDetails($individualAttribs['address']));
        }
    }
}
class_alias('Braintree\MerchantAccount\IndividualDetails', 'Braintree_MerchantAccount_IndividualDetails');

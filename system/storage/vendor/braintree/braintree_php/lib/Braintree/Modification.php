<?php
namespace Braintree;

class Modification extends Base
{
    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);
        return $instance;
    }

    protected function _initialize($attributes)
    {
        $this->_attributes = $attributes;
    }

    public function __toString() {
        return get_called_class() . '[' . Util::attributesToString($this->_attributes) . ']';
    }
}
class_alias('Braintree\Modification', 'Braintree_Modification');

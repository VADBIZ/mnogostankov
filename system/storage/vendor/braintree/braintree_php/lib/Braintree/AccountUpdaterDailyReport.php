<?php
namespace Braintree;

/**
 * Creates an instance of AccountUpdaterDailyReport
 *
 *
 * @package    Braintree
 *
 * @property-read string $reportUrl
 * @property-read date   $reportDate
 */
class AccountUpdaterDailyReport extends Base
{
    protected $_attributes = [];

    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);
        return $instance;
    }

    protected function _initialize($disputeAttribs)
    {
        $this->_attributes = $disputeAttribs;
    }

    public function  __toString()
    {
        $display = [
            'reportDate', 'reportUrl'
            ];

        $displayAttributes = [];
        foreach ($display AS $attrib) {
            $displayAttributes[$attrib] = $this->$attrib;
        }
        return __CLASS__ . '[' .
                Util::attributesToString($displayAttributes) .']';
    }
}
class_alias('Braintree\AccountUpdaterDailyReport', 'Braintree_AccountUpdaterDailyReport');

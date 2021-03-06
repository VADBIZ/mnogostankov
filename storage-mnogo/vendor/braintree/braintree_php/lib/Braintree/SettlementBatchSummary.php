<?php
namespace Braintree;

/**
 * @property-read array $records
 */
class SettlementBatchSummary extends Base
{
    /**
     *
     * @param array $attributes
     * @return SettlementBatchSummary
     */
    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);
        return $instance;
    }

    /**
     * @ignore
     * @param array $attributes
     */
    protected function _initialize($attributes)
    {
        $this->_attributes = $attributes;
    }

    /**
     * static method redirecting to gateway
     *
     * @param string $settlement_date Date YYYY-MM-DD
     * @param string $groupByCustomField
     * @return Result\Successful|Result\Error
     */
    public static function generate($settlement_date, $groupByCustomField = NULL)
    {
        return Configuration::gateway()->settlementBatchSummary()->generate($settlement_date, $groupByCustomField);
    }

    public function records()
    {
        return $this->_attributes['records'];
    }
}
class_alias('Braintree\SettlementBatchSummary', 'Braintree_SettlementBatchSummary');

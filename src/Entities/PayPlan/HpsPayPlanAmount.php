<?php

/**
 * Class HpsPayPlanAmount
 */
class HpsPayPlanAmount
{
    public $value    = null;
    public $currency = 'USD';
    /**
     * HpsPayPlanAmount constructor.
     *
     * @param      $value
     * @param null $currency
     */
    public function __construct($value, $currency = null)
    {
        $this->value = $value;
        if ($currency != null) {
            $this->currency = $currency;
        }
    }
}

<?php

class HpsPayPlanAmount
{
    public $value    = null;
    public $currency = 'USD';

    public function __construct($value, $currency = null)
    {
        $this->value = $value;
        if ($currency != null) {
            $this->currency = $currency;
        }
    }
}

<?php

class HpsFluentDebitService extends HpsSoapGatewayService
{
    public function __construct($config = null)
    {
        parent::__construct($config);
    }

    public function withConfig($config)
    {
        $this->_config = $config;
        return $this;
    }

    public function addValue($amount = null)
    {
        $builder = new HpsDebitServiceAddValueBuilder($this);
        return $builder
            ->withAmount($amount);
    }

    public function charge($amount = null)
    {
        $builder = new HpsDebitServiceChargeBuilder($this);
        return $builder
            ->withAmount($amount);
    }

    public function returnDebit($transactionId = null)
    {
        $builder = new HpsDebitServiceReturnBuilder($this);
        return $builder
            ->withTransactionId($transactionId);
    }

    public function reverse($transactionId = null)
    {
        $builder = new HpsDebitServiceReverseBuilder($this);
        return $builder
            ->withTransactionId($transactionId);
    }
}

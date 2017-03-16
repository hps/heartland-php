<?php

/**
 * Class HpsFluentDebitService
 */
class HpsFluentDebitService extends HpsSoapGatewayService
{
    /**
     * HpsFluentDebitService constructor.
     *
     * @param null $config
     */
    public function __construct($config = null)
    {
        parent::__construct($config);
    }
    /**
     * @param $config
     *
     * @return $this
     */
    public function withConfig($config)
    {
        $this->_config = $config;
        return $this;
    }
    /**
     * @param null $amount
     *
     * @return \HpsDebitServiceAddValueBuilder
     */
    public function addValue($amount = null)
    {
        $builder = new HpsDebitServiceAddValueBuilder($this);
        return $builder
            ->withAmount($amount);
    }
    /**
     * @param null $amount
     *
     * @return \HpsDebitServiceChargeBuilder
     */
    public function charge($amount = null)
    {
        $builder = new HpsDebitServiceChargeBuilder($this);
        return $builder
            ->withAmount($amount);
    }
    /**
     * @param null $transactionId
     *
     * @return \HpsDebitServiceReturnBuilder
     */
    public function returnDebit($transactionId = null)
    {
        $builder = new HpsDebitServiceReturnBuilder($this);
        return $builder
            ->withTransactionId($transactionId);
    }
    /**
     * @param null $transactionId
     *
     * @return \HpsDebitServiceReverseBuilder
     */
    public function reverse($transactionId = null)
    {
        $builder = new HpsDebitServiceReverseBuilder($this);
        return $builder
            ->withTransactionId($transactionId);
    }
}

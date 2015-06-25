<?php

/**
 * A fluent interface for creating and executing a addValue
 * transaction through the HpsDebitService.
 *
 * @method HpsDebitServiceAddValueBuilder withAmount(double $amount)
 * @method HpsDebitServiceAddValueBuilder withCurrency(string $currency)
 * @method HpsDebitServiceAddValueBuilder withTrackData(string $trackData)
 * @method HpsDebitServiceAddValueBuilder withPinBlock(string $pinBlock)
 * @method HpsDebitServiceAddValueBuilder withEncryptionData(HpsEncryptionData $encryptionData)
 * @method HpsDebitServiceAddValueBuilder withAllowDuplicates(bool $allowDuplicates)
 * @method HpsDebitServiceAddValueBuilder withCardHolder(HpsCardHolder $cardHolder)
 * @method HpsDebitServiceAddValueBuilder withDetails(HpsTransactionDetails $details)
 * @method HpsDebitServiceAddValueBuilder withClientTransactionId(string $clientTransactionId)
 */
class HpsDebitServiceAddValueBuilder extends HpsBuilderAbstract
{
    /** @var double|null */
    protected $amount              = null;

    /** @var string|null */
    protected $currency            = null;

    /** @var string|null */
    protected $trackData           = null;

    /** @var string|null */
    protected $pinBlock            = null;

    /** @var HpsEncryptionData|null */
    protected $encryptionData      = null;

    /** @var bool|null */
    protected $allowDuplicates     = false;

    /** @var HpsCardHolder|null */
    protected $cardHolder          = null;

    /** @var HpsTransactionDetails|null */
    protected $details             = null;

    /** @var string|null */
    protected $clientTransactionId = null;

    /**
     * Instatiates a new HpsDebitServiceAddValueBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a addValue transaction through the HpsDebitService
     */
    public function execute()
    {
        parent::execute();

        $addValueSvc = new HpsDebitService($this->service->servicesConfig());
        return $addValueSvc->addValue(
            $this->amount,
            $this->currency,
            $this->trackData,
            $this->pinBlock,
            $this->encryptionData,
            $this->allowDuplicates,
            $this->cardHolder,
            $this->details,
            $this->clientTransactionId
        );
    }

    /**
     * Setups up validations for building addValues.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'amountNotNull'), 'HpsArgumentException', 'AddValue needs an amount')
            ->addValidation(array($this, 'currencyNotNull'), 'HpsArgumentException', 'AddValue needs an currency')
            ->addValidation(array($this, 'trackDataNotNull'), 'HpsArgumentException', 'AddValue needs an trackData')
            ->addValidation(array($this, 'pinBlockNotNull'), 'HpsArgumentException', 'AddValue needs an pinBlock');
    }

    /**
     * Ensures an amount has been set.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    protected function amountNotNull($actionCounts)
    {
        return isset($actionCounts['amount']);
    }

    /**
     * Ensures a currency has been set.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    protected function currencyNotNull($actionCounts)
    {
        return isset($actionCounts['currency']);
    }

    /**
     * Ensures a trackData has been set.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    protected function trackDataNotNull($actionCounts)
    {
        return isset($actionCounts['trackData']);
    }

    /**
     * Ensures a pinBlock has been set.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    protected function pinBlockNotNull($actionCounts)
    {
        return isset($actionCounts['pinBlock']);
    }
}

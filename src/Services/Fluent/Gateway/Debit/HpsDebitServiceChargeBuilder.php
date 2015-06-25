<?php

/**
 * A fluent interface for creating and executing a charge
 * transaction through the HpsDebitService.
 *
 * @method HpsDebitServiceChargeBuilder withAmount(double $amount)
 * @method HpsDebitServiceChargeBuilder withCurrency(string $currency)
 * @method HpsDebitServiceChargeBuilder withTrackData(string $trackData)
 * @method HpsDebitServiceChargeBuilder withPinBlock(string $pinBlock)
 * @method HpsDebitServiceChargeBuilder withEncryptionData(HpsEncryptionData $encryptionData)
 * @method HpsDebitServiceChargeBuilder withAllowDuplicates(bool $allowDuplicates)
 * @method HpsDebitServiceChargeBuilder withCashBackAmount(double $caseBackAmount)
 * @method HpsDebitServiceChargeBuilder withAllowPartialAuth(bool $allowPartialAuth)
 * @method HpsDebitServiceChargeBuilder withCardHolder(HpsCardHolder $cardHolder)
 * @method HpsDebitServiceChargeBuilder withDetails(HpsTransactionDetails $details)
 * @method HpsDebitServiceChargeBuilder withClientTransactionId(string $clientTransactionId)
 */
class HpsDebitServiceChargeBuilder extends HpsBuilderAbstract
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

    /** @var double|null */
    protected $cashBackAmount      = null;

    /** @var bool|null */
    protected $allowPartialAuth    = false;

    /** @var HpsCardHolder|null */
    protected $cardHolder          = null;

    /** @var HpsTransactionDetails|null */
    protected $details             = null;

    /** @var string|null */
    protected $clientTransactionId = null;

    /**
     * Instatiates a new HpsDebitServiceChargeBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a charge transaction through the HpsDebitService
     */
    public function execute()
    {
        parent::execute();

        $chargeSvc = new HpsDebitService($this->service->servicesConfig());
        return $chargeSvc->charge(
            $this->amount,
            $this->currency,
            $this->trackData,
            $this->pinBlock,
            $this->encryptionData,
            $this->allowDuplicates,
            $this->cashBackAmount,
            $this->allowPartialAuth,
            $this->cardHolder,
            $this->details,
            $this->clientTransactionId
        );
    }

    /**
     * Setups up validations for building charges.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'amountNotNull'), 'HpsArgumentException', 'Charge needs an amount')
            ->addValidation(array($this, 'currencyNotNull'), 'HpsArgumentException', 'Charge needs an currency')
            ->addValidation(array($this, 'trackDataNotNull'), 'HpsArgumentException', 'Charge needs an trackData')
            ->addValidation(array($this, 'pinBlockNotNull'), 'HpsArgumentException', 'Charge needs an pinBlock');
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

<?php

/**
 * A fluent interface for creating and executing a return
 * transaction through the HpsDebitService.
 *
 * @method HpsDebitServiceReturnBuilder withTransactionId(string $transactionId)
 * @method HpsDebitServiceReturnBuilder withAmount(double $amount)
 * @method HpsDebitServiceReturnBuilder withTrackData(string $trackData)
 * @method HpsDebitServiceReturnBuilder withPinBlock(string $pinBlock)
 * @method HpsDebitServiceReturnBuilder withEncryptionData(HpsEncryptionData $encryptionData)
 * @method HpsDebitServiceReturnBuilder withAllowDuplicates(bool $allowDuplicates)
 * @method HpsDebitServiceReturnBuilder withCardHolder(HpsCardHolder $cardHolder)
 * @method HpsDebitServiceReturnBuilder withDetails(HpsTransactionDetails $details)
 * @method HpsDebitServiceReturnBuilder withClientTransactionId(string $clientTransactionId)
 */
class HpsDebitServiceReturnBuilder extends HpsBuilderAbstract
{
    /** @var string|null */
    protected $transactionId       = null;

    /** @var double|null */
    protected $amount              = null;

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
     * Instatiates a new HpsDebitServiceReturnBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a return transaction through the HpsDebitService
     */
    public function execute()
    {
        parent::execute();

        $returnSvc = new HpsDebitService($this->service->servicesConfig());
        return $returnSvc->returnDebit(
            $this->transactionId,
            $this->amount,
            $this->trackData,
            $this->pinBlock,
            $this->allowDuplicates,
            $this->cardHolder,
            $this->encryptionData,
            $this->details,
            $this->clientTransactionId
        );
    }

    /**
     * Setups up validations for building returns.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'transactionIdNotNull'), 'HpsArgumentException', 'Return needs an transactionId')
            ->addValidation(array($this, 'amountNotNull'), 'HpsArgumentException', 'Return needs an amount')
            ->addValidation(array($this, 'trackDataNotNull'), 'HpsArgumentException', 'Return needs an trackData')
            ->addValidation(array($this, 'pinBlockNotNull'), 'HpsArgumentException', 'Return needs an pinBlock');
    }

    /**
     * Ensures a transactionId has been set.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    protected function transactionIdNotNull($actionCounts)
    {
        return isset($actionCounts['transactionId']);
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

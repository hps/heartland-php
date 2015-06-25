<?php

/**
 * A fluent interface for creating and executing a reverse
 * transaction through the HpsDebitService.
 *
 * @method HpsDebitServiceReverseBuilder withTransactionId(string $transactionId)
 * @method HpsDebitServiceReverseBuilder withAmount(double $amount)
 * @method HpsDebitServiceReverseBuilder withTrackData(string $trackData)
 * @method HpsDebitServiceReverseBuilder withAuthorizedAmount(double $authorizedAmount)
 * @method HpsDebitServiceReverseBuilder withEncryptionData(HpsEncryptionData $encryptionData)
 * @method HpsDebitServiceReverseBuilder withDetails(HpsTransactionDetails $details)
 * @method HpsDebitServiceReverseBuilder withClientTransactionId(string $clientTransactionId)
 */
class HpsDebitServiceReverseBuilder extends HpsBuilderAbstract
{
    /** @var string|null */
    protected $transactionId       = null;

    /** @var double|null */
    protected $amount              = null;

    /** @var string|null */
    protected $trackData           = null;

    /** @var double|null */
    protected $authorizedAmount    = null;

    /** @var HpsEncryptionData|null */
    protected $encryptionData      = null;

    /** @var HpsTransactionDetails|null */
    protected $details             = null;

    /** @var string|null */
    protected $clientTransactionId = null;

    /**
     * Instatiates a new HpsDebitServiceReverseBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a reverse transaction through the HpsDebitService
     */
    public function execute()
    {
        parent::execute();

        $reverseSvc = new HpsDebitService($this->service->servicesConfig());
        return $reverseSvc->reverse(
            $this->transactionId,
            $this->amount,
            $this->trackData,
            $this->authorizedAmount,
            $this->encryptionData,
            $this->details,
            $this->clientTransactionId
        );
    }

    /**
     * Setups up validations for building reverses.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'transactionIdNotNull'), 'HpsArgumentException', 'Reverse needs an transactionId')
            ->addValidation(array($this, 'amountNotNull'), 'HpsArgumentException', 'Reverse needs an amount')
            ->addValidation(array($this, 'trackDataNotNull'), 'HpsArgumentException', 'Reverse needs an trackData');
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
}

<?php

/**
 * A fluent interface for creating and executing a capture
 * transaction through the HpsCreditService.
 *
 * @method HpsCreditServiceCaptureBuilder withTransactionId(string $transactionId)
 * @method HpsCreditServiceCaptureBuilder withAmount(double $amount)
 * @method HpsCreditServiceCaptureBuilder withGratuity(double $gratuity)
 * @method HpsCreditServiceCaptureBuilder withClientTransactionId(string $clientTransactionId)
 * @method HpsCreditServiceCaptureBuilder withDirectMarketData(HpsDirectMarketData $directMarketData)
 */
class HpsCreditServiceCaptureBuilder extends HpsBuilderAbstract
{
    /** @var string|null */
    protected $transactionId       = null;

    /** @var double|null */
    protected $amount              = null;

    /** @var double|null */
    protected $gratuity            = null;

    /** @var string|null */
    protected $clientTransactionId = null;

    /** @var HpsDirectMarketData|null */
    protected $directMarketData    = null;

    /**
     * Instatiates a new HpsCreditServiceCaptureBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a capture transaction through the HpsCreditService
     */
    public function execute()
    {
        parent::execute();

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsCreditAddToBatch = $xml->createElement('hps:CreditAddToBatch');

        $hpsCreditAddToBatch->appendChild($xml->createElement('hps:GatewayTxnId', $this->transactionId));
        if ($this->amount != null) {
            $amount = sprintf("%0.2f", round($this->amount, 3));
            $hpsCreditAddToBatch->appendChild($xml->createElement('hps:Amt', $amount));
        }
        if ($this->gratuity != null) {
            $hpsCreditAddToBatch->appendChild($xml->createElement('hps:GratuityAmtInfo', $this->gratuity));
        }

        if ($this->directMarketData != null && $this->directMarketData->invoiceNumber != null) {
            $hpsCreditAddToBatch->appendChild($this->_hydrateDirectMarketData($this->directMarketData, $xml));
        }

        $hpsTransaction->appendChild($hpsCreditAddToBatch);
        $response = $this->doRequest($hpsTransaction);
        $this->_processChargeGatewayResponse($response, 'CreditAddToBatch');

        return $this->service
            ->get($this->transactionId)
            ->execute();
    }

    /**
     * Setups up validations for building captures.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'transactionIdNotNull'), 'HpsArgumentException', 'Capture needs a transactionId');
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
}

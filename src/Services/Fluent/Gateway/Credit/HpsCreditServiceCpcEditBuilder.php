<?php

/**
 * A fluent interface for creating and executing a cpcEdit
 * transaction through the HpsCreditService.
 *
 * @method HpsCreditServiceCpcEditBuilder withTransactionId(string $transactionId)
 * @method HpsCreditServiceCpcEditBuilder withDirectMarketData(HpsCPCData $cpcData)
 */
class HpsCreditServiceCpcEditBuilder extends HpsBuilderAbstract
{
    /** @var string|null */
    protected $transactionId = null;

    /** @var HpsCPCData|null */
    protected $cpcData       = null;

    /**
     * Instatiates a new HpsCreditServiceCpcEditBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a cpcEdit transaction through the HpsCreditService
     */
    public function execute()
    {
        parent::execute();

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsPosCreditCPCEdit = $xml->createElement('hps:CreditCPCEdit');
        $hpsPosCreditCPCEdit->appendChild($xml->createElement('hps:GatewayTxnId', $this->transactionId));
        $hpsPosCreditCPCEdit->appendChild($this->service->_hydrateCPCData($this->cpcData, $xml));
        $hpsTransaction->appendChild($hpsPosCreditCPCEdit);

        return $this->service->_submitTransaction($hpsTransaction, 'CreditCPCEdit');
    }

    /**
     * Setups up validations for building cpcEdits.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'transactionIdNotNull'), 'HpsArgumentException', 'CpcEdit needs a transactionId')
            ->addValidation(array($this, 'cpcDataNotNull'), 'HpsArgumentException', 'CpcEdit needs cpcData');
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
     * Ensures cpcData has been set.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    protected function cpcDataNotNull($actionCounts)
    {
        return isset($actionCounts['cpcData']);
    }
}

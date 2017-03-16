<?php

/**
 * A fluent interface for creating and executing a get
 * transaction through the HpsCreditService.
 *
 * @method HpsCreditServiceGetBuilder withTransactionId(string $transactionId)
 */
class HpsCreditServiceGetBuilder extends HpsBuilderAbstract
{
    /** @var string|null */
    protected $transactionId = null;

    /**
     * Instatiates a new HpsCreditServiceGetBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a get transaction through the HpsCreditService
     */
    public function execute()
    {
        parent::execute();

        if ($this->transactionId <= 0) {
            throw new HpsArgumentException('Invalid Transaction Id',HpsExceptionCodes::INVALID_ORIGINAL_TRANSACTION);
        }

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsReportTxnDetail = $xml->createElement('hps:ReportTxnDetail');
        $hpsReportTxnDetail->appendChild($xml->createElement('hps:TxnId', $this->transactionId));
        $hpsTransaction->appendChild($hpsReportTxnDetail);

        return $this->service->_submitTransaction($hpsTransaction, 'ReportTxnDetail');
    }

    /**
     * Setups up validations for building edits.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'transactionIdNotNull'), 'HpsArgumentException', 'Get needs a transactionId');
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

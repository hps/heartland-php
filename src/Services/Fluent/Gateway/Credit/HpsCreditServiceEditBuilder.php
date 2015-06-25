<?php

/**
 * A fluent interface for creating and executing an edit
 * transaction through the HpsCreditService.
 *
 * @method HpsCreditServiceEditBuilder withTransactionId(string $transactionId)
 * @method HpsCreditServiceEditBuilder withAmount(double $amount)
 * @method HpsCreditServiceEditBuilder withGratuity(double $gratuity)
 * @method HpsCreditServiceEditBuilder withClientTransactionId(string $clientTransactionId)
 */
class HpsCreditServiceEditBuilder extends HpsBuilderAbstract
{
    /** @var string|null */
    protected $transactionId       = null;

    /** @var double|null */
    protected $amount              = null;

    /** @var double|null */
    protected $gratuity            = null;

    /** @var string|null */
    protected $clientTransactionId = null;

    /**
     * Instatiates a new HpsCreditServiceEditBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates an edit transaction through the HpsCreditService
     */
    public function execute()
    {
        parent::execute();

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsCreditTxnEdit = $xml->createElement('hps:CreditTxnEdit');

        $hpsCreditTxnEdit->appendChild($xml->createElement('hps:GatewayTxnId', $this->transactionId));
        if ($this->amount != null) {
            $amount = sprintf('%0.2f', round($this->amount, 3));
            $hpsCreditTxnEdit->appendChild($xml->createElement('hps:Amt', $amount));
        }
        if ($this->gratuity != null) {
            $hpsCreditTxnEdit->appendChild($xml->createElement('hps:GratuityAmtInfo', $this->gratuity));
        }

        $hpsTransaction->appendChild($hpsCreditTxnEdit);
        $trans = $this->service->_submitTransaction($hpsTransaction, 'CreditTxnEdit', $this->clientTransactionId);

        $trans->responseCode = '00';
        $trans->responseText = '';

        return $trans;
    }

    /**
     * Setups up validations for building edits.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'transactionIdNotNull'), 'HpsArgumentException', 'Edit needs a transactionId');
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

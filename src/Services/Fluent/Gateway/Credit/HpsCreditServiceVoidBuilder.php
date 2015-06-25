<?php

/**
 * A fluent interface for creating and executing a void
 * transaction through the HpsCreditService.
 *
 * @method HpsCreditServiceVoidBuilder withTransactionId(string $transactionId)
 * @method HpsCreditServiceVoidBuilder withClientTransactionId(string $clientTransactionId)
 */
class HpsCreditServiceVoidBuilder extends HpsBuilderAbstract
{
    /** @var string|null */
    protected $transactionId       = null;

    /** @var string|null */
    protected $clientTransactionId = null;

    /**
     * Instatiates a new HpsCreditServiceVoidBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a void transaction through the HpsCreditService
     */
    public function execute()
    {
        parent::execute();

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsCreditVoid = $xml->createElement('hps:CreditVoid');
        $hpsCreditVoid->appendChild($xml->createElement('hps:GatewayTxnId', $this->transactionId));
        $hpsTransaction->appendChild($hpsCreditVoid);

        return $this->service->_submitTransaction($hpsTransaction, 'CreditVoid', $this->clientTransactionId);
    }

    /**
     * Setups up validations for building edits.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'transactionIdNotNull'), 'HpsArgumentException', 'Void needs a transactionId');
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

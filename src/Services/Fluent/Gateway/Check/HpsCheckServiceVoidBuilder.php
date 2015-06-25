<?php

/**
 * A fluent interface for creating and executing a void
 * transaction through the HpsCheckService.
 *
 * @method HpsCheckServiceVoidBuilder withTransactionId(string $transactionId)
 * @method HpsCheckServiceVoidBuilder withClientTransactionId(string $clientTransactionId)
 */
class HpsCheckServiceVoidBuilder extends HpsBuilderAbstract
{
    /** @var string|null */
    protected $transactionId       = null;

    /** @var string|null */
    protected $clientTransactionId = null;

    /**
     * Instatiates a new HpsCheckServiceVoidBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a void transaction through the HpsCheckService
     */
    public function execute()
    {
        parent::execute();

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsCheckVoid = $xml->createElement('hps:CheckVoid');
        $hpsBlock1 = $xml->createElement('hps:Block1');

        if ($this->transactionId != null) {
            $hpsBlock1->appendChild($xml->createElement('hps:GatewayTxnId', $this->transactionId));
        } else if ($this->clientTransactionId != null) {
            $hpsBlock1->appendChild($xml->createElement('hps:ClientTxnId', $this->clientTransactionId));
        }

        $hpsCheckVoid->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCheckVoid);
        return $this->service->_submitTransaction($hpsTransaction, 'CheckVoid');
    }

    /**
     * Setups up validations for building voids.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'onlyOneTransactionId'), 'HpsArgumentException', 'Void can only use one transaction id');
    }

    /**
     * Ensures there is only one transaction id, and checks that
     * there is only one transactionId or one clientTransactionId
     * in use. Both cannot be used.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    public function onlyOneTransactionId($actionCounts)
    {
        return (isset($actionCounts['transactionId']) && $actionCounts['transactionId'] == 1
                && (!isset($actionCounts['clientTransactionId'])
                    || isset($actionCounts['clientTransactionId']) && $actionCounts['clientTransactionId'] == 0))
            || (isset($actionCounts['clientTransactionId']) && $actionCounts['clientTransactionId'] == 1
                && (!isset($actionCounts['transactionId'])
                    || isset($actionCounts['transactionId']) && $actionCounts['transactionId'] == 0));
    }
}

<?php

/*
 * @method HpsCreditServiceChargeBuilder withToken( $token)
* @method HpsCreditServiceUpdateTokenExpirationBuilder withExpMonth(string $transactionId)
* @method HpsCreditServiceUpdateTokenExpirationBuilder withExpYear(string $transactionId)
*/

/**
 * Class HpsCreditServiceUpdateTokenExpirationBuilder
 */
class HpsCreditServiceUpdateTokenExpirationBuilder extends HpsBuilderAbstract
{


    /** @var HpsTokenData|null */
    protected $token = null;
    /**
     * @var int
     */
    protected $expMonth   = 0;
    /**
     * @var int
     */
    protected $expYear    = 0;
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
        $hpsManageTokens = $xml->createElement('hps:ManageTokens');

        $hpsManageTokens->appendChild($xml->createElement('hps:TokenValue', trim((string)$this->token)));

        $hpsTokenActions = $xml->createElement('hps:TokenActions');
        $hpsSet = $xml->createElement('hps:Set');
        $hpsAttribute = $xml->createElement('hps:Attribute');

        $hpsAttribute->appendChild($xml->createElement('hps:Name', 'ExpMonth'));
        $hpsAttribute->appendChild($xml->createElement('hps:Value', (string)sprintf("%'.02d", (int)$this->expMonth)));

        $hpsSet->appendChild($hpsAttribute);

        $hpsAttribute = $xml->createElement('hps:Attribute');

        $hpsAttribute->appendChild($xml->createElement('hps:Name', 'ExpYear'));
        $hpsAttribute->appendChild($xml->createElement('hps:Value', (string)$this->expYear));

        $hpsSet->appendChild($hpsAttribute);

        $hpsTokenActions->appendChild($hpsSet);

        $hpsManageTokens->appendChild($hpsTokenActions);

        $hpsTransaction->appendChild($hpsManageTokens);
        $trans = $this->service->_submitTransaction($hpsTransaction, 'ManageTokens', null);

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
            ->addValidation(array($this, 'tokenNotNull'), 'HpsArgumentException', 'Edit needs a multi use token value');
    }

    /**
     * Ensures a token has been set.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    protected function tokenNotNull($actionCounts)
    {
        return isset($actionCounts['token']);
    }
}
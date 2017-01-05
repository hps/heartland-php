<?php

/**
 * A fluent interface for creating and executing a verify
 * transaction through the HpsCreditService.
 *
 * @method HpsCreditServiceVerifyBuilder withCard(HpsCreditCard $card)
 * @method HpsCreditServiceVerifyBuilder withToken(HpsTokenData $token)
 * @method HpsCreditServiceVerifyBuilder withTrackData(HpsTrackData $trackData)
 * @method HpsCreditServiceVerifyBuilder withCardHolder(HpsCardHolder $cardHolder)
 * @method HpsCreditServiceVerifyBuilder withRequestMultiUseToken(bool $requestMultiUseToken)
 * @method HpsCreditServiceVerifyBuilder withClientTransactionId(string $clientTransactionId)
 * @method HpsCreditServiceVerifyBuilder withCardPresent(bool $cardPresent)
 * @method HpsCreditServiceVerifyBuilder withReaderPresent(bool $readerPresent)
 */
class HpsCreditServiceVerifyBuilder extends HpsBuilderAbstract
{
    /** @var HpsCreditCard|null */
    protected $card                 = null;

    /** @var HpsTokenData|null */
    protected $token                = null;

    /** @var HpsTrackData|null */
    protected $trackData            = null;

    /** @var HpsCardHolder|null */
    protected $cardHolder           = null;

    /** @var bool */
    protected $requestMultiUseToken = false;

    /** @var string|null */
    protected $clientTransactionId  = null;

    /** @var bool */
    protected $cardPresent          = false;

    /** @var bool */
    protected $readerPresent        = false;

    /**
     * Instatiates a new HpsCreditServiceVerifyBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a verify transaction through the HpsCreditService
     */
    public function execute()
    {
        parent::execute();

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsCreditAccountVerify = $xml->createElement('hps:CreditAccountVerify');
        $hpsBlock1 = $xml->createElement('hps:Block1');

        if ($this->cardHolder != null) {
            $hpsBlock1->appendChild($this->service->_hydrateCardHolderData($this->cardHolder, $xml));
        }

        $cardData = $xml->createElement('hps:CardData');
        if ($this->card != null) {
            $cardData->appendChild($this->service->_hydrateManualEntry(
                $this->card,
                $xml,
                $this->cardPresent,
                $this->readerPresent
            ));
            if ($this->card->encryptionData != null) {
                $cardData->appendChild($this->service->_hydrateEncryptionData(
                    $this->card->encryptionData,
                    $xml
                ));
            }
        } else if ($this->token != null) {
            $cardData->appendChild($this->service->_hydrateTokenData(
                $this->token,
                $xml,
                $this->cardPresent,
                $this->readerPresent
            ));
        } else if ($this->trackData != null) {
            $cardData->appendChild($this->service->_hydrateTrackData($this->trackData, $xml));
            if ($this->trackData->encryptionData != null) {
                $cardData->appendChild($this->service->_hydrateEncryptionData(
                    $this->trackData->encryptionData,
                    $xml
                ));
            }
        }
        $cardData->appendChild($xml->createElement('hps:TokenRequest', ($this->requestMultiUseToken) ? 'Y' : 'N'));

        $hpsBlock1->appendChild($cardData);
        $hpsCreditAccountVerify->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCreditAccountVerify);

        return $this->service->_submitTransaction($hpsTransaction, 'CreditAccountVerify', $this->clientTransactionId);
    }

    /**
     * Setups up validations for building verifys.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'onlyOnePaymentMethod'), 'HpsArgumentException', 'Verify can only use one payment method');
    }

    /**
     * Ensures there is only one payment method, and checks that
     * there is only one card or one token in use. Both cannot be
     * used.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    public function onlyOnePaymentMethod($actionCounts)
    {
        $count = 0;
        if (isset($actionCounts['card'])) { $count++; }
        if (isset($actionCounts['token'])) { $count++; }
        if (isset($actionCounts['trackData'])) { $count++; }
        return 1 === $count;
    }
}

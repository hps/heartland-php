<?php

/**
 * A fluent interface for creating and executing an authorization
 * transaction through the HpsCreditService.
 *
 * @method HpsCreditServiceAuthorizeBuilder withAmount(double $amount)
 * @method HpsCreditServiceAuthorizeBuilder withCurrency(string $currency)
 * @method HpsCreditServiceAuthorizeBuilder withCard(HpsCreditCard $card)
 * @method HpsCreditServiceAuthorizeBuilder withToken(HpsTokenData $token)
 * @method HpsCreditServiceAuthorizeBuilder withTrackData(HpsTrackData $trackData)
 * @method HpsCreditServiceAuthorizeBuilder withCardHolder(HpsCardHolder $cardHolder)
 * @method HpsCreditServiceAuthorizeBuilder withRequestMultiUseToken(bool $requestMultiUseToken)
 * @method HpsCreditServiceAuthorizeBuilder withDetails(HpsTransactionDetails $details)
 * @method HpsCreditServiceAuthorizeBuilder withTxnDescriptor(string $txnDescriptor)
 * @method HpsCreditServiceAuthorizeBuilder withAllowPartialAuth(bool $allowPartialAuth)
 * @method HpsCreditServiceAuthorizeBuilder withCpcReq(bool $cpcReq)
 * @method HpsCreditServiceAuthorizeBuilder withAllowDuplicates(bool $allowDuplicates)
 * @method HpsCreditServiceAuthorizeBuilder withPaymentData(HpsPaymentData $paymentData)
 * @method HpsCreditServiceAuthorizeBuilder withCardPresent(bool $cardPresent)
 * @method HpsCreditServiceAuthorizeBuilder withReaderPresent(bool $readerPresent)
 * @method HpsCreditServiceAuthorizeBuilder withGratuity(double $gratuity)
 * @method HpsCreditServiceAuthorizeBuilder withAutoSubstantiation(HpsAutoSubstantiation $autoSubstantiation)
 * @method HpsCreditServiceAuthorizeBuilder withOriginalTxnReferenceData(HpsOriginalTxnReferenceData $originalTxnReferenceData)
 * @method HpsCreditServiceAuthorizeBuilder withDirectMarketData(HpsDirectMarketData $directMarketData)
 */
class HpsCreditServiceAuthorizeBuilder extends HpsBuilderAbstract
{
    /** @var double|null */
    protected $amount                   = null;

    /** @var string|null */
    protected $currency                 = null;

    /** @var HpsCreditCard|null */
    protected $card                     = null;

    /** @var HpsTokenData|null */
    protected $token                    = null;

    /** @var HpsTrackData|null */
    protected $trackData                = null;

    /** @var HpsCardHolder|null */
    protected $cardHolder               = null;

    /** @var bool */
    protected $requestMultiUseToken     = false;

    /** @var HpsTransactionDetails|null */
    protected $details                  = null;

    /** @var string|null */
    protected $txnDescriptor            = null;

    /** @var bool */
    protected $allowPartialAuth         = false;

    /** @var bool */
    protected $cpcReq                   = false;

    /** @var bool */
    protected $allowDuplicates          = false;

    /** @var HpsPaymentData|null */
    protected $paymentData              = null;

    /** @var bool */
    protected $cardPresent              = false;

    /** @var bool */
    protected $readerPresent            = false;

    /** @var double|null */
    protected $gratuity                 = null;

    /** @var HpsAutoSubstantiation|null */
    protected $autoSubstantiation       = null;

    /** @var HpsOriginalTxnReferenceData|null */
    protected $originalTxnReferenceData = null;

    /** @var HpsDirectMarketData|null */
    protected $directMarketData         = null;

    /**
     * Instatiates a new HpsCreditServiceAuthorizeBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates an authorization transaction through the HpsCreditService
     */
    public function execute()
    {
        parent::execute();

        HpsInputValidation::checkCurrency($this->currency);
        HpsInputValidation::checkAmount($this->amount);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsCreditAuth = $xml->createElement('hps:CreditAuth');
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:AllowDup', ($this->allowDuplicates ? 'Y' : 'N')));
        $hpsBlock1->appendChild($xml->createElement('hps:AllowPartialAuth', ($this->allowPartialAuth ? 'Y' : 'N')));
        $hpsBlock1->appendChild($xml->createElement('hps:Amt', $this->amount));

        if ($this->gratuity != null) {
            $hpsBlock1->appendChild($xml->createElement('hps:GratuityAmtInfo', $this->gratuity));
        }

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
                $cardData->appendChild($this->service->_hydrateEncryptionData($this->card->encryptionData, $xml));
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
                $cardData->appendChild($this->service->_hydrateEncryptionData($this->trackData->encryptionData, $xml));
            }
        } else if ($this->paymentData != null) {
            $manualEntry = $xml->createElement('hps:ManualEntry');
            $manualEntry->appendChild($xml->createElement('hps:CardNbr', $this->paymentData->applicationPrimaryAccountNumber));
            $expDate = (string)$this->paymentData->applicationExpirationDate;
            $manualEntry->appendChild($xml->createElement('hps:ExpMonth', substr($expDate, 2, 2)));
            $manualEntry->appendChild($xml->createElement('hps:ExpYear', '20'.substr($expDate, 0, 2)));
            $cardData->appendChild($manualEntry);
        }

        $cardData->appendChild($xml->createElement('hps:TokenRequest', ($this->requestMultiUseToken ? 'Y' : 'N')));

        $hpsBlock1->appendChild($cardData);

        if ($this->paymentData != null) {
            $hpsBlock1->appendChild($this->service->_hydrateSecureEcommerce($this->paymentData->paymentData, $xml));
        }

        if ($this->cpcReq == true) {
            $hpsBlock1->appendChild($xml->createElement('hps:CPCReq', 'Y'));
        }

        if ($this->details != null) {
            $hpsBlock1->appendChild($this->service->_hydrateAdditionalTxnFields($this->details, $xml));
        }

        if ($this->txnDescriptor != null) {
            $hpsBlock1->appendChild($xml->createElement('hps:TxnDescriptor', $this->txnDescriptor));
        }

        if ($this->autoSubstantiation != null) {
            $hpsBlock1->appendChild($this->service->_hydrateAutoSubstantiation($this->autoSubstantiation, $xml));
        }

        if ($this->originalTxnReferenceData != null) {
            $refElement = $xml->createElement('hps:OrigTxnRefData');
            $refElement->appendChild($xml->createElement('hps:AuthCode', $this->originalTxnReferenceData->authorizationCode));
            $refElement->appendChild($xml->createElement('hps:CardNbrLastFour', $this->originalTxnReferenceData->cardNumberLast4));
            $hpsBlock1->appendChild($refElement);
        }

        $hpsCreditAuth->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCreditAuth);

        return $this->service->_submitTransaction($hpsTransaction, 'CreditAuth', (isset($this->details->clientTransactionId) ? $this->details->clientTransactionId : null));
    }

    /**
     * Setups up validations for building authorizations.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'onlyOnePaymentMethod'), 'HpsArgumentException', 'Authorize can only use one payment method')
            ->addValidation(array($this, 'amountNotNull'), 'HpsArgumentException', 'Authorize needs an amount')
            ->addValidation(array($this, 'currencyNotNull'), 'HpsArgumentException', 'Authorize needs a currency');
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
        return (isset($actionCounts['card']) && $actionCounts['card'] == 1
                && (!isset($actionCounts['token'])
                    || isset($actionCounts['token']) && $actionCounts['token'] == 0))
            || (isset($actionCounts['token']) && $actionCounts['token'] == 1
                && (!isset($actionCounts['card'])
                    || isset($actionCounts['card']) && $actionCounts['card'] == 0));
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
     * Ensures a currency has been set.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    protected function currencyNotNull($actionCounts)
    {
        return isset($actionCounts['currency']);
    }
}

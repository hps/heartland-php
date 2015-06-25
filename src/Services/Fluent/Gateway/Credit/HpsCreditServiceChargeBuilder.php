<?php

/**
 * A fluent interface for creating and executing a charge
 * transaction through the HpsCreditService.
 *
 * @method HpsCreditServiceChargeBuilder withAmount(double $amount)
 * @method HpsCreditServiceChargeBuilder withCurrency(string $currency)
 * @method HpsCreditServiceChargeBuilder withCard(HpsCreditCard $card)
 * @method HpsCreditServiceChargeBuilder withToken(HpsTokenData $token)
 * @method HpsCreditServiceChargeBuilder withCardHolder(HpsCardHolder $cardHolder)
 * @method HpsCreditServiceChargeBuilder withRequestMultiUseToken(bool $requestMultiUseToken)
 * @method HpsCreditServiceChargeBuilder withDetails(HpsTransactionDetails $details)
 * @method HpsCreditServiceChargeBuilder withTxnDescriptor(string $txnDescriptor)
 * @method HpsCreditServiceChargeBuilder withAllowPartialAuth(bool $allowPartialAuth)
 * @method HpsCreditServiceChargeBuilder withCpcReq(bool $cpcReq)
 * @method HpsCreditServiceChargeBuilder withDirectMarketData(HpsDirectMarketData $directMarketData)
 * @method HpsCreditServiceChargeBuilder withAllowDuplicates(bool $allowDuplicates)
 * @method HpsCreditServiceChargeBuilder withGratuity(double $gratuity)
 */
class HpsCreditServiceChargeBuilder extends HpsBuilderAbstract
{
    /** @var double|null */
    protected $amount                   = null;

    /** @var string|null */
    protected $currency                 = null;

    /** @var HpsCreditCard|null */
    protected $card                     = null;

    /** @var HpsTokenData|null */
    protected $token                    = null;

    /** @var HpsCardHolder|null */
    protected $cardHolder               = null;

    /** @var bool|null */
    protected $requestMultiUseToken     = false;

    /** @var HpsTransactionDetails|null */
    protected $details                  = null;

    /** @var string|null */
    protected $txnDescriptor            = null;

    /** @var bool|null */
    protected $allowPartialAuth         = false;

    /** @var bool|null */
    protected $cpcReq                   = false;

    /** @var HpsDirectMarketData|null */
    protected $directMarketData     = null;

    /** @var bool|null */
    protected $allowDuplicates          = false;

    /** @var double|null */
    protected $gratuity                 = null;

    /** @var bool|null */
    protected $cardPresent              = false;

    /** @var bool|null */
    protected $readerPresent            = false;

    protected $originalTxnReferenceData = null;
    protected $paymentData              = null;

    /**
     * Instatiates a new HpsCreditServiceChargeBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a charge transaction through the HpsCreditService
     */
    public function execute()
    {
        parent::execute();

        HpsInputValidation::checkCurrency($this->currency);
        HpsInputValidation::checkAmount($this->amount);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsCreditSale = $xml->createElement('hps:CreditSale');
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
                $cardData->appendChild($this->service->_hydrateEncryptionData(
                    $this->card->encryptionData
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
            $cardData->appendChild($this->service->_hydrateTrackData($this->trackData));
            if ($this->trackData->encryptionData != null) {
                $cardData->appendChild($this->service->_hydrateEncryptionData(
                    $this->trackData->encryptionData
                ));
            }
        } else if ($this->paymentData != null) {
            $manualEntry = $xml->createElement('hps:ManualEntry');
            $manualEntry->appendChild($xml->createElement('hps:CardNbr', $this->paymentData->applicationPrimaryAccountNumber));
            $expDate = $this->paymentData->applicationExpirationDate;
            $manualEntry->appendChild($xml->createElement('hps:ExpMonth', substr($expDate, 2, 2)));
            $manualEntry->appendChild($xml->createElement('hps:ExpYear', '20' . substr($expDate, 0, 2)));
            $cardData->appendChild($manualEntry);
        }

        if ($this->cpcReq) {
            $hpsBlock1->appendChild($xml->createElement('hps:CPCReq', 'Y'));
        }

        $cardData->appendChild($xml->createElement('hps:TokenRequest', ($this->requestMultiUseToken ? 'Y' : 'N')));

        if ($this->details != null) {
            $hpsBlock1->appendChild($this->service->_hydrateAdditionalTxnFields($this->details, $xml));
        }

        if ($this->txnDescriptor != null && $this->txnDescriptor != '') {
            $hpsBlock1->appendChild($xml->createElement('hps:TxnDescriptor', $this->txnDescriptor));
        }

        if ($this->directMarketData != null && $this->directMarketData->invoiceNumber != null) {
            $hpsBlock1->appendChild($this->service->_hydrateDirectMarketData($this->directMarketData, $xml));
        }

        if ($this->originalTxnReferenceData != null) {
            $refElement = $xml->createElement('hps:OrigTxnRefData');
            $refElement->appendChild($xml->createElement('hps:AuthCode', $this->originalTxnReferenceData->authorizationCode));
            $refElement->appendChild($xml->createElement('hps:CardNbrLastFour', $this->originalTxnReferenceData->cardNumberLast4));
        }

        $hpsBlock1->appendChild($cardData);

        if ($this->paymentData != null) {
            $hpsBlock1->appendChild($this->service->_hydrateSecureEcommerce($this->paymentData->paymentData, $xml));
        }

        $hpsCreditSale->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCreditSale);

        return $this->service->_submitTransaction($hpsTransaction, 'CreditSale', (isset($details->clientTransactionId) ? $details->clientTransactionId : null));
    }

    /**
     * Setups up validations for building charges.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'amountNotNull'), 'HpsArgumentException', 'Charge needs an amount')
            ->addValidation(array($this, 'onlyOnePaymentMethod'), 'HpsArgumentException', 'Charge can only use one payment method')
            ->addValidation(array($this, 'currencyNotNull'), 'HpsArgumentException', 'Charge needs a currency');
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
        $methods = 0;
        if (isset($actionCounts['card']) && $actionCounts['card'] == 1) {
            $methods++;
        }
        if (isset($actionCounts['token']) && $actionCounts['token'] == 1) {
            $methods++;
        }
        if (isset($actionCounts['trackData']) && $actionCounts['trackData'] == 1) {
            $methods++;
        }
        return $methods == 1;
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

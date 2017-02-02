<?php

/**
 * A fluent interface for creating and executing a offline auth
 * transaction through the HpsCreditService.
 *
 * @method HpsCreditServiceOfflineAuthBuilder withAmount(double $amount)
 * @method HpsCreditServiceOfflineAuthBuilder withCurrency(string $currency)
 * @method HpsCreditServiceOfflineAuthBuilder withCard(HpsCreditCard $card)
 * @method HpsCreditServiceOfflineAuthBuilder withToken(HpsTokenData $token)
 * @method HpsCreditServiceOfflineAuthBuilder withTrackData(HpsTrackData $trackData)
 * @method HpsCreditServiceOfflineAuthBuilder withCardHolder(HpsCardHolder $cardHolder)
 * @method HpsCreditServiceOfflineAuthBuilder withRequestMultiUseToken(bool $requestMultiUseToken)
 * @method HpsCreditServiceOfflineAuthBuilder withDetails(HpsTransactionDetails $details)
 * @method HpsCreditServiceOfflineAuthBuilder withTxnDescriptor(string $txnDescriptor)
 * @method HpsCreditServiceOfflineAuthBuilder withCpcReq(bool $cpcReq)
 * @method HpsCreditServiceOfflineAuthBuilder withDirectMarketData(HpsDirectMarketData $directMarketData)
 * @method HpsCreditServiceOfflineAuthBuilder withAllowDuplicates(bool $allowDuplicates)
 * @method HpsCreditServiceOfflineAuthBuilder withPaymentData(HpsPaymentData $paymentData)
 * @method HpsCreditServiceOfflineAuthBuilder withCardPresent(bool $cardPresent)
 * @method HpsCreditServiceOfflineAuthBuilder withReaderPresent(bool $readerPresent)
 * @method HpsCreditServiceOfflineAuthBuilder withGratuity(double $gratuity)
 * @method HpsCreditServiceOfflineAuthBuilder withAutoSubstantiation(HpsAutoSubstantiation $autoSubstantiation)
 * @method HpsCreditServiceOfflineAuthBuilder withOfflineAuthCode(string $offlineAuthCode)
 * @method HpsCreditServiceOfflineAuthBuilder withConvenienceAmtInfo(double $convenienceAmtInfo)
 * @method HpsCreditServiceOfflineAuthBuilder withShippingAmtInfo(double $shippingAmtInfo) 
 */
class HpsCreditServiceOfflineAuthBuilder extends HpsBuilderAbstract
{
    /** @var double|null */
    protected $amount               = null;

    /** @var string|null */
    protected $currency             = null;

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

    /** @var HpsTransactionDetails|null */
    protected $details              = null;

    /** @var string|null */
    protected $txnDescriptor        = null;

    /** @var bool */
    protected $cpcReq               = false;

    /** @var HpsDirectMarketData|null */
    protected $directMarketData     = null;

    /** @var bool */
    protected $allowDuplicates      = false;

    /** @var HpsPaymentData|null */
    protected $paymentData          = null;

    /** @var bool */
    protected $cardPresent          = false;

    /** @var bool */
    protected $readerPresent        = false;

    /** @var double|null */
    protected $gratuity             = null;

    /** @var HpsAutoSubstantiation|null */
    protected $autoSubstantiation   = null;

    /** @var string|null */
    protected $offlineAuthCode      = null;
    
    /** @var double|null */
    protected $convenienceAmtInfo       = null;
    
    /** @var double|null */
    protected $shippingAmtInfo          = null;

    /**
     * Instatiates a new HpsCreditServiceOfflineAuthBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a offline auth transaction through the HpsCreditService
     */
    public function execute()
    {
        parent::execute();

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsCreditOfflineAuth = $xml->createElement('hps:CreditOfflineAuth');
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:AllowDup', ($this->allowDuplicates ? 'Y' : 'N')));
        $hpsBlock1->appendChild($xml->createElement('hps:Amt', $this->amount));
        
        //update convenienceAmtInfo if passed
        if ($this->convenienceAmtInfo != null && $this->convenienceAmtInfo != '') {
            $hpsBlock1->appendChild($xml->createElement('hps:ConvenienceAmtInfo', HpsInputValidation::checkAmount($this->convenienceAmtInfo)));
        }
        
         //update shippingAmtInfo if passed
        if ($this->shippingAmtInfo != null && $this->shippingAmtInfo != '') {
            $hpsBlock1->appendChild($xml->createElement('hps:ShippingAmtInfo', HpsInputValidation::checkAmount($this->shippingAmtInfo)));
        }

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
                $cardData->appendChild($this->service->_hydrateEncryptionData($this->trackData->encryptionData));
            }
        } else if ($this->paymentData != null) {
            $manualEntry = $xml->createElement('hps:ManualEntry');
            $manualEntry->appendChild($xml->createElement('hps:CardNbr', $this->paymentData->applicationPrimaryAccountNumber));
            $expDate = (string)$this->paymentData->applicationExpirationDate;
            $manualEntry->appendChild($xml->createElement('hps:ExpMonth', substr($expDate, 2, 2)));
            $manualEntry->appendChild($xml->createElement('hps:ExpYear', substr($expDate, 0, 2)));
            $cardData->appendChild($manualEntry);
        }

        $cardData->appendChild($xml->createElement('hps:TokenRequest', ($this->requestMultiUseToken ? 'Y' : 'N')));

        $hpsBlock1->appendChild($cardData);

        if ($this->paymentData != null) {
            $hpsBlock1->appendChild($this->service->_hydrateSecureEcommerce($this->paymentData->paymentData));
        }

        if ($this->cpcReq == true) {
            $hpsBlock1->appendChild($xml->createElement('hps:CPCReq', 'Y'));
        }

        if ($this->txnDescriptor != null) {
            $hpsBlock1->appendChild($xml->createElement('hps:TxnDescriptor', $this->txnDescriptor));
        }

        if ($this->autoSubstantiation != null) {
            $hpsBlock1->appendChild($this->service->_hydrateAutoSubstantiation($this->autoSubstantiation));
        }

        if ($this->offlineAuthCode != null) {
            $hpsBlock1->appendChild($xml->createElement('hps:OfflineAuthCode', $this->offlineAuthCode));
        }

        $hpsCreditOfflineAuth->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCreditOfflineAuth);

        return $this->service->_submitTransaction($hpsTransaction, 'CreditOfflineAuth', (isset($this->details->clientTransactionId) ? $this->details->clientTransactionId : null));
    }

    /**
     * Setups up validations for building offline auths.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'onlyOnePaymentMethod'), 'HpsArgumentException', 'Offline Auth can only use one payment method');
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
}

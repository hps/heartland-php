<?php

/**
 * A fluent interface for creating and executing a offline charge
 * transaction through the HpsCreditService.
 *
 * @method HpsCreditServiceOfflineChargeBuilder withAmount(double $amount)
 * @method HpsCreditServiceOfflineChargeBuilder withCurrency(string $currency)
 * @method HpsCreditServiceOfflineChargeBuilder withCard(HpsCreditCard $card)
 * @method HpsCreditServiceOfflineChargeBuilder withToken(HpsTokenData $token)
 * @method HpsCreditServiceOfflineChargeBuilder withTrackData(HpsTrackData $trackData)
 * @method HpsCreditServiceOfflineChargeBuilder withCardHolder(HpsCardHolder $cardHolder)
 * @method HpsCreditServiceOfflineChargeBuilder withRequestMultiUseToken(bool $requestMultiUseToken)
 * @method HpsCreditServiceOfflineChargeBuilder withDetails(HpsTransactionDetails $details)
 * @method HpsCreditServiceOfflineChargeBuilder withTxnDescriptor(string $txnDescriptor)
 * @method HpsCreditServiceOfflineChargeBuilder withCpcReq(bool $cpcReq)
 * @method HpsCreditServiceOfflineChargeBuilder withDirectMarketData(HpsDirectMarketData $directMarketData)
 * @method HpsCreditServiceOfflineChargeBuilder withAllowDuplicates(bool $allowDuplicates)
 * @method HpsCreditServiceOfflineChargeBuilder withPaymentData(HpsPaymentData $paymentData)
 * @method HpsCreditServiceOfflineChargeBuilder withCardPresent(bool $cardPresent)
 * @method HpsCreditServiceOfflineChargeBuilder withReaderPresent(bool $readerPresent)
 * @method HpsCreditServiceOfflineChargeBuilder withGratuity(double $gratuity)
 * @method HpsCreditServiceOfflineChargeBuilder withAutoSubstantiation(HpsAutoSubstantiation $autoSubstantiation)
 * @method HpsCreditServiceOfflineChargeBuilder withOfflineAuthCode(string $offlineAuthCode)
 * @method HpsCreditServiceOfflineChargeBuilder withConvenienceAmtInfo(double $convenienceAmtInfo)
 * @method HpsCreditServiceOfflineChargeBuilder withShippingAmtInfo(double $shippingAmtInfo)
 */
class HpsCreditServiceOfflineChargeBuilder extends HpsBuilderAbstract
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
     * Creates a offline charge transaction through the HpsCreditService
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
            if ($this->card->encryptionData != null) {
                $cardData->appendChild($this->service->_hydrateEncryptionData($this->card->encryptionData));
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
                $cardData->appendChild($this->service->_hydrateEncryptionData($this->trackData->encryptionData));
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
}

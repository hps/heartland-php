<?php

/**
 * A fluent interface for creating and executing a balance inquiry
 * transaction through the HpsCreditService.
 *
 * @method HpsCreditServicePrepaidBalanceInquiryBuilder withCard(HpsCreditCard $card)
 * @method HpsCreditServicePrepaidBalanceInquiryBuilder withToken(HpsTokenData $token)
 * @method HpsCreditServicePrepaidBalanceInquiryBuilder withTrackData(HpsTrackData $trackData)
 * @method HpsCreditServicePrepaidBalanceInquiryBuilder withCardHolder(HpsCardHolder $cardHolder)
 */
class HpsCreditServicePrepaidBalanceInquiryBuilder extends HpsBuilderAbstract
{
    /** @var HpsCreditCard|null */
    protected $card                 = null;

    /** @var HpsTokenData|null */
    protected $token                = null;

    /** @var HpsTrackData|null */
    protected $trackData            = null;

    /** @var HpsCardHolder|null */
    protected $cardHolder           = null;

    /**
     * Instatiates a new HpsCreditServicePrepaidBalanceInquiryBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a balance inquiry transaction through the HpsCreditService
     */
    public function execute()
    {
        parent::execute();

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsCreditAuth = $xml->createElement('hps:PrePaidBalanceInquiry');
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $cardData = $xml->createElement('hps:CardData');
        if ($this->card != null) {
            $cardData->appendChild($this->service->_hydrateManualEntry($this->card, $xml));
        } else if ($this->trackData != null) {
            $cardData->appendChild($this->service->_hydrateTrackData($this->trackData, $xml));
        } else if ($this->token != null) {
            $cardData->appendChild($this->service->_hydrateTokenData($this->token, $xml));
        }
        $hpsBlock1->appendChild($cardData);

        if ($this->cardHolder != null) {
            $hpsBlock1->appendChild($this->service->_hydrateCardHolderData($this->cardHolder, $xml));
        }

        $hpsCreditAuth->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCreditAuth);

        return $this->service->_submitTransaction($hpsTransaction, 'PrePaidBalanceInquiry');
    }

    /**
     * Setups up validations for building balance inquiries.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'onlyOnePaymentMethod'), 'HpsArgumentException', 'Balance Inquiry can only use one payment method');
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

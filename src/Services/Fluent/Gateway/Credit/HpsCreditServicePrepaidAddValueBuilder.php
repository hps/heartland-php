<?php

/**
 * A fluent interface for creating and executing an add value
 * transaction through the HpsCreditService.
 *
 * @method HpsCreditServicePrepaidAddValueBuilder withAmount(double $amount)
 * @method HpsCreditServicePrepaidAddValueBuilder withCurrency(string $currency)
 * @method HpsCreditServicePrepaidAddValueBuilder withAllowDuplicates(bool $allowDuplicates)
 * @method HpsCreditServicePrepaidAddValueBuilder withCard(HpsCreditCard $card)
 * @method HpsCreditServicePrepaidAddValueBuilder withToken(HpsTokenData $token)
 * @method HpsCreditServicePrepaidAddValueBuilder withTrackData(HpsTrackData $trackData)
 */
class HpsCreditServicePrepaidAddValueBuilder extends HpsBuilderAbstract
{
    /** @var double|null */
    protected $amount          = null;

    /** @var string|null */
    protected $currency          = null;

    /** @var bool */
    protected $allowDuplicates = false;

    /** @var HpsCreditCard|null */
    protected $card            = null;

    /** @var HpsTokenData|null */
    protected $token           = null;

    /** @var HpsTrackData|null */
    protected $trackData       = null;

    /**
     * Instatiates a new HpsCreditServicePrepaidAddValueBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates an add value transaction through the HpsCreditService
     */
    public function execute()
    {
        parent::execute();

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsCreditAuth = $xml->createElement('hps:PrePaidAddValue');
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:Amt', $this->amount));
        $hpsBlock1->appendChild($xml->createElement('hps:AllowDup', ($this->allowDuplicates ? 'Y' : 'N')));

        $cardData = $xml->createElement('hps:CardData');
        if ($this->card != null) {
            $cardData->appendChild($this->service->_hydrateManualEntry($this->card, $xml));
        } else if ($this->trackData != null) {
            $cardData->appendChild($this->service->_hydrateTrackData($this->trackData, $xml));
        } else if ($this->token != null) {
            $cardData->appendChild($this->service->_hydrateTokenData($this->token, $xml));
        }

        $hpsBlock1->appendChild($cardData);
        $hpsCreditAuth->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCreditAuth);

        return $this->service->_submitTransaction($hpsTransaction, 'PrePaidAddValue');
    }

    /**
     * Setups up validations for building balance inquiries.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'onlyOnePaymentMethod'), 'HpsArgumentException', 'Add Value can only use one payment method');
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

<?php

/**
 * A fluent interface for creating and executing a sale
 * transaction through the HpsGiftCardService.
 *
 * @method HpsGiftCardServiceSaleBuilder withCard(HpsGiftCard $card)
 * @method HpsGiftCardServiceSaleBuilder withToken(HpsTokenData $token)
 * @method HpsGiftCardServiceSaleBuilder withAmount(double $amount)
 * @method HpsGiftCardServiceSaleBuilder withCurrency(string $currency)
 * @method HpsGiftCardServiceRewardBuilder withGratuity(double $gratuity)
 * @method HpsGiftCardServiceRewardBuilder withTax(double $tax)
 */
class HpsGiftCardServiceSaleBuilder extends HpsBuilderAbstract
{
    /** @var HpsGiftCard|null */
    protected $card     = null;

    /** @var HpsTokenData|null */
    protected $token    = null;

    /** @var double|null */
    protected $amount   = null;

    /** @var string */
    protected $currency = 'usd';

    /** @var double|null */
    protected $gratuity = null;

    /** @var double|null */
    protected $tax      = null;

    /**
     * Instatiates a new HpsGiftCardServiceSaleBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a sale transaction through the HpsGiftCardService
     */
    public function execute()
    {
        parent::execute();

        HpsInputValidation::checkAmount($this->amount);
        $this->currency = strtolower($this->currency);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsGiftSale = $xml->createElement('hps:GiftCardSale');
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:Amt', $this->amount));
        if ($this->token != null && ($this->token instanceof HpsTokenData)) {
            if ($this->card == null) {
                $this->card = new HpsGiftCard();
            }
            $this->card->tokenValue = $this->token->tokenValue;
        }
        $cardData = $this->service->_hydrateGiftCardData($this->card, $xml);
        $hpsBlock1->appendChild($cardData);

        if (in_array($this->currency, array('points', 'usd'))) {
            $hpsBlock1->appendChild($xml->createElement('hps:Currency', strtoupper($this->currency)));
        }

        if ($this->gratuity != null) {
            $hpsBlock1->appendChild($xml->createElement('hps:GratuityAmtInfo', $this->gratuity));
        }

        if ($this->tax != null) {
            $hpsBlock1->appendChild($xml->createElement('hps:TaxAmtInfo', $this->tax));
        }

        $hpsGiftSale->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsGiftSale);

        return $this->service->_submitTransaction($hpsTransaction, 'GiftCardSale');
    }

    /**
     * Setups up validations for building sales.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'onlyOnePaymentMethod'), 'HpsArgumentException', 'Sale can only use one payment method')
            ->addValidation(array($this, 'amountNotNull'), 'HpsArgumentException', 'Sale needs an amount')
            ->addValidation(array($this, 'currencyNotNull'), 'HpsArgumentException', 'Sale needs a currency');
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
        return $methods == 1;
    }

    /**
     * Ensures a card has been set.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    protected function cardNotNull($actionCounts)
    {
        return isset($actionCounts['card']);
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

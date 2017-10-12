<?php

/**
 * A fluent interface for creating and executing an activate
 * transaction through the HpsGiftCardService.
 *
 * @method HpsGiftCardServiceActivateBuilder withCard(HpsGiftCard $card)
 * @method HpsGiftCardServiceActivateBuilder withToken(HpsTokenData $token)
 * @method HpsGiftCardServiceActivateBuilder withAmount(double $amount)
 * @method HpsGiftCardServiceActivateBuilder withCurrency(string $currency)
 */
class HpsGiftCardServiceActivateBuilder extends HpsBuilderAbstract
{
    /** @var HpsGiftCard|null */
    protected $card     = null;

    /** @var HpsTokenData|null */
    protected $token    = null;

    /** @var double|null */
    protected $amount   = null;

    /** @var string|null */
    protected $currency = null;

    /**
     * Instatiates a new HpsGiftCardServiceActivateBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates an activate transaction through the HpsGiftCardService
     */
    public function execute()
    {
        parent::execute();

        $activateSvc = new HpsGiftCardService($this->service->servicesConfig());
        if ($this->token != null && ($this->token instanceof HpsTokenData)) {
            if ($this->card == null) {
                $this->card = new HpsGiftCard();
            }
            $this->card->tokenValue = $this->token->tokenValue;
        }
        return $activateSvc->activate(
            $this->amount,
            $this->currency,
            $this->card
        );
    }

    /**
     * Setups up validations for building activates.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'onlyOnePaymentMethod'), 'HpsArgumentException', 'Activate can only use one payment method')
            ->addValidation(array($this, 'amountNotNull'), 'HpsArgumentException', 'Activate needs an amount')
            ->addValidation(array($this, 'currencyNotNull'), 'HpsArgumentException', 'Activate needs a currency');
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

<?php

/**
 * A fluent interface for creating and executing a balance
 * transaction through the HpsGiftCardService.
 *
 * @method HpsGiftCardServiceBalanceBuilder withCard(HpsGiftCard $card)
 * @method HpsGiftCardServiceBalanceBuilder withToken(HpsTokenData $token)
 */
class HpsGiftCardServiceBalanceBuilder extends HpsBuilderAbstract
{
    /** @var HpsGiftCard|null */
    protected $card     = null;

    /** @var HpsTokenData|null */
    protected $token    = null;

    /**
     * Instatiates a new HpsGiftCardServiceBalanceBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a balance transaction through the HpsGiftCardService
     */
    public function execute()
    {
        parent::execute();

        $balanceSvc = new HpsGiftCardService($this->service->servicesConfig());
        if ($this->token != null && ($this->token instanceof HpsTokenData)) {
            if ($this->card == null) {
                $this->card = new HpsGiftCard();
            }
            $this->card->tokenValue = $this->token->tokenValue;
        }
        return $balanceSvc->balance(
            $this->card
        );
    }

    /**
     * Setups up validations for building balances.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'onlyOnePaymentMethod'), 'HpsArgumentException', 'Balance can only use one payment method');
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
}

<?php

/**
 * A fluent interface for creating and executing a reward
 * transaction through the HpsGiftCardService.
 *
 * @method HpsGiftCardServiceRewardBuilder withCard(HpsGiftCard $card)
 * @method HpsGiftCardServiceRewardBuilder withToken(HpsTokenData $token)
 * @method HpsGiftCardServiceRewardBuilder withAmount(double $amount)
 * @method HpsGiftCardServiceRewardBuilder withCurrency(string $currency)
 * @method HpsGiftCardServiceRewardBuilder withGratuity(double $gratuity)
 * @method HpsGiftCardServiceRewardBuilder withTax(double $tax)
 */
class HpsGiftCardServiceRewardBuilder extends HpsBuilderAbstract
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
     * Instatiates a new HpsGiftCardServiceRewardBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a reward transaction through the HpsGiftCardService
     */
    public function execute()
    {
        parent::execute();

        $rewardSvc = new HpsGiftCardService($this->service->servicesConfig());
        if ($this->token != null && ($this->token instanceof HpsTokenData)) {
            if ($this->card == null) {
                $this->card = new HpsGiftCard();
            }
            $this->card->tokenValue = $this->token->tokenValue;
        }
        return $rewardSvc->reward(
            $this->card,
            $this->amount,
            $this->currency,
            $this->gratuity,
            $this->tax
        );
    }

    /**
     * Setups up validations for building rewards.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'onlyOnePaymentMethod'), 'HpsArgumentException', 'Reward can only use one payment method')
            ->addValidation(array($this, 'amountNotNull'), 'HpsArgumentException', 'Reward needs an amount');
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
}

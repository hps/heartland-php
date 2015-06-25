<?php

/**
 * A fluent interface for creating and executing a reward
 * transaction through the HpsGiftCardService.
 *
 * @method HpsGiftCardServiceRewardBuilder withCard(HpsGiftCard $card)
 * @method HpsGiftCardServiceRewardBuilder withAmount(double $amount)
 * @method HpsGiftCardServiceRewardBuilder withCurrency(string $currency)
 * @method HpsGiftCardServiceRewardBuilder withGratuity(double $gratuity)
 * @method HpsGiftCardServiceRewardBuilder withTax(double $tax)
 */
class HpsGiftCardServiceRewardBuilder extends HpsBuilderAbstract
{
    /** @var HpsGiftCard|null */
    protected $card     = null;

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
            ->addValidation(array($this, 'cardNotNull'), 'HpsArgumentException', 'Reward needs a card')
            ->addValidation(array($this, 'amountNotNull'), 'HpsArgumentException', 'Reward needs an amount');
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

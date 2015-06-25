<?php

/**
 * A fluent interface for creating and executing an activate
 * transaction through the HpsGiftCardService.
 *
 * @method HpsGiftCardServiceActivateBuilder withCard(HpsGiftCard $card)
 * @method HpsGiftCardServiceActivateBuilder withAmount(double $amount)
 * @method HpsGiftCardServiceActivateBuilder withCurrency(string $currency)
 */
class HpsGiftCardServiceActivateBuilder extends HpsBuilderAbstract
{
    /** @var HpsGiftCard|null */
    protected $card     = null;

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
            ->addValidation(array($this, 'cardNotNull'), 'HpsArgumentException', 'Activate needs a card')
            ->addValidation(array($this, 'amountNotNull'), 'HpsArgumentException', 'Activate needs an amount')
            ->addValidation(array($this, 'currencyNotNull'), 'HpsArgumentException', 'Activate needs a currency');
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

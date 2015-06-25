<?php

/**
 * A fluent interface for creating and executing an addValue
 * transaction through the HpsGiftCardService.
 *
 * @method HpsGiftCardServiceAddValueBuilder withCard(HpsGiftCard $card)
 * @method HpsGiftCardServiceAddValueBuilder withAmount(double $amount)
 * @method HpsGiftCardServiceAddValueBuilder withCurrency(string $currency)
 */
class HpsGiftCardServiceAddValueBuilder extends HpsBuilderAbstract
{
    /** @var HpsGiftCard|null */
    protected $card     = null;

    /** @var double|null */
    protected $amount   = null;

    /** @var string|null */
    protected $currency = null;

    /**
     * Instatiates a new HpsGiftCardServiceAddValueBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates an addValue transaction through the HpsGiftCardService
     */
    public function execute()
    {
        parent::execute();

        $addValueSvc = new HpsGiftCardService($this->service->servicesConfig());
        return $addValueSvc->addValue(
            $this->amount,
            $this->currency,
            $this->card
        );
    }

    /**
     * Setups up validations for building addValues.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'cardNotNull'), 'HpsArgumentException', 'AddValue needs a card')
            ->addValidation(array($this, 'amountNotNull'), 'HpsArgumentException', 'AddValue needs an amount')
            ->addValidation(array($this, 'currencyNotNull'), 'HpsArgumentException', 'AddValue needs a currency');
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

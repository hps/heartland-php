<?php

/**
 * A fluent interface for creating and executing a balance
 * transaction through the HpsGiftCardService.
 *
 * @method HpsGiftCardServiceBalanceBuilder withCard(HpsGiftCard $card)
 */
class HpsGiftCardServiceBalanceBuilder extends HpsBuilderAbstract
{
    /** @var HpsGiftCard|null */
    protected $card     = null;

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
            ->addValidation(array($this, 'cardNotNull'), 'HpsArgumentException', 'Balance needs a card');
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

<?php

/**
 * A fluent interface for creating and executing a deactivate
 * transaction through the HpsGiftCardService.
 *
 * @method HpsGiftCardServiceDeactivateBuilder withCard(HpsGiftCard $card)
 */
class HpsGiftCardServiceDeactivateBuilder extends HpsBuilderAbstract
{
    /** @var HpsGiftCard|null */
    protected $card     = null;

    /**
     * Instatiates a new HpsGiftCardServiceDeactivateBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a deactivate transaction through the HpsGiftCardService
     */
    public function execute()
    {
        parent::execute();

        $deactivateSvc = new HpsGiftCardService($this->service->servicesConfig());
        return $deactivateSvc->deactivate(
            $this->card
        );
    }

    /**
     * Setups up validations for building deactivates.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'cardNotNull'), 'HpsArgumentException', 'Deactivate needs a card');
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

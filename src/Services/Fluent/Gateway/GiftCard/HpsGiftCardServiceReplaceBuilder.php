<?php

/**
 * A fluent interface for creating and executing a replace
 * transaction through the HpsGiftCardService.
 *
 * @method HpsGiftCardServiceReplaceBuilder withOldCard(HpsGiftCard $oldCard)
 * @method HpsGiftCardServiceReplaceBuilder withNewCard(HpsGiftCard $newCard)
 */
class HpsGiftCardServiceReplaceBuilder extends HpsBuilderAbstract
{
    /** @var HpsGiftCard|null */
    protected $oldCard = null;

    /** @var HpsGiftCard|null */
    protected $newCard = null;

    /**
     * Instatiates a new HpsGiftCardServiceReplaceBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a replace transaction through the HpsGiftCardService
     */
    public function execute()
    {
        parent::execute();

        $replaceSvc = new HpsGiftCardService($this->service->servicesConfig());
        return $replaceSvc->replace(
            $this->oldCard,
            $this->newCard
        );
    }

    /**
     * Setups up validations for building replaces.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'oldCardNotNull'), 'HpsArgumentException', 'Replace needs an oldCard')
            ->addValidation(array($this, 'newCardNotNull'), 'HpsArgumentException', 'Replace needs a newCard');
    }

    /**
     * Ensures an oldCard has been set.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    protected function oldCardNotNull($actionCounts)
    {
        return isset($actionCounts['oldCard']);
    }

    /**
     * Ensures a newCard has been set.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    protected function newCardNotNull($actionCounts)
    {
        return isset($actionCounts['newCard']);
    }
}

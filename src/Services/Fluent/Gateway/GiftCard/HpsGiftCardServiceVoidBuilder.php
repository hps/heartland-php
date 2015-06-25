<?php

/**
 * A fluent interface for creating and executing a void
 * transaction through the HpsGiftCardService.
 *
 * @method HpsGiftCardServiceVoidBuilder withTransactionId(string $transactionId)
 */
class HpsGiftCardServiceVoidBuilder extends HpsBuilderAbstract
{
    /** @var string|null */
    protected $transactionId = null;

    /**
     * Instatiates a new HpsGiftCardServiceVoidBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a void transaction through the HpsGiftCardService
     */
    public function execute()
    {
        parent::execute();

        $voidSvc = new HpsGiftCardService($this->service->servicesConfig());
        return $voidSvc->void(
            $this->transactionId
        );
    }

    /**
     * Setups up validations for building voids.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'transactionIdNotNull'), 'HpsArgumentException', 'Void needs a transactionId');
    }

    /**
     * Ensures a transactionId has been set.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    protected function transactionIdNotNull($actionCounts)
    {
        return isset($actionCounts['transactionId']);
    }
}

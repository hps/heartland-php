<?php

/**
 * A fluent interface for creating and executing an override
 * transaction through the HpsCheckService.
 *
 * @method HpsCheckServiceOverrideBuilder withCheck(HpsCheck $check)
 * @method HpsCheckServiceOverrideBuilder withAmount(double $amount)
 * @method HpsCheckServiceOverrideBuilder withClientTransactionId(string $clientTransactionId)
 */
class HpsCheckServiceOverrideBuilder extends HpsBuilderAbstract
{
    /** @var HpsCheck|null */
    protected $check               = null;

    /** @var double|null */
    protected $amount              = null;

    /** @var string|null */
    protected $clientTransactionId = null;

    /**
     * Instatiates a new HpsCheckServiceOverrideBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates an override transaction through the HpsCheckService
     */
    public function execute()
    {
        parent::execute();

        return $this->service->_buildTransaction(
            'OVERRIDE',
            $this->check,
            $this->amount,
            $this->clientTransactionId
        );
    }

    /**
     * Setups up validations for building sales.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'checkNotNull'), 'HpsArgumentException', 'Override needs an check')
            ->addValidation(array($this, 'amountNotNull'), 'HpsArgumentException', 'Override needs an amount');
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
     * Ensures a check has been set.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    protected function checkNotNull($actionCounts)
    {
        return isset($actionCounts['check']);
    }
}

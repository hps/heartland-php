<?php

/**
 * A fluent interface for creating and executing a return
 * transaction through the HpsCheckService.
 *
 * @method HpsCheckServiceReturnBuilder withCheck(HpsCheck $check)
 * @method HpsCheckServiceReturnBuilder withAmount(double $amount)
 * @method HpsCheckServiceReturnBuilder withClientTransactionId(string $clientTransactionId)
 */
class HpsCheckServiceReturnBuilder extends HpsBuilderAbstract
{
    /** @var HpsCheck|null */
    protected $check               = null;

    /** @var double|null */
    protected $amount              = null;

    /** @var string|null */
    protected $clientTransactionId = null;

    /**
     * Instatiates a new HpsCheckServiceReturnBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a return transaction through the HpsCheckService
     */
    public function execute()
    {
        parent::execute();

        return $this->service->_buildTransaction(
            'RETURN',
            $this->check,
            $this->amount,
            $this->clientTransactionId
        );
    }

    /**
     * Setups up validations for building returns.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'checkNotNull'), 'HpsArgumentException', 'Return needs an check')
            ->addValidation(array($this, 'amountNotNull'), 'HpsArgumentException', 'Return needs an amount');
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

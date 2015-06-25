<?php

/**
 * A fluent interface for creating and executing a reverse
 * transaction through the HpsGiftCardService.
 *
 * @method HpsGiftCardServiceReverseBuilder withCard(HpsGiftCard $card)
 * @method HpsGiftCardServiceReverseBuilder withAmount(double $amount)
 * @method HpsGiftCardServiceReverseBuilder withCurrency(string $currency)
 * @method HpsGiftCardServiceReverseBuilder withTransactionId(string $transactionId)
 */
class HpsGiftCardServiceReverseBuilder extends HpsBuilderAbstract
{
    /** @var HpsGiftCard|null */
    protected $card          = null;

    /** @var double|null */
    protected $amount        = null;

    /** @var string */
    protected $currency      = 'usd';

    /** @var string|null */
    protected $transactionId = null;

    /**
     * Instatiates a new HpsGiftCardServiceReverseBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a reverse transaction through the HpsGiftCardService
     */
    public function execute()
    {
        parent::execute();

        $reverseSvc = new HpsGiftCardService($this->service->servicesConfig());
        return $reverseSvc->reverse(
            isset($this->card) ? $this->card : $this->transactionId,
            $this->amount,
            $this->currency
        );
    }

    /**
     * Setups up validations for building reverses.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'cardOrTransactionId'), 'HpsArgumentException', 'Reverse needs a card')
            ->addValidation(array($this, 'amountNotNull'), 'HpsArgumentException', 'Reverse needs an amount');
    }

    /**
     * Ensures a card has been set.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    protected function cardOrTransactionId($actionCounts)
    {
        return (isset($actionCounts['card']) && $actionCounts['card'] == 1
                && (!isset($actionCounts['transactionId'])
                    || isset($actionCounts['transactionId']) && $actionCounts['transactionId'] == 0))
            || (isset($actionCounts['transactionId']) && $actionCounts['transactionId'] == 1
                && (!isset($actionCounts['card'])
                    || isset($actionCounts['card']) && $actionCounts['card'] == 0));
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

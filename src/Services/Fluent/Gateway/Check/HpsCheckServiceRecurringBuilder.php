<?php

/**
 * A fluent interface for creating and executing a sale
 * transaction through the HpsCheckService.
 *
 * @method HpsCheckServiceRecurringBuilder withPaymentMethodKey(string $paymentMethodKey)
 * @method HpsCheckServiceRecurringBuilder withAmount(double $amount)
 * @method HpsCheckServiceRecurringBuilder withSchedule(HpsPayPlanSchedule|string $schedule)
 * @method HpsCheckServiceRecurringBuilder withOneTime(bool $oneTime)
 */
class HpsCheckServiceRecurringBuilder extends HpsBuilderAbstract
{
    /** @var string|null */
    protected $paymentMethodKey = null;

    /** @var double|null */
    protected $amount           = null;

    /** @var HpsPayPlanSchedule|string|null */
    protected $schedule         = null;

    /** @var bool */
    protected $oneTime          = false;

    /**
     * Instatiates a new HpsCheckServiceRecurringBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a sale transaction through the HpsCheckService
     */
    public function execute()
    {
        parent::execute();

        HpsInputValidation::checkAmount($this->amount);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsCheckSale = $xml->createElement('hps:CheckSale');
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:Amt', sprintf("%0.2f", round($this->amount, 3))));
        $hpsBlock1->appendChild($xml->createElement('hps:CheckAction', 'SALE'));
        $hpsBlock1->appendChild($xml->createElement('hps:PaymentMethodKey', $this->paymentMethodKey));

        $recurringData = $xml->createElement('hps:RecurringData');
        if ($this->schedule != null) {
            $scheduleKey = $this->schedule;
            if ($this->schedule instanceof HpsPayPlanSchedule) {
                $scheduleKey = $this->schedule->scheduleKey;
            }
            $recurringData->appendChild($xml->createElement('hps:ScheduleID', $scheduleKey));
        }
        $recurringData->appendChild($xml->createElement('hps:OneTime', ($this->oneTime ? 'Y' : 'N')));

        $hpsBlock1->appendChild($recurringData);
        $hpsCheckSale->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCheckSale);

        return $this->service->_submitTransaction($hpsTransaction, 'CheckSale');
    }

    /**
     * Setups up validations for building sales.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'paymentMethodKeyNotNull'), 'HpsArgumentException', 'Sale needs a payment method key')
            ->addValidation(array($this, 'amountNotNull'), 'HpsArgumentException', 'Sale needs an amount');
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
     * Ensures a paymentMethodKey has been set.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    protected function paymentMethodKeyNotNull($actionCounts)
    {
        return isset($actionCounts['paymentMethodKey']) && $actionCounts['paymentMethodKey'] == 1;
    }
}

<?php

/**
 * A fluent interface for creating and executing a recurring billing
 * transaction through the HpsCreditService.
 *
 * @method HpsCreditServiceRecurringBuilder withSchedule(HpsPayPlanSchedule|string $schedule)
 * @method HpsCreditServiceRecurringBuilder withAmount(double $amount)
 * @method HpsCreditServiceRecurringBuilder withCard(HpsCreditCard $card)
 * @method HpsCreditServiceRecurringBuilder withToken(HpsTokenData $token)
 * @method HpsCreditServiceRecurringBuilder withPaymentMethodKey(string $paymentMethodKey)
 * @method HpsCreditServiceRecurringBuilder withOneTime(bool $oneTime)
 * @method HpsCreditServiceRecurringBuilder withCardHolder(HpsCardHolder $cardHolder)
 * @method HpsCreditServiceRecurringBuilder withDetails(HpsTransactionDetails $details)
 * @method HpsCreditServiceRecurringBuilder withAllowDuplicates(bool $allowDuplicates)
 */
class HpsCreditServiceRecurringBuilder extends HpsBuilderAbstract
{
    /** @var HpsPayPlanSchedule|string|null */
    protected $schedule         = null;

    /** @var double|null */
    protected $amount           = null;

    /** @var HpsCreditCard|null */
    protected $card             = null;

    /** @var HpsTokenData|null */
    protected $token            = null;

    /** @var string|null */
    protected $paymentMethodKey = null;

    /** @var bool */
    protected $oneTime          = false;

    /** @var HpsCardHolder|null */
    protected $cardHolder       = null;

    /** @var HpsTransactionDetails|null */
    protected $details          = null;

    /** @var bool */
    protected $allowDuplicates  = false;

    /**
     * Instatiates a new HpsCreditServiceRecurringBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a recurring billing transaction through the HpsCreditService
     */
    public function execute()
    {
        parent::execute();

        HpsInputValidation::checkAmount($this->amount);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsRecurringBilling = $xml->createElement('hps:RecurringBilling');
        $hpsBlock1 = $xml->createElement('hps:Block1');

        $hpsBlock1->appendChild($xml->createElement('hps:AllowDup', 'Y'));
        $hpsBlock1->appendChild($xml->createElement('hps:Amt', $this->amount));
        if ($this->cardHolder != null) {
            $hpsBlock1->appendChild($this->service->_hydrateCardHolderData($this->cardHolder, $xml));
        }
        if ($this->details != null) {
            $hpsBlock1->appendChild($this->service->_hydrateAdditionalTxnFields($this->details, $xml));
        }

        if ($this->card != null) {
            $cardData = $xml->createElement('hps:CardData');
            $cardData->appendChild($this->service->_hydrateManualEntry($this->card, $xml));
            $hpsBlock1->appendChild($cardData);
        } else if ($this->token != null) {
            $cardData = $xml->createElement('hps:CardData');
            $cardData->appendChild($this->service->_hydrateTokenData($this->token, $xml));
            $hpsBlock1->appendChild($cardData);
        } else if ($this->paymentMethodKey != null) {
            $hpsBlock1->appendChild($xml->createElement('hps:PaymentMethodKey', $this->paymentMethodKey));
        }

        $recurringData = $xml->createElement('hps:RecurringData');
        if ($this->schedule != null) {
            $id = $this->schedule;
            if ($this->schedule instanceof HpsPayPlanSchedule) {
                $id = $this->schedule->scheduleIdentifier;
            }
            $recurringData->appendChild($xml->createElement('hps:ScheduleID', $id));
        }
        $recurringData->appendChild($xml->createElement('hps:OneTime', ($this->oneTime ? 'Y' : 'N')));

        $hpsBlock1->appendChild($recurringData);
        $hpsRecurringBilling->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsRecurringBilling);

        return $this->service->_submitTransaction($hpsTransaction, 'RecurringBilling', (isset($this->details->clientTransactionId) ? $this->details->clientTransactionId : null));
    }

    /**
     * Setups up validations for building recurring billings.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'onlyOnePaymentMethod'), 'HpsArgumentException', 'Recurring Billing can only use one payment method')
            ->addValidation(array($this, 'amountNotNull'), 'HpsArgumentException', 'Recurring Billing needs an amount');
    }

    /**
     * Ensures there is only one payment method, and checks that
     * there is only one card or one token in use. Both cannot be
     * used.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    public function onlyOnePaymentMethod($actionCounts)
    {
        $methods = 0;

        if (isset($actionCounts['card']) && $actionCounts['card'] == 1) {
            $methods++;
        }

        if (isset($actionCounts['token']) && $actionCounts['token'] == 1) {
            $methods++;
        }

        if (isset($actionCounts['paymentMethodKey']) && $actionCounts['paymentMethodKey'] == 1) {
            $methods++;
        }

        return $methods == 1;
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

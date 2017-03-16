<?php

/**
 * A fluent interface for creating and executing a listTransactions
 * transaction through the HpsCreditService.
 *
 * @method HpsCreditServiceListTransactionsBuilder withStartDate(string $startDate)
 * @method HpsCreditServiceListTransactionsBuilder withEndDate(string $endDate)
 * @method HpsCreditServiceListTransactionsBuilder withFilterBy(string $filterBy)
 * @method HpsCreditServiceListTransactionsBuilder withClientTransactionId(string $clientTransactionId)
 */
class HpsCreditServiceListTransactionsBuilder extends HpsBuilderAbstract
{
    /** @var string|null */
    protected $startDate = null;

    /** @var string|null */
    protected $endDate   = null;

    /** @var integer|string|null */
    protected $filterBy  = null;

    /**
     * Instatiates a new HpsCreditServiceListTransactionsBuilder
     *
     * @param HpsSoapGatewayService $service
     */
    public function __construct(HpsSoapGatewayService $service)
    {
        parent::__construct($service);
        $this->setUpValidations();
    }

    /**
     * Creates a listTransactions transaction through the HpsCreditService
     */
    public function execute()
    {
        parent::execute();

        date_default_timezone_set("UTC");
        $dateFormat = 'Y-m-d\TH:i:s.00\Z';
        $current = new DateTime();
        $currentTime = $current->format($dateFormat);

        HpsInputValidation::checkDateNotFuture($this->startDate);
        HpsInputValidation::checkDateNotFuture($this->endDate);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
        $hpsReportActivity = $xml->createElement('hps:ReportActivity');
        $hpsReportActivity->appendChild($xml->createElement('hps:RptStartUtcDT', $this->startDate));
        $hpsReportActivity->appendChild($xml->createElement('hps:RptEndUtcDT', $this->endDate));
        $hpsTransaction->appendChild($hpsReportActivity);

        return $this->service->_submitTransaction($hpsTransaction, 'ReportActivity');
    }

    /**
     * Setups up validations for building edits.
     *
     * @return null
     */
    private function setUpValidations()
    {
        $this
            ->addValidation(array($this, 'startDateNotNull'), 'HpsArgumentException', 'ListTransactions needs a startDate')
            ->addValidation(array($this, 'endDateNotNull'), 'HpsArgumentException', 'ListTransactions needs an endDate');
    }

    /**
     * Ensures a startDate has been set.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    protected function startDateNotNull($actionCounts)
    {
        return isset($actionCounts['startDate']);
    }

    /**
     * Ensures an endDate has been set.
     *
     * @param array $actionCounts
     *
     * @return bool
     */
    protected function endDateNotNull($actionCounts)
    {
        return isset($actionCounts['endDate']);
    }
}

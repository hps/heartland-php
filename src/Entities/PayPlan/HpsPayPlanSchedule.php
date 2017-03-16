<?php

/**
 * Class HpsPayPlanSchedule
 */
class HpsPayPlanSchedule extends HpsPayPlanResourceAbstract
{
    /** @var integer|null */
    public $scheduleKey               = null;

    /** @var string|null */
    public $scheduleIdentifier        = null;

    /** @var integer|null */
    public $customerKey               = null;

    /** @var string|null */
    public $scheduleName              = null;

    /** @var string|null */
    public $scheduleStatus            = null;

    /** @var string|null */
    public $paymentMethodKey          = null;

    /** @var object|array|null */
    public $subtotalAmount            = null;

    /** @var object|array|null */
    public $taxAmount                 = null;

    /** @var float|null */
    public $totalAmount               = null;

    /** @var integer|null */
    public $deviceId                  = null;

    /** @var string|null */
    public $startDate                 = null;

    /** @var string|null */
    public $processingDateInfo        = null;

    /** @var string|null */
    public $frequency                 = null;

    /** @var string|null */
    public $duration                  = null;

    /** @var string|null */
    public $endDate                   = null;

    /** @var integer|null */
    public $reprocessingCount         = null;

    /** @var string|null */
    public $emailReceipt              = null;

    /** @var string|null */
    public $emailAdvanceNotice        = null;

    /** @var string|null */
    public $nextProcessingDate        = null;

    /** @var string|null */
    public $previousProcessingDate    = null;

    /** @var integer|null */
    public $approvedTransactionCount  = null;

    /** @var integer|null */
    public $failureCount              = null;

    /** @var float|null */
    public $totalApprovedAmountToDate = null;

    /** @var integer|null */
    public $numberOfPayments          = null;

    /** @var integer|null */
    public $numberOfPaymentsRemaining = null;

    /** @var string|null */
    public $cancellationDate          = null;

    /** @var string|null */
    public $scheduleStarted           = null;

    /** @var string|null */
    public $invoiceNbr           = null;

    /** @var string|null */
    public $description           = null;
    /**
     * HpsPayPlanSchedule constructor.
     */
    public function __construct() {
        $this->emailReceipt = 'Never';
        $this->emailAdvanceNotice = 'No';
    }
    /**
     * @param \HpsPayPlanSchedule|null $schedule
     *
     * @return array
     */
    public static function getEditableFields( HpsPayPlanSchedule $schedule = null  )
    {
        $editableFields = array(
            'scheduleName',
            'scheduleStatus',
            'deviceId',
            'paymentMethodKey',
            'subtotalAmount',
            'taxAmount',
            'numberOfPaymentsRemaining',
            'endDate',
            'reprocessingCount',
            'emailReceipt',
            'emailAdvanceNotice',
            'processingDateInfo',
            'invoiceNbr',
            'description',
        );
        if ($schedule->scheduleStarted === true){
            $editableFields[] = 'cancellationDate';
            $editableFields[] = 'nextProcessingDate';
        }
        // Only editable when scheduleStarted = false
        else{
            $editableFields[] = 'scheduleIdentifier';
            $editableFields[] = 'startDate';
            $editableFields[] = 'frequency';
            $editableFields[] = 'duration';
        }
        return  $editableFields;
    }
    /**
     * @return array
     */
    public static function getSearchableFields()
    {
        return array(
            'scheduleIdentifier',
            'scheduleName',
            'deviceIdFilter',
            'deviceName',
            'customerIdentifier',
            'customerKey',
            'lastname',
            'company',
            'paymentMethodType',
            'paymentMethodKey',
            'achType',
            'accountType',
            'cardBrand',
            'totalAmount',
            'startDate',
            'previousProcessingDate',
            'nextProcessingDate',
            'frequency',
            'duration',
            'scheduleStatus',
        );
    }
    /**
     * @param $obj
     *
     * @return \HpsPayPlanSchedule
     */
    public static function fromStdClass($obj)
    {
        $ret = new HpsPayPlanSchedule();
        $ret->scheduleKey = property_exists($obj, 'scheduleKey') ? $obj->scheduleKey : null;
        $ret->scheduleIdentifier = property_exists($obj, 'scheduleIdentifier') ? $obj->scheduleIdentifier : null;
        $ret->customerKey = property_exists($obj, 'customerKey') ? $obj->customerKey : null;
        $ret->scheduleName = property_exists($obj, 'scheduleName') ? $obj->scheduleName : null;
        $ret->scheduleStatus = property_exists($obj, 'scheduleStatus') ? $obj->scheduleStatus : null;
        $ret->paymentMethodKey = property_exists($obj, 'paymentMethodKey') ? $obj->paymentMethodKey : null;
        $ret->subtotalAmount = property_exists($obj, 'subtotalAmount') ? $obj->subtotalAmount : null;
        $ret->taxAmount = property_exists($obj, 'taxAmount') ? $obj->taxAmount : null;
        $ret->totalAmount = property_exists($obj, 'totalAmount') ? $obj->totalAmount : null;
        $ret->deviceId = property_exists($obj, 'deviceId') ? $obj->deviceId : null;
        $ret->startDate = property_exists($obj, 'startDate') ? $obj->startDate : null;
        $ret->processingDateInfo = property_exists($obj, 'processingDateInfo') ? $obj->processingDateInfo : null;
        $ret->frequency = property_exists($obj, 'frequency') ? $obj->frequency : null;
        $ret->duration = property_exists($obj, 'duration') ? $obj->duration : null;
        $ret->endDate = property_exists($obj, 'endDate') ? $obj->endDate : null;
        $ret->reprocessingCount = property_exists($obj, 'reprocessingCount') ? $obj->reprocessingCount : null;
        $ret->emailReceipt = property_exists($obj, 'emailReceipt') ? $obj->emailReceipt : null;
        $ret->emailAdvanceNotice = property_exists($obj, 'emailAdvanceNotice') ? $obj->emailAdvanceNotice : null;
        $ret->nextProcessingDate = property_exists($obj, 'nextProcessingDate') ? $obj->nextProcessingDate : null;
        $ret->previousProcessingDate = property_exists($obj, 'previousProcessingDate') ? $obj->previousProcessingDate : null;
        $ret->approvedTransactionCount = property_exists($obj, 'approvedTransactionCount') ? $obj->approvedTransactionCount : null;
        $ret->failureCount = property_exists($obj, 'failureCount') ? $obj->failureCount : null;
        $ret->totalApprovedAmountToDate = property_exists($obj, 'totalApprovedAmountToDate') ? $obj->totalApprovedAmountToDate : null;
        $ret->numberOfPaymentsRemaining = property_exists($obj, 'numberOfPaymentsRemaining') ? $obj->numberOfPaymentsRemaining : null;
        $ret->cancellationDate = property_exists($obj, 'cancellationDate') ? $obj->cancellationDate : null;
        $ret->scheduleStarted = property_exists($obj, 'scheduleStarted') ? $obj->scheduleStarted : null;
        $ret->creationDate = property_exists($obj, 'creationDate') ? $obj->creationDate : null;
        $ret->lastChangeDate = property_exists($obj, 'lastChangeDate') ? $obj->lastChangeDate : null;
        $ret->statusSetDate = property_exists($obj, 'statusSetDate') ? $obj->statusSetDate : null;
        $ret->description = property_exists($obj, 'description') ? $obj->description : null;
        $ret->invoiceNbr = property_exists($obj, 'invoiceNbr') ? $obj->invoiceNbr : null;
        return $ret;
    }

    // Needs to be implemented to get name of child class
    /**
     * @param null   $params
     * @param string $class
     *
     * @return array
     */
    public function getEditableFieldsWithValues($params = null,$class = 'HpsPayPlanSchedule'){
        if ($params===null){
            $params=$this;
        }
        return parent::getEditableFieldsWithValues($class, $params);
    }
}

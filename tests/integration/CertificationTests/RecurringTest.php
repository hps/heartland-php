<?php

/**
 * Class RecurringTest
 */
class RecurringTest extends PHPUnit_Framework_TestCase
{
    const BATCH_NOT_OPEN = 'Transaction was rejected because it requires a batch to be open.';

    /** @var HpsPayPlanService|null */
    protected $service                         = null;

    /** @var HpsBatchService|null */
    protected $batchService                    = null;

    /** @var HpsFluentCreditService|null */
    protected $creditService                   = null;

    /** @var HpsFluentCheckService|null */
    protected $checkService                    = null;

    /** @var string|null */
    private static $todayDate                  = null;

    /** @var string|null */
    private static $identifierBase             = null;

    /** @var string|null */
    private static $customerPersonKey          = null;

    /** @var string|null */
    private static $customerCompanyKey         = null;

    /** @var string|null */
    private static $paymentMethodKeyVisa       = null;

    /** @var string|null */
    private static $paymentMethodKeyMastercard = null;

    /** @var string|null */
    private static $paymentMethodKeyCheckPpd   = null;

    /** @var string|null */
    private static $paymentMethodKeyCheckCcd   = null;

    /** @var string|null */
    private static $scheduleKeyVisa            = null;

    /** @var string|null */
    private static $scheduleKeyMastercard      = null;

    /** @var string|null */
    private static $scheduleKeyCheckPpd        = null;

    /** @var string|null */
    private static $scheduleKeyCheckCcd        = null;
    /**
     * @return \HpsServicesConfig
     */
    private function config()
    {
        $config = new HpsServicesConfig();
        $config->secretApiKey  = 'skapi_cert_MTyMAQBiHVEAewvIzXVFcmUd2UcyBge_eCpaASUp0A';
        $config->developerId   = '';
        $config->versionNumber = '';
        return $config;
    }

    public static function setupBeforeClass()
    {
        self::$todayDate = date('Ymd');
        self::$identifierBase = '%s-%s-'
            . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 10);
    }

    protected function setup()
    {
        $this->service       = new HpsPayPlanService($this->config());
        $this->batchService  = new HpsBatchService($this->config());
        $this->giftService   = new HpsFluentGiftCardService($this->config());
        $this->creditService = new HpsFluentCreditService($this->config());
        $this->checkService  = new HpsFluentCheckService($this->config());
    }
    /**
     * @param $identifier
     *
     * @return string
     */
    public function getIdentifier($identifier)
    {
        $rvalue = sprintf(self::$identifierBase, self::$todayDate, $identifier);
        // print $rvalue;
        return $rvalue;
    }

    /// Batching

    public function test000CloseBatch()
    {
        try {
            $response = $this->batchService->closeBatch();
            if ($response == null) {
                $this->fail("Response is null");
            }
            // printf("\nbatch id: %s:", $response->id);
            // printf("\nsequence number: %s:", $response->sequenceNumber);
        } catch (HpsException $e) {
            if ($e->getMessage() != self::BATCH_NOT_OPEN) {
                $this->fail($e->getMessage());
            }
        }
    }

    /// Clean up

    public function test000CleanUp()
    {
        #  remove the schedules
        $schResults = $this->service->findAllSchedules();
        foreach ($schResults->results as $schedule) {
            $this->service->deleteSchedule($schedule, true);
        }

        #  remove payment methods
        $pmResults = $this->service->findAllPaymentMethods();
        foreach ($pmResults->results as $pm) {
            $this->service->deletePaymentMethod($pm, true);
        }

        #  remove customers
        $custResults = $this->service->findAllCustomers();
        foreach ($custResults->results as $c) {
            $this->service->deleteCustomer($c, true);
        }
    }

    /// Customer Setup

    public function test001AddCustomerPerson()
    {
        $customer                     = new HpsPayPlanCustomer();
        $customer->customerIdentifier = $this->getIdentifier('Person');
        $customer->firstName          = 'John';
        $customer->lastName           = 'Doe';
        $customer->customer_status    = HpsPayPlanCustomerStatus::ACTIVE;
        $customer->primaryEmail       = 'john.doe@email.com';
        $customer->addressLine1       = '123 Main St';
        $customer->city               = 'Dallas';
        $customer->stateProvince      = 'TX';
        $customer->zipPostalCode      = '98765';
        $customer->country            = 'USA';
        $customer->phoneDay           = '5551112222';

        $response = $this->service->addCustomer($customer);
        $this->assertEquals(true, $response != null);
        $this->assertEquals(true, $response->customerKey != null);
        self::$customerPersonKey = $response->customerKey;
    }

    public function test002AddCustomerCompany()
    {
        $customer                     = new HpsPayPlanCustomer();
        $customer->customerIdentifier = $this->getIdentifier('Business');
        $customer->company            = 'AcmeCo';
        $customer->customer_status    = HpsPayPlanCustomerStatus::ACTIVE;
        $customer->primaryEmail       = 'acme@email.com';
        $customer->addressLine1       = '987 Elm St';
        $customer->city               = 'Princeton';
        $customer->stateProvince      = 'NJ';
        $customer->zipPostalCode      = '12345';
        $customer->country            = 'USA';
        $customer->phoneDay           = '5551112222';

        $response = $this->service->addCustomer($customer);
        $this->assertEquals(true, $response != null);
        $this->assertEquals(true, $response->customerKey != null);
        self::$customerCompanyKey = $response->customerKey;
    }

    /// Payment Setup

    public function test003AddPaymentCreditVisa()
    {
        $paymentMethod                          = new HpsPayPlanPaymentMethod();
        $paymentMethod->paymentMethodIdentifier = $this->getIdentifier('CreditV');
        $paymentMethod->paymentMethodType       = HpsPayPlanPaymentMethodType::CREDIT_CARD;
        $paymentMethod->nameOnAccount           = 'John Doe';
        $paymentMethod->accountNumber           = '4012002000060016';
        $paymentMethod->expirationDate          = '1225';
        $paymentMethod->customerKey             = self::$customerPersonKey;
        $paymentMethod->country                 = 'USA';

        $response = $this->service->addPaymentMethod($paymentMethod);
        $this->assertEquals(true, $response != null);
        $this->assertEquals(true, $response->paymentMethodKey != null);
        self::$paymentMethodKeyVisa = $response->paymentMethodKey;
    }

    public function test004AddPaymentCreditMastercard()
    {
        $paymentMethod                          = new HpsPayPlanPaymentMethod();
        $paymentMethod->paymentMethodIdentifier = $this->getIdentifier('CreditMC');
        $paymentMethod->paymentMethodType       = HpsPayPlanPaymentMethodType::CREDIT_CARD;
        $paymentMethod->nameOnAccount           = 'John Doe';
        $paymentMethod->accountNumber           = '5473500000000014';
        $paymentMethod->expirationDate          = '1225';
        $paymentMethod->customerKey             = self::$customerPersonKey;
        $paymentMethod->country                 = 'USA';

        $response = $this->service->addPaymentMethod($paymentMethod);
        $this->assertEquals(true, $response != null);
        $this->assertEquals(true, $response->paymentMethodKey != null);
        self::$paymentMethodKeyMastercard = $response->paymentMethodKey;
    }

    public function test005AddPaymentCheckPpd()
    {
        $paymentMethod                          = new HpsPayPlanPaymentMethod();
        $paymentMethod->paymentMethodIdentifier = $this->getIdentifier('CheckPPD');
        $paymentMethod->paymentMethodType       = HpsPayPlanPaymentMethodType::ACH;
        $paymentMethod->achType                 = 'Checking';
        $paymentMethod->accountType             = 'Personal';
        $paymentMethod->telephoneIndicator      = false;
        $paymentMethod->routingNumber           = '490000018';
        $paymentMethod->nameOnAccount           = 'John Doe';
        $paymentMethod->driversLicenseNumber    = '7418529630';
        $paymentMethod->driversLicenseState     = 'TX';
        $paymentMethod->accountNumber           = '24413815';
        $paymentMethod->addressLine1            = '123 Main St';
        $paymentMethod->city                    = 'Dallas';
        $paymentMethod->stateProvince           = 'TX';
        $paymentMethod->zipPostalCode           = '98765';
        $paymentMethod->customerKey             = self::$customerPersonKey;
        $paymentMethod->accountHolderYob        = '1989';

        $response = $this->service->addPaymentMethod($paymentMethod);
        $this->assertEquals(true, $response != null);
        $this->assertEquals(true, $response->paymentMethodKey != null);
        self::$paymentMethodKeyCheckPpd = $response->paymentMethodKey;
    }

    public function test006AddPaymentCheckCcd()
    {
        $paymentMethod                          = new HpsPayPlanPaymentMethod();
        $paymentMethod->paymentMethodIdentifier = $this->getIdentifier('CheckCCD');
        $paymentMethod->paymentMethodType       = HpsPayPlanPaymentMethodType::ACH;
        $paymentMethod->achType                 = 'Checking';
        $paymentMethod->accountType             = 'Business';
        $paymentMethod->telephoneIndicator      = 0;
        $paymentMethod->routingNumber           = '490000018';
        $paymentMethod->nameOnAccount           = 'Acme Co';
        $paymentMethod->driversLicenseNumber    = '3692581470';
        $paymentMethod->driversLicenseState     = 'TX';
        $paymentMethod->accountNumber           = '24413815';
        $paymentMethod->addressLine1            = '987 Elm St';
        $paymentMethod->city                    = 'Princeton';
        $paymentMethod->stateProvince           = 'NJ';
        $paymentMethod->zipPostalCode           = '12345';
        $paymentMethod->customerKey             = self::$customerCompanyKey;
        $paymentMethod->accountHolderYob        = '1989';

        $response = $this->service->addPaymentMethod($paymentMethod);
        $this->assertEquals(true, $response != null);
        $this->assertEquals(true, $response->paymentMethodKey != null);
        self::$paymentMethodKeyCheckCcd = $response->paymentMethodKey;
    }

    /// Payment Setup - Declined

    /**
     * @expectedException HpsException
     */
    public function test007AddPaymentCheckPpd()
    {
        $paymentMethod                          = new HpsPayPlanPaymentMethod();
        $paymentMethod->paymentMethodIdentifier = $this->getIdentifier('CheckPPD');
        $paymentMethod->paymentMethodType       = HpsPayPlanPaymentMethodType::ACH;
        $paymentMethod->achType                 = 'Checking';
        $paymentMethod->accountType             = 'Personal';
        $paymentMethod->telephoneIndicator      = 0;
        $paymentMethod->routingNumber           = '490000050';
        $paymentMethod->nameOnAccount           = 'John Doe';
        $paymentMethod->driversLicenseNumber    = '7418529630';
        $paymentMethod->accountNumber           = '24413815';
        $paymentMethod->addressLine1            = '123 Main St';
        $paymentMethod->city                    = 'Dallas';
        $paymentMethod->stateProvince           = 'TX';
        $paymentMethod->zipPostalCode           = '98765';
        $paymentMethod->customerKey             = self::$customerPersonKey;

        $this->service->addPaymentMethod($paymentMethod);
    }

    /// Recurring Billing using PayPlan - Managed Schedule

    public function test008AddScheduleCreditVisa()
    {
        $schedule                     = new HpsPayPlanSchedule();
        $schedule->scheduleIdentifier = $this->getIdentifier('CreditV');
        $schedule->customerKey        = self::$customerPersonKey;
        $schedule->scheduleStatus     = HpsPayPlanScheduleStatus::ACTIVE;
        $schedule->paymentMethodKey   = self::$paymentMethodKeyVisa;
        $schedule->subtotalAmount     = new HpsPayPlanAmount(3001);
        $schedule->startDate          = '02012027';
        $schedule->frequency          = HpsPayPlanScheduleFrequency::WEEKLY;
        $schedule->duration           = HpsPayPlanScheduleDuration::ONGOING;
        $schedule->reprocessingCount  = 1;

        $response = $this->service->addSchedule($schedule);
        $this->assertEquals(true, $response != null);
        $this->assertEquals(true, $response->scheduleKey != null);
        self::$scheduleKeyVisa = $response->scheduleKey;
    }

    public function test009AddScheduleCreditMastercard()
    {
        $schedule                     = new HpsPayPlanSchedule();
        $schedule->scheduleIdentifier = $this->getIdentifier('CreditMC');
        $schedule->customerKey        = self::$customerPersonKey;
        $schedule->scheduleStatus     = HpsPayPlanScheduleStatus::ACTIVE;
        $schedule->paymentMethodKey   = self::$paymentMethodKeyMastercard;
        $schedule->subtotalAmount     = new HpsPayPlanAmount(3002);
        $schedule->startDate          = '02012027';
        $schedule->frequency          = HpsPayPlanScheduleFrequency::WEEKLY;
        $schedule->duration           = HpsPayPlanScheduleDuration::END_DATE;
        $schedule->endDate            = '04012027';
        $schedule->reprocessingCount  = 2;

        $response = $this->service->addSchedule($schedule);
        $this->assertEquals(true, $response != null);
        $this->assertEquals(true, $response->scheduleKey != null);
        self::$scheduleKeyMastercard = $response->scheduleKey;
    }

    public function test010AddScheduleCheckPpd()
    {
        $schedule                     = new HpsPayPlanSchedule();
        $schedule->scheduleIdentifier = $this->getIdentifier('CheckPPD');
        $schedule->customerKey        = self::$customerPersonKey;
        $schedule->scheduleStatus     = HpsPayPlanScheduleStatus::ACTIVE;
        $schedule->paymentMethodKey   = self::$paymentMethodKeyCheckPpd;
        $schedule->subtotalAmount     = new HpsPayPlanAmount(3003);
        $schedule->startDate          = '02012027';
        $schedule->frequency          = HpsPayPlanScheduleFrequency::MONTHLY;
        $schedule->duration           = HpsPayPlanScheduleDuration::LIMITED_NUMBER;
        $schedule->reprocessingCount  = 1;
        $schedule->numberOfPayments   = 2;
        $schedule->processingDateInfo = '1';

        $response = $this->service->addSchedule($schedule);
        $this->assertEquals(true, $response != null);
        $this->assertEquals(true, $response->scheduleKey != null);
        self::$scheduleKeyCheckPpd = $response->scheduleKey;
    }

    public function test011AddScheduleCheckCcd()
    {
        $schedule                     = new HpsPayPlanSchedule();
        $schedule->scheduleIdentifier = $this->getIdentifier('CheckCCD');
        $schedule->customerKey        = self::$customerCompanyKey;
        $schedule->scheduleStatus     = HpsPayPlanScheduleStatus::ACTIVE;
        $schedule->paymentMethodKey   = self::$paymentMethodKeyCheckCcd;
        $schedule->subtotalAmount     = new HpsPayPlanAmount(3004);
        $schedule->startDate          = '02012027';
        $schedule->frequency          = HpsPayPlanScheduleFrequency::BIWEEKLY;
        $schedule->duration           = HpsPayPlanScheduleDuration::ONGOING;
        $schedule->reprocessingCount  = 1;

        $response = $this->service->addSchedule($schedule);
        $this->assertEquals(true, $response != null);
        $this->assertEquals(true, $response->scheduleKey != null);
        self::$scheduleKeyCheckCcd = $response->scheduleKey;
    }

    /// Recurring Billing - Declined

    /**
     * @expectedException HpsException
     */
    public function test012AddScheduleCreditVisa()
    {
        $schedule                     = new HpsPayPlanSchedule();
        $schedule->scheduleIdentifier = $this->getIdentifier('CreditV');
        $schedule->customerKey        = self::$customerPersonKey;
        $schedule->scheduleStatus     = HpsPayPlanScheduleStatus::ACTIVE;
        $schedule->paymentMethodKey   = self::$paymentMethodKeyVisa;
        $schedule->subtotalAmount     = new HpsPayPlanAmount(1008);
        $schedule->startDate          = '02012027';
        $schedule->frequency          = HpsPayPlanScheduleFrequency::WEEKLY;
        $schedule->duration           = HpsPayPlanScheduleDuration::ONGOING;
        $schedule->reprocessingCount  = 2;

        $this->service->addSchedule($schedule);
    }

    /**
     * @expectedException HpsException
     */
    public function test013AddScheduleCheckPpd()
    {
        $schedule                     = new HpsPayPlanSchedule();
        $schedule->scheduleIdentifier = $this->getIdentifier('CheckPPD');
        $schedule->customerKey        = self::$customerPersonKey;
        $schedule->scheduleStatus     = HpsPayPlanScheduleStatus::ACTIVE;
        $schedule->paymentMethodKey   = self::$paymentMethodKeyCheckPpd;
        $schedule->subtotalAmount     = new HpsPayPlanAmount(2501);
        $schedule->startDate          = '02012027';
        $schedule->frequency          = HpsPayPlanScheduleFrequency::WEEKLY;
        $schedule->duration           = HpsPayPlanScheduleDuration::LIMITED_NUMBER;
        $schedule->reprocessingCount  = 1;
        $schedule->numberOfPayments   = 2;
        $schedule->processingDateInfo = '1';

        $this->service->addSchedule($schedule);
    }

    /// Recurring Billing using PayPlan - Managed Schedule

    public function test014RecurringBillingVisa()
    {
        $response = $this->creditService
            ->recurring(20.01)
            ->withPaymentMethodKey(self::$paymentMethodKeyVisa)
            ->withSchedule(self::$scheduleKeyVisa)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
    }

    public function test015RecurringBillingMastercard()
    {
        $response = $this->creditService
            ->recurring(20.02)
            ->withPaymentMethodKey(self::$paymentMethodKeyMastercard)
            ->withSchedule(self::$scheduleKeyMastercard)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
    }

    public function test016RecurringBillingCheckPpd()
    {
        $response = $this->checkService
            ->recurring(20.03)
            ->withPaymentMethodKey(self::$paymentMethodKeyCheckPpd)
            ->withSchedule(self::$scheduleKeyCheckPpd)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    public function test017RecurringBillingCheckCcd()
    {
        $response = $this->checkService
            ->recurring(20.04)
            ->withPaymentMethodKey(self::$paymentMethodKeyCheckCcd)
            ->withSchedule(self::$scheduleKeyCheckCcd)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    /// One Time Bill Payment

    public function test018RecurringBillingVisa()
    {
        $response = $this->creditService
            ->recurring(20.06)
            ->withPaymentMethodKey(self::$paymentMethodKeyVisa)
            ->withOneTime(true)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
    }

    public function test019RecurringBillingMastercard()
    {
        $response = $this->creditService
            ->recurring(20.07)
            ->withPaymentMethodKey(self::$paymentMethodKeyMastercard)
            ->withOneTime(true)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
    }

    public function test020RecurringBillingCheckPpd()
    {
        $response = $this->checkService
            ->recurring(20.08)
            ->withPaymentMethodKey(self::$paymentMethodKeyCheckPpd)
            ->withOneTime(true)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    public function test021RecurringBillingCheckCcd()
    {
        $response = $this->checkService
            ->recurring(20.09)
            ->withPaymentMethodKey(self::$paymentMethodKeyCheckCcd)
            ->withOneTime(true)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    /// One Time Bill Payment - Declined

    /**
     * @expectedException HpsCreditException
     */
    public function test022RecurringBillingVisa()
    {
        $this->creditService
            ->recurring(10.08)
            ->withPaymentMethodKey(self::$paymentMethodKeyVisa)
            ->withOneTime(true)
            ->execute();
    }

    /**
     * @expectedException HpsCheckException
     */
    public function test023RecurringBillingCheckPpd()
    {
        $this->checkService
            ->recurring(25.02)
            ->withPaymentMethodKey(self::$paymentMethodKeyCheckPpd)
            ->withOneTime(true)
            ->execute();
    }

    /// CLOSE BATCH

    public function test999CloseBatch()
    {
        try {
            $response = $this->batchService->closeBatch();
            if ($response == null) {
                $this->fail("Response is null");
            }

            // printf("\nbatch id: %s", $response->id);
            // printf("\nsequence number: %s", $response->sequenceNumber);
            # print self::$customerPersonKey;
            # print self::$customerCompanyKey;
            # print self::$paymentMethodKeyVisa;
            # print self::$paymentMethodKeyMastercard;
            # print self::$paymentMethodKeyCheckPpd;
            # print self::$paymentMethodKeyCheckCcd;
            # print self::$scheduleKeyVisa;
            # print self::$scheduleKeyMastercard;
            # print self::$scheduleKeyCheckPpd;
            # print self::$scheduleKeyCheckCcd;
        } catch (HpsException $e) {
            $this->fail($e->getMessage());
        }
    }
}

<?php

class GatewayPayPlanCreditRecurringTest extends PHPUnit_Framework_TestCase
{
    protected $service;
    protected $schedule;
    protected $publicKey = 'pkapi_cert_jKc1FtuyAydZhZfbB3';

    protected function setup()
    {
        $config = new HpsServicesConfig();
        $config->secretApiKey = 'skapi_cert_MTyMAQBiHVEAewvIzXVFcmUd2UcyBge_eCpaASUp0A';
        $config->developerId = '002914';
        $config->versionNumber = '1510';
        $scheduleService = new HpsPayPlanScheduleService($config);
        $results = $scheduleService
            ->page(1, 0)
            ->findAll(array(
                'scheduleStatus'     => HpsPayPlanScheduleStatus::ACTIVE,
                'scheduleIdentifier' => 'SecureSubmit',
            ));
        $this->schedule = isset($results->results[0]) ? $results->results[0] : null;

        if ($this->schedule == null) {
            $customerService = new HpsPayPlanCustomerService($config);
            $id = date('Ymd').'-SecureSubmit-'.substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 10);
            $newCustomer = new HpsPayPlanCustomer();
            $newCustomer->customerIdentifier = $id;
            $newCustomer->firstName          = 'Bill';
            $newCustomer->lastName           = 'Johnson';
            $newCustomer->company            = 'Heartland Payment Systems';
            $newCustomer->country            = 'USA';
            $newCustomer->customerStatus     = HpsPayPlanCustomerStatus::ACTIVE;
            $customer = $customerService->add($newCustomer);

            $paymentMethodService = new HpsPayPlanPaymentMethodService($config);
            $newPaymentMethod = new HpsPayPlanPaymentMethod();
            $newPaymentMethod->customerKey    = $customer->customerKey;
            $newPaymentMethod->nameOnAccount  = 'Bill Johnson';
            $newPaymentMethod->accountNumber  = 4111111111111111;
            $newPaymentMethod->expirationDate = '0120';
            $newPaymentMethod->country        = 'USA';

            $paymentMethod = $paymentMethodService->add($newPaymentMethod);

            $id = date('Ymd').'-SecureSubmit-'.substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 10);
            $date = date('m30Y', strtotime(date('Y-m-d', strtotime(date('Y-m-d'))).'+1 month'));
            $newPaymentSchedule = new HpsPayPlanSchedule();
            $newPaymentSchedule->scheduleIdentifier = $id;
            $newPaymentSchedule->customerKey        = $paymentMethod->customerKey;
            $newPaymentSchedule->paymentMethodKey   = $paymentMethod->paymentMethodKey;
            $newPaymentSchedule->subtotalAmount     = array('value' => 100);
            $newPaymentSchedule->startDate          = $date;
            $newPaymentSchedule->frequency          = HpsPayPlanScheduleFrequency::WEEKLY;
            $newPaymentSchedule->duration           = HpsPayPlanScheduleDuration::LIMITED_NUMBER;
            $newPaymentSchedule->numberOfPayments   = 3;
            $newPaymentSchedule->reprocessingCount  = 2;
            $newPaymentSchedule->emailReceipt       = 'Never';
            $newPaymentSchedule->emailAdvanceNotice = 'No';
            $newPaymentSchedule->scheduleStatus     = HpsPayPlanScheduleStatus::ACTIVE;

            $this->schedule = $scheduleService->add($newPaymentSchedule);
        }

        $this->service = new HpsCreditService($config);
    }

    public function testOneTimeWithCardData()
    {
        $response = $this->service->recurring($this->schedule, 10, TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(), true);

        $this->assertNotNull($response);
        $this->assertEquals('00', $response->responseCode);
    }

    /** @group recurringtest */
    public function testOneTimeWithTokenData()
    {
        $token = $this->getToken(TestCreditCard::validVisaCreditCard());
        $response = $this->service->recurring($this->schedule, 10, $token, null, true);

        $this->assertNotNull($response);
        $this->assertEquals('00', $response->responseCode);
    }

    public function testOneTimeWithPaymentMethodKey()
    {
        $paymentMethodKey = $this->getPaymentMethodKey();
        $response = $this->service->recurring($this->schedule, 10, $paymentMethodKey, null, true);

        $this->assertNotNull($response);
        $this->assertEquals('00', $response->responseCode);
    }

    public function testWithCardData()
    {
        $response = $this->service->recurring($this->schedule, 10, TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder());

        $this->assertNotNull($response);
        $this->assertEquals('00', $response->responseCode);
    }

    /** @group recurringtest */
    public function testWithTokenData()
    {
        $token = $this->getToken(TestCreditCard::validVisaCreditCard());
        $response = $this->service->recurring($this->schedule, 10, $token);

        $this->assertNotNull($response);
        $this->assertEquals('00', $response->responseCode);
    }

    public function testWithPaymentMethodKey()
    {
        $paymentMethodKey = $this->getPaymentMethodKey();
        $response = $this->service->recurring($this->schedule, 10, $paymentMethodKey);

        $this->assertNotNull($response);
        $this->assertEquals('00', $response->responseCode);
    }

    private function getToken($card, $key = null)
    {
        $this->tokenService = new HpsTokenService($this->publicKey);
        $tokenResponse = $this->tokenService->getToken($card);
        if (isset($tokenResponse->token_value)) {
            $token = new HpsTokenData();
            $token->tokenValue = $tokenResponse->token_value;
            return $token;
        } else {
            return $tokenResponse;
        }
    }

    private function getPaymentMethodKey()
    {
        return $this->schedule->paymentMethodKey;
    }
}

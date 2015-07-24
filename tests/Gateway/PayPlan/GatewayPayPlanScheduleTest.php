<?php

class GatewayPayPlanScheduleTest extends PHPUnit_Framework_TestCase
{
    protected $paymentMethod;
    protected $service;
    private $alphabet = 'abcdefghijklmnopqrstuvwxyz';

    protected function setUp()
    {
        $config = new HpsServicesConfig();
        $config->secretApiKey = 'skapi_cert_MTyMAQBiHVEAewvIzXVFcmUd2UcyBge_eCpaASUp0A';
        $this->service = new HpsPayPlanScheduleService($config);

        $paymentMethodService = new HpsPayPlanPaymentMethodService($config);
        $this->paymentMethod = $paymentMethodService
            ->page(1, 0)
            ->findAll(array(
                'customerIdentifier' => 'SecureSubmit',
                'paymentStatus'      => HpsPayPlanPaymentMethodStatus::ACTIVE,
            ))
            ->results[0];
    }

    public function testAdd()
    {
        $id = date('Ymd').'-SecureSubmit-'.substr(str_shuffle($this->alphabet), 0, 10);
        $date = date('m30Y', strtotime(date('Y-m-d', strtotime(date('Y-m-d'))).'+1 month'));
        $newPaymentSchedule = new HpsPayPlanSchedule();
        $newPaymentSchedule->scheduleIdentifier = $id;
        $newPaymentSchedule->customerKey        = $this->paymentMethod->customerKey;
        $newPaymentSchedule->paymentMethodKey   = $this->paymentMethod->paymentMethodKey;
        $newPaymentSchedule->subtotalAmount     = array('value' => 100);
        $newPaymentSchedule->startDate          = $date;
        $newPaymentSchedule->frequency          = HpsPayPlanScheduleFrequency::WEEKLY;
        $newPaymentSchedule->duration           = HpsPayPlanScheduleDuration::LIMITED_NUMBER;
        $newPaymentSchedule->numberOfPayments   = 3;
        $newPaymentSchedule->reprocessingCount  = 2;
        $newPaymentSchedule->emailReceipt       = 'Never';
        $newPaymentSchedule->emailAdvanceNotice = 'No';
        $newPaymentSchedule->scheduleStatus     = HpsPayPlanScheduleStatus::ACTIVE;

        $result = $this->service->add($newPaymentSchedule);

        $this->assertNotNull($result);
        $this->assertNotNull($result->scheduleKey);
    }

    /**
     * @expectedException        PHPUnit_Framework_Error
     * @expectedExceptionMessage must be an instance of HpsPayPlanSchedule
     */
    public function testAddFromArray()
    {
        $id = date('Ymd').'-SecureSubmit-'.substr(str_shuffle($this->alphabet), 0, 10);
        $newPaymentSchedule = array(
            'scheduleIdentifier' => $id,
            'paymentMethodKey'   => $this->paymentMethod->paymentMethodKey,
            'subtotalAmount'     => array('value' => 100),
            'startDate'          => date('m15Y'),
            'frequency'          => HpsPayPlanScheduleFrequency::WEEKLY,
            'duration'           => HpsPayPlanScheduleDuration::LIMITED_NUMBER,
            'numberOfPayments'   => 3,
            'reprocessingCount'  => 2,
            'emailReceipt'       => 'Never',
            'emailAdvanceNotice' => 'No',
            'scheduleStatus'     => HpsPayPlanScheduleStatus::ACTIVE,
        );

        $result = $this->service->add($newPaymentSchedule);
    }

    /**
     * @expectedException        PHPUnit_Framework_Error
     * @expectedExceptionMessage must be an instance of HpsPayPlanSchedule
     */
    public function testAddWithNull()
    {
        $result = $this->service->add(null);
    }

    public function testEdit()
    {
        // Get a SecureSubmit paymentSchedule
        $results = $this->service
            ->page(1, 0)
            ->findAll(array('scheduleIdentifier' => 'SecureSubmit'));

        $this->assertNotNull($results);
        $this->assertEquals(true, count($results->results) >= 1);

        // Make the edit
        $paymentSchedule = $results->results[0];
        $scheduleStatus = $paymentSchedule->scheduleStatus == HpsPayPlanScheduleStatus::ACTIVE
            ? HpsPayPlanScheduleStatus::INACTIVE : HpsPayPlanScheduleStatus::ACTIVE;
        $paymentSchedule->scheduleStatus = $scheduleStatus;
        $paymentSchedule->startDate = null;

        $result = $this->service->edit($paymentSchedule);

        $this->assertNotNull($result);
        $this->assertEquals($paymentSchedule->scheduleKey, $result->scheduleKey);
        $this->assertEquals($scheduleStatus, $result->scheduleStatus);

        // Verify the edit
        $result = null;
        $result = $this->service->get($paymentSchedule->scheduleKey);

        $this->assertNotNull($result);
        $this->assertEquals($paymentSchedule->scheduleKey, $result->scheduleKey);
        $this->assertEquals($scheduleStatus, $result->scheduleStatus);
    }

    /**
     * @expectedException        PHPUnit_Framework_Error
     * @expectedExceptionMessage must be an instance of HpsPayPlanSchedule
     */
    public function testEditWithArray()
    {
        $oldPaymentSchedule = array();

        $result = $this->service->edit($oldPaymentSchedule);
    }

    /**
     * @expectedException        PHPUnit_Framework_Error
     * @expectedExceptionMessage must be an instance of HpsPayPlanSchedule
     */
    public function testEditWithNull()
    {
        $result = $this->service->edit(null);
    }

    public function testFindAll()
    {
        $results = $this->service->findAll();

        $this->assertNotNull($results);
        $this->assertEquals(true, count($results->results) > 0);
    }

    public function testFindAllWithPaging()
    {
        $results = $this->service
            ->page(1, 0)
            ->findAll();

        $this->assertNotNull($results);
        $this->assertTrue(1 == count($results->results));
    }

    public function testFindAllWithFilters()
    {
        $results = $this->service
            ->findAll(array('scheduleIdentifier' => 'SecureSubmit'));

        $this->assertNotNull($results);
        $this->assertEquals(true, count($results->results) >= 1);
    }

    /**
     * @expectedException        HpsGatewayException
     * @expectedExceptionMessage Invalid Request
     */
    public function testFindAllWithNullData()
    {
        $results = $this->service->findAll(null);
    }

    public function testGetByPaymentSchedule()
    {
        $results = $this->service->page(1, 0)->findAll();

        $this->assertNotNull($results);
        $this->assertTrue(1 == count($results->results));

        $paymentSchedule = $this->service->get($results->results[0]);

        $this->assertNotNull($paymentSchedule);
        $this->assertEquals($results->results[0]->scheduleKey, $paymentSchedule->scheduleKey);
    }

    public function testGetByPaymentScheduleKey()
    {
        $results = $this->service
            ->page(1, 0)
            ->findAll();

        $this->assertNotNull($results);
        $this->assertTrue(1 == count($results->results));

        $paymentSchedule = $this->service->get($results->results[0]->scheduleKey);

        $this->assertNotNull($paymentSchedule);
        $this->assertEquals($results->results[0]->scheduleKey, $paymentSchedule->scheduleKey);
    }

    public function testDeleteByPaymentSchedule()
    {
        $this->testAdd();

        $results = $this->service
            ->page(1, 0)
            ->findAll(array('scheduleIdentifier' => 'SecureSubmit'));

        $this->assertNotNull($results);
        $this->assertTrue(1 == count($results->results));

        $delete = $this->service->delete($results->results[0]);

        $this->assertNull($delete);
    }

    public function testDeleteByPaymentScheduleKey()
    {
        $this->testAdd();

        $results = $this->service
            ->page(1, 0)
            ->findAll(array('scheduleIdentifier' => 'SecureSubmit'));

        $this->assertNotNull($results);
        $this->assertTrue(1 == count($results->results));

        $delete = $this->service->delete($results->results[0]->scheduleKey);

        $this->assertNull($delete);
    }

    /**
     * @expectedException        HpsGatewayException
     * @expectedExceptionMessage Invalid Request
     */
    public function testDeleteWithNullData()
    {
        $results = $this->service->delete(null);
    }
}

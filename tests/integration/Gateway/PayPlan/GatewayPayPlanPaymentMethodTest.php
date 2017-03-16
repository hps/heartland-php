<?php

/**
 * Class GatewayPayPlanPaymentMethodTest
 */
class GatewayPayPlanPaymentMethodTest extends PHPUnit_Framework_TestCase
{
    protected $customer;
    protected $service;
    private $alphabet = 'abcdefghijklmnopqrstuvwxyz';

    protected function setUp()
    {
        $config = new HpsServicesConfig();
        $config->secretApiKey = 'skapi_cert_MTyMAQBiHVEAewvIzXVFcmUd2UcyBge_eCpaASUp0A';
        $this->service = new HpsPayPlanPaymentMethodService($config);

        $customerService = new HpsPayPlanCustomerService($config);
        $this->customer = $customerService->page(1, 0)->findAll()->results[0];
    }

    public function testAdd()
    {
        $newPaymentMethod = new HpsPayPlanPaymentMethod();
        $newPaymentMethod->customerKey    = $this->customer->customerKey;
        $newPaymentMethod->nameOnAccount  = 'Bill Johnson';
        $newPaymentMethod->accountNumber  = 4111111111111111;
        $newPaymentMethod->expirationDate = '0120';
        $newPaymentMethod->country        = 'USA';

        $result = $this->service->add($newPaymentMethod);

        $this->assertNotNull($result);
        $this->assertNotNull($result->paymentMethodKey);
    }

    public function testAddFromSingleUseToken()
    {
        $tokenService = new HpsTokenService('pkapi_cert_jKc1FtuyAydZhZfbB3');

        $card = new HpsCreditCard();
        $card->number = '4111111111111111';
        $card->expMonth = '12';
        $card->expYear = '2020';
        $card->cvv = '123';

        $response = $tokenService->getToken($card);
        if (isset($response->error)) {
            $this->fail($response->error->message);
        }

        $newPaymentMethod = new HpsPayPlanPaymentMethod();
        $newPaymentMethod->customerKey    = $this->customer->customerKey;
        $newPaymentMethod->nameOnAccount  = 'Bill Johnson';
        $newPaymentMethod->paymentToken   = $response->token_value;
        $newPaymentMethod->country        = 'USA';

        $result = $this->service->add($newPaymentMethod);

        $this->assertNotNull($result);
        $this->assertNotNull($result->paymentMethodKey);
    }

    /**
     * @expectedException        PHPUnit_Framework_Error
     * @expectedExceptionMessage must be an instance of HpsPayPlanPaymentMethod
     */
    public function testAddFromArray()
    {
        $newPaymentMethod = array(
            'customerKey'    => $this->customer->customerKey,
            'nameOnAccount'  => 'Bill Johnson',
            'accountNumber'  => 4111111111111111,
            'expirationDate' => '0120',
            'country'        => 'USA',
        );

        $result = $this->service->add($newPaymentMethod);
    }

    /**
     * @expectedException        PHPUnit_Framework_Error
     * @expectedExceptionMessage must be an instance of HpsPayPlanPaymentMethod
     */
    public function testAddWithNull()
    {
        $result = $this->service->add(null);
    }

    public function testEdit()
    {
        // Get a SecureSubmit paymentMethod
        $results = $this->service
            ->page(1, 0)
            ->findAll(array('customerIdentifier' => 'SecureSubmit'));

        $this->assertNotNull($results);
        $this->assertEquals(true, count($results->results) >= 1);

        // Make the edit
        $paymentMethod = $results->results[0];
        $paymentStatus = $paymentMethod->paymentStatus == HpsPayPlanPaymentMethodStatus::ACTIVE
            ? HpsPayPlanPaymentMethodStatus::INACTIVE : HpsPayPlanPaymentMethodStatus::ACTIVE;
        $paymentMethod->paymentStatus = $paymentStatus;

        $result = $this->service->edit($paymentMethod);

        $this->assertNotNull($result);
        $this->assertEquals($paymentMethod->paymentMethodKey, $result->paymentMethodKey);
        $this->assertEquals($paymentStatus, $result->paymentStatus);

        // Verify the edit
        $result = null;
        $result = $this->service->get($paymentMethod->paymentMethodKey);

        $this->assertNotNull($result);
        $this->assertEquals($paymentMethod->paymentMethodKey, $result->paymentMethodKey);
        $this->assertEquals($paymentStatus, $result->paymentStatus);
    }

    /**
     * @expectedException        PHPUnit_Framework_Error
     * @expectedExceptionMessage must be an instance of HpsPayPlanPaymentMethod
     */
    public function testEditWithArray()
    {
        $oldPaymentMethod = array();

        $result = $this->service->edit($oldPaymentMethod);
    }

    /**
     * @expectedException        PHPUnit_Framework_Error
     * @expectedExceptionMessage must be an instance of HpsPayPlanPaymentMethod
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
            ->findAll(array('customerIdentifier' => 'SecureSubmit'));

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

    public function testGetByPaymentMethod()
    {
        $results = $this->service->page(1, 0)->findAll();

        $this->assertNotNull($results);
        $this->assertTrue(1 == count($results->results));

        $paymentMethod = $this->service->get($results->results[0]);

        $this->assertNotNull($paymentMethod);
        $this->assertEquals($results->results[0]->paymentMethodKey, $paymentMethod->paymentMethodKey);
    }

    public function testGetByPaymentMethodKey()
    {
        $results = $this->service
            ->page(1, 0)
            ->findAll();

        $this->assertNotNull($results);
        $this->assertTrue(1 == count($results->results));

        $paymentMethod = $this->service->get($results->results[0]->paymentMethodKey);

        $this->assertNotNull($paymentMethod);
        $this->assertEquals($results->results[0]->paymentMethodKey, $paymentMethod->paymentMethodKey);
    }

    public function testDeleteByPaymentMethod()
    {
        $this->testAdd();

        $results = $this->service
            ->page(1, 0)
            ->findAll(array('customerIdentifier' => 'SecureSubmit'));

        $this->assertNotNull($results);
        $this->assertTrue(1 == count($results->results));

        $delete = $this->service->delete($results->results[0]);

        $this->assertNull($delete);
    }

    public function testDeleteByPaymentMethodKey()
    {
        $this->testAdd();

        $results = $this->service
            ->page(1, 0)
            ->findAll(array('customerIdentifier' => 'SecureSubmit'));

        $this->assertNotNull($results);
        $this->assertTrue(1 == count($results->results));

        $delete = $this->service->delete($results->results[0]->paymentMethodKey);

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

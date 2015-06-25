<?php

class GatewayPayPlanCustomerTest extends PHPUnit_Framework_TestCase
{
    protected $service;
    private $alphabet = 'abcdefghijklmnopqrstuvwxyz';

    protected function setUp()
    {
        $config = new HpsServicesConfig();
        $config->secretApiKey = 'skapi_uat_MY5OAAAUrmIFvLDRpO_ufLlFQkgg0Rms2G8WoI1THQ';
        $this->service = new HpsPayPlanCustomerService($config);
    }

    public function testAdd()
    {
        $id = date('Ymd').'-SecureSubmit-'.substr(str_shuffle($this->alphabet), 0, 10);
        $newCustomer = new HpsPayPlanCustomer();
        $newCustomer->customerIdentifier = $id;
        $newCustomer->firstName          = 'Bill';
        $newCustomer->lastName           = 'Johnson';
        $newCustomer->company            = 'Heartland Payment Systems';
        $newCustomer->country            = 'USA';
        $newCustomer->customerStatus     = HpsPayPlanCustomerStatus::ACTIVE;

        $result = $this->service->add($newCustomer);

        $this->assertNotNull($result);
        $this->assertNotNull($result->customerKey);
    }

    /**
     * @expectedException        PHPUnit_Framework_Error
     * @expectedExceptionMessage must be an instance of HpsPayPlanCustomer
     */
    public function testAddFromArray()
    {
        $id = date('Ymd').'-SecureSubmit-'.substr(str_shuffle($this->alphabet), 0, 10);
        $newCustomer = array(
            'customerIdentifier' => $id,
            'firstName'          => 'Bill',
            'lastName'           => 'Johnson',
            'company'            => 'Heartland Payment Systems',
            'country'            => 'USA',
            'customerStatus'     => HpsPayPlanCustomerStatus::ACTIVE,
        );

        $result = $this->service->add($newCustomer);
    }

    /**
     * @expectedException        PHPUnit_Framework_Error
     * @expectedExceptionMessage must be an instance of HpsPayPlanCustomer
     */
    public function testAddWithNull()
    {
        $result = $this->service->add(null);
    }

    public function testEdit()
    {
        // Get a SecureSubmit customer
        $results = $this->service
            ->page(1, 0)
            ->findAll(array('customerIdentifier' => 'SecureSubmit'));

        $this->assertNotNull($results);
        $this->assertEquals(true, count($results->results) >= 1);

        // Make the edit
        $phoneDay = '555'.substr(str_shuffle('123456789123456789'), 0, 7);
        $customer = $results->results[0];
        $customer->phoneDay = $phoneDay;

        $result = $this->service->edit($customer);

        $this->assertNotNull($result);
        $this->assertEquals($customer->customerKey, $result->customerKey);
        $this->assertEquals($phoneDay, $result->phoneDay);

        // Verify the edit
        $result = null;
        $result = $this->service->get($customer->customerKey);

        $this->assertNotNull($result);
        $this->assertEquals($customer->customerKey, $result->customerKey);
        $this->assertEquals($phoneDay, $result->phoneDay);
    }

    /**
     * @expectedException        PHPUnit_Framework_Error
     * @expectedExceptionMessage must be an instance of HpsPayPlanCustomer
     */
    public function testEditWithArray()
    {
        $oldCustomer = array(
            'customerKey'        => '12345',
            'firstName'          => 'Bill',
            'lastName'           => 'Johnson',
            'company'            => 'Heartland Payment Systems',
            'country'            => 'USA',
            'customerStatus'     => HpsPayPlanCustomerStatus::ACTIVE,
        );

        $result = $this->service->edit($oldCustomer);
    }

    /**
     * @expectedException        PHPUnit_Framework_Error
     * @expectedExceptionMessage must be an instance of HpsPayPlanCustomer
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

    public function testGetByCustomer()
    {
        $results = $this->service->page(1, 0)->findAll();

        $this->assertNotNull($results);
        $this->assertTrue(1 == count($results->results));

        $customer = $this->service->get($results->results[0]);

        $this->assertNotNull($customer);
        $this->assertEquals($results->results[0]->customerKey, $customer->customerKey);
    }

    public function testGetByCustomerKey()
    {
        $results = $this->service
            ->page(1, 0)
            ->findAll();

        $this->assertNotNull($results);
        $this->assertTrue(1 == count($results->results));

        $customer = $this->service->get($results->results[0]->customerKey);

        $this->assertNotNull($customer);
        $this->assertEquals($results->results[0]->customerKey, $customer->customerKey);
    }

    public function testDeleteByCustomer()
    {
        $this->testAdd();

        $results = $this->service
            ->page(1, 0)
            ->findAll();

        $this->assertNotNull($results);
        $this->assertTrue(1 == count($results->results));

        $delete = $this->service->delete($results->results[0]);

        $this->assertNull($delete);
    }

    public function testDeleteByCustomerKey()
    {
        $this->testAdd();

        $results = $this->service
            ->page(1, 0)
            ->findAll();

        $this->assertNotNull($results);
        $this->assertTrue(1 == count($results->results));

        $delete = $this->service->delete($results->results[0]->customerKey);

        $this->assertNull($delete);
    }

    /**
     * @expectedException        HpsGatewayException
     * @expectedExceptionMessage Unexpected response
     */
    public function testDeleteWithNullData()
    {
        $results = $this->service->delete(null);
    }
}

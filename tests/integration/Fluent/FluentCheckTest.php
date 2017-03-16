<?php

/**
 * Class FluentCheckTest
 */
class FluentCheckTest extends PHPUnit_Framework_TestCase
{
    protected $service;

    protected function setUp()
    {
        $this->service = new HpsFluentCheckService(TestServicesConfig::validMultiUseConfig());
    }

    /**
     * @expectedException HpsException
     */
    public function testOverride()
    {
        $check = TestCheck::certification();

        $sale = $this->service
            ->sale()
            ->withCheck($check)
            ->withAmount(10)
            ->execute();

        $this->assertNotNull($sale);

        $override = $this->service
            ->override()
            ->withCheck($check)
            ->withAmount(10)
            ->execute();

        $this->assertNotNull($override);
        $this->assertEquals("0", $override->responseCode);
    }

    /**
     * @expectedException HpsException
     */
    public function testReturn()
    {
        $check = TestCheck::certification();

        $sale = $this->service
            ->sale()
            ->withCheck($check)
            ->withAmount(10)
            ->execute();

        $this->assertNotNull($sale);

        $return = $this->service
            ->returnCheck()
            ->withCheck($check)
            ->withAmount(10)
            ->execute();

        $this->assertNotNull($return);
        $this->assertEquals("0", $return->responseCode);
    }

    public function testSale()
    {
        $check = TestCheck::certification();

        $sale = $this->service
            ->sale()
            ->withCheck($check)
            ->withAmount(10)
            ->execute();

        $this->assertNotNull($sale);
        $this->assertEquals("0", $sale->responseCode);
    }

    public function testVoid()
    {
        $check = TestCheck::certification();

        $sale = $this->service
            ->sale()
            ->withCheck($check)
            ->withAmount(10)
            ->execute();

        $this->assertNotNull($sale->transactionId);

        $void = $this->service
            ->void()
            ->withTransactionId($sale->transactionId)
            ->execute();

        $this->assertNotNull($void);
        $this->assertEquals("0", $void->responseCode);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Sale needs an amount
     */
    public function testNoAmount()
    {
        $this->service
            ->sale()
            ->withCheck(TestCheck::certification())
            ->execute();
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Sale needs a check
     */
    public function testNoCheck()
    {
        $this->service
            ->sale()
            ->withAmount(10)
            ->execute();
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Void can only use one transaction id
     */
    public function testVoidWithMultipleTransactionIds()
    {
        $this->service
            ->void()
            ->withTransactionId('54896311524')
            ->withClientTransactionId('54896311524')
            ->execute();
    }
}

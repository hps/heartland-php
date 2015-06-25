<?php

class GiftCardCertificationTest extends PHPUnit_Framework_TestCase
{
    protected $service;

    protected function setUp()
    {
        $this->service = new HpsFluentGiftCardService($this->config());
    }
    /**
     * Test #96
     */
    public function testActivateSVA1()
    {
        $activation = $this->service
            ->activate()
            ->withCard($this->sva1())
            ->withAmount(6.00)
            ->withCurrency('usd')
            ->execute();

        $this->assertEquals('0', $activation->responseCode);
    }

    /**
     * Test #97
     */
    public function testActivateSVA2()
    {
        $activation = $this->service
            ->activate()
            ->withCard($this->sva2())
            ->withAmount(7.00)
            ->withCurrency('usd')
            ->execute();

        $this->assertEquals('0', $activation->responseCode);
    }

    /**
     * Test #98
     */
    public function testAddValueSVA1()
    {
        $addValue = $this->service
            ->addValue()
            ->withCard($this->sva1())
            ->withAmount(8.00)
            ->withCurrency('usd')
            ->execute();

        $this->assertEquals('0', $addValue->responseCode);
    }

    /**
     * Test #99
     */
    public function testAddValueSVA2()
    {
        $addValue = $this->service
            ->addValue()
            ->withCard($this->sva2())
            ->withAmount(9.00)
            ->withCurrency('usd')
            ->execute();

        $this->assertEquals('0', $addValue->responseCode);
    }

    /**
     * Test #100
     */
    public function testBalanceSVA1()
    {
        $balance = $this->service
            ->balance()
            ->withCard($this->sva1())
            ->execute();

        $this->assertEquals('0', $balance->responseCode);
        $this->assertEquals(10, (integer)$balance->balanceAmount);
    }

    /**
     * Test #101
     */
    public function testBalanceSVA2()
    {
        $balance = $this->service
            ->balance()
            ->withCard($this->sva2())
            ->execute();

        $this->assertEquals('0', $balance->responseCode);
        $this->assertEquals(10, (integer)$balance->balanceAmount);
    }

    /**
     * Test #102
     */
    public function testReplaceSVA1()
    {
        $replace = $this->service
            ->replace()
            ->withOldCard($this->sva1())
            ->withNewCard($this->sva2())
            ->execute();

        $this->assertEquals('0', $replace->responseCode);
    }

    /**
     * Test #103
     */
    public function testReplaceSVA2()
    {
        $replace = $this->service
            ->replace()
            ->withOldCard($this->sva2())
            ->withNewCard($this->sva1())
            ->execute();

        $this->assertEquals('0', $replace->responseCode);
    }

    /**
     * Test #104
     */
    public function testSaleSVA1()
    {
        $sale = $this->service
            ->sale()
            ->withCard($this->sva1())
            ->withAmount(1.00)
            ->withCurrency('usd')
            ->execute();

        $this->assertEquals('0', $sale->responseCode);
    }

    /**
     * Test #105
     */
    public function testSaleSVA2()
    {
        $sale = $this->service
            ->sale()
            ->withCard($this->sva2())
            ->withAmount(2.00)
            ->withCurrency('usd')
            ->execute();

        $this->assertEquals('0', $sale->responseCode);
    }

    /**
     * Test #106 & Test #108
     */
    public function testSaleAndVoidSVA1()
    {
        $sale = $this->service
            ->sale()
            ->withCard($this->sva1())
            ->withAmount(3.00)
            ->withCurrency('usd')
            ->execute();

        $this->assertEquals('0', $sale->responseCode);

        $void = $this->service
            ->void()
            ->withTransactionId($sale->transactionId)
            ->execute();

        $this->assertEquals('0', $void->responseCode);
    }

    /**
     * Test #107 & Test #109
     */
    public function testSaleAndReversalSVA2()
    {
        $sale = $this->service
            ->sale()
            ->withCard($this->sva2())
            ->withAmount(4.00)
            ->withCurrency('usd')
            ->execute();

        $this->assertEquals('0', $sale->responseCode);

        $reverse = $this->service
            ->reverse()
            ->withTransactionId($sale->transactionId)
            ->withAmount(4.00)
            ->execute();

        $this->assertEquals('0', $reverse->responseCode);
    }

    /**
     * Test #110
     */
    public function testDeactivateSVA1()
    {
        $deactivate = $this->service
            ->deactivate()
            ->withCard($this->sva1())
            ->execute();
    }

    public function testDeactivateSVA2()
    {
        $deactivate = $this->service
            ->deactivate()
            ->withCard($this->sva2())
            ->execute();
    }

    protected function config()
    {
        return TestServicesConfig::validMultiUseConfig();
    }

    protected function sva1()
    {
        return TestGiftCard::validGiftCardNotEncrypted();
    }

    protected function sva2()
    {
        return TestGiftCard::validGiftCardNotEncrypted2();
    }
}

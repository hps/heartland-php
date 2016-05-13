<?php

class GatewayCheckTest extends PHPUnit_Framework_TestCase
{
    private $checkService;

    public function setUp()
    {
        $this->checkService = new HpsCheckService(TestServicesConfig::validMultiUseConfig());
    }

    public function testCheckShouldDecline()
    {
        try {
            $this->checkService->sale(TestCheck::decline(), 5.00);
        } catch (HpsCheckException $e) {
            $this->assertEquals('1', $e->code);
        }
    }

    public function testShouldThrowHpsCheckException()
    {
        try {
            $this->checkService->sale(TestCheck::invalidCheckHolder(), 5.00);
        } catch (HpsCheckException $e) {
            $this->assertEquals('1', $e->code);
        }
    }

    public function testCheckShouldSale()
    {
        $response = $this->checkService->sale(TestCheck::approve(), 5.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testCheckShouldVoid()
    {
        $saleResponse = $this->checkService->sale(TestCheck::approve(), 5.00);
        $voidResponse = $this->checkService->void($saleResponse->transactionId);
        $this->assertNotNull($voidResponse);
        $this->assertEquals('0', $voidResponse->responseCode);
    }

    public function testSaleAndVoidWithClientTxnId()
    {
        $clientTransactionId = 10244203;
        $response = $this->checkService->sale(TestCheck::approve(), 5.00, $clientTransactionId);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);

        $voidResponse = $this->checkService->void(null, $clientTransactionId);
        $this->assertNotNull($voidResponse);
        $this->assertEquals('0', $voidResponse->responseCode);
    }
}

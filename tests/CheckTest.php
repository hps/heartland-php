<?php

require_once("setup.php");

class CheckTests extends PHPUnit_Framework_TestCase{

    private $checkService;

    public function setUp(){
        $this->checkService = new HpsCheckService(TestServicesConfig::ValidMultiUseConfig());
    }

    public function testCheckShouldSale(){
        $response = $this->checkService->sale('SALE',TestCheck::approve(), 5.00);
        $this->assertNotNull($response,'Response is null');
        $this->assertEquals('0',$response->responseCode);
        $this->assertEquals('Transaction Approved',$response->responseText);
    }

    /**
     * @expectedException       HpsCheckException
     * @expectedCode            1
     */
    public function testCheckShouldDecline(){
        $this->checkService->sale('sale',TestCheck::decline(), 5.00);
    }

    public function testCheckShouldVoid(){
        $saleResponse = $this->checkService->sale('SALE',TestCheck::approve(), 5.00);
        $voidResponse = $this->checkService->void($saleResponse->transactionId);
        $this->assertNotNull($voidResponse);
        $this->assertEquals('0',$voidResponse->responseCode);
    }


    /**
     * @expectedException       HpsCheckException
     * @expectedCode            1
     */
    public function testShouldThrowHpsCheckException(){
        $this->checkService->sale('SALE',TestCheck::invalidCheckHolder(), 5.00);
    }

    public function testSaleAndVoidWithClientTxnId(){
        $clientTransactionId = 10244201;
        $response = $this->checkService->sale('SALE',TestCheck::approve(), 5.00,$clientTransactionId);
        $this->assertNotNull($response,'Response is null');
        $this->assertEquals('0',$response->responseCode);
        $this->assertEquals('Transaction Approved',$response->responseText);

        $voidResponse = $this->checkService->void(null, $clientTransactionId);
        $this->assertNotNull($voidResponse);
        $this->assertEquals('0',$voidResponse->responseCode);
    }
} 
<?php

/**
 * Class FluentCreditTest
 */
class FluentCreditTest extends PHPUnit_Framework_TestCase
{
    /** @var HpsFluentCreditService $service */
    protected $service;

    protected function setUp()
    {
        $this->service = new HpsFluentCreditService(TestServicesConfig::validMultiUseConfig());
    }

    public function testAuthorizeAndCapture()
    {
        $config = TestServicesConfig::validMultiUseConfig();

        $auth = $this->service
            ->authorize()
            ->withAmount(10)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->withAllowDuplicates(true)
            ->execute();

        $this->assertEquals("00", $auth->responseCode);
        $this->assertNotNull($auth->transactionId);

        $get = $this->service->get($auth->transactionId)->execute();

        $this->assertEquals($get->authorizedAmount, $get->settlementAmount);

        /** @var \HpsReportTransactionDetails $capture */
        $capture = $this->service
            ->capture($auth->transactionId)
            ->withAmount(15)
            ->execute();

        $this->assertEquals("0", $capture->responseCode);
        $this->assertEquals("15.00", $capture->settlementAmount);
        $this->assertNotEquals($capture->authorizedAmount, $capture->settlementAmount);
    }
    public function testAuthorizeAndCaptureWithGratuity()
    {
        $config = TestServicesConfig::validMultiUseConfig();

        $auth = $this->service
            ->authorize()
            ->withAmount(10)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->withAllowDuplicates(true)
            ->execute();

        $this->assertEquals("00", $auth->responseCode);
        $this->assertNotNull($auth->transactionId);
/** @var \HpsReportTransactionDetails $capture */
        $capture = $this->service
            ->capture()
            ->withTransactionId($auth->transactionId)
            ->withAmount(15)
            ->withGratuity(5)
            ->execute();

        $this->assertEquals("0", $capture->responseCode);
        $this->assertEquals("15.00", $capture->settlementAmount);
        $this->assertEquals("5.00", $capture->gratuityAmount);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Authorize needs an amount
     */
    public function testAuthorizeWithNoAmount()
    {
        $this->service
            ->authorize()
            ->withCurrency('usd')
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->execute();
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Authorize can only use one payment method
     */
    public function testAuthorizeWithMultiplePaymentOptions()
    {
        $this->service
            ->authorize()
            ->withAmount(10)
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withToken('123456789')
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->execute();
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Capture needs a transactionId
     */
    public function testCaptureWithNoTransactionId()
    {
        $this->service
            ->capture()
            ->execute();
    }

    public function testCharge()
    {
        $response = $this->service
            ->charge()
            ->withAmount(10)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::certCardHolderShortZip())
            ->execute();

        $this->assertNotNull($response);
        $this->assertEquals("00", $response->responseCode);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Charge needs an amount
     */
    public function testChargeWithNoAmount()
    {
        $this->service
            ->charge()
            ->withCurrency('usd')
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->execute();
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Charge can only use one payment method
     */
    public function testChargeWithMultiplePaymentOptions()
    {
        $this->service
            ->charge()
            ->withAmount(10)
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withToken('123456789')
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->execute();
    }


    /**
     * @test
     */

    public function testUpdateTokenExpirationToExpiredDate()
    {
        $updateTokenExpireation = $this->service
            ->updateTokenExpiration()
            ->withToken(TestCreditCard::validVisaMUT())
            ->withExpMonth(1)
            ->withExpYear(2009)
            ->execute();
        $this->assertNotNull($updateTokenExpireation->transactionId);
    }


    /**
     * @test
     * @expectedException HpsGatewayException
     * @expectedExceptionCode 10
     * @expectedExceptionMessage Invalid card data
     */
    public function testUpdateTokenExpirationToNull()
    {
        $this->service
            ->updateTokenExpiration()
            ->withToken(TestCreditCard::validVisaMUT())
            ->withExpMonth(null)
            ->withExpYear(null)
            ->execute();
    }

    /**
     * @test
     * @expectedException HpsGatewayException
     * @expectedExceptionCode 10
     * @expectedExceptionMessage Invalid card data
     */
    public function testUpdateTokenExpirationOnInValidTokenShouldReturnException()
    {
        $this->service
            ->updateTokenExpiration()
            ->withToken(TestCreditCard::invalidMUT())
            ->withExpMonth(1)
            ->withExpYear(2019)
            ->execute();
    }

    /**
     * @test
     * @expectedException HpsArgumentException
     * @expectedExceptionMessage Edit needs a multi use token value
     */
    public function testUpdateTokenExpirationOnNullTokenShouldReturnException()
    {
        $this->service
            ->updateTokenExpiration()
            ->withToken(TestCreditCard::NullMUT())
            ->withExpMonth(1)
            ->withExpYear(2019)
            ->execute();

    }

    /**
     * @test
     */

    public function testUpdateTokenExpiration(){
        $updateTokenExpireation = $this->service
            ->updateTokenExpiration()
            ->withToken(TestCreditCard::validVisaMUT())
            ->withExpMonth(1)
            ->withExpYear(2019)
            ->execute();

        $this->assertNotNull($updateTokenExpireation->transactionId);
    }





    public function testCpcEdit()
    {
        $charge = $this->service
            ->charge()
            ->withAmount(10)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::certCardHolderShortZip())
            ->withAllowDuplicates(true)
            ->execute();

        $this->assertNotNull($charge->transactionId);

        $cpcData = new HpsCpcData();
        $cpcData->CardHolderPONbr = '12345';
        $cpcData->TaxType = HpsTaxType::SALES_TAX;
        $cpcData->TaxAmt = 0.06;

        $cpcEdit = $this->service
            ->cpcEdit()
            ->withTransactionId($charge->transactionId)
            ->withCpcData($cpcData)
            ->execute();

        $this->assertNotNull($cpcEdit);
        $this->assertEquals("00", $cpcEdit->responseCode);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage CpcEdit needs a transactionId
     */
    public function testCpcEditWithNoTransactionId()
    {
        $cpcData = new HpsCpcData();
        $cpcData->CardHolderPONbr = '12345';
        $cpcData->TaxType = HpsTaxType::SALES_TAX;
        $cpcData->TaxAmt = 0.06;

        $this->service
            ->cpcEdit()
            ->withCpcData($cpcData)
            ->execute();
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage CpcEdit needs cpcData
     */
    public function testCpcEditWithNoCpcData()
    {
        $this->service
            ->cpcEdit()
            ->withTransactionId('123456')
            ->execute();
    }

    public function testEdit()
    {
        $charge = $this->service
            ->charge()
            ->withAmount(10)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::certCardHolderShortZip())
            ->withAllowDuplicates(true)
            ->execute();

        $this->assertNotNull($charge->transactionId);

        $edit = $this->service
            ->edit()
            ->withTransactionId($charge->transactionId)
            ->withAmount(11)
            ->execute();

        $this->assertNotNull($edit);
        $this->assertEquals("00", $edit->responseCode);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Edit needs a transactionId
     */
    public function testEditWithNoTransactionId()
    {
        $this->service
            ->edit()
            ->withAmount(11)
            ->execute();
    }

    public function testGet()
    {
        $charge = $this->service
            ->charge()
            ->withAmount(10)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::certCardHolderShortZip())
            ->withAllowDuplicates(true)
            ->execute();

        $this->assertNotNull($charge->transactionId);

        $get = $this->service
            ->get()
            ->withTransactionId($charge->transactionId)
            ->execute();

        $this->assertNotNull($get);
        $this->assertEquals("00", $get->responseCode);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Get needs a transactionId
     */
    public function testGetWithNoTransactionId()
    {
        $this->service
            ->get()
            ->execute();
    }

    public function testListTransactions()
    {
        date_default_timezone_set("UTC");
        $config = TestServicesConfig::validMultiUseConfig();
        $dateFormat = 'Y-m-d\TH:i:s.00\Z';
        $dateMinus10 = new DateTime();
        $dateMinus10->sub(new DateInterval('PT10H'));
        $dateMinus10Utc = gmdate($dateFormat, $dateMinus10->Format('U'));
        $nowUtc = gmdate($dateFormat);

        $transactions = $this->service
            ->listTransactions()
            ->withStartDate($dateMinus10Utc)
            ->withEndDate($nowUtc)
            ->withFilterBy("CreditSale") // HpsTransactionType::CAPTURE
            ->execute();

        $this->assertTrue(0 != count($transactions));
        $charge0 = $transactions[0]->originalTransactionId;
        $charge1 = $transactions[1]->originalTransactionId;
        $this->assertNotNull($charge0);
        $this->assertNotNull($charge1);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage ListTransactions needs a startDate
     */
    public function testListTransactionsWithNoStartDate()
    {
        $this->service
            ->listTransactions()
            ->withEndDate(new DateTime())
            ->execute();
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage ListTransactions needs an endDate
     */
    public function testListTransactionsWithNoEndDate()
    {
        $this->service
            ->listTransactions()
            ->withStartDate(new DateTime())
            ->execute();
    }

    public function testRefund()
    {
        $charge = $this->service
            ->charge()
            ->withAmount(10)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::certCardHolderShortZip())
            ->withAllowDuplicates(true)
            ->execute();

        $refund = $this->service
            ->refund()
            ->withAmount(10)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::certCardHolderShortZip())
            ->execute();

        $this->assertNotNull($refund);
        $this->assertEquals("0", $refund->responseCode);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Refund needs an amount
     */
    public function testRefundWithNoAmount()
    {
        $this->service
            ->refund()
            ->withTransactionId('123456')
            ->execute();
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Refund can only use one payment method
     */
    public function testRefundWithTransactionIdAndCard()
    {
        $this->service
            ->refund(10)
            ->withTransactionId('123456')
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->execute();
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Refund can only use one payment method
     */
    public function testRefundWithTransactionIdAndToken()
    {
        $this->service
            ->refund(10)
            ->withTransactionId('123456')
            ->withToken('123456')
            ->execute();
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Refund can only use one payment method
     */
    public function testRefundWithCardAndToken()
    {
        $this->service
            ->refund(10)
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withToken('123456')
            ->execute();
    }

    public function testReverse()
    {
        $charge = $this->service
            ->charge()
            ->withAmount(10)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::certCardHolderShortZip())
            ->withAllowDuplicates(true)
            ->execute();

        $reverse = $this->service
            ->reverse()
            ->withAmount(10)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->execute();

        $this->assertNotNull($reverse);
        $this->assertEquals("00", $reverse->responseCode);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Reverse needs an amount
     */
    public function testReverseWithNoAmount()
    {
        $this->service
            ->reverse()
            ->withCurrency('usd')
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->execute();
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Reverse can only use one payment method
     */
    public function testReverseWithCardAndTransactionId()
    {
        $this->service
            ->reverse()
            ->withAmount(10)
            ->withCurrency('usd')
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withTransactionId('123456')
            ->execute();
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Reverse can only use one payment method
     */
    public function testReverseWithTokenAndTransactionId()
    {
        $this->service
            ->reverse()
            ->withAmount(10)
            ->withCurrency('usd')
            ->withToken('123456')
            ->withTransactionId('123456')
            ->execute();
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Reverse can only use one payment method
     */
    public function testReverseWithCardAndToken()
    {
        $this->service
            ->reverse()
            ->withAmount(10)
            ->withCurrency('usd')
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withToken('123456')
            ->execute();
    }

    public function testVerify()
    {
        $response = $this->service
            ->verify()
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->execute();

        $this->assertNotNull($response);
        $this->assertEquals("85", $response->responseCode);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Verify can only use one payment method
     */
    public function testVerifyWithCardAndToken()
    {
        $this->service
            ->verify()
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withToken('123456')
            ->execute();
    }

    public function testVoid()
    {
        $charge = $this->service
            ->charge()
            ->withAmount(10)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::certCardHolderShortZip())
            ->withAllowDuplicates(true)
            ->execute();

        $this->assertNotNull($charge->transactionId);

        $void = $this->service
            ->void()
            ->withTransactionId($charge->transactionId)
            ->execute();

        $this->assertNotNull($void);
        $this->assertEquals("00", $void->responseCode);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Void needs a transactionId
     */
    public function testVoidWithNoTransactionId()
    {
        $this->service
            ->void()
            ->execute();
    }    
    
    public function testChargeConvenienceAmount()
    {
        $charge = $this->service
            ->charge()
            ->withAmount(30)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->withAllowDuplicates(true)
            ->withConvenienceAmtInfo(20)
            ->execute();

        $this->assertNotNull($charge->transactionId);

        $reportTxnDetail = $this->service
            ->get()
            ->withTransactionId($charge->transactionId)
            ->execute();
        
        $this->assertNotNull($reportTxnDetail);
        $this->assertEquals(20, $reportTxnDetail->convenienceAmount);
    }
    
    public function testChargeShippingAmount()
    {
        $charge = $this->service
            ->charge()
            ->withAmount(25)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->withAllowDuplicates(true)
            ->withShippingAmtInfo(15)
            ->execute();

        $this->assertNotNull($charge->transactionId);

        $reportTxnDetail = $this->service
            ->get()
            ->withTransactionId($charge->transactionId)
            ->execute();
        
        $this->assertNotNull($reportTxnDetail);
        $this->assertEquals(15, $reportTxnDetail->shippingAmount);
    }
    
    public function testAuthorizeConvenienceAmount()
    {
        $charge = $this->service
            ->authorize()
            ->withAmount(30)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->withAllowDuplicates(true)
            ->withConvenienceAmtInfo(20)
            ->execute();

        $this->assertNotNull($charge->transactionId);

        $reportTxnDetail = $this->service
            ->get()
            ->withTransactionId($charge->transactionId)
            ->execute();
        
        $this->assertNotNull($reportTxnDetail);
        $this->assertEquals(20, $reportTxnDetail->convenienceAmount);
    }
    
    public function testAuthorizeShippingAmount()
    {
        $charge = $this->service
            ->authorize()
            ->withAmount(25)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->withAllowDuplicates(true)
            ->withShippingAmtInfo(15)
            ->execute();

        $this->assertNotNull($charge->transactionId);

        $reportTxnDetail = $this->service
            ->get()
            ->withTransactionId($charge->transactionId)
            ->execute();
        
        $this->assertNotNull($reportTxnDetail);
        $this->assertEquals(15, $reportTxnDetail->shippingAmount);
    }
    
    public function testChargeConvenienceAndShippingAmount()
    {
        $charge = $this->service
            ->charge()
            ->withAmount(35)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->withAllowDuplicates(true)
            ->withConvenienceAmtInfo(10)                
            ->withShippingAmtInfo(15)
            ->execute();

        $this->assertNotNull($charge->transactionId);

        $reportTxnDetail = $this->service
            ->get()
            ->withTransactionId($charge->transactionId)
            ->execute();
        
        $this->assertNotNull($reportTxnDetail);
        $this->assertEquals(10, $reportTxnDetail->convenienceAmount);
        $this->assertEquals(15, $reportTxnDetail->shippingAmount);
    }
    
    public function testAuthorizeConvenienceAndShippingAmount()
    {
        $charge = $this->service
            ->authorize()
            ->withAmount(35)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->withAllowDuplicates(true)
            ->withConvenienceAmtInfo(10)                
            ->withShippingAmtInfo(15)
            ->execute();

        $this->assertNotNull($charge->transactionId);

        $reportTxnDetail = $this->service
            ->get()
            ->withTransactionId($charge->transactionId)
            ->execute();
        
        $this->assertNotNull($reportTxnDetail);
        $this->assertEquals(10, $reportTxnDetail->convenienceAmount);
        $this->assertEquals(15, $reportTxnDetail->shippingAmount);
    }
    
    /**
     * @expectedException        HpsInvalidRequestException
     * @expectedExceptionCode    HpsExceptionCodes::INVALID_AMOUNT
     * @expectedExceptionMessage Must be greater than or equal to 0
     */
    public function testChargeConvenienceAndShippingInvalidAmount()
    {
        $this->service
            ->charge()
            ->withAmount(35)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->withAllowDuplicates(true)
            ->withConvenienceAmtInfo(-10)                
            ->withShippingAmtInfo(-10)
            ->execute();
    }
    
    /**
     * @expectedException        HpsInvalidRequestException
     * @expectedExceptionCode    HpsExceptionCodes::INVALID_AMOUNT
     * @expectedExceptionMessage Must be greater than or equal to 0
     */
    public function testChargeInvalidShippingAmount()
    {       
        $this->service
            ->charge()
            ->withAmount(35)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->withAllowDuplicates(true)
            ->withConvenienceAmtInfo(20)                
            ->withShippingAmtInfo(-10)
            ->execute();        
    }
    
    /**
     * @expectedException        HpsInvalidRequestException
     * @expectedExceptionCode    HpsExceptionCodes::INVALID_AMOUNT
     * @expectedExceptionMessage Must be greater than or equal to 0
     */
    public function testAuthorizeConvenienceAndShippingInvalidAmount()
    {
        $this->service
            ->authorize()
            ->withAmount(35)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->withAllowDuplicates(true)
            ->withConvenienceAmtInfo(-10)                
            ->withShippingAmtInfo(-15)
            ->execute();
    }
    
    /**
     * @expectedException        HpsInvalidRequestException
     * @expectedExceptionCode    HpsExceptionCodes::INVALID_AMOUNT
     * @expectedExceptionMessage Must be greater than or equal to 0
     */
    public function testAuthorizeInvalidShippingAmount()
    {       
        $this->service
            ->authorize()
            ->withAmount(35)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->withAllowDuplicates(true)
            ->withConvenienceAmtInfo(20)                
            ->withShippingAmtInfo(-10)
            ->execute();        
    }
    
}

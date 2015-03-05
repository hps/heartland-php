<?php

require_once("setup.php");

class TokenServiceTests extends PHPUnit_Framework_TestCase{

    public $tokenService = null;
    public $publicKey = null;

    private function _getToken($card, $key = null){
        if($key != null and $key != ""){
            $this->publicKey = $key;
        }else{
            $this->publicKey = TestServicesConfig::ValidMultiUsePublicKey();
        }

        $this->tokenService = new HpsTokenService($this->publicKey);
        $tokenResponse = $this->tokenService->getToken($card);
        if(isset($tokenResponse->token_value)){
            $token = new HpsTokenData();
            $token->tokenValue = (string)$tokenResponse->token_value;
            return $token;
        }else{
            return $tokenResponse;
        }
    }

    // Basic single token fetching tests

    /**
     * @test
     * This test will return a valid token
     */
    public function ShouldReturnValidToken(){
        $token = $this->_getToken(TestCreditCard::ValidVisaCreditCard());
        $this->assertTrue(!empty($token->tokenValue));
        $this->assertStringStartsWith('supt',$token->tokenValue);
    }

    /**
     * @test
     * @expectedException        HpsException
     * @expectedExceptionCode    0
     * @expectedExceptionMessage Public API Key must Contain three underscores
     */
    public function ShouldThrow_BadPublicKeyError(){
        $this->_getToken(TestCreditCard::ValidVisaCreditCard(),'BAD_KEY');
    }

    /**
     * @test
     */
    public function ShouldThrow_BadPublic(){
        $token = $this->_getToken(TestCreditCard::ValidVisaCreditCard(),'STILL_BAD_KEY');
        $this->assertTrue(empty($token));
    }

    /**
     * @test
     * @expectedException        HpsException
     * @expectedExceptionCode    2
     * @expectedExceptionMessage Card number is invalid
     */
    public function test_Validation_InvalidCardNumber_ShouldReturnError(){
        $card = TestCreditCard::ValidVisaCreditCard();
        $card->number = "11111111111111111111111111111111111";
        $this->_getToken($card);
    }

    /**
     * @test
     * @expectedException        HpsException
     * @expectedExceptionCode    2
     * @expectedExceptionMessage Card expiration month is invalid.
     */
    public function test_Validation_TooHighExpirationMonth_ShouldReturnError(){
        $card = TestCreditCard::ValidVisaCreditCard();
        $card->expMonth = 13;
        $this->_getToken($card);
    }

    /**
     * @test
     * @expectedException        HpsException
     * @expectedExceptionCode    2
     * @expectedExceptionMessage Card expiration year is invalid.
     */
    public function test_Validation_TooLowExpirationYear_ShouldReturnError(){
        $card = TestCreditCard::ValidVisaCreditCard();
        $card->expYear = 12;
        $this->_getToken($card);
    }

    // Charge Testing with a token
    /**
     * @test
     * Testing getting an Amex single use token then charging it
     */
    public function test_get_token_from_Amex_and_charge_it(){
        $token = $this->_getToken(TestCreditCard::validAmexCreditCard());
        $chargeService = new HpsChargeService(TestServicesConfig::ValidMultiUseConfig());
        $charge = $chargeService->charge(1,'USD',$token,TestCardHolder::ValidCardHolder());
        $this->assertNotNull($charge->transactionId);
        $this->assertEquals('00',$charge->responseCode);
    }

    /**
     * @test
     * Testing getting a Discover Card single use token then charging it
     */
    public function test_get_token_from_Discover_and_charge_it(){
        $token = $this->_getToken(TestCreditCard::validDiscoverCreditCard());
        $chargeService = new HpsChargeService(TestServicesConfig::ValidMultiUseConfig());
        $charge = $chargeService->charge(1,'USD',$token,TestCardHolder::ValidCardHolder());
        $this->assertNotNull($charge->transactionId);
        $this->assertEquals('00',$charge->responseCode);
    }

    /**
     * @test
     * Testing getting a Master Card single use token then charging it
     */
    public function test_get_token_from_MasterCard_and_charge_it(){
        $token = $this->_getToken(TestCreditCard::validMasterCreditCard());
        $chargeService = new HpsChargeService(TestServicesConfig::ValidMultiUseConfig());
        $charge = $chargeService->charge(1,'USD',$token,TestCardHolder::ValidCardHolder());
        $this->assertNotNull($charge->transactionId);
        $this->assertEquals('00',$charge->responseCode);
    }

    /**
     * @test
     * Testing getting a Visa single use token then charging it
     */
    public function test_get_token_from_Visa_and_charge_it(){
        $token = $this->_getToken(TestCreditCard::validVisaCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::ValidMultiUseConfig());
        $charge = $chargeService->charge(1,'USD',$token,TestCardHolder::ValidCardHolder());
        $this->assertNotNull($charge->transactionId);
        $this->assertEquals('00',$charge->responseCode);
    }

    // Charge Testing with a Multi use token

    /**
     * @test
     * Testing getting an Amex single use token then charging it and requesting a multiuse token
     */
    public function test_get_multi_use_token_from_Amex_and_charge_it(){
        $token = $this->_getToken(TestCreditCard::validAmexCreditCard());
        $chargeService = new HpsChargeService(TestServicesConfig::ValidMultiUseConfig());
        $charge = $chargeService->charge(1,'USD',$token,TestCardHolder::ValidCardHolder(),true);
        $this->assertNotNull($charge->transactionId);
        $this->assertEquals('00',$charge->responseCode);
        $this->assertNotNull($charge->tokenData->tokenValue);

        $multiToken = $charge->tokenData;
        $chargeMulti = $chargeService->charge(1,'usd',$multiToken);
        $this->assertNotNull($chargeMulti->transactionId);
        $this->assertEquals('00',$chargeMulti->responseCode);
    }

    /**
     * @test
     * Testing getting an Discover single use token then charging it and requesting a multiuse token
     */
    public function test_get_multi_use_token_from_Discover_and_charge_it(){
        $token = $this->_getToken(TestCreditCard::validDiscoverCreditCard());
        $chargeService = new HpsChargeService(TestServicesConfig::ValidMultiUseConfig());
        $charge = $chargeService->charge(1,'USD',$token,TestCardHolder::ValidCardHolder(),true);
        $this->assertNotNull($charge->transactionId);
        $this->assertEquals('00',$charge->responseCode);
        $this->assertNotNull($charge->tokenData->tokenValue);

        $multiToken = $charge->tokenData;
        $chargeMulti = $chargeService->charge(1,'usd',$multiToken);
        $this->assertNotNull($chargeMulti->transactionId);
        $this->assertEquals('00',$chargeMulti->responseCode);
    }

    /**
     * @test
     * Testing getting an MasterCard single use token then charging it and requesting a multiuse token
     */
    public function test_get_multi_use_token_from_MasterCard_and_charge_it(){
        $token = $this->_getToken(TestCreditCard::validMasterCreditCard());
        $chargeService = new HpsChargeService(TestServicesConfig::ValidMultiUseConfig());
        $charge = $chargeService->charge(1,'USD',$token,TestCardHolder::ValidCardHolder(),true);
        $this->assertNotNull($charge->transactionId);
        $this->assertNotNull($charge->tokenData->tokenValue);

        $multiToken = $charge->tokenData;
        $chargeMulti = $chargeService->charge(1,'usd',$multiToken);
        $this->assertNotNull($chargeMulti->transactionId);
        $this->assertEquals('00',$chargeMulti->responseCode);
    }

    /**
     * @test
     * Testing getting a Visa single use token then charging it and requesting a multiuse token
     */
    public function test_get_multi_use_token_from_Visa_and_charge_it(){
        $token = $this->_getToken(TestCreditCard::validVisaCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::ValidMultiUseConfig());
        $charge = $chargeService->charge(1,'USD',$token,TestCardHolder::ValidCardHolder(),true);
        $this->assertNotNull($charge->transactionId);
        $this->assertNotNull($charge->tokenData->tokenValue);

        $multiToken = $charge->tokenData;
        $chargeMulti = $chargeService->charge(1,'usd',$multiToken);
        $this->assertNotNull($chargeMulti->transactionId);
        $this->assertEquals('00',$chargeMulti->responseCode);
    }

    // Authorize Token Tests

    /**
     * @test
    /// Amex authorize should return response code '00'.
     */
    public function Amex_Token_Authorize_ShouldReturnOk()
    {
        $token = $this->_getToken(TestCreditCard::validAmexCreditCard());
        $chargeService = new HpsChargeService(TestServicesConfig::ValidMultiUseConfig());
        $response = $chargeService->authorize(50, "usd", $token, TestCardHolder::ValidCardHolder());
        $this->assertEquals("00", $response->responseCode);
    }
    /**
     * @test
    /// Visa authorize should return response code '00'.
     */
    public function Discover_Token_Authorize_ShouldReturnOk()
    {
        $token = $this->_getToken(TestCreditCard::validDiscoverCreditCard());
        $chargeService = new HpsChargeService(TestServicesConfig::ValidMultiUseConfig());
        $response = $chargeService->authorize(50, "usd", $token, TestCardHolder::ValidCardHolder());
        $this->assertEquals("00", $response->responseCode);
    }

    /**
     * @test
    /// Visa authorize should return response code '00'.
     */
    public function MasterCard_Token_Authorize_ShouldReturnOk()
    {
        $token = $this->_getToken(TestCreditCard::validMasterCreditCard());
        $chargeService = new HpsChargeService(TestServicesConfig::ValidMultiUseConfig());
        $response = $chargeService->authorize(50, "usd", $token, TestCardHolder::ValidCardHolder());
        $this->assertEquals("00", $response->responseCode);
    }

    /**
     * @test
    /// Visa authorize should return response code '00'.
     */
    public function Visa_Token_Authorize_ShouldReturnOk()
    {
        $token = $this->_getToken(TestCreditCard::validVisaCreditCard());
        $chargeService = new HpsChargeService(TestServicesConfig::ValidMultiUseConfig());
        $response = $chargeService->authorize(50, "usd", $token, TestCardHolder::ValidCardHolder());
        $this->assertEquals("00", $response->responseCode);
    }

    // Authorize Testing with a multi use token
    /**
     * @test
    /// Amex authorize should return response code '00' and return multiuse token
     */
    public function Amex_Token_Authorize_ShouldReturnOk_and_Multiuse()
    {
        $token = $this->_getToken(TestCreditCard::validAmexCreditCard());
        $chargeService = new HpsChargeService(TestServicesConfig::ValidMultiUseConfig());
        $auth = $chargeService->authorize(50, "usd", $token, TestCardHolder::ValidCardHolder(),true);
        $this->assertEquals("00", $auth->responseCode);
        $this->assertNotNull($auth->tokenData->tokenValue);

        $multiToken = $auth->tokenData;
        $chargeMulti = $chargeService->authorize(1,'usd',$multiToken);
        $this->assertNotNull($chargeMulti->transactionId);
        $this->assertEquals('00',$chargeMulti->responseCode);
    }

    /**
     * @test
    /// Discover authorize should return response code '00' and return multiuse token
     */
    public function Discover_Token_Authorize_ShouldReturnOk_and_Multiuse()
    {
        $token = $this->_getToken(TestCreditCard::validDiscoverCreditCard());
        $chargeService = new HpsChargeService(TestServicesConfig::ValidMultiUseConfig());
        $auth = $chargeService->authorize(50, "usd", $token, TestCardHolder::ValidCardHolder(),true);
        $this->assertEquals("00", $auth->responseCode);
        $this->assertNotNull($auth->tokenData->tokenValue);

        $multiToken = $auth->tokenData;
        $chargeMulti = $chargeService->authorize(1,'usd',$multiToken);
        $this->assertNotNull($chargeMulti->transactionId);
        $this->assertEquals('00',$chargeMulti->responseCode);
    }

    /**
     * @test
    /// MasterCard authorize should return response code '00' and return multiuse token
     */
    public function MasterCard_Token_Authorize_ShouldReturnOk_and_Multiuse()
    {
        $token = $this->_getToken(TestCreditCard::validMasterCreditCard());
        $chargeService = new HpsChargeService(TestServicesConfig::ValidMultiUseConfig());
        $auth = $chargeService->authorize(50, "usd", $token, TestCardHolder::ValidCardHolder(),true);
        $this->assertEquals("00", $auth->responseCode);
        $this->assertNotNull($auth->tokenData->tokenValue);

        $multiToken = $auth->tokenData;
        $chargeMulti = $chargeService->authorize(1,'usd',$multiToken);
        $this->assertNotNull($chargeMulti->transactionId);
        $this->assertEquals('00',$chargeMulti->responseCode);
    }

    /**
     * @test
    /// Visa authorize should return response code '00' and return multiuse token
     */
    public function Visa_Token_Authorize_ShouldReturnOk_and_Multiuse()
    {
        $token = $this->_getToken(TestCreditCard::validVisaCreditCard());
        $chargeService = new HpsChargeService(TestServicesConfig::ValidMultiUseConfig());
        $auth = $chargeService->authorize(50, "usd", $token, TestCardHolder::ValidCardHolder(),true);
        $this->assertEquals("00", $auth->responseCode);
        $this->assertNotNull($auth->tokenData->tokenValue);

        $multiToken = $auth->tokenData;
        $chargeMulti = $chargeService->authorize(1,'usd',$multiToken);
        $this->assertNotNull($chargeMulti->transactionId);
        $this->assertEquals('00',$chargeMulti->responseCode);
    }

    // Testing Verify with a token

    /**
     * @test
     * Testing getting a single use token then running a verify with Amex
     */
    public function test_Integration_WhenTokenIsAcquired_ShouldBeAbleToVerifyAmex(){
        $token = $this->_getToken(TestCreditCard::validAmexCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::ValidMultiUseConfig());
        $response = $chargeService->verify($token,TestCardHolder::ValidCardHolder());
        $this->assertEquals($response->responseCode, "00");
    }

    /**
     * @test
     * Testing getting a single use token then running a verify with Discover
     */
    public function test_Integration_WhenTokenIsAcquired_ShouldBeAbleToVerifyDiscover(){
        $token = $this->_getToken(TestCreditCard::validDiscoverCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::ValidMultiUseConfig());
        $response = $chargeService->verify($token,TestCardHolder::ValidCardHolder());
        $this->assertEquals($response->responseCode, "85");
    }

    /**
     * @test
     * Testing getting a single use token then running a verify with Master Card
     */
    public function test_Integration_WhenTokenIsAcquired_ShouldBeAbleToVerifyMaster(){
        $token = $this->_getToken(TestCreditCard::validMasterCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::ValidMultiUseConfig());
        $response = $chargeService->verify($token,TestCardHolder::ValidCardHolder());
        $this->assertEquals($response->responseCode, "85");
    }
    /**
     * @test
     * Testing getting a single use token then running a verify with visa
     */
    public function test_Integration_WhenTokenIsAcquired_ShouldBeAbleToVerifyVisa(){
        $token = $this->_getToken(TestCreditCard::validVisaCreditCard());
        $chargeService = new HpsChargeService(TestServicesConfig::ValidMultiUseConfig());
        $response = $chargeService->verify($token,TestCardHolder::ValidCardHolder());
        $this->assertEquals($response->responseCode, "85");
    }

    // Verify Testing with multi use token

    /**
     * @test
     * Testing getting a single use token then running a verify with amex and requesting a multiuse token
     */
    public function test_get_token_from_amex_verify_get_multi_token_test_and_on_error(){
        $token = $this->_getToken(TestCreditCard::validAmexCreditCard());
        $chargeService = new HpsChargeService(TestServicesConfig::ValidMultiUseConfig());
        $response = $chargeService->verify($token,TestCardHolder::ValidCardHolder(),true);
        $this->assertEquals('00',$response->responseCode);
        $this->assertNotNull($response->tokenData->tokenValue);

        $multiToken = $response->tokenData;
        $verifyMultiToken = $chargeService->verify($multiToken,TestCardHolder::ValidCardHolder());
        $this->assertEquals('00',$verifyMultiToken->responseCode);
    }

    /**
     * @test
     * Testing getting a single use token then running a verify with visa and requesting a multiuse token
     */
    public function test_get_token_from_discover_verify_get_multi_token_test_and_on_error(){
        $token = $this->_getToken(TestCreditCard::validDiscoverCreditCard());
        $chargeService = new HpsChargeService(TestServicesConfig::ValidMultiUseConfig());
        $response = $chargeService->verify($token,TestCardHolder::ValidCardHolder(),true);
        $this->assertEquals('85',$response->responseCode);
        $this->assertNotNull($response->tokenData->tokenValue);

        $multiToken = $response->tokenData;
        $verifyMultiToken = $chargeService->verify($multiToken);
        $this->assertEquals('85',$verifyMultiToken->responseCode);
    }

    /**
     * @test
     * Testing getting a single use token then running a verify with visa and requesting a multiuse token
     */
    public function test_get_token_from_MasterCard_verify_get_multi_token_test_and_on_error(){
        $token = $this->_getToken(TestCreditCard::validMasterCreditCard());
        $chargeService = new HpsChargeService(TestServicesConfig::ValidMultiUseConfig());
        $response = $chargeService->verify($token,TestCardHolder::ValidCardHolder(),true);
        $this->assertEquals('85',$response->responseCode);
        $this->assertNotNull($response->tokenData->tokenValue);

        $multiToken = $response->tokenData;
        $verifyMultiToken = $chargeService->verify($multiToken);
        $this->assertEquals('85',$verifyMultiToken->responseCode);
    }

    /**
     * @test
     * Testing getting a single use token then running a verify with visa and requesting a multiuse token
     */
    public function test_get_token_from_visa_verify_get_multi_token_test_and_on_error(){
        $token = $this->_getToken(TestCreditCard::validVisaCreditCard());
        $chargeService = new HpsChargeService(TestServicesConfig::ValidMultiUseConfig());
        $response = $chargeService->verify($token,TestCardHolder::ValidCardHolder(),true);
        $this->assertEquals('85',$response->responseCode);
        $this->assertNotNull($response->tokenData->tokenValue);

        $multiToken = $response->tokenData;
        $verifyMultiToken = $chargeService->verify($multiToken);
        $this->assertEquals('85',$verifyMultiToken->responseCode);
    }


    // Refund Token Tests

    /**
     * @test
    /// Amex refund test with token
     */
    public function Amex_Token_Return_ShouldBe_Ok()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->_getToken(TestCreditCard::validAmexCreditCard());
        $chargeSvc = new HpsChargeService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->refund(15.15, "usd", $token, TestCardHolder::certCardHolderShortZip());
        $this->assertEquals($response->responseCode, "0");
    }

    /**
     * @test
    /// Mastercard return test with token
     */
    public function Mastercard_Token_Return_ShouldBe_Ok()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->_getToken(TestCreditCard::validMasterCreditCard());
        $chargeSvc = new HpsChargeService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->refund(15.15, "usd", $token, TestCardHolder::certCardHolderShortZip());
        $this->assertEquals($response->responseCode, "0");
    }

    /**
     * @test
    /// Discover return test with token
     */
    public function Discover_Token_Return_ShouldBe_Ok()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->_getToken(TestCreditCard::validDiscoverCreditCard());
        $chargeSvc = new HpsChargeService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->refund(15.15, "usd", $token, TestCardHolder::certCardHolderShortZip());
        $this->assertEquals($response->responseCode, "0");
    }

    /**
     * @test
    /// Visa refund test with token
     */
    public function Visa_Token_Refund_ShouldBe_Ok()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->_getToken(TestCreditCard::validVisaCreditCard());
        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->refund(15.15, "usd", $token, TestCardHolder::certCardHolderShortZip());
        $this->assertEquals($response->responseCode, "0");
    }

    // Refund Multi Token Tests

    /**
     * @test
    /// Amex multi Token refund test with token
     */
    public function Amex_Multi_Token_Return_ShouldBe_Ok()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->_getToken(TestCreditCard::validAmexCreditCard());
        $chargeSvc = new HpsChargeService($testConfig->ValidMultiUseConfig());
        $charge = $chargeSvc->charge(50,'usd',$token,TestCardHolder::ValidCardHolder(),true);
        $this->assertEquals($charge->responseCode, "0");
        $this->assertNotNull($charge->tokenData->tokenValue);

        $multiToken = $charge->tokenData;
        $refundMultiToken = $chargeSvc->refund(50, "usd", $multiToken, TestCardHolder::certCardHolderShortZip());
        $this->assertEquals('00',$refundMultiToken->responseCode);
    }

    /**
     * @test
    /// Discover multi Token refund test with token
     */
    public function Discover_Multi_Token_Return_ShouldBe_Ok()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->_getToken(TestCreditCard::validDiscoverCreditCard());
        $chargeSvc = new HpsChargeService($testConfig->ValidMultiUseConfig());
        $charge = $chargeSvc->charge(50,'usd',$token,TestCardHolder::ValidCardHolder(),true);
        $this->assertEquals($charge->responseCode, "0");
        $this->assertNotNull($charge->tokenData->tokenValue);

        $multiToken = $charge->tokenData;
        $refundMultiToken = $chargeSvc->refund(50, "usd", $multiToken, TestCardHolder::certCardHolderShortZip());
        $this->assertEquals('00',$refundMultiToken->responseCode);
    }

    /**
     * @test
    /// MasterCard multi Token refund test with token
     */
    public function MasterCard_Multi_Token_Return_ShouldBe_Ok()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->_getToken(TestCreditCard::validMasterCreditCard());
        $chargeSvc = new HpsChargeService($testConfig->ValidMultiUseConfig());
        $charge = $chargeSvc->charge(50,'usd',$token,TestCardHolder::ValidCardHolder(),true);
        $this->assertEquals($charge->responseCode, "0");
        $this->assertNotNull($charge->tokenData->tokenValue);

        $multiToken = $charge->tokenData;
        $refundMultiToken = $chargeSvc->refund(50, "usd", $multiToken, TestCardHolder::certCardHolderShortZip());
        $this->assertEquals('00',$refundMultiToken->responseCode);
    }

    /**
     * @test
    /// Visa multi Token refund test with token
     */
    public function Visa_Multi_Token_Return_ShouldBe_Ok()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->_getToken(TestCreditCard::validVisaCreditCard());
        $chargeSvc = new HpsChargeService($testConfig->ValidMultiUseConfig());
        $charge = $chargeSvc->charge(50,'usd',$token,TestCardHolder::ValidCardHolder(),true);
        $this->assertEquals($charge->responseCode, "0");
        $this->assertNotNull($charge->tokenData->tokenValue);

        $multiToken = $charge->tokenData;
        $refundMultiToken = $chargeSvc->refund(50, "usd", $multiToken, TestCardHolder::certCardHolderShortZip());
        $this->assertEquals('00',$refundMultiToken->responseCode);
    }

    //Reverse Token Tests

    /**
     * @test
    /// Amex Token refund test with token
     */
    public function Amex_Token_Reverse_ShouldBe_Ok()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->_getToken(TestCreditCard::validAmexCreditCard());
        $chargeSvc = new HpsChargeService($testConfig->ValidMultiUseConfig());
        $reverse = $chargeSvc->reverse($token,50,'usd');

        $this->assertEquals('00',$reverse->responseCode);
    }

    /**
     * @test
    /// Discover Token refund test with token
     */
    public function Discover_Token_Reverse_ShouldBe_Ok()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->_getToken(TestCreditCard::validDiscoverCreditCard());
        $chargeSvc = new HpsChargeService($testConfig->ValidMultiUseConfig());
        $reverse = $chargeSvc->reverse($token,50,'usd');

        $this->assertEquals('00',$reverse->responseCode);
    }

    /**
     * @test
    /// MasterCard Token refund test with token
     */
    public function MasterCard_Token_Reverse_ShouldBe_Ok()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->_getToken(TestCreditCard::validMasterCreditCard());
        $chargeSvc = new HpsChargeService($testConfig->ValidMultiUseConfig());
        $reverse = $chargeSvc->reverse($token,50,'usd');

        $this->assertEquals('00',$reverse->responseCode);
    }

    /**
     * @test
    /// Visa Token refund test with token
     */
    public function Visa_Token_Reverse_ShouldBe_Ok()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->_getToken(TestCreditCard::validVisaCreditCard());
        $chargeSvc = new HpsChargeService($testConfig->ValidMultiUseConfig());
        $reverse = $chargeSvc->reverse($token,50,'usd');

        $this->assertEquals('00',$reverse->responseCode);
    }

    // Reverse Multi Token Tests

    /**
     * @test
    /// Amex Reverse multi Token tests
     */
    public function Amex_Mulit_Token_Reverse_ShouldBe_Ok()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->_getToken(TestCreditCard::validAmexCreditCard());
        $chargeSvc = new HpsChargeService($testConfig->ValidMultiUseConfig());
        $chargeResponse =$chargeSvc->charge(17.01,'usd',$token,null,true);
        $this->assertEquals($chargeResponse->responseCode, "0");

        $muToken = $chargeResponse->tokenData;
        $response = $chargeSvc->reverse($muToken,17.01, "usd");
        $this->assertEquals($response->responseCode, "0");
    }

    /**
     * @test
    /// Discover Reverse multi Token tests
     */
    public function Discover_Multi_Token_Reverse_ShouldBe_Ok()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->_getToken(TestCreditCard::validDiscoverCreditCard());
        $chargeSvc = new HpsChargeService($testConfig->ValidMultiUseConfig());
        $chargeResponse =$chargeSvc->charge(17.01,'usd',$token,null,true);
        $this->assertEquals($chargeResponse->responseCode, "0");

        $muToken = $chargeResponse->tokenData;
        $response = $chargeSvc->reverse($muToken,17.01, "usd");
        $this->assertEquals($response->responseCode, "0");
    }

    /**
     * @test
    /// MasterCard Reverse multi Token tests
     */
    public function MasterCard_Multi_Token_Reverse_ShouldBe_Ok()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->_getToken(TestCreditCard::validMasterCreditCard());
        $chargeSvc = new HpsChargeService($testConfig->ValidMultiUseConfig());
        $chargeResponse =$chargeSvc->charge(17.01,'usd',$token,null,true);
        $this->assertEquals($chargeResponse->responseCode, "0");

        $muToken = $chargeResponse->tokenData;
        $response = $chargeSvc->reverse($muToken,17.01, "usd");
        $this->assertEquals($response->responseCode, "0");
    }

    /**
     * @test
    /// Visa Reverse multi Token tests
     */
    public function Visa_Multi_Token_Reverse_ShouldBe_Ok()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->_getToken(TestCreditCard::validVisaCreditCard());
        $chargeSvc = new HpsChargeService($testConfig->ValidMultiUseConfig());
        $chargeResponse =$chargeSvc->charge(17.01,'usd',$token,null,true);
        $this->assertEquals($chargeResponse->responseCode, "0");

        $muToken = $chargeResponse->tokenData;
        $response = $chargeSvc->reverse($muToken,17.01, "usd");
        $this->assertEquals($response->responseCode, "0");
    }
}

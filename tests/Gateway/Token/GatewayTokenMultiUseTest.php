<?php

class GatewayTokenMultiUseTest extends PHPUnit_Framework_TestCase
{
    public $tokenService = null;
    public $publicKey    = null;

    private function getToken($card, $key = null)
    {
        if ($key != null and $key != "") {
            $this->publicKey = $key;
        } else {
            $this->publicKey = TestServicesConfig::ValidMultiUsePublicKey();
        }

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
    
    // Charge Testing with a Multi use token

    /**
     * @test
     * Testing getting an Amex single use token then charging it and requesting a multiuse token
     */
    public function testGetMultiUseTokenFromAmexAndChargeIt()
    {
        $token = $this->getToken(TestCreditCard::validAmexCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::ValidMultiUseConfig());
        $charge = $chargeService->charge(1, 'USD', $token, TestCardHolder::ValidCardHolder(), true);
        $this->assertNotNull($charge->transactionId);
        $this->assertEquals('00', $charge->responseCode);
        $this->assertNotNull($charge->tokenData->tokenValue);

        $multiToken = $charge->tokenData;
        $chargeMulti = $chargeService->charge(1, 'usd', $multiToken);
        $this->assertNotNull($chargeMulti->transactionId);
        $this->assertEquals('00', $chargeMulti->responseCode);
    }

    /**
     * @test
     * Testing getting an Discover single use token then charging it and requesting a multiuse token
     */
    public function testGetMultiUseTokenFromDiscoverAndChargeIt()
    {
        $token = $this->getToken(TestCreditCard::validDiscoverCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::ValidMultiUseConfig());
        $charge = $chargeService->charge(1, 'USD', $token, TestCardHolder::ValidCardHolder(), true);
        $this->assertNotNull($charge->transactionId);
        $this->assertEquals('00', $charge->responseCode);
        $this->assertNotNull($charge->tokenData->tokenValue);

        $multiToken = $charge->tokenData;
        $chargeMulti = $chargeService->charge(1, 'usd', $multiToken);
        $this->assertNotNull($chargeMulti->transactionId);
        $this->assertEquals('00', $chargeMulti->responseCode);
    }

    /**
     * @test
     * Testing getting an MasterCard single use token then charging it and requesting a multiuse token
     */
    public function testGetMultiUseTokenFromMasterCardAndChargeIt()
    {
        $token = $this->getToken(TestCreditCard::validMasterCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::ValidMultiUseConfig());
        $charge = $chargeService->charge(1, 'USD', $token, TestCardHolder::ValidCardHolder(), true);
        $this->assertNotNull($charge->transactionId);
        $this->assertNotNull($charge->tokenData->tokenValue);

        $multiToken = $charge->tokenData;
        $chargeMulti = $chargeService->charge(1, 'usd', $multiToken);
        $this->assertNotNull($chargeMulti->transactionId);
        $this->assertEquals('00', $chargeMulti->responseCode);
    }

    /**
     * @test
     * Testing getting a Visa single use token then charging it and requesting a multiuse token
     */
    public function testGetMultiUseTokenFromVisaAndChargeIt()
    {
        $token = $this->getToken(TestCreditCard::validVisaCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::ValidMultiUseConfig());
        $charge = $chargeService->charge(1, 'USD', $token, TestCardHolder::ValidCardHolder(), true);
        $this->assertNotNull($charge->transactionId);
        $this->assertNotNull($charge->tokenData->tokenValue);

        $multiToken = $charge->tokenData;
        $chargeMulti = $chargeService->charge(1, 'usd', $multiToken);
        $this->assertNotNull($chargeMulti->transactionId);
        $this->assertEquals('00', $chargeMulti->responseCode);
    }

    // Authorize Testing with a multi use token
    /**
     * @test
     * /// Amex authorize should return response code '00' and return multiuse token
     */
    public function testAmexTokenAuthorizeShouldReturnOkandMultiuse()
    {
        $token = $this->getToken(TestCreditCard::validAmexCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::ValidMultiUseConfig());
        $auth = $chargeService->authorize(50, "usd", $token, TestCardHolder::ValidCardHolder(), true);
        $this->assertEquals("00", $auth->responseCode);
        $this->assertNotNull($auth->tokenData->tokenValue);

        $multiToken = $auth->tokenData;
        $chargeMulti = $chargeService->authorize(1, 'usd', $multiToken);
        $this->assertNotNull($chargeMulti->transactionId);
        $this->assertEquals('00', $chargeMulti->responseCode);
    }

    /**
     * @test
     * /// Discover authorize should return response code '00' and return multiuse token
     */
    public function testDiscoverTokenAuthorizeShouldReturnOkandMultiuse()
    {
        $token = $this->getToken(TestCreditCard::validDiscoverCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::ValidMultiUseConfig());
        $auth = $chargeService->authorize(50, "usd", $token, TestCardHolder::ValidCardHolder(), true);
        $this->assertEquals("00", $auth->responseCode);
        $this->assertNotNull($auth->tokenData->tokenValue);

        $multiToken = $auth->tokenData;
        $chargeMulti = $chargeService->authorize(1, 'usd', $multiToken);
        $this->assertNotNull($chargeMulti->transactionId);
        $this->assertEquals('00', $chargeMulti->responseCode);
    }

    /**
     * @test
     * /// MasterCard authorize should return response code '00' and return multiuse token
     */
    public function testMasterCardTokenAuthorizeShouldReturnOkandMultiuse()
    {
        $token = $this->getToken(TestCreditCard::validMasterCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::ValidMultiUseConfig());
        $auth = $chargeService->authorize(50, "usd", $token, TestCardHolder::ValidCardHolder(), true);
        $this->assertEquals("00", $auth->responseCode);
        $this->assertNotNull($auth->tokenData->tokenValue);

        $multiToken = $auth->tokenData;
        $chargeMulti = $chargeService->authorize(1, 'usd', $multiToken);
        $this->assertNotNull($chargeMulti->transactionId);
        $this->assertEquals('00', $chargeMulti->responseCode);
    }

    /**
     * @test
     * /// Visa authorize should return response code '00' and return multiuse token
     */
    public function testVisaTokenAuthorizeShouldReturnOkandMultiuse()
    {
        $token = $this->getToken(TestCreditCard::validVisaCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::ValidMultiUseConfig());
        $auth = $chargeService->authorize(50, "usd", $token, TestCardHolder::ValidCardHolder(), true);
        $this->assertEquals("00", $auth->responseCode);
        $this->assertNotNull($auth->tokenData->tokenValue);

        $multiToken = $auth->tokenData;
        $chargeMulti = $chargeService->authorize(1, 'usd', $multiToken);
        $this->assertNotNull($chargeMulti->transactionId);
        $this->assertEquals('00', $chargeMulti->responseCode);
    }

    // Verify Testing with multi use token

    /**
     * @test
     * Testing getting a single use token then running a verify with amex and requesting a multiuse token
     */
    public function testGetTokenFromAmexVerifyGetMultiTokenTestAndOnError()
    {
        $token = $this->getToken(TestCreditCard::validAmexCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::ValidMultiUseConfig());
        $response = $chargeService->verify($token, TestCardHolder::ValidCardHolder(), true);
        $this->assertEquals('00', $response->responseCode);
        $this->assertNotNull($response->tokenData->tokenValue);

        $multiToken = $response->tokenData;
        $verifyMultiToken = $chargeService->verify($multiToken, TestCardHolder::ValidCardHolder());
        $this->assertEquals('00', $verifyMultiToken->responseCode);
    }

    /**
     * @test
     * Testing getting a single use token then running a verify with visa and requesting a multiuse token
     */
    public function testGetTokenFromDiscoverVerifyGetMultiTokenTestAndOnError()
    {
        $token = $this->getToken(TestCreditCard::validDiscoverCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::ValidMultiUseConfig());
        $response = $chargeService->verify($token, TestCardHolder::ValidCardHolder(), true);
        $this->assertEquals('85', $response->responseCode);
        $this->assertNotNull($response->tokenData->tokenValue);

        $multiToken = $response->tokenData;
        $verifyMultiToken = $chargeService->verify($multiToken);
        $this->assertEquals('85', $verifyMultiToken->responseCode);
    }

    /**
     * @test
     * Testing getting a single use token then running a verify with visa and requesting a multiuse token
     */
    public function testGetTokenFromMasterCardVerifyGetMultiTokenTestAndOnError()
    {
        $token = $this->getToken(TestCreditCard::validMasterCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::ValidMultiUseConfig());
        $response = $chargeService->verify($token, TestCardHolder::ValidCardHolder(), true);
        $this->assertEquals('85', $response->responseCode);
        $this->assertNotNull($response->tokenData->tokenValue);

        $multiToken = $response->tokenData;
        $verifyMultiToken = $chargeService->verify($multiToken);
        $this->assertEquals('85', $verifyMultiToken->responseCode);
    }

    /**
     * @test
     * Testing getting a single use token then running a verify with visa and requesting a multiuse token
     */
    public function testGetTokenFromVisaVerifyGetMultiTokenTestAndOnError()
    {
        $token = $this->getToken(TestCreditCard::validVisaCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::ValidMultiUseConfig());
        $response = $chargeService->verify($token, TestCardHolder::ValidCardHolder(), true);
        $this->assertEquals('85', $response->responseCode);
        $this->assertNotNull($response->tokenData->tokenValue);

        $multiToken = $response->tokenData;
        $verifyMultiToken = $chargeService->verify($multiToken);
        $this->assertEquals('85', $verifyMultiToken->responseCode);
    }

    // Refund Multi Token Tests

    /**
     * @test
     * /// Amex multi Token refund test with token
     */
    public function testAmexMultiTokenReturnShouldBeOk()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->getToken(TestCreditCard::validAmexCreditCard());
        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $charge = $chargeSvc->charge(50, 'usd', $token, TestCardHolder::ValidCardHolder(), true);
        $this->assertEquals($charge->responseCode, "0");
        $this->assertNotNull($charge->tokenData->tokenValue);

        $multiToken = $charge->tokenData;
        $refundMultiToken = $chargeSvc->refund(50, "usd", $multiToken);
        $this->assertEquals('00', $refundMultiToken->responseCode);
    }

    /**
     * @test
     * /// Discover multi Token refund test with token
     */
    public function testDiscoverMultiTokenReturnShouldBeOk()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->getToken(TestCreditCard::validDiscoverCreditCard());
        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $charge = $chargeSvc->charge(50, 'usd', $token, TestCardHolder::ValidCardHolder(), true);
        $this->assertEquals($charge->responseCode, "0");
        $this->assertNotNull($charge->tokenData->tokenValue);

        $multiToken = $charge->tokenData;
        $refundMultiToken = $chargeSvc->refund(50, "usd", $multiToken, TestCardHolder::certCardHolderShortZip());
        $this->assertEquals('00', $refundMultiToken->responseCode);
    }

    /**
     * @test
     * /// MasterCard multi Token refund test with token
     */
    public function testMasterCardMultiTokenReturnShouldBeOk()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->getToken(TestCreditCard::validMasterCreditCard());
        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $charge = $chargeSvc->charge(50, 'usd', $token, TestCardHolder::ValidCardHolder(), true);
        $this->assertEquals($charge->responseCode, "0");
        $this->assertNotNull($charge->tokenData->tokenValue);

        $multiToken = $charge->tokenData;
        $refundMultiToken = $chargeSvc->refund(50, "usd", $multiToken, TestCardHolder::certCardHolderShortZip());
        $this->assertEquals('00', $refundMultiToken->responseCode);
    }

    /**
     * @test
     * /// Visa multi Token refund test with token
     */
    public function testVisaMultiTokenReturnShouldBeOk()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->getToken(TestCreditCard::validVisaCreditCard());
        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $charge = $chargeSvc->charge(50, 'usd', $token, TestCardHolder::ValidCardHolder(), true);
        $this->assertEquals($charge->responseCode, "0");
        $this->assertNotNull($charge->tokenData->tokenValue);

        $multiToken = $charge->tokenData;
        $refundMultiToken = $chargeSvc->refund(50, "usd", $multiToken, TestCardHolder::certCardHolderShortZip());
        $this->assertEquals('00', $refundMultiToken->responseCode);
    }

    // Reverse Multi Token Tests

    /**
     * @test
     * /// Amex Reverse multi Token tests
     */
    public function testAmexMulitTokenReverseShouldBeOk()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->getToken(TestCreditCard::validAmexCreditCard());
        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $chargeResponse =$chargeSvc->charge(17.01, 'usd', $token, null, true);
        $this->assertEquals($chargeResponse->responseCode, "0");

        $muToken = $chargeResponse->tokenData;
        $response = $chargeSvc->reverse($muToken, 17.01, "usd");
        $this->assertEquals($response->responseCode, "0");
    }

    /**
     * @test
     * /// Discover Reverse multi Token tests
     */
    public function testDiscoverMultiTokenReverseShouldBeOk()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->getToken(TestCreditCard::validDiscoverCreditCard());
        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $chargeResponse =$chargeSvc->charge(17.01, 'usd', $token, null, true);
        $this->assertEquals($chargeResponse->responseCode, "0");

        $muToken = $chargeResponse->tokenData;
        $response = $chargeSvc->reverse($muToken, 17.01, "usd");
        $this->assertEquals($response->responseCode, "0");
    }

    /**
     * @test
     * /// MasterCard Reverse multi Token tests
     */
    public function testMasterCardMultiTokenReverseShouldBeOk()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->getToken(TestCreditCard::validMasterCreditCard());
        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $chargeResponse =$chargeSvc->charge(17.01, 'usd', $token, null, true);
        $this->assertEquals($chargeResponse->responseCode, "0");

        $muToken = $chargeResponse->tokenData;
        $response = $chargeSvc->reverse($muToken, 17.01, "usd");
        $this->assertEquals($response->responseCode, "0");
    }

    /**
     * @test
     * /// Visa Reverse multi Token tests
     */
    public function testVisaMultiTokenReverseShouldBeOk()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->getToken(TestCreditCard::validVisaCreditCard());
        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $chargeResponse =$chargeSvc->charge(17.01, 'usd', $token, null, true);
        $this->assertEquals($chargeResponse->responseCode, "0");

        $muToken = $chargeResponse->tokenData;
        $response = $chargeSvc->reverse($muToken, 17.01, "usd");
        $this->assertEquals($response->responseCode, "0");
    }
}

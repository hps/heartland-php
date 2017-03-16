<?php

/**
 * Class GatewayTokenSingleUseTest
 */
class GatewayTokenSingleUseTest extends PHPUnit_Framework_TestCase
{
    public $tokenService = null;
    public $publicKey    = null;
    /**
     * @param      $card
     * @param null $key
     *
     * @return \HpsTokenData|mixed
     */
    private function getToken($card, $key = null)
    {
        if ($key != null and $key != "") {
            $this->publicKey = $key;
        } else {
            $this->publicKey = TestServicesConfig::validMultiUsePublicKey();
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

    // Basic single token fetching tests

    /**
     * @test
     * This test will return a valid token
     */
    public function testShouldReturnValidToken()
    {
        $token = $this->getToken(TestCreditCard::validVisaCreditCard());
        $this->assertTrue(!empty($token->tokenValue));
        $this->assertStringStartsWith('supt', $token->tokenValue);
    }

    /**
     * @test
     * @expectedException        HpsAuthenticationException
     * @expectedExceptionCode    HpsExceptionCodes::INVALID_CONFIGURATION
     * @expectedExceptionMessage The HPS SDK requires a valid public API key to be used
     */
    public function testShouldThrowBadPublicKeyError()
    {
        $this->getToken(TestCreditCard::validVisaCreditCard(), 'BADKEY');
    }

    /**
     * @test
     * @expectedException        HpsAuthenticationException
     * @expectedExceptionCode    HpsExceptionCodes::INVALID_CONFIGURATION
     * @expectedExceptionMessage The HPS SDK requires a valid public API key to be used
     */
    public function testShouldThrowBadPublic()
    {
        $token = $this->getToken(TestCreditCard::validVisaCreditCard(), 'STILLBADKEY');
        $this->assertTrue(empty($token));
    }

    /**
     * @test
     * @expectedException        HpsException
     * @expectedExceptionCode    2
     * @expectedExceptionMessage Card number is invalid
     */
    public function testValidationInvalidCardNumberShouldReturnError()
    {
        $card = TestCreditCard::validVisaCreditCard();
        $card->number = "11111111111111111111111111111111111";
        $this->getToken($card);
    }

    /**
     * @test
     * @expectedException        HpsException
     * @expectedExceptionCode    2
     * @expectedExceptionMessage Card expiration month is invalid.
     */
    public function testValidationTooHighExpirationMonthShouldReturnError()
    {
        $card = TestCreditCard::validVisaCreditCard();
        $card->expMonth = 13;
        $this->getToken($card);
    }

    /**
     * @test
     * @expectedException        HpsException
     * @expectedExceptionCode    2
     * @expectedExceptionMessage Card expiration year is invalid.
     */
    public function testValidationTooLowExpirationYearShouldReturnError()
    {
        $card = TestCreditCard::validVisaCreditCard();
        $card->expYear = 12;
        $this->getToken($card);
    }

    // Charge Testing with a token
    /**
     * @test
     * Testing getting an Amex single use token then charging it
     */
    public function testGetTokenFromAmexAndChargeIt()
    {
        $token = $this->getToken(TestCreditCard::validAmexCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $charge = $chargeService->charge(1, 'USD', $token, TestCardHolder::validCardHolder());
        $this->assertNotNull($charge->transactionId);
        $this->assertEquals('00', $charge->responseCode);
    }
    // Charge Testing with a token
    /**
     * @test
     * Testing getting an Amex single use token then charging it
     */
    public function testGetTokenFromAmexAndChargeTokenValue()
    {
        $token = $this->getToken(TestCreditCard::validAmexCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $charge = $chargeService->charge(1, 'USD', $token->tokenValue, TestCardHolder::validCardHolder());
        $this->assertNotNull($charge->transactionId);
        $this->assertEquals('00', $charge->responseCode);
    }

    /**
     * @test
     * Testing getting a Discover Card single use token then charging it
     */
    public function testGetTokenFromDiscoverAndChargeIt()
    {
        $token = $this->getToken(TestCreditCard::validDiscoverCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $charge = $chargeService->charge(1, 'USD', $token, TestCardHolder::validCardHolder());
        $this->assertNotNull($charge->transactionId);
        $this->assertEquals('00', $charge->responseCode);
    }
    /**
     * @test
     * Testing getting a Discover Card single use token then charging it
     */
    public function testGetTokenFromDiscoverAndChargeTokenValue()
    {
        $token = $this->getToken(TestCreditCard::validDiscoverCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $charge = $chargeService->charge(1, 'USD', $token->tokenValue, TestCardHolder::validCardHolder());
        $this->assertNotNull($charge->transactionId);
        $this->assertEquals('00', $charge->responseCode);
    }

    /**
     * @test
     * Testing getting a Master Card single use token then charging it
     */
    public function testGetTokenFromMasterCardAndChargeIt()
    {
        $token = $this->getToken(TestCreditCard::validMasterCardCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $charge = $chargeService->charge(1, 'USD', $token, TestCardHolder::validCardHolder());
        $this->assertNotNull($charge->transactionId);
        $this->assertEquals('00', $charge->responseCode);
    }
    /**
     * @test
     * Testing getting a Master Card single use token then charging it
     */
    public function testGetTokenFromMasterCardAndChargeTokenValue()
    {
        $token = $this->getToken(TestCreditCard::validMasterCardCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $charge = $chargeService->charge(1, 'USD', $token->tokenValue, TestCardHolder::validCardHolder());
        $this->assertNotNull($charge->transactionId);
        $this->assertEquals('00', $charge->responseCode);
    }

    /**
     * @test
     * Testing getting a Visa single use token then charging it
     */
    public function testGetTokenFromVisaAndChargeIt()
    {
        $token = $this->getToken(TestCreditCard::validVisaCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $charge = $chargeService->charge(1, 'USD', $token, TestCardHolder::validCardHolder());
        $this->assertNotNull($charge->transactionId);
        $this->assertEquals('00', $charge->responseCode);
    }
    /**
     * @test
     * Testing getting a Visa single use token then charging it
     */
    public function testGetTokenFromVisaAndChargeTokenValue()
    {
        $token = $this->getToken(TestCreditCard::validVisaCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $charge = $chargeService->charge(1, 'USD', $token->tokenValue, TestCardHolder::validCardHolder());
        $this->assertNotNull($charge->transactionId);
        $this->assertEquals('00', $charge->responseCode);
    }

    // Authorize Token Tests

    /**
     * @test
     * /// Amex authorize should return response code '00'.
     */
    public function testAmexTokenAuthorizeShouldReturnOk()
    {
        $token = $this->getToken(TestCreditCard::validAmexCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeService->authorize(50, "usd", $token, TestCardHolder::validCardHolder());
        $this->assertEquals("00", $response->responseCode);
    }
    /**
     * @test
     * /// Visa authorize should return response code '00'.
     */
    public function testDiscoverTokenAuthorizeShouldReturnOk()
    {
        $token = $this->getToken(TestCreditCard::validDiscoverCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeService->authorize(50, "usd", $token, TestCardHolder::validCardHolder());
        $this->assertEquals("00", $response->responseCode);
    }

    /**
     * @test
     * /// Visa authorize should return response code '00'.
     */
    public function testMasterCardTokenAuthorizeShouldReturnOk()
    {
        $token = $this->getToken(TestCreditCard::validMasterCardCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeService->authorize(50, "usd", $token, TestCardHolder::validCardHolder());
        $this->assertEquals("00", $response->responseCode);
    }

    /**
     * @test
     * /// Visa authorize should return response code '00'.
     */
    public function testVisaTokenAuthorizeShouldReturnOk()
    {
        $token = $this->getToken(TestCreditCard::validVisaCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeService->authorize(50, "usd", $token, TestCardHolder::validCardHolder());
        $this->assertEquals("00", $response->responseCode);
    }

    // Testing Verify with a token

    /**
     * @test
     * Testing getting a single use token then running a verify with Amex
     */
    public function testIntegrationWhenTokenIsAcquiredShouldBeAbleToVerifyAmex()
    {
        $token = $this->getToken(TestCreditCard::validAmexCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeService->verify($token, TestCardHolder::validCardHolder());
        $this->assertEquals($response->responseCode, "00");
    }

    /**
     * @test
     * Testing getting a single use token then running a verify with Discover
     */
    public function testIntegrationWhenTokenIsAcquiredShouldBeAbleToVerifyDiscover()
    {
        $token = $this->getToken(TestCreditCard::validDiscoverCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeService->verify($token, TestCardHolder::validCardHolder());
        $this->assertEquals($response->responseCode, "85");
    }

    /**
     * @test
     * Testing getting a single use token then running a verify with Master Card
     */
    public function testIntegrationWhenTokenIsAcquiredShouldBeAbleToVerifyMaster()
    {
        $token = $this->getToken(TestCreditCard::validMasterCardCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeService->verify($token, TestCardHolder::validCardHolder());
        $this->assertEquals($response->responseCode, "85");
    }
    /**
     * @test
     * Testing getting a single use token then running a verify with visa
     */
    public function testIntegrationWhenTokenIsAcquiredShouldBeAbleToVerifyVisa()
    {
        $token = $this->getToken(TestCreditCard::validVisaCreditCard());
        $chargeService = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeService->verify($token, TestCardHolder::validCardHolder());
        $this->assertEquals($response->responseCode, "85");
    }


    // Refund Token Tests

    /**
     * @test
     * /// Amex refund test with token
     */
    public function testAmexTokenReturnShouldBeOk()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->getToken(TestCreditCard::validAmexCreditCard());
        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->refund(15.15, "usd", $token, TestCardHolder::certCardHolderShortZip());
        $this->assertEquals($response->responseCode, "0");
    }

    /**
     * @test
     * /// Mastercard return test with token
     */
    public function testMastercardTokenReturnShouldBeOk()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->getToken(TestCreditCard::validMasterCardCreditCard());
        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->refund(15.15, "usd", $token, TestCardHolder::certCardHolderShortZip());
        $this->assertEquals($response->responseCode, "0");
    }

    /**
     * @test
     * /// Discover return test with token
     */
    public function testDiscoverTokenReturnShouldBeOk()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->getToken(TestCreditCard::validDiscoverCreditCard());
        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->refund(15.15, "usd", $token, TestCardHolder::certCardHolderShortZip());
        $this->assertEquals($response->responseCode, "0");
    }

    /**
     * @test
     * /// Visa refund test with token
     */
    public function testVisaTokenRefundShouldBeOk()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->getToken(TestCreditCard::validVisaCreditCard());
        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->refund(15.15, "usd", $token, TestCardHolder::certCardHolderShortZip());
        $this->assertEquals($response->responseCode, "0");
    }

    //Reverse Token Tests

    /**
     * @test
     * /// Amex Token refund test with token
     */
    public function testAmexTokenReverseShouldBeOk()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->getToken(TestCreditCard::validAmexCreditCard());
        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $reverse = $chargeSvc->reverse($token, 50, 'usd');

        $this->assertEquals('00', $reverse->responseCode);
    }

    /**
     * @test
     * /// Discover Token refund test with token
     */
    public function testDiscoverTokenReverseShouldBeOk()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->getToken(TestCreditCard::validDiscoverCreditCard());
        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $reverse = $chargeSvc->reverse($token, 50, 'usd');

        $this->assertEquals('00', $reverse->responseCode);
    }

    /**
     * @test
     * /// MasterCard Token refund test with token
     */
    public function testMasterCardTokenReverseShouldBeOk()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->getToken(TestCreditCard::validMasterCardCreditCard());
        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $reverse = $chargeSvc->reverse($token, 50, 'usd');

        $this->assertEquals('00', $reverse->responseCode);
    }

    /**
     * @test
     * /// Visa Token refund test with token
     */
    public function testVisaTokenReverseShouldBeOk()
    {
        $testConfig = new TestServicesConfig();

        $token = $this->getToken(TestCreditCard::validVisaCreditCard());
        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $reverse = $chargeSvc->reverse($token, 50, 'usd');

        $this->assertEquals('00', $reverse->responseCode);
    }
}

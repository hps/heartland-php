<?php

class CreditCertificationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
    */
    public function testCertShouldRunOk()
    {
        $this->testBatchShouldCloseOk();
        $this->testVisaShouldChargeOk();
        $this->testMasterCardShouldChargeOk();
        $this->testDiscoverShouldChargeOk();
        $this->testAmexShouldChargeOk();
        $this->testJcbShouldChargeOk();
        $this->testVisaShouldVerifyOk();
        $this->testMasterCardShouldVerifyOk();
        $this->testDiscoverShouldVerifyOk();
        $this->testAmexAvsShouldBeOk();
        $this->testMastercardReturnShouldBeOk();
        $this->testVisaShouldReverseOk();
        $this->testBatchShouldCloseOk();
    }

    /**
     * @test
     * /// <summary>Batch close cert test.</summary>
     */
    public function testBatchShouldCloseOk()
    {
        try {
            /*
            $batchSvc = new HpsBatchService(TestServicesConfig::validMultiUseConfig());
            $response = batchSvc.CloseBatch();
             */
            $batchSvc = new HpsBatchService(TestServicesConfig::validMultiUseConfig());
            $response = $batchSvc->closeBatch();

            if ($response == null) {
                $this->fail("Response is null.");
            }
        } catch (HpsException $e) {
            if ($e->code != HpsExceptionCodes::NO_OPEN_BATCH) {
                $this->fail("Something failed other than 'no open batch'.");
            }
        }
    }

    /**
     * @test
     * /// <summary>VISA charge cert test.</summary>
    */
    public function testVisaShouldChargeOk()
    {
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->charge(17.01, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::certCardHolderShortZip());
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    /**
     * @test
     * /// <summary>MasterCard charge cert test.</summary>
    */
    public function testMasterCardShouldChargeOk()
    {
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->charge(17.02, "usd", TestCreditCard::validMasterCardCreditCard(), TestCardHolder::certCardHolderShortZipNoStreet());
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    /**
     * @test
     * /// <summary>Discover charge cert test.</summary>
    */
    public function testDiscoverShouldChargeOk()
    {
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->charge(17.03, "usd", TestCreditCard::validDiscoverCreditCard(), TestCardHolder::certCardHolderLongZipNoStreet());
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    /**
     * @test
     * /// <summary>Amex charge cert test.</summary>
    */
    public function testAmexShouldChargeOk()
    {
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->charge(17.04, "usd", TestCreditCard::validAmexCreditCard(), TestCardHolder::certCardHolderShortZip());
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    /**
     * @test
     * /// <summary>JCB charge cert test.</summary>
    */
    public function testJcbShouldChargeOk()
    {
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->charge(17.05, "usd", TestCreditCard::validJCBCreditCard(), TestCardHolder::certCardHolderLongZip());
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    /**
     * @test
     * /// <summary>VISA verify cert test.</summary>
    */
    public function testVisaShouldVerifyOk()
    {
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->verify(TestCreditCard::validVisaCreditCard());
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "85");
    }

    /**
     * @test
     * /// <summary>MasterCard verify cert test.</summary>
    */
    public function testMasterCardShouldVerifyOk()
    {
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->verify(TestCreditCard::validMasterCardCreditCard());
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "85");
    }

    /**
     * @test
     * /// <summary>Discover verify cert test.</summary>
    */
    public function testDiscoverShouldVerifyOk()
    {
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->verify(TestCreditCard::validDiscoverCreditCard());
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "85");
    }

    /**
     * @test
     * /// <summary>Amex AVS cert test.</summary>
    */
    public function testAmexAvsShouldBeOk()
    {
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->verify(TestCreditCard::validAmexCreditCard(), TestCardHolder::certCardHolderShortZipNoStreet());
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    /**
     * @test
     * /// <summary>Mastercard return test.</summary>
    */
    public function testMastercardReturnShouldBeOk()
    {
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->refund(15.15, "usd", TestCreditCard::validMasterCardCreditCard(), TestCardHolder::certCardHolderShortZip());
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "0");
    }

    /**
     * @test
     * /// <summary>VISA verify cert test.</summary>
    */
    public function testVisaShouldReverseOk()
    {
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->reverse(TestCreditCard::validVisaCreditCard(), 17.01, "usd");
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    // Authorize Tests
    /**
     * @test
     * /// Visa authorize and Capture should return response code '00'.
     */
    public function testVisaAuthorizeAndCaptureShouldReturnOk()
    {
        $chargeService = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $auth = $chargeService->authorize(17.06, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("00", $auth->responseCode);

        $capture = $chargeService->capture($auth->transactionId, 17.06);
        $this->assertEquals("0", $capture->responseCode);
    }

    /**
     * @test
     * /// MasterCard authorize and capture should return response code '00'.
     */
    public function testMasterCardAuthorizeAndCaptureShouldReturnOk()
    {
        $chargeService = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $auth = $chargeService->authorize(17.07, "usd", TestCreditCard::validMasterCardCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("00", $auth->responseCode);

        $capture = $chargeService->capture($auth->transactionId, 17.07);
        $this->assertEquals("0", $capture->responseCode);
    }

    /**
     * @test
     * /// Discover authorize and capture should return response code '00'.
     */
    public function testDiscoverAuthorizeAndCaptureShouldReturnOk()
    {
        $chargeService = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $auth = $chargeService->authorize(17.08, "usd", TestCreditCard::validDiscoverCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("00", $auth->responseCode);

        $capture = $chargeService->capture($auth->transactionId, 17.08);
        $this->assertEquals("0", $capture->responseCode);
    }
}

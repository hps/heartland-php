<?php
require_once(dirname(__FILE__).'/../setup.php');

class CreditCertificationTests extends PHPUnit_Framework_TestCase
{
    /**
     * @test
    */
    public function Cert_ShouldRun_Ok()
    {
        $this->Batch_ShouldClose_Ok();
        $this->Visa_ShouldCharge_Ok();
        $this->MasterCard_ShouldCharge_Ok();
        $this->Discover_ShouldCharge_Ok();
        $this->Amex_ShouldCharge_Ok();
        $this->Jcb_ShouldCharge_Ok();
        $this->Visa_ShouldVerify_Ok();
        $this->MasterCard_ShouldVerify_Ok();
        $this->Discover_ShouldVerify_Ok();
        $this->Amex_Avs_ShouldBe_Ok();
        $this->Mastercard_Return_ShouldBe_Ok();
        $this->Visa_ShouldReverse_Ok();
        $this->Batch_ShouldClose_Ok();
    }

    /**
     * @test
    /// <summary>Batch close cert test.</summary>
    */
    public function Batch_ShouldClose_Ok()
    {
        $testConfig = new TestServicesConfig();

        try
        {
            /*
            $batchSvc = new HpsBatchService($testConfig->ValidMultiUseConfig());
            $response = batchSvc.CloseBatch();
             */
            $batchSvc = new HpsBatchService($testConfig->ValidMultiUseConfig());
            $response = $batchSvc->closeBatch();

            if ($response == null)
            {
                $this->fail("Response is null.");
            }
        }
        catch (HpsException $e)
        {
            if ($e->Code() != 5)
            {
                $this->fail("Something failed other than 'no open batch'.");
            }
        }
    }

    /**
     * @test
    /// <summary>VISA charge cert test.</summary>
    */
    public function Visa_ShouldCharge_Ok()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->charge(17.01, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::certCardHolderShortZip());
        if ($response == null)
        {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    /**
     * @test
    /// <summary>MasterCard charge cert test.</summary>
    */
    public function MasterCard_ShouldCharge_Ok()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->charge(17.02, "usd", TestCreditCard::validMasterCreditCard(), TestCardHolder::certCardHolderShortZipNoStreet());
        if ($response == null)
        {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    /**
     * @test
    /// <summary>Discover charge cert test.</summary>
    */
    public function Discover_ShouldCharge_Ok()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->charge(17.03, "usd", TestCreditCard::validDiscoverCreditCard(), TestCardHolder::certCardHolderLongZipNoStreet());
        if ($response == null)
        {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    /**
     * @test
    /// <summary>Amex charge cert test.</summary>
    */
    public function Amex_ShouldCharge_Ok()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->charge(17.04, "usd", TestCreditCard::validAmexCreditCard(), TestCardHolder::certCardHolderShortZip());
        if ($response == null)
        {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    /**
     * @test
    /// <summary>JCB charge cert test.</summary>
    */
    public function Jcb_ShouldCharge_Ok()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->charge(17.05, "usd", TestCreditCard::validJBCCreditCard(), TestCardHolder::certCardHolderLongZip());
        if ($response == null)
        {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    /**
     * @test
    /// <summary>VISA verify cert test.</summary>
    */
    public function Visa_ShouldVerify_Ok()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->verify(TestCreditCard::validVisaCreditCard());
        if ($response == null)
        {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "85");
    }

    /**
     * @test
    /// <summary>MasterCard verify cert test.</summary>
    */
    public function MasterCard_ShouldVerify_Ok()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->verify(TestCreditCard::validMasterCreditCard());
        if ($response == null)
        {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "85");
    }

    /**
     * @test
    /// <summary>Discover verify cert test.</summary>
    */
    public function Discover_ShouldVerify_Ok()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->verify(TestCreditCard::validDiscoverCreditCard());
        if ($response == null)
        {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "85");
    }

    /**
     * @test
    /// <summary>Amex AVS cert test.</summary>
    */
    public function Amex_Avs_ShouldBe_Ok()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->verify(TestCreditCard::validAmexCreditCard(), TestCardHolder::certCardHolderShortZipNoStreet());
        if ($response == null)
        {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    /**
     * @test
    /// <summary>Mastercard return test.</summary>
    */
    public function Mastercard_Return_ShouldBe_Ok()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->refund(15.15, "usd", TestCreditCard::validMasterCreditCard(), TestCardHolder::certCardHolderShortZip());
        if ($response == null)
        {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "0");
    }

    /**
     * @test
    /// <summary>VISA verify cert test.</summary>
    */
    public function Visa_ShouldReverse_Ok()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->reverse(TestCreditCard::validVisaCreditCard(), 17.01, "usd");
        if ($response == null)
        {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    // Authorize Tests
    /**
     * @test
    /// Visa authorize and Capture should return response code '00'.
     */
    public function Visa_AuthorizeAndCapture_ShouldReturnOk()
    {
        $chargeService = new HpsCreditService(TestServicesConfig::ValidMultiUseConfig());
        $auth = $chargeService->authorize(17.06, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("00", $auth->responseCode);

        $capture = $chargeService->capture($auth->transactionId,17.06);
        $this->assertEquals("0",$capture->responseCode);

        $getCapture = $chargeService->get($capture->transactionId);
        $this->assertEquals("0",$getCapture->responseCode);
    }

    /**
     * @test
    /// MasterCard authorize and capture should return response code '00'.
     */
    public function MasterCard_AuthorizeAndCapture_ShouldReturnOk()
    {
        $chargeService = new HpsCreditService(TestServicesConfig::ValidMultiUseConfig());
        $auth = $chargeService->authorize(17.07, "usd", TestCreditCard::validMasterCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("00", $auth->responseCode);

        $capture = $chargeService->capture($auth->transactionId,17.07);
        $this->assertEquals("0",$capture->responseCode);

        $getCapture = $chargeService->get($capture->transactionId);
        $this->assertEquals("0",$getCapture->responseCode);
    }

    /**
     * @test
    /// Discover authorize and capture should return response code '00'.
     */
    public function Discover_AuthorizeAndCapture_ShouldReturnOk()
    {
        $chargeService = new HpsCreditService(TestServicesConfig::ValidMultiUseConfig());
        $auth = $chargeService->authorize(17.08, "usd", TestCreditCard::validDiscoverCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("00", $auth->responseCode);

        $capture = $chargeService->capture($auth->transactionId,17.08);
        $this->assertEquals("0",$capture->responseCode);

        $getCapture = $chargeService->get($capture->transactionId);
        $this->assertEquals("0",$getCapture->responseCode);
    }

}

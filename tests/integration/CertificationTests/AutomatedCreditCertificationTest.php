<?php

/**
 * Class AutomatedCreditCertificationTest
 */
class AutomatedCreditCertificationTest extends PHPUnit_Framework_TestCase
{

    public function testChargeVisaLongZip()
    {
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->charge(10.00, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::certCardHolderLongZip());
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    public function testChargeMasterCardShortZip()
    {
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->charge(11.00, "usd", TestCreditCard::validMasterCardCreditCard(), TestCardHolder::certCardHolderShortZip());
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    public function testChargeDiscoverLongZipStreet()
    {
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->charge(12.00, "usd", TestCreditCard::validDiscoverCreditCard(), TestCardHolder::certCardHolderLongZipStreet());
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    public function testChargeAmexShortZipStreet()
    {
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->charge(13.00, "usd", TestCreditCard::validAmexCreditCard(), TestCardHolder::certCardHolderShortZipStreet());
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    public function testAuthAndCaptureVisaLongZip()
    {
        $amount = '15.00';

        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->authorize($amount, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::certCardHolderLongZip());

        $capture = $chargeSvc->capture($response->transactionId, $amount);
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    public function testAuthAndCaptureMasterCardShortZip()
    {
        $amount = '16.00';

        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->authorize($amount, "usd", TestCreditCard::validMasterCardCreditCard(), TestCardHolder::certCardHolderShortZip());

        $capture = $chargeSvc->capture($response->transactionId, $amount);
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    public function testAuthAndCaptureDiscoverLongZipStreet()
    {
        $amount = '17.00';

        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->authorize($amount, "usd", TestCreditCard::validDiscoverCreditCard(), TestCardHolder::certCardHolderLongZipStreet());

        $capture = $chargeSvc->capture($response->transactionId, $amount);
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    public function testAuthAndCaptureAmexShortZipStreet()
    {
        $amount = '18.00';

        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->authorize($amount, "usd", TestCreditCard::validAmexCreditCard(), TestCardHolder::certCardHolderShortZipStreet());

        $capture = $chargeSvc->capture($response->transactionId, $amount);
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    public function testVerifyVisa()
    {
        $card = TestCreditCard::validVisaCreditCard();
        $card->cvv = null;
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->verify($card);

        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "85");
    }

    public function testVerifyMasterCard()
    {
        $card = TestCreditCard::validMasterCardCreditCard();
        $card->cvv = null;
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->verify($card);

        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "85");
    }

    public function testVerifyDiscover()
    {
        $card = TestCreditCard::validDiscoverCreditCard();
        $card->cvv = null;
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->verify($card);

        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "85");
    }

    public function testCreditReturnVisa()
    {
        $amount = '50.00';

        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->refund($amount, 'usd', TestCreditCard::validVisaCreditCard());

        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    public function testCreditReturnMasterCard()
    {
        $amount = '51.00';

        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->refund($amount, 'usd', TestCreditCard::validMasterCardCreditCard());

        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    public function testCreditReturnDiscover()
    {
        $amount = '52.00';

        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->refund($amount, 'usd', TestCreditCard::validDiscoverCreditCard());

        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    public function testCreditReturnAmex()
    {
        $amount = '53.00';

        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->refund($amount, 'usd', TestCreditCard::validAmexCreditCard());

        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }


    public function testSaleAndReturnVisa()
    {
        $amount = '50.00';

        $card = TestCreditCard::validVisaCreditCard();
        $card->cvv = null;
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->charge($amount, "usd", $card);

        $chargeSvc->refund($amount, 'usd', $response->transactionId);
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    public function testSaleAndReturnMasterCard()
    {
        $amount = '51.00';

        $card = TestCreditCard::validMasterCardCreditCard();
        $card->cvv = null;
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->charge($amount, "usd", $card);

        $chargeSvc->refund($amount, 'usd', $response->transactionId);
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    public function testSaleAndReturnDiscover()
    {
        $amount = '52.00';

        $card = TestCreditCard::validDiscoverCreditCard();
        $card->cvv = null;
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->charge($amount, "usd", $card);

        $chargeSvc->refund($amount, 'usd', $response->transactionId);
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    public function testSaleAndReturnAmex()
    {
        $amount = '53.00';

        $card = TestCreditCard::validAmexCreditCard();
        $card->cvv = null;
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->charge($amount, "usd", $card);

        $chargeSvc->refund($amount, 'usd', $response->transactionId);
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }

    public function testAuthAndReversalVisa()
    {
        $amount = '60.00';
        $card = TestCreditCard::validVisaCreditCard();
        $card->cvv = null;
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->authorize($amount, "usd", $card);

        $chargeSvc->capture($response->transactionId);
        $chargeSvc->reverse($response->transactionId, $amount, 'usd');
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }


    public function testAuthAndReversalMasterCard()
    {
        $amount = '61.00';

        $card = TestCreditCard::validMasterCardCreditCard();
        $card->cvv = null;
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->authorize($amount, "usd", $card);

        $chargeSvc->capture($response->transactionId);
        $chargeSvc->reverse($response->transactionId, $amount, 'usd');
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }


    public function testAuthAndReversalDiscover()
    {
        $amount = '62.00';

        $card = TestCreditCard::validDiscoverCreditCard();
        $card->cvv = null;
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->authorize($amount, "usd", $card);

        $chargeSvc->capture($response->transactionId);
        $chargeSvc->reverse($response->transactionId, $amount, 'usd');
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }


    public function testAuthAndReversalAmex()
    {
        $amount = '63.00';

        $card = TestCreditCard::validAmexCreditCard();
        $card->cvv = null;
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        $response = $chargeSvc->authorize($amount, "usd", $card);

        $chargeSvc->capture($response->transactionId);
        $chargeSvc->reverse($response->transactionId, $amount, 'usd');
        if ($response == null) {
            $this->fail("Response is null.");
        }

        $this->assertEquals($response->responseCode, "00");
    }
}

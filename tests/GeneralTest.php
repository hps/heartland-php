<?php

class GeneralTests extends PHPUnit_Framework_TestCase
{
    /**
     * The less than zero amount test method.
     *
     * @expectedException        HpsInvalidRequestException
     * @expectedExceptionCode    HpsExceptionCodes::INVALID_AMOUNT
     * @expectedExceptionMessage Must be greater than or equal to 0.
     */
    public function testChargeWhenAmountIsLessThanZeroShouldThrowArgumentOutOfRange()
    {
        $ChargeAmount = -5;
        $chargeSvc = new HpsCreditService();

        $chargeSvc->Charge($ChargeAmount, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::validCardHolder());
    }

    /**
     * The empty currency test method.
     *
     * @expectedException        HpsInvalidRequestException
     * @expectedExceptionCode    HpsExceptionCodes::MISSING_CURRENCY
     * @expectedExceptionMessage Currency cannot be none
     */
    public function testChargeWhenCurrencyIsEmptyShouldThrowArgumentNull()
    {
        $ChargeAmount = 50;
        $Currency = "";
        $chargeSvc = new HpsCreditService();

        $chargeSvc->Charge($ChargeAmount, $Currency, TestCreditCard::validVisaCreditCard(), TestCardHolder::validCardHolder());
    }

    /**
     * The invalid currency test method.
     *
     * @expectedException        HpsInvalidRequestException
     * @expectedExceptionCode    HpsExceptionCodes::INVALID_CURRENCY
     * @expectedExceptionMessage 'eur' is not a supported currency
     */
    public function testChargeWhenCurrencyIsNotUsdShouldThrowArgumentException()
    {
        $ChargeAmount = 50;
        $Currency = "eur";
        $chargeSvc = new HpsCreditService();

        $chargeSvc->Charge($ChargeAmount, $Currency, TestCreditCard::validVisaCreditCard(), TestCardHolder::validCardHolder());
    }

    /**
     * The invalid HPS config test method.
     *
     * @expectedException        HpsAuthenticationException
     * @expectedExceptionMessage Authentication Error. Please double check your service configuration
     */
    public function testChargeWhenHpsConfigIsInvalidShouldThrowAuthenticationException()
    {
        $testConfig = new TestServicesConfig();

        $ChargeAmount = 50;
        $Currency = "usd";
        $chargeSvc = new HpsCreditService($testConfig->BadLicenseId());

        $chargeSvc->Charge($ChargeAmount, $Currency, TestCreditCard::validVisaCreditCard(), TestCardHolder::validCardHolder());
    }

    /**
     * The invalid HPS licenseId test method.
     *
     * @expectedException        HpsAuthenticationException
     * @expectedExceptionMessage Authentication Error. Please double check your
     * service configuration
     */
    public function testChargeWhenHpsLicenseIdIsInvalidShouldThrowHpsException()
    {
        $testConfig = new TestServicesConfig();

        $ChargeAmount = 50;
        $Currency = "usd";
        $chargeSvc = new HpsCreditService($testConfig->BadLicenseId());

        $chargeSvc->Charge($ChargeAmount, $Currency, TestCreditCard::validVisaCreditCard(), TestCardHolder::validCardHolder());
    }

    /**
     * The invalid HPS config test method.
     *
     * @expectedException        HpsGatewayException
     * @expectedExceptionCode    HpsExceptionCodes::INVALID_NUMBER
     * @expectedExceptionMessage The card number is not valid
     */
    public function testChargeWhenCardNumberIsInvalidShouldThrowHpsException()
    {
        $testConfig = new TestServicesConfig();

        $ChargeAmount = 50;
        $Currency = "usd";
        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());

        $chargeSvc->Charge($ChargeAmount, $Currency, TestCreditCard::invalidCreditCard(), TestCardHolder::validCardHolder());
    }

    /**
     * The list transactions test method.
     */
    public function testListWhenConfigValidShouldListTransactions()
    {
        date_default_timezone_set("UTC");
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());

        $dateFormat = 'Y-m-d\TH:i:s.00\Z';
        $dateMinus10 = new DateTime();
        $dateMinus10->sub(new DateInterval('P1D'));
        $current = new DateTime();

        $items = $chargeSvc->listTransactions($dateMinus10->format($dateFormat), $current->format($dateFormat));
        $this->assertNotNull($items);
        $this->assertGreaterThan(1, count($items));

        $charge0 = $items[0]->transactionId;
        $charge1 = $items[1]->transactionId;
        $this->assertNotNull($charge0);
        $this->assertNotNull($charge1);
        $this->assertNotEquals($charge0, $charge1);
    }

    /**
     * The list charges test method.
     */
    public function testListWhenConfigValidShouldListChargesWithString()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());

        $dateFormat = 'Y-m-d\TH:i:s.00\Z';
        $dateMinus10 = new DateTime();
        $dateMinus10->sub(new DateInterval('P1D'));
        $dateMinus10Utc = gmdate($dateFormat, $dateMinus10->Format('U'));
        $nowUtc = gmdate($dateFormat);

        $items = $chargeSvc->ListTransactions($dateMinus10Utc, $nowUtc, "CreditSale"); // HpsTransactionType::Capture
        $this->assertNotNull($items);
        $this->assertGreaterThan(1, count($items));

        $charge0 = $items[0]->transactionId;
        $charge1 = $items[1]->transactionId;
        $this->assertNotNull($charge0);
        $this->assertNotNull($charge1);
        $this->assertNotEquals($charge0, $charge1);
    }

    /**
     * The list charges test method.
     */
    public function testtestListWhenConfigValidShouldListChargesWithInteger()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());

        $dateFormat = 'Y-m-d\TH:i:s.00\Z';
        $dateMinus10 = new DateTime();
        $dateMinus10->sub(new DateInterval('P1D'));
        $dateMinus10Utc = gmdate($dateFormat, $dateMinus10->Format('U'));
        $nowUtc = gmdate($dateFormat);

        $items = $chargeSvc->listTransactions($dateMinus10Utc, $nowUtc, HpsTransactionType::CAPTURE); // HpsTransactionType::CAPTURE
        $this->assertNotNull($items);
        $this->assertGreaterThan(1, count($items));

        $charge0 = $items[0]->transactionId;
        $charge1 = $items[1]->transactionId;
        $this->assertNotNull($charge0);
        $this->assertNotNull($charge1);
        $this->assertNotEquals($charge0, $charge1);
    }

    /**
 * @group test
     * The get first charge test method.
     */
    public function testGetFirstWhenConfigValidShouldGetTheFirstCharge()
    {
        date_default_timezone_set("UTC");
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());

        $dateFormat = 'Y-m-d\TH:i:s.00\Z';
        $dateMinus10 = new DateTime();
        $dateMinus10->sub(new DateInterval('PT10H'));
        $dateMinus10Utc = gmdate($dateFormat, $dateMinus10->Format('U'));
        $nowUtc = gmdate($dateFormat);

        $items = $chargeSvc->ListTransactions($dateMinus10Utc, $nowUtc, "CreditSale");  // HpsTransactionType::Capture
        $this->assertTrue(0 != count($items));

        $charge0 = $items[0]->transactionId;
        $charge1 = $items[1]->transactionId;
        $this->assertNotNull($charge0);
        $this->assertNotNull($charge1);
        $this->assertNotEquals($charge0, $charge1);
    }

    public function testGatewayResponseAccessible()
    {
        $chargeSvc = new HpsCreditService(TestServicesConfig::validMultiUseConfig());
        echo $response = $chargeSvc->charge(10, 'usd', TestCreditCard::validVisaCreditCard(), TestCardHolder::validCardHolder());

        $this->assertEquals('00', $response->responseCode);
        $this->assertNotNull($response->gatewayResponse()->code);
        $this->assertNotNull($response->gatewayResponse()->message);
    }
}

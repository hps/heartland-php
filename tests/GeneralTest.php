<?php

use Heartland\Infrastructure\CardException;
use Heartland\Infrastructure\InvalidRequestException;
use Heartland\Services\HpsCreditService;

require_once("setup.php");

class GeneralTests extends PHPUnit_Framework_TestCase
{
    /**
     * @test
    /// The less than zero amount test method.
    */
    public function Charge_WhenAmountIsLessThanZero_ShouldThrowArgumentOutOfRange()
    {
        $ChargeAmount = -5;
        $chargeSvc = new HpsCreditService();

        try
        {
            $chargeSvc->Charge($ChargeAmount, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder());
        }
        catch (InvalidRequestException $e)
        {
            $this->assertEquals('invalid_amount', $e->code());
            $this->assertContains('Must be greater than or equal 0.', $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// The empty currency test method.
    */
    public function Charge_WhenCurrencyIsEmpty_ShouldThrowArgumentNull()
    {
        $ChargeAmount = 50;
        $Currency = "";
        $chargeSvc = new HpsCreditService();

        try
        {
            $chargeSvc->Charge($ChargeAmount, $Currency, TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder());
        }
        catch (InvalidRequestException $e)
        {
            $this->assertEquals('missing_currency', $e->code());
            $this->assertContains("Argument can't be null.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// The invalid currency test method.
    */
    public function Charge_WhenCurrencyIsNotUsd_ShouldThrowArgumentException()
    {
        $ChargeAmount = 50;
        $Currency = "eur";
        $chargeSvc = new HpsCreditService();

        try
        {
            $chargeSvc->Charge($ChargeAmount, $Currency, TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder());
        }
        catch (InvalidRequestException $e)
        {
            $this->assertEquals('invalid_currency', $e->code());
            $this->assertContains("The only supported currency is \"usd\"", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// The invalid HPS config test method.
    */
    public function Charge_WhenHpsConfigIsInvalid_ShouldThrowAuthenticationException()
    {
        $testConfig = new TestServicesConfig();

        $ChargeAmount = 50;
        $Currency = "usd";
        $chargeSvc = new HpsCreditService($testConfig->BadLicenseId());

        try
        {
            $chargeSvc->Charge($ChargeAmount, $Currency, TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder());
        }
        catch (AuthenticationException $e)
        {
            $this->assertEquals("Authentication error. Please double check your service configuration.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// The invalid HPS licenseId test method.
    */
    public function Charge_WhenHpsLicenseIdIsInvalid_ShouldThrowHpsException()
    {
        $testConfig = new TestServicesConfig();

        $ChargeAmount = 50;
        $Currency = "usd";
        $chargeSvc = new HpsCreditService($testConfig->BadLicenseId());

        try
        {
            $chargeSvc->Charge($ChargeAmount, $Currency, TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder());
        }
        catch (AuthenticationException $e)
        {
            $this->assertEquals("Authentication error. Please double check your service configuration.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// The invalid HPS config test method.
    */
    public function Charge_WhenCardNumberIsInvalid_ShouldThrowHpsException()
    {
        $testConfig = new TestServicesConfig();

        $ChargeAmount = 50;
        $Currency = "usd";
        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());

        try
        {
            $chargeSvc->Charge($ChargeAmount, $Currency, TestCreditCard::invalidCreditCard(), TestCardHolder::ValidCardHolder());
        }
        catch (CardException $e)
        {
            $this->assertEquals("14", $e->Code());
            $this->assertEquals("The card number is not a valid credit card number.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// The list transactions test method.
    */
    public function List_WhenConfigValid_ShouldListTransactions()
    {
        date_default_timezone_set("UTC");
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());

        $dateFormat = 'Y-m-d\TH:i:s.00\Z';
        $dateMinus10 = new DateTime();
        $dateMinus10->sub(new DateInterval('P10D'));
        $current = new DateTime();

        $items = $chargeSvc->listTransactions($dateMinus10->format($dateFormat), $current->format($dateFormat));
        $this->assertNotNull($items);
    }

    /**
     * @test
    /// The list charges test method.
    */
    public function List_WhenConfigValid_ShouldListCharges()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());

        $dateFormat = 'Y-m-d\TH:i:s.00\Z';
        $dateMinus10 = new DateTime();
        $dateMinus10->sub(new DateInterval('P10D'));
        $dateMinus10Utc = gmdate($dateFormat, $dateMinus10->Format('U'));
        $nowUtc = gmdate($dateFormat);

        $items = $chargeSvc->ListTransactions($dateMinus10Utc, $nowUtc, "CreditSale"); // HpsTransactionType::Capture
        $this->assertNotNull($items);
    }

    /**
     * @test
    /// The get first charge test method.
    */
    public function GetFirst_WhenConfigValid_ShouldGetTheFirstCharge()
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
        if (count($items)> 0)
        {
            //$charge = $chargeSvc->Get($items[0]->TransactionId);
            //$charge = $items['Details'][0]->GatewayTxnId;
            $charge = $items->transactionId;
            $this->assertNotNull($charge);
        }
    }
}
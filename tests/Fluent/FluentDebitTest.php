<?php

class FluentDebitTest extends PHPUnit_Framework_TestCase
{
    protected $service;

    protected function setUp()
    {
        $this->service = new HpsFluentDebitService(TestServicesConfig::validMultiUseConfig());
    }

    public function testCharge()
    {
        $charge = $this->service
            ->charge()
            ->withAmount(50)
            ->withCurrency("usd")
            ->withTrackData("%B4012002000060016^VI TEST CREDIT^251210118039000000000396?;4012002000060016=25121011803939600000?")
            ->withPinBlock("32539F50C245A6A93D123412324000AA")
            ->withAllowDuplicates(true)
            ->execute();

        $this->assertNotNull($charge);
        $this->assertEquals("00", $charge->responseCode);
    }

    public function testReturn()
    {
        $charge = $this->service
            ->charge()
            ->withAmount(50)
            ->withCurrency("usd")
            ->withTrackData("%B4012002000060016^VI TEST CREDIT^251210118039000000000396?;4012002000060016=25121011803939600000?")
            ->withPinBlock("32539F50C245A6A93D123412324000AA")
            ->withAllowDuplicates(true)
            ->execute();

        $this->assertNotNull($charge);
        $this->assertEquals("00", $charge->responseCode);

        $debitReturn = $this->service
            ->returnDebit()
            ->withTransactionId($charge->transactionId)
            ->withAmount(50)
            ->withTrackData("%B4012002000060016^VI TEST CREDIT^251210118039000000000396?;4012002000060016=25121011803939600000?")
            ->withPinBlock("32539F50C245A6A93D123412324000AA")
            ->withAllowDuplicates(true)
            ->execute();

        $this->assertNotNull($debitReturn);
        $this->assertEquals("00", $debitReturn->responseCode);
    }

    public function testReverse()
    {
        $charge = $this->service
            ->charge()
            ->withAmount(50)
            ->withCurrency("usd")
            ->withTrackData("%B4012002000060016^VI TEST CREDIT^251210118039000000000396?;4012002000060016=25121011803939600000?")
            ->withPinBlock("32539F50C245A6A93D123412324000AA")
            ->withAllowDuplicates(true)
            ->execute();

        $this->assertNotNull($charge);
        $this->assertEquals("00", $charge->responseCode);

        $reverse = $this->service
            ->reverse()
            ->withTransactionId($charge->transactionId)
            ->withAmount(50)
            ->withTrackData("%B4012002000060016^VI TEST CREDIT^251210118039000000000396?;4012002000060016=25121011803939600000?")
            ->execute();

        $this->assertNotNull($reverse);
        $this->assertEquals("00", $reverse->responseCode);
    }
}

<?php

class GatewayDebitVisaTest extends PHPUnit_Framework_TestCase
{
    public function testVisaDebitWhenValidTrackDataShouldChargeOk()
    {
        $service = new HpsDebitService(TestServicesConfig::validMultiUseConfig());
        $charge = $service->charge(
            50,
            "usd",
            "%B4012002000060016^VI TEST CREDIT^251210118039000000000396?;4012002000060016=25121011803939600000?",
            "32539F50C245A6A93D123412324000AA",
            null,
            true
        );
        $this->assertNotNull($charge);
        $this->assertEquals("00", $charge->responseCode);
    }

    public function testVisaDebitWhenValidE3DataShouldChargeOk()
    {
        $e3 = "&lt;E1050711%B4012001000000016^VI TEST CREDIT^251200000000000000000000?|LO04K0WFOmdkDz0um+GwUkILL8ZZOP6Z" .
              "c4rCpZ9+kg2T3JBT4AEOilWTI|+++++++Dbbn04ekG|11;4012001000000016=25120000000000000000?|1u2F/aEhbdoPixyAPGy" .
              "IDv3gBfF|+++++++Dbbn04ekG|00|||/wECAQECAoFGAgEH2wYcShV78RZwb3NAc2VjdXJlZXhjaGFuZ2UubmV0PX50qfj4dt0lu9oFB" .
              "ESQQNkpoxEVpCW3ZKmoIV3T93zphPS3XKP4+DiVlM8VIOOmAuRrpzxNi0TN/DWXWSjUC8m/PI2dACGdl/hVJ/imfqIs68wYDnp8j0Zfg" .
              "vM26MlnDbTVRrSx68Nzj2QAgpBCHcaBb/FZm9T7pfMr2Mlh2YcAt6gGG1i2bJgiEJn8IiSDX5M2ybzqRT86PCbKle/XCTwFFe1X|&gt;";
        $service = new HpsDebitService(TestServicesConfig::validMultiUseConfig());
        $encryptionData = new HpsEncryptionData();
        $encryptionData->version = "01";
        $charge = $service->charge(
            50,
            "usd",
            $e3,
            "32539F50C245A6A93D123412324000AA",
            $encryptionData,
            true
        );
        $this->assertNotNull($charge);
        $this->assertEquals("00", $charge->responseCode);
    }

    public function testVisaDebitWhenValidTrackDataShouldReturnOk()
    {
        $service = new HpsDebitService(TestServicesConfig::validMultiUseConfig());
        $charge = $service->charge(
            50,
            "usd",
            "%B4012002000060016^VI TEST CREDIT^251210118039000000000396?;4012002000060016=25121011803939600000?",
            "32539F50C245A6A93D123412324000AA",
            null,
            true
        );
        $this->assertNotNull($charge);
        $this->assertEquals("00", $charge->responseCode);

        $debitReturn = $service->returnDebit(
            $charge->transactionId,
            50,
            "%B4012002000060016^VI TEST CREDIT^251210118039000000000396?;4012002000060016=25121011803939600000?",
            "32539F50C245A6A93D123412324000AA",
            true
        );
        $this->assertNotNull($debitReturn);
        $this->assertEquals("00", $debitReturn->responseCode);
    }

    public function testVisaDebitWhenValidTrackDataShouldReverseOk()
    {
        $service = new HpsDebitService(TestServicesConfig::validMultiUseConfig());
        $charge = $service->charge(
            50,
            "usd",
            "%B4012002000060016^VI TEST CREDIT^251210118039000000000396?;4012002000060016=25121011803939600000?",
            "32539F50C245A6A93D123412324000AA",
            null,
            true
        );
        $this->assertNotNull($charge);
        $this->assertEquals("00", $charge->responseCode);

        $reverse = $service->reverse(
            $charge->transactionId,
            50,
            "%B4012002000060016^VI TEST CREDIT^251210118039000000000396?;4012002000060016=25121011803939600000?"
        );
        $this->assertNotNull($reverse);
        $this->assertEquals("00", $reverse->responseCode);
    }
}

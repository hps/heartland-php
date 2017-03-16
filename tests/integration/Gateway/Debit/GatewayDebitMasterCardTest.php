<?php

/**
 * Class GatewayDebitMasterCardTest
 */
class GatewayDebitMasterCardTest extends PHPUnit_Framework_TestCase
{
    public function testMastercardDebitWhenValidTrackDataShouldChargeOk()
    {
        $service = new HpsDebitService(TestServicesConfig::validMultiUseConfig());
        $charge = $service->charge(
            50,
            "usd",
            "%B5473500000000014^MC TEST CARD^251210199998888777766665555444433332?;5473500000000014=25121019999888877776?",
            "F505AD81659AA42A3D123412324000AB",
            null,
            true
        );
        $this->assertNotNull($charge);
        $this->assertEquals("00", $charge->responseCode);
    }

    public function testMastercardDebitWhenValidE3DataShouldChargeOk()
    {
        $e3 = "&lt;E1052711%B5473501000000014^MC TEST CARD^251200000000000000000000000000000000?|GVEY/MKaKXuqqjKRRueIdCHP" .
              "Poj1gMccgNOtHC41ymz7bIvyJJVdD3LW8BbwvwoenI+|+++++++C4cI2zjMp|11;5473501000000014=25120000000000000000?|8XqYkQGMdGeiIsgM0" .
              "pzdCbEGUDP|+++++++C4cI2zjMp|00|||/wECAQECAoFGAgEH2wYcShV78RZwb3NAc2VjdXJlZXhjaGFuZ2UubmV0PX50qfj4dt0lu9oFBESQQNkpoxEVpCW" .
              "3ZKmoIV3T93zphPS3XKP4+DiVlM8VIOOmAuRrpzxNi0TN/DWXWSjUC8m/PI2dACGdl/hVJ/imfqIs68wYDnp8j0ZfgvM26MlnDbTVRrSx68Nzj2QAgpBCHca" .
              "Bb/FZm9T7pfMr2Mlh2YcAt6gGG1i2bJgiEJn8IiSDX5M2ybzqRT86PCbKle/XCTwFFe1X|&gt;";
        $service = new HpsDebitService(TestServicesConfig::validMultiUseConfig());
        $encryptionData = new HpsEncryptionData();
        $encryptionData->version = "01";
        $charge = $service->charge(
            50,
            "usd",
            $e3,
            "F505AD81659AA42A3D123412324000AB",
            $encryptionData,
            true
        );
        $this->assertNotNull($charge);
        $this->assertEquals("00", $charge->responseCode);
    }

    public function testMastercardDebitWhenValidTrackDataShouldReturnOk()
    {
        $service = new HpsDebitService(TestServicesConfig::validMultiUseConfig());
        $charge = $service->charge(
            50,
            "usd",
            "%B5473500000000014^MC TEST CARD^251210199998888777766665555444433332?;5473500000000014=25121019999888877776?",
            "F505AD81659AA42A3D123412324000AB",
            null,
            true
        );
        $this->assertNotNull($charge);
        $this->assertEquals("00", $charge->responseCode);

        $debitReturn = $service->returnDebit(
            $charge->transactionId,
            50,
            "%B5473500000000014^MC TEST CARD^251210199998888777766665555444433332?;5473500000000014=25121019999888877776?",
            "F505AD81659AA42A3D123412324000AB",
            true
        );
        $this->assertNotNull($debitReturn);
        $this->assertEquals("00", $debitReturn->responseCode);
    }

    public function testMastercardDebitWhenValidTrackDataShouldReverseOk()
    {
        $service = new HpsDebitService(TestServicesConfig::validMultiUseConfig());
        $charge = $service->charge(
            50,
            "usd",
            "%B5473500000000014^MC TEST CARD^251210199998888777766665555444433332?;5473500000000014=25121019999888877776?",
            "F505AD81659AA42A3D123412324000AB",
            null,
            true
        );
        $this->assertNotNull($charge);
        $this->assertEquals("00", $charge->responseCode);

        $reverse = $service->reverse(
            $charge->transactionId,
            50,
            "%B5473500000000014^MC TEST CARD^251210199998888777766665555444433332?;5473500000000014=25121019999888877776?"
        );
        $this->assertNotNull($reverse);
        $this->assertEquals("00", $reverse->responseCode);
    }
}

<?php

class TestServicesConfig
{
    private $uatServiceUri = "https://posgateway.cert.secureexchange.net/Hps.Exchange.PosGateway/PosGatewayService.asmx?wsdl";
 
    // <summary>A valid HPS services config.</summary>
    static public function ValidMultiUseConfig(){
        $secretApiKey = "skapi_cert_MYl2AQAowiQAbLp5JesGKh7QFkcizOP2jcX9BrEMqQ";
        $config = new HpsServicesConfig();
        $config->secretApiKey = $secretApiKey;
        $config->versionNumber = '1510';
        $config->developerId = '002914';
        return $config;
    }

    static public function ValidMultiUsePublicKey(){
        return "pkapi_cert_P6dRqs1LzfWJ6HgGVZ";
    }

    static public function ValidEGoldConfig(){
        $config = new HpsServicesConfig();
        $config->siteId = '95881';
        $config->licenseId = '95878';
        $config->deviceId = '90911485';
        $config->username = '777700778679';
        $config->password = '$Test1234';
        return $config;
    }

    // <summary>An invalid HPS services config.</summary>
    public function BadLicenseId()
    {
        $secretApiKey = "skapi_cert_MWpSAACkRhcAx56PfFNi9orh4N-vix5-5qMTZMBChAf";
        $config = new HpsServicesConfig();
        $config->secretApiKey = $secretApiKey;
        $config->versionNumber = '1510';
        $config->developerId = '002914';

        return $config;
    }

    // Use with echeck and should work with giftcards
    static public function CertServicesConfigWithDescriptor(){
      $config = new HpsServicesConfig();
      $config->deviceId = 1520053;
      $config->licenseId = 20903;
      $config->password = '$Test1234';
      $config->siteId = 20904;
      $config->siteTrace = "trace0001";
      $config->username = "777700004597";
      $config->developerId = "123456";
      $config->versionNumber = "1234";
      $config->soapServiceUri = "https://posgateway.cert.secureexchange.net/Hps.Exchange.PosGateway/PosGatewayService.asmx";

      return $config;
    }
}

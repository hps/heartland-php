<?php

class TestServicesConfig
{
    private $uatServiceUri = "https://posgateway.cert.secureexchange.net/Hps.Exchange.PosGateway/PosGatewayService.asmx?wsdl";
 
    // <summary>A valid HPS services config.</summary>
    static public function ValidMultiUseConfig(){
        $secretApiKey = "skapi_cert_MYl2AQAowiQAbLp5JesGKh7QFkcizOP2jcX9BrEMqQ";
        $config = new HpsConfiguration();
        $config->secretApiKey = $secretApiKey;
        return $config;
    }

    static public function ValidMultiUsePublicKey(){
        return "pkapi_cert_P6dRqs1LzfWJ6HgGVZ";
    }

    // <summary>An invalid HPS services config.</summary>
    public function BadLicenseId()
    {
        $secretApiKey = "skapi_cert_MWpSAACkRhcAx56PfFNi9orh4N-vix5-5qMTZMBChAf";
        $config = new HpsConfiguration();
        $config->secretApiKey = $secretApiKey;
        $config->versionNumber = '1510';
        $config->developerId = '002914';

        return $config;
    }
}
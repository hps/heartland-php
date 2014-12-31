<?php

class HpsServicesConfig {
    public $credentialToken = null,
           $securityApiKey  = null,
           $licenseId       = null,
           $siteId          = null,
           $deviceId        = null,
           $versionNumber   = null,
           $username        = null,
           $password        = null,
           $developerId     = null,
           $siteTrace       = null,
           $useProxy        = null,
           $proxyOptions    = null,
           $soapServiceUri  = "https://posgateway.cert.secureexchange.net/Hps.Exchange.PosGateway/PosGatewayService.asmx",
           $payPlanBaseUri  = null;

    public function serviceUri() {
        return $this->soapServiceUri;
    }

    public function setServiceUri(string $value) {
        $this->soapServiceUri = $value;
    }
};

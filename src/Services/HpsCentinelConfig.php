<?php

class HpsCentinelConfig implements HpsConfigInterface
{
    public $proccesorId    = null;
    public $merchantId     = null;
    public $transactionPwd = null;
    public $version        = 1.7;
    public $useProxy       = false;
    public $proxyOptions   = null;
    public $serviceUri     = 'https://centineltest.cardinalcommerce.com/maps/txns.asp';
    public $curlOptions    = null;

    public function serviceUri()
    {
        return $this->serviceUri;
    }

    public function setServiceUri($value)
    {
        $this->serviceUri = $value;
    }

    public function validate($keyType)
    {
        return true;
    }
}

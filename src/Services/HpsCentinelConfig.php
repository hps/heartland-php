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

    public function serviceUri()
    {
        return $this->serviceUri;
    }

    public function setServiceUri(string $value)
    {
        $this->serviceUri = $value;
    }

    public function validate($keyType)
    {
        return true;
    }
}

<?php

/**
 * Class HpsCentinelConfig
 */
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
    /**
     * @return string
     */
    public function serviceUri()
    {
        return $this->serviceUri;
    }
    /**
     * @param $value
     *
     * @return mixed|void
     */
    public function setServiceUri($value)
    {
        $this->serviceUri = $value;
    }
    /**
     * @param $keyType
     *
     * @return bool
     */
    public function validate($keyType)
    {
        return true;
    }
}

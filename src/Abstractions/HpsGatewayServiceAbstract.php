<?php

abstract class HpsGatewayServiceAbstract
{
    protected $_config     = null;
    protected $_baseConfig = null;
    protected $_url        = null;
    protected $_amount   = null;
    protected $_currency = null;
    protected $_filterBy = null;

    public function __construct(HpsServicesConfig $config = null)
    {
        if ($config != null) {
            $this->_config = $config;
        }
    }

    public function servicesConfig()
    {
        return $this->_config;
    }

    public function setServicesConfig($value)
    {
        $this->_config = $value;
    }

    abstract protected function processResponse($curlResponse, $curlInfo, $curlError);

    protected function submitRequest($url, $headers, $data = null, $httpVerb = 'POST', $keyType = HpsServicesConfig::KEY_TYPE_SECRET, $options = null)
    {
        if ($this->_isConfigInvalid()) {
            throw new HpsAuthenticationException(
                HpsExceptionCodes::INVALID_CONFIGURATION,
                "The HPS SDK has not been properly configured. "
                ."Please make sure to initialize the config "
                ."in a service constructor."
            );
        }

        if (!$this->_config->validateApiKey($keyType)) {
            $type = $this->_config->getKeyType($keyType);
            $message = "The HPS SDK requires a valid {$keyType} API key to be used";
            if ($type == $keyType) {
                $message .= ".";
            } else {
                $message .= ", but a(n) {$type} key is currently configured.";
            }
            throw new HpsAuthenticationException(
                HpsExceptionCodes::INVALID_CONFIGURATION,
                $message
            );
        }

        try {
            $request = curl_init();
            curl_setopt($request, CURLOPT_URL, $url);
            curl_setopt($request, CURLOPT_CONNECTTIMEOUT, 60);
            curl_setopt($request, CURLOPT_TIMEOUT, 60);
            curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($request, CURLOPT_SSL_VERIFYHOST, false);
            if ($data != null) {
                curl_setopt($request, CURLOPT_CUSTOMREQUEST, $httpVerb);
                curl_setopt($request, CURLOPT_POSTFIELDS, $data);
            }
            curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($request, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);

            if ($this->_config->useProxy) {
                curl_setopt($request, CURLOPT_PROXY, $this->_config->proxyOptions['proxy_host']);
                curl_setopt($request, CURLOPT_PROXYPORT, $this->_config->proxyOptions['proxy_port']);
            }
            $curlResponse = curl_exec($request);
            $curlInfo = curl_getinfo($request);
            $curlError = curl_errno($request);

            if ($curlError == 28) {
                throw new HpsException("gateway_time-out");
            }

            return $this->processResponse($curlResponse, $curlInfo, $curlError);
        } catch (Exception $e) {
            throw new HpsGatewayException(
                $e->getCode() != null ? $e->getCode() : HpsExceptionCodes::UNKNOWN_GATEWAY_ERROR,
                $e->getMessage() != null ? $e->getMessage() : 'Unable to process transaction',
                null,
                null,
                $e
            );
        }
    }

    protected function _isConfigInvalid()
    {
        if ($this->_config == null && (
                $this->_config->secretApiKey == null ||
                $this->_config->userName == null ||
                $this->_config->password == null ||
                $this->_config->licenseId == -1 ||
                $this->_config->deviceId == -1 ||
                $this->_config->siteId == -1)
        ) {
            return true;
        }
        return false;
    }
}

<?php

class HpsRestGatewayService extends HpsGatewayServiceAbstract
{
    const CERT_URL = 'https://cert.api2.heartlandportico.com/Portico.PayPlan.v2';
    const PROD_URL = 'https://api2.heartlandportico.com/payplan.v2';
    const UAT_URL  = 'https://api-uat.heartlandportico.com/payplan.v2';
    protected $limit = null;
    protected $offset = null;
    protected $searchFields = null;

    public function page($limit, $offset)
    {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

    public function search($searchFields)
    {
        $this->searchFields = $searchFields;
        return $this;
    }

    protected function doRequest($data = null, $options = array())
    {
        $endpoint = isset($options['endpoint']) ? $options['endpoint'] : '';
        $verb = isset($options['verb']) ? $options['verb'] : 'GET';
        $url = $this->_gatewayUrlForKey() . '/' . $endpoint;

        if (isset($this->limit) && isset($this->offset)) {
            $paging = array(
                'limit'  => $this->limit,
                'offset' => $this->offset,
            );
            $url .= '?' . http_build_query($paging);
        }

        if ($this->searchFields != null) {
            $data = $this->searchFields;
        }

        $encodedData = json_encode($data);

        $identity = array();
        if (isset($this->_config->siteId)) {
            $identity[0] = 'SiteID='.$this->_config->siteId;
        }
        if (isset($this->_config->deviceId)) {
            $identity[1] = 'DeviceID='.$this->_config->deviceId;
        }
        if (isset($this->_config->licenseId)) {
            $identity[2] = 'LicenseID='.$this->_config->licenseId;
        }

        $auth = isset($this->_config->username)
            ? $this->_config->username.':'.$this->_config->password
            : $this->_config->secretApiKey;
        $header = array(
            'Authorization: Basic '.base64_encode($auth),
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: '.strlen($encodedData),
        );

        if (isset($this->_config->username)) {
            $header[] = 'HPS-Identity: '.implode(',', $identity);
        }
        $keyType = HpsServicesConfig::KEY_TYPE_SECRET;
        // print "\n" . $encodedData;
        return $this->submitRequest($url, $header, $encodedData, $verb, $keyType);
    }

    protected function processResponse($curlResponse, $curlInfo, $curlError)
    {
        // print "\n" . $curlResponse;
        $response = json_decode($curlResponse);

        switch ($curlInfo['http_code']) {
            case '200':
            case '204':
                return $response;
                break;
            case '400':
                throw new HpsException($response->error->message);
                break;
            default:
                throw new HpsException('Unexpected response');
                break;
        }
    }

    protected function hydrateObject($result, $type)
    {
        return call_user_func(array($type, 'fromStdClass'), $result);
    }

    protected function hydrateSearchResults($resultSet, $type)
    {
        $results = array();
        foreach ($resultSet->results as $result) {
            $results[] = $this->hydrateObject($result, $type);
        }
        unset($result);

        return (object)array(
            'offset'  => $resultSet->offset,
            'limit'   => $resultSet->limit,
            'total'   => $resultSet->totalMatchingRecords,
            'results' => $results,
        );
    }

    private function _gatewayUrlForKey()
    {
        if ($this->_config->secretApiKey != null && $this->_config->secretApiKey != "") {
            if (strpos($this->_config->secretApiKey, '_cert_') !== false) {
                return self::CERT_URL;
            } else if (strpos($this->_config->secretApiKey, '_uat_') !== false) {
                return self::UAT_URL;
            } else {
                return self::PROD_URL;
            }
        } else {
            return $this->_config->soapServiceUri;
        }
    }
}

<?php
// This should only be used for testing tokens.
/**
 * Class HpsTokenService
 */
class HpsTokenService extends HpsRestGatewayService
{
    protected $_publicAPIKey = null;
    protected $_url          = null;
    /**
     * HpsTokenService constructor.
     *
     * @param \HpsConfigInterface|null $publicAPIKey
     */
    public function __construct($publicAPIKey)
    {
        $this->_config = new HpsServicesConfig();
        $this->_config->publicApiKey = $publicAPIKey;

        $components = explode("_", $publicAPIKey);
        if (count($components) == 3 && strtolower($components[1]) == "prod") {
            $this->_url = "https://api2.heartlandportico.com/SecureSubmit.v1/api/token";
        } else {
            $this->_url = "https://cert.api2.heartlandportico.com/Hps.Exchange.PosGateway.Hpf.v1/api/token";
        }
    }
    /**
     * @param \HpsCreditCard $cardData
     *
     * @return mixed
     * @throws \HpsAuthenticationException
     * @throws \HpsGatewayException
     */
    public function getToken(HpsCreditCard $cardData)
    {
        $data = array();
        $data['api_key'] = $this->_config->publicApiKey;
        $data['object'] = 'token';
        $data['token_type'] = 'supt';
        $data['_method'] = 'post';
        $data['card[number]'] = $cardData->number;
        $data['card[cvc]'] = $cardData->cvv;
        $data['card[exp_month]'] = $cardData->expMonth;
        $data['card[exp_year]'] = $cardData->expYear;

        $url = $this->_url ."?". http_build_query($data);
        $header = array('Content-type: application/json');

        return $this->submitRequest($url, $header, null, 'GET', HpsServicesConfig::KEY_TYPE_PUBLIC);
    }
    /**
     * @param $curlResponse
     * @param $curlInfo
     * @param $curlError
     *
     * @return mixed
     * @throws \HpsException
     */
    protected function processResponse($curlResponse, $curlInfo, $curlError)
    {
        $response = json_decode($curlResponse);

        if (isset($response->error) && is_object($response->error)) {
            throw new HpsException($response->error->message, $response->error->code);
        }
        
        return $response;
    }
    /**
     * @return bool
     */
    protected function _isConfigInvalid()
    {
        return $this->_config->publicApiKey == null || $this->_url == null;
    }
}

<?php
// This should only be used for testing tokens.

class HpsTokenService {
    private $_publicAPIKey  = null;
    private $_url           = null;

    function __construct($publicAPIKey){
        $this->_publicAPIKey = $publicAPIKey;

        if($publicAPIKey == null || $publicAPIKey == ""){
            throw new HpsException("Public Key not found","0");
        }

        $components = explode("_",$publicAPIKey);
        if(count($components) != 3){
            throw new HpsException("Public API Key must Contain three underscores","0");
        }

        if(strtolower($components[1]) == "prod"){
            $this->_url = "https://api.heartlandportico.com/SecureSubmit.v1/api/token";
        }else {
            $this->_url = "https://posgateway.cert.secureexchange.net/Hps.Exchange.PosGateway.Hpf.v1/api/token";
        }
    }

    function getToken(HpsCreditCard $cardData){
        try{
            $data['api_key'] = $this->_publicAPIKey;
            $data['object'] = 'token';
            $data['token_type'] = 'supt';
            $data['_method'] = 'post';
            $data['card[number]'] = $cardData->number;
            $data['card[cvc]'] = $cardData->cvv;
            $data['card[exp_month]'] = $cardData->expMonth;
            $data['card[exp_year]'] = $cardData->expYear;

            $header = array('Content-type: application/json');

            $tokenFetch = curl_init();
            curl_setopt($tokenFetch, CURLOPT_URL, $this->_url ."?". http_build_query($data));
            curl_setopt($tokenFetch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($tokenFetch, CURLOPT_TIMEOUT,        10);
            curl_setopt($tokenFetch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt($tokenFetch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($tokenFetch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($tokenFetch, CURLOPT_HTTPHEADER, $header);
            $curlResponse = curl_exec($tokenFetch);

            $response = json_decode($curlResponse);
            if(isset($response->error) && is_object($response->error)){
                throw new HpsException($response->error->message,$response->error->code);
            }
            return $response;
        }catch (Exception $e){
            throw new HpsException($e->getMessage(),$e->getCode());
        }
    }
}
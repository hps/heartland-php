<?php

class HpsService {
    public $config = null;
    protected $_amount = null;
    protected $_currency = null;
    protected $_filterBy = null;

    public function __construct(HpsConfiguration $config=null){
        if($config != null){
            $this->config = $config;
        }
    }

    public function doTransaction($transaction,$clientTransactionId=null){
        if($this->_configurationInvalid()){
            throw HpsExceptionMapper::map_sdk_exception(HpsSdkCodes::$invalidConfig);
        }

        $xml = new DOMDocument('1.0', 'utf-8');
        $soapEnvelope = $xml->createElement('soapenv:Envelope');
        $soapEnvelope->setAttribute('xmlns:soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
        $soapEnvelope->setAttribute('xmlns:hps', 'http://Hps.Exchange.PosGateway');

        $soapBody = $xml->createElement('soapenv:Body');
            $hpsRequest = $xml->createElement('hps:PosRequest');
                $hpsVersion = $xml->createElement('hps:Ver1.0');
                    $hpsHeader = $xml->createElement('hps:Header');

                        if ($this->config->secretApiKey != NULL && $this->config->secretApiKey != ""){
                            $hpsHeader->appendChild($xml->createElement('hps:SecretAPIKey',$this->config->secretApiKey));
                        }else{
                            $hpsHeader->appendChild($xml->createElement('hps:SiteId',$this->config->siteId));
                            $hpsHeader->appendChild($xml->createElement('hps:DeviceId',$this->config->deviceId));
                            $hpsHeader->appendChild($xml->createElement('hps:LicenseId',$this->config->licenseId));
                            $hpsHeader->appendChild($xml->createElement('hps:UserName',$this->config->userName));
                            $hpsHeader->appendChild($xml->createElement('hps:Password',$this->config->password));
                        }
                        if ($this->config->developerId != null && $this->config->developerId != ""){
                            $hpsHeader->appendChild($xml->createElement('hps:DeveloperID',$this->config->developerId));
                            $hpsHeader->appendChild($xml->createElement('hps:VersionNbr',$this->config->versionNumber));
                            $hpsHeader->appendChild($xml->createElement('hps:SiteTrace',$this->config->siteTrace));
                        }
                        if ($clientTransactionId != null){
                            $hpsHeader->appendChild($xml->createElement('hps:ClientTxnId', $clientTransactionId));
                        }

                $hpsVersion->appendChild($hpsHeader);
                $transaction = $xml->importNode($transaction,true);
                $hpsVersion->appendChild($transaction);
            $hpsRequest->appendChild($hpsVersion);
        $soapBody->appendChild($hpsRequest);
        $soapEnvelope->appendChild($soapBody);
        $xml->appendChild($soapEnvelope);

        //cURL
        try{
            $header = array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "SOAPAction: \"\"",
                "Content-length: ".strlen($xml->saveXML()),
            );
            $soap_do = curl_init();
            curl_setopt($soap_do, CURLOPT_URL, $this->_gatewayUrlForKey($this->config->secretApiKey));
            curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
            curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($soap_do, CURLOPT_POST, true);
            curl_setopt($soap_do, CURLOPT_POSTFIELDS, $xml->saveXML());
            curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $header);

            if($this->config->useProxy){
                curl_setopt($soap_do, CURLOPT_PROXY, $this->config->proxyOptions['proxy_host']);
                curl_setopt($soap_do, CURLOPT_PROXYPORT, $this->config->proxyOptions['proxy_port']);
            }
            $curlResponse = curl_exec($soap_do);
            $curlInfo = curl_getinfo($soap_do);

            if($curlInfo['http_code'] == '200'){
                $responseObject = $this->_XML2Array($curlResponse);
                $ver = "Ver1.0";
                return $responseObject->$ver;
            }else{
                throw HpsExceptionMapper::map_sdk_exception(HpsSdkCodes::$unableToProcessTransaction);
            }
        }catch (Exception $e){
            throw HpsExceptionMapper::map_sdk_exception(HpsSdkCodes::$unableToProcessTransaction, $e);
        }
    }

    private function _configurationInvalid(){
        if($this->config == null && (
                $this->config->secretApiKey == null ||
                $this->config->userName == null ||
                $this->config->password == null ||
                $this->config->licenseId == null ||
                $this->config->deviceId == null ||
                $this->config->siteId == null)
        ){
            return true;
        }
        return false;
    }

    private function _gatewayUrlForKey($apiKey){
        if ($apiKey != NULL && $apiKey != ""){
            if( strpos($apiKey, '_cert_') !== false){
                return "https://posgateway.cert.secureexchange.net/Hps.Exchange.PosGateway/PosGatewayService.asmx";
            }else{
                return "https://posgateway.secureexchange.net/Hps.Exchange.PosGateway/PosGatewayService.asmx";
            }
        }else{
            return $this->config->serviceUri;
        }
    }

    private function _XML2Array($xml){
        $envelope = simplexml_load_string($xml, "SimpleXMLElement", 0,'http://schemas.xmlsoap.org/soap/envelope/');
        foreach($envelope->Body as $response) {
            foreach ($response->children('http://Hps.Exchange.PosGateway') as $item) {
                return $item;
            }
        }
    }
} 
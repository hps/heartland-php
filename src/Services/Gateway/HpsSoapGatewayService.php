<?php

/**
 * Class HpsSoapGatewayService
 */
class HpsSoapGatewayService extends HpsGatewayServiceAbstract implements HpsGatewayServiceInterface
{
    /**
     * @param       $transaction
     * @param array $options
     *
     * @return mixed
     * @throws \HpsAuthenticationException
     * @throws \HpsGatewayException
     */
    public function doRequest($transaction, $options = array())
    {
        $xml = new DOMDocument('1.0', 'utf-8');
        $soapEnvelope = $xml->createElement('soapenv:Envelope');
        $soapEnvelope->setAttribute('xmlns:soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
        $soapEnvelope->setAttribute('xmlns:hps', 'http://Hps.Exchange.PosGateway');

        $soapBody = $xml->createElement('soapenv:Body');
        $hpsRequest = $xml->createElement('hps:PosRequest');

        $hpsVersion = $xml->createElement('hps:Ver1.0');
        $hpsHeader = $xml->createElement('hps:Header');

        if ($this->_config->secretApiKey != null && $this->_config->secretApiKey != "") {
            $hpsHeader->appendChild($xml->createElement('hps:SecretAPIKey', trim($this->_config->secretApiKey)));
        } else {
            $hpsHeader->appendChild($xml->createElement('hps:SiteId', $this->_config->siteId));
            $hpsHeader->appendChild($xml->createElement('hps:DeviceId', $this->_config->deviceId));
            $hpsHeader->appendChild($xml->createElement('hps:LicenseId', $this->_config->licenseId));
            $hpsHeader->appendChild($xml->createElement('hps:UserName', $this->_config->username));
            $hpsHeader->appendChild($xml->createElement('hps:Password', $this->_config->password));
        }
        if ($this->_config->developerId != null && $this->_config->developerId != "") {
            $hpsHeader->appendChild($xml->createElement('hps:DeveloperID', $this->_config->developerId));
            $hpsHeader->appendChild($xml->createElement('hps:VersionNbr', $this->_config->versionNumber));
            $hpsHeader->appendChild($xml->createElement('hps:SiteTrace', $this->_config->siteTrace));
        }
        if (isset($options['clientTransactionId'])) {
            $hpsHeader->appendChild($xml->createElement('hps:ClientTxnId', $options['clientTransactionId']));
        }

        $hpsVersion->appendChild($hpsHeader);
        $transaction = $xml->importNode($transaction, true);
        $hpsVersion->appendChild($transaction);

        $hpsRequest->appendChild($hpsVersion);
        $soapBody->appendChild($hpsRequest);
        $soapEnvelope->appendChild($soapBody);
        $xml->appendChild($soapEnvelope);

        $url = $this->_gatewayUrlForKey();
        $header = array(
            'Content-type: text/xml;charset="utf-8"',
            'Accept: text/xml',
            'SOAPAction: ""',
            'Content-length: '.strlen($xml->saveXML()),
        );
        $data = $xml->saveXML();
        // print "\n" . $data;

        return $this->submitRequest($url, $header, $data);
    }
    /**
     * @param $curlResponse
     * @param $curlInfo
     * @param $curlError
     *
     * @return mixed
     * @throws \HpsException
     */
    public function processResponse($curlResponse, $curlInfo, $curlError)
    {
        // print "\n" . $curlResponse;
        switch ($curlInfo['http_code']) {
            case '200':
                $responseObject = $this->_XML2Array($curlResponse);
                $ver = "Ver1.0";
                return $responseObject->$ver;
                break;
            case '500':
                $faultString = $this->_XMLFault2String($curlResponse);
                throw new HpsException($faultString);
                break;
            default:
                throw new HpsException('Unexpected response');
                break;
        }
    }
    /**
     * @param              $details
     * @param \DOMDocument $xml
     *
     * @return \DOMElement
     */
    public function _hydrateAdditionalTxnFields($details, DOMDocument $xml)
    {
        $additionalTxnFields = $xml->createElement('hps:AdditionalTxnFields');

        if ($details->memo != null && $details->memo != "") {
            $additionalTxnFields->appendChild($xml->createElement('hps:Description', $details->memo));
        }

        if ($details->invoiceNumber != null && $details->invoiceNumber != "") {
            $additionalTxnFields->appendChild($xml->createElement('hps:InvoiceNbr', $details->invoiceNumber));
        }

        if ($details->customerId != null && $details->customerId != "") {
            $additionalTxnFields->appendChild($xml->createElement('hps:CustomerID', $details->customerId));
        }

        return $additionalTxnFields;
    }
    /**
     * @param \HpsCardHolder $cardHolder
     * @param \DOMDocument   $xml
     *
     * @return \DOMElement
     * @throws \HpsInvalidRequestException
     */
    public function _hydrateCardHolderData(HpsCardHolder $cardHolder, DOMDocument $xml)
    {
        //handle both phone and phoneNumber properties as a valid phone
        if($cardHolder->phone === null && !empty($cardHolder->phoneNumber) === true){
            $cardHolder->phone = $cardHolder->phoneNumber;
        }              
        //handle both email and emailAddress properties as a valid email
        if($cardHolder->email === null && !empty($cardHolder->emailAddress) === true){
            $cardHolder->email = $cardHolder->emailAddress;
        }               
        
        $cardHolderData = $xml->createElement('hps:CardHolderData');
        $cardHolderData->appendChild($xml->createElement('hps:CardHolderFirstName', HpsInputValidation::checkCardHolderData($cardHolder->firstName, 'FirstName')));
        $cardHolderData->appendChild($xml->createElement('hps:CardHolderLastName', HpsInputValidation::checkCardHolderData($cardHolder->lastName,'LastName')));
        $cardHolderData->appendChild($xml->createElement('hps:CardHolderEmail', HpsInputValidation::checkEmailAddress($cardHolder->email)));
        $cardHolderData->appendChild($xml->createElement('hps:CardHolderPhone', HpsInputValidation::checkPhoneNumber($cardHolder->phone)));
        $cardHolderData->appendChild($xml->createElement('hps:CardHolderAddr', HpsInputValidation::checkCardHolderData($cardHolder->address->address)));
        $cardHolderData->appendChild($xml->createElement('hps:CardHolderCity', HpsInputValidation::checkCardHolderData($cardHolder->address->city, 'City')));
        $cardHolderData->appendChild($xml->createElement('hps:CardHolderState', HpsInputValidation::checkCardHolderData($cardHolder->address->state, 'State')));
        $cardHolderData->appendChild($xml->createElement('hps:CardHolderZip', HpsInputValidation::checkZipCode($cardHolder->address->zip)));

        return $cardHolderData;
    }
    /**
     * @param \HpsCheck    $check
     * @param \DOMDocument $xml
     *
     * @return \DOMElement
     */
    public function _hydrateCheckData(HpsCheck $check, DOMDocument $xml)
    {
        $checkData = $xml->createElement('hps:AccountInfo');

        if ($check->accountNumber != null) {
            $checkData->appendChild($xml->createElement('hps:AccountNumber', $check->accountNumber));
        }

        if ($check->checkNumber != null) {
            $checkData->appendChild($xml->createElement('hps:CheckNumber', $check->checkNumber));
        }

        if ($check->micrNumber != null) {
            $checkData->appendChild($xml->createElement('hps:MICRData', $check->micrNumber));
        }

        if ($check->routingNumber != null) {
            $checkData->appendChild($xml->createElement('hps:RoutingNumber', $check->routingNumber));
        }

        if ($check->accountType != null) {
            $checkData->appendChild($xml->createElement('hps:AccountType', strtoupper($check->accountType)));
        }

        return $checkData;
    }
    /**
     * @param \HpsCheck    $check
     * @param \DOMDocument $xml
     *
     * @return \DOMElement
     */
    public function _hydrateConsumerInfo(HpsCheck $check, DOMDocument $xml)
    {
        $consumerInfo = $xml->createElement('hps:ConsumerInfo');

        if ($check->checkHolder->address != null) {
            $consumerInfo->appendChild($xml->createElement('hps:Address1', $check->checkHolder->address->address));
            $consumerInfo->appendChild($xml->createElement('hps:City', $check->checkHolder->address->city));
            $consumerInfo->appendChild($xml->createElement('hps:State', $check->checkHolder->address->state));
            $consumerInfo->appendChild($xml->createElement('hps:Zip', $check->checkHolder->address->zip));
        }

        if ($check->checkHolder->checkName != null) {
            $consumerInfo->appendChild($xml->createElement('hps:CheckName', $check->checkHolder->checkName));
        }

        if ($check->checkHolder->courtesyCard != null) {
            $consumerInfo->appendChild($xml->createElement('hps:CourtesyCard', $check->checkHolder->courtesyCard));
        }

        if ($check->checkHolder->dlNumber != null) {
            $consumerInfo->appendChild($xml->createElement('hps:DLNumber', $check->checkHolder->dlNumber));
        }

        if ($check->checkHolder->dlState != null) {
            $consumerInfo->appendChild($xml->createElement('hps:DLState', $check->checkHolder->dlState));
        }

        if ($check->checkHolder->email != null) {
            $consumerInfo->appendChild($xml->createElement('hps:EmailAddress', $check->checkHolder->email));
        }

        if ($check->checkHolder->firstName != null) {
            $consumerInfo->appendChild($xml->createElement('hps:FirstName', $check->checkHolder->firstName));
        }

        if ($check->checkHolder->lastName != null) {
            $consumerInfo->appendChild($xml->createElement('hps:LastName', $check->checkHolder->lastName));
        }

        if ($check->checkHolder->phone != null) {
            $consumerInfo->appendChild($xml->createElement('hps:PhoneNumber', $check->checkHolder->phone));
        }

        if ($check->checkHolder->ssl4 != null || $check->checkHolder->dobYear != null) {
            $identityInfo = $xml->createElement('hps:IdentityInfo');
            if ($check->checkHolder->ssl4 != null) {
                $identityInfo->appendChild($xml->createElement('hps:SSNL4', $check->checkHolder->ssl4));
            }
            if ($check->checkHolder->dobYear != null) {
                $identityInfo->appendChild($xml->createElement('hps:DOBYear', $check->checkHolder->dobYear));
            }
            $consumerInfo->appendChild($identityInfo);
        }

        return $consumerInfo;
    }
    /**
     * @param \HpsCPCData  $cpcData
     * @param \DOMDocument $xml
     *
     * @return \DOMElement
     */
    public function _hydrateCPCData(HpsCPCData $cpcData, DOMDocument $xml)
    {
        $cpcDataElement = $xml->createElement('hps:CPCData');
        if (isset($cpcData->cardHolderPONbr)) {
            $cpcDataElement->appendChild($xml->createElement('hps:CardHolderPONbr', $cpcData->cardHolderPONbr));
        }
        if (isset($cpcData->taxAmt)) {
            $cpcDataElement->appendChild($xml->createElement('hps:TaxAmt', $cpcData->taxAmt));
        }
        if (isset($cpcData->taxType)) {
            $cpcDataElement->appendChild($xml->createElement('hps:TaxType', $cpcData->taxType));
        }

        return $cpcDataElement;
    }
    /**
     * @param \HpsDirectMarketData $data
     * @param \DOMDocument         $xml
     *
     * @return \DOMElement
     */
    public function _hydrateDirectMarketData(HpsDirectMarketData $data, DOMDocument $xml)
    {
        $directMktDataElement = $xml->createElement('hps:DirectMktData');
        $directMktDataElement->appendChild($xml->createElement('hps:DirectMktInvoiceNbr', $data->invoiceNumber));
        $directMktDataElement->appendChild($xml->createElement('hps:DirectMktShipMonth', $data->shipMonth));
        $directMktDataElement->appendChild($xml->createElement('hps:DirectMktShipDay', $data->shipDay));

        return $directMktDataElement;
    }
    /**
     * @param \HpsEncryptionData $encryptionData
     * @param \DOMDocument       $xml
     *
     * @return \DOMElement
     */
    public function _hydrateEncryptionData(HpsEncryptionData $encryptionData, DOMDocument $xml)
    {
        $encData = $xml->createElement('hps:EncryptionData');
        if ($encryptionData->encryptedTrackNumber != null) {
            $encData->appendChild($xml->createElement('hps:EncryptedTrackNumber', $encryptionData->encryptedTrackNumber));
        }
        $encData->appendChild($xml->createElement('hps:KSN', $encryptionData->ksn));
        $encData->appendChild($xml->createElement('hps:KTB', $encryptionData->ktb));
        $encData->appendChild($xml->createElement('hps:Version', $encryptionData->version));
        return $encData;
    }
    /**
     * @param \HpsGiftCard $giftCard
     * @param \DOMDocument $xml
     * @param string       $elementName
     *
     * @return \DOMElement
     */
    public function _hydrateGiftCardData(HpsGiftCard $giftCard, DOMDocument $xml, $elementName = 'CardData')
    {
        $giftCardData = $xml->createElement('hps:'.$elementName);
        if ($giftCard->number != null) {
            $giftCardData->appendChild($xml->createElement('hps:CardNbr', $giftCard->number));
        } else if ($giftCard->trackData != null) {
            $giftCardData->appendChild($xml->createElement('hps:TrackData', $giftCard->trackData));
        } else if ($giftCard->alias != null) {
            $giftCardData->appendChild($xml->createElement('hps:Alias', $giftCard->alias));
        } else if ($giftCard->tokenValue != null) {
            $giftCardData->appendChild($xml->createElement('hps:TokenValue', $giftCard->tokenValue));
        }

        if ($giftCard->encryptionData != null) {
            $giftCardData->appendChild($this->_hydrateEncryptionData($giftCard->encryptionData, $xml));
        }

        if ($giftCard->pin != null) {
            $giftCardData->appendChild($xml->createElement('hps:PIN', $giftCard->pin));
        }

        return $giftCardData;
    }
    /**
     * @param \HpsCreditCard $card
     * @param \DOMDocument   $xml
     * @param bool           $cardPresent
     * @param bool           $readerPresent
     *
     * @return \DOMElement
     */
    public function _hydrateManualEntry(HpsCreditCard $card, DOMDocument $xml, $cardPresent = false, $readerPresent = false)
    {
        $manualEntry = $xml->createElement('hps:ManualEntry');

        if (isset($card->number)) {
            $manualEntry->appendChild($xml->createElement('hps:CardNbr', $card->number));
        }

        if (isset($card->expMonth)) {
            $manualEntry->appendChild($xml->createElement('hps:ExpMonth', $card->expMonth));
        }

        if (isset($card->expYear)) {
            $manualEntry->appendChild($xml->createElement('hps:ExpYear', $card->expYear));
        }

        if (isset($card->cvv)) {
            $manualEntry->appendChild($xml->createElement('hps:CVV2', $card->cvv));
        }

        $manualEntry->appendChild($xml->createElement('hps:CardPresent', ($cardPresent ? 'Y' : 'N')));
        $manualEntry->appendChild($xml->createElement('hps:ReaderPresent', ($readerPresent ? 'Y' : 'N')));

        return $manualEntry;
    }
    /**
     * @param $data
     * @param $xml
     *
     * @return mixed
     */
    public function _hydrateSecureEcommerce($data, $xml)
    {
        $secureEcommerce = $xml->createElement('hps:SecureECommerce');
        $secureEcommerce->appendChild($xml->createElement('hps:PaymentDataSource', $data->dataSource));
        $secureEcommerce->appendChild($xml->createElement('hps:TypeOfPaymentData', $data->type));

        $paymentDataElement = $xml->createElement('hps:PaymentData', $data->data);
        $paymentDataElementEncoding = $xml->createAttribute('encoding');
        $paymentDataElementEncoding->value = 'base64';
        $paymentDataElement->appendChild($paymentDataElementEncoding);
        $secureEcommerce->appendChild($paymentDataElement);

        if ($data->eciFlag != null && $data->eciFlag != '') {
            $secureEcommerce->appendChild($xml->createElement('hps:ECommerceIndicator', $data->eciFlag));
        }

        $xidElement = $xml->createElement('hps:XID', $data->xid);
        $xidElementEncoding = $xml->createAttribute('encoding');
        $xidElementEncoding->value = 'base64';
        $xidElement->appendChild($xidElementEncoding);
        $secureEcommerce->appendChild($xidElement);

        return $secureEcommerce;
    }
 /*
  * @link https://github.com/hps/heartland-php/pull/21
  * @description resolves a recursion issue identified in the link above
  */
    /**
     * @param              $token
     * @param \DOMDocument $xml
     * @param bool         $cardPresent
     * @param bool         $readerPresent
     *
     * @return \DOMElement
     */
    public function _hydrateTokenData($token, DOMDocument $xml, $cardPresent = false, $readerPresent = false)
    {
        if (!$token instanceof HpsTokenData) {
            $tokenValue = $token;
            $token = new HpsTokenData();
            $token->tokenValue = $tokenValue;
        }

        $tokenData = $xml->createElement('hps:TokenData');
        $tokenData->appendChild($xml->createElement('hps:TokenValue', $token->tokenValue));

        if (isset($token->expMonth)) {
            $tokenData->appendChild($xml->createElement('hps:ExpMonth', $token->expMonth));
        }

        if (isset($token->expYear)) {
            $tokenData->appendChild($xml->createElement('hps:ExpYear', $token->expYear));
        }

        if (isset($token->cvv)) {
            $tokenData->appendChild($xml->createElement('hps:CVV2', $token->cvv));
        }

        $tokenData->appendChild($xml->createElement('hps:CardPresent', ($cardPresent ? 'Y' : 'N')));
        $tokenData->appendChild($xml->createElement('hps:ReaderPresent', ($readerPresent ? 'Y' : 'N')));
        return $tokenData;
    }
    /**
     * @param \HpsTrackData $trackData
     * @param               $xml
     *
     * @return mixed
     */
    public function _hydrateTrackData(HpsTrackData $trackData, $xml)
    {
        $trackDataElement = $xml->createElement('hps:TrackData', $trackData->value);
        $trackDataElementMethod = $xml->createAttribute('method');
        $trackDataElementMethod->value = $trackData->method;
        $trackDataElement->appendChild($trackDataElementMethod);
        return $trackDataElement;
    }
    /**
     * @return string
     */
    private function _gatewayUrlForKey()
    {
        if ($this->_config->secretApiKey != null && $this->_config->secretApiKey != "") {
            if (strpos($this->_config->secretApiKey, '_cert_') !== false) {
                return "https://cert.api2.heartlandportico.com/Hps.Exchange.PosGateway/PosGatewayService.asmx";
            } else if (strpos($this->_config->secretApiKey, '_uat_') !== false) {
                return "https://posgateway.uat.secureexchange.net/Hps.Exchange.PosGateway/PosGatewayService.asmx";
            } else {
                return "https://api2.heartlandportico.com/Hps.Exchange.PosGateway/PosGatewayService.asmx";
            }
        } else {
            return $this->_config->soapServiceUri;
        }
    }
    /**
     * @param $xml
     *
     * @return mixed
     */
    private function _XML2Array($xml)
    {
        $envelope = simplexml_load_string($xml, "SimpleXMLElement", 0, 'http://schemas.xmlsoap.org/soap/envelope/');
        foreach ($envelope->Body as $response) {
            foreach ($response->children('http://Hps.Exchange.PosGateway') as $item) {
                return $item;
            }
        }
        return null;
    }
    /**
     * @param $xml
     *
     * @return string
     */
    private function _XMLFault2String($xml)
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        return $dom->getElementsByTagName('faultstring')->item(0)->nodeValue;
    }
}

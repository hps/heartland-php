<?php

class HpsChargeService extends HpsService{

    public function authorize($amount, $currency, $cardOrToken, $cardHolder=null, $requestMultiUseToken=false, $details=null){
        $this->_checkAmount($amount);
        $this->_checkCurrency($currency);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
            $hpsCreditAuth = $xml->createElement('hps:CreditAuth');
                $hpsBlock1 = $xml->createElement('hps:Block1');
                    $hpsBlock1->appendChild($xml->createElement('hps:AllowDup','Y'));
                    $hpsBlock1->appendChild($xml->createElement('hps:Amt',$amount));
                    if($cardHolder != null){
                        $hpsBlock1->appendChild($this->_hydrateCardHolderData($cardHolder,$xml));
                    }

                    if($details != null){
                        $hpsBlock1->appendChild($this->_hydrateAdditionalTxnFields($details,$xml));
                    }
                    $cardData = $xml->createElement('hps:CardData');
                    if($cardOrToken instanceOf HpsCreditCard){
                        $cardData->appendChild($this->_hydrateManualEntry($cardOrToken,$xml));
                    }else{
                        $tokenData = $xml->createElement('hps:TokenData');
                        $tokenData->appendChild($xml->createElement('hps:TokenValue',$cardOrToken->tokenValue));
                        $cardData->appendChild($tokenData);
                    }
                    $cardData->appendChild($xml->createElement('hps:TokenRequest',($requestMultiUseToken) ? 'Y' : 'N'));
                $hpsBlock1->appendChild($cardData);
            $hpsCreditAuth->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCreditAuth);
        return $this->_submitAuthorize($hpsTransaction, $amount, $currency);
    }

    public function capture($transactionId, $amount=null, $gratuity=null){
        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
            $hpsCreditAddToBatch = $xml->createElement('hps:CreditAddToBatch');
                $hpsCreditAddToBatch->appendChild($xml->createElement('hps:GatewayTxnId',$transactionId));
                if($amount != null){
                    $hpsCreditAddToBatch->appendChild($xml->createElement('hps:Amt',$amount));
                }
                if($gratuity != null){
                    $hpsCreditAddToBatch->appendChild($xml->createElement('hps:GratuityAmtInfo',$gratuity));
                }
        $hpsTransaction->appendChild($hpsCreditAddToBatch);
        $response = $this->doTransaction($hpsTransaction);
        $header = $response->Header;

        if($header->GatewayRspCode != 0){
            throw $this->exceptionMapper->map_gateway_exception($transactionId,$header->GatewayRspCode,$header->GatewayRspMsg);
        }

        return $this->get($transactionId);
    }

    public function charge($amount, $currency, $cardOrToken, $cardHolder=null, $requestMultiUseToken=false, $details=null){
        $this->_checkAmount($amount);
        $this->_checkCurrency($currency);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
            $hpsCreditSale = $xml->createElement('hps:CreditSale');
                $hpsBlock1 = $xml->createElement('hps:Block1');
                    $hpsBlock1->appendChild($xml->createElement('hps:AllowDup','Y'));
                    $hpsBlock1->appendChild($xml->createElement('hps:Amt',$amount));
                    if($cardHolder != null){
                        $hpsBlock1->appendChild($this->_hydrateCardHolderData($cardHolder,$xml));
                    }

                    if($details != null){
                        $hpsBlock1->appendChild($this->_hydrateAdditionalTxnFields($details,$xml));
                    }
                    $cardData = $xml->createElement('hps:CardData');
                    if($cardOrToken instanceOf HpsCreditCard){
                        $cardData->appendChild($this->_hydrateManualEntry($cardOrToken,$xml));
                    }else{
                        $tokenData = $xml->createElement('hps:TokenData');
                        $tokenData->appendChild($xml->createElement('hps:TokenValue',$cardOrToken->tokenValue));
                        $cardData->appendChild($tokenData);
                    }
                    $cardData->appendChild($xml->createElement('hps:TokenRequest',($requestMultiUseToken) ? 'Y' : 'N'));
                $hpsBlock1->appendChild($cardData);
            $hpsCreditSale->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCreditSale);

        return $this->_submitCharge($hpsTransaction,$amount,$currency);
    }

    public function get($transactionId){
        if($transactionId <= 0){
            throw $this->exceptionMapper->map_sdk_exception(HpsSdkCodes::$invalidTransactionId);
        }

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
            $hpsReportTxnDetail = $xml->createElement('hps:ReportTxnDetail');
            $hpsReportTxnDetail->appendChild($xml->createElement('hps:TxnId',$transactionId));
        $hpsTransaction->appendChild($hpsReportTxnDetail);

        $response = $this->doTransaction($hpsTransaction);
        $detail = $response->Transaction->ReportTxnDetail;

        $header = $this->hydrateTransactionHeader($response->Header);
        $result = new HpsReportTransactionDetails($header);
        $result->transactionId = $detail->GatewayTxnId;
        $result->originalTransactionId = (isset($detail->OriginalGatewayTxnId) ? $detail->OriginalGatewayTxnId : null);
        $result->authorizedAmount = (isset($detail->Data->AuthAmt) ? $detail->Data->AuthAmt : null);
        $result->authorizationCode = (isset($detail->Data->AuthCode) ? $detail->Data->AuthCode : null);
        $result->avsResultCode = (isset($detail->Data->AVSRsltCode) ? $detail->Data->AVSRsltCode : null);
        $result->avsResultText = (isset($detail->Data->AVSRsltText) ? $detail->Data->AVSRsltText : null);
        $result->cardType = (isset($detail->Data->CardType) ? $detail->Data->CardType : null);
        $result->maskedCardNumber = (isset($detail->Data->MaskedCardNbr) ? $detail->Data->MaskedCardNbr : null);
        $result->transactionType = (isset($detail->ServiceName) ? HpsTransaction::serviceNameToTransactionType($detail->ServiceName) : null);
        $result->transactionDate = (isset($detail->RspUtcDT) ? $detail->RspUtcDT : null);
        $result->cpcIndicator = (isset($detail->Data->CPCInd) ? $detail->Data->CPCInd : null);
        $result->cvvResultCode = (isset($detail->Data->CVVRsltCode) ? $detail->Data->CVVRsltCode : null);
        $result->cvvResultText = (isset($detail->Data->CVVRsltText) ? $detail->Data->CVVRsltText : null);
        $result->referenceNumber = (isset($detail->Data->RefNbr) ? $detail->Data->RefNbr : null);
        $result->responseCode = (isset($detail->Data->RspCode) ? $detail->Data->RspCode : null);
        $result->responseText = (isset($detail->Data->RspText) ? $detail->Data->RspText : null);

        $tokenizationMessage =  (isset($detail->Data->TokenizationMsg) ? $detail->Data->TokenizationMsg : null);
        if($tokenizationMessage != null){
            $result->tokenData = new HpsTokenData($tokenizationMessage);
        }

        $headerResponseCode =  (isset($response->Header->GatewayRspCode) ? $response->Header->GatewayRspCode : null);
        $dataResponseCode =  (isset($detail->Data->RspCode) ? $detail->Data->RspCode : null);

        if($headerResponseCode != "0" || $dataResponseCode != "00"){
            $exceptions = new HpsChargeExceptions();

            if($headerResponseCode != "0"){
                $message = $response->Header->GatewayRspMsg;
                $exceptions->hpsException = $this->exceptionMapper->map_gateway_exception($result->transactionId,$headerResponseCode,$message);
            }
            if($dataResponseCode != "00"){
                $message = $detail->Data->RspText;
                $exceptions->cardException = $this->exceptionMapper->map_issuer_exception($transactionId,$dataResponseCode,$message);
            }
            $result->exceptions = $exceptions;
        }
        return $result;
    }

    public function listTransactions($startDate, $endDate, $filterBy=null){
        date_default_timezone_set("UTC");
        $dateFormat = 'Y-m-d\TH:i:s.00\Z';
        $current = new DateTime();
        $currentTime = $current->format($dateFormat);

        if($startDate > $currentTime){
            throw $this->exceptionMapper->map_sdk_exception(HpsSdkCodes::$invalidStartDate);
        }
        else if($endDate > $currentTime){
            throw $this->exceptionMapper->map_sdk_exception(HpsSdkCodes::$invalidEndDate);
        }

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
            $hpsReportActivity = $xml->createElement('hps:ReportActivity');
                $hpsReportActivity->appendChild($xml->createElement('hps:RptStartUtcDT',$startDate));
                $hpsReportActivity->appendChild($xml->createElement('hps:RptEndUtcDT',$endDate));
        $hpsTransaction->appendChild($hpsReportActivity);

        $response = $this->doTransaction($hpsTransaction);

        // Gateway Exception
        if($response->Header->GatewayRspCode !=0){
            $transactionId = $response->Header->GatewayTxnId;
            $responseCode = $response->Header->GatewayRspCode;
            $responseMessage = $response->Header->GatewayRspMessage;
            throw $this->exceptionMapper->map_gateway_exception($transactionId,$responseCode,$responseMessage);
        }
        $result = array();
        if($response->Transaction->ReportActivity->Header->TxnCnt == "0"){
            return $result;
        }

        foreach ($response->Transaction->ReportActivity->Details as $charge) {
            if($filterBy != null && $charge->ServiceName != HpsTransaction::transactionTypeToServiceName($filterBy)){
                continue;
            }else{
                $summary = new HpsReportTransactionSummary();
                $summary->transactionId = (isset($charge->GatewayTxnId) ? $charge->GatewayTxnId : null);
                $summary->originalTransactionId = (isset($charge->OriginalGatewayTxnId) ? $charge->OriginalGatewayTxnId : null);
                $summary->maskedCardNumber = (isset($charge->MaskedCardNbr) ? $charge->MaskedCardNbr : null);
                $summary->responseCode = (isset($charge->IssuerRspCode) ? $charge->IssuerRspCode : null);
                $summary->responseText = (isset($charge->IssuerRspText) ? $charge->IssuerRspText : null);

                if($filterBy != null ){
                    $summary->transactionType = (isset($charge->ServiceName) ? HpsTransaction::transactionTypeToServiceName($charge->ServiceName) : null);
                }

                $gwResponseCode = (isset($charge->GatewayRspCode) ? $charge->GatewayRspCode : null);
                $issuerResponseCode  = (isset($charge->IssuerRspCode) ? $charge->IssuerRspCode : null);

                if($gwResponseCode != "0" || $issuerResponseCode != "00"){
                    $exceptions = new HpsChargeExceptions();
                    if($gwResponseCode != "0"){
                        $message = $charge->GatewayRspMsg;
                        $exceptions->hpsException = $this->exceptionMapper->map_gateway_exception($charge->GatewayTxnId, $gwResponseCode, $message);
                    }
                    if($issuerResponseCode != "00"){
                        $message = $charge->IssuerRspText;
                        $exceptions->cardException = $this->exceptionMapper->map_issuer_exception($charge->GatewayTxnId, $issuerResponseCode, $message);
                    }
                    $summary->exceptions = $exceptions;
                }
            }
            $result = $summary;
        }
        return $result;
    }

    public function refund($amount, $currency, $cardOrToken, $cardHolder=null, $details=null){
        $this->_checkAmount($amount);
        $this->_checkCurrency($currency);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
            $hpsCreditReturn = $xml->createElement('hps:CreditReturn');
                $hpsBlock1 = $xml->createElement('hps:Block1');
                    $hpsBlock1->appendChild($xml->createElement('hps:AllowDup','Y'));
                    $hpsBlock1->appendChild($xml->createElement('hps:Amt',$amount));
                    if($cardHolder != null){
                        $hpsBlock1->appendChild($this->_hydrateCardHolderData($cardHolder,$xml));
                    }
                    if($details != null){
                        $hpsBlock1->appendChild($this->_hydrateAdditionalTxnFields($details,$xml));
                    }
                    $cardData = $xml->createElement('hps:CardData');
                    if($cardOrToken instanceOf HpsCreditCard){
                        $cardData->appendChild($this->_hydrateManualEntry($cardOrToken,$xml));
                    }else{
                        $tokenData = $xml->createElement('hps:TokenData');
                        $tokenData->appendChild($xml->createElement('hps:TokenValue',$cardOrToken->tokenValue));
                        $cardData->appendChild($tokenData);
                    }
                $hpsBlock1->appendChild($cardData);
            $hpsCreditReturn->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCreditReturn);
        return $this->_submitRefund($hpsTransaction);
    }

    public function refundTransaction($amount,$currency,$transactionId, $cardHolder=null, $details=null){
        $this->_checkAmount($amount);
        $this->_checkCurrency($currency);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
            $hpsCreditReturn = $xml->createElement('hps:CreditReturn');
                $hpsBlock1 = $xml->createElement('hps:Block1');
                    $hpsBlock1->appendChild($xml->createElement('hps:AllowDup','Y'));
                    $hpsBlock1->appendChild($xml->createElement('hps:Amt',$amount));
                    $hpsBlock1->appendChild($xml->createElement('hps:GatewayTxnId',$transactionId));
                    if($cardHolder != null){
                        $hpsBlock1->appendChild($this->_hydrateCardHolderData($cardHolder,$xml));
                    }
                    if($details != null){
                        $hpsBlock1->appendChild($this->_hydrateAdditionalTxnFields($details,$xml));
                    }
            $hpsCreditReturn->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCreditReturn);
        return $this->_submitRefund($hpsTransaction);
    }

    public function reverse($cardOrToken, $amount, $currency, $details=null){
        $this->_checkAmount($amount);
        $this->_checkCurrency($currency);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
            $hpsCreditReversal = $xml->createElement('hps:CreditReversal');
                $hpsBlock1 = $xml->createElement('hps:Block1');
                    $hpsBlock1->appendChild($xml->createElement('hps:Amt',$amount));
                    if($details != null){
                        $hpsBlock1->appendChild($this->_hydrateAdditionalTxnFields($details,$xml));
                    }
                    $cardData = $xml->createElement('hps:CardData');
                    if($cardOrToken instanceOf HpsCreditCard){
                        $cardData->appendChild($this->_hydrateManualEntry($cardOrToken,$xml));
                    }else{
                        $tokenData = $xml->createElement('hps:TokenData');
                        $tokenData->appendChild($xml->createElement('hps:TokenValue',$cardOrToken->tokenValue));
                        $cardData->appendChild($tokenData);
                    }
                $hpsBlock1->appendChild($cardData);
            $hpsCreditReversal->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCreditReversal);

        return $this->_submitReverse($hpsTransaction);
    }

    public function reverseTransaction($transactionId, $amount,$currency, $details=null){
        $this->_checkAmount($amount);
        $this->_checkCurrency($currency);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
            $hpsCreditReversal = $xml->createElement('hps:CreditReversal');
                $hpsBlock1 = $xml->createElement('hps:Block1');
                    $hpsBlock1->appendChild($xml->createElement('hps:Amt',$amount));
                    $hpsBlock1->appendChild($xml->createElement('hps:GatewayTxnId',$transactionId));
                    if($details != null){
                        $hpsBlock1->appendChild($this->_hydrateAdditionalTxnFields($details,$xml));
                    }
            $hpsCreditReversal->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCreditReversal);

        return $this->_submitReverse($hpsTransaction);
    }

    public function verify($cardOrToken, $cardHolder=null, $requestMultiUseToken=false){
        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
            $hpsCreditAccountVerify = $xml->createElement('hps:CreditAccountVerify');
                $hpsBlock1 = $xml->createElement('hps:Block1');
                    if($cardHolder != null){
                        $hpsBlock1->appendChild($this->_hydrateCardHolderData($cardHolder,$xml));
                    }
                    $cardData = $xml->createElement('hps:CardData');
                    if($cardOrToken instanceOf HpsCreditCard){
                        $cardData->appendChild($this->_hydrateManualEntry($cardOrToken,$xml));
                    }else{
                        $tokenData = $xml->createElement('hps:TokenData');
                        $tokenData->appendChild($xml->createElement('hps:TokenValue',$cardOrToken->tokenValue));
                        $cardData->appendChild($tokenData);
                    }
                    $cardData->appendChild($xml->createElement('hps:TokenRequest',($requestMultiUseToken) ? 'Y' : 'N'));
                $hpsBlock1->appendChild($cardData);
            $hpsCreditAccountVerify->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCreditAccountVerify);

        $response =  $this->doTransaction($hpsTransaction);
        $header = $response->Header;

        if($header->GatewayRspCode != "0"){
            throw $this->exceptionMapper->map_gateway_exception($header->GatewayTxnId,$header->GatewayRspCode,$header->GatewayRspMsg);
        }

        $accountVerify = $response->Transaction->CreditAccountVerify;
        $result = new HpsAccountVerify($this->hydrateTransactionHeader($header));
        $result->transactionId = (isset($accountVerify->GatewayTxnId) ? $accountVerify->GatewayTxnId : null);
        $result->avsResultCode = (isset($accountVerify->AVSRsltCode) ? $accountVerify->AVSRsltCode : null);
        $result->avsResultText = (isset($accountVerify->AVSRsltText) ? $accountVerify->AVSRsltText : null);
        $result->referenceNumber = (isset($accountVerify->RefNbr) ? $accountVerify->RefNbr : null);
        $result->responseCode = (isset($accountVerify->RspCode) ? $accountVerify->RspCode : null);
        $result->responseText = (isset($accountVerify->RspText) ? $accountVerify->RspText : null);
        $result->cardType = (isset($accountVerify->CardType) ? $accountVerify->CardType : null);
        $result->cpcIndicator = (isset($accountVerify->CPCInd) ? $accountVerify->CPCInd : null);
        $result->cvvResultCode = (isset($accountVerify->CVVRsltCode) ? $accountVerify->CVVRsltCode : null);
        $result->cvvResultText = (isset($accountVerify->CVVRsltText) ? $accountVerify->CVVRsltText : null);
        $result->authorizationCode = (isset($accountVerify->AuthCode) ? $accountVerify->AuthCode : null);
        $result->authorizedAmount = (isset($accountVerify->AuthAmt) ? $accountVerify->AuthAmt : null);

        if($result->responseCode != "00" && $result->responseCode != "85"){
            throw $this->exceptionMapper->map_issuer_exception($result->transactionId, $result->responseCode, $result->responseText);
        }
        return $result;
    }

    public function void($transactionId){
        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
            $hpsCreditVoid = $xml->createElement('hps:CreditVoid');
            $hpsCreditVoid->appendChild($xml->createElement('hps:GatewayTxnId',$transactionId));
        $hpsTransaction->appendChild($hpsCreditVoid);

        $response =  $this->doTransaction($hpsTransaction);
        $header = $response->Header;

        if($header->GatewayRspCode != "0"){
            throw $this->exceptionMapper->map_gateway_exception($header->GatewayTxnId,$header->GatewayRspCode,$header->GatewayRspMsg);
        }

        $creditVoid = $response->Transaction->CreditVoid;
        $result = new HpsVoid($this->hydrateTransactionHeader($header));
        $result->transactionId = (isset($creditVoid->GatewayTxnId) ? $creditVoid->GatewayTxnId : null);
        $result->responseCode = "00";
        $result->responseText = "";
        return $result;
    }

    private function  _checkAmount($amount){
        if ($amount <= 0 || $amount == null){
            throw $this->exceptionMapper->map_sdk_exception(HpsSdkCodes::$invalidAmount);
        }
    }

    private function _checkCurrency($currency){
        if ($currency == null or $currency == ""){
            throw $this->exceptionMapper->map_sdk_exception(HpsSdkCodes::$missingCurrency);
        }
        if (strtolower($currency) != "usd"){
            throw $this->exceptionMapper->map_sdk_exception(HpsSdkCodes::$invalidCurrency);
        }
    }

    private function _hydrateAdditionalTxnFields($details,DOMDocument $xml){
        $additionalTxnFields = $xml->createElement('hps:AdditionalTxnFields');
        if($details->memo != null && $details->memo != ""){
            $additionalTxnFields->appendChild($xml->createElement('hps:Description',$details->memo));
        }
        if($details->invoiceNumber != null && $details->invoiceNumber != ""){
            $additionalTxnFields->appendChild($xml->createElement('hps:InvoiceNbr',$details->invoiceNumber));
        }
        if($details->customerId != null && $details->customerId != ""){
            $additionalTxnFields->appendChild($xml->createElement('hps:CustomerID',$details->customerId));
        }
        return $additionalTxnFields;
    }

    private function _hydrateCardHolderData(HpsCardHolder $cardHolder, DOMDocument $xml){
        $cardHolderData = $xml->createElement('hps:CardHolderData');
        $cardHolderData->appendChild($xml->createElement('hps:CardHolderFirstName',$cardHolder->firstName));
        $cardHolderData->appendChild($xml->createElement('hps:CardHolderLastName',$cardHolder->lastName));
        $cardHolderData->appendChild($xml->createElement('hps:CardHolderEmail',$cardHolder->emailAddress));
        $cardHolderData->appendChild($xml->createElement('hps:CardHolderPhone',$cardHolder->phone));
        $cardHolderData->appendChild($xml->createElement('hps:CardHolderAddr',$cardHolder->address->address));
        $cardHolderData->appendChild($xml->createElement('hps:CardHolderCity',$cardHolder->address->city));
        $cardHolderData->appendChild($xml->createElement('hps:CardHolderState',$cardHolder->address->state));
        $cardHolderData->appendChild($xml->createElement('hps:CardHolderZip',$cardHolder->address->zip));

        return $cardHolderData;
    }

    private function _hydrateManualEntry(HpsCreditCard $card,DOMDocument $xml){
        $manualEntry = $xml->createElement('hps:ManualEntry');
        $manualEntry->appendChild($xml->createElement('hps:CardNbr',$card->number));
        $manualEntry->appendChild($xml->createElement('hps:ExpMonth',$card->expMonth));
        $manualEntry->appendChild($xml->createElement('hps:ExpYear',$card->expYear));
        $manualEntry->appendChild($xml->createElement('hps:CVV2',$card->cvv));
        $manualEntry->appendChild($xml->createElement('hps:CardPresent',"N"));
        $manualEntry->appendChild($xml->createElement('hps:ReaderPresent',"N"));

        return $manualEntry;
    }

    private function _processChargeGatewayResponse($responseCode, $responseText, $transactionId, $amount, $currency){
        if($responseCode != 0){
            if($responseCode == 30){
                try{
                    $this->reverseTransaction($transactionId, $amount, $currency);
                }catch (Exception $e){
                    $exception = $this->exceptionMapper->map_sdk_exception(HpsSdkCodes::$reversalErrorAfterGatewayTimeout, $e);
                    $exception->responseCode = $responseCode;
                    $exception->responseText = $responseText;
                    throw $exception;
                }
            }
            $exception = $this->exceptionMapper->map_gateway_exception($transactionId,$responseCode, $responseText);
            $exception->responseCode = $responseCode;
            $exception->responseText = $responseText;
            throw $exception;
        }
    }

    private function _processChargeIssuerResponse($responseCode, $responseText, $transactionId, $amount, $currency){
        if($responseCode == "91"){
            try{
                $this->reverseTransaction($transactionId, $amount, $currency);
            }catch (Exception $e){
                $exception = $this->exceptionMapper->map_sdk_exception(HpsSdkCodes::$reversalErrorAfterIssuerTimeout, $e);
                $exception->responseCode = $responseCode;
                $exception->responseText = $responseText;
                throw $exception;
            }
            $exception = $this->exceptionMapper->map_sdk_exception(HpsSdkCodes::$processingError);
            $exception->responseCode = $responseCode;
            $exception->responseText = $responseText;
            throw $exception;
        }else if($responseCode != "00"){
            $exception = $this->exceptionMapper->map_issuer_exception($transactionId, $responseCode, $responseText);
            $exception->responseCode = $responseCode;
            $exception->responseText = $responseText;
            throw $exception;
        }
    }

    private function _submitAuthorize($transaction, $amount, $currency){
        $response = $this->doTransaction($transaction);
        $avsChecking = new AVSResponseCodeHandler($response,$this,$this->config);
        $header = $response->Header;
        $this->_processChargeGatewayResponse($header->GatewayRspCode,$header->GatewayRspMsg,$header->GatewayTxnId,$amount,$currency);

        $authResponse = $response->Transaction->CreditAuth;
        $this->_processChargeIssuerResponse($authResponse->RspCode,$authResponse->RspText,$authResponse->GatewayTxnId,$amount,$currency);

        $result = new HpsAuthorization($this->hydrateTransactionHeader($header));
        $result->transactionId = $header->GatewayTxnId;
        $result->authorizedAmount = (isset($authResponse->AuthAmt) ? $authResponse->AuthAmt : null);
        $result->authorizationCode = (isset($authResponse->AuthCode) ? $authResponse->AuthCode : null);
        $result->avsResultCode = (isset($authResponse->AVSRsltCode) ? $authResponse->AVSRsltCode : null);
        $result->avsResultText = (isset($authResponse->AVSRsltText) ? $authResponse->AVSRsltText : null);
        $result->cardType = (isset($authResponse->CardType) ? $authResponse->CardType : null);
        $result->cpcIndicator = (isset($authResponse->CPCInd) ? $authResponse->CPCInd : null);
        $result->cvvResultCode = (isset($authResponse->CVVRsltCode) ? $authResponse->CVVRsltCode : null);
        $result->cvvResultText = (isset($authResponse->CVVRsltText) ? $authResponse->CVVRsltText : null);
        $result->referenceNumber = (isset($authResponse->RefNbr) ? $authResponse->RefNbr : null);
        $result->responseCode = (isset($authResponse->RspCode) ? $authResponse->RspCode : null);
        $result->responseText = (isset($authResponse->RspText) ? $authResponse->RspText : null);

        if(isset($header->TokenData) && is_object($header->TokenData)){
            $result->tokenData = new HpsTokenData();
            $result->tokenData->responseCode = $header->TokenData->TokenRspCode;
            $result->tokenData->responseMessage = $header->TokenData->TokenRspMsg;
            $result->tokenData->tokenValue = $header->TokenData->TokenValue;
        }

        return $result;
    }

    private function _submitCharge($transaction, $amount, $currency){
        $response = $this->doTransaction($transaction);
        $avsChecking = new AVSResponseCodeHandler($response,$this,$this->config);
        $header = $response->Header;
        $this->_processChargeGatewayResponse($header->GatewayRspCode,$header->GatewayRspMsg,$header->GatewayTxnId,$amount,$currency);

        $creditSaleRsp = $response->Transaction->CreditSale;
        $this->_processChargeIssuerResponse($creditSaleRsp->RspCode,$creditSaleRsp->RspText,$creditSaleRsp->GatewayTxnId,$amount,$currency);

        $result = new HpsCharge($this->hydrateTransactionHeader($header));
        $result->transactionId = $header->GatewayTxnId;
        $result->authorizedAmount = (isset($creditSaleRsp->AuthAmt) ? $creditSaleRsp->AuthAmt : null);
        $result->authorizationCode = (isset($creditSaleRsp->AuthCode) ? $creditSaleRsp->AuthCode : null);
        $result->avsResultCode = (isset($creditSaleRsp->AVSRsltCode) ? $creditSaleRsp->AVSRsltCode : null);
        $result->avsResultText = (isset($creditSaleRsp->AVSRsltText) ? $creditSaleRsp->AVSRsltText : null);
        $result->cardType = (isset($creditSaleRsp->CardType) ? $creditSaleRsp->CardType : null);
        $result->cpcIndicator = (isset($creditSaleRsp->CPCInd) ? $creditSaleRsp->CPCInd : null);
        $result->cvvResultCode = (isset($creditSaleRsp->CVVRsltCode) ? $creditSaleRsp->CVVRsltCode : null);
        $result->cvvResultText = (isset($creditSaleRsp->CVVRsltText) ? $creditSaleRsp->CVVRsltText : null);
        $result->referenceNumber = (isset($creditSaleRsp->RefNbr) ? $creditSaleRsp->RefNbr : null);
        $result->responseCode = (isset($creditSaleRsp->RspCode) ? $creditSaleRsp->RspCode : null);
        $result->responseText = (isset($creditSaleRsp->RspText) ? $creditSaleRsp->RspText : null);

        if(isset($header->TokenData) && is_object($header->TokenData)){
            $result->tokenData = new HpsTokenData();
            $result->tokenData->responseCode = $header->TokenData->TokenRspCode;
            $result->tokenData->responseMessage = $header->TokenData->TokenRspMsg;
            $result->tokenData->tokenValue = $header->TokenData->TokenValue;
        }

        return $result;
    }

    private function _submitRefund($transaction){
        $response = $this->doTransaction($transaction);
        $header = $response->Header;

        if($header->GatewayRspCode != "0"){
            throw $this->exceptionMapper->map_gateway_exception($header->GatewayTxnId,$header->GatewayRspCode,$header->GatewayRspMsg);
        }

        $result = new HpsRefund($this->hydrateTransactionHeader($header));
        $result->transactionId = $header->GatewayTxnId;
        $result->responseCode = "00";
        $result->responseText = "";

        return $result;
    }

    private function _submitReverse($transaction){
        $response = $this->doTransaction($transaction);
        $header = $response->Header;

        if($header->GatewayRspCode != "0"){
            throw $this->exceptionMapper->map_gateway_exception($header->GatewayTxnId,$header->GatewayRspCOde,$header->GatewayRspMsg);
        }

        $reversal = (isset($response->Transaction->CreditReversal) ? $response->Transaction->CreditReversal : null);
        $result = new HpsReversal($this->hydrateTransactionHeader($header));
        $result->transactionId = (isset($header->GatewayTxnId) ? $header->GatewayTxnId : null);
        $result->avsResultCode = (isset($reversal->AVSRsltCode) ? $reversal->AVSRsltCode : null);
        $result->avsResultText = (isset($reversal->AVSRsltText) ? $reversal->AVSRsltText : null);
        $result->cpcIndicator = (isset($reversal->CPCInd) ? $reversal->CPCInd : null);
        $result->cvvResultCode = (isset($reversal->CVVRsltCode) ? $reversal->CVVRsltCode : null);
        $result->cvvResultText = (isset($reversal->CVVRsltText) ? $reversal->CVVRsltText : null);
        $result->referenceNumber = (isset($reversal->RefNbr) ? $reversal->RefNbr : null);
        $result->responseCode = (isset($reversal->RspCode) ? $reversal->RspCode : null);
        $result->responseText = (isset($reversal->RspText) ? $reversal->RspText : null);
        return $result;
    }
} 
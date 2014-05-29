<?php

class HpsCreditService extends HpsService{

    public function authorize($amount, $currency, $cardOrToken, $cardHolder=null, $requestMultiUseToken=false, $details=null, $txnDescriptor=null){
        Validation::checkCurrency($currency);
        $this->_currency = $currency;
        $this->_amount = Validation::checkAmount($amount);

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
                    if($txnDescriptor != null && $txnDescriptor != ''){
                        $hpsBlock1->appendChild($xml->createElement('hps:TxnDescriptor',$txnDescriptor));
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
        return $this->_submitTransaction($hpsTransaction, 'CreditAuth', (isset($details->clientTxnId) ? $details->clientTxnId : null));
    }

    public function capture($transactionId, $amount=null, $gratuity=null){
        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
            $hpsCreditAddToBatch = $xml->createElement('hps:CreditAddToBatch');
                $hpsCreditAddToBatch->appendChild($xml->createElement('hps:GatewayTxnId',$transactionId));
                if($amount != null){
                    $amount = sprintf("%0.2f",round($amount,3));
                    $hpsCreditAddToBatch->appendChild($xml->createElement('hps:Amt',$amount));
                }
                if($gratuity != null){
                    $hpsCreditAddToBatch->appendChild($xml->createElement('hps:GratuityAmtInfo',$gratuity));
                }
        $hpsTransaction->appendChild($hpsCreditAddToBatch);
        $response = $this->doTransaction($hpsTransaction);
        $header = $response->Header;

        if($header->GatewayRspCode != 0){
            throw HpsExceptionMapper::map_gateway_exception($transactionId,$header->GatewayRspCode,$header->GatewayRspMsg);
        }

        return $this->get($transactionId);
    }

    public function charge($amount, $currency, $cardOrToken, $cardHolder=null, $requestMultiUseToken=false, $details=null, $txnDescriptor=null){
        Validation::checkCurrency($currency);
        $this->_currency = $currency;
        $this->_amount = Validation::checkAmount($amount);

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
                    if($txnDescriptor != null && $txnDescriptor != ''){
                        $hpsBlock1->appendChild($xml->createElement('hps:TxnDescriptor',$txnDescriptor));
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

        return $this->_submitTransaction($hpsTransaction,'CreditSale',(isset($details->clientTxnId) ? $details->clientTxnId : null));
    }

    public function get($transactionId){
        if($transactionId <= 0){
            throw HpsExceptionMapper::map_sdk_exception(HpsSdkCodes::$invalidTransactionId);
        }

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
            $hpsReportTxnDetail = $xml->createElement('hps:ReportTxnDetail');
            $hpsReportTxnDetail->appendChild($xml->createElement('hps:TxnId',$transactionId));
        $hpsTransaction->appendChild($hpsReportTxnDetail);

        return $this->_submitTransaction($hpsTransaction,'ReportTxnDetail');
    }

    public function listTransactions($startDate, $endDate, $filterBy=null){
        $this->_filterBy = $filterBy;
        date_default_timezone_set("UTC");
        $dateFormat = 'Y-m-d\TH:i:s.00\Z';
        $current = new DateTime();
        $currentTime = $current->format($dateFormat);

        if($startDate > $currentTime){
            throw HpsExceptionMapper::map_sdk_exception(HpsSdkCodes::$invalidStartDate);
        }
        else if($endDate > $currentTime){
            throw HpsExceptionMapper::map_sdk_exception(HpsSdkCodes::$invalidEndDate);
        }

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
            $hpsReportActivity = $xml->createElement('hps:ReportActivity');
                $hpsReportActivity->appendChild($xml->createElement('hps:RptStartUtcDT',$startDate));
                $hpsReportActivity->appendChild($xml->createElement('hps:RptEndUtcDT',$endDate));
        $hpsTransaction->appendChild($hpsReportActivity);

        return $this->_submitTransaction($hpsTransaction, 'ReportActivity');
    }

    public function refund($amount, $currency, $cardOrToken, $cardHolder=null, $details=null){
        Validation::checkCurrency($currency);
        $this->_currency = $currency;
        $this->_amount = Validation::checkAmount($amount);

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
        return $this->_submitTransaction($hpsTransaction,'CreditReturn',(isset($details->clientTxnId) ? $details->clientTxnId : null));
    }

    public function refundTransaction($amount,$currency,$transactionId, $cardHolder=null, $details=null){
        Validation::checkCurrency($currency);
        $this->_currency = $currency;
        $this->_amount = Validation::checkAmount($amount);

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
        return $this->_submitTransaction($hpsTransaction,'CreditReturn',(isset($details->clientTxnId) ? $details->clientTxnId : null));
    }

    public function reverse($cardOrToken, $amount, $currency, $details=null){
        Validation::checkCurrency($currency);
        $this->_currency = $currency;
        $this->_amount = Validation::checkAmount($amount);

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

        return $this->_submitTransaction($hpsTransaction,'CreditReversal',(isset($details->clientTxnId) ? $details->clientTxnId : null));
    }

    public function reverseTransaction($transactionId, $amount,$currency, $details=null){
        Validation::checkCurrency($currency);
        $this->_currency = $currency;
        $this->_amount = Validation::checkAmount($amount);

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

        return $this->_submitTransaction($hpsTransaction,'CreditReversal',(isset($details->clientTxnId) ? $details->clientTxnId : null));
    }

    public function verify($cardOrToken, $cardHolder=null, $requestMultiUseToken=false,$clientTransactionId=null){
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
        return $this->_submitTransaction($hpsTransaction,'CreditAccountVerify',$clientTransactionId);

    }

    public function void($transactionId,$clientTransactionId=null){
        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
            $hpsCreditVoid = $xml->createElement('hps:CreditVoid');
            $hpsCreditVoid->appendChild($xml->createElement('hps:GatewayTxnId',$transactionId));
        $hpsTransaction->appendChild($hpsCreditVoid);

        return $this->_submitTransaction($hpsTransaction,'CreditVoid',$clientTransactionId);
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
        $cardHolderData->appendChild($xml->createElement('hps:CardHolderEmail',$cardHolder->email));
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

    private function _submitTransaction($transaction, $txnType, $clientTxnId=null){
        $response = $this->doTransaction($transaction,$clientTxnId);

        if($txnType == 'CreditSale' || $txnType == 'CreditAuth'){
            $args = array(
                'object' => $this,
                'reversalMethod' => 'reverseTransaction',
                'amount' => $this->_amount,
                'currency' => $this->_currency,
            );
            Validation::checkGatewayResponse($response,$txnType,$args);
            Validation::checkTransactionResponse($response,$txnType,$args);
        }else{
            Validation::checkGatewayResponse($response,$txnType);
            Validation::checkTransactionResponse($response,$txnType);
        }

        if($txnType == 'ReportTxnDetail'){ $rvalue = HpsReportTransactionDetails::fromDict($response,$txnType);}
        elseif($txnType == 'ReportActivity'){ $rvalue = HpsReportTransactionSummary::fromDict($response,$txnType,$this->_filterBy);}
        elseif($txnType == 'CreditSale'){ $rvalue = HpsCharge::fromDict($response,$txnType);}
        elseif($txnType == 'CreditAccountVerify'){ $rvalue = HpsAccountVerify::fromDict($response,$txnType);}
        elseif($txnType == 'CreditAuth'){ $rvalue = HpsAuthorization::fromDict($response,$txnType);}
        elseif($txnType == 'CreditReturn'){ $rvalue = HpsRefund::fromDict($response,$txnType);}
        elseif($txnType == 'CreditReversal'){ $rvalue = HpsReversal::fromDict($response,$txnType);}
        elseif($txnType == 'CreditVoid'){ $rvalue = HpsVoid::fromDict($response,$txnType);}

        return $rvalue;
    }
} 
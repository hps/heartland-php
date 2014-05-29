<?php

class HpsCheckService extends HpsService {

    /**
     * A Sale transaction is used to process transactions using bank account information as the payment method.
     * The transaction service can be used to perform a Sale or Return transaction by indicating the Check Action.
     *
     * <b>NOTE:</b> The Portico Gateway supports both GETI and HPS Colonnade for processing check transactions. While
     * the available services are the same regardless of the check processor, the services may have different behaviors.
     * For example, GETI-processed Check Sale transactions support the ability to override a Check Sale transaction
     * already presented as well as the ability to verify a check.
     * @param string $action Type of Check Action (Sale, Return, Override)
     * @param string $check The Check information.
     * @param string $amount The amount of the sale.
     *
     * @returns HpsCheckSale
     */
    public function sale($action, HpsCheck $check, $amount, $clientTransactionId=null){
        $amount = Validation::checkAmount($amount);

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
            $hpsCheckSale = $xml->createElement('hps:CheckSale');
                $hpsBlock1 = $xml->createElement('hps:Block1');
                $hpsBlock1->appendChild($xml->createElement('hps:Amt',$amount));
                $hpsBlock1->appendChild($this->_hydrateCheckData($check,$xml));
                $hpsBlock1->appendChild($xml->createElement('hps:CheckAction',strtoupper($action)));
                $hpsBlock1->appendChild($xml->createElement('hps:SECCode',strtoupper($check->secCode)));
                if($check->checkType != null){
                    $hpsBlock1->appendChild($xml->createElement('hps:CheckType',strtoupper($check->checkType)));
                }
                if($check->dataEntryMode != null){
                    $hpsBlock1->appendChild($xml->createElement('hps:DataEntryMode',$check->dataEntryMode));
                }
                if($check->checkHolder != null){
                    $hpsBlock1->appendChild($this->_hydrateConsumerInfo($check,$xml));
                }
            $hpsCheckSale->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCheckSale);
        return $this->_submitTransaction($hpsTransaction,'CheckSale',$clientTransactionId);
    }

    /**
     * A <b>Void</b> transaction is used to cancel a previously successful sale transaction. The original sale transaction
     * can be identified by the GatewayTxnid of the original or by the ClientTxnId of the original if provided on the
     * original Sale Transaction.
     *
     * @param null $transactionId
     * @param null $clientTransactionId
     */
    public function void($transactionId = null, $clientTransactionId = null){
        if( ($transactionId == null && $clientTransactionId == null) || ($transactionId != null && $clientTransactionId != null) ){
            throw new HpsException('Please provide either a transaction id or a client transaction id');
        }

        $xml = new DOMDocument();
        $hpsTransaction = $xml->createElement('hps:Transaction');
            $hpsCheckVoid = $xml->createElement('hps:CheckVoid');
                $hpsBlock1 = $xml->createElement('hps:Block1');
                    if($transactionId != null){
                        $hpsBlock1->appendChild($xml->createElement('hps:GatewayTxnId',$transactionId));
                    }
                    if($clientTransactionId != null){
                        $hpsBlock1->appendChild($xml->createElement('hps:ClientTxnId',$clientTransactionId));
                    }
            $hpsCheckVoid->appendChild($hpsBlock1);
        $hpsTransaction->appendChild($hpsCheckVoid);
        return $this->_submitTransaction($hpsTransaction,'CheckVoid');
    }

    private function _hydrateCheckData(HpsCheck $check,DOMDocument $xml){
        $checkData = $xml->createElement('hps:AccountInfo');
        if($check->accountNumber != null){
            $checkData->appendChild($xml->createElement('hps:AccountNumber',$check->accountNumber));
        }
        if($check->checkNumber != null){
            $checkData->appendChild($xml->createElement('hps:CheckNumber',$check->checkNumber));
        }
        if($check->micrNumber != null){
            $checkData->appendChild($xml->createElement('hps:MICRData',$check->micrNumber));
        }
        if($check->routingNumber != null){
            $checkData->appendChild($xml->createElement('hps:RoutingNumber',$check->routingNumber));
        }

        if ($check->accountType != null) {
            $checkData->appendChild($xml->createElement('hps:AccountType',strtoupper($check->accountType)));
        }

        return $checkData;
    }

    private function _hydrateConsumerInfo(HpsCheck $check, DOMDocument $xml){
        $consumerInfo = $xml->createElement('hps:ConsumerInfo');
        if($check->checkHolder->address != null){
            $consumerInfo->appendChild($xml->createElement('hps:Address1',$check->checkHolder->address->address));
            $consumerInfo->appendChild($xml->createElement('hps:City',$check->checkHolder->address->city));
            $consumerInfo->appendChild($xml->createElement('hps:State',$check->checkHolder->address->state));
            $consumerInfo->appendChild($xml->createElement('hps:Zip',$check->checkHolder->address->zip));
        }

        if($check->checkHolder->checkName != null){
            $consumerInfo->appendChild($xml->createElement('hps:CheckName',$check->checkHolder->checkName));
        }
        if($check->checkHolder->courtesyCard != null){
            $consumerInfo->appendChild($xml->createElement('hps:CourtesyCard',$check->checkHolder->courtesyCard));
        }
        if($check->checkHolder->dlNumber != null){
            $consumerInfo->appendChild($xml->createElement('hps:DLNumber',$check->checkHolder->dlNumber));
        }
        if($check->checkHolder->dlState != null){
            $consumerInfo->appendChild($xml->createElement('hps:DLState',$check->checkHolder->dlState));
        }
        if($check->checkHolder->email != null){
            $consumerInfo->appendChild($xml->createElement('hps:EmailAddress',$check->checkHolder->email));
        }
        if($check->checkHolder->firstName != null){
            $consumerInfo->appendChild($xml->createElement('hps:FirstName',$check->checkHolder->firstName));
        }
        if($check->checkHolder->lastName != null){
            $consumerInfo->appendChild($xml->createElement('hps:LastName',$check->checkHolder->lastName));
        }
        if($check->checkHolder->phone != null){
            $consumerInfo->appendChild($xml->createElement('hps:PhoneNumber',$check->checkHolder->phone));
        }

        return $consumerInfo;
    }

    private function _submitTransaction($transaction,$txnType, $clientTransactionId = null){
        $rsp = $this->doTransaction($transaction, $clientTransactionId);
        Validation::checkGatewayResponse($rsp,$txnType);
        $response = HpsCheckResponse::fromDict($rsp,$txnType);

        if($response->responseCode != 0){
            throw new HpsCheckException($rsp->Header->GatewayTxnId,$response->details,$response->responseCode,$response->responseText);
        }

        return $response;
    }
}
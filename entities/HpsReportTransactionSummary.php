<?php

class HpsReportTransactionSummary extends HpsTransaction{
    public  $amount                 = null,
            $originalTransactionId  = null,
            $maskedCardNumber       = null,
            $transactionType        = null,
            $transactionDate        = null,
            $exceptions             = null;

    public static function fromDict($rsp,$filterBy){
        $transactions = array();
        if($rsp->Transaction->ReportActivity->Header->TxnCnt == "0"){
            return $transactions;
        }
        $summary = null;
        $serviceName = (isset($filterBy) ? HpsTransaction::transactionTypeToServiceName($filterBy) : null);
        foreach ($rsp->Transaction->ReportActivity->Details as $charge) {
            if($filterBy == null || $charge->ServiceName != $serviceName){
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
                        $exceptions->hpsException = HpsExceptionMapper::map_gateway_exception($charge->GatewayTxnId, $gwResponseCode, $message);
                    }
                    if($issuerResponseCode != "00"){
                        $message = $charge->IssuerRspText;
                        $exceptions->cardException = HpsExceptionMapper::map_issuer_exception($charge->GatewayTxnId, $issuerResponseCode, $message);
                    }
                    $summary->exceptions = $exceptions;
                }
            }
            $transactions = $summary;
        }
        return $transactions;
    }
} 
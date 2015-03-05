<?php


class HpsReportTransactionDetails extends HpsAuthorization {
    public  $originalTransactionId  = null,
            $maskedCardNumber       = null,
            $transactionType        = null,
            $transactionDate        = null,
            $exceptions             = null;

    public static function fromDict($rsp,$txnType){
        $reportResponse = $rsp->Transaction->$txnType;

        $details = new HpsReportTransactionDetails();
        $details->transactionId = (string)$reportResponse->GatewayTxnId;
        $details->originalTransactionId = (isset($reportResponse->OriginalGatewayTxnId) ? (string)$reportResponse->OriginalGatewayTxnId : null);
        $details->authorizedAmount = (isset($reportResponse->Data->AuthAmt) ? (string)$reportResponse->Data->AuthAmt : null);
        $details->authorizationCode = (isset($reportResponse->Data->AuthCode) ? (string)$reportResponse->Data->AuthCode : null);
        $details->avsResultCode = (isset($reportResponse->Data->AVSRsltCode) ? (string)$reportResponse->Data->AVSRsltCode : null);
        $details->avsResultText = (isset($reportResponse->Data->AVSRsltText) ? (string)$reportResponse->Data->AVSRsltText : null);
        $details->cardType = (isset($reportResponse->Data->CardType) ? (string)$reportResponse->Data->CardType : null);
        $details->maskedCardNumber = (isset($reportResponse->Data->MaskedCardNbr) ? (string)$reportResponse->Data->MaskedCardNbr : null);
        $details->transactionType = (isset($reportResponse->ServiceName) ? HpsTransaction::serviceNameToTransactionType((string)$reportResponse->ServiceName) : null);
        $details->transactionDate = (isset($reportResponse->RspUtcDT) ? (string)$reportResponse->RspUtcDT : null);
        $details->cpcIndicator = (isset($reportResponse->Data->CPCInd) ? (string)$reportResponse->Data->CPCInd : null);
        $details->cvvResultCode = (isset($reportResponse->Data->CVVRsltCode) ? (string)$reportResponse->Data->CVVRsltCode : null);
        $details->cvvResultText = (isset($reportResponse->Data->CVVRsltText) ? (string)$reportResponse->Data->CVVRsltText : null);
        $details->referenceNumber = (isset($reportResponse->Data->RefNbr) ? (string)$reportResponse->Data->RefNbr : null);
        $details->responseCode = (isset($reportResponse->Data->RspCode) ? (string)$reportResponse->Data->RspCode : null);
        $details->responseText = (isset($reportResponse->Data->RspText) ? (string)$reportResponse->Data->RspText : null);

        $tokenizationMessage =  (isset($reportResponse->Data->TokenizationMsg) ? (string)$reportResponse->Data->TokenizationMsg : null);
        if($tokenizationMessage != null){
            $details->tokenData = new HpsTokenData($tokenizationMessage);
        }

        $headerResponseCode =  (isset($rsp->Header->GatewayRspCode) ? (string)$rsp->Header->GatewayRspCode : null);
        $dataResponseCode =  (isset($reportResponse->Data->RspCode) ? (string)$reportResponse->Data->RspCode : null);

        if($headerResponseCode != "0" || $dataResponseCode != "00"){
            $exceptions = new HpsChargeExceptions();

            if($headerResponseCode != "0"){
                $message = (string)$rsp->Header->GatewayRspMsg;
                $exceptions->hpsException = HpsExceptionMapper::map_gateway_exception((string)$rsp->Header->GatewayTxnId,$headerResponseCode,$message);
            }
            if($dataResponseCode != "00"){
                $message = (string)$reportResponse->Data->RspText;
                $exceptions->cardException = HpsExceptionMapper::map_issuer_exception((string)$rsp->Header->GatewayTxnId,$dataResponseCode,$message);
            }
            $details->exceptions = $exceptions;
        }

        return $details;
    }
} 

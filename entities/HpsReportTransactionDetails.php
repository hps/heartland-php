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
        $details->transactionId = $reportResponse->GatewayTxnId;
        $details->originalTransactionId = (isset($reportResponse->OriginalGatewayTxnId) ? $reportResponse->OriginalGatewayTxnId : null);
        $details->authorizedAmount = (isset($reportResponse->Data->AuthAmt) ? $reportResponse->Data->AuthAmt : null);
        $details->authorizationCode = (isset($reportResponse->Data->AuthCode) ? $reportResponse->Data->AuthCode : null);
        $details->avsResultCode = (isset($reportResponse->Data->AVSRsltCode) ? $reportResponse->Data->AVSRsltCode : null);
        $details->avsResultText = (isset($reportResponse->Data->AVSRsltText) ? $reportResponse->Data->AVSRsltText : null);
        $details->cardType = (isset($reportResponse->Data->CardType) ? $reportResponse->Data->CardType : null);
        $details->maskedCardNumber = (isset($reportResponse->Data->MaskedCardNbr) ? $reportResponse->Data->MaskedCardNbr : null);
        $details->transactionType = (isset($reportResponse->ServiceName) ? HpsTransaction::serviceNameToTransactionType($reportResponse->ServiceName) : null);
        $details->transactionDate = (isset($reportResponse->RspUtcDT) ? $reportResponse->RspUtcDT : null);
        $details->cpcIndicator = (isset($reportResponse->Data->CPCInd) ? $reportResponse->Data->CPCInd : null);
        $details->cvvResultCode = (isset($reportResponse->Data->CVVRsltCode) ? $reportResponse->Data->CVVRsltCode : null);
        $details->cvvResultText = (isset($reportResponse->Data->CVVRsltText) ? $reportResponse->Data->CVVRsltText : null);
        $details->referenceNumber = (isset($reportResponse->Data->RefNbr) ? $reportResponse->Data->RefNbr : null);
        $details->responseCode = (isset($reportResponse->Data->RspCode) ? $reportResponse->Data->RspCode : null);
        $details->responseText = (isset($reportResponse->Data->RspText) ? $reportResponse->Data->RspText : null);

        $tokenizationMessage =  (isset($reportResponse->Data->TokenizationMsg) ? $reportResponse->Data->TokenizationMsg : null);
        if($tokenizationMessage != null){
            $details->tokenData = new HpsTokenData($tokenizationMessage);
        }

        $headerResponseCode =  (isset($rsp->Header->GatewayRspCode) ? $rsp->Header->GatewayRspCode : null);
        $dataResponseCode =  (isset($reportResponse->Data->RspCode) ? $reportResponse->Data->RspCode : null);

        if($headerResponseCode != "0" || $dataResponseCode != "00"){
            $exceptions = new HpsChargeExceptions();

            if($headerResponseCode != "0"){
                $message = $rsp->Header->GatewayRspMsg;
                $exceptions->hpsException = HpsExceptionMapper::map_gateway_exception($rsp->Header->GatewayTxnId,$headerResponseCode,$message);
            }
            if($dataResponseCode != "00"){
                $message = $reportResponse->Data->RspText;
                $exceptions->cardException = HpsExceptionMapper::map_issuer_exception($rsp->Header->GatewayTxnId,$dataResponseCode,$message);
            }
            $details->exceptions = $exceptions;
        }

        return $details;
    }
} 
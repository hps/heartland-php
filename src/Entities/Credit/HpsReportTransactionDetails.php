<?php

class HpsReportTransactionDetails extends HpsAuthorization
{
    public $issuerTransactionId   = null;
    public $issuerValidationCode  = null;
    public $originalTransactionId = null;
    public $maskedCardNumber      = null;
    public $settlementAmount      = null;
    public $transactionType       = null;
    public $transactionUTCDate    = null;
    public $exceptions            = null;
    public $memo                  = null;
    public $invoiceNumber         = null;
    public $customerId            = null;
    public $transactionStatus     = null;

    public static function fromDict($rsp, $txnType, $returnType = 'HpsReportTransactionDetails')
    {
        $reportResponse = $rsp->Transaction->$txnType;

        $details = parent::fromDict($rsp, $txnType, $returnType);
        $details->originalTransactionId = (isset($reportResponse->OriginalGatewayTxnId) ? (string)$reportResponse->OriginalGatewayTxnId : null);
        $details->authorizedAmount = (isset($reportResponse->Data->AuthAmt) ? (string)$reportResponse->Data->AuthAmt : null);
        $details->maskedCardNumber = (isset($reportResponse->Data->MaskedCardNbr) ? (string)$reportResponse->Data->MaskedCardNbr : null);
        $details->authorizationCode = (isset($reportResponse->Data->AuthCode) ? (string)$reportResponse->Data->AuthCode : null);
        $details->avsResultCode = (isset($reportResponse->Data->AVSRsltCode) ? (string)$reportResponse->Data->AVSRsltCode : null);
        $details->avsResultText = (isset($reportResponse->Data->AVSRsltText) ? (string)$reportResponse->Data->AVSRsltText : null);
        $details->cardType = (isset($reportResponse->Data->CardType) ? (string)$reportResponse->Data->CardType : null);
        $details->descriptor = (isset($reportResponse->Data->TxnDescriptor) ? (string)$reportResponse->Data->TxnDescriptor : null);
        $details->transactionType = (isset($reportResponse->ServiceName) ? HpsTransaction::serviceNameToTransactionType((string)$reportResponse->ServiceName) : null);
        $details->transactionUTCDate = (isset($reportResponse->RspUtcDT) ? (string)$reportResponse->RspUtcDT : null);
        $details->cpcIndicator = (isset($reportResponse->Data->CPCInd) ? (string)$reportResponse->Data->CPCInd : null);
        $details->cvvResultCode = (isset($reportResponse->Data->CVVRsltCode) ? (string)$reportResponse->Data->CVVRsltCode : null);
        $details->cvvResultText = (isset($reportResponse->Data->CVVRsltText) ? (string)$reportResponse->Data->CVVRsltText : null);
        $details->referenceNumber = (isset($reportResponse->Data->RefNbr) ? (string)$reportResponse->Data->RefNbr : null);
        $details->responseCode = (isset($reportResponse->Data->RspCode) ? (string)$reportResponse->Data->RspCode : null);
        $details->responseText = (isset($reportResponse->Data->RspText) ? (string)$reportResponse->Data->RspText : null);
        $details->transactionStatus = (isset($reportResponse->Data->TxnStatus) ? (string)$reportResponse->Data->TxnStatus : null);

        if (isset($reportResponse->Data->TokenizationMsg)) {
            $details->tokenData = new HpsTokenData();
            $details->tokenData->responseMessage = (string)$reportResponse->Data->TokenizationMsg;
        }

        if (isset($reportResponse->Data->AdditionalTxnFields)) {
            $additionalTxnFields = $reportResponse->Data->AdditionalTxnFields;
            $details->memo = (isset($additionalTxnFields->Description) ? (string)$additionalTxnFields->Description : null);
            $details->invoiceNumber = (isset($additionalTxnFields->InvoiceNbr) ? (string)$additionalTxnFields->InvoiceNbr : null);
            $details->customerId = (isset($additionalTxnFields->CustomerId) ? (string)$additionalTxnFields->CustomerId : null);
        }

        if ((string)$reportResponse->Data->RspCode != '00') {
            if ($details->exceptions == null) {
                $details->exceptions = new HpsChargeExceptions();
            }

            $details->exceptions->issuerException = HpsIssuerResponseValidation::getException(
                (string)$rsp->Header->GatewayTxnId,
                (string)$reportResponse->Data->RspCode,
                (string)$reportResponse->Data->RspText
            );
        }

        return $details;
    }
}

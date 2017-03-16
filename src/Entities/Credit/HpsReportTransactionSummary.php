<?php

/**
 * Class HpsReportTransactionSummary
 */
class HpsReportTransactionSummary extends HpsTransaction
{
    public $amount                = null;
    public $settlementAmount      = null;
    public $originalTransactionId = null;
    public $maskedCardNumber      = null;
    public $transactionType       = null;
    public $transactionUTCDate    = null;
    public $exceptions            = null;
    /**
     * @param        $rsp
     * @param        $txnType
     * @param null   $filterBy
     * @param string $returnType
     *
     * @return array
     */
    public static function fromDict($rsp, $txnType, $filterBy = null, $returnType = 'HpsReportTransactionSummary')
    {
        $transactions = array();

        if ((string)$rsp->Transaction->ReportActivity->Header->TxnCnt == "0") {
            return $transactions;
        }

        if ($filterBy != null && is_string($filterBy)) {
            $filterBy = HpsTransaction::serviceNameToTransactionType($filterBy);
        }

        $summary = null;
        $serviceName = (isset($filterBy) ? HpsTransaction::transactionTypeToServiceName($filterBy) : null);

        foreach ($rsp->Transaction->ReportActivity->Details as $charge) {
            if (isset($serviceName) && $serviceName != (string)$charge->ServiceName) {
                continue;
            }

            $summary = new HpsReportTransactionSummary();

            // Hydrate the header
            $summary->_header = new HpsTransactionHeader();
            $summary->_header->gatewayResponseCode = (string)$charge->GatewayRspCode;
            $summary->_header->gatewayResponseMessage = (string)$charge->GatewayRspMsg;

            $summary->transactionId = (string)$charge->GatewayTxnId;

            $summary->originalTransactionId = (isset($charge->OriginalGatewayTxnId) ? (string)$charge->OriginalGatewayTxnId : null);
            $summary->maskedCardNumber = (isset($charge->MaskedCardNbr) ? (string)$charge->MaskedCardNbr : null);
            $summary->responseCode = (isset($charge->IssuerRspCode) ? (string)$charge->IssuerRspCode : null);
            $summary->responseText = (isset($charge->IssuerRspText) ? (string)$charge->IssuerRspText : null);
            $summary->amount = (isset($charge->Amt) ? (string)$charge->Amt : null);
            $summary->settlementAmount = (isset($charge->SettlementAmt) ? (string)$charge->SettlementAmt : null);
            $summary->transactionType = (isset($charge->ServiceName) ? HpsTransaction::serviceNameToTransactionType((string)$charge->ServiceName) : null);
            $summary->transactionUTCDate = (isset($charge->TxnUtcDT) ? (string)$charge->TxnUtcDT : null);

            $gwResponseCode = (isset($charge->GatewayRspCode) ? (string)$charge->GatewayRspCode : null);
            $issuerResponseCode  = (isset($charge->IssuerRspCode) ? (string)$charge->IssuerRspCode : null);

            if ($gwResponseCode != "0" || $issuerResponseCode != "00") {
                $exceptions = new HpsChargeExceptions();
                if ($gwResponseCode != "0") {
                    $message = (string)$charge->GatewayRspMsg;
                    $exceptions->hpsException = HpsGatewayResponseValidation::getException((string)$charge->GatewayTxnId, $gwResponseCode, $message);
                }
                if ($issuerResponseCode != "00") {
                    $message = (string)$charge->IssuerRspText;
                    $exceptions->cardException = HpsIssuerResponseValidation::getException((string)$charge->GatewayTxnId, $issuerResponseCode, $message, 'credit');
                }
                $summary->exceptions = $exceptions;
            }

            $transactions[] = $summary;
        }
        return $transactions;
    }
}

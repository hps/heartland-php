<?php

class HpsTransactionStatus extends HpsTransaction
{
    public $originalGatewayResponseCode;
    public $originalGatewayResponseText;
    public $originalResponseCode;
    public $originalResponseText;
    public $transactionStatus;
    public $originalTransactionId;
    public $altPayment;
    public $timezoneConversion;

    public static function fromDict($rsp, $txnType, $returnType = 'HpsTransactionStatus')
    {
        $statusResponse = $rsp->Transaction->$txnType;

        $status = parent::fromDict($rsp, $txnType, $returnType);
        $status->authorizationCode = (isset($statusResponse->TransactionStatus->AuthCode) ? (string)$statusResponse->TransactionStatus->AuthCode : null);
        $status->originalGatewayResponseCode = (isset($statusResponse->TransactionStatus->GatewayRspCode) ? (string)$statusResponse->TransactionStatus->GatewayRspCode : null);
        $status->originalGatewayResponseText = (isset($statusResponse->TransactionStatus->GatewayRspMsg) ? (string)$statusResponse->TransactionStatus->GatewayRspMsg : null);
        $status->originalResponseCode = (isset($statusResponse->TransactionStatus->RspCode) ? (string)$statusResponse->TransactionStatus->RspCode : null);
        $status->originalResponseText = (isset($statusResponse->TransactionStatus->RspText) ? (string)$statusResponse->TransactionStatus->RspText : null);
        $status->transactionStatus = (isset($statusResponse->TransactionStatus->TxnStatus) ? (string)$statusResponse->TransactionStatus->TxnStatus : null);
        $status->originalTransactionId = (isset($statusResponse->TransactionStatus->GatewayTxnId) ? (string)$statusResponse->TransactionStatus->GatewayTxnId : null);
        $status->altPayment = (isset($statusResponse->TransactionStatus->AltPayment) ? $statusResponse->TransactionStatus->AltPayment : null);
        $status->timezoneConversion = (isset($statusResponse->TzConversion) ? (string)$statusResponse->TzConversion : null);

        return $status;
    }
}

<?php

/**
 * Class HpsTransaction
 */
class HpsTransaction
{
    public $transactionId       = null;
    public $clientTransactionId = null;
    public $responseCode        = null;
    public $responseText        = null;
    public $referenceNumber     = null;
    protected $_header          = null;
    /**
     * @param        $rsp
     * @param        $txnType
     * @param string $returnType
     *
     * @return mixed
     */
    public static function fromDict($rsp, $txnType, $returnType = 'HpsTransaction')
    {
        $transaction = new $returnType();

        // Hydrate the header
        $transaction->_header = new HpsTransactionHeader();
        $transaction->_header->gatewayResponseCode = (string)$rsp->Header->GatewayRspCode;
        $transaction->_header->gatewayResponseMessage = (string)$rsp->Header->GatewayRspMsg;
        $transaction->_header->responseDt = (string)$rsp->Header->RspDT;
        $transaction->_header->clientTxnId = (isset($rsp->Header->ClientTxnId) ? (string)$rsp->Header->ClientTxnId : null);

        $transaction->transactionId = (string)$rsp->Header->GatewayTxnId;
        if (isset($rsp->Header->ClientTxnId)) {
            $transaction->clientTransactionId = (string)$rsp->Header->ClientTxnId;
        }

        // Hydrate the body
        if (!isset($rsp->Transaction) || !isset($rsp->Transaction->{$txnType})) {
            return $transaction;
        }
        // Hydrate the body
        $item = $rsp->Transaction->{$txnType};
        if ($item != null) {
            $transaction->responseCode = (isset($item->RspCode) ? (string)$item->RspCode : null);
            $transaction->responseText = (isset($item->RspText) ? (string)$item->RspText : null);
            $transaction->referenceNumber = (isset($item->RefNbr) ? (string)$item->RefNbr : null);
        }

        return $transaction;
    }
    /**
     * @return object
     */
    public function gatewayResponse()
    {
        return (object)array(
            'code'    => $this->_header->gatewayResponseCode,
            'message' => $this->_header->gatewayResponseMessage,
        );
    }
    /**
     * @param $transactionType
     *
     * @return string
     */
    public static function transactionTypeToServiceName($transactionType)
    {
        switch ($transactionType) {
            case HpsTransactionType::AUTHORIZE:
                return HpsItemChoiceTypePosResponseVer10Transaction::CREDIT_AUTH;
                break;
            case HpsTransactionType::CAPTURE:
                return HpsItemChoiceTypePosResponseVer10Transaction::CREDIT_ADD_TO_BATCH;
                break;
            case HpsTransactionType::CHARGE:
                return HpsItemChoiceTypePosResponseVer10Transaction::CREDIT_SALE;
                break;
            case HpsTransactionType::REFUND:
                return HpsItemChoiceTypePosResponseVer10Transaction::CREDIT_RETURN;
                break;
            case HpsTransactionType::REVERSE:
                return HpsItemChoiceTypePosResponseVer10Transaction::CREDIT_REVERSAL;
                break;
            case HpsTransactionType::VERIFY:
                return HpsItemChoiceTypePosResponseVer10Transaction::CREDIT_ACCOUNT_VERIFY;
                break;
            case HpsTransactionType::LIST_TRANSACTION:
                return HpsItemChoiceTypePosResponseVer10Transaction::REPORT_ACTIVITY;
                break;
            case HpsTransactionType::GET:
                return HpsItemChoiceTypePosResponseVer10Transaction::REPORT_TXN_DETAIL;
                break;
            case HpsTransactionType::VOID:
                return HpsItemChoiceTypePosResponseVer10Transaction::CREDIT_VOID;
                break;
            case HpsTransactionType::BATCH_CLOSE:
                return HpsItemChoiceTypePosResponseVer10Transaction::BATCH_CLOSE;
                break;
            case HpsTransactionType::SECURITY_ERROR:
                return "SecurityError";
                break;
            default:
                return "";
        }
    }
    /**
     * @param $serviceName
     *
     * @return int|null
     */
    public static function serviceNameToTransactionType($serviceName)
    {
        switch ($serviceName) {
            case HpsItemChoiceTypePosResponseVer10Transaction::CREDIT_AUTH:
                return HpsTransactionType::AUTHORIZE;
                break;
            case HpsItemChoiceTypePosResponseVer10Transaction::CREDIT_ADD_TO_BATCH:
                return HpsTransactionType::CAPTURE;
                break;
            case HpsItemChoiceTypePosResponseVer10Transaction::CREDIT_SALE:
                return HpsTransactionType::CHARGE;
                break;
            case HpsItemChoiceTypePosResponseVer10Transaction::CREDIT_RETURN:
                return HpsTransactionType::REFUND;
                break;
            case HpsItemChoiceTypePosResponseVer10Transaction::CREDIT_REVERSAL:
                return HpsTransactionType::REVERSE;
                break;
            case HpsItemChoiceTypePosResponseVer10Transaction::CREDIT_ACCOUNT_VERIFY:
                return HpsTransactionType::VERIFY;
                break;
            case HpsItemChoiceTypePosResponseVer10Transaction::REPORT_ACTIVITY:
                return HpsTransactionType::LIST_TRANSACTION;
                break;
            case HpsItemChoiceTypePosResponseVer10Transaction::REPORT_TXN_DETAIL:
                return HpsTransactionType::GET;
                break;
            case HpsItemChoiceTypePosResponseVer10Transaction::CREDIT_VOID:
                return HpsTransactionType::VOID;
                break;
            case HpsItemChoiceTypePosResponseVer10Transaction::BATCH_CLOSE:
                return HpsTransactionType::BATCH_CLOSE;
                break;
            default:
                return null;
        }
    }
}

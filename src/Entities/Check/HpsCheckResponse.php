<?php

class HpsCheckResponse extends HpsTransaction
{
    public $authorizationCode = null;
    public $customerId        = null;
    public $details           = null;

    public static function fromDict($rsp, $txnType, $returnType = 'HpsCheckResponse')
    {
        $response = $rsp->Transaction->$txnType;

        $sale = parent::fromDict($rsp, $txnType, $returnType);
        $sale->responseCode = (isset($response->RspCode) ? (string)$response->RspCode : null);
        $sale->responseText = (isset($response->RspMessage) ? (string)$response->RspMessage : null);
        $sale->authorizationCode = (isset($response->AuthCode) ? (string)$response->AuthCode : null);

        if ($response->CheckRspInfo) {
            $sale->details = array();

            $checkInfo = $response->CheckRspInfo;
            if (count($checkInfo) > 1) {
                foreach ($checkInfo as $details) {
                    $sale->details[] = self::_hydrateRspDetails($details);
                }
            } else {
                $sale->details = self::_hydrateRspDetails($checkInfo);
            }
        }

        return $sale;
    }

    private static function _hydrateRspDetails($checkInfo)
    {
        $details = new HpsCheckResponseDetails();
        $details->messageType = (isset($checkInfo->Type) ? (string)$checkInfo->Type : null);
        $details->code = (isset($checkInfo->Code) ? (string)$checkInfo->Code : null);
        $details->message = (isset($checkInfo->Message) ? (string)$checkInfo->Message : null);
        $details->fieldNumber = (isset($checkInfo->FieldNumber) ? (string)$checkInfo->FieldNumber : null);
        $details->fieldName = (isset($checkInfo->FieldName) ? (string)$checkInfo->FieldName : null);
        return $details;
    }
}

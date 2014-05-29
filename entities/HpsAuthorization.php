<?php


class HpsAuthorization extends HpsTransaction {
    public  $avsResultCode      = null,
            $avsResultText      = null,
            $cvvResultCode      = null,
            $cvvResultText      = null,
            $cpcIndicator       = null,
            $authorizationCode  = null,
            $authorizedAmount   = null,
            $cardType           = null,
            $description        = null,
            $invoiceNumber      = null,
            $customerId         = null,
            $descriptor         = null,
            $tokenData          = null;

    static public function fromDict($rsp,$txnType){
        $authResponse = $rsp->Transaction->$txnType;

        $auth = parent::fromDict($rsp,$txnType);
        $auth->authorizationCode = (isset($authResponse->AuthCode) ? $authResponse->AuthCode : null);
        $auth->avsResultCode = (isset($authResponse->AVSRsltCode) ? $authResponse->AVSRsltCode : null);
        $auth->avsResultText = (isset($authResponse->AVSRsltText) ? $authResponse->AVSRsltText : null);
        $auth->cvvResultCode = (isset($authResponse->CVVRsltCode) ? $authResponse->CVVRsltCode : null);
        $auth->cvvResultText = (isset($authResponse->CVVRsltText) ? $authResponse->CVVRsltText : null);
        $auth->authorizedAmount = (isset($authResponse->AuthAmt) ? $authResponse->AuthAmt : null);
        $auth->cardType = (isset($authResponse->CardType) ? $authResponse->CardType : null);
        $auth->descriptor = (isset($authResponse->TxnDescriptor) ? $authResponse->TxnDescriptor : null);
        $auth->cpcIndicator = (isset($authResponse->CPCInd) ? $authResponse->CPCInd : null);

        if(isset($rsp->Header->TokenData)){
            $auth->tokenData = new HpsTokenData();
            $auth->tokenData->responseCode = (isset($rsp->Header->TokenData->TokenRspCode) ? $rsp->Header->TokenData->TokenRspCode : null);
            $auth->tokenData->responseMessage = (isset($rsp->Header->TokenData->TokenRspMsg) ? $rsp->Header->TokenData->TokenRspMsg : null);
            $auth->tokenData->tokenValue = (isset($rsp->Header->TokenData->TokenValue) ? $rsp->Header->TokenData->TokenValue : null);
        }
        return $auth;
    }
} 
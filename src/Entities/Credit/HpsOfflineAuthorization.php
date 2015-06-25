<?php

class HpsOfflineAuthorization extends HpsTransaction
{
    public static function fromDict($rsp, $txnType, $returnType = 'HpsOfflineAuthorization')
    {
        $offlineAuth = parent::fromDict($rsp, $txnType, $returnType);
        $offlineAuth->responseCode = '00';
        $offlineAuth->responseText = '';
        return $offlineAuth;
    }
}

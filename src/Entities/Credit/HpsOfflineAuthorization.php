<?php

/**
 * Class HpsOfflineAuthorization
 */
class HpsOfflineAuthorization extends HpsTransaction
{
    /**
     * @param        $rsp
     * @param        $txnType
     * @param string $returnType
     *
     * @return mixed
     */
    public static function fromDict($rsp, $txnType, $returnType = 'HpsOfflineAuthorization')
    {
        $offlineAuth = parent::fromDict($rsp, $txnType, $returnType);
        $offlineAuth->responseCode = '00';
        $offlineAuth->responseText = '';
        return $offlineAuth;
    }
}

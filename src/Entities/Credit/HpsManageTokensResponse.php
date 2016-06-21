<?php

/**
 * Class HpsManageTokensResponse
 */
class HpsManageTokensResponse extends HpsTransaction
{
    /**
     * @param \SimpleXMLElement $rsp
     * @param null   $txnType
     * @param string $returnType
     *
     * @return HpsManageTokensResponse
     */
    public static function fromDict($rsp, $txnType = null, $returnType = 'HpsManageTokensResponse')
    {
        $transaction = parent::fromDict($rsp, $txnType, $returnType);
        $transaction->responseCode = '00';
        $transaction->responseText = '';
        return $transaction;
    }
}
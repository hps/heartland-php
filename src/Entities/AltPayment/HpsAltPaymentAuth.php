<?php

/**
 * Class HpsAltPaymentAuth
 */
class HpsAltPaymentAuth extends HpsAltPaymentResponse
{
    public $status = null;
    public $statusMessage = null;
    /**
     * @param        $rsp
     * @param        $txnType
     * @param string $returnType
     *
     * @return mixed
     */
    public static function fromDict($rsp, $txnType, $returnType = 'HpsAltPaymentAuth')
    {
        $authorize = $rsp->Transaction->$txnType;

        $auth = parent::fromDict($rsp, $txnType, $returnType);

        $auth->status = isset($authorize->Status) ? (string)$authorize->Status : null;
        $auth->statusMessage = isset($authorize->StatusMessage) ? (string)$authorize->StatusMessage : null;

        return $auth;
    }
}

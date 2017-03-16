<?php

/**
 * Class HpsRefund
 */
class HpsRefund extends HpsTransaction
{
    /**
     * @param        $rsp
     * @param        $txnType
     * @param string $returnType
     *
     * @return mixed
     */
    public static function fromDict($rsp, $txnType, $returnType = 'HpsRefund')
    {
        $refund = parent::fromDict($rsp, $txnType, $returnType);
        $refund->responseCode = '00';
        $refund->responseText = '';
        return $refund;
    }
}

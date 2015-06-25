<?php

class HpsRefund extends HpsTransaction
{
    public static function fromDict($rsp, $txnType, $returnType = 'HpsRefund')
    {
        $refund = parent::fromDict($rsp, $txnType, $returnType);
        $refund->responseCode = '00';
        $refund->responseText = '';
        return $refund;
    }
}

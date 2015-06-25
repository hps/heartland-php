<?php

class HpsCPCEdit extends HpsTransaction
{
    public static function fromDict($rsp, $txnType, $returnType = 'HpsCPCEdit')
    {
        $cpcEdit = parent::fromDict($rsp, $txnType, $returnType);
        $cpcEdit->responseCode = '00';
        $cpcEdit->responseText = '';
        return $cpcEdit;
    }
}

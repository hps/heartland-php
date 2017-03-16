<?php

/**
 * Class HpsCPCEdit
 */
class HpsCPCEdit extends HpsTransaction
{
    /**
     * @param        $rsp
     * @param        $txnType
     * @param string $returnType
     *
     * @return mixed
     */
    public static function fromDict($rsp, $txnType, $returnType = 'HpsCPCEdit')
    {
        $cpcEdit = parent::fromDict($rsp, $txnType, $returnType);
        $cpcEdit->responseCode = '00';
        $cpcEdit->responseText = '';
        return $cpcEdit;
    }
}

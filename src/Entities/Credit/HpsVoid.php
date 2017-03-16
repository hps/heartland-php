<?php

/**
 * Class HpsVoid
 */
class HpsVoid extends HpsTransaction
{
    /**
     * @param        $rsp
     * @param        $txnType
     * @param string $returnType
     *
     * @return mixed
     */
    public static function fromDict($rsp, $txnType, $returnType = 'HpsVoid')
    {
        $void = parent::fromDict($rsp, $txnType, $returnType);
        $void->responseCode = '00';
        $void->responseText = '';
        return $void;
    }
}

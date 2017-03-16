<?php

/**
 * Class HpsReversal
 */
class HpsReversal extends HpsTransaction
{
    public $avsResultCode = null;
    public $avsResultText = null;
    public $cvvResultCode = null;
    public $cvvResultText = null;
    public $cpcIndicator  = null;
    /**
     * @param        $rsp
     * @param        $txnType
     * @param string $returnType
     *
     * @return mixed
     */
    public static function fromDict($rsp, $txnType, $returnType = 'HpsReversal')
    {
        $reverseResponse = $rsp->Transaction->$txnType;

        $reverse = parent::fromDict($rsp, $txnType, $returnType);
        $reverse->avsResultCode = (isset($reverseResponse->AVSRsltCode) ? (string)$reverseResponse->AVSRsltCode : null);
        $reverse->avsResultText = (isset($reverseResponse->AVSRsltText) ? (string)$reverseResponse->AVSRsltText : null);
        $reverse->cpcIndicator  = (isset($reverseResponse->CPCInd) ? (string)$reverseResponse->CPCInd : null);
        $reverse->cvvResultCode = (isset($reverseResponse->CVVRsltCode) ? (string)$reverseResponse->CVVRsltCode : null);
        $reverse->cvvResultText = (isset($reverseResponse->CVVRsltText) ? (string)$reverseResponse->CVVRsltText : null);

        return $reverse;
    }
}

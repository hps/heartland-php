<?php

class HpsAltPaymentCapture extends HpsAltPaymentResponse
{
    public $status = null;
    public $statusMessage = null;

    public static function fromDict($rsp, $txnType, $returnType = 'HpsAltPaymentCapture')
    {
        $addToBatch = $rsp->Transaction->$txnType;

        $capture = parent::fromDict($rsp, $txnType, $returnType);

        $capture->status = isset($addToBatch->Status) ? (string)$addToBatch->Status : null;
        $capture->statusMessage = isset($addToBatch->StatusMessage) ? (string)$addToBatch->StatusMessage : null;

        return $capture;
    }
}

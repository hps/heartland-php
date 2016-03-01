<?php

class HpsCardinalMPIPreapprovalResponse extends HpsCardinalMPIResponse
{
    public $longAccessToken = null;
    public $preCheckoutData = null;
    public $preCheckoutTransactionId = null;

    public static function fromObject($data, $returnType = 'HpsCardinalMPIPreapprovalResponse')
    {
        $response = parent::fromObject($data, $returnType);
        $response->longAccessToken = self::readDataKey($data, 'LongAccessToken');
        $response->preCheckoutData = isset($data->PrecheckoutData) ? simplexml_load_string($data->PrecheckoutData)->PrecheckoutData : null;
        $response->preCheckoutTransactionId = self::readDataKey($data, 'PrecheckoutTransactionId');
        return $response;
    }
}
<?php

class HpsCardinalMPILookupResponse extends HpsCardinalMPIResponse
{
    public $acsUrl = null;
    public $enrolled = null;
    public $payload = null;
    public $processorTransactionIdPairing = null;

    public static function fromObject($data, $returnType = 'HpsCardinalMPILookupResponse')
    {
        $response = parent::fromObject($data, $returnType);
        $response->acsUrl = self::readDataKey($data, 'ACSUrl');
        $response->enrolled = self::readDataKey($data, 'Enrolled');
        $response->payload = self::readDataKey($data, 'Payload');
        $response->processorTransactionIdPairing = self::readDataKey($data, 'ProcessorTransactionIdPairing');
        return $response;
    }
}
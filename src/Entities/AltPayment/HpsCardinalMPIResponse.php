<?php

/**
 * Class HpsCardinalMPIResponse
 */
class HpsCardinalMPIResponse
{
    public $errorDescription = null;
    public $errorNumber = null;
    public $merchantData = null;
    public $merchantReferenceNumber = null;
    public $orderId = null;
    public $orderNumber = null;
    public $processorOrderNumber = null;
    public $processorStatusCode = null;
    public $processorTransactionId = null;
    public $reasonCode = null;
    public $reasonDescription = null;
    public $statusCode = null;
    public $transactionId = null;
    /**
     * @param        $data
     * @param string $returnType
     *
     * @return mixed
     */
    public static function fromObject($data, $returnType = 'HpsCardinalMPIResponse')
    {
        $response = new $returnType();
        $response->errorDescription = self::readDataKey($data, 'ErrorDesc');
        $response->errorNumber = self::readDataKey($data, 'ErrorNo');
        $response->merchantData = self::readDataKey($data, 'MerchantData');
        $response->merchantReferenceNumber = self::readDataKey($data, 'MerchantReferenceNumber');
        $response->orderId = self::readDataKey($data, 'OrderId');
        $response->orderNumber = self::readDataKey($data, 'OrderNumber');
        $response->processorOrderNumber = self::readDataKey($data, 'ProcessorOrderNumber');
        $response->processorStatusCode = self::readDataKey($data, 'ProcessorStatusCode');
        $response->processorTransactionId = self::readDataKey($data, 'ProcessorTransactionId');
        $response->reasonCode = self::readDataKey($data, 'ReasonCode');
        $response->reasonDescription = self::readDataKey($data, 'ReasonDesc');
        $response->statusCode = self::readDataKey($data, 'StatusCode');
        $response->transactionId = self::readDataKey($data, 'TransactionId');
        return $response;
    }
    /**
     * @param $data
     * @param $key
     *
     * @return null|string
     */
    public static function readDataKey($data, $key)
    {
        return isset($data->{$key}) ? (string)$data->{$key} : null;
    }
}
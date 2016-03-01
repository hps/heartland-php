<?php

class HpsCardinalMPIAuthorizeResponse extends HpsCardinalMPIResponse
{
    public $authorizationCode = null;
    public $avsResult = null;
    public $cardBin = null;
    public $cardCodeResult = null;
    public $cardExpMonth = null;
    public $cardExpYear = null;
    public $cardLastFour = null;
    public $cardType = null;
    public $longAccessToken = null;
    public $nameOnCard = null;
    public $processorBillingAddress1 = null;
    public $processorBillingAddress2 = null;
    public $processorBillingCity = null;
    public $processorBillingCountryCode = null;
    public $processorBillingFirstName = null;
    public $processorBillingLastName = null;
    public $processorBillingMiddleName = null;
    public $processorBillingPhone = null;
    public $processorBillingPostalCode = null;
    public $processorBillingState = null;
    public $processorCavv = null;
    public $processorEciFlag = null;
    public $processorEmail = null;
    public $processorPayresStatus = null;
    public $processorShippingAddress1 = null;
    public $processorShippingAddress2 = null;
    public $processorShippingCity = null;
    public $processorShippingCountryCode = null;
    public $processorShippingFullName = null;
    public $processorShippingPhone = null;
    public $processorShippingPostalCode = null;
    public $processorShippingState = null;

    public static function fromObject($data, $returnType = 'HpsCardinalMPIAuthorizeResponse')
    {
        $response = parent::fromObject($data, $returnType);
        $response->authorizationCode = self::readDataKey($data, 'AuthorizationCode');
        $response->avsResult = self::readDataKey($data, 'AvsResult');
        $response->cardBin = self::readDataKey($data, 'CardBin');
        $response->cardExpMonth = self::readDataKey($data, 'CardExpMonth');
        $response->cardExpYear = self::readDataKey($data, 'CardExpYear');
        $response->cardLastFour = self::readDataKey($data, 'CardLastFour');
        $response->cardType = self::readDataKey($data, 'CardType');
        $response->longAccessToken = self::readDataKey($data, 'LongAccessToken');
        $response->nameOnCard = self::readDataKey($data, 'NameOnCard');
        $response->processorBillingAddress1 = self::readDataKey($data, 'ProcessorBillingAddress1');
        $response->processorBillingAddress2 = self::readDataKey($data, 'ProcessorBillingAddress2');
        $response->processorBillingCity = self::readDataKey($data, 'ProcessorBillingCity');
        $response->processorBillingCountryCode = self::readDataKey($data, 'ProcessorBillingCountryCode');
        $response->processorBillingFirstName = self::readDataKey($data, 'ProcessorBillingFirstName');
        $response->processorBillingLastName = self::readDataKey($data, 'ProcessorBillingLastName');
        $response->processorBillingMiddleName = self::readDataKey($data, 'ProcessorBillingMiddleName');
        $response->processorBillingPhone = self::readDataKey($data, 'ProcessorBillingPhone');
        $response->processorBillingPostalCode = self::readDataKey($data, 'ProcessorBillingPostalCode');
        $response->processorBillingState = self::readDataKey($data, 'ProcessorBillingState');
        $response->processorCavv = self::readDataKey($data, 'ProcessorCavv');
        $response->processorEciFlag = self::readDataKey($data, 'ProcessorEciFlag');
        $response->processorEmail = self::readDataKey($data, 'ProcessorEmail');
        $response->processorPayresStatus = self::readDataKey($data, 'ProcessorPayresStatus');
        $response->processorShippingAddress1 = self::readDataKey($data, 'ProcessorShippingAddress1');
        $response->processorShippingAddress2 = self::readDataKey($data, 'ProcessorShippingAddress2');
        $response->processorShippingCity = self::readDataKey($data, 'ProcessorShippingCity');
        $response->processorShippingCountryCode = self::readDataKey($data, 'ProcessorShippingCountryCode');
        $response->processorShippingFullName = self::readDataKey($data, 'ProcessorShippingFullName');
        $response->processorShippingPhone = self::readDataKey($data, 'ProcessorShippingPhone');
        $response->processorShippingPostalCode = self::readDataKey($data, 'ProcessorShippingPostalCode');
        $response->processorShippingState = self::readDataKey($data, 'ProcessorShippingState');
        return $response;
    }
}
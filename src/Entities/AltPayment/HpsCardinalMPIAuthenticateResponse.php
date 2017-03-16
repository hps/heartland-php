<?php

/**
 * Class HpsCardinalMPIAuthenticateResponse
 */
class HpsCardinalMPIAuthenticateResponse extends HpsCardinalMPIResponse
{
    public $authenticateMethod = null;
    public $authorizationCode = null;
    public $cardBin = null;
    public $cardEnrollmentMethod = null;
    public $cardExpMonth = null;
    public $cardExpYear = null;
    public $cardLastFour = null;
    public $cardType = null;
    public $cardTypeName = null;
    public $longAccessToken = null;
    public $mastercardAssignedId = null;
    public $nameOnCard = null;
    public $paResStatus = null;
    public $payPassWalletIndicator = null;
    public $paymentProcessorOrderNumber = null;
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
    public $processorSignatureVerification = null;
    public $processorTransactionIdPairing = null;
    public $processorXid = null;
    public $rewardExpMonth = null;
    public $rewardExpYear = null;
    public $rewardId = null;
    public $rewardName = null;
    public $rewardNumber = null;
    public $scEnrollmentStatus = null;
    public $signatureVerification = null;
    /**
     * @param        $data
     * @param string $returnType
     *
     * @return mixed
     */
    public static function fromObject($data, $returnType = 'HpsCardinalMPIAuthenticateResponse')
    {
        $response = parent::fromObject($data, $returnType);
        $response->authenticateMethod = self::readDataKey($data, 'AuthenticateMethod');
        $response->authorizationCode = self::readDataKey($data, 'AuthorizationCode');
        $response->cardBin = self::readDataKey($data, 'CardBin');
        $response->cardEnrollmentMethod = self::readDataKey($data, 'CardEnrollmentMethod');
        $response->cardExpMonth = self::readDataKey($data, 'CardExpMonth');
        $response->cardExpYear = self::readDataKey($data, 'CardExpYear');
        $response->cardLastFour = self::readDataKey($data, 'CardLastFour');
        $response->cardType = self::readDataKey($data, 'CardType');
        $response->cardTypeName = self::readDataKey($data, 'CardTypeName');
        $response->longAccessToken = self::readDataKey($data, 'LongAccessToken');
        $response->mastercardAssignedId = self::readDataKey($data, 'MasterCardAssignedId');
        $response->nameOnCard = self::readDataKey($data, 'NameOnCard');
        $response->paResStatus = self::readDataKey($data, 'PaResStatus');
        $response->payPassWalletIndicator = self::readDataKey($data, 'PayPassWalletIndicator');
        $response->paymentProcessorOrderNumber = self::readDataKey($data, 'PaymentProcessorOrderNumber');
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
        $response->processorSignatureVerification = self::readDataKey($data, 'ProcessorSignatureVerification');
        $response->processorXid = self::readDataKey($data, 'ProcessorXid');
        $response->rewardExpMonth = self::readDataKey($data, 'RewardExpMonth');
        $response->rewardExpYear = self::readDataKey($data, 'RewardExpYear');
        $response->rewardId = self::readDataKey($data, 'RewardId');
        $response->rewardName = self::readDataKey($data, 'RewardName');
        $response->rewardNumber = self::readDataKey($data, 'RewardNumber');
        $response->scEnrollmentStatus = self::readDataKey($data, 'ScEnrollmentStatus');
        $response->signatureVerification = self::readDataKey($data, 'SignatureVerification');
        return $response;
    }
}
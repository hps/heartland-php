<?php

/**
 * Class HpsPayPlanPaymentMethod
 */
class HpsPayPlanPaymentMethod extends HpsPayPlanResourceAbstract
{
    /** @var string|null */
    public $paymentMethodKey          = null;

    /** @var string|null */
    public $paymentMethodType         = null;

    /** @var string|null */
    public $preferredPayment          = null;

    /** @var string|null */
    public $paymentStatus             = null;

    /** @var string|null */
    public $paymentMethodIdentifier   = null;

    /** @var integer|null */
    public $customerKey               = null;

    /** @var string|null */
    public $customerIdentifier        = null;

    /** @var string|null */
    public $customerStatus            = null;

    /** @var string|null */
    public $firstName                 = null;

    /** @var string|null */
    public $lastName                  = null;

    /** @var string|null */
    public $company                   = null;

    /** @var string|null */
    public $nameOnAccount             = null;

    /** @var string|null */
    public $accountNumberLast4        = null;

    /** @var string|null */
    public $paymentMethod             = null;

    /** @var string|null */
    public $cardBrand                 = null;

    /** @var string|null */
    public $expirationDate            = null;

    /** @var string|null */
    public $cvvResponseCode           = null;

    /** @var string|null */
    public $avsResponseCode           = null;

    /** @var string|null */
    public $achType                   = null;

    /** @var string|null */
    public $accountType               = null;

    /** @var string|null */
    public $routingNumber             = null;

    /** @var string|null */
    public $telephoneIndicator        = null;

    /** @var string|null */
    public $addressLine1              = null;

    /** @var string|null */
    public $addressLine2              = null;

    /** @var string|null */
    public $city                      = null;

    /** @var string|null */
    public $stateProvince             = null;

    /** @var string|null */
    public $zipPostalCode             = null;

    /** @var string|null */
    public $country                   = null;

    /** @var string|null */
    public $accountHolderYob          = null;

    /** @var string|null */
    public $driversLicenseState       = null;

    /** @var string|null */
    public $driversLicenseNumber      = null;

    /** @var string|null */
    public $socialSecurityNumberLast4 = null;

    /** @var string|null */
    public $hasSchedules              = null;

    /** @var string|null */
    public $hasActiveSchedules        = null;

    /** @var string|null */
    public $accountNumber             = null;

    /** @var string|null */
    public $paymentToken              = null;
    /**
     * @param null $type
     *
     * @return array
     */
    public static function getEditableFields($type = null)
    {
        $fields = array(
            'preferredPayment',
            'paymentStatus',
            'paymentMethodIdentifier',
            'nameOnAccount',
            'addressLine1',
            'addressLine2',
            'city',
            'stateProvince',
            'zipPostalCode',
        );

        $ccOnly = array(
            'expirationDate',
            'country',
        );

        $achOnly = array(
            'telephoneIndicator',
            'accountHolderYob',
            'driversLicenseState',
            'driversLicenseNumber',
            'socialSecurityNumberLast4',
        );

        switch ($type) {
            case HpsPayPlanPaymentMethodType::ACH:
                return array_merge($fields, $achOnly);
                break;
            case HpsPayPlanPaymentMethodType::CREDIT_CARD:
                return array_merge($fields, $ccOnly);
                break;
            default:
                return array_merge($fields, $achOnly, $ccOnly);
                break;
        }
    }
    /**
     * @return array
     */
    public static function getSearchableFields()
    {
        return array(
            'customerIdentifier',
            'achType',
            'accountType',
            'accountNumberLast4',
            'routingNumber',
            'cardBrand',
            'cardBINNumber',
            'expirationDateStart',
            'expirationDateEnd',
            'paymentMethodType',
            'paymentStatus',
            'hasSchedules',
            'hasActiveSchedules',
        );
    }
    /**
     * @param $obj
     *
     * @return \HpsPayPlanPaymentMethod
     */
    public static function fromStdClass($obj)
    {
        $ret = new HpsPayPlanPaymentMethod();
        $ret->paymentMethodKey = property_exists($obj, 'paymentMethodKey') ? $obj->paymentMethodKey : null;
        $ret->paymentMethodType = property_exists($obj, 'paymentMethodType') ? $obj->paymentMethodType : null;
        $ret->preferredPayment = property_exists($obj, 'preferredPayment') ? $obj->preferredPayment : null;
        $ret->paymentStatus = property_exists($obj, 'paymentStatus') ? $obj->paymentStatus : null;
        $ret->paymentMethodIdentifier = property_exists($obj, 'paymentMethodIdentifier') ? $obj->paymentMethodIdentifier : null;
        $ret->customerKey = property_exists($obj, 'customerKey') ? $obj->customerKey : null;
        $ret->customerIdentifier = property_exists($obj, 'customerIdentifier') ? $obj->customerIdentifier : null;
        $ret->customerStatus = property_exists($obj, 'customerStatus') ? $obj->customerStatus : null;
        $ret->firstName = property_exists($obj, 'firstName') ? $obj->firstName : null;
        $ret->lastName = property_exists($obj, 'lastName') ? $obj->lastName : null;
        $ret->company = property_exists($obj, 'company') ? $obj->company : null;
        $ret->nameOnAccount = property_exists($obj, 'nameOnAccount') ? $obj->nameOnAccount : null;
        $ret->accountNumberLast4 = property_exists($obj, 'accountNumberLast4') ? $obj->accountNumberLast4 : null;
        $ret->paymentMethod = property_exists($obj, 'paymentMethod') ? $obj->paymentMethod : null;
        $ret->cardBrand = property_exists($obj, 'cardBrand') ? $obj->cardBrand : null;
        $ret->expirationDate = property_exists($obj, 'expirationDate') ? $obj->expirationDate : null;
        $ret->cvvResponseCode = property_exists($obj, 'cvvResponseCode') ? $obj->cvvResponseCode : null;
        $ret->avsResponseCode = property_exists($obj, 'avsResponseCode') ? $obj->avsResponseCode : null;
        $ret->achType = property_exists($obj, 'achType') ? $obj->achType : null;
        $ret->accountType = property_exists($obj, 'accountType') ? $obj->accountType : null;
        $ret->routingNumber = property_exists($obj, 'routingNumber') ? $obj->routingNumber : null;
        $ret->telephoneIndicator = property_exists($obj, 'telephoneIndicator') ? $obj->telephoneIndicator : null;
        $ret->addressLine1 = property_exists($obj, 'addressLine1') ? $obj->addressLine1 : null;
        $ret->addressLine2 = property_exists($obj, 'addressLine2') ? $obj->addressLine2 : null;
        $ret->city = property_exists($obj, 'city') ? $obj->city : null;
        $ret->stateProvince = property_exists($obj, 'stateProvince') ? $obj->stateProvince : null;
        $ret->zipPostalCode = property_exists($obj, 'zipPostalCode') ? $obj->zipPostalCode : null;
        $ret->country = property_exists($obj, 'country') ? $obj->country : null;
        $ret->accountHolderYob = property_exists($obj, 'accountHolderYob') ? $obj->accountHolderYob : null;
        $ret->driversLicenseState = property_exists($obj, 'driversLicenseState') ? $obj->driversLicenseState : null;
        $ret->driversLicenseNumber = property_exists($obj, 'driversLicenseNumber') ? $obj->driversLicenseNumber : null;
        $ret->socialSecurityNumberLast4 = property_exists($obj, 'socialSecurityNumberLast4') ? $obj->socialSecurityNumberLast4 : null;
        $ret->hasSchedules = property_exists($obj, 'hasSchedules') ? $obj->hasSchedules : null;
        $ret->hasActiveSchedules = property_exists($obj, 'hasActiveSchedules') ? $obj->hasActiveSchedules : null;
        $ret->creationDate = property_exists($obj, 'creationDate') ? $obj->creationDate : null;
        $ret->lastChangeDate = property_exists($obj, 'lastChangeDate') ? $obj->lastChangeDate : null;
        $ret->statusSetDate = property_exists($obj, 'statusSetDate') ? $obj->statusSetDate : null;
        return $ret;
    }

    // Needs to be implemented to get name of child class
    /**
     * @param string $class
     * @param array  $params
     *
     * @return array
     */
    public function getEditableFieldsWithValues($class = '', $params = array())
    {
        return parent::getEditableFieldsWithValues(get_class(), array($this->paymentMethodType));
    }
}

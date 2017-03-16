<?php

/**
 * Class HpsPayPlanCustomer
 */
class HpsPayPlanCustomer extends HpsPayPlanResourceAbstract
{
    /** @var string|null */
    public $customerKey        = null;

    /** @var string|null */
    public $customerIdentifier = null;

    /** @var string|null */
    public $firstName          = null;

    /** @var string|null */
    public $lastName           = null;

    /** @var string|null */
    public $company            = null;

    /** @var string|null */
    public $customerStatus     = null;

    /** @var string|null */
    public $primaryEmail       = null;

    /** @var string|null */
    public $phoneDay           = null;

    /** @var string|null */
    public $phoneDayExt        = null;

    /** @var string|null */
    public $phoneEvening       = null;

    /** @var string|null */
    public $phoneEveningExt    = null;

    /** @var string|null */
    public $phoneMobile        = null;

    /** @var string|null */
    public $phoneMobileExt     = null;

    /** @var string|null */
    public $fax                = null;

    /** @var string|null */
    public $title              = null;

    /** @var string|null */
    public $department         = null;

    /** @var string|null */
    public $addressLine1       = null;

    /** @var string|null */
    public $addressLine2       = null;

    /** @var string|null */
    public $city               = null;

    /** @var string|null */
    public $country            = null;

    /** @var string|null */
    public $stateProvince      = null;

    /** @var string|null */
    public $zipPostalCode      = null;

    /** @var array(HpsPayPlanPaymentMethod)|null */
    public $paymentMethods     = null;

    /** @var array(HpsPayPlanSchedule)|null */
    public $schedules          = null;
    /**
     * @return array
     */
    public static function getEditableFields()
    {
        return array(
            'customerIdentifier',
            'firstName',
            'lastName',
            'company',
            'customerStatus',
            'title',
            'department',
            'primaryEmail',
            'secondaryEmail',
            'phoneDay',
            'phoneDayExt',
            'phoneEvening',
            'phoneEveningExt',
            'phoneMobile',
            'phoneMobileExt',
            'fax',
            'addressLine1',
            'addressLine2',
            'city',
            'stateProvince',
            'zipPostalCode',
            'country',
        );
    }
    /**
     * @return array
     */
    public static function getSearchableFields()
    {
        return array(
            'customerIdentifier',
            'company',
            'firstName',
            'lastName',
            'primaryEmail',
            'customerStatus',
            'phoneNumber',
            'city',
            'stateProvince',
            'zipPostalCode',
            'country',
            'hasSchedules',
            'hasActiveSchedules',
            'hasPaymentMethods',
            'hasActivePaymentMethods',
        );
    }
    /**
     * @param $obj
     *
     * @return \HpsPayPlanCustomer
     */
    public static function fromStdClass($obj)
    {
        $ret = new HpsPayPlanCustomer();
        $ret->customerKey = property_exists($obj, 'customerKey') ? $obj->customerKey : null;
        $ret->customerIdentifier = property_exists($obj, 'customerIdentifier') ? $obj->customerIdentifier : null;
        $ret->firstName = property_exists($obj, 'firstName') ? $obj->firstName : null;
        $ret->lastName = property_exists($obj, 'lastName') ? $obj->lastName : null;
        $ret->company = property_exists($obj, 'company') ? $obj->company : null;
        $ret->customerStatus = property_exists($obj, 'customerStatus') ? $obj->customerStatus : null;
        $ret->primaryEmail = property_exists($obj, 'primaryEmail') ? $obj->primaryEmail : null;
        $ret->phoneDay = property_exists($obj, 'phoneDay') ? $obj->phoneDay : null;
        $ret->phoneDayExt = property_exists($obj, 'phoneDayExt') ? $obj->phoneDayExt : null;
        $ret->phoneEvening = property_exists($obj, 'phoneEvening') ? $obj->phoneEvening : null;
        $ret->phoneEveningExt = property_exists($obj, 'phoneEveningExt') ? $obj->phoneEveningExt : null;
        $ret->phoneMobile = property_exists($obj, 'phoneMobile') ? $obj->phoneMobile : null;
        $ret->phoneMobileExt = property_exists($obj, 'phoneMobileExt') ? $obj->phoneMobileExt : null;
        $ret->fax = property_exists($obj, 'fax') ? $obj->fax : null;
        $ret->title = property_exists($obj, 'title') ? $obj->title : null;
        $ret->department = property_exists($obj, 'department') ? $obj->department : null;
        $ret->addressLine1 = property_exists($obj, 'addressLine1') ? $obj->addressLine1 : null;
        $ret->addressLine2 = property_exists($obj, 'addressLine2') ? $obj->addressLine2 : null;
        $ret->city = property_exists($obj, 'city') ? $obj->city : null;
        $ret->country = property_exists($obj, 'country') ? $obj->country : null;
        $ret->stateProvince = property_exists($obj, 'stateProvince') ? $obj->stateProvince : null;
        $ret->zipPostalCode = property_exists($obj, 'zipPostalCode') ? $obj->zipPostalCode : null;
        $ret->paymentMethods = property_exists($obj, 'paymentMethods') ? array_map('HpsPayPlanPaymentMethod::fromStdClass', $obj->paymentMethods) : null;
        $ret->schedules = property_exists($obj, 'schedules') ? array_map('HpsPayPlanSchedule::fromStdClass', $obj->schedules) : null;
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
        return parent::getEditableFieldsWithValues(get_class(), $params);
    }
}

<?php

/**
 * Class HpsInputValidation
 */
class HpsInputValidation
{
    private static $_defaultAllowedCurrencies = array('usd');
    private static $_inputFldMaxLength = array(
        'PhoneNumber' => 20,
        'ZipCode' => 9,
        'FirstName' => 26,
        'LastName' => 26,
        'City' => 20,
        'Email' => 100,
	'State' => 20
    );
    /**
     * @param $amount
     *
     * @return string
     * @throws \HpsInvalidRequestException
     */
    public static function checkAmount($amount)
    {
        if ($amount < 0 || $amount === null) {
            throw new HpsInvalidRequestException(
                HpsExceptionCodes::INVALID_AMOUNT,
                'Must be greater than or equal to 0.',
                'amount'
            );
        }
        $amount = preg_replace('/[^0-9\.]/', '', $amount);
        return sprintf("%0.2f", round($amount, 3));
    }
    /**
     * @param      $currency
     * @param null $allowedCurrencies
     *
     * @throws \HpsInvalidRequestException
     */
    public static function checkCurrency($currency, $allowedCurrencies = null)
    {
        $currencies = self::$_defaultAllowedCurrencies;
        if (isset($allowedCurrencies) && is_array($allowedCurrencies)) {
            $currencies = $allowedCurrencies;
        }

        if ($currency == null || $currency == '') {
            throw new HpsInvalidRequestException(
                HpsExceptionCodes::MISSING_CURRENCY,
                'Currency cannot be none',
                'currency'
            );
        } else if (!in_array(strtolower($currency), $currencies)) {
            throw new HpsInvalidRequestException(
                HpsExceptionCodes::INVALID_CURRENCY,
                "'".strtolower($currency)."' is not a supported currency",
                'currency'
            );
        }
    }
    /**
     * @param $number
     *
     * @return mixed
     */
    public static function cleanPhoneNumber($number)
    {
        return preg_replace('/\D+/', '', trim($number));
    }
    /**
     * @param $zip
     *
     * @return mixed
     */
    public static function cleanZipCode($zip)
    {
        return preg_replace('/[^0-9A-Za-z]/', '', trim($zip));
    }
    /**
     * @param $date
     *
     * @throws \HpsInvalidRequestException
     */
    public static function checkDateNotFuture($date)
    {
        $current = date('Y-m-d\TH:i:s.00\Z', time());

        if ($date != null && $date > $current) {
            throw new HpsInvalidRequestException(
                HpsExceptionCodes::INVALID_DATE,
                'Date cannot be in the future'
            );
        }
    }
    /**
     * @param $text
     *
     * @return mixed
     */
    public static function cleanAscii($text)
    {
        return preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $text);
    }
	
    /** 	 
     * This method clears the user input and return the phone number in correct format or throw an exception  
     * 	
     * @param string $phoneNumber this is user entered phone number    
     * @return string
     * @throws HpsInvalidRequestException     
     */
    public static function checkPhoneNumber($phoneNumber) {
        $phoneNumber = self::cleanPhoneNumber($phoneNumber);

        if (!empty($phoneNumber) && strlen($phoneNumber) > self::$_inputFldMaxLength['PhoneNumber']) {
            $errorMessage = 'The value for phone number can be no more than ' . self::$_inputFldMaxLength['PhoneNumber'] . ' characters, Please try again after making corrections';
            throw new HpsInvalidRequestException(
            HpsExceptionCodes::INVALID_PHONE_NUMBER, $errorMessage
            );
        }
        return $phoneNumber;
    }
    
    /** 	 
     * This method clears the user input and return the Zip code in correct format or throw an exception  
     * 	
     * @param string $zipCode this is user entered zip code    
     * @return string
     * @throws HpsInvalidRequestException     
     */
    public static function checkZipCode($zipCode) {
        $zipCode = self::cleanZipCode($zipCode);

        if (!empty($zipCode) && strlen($zipCode) > self::$_inputFldMaxLength['ZipCode']) {
            $errorMessage = 'The value for zip code can be no more than ' . self::$_inputFldMaxLength['ZipCode'] . ' characters, Please try again after making corrections';
            throw new HpsInvalidRequestException(
            HpsExceptionCodes::INVALID_ZIP_CODE, $errorMessage
            );
        }
        return $zipCode;
    }
    
    /** 	 
     * This method clears the user input and return the user input in correct format or throw an exception  
     * 	
     * @param string $value this is user entered value (first name or last name or email or city)    
     * @param string $type this is user entered value field name
     * @return string
     * @throws HpsInvalidRequestException     
     */
    public static function checkCardHolderData($value, $type = '') {       
        
        $value = filter_var(trim($value),FILTER_SANITIZE_SPECIAL_CHARS);
        
        //validate length of input data and throw exception
        //if maximum characters is not mentioned in $_inputFldMaxLength the sanitized values will be returned
        if (!empty(self::$_inputFldMaxLength[$type]) && strlen($value) > self::$_inputFldMaxLength[$type]) {            
            $errorMessage = "The value for $type can be no more than " . self::$_inputFldMaxLength[$type] . ' characters, Please try again after making corrections';
            throw new HpsInvalidRequestException(
                HpsExceptionCodes::INVALID_INPUT_LENGTH, $errorMessage
            );
        }
        return $value;
    }
    
    /** 	 
     * This method clears the user input and return the email in correct format or throw an exception  
     * 	
     * @param string $value this is user entered email address 
     * @return string
     * @throws HpsInvalidRequestException     
     */
    public static function checkEmailAddress($value) {
        $value = filter_var(trim($value),FILTER_SANITIZE_EMAIL);
        
        //validate the email address format
        if(!empty($value) && filter_var($value, FILTER_VALIDATE_EMAIL) === false){            
            throw new HpsInvalidRequestException(
                HpsExceptionCodes::INVALID_EMAIL_ADDRESS, 'Invalid email address'
            );
        }

        //validate length of input data and throw exception
        if (!empty(self::$_inputFldMaxLength['Email']) && strlen($value) > self::$_inputFldMaxLength['Email']) {            
            $errorMessage = "The value for Email can be no more than " . self::$_inputFldMaxLength['Email'] . ' characters, Please try again after making corrections';
            throw new HpsInvalidRequestException(
                HpsExceptionCodes::INVALID_INPUT_LENGTH, $errorMessage
            );
        }
        return $value;
    }
    
    

}

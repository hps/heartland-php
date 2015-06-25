<?php

class HpsInputValidation
{
    private static $_defaultAllowedCurrencies = array('usd');

    public static function checkAmount($amount)
    {
        if ($amount < 0 || $amount == null) {
            throw new HpsInvalidRequestException(
                HpsExceptionCodes::INVALID_AMOUNT,
                'Must be greater than or equal to 0.',
                'amount'
            );
        }
        $amount = preg_replace('/[^0-9\.]/', '', $amount);
        return sprintf("%0.2f", round($amount, 3));
    }

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

    public static function cleanPhoneNumber($number)
    {
        return preg_replace('/\D+/', '', $number);
    }

    public static function cleanZipCode($zip)
    {
        return preg_replace('/\D+/', '', $zip);
    }

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
}

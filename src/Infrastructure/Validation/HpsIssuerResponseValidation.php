<?php

class HpsIssuerResponseValidation
{
    public static $_issuerCodeToCreditExceptionCode = array(
        '02' => HpsExceptionCodes::CARD_DECLINED,
        '03' => HpsExceptionCodes::CARD_DECLINED,
        '04' => HpsExceptionCodes::CARD_DECLINED,
        '05' => HpsExceptionCodes::CARD_DECLINED,
        '41' => HpsExceptionCodes::CARD_DECLINED,
        '43' => HpsExceptionCodes::CARD_DECLINED,
        '44' => HpsExceptionCodes::CARD_DECLINED,
        '51' => HpsExceptionCodes::CARD_DECLINED,
        '56' => HpsExceptionCodes::CARD_DECLINED,
        '61' => HpsExceptionCodes::CARD_DECLINED,
        '62' => HpsExceptionCodes::CARD_DECLINED,
        '63' => HpsExceptionCodes::CARD_DECLINED,
        '65' => HpsExceptionCodes::CARD_DECLINED,
        '78' => HpsExceptionCodes::CARD_DECLINED,
        '06' => HpsExceptionCodes::PROCESSING_ERROR,
        '07' => HpsExceptionCodes::PROCESSING_ERROR,
        '12' => HpsExceptionCodes::PROCESSING_ERROR,
        '15' => HpsExceptionCodes::PROCESSING_ERROR,
        '19' => HpsExceptionCodes::PROCESSING_ERROR,
        '52' => HpsExceptionCodes::PROCESSING_ERROR,
        '53' => HpsExceptionCodes::PROCESSING_ERROR,
        '57' => HpsExceptionCodes::PROCESSING_ERROR,
        '58' => HpsExceptionCodes::PROCESSING_ERROR,
        '76' => HpsExceptionCodes::PROCESSING_ERROR,
        '77' => HpsExceptionCodes::PROCESSING_ERROR,
        '96' => HpsExceptionCodes::PROCESSING_ERROR,
        'EC' => HpsExceptionCodes::PROCESSING_ERROR,
        '13' => HpsExceptionCodes::INVALID_AMOUNT,
        '14' => HpsExceptionCodes::INCORRECT_NUMBER,
        '54' => HpsExceptionCodes::EXPIRED_CARD,
        '55' => HpsExceptionCodes::INVALID_PIN,
        '75' => HpsExceptionCodes::PIN_ENTRIES_EXCEEDED,
        '80' => HpsExceptionCodes::INVALID_EXPIRY,
        '86' => HpsExceptionCodes::PIN_VERIFICATION,
        '91' => HpsExceptionCodes::ISSUER_TIMEOUT,
        'EB' => HpsExceptionCodes::INCORRECT_CVC,
        'N7' => HpsExceptionCodes::INCORRECT_CVC,
    );

    public static $_creditExceptionCodeToMessage = array(
        HpsExceptionCodes::CARD_DECLINED        => "The card was declined.",
        HpsExceptionCodes::PROCESSING_ERROR     => "An error occurred while processing the card.",
        HpsExceptionCodes::INVALID_AMOUNT       => "Must be greater than or equal 0.",
        HpsExceptionCodes::EXPIRED_CARD         => "The card has expired.",
        HpsExceptionCodes::INVALID_PIN          => "The 4-digit pin is invalid.",
        HpsExceptionCodes::PIN_ENTRIES_EXCEEDED => "Maximum number of pin retries exceeded.",
        HpsExceptionCodes::INVALID_EXPIRY       => "Card expiration date is invalid.",
        HpsExceptionCodes::PIN_VERIFICATION     => "Can't verify card pin number.",
        HpsExceptionCodes::INCORRECT_CVC        => "The card's security code is incorrect.",
        HpsExceptionCodes::ISSUER_TIMEOUT       => "The card issuer timed-out.",
        HpsExceptionCodes::UNKNOWN_CREDIT_ERROR => "An unknown issuer error has occurred.",
        HpsExceptionCodes::INCORRECT_NUMBER     => "The card number is incorrect."
    );

    public static function checkResponse($transactionId, $responseCode, $responseText)
    {
        $e = HpsIssuerResponseValidation::getException($transactionId, $responseCode, $responseText);

        if ($e != null) {
            throw $e;
        }
    }

    public static function getException($transactionId, $responseCode, $responseText)
    {
        $acceptedCodes = array('85', '00', '0', '10');
        $responseCode = (string)$responseCode;
        if (in_array($responseCode, $acceptedCodes)) {
            return null;
        }

        $code = null;
        if (array_key_exists($responseCode, self::$_issuerCodeToCreditExceptionCode)) {
            $code = self::$_issuerCodeToCreditExceptionCode[$responseCode];
        }

        if ($code == null) {
            return new HpsCreditException(
                $transactionId,
                HpsExceptionCodes::UNKNOWN_CREDIT_ERROR,
                self::$_creditExceptionCodeToMessage[HpsExceptionCodes::UNKNOWN_CREDIT_ERROR],
                $responseCode,
                $responseText
            );
        }

        $message = null;
        if (array_key_exists($code, self::$_creditExceptionCodeToMessage)) {
            $message = self::$_creditExceptionCodeToMessage[$code];
        } else {
            $message = 'Unknown issuer error';
        }

        return new HpsCreditException($transactionId, $code, $message, $responseCode, $responseText);
    }
}

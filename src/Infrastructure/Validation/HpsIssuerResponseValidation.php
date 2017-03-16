<?php

/**
 * Class HpsIssuerResponseValidation
 */
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
        'FR' => HpsExceptionCodes::POSSIBLE_FRAUD_DETECTED,
    );

    public static $_issuerCodeToGiftExceptionCode = array(
        '1'  => HpsExceptionCodes::UNKNOWN_GIFT_ERROR,
        '2'  => HpsExceptionCodes::UNKNOWN_GIFT_ERROR,
        '11' => HpsExceptionCodes::UNKNOWN_GIFT_ERROR,
        '3'  => HpsExceptionCodes::INVALID_CARD_DATA,
        '8'  => HpsExceptionCodes::INVALID_CARD_DATA,
        '4'  => HpsExceptionCodes::EXPIRED_CARD,
        '5'  => HpsExceptionCodes::CARD_DECLINED,
        '12' => HpsExceptionCodes::CARD_DECLINED,
        '6'  => HpsExceptionCodes::PROCESSING_ERROR,
        '7'  => HpsExceptionCodes::PROCESSING_ERROR,
        '10' => HpsExceptionCodes::PROCESSING_ERROR,
        '9'  => HpsExceptionCodes::INVALID_AMOUNT,
        '13' => HpsExceptionCodes::PARTIAL_APPROVAL,
        '14' => HpsExceptionCodes::INVALID_PIN,
    );

    public static $_creditExceptionCodeToMessage = array(
        HpsExceptionCodes::CARD_DECLINED        => "The card was declined.",
        HpsExceptionCodes::PROCESSING_ERROR     => "An error occurred while processing the card.",
        HpsExceptionCodes::INVALID_AMOUNT       => "Must be greater than or equal 0.",
        HpsExceptionCodes::EXPIRED_CARD         => "The card has expired.",
        HpsExceptionCodes::INVALID_PIN          => "The pin is invalid.",
        HpsExceptionCodes::PIN_ENTRIES_EXCEEDED => "Maximum number of pin retries exceeded.",
        HpsExceptionCodes::INVALID_EXPIRY       => "Card expiration date is invalid.",
        HpsExceptionCodes::PIN_VERIFICATION     => "Can't verify card pin number.",
        HpsExceptionCodes::INCORRECT_CVC        => "The card's security code is incorrect.",
        HpsExceptionCodes::ISSUER_TIMEOUT       => "The card issuer timed-out.",
        HpsExceptionCodes::UNKNOWN_CREDIT_ERROR => "An unknown issuer error has occurred.",
        HpsExceptionCodes::INCORRECT_NUMBER     => "The card number is incorrect.",
        HpsExceptionCodes::POSSIBLE_FRAUD_DETECTED => "Possible fraud detected",
        HpsExceptionCodes::UNKNOWN_GIFT_ERROR   => "An unknown gift error has occurred.",
        HpsExceptionCodes::PARTIAL_APPROVAL     => "The amount was partially approved.",
        HpsExceptionCodes::INVALID_CARD_DATA    => "The card data is invalid.",
    );
    /**
     * @param        $transactionId
     * @param        $responseCode
     * @param        $responseText
     * @param string $type
     *
     * @throws \HpsCreditException
     * @throws null
     */
    public static function checkResponse($transactionId, $responseCode, $responseText, $type = 'credit')
    {
        $e = HpsIssuerResponseValidation::getException(
            (string)$transactionId,
            (string)$responseCode,
            (string)$responseText,
            $type
        );

        if ($e != null) {
            throw $e;
        }
    }
    /**
     * @param $transactionId
     * @param $responseCode
     * @param $responseText
     * @param $type
     *
     * @return \HpsCreditException|null
     */
    public static function getException($transactionId, $responseCode, $responseText, $type)
    {
        $acceptedCodes = array('00', '0');
        $map = array();

        switch ($type) {
        case 'credit':
            $acceptedCodes = array_merge($acceptedCodes, array('85', '10'));
            $map = self::$_issuerCodeToCreditExceptionCode;
            break;
        case 'gift':
            $acceptedCodes = array_merge($acceptedCodes, array('13'));
            $map = self::$_issuerCodeToGiftExceptionCode;
            break;
        }

        if (in_array($responseCode, $acceptedCodes)) {
            return null;
        }

        $code = null;
        if (array_key_exists($responseCode, $map)) {
            $code = $map[$responseCode];
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

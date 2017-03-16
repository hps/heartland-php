<?php

/**
 * Class HpsIssuerResponseValidationTest
 */
class HpsIssuerResponseValidationTest extends PHPUnit_Framework_TestCase
{
    public function testCheckResponseSuccess()
    {
        $this->assertNull(HpsIssuerResponseValidation::checkResponse(null, '00', null));
    }

    /**
     * @expectedException        HpsCreditException
     * @expectedExceptionCode    HpsExceptionCodes::CARD_DECLINED
     * @expectedExceptionMessage The card was declined.
     */
    public function testCheckResponseFailure()
    {
        HpsIssuerResponseValidation::checkResponse(null, '02', null);
    }

    /// Credit

    public function testGetExceptionSuccess()
    {
        $sxml = $this->createStringFromTextNode('00');
        $codes = array('85', '00', '0', '10', $sxml);
        foreach ($codes as $code) {
            $this->assertNull(HpsIssuerResponseValidation::getException(null, $code, null, 'credit'));
        }
    }

    public function testGetExceptionCardDeclined()
    {
        $sxml = $this->createStringFromTextNode('02');
        $codes = array('02', '04', '41', '56', $sxml);
        $this->assertExceptionValues(
            $codes,
            HpsExceptionCodes::CARD_DECLINED,
            'The card was declined.',
            'credit'
        );
    }

    public function testGetExceptionProcessingError()
    {
        $sxml = $this->createStringFromTextNode('06');
        $codes = array('06', '52', '76', 'EC', $sxml);
        $this->assertExceptionValues(
            $codes,
            HpsExceptionCodes::PROCESSING_ERROR,
            'An error occurred while processing the card.',
            'credit'
        );
    }

    public function testGetExceptionInvalidAmount()
    {
        $sxml = $this->createStringFromTextNode('13');
        $codes = array('13', $sxml);
        $this->assertExceptionValues(
            $codes,
            HpsExceptionCodes::INVALID_AMOUNT,
            'Must be greater than or equal 0.',
            'credit'
        );
    }

    public function testGetExceptionIncorrectNumber()
    {
        $sxml = $this->createStringFromTextNode('14');
        $codes = array('14', $sxml);
        $this->assertExceptionValues(
            $codes,
            HpsExceptionCodes::INCORRECT_NUMBER,
            'The card number is incorrect.',
            'credit'
        );
    }

    public function testGetExceptionExpiredCard()
    {
        $sxml = $this->createStringFromTextNode('54');
        $codes = array('54', $sxml);
        $this->assertExceptionValues(
            $codes,
            HpsExceptionCodes::EXPIRED_CARD,
            'The card has expired.',
            'credit'
        );
    }

    public function testGetExceptionInvalidPin()
    {
        $sxml = $this->createStringFromTextNode('55');
        $codes = array('55', $sxml);
        $this->assertExceptionValues(
            $codes,
            HpsExceptionCodes::INVALID_PIN,
            'The pin is invalid.',
            'credit'
        );
    }

    public function testGetExceptionPinEntriesExceeded()
    {
        $sxml = $this->createStringFromTextNode('75');
        $codes = array('75', $sxml);
        $this->assertExceptionValues(
            $codes,
            HpsExceptionCodes::PIN_ENTRIES_EXCEEDED,
            'Maximum number of pin retries exceeded.',
            'credit'
        );
    }

    public function testGetExceptionInvalidExpiry()
    {
        $sxml = $this->createStringFromTextNode('80');
        $codes = array('80', $sxml);
        $this->assertExceptionValues(
            $codes,
            HpsExceptionCodes::INVALID_EXPIRY,
            'Card expiration date is invalid.',
            'credit'
        );
    }

    public function testGetExceptionPinVerification()
    {
        $sxml = $this->createStringFromTextNode('86');
        $codes = array('86', $sxml);
        $this->assertExceptionValues(
            $codes,
            HpsExceptionCodes::PIN_VERIFICATION,
            'Can\'t verify card pin number.',
            'credit'
        );
    }

    public function testGetExceptionIssuerTimeout()
    {
        $sxml = $this->createStringFromTextNode('91');
        $codes = array('91', $sxml);
        $this->assertExceptionValues(
            $codes,
            HpsExceptionCodes::ISSUER_TIMEOUT,
            'The card issuer timed-out.',
            'credit'
        );
    }

    public function testGetExceptionIncorrectCVC()
    {
        $sxml = $this->createStringFromTextNode('EB');
        $codes = array('EB', 'N7', $sxml);
        $this->assertExceptionValues(
            $codes,
            HpsExceptionCodes::INCORRECT_CVC,
            'The card\'s security code is incorrect.',
            'credit'
        );
    }

    /// Gift
    public function testGetExceptionGiftUnknownGiftError()
    {
        $sxml = $this->createStringFromTextNode('1');
        $codes = array('1', '2', '11', $sxml);
        $this->assertExceptionValues(
            $codes,
            HpsExceptionCodes::UNKNOWN_GIFT_ERROR,
            'An unknown gift error has occurred.',
            'gift'
        );
    }

    public function testGetExceptionGiftInvalidCardData()
    {
        $sxml = $this->createStringFromTextNode('3');
        $codes = array('8', '8', $sxml);
        $this->assertExceptionValues(
            $codes,
            HpsExceptionCodes::INVALID_CARD_DATA,
            'The card data is invalid.',
            'gift'
        );
    }

    public function testGetExceptionGiftExpiredCard()
    {
        $sxml = $this->createStringFromTextNode('4');
        $codes = array('4', $sxml);
        $this->assertExceptionValues(
            $codes,
            HpsExceptionCodes::EXPIRED_CARD,
            'The card has expired.',
            'gift'
        );
    }

    public function testGetExceptionGiftCardDeclined()
    {
        $sxml = $this->createStringFromTextNode('5');
        $codes = array('5', '12', $sxml);
        $this->assertExceptionValues(
            $codes,
            HpsExceptionCodes::CARD_DECLINED,
            'The card was declined.',
            'gift'
        );
    }

    public function testGetExceptionGiftProcessingError()
    {
        $sxml = $this->createStringFromTextNode('6');
        $codes = array('6', '7', '10', $sxml);
        $this->assertExceptionValues(
            $codes,
            HpsExceptionCodes::PROCESSING_ERROR,
            'An error occurred while processing the card.',
            'gift'
        );
    }

    public function testGetExceptionGiftInvalidAmount()
    {
        $sxml = $this->createStringFromTextNode('9');
        $codes = array('9', $sxml);
        $this->assertExceptionValues(
            $codes,
            HpsExceptionCodes::INVALID_AMOUNT,
            'Must be greater than or equal 0.',
            'gift'
        );
    }

    public function testGetExceptionGiftPartialAmount()
    {
        $sxml = $this->createStringFromTextNode('13');
        $codes = array('13', $sxml);
        foreach ($codes as $code) {
            $this->assertNull(HpsIssuerResponseValidation::getException(null, $code, null, 'gift'));
        }
    }
    /**
     * @param $codes
     * @param $exceptionCode
     * @param $exceptionMessage
     * @param $type
     */
    protected function assertExceptionValues($codes, $exceptionCode, $exceptionMessage, $type)
    {
        foreach ($codes as $code) {
            $exception = HpsIssuerResponseValidation::getException(null, $code, null, $type);
            $this->assertNotNull($exception);
            $this->assertEquals($exceptionCode, $exception->getCode());
            $this->assertEquals($exceptionMessage, $exception->getMessage());
        }
    }
    /**
     * @param $value
     *
     * @return string
     */
    protected function createStringFromTextNode($value)
    {
        return (string)simplexml_load_string('<RspCode>' . $value . '</RspCode>');
    }
}

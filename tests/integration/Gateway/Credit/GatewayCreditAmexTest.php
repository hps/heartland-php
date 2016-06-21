<?php

class GatewayCreditAmexTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * /// The AMEX is ok test method.
     */
    public function testAmexWhenCardIsOkShouldReturnValidResult()
    {
        $response = $this->chargeValidAmex(50);
        $this->assertEquals($response->responseCode, "00");
    }

    #region Update Expiration date on token

    /**
     * @test
     *
     */

    public function testUpdateTokenExpirationShouldReturnOk()
    {
        $testConfig = new TestServicesConfig();
        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->updateTokenExpiration(TestCreditCard::validAmexMUT(), 1, 2019);
        $this->assertEquals("0", $response->responseCode);
    }


    #endregion

    #region AVS Tests

    /**
     * @test
     * /// AVS result code should be "A" test method.
     */
    public function testAmexAVSRsltCodeShouldEqualA()
    {
        $response = $this->chargeValidAmex(90.01);
        $this->assertEquals($response->avsResultCode, "A");
    }

    /**
     * @test
     * /// AVS result code should be "N" test method.
     */
    public function testAmexAVSRsltCodeShouldEqualN()
    {
        $response = $this->chargeValidAmex(90.02);
        $this->assertEquals($response->avsResultCode, "N");
    }

    /**
     * @test
     * /// AVS result code should be "R" test method.
     */
    public function testAmexAVSRsltCodeShouldEqualR()
    {
        $response = $this->chargeValidAmex(90.03);
        $this->assertEquals($response->avsResultCode, "R");
    }

    /**
     * @test
     * /// AVS result code should be "S" test method.
     */
    public function testAmexAVSRsltCodeShouldEqualS()
    {
        $response = $this->chargeValidAmex(90.04);
        $this->assertEquals($response->avsResultCode, "S");
    }

    /**
     * @test
     * /// AVS result code should be "U" test method.
     */
    public function testAmexAVSRsltCodeShouldEqualU()
    {
        $response = $this->chargeValidAmex(90.05);
        $this->assertEquals($response->avsResultCode, "U");
    }

    /**
     * @test
     * /// AVS result code should be "W" test method.
     */
    public function testAmexAVSRsltCodeShouldEqualW()
    {
        $response = $this->chargeValidAmex(90.06);
        $this->assertEquals($response->avsResultCode, "W");
    }

    /**
     * @test
     * /// AVS result code should be "X" test method.
     */
    public function testAmexAVSRsltCodeShouldEqualX()
    {
        $response = $this->chargeValidAmex(90.07);
        $this->assertEquals($response->avsResultCode, "X");
    }

    /**
     * @test
     * /// AVS result code should be "Y" test method.
     */
    public function testAmexAVSRsltCodeShouldEqualY()
    {
        $response = $this->chargeValidAmex(90.08);
        $this->assertEquals($response->avsResultCode, "Y");
    }

    /**
     * @test
     * /// AVS result code should be "Z" test method.
     */
    public function testAmexAVSRsltCodeShouldEqualZ()
    {
        $response = $this->chargeValidAmex(90.09);
        $this->assertEquals($response->avsResultCode, "Z");
    }

    #endregion

    #region CVV Tests

    /**
     * @test
     * /// CVV result code should be "M" test method.
      * /// Changed as of 2013-08-19 expect Y instead
      * /// Changed back as of 2014-02-11 to "M"
     */
    public function testAmexCVVRsltCodeShouldEqualM()
    {
        $response = $this->chargeValidAmex(97.01);
        $this->assertEquals($response->cvvResultCode, "M");
    }

    /**
     * @test
     * /// CVV result code should be "N" test method.
     */
    public function testAmexCVVRsltCodeShouldEqualN()
    {
        $response = $this->chargeValidAmex(97.02);
        $this->assertEquals($response->cvvResultCode, "N");
    }

    /**
     * @test
     * /// CVV result code should be "P" test method.
     */
    public function testAmexCVVRsltCodeShouldEqualP()
    {
        $response = $this->chargeValidAmex(97.03);
        $this->assertEquals($response->cvvResultCode, "P");
    }

    #endregion

    #region Amex to Visa 2nd

    /**
     * @test
     * /// Transaction response code should indicate denied (ResponseText: 'DECLINE', ResponseCode: '51').
     */
    public function testAmexResponseCodeShouldIndicateDenied()
    {
        try {
            $this->chargeValidAmex(10.08);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate card expired (ResponseText: 'EXPIRED CARD', ResponseCode: '54').
     */
    public function testAmexResponseCodeShouldIndicateCardExpired()
    {
        try {
            $this->chargeValidAmex(10.32);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::EXPIRED_CARD, $e->code);
            $this->assertEquals("The card has expired.", $e->getMessage());
            return;
        }

        $this->Fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate please call (ResponseText: 'CALL', ResponseCode: '02').
     */
    public function testAmexResponseCodeShouldIndicatePleaseCall()
    {
        try {
            $this->chargeValidAmex(10.34);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->Fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate invalid merchant (ResponseText: 'TERM ID ERROR', ResponseCode: '03').
     */
    public function testAmexResponseCodeShouldIndicateInvalidMerchant()
    {
        try {
            $this->chargeValidAmex(10.22);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->Fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate invalid amount (ResponseText: 'AMOUNT ERROR', ResponseCode: '13').
     */
    public function testAmexResponseCodeShouldIndicateInvalidAmount()
    {
        try {
            $this->chargeValidAmex(10.27);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::INVALID_AMOUNT, $e->code);
            $this->assertEquals("Must be greater than or equal 0.", $e->getMessage());
            return;
        }

        $this->Fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate incorect card number (ResponseCode: '14')
     */
    public function testAmexResponseCodeShouldIndicateNoActionTaken()
    {
        try {
            $this->chargeValidAmex(10.14);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::INCORRECT_NUMBER, $e->code);
            $this->assertEquals("The card number is incorrect.", $e->getMessage());
            return;
        }

        $this->Fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate invalid CVV2 (ResponseText: 'CVV2 MISMATCH', ResponseCode: 'N7').
     */
    public function testAmexResponseCodeShouldIndicateInvalidCvv2()
    {
        try {
            $this->chargeValidAmex(10.23);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::INCORRECT_CVC, $e->code);
            $this->assertEquals("The card's security code is incorrect.", $e->getMessage());
            return;
        }

        $this->Fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate message format error (ResponseText: 'CID FORMAT ERROR', ResponseCode: 'EC').
     */
    public function testAmexResponseCodeShouldIndicateMessageFormatError()
    {
        try {
            $this->chargeValidAmex(10.06);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::PROCESSING_ERROR, $e->code);
            $this->assertEquals("An error occurred while processing the card.", $e->getMessage());
            return;
        }

        $this->Fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate invalid originator (ResponseText: 'SERV NOT ALLOWED', ResponseCode: '58').
     */
    public function testAmexResponseCodeShouldIndicateInvalidOriginator()
    {
        try {
            $this->chargeValidAmex(10.30);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::PROCESSING_ERROR, $e->code);
            $this->assertEquals("An error occurred while processing the card.", $e->getMessage());
            return;
        }

        $this->Fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate card declined (ResponseText: 'DECLINE', ResponseCode: '05').
     */
    public function testAmexResponseCodeShouldIndicateCardDeclined()
    {
        try {
            $this->chargeValidAmex(10.25);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->Fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate account cancelled (ResponseText: 'NO ACCOUNT', ResponseCode: '78').
     */
    public function testAmexResponseCodeShouldIndicateAccountCancelled()
    {
        try {
            $this->chargeValidAmex(10.13);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->Fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate merchant close (ResponseText: 'ERROR', ResponseCode: '06').
     */
    public function testAmexResponseCodeShouldIndicateMerchantClose()
    {
        try {
            $this->chargeValidAmex(10.12);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::PROCESSING_ERROR, $e->code);
            $this->assertEquals("An error occurred while processing the card.", $e->getMessage());
            return;
        }

        $this->Fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate pick up card (ResponseText: 'HOLD-CALL', ResponseCode: '44').
     */
    public function testAmexResponseCodeShouldIndicatePickUpCard()
    {
        try {
            $this->chargeValidAmex(10.04);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->Fail("No exception was thrown.");
    }

    #endregion

    #region Verify, Authorize & Capture

    /**
     * @test
     * /// Amex verify should return response code '85'.
     */
    public function testAmexVerifyShouldReturnOk()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->Verify(TestCreditCard::validAmexCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("00", $response->responseCode);
    }

    /**
     * @test
     * /// Amex authorize should return response code '00'.
     */
    public function testAmexAuthorizeShouldReturnOk()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->authorize(50, "usd", TestCreditCard::validAmexCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("00", $response->responseCode);
    }

    /**
     * @test
     * /// Amex authorize should return response code '00'.
     */
    public function testAmexAuthorizeAndRequestTokenShouldGetTokenAndReturnOk()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->authorize(50, "usd", TestCreditCard::validAmexCreditCard(), TestCardHolder::ValidCardHolder(), true);
        $this->assertEquals("0", $response->tokenData->responseCode);
        $this->assertEquals("00", $response->responseCode);
    }

    /**
     * @test
     * /// Amex authorize should return response code '00'.
     */
    public function testAmexCaptureShouldReturnOk()
    {
        $testConfig = new TestServicesConfig();

        // Authorize the card.
        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $authResponse = $chargeSvc->authorize(50, "usd", TestCreditCard::validAmexCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("00", $authResponse->responseCode);

        // Capture the authorization.
        $captureResponse = $chargeSvc->Capture($authResponse->transactionId);
        $this->assertEquals("0", $captureResponse->responseCode);
    }

    #endregion

    /// Charge an AMEX with a valid config and valid AMEX info.
    /// <param name="amt">Amount to charge</param>
    /// <returns>The HPS Charge.</returns>
    private function chargeValidAmex($amt)
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->charge($amt, "usd", TestCreditCard::validAmexCreditCard(), TestCardHolder::ValidCardHolder());
        if ($response == null) {
            $this->Fail("Response is null.");
        }

        return $response;
    }
}

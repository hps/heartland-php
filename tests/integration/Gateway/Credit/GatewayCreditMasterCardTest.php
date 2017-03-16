<?php

/**
 * Class GatewayCreditMasterCardTest
 */
class GatewayCreditMasterCardTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * /// The MasterCard is ok test method.
     */
    public function testMasterCardWhenCardIsOkShouldReturnValidResult()
    {
        $response = $this->chargeValidMasterCard(50);
        $this->assertEquals($response->responseCode, "00");
    }

    #region AVS Tests

    /**
     * @test
     * /// AVS result code should be "A" test method.
     */
    public function testMasterCardAVSRsltCodeShouldEqualA()
    {
        $response = $this->chargeValidMasterCard(90.01);
        $this->assertEquals($response->avsResultCode, "A");
    }

    /**
     * @test
     * /// AVS result code should be "N" test method.
     */
    public function testMasterCardAVSRsltCodeShouldEqualN()
    {
        $response = $this->chargeValidMasterCard(90.02);
        $this->assertEquals($response->avsResultCode, "N");
    }

    /**
     * @test
     * /// AVS result code should be "R" test method.
     */
    public function testMasterCardAVSRsltCodeShouldEqualR()
    {
        $response = $this->chargeValidMasterCard(90.03);
        $this->assertEquals($response->avsResultCode, "R");
    }

    /**
     * @test
     * /// AVS result code should be "S" test method.
     */
    public function testMasterCardAVSRsltCodeShouldEqualS()
    {
        $response = $this->chargeValidMasterCard(90.04);
        $this->assertEquals($response->avsResultCode, "S");
    }

    /**
     * @test
     * /// AVS result code should be "U" test method.
     */
    public function testMasterCardAVSRsltCodeShouldEqualU()
    {
        $response = $this->chargeValidMasterCard(90.05);
        $this->assertEquals($response->avsResultCode, "U");
    }

    /**
     * @test
     * /// AVS result code should be "W" test method.
     */
    public function testMasterCardAVSRsltCodeShouldEqualW()
    {
        $response = $this->chargeValidMasterCard(90.06);
        $this->assertEquals($response->avsResultCode, "W");
    }

    /**
     * @test
     * /// AVS result code should be "X" test method.
     */
    public function testMasterCardAVSRsltCodeShouldEqualX()
    {
        $response = $this->chargeValidMasterCard(90.07);
        $this->assertEquals($response->avsResultCode, "X");
    }

    /**
     * @test
     * /// AVS result code should be "Y" test method.
     */
    public function testMasterCardAVSRsltCodeShouldEqualY()
    {
        $response = $this->chargeValidMasterCard(90.08);
        $this->assertEquals($response->avsResultCode, "Y");
    }

    /**
     * @test
     * /// AVS result code should be "Z" test method.
     */
    public function testMasterCardAVSRsltCodeShouldEqualZ()
    {
        $response = $this->chargeValidMasterCard(90.09);
        $this->assertEquals($response->avsResultCode, "Z");
    }

    #endregion

    #region Update Expiration date on token

    /**
     * @test
     *
     */

    public function testUpdateTokenExpirationShouldReturnOk()
    {
        $testConfig = new TestServicesConfig();
        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->updateTokenExpiration(TestCreditCard::validMastercardMUT(), 1, 2019);
        $this->assertEquals("0", $response->responseCode);
    }


    #endregion

    #region CVV Tests

    /**
     * @test
     * /// CVV result code should be "M" test method.
     */
    public function testMasterCardCvvResultCodeShouldEqualM()
    {
        $response = $this->chargeValidMasterCard(95.01);
        $this->assertEquals($response->cvvResultCode, "M");
    }

    /**
     * @test
     * /// CVV result code should be "N" test method.
     */
    public function testMasterCardCvvResultCodeShouldEqualN()
    {
        $response = $this->chargeValidMasterCard(95.02);
        $this->assertEquals($response->cvvResultCode, "N");
    }

    /**
     * @test
     * /// CVV result code should be "P" test method.
     */
    public function testMasterCardCvvResultCodeShouldEqualP()
    {
        $response = $this->chargeValidMasterCard(95.03);
        $this->assertEquals($response->cvvResultCode, "P");
    }

    /**
     * @test
     * /// CVV result code should be "U" test method.
     */
    public function testMasterCardCvvResultCodeShouldEqualU()
    {
        $response = $this->chargeValidMasterCard(95.04);
        $this->assertEquals($response->cvvResultCode, "U");
    }

    #endregion

    #region MasterCard to 8583

    /**
     * @test
     * /// Transaction response code should indicate refer card issuer (ResponseText: 'CALLS', ResponseCode: '02').
     */
    public function testMastercardResponseCodeShouldIndicateReferCardIssuer()
    {
        try {
            $this->chargeValidMasterCard(10.34);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate term ID error (ResponseText: 'TERM ID ERROR', ResponseCode: '03').
     */
    public function testMastercardResponseCodeShouldIndicateTermIdError()
    {
        try {
            $this->chargeValidMasterCard(10.22);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate invalid merchant (ResponseText: 'HOLD-CALL', ResponseCode: '04').
     */
    public function testMastercardResponseCodeShouldIndicateInvalidMerchant()
    {
        try {
            $this->chargeValidMasterCard(10.01);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate decline (ResponseText: 'DECLINE', ResponseCode: '05').
     */
    public function testMastercardResponseCodeShouldIndicateDoNotHonor()
    {
        try {
            $this->chargeValidMasterCard(10.25);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate invalid transaction (ResponseText: 'INVALID TRANS', ResponseCode: '12').
     */
    public function testMastercardResponseCodeShouldIndicateInvalidTransaction()
    {
        try {
            $this->chargeValidMasterCard(10.26);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::PROCESSING_ERROR, $e->code);
            $this->assertEquals("An error occurred while processing the card.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate invalid amount (ResponseText: 'AMOUNT ERROR', ResponseCode: '13').
     */
    public function testMastercardResponseCodeShouldIndicateInvalidAmount()
    {
        try {
            $this->chargeValidMasterCard(10.27);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::INVALID_AMOUNT, $e->code);
            $this->assertEquals("Must be greater than or equal 0.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate invalid card (ResponseText: 'CARD NO. ERROR', ResponseCode: '14').
     */
    public function testMastercardResponseCodeShouldIndicateInvalidCard()
    {
        try {
            $this->chargeValidMasterCard(10.28);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::INCORRECT_NUMBER, $e->code);
            $this->assertEquals("The card number is incorrect.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate invalid issuer (ResponseText: 'NO SUCH ISSUER', ResponseCode: '15').
     */
    public function testMastercardResponseCodeShouldIndicateInvalidIssuer()
    {
        try {
            $this->chargeValidMasterCard(10.18);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::PROCESSING_ERROR, $e->code);
            $this->assertEquals("An error occurred while processing the card.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate lost card (ResponseText: 'HOLD-CALL', ResponseCode: '41').
     */
    public function testMastercardResponseCodeShouldIndicateLostCard()
    {
        try {
            $this->chargeValidMasterCard(10.31);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate hold-call (ResponseText: 'HOLD-CALL', ResponseCode: '43').
     */
    public function testMastercardResponseCodeShouldIndicateHoldCall()
    {
        try {
            $this->chargeValidMasterCard(10.03);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate decline (ResponseText: 'DECLINE', ResponseCode: '51').
     */
    public function testMastercardResponseCodeShouldIndicateDecline()
    {
        try {
            $this->chargeValidMasterCard(10.08);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate expired card (ResponseText: 'EXPIRED CARD', ResponseCode: '54').
     */
    public function testMastercardResponseCodeShouldIndicateExpiredCard()
    {
        try {
            $this->chargeValidMasterCard(10.32);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::EXPIRED_CARD, $e->code);
            $this->assertEquals("The card has expired.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate exceeds limit (ResponseText: 'DECLINE', ResponseCode: '61').
     */
    public function testMastercardResponseCodeShouldIndicateExceedsLimit()
    {
        try {
            $this->chargeValidMasterCard(10.09);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate restricted card (ResponseText: 'DECLINE', ResponseCode: '62').
     */
    public function testMastercardResponseCodeShouldIndicateRestrictedCard()
    {
        try {
            $this->chargeValidMasterCard(10.10);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate security violation (ResponseText: 'SEC VIOLATION', ResponseCode: '63').
     */
    public function testMastercardResponseCodeShouldIndicateSecurityViolation()
    {
        try {
            $this->chargeValidMasterCard(10.19);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate exceeds freq limit (ResponseText: 'DECLINE$', ResponseCode: '65').
     */
    public function testMastercardResponseCodeShouldIndicateExceedsFreqLimit()
    {
        try {
            $this->chargeValidMasterCard(10.11);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate invalid account (ResponseText: 'CARD NO. ERROR', ResponseCode: '14').
     */
    public function testMastercardResponseCodeShouldIndicateCardNoError()
    {
        try {
            $this->chargeValidMasterCard(10.14);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::INCORRECT_NUMBER, $e->code);
            $this->assertEquals("The card number is incorrect.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate format error (ResponseText: 'CID FORMAT ERROR', ResponseCode: '79').
     */
    public function testMastercardResponseCodeShouldIndicateInvalidAccount()
    {
        try {
            $this->chargeValidMasterCard(10.06);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::PROCESSING_ERROR, $e->code);
            $this->assertEquals("An error occurred while processing the card.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate system error (ResponseText: 'SYSTEM ERROR', ResponseCode: '96').
     */
    public function testMastercardResponseCodeShouldIndicateSystemError()
    {
        try {
            $this->chargeValidMasterCard(10.21);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::PROCESSING_ERROR, $e->code);
            $this->assertEquals("An error occurred while processing the card.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    #endregion

    #region Verify, Authorize, Refund & Capture

    /**
     * @test
     * /// Mastercard verify should return response code '85'.
     */
    public function testMastercardVerifyShouldReturnOk()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->verify(TestCreditCard::validMasterCardCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("85", $response->responseCode);
    }

    /**
     * @test
     * /// Mastercard authorize should return response code '00'.
     */
    public function testMastercardAuthorizeShouldReturnOk()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->authorize(50, "usd", TestCreditCard::validMasterCardCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("00", $response->responseCode);
    }

    /**
     * @test
     * /// Mastercard authorize should return response code '00'.
     */
    public function testMastercardAuthorizeAndRequestTokenShouldGetTokenAndReturnOk()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->authorize(50, "usd", TestCreditCard::validMasterCardCreditCard(), TestCardHolder::ValidCardHolder(), true);
        $this->assertEquals("0", $response->tokenData->responseCode);
        $this->assertEquals("00", $response->responseCode);
    }

    /**
     * @test
     * /// MasterCard refund test.
     */
    public function testMasterCardShouldRefundOk()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $chargeResponse = $chargeSvc->charge(25.00, "usd", TestCreditCard::validMasterCardCreditCard(), TestCardHolder::certCardHolderShortZipNoStreet());
        $refundResponse = $chargeSvc->refund(25.00, "usd", $chargeResponse->transactionId);
        $this->assertEquals($refundResponse->responseCode, "0");
    }

    /**
     * @test
     * /// Mastercard authorize should return response code '00'.
     */
    public function testMastercardCaptureShouldReturnOk()
    {
        $testConfig = new TestServicesConfig();

        // Authorize the card.
        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $authResponse = $chargeSvc->authorize(50, "usd", TestCreditCard::validMasterCardCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("0", $authResponse->responseCode);

        // Capture the authorization.
        $captureResponse = $chargeSvc->capture($authResponse->transactionId);
        $this->assertEquals("0", $captureResponse->responseCode);
    }

    #endregion

    /// Charge a MC with a valid config and valid MC info.
    /// <param name="amt">Amount to charge</param>
    /// <returns>The HPS Charge.</returns>
    /**
     * @param $amt
     *
     * @return array|null
     */
    private function chargeValidMasterCard($amt)
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->charge($amt, "usd", TestCreditCard::validMasterCardCreditCard(), TestCardHolder::ValidCardHolder());
        if ($response == null) {
            $this->fail("Response is null.");
        }

        return $response;
    }
}

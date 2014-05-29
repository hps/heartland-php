<?php

require_once("setup.php");

class MasterCardTests extends PHPUnit_Framework_TestCase
{
    /**
     * @test
    /// The MasterCard is ok test method.
     */
    public function MasterCard_WhenCardIsOk_ShouldReturnValidResult()
    {
        $response = $this->chargeValidMasterCard(50);
        $this->assertEquals($response->responseCode, "00");
    }

    #region AVS Tests

    /**
     * @test
    /// AVS result code should be "A" test method.
     */
    public function MasterCard_AVSRsltCode_ShouldEqualA()
    {
        $response = $this->chargeValidMasterCard(90.01);
        $this->assertEquals($response->avsResultCode, "A");
    }

    /**
     * @test
    /// AVS result code should be "N" test method.
     */
    public function MasterCard_AVSRsltCode_ShouldEqualN()
    {
        $response = $this->chargeValidMasterCard(90.02);
        $this->assertEquals($response->avsResultCode, "N");
    }

    /**
     * @test
    /// AVS result code should be "R" test method.
     */
    public function MasterCard_AVSRsltCode_ShouldEqualR()
    {
        $response = $this->chargeValidMasterCard(90.03);
        $this->assertEquals($response->avsResultCode, "R");
    }

    /**
     * @test
    /// AVS result code should be "S" test method.
     */
    public function MasterCard_AVSRsltCode_ShouldEqualS()
    {
        $response = $this->chargeValidMasterCard(90.04);
        $this->assertEquals($response->avsResultCode, "S");
    }

    /**
     * @test
    /// AVS result code should be "U" test method.
     */
    public function MasterCard_AVSRsltCode_ShouldEqualU()
    {
        $response = $this->chargeValidMasterCard(90.05);
        $this->assertEquals($response->avsResultCode, "U");
    }

    /**
     * @test
    /// AVS result code should be "W" test method.
     */
    public function MasterCard_AVSRsltCode_ShouldEqualW()
    {
        $response = $this->chargeValidMasterCard(90.06);
        $this->assertEquals($response->avsResultCode, "W");
    }

    /**
     * @test
    /// AVS result code should be "X" test method.
     */
    public function MasterCard_AVSRsltCode_ShouldEqualX()
    {
        $response = $this->chargeValidMasterCard(90.07);
        $this->assertEquals($response->avsResultCode, "X");
    }

    /**
     * @test
    /// AVS result code should be "Y" test method.
     */
    public function MasterCard_AVSRsltCode_ShouldEqualY()
    {
        $response = $this->chargeValidMasterCard(90.08);
        $this->assertEquals($response->avsResultCode, "Y");
    }

    /**
     * @test
    /// AVS result code should be "Z" test method.
     */
    public function MasterCard_AVSRsltCode_ShouldEqualZ()
    {
        $response = $this->chargeValidMasterCard(90.09);
        $this->assertEquals($response->avsResultCode, "Z");
    }

    #endregion

    #region CVV Tests

    /**
     * @test
    /// CVV result code should be "M" test method.
     */
    public function MasterCard_CvvResultCode_ShouldEqualM()
    {
        $response = $this->chargeValidMasterCard(95.01);
        $this->assertEquals($response->cvvResultCode, "M");
    }

    /**
     * @test
    /// CVV result code should be "N" test method.
     */
    public function MasterCard_CvvResultCode_ShouldEqualN()
    {
        $response = $this->chargeValidMasterCard(95.02);
        $this->assertEquals($response->cvvResultCode, "N");
    }

    /**
     * @test
    /// CVV result code should be "P" test method.
     */
    public function MasterCard_CvvResultCode_ShouldEqualP()
    {
        $response = $this->chargeValidMasterCard(95.03);
        $this->assertEquals($response->cvvResultCode, "P");
    }

    /**
     * @test
    /// CVV result code should be "U" test method.
     */
    public function MasterCard_CvvResultCode_ShouldEqualU()
    {
        $response = $this->chargeValidMasterCard(95.04);
        $this->assertEquals($response->cvvResultCode, "U");
    }

    #endregion

    #region MasterCard to 8583

    /**
     * @test
    /// Transaction response code should indicate refer card issuer (ResponseText: 'CALLS', ResponseCode: '02').
     */
    public function Mastercard_ResponseCode_ShouldIndicateReferCardIssuer()
    {
        try
        {
            $this->chargeValidMasterCard(10.34);
        }
        catch (CardException $e)
        {
            $this->assertEquals("card_declined", $e->code());
            $this->assertEquals("The card was declined.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate term ID error (ResponseText: 'TERM ID ERROR', ResponseCode: '03').
     */
    public function Mastercard_ResponseCode_ShouldIndicateTermIdError()
    {
        try
        {
            $this->chargeValidMasterCard(10.22);
        }
        catch (CardException $e)
        {
            $this->assertEquals("card_declined", $e->code());
            $this->assertEquals("The card was declined.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate invalid merchant (ResponseText: 'HOLD-CALL', ResponseCode: '04').
     */
    public function Mastercard_ResponseCode_ShouldIndicateInvalidMerchant()
    {
        try
        {
            $this->chargeValidMasterCard(10.01);
        }
        catch (CardException $e)
        {
            $this->assertEquals("card_declined", $e->code());
            $this->assertEquals("The card was declined.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate decline (ResponseText: 'DECLINE', ResponseCode: '05').
     */
    public function Mastercard_ResponseCode_ShouldIndicateDoNotHonor()
    {
        try
        {
            $this->chargeValidMasterCard(10.25);
        }
        catch (CardException $e)
        {
            $this->assertEquals("card_declined", $e->code());
            $this->assertEquals("The card was declined.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate invalid transaction (ResponseText: 'INVALID TRANS', ResponseCode: '12').
     */
    public function Mastercard_ResponseCode_ShouldIndicateInvalidTransaction()
    {
        try
        {
            $this->chargeValidMasterCard(10.26);
        }
        catch (CardException $e)
        {
            $this->assertEquals("processing_error", $e->code());
            $this->assertEquals("An error occurred while processing the card.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate invalid amount (ResponseText: 'AMOUNT ERROR', ResponseCode: '13').
     */
    public function Mastercard_ResponseCode_ShouldIndicateInvalidAmount()
    {
        try
        {
            $this->chargeValidMasterCard(10.27);
        }
        catch (CardException $e)
        {
            $this->assertEquals("invalid_amount", $e->code());
            $this->assertEquals("Must be greater than or equal 0.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate invalid card (ResponseText: 'CARD NO. ERROR', ResponseCode: '14').
     */
    public function Mastercard_ResponseCode_ShouldIndicateInvalidCard()
    {
        try
        {
            $this->chargeValidMasterCard(10.28);
        }
        catch (CardException $e)
        {
            $this->assertEquals("incorrect_number", $e->code());
            $this->assertEquals("The card number is incorrect.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate invalid issuer (ResponseText: 'NO SUCH ISSUER', ResponseCode: '15').
     */
    public function Mastercard_ResponseCode_ShouldIndicateInvalidIssuer()
    {
        try
        {
            $this->chargeValidMasterCard(10.18);
        }
        catch (CardException $e)
        {
            $this->assertEquals("processing_error", $e->code());
            $this->assertEquals("An error occurred while processing the card.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate lost card (ResponseText: 'HOLD-CALL', ResponseCode: '41').
     */
    public function Mastercard_ResponseCode_ShouldIndicateLostCard()
    {
        try
        {
            $this->chargeValidMasterCard(10.31);
        }
        catch (CardException $e)
        {
            $this->assertEquals("card_declined", $e->code());
            $this->assertEquals("The card was declined.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate hold-call (ResponseText: 'HOLD-CALL', ResponseCode: '43').
     */
    public function Mastercard_ResponseCode_ShouldIndicateHoldCall()
    {
        try
        {
            $this->chargeValidMasterCard(10.03);
        }
        catch (CardException $e)
        {
            $this->assertEquals("card_declined", $e->code());
            $this->assertEquals("The card was declined.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate decline (ResponseText: 'DECLINE', ResponseCode: '51').
     */
    public function Mastercard_ResponseCode_ShouldIndicateDecline()
    {
        try
        {
            $this->chargeValidMasterCard(10.08);
        }
        catch (CardException $e)
        {
            $this->assertEquals("card_declined", $e->code());
            $this->assertEquals("The card was declined.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate expired card (ResponseText: 'EXPIRED CARD', ResponseCode: '54').
     */
    public function Mastercard_ResponseCode_ShouldIndicateExpiredCard()
    {
        try
        {
            $this->chargeValidMasterCard(10.32);
        }
        catch (CardException $e)
        {
            $this->assertEquals("expired_card", $e->code());
            $this->assertEquals("The card has expired.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate exceeds limit (ResponseText: 'DECLINE', ResponseCode: '61').
     */
    public function Mastercard_ResponseCode_ShouldIndicateExceedsLimit()
    {
        try
        {
            $this->chargeValidMasterCard(10.09);
        }
        catch (CardException $e)
        {
            $this->assertEquals("card_declined", $e->code());
            $this->assertEquals("The card was declined.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate restricted card (ResponseText: 'DECLINE', ResponseCode: '62').
     */
    public function Mastercard_ResponseCode_ShouldIndicateRestrictedCard()
    {
        try
        {
            $this->chargeValidMasterCard(10.10);
        }
        catch (CardException $e)
        {
            $this->assertEquals("card_declined", $e->code());
            $this->assertEquals("The card was declined.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate security violation (ResponseText: 'SEC VIOLATION', ResponseCode: '63').
     */
    public function Mastercard_ResponseCode_ShouldIndicateSecurityViolation()
    {
        try
        {
            $this->chargeValidMasterCard(10.19);
        }
        catch (CardException $e)
        {
            $this->assertEquals("card_declined", $e->code());
            $this->assertEquals("The card was declined.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate exceeds freq limit (ResponseText: 'DECLINE$', ResponseCode: '65').
     */
    public function Mastercard_ResponseCode_ShouldIndicateExceedsFreqLimit()
    {
        try
        {
            $this->chargeValidMasterCard(10.11);
        }
        catch (CardException $e)
        {
            $this->assertEquals("card_declined", $e->code());
            $this->assertEquals("The card was declined.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate invalid account (ResponseText: 'CARD NO. ERROR', ResponseCode: '14').
     */
    public function Mastercard_ResponseCode_ShouldIndicateCardNoError()
    {
        try
        {
            $this->chargeValidMasterCard(10.14);
        }
        catch (CardException $e)
        {
            $this->assertEquals("incorrect_number", $e->code());
            $this->assertEquals("The card number is incorrect.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate format error (ResponseText: 'CID FORMAT ERROR', ResponseCode: '79').
     */
    public function Mastercard_ResponseCode_ShouldIndicateInvalidAccount()
    {
        try
        {
            $this->chargeValidMasterCard(10.06);
        }
        catch (CardException $e)
        {
            $this->assertEquals("processing_error", $e->code());
            $this->assertEquals("An error occurred while processing the card.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate switch not available (ResponseText: 'NO REPLY', ResponseCode: '14').
     */
    public function Mastercard_ResponseCode_ShouldIndicateSwitchNotAvailable()
    {
        try
        {
            $this->chargeValidMasterCard(10.33);
        }
        catch (HpsException $e)
        {
            $this->assertEquals("issuer_timeout", $e->code());
            $this->assertEquals("Error occurred while reversing a charge due to issuer time-out.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate system error (ResponseText: 'SYSTEM ERROR', ResponseCode: '96').
     */
    public function Mastercard_ResponseCode_ShouldIndicateSystemError()
    {
        try
        {
            $this->chargeValidMasterCard(10.21);
        }
        catch (CardException $e)
        {
            $this->assertEquals("processing_error", $e->code());
            $this->assertEquals("An error occurred while processing the card.",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    #endregion

    #region Verify, Authorize, Refund & Capture

    /**
     * @test
    /// Mastercard verify should return response code '85'.
     */
    public function Mastercard_Verify_ShouldReturnOk()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidServicesConfig());
        $response = $chargeSvc->verify(TestCreditCard::validMasterCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("85", $response->responseCode);
    }

    /**
     * @test
    /// Mastercard authorize should return response code '00'.
     */
    public function Mastercard_Authorize_ShouldReturnOk()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidServicesConfig());
        $response = $chargeSvc->authorize(50, "usd", TestCreditCard::validMasterCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("00", $response->responseCode);
    }

    /**
     * @test
    /// Mastercard authorize should return response code '00'.
     */
    public function Mastercard_AuthorizeAndRequestToken_ShouldGetTokenAndReturnOk()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidServicesConfig());
        $response = $chargeSvc->authorize(50, "usd", TestCreditCard::validMasterCreditCard(), TestCardHolder::ValidCardHolder(), true);
        $this->assertEquals("0", $response->tokenData->responseCode);
        $this->assertEquals("00", $response->responseCode);
    }

    /**
     * @test
    /// MasterCard refund test.
     */
    public function MasterCard_ShouldRefund_Ok()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->CertServicesConfig());
        $chargeResponse = $chargeSvc->charge(25.00, "usd", TestCreditCard::validMasterCreditCard(), TestCardHolder::certCardHolderShortZipNoStreet());
        $refundResponse = $chargeSvc->refundTransaction(25.00, "usd", $chargeResponse->transactionId);
        $this->assertEquals($refundResponse->responseCode, "0");
    }

    /**
     * @test
    /// Mastercard authorize should return response code '00'.
     */
    public function Mastercard_Capture_ShouldReturnOk()
    {
        $testConfig = new TestServicesConfig();

        // Authorize the card.
        $chargeSvc = new HpsCreditService($testConfig->ValidServicesConfig());
        $authResponse = $chargeSvc->authorize(50, "usd", TestCreditCard::validMasterCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("0", $authResponse->responseCode);

        // Capture the authorization.
        $captureResponse = $chargeSvc->capture($authResponse->transactionId);
        $this->assertEquals("0", $captureResponse->responseCode);
    }

    #endregion

    /// Charge a MC with a valid config and valid MC info.
    /// <param name="amt">Amount to charge</param>
    /// <returns>The HPS Charge.</returns>
    private function chargeValidMasterCard($amt)
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->charge($amt, "usd", TestCreditCard::validMasterCreditCard(), TestCardHolder::ValidCardHolder());
        if ($response == null)
        {
            $this->fail("Response is null.");
        }

        return $response;
    }
}
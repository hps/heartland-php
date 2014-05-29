<?php

require_once("setup.php");

class DiscoverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     /// The Discover is ok test method.
     */
    public function Discover_WhenCardIsOk_ShouldReturnValidResult()
    {
        $response = $this->chargeValidDiscover(50);
        $this->assertEquals($response->responseCode, "00");
    }

    #region AVS Tests

    /**
     * @test
        /// AVS result code should be "A" test method.
        // Changed as of 2013-08-19 to return 'Y'
     * // Changed as of 2014-02-21 to return 'A'
     */
    public function Discover_AVSRsltCode_ShouldEqualA()
    {
        $response = $this->chargeValidDiscover(91.01);
        $this->assertEquals($response->avsResultCode, "A");
    }

    /**
     * @test
        /// AVS result code should be "N" test method.
     */
    public function Discover_AVSRsltCode_ShouldEqualN()
    {
        $response = $this->chargeValidDiscover(91.02);
        $this->assertEquals($response->avsResultCode, "N");
    }

    /**
     * @test
        /// AVS result code should be "R" test method.
        // Changed as of 2013-08-19 to return 'U'
     * // Changed as of 2014-02-21 to return 'R'
     */
    public function Discover_AVSRsltCode_ShouldEqualR()
    {
        $response = $this->chargeValidDiscover(91.03);
        $this->assertEquals($response->avsResultCode, "R");
    }

    /**
     * @test
        /// AVS result code should be "U" test method.
        // Changed as of 2013-08-19 to return 'W'
     * // Changed as of 2014-02-21 to return 'R'
     */
    public function Discover_AVSRsltCode_ShouldEqualU()
    {
        $response = $this->chargeValidDiscover(91.05);
        $this->assertEquals($response->avsResultCode, "U");
    }

    /**
     * @test
        /// AVS result code should be "Y" test method.
        // Changed as of 2013-08-19 to return 'A'
     * // Changed as of 2014-02-21 to return 'Y'
     */
    public function Discover_AVSRsltCode_ShouldEqualY()
    {
        $response = $this->chargeValidDiscover(91.06);
        $this->assertEquals($response->avsResultCode, "Y");
    }

    /**
     * @test
        /// AVS result code should be "Z" test method.
        // Changed as of 2013-08-19 to return 'T'
     * // Changed as of 2014-02-21 to return 'Z'
     */
    public function Discover_AVSRsltCode_ShouldEqualZ()
    {
        $response = $this->chargeValidDiscover(91.07);
        $this->assertEquals($response->avsResultCode, "Z");
    }

    #endregion

    #region Discover to Visa 2nd

    /**
     * @test
    /// Transaction response code should indicate refer card issuer (ResponseText: 'CALLS', ResponseCode: '02').
     */
    public function Discover_ResponseCode_ShouldIndicateReferCardIssuer()
    {
        try
        {
            $this->chargeValidDiscover(10.34);
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
    /// Transaction response code should indicate invalid merchant (ResponseText: 'TERM ID ERROR', ResponseCode: '03').
     */
    public function Discover_ResponseCode_ShouldIndicateInvalidMerchant()
    {
        try
        {
            $this->chargeValidDiscover(10.22);
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
    /// Transaction response code should indicate pick up card (ResponseText: 'HOLD-CALLS', ResponseCode: '44').
     */
    public function Discover_ResponseCode_ShouldIndicatePickUpCard()
    {
        try
        {
            $this->chargeValidDiscover(10.04);
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
    /// Transaction response code should indicate do not honor (ResponseText: 'DECLINE', ResponseCode: '05').
     */
    public function Discover_ResponseCode_ShouldIndicateDoNotHonor()
    {
        try
        {
            $this->chargeValidDiscover(10.25);
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
    public function Discover_ResponseCode_ShouldIndicateInvalidTransaction()
    {
        try
        {
            $this->chargeValidDiscover(10.26);
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
    public function Discover_ResponseCode_ShouldIndicateInvalidAmount()
    {
        try
        {
            $this->chargeValidDiscover(10.27);
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
    public function Discover_ResponseCode_ShouldIndicateInvalidCard()
    {
        try
        {
            $this->chargeValidDiscover(10.28);
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
/* // As of 2013-08-19 this no longer returns a non-zero response code.
    public function Discover_ResponseCode_ShouldIndicateInvalidIssuer()
    {
        try
        {
            $this->chargeValidDiscover(10.18);
        }
        catch (CardException $e)
        {
            $this->assertEquals(ExceptionCodes::ProcessingError, $e->Code());
            $this->assertEquals($e->Message(), ExceptionMessages::ProcessingError);
            return;
        }

        $this->fail("No exception was thrown.");
    }
*/
    /**
     * @test
    /// Transaction response code should indicate system error re-enter (ResponseText: 'RE ENTER', ResponseCode: '19').
     */
    public function Discover_ResponseCode_ShouldIndicateSystemErrorReenter()
    {
        try
        {
            $this->chargeValidDiscover(10.29);
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
    /// Transaction response code should indicate message format error (ResponseText: 'CID FORMAT ERROR', ResponseCode: 'EC').
     */
    public function Discover_ResponseCode_ShouldIndicateMessageFormatError()
    {
        try
        {
            $this->chargeValidDiscover(10.06);
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
    public function Discover_ResponseCode_ShouldIndicateLostCard()
    {
        try
        {
            $this->chargeValidDiscover(10.31);
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
    /// Transaction response code should indicate insufficient funds (ResponseText: 'DECLINE', ResponseCode: '05').
     */
    public function Discover_ResponseCode_ShouldIndicateInsufficientFunds()
    {
        try
        {
            $this->chargeValidDiscover(10.08);
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
    /// Transaction response code should indicate no saving account (ResponseText: 'NO SAVE ACCOUNT', ResponseCode: '53').
     */
    public function Discover_ResponseCode_ShouldIndicateNoSavingAccount()
    {
        try
        {
            $this->chargeValidDiscover(10.17);
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
    /// Transaction response code should indicate expired card (ResponseText: 'EXPIRED CARD', ResponseCode: '54').
     */
    public function Discover_ResponseCode_ShouldIndicateExpiredCard()
    {
        try
        {
            $this->chargeValidDiscover(10.32);
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
    /// Transaction response code should indicate no card record (ResponseText: 'INVALID TRANS', ResponseCode: '56').
     */
    public function Discover_ResponseCode_ShouldIndicateNoCardRecord()
    {
        try
        {
            $this->chargeValidDiscover(10.24);
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
    /// Transaction response code should indicate transaction not permitted on card (ResponseText: 'SERV NOT ALLOWED', ResponseCode: '57').
     */
    public function Discover_ResponseCode_ShouldIndicateTxnNotPermittedOnCard()
    {
        try
        {
            $this->chargeValidDiscover(10.20);
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
    /// Transaction response code should indicate invalid acquirer (ResponseText: 'SERV NOT ALLOWED', ResponseCode: '58').
     */
    public function Discover_ResponseCode_ShouldIndicateInvalidAcquirer()
    {
        try
        {
            $this->chargeValidDiscover(10.30);
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
    /// Transaction response code should indicate exceeds limit (ResponseText: 'DECLINE', ResponseCode: '61').
     */
    public function Discover_ResponseCode_ShouldIndicateExceedsLimit()
    {
        try
        {
            $this->chargeValidDiscover(10.09);
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
    public function Discover_ResponseCode_ShouldIndicateRestrictedCard()
    {
        try
        {
            $this->chargeValidDiscover(10.10);
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
    public function Discover_ResponseCode_ShouldIndicateSecurityViolation()
    {
        try
        {
            $this->chargeValidDiscover(10.19);
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
    /// Transaction response code should indicate exceeds frequency limit (ResponseText: 'DECLINE$', ResponseCode: '65').
     */
    public function Discover_ResponseCode_ShouldIndicateExceedsFreqLimit()
    {
        try
        {
            $this->chargeValidDiscover(10.11);
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
    /// Transaction response code should indicate no to account (ResponseText: 'NO ACCOUNT', ResponseCode: '78').
     */
    public function Discover_ResponseCode_ShouldIndicateNoToAccount()
    {
        try
        {
            $this->chargeValidDiscover(10.13);
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
    public function Discover_ResponseCode_ShouldIndicateInvalidAccount()
    {
        try
        {
            $this->chargeValidDiscover(10.14);
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
    /// Transaction response code should indicate switch not available (ResponseText: 'NO REPLY', ResponseCode: '14').
     */
/* // As of 2013-08-19 this no longer returns a non-zero response code.
    public function Discover_ResponseCode_ShouldIndicateSwitchNotAvailable()
    {
        try
        {
            $this->chargeValidDiscover(10.33);
        }
        catch (CardException $e)
        {
            $this->assertEquals(ExceptionCodes::ProcessingError, $e->Code());
            $this->assertEquals($e->Message(), ExceptionMessages::ProcessingError);
            return;
        }

        $this->fail("No exception was thrown.");
    }
*/
    /**
     * @test
    /// Transaction response code should indicate system error (ResponseText: 'SYSTEM ERROR', ResponseCode: '96').
     */
    public function Discover_ResponseCode_ShouldIndicateSystemError()
    {
        try
        {
            $this->chargeValidDiscover(10.21);
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

    #region Verify, Authorize & Capture

    /**
     * @test
    /// Discover verify should return response code '85'.
     */
    public function Discover_Verify_ShouldReturnOk()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->verify(TestCreditCard::validDiscoverCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("85", $response->responseCode);
    }

    /**
     * @test
    /// Discover authorize should return response code '00'.
     */
    public function Discover_Authorize_ShouldReturnOk()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->authorize(50, "usd", TestCreditCard::validDiscoverCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("00", $response->responseCode);
    }

    /**
     * @test
    /// Discover authorize should return response code '00'.
     */
    public function Discover_AuthorizeAndRequestToken_ShouldGetTokenAndReturnOk()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->authorize(50, "usd",TestCreditCard::validDiscoverCreditCard(), TestCardHolder::ValidCardHolder(), true);
        $this->assertEquals("0", $response->tokenData->responseCode);
        $this->assertEquals("00", $response->responseCode);
    }

    /**
     * @test
    /// Discover authorize should return response code '00'.
     */
    public function Discover_Capture_ShouldReturnOk()
    {
        // Authorize the card.

        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $authResponse = $chargeSvc->authorize(50, "usd", TestCreditCard::validDiscoverCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("00", $authResponse->responseCode);

        // Capture the authorization.
        $captureResponse = $chargeSvc->capture($authResponse->transactionId);
        $this->assertEquals("0", $captureResponse->responseCode);
    }

    #endregion

    /// Charge a Discover with a valid config and valid Discover info.
    /// <param name="amt">Amount to charge</param>
    /// <returns>The HPS Charge.</returns>
    private function chargeValidDiscover($amt)
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->charge($amt, "usd", TestCreditCard::validDiscoverCreditCard(), TestCardHolder::ValidCardHolder());
        if ($response == null)
        {
            $this->fail("Response is null.");
        }

        return $response;
    }
}
<?php

require_once("setup.php");

class VisaTests extends PHPUnit_Framework_TestCase
{
    /**
     * @test
    /// The VISA is ok test method.
     */
    public function Visa_WhenCardIsOk_ShouldReturnValidResult()
    {
        $response = $this->chargeValidVisa(50);
        $this->assertEquals($response->responseCode, "00");
    }

    #region AVS Tests

    /**
     * @test
    /// AVS result code should be "B" test method.
     */
    public function Visa_AvsResultCode_ShouldEqualB()
    {
        $response = $this->chargeValidVisa(91.01);
        $this->assertEquals($response->avsResultCode, "B");
    }

    /**
     * @test
    /// AVS result code should be "C" test method.
     */
    public function Visa_AvsResultCode_ShouldEqualC()
    {
        $response = $this->chargeValidVisa(91.02);
        $this->assertEquals($response->avsResultCode, "C");
    }

    /**
     * @test
    /// AVS result code should be "D" test method.
     */
    public function Visa_AvsResultCode_ShouldEqualD()
    {
        $response = $this->chargeValidVisa(91.03);
        $this->assertEquals($response->avsResultCode, "D");
    }

    /**
     * @test
    /// AVS result code should be "I" test method.
     */
    public function Visa_AvsResultCode_ShouldEqualI()
    {
        $response = $this->chargeValidVisa(91.05);
        $this->assertEquals($response->avsResultCode, "I");
    }

    /**
     * @test
    /// AVS result code should be "M" test method.
     */
    public function Visa_AvsResultCode_ShouldEqualM()
    {
        $response = $this->chargeValidVisa(91.06);
        $this->assertEquals($response->avsResultCode, "M");
    }

    /**
     * @test
    /// AVS result code should be "P" test method.
     */
    public function Visa_AvsResultCode_ShouldEqualP()
    {
        $response = $this->chargeValidVisa(91.07);
        $this->assertEquals($response->avsResultCode, "P");
    }

    #endregion

    #region CVV Tests

    /**
     * @test
    /// CVV result code should be "M" test method.
     */
    public function Visa_CvvResultCode_ShouldEqualM()
    {
        $response = $this->chargeValidVisa(96.01);
        $this->assertEquals($response->cvvResultCode, "M");
    }

    /**
     * @test
    /// CVV result code should be "N" test method.
     */
    public function Visa_CvvResultCode_ShouldEqualN()
    {
        $response = $this->chargeValidVisa(96.02);
        $this->assertEquals($response->cvvResultCode, "N");
    }

    /**
     * @test
    /// CVV result code should be "P" test method.
     */
    public function Visa_CvvResultCode_ShouldEqualP()
    {
        $response = $this->chargeValidVisa(96.03);
        $this->assertEquals($response->cvvResultCode, "P");
    }

    /**
     * @test
    /// CVV result code should be "S" test method.
     */
    public function Visa_CvvResultCode_ShouldEqualS()
    {
        $response = $this->chargeValidVisa(96.04);
        $this->assertEquals($response->cvvResultCode, "S");
    }

    /**
     * @test
    /// CVV result code should be "U" test method.
     */
    public function Visa_CvvResultCode_ShouldEqualU()
    {
        $response = $this->chargeValidVisa(96.05);
        $this->assertEquals($response->cvvResultCode, "U");
    }

    #endregion

    #region Visa to Visa 2nd

    /**
     * @test
    /// Transaction response code should indicate refer card issuer (ResponseText: 'CALLS', ResponseCode: '02').
     */
    public function Visa_ResponseCode_ShouldIndicateReferCardIssuer()
    {
        try
        {
            $this->chargeValidVisa(10.34);
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
    public function Visa_ResponseCode_ShouldIndicateInvalidMerchant()
    {
        try
        {
            $this->chargeValidVisa(10.22);
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
    public function Visa_ResponseCode_ShouldIndicatePickUpCard()
    {
        try
        {
            $this->chargeValidVisa(10.04);
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
    public function Visa_ResponseCode_ShouldIndicateDoNotHonor()
    {
        try
        {
            $this->chargeValidVisa(10.25);
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
    public function Visa_ResponseCode_ShouldIndicateInvalidTransaction()
    {
        try
        {
            $this->chargeValidVisa(10.26);
        }
        catch (HpsException $e)
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
    public function Visa_ResponseCode_ShouldIndicateInvalidAmount()
    {
        try
        {
            $this->chargeValidVisa(10.27);
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
    public function Visa_ResponseCode_ShouldIndicateInvalidCard()
    {
        try
        {
            $this->chargeValidVisa(10.28);
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
    public function Visa_ResponseCode_ShouldIndicateInvalidIssuer()
    {
        try
        {
            $this->chargeValidVisa(10.18);
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
    /// Transaction response code should indicate system error re-enter (ResponseText: 'RE ENTER', ResponseCode: '19').
     */
    public function Visa_ResponseCode_ShouldIndicateSystemErrorReenter()
    {
        try
        {
            $this->chargeValidVisa(10.29);
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
    public function Visa_ResponseCode_ShouldIndicateLostCard()
    {
        try
        {
            $this->chargeValidVisa(10.31);
        }
        catch (CardException $e)
        {
            $this->assertEquals("card_declined", $e->code());
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate hot card pick-up (ResponseText: 'HOLD-CALL', ResponseCode: '43').
     */
    public function Visa_ResponseCode_ShouldIndicateHotCardPickUp()
    {
        try
        {
            $this->chargeValidVisa(10.03);
        }
        catch (CardException $e)
        {
            $this->assertEquals("card_declined", $e->code());
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate insufficient funds (ResponseText: 'DECLINE', ResponseCode: '05').
     */
    public function Visa_ResponseCode_ShouldIndicateInsufficientFunds()
    {
        try
        {
            $this->chargeValidVisa(10.08);
        }
        catch (CardException $e)
        {
            $this->assertEquals("card_declined", $e->code());
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate no checking account (ResponseText: 'NO CHECK ACCOUNT', ResponseCode: '52').
     */
    public function Visa_ResponseCode_ShouldIndicateNoCheckAccount()
    {
        try
        {
            $this->chargeValidVisa(10.16);
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
    /// Transaction response code should indicate no saving account (ResponseText: 'NO SAVE ACCOUNT', ResponseCode: '53').
     */
    public function Visa_ResponseCode_ShouldIndicateNoSavingAccount()
    {
        try
        {
            $this->chargeValidVisa(10.17);
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
    public function Visa_ResponseCode_ShouldIndicateExpiredCard()
    {
        try
        {
            $this->chargeValidVisa(10.32);
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
    /// Transaction response code should indicate expired card (ResponseText: 'EXPIRED CARD', ResponseCode: '54').
     */
    public function Visa_ResponseCode_ShouldIndicateExpiredCard_OnAuth()
    {
        try
        {
            $testConfig = new TestServicesConfig();

            $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());

            $chargeSvc->authorize(10.32, 'USD',TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(), true);
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
    /// Transaction response code should indicate transaction not permitted on card (ResponseText: 'SERV NOT ALLOWED', ResponseCode: '57').
    // Changed as of 2013-08-19 to be 'R1' 'STOP RECURRING'
     */
    public function Visa_ResponseCode_ShouldIndicateTxnNotPermittedOnCard()
    {
        try
        {
            $this->chargeValidVisa(10.20);
        }
        catch (CardException $e)
        {
            //$this->assertEquals("processing_error", $e->code());
            //$this->assertEquals("An error occurred while processing the card.",$e->getMessage());
            $this->assertEquals("unknown_card_exception", $e->code());
            $this->assertEquals("STOP RECURRING",$e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate invalid acquirer (ResponseText: 'SERV NOT ALLOWED', ResponseCode: '58').
     */
    public function Visa_ResponseCode_ShouldIndicateInvalidAcquirer()
    {
        try
        {
            $this->chargeValidVisa(10.30);
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
    public function Visa_ResponseCode_ShouldIndicateExceedsLimit()
    {
        try
        {
            $this->chargeValidVisa(10.09);
        }
        catch (CardException $e)
        {
            $this->assertEquals("card_declined", $e->code());
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate restricted card (ResponseText: 'DECLINE', ResponseCode: '62').
     */
    public function Visa_ResponseCode_ShouldIndicateRestrictedCard()
    {
        try
        {
            $this->chargeValidVisa(10.10);
        }
        catch (CardException $e)
        {
            $this->assertEquals("card_declined", $e->code());
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
    /// Transaction response code should indicate exceeds freq limit (ResponseText: 'DECLINE', ResponseCode: '65').
     */
    public function Visa_ResponseCode_ShouldIndicateSecurityViolation()
    {
        try
        {
            $this->chargeValidVisa(10.11);
        }
        catch (CardException $e)
        {
            $this->assertEquals("card_declined", $e->code());
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * @expectedException        CardException
     * @expectedExceptionCode    incorrect_cvc
     * @expectedExceptionMessage The card's security code is incorrect.
    /// Transaction response code should indicate invalid CVV2 (ResponseText: 'CHECK DIGIT ERR', ResponseCode: 'EB').
     */
    public function Visa_ResponseCode_ShouldIndicateCheckDigitErr()
    {
        $this->chargeValidVisa(10.05);
    }

    /**
     * @test
     * @expectedException        HpsException
     * @expectedExceptionCode    issuer_timeout
     * @expectedExceptionMessage Error occurred while reversing a charge due to issuer time-out.
    /// Transaction response code should indicate switch not available (ResponseText: 'NO REPLY', ResponseCode: '14').
     */
    public function Visa_ResponseCode_ShouldIndicateSwitchNotAvailable()
    {
        $this->chargeValidVisa(10.33);
    }

    /**
     * @test
     * @expectedException        CardException
     * @expectedExceptionCode    processing_error
     * @expectedExceptionMessage An error occurred while processing the card.
    /// Transaction response code should indicate system error (ResponseText: 'SYSTEM ERROR', ResponseCode: '96').
     */
    public function Visa_ResponseCode_ShouldIndicateSystemError()
    {
        $this->chargeValidVisa(10.21);
    }

    /**
     * @test
     * @expectedException        CardException
     * @expectedExceptionCode    incorrect_cvc
     * @expectedExceptionMessage The card's security code is incorrect.
    /// Transaction response code should indicate CVV2 mismatch (ResponseText: 'CVV2 MISMATCH', ResponseCode: 'N7').
     */
    public function Visa_ResponseCode_ShouldIndicateCvv2Mismatch()
    {
        $this->chargeValidVisa(10.23);
    }

    #endregion

    #region Verify, Authorize & Capture

    /**
     * @test
    /// Visa verify should return response code '85'.
     */
    public function Visa_Verify_ShouldReturnOk()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->Verify(TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("85", $response->responseCode);
    }

    /**
     * @test
    /// Visa authorize should return response code '00'.
     */
    public function Visa_Authorize_ShouldReturnOk()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->authorize(50, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("00", $response->responseCode);
    }

    /**
     * @test
    /// Visa authorize should return response code '00'.
     */
    public function Visa_AuthorizeAndRequestToken_ShouldGetTokenAndReturnOk()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->authorize(50, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(), true);
        $this->assertEquals("0",  $response->tokenData->responseCode);
        $this->assertEquals("00", $response->responseCode);
    }

    /**
     * @test
    /// Visa authorize should return response code '00'.
     */
    public function Visa_Capture_ShouldReturnOk()
    {
        $testConfig = new TestServicesConfig();

        // Authorize the card.
        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $authResponse = $chargeSvc->authorize(50, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("00", $authResponse->responseCode);

        // Capture the authorization.
        $captureResponse = $chargeSvc->capture($authResponse->transactionId);
        $this->assertEquals("0", $captureResponse->responseCode);
    }

    #endregion

    #region AdditionalTxnFields and Dynamic descriptor

    /**
     * @test
    /// Visa Additional Txn Fields test
     */
    public function Visa_AdditionalTxnFields(){
        $details = new HpsTransactionDetails();
        $details->memo = "Test Memo";
        $details->invoiceNumber = "777777";
        $details->customerId = "8975964";

        $memoTest = $this->chargeValidVisa(50,false,$details);
        $this->assertEquals("0", $memoTest->responseCode);
    }

    /**
     * @test
    /// Visa Dynamic Descriptor test
     */
    public function Visa_Dynamic_Descriptor_Charge(){
        $txnDescriptor = "Best Company Every";

        $testConfig = new TestServicesConfig();
        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());

        $descriptorTest = $chargeSvc->charge(25, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(),false,null,$txnDescriptor);
        //This is being printed so that you can look it up on the gate way to make sure it actually worked.
        print_r($descriptorTest->transactionId);
        $this->assertEquals("0", $descriptorTest->responseCode);
    }


    /**
     * @test
    /// Visa Auth Dynamic Descriptor test
     */
    public function Visa_Dynamic_Descriptor_Auth(){
        $txnDescriptor = "Best Company Every";

        $testConfig = new TestServicesConfig();
        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());

        $descriptorTest = $chargeSvc->authorize(25, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(),false,null,$txnDescriptor);
        //This is being printed so that you can look it up on the gate way to make sure it actually worked.
        print_r($descriptorTest->transactionId);
        $this->assertEquals("0", $descriptorTest->responseCode);
    }

    #endregion

    #region Void and reverseTransaction

    /**
     * @test
     * Visa charge and void should return response code '00'.
     */
    public function Visa_ChargeAndVoid_Should_Return_Ok()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->charge(50, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(), true);
        $this->assertEquals("0",  $response->tokenData->responseCode);
        $this->assertEquals("00", $response->responseCode);

        $voidResponse = $chargeSvc->void($response->transactionId);
        $this->assertEquals("00", $voidResponse->responseCode);
    }

    /**
     * @test
     * Visa charge and reverseTransaction should return response code '00'.
     */
    public function Visa_ChargeAndTransaction_Should_Return_Ok()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->charge(50, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(), true);
        $this->assertEquals("0",  $response->tokenData->responseCode);
        $this->assertEquals("00", $response->responseCode);

        $reverseResponse = $chargeSvc->reverseTransaction($response->transactionId,50,'usd');
        $this->assertEquals("00", $reverseResponse->responseCode);
    }

    #endregion

    /// Charge a Visa with a valid config and valid Visa info.
    /// <param name="amt">Amount to charge</param>
    /// <returns>The HPS Charge.</returns>
    private function chargeValidVisa($amt, $multiUseRequest = false, $details = null, $txnDescriptors=null)
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig->ValidMultiUseConfig());
        $response = $chargeSvc->charge($amt, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(), $multiUseRequest, $details, $txnDescriptors);
        if ($response == null)
        {
            $this->fail("Response is null.");
        }

        return $response;
    }
}
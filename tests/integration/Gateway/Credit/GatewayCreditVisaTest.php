<?php

class GatewayCreditVisaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * /// The VISA is ok test method.
     */
    public function testVisaWhenCardIsOkShouldReturnValidResult()
    {
        $response = $this->chargeValidVisa(50);
        $this->assertEquals($response->responseCode, "00");
    }

    #region AVS Tests

    /**
     * @test
     * /// AVS result code should be "B" test method.
     */
    public function testVisaAvsResultCodeShouldEqualB()
    {
        $response = $this->chargeValidVisa(91.01);
        $this->assertEquals($response->avsResultCode, "B");
    }

    /**
     * @test
     * /// AVS result code should be "C" test method.
     */
    public function testVisaAvsResultCodeShouldEqualC()
    {
        $response = $this->chargeValidVisa(91.02);
        $this->assertEquals($response->avsResultCode, "C");
    }

    /**
     * @test
     * /// AVS result code should be "D" test method.
     */
    public function testVisaAvsResultCodeShouldEqualD()
    {
        $response = $this->chargeValidVisa(91.03);
        $this->assertEquals($response->avsResultCode, "D");
    }

    /**
     * @test
     * /// AVS result code should be "I" test method.
     */
    public function testVisaAvsResultCodeShouldEqualI()
    {
        $response = $this->chargeValidVisa(91.05);
        $this->assertEquals($response->avsResultCode, "I");
    }

    /**
     * @test
     * /// AVS result code should be "M" test method.
     */
    public function testVisaAvsResultCodeShouldEqualM()
    {
        $response = $this->chargeValidVisa(91.06);
        $this->assertEquals($response->avsResultCode, "M");
    }

    /**
     * @test
     * /// AVS result code should be "P" test method.
     */
    public function testVisaAvsResultCodeShouldEqualP()
    {
        $response = $this->chargeValidVisa(91.07);
        $this->assertEquals($response->avsResultCode, "P");
    }

    #endregion

    #region CVV Tests

    /**
     * @test
     * /// CVV result code should be "M" test method.
     */
    public function testVisaCvvResultCodeShouldEqualM()
    {
        $response = $this->chargeValidVisa(96.01);
        $this->assertEquals($response->cvvResultCode, "M");
    }

    /**
     * @test
     * /// CVV result code should be "N" test method.
     */
    public function testVisaCvvResultCodeShouldEqualN()
    {
        $response = $this->chargeValidVisa(96.02);
        $this->assertEquals($response->cvvResultCode, "N");
    }


    /**
     * @test
     * /// CVV result code should be "P" test method.
     */
    public function testVisaCvvResultCodeShouldEqualP()
    {
        $response = $this->chargeValidVisa(96.03);
        $this->assertEquals($response->cvvResultCode, "P");
    }

    /**
     * @test
     * /// CVV result code should be "S" test method.
     */
    public function testVisaCvvResultCodeShouldEqualS()
    {
        $response = $this->chargeValidVisa(96.04);
        $this->assertEquals($response->cvvResultCode, "S");
    }

    /**
     * @test
     * /// CVV result code should be "U" test method.
     */
    public function testVisaCvvResultCodeShouldEqualU()
    {
        $response = $this->chargeValidVisa(96.05);
        $this->assertEquals($response->cvvResultCode, "U");
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
        $response = $chargeSvc->updateTokenExpiration(TestCreditCard::validVisaMUT(), 1, 2019);
        $this->assertEquals("0", $response->responseCode);
    }

    /**
     * @test
     */

    public function testUpdateTokenExpirationToExpiredDate()
    {
        $testConfig = new TestServicesConfig();
        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->updateTokenExpiration(TestCreditCard::validVisaMUT(), 1, 2009);
        $this->assertEquals("0", $response->responseCode);
    }

    /**
     * @test
     * @expectedException HpsGatewayException
     * @expectedExceptionCode 10
     * @expectedExceptionMessage Invalid card data
     */

    public function testUpdateTokenExpirationToInvalidYear()
    {
        $testConfig = new TestServicesConfig();
        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $chargeSvc->updateTokenExpiration(TestCreditCard::validVisaMUT(), 1, 19);
    }


    /**
     * @test
     * @expectedException HpsGatewayException
     * @expectedExceptionCode 10
     * @expectedExceptionMessage Invalid card data
     */
    public function testUpdateTokenExpirationToNull()
    {
        $testConfig = new TestServicesConfig();
        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $chargeSvc->updateTokenExpiration(TestCreditCard::validVisaMUT(), null, null);
    }

    /**
     * @test
     * @expectedException HpsGatewayException
     * @expectedExceptionCode 10
     * @expectedExceptionMessage Invalid card data
     */
    public function testUpdateTokenExpirationOnInValidTokenShouldReturnException()
    {
        $testConfig = new TestServicesConfig();
        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $chargeSvc->updateTokenExpiration(TestCreditCard::invalidMUT(), 1, 2019);
    }
    /**
     * @test
     * @expectedException HpsGatewayException
     * @expectedExceptionCode 10
     * @expectedExceptionMessage Invalid card data
     */
    public function testUpdateTokenExpirationOnNullTokenShouldReturnException()
    {
        $testConfig = new TestServicesConfig();
        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $chargeSvc->updateTokenExpiration(TestCreditCard::NullMUT(), 1, 2019);
    }


    #endregion
    #region Visa to Visa 2nd

    /**
     * @test
     * /// Transaction response code should indicate refer card issuer (ResponseText: 'CALLS', ResponseCode: '02').
     */
    public function testVisaResponseCodeShouldIndicateReferCardIssuer()
    {
        try {
            $this->chargeValidVisa(10.34);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate invalid merchant (ResponseText: 'TERM ID ERROR', ResponseCode: '03').
     */
    public function testVisaResponseCodeShouldIndicateInvalidMerchant()
    {
        try {
            $this->chargeValidVisa(10.22);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate pick up card (ResponseText: 'HOLD-CALLS', ResponseCode: '44').
     */
    public function testVisaResponseCodeShouldIndicatePickUpCard()
    {
        try {
            $this->chargeValidVisa(10.04);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate do not honor (ResponseText: 'DECLINE', ResponseCode: '05').
     */
    public function testVisaResponseCodeShouldIndicateDoNotHonor()
    {
        try {
            $this->chargeValidVisa(10.25);
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
    public function testVisaResponseCodeShouldIndicateInvalidTransaction()
    {
        try {
            $this->chargeValidVisa(10.26);
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
    public function testVisaResponseCodeShouldIndicateInvalidAmount()
    {
        try {
            $this->chargeValidVisa(10.27);
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
    public function testVisaResponseCodeShouldIndicateInvalidCard()
    {
        try {
            $this->chargeValidVisa(10.28);
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
    public function testVisaResponseCodeShouldIndicateInvalidIssuer()
    {
        try {
            $this->chargeValidVisa(10.18);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::PROCESSING_ERROR, $e->code);
            $this->assertEquals("An error occurred while processing the card.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate system error re-enter (ResponseText: 'RE ENTER', ResponseCode: '19').
     */
    public function testVisaResponseCodeShouldIndicateSystemErrorReenter()
    {
        try {
            $this->chargeValidVisa(10.29);
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
    public function testVisaResponseCodeShouldIndicateLostCard()
    {
        try {
            $this->chargeValidVisa(10.31);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate hot card pick-up (ResponseText: 'HOLD-CALL', ResponseCode: '43').
     */
    public function testVisaResponseCodeShouldIndicateHotCardPickUp()
    {
        try {
            $this->chargeValidVisa(10.03);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate insufficient funds (ResponseText: 'DECLINE', ResponseCode: '05').
     */
    public function testVisaResponseCodeShouldIndicateInsufficientFunds()
    {
        try {
            $this->chargeValidVisa(10.08);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate no checking account (ResponseText: 'NO CHECK ACCOUNT', ResponseCode: '52').
     */
    public function testVisaResponseCodeShouldIndicateNoCheckAccount()
    {
        try {
            $this->chargeValidVisa(10.16);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::PROCESSING_ERROR, $e->code);
            $this->assertEquals("An error occurred while processing the card.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate no saving account (ResponseText: 'NO SAVE ACCOUNT', ResponseCode: '53').
     */
    public function testVisaResponseCodeShouldIndicateNoSavingAccount()
    {
        try {
            $this->chargeValidVisa(10.17);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::PROCESSING_ERROR, $e->code);
            $this->assertEquals("An error occurred while processing the card.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate expired card (ResponseText: 'EXPIRED CARD', ResponseCode: '54').
     */
    public function testVisaResponseCodeShouldIndicateExpiredCard()
    {
        try {
            $this->chargeValidVisa(10.32);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::EXPIRED_CARD, $e->code);
            $this->assertEquals("The card has expired.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate expired card (ResponseText: 'EXPIRED CARD', ResponseCode: '54').
     */
    public function testVisaResponseCodeShouldIndicateExpiredCardOnAuth()
    {
        try {
            $testConfig = new TestServicesConfig();

            $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());

            $chargeSvc->authorize(10.32, 'USD', TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(), true);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::EXPIRED_CARD, $e->code);
            $this->assertEquals("The card has expired.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate transaction not permitted on card (ResponseText: 'SERV NOT ALLOWED', ResponseCode: '58').
     */
    public function testVisaResponseCodeShouldIndicateTxnNotPermittedOnCard()
    {
        try {
            $this->chargeValidVisa(10.30);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::PROCESSING_ERROR, $e->code);
            $this->assertEquals("An error occurred while processing the card.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate invalid acquirer (ResponseText: 'SERV NOT ALLOWED', ResponseCode: '58').
     */
    public function testVisaResponseCodeShouldIndicateInvalidAcquirer()
    {
        try {
            $this->chargeValidVisa(10.30);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::PROCESSING_ERROR, $e->code);
            $this->assertEquals("An error occurred while processing the card.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate exceeds limit (ResponseText: 'DECLINE', ResponseCode: '61').
     */
    public function testVisaResponseCodeShouldIndicateExceedsLimit()
    {
        try {
            $this->chargeValidVisa(10.09);
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
    public function testVisaResponseCodeShouldIndicateRestrictedCard()
    {
        try {
            $this->chargeValidVisa(10.10);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * /// Transaction response code should indicate exceeds freq limit (ResponseText: 'DECLINE', ResponseCode: '65').
     */
    public function testVisaResponseCodeShouldIndicateSecurityViolation()
    {
        try {
            $this->chargeValidVisa(10.11);
        } catch (HpsCreditException $e) {
            $this->assertEquals(HpsExceptionCodes::CARD_DECLINED, $e->code);
            $this->assertEquals("The card was declined.", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }

    /**
     * @test
     * @expectedException        HpsCreditException
     * @expectedExceptionCode    HpsExceptionCodes::INCORRECT_CVC
     * @expectedExceptionMessage The card's security code is incorrect.
     * /// Transaction response code should indicate invalid CVV2 (ResponseText: 'CHECK DIGIT ERR', ResponseCode: 'EB').
     */
    public function testVisaResponseCodeShouldIndicateCheckDigitErr()
    {
        $this->chargeValidVisa(10.05);
    }

    /**
     * @test
     * @expectedException        HpsCreditException
     * @expectedExceptionCode    HpsExceptionCodes::PROCESSING_ERROR
     * @expectedExceptionMessage An error occurred while processing the card.
     * /// Transaction response code should indicate system error (ResponseText: 'SYSTEM ERROR', ResponseCode: '96').
     */
    public function testVisaResponseCodeShouldIndicateSystemError()
    {
        $this->chargeValidVisa(10.21);
    }

    /**
     * @test
     * @expectedException        HpsCreditException
     * @expectedExceptionCode    HpsExceptionCodes::INCORRECT_CVC
     * @expectedExceptionMessage The card's security code is incorrect.
     * /// Transaction response code should indicate CVV2 mismatch (ResponseText: 'CVV2 MISMATCH', ResponseCode: 'N7').
     */
    public function testVisaResponseCodeShouldIndicateCvv2Mismatch()
    {
        $this->chargeValidVisa(10.23);
    }

    #endregion



    #region Verify, Authorize & Capture

    /**
     * @test
     * /// Visa verify should return response code '85'.
     */
    public function testVisaVerifyShouldReturnOk()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->Verify(TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("85", $response->responseCode);
    }

    /**
     * @test
     * /// Visa authorize should return response code '00'.
     */
    public function testVisaAuthorizeShouldReturnOk()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->authorize(50, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder());
        $this->assertEquals("00", $response->responseCode);
    }

    /**
     * @test
     * /// Visa authorize should return response code '00'.
     */
    public function testVisaAuthorizeAndRequestTokenShouldGetTokenAndReturnOk()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->authorize(50, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(), true);
        $this->assertEquals("0", $response->tokenData->responseCode);
        $this->assertEquals("00", $response->responseCode);
    }

    /**
     * @test
     * /// Visa authorize should return response code '00'.
     */
    public function testVisaCaptureShouldReturnOk()
    {
        $testConfig = new TestServicesConfig();

        // Authorize the card.
        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
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
     * /// Visa Additional Txn Fields test
     */
    public function testVisaAdditionalTxnFields()
    {
        $details = new HpsTransactionDetails();
        $details->memo = "Test Memo";
        $details->invoiceNumber = "777777";
        $details->customerId = "8975964";
        $details->clientTransactionId = "123456789";

        $memoTest = $this->chargeValidVisa(50, false, $details);
        $this->assertEquals('123456789', $memoTest->clientTransactionId);
        $this->assertEquals("0", $memoTest->responseCode);
    }

    /**
     * @test
     * Visa ClientTransactionId on Auth
     */
    public function testVisaClientTransactionIdOnAuth()
    {
        $details = new HpsTransactionDetails();
        $details->memo = "Test Memo";
        $details->invoiceNumber = "777777";
        $details->customerId = "8975964";
        $details->clientTransactionId = "123456789";

        $testConfig = new TestServicesConfig();
        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());

        $response = $chargeSvc->authorize(25, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(), false, $details);
        $this->assertEquals('123456789', $response->clientTransactionId);
        $this->assertEquals("0", $response->responseCode);
    }

    /**
     * @test
     * /// Visa Dynamic Descriptor test
     */
    public function testVisaDynamicDescriptorCharge()
    {
        $txnDescriptor = "Best Company Every";

        $testConfig = new TestServicesConfig();
        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());

        $descriptorTest = $chargeSvc->charge(25, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(), false, null, $txnDescriptor);
        //This is being printed so that you can look it up on the gate way to make sure it actually worked.
        // print_r($descriptorTest->transactionId);
        $this->assertEquals("0", $descriptorTest->responseCode);
    }


    /**
     * @test
     * /// Visa Auth Dynamic Descriptor test
     */
    public function testVisaDynamicDescriptorAuth()
    {
        $txnDescriptor = "Best Company Every";

        $testConfig = new TestServicesConfig();
        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());

        $descriptorTest = $chargeSvc->authorize(25, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(), false, null, $txnDescriptor);
        //This is being printed so that you can look it up on the gate way to make sure it actually worked.
        // print_r($descriptorTest->transactionId);
        $this->assertEquals("0", $descriptorTest->responseCode);
    }

    #endregion

    #region Void and reverseTransaction

    /**
     * @test
     * Visa charge and void should return response code '00'.
     */
    public function testVisaChargeAndVoidShouldReturnOk()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->charge(50, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(), true);
        $this->assertEquals("0", $response->tokenData->responseCode);
        $this->assertEquals("00", $response->responseCode);

        $voidResponse = $chargeSvc->void($response->transactionId);
        $this->assertEquals("00", $voidResponse->responseCode);
    }

    /**
     * @test
     * Visa charge and reverseTransaction should return response code '00'.
     */
    public function testVisaChargeAndTransactionShouldReturnOk()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->charge(50, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(), true);
        $this->assertEquals("0", $response->tokenData->responseCode);
        $this->assertEquals("00", $response->responseCode);

        $reverseResponse = $chargeSvc->reverse($response->transactionId, 50, 'usd');
        $this->assertEquals("00", $reverseResponse->responseCode);
    }

    #endregion

    #CPCRegion

    /**
     * @test
     * Visa charge and CPC Req should return cpcIndicator 'B'.
     */
    public function testVisaChargeCPCReqShouldReturnBusiness()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->charge(112.34, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(), false, null, null, false, true);
        $this->assertEquals("00", $response->responseCode);
        $this->assertEquals("B", $response->cpcIndicator);  //Business Card Check

        $cpcData = new HpsCPCData();
        $cpcData->CardHolderPONbr = "123456789";
        $cpcData->TaxType = HpsTaxType::SALES_TAX;
        $cpcData->TaxAmt = '15';

        $response2 = $chargeSvc->cpcEdit($response->transactionId, $cpcData);
        $this->assertEquals("00", $response2->responseCode);
    }

    /**
     * @test
     * Visa charge and CPC Req should return cpcIndicator 'R'.
     */
    public function testVisaChargeCPCReqShouldReturnCorporate()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->charge(123.45, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(), false, null, null, false, true);
        $this->assertEquals("00", $response->responseCode);
        $this->assertEquals("R", $response->cpcIndicator);

        $cpcData = new HpsCPCData();
        $cpcData->CardHolderPONbr = "123456789";
        $cpcData->TaxType = HpsTaxType::SALES_TAX;
        $cpcData->TaxAmt = '15';

        $response2 = $chargeSvc->cpcEdit($response->transactionId, $cpcData);
        $this->assertEquals("00", $response2->responseCode);
    }

    /**
     * @test
     * Visa charge and CPC Req should return cpcIndicator 'S'.
     */
    public function testVisaChargeCPCReqShouldReturnPurchasing()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->charge(134.56, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(), false, null, null, false, true);
        $this->assertEquals("00", $response->responseCode);
        $this->assertEquals("S", $response->cpcIndicator);

        $cpcData = new HpsCPCData();
        $cpcData->CardHolderPONbr = "123456789";
        $cpcData->TaxType = HpsTaxType::SALES_TAX;
        $cpcData->TaxAmt = '15';

        $response2 = $chargeSvc->cpcEdit($response->transactionId, $cpcData);
        $this->assertEquals("00", $response2->responseCode);
    }


    /**
     * @test
     * Visa auth and CPC Req should return cpcIndicator 'B'.
     */
    public function testVisaAuthCPCReqShouldReturnBusiness()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->authorize(112.34, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(), false, null, null, false, true);
        $this->assertEquals("00", $response->responseCode);
        $this->assertEquals("B", $response->cpcIndicator);  //Business Card Check

        $cpcData = new HpsCPCData();
        $cpcData->CardHolderPONbr = "123456789";
        $cpcData->TaxType = HpsTaxType::SALES_TAX;
        $cpcData->TaxAmt = '15';

        $response2 = $chargeSvc->cpcEdit($response->transactionId, $cpcData);
        $this->assertEquals("00", $response2->responseCode);
    }

    /**
     * @test
     * Visa auth and CPC Req should return cpcIndicator 'R'.
     */
    public function testVisaAuthCPCReqShouldReturnCorporate()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->authorize(123.45, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(), false, null, null, false, true);
        $this->assertEquals("00", $response->responseCode);
        $this->assertEquals("R", $response->cpcIndicator);

        $cpcData = new HpsCPCData();
        $cpcData->CardHolderPONbr = "123456789";
        $cpcData->TaxType = HpsTaxType::SALES_TAX;
        $cpcData->TaxAmt = '15';

        $response2 = $chargeSvc->cpcEdit($response->transactionId, $cpcData);
        $this->assertEquals("00", $response2->responseCode);
    }

    /**
     * @test
     * Visa auth and CPC Req should return cpcIndicator 'S'.
     */
    public function testVisaAuthCPCReqShouldReturnPurchasing()
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->authorize(134.56, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(), false, null, null, false, true);
        $this->assertEquals("00", $response->responseCode);
        $this->assertEquals("S", $response->cpcIndicator);

        $cpcData = new HpsCPCData();
        $cpcData->CardHolderPONbr = "123456789";
        $cpcData->TaxType = HpsTaxType::SALES_TAX;
        $cpcData->TaxAmt = '15';

        $response2 = $chargeSvc->cpcEdit($response->transactionId, $cpcData);
        $this->assertEquals("00", $response2->responseCode);
    }

    #endregion

    /// Charge a Visa with a valid config and valid Visa info.
    /// <param name="amt">Amount to charge</param>
    /// <returns>The HPS Charge.</returns>
    private function chargeValidVisa($amt, $multiUseRequest = false, $details = null, $txnDescriptors = null)
    {
        $testConfig = new TestServicesConfig();

        $chargeSvc = new HpsCreditService($testConfig::validMultiUseConfig());
        $response = $chargeSvc->charge($amt, "usd", TestCreditCard::validVisaCreditCard(), TestCardHolder::ValidCardHolder(), $multiUseRequest, $details, $txnDescriptors);
        if ($response == null) {
            $this->fail("Response is null.");
        }

        return $response;
    }
}

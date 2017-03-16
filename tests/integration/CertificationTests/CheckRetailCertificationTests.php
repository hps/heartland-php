<?php

/**
 * Class CheckRetailCertificationTest
 */
class CheckRetailCertificationTest extends PHPUnit_Framework_TestCase
{
    // ACH Debit Consumer Tests
    public function testACHDebitConsumer1Swipe()
    {
        $check = TestCheck::certification();
        $check->dataEntryMode = HpsDataEntryMode::SWIPE;

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale($check, 11.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);

        $voidResponse = $checkService->void($response->transactionId);
        $this->assertEquals('0', $voidResponse->responseCode);
        $this->assertEquals('Transaction Approved', $voidResponse->responseText);
    }

    public function testACHDebitConsumer2SwipeCheckingBusiness()
    {
        $check = TestCheck::certification();
        $check->checkType = HpsCheckType::BUSINESS;
        $check->dataEntryMode = HpsDataEntryMode::SWIPE;

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale($check, 12.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testACHDebitConsumer3SwipeSavingsPersonal()
    {
        $check = TestCheck::certification();
        $check->accountType = HpsAccountType::SAVINGS;
        $check->dataEntryMode = HpsDataEntryMode::SWIPE;

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale($check, 14.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testACHDebitConsumer4SwipeSavingsBusiness()
    {
        $check = TestCheck::certification();
        $check->accountType = HpsAccountType::SAVINGS;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->dataEntryMode = HpsDataEntryMode::SWIPE;

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale($check, 15.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testACHDebitConsumer5()
    {
        $check = TestCheck::certification();

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale($check, 16.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);

        $voidResponse = $checkService->void($response->transactionId);
        $this->assertEquals('0', $voidResponse->responseCode);
        $this->assertEquals('Transaction Approved', $voidResponse->responseText);
    }

    public function testACHDebitConsumer6CheckingBusiness()
    {
        $check = TestCheck::certification();
        $check->checkType = HpsCheckType::BUSINESS;

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale($check, 17.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testACHDebitConsumer7SavingsPersonal()
    {
        $check = TestCheck::certification();
        $check->accountType = HpsAccountType::SAVINGS;

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale($check, 18.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testACHDebitConsumer8SavingsBusiness()
    {
        $check = TestCheck::certification();
        $check->accountType = HpsAccountType::SAVINGS;
        $check->checkType = HpsCheckType::BUSINESS;

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale($check, 19.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    // END ACH DEBIT CONSUMER TESTS

    // BEGIN ACH DEBIT CORPORATE TESTS

    public function testACHDebitCorporate9Swipe()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::CCD;
        $check->dataEntryMode = HpsDataEntryMode::SWIPE;
        $check->checkHolder->checkName = 'Heartland Pays';

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale($check, 11.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testACHDebitCorporate10SwipeCheckingBusiness()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::CCD;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->dataEntryMode = HpsDataEntryMode::SWIPE;
        $check->checkHolder->checkName = 'Heartland Pays';

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale($check, 12.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);

        $voidResponse = $checkService->void($response->transactionId);
        $this->assertEquals('0', $voidResponse->responseCode);
        $this->assertEquals('Transaction Approved', $voidResponse->responseText);
    }

    public function testACHDebitCorporate11SwipeSavingsPersonal()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::CCD;
        $check->accountType = HpsAccountType::SAVINGS;
        $check->dataEntryMode = HpsDataEntryMode::SWIPE;
        $check->checkHolder->checkName = 'Heartland Pays';

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale($check, 14.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testACHDebitCorporate12SwipeSavingsBusiness()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::CCD;
        $check->accountType = HpsAccountType::SAVINGS;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->dataEntryMode = HpsDataEntryMode::SWIPE;
        $check->checkHolder->checkName = 'Heartland Pays';

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale($check, 15.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testACHDebitConsumerCorporate13()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::CCD;
        $check->checkHolder->checkName = 'Heartland Pays';

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale($check, 16.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testACHDebitCorporate14CheckingBusiness()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::CCD;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->checkHolder->checkName = 'Heartland Pays';

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale($check, 17.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);

        $voidResponse = $checkService->void($response->transactionId);
        $this->assertEquals('0', $voidResponse->responseCode);
        $this->assertEquals('Transaction Approved', $voidResponse->responseText);
    }

    public function testACHDebitCorporate15SavingsPersonal()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::CCD;
        $check->accountType = HpsAccountType::SAVINGS;
        $check->checkHolder->checkName = 'Heartland Pays';

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale($check, 18.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testACHDebitCorporate16SavingsBusiness()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::CCD;
        $check->accountType = HpsAccountType::SAVINGS;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->checkHolder->checkName = 'Heartland Pays';

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale($check, 19.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    // END ACH DEBIT CORPORATE TESTS


    // Start ACH eGold Tests

    public function testEGold17Swipe()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::POP;
        $check->dataEntryMode = HpsDataEntryMode::SWIPE;

        $checkService = new HpsCheckService(TestServicesConfig::ValidEGoldConfig());
        $response = $checkService->sale($check, 11.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testEGold18SwipeCheckingBusiness()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::POP;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->dataEntryMode = HpsDataEntryMode::SWIPE;

        $checkService = new HpsCheckService(TestServicesConfig::ValidEGoldConfig());
        $response = $checkService->sale($check, 12.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testEGold19SwipeSavingsPersonal()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::POP;
        $check->accountType = HpsAccountType::SAVINGS;
        $check->dataEntryMode = HpsDataEntryMode::SWIPE;

        $checkService = new HpsCheckService(TestServicesConfig::ValidEGoldConfig());
        $response = $checkService->sale($check, 14.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);

        $voidResponse = $checkService->void($response->transactionId);
        $this->assertEquals('0', $voidResponse->responseCode);
        $this->assertEquals('Transaction Approved', $voidResponse->responseText);
    }

    public function testEGold20SwipeSavingsBusiness()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::POP;
        $check->accountType = HpsAccountType::SAVINGS;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->dataEntryMode = HpsDataEntryMode::SWIPE;

        $checkService = new HpsCheckService(TestServicesConfig::ValidEGoldConfig());
        $response = $checkService->sale($check, 15.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testEGold21()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::POP;

        $checkService = new HpsCheckService(TestServicesConfig::ValidEGoldConfig());
        $response = $checkService->sale($check, 16.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testEGold22CheckingBusiness()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::POP;
        $check->checkType = HpsCheckType::BUSINESS;

        $checkService = new HpsCheckService(TestServicesConfig::ValidEGoldConfig());
        $response = $checkService->sale($check, 17.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testEGold23SavingsPersonal()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::POP;
        $check->accountType = HpsAccountType::SAVINGS;

        $checkService = new HpsCheckService(TestServicesConfig::ValidEGoldConfig());
        $response = $checkService->sale($check, 18.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);

        $voidResponse = $checkService->void($response->transactionId);
        $this->assertEquals('0', $voidResponse->responseCode);
        $this->assertEquals('Transaction Approved', $voidResponse->responseText);
    }

    public function testEGold24SavingsBusiness()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::POP;
        $check->accountType = HpsAccountType::SAVINGS;
        $check->checkType = HpsCheckType::BUSINESS;

        $checkService = new HpsCheckService(TestServicesConfig::ValidEGoldConfig());
        $response = $checkService->sale($check, 19.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    //End ACH eGold Tests

    //Start ACH eSilver Tests

    public function testESilver25Swipe()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::POP;
        $check->dataEntryMode = HpsDataEntryMode::SWIPE;

        $checkService = new HpsCheckService(TestServicesConfig::ValidEGoldConfig());
        $response = $checkService->sale($check, 11.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testESilver26SwipeCheckingBusiness()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::POP;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->dataEntryMode = HpsDataEntryMode::SWIPE;

        $checkService = new HpsCheckService(TestServicesConfig::ValidEGoldConfig());
        $response = $checkService->sale($check, 12.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testESilver27SwipeSavingsPersonal()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::POP;
        $check->accountType = HpsAccountType::SAVINGS;
        $check->dataEntryMode = HpsDataEntryMode::SWIPE;

        $checkService = new HpsCheckService(TestServicesConfig::ValidEGoldConfig());
        $response = $checkService->sale($check, 14.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testESilver28SwipeSavingsBusiness()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::POP;
        $check->accountType = HpsAccountType::SAVINGS;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->dataEntryMode = HpsDataEntryMode::SWIPE;

        $checkService = new HpsCheckService(TestServicesConfig::ValidEGoldConfig());
        $response = $checkService->sale($check, 15.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);

        $voidResponse = $checkService->void($response->transactionId);
        $this->assertEquals('0', $voidResponse->responseCode);
        $this->assertEquals('Transaction Approved', $voidResponse->responseText);
    }

    public function testESilver29()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::POP;

        $checkService = new HpsCheckService(TestServicesConfig::ValidEGoldConfig());
        $response = $checkService->sale($check, 16.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testESilver30CheckingBusiness()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::POP;
        $check->checkType = HpsCheckType::BUSINESS;

        $checkService = new HpsCheckService(TestServicesConfig::ValidEGoldConfig());
        $response = $checkService->sale($check, 17.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testESilver31SavingsPersonal()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::POP;
        $check->accountType = HpsAccountType::SAVINGS;

        $checkService = new HpsCheckService(TestServicesConfig::ValidEGoldConfig());
        $response = $checkService->sale($check, 18.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testESilver32SavingsBusiness()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::POP;
        $check->accountType = HpsAccountType::SAVINGS;
        $check->checkType = HpsCheckType::BUSINESS;

        $checkService = new HpsCheckService(TestServicesConfig::ValidEGoldConfig());
        $response = $checkService->sale($check, 19.00);
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);

        $voidResponse = $checkService->void($response->transactionId);
        $this->assertEquals('0', $voidResponse->responseCode);
        $this->assertEquals('Transaction Approved', $voidResponse->responseText);
    }

    //End ACH eSilver Tests

    //Start ACH eBronze Verify Tests

    public function testEBronzeVerify33SwipeCheckingPersonal()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::EBRONZE;
        $check->dataEntryMode = HpsDataEntryMode::SWIPE;

        $checkService = new HpsCheckService(TestServicesConfig::ValidMultiUseConfig());
        $response = $checkService->sale($check, 1);

        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testEBronzeVerify34SwipeCheckingBusiness()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::EBRONZE;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->dataEntryMode = HpsDataEntryMode::SWIPE;

        $checkService = new HpsCheckService(TestServicesConfig::ValidMultiUseConfig());
        $response = $checkService->sale($check, 1);

        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testEBronzeVerify35SwipeSavingsPersonal()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::EBRONZE;
        $check->accountType = HpsAccountType::SAVINGS;
        $check->dataEntryMode = HpsDataEntryMode::SWIPE;

        $checkService = new HpsCheckService(TestServicesConfig::ValidMultiUseConfig());
        $response = $checkService->sale($check, 1);

        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testEBronzeVerify36SwipeSavingsBusiness()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::EBRONZE;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->accountType = HpsAccountType::SAVINGS;
        $check->dataEntryMode = HpsDataEntryMode::SWIPE;

        $checkService = new HpsCheckService(TestServicesConfig::ValidMultiUseConfig());
        $response = $checkService->sale($check, 1);

        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testEBronzeVerify37CheckingPersonal()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::EBRONZE;

        $checkService = new HpsCheckService(TestServicesConfig::ValidMultiUseConfig());
        $response = $checkService->sale($check, 1);

        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testEBronzeVerify38CheckingBusiness()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::EBRONZE;
        $check->checkType = HpsCheckType::BUSINESS;

        $checkService = new HpsCheckService(TestServicesConfig::ValidMultiUseConfig());
        $response = $checkService->sale($check, 1);

        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testEBronzeVerify39SavingsPersonal()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::EBRONZE;
        $check->accountType = HpsAccountType::SAVINGS;

        $checkService = new HpsCheckService(TestServicesConfig::ValidMultiUseConfig());
        $response = $checkService->sale($check, 1);

        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    public function testEBronzeVerify40SavingsBusiness()
    {
        $check = TestCheck::certification();
        $check->secCode = HpsSECCode::EBRONZE;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->accountType = HpsAccountType::SAVINGS;

        $checkService = new HpsCheckService(TestServicesConfig::ValidMultiUseConfig());
        $response = $checkService->sale($check, 1);

        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('Transaction Approved', $response->responseText);
    }

    //End ACH eBronze Verify Tests
}

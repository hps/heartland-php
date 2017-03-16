<?php

/**
 * Class EcommerceTest
 */
class EcommerceTest extends PHPUnit_Framework_TestCase
{
    const BATCH_NOT_OPEN = 'Transaction was rejected because it requires a batch to be open.';

    /** @var HpsFluentCreditService|null */
    private $service               = null;

    /** @var HpsBatchService|null */
    private $batchService          = null;

    /** @var HpsGiftService|null */
    private $giftService           = null;

    /** @var bool */
    private $useTokens             = false;

    /** @var bool */
    private $usePrepaid            = false;

    /** @var string */
    private $publicKey             = '';

    /** @var string|null */
    public static $visaToken       = null;

    /** @var string|null */
    public static $mastercardToken = null;

    /** @var string|null */
    public static $discoverToken   = null;

    /** @var string|null */
    public static $amexToken       = null;

    /** @var string|null */
    public static $transactionId10 = null;

    /** @var string|null */
    public static $transactionId20 = null;

    /** @var string|null */
    public static $transactionId39 = null;

    /** @var string|null */
    public static $transactionId52 = null;

    /** @var string|null */
    public static $transactionId53 = null;
    /**
     * @return \HpsServicesConfig
     */
    private function config()
    {
        $config = new HpsServicesConfig();
        $config->secretApiKey  = 'skapi_cert_MTyMAQBiHVEAewvIzXVFcmUd2UcyBge_eCpaASUp0A';
        $config->developerId   = '';
        $config->versionNumber = '';
        return $config;
    }

    protected function setup()
    {
        $this->service      = new HpsFluentCreditService($this->config());
        $this->batchService = new HpsBatchService($this->config());
        $this->giftService  = new HpsFluentGiftCardService($this->config());

        $this->useTokens  = true;
        $this->usePrepaid = true;
        $this->publicKey  = 'pkapi_cert_jKc1FtuyAydZhZfbB3';
    }

    /// CARD VERIFY

    public function test000CloseBatch()
    {
        try {
            $response = $this->batchService->closeBatch();
            if ($response == null) {
                $this->fail('Response is null');
            }
            // print 'batch id: ' . $response->id . "\n";
            // print 'sequence number: ' . $response->sequenceNumber . "\n";
        } catch (HpsException $e) {
            if ($e->getMessage() != self::BATCH_NOT_OPEN) {
                $this->fail($e->getMessage());
            }
        }
    }

    /// Account Verification

    public function test001VerifyVisa()
    {

        /** @var HpsAccountVerify $response */
        $response = $this->service
            ->verify()
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withRequestMultiUseToken($this->useTokens)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('85', $response->responseCode);
    }

    public function test002VerifyMasterCard()
    {
        /** @var HpsAccountVerify $response */
        $response = $this->service
            ->verify()
            ->withCard(TestCreditCard::validMastercardCreditCard())
            ->withRequestMultiUseToken($this->useTokens)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('85', $response->responseCode);
    }

    public function test003VerifyDiscover()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->zip = '75024';

        /** @var HpsAccountVerify $response */
        $response = $this->service
            ->verify()
            ->withCard(TestCreditCard::validDiscoverCreditCard())
            ->withCardHolder($cardHolder)
            ->withRequestMultiUseToken($this->useTokens)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('85', $response->responseCode);
    }

    /// Address Verification

    public function test004VerifyAmex()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->zip = '75024';

        /** @var HpsAccountVerify $response */
        $response = $this->service
            ->verify()
            ->withCard(TestCreditCard::validAmexCreditCard(array('cvv'=>false)))
            ->withCardHolder($cardHolder)
            ->withRequestMultiUseToken($this->useTokens)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
    }

    /// Balance Inquiry (for Prepaid Card)

    /**
     * TODO: Remove exception assertions with proper prepaid account
     *
     * @expectedException        HpsCreditException
     * @expectedExceptionMessage card issuer timed-out
     */
    public function test005BalanceInquiryVisa()
    {
        /** @var HpsAccountVerify $response */
        $response = $this->service
            ->prepaidBalanceInquiry()
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('85', $response->responseCode);
    }

    /// CREDIT SALE (For Multi-Use Token Only)

    public function test006ChargeVisaToken()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860 Dallas Pkwy';
        $cardHolder->address->zip     = '75024';

        $response = $this->service
            ->charge()
            ->withAmount(13.01)
            ->withCard(TestCreditCard::validVisaCreditCard(array('cvv'=>false)))
            ->withCardHolder($cardHolder)
            ->withRequestMultiUseToken(true)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        $this->assertEquals(true, $response->tokenData != null);
        $this->assertEquals(true, $response->tokenData->tokenValue != null);
        self::$visaToken = $response->tokenData->tokenValue;
    }

    public function test007ChargeMasterCardToken()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860';
        $cardHolder->address->zip     = '75024';

        $response = $this->service
            ->charge()
            ->withAmount(13.02)
            ->withCard(TestCreditCard::validMastercardCreditCard())
            ->withCardHolder($cardHolder)
            ->withRequestMultiUseToken(true)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        $this->assertEquals(true, $response->tokenData != null);
        $this->assertEquals(true, $response->tokenData->tokenValue != null);
        self::$mastercardToken = $response->tokenData->tokenValue;
    }

    public function test008ChargeDiscoverToken()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860 Dallas Pkwy';
        $cardHolder->address->zip     = '75024';

        $response = $this->service
            ->charge()
            ->withAmount(13.03)
            ->withCard(TestCreditCard::validDiscoverCreditCard())
            ->withCardHolder($cardHolder)
            ->withRequestMultiUseToken(true)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        $this->assertEquals(true, $response->tokenData != null);
        $this->assertEquals(true, $response->tokenData->tokenValue != null);
        self::$discoverToken = $response->tokenData->tokenValue;
    }

    public function test009ChargeAmexToken()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860 Dallas Pkwy';
        $cardHolder->address->zip     = '75024';

        $response = $this->service
            ->charge()
            ->withAmount(13.04)
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder($cardHolder)
            ->withRequestMultiUseToken(true)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        $this->assertEquals(true, $response->tokenData != null);
        $this->assertEquals(true, $response->tokenData->tokenValue != null);
        self::$amexToken = $response->tokenData->tokenValue;
    }

    /// CREDIT SALE

    public function test010ChargeVisa()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860 Dallas Pkwy';
        $cardHolder->address->zip     = '75024';

        $directMarketData = new HpsDirectMarketData('123456');

        $builder = $this->service
            ->charge()
            ->withAmount(17.01)
            ->withCardHolder($cardHolder)
            ->withDirectMarketData($directMarketData);

        if ($this->useTokens) {
            $token = new HpsTokenData();
            $token->tokenValue = self::$visaToken;
            $builder = $builder->withToken($token);
        } else {
            $builder = $builder->withCard(TestCreditCard::validVisaCreditCard());
        }

        $response = $builder->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        self::$transactionId10 = $response->transactionId;
    }

    public function test011ChargeMastercard()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860';
        $cardHolder->address->zip     = '75024';

        $directMarketData = new HpsDirectMarketData('123456');

        $builder = $this->service
            ->charge()
            ->withAmount(17.02)
            ->withCardHolder($cardHolder)
            ->withDirectMarketData($directMarketData);

        if ($this->useTokens) {
            $token = new HpsTokenData();
            $token->tokenValue = self::$mastercardToken;
            $builder = $builder->withToken($token);
        } else {
            $builder = $builder->withCard(TestCreditCard::validMastercardCreditCard());
        }

        $response = $builder->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
    }

    public function test012ChargeDiscover()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860';
        $cardHolder->address->zip     = '750241234';

        $directMarketData = new HpsDirectMarketData('123456');

        $builder = $this->service
            ->charge()
            ->withAmount(17.03)
            ->withCardHolder($cardHolder)
            ->withDirectMarketData($directMarketData);

        if ($this->useTokens) {
            $token = new HpsTokenData();
            $token->tokenValue = self::$discoverToken;
            $builder = $builder->withToken($token);
        } else {
            $builder = $builder->withCard(TestCreditCard::validDiscoverCreditCard());
        }

        $response = $builder->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
    }

    public function test013ChargeAmex()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860 Dallas Pkwy';
        $cardHolder->address->zip     = '75024';

        $directMarketData = new HpsDirectMarketData('123456');

        $builder = $this->service
            ->charge()
            ->withAmount(17.04)
            ->withCardHolder($cardHolder)
            ->withDirectMarketData($directMarketData);

        if ($this->useTokens) {
            $token = new HpsTokenData();
            $token->tokenValue = self::$amexToken;
            $builder = $builder->withToken($token);
        } else {
            $builder = $builder->withCard(TestCreditCard::validAmexCreditCard());
        }

        $response = $builder->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
    }

    public function test014ChargeJcb()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860 Dallas Pkwy';
        $cardHolder->address->zip     = '75024';

        $directMarketData = new HpsDirectMarketData('123456');

        $response = $this->service
            ->charge()
            ->withAmount(17.04)
            ->withCardHolder($cardHolder)
            ->withDirectMarketData($directMarketData)
            ->withCard(TestCreditCard::validJCBCreditCard())
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
    }

    /// AUTHORIZATION

    public function test015AuthorizationVisa()
    {
        # Test 015a Authorization
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860 Dallas Pkwy';
        $cardHolder->address->zip = '75024';

        $directMarketData = new HpsDirectMarketData('123456');

        $response = $this->service
            ->authorize(17.06)
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder($cardHolder)
            ->withDirectMarketData($directMarketData)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);

        # test 015b Capture/AddToBatch
        $capture = $this->service
            ->capture($response->transactionId)
            ->execute();
        $this->assertEquals(true, $capture != null);
        $this->assertEquals('00', $capture->responseCode);
    }

    public function test016AuthorizationMastercard()
    {
        # Test 016a Authorization
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860 Dallas Pkwy';
        $cardHolder->address->zip = '750241234';

        $directMarketData = new HpsDirectMarketData('123456');

        $response = $this->service
            ->authorize(17.07)
            ->withCard(TestCreditCard::validMastercardCreditCard())
            ->withCardHolder($cardHolder)
            ->withDirectMarketData($directMarketData)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);

        # test 016b Capture/AddToBatch
        $capture = $this->service
            ->capture($response->transactionId)
            ->execute();
        $this->assertEquals(true, $capture != null);
        $this->assertEquals('00', $capture->responseCode);
    }

    public function test017AuthorizationDiscover()
    {
        # Test 017a Authorization
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860';
        $cardHolder->address->zip = '75024';

        $directMarketData = new HpsDirectMarketData('123456');

        $response = $this->service
            ->authorize(17.08)
            ->withCard(TestCreditCard::validDiscoverCreditCard())
            ->withCardHolder($cardHolder)
            ->withDirectMarketData($directMarketData)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);

        # test 017b Capture/AddToBatch
        # do not capture
    }

    /// PARTIALLY - APPROVED SALE

    public function test018PartialApprovalVisa()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860';
        $cardHolder->address->zip = '75024';

        $directMarketData = new HpsDirectMarketData('123456');

        $response = $this->service
            ->charge(130)
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder($cardHolder)
            ->withDirectMarketData($directMarketData)
            ->withAllowPartialAuth(true)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('10', $response->responseCode);
        $this->assertEquals(true, $response->authorizedAmount != null);
        $this->assertEquals('110.00', $response->authorizedAmount);
    }

    public function test019PartialApprovalDiscover()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860';
        $cardHolder->address->zip = '75024';

        $directMarketData = new HpsDirectMarketData('123456');

        $response = $this->service
            ->charge(145)
            ->withCard(TestCreditCard::validDiscoverCreditCard())
            ->withCardHolder($cardHolder)
            ->withDirectMarketData($directMarketData)
            ->withAllowPartialAuth(true)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('10', $response->responseCode);
        $this->assertEquals(true, $response->authorizedAmount != null);
        $this->assertEquals('65.00', $response->authorizedAmount);
    }

    public function test020PartialApprovalMastercard()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860';
        $cardHolder->address->zip = '75024';

        $directMarketData = new HpsDirectMarketData('123456');

        $response = $this->service
            ->charge(155)
            ->withCard(TestCreditCard::validDiscoverCreditCard())
            ->withCardHolder($cardHolder)
            ->withDirectMarketData($directMarketData)
            ->withAllowPartialAuth(true)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('10', $response->responseCode);
        $this->assertEquals(true, $response->authorizedAmount != null);
        $this->assertEquals('100.00', $response->authorizedAmount);
        self::$transactionId20 = $response->transactionId;
    }

    /// LEVEL II CORPORATE PURCHASE CARD

    public function test021LevelIIResponseB()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860 Dallas Pkwy';
        $cardHolder->address->zip = '750241234';

        $response = $this->service
            ->charge(112.34)
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder($cardHolder)
            ->withCpcReq(true)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        $this->assertEquals('B', $response->cpcIndicator);

        $cpcData = new HpsCPCData('9876543210', HpsTaxType::NOT_USED);
        $cpcResponse = $this->service
            ->cpcEdit($response->transactionId)
            ->withCpcData($cpcData)
            ->execute();

        $this->assertEquals(true, $cpcResponse != null);
        $this->assertEquals('00', $cpcResponse->responseCode);
    }

    public function test022LevelIIResponseB()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860';
        $cardHolder->address->zip = '750241234';

        $response = $this->service
            ->charge(112.34)
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder($cardHolder)
            ->withAllowDuplicates(true)
            ->withCpcReq(true)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        $this->assertEquals('B', $response->cpcIndicator);

        $cpcData = new HpsCPCData('', HpsTaxType::SALES_TAX, 1.00);
        $cpcResponse = $this->service
            ->cpcEdit($response->transactionId)
            ->withCpcData($cpcData)
            ->execute();

        $this->assertEquals(true, $cpcResponse != null);
        $this->assertEquals('00', $cpcResponse->responseCode);
    }

    public function test023LevelIIResponseR()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860';
        $cardHolder->address->zip = '75024';

        $response = $this->service
            ->charge(123.45)
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder($cardHolder)
            ->withCpcReq(true)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        $this->assertEquals('R', $response->cpcIndicator);

        $cpcData = new HpsCPCData('', HpsTaxType::TAX_EXEMPT);
        $cpcResponse = $this->service
            ->cpcEdit($response->transactionId)
            ->withCpcData($cpcData)
            ->execute();

        $this->assertEquals(true, $cpcResponse != null);
        $this->assertEquals('00', $cpcResponse->responseCode);
    }

    public function test024LevelIIResponseS()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860';
        $cardHolder->address->zip = '75024';

        $response = $this->service
            ->charge(134.56)
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder($cardHolder)
            ->withCpcReq(true)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        $this->assertEquals('S', $response->cpcIndicator);

        $cpcData = new HpsCPCData('9876543210', HpsTaxType::SALES_TAX, 1.00);
        $cpcResponse = $this->service
            ->cpcEdit($response->transactionId)
            ->withCpcData($cpcData)
            ->execute();

        $this->assertEquals(true, $cpcResponse != null);
        $this->assertEquals('00', $cpcResponse->responseCode);
    }

    public function test025LevelIIResponseS()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860';
        $cardHolder->address->zip = '75024';

        $response = $this->service
            ->charge(111.06)
            ->withCard(TestCreditCard::validMastercardCreditCard())
            ->withCardHolder($cardHolder)
            ->withCpcReq(true)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        $this->assertEquals('S', $response->cpcIndicator);

        $cpcData = new HpsCPCData('9876543210', HpsTaxType::NOT_USED);
        $cpcResponse = $this->service
            ->cpcEdit($response->transactionId)
            ->withCpcData($cpcData)
            ->execute();

        $this->assertEquals(true, $cpcResponse != null);
        $this->assertEquals('00', $cpcResponse->responseCode);
    }

    public function test026LevelIIResponseS()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860';
        $cardHolder->address->zip = '75024';

        $response = $this->service
            ->charge(111.07)
            ->withCard(TestCreditCard::validMastercardCreditCard())
            ->withCardHolder($cardHolder)
            ->withCpcReq(true)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        $this->assertEquals('S', $response->cpcIndicator);

        $cpcData = new HpsCPCData('', HpsTaxType::SALES_TAX, 1.00);
        $cpcResponse = $this->service
            ->cpcEdit($response->transactionId)
            ->withCpcData($cpcData)
            ->execute();

        $this->assertEquals(true, $cpcResponse != null);
        $this->assertEquals('00', $cpcResponse->responseCode);
    }

    public function test027LevelIIResponseS()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860';
        $cardHolder->address->zip = '75024';

        $response = $this->service
            ->charge(111.08)
            ->withCard(TestCreditCard::validMastercardCreditCard())
            ->withCardHolder($cardHolder)
            ->withCpcReq(true)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        $this->assertEquals('S', $response->cpcIndicator);

        $cpcData = new HpsCPCData('9876543210', HpsTaxType::SALES_TAX, 1.00);
        $cpcResponse = $this->service
            ->cpcEdit($response->transactionId)
            ->withCpcData($cpcData)
            ->execute();

        $this->assertEquals(true, $cpcResponse != null);
        $this->assertEquals('00', $cpcResponse->responseCode);
    }

    public function test028LevelIIResponseS()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860';
        $cardHolder->address->zip = '75024';

        $response = $this->service
            ->charge(111.09)
            ->withCard(TestCreditCard::validMastercardCreditCard())
            ->withCardHolder($cardHolder)
            ->withCpcReq(true)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        $this->assertEquals('S', $response->cpcIndicator);

        $cpcData = new HpsCPCData('9876543210', HpsTaxType::TAX_EXEMPT);
        $cpcResponse = $this->service
            ->cpcEdit($response->transactionId)
            ->withCpcData($cpcData)
            ->execute();

        $this->assertEquals(true, $cpcResponse != null);
        $this->assertEquals('00', $cpcResponse->responseCode);
    }

    public function test029LevelIINoResponse()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860';
        $cardHolder->address->zip = '75024';

        $response = $this->service
            ->charge(111.10)
            ->withCard(TestCreditCard::validAmexCreditCard())
            ->withCardHolder($cardHolder)
            ->withCpcReq(true)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        $this->assertEquals('0', $response->cpcIndicator);

        $cpcData = new HpsCPCData('9876543210', HpsTaxType::NOT_USED);
        $cpcResponse = $this->service
            ->cpcEdit($response->transactionId)
            ->withCpcData($cpcData)
            ->execute();

        $this->assertEquals(true, $cpcResponse != null);
        $this->assertEquals('00', $cpcResponse->responseCode);
    }

    public function test030LevelIINoResponse()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860';
        $cardHolder->address->zip = '750241234';

        $response = $this->service
            ->charge(111.11)
            ->withCard(TestCreditCard::validAmexCreditCard())
            ->withCardHolder($cardHolder)
            ->withCpcReq(true)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        $this->assertEquals('0', $response->cpcIndicator);

        $cpcData = new HpsCPCData('', HpsTaxType::SALES_TAX, 1.00);
        $cpcResponse = $this->service
            ->cpcEdit($response->transactionId)
            ->withCpcData($cpcData)
            ->execute();

        $this->assertEquals(true, $cpcResponse != null);
        $this->assertEquals('00', $cpcResponse->responseCode);
    }

    public function test031LevelIINoResponse()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860';
        $cardHolder->address->zip = '75024';

        $response = $this->service
            ->charge(111.12)
            ->withCard(TestCreditCard::validAmexCreditCard())
            ->withCardHolder($cardHolder)
            ->withCpcReq(true)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        $this->assertEquals('0', $response->cpcIndicator);

        $cpcData = new HpsCPCData('9876543210', HpsTaxType::SALES_TAX, 1.00);
        $cpcResponse = $this->service
            ->cpcEdit($response->transactionId)
            ->withCpcData($cpcData)
            ->execute();

        $this->assertEquals(true, $cpcResponse != null);
        $this->assertEquals('00', $cpcResponse->responseCode);
    }

    public function test032LevelIINoResponse()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860';
        $cardHolder->address->zip = '75024';

        $response = $this->service
            ->charge(111.13)
            ->withCard(TestCreditCard::validAmexCreditCard())
            ->withCardHolder($cardHolder)
            ->withCpcReq(true)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        $this->assertEquals('0', $response->cpcIndicator);

        $cpcData = new HpsCPCData('9876543210', HpsTaxType::TAX_EXEMPT);
        $cpcResponse = $this->service
            ->cpcEdit($response->transactionId)
            ->withCpcData($cpcData)
            ->execute();

        $this->assertEquals(true, $cpcResponse != null);
        $this->assertEquals('00', $cpcResponse->responseCode);
    }

    /// PRIOR / VOICE AUTHORIZATION

    public function test033OfflineSale()
    {
        $directMarketData = new HpsDirectMarketData('123456');

        $response = $this->service
            ->offlineCharge(17.10)
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withOfflineAuthCode('654321')
            ->withDirectMarketData($directMarketData)                
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);        
    }
    
    
    public function testOfflineChargeConvenienceAmount()
    {
        $directMarketData = new HpsDirectMarketData('123456');
        
        $offlineCharge = $this->service
            ->offlineCharge(37.10)
            ->withCard(TestCreditCard::validVisaCreditCard())            
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->withDirectMarketData($directMarketData)                
            ->withOfflineAuthCode('654321')   
            ->withAllowDuplicates(true)
            ->withConvenienceAmtInfo(20)                           
            ->execute();

        $this->assertEquals('00', $offlineCharge->responseCode);
        $this->assertNotNull($offlineCharge->transactionId);        

        $reportTxnDetail = $this->service
                ->get()
                ->withTransactionId($offlineCharge->transactionId)
                ->execute();        
       
        $this->assertNotNull($reportTxnDetail);
        $this->assertEquals(20, $reportTxnDetail->convenienceAmount);
    }
    
    public function testOfflineChargeShippingAmount()
    {
        $directMarketData = new HpsDirectMarketData('123456');
        $offlineCharge = $this->service
            ->offlineCharge(32.10)
            ->withCard(TestCreditCard::validVisaCreditCard())            
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->withDirectMarketData($directMarketData)                
            ->withOfflineAuthCode('654321')   
            ->withAllowDuplicates(true)
            ->withShippingAmtInfo(15)
            ->execute();

        $this->assertEquals('00', $offlineCharge->responseCode);
        $this->assertNotNull($offlineCharge->transactionId);        

        $reportTxnDetail = $this->service
                ->get()
                ->withTransactionId($offlineCharge->transactionId)
                ->execute();        
        
        $this->assertNotNull($reportTxnDetail);
        $this->assertEquals(15, $reportTxnDetail->shippingAmount);
    }    

    public function testOfflineAuthConvenienceAmount()
    {
        $directMarketData = new HpsDirectMarketData('123456');
        
        $offlineCharge = $this->service
            ->offlineAuth(37.10)
            ->withCard(TestCreditCard::validVisaCreditCard())            
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->withDirectMarketData($directMarketData)                
            ->withOfflineAuthCode('654321')   
            ->withAllowDuplicates(true)
            ->withConvenienceAmtInfo(20)                           
            ->execute();

        $this->assertEquals('00', $offlineCharge->responseCode);
        $this->assertNotNull($offlineCharge->transactionId);        

        $reportTxnDetail = $this->service
                ->get()
                ->withTransactionId($offlineCharge->transactionId)
                ->execute();        
       
        $this->assertNotNull($reportTxnDetail);
        $this->assertEquals(20, $reportTxnDetail->convenienceAmount);
    }
    
    public function testOfflineAuthShippingAmount()
    {
        $directMarketData = new HpsDirectMarketData('123456');
        $offlineCharge = $this->service
            ->offlineAuth(32.10)
            ->withCard(TestCreditCard::validVisaCreditCard())            
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->withDirectMarketData($directMarketData)                
            ->withOfflineAuthCode('654321')   
            ->withAllowDuplicates(true)
            ->withShippingAmtInfo(15)
            ->execute();

        $this->assertEquals('00', $offlineCharge->responseCode);
        $this->assertNotNull($offlineCharge->transactionId);        

        $reportTxnDetail = $this->service
                ->get()
                ->withTransactionId($offlineCharge->transactionId)
                ->execute();        
        
        $this->assertNotNull($reportTxnDetail);
        $this->assertEquals(15, $reportTxnDetail->shippingAmount);
    }    
    
    public function test033OfflineAuthorization()
    {
        $directMarketData = new HpsDirectMarketData('123456');

        $response = $this->service
            ->offlineAuth(17.10)
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withOfflineAuthCode('654321')
            ->withDirectMarketData($directMarketData)
            ->withAllowDuplicates(true)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
    }

    /// RETURN

    public function test034OfflineCreditReturn()
    {
        $directMarketData = new HpsDirectMarketData('123456');

        $response = $this->service
            ->refund(15.15)
            ->withCard(TestCreditCard::validMastercardCreditCard())
            ->withDirectMarketData($directMarketData)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
    }

    /// ONLINE VOID / REVERSAL

    public function test035VoidTest10()
    {
        $voidResponse = $this->service
            ->void(self::$transactionId10)
            ->execute();
        $this->assertEquals(true, $voidResponse != null);
        $this->assertEquals('00', $voidResponse->responseCode);
    }

    public function test036VoidTest20()
    {
        $voidResponse = $this->service
            ->void(self::$transactionId20)
            ->execute();
        $this->assertEquals(true, $voidResponse != null);
        $this->assertEquals('00', $voidResponse->responseCode);
    }

    /// ADVANCED FRAUD SCREENING

    /**
     * TODO: Change code assertions when AFS is enabled on account
     */
    public function test037FraudPreventionSale()
    {
        $response = $this->service
            ->charge(15000)
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        // $this->assertEquals('FR', $response->responseCode);
    }

    /**
     * TODO: Change code assertions when AFS is enabled on account
     */
    public function test038FraudPreventionReturn()
    {
        $response = $this->service
            ->refund(15000)
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        // $this->assertEquals('41', $response->responseCode);
    }

    /// ONE CARD - GSB CARD FUNCTIONS

    /// BALANCE INQUIRY

    public function test037BalanceInquiryGsb()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860';
        $cardHolder->address->zip = '75024';

        $response = $this->service
            ->prepaidBalanceInquiry()
            ->withCard(TestCreditCard::validGsbCardEcommerce())
            ->withCardHolder($cardHolder)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
    }

    /// ADD VALUE

    /**
     * Test only fails due to account settings.
     *
     * @expectedException HpsCreditException
     */
    public function test038AddValueGsb()
    {
        $card = new HpsTrackData();
        $card->value = '%B6277220572999800^   /                         ^49121010557010000016000000?F;6277220572999800=49121010557010000016?';

        $response = $this->service
            ->prepaidAddValue(15.00)
            ->withTrackData($card)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
    }

    /// SALE

    public function test039ChargeGsb()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860';
        $cardHolder->address->zip = '75024';

        $directMarketData = new HpsDirectMarketData('123456');

        $response = $this->service
            ->charge(2.05)
            ->withCard(TestCreditCard::validGsbCardEcommerce())
            ->withCardHolder($cardHolder)
            ->withDirectMarketData($directMarketData)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        self::$transactionId39 = $response->transactionId;
    }

    public function test040ChargeGsb()
    {
        $cardHolder = new HpsCardHolder();
        $cardHolder->address = new HpsAddress();
        $cardHolder->address->address = '6860';
        $cardHolder->address->zip = '75024';

        $directMarketData = new HpsDirectMarketData('123456');

        $response = $this->service
            ->charge(2.10)
            ->withCard(TestCreditCard::validGsbCardEcommerce())
            ->withCardHolder($cardHolder)
            ->withDirectMarketData($directMarketData)
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
    }

    /// ONLINE VOID / REVERSAL

    public function test041VoidGsb()
    {
        $voidResponse = $this->service
            ->void(self::$transactionId39)
            ->execute();
        $this->assertEquals(true, $voidResponse != null);
        $this->assertEquals('00', $voidResponse->responseCode);
    }

    /// HMS GIFT - REWARDS

    /// GIFT

    /**
     * @return \HpsGiftCard
     */
    public function giftCard1()
    {
        $card = new HpsGiftCard();
        $card->number = '5022440000000000098';

        return $card;
    }
    /**
     * @return \HpsGiftCard
     */
    public function giftCard2()
    {
        $card = new HpsGiftCard();
        $card->number = '5022440000000000007';

        return $card;
    }

    /// ACTIVATE

    public function test042ActivateGift1()
    {
        $response = $this->giftService
            ->activate(6.00)
            ->withCard($this->giftCard1())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    public function test043ActivateGift2()
    {
        $response = $this->giftService
            ->activate(7.00)
            ->withCard($this->giftCard2())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    /// LOAD / ADD VALUE

    public function test044AddValueGift1()
    {
        $response = $this->giftService
            ->addValue(8.00)
            ->withCurrency('usd')
            ->withCard($this->giftCard1())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    public function test045AddValueGift2()
    {
        $response = $this->giftService
            ->addValue(9.00)
            ->withCurrency('usd')
            ->withCard($this->giftCard2())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    /// BALANCE INQUIRY

    public function test046BalanceInquiryGift1()
    {
        $response = $this->giftService
            ->balance()
            ->withCard($this->giftCard1())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('10.00', $response->balanceAmount);
    }

    public function test047BalanceInquiryGift2()
    {
        $response = $this->giftService
            ->balance()
            ->withCard($this->giftCard2())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('10.00', $response->balanceAmount);
    }

    /// REPLACE / TRANSFER

    public function test048ReplaceGift1()
    {
        $response = $this->giftService
            ->replace()
            ->withOldCard($this->giftCard1())
            ->withNewCard($this->giftCard2())
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    public function test049ReplaceGift2()
    {
        $response = $this->giftService
            ->replace()
            ->withOldCard($this->giftCard2())
            ->withNewCard($this->giftCard1())
            ->execute();

        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    /// SALE / REDEEM

    public function test050SaleGift1()
    {
        $response = $this->giftService
            ->sale(1.00)
            ->withCard($this->giftCard1())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    public function test051SaleGift2()
    {
        $response = $this->giftService
            ->sale(2.00)
            ->withCard($this->giftCard2())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    public function test052SaleGift1Void()
    {
        $response = $this->giftService
            ->sale(3.00)
            ->withCard($this->giftCard1())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        self::$transactionId52 = $response->transactionId;
    }

    public function test053SaleGift2Reversal()
    {
        $response = $this->giftService
            ->sale(4.00)
            ->withCard($this->giftCard2())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        self::$transactionId53 = $response->transactionId;
    }

    /// VOID

    public function test054VoidGift()
    {
        $voidResponse = $this->giftService
            ->void(self::$transactionId52)
            ->execute();
        $this->assertEquals(true, $voidResponse != null);
        $this->assertEquals('0', $voidResponse->responseCode);
    }

    /// REVERSAL

    public function test055ReversalGift()
    {
        $reversalResponse = $this->giftService
            ->reverse(4.00)
            ->withTransactionId(self::$transactionId53)
            ->execute();
        $this->assertEquals(true, $reversalResponse != null);
        $this->assertEquals('0', $reversalResponse->responseCode);
    }

    public function test056ReversalGift2()
    {
        $reversalResponse = $this->giftService
            ->reverse(2.00)
            ->withCard($this->giftCard2())
            ->execute();
        $this->assertEquals(true, $reversalResponse != null);
        $this->assertEquals('0', $reversalResponse->responseCode);
    }

    /// DEACTIVATE

    public function test057DeactivateGift1()
    {
        $response = $this->giftService
            ->deactivate()
            ->withCard($this->giftCard1())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    /// RECEIPTS MESSAGING

    public function test058ReceiptsMessaging()
    {
        return;  # print and scan receipt for test 51
    }

    /// REWARD

    /// BALANCE INQUIRY

    public function test059BalanceInquiryRewards1()
    {
        $response = $this->giftService
            ->balance()
            ->withCard($this->giftCard1())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('0', $response->pointsBalanceAmount);
    }

    public function test060BalanceInquiryRewards2()
    {
        $response = $this->giftService
            ->balance()
            ->withCard($this->giftCard2())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        $this->assertEquals('0', $response->pointsBalanceAmount);
    }

    /// ALIAS

    public function test061CreateAliasGift1()
    {
        $response = $this->giftService
            ->alias()
            ->withAlias('9725550100')
            ->withAction('CREATE')
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    public function test062CreateAliasGift2()
    {
        $response = $this->giftService
            ->alias()
            ->withAlias('9725550100')
            ->withAction('CREATE')
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    public function test063AddAliasGift1()
    {
        $response = $this->giftService
            ->alias()
            ->withCard($this->giftCard1())
            ->withAlias('2145550199')
            ->withAction('ADD')
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    public function test064AddAliasGift2()
    {
        $response = $this->giftService
            ->alias()
            ->withCard($this->giftCard2())
            ->withAlias('2145550199')
            ->withAction('ADD')
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    public function test065DeleteAliasGift1()
    {
        $response = $this->giftService
            ->alias()
            ->withCard($this->giftCard1())
            ->withAlias('2145550199')
            ->withAction('DELETE')
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    /// SALE / REDEEM

    public function test066RedeemPointsGift1()
    {
        $response = $this->giftService
            ->sale(100)
            ->withCurrency('points')
            ->withCard($this->giftCard1())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    public function test067RedeemPointsGift2()
    {
        $response = $this->giftService
            ->sale(200)
            ->withCurrency('points')
            ->withCard($this->giftCard2())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    public function test068RedeemPointsGift2()
    {
        $gift = new HpsGiftCard();
        $gift->alias = '9725550100';

        $response = $this->giftService
            ->sale(300)
            ->withCurrency('points')
            ->withCard($gift)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    /// REWARDS

    public function test069RewardsGift1()
    {
        $response = $this->giftService
            ->reward(10)
            ->withCard($this->giftCard1())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    public function test070RewardsGift2()
    {
        $response = $this->giftService
            ->reward(11)
            ->withCard($this->giftCard2())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    /// REPLACE / TRANSFER

    public function test071ReplaceGift1()
    {
        $response = $this->giftService
            ->replace()
            ->withOldCard($this->giftCard1())
            ->withNewCard($this->giftCard2())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    public function test072ReplaceGift2()
    {
        $response = $this->giftService
            ->replace()
            ->withOldCard($this->giftCard2())
            ->withNewCard($this->giftCard1())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    /// DEACTIVATE

    public function test073DeactivateGift1()
    {
        $response = $this->giftService
            ->deactivate()
            ->withCard($this->giftCard1())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    public function test074DeactivateGift2()
    {
        $response = $this->giftService
            ->deactivate()
            ->withCard($this->giftCard2())
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
    }

    /// RECEIPTS MESSAGING

    public function test075ReceiptsMessaging()
    {
        return;  # print and scan receipt for test 51
    }

    /// CLOSE BATCH

    public function test999CloseBatch()
    {
        try {
            $response = $this->batchService->closeBatch();
            if ($response == null) {
                $this->fail("Response == null");
            }
            // printf('batch id: %s', $response->id);
            // printf('sequence number: %s', $response->sequenceNumber);
        } catch (HpsException $e) {
            $this->fail($e->getMessage());
        }
    }
    
    public function testOfflineChargeConvenienceAndShippingAmount()
    {
        $directMarketData = new HpsDirectMarketData('123456');
        $offlineCharge = $this->service
            ->offlineCharge(50)
            ->withCard(TestCreditCard::validVisaCreditCard())            
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->withDirectMarketData($directMarketData)                
            ->withOfflineAuthCode('654321')   
            ->withAllowDuplicates(true)
            ->withConvenienceAmtInfo(20)                    
            ->withShippingAmtInfo(15)
            ->execute();

        $this->assertEquals('00', $offlineCharge->responseCode);
        $this->assertNotNull($offlineCharge->transactionId);        

        $reportTxnDetail = $this->service
                ->get()
                ->withTransactionId($offlineCharge->transactionId)
                ->execute();        
        
        $this->assertNotNull($reportTxnDetail);
        $this->assertEquals(20, $reportTxnDetail->convenienceAmount);
        $this->assertEquals(15, $reportTxnDetail->shippingAmount);
    }    

    public function testOfflineAuthConvenienceAndShippingAmount()
    {
        $directMarketData = new HpsDirectMarketData('123456');
        
        $offlineCharge = $this->service
            ->offlineAuth(50)
            ->withCard(TestCreditCard::validVisaCreditCard())            
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->withDirectMarketData($directMarketData)                
            ->withOfflineAuthCode('654321')   
            ->withAllowDuplicates(true)
            ->withConvenienceAmtInfo(20)                    
            ->withShippingAmtInfo(15)                           
            ->execute();

        $this->assertEquals('00', $offlineCharge->responseCode);
        $this->assertNotNull($offlineCharge->transactionId);        

        $reportTxnDetail = $this->service
                ->get()
                ->withTransactionId($offlineCharge->transactionId)
                ->execute();        
       
        $this->assertNotNull($reportTxnDetail);
        $this->assertEquals(20, $reportTxnDetail->convenienceAmount);
        $this->assertEquals(15, $reportTxnDetail->shippingAmount);
    }
    
    /**
     * @expectedException        HpsInvalidRequestException
     * @expectedExceptionCode    HpsExceptionCodes::INVALID_AMOUNT
     * @expectedExceptionMessage Must be greater than or equal to 0
     */
    public function testOfflineChargeConvenienceAndShippingInvalidAmount()
    {
        $directMarketData = new HpsDirectMarketData('123456');
        $this->service
            ->offlineCharge(50)
            ->withCard(TestCreditCard::validVisaCreditCard())            
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->withDirectMarketData($directMarketData)                
            ->withOfflineAuthCode('654321')   
            ->withAllowDuplicates(true)
            ->withConvenienceAmtInfo(-20)                    
            ->withShippingAmtInfo(-15)
            ->execute();
    }    

    /**
     * @expectedException        HpsInvalidRequestException
     * @expectedExceptionCode    HpsExceptionCodes::INVALID_AMOUNT
     * @expectedExceptionMessage Must be greater than or equal to 0
     */
    public function testOfflineAuthConvenienceAndShippingInvalidAmount()
    {
        $directMarketData = new HpsDirectMarketData('123456');
        
        $this->service
            ->offlineAuth(50)
            ->withCard(TestCreditCard::validVisaCreditCard())            
            ->withCardHolder(TestCardHolder::ValidCardHolder())
            ->withDirectMarketData($directMarketData)                
            ->withOfflineAuthCode('654321')   
            ->withAllowDuplicates(true)
            ->withConvenienceAmtInfo(-20)                    
            ->withShippingAmtInfo(-15)                           
            ->execute();
    }
}

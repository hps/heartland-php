<?php

class ECommerceCheckTests extends PHPUnit_Framework_TestCase
{
    /** @var HpsBatchService|null */
    private $batchService          = null;

    /** @var HpsFluentCheckService|null */
    private $service               = null;

    /** @var string|null */
    public static $transactionId01 = null;

    /** @var string|null */
    public static $transactionId05 = null;

    /** @var string|null */
    public static $transactionId10 = null;

    /** @var string|null */
    public static $transactionId14 = null;

    /** @var string|null */
    public static $transactionId23 = null;

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
        $this->service      = new HpsFluentCheckService($this->config());
        $this->batchService = new HpsBatchService($this->config());
        $this->publicKey    = 'pkapi_cert_jKc1FtuyAydZhZfbB3';
    }

    # certification check
    public function baseCheck()
    {
        $certification                = new HpsCheck();
        $certification->accountNumber = '24413815';
        $certification->routingNumber = '490000018';

        $address          = new HpsAddress();
        $address->address = '123 Main St.';
        $address->city    = 'Downtown';
        $address->state   = 'NJ';
        $address->zip     = '12345';

        $checkHolder            = new HpsCheckHolder();
        $checkHolder->address   = $address;
        $checkHolder->dlNumber  = '09876543210';
        $checkHolder->dlState   = 'TX';
        $checkHolder->firstName = 'John';
        $checkHolder->lastName  = 'Doe';
        $checkHolder->phone     = '8003214567';
        $checkHolder->dobYear   = '1997';
        $checkHolder->ssl4      = '4321';

        $certification->checkHolder = $checkHolder;

        return $certification;
    }

    /// GETI

    /// Check Sale

    /// ACH Debit - Consumer

    public function test001ConsumerPersonalChecking()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::PPD;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::PERSONAL;
        $check->accountType = HpsAccountType::CHECKING;

        $response = $this->service
            ->sale(11.00)
            ->withCheck($check)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        printf("\n001: %s", $response->transactionId);
        self::$transactionId01 = $response->transactionId;
    }

    public function test002ConsumerBusinessChecking()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::PPD;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->accountType = HpsAccountType::CHECKING;

        $response = $this->service
            ->sale(12.00)
            ->withCheck($check)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        printf("\n002: %s", $response->transactionId);
    }

    public function test003ConsumerPersonalSavings()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::PPD;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::PERSONAL;
        $check->accountType = HpsAccountType::SAVINGS;

        $response = $this->service
            ->sale(13.00)
            ->withCheck($check)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        printf("\n003: %s", $response->transactionId);
    }

    public function test004ConsumerBusinessSavings()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::PPD;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->accountType = HpsAccountType::SAVINGS;

        $response = $this->service
            ->sale(14.00)
            ->withCheck($check)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        printf("\n004: %s", $response->transactionId);
    }

    /// ACH Debit - Corporate

    public function test005CorporatePersonalChecking()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::CCD;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::PERSONAL;
        $check->accountType = HpsAccountType::CHECKING;
        $check->checkHolder->checkName = 'Heartland Pays';

        $response = $this->service
            ->sale(15.00)
            ->withCheck($check)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        printf("\n005: %s", $response->transactionId);
        self::$transactionId05 = $response->transactionId;
    }

    public function test006CorporateBusinessChecking()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::CCD;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->accountType = HpsAccountType::CHECKING;
        $check->checkHolder->checkName = 'Heartland Pays';

        $response = $this->service
            ->sale(16.00)
            ->withCheck($check)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        printf("\n006: %s", $response->transactionId);
    }

    public function test007CorporatePersonalSavings()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::CCD;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::PERSONAL;
        $check->accountType = HpsAccountType::SAVINGS;
        $check->checkHolder->checkName = 'Heartland Pays';

        $response = $this->service
            ->sale(17.00)
            ->withCheck($check)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        printf("\n007: %s", $response->transactionId);
    }

    public function test008CorporateBusinessSavings()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::CCD;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->accountType = HpsAccountType::SAVINGS;
        $check->checkHolder->checkName = 'Heartland Pays';

        $response = $this->service
            ->sale(18.00)
            ->withCheck($check)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        printf("\n008: %s", $response->transactionId);
    }

    /// eGold

    public function test009EgoldPersonalChecking()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::POP;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::PERSONAL;
        $check->accountType = HpsAccountType::CHECKING;

        $response = $this->service
            ->sale(11.00)
            ->withCheck($check)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        printf("\n009: %s", $response->transactionId);
    }

    public function test010EgoldBusinessChecking()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::POP;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->accountType = HpsAccountType::CHECKING;

        $response = $this->service
            ->sale(12.00)
            ->withCheck($check)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        printf("\n010: %s", $response->transactionId);
        self::$transactionId10 = $response->transactionId;
    }

    public function test011EgoldPersonalSavings()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::POP;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::PERSONAL;
        $check->accountType = HpsAccountType::SAVINGS;

        $response = $this->service
            ->sale(13.00)
            ->withCheck($check)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        printf("\n011: %s", $response->transactionId);
    }

    public function test012EgoldBusinessSavings()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::POP;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->accountType = HpsAccountType::SAVINGS;

        $response = $this->service
            ->sale(14.00)
            ->withCheck($check)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        printf("\n012: %s", $response->transactionId);
    }

    /// eSilver

    public function test013EsilverPersonalChecking()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::POP;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::PERSONAL;
        $check->accountType = HpsAccountType::CHECKING;

        $response = $this->service
            ->sale(15.00)
            ->withCheck($check)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        printf("\n013: %s", $response->transactionId);
    }

    public function test014EsilverBusinessChecking()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::POP;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->accountType = HpsAccountType::CHECKING;

        $response = $this->service
            ->sale(16.00)
            ->withCheck($check)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        printf("\n014: %s", $response->transactionId);
        self::$transactionId14 = $response->transactionId;
    }

    public function test015EsilverPersonalSavings()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::POP;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::PERSONAL;
        $check->accountType = HpsAccountType::SAVINGS;

        $response = $this->service
            ->sale(17.00)
            ->withCheck($check)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        printf("\n015: %s", $response->transactionId);
    }

    public function test016EsilverBusinessSavings()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::POP;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->accountType = HpsAccountType::SAVINGS;

        $response = $this->service
            ->sale(18.00)
            ->withCheck($check)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        printf("\n016: %s", $response->transactionId);
    }

    /// eBronze (verify only)

    public function test017EbronzePersonalChecking()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::EBRONZE;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::PERSONAL;
        $check->accountType = HpsAccountType::CHECKING;

        $response = $this->service
            ->sale()
            ->withCheck($check)
            ->withCheckVerify(true)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        printf("\n017: %s", $response->transactionId);
    }

    public function test018EbronzeBusinessChecking()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::EBRONZE;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->accountType = HpsAccountType::CHECKING;

        $response = $this->service
            ->sale()
            ->withCheck($check)
            ->withCheckVerify(true)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        printf("\n018: %s", $response->transactionId);
    }

    public function test019EbronzePersonalSavings()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::EBRONZE;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::PERSONAL;
        $check->accountType = HpsAccountType::SAVINGS;

        $response = $this->service
            ->sale()
            ->withCheck($check)
            ->withCheckVerify(true)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        printf("\n019: %s", $response->transactionId);
    }

    public function test020EbronzeBusinessSavings()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::EBRONZE;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->accountType = HpsAccountType::SAVINGS;

        $response = $this->service
            ->sale()
            ->withCheck($check)
            ->withCheckVerify(true)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('00', $response->responseCode);
        printf("\n020: %s", $response->transactionId);
    }

    /// Checks-by-Web

    public function test021WebPersonalChecking()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::WEB;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::PERSONAL;
        $check->accountType = HpsAccountType::CHECKING;

        $response = $this->service
            ->sale(19.00)
            ->withCheck($check)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        printf("\n021: %s", $response->transactionId);
    }

    public function test022WebBusinessChecking()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::WEB;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->accountType = HpsAccountType::CHECKING;

        $response = $this->service
            ->sale(20.00)
            ->withCheck($check)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        printf("\n022: %s", $response->transactionId);
    }

    public function test023WebPersonalSavings()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::WEB;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::PERSONAL;
        $check->accountType = HpsAccountType::SAVINGS;

        $response = $this->service
            ->sale(21.00)
            ->withCheck($check)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        printf("\n023: %s", $response->transactionId);
        self::$transactionId23 = $response->transactionId;
    }

    public function test024WebBusinessSavings()
    {
        $check = $this->baseCheck();
        $check->secCode = HpsSECCode::WEB;
        $check->dataEntryMode = HpsDataEntryMode::MANUAL;
        $check->checkType = HpsCheckType::BUSINESS;
        $check->accountType = HpsAccountType::SAVINGS;

        $response = $this->service
            ->sale(22.00)
            ->withCheck($check)
            ->execute();
        $this->assertEquals(true, $response != null);
        $this->assertEquals('0', $response->responseCode);
        printf("\n024: %s", $response->transactionId);
    }

    /// CHECK VOID

    public function test025PpdCheckVoid()
    {
        $voidResponse = $this->service
            ->void()
            ->withTransactionId(self::$transactionId01)
            ->execute();
        $this->assertEquals(true, $voidResponse != null);
        $this->assertEquals('0', $voidResponse->responseCode);
        printf("\n025: %s", $voidResponse->transactionId);
    }

    public function test026CcdCheckVoid()
    {

        $voidResponse = $this->service
            ->void()
            ->withTransactionId(self::$transactionId05)
            ->execute();
        $this->assertEquals(true, $voidResponse != null);
        $this->assertEquals('0', $voidResponse->responseCode);
        printf("\n026: %s", $voidResponse->transactionId);
    }

    public function test027PopCheckVoid()
    {
        $voidResponse = $this->service
            ->void()
            ->withTransactionId(self::$transactionId10)
            ->execute();
        $this->assertEquals(true, $voidResponse != null);
        $this->assertEquals('0', $voidResponse->responseCode);
        printf("\n027: %s", $voidResponse->transactionId);
    }

    public function test028PopCheckVoid()
    {
        $voidResponse = $this->service
            ->void()
            ->withTransactionId(self::$transactionId14)
            ->execute();
        $this->assertEquals(true, $voidResponse != null);
        $this->assertEquals('0', $voidResponse->responseCode);
        printf("\n028: %s", $voidResponse->transactionId);
    }

    public function test029WebCheckVoid()
    {
        $voidResponse = $this->service
            ->void()
            ->withTransactionId(self::$transactionId23)
            ->execute();
        $this->assertEquals(true, $voidResponse != null);
        $this->assertEquals('0', $voidResponse->responseCode);
        printf("\n029: %s", $voidResponse->transactionId);
    }
}

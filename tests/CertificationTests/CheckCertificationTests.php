<?php

require_once(dirname(__FILE__).'/../setup.php');

class CheckCertificationTests extends PHPUnit_Framework_TestCase{

    public function testACHDebitConsumer1(){
        $check = TestCheck::certification();

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale('SALE',$check, 11.00);
        $this->assertNotNull($response,'Response is null');
        $this->assertEquals('0',$response->responseCode);
        $this->assertEquals('Transaction Approved',$response->responseText);

        $voidResponse = $checkService->void($response->transactionId);
        $this->assertEquals('0',$voidResponse->responseCode);
        $this->assertEquals('Transaction Approved',$voidResponse->responseText);
    }

    public function testACHDebitConsumer2CheckingBusiness(){
        $check = TestCheck::certification();
        $check->checkType = 'business';

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale('SALE',$check, 12.00);
        $this->assertNotNull($response,'Response is null');
        $this->assertEquals('0',$response->responseCode);
        $this->assertEquals('Transaction Approved',$response->responseText);
    }

    public function testACHDebitConsumer3SavingsPersonal(){
        $check = TestCheck::certification();
        $check->accountType = 'savings';

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale('SALE',$check, 13.00);
        $this->assertNotNull($response,'Response is null');
        $this->assertEquals('0',$response->responseCode);
        $this->assertEquals('Transaction Approved',$response->responseText);
    }

    public function testACHDebitConsumer4SavingsBusiness(){
        $check = TestCheck::certification();
        $check->accountType = 'savings';
        $check->checkType = 'business';

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale('SALE',$check, 14.00);
        $this->assertNotNull($response,'Response is null');
        $this->assertEquals('0',$response->responseCode);
        $this->assertEquals('Transaction Approved',$response->responseText);
    }

    public function testACHDebitCorporate5(){
        $check = TestCheck::certification();
        $check->secCode = 'POP';

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale('SALE',$check, 15.00);
        $this->assertNotNull($response,'Response is null');
        $this->assertEquals('0',$response->responseCode);
        $this->assertEquals('Transaction Approved',$response->responseText);

        $voidResponse = $checkService->void($response->transactionId);
        $this->assertEquals('0',$voidResponse->responseCode);
        $this->assertEquals('Transaction Approved',$voidResponse->responseText);
    }

    public function testACHDebitCorporate6CheckingBusiness(){
        $check = TestCheck::certification();
        $check->checkType = 'business';
        $check->secCode ='eBronze';

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale('SALE',$check, 16.00);
        $this->assertNotNull($response,'Response is null');
        $this->assertEquals('0',$response->responseCode);
        $this->assertEquals('Transaction Approved',$response->responseText);
    }

    public function testACHDebitCorporate7SavingsPersonal(){
        $check = TestCheck::certification();
        $check->accountType = 'savings';
        $check->secCode = 'ccd';

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale('SALE',$check, 17.00);
        $this->assertNotNull($response,'Response is null');
        $this->assertEquals('0',$response->responseCode);
        $this->assertEquals('Transaction Approved',$response->responseText);
    }

    public function testACHDebitCorporate8SavingsBusiness(){
        $check = TestCheck::certification();
        $check->accountType = 'savings';
        $check->checkType = 'business';
        $check->secCode = 'ccd';

        $checkService = new HpsCheckService(TestServicesConfig::CertServicesConfigWithDescriptor());
        $response = $checkService->sale('SALE',$check, 18.00);
        $this->assertNotNull($response,'Response is null');
        $this->assertEquals('0',$response->responseCode);
        $this->assertEquals('Transaction Approved',$response->responseText);
    }
} 
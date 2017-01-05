<?php

class FluentCardHolderDataTest extends PHPUnit_Framework_TestCase{
    
    protected $service;

    protected function setUp()
    {
        $this->service = new HpsFluentCreditService(TestServicesConfig::validMultiUseConfig());
    }
    
    /**
     * @test
     * Testing get exception when first name length is greater than 26
     */
    public function testCardHolderInValidFirstNameLength()
    {
        try {
            $response = $this->service
            ->charge()
            ->withAmount(10)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::certCardHolderLongFirstName())
            ->withAllowDuplicates(true)
            ->execute();           
            
        } catch (HpsInvalidRequestException $e) {
            $this->assertEquals(HpsExceptionCodes::INVALID_INPUT_LENGTH, $e->code);
            $this->assertEquals("The value for FirstName can be no more than 26 characters, Please try again after making corrections", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }
    
    /**
     * @test
     * Testing get exception and error code when last name length is greater than 26
     */
    public function testCardHolderInValidLastNameLength()
    {
        try {
            $response = $this->service
            ->charge()
            ->withAmount(20)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validMastercardCreditCard())
            ->withCardHolder(TestCardHolder::certCardHolderLongLastName())
            ->withAllowDuplicates(true)                    
            ->execute(); 
        } catch (HpsInvalidRequestException $e) {
            $this->assertEquals(HpsExceptionCodes::INVALID_INPUT_LENGTH, $e->code);
            $this->assertEquals("The value for LastName can be no more than 26 characters, Please try again after making corrections", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }
    
    /**
     * @test
     * Testing get exception and error code when city length is greater than 20
     */
    public function testCardHolderInValidCityLength()
    {
        try {
            $response = $this->service
            ->charge()
            ->withAmount(30)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validDiscoverCreditCard())
            ->withCardHolder(TestCardHolder::certCardHolderLongCityName())
            ->withAllowDuplicates(true)                    
            ->execute(); 
        } catch (HpsInvalidRequestException $e) {
            $this->assertEquals(HpsExceptionCodes::INVALID_INPUT_LENGTH, $e->code);
            $this->assertEquals("The value for City can be no more than 20 characters, Please try again after making corrections", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }
    
    /**
     * @test
     * Testing get exception and error code when state length is greater than 20
     */
    public function testCardHolderInValidStateLength()
    {
        try {
            $response = $this->service
            ->charge()
            ->withAmount(10)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::certCardHolderLongStateName())
            ->withAllowDuplicates(true)                    
            ->execute(); 
        } catch (HpsInvalidRequestException $e) {
            $this->assertEquals(HpsExceptionCodes::INVALID_INPUT_LENGTH, $e->code);
            $this->assertEquals("The value for State can be no more than 20 characters, Please try again after making corrections", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }
    
    /**
     * @test
     * Testing get exception and error code when Email length is greater than 100
     */
    public function testCardHolderInValidEmailLength()
    {
        try {
            $response = $this->service
            ->charge()
            ->withAmount(10)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::certCardHolderLongEmailAddress())
            ->withAllowDuplicates(true)                    
            ->execute(); 
            
        } catch (HpsInvalidRequestException $e) {
            $this->assertEquals(HpsExceptionCodes::INVALID_INPUT_LENGTH, $e->code);
            $this->assertEquals("The value for Email can be no more than 100 characters, Please try again after making corrections", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }
    
    /**
     * @test
     * Testing get exception and error code when Email address is invalid
     */
    public function testCardHolderInValidEmailAddress()
    {
        try {
            $response = $this->service
            ->charge()
            ->withAmount(10)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::certCardHolderInvalidEmailAddress())
            ->withAllowDuplicates(true)                    
            ->execute();             
        } catch (HpsInvalidRequestException $e) {
            $this->assertEquals(HpsExceptionCodes::INVALID_EMAIL_ADDRESS, $e->code);
            $this->assertEquals("Invalid email address", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }
   
   
    /**
     * @test
     * Testing get exception when phone number length is greater than 20
     */
    public function testCardHolderInValidPhoneNumberLength()
    {
        try {
            $response = $this->service
            ->charge()
            ->withAmount(10)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::certCardHolderLongPhoneNumber())
            ->withAllowDuplicates(true)                    
            ->execute();             
            
        } catch (HpsInvalidRequestException $e) {
            $this->assertEquals(HpsExceptionCodes::INVALID_PHONE_NUMBER, $e->code);
            $this->assertEquals("The value for phone number can be no more than 20 characters, Please try again after making corrections", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }
    
    /**
     * @test
     * Testing get exception when zip code length is greater than 9
     */
    public function testCardHolderInValidZipCodeLength()
    {
        try {
            $response = $this->service
            ->charge()
            ->withAmount(10)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::certCardHolderLongZipCode())
            ->withAllowDuplicates(true)                    
            ->execute(); 
            
        } catch (HpsInvalidRequestException $e) {
            $this->assertEquals(HpsExceptionCodes::INVALID_ZIP_CODE, $e->code);
            $this->assertEquals("The value for zip code can be no more than 9 characters, Please try again after making corrections", $e->getMessage());
            return;
        }

        $this->fail("No exception was thrown.");
    }
    
    public function testChargeWithCanadianZipCode()
    {
        $response = $this->service
            ->charge()
            ->withAmount(10)
            ->withCurrency("usd")
            ->withCard(TestCreditCard::validVisaCreditCard())
            ->withCardHolder(TestCardHolder::certCardHolderCanadianZipCode())
            ->withAllowDuplicates(true)                
            ->execute();

        $this->assertNotNull($response);
        $this->assertEquals("00", $response->responseCode);
    }
    
}

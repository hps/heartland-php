<?php

class GatewayGiftCardTest extends PHPUnit_Framework_TestCase
{

    public function testGiftCardManualCardShouldActivate()
    {
        $giftService = new HpsGiftCardService(TestServicesConfig::ValidMultiUseConfig());
        $response = $giftService->activate(100.00, 'usd', TestGiftCard::validGiftCardNotEncrypted());
        $this->assertEquals('0', $response->responseCode);
    }

    public function testGiftCardManualCardShouldAddValue()
    {
        $giftService = new HpsGiftCardService(TestServicesConfig::ValidMultiUseConfig());
        $response = $giftService->addValue(10.00, 'usd', TestGiftCard::validGiftCardNotEncrypted());
        $this->assertEquals('0', $response->responseCode);
    }

    public function testGiftCardManualCardShouldBalance()
    {
        $giftService = new HpsGiftCardService(TestServicesConfig::ValidMultiUseConfig());
        $response = $giftService->balance(TestGiftCard::validGiftCardNotEncrypted());
        $this->assertEquals('0', $response->responseCode);
    }

    public function testGiftCardManualCardShouldDeactivate()
    {
        $giftService = new HpsGiftCardService(TestServicesConfig::ValidMultiUseConfig());
        $response = $giftService->deactivate(TestGiftCard::validGiftCardNotEncrypted());
        $this->assertEquals('0', $response->responseCode);
    }

    public function testGiftCardManualCardShouldReplace()
    {
        $giftService = new HpsGiftCardService(TestServicesConfig::ValidMultiUseConfig());
        $response = $giftService->replace(TestGiftCard::validGiftCardNotEncrypted(), TestGiftCard::validGiftCardNotEncrypted2());
        $this->assertEquals('0', $response->responseCode);
    }

    public function testGiftCardManualCardShouldReward()
    {
        $giftService = new HpsGiftCardService(TestServicesConfig::ValidMultiUseConfig());
        $response = $giftService->reward(TestGiftCard::validGiftCardNotEncrypted(), 10.00);
        $this->assertEquals('0', $response->responseCode);
    }

    public function testGiftCardManualCardShouldSale()
    {
        $giftService = new HpsGiftCardService(TestServicesConfig::ValidMultiUseConfig());
        $response = $giftService->sale(TestGiftCard::validGiftCardNotEncrypted(), 10.00);
        $this->assertEquals('0', $response->responseCode);
    }

    public function testGiftCardManualCardShouldVoid()
    {
        $giftService = new HpsGiftCardService(TestServicesConfig::ValidMultiUseConfig());
        $response = $giftService->sale(TestGiftCard::validGiftCardNotEncrypted(), 10.00);
        $this->assertEquals('0', $response->responseCode);
        $void_response = $giftService->void($response->transactionId);
        $this->assertEquals('0', $void_response->responseCode);
    }

    public function testGiftCardManualCardShouldReverseUsingTxnId()
    {
        $giftService = new HpsGiftCardService(TestServicesConfig::ValidMultiUseConfig());
        $response = $giftService->sale(TestGiftCard::validGiftCardNotEncrypted(), 10.00);
        $this->assertEquals('0', $response->responseCode);
        $reverseResponse = $giftService->reverse($response->transactionId, 10.00);
        $this->assertEquals('0', $reverseResponse->responseCode);
    }

    public function testGiftCardManualCardShouldReverseUsingGiftCard()
    {
        $giftService = new HpsGiftCardService(TestServicesConfig::ValidMultiUseConfig());
        $response = $giftService->sale(TestGiftCard::validGiftCardNotEncrypted(), 10.00);
        $this->assertEquals('0', $response->responseCode);
        $reverseResponse = $giftService->reverse(TestGiftCard::validGiftCardNotEncrypted(), 10.00);
        $this->assertEquals('0', $reverseResponse->responseCode);
    }

    /**
     * @expectedException        HpsException
     * @expectedExceptionMessage The pin is invalid
     * @expectedExceptionCode    HpsExceptionCodes::INVALID_PIN
     */
    public function testGiftCardManualCardWithInvalidPin()
    {
        $giftService = new HpsGiftCardService(TestServicesCOnfig::validMultiUseConfig());
        $response = $giftService->sale(TestGiftCard::validGiftCardNotEncrypted(), 3.05);
    }
}

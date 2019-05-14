<?php

/**
 * Class FluentGiftCardTest
 */
class FluentGiftCardTest extends PHPUnit_Framework_TestCase
{
    protected $service;

    protected function setUp()
    {
        $this->service = new HpsFluentGiftCardService(TestServicesConfig::validMultiUseConfig());
    }

    public function testActivateWithCard()
    {
        $response = $this->service
            ->activate()
            ->withAmount(100.00)
            ->withCurrency('usd')
            ->withCard(TestGiftCard::validGiftCardNotEncrypted())
            ->execute();

        $this->assertEquals('0', $response->responseCode);
    }
    
    public function testActivateWithToken()
    {
        $response = $this->service
            ->activate()
            ->withAmount(100.00)
            ->withCurrency('usd')
            ->withToken(TestGiftCard::validGiftCardToken())
            ->execute();

        $this->assertEquals('0', $response->responseCode);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Activate can only use one payment method
     */
    public function testActivateWithNoCardorToken()
    {
        $this->service
            ->activate()
            ->withAmount(10)
            ->withCurrency('usd')
            ->execute();
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Activate needs an amount
     */
    public function testActivateWithNoAmount()
    {
        $this->service
            ->activate()
            ->withCurrency('usd')
            ->withCard(TestGiftCard::validGiftCardNotEncrypted())
            ->execute();
    }

    public function testAddValueWithCard()
    {
        $response = $this->service
            ->addValue()
            ->withAmount(10.00)
            ->withCurrency('usd')
            ->withCard(TestGiftCard::validGiftCardNotEncrypted())
            ->execute();

        $this->assertEquals('0', $response->responseCode);
    }

    public function testAddValueWithToken()
    {
        $response = $this->service
            ->addValue()
            ->withAmount(10.00)
            ->withCurrency('usd')
            ->withToken(TestGiftCard::validGiftCardToken())
            ->execute();

        $this->assertEquals('0', $response->responseCode);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage AddValue can only use one payment method
     */
    public function testAddValueWithNoCardorToken()
    {
        $this->service
            ->addValue()
            ->withAmount(10)
            ->withCurrency('usd')
            ->execute();
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage AddValue needs an amount
     */
    public function testAddValueWithNoAmount()
    {
        $this->service
            ->addValue()
            ->withCurrency('usd')
            ->withCard(TestGiftCard::validGiftCardNotEncrypted())
            ->execute();
    }

    public function testBalanceWithCard()
    {
        $response = $this->service
            ->balance()
            ->withCard(TestGiftCard::validGiftCardNotEncrypted())
            ->execute();

        $this->assertEquals('0', $response->responseCode);
    }

    public function testBalanceWithToken()
    {
        $response = $this->service
            ->balance()
            ->withToken(TestGiftCard::validGiftCardToken())
            ->execute();

        $this->assertEquals('0', $response->responseCode);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Balance can only use one payment method
     */
    public function testBalanceWithNoCardorToken()
    {
        $this->service
            ->balance()
            ->execute();
    }

    public function testDeactivate()
    {
        $response = $this->service
            ->deactivate()
            ->withCard(TestGiftCard::validGiftCardNotEncrypted())
            ->execute();

        $this->assertEquals('0', $response->responseCode);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Deactivate can only use one payment method
     */
    public function testDeactivateWithNoCardorToken()
    {
        $this->service
            ->deactivate()
            ->execute();
    }

    public function testReplace()
    {
        $response = $this->service
            ->replace()
            ->withOldCard(TestGiftCard::validGiftCardNotEncrypted())
            ->withNewCard(TestGiftCard::validGiftCardNotEncrypted2())
            ->execute();

        $this->assertEquals('0', $response->responseCode);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Replace needs an oldCard
     */
    public function testReplaceWithNoOldCard()
    {
        $this->service
            ->replace()
            ->withNewCard(TestGiftCard::validGiftCardNotEncrypted2())
            ->execute();
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Replace needs a newCard
     */
    public function testReplaceWithNoNewCard()
    {
        $this->service
            ->replace()
            ->withOldCard(TestGiftCard::validGiftCardNotEncrypted())
            ->execute();
    }

    public function testReverse()
    {
        $response = $this->service
            ->sale()
            ->withCard(TestGiftCard::validGiftCardNotEncrypted())
            ->withAmount(10.00)
            ->execute();

        $this->assertEquals('0', $response->responseCode);

        $reverseResponse = $this->service
            ->reverse()
            ->withCard(TestGiftCard::validGiftCardNotEncrypted())
            ->withAmount(10.00)
            ->execute();

        $this->assertEquals('0', $reverseResponse->responseCode);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Reverse can only use one payment method
     */
    public function testReverseWithNoCardorToken()
    {
        $this->service
            ->reverse()
            ->withAmount(10)
            ->execute();
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Reverse needs an amount
     */
    public function testReverseWithNoAmount()
    {
        $this->service
            ->reverse()
            ->withCard(TestGiftCard::validGiftCardNotEncrypted())
            ->execute();
    }

    public function testReward()
    {
        $response = $this->service
            ->reward()
            ->withCard(TestGiftCard::validGiftCardNotEncrypted())
            ->withAmount(10.00)
            ->execute();

        $this->assertEquals('0', $response->responseCode);
    }

    public function testRewardWithToken()
    {
        $response = $this->service
            ->reward()
            ->withToken(TestGiftCard::validGiftCardToken())
            ->withAmount(10.00)
            ->execute();

        $this->assertEquals('0', $response->responseCode);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Reward can only use one payment method
     */
    public function testRewardWithNoCardorToken()
    {
        $this->service
            ->reward()
            ->withAmount(10)
            ->execute();
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Reward needs an amount
     */
    public function testRewardWithNoAmount()
    {
        $this->service
            ->reward()
            ->withCard(TestGiftCard::validGiftCardNotEncrypted())
            ->execute();
    }

    public function testSaleWithCard()
    {
        $response = $this->service
            ->sale()
            ->withCard(TestGiftCard::validGiftCardNotEncrypted())
            ->withAmount(10.00)
            ->execute();

        $this->assertEquals('0', $response->responseCode);
    }
    
    public function testSaleWithToken()
    {
        $response = $this->service
            ->sale()
            ->withToken(TestGiftCard::validGiftCardToken())
            ->withAmount(10.00)
            ->execute();

        $this->assertEquals('0', $response->responseCode);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Sale can only use one payment method
     */
    public function testSaleWithNoCardorToken()
    {
        $this->service
            ->sale()
            ->withAmount(10)
            ->execute();
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Sale needs an amount
     */
    public function testSaleWithNoAmount()
    {
        $this->service
            ->sale()
            ->withCard(TestGiftCard::validGiftCardNotEncrypted())
            ->execute();
    }

    public function testVoid()
    {
        $response = $this->service
            ->sale()
            ->withCard(TestGiftCard::validGiftCardNotEncrypted())
            ->withAmount(10.00)
            ->execute();

        $this->assertEquals('0', $response->responseCode);

        $voidResponse = $this->service
            ->void()
            ->withTransactionId($response->transactionId)
            ->execute();

        $this->assertEquals('0', $voidResponse->responseCode);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Void needs a transactionId
     */
    public function testVoidWithNoTransactionId()
    {
        $this->service
            ->void()
            ->execute();
    }

    public function testAliasWithCreateAction()
    {
        $response = $this->service
            ->alias()
            ->withAlias('9725550100')
            ->withAction('CREATE')
            ->execute();
            
        $this->assertEquals('0', $response->responseCode);
        $this->assertNotNull($response->giftCard->number->PIN);
    }

    public function testAliasWithAddAction()
    {
        $response = $this->service
            ->alias()
            ->withAlias('9725550100')
            ->withCard(TestGiftCard::validGiftCardNotEncrypted())
            ->withAction('ADD')
            ->execute();
            
        $this->assertEquals('0', $response->responseCode);
        $this->assertNotNull($response->giftCard->number->CardNbr);
    }

    public function testAliasWithDeleteAction()
    {
        $response = $this->service
            ->alias()
            ->withAlias('9725550100')
            ->withCard(TestGiftCard::validGiftCardNotEncrypted())
            ->withAction('DELETE')
            ->execute();
        
        $this->assertEquals('0', $response->responseCode);
        $this->assertNotNull($response->giftCard->number->CardNbr);
        $this->assertNotNull($response->giftCard->number->Alias);
    }
}

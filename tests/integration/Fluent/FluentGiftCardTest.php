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

    public function testActivate()
    {
        $response = $this->service
            ->activate()
            ->withAmount(100.00)
            ->withCurrency('usd')
            ->withCard(TestGiftCard::validGiftCardNotEncrypted())
            ->execute();

        $this->assertEquals('0', $response->responseCode);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Activate needs a card
     */
    public function testActivateWithNoCard()
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

    public function testAddValue()
    {
        $response = $this->service
            ->addValue()
            ->withAmount(10.00)
            ->withCurrency('usd')
            ->withCard(TestGiftCard::validGiftCardNotEncrypted())
            ->execute();

        $this->assertEquals('0', $response->responseCode);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage AddValue needs a card
     */
    public function testAddValueWithNoCard()
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

    public function testBalance()
    {
        $response = $this->service
            ->balance()
            ->withCard(TestGiftCard::validGiftCardNotEncrypted())
            ->execute();

        $this->assertEquals('0', $response->responseCode);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Balance needs a card
     */
    public function testBalanceWithNoCard()
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
     * @expectedExceptionMessage Deactivate needs a card
     */
    public function testDeactivateWithNoCard()
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
     * @expectedExceptionMessage Reverse needs a card
     */
    public function testReverseWithNoCard()
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

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Reward needs a card
     */
    public function testRewardWithNoCard()
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

    public function testSale()
    {
        $response = $this->service
            ->sale()
            ->withCard(TestGiftCard::validGiftCardNotEncrypted())
            ->withAmount(10.00)
            ->execute();

        $this->assertEquals('0', $response->responseCode);
    }

    /**
     * @expectedException        HpsArgumentException
     * @expectedExceptionMessage Sale needs a card
     */
    public function testSaleWithNoCard()
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
}

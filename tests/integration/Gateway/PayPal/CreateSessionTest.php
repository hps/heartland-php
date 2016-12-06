<?php

class GatewayPayPalCreateSessionTest extends PHPUnit_Framework_TestCase
{
    /** @var HpsPayPalService */
    protected $service = null;

    public function setup()
    {
        $config = new HpsServicesConfig();
        $config->username  = '30360021';
        $config->password  = '$Test1234';
        $config->deviceId  = '90911395';
        $config->licenseId = '20527';
        $config->siteId    = '20518';
        $config->soapServiceUri  = "https://api-uat.heartlandportico.com/paymentserver.v1/PosGatewayService.asmx";
        $this->service = new HpsPayPalService($config);
    }

    public function testCanCreateWithInvalidCharacters()
    {
        $buyer = new HpsBuyerData();
        $buyer->returnUrl = 'https://developer.heartlandpaymentsystems.com';
        $buyer->cancelUrl = 'https://developer.heartlandpaymentsystems.com';

        $payment = new HpsPaymentData();
        $payment->subtotal = '10';
        $payment->shippingAmount = '0';
        $payment->taxAmount = '0';
        $payment->paymentType = 'Sale';

        $lineItems = array();
        $lineItem = new HpsLineItem();
        $lineItem->number = '1';
        $lineItem->quantity = '1';
        $lineItem->name = 'Name with special™';
        $lineItem->description = 'Description with special™';
        $lineItem->amount = '10';
        $lineItems[] = $lineItem;

        $response = $this->service->createSession(
            $payment->subtotal + $payment->shippingAmount + $payment->taxAmount,
            'usd',
            $buyer,
            $payment,
            null,
            $lineItems
        );

        $this->assertNotNull($response);
        $this->assertNotNull($response->transactionId);
        $this->assertEquals('00', $response->responseCode);
    }
}

<?php

require_once '../../Hps.php';

// Amount
$amount = '258.45';

// Currency
$currency = 'usd';

// Create BuyerInfo
$buyer = new HpsBuyerData();
$buyer->returnUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/charge.php';
$buyer->cancelUrl = $buyer->returnUrl;

// Create PaymentInfo
$payment = new HpsPaymentData();
$payment->subtotal = '226.57';
$payment->shippingAmount = '12.74';
$payment->taxAmount = '19.14';
$payment->paymentType = 'Sale';

// Create ShippingInfo
$shipping = new HpsShippingInfo();
$shipping->name = 'Joe Tester';
$shipping->address = new HpsAddress();
$shipping->address->address = '6860 Dallas Pkwy';
$shipping->address->city = 'Plano';
$shipping->address->state = 'TX';
$shipping->address->zip = '75024';
$shipping->address->country = 'US';

// Line Items
$items = array();

$item1 = new HpsLineItem();
$item1->name = 'Blanton\'s Bourbon Single Barrel 750ML';
$item1->number = '1';
$item1->amount = '37.19';
$item1->quantity = '1';
$items[] = $item1;

$item2 = new HpsLineItem();
$item2->name = 'Pappy Van Winkle\'s Family Reserve 23-Year-Old Kentucky Straight Bourbon';
$item2->number = '1';
$item2->amount = '108.99';
$item2->quantity = '1';
$items[] = $item2;

$item3 = new HpsLineItem();
$item3->name = 'Blood Oath Bourbon Pact No. 1 750ML';
$item3->number = '1';
$item3->amount = '80.39';
$item3->quantity = '1';
$items[] = $item3;

// Create session
$config = new HpsServicesConfig();
$config->username = '30360021';
$config->password = '$Test1234';
$config->deviceId = '90911395';
$config->licenseId = '20527';
$config->siteId = '20518';
$config->soapServiceUri = 'https://api-uat.heartlandportico.com/paymentserver.v1/PosGatewayService.asmx?wsdl';

$service = new HpsPayPalService($config);
$response = $service->createSession($amount, $currency, $buyer, $payment, $shipping, $items);

$token = $response->sessionId;
header('Location: ' . $response->redirectUrl);
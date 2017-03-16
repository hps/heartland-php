<?php

$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
require_once '../../Hps.php';

// Amount
$amount = '258.45';

// Currency
$currency = 'usd';

// Create BuyerInfo
$buyer = new HpsBuyerData();
$buyer->returnUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/charge.php';
$buyer->cancelUrl = $buyer->returnUrl;
$buyer->payerId = $_GET['PayerID'];

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
$response = null;
$errorMessage = null;
try {
    $response = $service->sale($_GET['token'], $amount, $currency, $buyer, $payment, $shipping, $items);
} catch (HpsException $e) {
    $errorMessage = $e->getMessage();
}
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SecureSubmit PHP PayPal payment example">
    <meta name="author" content="Heartland">
    <title>PayPal Demo</title>

    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-md-11">
          <h1>Order details:</h1>
        </div>
        <div class="col-md-1">
          <a class="btn btn-success" href="/index.html" role="button" style="margin-top:20px">
            <span class="glyphicon glyphicon-home">Home</span>
          </a>
        </div>
      </div>

      <hr>
      <div class="row">
        <div class="col-md-6 info">
          <strong>Billing Info:</strong><br>
          <?= $billing->name; ?><br>
          <?= $billing->address->address; ?><br>
          <?= $billing->address->city; ?>, <?= $billing->address->state; ?> <?= $billing->address->zip; ?><br>
          <?= $billing->address->country; ?>
        </div>
        <div class="col-md-6 info">
          <strong>Shipping Info:</strong><br>
          <?= $shipping->name; ?><br>
          <?= $shipping->address->address; ?><br>
          <?= $shipping->address->city; ?>, <?= $shipping->address->state; ?> <?= $shipping->address->zip; ?><br>
          <?= $shipping->address->country; ?>
        </div>
      </div>

      <hr>
      <table class="table">
          <thead>
              <tr class="active">
                  <th width="10%">Id</th>
                  <th width="60%">Name</th>
                  <th width="10%">Amount</th>
                  <th width="10%">Quantity</th>
                  <th>Subtotal</th>
              </tr>
          </thead>
          <tbody>
              <?php foreach ($items as $item): ?>
                  <tr>
                      <td><?= $item->number; ?></td>
                      <td><?= $item->name; ?></td>
                      <td><?= $item->amount; ?></td>
                      <td><?= $item->quantity; ?></td>
                      <td><?= $item->amount * $item->quantity; ?></td>
                  </tr>
              <?php endforeach; ?>
          </tbody>
          <tfoot>
              <tr class="active">
                  <td colspan="3">&nbsp;</td>
                  <td>
                      Subtotal:<br>
                      Shipping:<br>
                      Tax:<br>
                      <strong>Total:</strong>
                  </td>
                  <td>
                      $<?= $payment->subtotal; ?><br>
                      $<?= $payment->shippingAmount; ?><br>
                      $<?= $payment->taxAmount; ?><br>
                      <strong>$<?= $amount; ?></strong>
                  </td>
              </tr>
          </tfoot>
      </table>
      <strong>Transaction Status:</strong>
      <?php if ($errorMessage == ''): ?>
          <span style="color:green">
              Success - Transaction Id: <?= $response->transactionId; ?>
          </span>
      <?php else: ?>
          <span style="color:red">
              Failure - <?= $errorMessage; ?>
          </span>
      <?php endif; ?>
    </div>
  </body>
</html>

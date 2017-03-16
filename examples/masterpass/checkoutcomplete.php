<?php
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
/**
 * MasterPass Test
 *
 * PHP Version 5.2+
 *
 * @category PHP
 * @package  HPS
 * @author   Heartland Payment Systems <EntApp_DevPortal@e-hps.com>
 * @license  https://github.com/hps/heartland-php/blob/master/LICENSE.txt Custom
 * @link     https://github.com/hps/heartland-php
 */

require '../../Hps.php';
session_start();
error_reporting(E_ALL);

// Grab parameters sent by MasterPass from query string
$status = isset($_GET['mpstatus'])
        ? $_GET['mpstatus']
        : '';
$resourceUrl = isset($_GET['checkout_resource_url'])
             ? urldecode($_GET['checkout_resource_url'])
             : '';
$oauthVerifier = isset($_GET['oauth_verifier'])
               ? $_GET['oauth_verifier']
               : '';
$oauthToken = isset($_GET['oauth_token'])
            ? $_GET['oauth_token']
            : '';
$pairingVerifier = isset($_GET['pairing_verifier'])
                 ? $_GET['pairing_verifier']
                 : '';
$pairingToken = isset($_GET['pairing_token'])
              ? $_GET['pairing_token']
              : '';

// Grab parameters sent by us from the first step
$action = $_GET['mp_action'];
$payload = urldecode($_GET['mp_payload']);
$orderId = $_GET['mp_orderId'];
$orderNumber = $_GET['mp_orderNumber'];

// Cancel request from MasterPass UI
if ($status == 'cancel') {
    header('Location: /index.php');
    exit();
}

$config = new HpsCentinelConfig();
$config->processorId    = 475;
$config->merchantId     = 'heartland_mark';
$config->transactionPwd = '5XcypXzRAkywLxgk';

$service = new HpsMasterPassService($config);

$orderData = new HpsOrderData();
$orderData->transactionStatus = $status;
if ($pairingToken !== '' && $pairingVerifier !== '') {
    $orderData->pairingToken = $pairingToken;
    $orderData->pairingVerifier = $pairingVerifier;
    $orderData->checkoutType = HpsCentinelCheckoutType::PAIRING_CHECKOUT;
}

// Authenticate the request with the information we've gathered
$result = $service->authenticate(
    $orderId,
    $oauthToken,
    $oauthVerifier,
    $payload,
    $resourceUrl,
    $orderData
);
error_log(print_r($result, true));

$buyer = new HpsBuyerData();
$buyer->firstName = 'John';
$buyer->lastName  = 'Consumer';
$address = new HpsAddress();
$address->address = '123 Main Street';
$address->city = 'Cleveland';
$address->state = 'OH';
$address->zip = '44111';
$buyer->address = $address;
$buyer->countryCode = 'US';
$buyer->phoneNumber = '2162162116';

$shipping = new HpsShippingInfo();
$shipping->firstName = 'John';
$shipping->lastName  = 'Consumer';
$address = new HpsAddress();
$address->address = '123 Main Street';
$address->city = 'Cleveland';
$address->state = 'OH';
$address->zip = '44111';
$shipping->address = $address;
$shipping->countryCode = 'US';
$shipping->phoneNumber = '2162162116';

$payment = new HpsPaymentData();

$lineItems = array();
$item1 = new HpsLineItem();
$item1->name = 'Demo 1';
$item1->description = 'This is a demo';
$item1->amount = 1;
$item1->quanity = 1;
$item1->number = '123456';
$lineItems[] = $item1;

$amount = 0;
foreach ($lineItems as $item) {
    $amount += $item->amount;
}

// Create an authorization
$response = null;
if ($action == 'sale') {
    $response = $service->sale(
        $orderId,
        $amount,
        'usd',
        $buyer,
        $payment,
        $shipping,
        $lineItems
    );
} else {
    $response = $service->authorize(
        $orderId,
        $amount,
        'usd',
        $buyer,
        $payment,
        $shipping,
        $lineItems
    );
}
error_log(print_r($response, true));

$captureEndpoint = '/capture.php?'
                 . 'orderId=' . $orderId . '&'
                 . 'orderNumber=' . $orderNumber . '&'
                 . 'amount=' . $amount;
$voidEndpoint = '/void.php?'
              . 'orderId=' . $orderId . '&'
              . 'orderNumber=' . $orderNumber;

?><!doctype html>
<html>
<head>
  <title>MasterPass Test</title>
</head>
<body>
  <a href="<?php echo $captureEndpoint; ?>">Capture</a> |
  <a href="<?php echo $voidEndpoint; ?>">Void</a>
  <ul>
    <?php if ($pairingToken !== '' && $pairingVerifier !== ''): ?>
      <li>Long Access Token: <?php echo $result->longAccessToken; ?></li>
    <?php endif; ?>
    <li>Transaction ID: <?php echo $result->transactionId; ?></li>
  </ul>
</body>
</html>

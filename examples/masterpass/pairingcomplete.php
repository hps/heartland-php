<?php
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
$pairingVerifier = isset($_GET['pairing_verifier'])
                 ? $_GET['pairing_verifier']
                 : '';
$pairingToken = isset($_GET['pairing_token'])
              ? $_GET['pairing_token']
              : '';

// Grab parameters sent by us from the first step
$payload = urldecode($_GET['mp_payload']);
$orderId = $_GET['mp_orderId'];
$orderNumber = $_GET['mp_orderNumber'];

// Cancel request from MasterPass UI
if ($pairingVerifier === '' && $pairingToken === '') {
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
$orderData->checkoutType = HpsCentinelCheckoutType::PAIRING;
$orderData->pairingToken = $pairingToken;
$orderData->pairingVerifier = $pairingVerifier;

// Authenticate the request with the information we've gathered
$result = $service->authenticate(
    $orderId,
    null,
    null,
    $payload,
    null,
    $orderData
);
error_log(print_r($result, true));
?><!doctype html>
<html>
<head>
  <title>MasterPass Test</title>
</head>
<body>
  <a href="/connect.php?longAccessToken=<?php echo $result->longAccessToken;?>">Checkout</a>
  <ul>
    <li>Long Access Token: <?php echo $result->longAccessToken; ?></li>
    <li>Transaction ID: <?php echo $result->transactionId; ?></li>
  </ul>
</body>
</html>

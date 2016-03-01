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
$orderId = isset($_GET['orderId'])
         ? $_GET['orderId']
         : '';
$orderNumber = isset($_GET['orderNumber'])
             ? $_GET['orderNumber']
             : '';

$config = new HpsCentinelConfig();
$config->processorId    = 475;
$config->merchantId     = 'heartland_mark';
$config->transactionPwd = '5XcypXzRAkywLxgk';

$service = new HpsMasterPassService($config);

$orderData = new HpsOrderData();
$orderData->orderNumber = $orderNumber;
$orderData->currencyCode = 'usd';

// Authenticate the request with the information we've gathered
$result = $service->void(
    $orderId,
    $orderData
);
error_log(print_r($result, true));
?><!doctype html>
<html>
<head>
  <title>MasterPass Test</title>
</head>
<body>
  <ul>
    <li>Error Number: <?php echo $result->errorNumber; ?></li>
    <li>Reason Code: <?php echo $result->reasonCode; ?></li>
    <li>Reason Description: <?php echo $result->reasonDescription; ?></li>
    <li>Transaction ID: <?php echo $result->transactionId; ?></li>
  </ul>
</body>
</html>
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
ini_set('display_errors');
error_reporting(E_ALL);

$pairing = isset($_GET['pair']) && $_GET['pair'] === 'true';
$checkout = isset($_GET['checkout']) && $_GET['checkout'] === 'true';
$connect = isset($_GET['connect']) && $_GET['connect'] === 'true';

$checkoutType = '';
if ($pairing && $checkout) {
    $checkoutType = HpsCentinelCheckoutType::PAIRING_CHECKOUT;
} else if ($pairing && !$checkout) {
    $checkoutType = HpsCentinelCheckoutType::PAIRING;
} else if (!$pairing && $connect) {
    $checkoutType = HpsCentinelCheckoutType::CONNECT;
} else {
    $checkoutType = HpsCentinelCheckoutType::LIGHTBOX;
}

$config = new HpsCentinelConfig();
$config->processorId    = 475;
$config->merchantId     = 'heartland_mark';
$config->transactionPwd = '5XcypXzRAkywLxgk';
$service = new HpsMasterPassService($config);

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

$payment = new HpsPaymentData();

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

$orderData = new HpsOrderData();
$orderData->orderNumber = str_shuffle('abcdefghijklmnopqrstuvwxyz');
$orderData->ipAddress = $_SERVER['REMOTE_ADDR'];
$orderData->browserHeader = $_SERVER['HTTP_ACCEPT'];
$orderData->userAgent = $_SERVER['HTTP_USER_AGENT'];
$orderData->originUrl = 'http://localhost:8000/test.php';
$orderData->termUrl = 'http://localhost:8000/checkoutauthenticate.php';
$orderData->checkoutType = $checkoutType;

$result = $service->createSession(
    $amount,
    'usd',
    $buyer,
    $payment,
    $shipping,
    $lineItems,
    $orderData
);
error_log(print_r($result, true));

?><!doctype html>
<html>
<head>
  <title>MasterPass Test</title>
</head>
<body>
  <?php if ($checkout): ?>
    <button type="button"
            onclick="startMasterPassCheckout('authorize', <?php echo $pairing ? 'true' : 'false'; ?>);">
      Authorize<?php if ($pairing): ?> + Pair<?php endif; ?>
    </button>
  <?php endif; ?>
  <?php if ($pairing && !$checkout): ?>
    <button type="button"
            onclick="startMasterPassConnect();">
      Connect
    </button>
  <?php endif; ?>
  <!-- <button type="button"
          onclick="startMasterPassCheckout('sale');">
    Sale
  </button> -->

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <!-- <script src="https://www.masterpass.com/lightbox/Switch/integration/MasterPass.client.js"></script> -->
  <script src="https://sandbox.masterpass.com/lightbox/Switch/integration/MasterPass.client.js "></script>
  <script>
    function startMasterPassCheckout(action, pair) {
      var data = {
        requestToken: "<?php echo $result->processorTransactionId; ?>",
        callbackUrl: 'http://localhost:8000/checkoutcomplete.php?' +
          'mp_action=' + action + '&' +
          'mp_orderNumber=<?php echo isset($result->orderNumber) ? $result->orderNumber : $orderData->orderNumber;?>&' +
          'mp_payload=<?php echo isset($result->payload) ? urlencode($result->payload) : '';?>&' +
          'mp_orderId=<?php echo $result->orderId;?>',
        merchantCheckoutId: "a4a6w4vnh3ymkif00kduz1if4allt72b3a",
        allowedCardTypes: ["master,amex,diners,discover,maestro,visa"],
        version: "v6"
      };
      <?php if (isset($_GET['cardId'])): ?>
        data.cardId = '<?php echo $_GET['cardId']; ?>';
      <?php endif; ?>
      <?php if (isset($_GET['shipId'])): ?>
        data.shippingId = '<?php echo $_GET['shipId']; ?>';
      <?php endif; ?>
      <?php if (isset($_GET['preCheckoutTransactionId'])): ?>
        data.precheckoutTransactionId = '<?php echo $_GET['preCheckoutTransactionId']; ?>';
      <?php endif; ?>
      <?php if (isset($_GET['walletName'])): ?>
        data.walletName = '<?php echo $_GET['walletName']; ?>';
      <?php endif; ?>
      <?php if (isset($_GET['walletId'])): ?>
        data.consumerwalletId = '<?php echo $_GET['walletId']; ?>';
      <?php endif; ?>
      if (pair) {
        <?php if ($pairing): ?>
          data.pairingRequestToken = "<?php echo $result->processorTransactionIdPairing; ?>";
        <?php endif; ?>
        data.requestedDataTypes = "[REWARD_PROGRAM, ADDRESS, PROFILE, CARD]";
        data.requestPairing = true;
      }
      MasterPass.client.checkout(data);
    }
    <?php if ($pairing): ?>
      function startMasterPassConnect() {
        MasterPass.client.connect({
          pairingRequestToken: "<?php echo $result->processorTransactionIdPairing; ?>",
          callbackUrl: 'http://localhost:8000/pairingcomplete.php?' +
            'mp_orderNumber=<?php echo $orderData->orderNumber; ?>&' +
            'mp_payload=<?php echo urlencode($result->payload);?>&' +
            'mp_orderId=<?php echo $result->orderId;?>',
          merchantCheckoutId: "a4a6w4vnh3ymkif00kduz1if4allt72b3a",
          requestedDataTypes: "[REWARD_PROGRAM, ADDRESS, PROFILE, CARD]",
          requestPairing: true,
          version: "v6"
        });
      }
    <?php endif; ?>
    function onSuccessfulCheckout(data) { console.log(data); }
  </script>
</body>
</html>
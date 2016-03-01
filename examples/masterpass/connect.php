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
$longAccessToken = isset($_GET['longAccessToken'])
                 ? $_GET['longAccessToken']
                 : '';

if ($longAccessToken === '') {
    header('Location: /test.php?pair=true');
    exit();
}

$config = new HpsCentinelConfig();
$config->processorId    = 475;
$config->merchantId     = 'heartland_mark';
$config->transactionPwd = '5XcypXzRAkywLxgk';

$service = new HpsMasterPassService($config);

$result = $service->preApproval($longAccessToken);
error_log(print_r($result, true));
?><!doctype html>
<html>
<head>
  <title>MasterPass Test</title>
</head>
<body>
  <ul>
    <li>Transaction ID: <?php echo $result->transactionId; ?></li>
    <li>Long Access Token: <?php echo $result->longAccessToken; ?></li>
  </ul>
  <form action="/test.php" method="GET">
    <input type="hidden" name="checkout" value="true" />
    <input type="hidden" name="longAccessToken" value="<?php echo $result->longAccessToken; ?>" />
    <input type="hidden" name="walletName" value="<?php echo $result->preCheckoutData->WalletName; ?>" />
    <input type="hidden" name="walletId" value="<?php echo $result->preCheckoutData->ConsumerWalletId; ?>" />
    <input type="hidden" name="preCheckoutTransactionId" value="<?php echo $result->preCheckoutTransactionId; ?>" />
    <div class="card">
      <h6>Credit Cards</h6>
      <?php foreach ($result->preCheckoutData->Cards->Card as $card): ?>
        <label>
          <input type="radio" name="cardId" value="<?php echo $card->CardId; ?>"<?php if ($card->SelectedAsDefault == 'true'): ?> checked="checked"<?php endif;?>/>
          <?php echo $card->BrandName; ?>
          ending in <?php echo $card->LastFour; ?>
          expiring <?php echo $card->ExpiryMonth; ?>/<?php echo $card->ExpiryYear; ?>
          <?php if ($card->SelectedAsDefault == 'true'): ?>(default)<?php endif; ?>
        </label><br />
      <?php endforeach; ?>
    </div>
    <div class="shipping">
      <h6>Shipping Addresses</h6>
      <?php foreach ($result->preCheckoutData->ShippingAddresses->ShippingAddress as $shipping): ?>
        <label>
          <input type="radio" name="shipId" value="<?php echo $shipping->AddressId;?>"<?php if ($shipping->SelectedAsDefault == 'true'): ?> checked="checked"<?php endif; ?>/>
          <?php echo $shipping->RecipientName; ?>
          <?php if ($shipping->SelectedAsDefault == 'true'): ?>(default)<?php endif; ?>
        </label><br />
      <?php endforeach; ?>
    </div>
    <input type="submit" value="Checkout" />
  </form>
</body>
</html>
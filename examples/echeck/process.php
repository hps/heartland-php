<?php

$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
require_once '../../Hps.php';

$address          = new HpsAddress();
$address->address = $_POST['holder_address_address'];
$address->city    = $_POST['holder_address_city'];
$address->state   = $_POST['holder_address_state'];
$address->zip     = $_POST['holder_address_zip'];

$holder            = new HpsCheckHolder();
$holder->address   = $address;
$holder->dlNumber  = $_POST['holder_dlnumber'];
$holder->dlState   = $_POST['holder_dlstate'];
$holder->firstName = $_POST['holder_firstname'];
$holder->lastName  = $_POST['holder_lastname'];
$holder->phone     = $_POST['holder_phone'];
$holder->dobYear   = $_POST['holder_dobyear'];
$holder->ssl4      = $_POST['holder_ssl4'];

$check                = new HpsCheck();
$check->accountNumber = $_POST['check_accountnumber'];
$check->routingNumber = $_POST['check_routingnumber'];
$check->checkType     = HpsCheckType::PERSONAL;
$check->secCode       = HpsSECCode::PPD;
$check->accountType   = HpsAccountType::CHECKING;
$check->dataEntryMode = HpsDataEntryMode::MANUAL;
$check->checkHolder   = $holder;

$config                = new HpsServicesConfig();
$config->secretApiKey  = 'skapi_cert_MTyMAQBiHVEAewvIzXVFcmUd2UcyBge_eCpaASUp0A';
$config->versionNumber = '0000';
$config->developerId   = '000000';

try {
    $service      = new HpsCheckService($config);
    $amount       = $_POST['payment_amount'];
    $saleResponse = $service->sale($check, $amount);

    printf('Success! Transaction ID: %s', $saleResponse->transactionId);

} catch (HpsException $e) {
    printf('Error running check sale: %s', $e->getMessage());
    printf('<pre><code>%s</code></pre>', print_r($e, true));
}

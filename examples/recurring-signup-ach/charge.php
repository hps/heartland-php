<?php
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

function SendEmail($to, $from, $subject, $body, $isHtml)
{
    $message = '<html><body>';
    $message .= $body;
    $message .= '</body></html>';

    $headers = "From: $from\r\n";
    $headers .= "Reply-To: $from\r\n";

    if ($isHtml) {
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=ISO-8859-1\r\n";
    }

    mail($to, $subject, $message, $headers);
}

function getIdentifier($id)
{
    $identifierBase = '%s-%s' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 10);
    return sprintf($identifierBase, date('Ymd'), $id);
}

function createCustomer(HpsPayPlanService $service, HpsCheckHolder $checkHolder)
{
    $customer                     = new HpsPayPlanCustomer();
    $customer->customerIdentifier = getIdentifier($checkHolder->firstName.$checkHolder->lastName);
    $customer->firstName          = $checkHolder->firstName;
    $customer->lastName           = $checkHolder->lastName;
    $customer->customerStatus     = HpsPayPlanCustomerStatus::ACTIVE;
    $customer->primaryEmail       = $checkHolder->emailAddress;
    $customer->addressLine1       = $checkHolder->address->address;
    $customer->city               = $checkHolder->address->city;
    $customer->stateProvince      = $checkHolder->address->state;
    $customer->zipPostalCode      = $checkHolder->address->zip;
    $customer->country            = $checkHolder->address->country;
    $customer->phoneDay           = $checkHolder->phoneNumber;
    $response = $service->addCustomer($customer);
    return $response->customerKey;
}

function createPaymentMethod(HpsPayPlanService $service, $customerKey, HpsCheckHolder $checkHolder, HpsCheck $check)
{
    $paymentMethod                          = new HpsPayPlanPaymentMethod();
    $paymentMethod->paymentMethodIdentifier = getIdentifier('Check'.substr($check->accountNumber, -4));
    $paymentMethod->paymentMethodType       = HpsPayPlanPaymentMethodType::ACH;
    $paymentMethod->achType                 = 'Checking';
    $paymentMethod->accountType             = 'Personal';
    $paymentMethod->telephoneIndicator      = 0;
    $paymentMethod->routingNumber           = $check->routingNumber;
    $paymentMethod->nameOnAccount           = $checkHolder->firstName . ' ' . $checkHolder->lastName;
    $paymentMethod->driversLicenseNumber    = $checkHolder->dlNumber;
    $paymentMethod->driversLicenseState     = $checkHolder->dlState;
    $paymentMethod->accountNumber           = $check->accountNumber;
    $paymentMethod->addressLine1            = $checkHolder->address->address;
    $paymentMethod->city                    = $checkHolder->address->city;
    $paymentMethod->stateProvince           = $checkHolder->address->state;
    $paymentMethod->zipPostalCode           = $checkHolder->address->zip;
    $paymentMethod->customerKey             = $customerKey;
    $paymentMethod->accountHolderYob        = $checkHolder->dobYear;
    $response = $service->addPaymentMethod($paymentMethod);
    return $response->paymentMethodKey;
}

function createSchedule(HpsPayPlanService $service, $customerKey, $paymentMethodKey, $amount)
{
    $schedule                     = new HpsPayPlanSchedule();
    $schedule->scheduleIdentifier = getIdentifier('CreditV');
    $schedule->customerKey        = $customerKey;
    $schedule->scheduleStatus     = HpsPayPlanScheduleStatus::ACTIVE;
    $schedule->paymentMethodKey   = $paymentMethodKey;
    $schedule->subtotalAmount     = new HpsPayPlanAmount($amount);
    $schedule->startDate          = date('m30Y', strtotime(date('Y-m-d', strtotime(date('Y-m-d'))).'+1 month'));
    $schedule->processingDateInfo = '31';
    $schedule->frequency          = HpsPayPlanScheduleFrequency::MONTHLY;
    $schedule->duration           = HpsPayPlanScheduleDuration::ONGOING;
    $schedule->reprocessingCount  = 1;
    $response = $service->addSchedule($schedule);
    return $response->scheduleKey;
}

require_once "../../Hps.php";

$config = new HpsServicesConfig();
$config->secretApiKey = 'skapi_cert_MTyMAQBiHVEAewvIzXVFcmUd2UcyBge_eCpaASUp0A';

// the following variables will be provided to you during certificaiton.
$config->versionNumber = '0000';
$config->developerId = '000000';

$payPlanService = new HpsPayPlanService($config);

$address = new HpsAddress();
$address->address = $_GET["Address"];
$address->city = $_GET["City"];
$address->state = $_GET["State"];
$address->zip = preg_replace('/[^0-9]/', '', $_GET["Zip"]);
$address->country = "USA";

$validCheckHolder = new HpsCheckHolder();
$validCheckHolder->firstName = $_GET["FirstName"];
$validCheckHolder->lastName = $_GET["LastName"];
$validCheckHolder->address = $address;
$validCheckHolder->phoneNumber = preg_replace('/[^0-9]/', '', $_GET["PhoneNumber"]);
$validCheckHolder->emailAddress = $_GET['Email'];
$validCheckHolder->dlState = $_GET['DLState'];
$validCheckHolder->dlNumber = $_GET['DLNumber'];
$validCheckHolder->dobYear = $_GET['DOBYear'];

$validCheck = new HpsCheck();
$validCheck->accountNumber = $_GET['AccountNumber'];
$validCheck->routingNumber = $_GET['RoutingNumber'];

$amount = $_GET['payment_amount'];

$customerKey = null;
$paymentMethodKey = null;
$scheduleKey = null;
try {
    $customerKey = createCustomer($payPlanService, $validCheckHolder);
    $paymentMethodKey = createPaymentMethod($payPlanService, $customerKey, $validCheckHolder, $validCheck);
    $scheduleKey = createSchedule($payPlanService, $customerKey, $paymentMethodKey, $amount);
} catch (HpsException $e) {
    die($e->getMessage());
}

$body = '<h1>Success!</h1>';
$body .= '<p>Thank you, '.$_GET['FirstName'].', for your subscription.';

printf('customerKey: %s<br />', $customerKey);
printf('paymentMethodKey: %s<br />', $paymentMethodKey);
printf('scheduleKey: %s<br />', $scheduleKey);

// i'm running windows, so i had to update this:
//ini_set("SMTP", "my-mail-server");

//SendEmail($_GET['Email'], 'donotreply@e-hps.com', 'Successful Charge!', $body, true);

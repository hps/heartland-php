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

function createCustomer(HpsPayPlanService $service, HpsCardHolder $cardHolder)
{
    $customer                     = new HpsPayPlanCustomer();
    $customer->customerIdentifier = getIdentifier($cardHolder->firstName.$cardHolder->lastName);
    $customer->firstName          = $cardHolder->firstName;
    $customer->lastName           = $cardHolder->lastName;
    $customer->customerStatus     = HpsPayPlanCustomerStatus::ACTIVE;
    $customer->primaryEmail       = $cardHolder->emailAddress;
    $customer->addressLine1       = $cardHolder->address->address;
    $customer->city               = $cardHolder->address->city;
    $customer->stateProvince      = $cardHolder->address->state;
    $customer->zipPostalCode      = $cardHolder->address->zip;
    $customer->country            = $cardHolder->address->country;
    $customer->phoneDay           = $cardHolder->phoneNumber;
    $response = $service->addCustomer($customer);
    return $response->customerKey;
}

function createPaymentMethod(HpsPayPlanService $service, $customerKey, HpsCardHolder $cardHolder, HpsTokenData $token)
{
    $paymentMethod                          = new HpsPayPlanPaymentMethod();
    $paymentMethod->paymentMethodIdentifier = getIdentifier('Credit'.$token->lastFour);
    $paymentMethod->paymentMethodType       = HpsPayPlanPaymentMethodType::CREDIT_CARD;
    $paymentMethod->nameOnAccount           = $cardHolder->firstName . ' ' . $cardHolder->lastName;
    $paymentMethod->paymentToken            = $token->tokenValue;
    $paymentMethod->customerKey             = $customerKey;
    $paymentMethod->country                 = $cardHolder->address->country;
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
$config->secretApiKey = 'skapi_cert_MYl2AQAowiQAbLp5JesGKh7QFkcizOP2jcX9BrEMqQ';

// the following variables will be provided to you during certificaiton.
$config->versionNumber = '0000';
$config->developerId = '000000';

$payPlanService = new HpsPayPlanService($config);

$address = new HpsAddress();
$address->address = $_GET["Address"];
$address->city = $_GET["City"];
$address->state = $_GET["State"];
$address->zip = $_GET["Zip"];
$address->country = "USA";

$validCardHolder = new HpsCardHolder();
$validCardHolder->firstName = $_GET["FirstName"];
$validCardHolder->lastName = $_GET["LastName"];
$validCardHolder->address = $address;
$validCardHolder->phone = $_GET["PhoneNumber"];
$validCardHolder->email = $_GET['Email'];

$suToken = new HpsTokenData();
$suToken->tokenValue = isset($_GET['token_value']) ? $_GET['token_value'] : '';
$suToken->lastFour = isset($_GET['card_last_four']) ? $_GET['card_last_four'] : '';

$amount = $_GET['payment_amount'];

$customerKey = null;
$paymentMethodKey = null;
$scheduleKey = null;
try {
    $customerKey = createCustomer($payPlanService, $validCardHolder);
    $paymentMethodKey = createPaymentMethod($payPlanService, $customerKey, $validCardHolder, $suToken);
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

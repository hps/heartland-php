<?php
    function SendEmail($to, $from, $subject, $body, $isHtml) {
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

    function chargeToken($chargeService,$suToken,$validCardHolder){
        try {
            $response = $chargeService->charge(
                15.15,
                'usd',
                $suToken,
                $validCardHolder);
        }
        catch (CardException $e) {
            return 'Failure: ' . $e->getMessage();
        }
        catch (Exception $e) {
            return 'Failure: ' . $e->getMessage();
        }
        return $response;
    }

    require_once "sdk/Hps.php";

    $config = new HpsConfiguration();
    $config->secretApiKey = 'skapi_cert_MYl2AQAowiQAbLp5JesGKh7QFkcizOP2jcX9BrEMqQ';

    // the following variables will be provided to you during certificaiton.
    $config->versionNumber = '0000';
    $config->developerId = '000000';

    $chargeService = new HpsChargeService($config);

    $address = new HpsAddress();
    $address->address = $_GET["Address"];
    $address->city = $_GET["City"];
    $address->state = $_GET["State"];
    $address->zip = preg_replace('/[^0-9]/', '', $_GET["Zip"]);
    $address->country = "United States";

    $validCardHolder = new HpsCardHolder();
    $validCardHolder->firstName = $_GET["FirstName"];
    $validCardHolder->lastName = $_GET["LastName"];
    $validCardHolder->address = $address;
    $validCardHolder->phoneNumber = preg_replace('/[^0-9]/', '', $_GET["PhoneNumber"]);

    $suToken = new HpsTokenData();
    $suToken->tokenValue = $_GET['token_value'];

    $response = chargeToken($chargeService,$suToken,$validCardHolder);

    if (is_string($response)){
        echo $response;
        exit;
    }

    $body = '<h1>Success!</h1>';
    $body .= '<p>Thank you, '.$_GET['FirstName'].', for your order of $15.15.</p>';

    echo "Transaction Id: " . $response->transactionId;

    // i'm running windows, so i had to update this:
    //ini_set("SMTP", "my-mail-server");

    SendEmail($_GET['EMAIL'], 'donotreply@e-hps.com', 'Successful Charge!', $body, true);


?>

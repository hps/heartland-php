<?php

$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'JWT.php';

if (!empty($_GET)) {
    include '../../Hps.php';

    class SimpleLogger implements HpsLoggerInterface
    {
        public function log($message, $object = null)
        {
            print '<pre><code>';
            print str_replace(
                array('<', '>'),
                array('&lt;', '&gt;'),
                sprintf('LOG: %s DATA: %s', $message, print_r($object, true))
            );
            print '</code></pre>';
        }
    }
    $logger = HpsLogger::getInstance();
    $logger->useLogger(new SimpleLogger());

    print '<pre><code>';
    print_r($_GET);
    print '</code></pre>';

    $config = new HpsServicesConfig();
    $config->secretApiKey = 'skapi_cert_MT2PAQB-9VQA5Z1mOXQbzZcH6O5PpdhjWtFhMBoL4A';

    $token = new HpsTokenData();
    $token->tokenValue = $_GET['heartlandToken'];

    $secureEcommerce = new HpsSecureEcommerce();
    $secureEcommerce->dataSource = 'Visa 3DSecure';
    $secureEcommerce->type       = '3DSecure';
    $secureEcommerce->data       = $_GET['cavv'];
    $secureEcommerce->eciFlag    = substr($_GET['eciflag'], 1);
    $secureEcommerce->xid        = $_GET['xid'];

    $creditService = new HpsFluentCreditService($config);
    $response = $creditService->charge()
        ->withAmount('1.00')
        ->withCurrency('usd')
        ->withToken($token)
        ->withSecureEcommerce($secureEcommerce)
        ->execute();

    print '<pre><code>';
    print_r($response);
    print '</code></pre>';
} else {
    $orderNumber = str_shuffle('abcdefghijklmnopqrstuvwxyz');
    //$apiIdentifier = 'Merchant-uatmerchant-Key';
    //$orgUnitId = '55ef3e43f723aa431c9969ae';
    //$apiKey = 'ac848959-f878-4f62-a0a2-4b2a648446c3';
    $apiIdentifier = '579bc985da529378f0ec7d0e';
    $orgUnitId = '5799c3c433fadd4cf427d01a';
    $apiKey = 'a32ed153-3759-4302-a314-546811590b43';

    $data = array(
        'jti' => str_shuffle('abcdefghijklmnopqrstuvwxyz'),
        'iat' => time(),
        'iss' => $apiIdentifier,
        'OrgUnitId' => $orgUnitId,
        'Payload' => array(
            'OrderDetails' => array(
                'OrderNumber' => $orderNumber,
                'Amount' => '1500',
                'CurrencyCode' => '840',
            ),
        ),
    );
    $jwt = JWT::encode($apiKey, $data);

    ?>
    <div id="button-container"></div>
    <form id="form">
        <div id="cardNumber"></div>
        <div id="cardExpiration"></div>
        <div id="cardCvv"></div>
        <div id="submit"></div>
        <input type="hidden" id="cardinalToken" name="cardinalToken">
        <input type="hidden" id="heartlandToken" name="heartlandToken">
        <input type="hidden" id="cavv" name="cavv">
        <input type="hidden" id="eciflag" name="eciflag">
        <input type="hidden" id="enrolled" name="enrolled">
        <input type="hidden" id="paresstatus" name="paresstatus">
        <input type="hidden" id="xid" name="xid">
        <input type="hidden" id="jwt" name="jwt">
    </form>
    <br>
    <label>
        jwt
        <input value="<?php echo $jwt; ?>">
    </label>
    <br>
    <label>
        order number
        <input value="<?php echo $orderNumber; ?>">
    </label>
    <script src="https://hps.github.io/token/2.1/securesubmit.js"></script>
    <script src="https://includestest.ccdc02.com/cardinalcruise/v1/songbird.js"></script>
    <script>

        new Heartland.HPS({
            cca: {
                jwt: '<?php print $jwt; ?>',
                orderNumber: '<?php print $orderNumber; ?>'
            },
            publicKey: 'pkapi_cert_dNpEYIISXCGDDyKJiV',
            type:      'iframe',
            fields: {
                cardNumber: {
                    target:      'cardNumber',
                    placeholder: '•••• •••• •••• ••••'
                },
                cardExpiration: {
                    target:      'cardExpiration',
                    placeholder: 'MM / YYYY'
                },
                cardCvv: {
                    target:      'cardCvv',
                    placeholder: 'CVV'
                },
                submit: {
                    target:       'submit'
                }
            },
            onTokenSuccess: function (response) {
                console.log(response);
                document.getElementById('cardinalToken').value = response.cardinal.token_value;
                document.getElementById('heartlandToken').value = response.heartland.token_value;
                cca();
            },
            onTokenError: function (response) {
                console.log(response);
            }
        });

    function cca() {
        Cardinal.setup('init', {
            jwt: '<?php echo $jwt ?>',
                button: {
                    containerId: 'button-container'
                }
        });
        Cardinal.on('payments.validated', function (data, jwt) {
            console.log(data);
            switch (data.ActionCode) {
                case 'SUCCESS':
                case 'NOACTION':
                    // Handle successful authentication scenario
                    document.getElementById('cavv').value =
                        data.Payment.ExtendedData.CAVV
                        ? data.Payment.ExtendedData.CAVV
                        : '';
                    document.getElementById('eciflag').value =
                        data.Payment.ExtendedData.ECIFlag
                        ? data.Payment.ExtendedData.ECIFlag
                        : '';
                    document.getElementById('enrolled').value =
                        data.Payment.ExtendedData.Enrolled
                        ? data.Payment.ExtendedData.Enrolled
                        : '';
                    document.getElementById('paresstatus').value =
                        data.Payment.ExtendedData.PAResStatus
                        ? data.Payment.ExtendedData.PAResStatus
                        : '';
                    document.getElementById('xid').value =
                        data.Payment.ExtendedData.XID
                        ? data.Payment.ExtendedData.XID
                        : '';
                    document.getElementById('jwt').value =
                        jwt
                        ? jwt
                        : '';
                    document.getElementById('form').submit();
                    break;

                case 'FAILURE':
                    // Handle authentication failed or error encounter scenario
                    break;

                case 'ERROR':
                    // Handle service level error
                    break;
            }
        });
        Cardinal.start('cca', {
            OrderDetails: {
                OrderNumber: '<?php echo $orderNumber ?>cca'
            },
            Token: {
                Token: document.getElementById('cardinalToken').value,
                ExpirationMonth: '01',
                ExpirationYear:  '2099'
            }
        });
    }
    </script>
<?php }

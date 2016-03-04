<?php

/**
 * Setup autoloading
 */
require_once __DIR__ . '/../vendor/autoload.php';

//set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." ));

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'integration/TestData/TestCreditCard.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'integration/TestData/TestCardHolder.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'integration/TestData/TestServicesConfig.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'integration/TestData/TestCheck.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'integration/TestData/TestGiftCard.php');

// Uncomment the below to enable logging
// class SimpleLogger implements HpsLoggerInterface
// {
//     public function log($message, $object = null)
//     {
//         error_log(sprintf('LOG: %s DATA: %s', $message, print_r($object, true)));
//     }
// }
// $logger = HpsLogger::getInstance();
// $logger->useLogger(new SimpleLogger());

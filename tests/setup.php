<?php
//set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." ));

// Everything you need to make a charge is included from the file below.
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'../Hps.php');

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'TestData/TestCreditCard.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'TestData/TestCardHolder.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'TestData/TestServicesConfig.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'TestData/TestCheck.php');
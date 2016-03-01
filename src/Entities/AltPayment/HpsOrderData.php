<?php
/**
 * Order Data
 *
 * PHP Version 5.2+
 *
 * @category PHP
 * @package  HPS
 * @author   Heartland Payment Systems <EntApp_DevPortal@e-hps.com>
 * @license  https://github.com/hps/heartland-php/blob/master/LICENSE.txt Custom
 * @link     https://github.com/hps/heartland-php
 */

/**
 * Order Data
 *
 * @category PHP
 * @package  HPS
 * @author   Heartland Payment Systems <EntApp_DevPortal@e-hps.com>
 * @license  https://github.com/hps/heartland-php/blob/master/LICENSE.txt Custom
 * @link     https://github.com/hps/heartland-php
 */
class HpsOrderData
{
    public $transactionStatus = null;
    public $currencyCode = null;
    public $orderId = null;
    public $orderNumber = null;
    public $transactionMode = 'S';
    public $ipAddress = null;
    public $browserHeader = null;
    public $userAgent = null;
    public $originUrl = null;
    public $termUrl = null;
    public $checkoutType = null;
    public $pairingToken = null;
    public $pairingVerifier = null;
}
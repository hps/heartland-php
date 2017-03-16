<?php

/**
 * Class HpsDirectMarketData
 */
class HpsDirectMarketData
{
    public $invoiceNumber = null;
    public $shipMonth     = null;
    public $shipDay       = null;
    /**
     * HpsDirectMarketData constructor.
     *
     * @param null $invoiceNumber
     * @param null $shipMonth
     * @param null $shipDay
     */
    public function __construct($invoiceNumber = null, $shipMonth = null, $shipDay = null)
    {
        $this->invoiceNumber = $invoiceNumber;

        if ($shipMonth == null) {
          $shipMonth = date('m');
        }
        $this->shipMonth = $shipMonth;

        if ($shipDay == null) {
          $shipDay = date('d');
        }
        $this->shipDay = $shipDay;
    }
}

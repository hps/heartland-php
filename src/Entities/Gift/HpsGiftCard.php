<?php

/**
 * Class HpsGiftCard
 */
class HpsGiftCard
{
    public $number         = null;
    public $trackData      = null;
    public $alias          = null;
    public $tokenValue     = null;
    public $encryptionData = null;
    public $pin            = null;
    /**
     * HpsGiftCard constructor.
     *
     * @param null $number
     */
    public function __construct($number = null)
    {
        $this->number = $number;
    }
}
